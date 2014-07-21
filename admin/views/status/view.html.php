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

class UserIdeasViewStatus extends JViewLegacy
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

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
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
        $isNew = ($this->item->id == 0);

        $this->documentTitle = $isNew ? JText::_('COM_USERIDEAS_ADD_STATUS')
            : JText::_('COM_USERIDEAS_EDIT_STATUS');

        JToolbarHelper::title($this->documentTitle);

        JToolbarHelper::apply('status.apply');
        JToolbarHelper::save2new('status.save2new');
        JToolbarHelper::save('status.save');

        if (!$isNew) {
            JToolbarHelper::cancel('status.cancel', 'JTOOLBAR_CANCEL');
        } else {
            JToolbarHelper::cancel('status.cancel', 'JTOOLBAR_CLOSE');
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
