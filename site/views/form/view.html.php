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
     * @var JDocumentHtml
     */
    public $document;

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

    protected $option;

    protected $pageclass_sfx;
    
    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $this->option = JFactory::getApplication()->input->getCmd('option');
        
//        $this->item   = $this->get('Item');
        $this->form   = $this->get('Form');
        $this->state  = $this->get('State');

        $this->params = $this->state->get('params');

        $user   = JFactory::getUser();
        if (!$user->authorise('core.create', 'com_userideas')) {
            $returnUrl = UserideasHelperRoute::getFormRoute();
            $loginUrl  = JRoute::_('index.php?option=com_users&view=login&return='.base64_encode($returnUrl), false);
            $loginUrl  = trim($this->params->get('login_page_url', $loginUrl));

            $app->enqueueMessage(JText::_('COM_USERIDEAS_ERROR_NO_PERMISSIONS_TO_DO_ACTION'), 'notice');
            $app->redirect($loginUrl);
            return;
        }

        // Authorize the user to create or edit content.
        $model       = $this->getModel();
        $this->item  = $model->getItem();
        if (!$this->item or !$this->item->id) { // Check if it is new record.
            $authorised = $user->authorise('core.create', 'com_userideas') || (count($user->getAuthorisedCategories('com_userideas', 'core.create')));
        } else {
            $authorised = $this->item->params->get('access-edit');
        }

        // Redirect the user to login form if he is not authorized.
        if (!$authorised) {
            $returnUrl = UserideasHelperRoute::getFormRoute();
            $loginUrl  = JRoute::_('index.php?option=com_users&view=login&return='.base64_encode($returnUrl));
            $loginUrl  = trim($this->params->get('login_page_url', $loginUrl));

            $app->enqueueMessage(JText::_('COM_USERIDEAS_ERROR_NO_PERMISSIONS_TO_DO_ACTION'), 'notice');
            $app->redirect($loginUrl);
            return;
        }

        $this->prepareDebugMode();
        $this->prepareDocument();

        parent::display($tpl);
    }

    /**
     * Check the system for debug mode
     */
    protected function prepareDebugMode()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $this->disabledButton = '';

        // Check for maintenance (debug) state
        $params          = $this->state->get('params');
        $this->debugMode = $params->get('debug_item_adding_disabled', 0);
        if ($this->debugMode) {
            $msg = trim($params->get('debug_disabled_functionality_msg'));
            if (!$msg) {
                $msg = JText::_('COM_USERIDEAS_DEBUG_MODE_DEFAULT_MSG');
            }
            $app->enqueueMessage($msg, 'notice');

            $this->disabledButton = 'disabled="disabled"';
        }
    }

    /**
     * Prepares the document.
     */
    protected function prepareDocument()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Prepare page suffix
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        $menus = $app->getMenu();

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
            $title = $app->get('sitename');
        } elseif ((int)$app->get('sitename_pagetitles', 0)) { // Set site name if it is necessary ( the option 'sitename' = 1 )
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        }

        $this->document->setTitle($title);

        // Meta Description
        $this->document->setDescription($this->params->get('menu-meta_description'));

        // Meta keywords
        $this->document->setMetaData('keywords', $this->params->get('menu-meta_keywords'));

        // Add current layout into breadcrumbs
        $pathway = $app->getPathway();
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
