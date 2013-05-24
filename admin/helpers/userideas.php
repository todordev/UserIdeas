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
	    
	    JSubMenuHelper::addEntry(
			JText::_('COM_USERIDEAS_DASHBOARD'),
			'index.php?option='.self::$extension.'&view=dashboard',
			$vName == 'dashboard'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_USERIDEAS_CATEGORIES'),
			'index.php?option=com_categories&extension='.self::$extension.'',
			$vName == 'categories'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_USERIDEAS_ITEMS'),
			'index.php?option='.self::$extension.'&view=items',
			$vName == 'items'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_USERIDEAS_VOTES'),
			'index.php?option='.self::$extension.'&view=votes',
			$vName == 'votes'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_USERIDEAS_COMMENTS'),
			'index.php?option='.self::$extension.'&view=comments',
			$vName == 'comments'
		);
		
		JSubMenuHelper::addEntry(
    		JText::_('COM_USERIDEAS_PLUGINS'),
    		'index.php?option=com_plugins&view=plugins&filter_search='.rawurlencode("user ideas"),
    		$vName == 'plugins'
        );
		
	}
	
	/**
     * Get a category
     * 
     * @params  integer  Category Id
     * @return  array 	 Associative array
     * 
     * @return mixed object or null
     */
    public static function getCategory($categoryId) {
        
        $db     = JFactory::getDBO();
        /** @var $db JDatabaseMySQLi **/
        
        $query  = $db->getQuery(true);
        $query
            ->select(
                "a.title, a.description," . 
                $query->concatenate(array("a.id", "a.alias"), "-") . " AS slug") 
            ->from($db->quoteName("#__categories") . " AS a")
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