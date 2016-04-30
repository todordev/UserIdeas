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

class UserideasViewItems extends JViewLegacy
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

    protected $comments;
    protected $canCreate;
    protected $user;
    protected $userId;
    protected $socialProfiles;
    protected $integrationOptions = array();
    protected $commentsEnabled = false;

    protected $option;

    protected $pageclass_sfx;

    public function display($tpl = null)
    {
        $app              = JFactory::getApplication();

        $this->option     = $app->input->get('option');
        
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $this->params     = $this->state->get('params');

        $user = JFactory::getUser();
        $this->userId = $user->get('id');

        // Check access view.
        if (!$this->canView($app, $user)) {
            $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            $app->setHeader('status', 403, true);
            return;
        }
        
        // Set permission state. Is it possible to be edited items?
        $this->canCreate = $user->authorise('core.create', 'com_userideas') || (count($user->getAuthorisedCategories('com_userideas', 'core.create')));

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
     * Prepare social profiles.
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

            $options = new \Joomla\Registry\Registry(array(
                'platform' => $params->get('integration_social_platform'),
                'user_ids' => $usersIds
            ));
            $factory = new Prism\Integration\Profiles\Factory($options);
            $this->socialProfiles = $factory->create();
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

    /**
     * Check the access view.
     *
     * @param JApplicationCms $app
     * @param JUser $user
     *
     * @return bool
     * @throws Exception
     */
    protected function canView($app, JUser $user)
    {
        $activeMenu = $app->getMenu()->getActive();
        $groups     = $user->getAuthorisedViewLevels();

        $canView    = false;
        if ($activeMenu !== null) {
            $canView = in_array((int)$activeMenu->access, $groups, true);
        }

        return $canView;
    }
}
