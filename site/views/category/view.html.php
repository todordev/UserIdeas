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

class UserideasViewCategory extends JViewLegacy
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

    protected $items;
    protected $pagination;

    protected $category;
    protected $canCreate;
    protected $comments;
    protected $userId;
    protected $socialProfiles;
    protected $integrationOptions = array();
    protected $commentsEnabled;
    protected $tags;

    protected $option;

    protected $version;

    protected $pageclass_sfx;

    public function display($tpl = null)
    {
        $this->option     = JFactory::getApplication()->input->get('option');
        
        $this->items      = $this->get('Items');
        $this->state      = $this->get('State');
        $this->pagination = $this->get('Pagination');

        $this->params     = $this->state->get('params');

        $this->category = new Userideas\Category\Category(JFactory::getDbo());
        $this->category->load($this->state->get('filter.category_id'));

        if ($this->params->get('show_cat_tags', 0)) {
            $this->category->setTagsHelper(new JHelperTags);
            $this->tags = $this->category->getTags();
        }

        $user = JFactory::getUser();
        $this->userId = $user->get('id');

        // Set permission state. Is it possible to be edited items?
        $this->canCreate = $user->authorise('core.create', 'com_userideas') || (count($user->getAuthorisedCategories('com_userideas', 'core.create')));

        if (!$this->category->getId()) {
            $app = JFactory::getApplication();
            /** @var $app JApplicationSite */

            $app->enqueueMessage(JText::_('COM_USERIDEAS_ERROR_INVALID_CATEGORY'), 'notice');
            $app->redirect(JRoute::_('index.php', false));
            return;
        }

        $helpersOptions = array();
        $helperBus      = new Prism\Helper\HelperBus($this->items);
        $helperBus->addCommand(new Userideas\Helper\PrepareParamsHelper());
        $helperBus->addCommand(new Userideas\Helper\PrepareStatusesHelper());
        $helperBus->addCommand(new Userideas\Helper\PrepareAccessHelper(JFactory::getUser()));

        // Set helper command that prepares tags.
        if ($this->params->get('show_tags')) {
            $helpersOptions['content_type']  = 'com_userideas.item';
            $helpersOptions['access_groups'] = \JFactory::getUser()->getAuthorisedViewLevels();

            $helperBus->addCommand(new Userideas\Helper\PrepareTagsHelper());
        }
        $helperBus->handle($helpersOptions);

        $this->prepareComments();
        $this->prepareIntegration($this->params);
        $this->prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepare document
     */
    protected function prepareDocument()
    {
        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        // Prepare page heading
        $this->preparePageHeading();

        // Prepare page heading
        $this->preparePageTitle();

        // Meta Description
        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        // Meta keywords
        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetaData('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetaData('robots', $this->params->get('robots'));
        }

        // Scripts
        JHtml::_('bootstrap.tooltip');
        JHtml::_('Prism.ui.pnotify');
    }

    private function preparePageHeading()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menus = $app->getMenu();
        $menu  = $menus->getActive();

        // Prepare page heading
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_USERIDEAS_ITEMS_DEFAULT_PAGE_TITLE'));
        }
    }

    private function preparePageTitle()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Prepare page title
        $title = $this->params->get('page_title', '');

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

    /**
     * Prepare social profiles
     *
     * @param Joomla\Registry\Registry $params
     */
    protected function prepareIntegration($params)
    {
        // Get users IDs
        $usersIds = array();
        foreach ($this->items as $item) {
            if ($item->user_id) {
                $usersIds[] = $item->user_id;
            }
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

    /**
     * Prepare comments.
     */
    protected function prepareComments()
    {
        if ($this->params->get('comments_enabled') and $this->params->get('show_button_comments')) {
            $itemsIds = array();
            foreach ($this->items as $item) {
                $itemsIds[] = $item->id;
            }

            $this->commentsEnabled = true;

            $comments = new Userideas\Comment\Comments(JFactory::getDbo());
            $this->comments = $comments->advancedCount(array('items_ids' => $itemsIds));
        }
    }
}
