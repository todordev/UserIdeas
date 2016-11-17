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

class UserideasViewComments extends JViewLegacy
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
    protected $canDo;

    public $activeFilters;
    public $filterForm;

    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');
        
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $helperBus     = new Prism\Helper\HelperBus($this->items);
        $helperBus->addCommand(new Userideas\Helper\PrepareAttachmentsNumberHelper());
        $helperBus->handle(['type' => 'comment']);

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

        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        UserideasHelper::addSubmenu($this->getName());

        JHtmlSidebar::setAction('index.php?option=' . $this->option . '&view=' . $this->getName());

        JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_state',
            JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived' => false, 'trash' => false)), 'value', 'text', $this->state->get('filter.state'), true)
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
        $this->canDo = JHelperContent::getActions('com_userideas');

        // Set toolbar items for the page
        JToolbarHelper::title(JText::_('COM_USERIDEAS_COMMENTS_MANAGER'));

        if ($this->canDo->get('core.edit')) {
            JToolbarHelper::editList('comment.edit');
        }
        if ($this->canDo->get('core.edit.state')) {
            JToolbarHelper::publishList('comments.publish');
            JToolbarHelper::unpublishList('comments.unpublish');
        }

        if ($this->canDo->get('core.delete')) {
            JToolbarHelper::deleteList(JText::_('COM_USERIDEAS_DELETE_ITEMS_QUESTION'), 'comments.delete');
        }

        JToolbarHelper::custom('comments.backToDashboard', 'dashboard', '', JText::_('COM_USERIDEAS_DASHBOARD'), false);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_USERIDEAS_COMMENTS_MANAGER'));

        // Scripts
        JHtml::_('behavior.multiselect');
        JHtml::_('bootstrap.tooltip');

        JHtml::_('formbehavior.chosen', 'select');
    }
}
