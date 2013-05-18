<?php
/**
 * @package      ITPrism Components
 * @subpackage   UserIdeas
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * UserIdeas is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.categories');
jimport('joomla.application.component.view');

class UserIdeasViewDetails extends JView {
    
    protected $state      = null;
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
        $this->item       = $this->get('Item');
        $this->params     = $this->state->get("params");
        
        $this->category   = UserIdeasHelper::getCategory($this->item->catid);
        
        $this->userId     = JFactory::getUser()->id;
        
        // Get the model of the comments
        // that I will use to load all comments for this item.
        $model            = JModel::getInstance("Comments", "UserIdeasModel");
        $this->comments   = $model->getItems();
        
        // Get the model of the comment
        $modelForm        = JModel::getInstance("Comment", "UserIdeasModel");
        
        // Validate the owner of the comment,
        // If someone wants to edit it.
        $commentId        = $modelForm->getState("comment_id");
        if(!empty($commentId)) {
            
            try {
                $item         = $modelForm->getItem($commentId, $this->userId);
            } catch (Exception $e) {
                
                $app->enqueueMessage(JText::_("COM_USERIDEAS_ERROR_INVALID_COMMENT"), "error");
                $app->redirect( JRoute::_(UserIdeasHelperRoute::getDetailsRoute($item->slug, $item->catid), false) );
                return; 

            }
            
        }
        
        // Get comment form
        $this->form       = $modelForm->getForm();
        
        $this->version    = new UserIdeasVersion();
        
        $this->socialPlatform   = $this->params->get("integration_social_platform");
        
        JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
        
        $this->prepareDebugMode();
        $this->prepareDocument();
        
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
        
        // Prepare page heading
        $this->prepearePageHeading();
        
        // Prepare page heading
        $this->prepearePageTitle();
        
        // Meta Description
        $this->document->setDescription($this->params->get('menu-meta_description'));
        
        // Meta keywords
        $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        
        // Add current layout into breadcrumbs 
        $pathway    = $app->getPathway();
        $pathway->addItem(JText::_("COM_USERIDEAS_PATHWAY_FORM_TITLE"));
        
        // Styles
        $this->document->addStyleSheet(JURI::root() . 'media/'.$this->option.'/css/site/bootstrap.min.css');
        $this->document->addStyleSheet('media/'.$this->option.'/css/site/style.css');
        
        // Scripts
        JHtml::_('behavior.keepalive');
		        
    }

    private function prepearePageTitle() {
        
        $app        = JFactory::getApplication();
        /** @var $app JSite **/
        
        // If it is assigned to menu item, params will conatins "page_title".
        // If it is not assigned, I will use the title of the item
        if($this->params->get("page_title")) {
            $title = $this->params->get("page_title");
        } else {
        
            $seo = $this->params->get("seo_cat_to_title");
            
            switch($seo) {
    	        
    	        case "1": // Before page title
    	            $title = $this->category->title . " | " . $this->item->title;
    	            break;
    	            
                case "2": // After page title
    	            $title = $this->item->title . " | " . $this->category->title;
    	            break;
    	            
    	        default: // NONE
    	            $title = $this->item->title;
                    break;
    	    }
        }
            
		
        // Add title before or after Site Name
        if(!$title){
            $title = $app->getCfg('sitename');
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		} elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		
        $this->document->setTitle($title);
		
    }
    
    private function prepearePageHeading() {
        
        $app         = JFactory::getApplication();
        /** @var $app JSite **/
        
        $menus       = $app->getMenu();
        
        // Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu        = $menus->getActive();
		
        if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', $this->item->title);
		}
		
    }
    
}