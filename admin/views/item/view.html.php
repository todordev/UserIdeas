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

jimport('joomla.application.component.view');

class UserIdeasViewItem extends JViewLegacy {

    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var JRegistry
     */
    protected $state;

    protected $item;
    protected $form;

    protected $option;
    protected $documentTitle;

    public function __construct($config) {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    /**
     * Display the view
     */
    public function display($tpl = null){
        
        $this->state= $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        
        // Prepare actions, behaviors, scritps and document
        $this->addToolbar();
        $this->setDocument();
        
        parent::display($tpl);
    }
    
    /**
     * Add the page title and toolbar.
     * @since   1.6
     */
    protected function addToolbar(){
        
        JFactory::getApplication()->input->set('hidemainmenu', true);
        $isNew = ($this->item->id == 0);
        
        $this->documentTitle = $isNew ? JText::_('COM_USERIDEAS_ADD_ITEM')
		                              : JText::_('COM_USERIDEAS_EDIT_ITEM');
		                             
        JToolbarHelper::title($this->documentTitle);
		                             
        JToolbarHelper::apply('item.apply');
        JToolbarHelper::save2new('item.save2new');
        JToolbarHelper::save('item.save');
    
        if(!$isNew){
            JToolbarHelper::cancel('item.cancel', 'JTOOLBAR_CANCEL');
        }else{
            JToolbarHelper::cancel('item.cancel', 'JTOOLBAR_CLOSE');
        }
        
    }
    
	/**
	 * Method to set up the document properties
	 * @return void
	 */
	protected function setDocument() {
	    
		$this->document->setTitle($this->documentTitle);
		
		// Add behaviors
        JHtml::_('behavior.tooltip');
        JHtml::_('behavior.formvalidation');
        
        JHtml::_('formbehavior.chosen', 'select');
        
		// Add scripts
		$this->document->addScript(JURI::root() . 'media/'.$this->option.'/js/admin/'.JString::strtolower($this->getName()).'.js');
	}
	

}