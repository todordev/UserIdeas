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
     * @var JDocument
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

    /**
     * @var JApplicationSite
     */
    protected $app;

    protected $form;
    protected $item;
    
    protected $userId;
    protected $socialProfiles;
    protected $integrationOptions = array();
    
    // Page
    protected $category;
    protected $option;
    protected $pageclass_sfx;
    protected $disabledButton;
    protected $debugMode;
    protected $returnUrl;
    protected $sweetAlertInitialized = false;

    // Item
    protected $canEdit;
    protected $hasAttachment;
    protected $itemLayoutData;
    protected $mediaFolder;

    // Comments
    protected $comments;
    protected $commentsEnabled;
    protected $commentsAttachmentsEnabled;
    protected $commentsAttachments = array();
    protected $formEncrypt;
    protected $maxFileSize;
    protected $maxFileSizeBites;
    protected $canComment;
    protected $canEditComment;
    protected $commentLayoutData;

    public function display($tpl = null)
    {
        $this->app    = JFactory::getApplication();
        $this->option = $this->app->input->getCmd('option');

        $this->state  = $this->get('State');
        $this->item   = $this->get('Item');
        $this->params = $this->state->get('params');

        // Redirect if there is no item.
        if (!$this->item->id) {
            $this->app->redirect(JRoute::_(UserideasHelperRoute::getItemsRoute(), false));
            return;
        }

        $this->category = new Userideas\Category\Category(JFactory::getDbo());
        $this->category->load($this->item->catid);

        $user         = JFactory::getUser();
        $this->userId = (int)$user->get('id');

        // Handle bus helper.
        $helperBus = new Prism\Helper\HelperBus($this->item);
        $helperBus->addCommand(new Userideas\Helper\PrepareItemParamsHelper());
        $helperBus->addCommand(new Userideas\Helper\PrepareItemStatusHelper());
        $helperBus->addCommand(new Userideas\Helper\PrepareItemAccessHelper(JFactory::getUser()));
        if ($this->params->get('show_tags')) {
            $helperBus->addCommand(new Userideas\Helper\PrepareItemTagsHelper());
        }
        $helperBus->handle();

        // Check the view access to the article (the model has already computed the values).
        if ($this->item->params->get('access-view') === false) {
            $this->app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            $this->app->setHeader('status', 403, true);
            return;
        }

        // Set permission state. Is it possible to be edited items?
        $this->canEdit         = $this->item->params->get('access-edit');
        $this->commentsEnabled = $this->params->get('comments_enabled', Prism\Constants::DISABLED);

        $this->prepareDebugMode();
        $this->prepareDocument();

        // Prepare the link to the details page.
        $this->item->link = UserideasHelperRoute::getDetailsRoute($this->item->slug, $this->item->catslug);
        $this->item->text = $this->item->description;
        $this->returnUrl  = JRoute::_($this->item->link, false);

        // If attachments are allowed, prepare it.
        if ($this->params->get('allow_attachment', Prism\Constants::DISABLED)) {
            $this->prepareAttachment();
        }

        // If comments are enabled, prepare them.
        if ($this->commentsEnabled) {
            $this->prepareComments($user);
        }

        // Prepare integration. Load avatars and profiles.
        $this->prepareIntegration($this->params);

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
     * Prepare data for attachment layout.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function prepareAttachment()
    {
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
                $this->mediaFolder = $filesystemHelper->getMediaFolderUri($this->item->id, Userideas\Constants::ITEM_FOLDER);
                $fileUrl           = $this->mediaFolder .'/'. $attachment->getFilename();

                // Prepare layout data.
                $this->itemLayoutData               = new stdClass;
                $this->itemLayoutData->attachment   = $attachment;
                $this->itemLayoutData->canEdit      = $this->canEdit;
                $this->itemLayoutData->fileUrl      = $fileUrl;
                $this->itemLayoutData->returnUrl    = $this->returnUrl;

                if ($this->canEdit) {
                    $this->initSweetAlert();
                }
            }
        }
    }

    /**
     * @param JUser $user
     *
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    protected function prepareComments(JUser $user)
    {
        $this->canComment      = $user->authorise('userideas.comment.create', 'com_userideas');
        $this->canEditComment  = $user->authorise('userideas.comment.edit.own', 'com_userideas');
        $this->commentsAttachmentsEnabled  = $this->params->get('comments_allow_attachment', Prism\Constants::DISABLED);

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

            if ($comment === null or !$comment->id) {
                throw new RuntimeException(JText::_('COM_USERIDEAS_ERROR_INVALID_COMMENT'));
            }
        }

        // Get comment form
        $this->form       = $commentModelForm->getForm();
        
        if ($this->commentsAttachmentsEnabled) {
            $this->prepareCommentsAttachment();
        }
    }

    /**
     * Prepare data for attachment layout.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function prepareCommentsAttachment()
    {
        $this->formEncrypt      = 'enctype="multipart/form-data"';
        $this->maxFileSize      = Prism\Utilities\FileHelper::getMaximumFileSize((int)$this->params->get('max_size', 5), 'MB');
        $this->maxFileSizeBites = Prism\Utilities\MathHelper::convertToBytes($this->params->get('max_size', 5), 'MB');

        JHtml::_('Prism.ui.bootstrap3FileInput');

        // Include JavaScript translation.
        JText::script('COM_USERIDEAS_PICK_FILE');
        JText::script('COM_USERIDEAS_REMOVE');

        $options = array(
            'comments_ids' => Prism\Utilities\ArrayHelper::getIds($this->comments),
            'index'        => 'comment_id'
        );
        $attachments = new Userideas\Attachment\Attachments(JFactory::getDbo());
        $attachments->load($options);

        $this->commentsAttachments = $attachments->getAttachments(Prism\Constants::NO);
        if (count($this->commentsAttachments) > 0) {
            $filesystemHelper       = new Prism\Filesystem\Helper($this->params);
            $this->mediaFolder      = $filesystemHelper->getMediaFolderUri($this->item->id, Userideas\Constants::ITEM_FOLDER);
        }

        $this->commentLayoutData              = new stdClass;
        $this->commentLayoutData->canEdit     = $this->canEditComment;
        $this->commentLayoutData->returnUrl   = $this->returnUrl;

        $version = new Userideas\Version();
        $this->document->addScript('media/' . $this->option . '/js/site/details_comments.js?v=' . $version->getShortVersion());

        if ($this->canEditComment) {
            $this->initSweetAlert();
        }
    }

    protected function initSweetAlert()
    {
        if (!$this->sweetAlertInitialized) {
            JHtml::_('Prism.ui.sweetAlert');
            JText::script('COM_USERIDEAS_CANCEL');
            JText::script('COM_USERIDEAS_YES_DELETE_IT');
            JText::script('COM_USERIDEAS_ARE_YOU_SURE');
            JText::script('COM_USERIDEAS_CANNOT_RECOVER_FILE');

            $version = new Userideas\Version();
            $this->document->addScript('media/' . $this->option . '/js/site/details.js?v=' . $version->getShortVersion());
            $this->sweetAlertInitialized = true;
        }
    }

    /**
     * Check the system for debug mode
     */
    protected function prepareDebugMode()
    {
        $this->disabledButton = '';

        // Check for maintenance (debug) state
        $params = $this->state->get('params');
        /** @var $params Joomla\Registry\Registry */

        $this->debugMode = $params->get('debug_item_adding_disabled', 0);
        if ($this->debugMode) {
            $msg = Joomla\String\StringHelper::trim($params->get('debug_disabled_functionality_msg'));
            if (!$msg) {
                $msg = JText::_('COM_USERIDEAS_DEBUG_MODE_DEFAULT_MSG');
            }
            $this->app->enqueueMessage($msg, 'notice');

            $this->disabledButton = 'disabled="disabled"';
        }
    }

    /**
     * Prepares the document.
     *
     * @throws InvalidArgumentException
     */
    protected function prepareDocument()
    {
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
        $pathway = $this->app->getPathway();
        $pathway->addItem(JText::_('COM_USERIDEAS_PATHWAY_FORM_TITLE'));

        // Scripts
        JHtml::_('jquery.framework');
        JHtml::_('Prism.ui.pnotify');
    }

    private function preparePageTitle()
    {
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
            $title = $this->app->get('sitename');
        } elseif ((int)$this->app->get('sitename_pagetitles', 0) === 1) {
            $title = JText::sprintf('JPAGETITLE', $this->app->get('sitename'), $title);
        } elseif ((int)$this->app->get('sitename_pagetitles', 0) === 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $this->app->get('sitename'));
        }

        $this->document->setTitle($title);
    }

    private function preparePageHeading()
    {
        $menus = $this->app->getMenu();

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
        if (is_array($this->comments)) {
            $usersIds = Prism\Utilities\ArrayHelper::getIds($this->comments, 'user_id');
        }

        // Add the ID of item owner.
        if ($this->item->user_id > 0) {
            $usersIds[] = $this->item->user_id;
        }
        $usersIds = array_filter(array_unique($usersIds));
        sort($usersIds);

        // If there are no users, do not continue.
        if (count($usersIds) > 0) {
            $this->integrationOptions = array(
                'size'    => $params->get('integration_avatars_size', 'small'),
                'default' => $params->get('integration_avatars_default', '/media/com_userideas/images/no-profile.png')
            );

            $options = new \Joomla\Registry\Registry(array(
                'platform' => $params->get('integration_social_platform'),
                'user_ids' => $usersIds
            ));

            $socialProfilesBuilder = new Prism\Integration\Profiles\Factory($options);
            $this->socialProfiles = $socialProfilesBuilder->create();
        }
    }
}
