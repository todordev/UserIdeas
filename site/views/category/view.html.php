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

class UserIdeasViewCategory extends JViewLegacy {

    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var JRegistry
     */
    protected $state;

    /**
     * @var JRegistry
     */
    protected $params;

	protected $items;
	protected $pagination;

    protected $category;
    protected $comments;
    protected $userId;
    protected $socialProfiles;
    protected $avatarsSize;
    protected $defaultAvatar;

    protected $option;

    protected $version;

    protected $pageclass_sfx;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    public function display($tpl = null) {
        
        $this->items      = $this->get('Items');
		$this->state	  = $this->get('State');
		$this->pagination = $this->get('Pagination');
		
		$this->params	  = $this->state->get('params');
        
		$categoryId       = $this->state->get("filter.category_id");
        jimport("userideas.category");
		$this->category   = new UserIdeasCategory($categoryId);
        $this->category->setDb(JFactory::getDbo());
        $this->category->load();
		
		$this->comments   = $this->get("Comments");
		
		$this->userId     = JFactory::getUser()->get("id");
		
		if(empty($this->category)) {
		    
		    $app = JFactory::getApplication();
		    /** @var $app JApplicationSite **/
		    
		    $app->enqueueMessage(JText::_("COM_USERIDEAS_ERROR_INVALID_CATEGORY"), "notice");
            $app->redirect( JRoute::_('index.php', false) );
            return; 
		}

        $this->items      = UserIdeasHelper::prepareStatuses($this->items);

		// Prepare integration. Load avatars and profiles.
		$this->prepareIntegration($this->params);

        $this->version    = new UserIdeasVersion();

        $this->prepareDocument();
                
        parent::display($tpl);
    }
    
    /**
     * Prepare document
     */
    protected function prepareDocument(){
        
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
        /** @var $app JApplicationSite **/
        
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
            $title = $app->get('sitename');
        } elseif ($app->get('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		} elseif ($app->get('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}
		
        $this->document->setTitle($title);
		
    }
    
    /**
     * Prepare social profiles
     *
     * @param JRegistry $params
     *
     * @todo Move it to a trait when traits become mass.
     */
    protected function prepareIntegration($params) {

        // Get users IDs
        $usersIds = array();
        foreach($this->items as $item) {
            $usersIds[] = $item->user_id;
        }
        $usersIds   = array_unique($usersIds);

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