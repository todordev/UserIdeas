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

class UserIdeasViewItems extends JViewLegacy
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
    protected $canEdit;
    protected $user;
    protected $userId;
    protected $socialProfiles;
    protected $integrationOptions;
    protected $commentsEnabled;

    protected $option;

    protected $pageclass_sfx;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    public function display($tpl = null)
    {
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $this->params = $this->state->get('params');

        $this->comments = $this->get("Comments");

        $user = JFactory::getUser();
        $this->userId = $user->get("id");

        // Set permission state. Is it possible to be edited items?
        $this->canEdit = $user->authorise('core.edit.own', 'com_userideas');

        $this->items = UserIdeasHelper::prepareStatuses($this->items);

        $this->commentsEnabled = $this->params->get("comments_enabled", 1);

        // Prepare integration. Load avatars and profiles.
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
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }

        // Scripts
        JHtml::_('bootstrap.tooltip');
        JHtml::_('itprism.ui.pnotify');
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
        } elseif ($app->get('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        } elseif ($app->get('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        $this->document->setTitle($title);
    }

    /**
     * Prepare social profiles
     *
     * @param Joomla\Registry\Registry $params
     *
     * @todo Move it to a trait when traits become mass.
     */
    protected function prepareIntegration($params)
    {
        // Get users IDs
        $usersIds = array();
        foreach ($this->items as $item) {
            if (!empty($item->user_id)) {
                $usersIds[] = $item->user_id;
            }
        }
        $usersIds = array_unique($usersIds);

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
