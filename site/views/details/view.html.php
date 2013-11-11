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

class UserIdeasViewDetails extends JViewLegacy {
    
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
        $model            = JModelLegacy::getInstance("Comments", "UserIdeasModel");
        $this->comments   = $model->getItems();
        
        // Get the model of the comment
        $modelForm        = JModelLegacy::getInstance("Comment", "UserIdeasModel");
        
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
        
        // Get users IDs
        $usersIds = array();
        foreach($this->comments as $comment) {
            $usersIds[] = $comment->user_id;
        }
        $usersIds[] = $this->item->user_id;
        $usersIds   = array_unique($usersIds);
        
        // Prepare integration. Load avatars and profiles.
        $this->prepareIntegration($usersIds, $this->params);
        
        // Prepare the link to the details page.
        $this->item->link       = UserIdeasHelperRoute::getDetailsRoute($this->item->slug, $this->item->catslug);
        $this->item->text       = $this->item->description;
        
        // HTML Helpers
		JHtml::addIncludePath(ITPRISM_PATH_LIBRARY.'/ui/helpers');
		JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
        
        $this->prepareDebugMode();
        $this->prepareDocument();
        
        // Events
        JPluginHelper::importPlugin('content');
        $dispatcher	       = JEventDispatcher::getInstance();
        $this->item->event = new stdClass();
        $offset            = 0;
        
        $dispatcher->trigger('onContentPrepare', array ('com_userideas.details', &$this->item, &$this->params, $offset));
        
        $results           = $dispatcher->trigger('onContentBeforeDisplay', array('com_userideas.details', &$this->item, &$this->params, $offset));
        $this->item->event->beforeDisplayContent = trim(implode("\n", $results));
        
        $results           = $dispatcher->trigger('onContentAfterDisplay', array('com_userideas.details', &$this->item, &$this->params, $offset));
        $this->item->event->onContentAfterDisplay = trim(implode("\n", $results));
        
        $this->item->description = $this->item->text;
        unset($this->item->text);
        
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
        $this->document->addStyleSheet('media/'.$this->option.'/css/site/style.css');
        
        // Scripts
        JHtml::_('behavior.keepalive');
        
        JHtml::_('jquery.framework');
        JHtml::_('itprism.ui.pnotify');
        
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
    
    /**
     * Prepare social profiles.
     *
     * @param array     $usersIds
     * @param JRegistry $params
     *
     * @todo Move it to a trait when traits become mass.
     */
    protected function prepareIntegration($usersIds, $params) {

        // Get a social platform for integration
        $socialPlatform         = $params->get("integration_social_platform");
        $this->socialProfiles   = null;
        
        $this->avatarsSize      = $params->get("integration_avatars_size", 50);
        $this->defaultAvatar    = $params->get("integration_avatars_default", "/media/com_crowdfunding/images/no-profile.png");
        
        // If there is now users, do not continue.
        if(!$usersIds) {
            return;
        }
        
        // Load the class
        if(!empty($socialPlatform)) {
            jimport("itprism.integrate.profiles");
            $this->socialProfiles   =  ITPrismIntegrateProfiles::factory($socialPlatform, $usersIds);
        }
    
    }
    
}