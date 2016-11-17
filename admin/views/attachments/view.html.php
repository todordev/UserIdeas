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

class UserideasViewAttachments extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var JApplicationAdministrator
     */
    public $app;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $state;
    protected $params;

    protected $items;
    protected $pagination;

    protected $option;

    protected $listOrder;
    protected $listDirn;
    protected $saveOrder;
    protected $saveOrderingUrl;
    protected $sortFields;

    public $activeFilters;
    public $filterForm;
    public $mediaFolder;

    protected $sidebar;
    protected $canDo;

    /**
     * @var Userideas\Item\Item
     */
    protected $item;

    public function display($tpl = null)
    {
        $this->app        = JFactory::getApplication();
        $this->option     = $this->app->input->get('option');
        
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        $this->params     = $this->state->get('params');
        $itemId           = $this->state->get('item.id');

        if (!$itemId) {
            $this->app->redirect(JRoute::_('index.php?option=com_userideas&view=items', false));
            return;
        }

        $this->item = new Userideas\Item\Item(JFactory::getDbo());
        $this->item->load($itemId);

        $filesystemHelper  = new Prism\Filesystem\Helper($this->params);
        $this->mediaFolder = $filesystemHelper->getMediaFolderUri($this->item->getId(), Userideas\Constants::ITEM_FOLDER);

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
        JToolbarHelper::title(JText::sprintf('COM_USERIDEAS_ATTACHMENT_MANAGER_', $this->item->getTitle()));

        $this->canDo  = JHelperContent::getActions('com_userideas', 'item', $this->item->getId());

        if ($this->canDo->get('core.delete')) {
            JToolbarHelper::deleteList(JText::_('COM_USERIDEAS_DELETE_ITEMS_QUESTION'), 'attachments.delete');
        }

        JToolbarHelper::divider();

        // Add custom buttons
        $bar = JToolbar::getInstance('toolbar');
        $link = JRoute::_('index.php?option=com_userideas&view=items');
        $bar->appendButton('Link', 'arrow-left-3', JText::_('COM_USERIDEAS_BACK_TO_ITEMS'), $link);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_USERIDEAS_ATTACHMENT_MANAGER'));

        // Scripts
        JHtml::_('behavior.multiselect');
        JHtml::_('bootstrap.tooltip');

        JHtml::_('formbehavior.chosen', 'select');
    }
}
