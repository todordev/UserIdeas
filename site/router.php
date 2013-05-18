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

defined('_JEXEC') or die;

// Load router
if(!class_exists("UserIdeasHelperRoute")) {
    JLoader::register("UserIdeasHelperRoute", JPATH_SITE . DIRECTORY_SEPARATOR. "components" . DIRECTORY_SEPARATOR ."com_userideas". DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "route.php");
}

/**
 * Method to build Route
 * @param array $query
 */
function UserIdeasBuildRoute(&$query){
    
    $segments = array();
    
    // get a menu item based on Itemid or currently active
    $app  = JFactory::getApplication();
    $menu = $app->getMenu();
    
    // we need a menu item.  Either the one specified in the query, or the current active one if none specified
    if(empty($query['Itemid'])){
        $menuItem = $menu->getActive();
    }else{
        $menuItem = $menu->getItem($query['Itemid']);
    }
    
    $mOption	= (empty($menuItem->query['option'])) ? null : $menuItem->query['option'];
    $mView	    = (empty($menuItem->query['view']))   ? null : $menuItem->query['view'];
	$mCatid	    = (empty($menuItem->query['catid']))  ? null : $menuItem->query['catid'];
	$mId	    = (empty($menuItem->query['id']))     ? null : $menuItem->query['id'];
	
	// If is set view and Itemid missing, we have to put the view to the segments
	if (isset($query['view'])) {
		$view = $query['view'];
		
		if (empty($query['Itemid']) OR ($mOption !== "com_userideas")) {
			$segments[] = $query['view'];
		}

		// We need to keep the view for forms since they never have their own menu item
		if ($view != 'form') {
			unset($query['view']);
		}
	};
    
    // are we dealing with a category that is attached to a menu item?
	if (isset($view) AND ($mView == $view) AND (isset($query['id'])) AND ($mId == intval($query['id']))) {
		unset($query['view']);
		unset($query['catid']);
		unset($query['id']);
		return $segments;
	}
	
    // Views
	if(isset($view)) {
	    
    	switch($view) {
    	    
	       case "details":
	            
    	        $catId      = $query['catid'];
    	        UserIdeasHelperRoute::prepareCategoriesSegments($catId, $segments, $mId);
    	        
    	        $id         = $query['id'];
				$segments[] = $id;
				
				unset($query['id']);
	            unset($query['catid']);
	            
    	        break;
	        
    	}
        
	}
    
	// Layout
    if (isset($query['layout'])) {
		if (!empty($query['Itemid']) && isset($menuItem->query['layout'])) {
			if ($query['layout'] == $menuItem->query['layout']) {
				unset($query['layout']);
			}
		} else {
			if ($query['layout'] == 'default') {
				unset($query['layout']);
			}
		}
	};
	
    
    return $segments;
}

/**
 * Method to parse Route
 * @param array $segments
 */
function UserIdeasParseRoute($segments){
    
    $vars = array();
    
    //Get the active menu item.
    $app        = JFactory::getApplication();
    $menu       = $app->getMenu();
    $item       = $menu->getActive();
    
    // Count route segments
    $count      = count($segments);
    
    // Standard routing for item.  If we don't pick up an Itemid then we get the view from the segments
	// the first segment is the view and the last segment is the id of the details, category,...
    if(!isset($item)) {
        $vars['view']   = $segments[0];
        $vars['id']     = $segments[$count - 1];
        return $vars;
    } 
    
    // Details 
	if($count == 1) { 
	    $vars['view']   = 'details';
		$vars['id']     = (int)$segments[0];
		return $vars;
	}
	
    // If there was more than one segment, then we can determine where the URL points to
	// because the first segment will have the target item id prepended to it.  If the
	// last segment has a number prepended, it is details, otherwise, it is a category.
	$catId     = (int)$segments[0];
	$itemId    = (int)$segments[$count - 1];

	if ($itemId > 0) {
		$vars['view']   = 'details';
		$vars['catid']  = $catId;
		$vars['id']     = $itemId;
	} else {
		$vars['view']   = 'items';
		$vars['id']     = $catId;
	}

    return $vars;
}