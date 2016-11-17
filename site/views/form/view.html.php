<?php
/**
 * @package      Userideas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class UserideasViewForm extends JViewLegacy
{
    /**
     * @var JDocument
     */
    public $document;

    /**
     * @var JApplicationSite
     */
    protected $app;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $state;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $params;

    protected $form;
    protected $item;

    protected $disabledButton;
    protected $debugMode;
    protected $formEncrypt = '';
    protected $maxFileSize;
    protected $maxFileSizeBites = 0;
    protected $hasAttachment = false;
    protected $layoutData;
    protected $authorised;

    protected $option;
    protected $pageclass_sfx;
    
    public function display($tpl = null)
    {
        $this->app     = JFactory::getApplication();
        $this->option  = $this->app->input->getCmd('option');
        
        $this->form    = $this->get('Form');
        $this->state   = $this->get('State');

        $this->params    = $this->state->get('params');

        $user          = JFactory::getUser();
        if (!$user->authorise('core.create', 'com_userideas')) {
            $loginUrl  = JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(UserideasHelperRoute::getFormRoute()), false);
            $loginUrl  = trim($this->params->get('login_page_url', $loginUrl));

            $this->app->enqueueMessage(JText::_('COM_USERIDEAS_ERROR_NO_PERMISSIONS_TO_DO_ACTION'), 'notice');
            $this->app->redirect($loginUrl);
            return;
        }

        // Authorize the user to create or edit content.
        $this->item  = $this->get('Item');
        if (!$this->item or !$this->item->id) { // Check if it is new record.
            $this->authorised = $user->authorise('core.create', 'com_userideas') || count($user->getAuthorisedCategories('com_userideas', 'core.create'));
        } else {
            $this->authorised = $this->item->params->get('access-edit');
        }

        // Redirect the user to login form if he is not authorized.
        if (!$this->authorised) {
            $loginUrl  = JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(UserideasHelperRoute::getFormRoute()), false);
            $loginUrl  = trim($this->params->get('login_page_url', $loginUrl));

            $this->app->enqueueMessage(JText::_('COM_USERIDEAS_ERROR_NO_PERMISSIONS_TO_DO_ACTION'), 'notice');
            $this->app->redirect($loginUrl);
            return;
        }

        $this->prepareDebugMode();
        $this->prepareDocument();

        // If attachments are allowed, execute the following code.
        if ($this->params->get('allow_attachment', Prism\Constants::DISABLED)) {
            $this->prepareAttachment();
        }

        parent::display($tpl);
    }

    /**
     * Prepare data for attachment layout.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function prepareAttachment()
    {
        $this->formEncrypt      = 'enctype="multipart/form-data"';
        $this->maxFileSize      = Prism\Utilities\FileHelper::getMaximumFileSize((int)$this->params->get('max_size', 5), 'MB');
        $this->maxFileSizeBites = Prism\Utilities\MathHelper::convertToBytes($this->params->get('max_size', 5), 'MB');

        if ($this->item !== null) {
            $keys    = array(
                'item_id' => $this->item->id,
                'source'  => 'item'
            );

            $attachment = new Userideas\Attachment\Attachment(JFactory::getDbo());
            $attachment->load($keys);

            $this->hasAttachment = $attachment->getId() ? true : false;

            if ($this->hasAttachment) {
                $filesystemHelper = new Prism\Filesystem\Helper($this->params);

                // Get the filename from the session.
                $mediaFolder = $filesystemHelper->getMediaFolderUri($this->item->id, Userideas\Constants::ITEM_FOLDER);
                $fileUrl     = $mediaFolder . '/' . $attachment->getFilename();

                // Prepare layout data.
                $this->layoutData             = new stdClass;
                $this->layoutData->attachment = $attachment;
                $this->layoutData->canEdit    = $this->authorised;
                $this->layoutData->fileUrl    = $fileUrl;
                $this->layoutData->returnUrl  = JRoute::_(UserideasHelperRoute::getFormRoute($this->item->id), false);
            }
        }

        JHtml::_('Prism.ui.bootstrap3FileInput');

        // Include JavaScript translation.
        JText::script('COM_USERIDEAS_PICK_FILE');
        JText::script('COM_USERIDEAS_REMOVE');

        if ($this->authorised) {
            JHtml::_('Prism.ui.sweetAlert');
            JText::script('COM_USERIDEAS_CANCEL');
            JText::script('COM_USERIDEAS_YES_DELETE_IT');
            JText::script('COM_USERIDEAS_ARE_YOU_SURE');
            JText::script('COM_USERIDEAS_CANNOT_RECOVER_FILE');
        }

        $version = new Userideas\Version();
        $this->document->addScript('media/' . $this->option . '/js/site/form.js?v=' . $version->getShortVersion());
    }
    
    /**
     * Check the system for debug mode
     */
    protected function prepareDebugMode()
    {
        $this->disabledButton = '';

        // Check for maintenance (debug) state
        $params          = $this->state->get('params');
        $this->debugMode = $params->get('debug_item_adding_disabled', 0);
        if ($this->debugMode) {
            $msg = trim($params->get('debug_disabled_functionality_msg'));
            if (!$msg) {
                $msg = JText::_('COM_USERIDEAS_DEBUG_MODE_DEFAULT_MSG');
            }
            $this->app->enqueueMessage($msg, 'notice');

            $this->disabledButton = 'disabled="disabled"';
        }
    }

    /**
     * Prepares the document.
     */
    protected function prepareDocument()
    {
        // Prepare page suffix
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        $menus = $this->app->getMenu();

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();

        if ($menu) {
            $this->params->def('page_heading', $menu->title);
        } else {
            $this->params->def('page_heading', JText::_('COM_USERIDEAS_FROM_DEFAULT_PAGE_TITLE'));
        }

        // Prepare page title
        $title = $menu->title;
        if (!$title) {
            $title = $this->app->get('sitename');
        } elseif ((int)$this->app->get('sitename_pagetitles', 0)) { // Set site name if it is necessary ( the option 'sitename' = 1 )
            $title = JText::sprintf('JPAGETITLE', $this->app->get('sitename'), $title);
        }

        $this->document->setTitle($title);

        // Meta Description
        $this->document->setDescription($this->params->get('menu-meta_description'));

        // Meta keywords
        $this->document->setMetaData('keywords', $this->params->get('menu-meta_keywords'));

        // Add current layout into breadcrumbs
        $pathway = $this->app->getPathway();
        $pathway->addItem(JText::_('COM_USERIDEAS_PATHWAY_FORM_TITLE'));

        // Scripts
        JHtml::_('bootstrap.framework');
        JHtml::_('bootstrap.tooltip');

        if ($this->params->get('enable_chosen', 0)) {
            JHtml::_('formbehavior.chosen', '.js-uideas-catid-select');
        }

        JHtml::_('behavior.keepalive');
        JHtml::_('behavior.formvalidation');
    }
}
