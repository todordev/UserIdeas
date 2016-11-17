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

    protected $items;
    protected $pagination;

    protected $option;

    protected $listOrder;
    protected $listDirn;
    protected $saveOrder;
    protected $saveOrderingUrl;

    public $activeFilters;
    public $filterForm;

    protected $sidebar;

    public function display($tpl = null)
    {
        $this->option     = JFactory::getApplication()->input->get('option');
        
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $helperBus     = new Prism\Helper\HelperBus($this->items);
        $helperBus->addCommand(new Userideas\Helper\PrepareStatusesHelper());
        $helperBus->addCommand(new Userideas\Helper\PrepareAttachmentsNumberHelper());
        $helperBus->addCommand(new Userideas\Helper\PrepareCommentsNumberHelper());
        $helperBus->handle(['type' => 'item']);

        // Prepare sorting data
        $this->prepareSorting();

        // Prepare actions
        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Prepare sortable fields, sort values and filters.
     */
    protected function prepareSorting()
    {
        // Prepare filters
        $this->listOrder = $this->escape($this->state->get('list.ordering'));
        $this->listDirn  = $this->escape($this->state->get('list.direction'));
        $this->saveOrder = (strcmp($this->listOrder, 'a.ordering') === 0);

        if ($this->saveOrder) {
            $this->saveOrderingUrl = 'index.php?option=' . $this->option . '&task=' . $this->getName() . '.saveOrderAjax&format=raw';
            JHtml::_('sortablelist.sortable', $this->getName() . 'List', 'adminForm', strtolower($this->listDirn), $this->saveOrderingUrl);
        }

        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        UserideasHelper::addSubmenu($this->getName());

        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        $canDo = JHelperContent::getActions('com_userideas', 'category', $this->state->get('filter.category'));
        $user  = JFactory::getUser();

        // Set toolbar items for the page
        JToolbarHelper::title(JText::_('COM_USERIDEAS_ITEMS_MANAGER'), 'items');

        if ($canDo->get('core.create') or (count($user->getAuthorisedCategories('com_userideas', 'core.create'))) > 0) {
            JToolbarHelper::addNew('item.add');
        }

        if ($canDo->get('core.edit') or $canDo->get('core.edit.own')) {
            JToolbarHelper::editList('item.edit');
        }

        if ($canDo->get('core.edit.state')) {
            JToolbarHelper::publishList('items.publish');
            JToolbarHelper::unpublishList('items.unpublish');
        }

        if ((int)$this->state->get('filter.state') === -2 and $canDo->get('core.delete')) {
            JToolbarHelper::deleteList(JText::_('COM_USERIDEAS_DELETE_ITEMS_QUESTION'), 'items.delete', 'JTOOLBAR_EMPTY_TRASH');
        } elseif ($canDo->get('core.edit.state')) {
            JToolbarHelper::trash('items.trash');
        }

        JToolbarHelper::divider();
        JToolbarHelper::custom('items.backToDashboard', 'dashboard', '', JText::_('COM_USERIDEAS_DASHBOARD'), false);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_USERIDEAS_ITEMS_MANAGER'));

        // Scripts
        JHtml::_('bootstrap.tooltip');
        JHtml::_('behavior.multiselect');

        JHtml::_('formbehavior.chosen', 'select');
    }
}
