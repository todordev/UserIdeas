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

    protected $items;
    protected $pagination;

    protected $option;

    protected $listOrder;
    protected $listDirn;
    protected $saveOrder;
    protected $saveOrderingUrl;
    protected $sortFields;

    protected $sidebar;

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

        $this->items = UserIdeasHelper::prepareStatuses($this->items);

        // Add submenu
        UserIdeasHelper::addSubmenu($this->getName());

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
        $this->saveOrder = (strcmp($this->listOrder, 'a.ordering') != 0) ? false : true;

        if ($this->saveOrder) {
            $this->saveOrderingUrl = 'index.php?option=' . $this->option . '&task=' . $this->getName() . '.saveOrderAjax&format=raw';
            JHtml::_('sortablelist.sortable', $this->getName() . 'List', 'adminForm', strtolower($this->listDirn), $this->saveOrderingUrl);
        }

        $this->sortFields = array(
            'a.title'       => JText::_('COM_USERIDEAS_TITLE'),
            'a.published'   => JText::_('JSTATUS'),
            'a.record_date' => JText::_('COM_USERIDEAS_CREATED'),
            'a.votes'       => JText::_('COM_USERIDEAS_VOTES'),
            'b.name'        => JText::_('COM_USERIDEAS_USER'),
            'c.title'       => JText::_('COM_USERIDEAS_CATEGORY'),
            'a.id'          => JText::_('JGRID_HEADING_ID')
        );

    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        JHtmlSidebar::setAction('index.php?option=' . $this->option . '&view=' . $this->getName());

        JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_state',
            JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array("archived" => false, "trash" => false)), 'value', 'text', $this->state->get('filter.state'), true)
        );

        JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_CATEGORY'),
            'filter_category',
            JHtml::_('select.options', JHtml::_('category.options', 'com_userideas'), 'value', 'text', $this->state->get('filter.category'), true)
        );

        // Item statuses
        jimport("userideas.statuses");
        $statuses = UserIdeasStatuses::getInstance(JFactory::getDbo());
        JHtmlSidebar::addFilter(
            JText::_('COM_USERIDEAS_SELECT_ITEM_STATUS'),
            'filter_status',
            JHtml::_('select.options', $statuses->getStatusesOptions(), 'value', 'text', $this->state->get('filter.status'), true)
        );

        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        // Set toolbar items for the page
        JToolbarHelper::title(JText::_('COM_USERIDEAS_ITEMS_MANAGER'), 'items');
        JToolbarHelper::addNew('item.add');
        JToolbarHelper::editList('item.edit');
        JToolbarHelper::divider();
        JToolbarHelper::publishList("items.publish");
        JToolbarHelper::unpublishList("items.unpublish");
        JToolbarHelper::divider();
        JToolbarHelper::deleteList(JText::_("COM_USERIDEAS_DELETE_ITEMS_QUESTION"), "items.delete");
        JToolbarHelper::divider();
        JToolbarHelper::custom('items.backToDashboard', "dashboard", "", JText::_("COM_USERIDEAS_DASHBOARD"), false);
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

        JHtml::_('itprism.ui.joomla_list');
    }
}
