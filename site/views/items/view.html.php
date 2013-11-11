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

jimport('joomla.application.component.view');

class UserIdeasViewItems extends JViewLegacy {
    
	protected $state;
	protected $items;
	protected $params;
	
	protected $pagination = null;
	
    protected $option;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    public function display($tpl = null) {
        
		$this->state	  = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		
		$this->params	  = $this->state->get('params');
        
		$this->comments   = $this->get("Comments");
		
		$this->userId     = JFactory::getUser()->id;
		
		// Get users IDs
		$usersIds = array();
		foreach($this->items as $item) {
		    $usersIds[] = $item->user_id;
		}
		$usersIds   = array_unique($usersIds);
		
		// Prepare integration. Load avatars and profiles.
		$this->prepareIntegration($usersIds, $this->params);
		
		// HTML Helpers
		JHtml::addIncludePath(ITPRISM_PATH_LIBRARY.'/ui/helpers');
		JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
		
		$this->version   = new UserIdeasVersion();
		
        $this->prepareDocument();
                
        parent::display($tpl);
    }
    
    /**
     * Prepare document
     */
    protected function prepareDocument(){
        
        $app       = JFactory::getApplication();
        
        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
        
        // Prepare page heading
        $this->prepearePageHeading();
        
        // Prepare page heading
        $this->prepearePageTitle();
        
        // Meta Description
        if($this->params->get('menu-meta_description')){
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }
        
        // Meta keywords
        if($this->params->get('menu-meta_keywords')){
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }
        
        if ($this->params->get('robots')){
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		
        // Styles
        $this->document->addStyleSheet('media/'.$this->option.'/css/site/style.css');

        // Scripts
        JHtml::_('bootstrap.tooltip');
        JHtml::_('itprism.ui.pnotify');
    }
    
    private function prepearePageHeading() {
        
        $app      = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menus    = $app->getMenu();
		$menu     = $menus->getActive();
		
		// Prepare page heading
        if($menu){
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        }else{
            $this->params->def('page_heading', JText::_('COM_USERIDEAS_ITEMS_DEFAULT_PAGE_TITLE'));
        }
		
    }
    
    private function prepearePageTitle() {
        
        $app      = JFactory::getApplication();
        /** @var $app JSite **/
        
		// Prepare page title
        $title    = $this->params->get('page_title', '');
        
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
    
    /**
     * Prepare social profiles
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
        
        $this->avatarsSize           = $params->get("integration_avatars_size", 50);
        $this->defaultAvatar         = $params->get("integration_avatars_default", "/media/com_crowdfunding/images/no-profile.png");
        
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