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

class UserideasViewCategories extends JViewLegacy
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

    protected $categories;
    protected $user;
    protected $userId;

    protected $option;
    protected $event;
    protected $showTitle;
    protected $showImages;
    protected $showDescription;
    protected $templateLayout;
    protected $numberOfItems;
    protected $showItemsNumber;
    protected $showSubcategories;

    protected $pageclass_sfx;

    public function display($tpl = null)
    {
        $app              = JFactory::getApplication();

        $this->option     = $app->input->get('option');
        
        $this->state      = $this->get('State');
//        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $this->params     = $this->state->get('params');

        $user         = JFactory::getUser();
        $this->userId = $user->get('id');

        // Check access view.
        if (!$this->canView($app, $user)) {
            $app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
            $app->setHeader('status', 403, true);
            return;
        }

        $parentCategory     = null;
        $this->activeItemId = 0;

        // Get parent ID.
        $parentId = (int)$app->input->getInt('id');
        $parentId = $parentId ?: Prism\Constants::CATEGORY_ROOT;

        // Get the categories.
        $items = new Userideas\Category\Categories();
        if ($parentId === Prism\Constants::CATEGORY_ROOT) {
            $root       = $items->get();
            $categories = $root->getChildren();
        } else {
            $parentCategory   = $items->get($parentId);
            $categories       = $parentCategory->getChildren();
            if (count($categories) === 0) {
                $parentCategory   = $parentCategory->getParent();
                if ($parentCategory !== null) {
                    $categories       = $parentCategory->getChildren();
                }
            }
        }

        $helpersOptions = array();
        $helperBus      = new Prism\Helper\HelperBus($categories);
        $helperBus->addCommand(new Userideas\Helper\PrepareParamsHelper());
        $helperBus->handle($helpersOptions);

        $this->items = $categories;

        $this->showTitle         = $this->params->get('show_categories_title', Prism\Constants::NO);
        $this->showImages        = $this->params->get('show_categories_image', Prism\Constants::NO);
        $this->showDescription   = $this->params->get('show_categories_description', Prism\Constants::NO);
        $this->showSubcategories = $this->params->get('show_categories_subcategories', Prism\Constants::NO);

        // Get number of items.
        $this->numberOfItems   = array();
        $this->showItemsNumber = $this->params->get('show_categories_items_number', Prism\Constants::NO);
        if ($this->showItemsNumber) {
            $ids = Prism\Utilities\ArrayHelper::getIds($categories);
            if ($parentCategory !== null) {
                $ids[] = (int)$parentCategory->id;
            }

            $statistics    = new Userideas\Statistic\Basic(\JFactory::getDbo());
            $this->numberOfItems = $statistics->getCategoryItems($ids);
        }

        // Set the layout.
        $templateLayout       = $this->params->get('categories_layout', 'boxes');
        $this->templateLayout = in_array($templateLayout, ['boxes', 'list'], true) ? $templateLayout : 'boxes';

        // Import content plugins
        JPluginHelper::importPlugin('content');

        // Events
        $dispatcher  = JEventDispatcher::getInstance();
        $this->event = new stdClass();

        $results                                 = $dispatcher->trigger('onContentBeforeDisplay', array('com_userideas.categories', &$this->items, &$this->params));
        $this->event->onContentBeforeDisplay     = trim(implode("\n", $results));

        $results                                 = $dispatcher->trigger('onContentAfterDisplay', array('com_userideas.categories', &$this->items, &$this->params));
        $this->event->onContentAfterDisplay      = trim(implode("\n", $results));

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
            $this->params->def('page_heading', JText::_('COM_USERIDEAS_CATEGORIES_DEFAULT_PAGE_TITLE'));
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
