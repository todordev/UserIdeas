<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.categories');
jimport('joomla.application.component.view');

class UserIdeasViewForm extends JViewLegacy {
    
    protected $state      = null;
    protected $form       = null;
    protected $params     = null;
    protected $item       = null;
    
    protected $option     = null;
    
    public function __construct($config){
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->getCmd("option");
    }
    
    /**
     * Display the view
     * @todo add ACL
     */
    public function display($tpl = null){
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Initialise variables
        $this->state      = $this->get('State');
        $this->form       = $this->get('Form');
        $this->params     = $this->state->get("params");
        
        $userId           = JFactory::getUser()->id;
        if(!$userId) {
		    $app->enqueueMessage(JText::_("COM_USERIDEAS_ERROR_NOT_LOG_IN"), "notice");
            $app->redirect(JRoute::_('index.php?option=com_users&view=login', false));
            return; 
        }
        
        $itemId = $this->state->get("form.id");
        if(!empty($itemId)) {
            
            $db   = JFactory::getDbo();
            
            jimport("userideas.item");
            $item = new UserIdeasItem($itemId);
            
            if(!$item->isValid($itemId, $userId)) {
    		    $app->enqueueMessage(JText::_("COM_USERIDEAS_ERROR_INVALID_ITEM"), "notice");
                $app->redirect( JRoute::_('index.php', false) );
                return; 
            }
            
        }
        
        $this->prepareDebugMode();
        $this->prepareDocument();
        
        $this->version   = new UserIdeasVersion();
        
        parent::display($tpl);
    }
    
    /**
     * Check the system for debug mode
     */
    protected function prepareDebugMode() {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/

        $this->disabledButton = "";
        
        // Check for maintenance (debug) state
        $params = $this->state->get("params");
        $this->debugMode = $params->get("debug_item_adding_disabled", 0);
        if($this->debugMode) {
            $msg = JString::trim($params->get("debug_disabled_functionality_msg"));
            if(!$msg) {
                $msg = JText::_("COM_USERIDEAS_DEBUG_MODE_DEFAULT_MSG");
            }
            $app->enqueueMessage($msg, "notice");
            
            $this->disabledButton = 'disabled="disabled"';
        }
        
    }
    
    
    /**
     * Prepares the document
     */
    protected function prepareDocument(){

        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
         // Prepare page suffix
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
        
        $menus = $app->getMenu();
        
        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu  = $menus->getActive();
        
        if($menu) {
            $this->params->def('page_heading', $menu->title);
        } else {
            $this->params->def('page_heading', JText::_('COM_USERIDEAS_FROM_DEFAULT_PAGE_TITLE'));
        }
        
        // Prepare page title
        $title = $menu->title;
        if(!$title){
            $title = $app->getCfg('sitename');
        }elseif($app->getCfg('sitename_pagetitles', 0)){ // Set site name if it is necessary ( the option 'sitename' = 1 )
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        }
            
        $this->document->setTitle($title);
        
        // Meta Description
        $this->document->setDescription($this->params->get('menu-meta_description'));
        
        // Meta keywords
        $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        
        // Add current layout into breadcrumbs 
        $pathway    = $app->getPathway();
        $pathway->addItem(JText::_("COM_USERIDEAS_PATHWAY_FORM_TITLE"));
        
        // Styles
        $this->document->addStyleSheet('media/'.$this->option.'/css/site/style.css');
        
        // Scripts
        JHtml::_('bootstrap.framework');
        JHtml::_('bootstrap.tooltip');
        JHtml::_('formbehavior.chosen', 'select');
        
        JHtml::_('behavior.keepalive');
        JHtml::_('behavior.formvalidation');
        
    }

}