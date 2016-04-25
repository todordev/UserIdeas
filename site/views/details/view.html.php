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

class UserideasViewDetails extends JViewLegacy
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

    protected $category;
    protected $canEdit;
    protected $canComment;
    protected $canEditComment;
    protected $comments;
    protected $userId;
    protected $socialProfiles;
    protected $integrationOptions = array();

    protected $disabledButton;
    protected $debugMode;
    protected $commentsEnabled;

    protected $option;

    protected $pageclass_sfx;
    
    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $this->option = JFactory::getApplication()->input->getCmd('option');
        
        $this->state  = $this->get('State');
        $this->item   = $this->get('Item');
        $this->params = $this->state->get('params');

        $this->category = new Userideas\Category\Category(JFactory::getDbo());
        $this->category->load($this->item->catid);

        $user = JFactory::getUser();
        $this->userId = $user->get('id');

        $helperBus     = new Prism\Helper\HelperBus($this->item);
        $helperBus->addCommand(new Userideas\Helper\PrepareItemParams());
        $helperBus->addCommand(new Userideas\Helper\PrepareItemStatus());
        $helperBus->addCommand(new Userideas\Helper\PrepareItemAccess(JFactory::getUser()));

        if ($this->params->get('show_tags')) {
            $helperBus->addCommand(new Userideas\Helper\PrepareItemTags());
        }

        $helperBus->handle();

        // Set permission state. Is it possible to be edited items?
        $this->canEdit         = $user->authorise('core.edit.own', 'com_userideas');

        $this->commentsEnabled = $this->params->get('comments_enabled', 1);
        $this->canComment      = $user->authorise('userideas.comment.create', 'com_userideas');
        $this->canEditComment  = $user->authorise('userideas.comment.edit.own', 'com_userideas');

        // Get the model of the comments
        // that I will use to load all comments for this item.
        $modelComments  = JModelLegacy::getInstance('Comments', 'UserideasModel');
        $this->comments = $modelComments->getItems();

        // Get the model of the comment
        $commentModelForm = JModelLegacy::getInstance('Comment', 'UserideasModel');

        // Validate the owner of the comment,
        // If someone wants to edit it.
        $commentId = (int)$commentModelForm->getState('comment_id');
        if ($commentId > 0) {
            $comment = $commentModelForm->getItem($commentId, $this->userId);

            if (!$comment) {
                $app->enqueueMessage(JText::_('COM_USERIDEAS_ERROR_INVALID_COMMENT'), 'error');
                $app->redirect(JRoute::_(UserideasHelperRoute::getItemsRoute(), false));
                return;
            }
        }

        // Get comment form
        $this->form = $commentModelForm->getForm();

        // Prepare integration. Load avatars and profiles.
        $this->prepareIntegration($this->params);

        // Prepare the link to the details page.
        $this->item->link = UserideasHelperRoute::getDetailsRoute($this->item->slug, $this->item->catslug);
        $this->item->text = $this->item->description;

        $this->prepareDebugMode();
        $this->prepareDocument();

        // Events
        JPluginHelper::importPlugin('content');
        $dispatcher        = JEventDispatcher::getInstance();
        $this->item->event = new stdClass();
        $offset            = 0;

        $dispatcher->trigger('onContentPrepare', array('com_userideas.details', &$this->item, &$this->params, $offset));

        $results                                 = $dispatcher->trigger('onContentBeforeDisplay', array('com_userideas.details', &$this->item, &$this->params, $offset));
        $this->item->event->beforeDisplayContent = trim(implode("\n", $results));

        $results                                  = $dispatcher->trigger('onContentAfterDisplay', array('com_userideas.details', &$this->item, &$this->params, $offset));
        $this->item->event->onContentAfterDisplay = trim(implode("\n", $results));

        $this->item->description = $this->item->text;
        unset($this->item->text);

        // Count hits
        $model = $this->getModel();
        $model->hit($this->item->id);

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
        $params = $this->state->get('params');
        /** @var $params Joomla\Registry\Registry */

        $this->debugMode = $params->get('debug_item_adding_disabled', 0);
        if ($this->debugMode) {
            $msg = JString::trim($params->get('debug_disabled_functionality_msg'));
            if (!$msg) {
                $msg = JText::_('COM_USERIDEAS_DEBUG_MODE_DEFAULT_MSG');
            }
            $app->enqueueMessage($msg, 'notice');

            $this->disabledButton = 'disabled="disabled"';
        }
    }

    /**
     * Prepares the document
     */
    protected function prepareDocument()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Prepare page suffix
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        // Prepare page heading
        $this->preparePageHeading();

        // Prepare page heading
        $this->preparePageTitle();

        // Meta Description
        $this->document->setDescription($this->params->get('menu-meta_description'));

        // Meta keywords
        $this->document->setMetaData('keywords', $this->params->get('menu-meta_keywords'));

        // Add current layout into breadcrumbs
        $pathway = $app->getPathway();
        $pathway->addItem(JText::_('COM_USERIDEAS_PATHWAY_FORM_TITLE'));

        // Scripts
        JHtml::_('behavior.keepalive');

        JHtml::_('jquery.framework');
        JHtml::_('Prism.ui.pnotify');
    }

    private function preparePageTitle()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // If it is assigned to menu item, params will contains "page_title".
        // If it is not assigned, I will use the title of the item
        if ($this->params->get('page_title')) {
            $title = $this->params->get('page_title');
        } else {
            $seo = $this->params->get('category_in_title');

            switch ($seo) {
                case '1': // Before page title
                    $title = $this->category->title . ' | ' . $this->item->title;
                    break;

                case '2': // After page title
                    $title = $this->item->title . ' | ' . $this->category->title;
                    break;

                default: // NONE
                    $title = $this->item->title;
                    break;
            }
        }

        // Add title before or after Site Name
        if (!$title) {
            $title = $app->get('sitename');
        } elseif ((int)$app->get('sitename_pagetitles', 0) === 1) {
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        } elseif ((int)$app->get('sitename_pagetitles', 0) === 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        $this->document->setTitle($title);
    }

    private function preparePageHeading()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $menus = $app->getMenu();

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();

        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', $this->item->title);
        }
    }

    /**
     * Prepare social profiles.
     *
     * @param Joomla\Registry\Registry $params
     */
    protected function prepareIntegration($params)
    {
        // Get users IDs
        $usersIds = array();
        foreach ($this->comments as $comment) {
            if ($comment->user_id > 0) {
                $usersIds[] = $comment->user_id;
            }
        }

        // Add the ID of item owner.
        if ($this->item->user_id > 0) {
            $usersIds[] = $this->item->user_id;
        }
        $usersIds = array_filter(array_unique($usersIds));

        // If there are no users, do not continue.
        if (count($usersIds) > 0) {
            $this->integrationOptions = array(
                'size' => $params->get('integration_avatars_size', 'small'),
                'default' => $params->get('integration_avatars_default', '/media/com_userideas/images/no-profile.png')
            );

            $socialProfilesBuilder = new Prism\Integration\Profiles\Builder(
                array(
                    'social_platform' => $params->get('integration_social_platform'),
                    'users_ids'       => $usersIds
                )
            );

            $socialProfilesBuilder->build();

            $this->socialProfiles = $socialProfilesBuilder->getProfiles();
        }
    }
}
