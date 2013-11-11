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

/**
 * It is UserIdeas helper class
 */
class UserIdeasHelper {
	
    static $extension  = "com_userideas";
      
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 * @since	1.6
	 */
	public static function addSubmenu($vName = 'dashboard') {
	    
	    JHtmlSidebar::addEntry(
			JText::_('COM_USERIDEAS_DASHBOARD'),
			'index.php?option='.self::$extension.'&view=dashboard',
			$vName == 'dashboard'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_USERIDEAS_CATEGORIES'),
			'index.php?option=com_categories&extension='.self::$extension.'',
			$vName == 'categories'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_USERIDEAS_ITEMS'),
			'index.php?option='.self::$extension.'&view=items',
			$vName == 'items'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_USERIDEAS_VOTES'),
			'index.php?option='.self::$extension.'&view=votes',
			$vName == 'votes'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_USERIDEAS_COMMENTS'),
			'index.php?option='.self::$extension.'&view=comments',
			$vName == 'comments'
		);
		
		JHtmlSidebar::addEntry(
			JText::_('COM_USERIDEAS_STATUSES'),
			'index.php?option='.self::$extension.'&view=statuses',
			$vName == 'statuses'
		);
		
		JHtmlSidebar::addEntry(
    		JText::_('COM_USERIDEAS_EMAILS'),
    		'index.php?option='.self::$extension.'&view=emails',
    		$vName == 'emails'
        );
		
		JHtmlSidebar::addEntry(
    		JText::_('COM_USERIDEAS_PLUGINS'),
    		'index.php?option=com_plugins&view=plugins&filter_search='.rawurlencode("user ideas"),
    		$vName == 'plugins'
        );
		
	}
	
	/**
     * Get a category.
     * 
     * @params  integer  Category Id
     * 
     * @return  object|null
     */
    public static function getCategory($categoryId) {
        
        $db     = JFactory::getDBO();
        /** @var $db JDatabaseMySQLi **/
        
        $query  = $db->getQuery(true);
        $query
            ->select(
                "a.title, a.description," . 
                $query->concatenate(array("a.id", "a.alias"), "-") . " AS slug") 
            ->from($db->quoteName("#__categories", "a"))
            ->where("a.id = ". (int)$categoryId)
            ->where("a.extension = ". $db->quote("com_userideas"));
    	
        $db->setQuery($query);
        $category = $db->loadObject();
        
        if(!$category) {
            $category = null;
        }
        
        return $category;
    }
    
}