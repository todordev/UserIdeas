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

class UserideasViewStatus extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $state;

    protected $item;
    protected $form;

    protected $option;
    protected $documentTitle;
    protected $canDo;

    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');
        
        $this->item  = $this->get('Item');
        $this->form  = $this->get('Form');
        $this->state = $this->get('State');

        // Prepare actions, behaviors, scripts and document
        $this->addToolbar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     * @since   1.6
     */
    protected function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $this->canDo  = JHelperContent::getActions('com_userideas');

        $isNew = ((int)$this->item->id === 0);

        $this->documentTitle = $isNew ? JText::_('COM_USERIDEAS_ADD_STATUS') : JText::_('COM_USERIDEAS_EDIT_STATUS');

        JToolbarHelper::title($this->documentTitle);

        if ($isNew and $this->canDo->get('core.create')) {
            JToolbarHelper::apply('status.apply');
            JToolbarHelper::save2new('status.save2new');
            JToolbarHelper::save('status.save');
            JToolbarHelper::cancel('status.cancel', 'JTOOLBAR_CLOSE');
        } else {
            // Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
            if ($this->canDo->get('core.edit')) {
                JToolbarHelper::apply('status.apply');
                JToolbarHelper::save('status.save');

                // We can save this record, but check the create permission to see if we can return to make a new one.
                if ($this->canDo->get('core.create')) {
                    JToolbarHelper::save2new('item.save2new');
                }
            }

            JToolbarHelper::cancel('status.cancel', 'JTOOLBAR_CANCEL');
        }
    }

    /**
     * Method to set up the document properties
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle($this->documentTitle);

        // Add behaviors
        JHtml::_('behavior.tooltip');
        JHtml::_('behavior.formvalidation');

        // Add scripts
        $this->document->addScript('../media/' . $this->option . '/js/admin/' . JString::strtolower($this->getName()) . '.js');
    }
}
