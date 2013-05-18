<?php
/**
 * @package      ITPrism Plugins
 * @subpackage   UserIdeasVote
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * UserIdeasVote is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.helper');
jimport('joomla.plugin.plugin');

/**
* This plugin initializes the job of the button
* which is used for voting.
*
* @package 		ITPrism Plugins
* @subpackage	UserIdeasVote
*/
class plgSystemUserIdeasVote extends JPlugin {
	
	/**
	 * Put tags into the HEAD tag
	 */
	public function onBeforeCompileHead() {
	    
	    $app = JFactory::getApplication();
        /** @var $app JSite **/

        if($app->isAdmin()) {
            return;
        }
        
        $document = JFactory::getDocument();
        /** @var $document JDocumentHTML **/
        
        $type = $document->getType();
        if(strcmp("html",$type) != 0) {
             return;   
        }
        
        // Check component enabled
	    if (!JComponentHelper::isEnabled('com_userideas', true)) {
            return;
        }

        // Check for view. The extensions will work only on view "items"
        $allowedViews = array("items", "details");
        $view         = $app->input->getCmd("view");
        if(!in_array($view, $allowedViews)) {
            return;
        }
        
        JHtml::_('userideas.pnotify');
        JHtml::_('userideas.helper');
        
        JHtml::_('script', 'plugins/system/userideasvote/votebutton.js', false, false, false, false, false);
        
	}
	
}