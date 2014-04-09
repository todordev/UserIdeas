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

    public static function prepareStatuses($items) {

        jimport("userideas.status");

        foreach($items as &$item) {

            if(!empty($item->status_params)) {
                $statusParams = json_decode($item->status_params, true);

                if(!empty($statusParams)) {
                    $item->status_params = $statusParams;
                } else {
                    $item->status_params = null;
                }
            }

            $statusData = array(
                "id"        => $item->status_id,
                "name"      => $item->status_name,
                "default"   => $item->status_default,
                "params"    => $item->status_params
            );

            $item->status = new UserIdeasStatus();
            $item->status->bind($statusData);
        }

        return $items;
    }
}