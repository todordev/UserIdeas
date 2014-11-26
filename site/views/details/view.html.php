<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

class UserIdeasViewDetails extends JViewLegacy
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
    protected $integrationOptions;

    protected $disabledButton;
    protected $debugMode;
    protected $commentsEnabled;

    protected $option;

    protected $pageclass_sfx;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->getCmd("option");
    }

    /**
     * Display the view.
     */
    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Initialise variables
        $this->state  = $this->get('State');
        $this->item   = $this->get('Item');
        $this->params = $this->state->get("params");

        jimport("userideas.category");
        $this->category = new UserIdeasCategory(JFactory::getDbo());
        $this->category->load($this->item->catid);

        $user = JFactory::getUser();
        $this->userId = $user->get("id");

        // Set permission state. Is it possible to be edited items?
        $this->canEdit = $user->authorise('core.edit.own', 'com_userideas');

        $this->commentsEnabled = $this->params->get("comments_enabled", 1);
        $this->canComment = $user->authorise('userideas.comment.create', 'com_userideas');
        $this->canEditComment = $user->authorise('userideas.comment.edit.own', 'com_userideas');

        // Get the model of the comments
        // that I will use to load all comments for this item.
        $modelComments  = JModelLegacy::getInstance("Comments", "UserIdeasModel");
        $this->comments = $modelComments->getItems();

        // Get the model of the comment
        $commentModelForm = JModelLegacy::getInstance("Comment", "UserIdeasModel");

        // Validate the owner of the comment,
        // If someone wants to edit it.
        $commentId = $commentModelForm->getState("comment_id");

        if (!empty($commentId)) {

            $comment = $commentModelForm->getItem($commentId, $this->userId);

            if (!$comment) {
                $app->enqueueMessage(JText::_("COM_USERIDEAS_ERROR_INVALID_COMMENT"), "error");
                $app->redirect(JRoute::_(UserIdeasHelperRoute::getItemsRoute(), false));

                return;
            }

        }

        // Get comment form
        $this->form = $commentModelForm->getForm();

        // Prepare integration. Load avatars and profiles.
        $this->prepareIntegration($this->params);

        // Prepare the link to the details page.
        $this->item->link = UserIdeasHelperRoute::getDetailsRoute($this->item->slug, $this->item->catslug);
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

        $this->disabledButton = "";

        // Check for maintenance (debug) state
        $params = $this->state->get("params");
        /** @var $params Joomla\Registry\Registry */

        $this->debugMode = $params->get("debug_item_adding_disabled", 0);
        if ($this->debugMode) {
            $msg = JString::trim($params->get("debug_disabled_functionality_msg"));
            if (!$msg) {
                $msg = JText::_("COM_USERIDEAS_DEBUG_MODE_DEFAULT_MSG");
            }
            $app->enqueueMessage($msg, "notice");

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
        $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));

        // Add current layout into breadcrumbs
        $pathway = $app->getPathway();
        $pathway->addItem(JText::_("COM_USERIDEAS_PATHWAY_FORM_TITLE"));

        // Scripts
        JHtml::_('behavior.keepalive');

        JHtml::_('jquery.framework');
        JHtml::_('itprism.ui.pnotify');
    }

    private function preparePageTitle()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // If it is assigned to menu item, params will contains "page_title".
        // If it is not assigned, I will use the title of the item
        if ($this->params->get("page_title")) {
            $title = $this->params->get("page_title");
        } else {

            $seo = $this->params->get("seo_cat_to_title");

            switch ($seo) {

                case "1": // Before page title
                    $title = $this->category->title . " | " . $this->item->title;
                    break;

                case "2": // After page title
                    $title = $this->item->title . " | " . $this->category->title;
                    break;

                default: // NONE
                    $title = $this->item->title;
                    break;
            }
        }

        // Add title before or after Site Name
        if (!$title) {
            $title = $app->get('sitename');
        } elseif ($app->get('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        } elseif ($app->get('sitename_pagetitles', 0) == 2) {
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
     *
     * @todo Move it to a trait when traits become mass.
     */
    protected function prepareIntegration($params)
    {
        // Get users IDs
        $usersIds = array();
        foreach ($this->comments as $comment) {
            if (!empty($comment->user_id)) {
                $usersIds[] = $comment->user_id;
            }
        }

        // Add the ID of item owner.
        if (!empty($this->item->user_id)) {
            $usersIds[] = $this->item->user_id;
        }
        $usersIds   = array_unique($usersIds);

        // Get a social platform for integration
        $socialPlatform       = $params->get("integration_social_platform");

        $this->integrationOptions = array(
            "size" => $params->get("integration_avatars_size", 50),
            "default" => $params->get("integration_avatars_default", "/media/com_crowdfunding/images/no-profile.png"),
            "width" => $params->get("integration_avatar_width", 24),
            "height" => $params->get("integration_avatar_height", 24),
        );

        // If there is now users, do not continue.
        if (!$usersIds) {
            return;
        }

        $options = array(
            "social_platform" => $socialPlatform,
            "users_ids" => $usersIds
        );

        jimport("itprism.integrate.profiles.builder");
        $profileBuilder = new ITPrismIntegrateProfilesBuilder($options);
        $profileBuilder->build();

        $this->socialProfiles = $profileBuilder->getProfiles();
    }
}
