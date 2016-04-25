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

class UserideasViewStatuses extends JViewLegacy
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
    protected $canDo;

    protected $sidebar;

    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');
        
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

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

        $this->sortFields = array(
            'a.name' => JText::_('COM_USERIDEAS_NAME'),
            'a.id'   => JText::_('JGRID_HEADING_ID')
        );
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
        $this->canDo = JHelperContent::getActions('com_userideas');

        JToolbarHelper::title(JText::_('COM_USERIDEAS_STATUSES_MANAGER'));

        if ($this->canDo->get('core.create')) {
            JToolbarHelper::addNew('status.add');
        }

        if ($this->canDo->get('core.edit')) {
            JToolbarHelper::editList('status.edit');
        }

        if ($this->canDo->get('core.edit.state')) {
            JToolbarHelper::makeDefault('statuses.setDefault', 'COM_USERIDEAS_DEFAULT');
        }

        if ($this->canDo->get('core.delete')) {
            JToolbarHelper::deleteList(JText::_('COM_USERIDEAS_DELETE_ITEMS_QUESTION'), 'statuses.delete');
        }

        JToolbarHelper::custom('statuses.backToDashboard', 'dashboard', '', JText::_('COM_USERIDEAS_DASHBOARD'), false);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_USERIDEAS_STATUSES_MANAGER'));

        // Scripts
        JHtml::_('behavior.multiselect');
        JHtml::_('bootstrap.tooltip');

        JHtml::_('formbehavior.chosen', 'select');

        JHtml::_('Prism.ui.joomlaList');
    }
}
