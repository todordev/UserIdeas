<?php
/**
 * @package		 ITPrism Plugins
 * @subpackage	 UserIdeas Vote
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * UserIdeas Vote is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * UserIdeas Vote Plugin
 *
 * @package		ITPrism Plugins
 * @subpackage	UserIdeas
 */
class plgUserIdeasVote extends JPlugin {
    
    /**
     * 
     * This method is triggered bofore user vote be stored
     * @param string 	$context
     * @param array 	$data
     * @param JRegistry $params
     */
    public function onBeforeVote($context, $data, $params) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/

        if($app->isAdmin()) {
            return;
        }

        $doc     = JFactory::getDocument();
        /**  @var $doc JDocumentHtml **/
        
        // Check document type
        $docType = $doc->getType();
        if(strcmp("raw", $docType) != 0){
            return;
        }
       
        if(strcmp("com_userideas.beforevote", $context) != 0){
            return;
        }
        
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        $itemId = JArrayHelper::getValue($data, "id", 0, "int");
        $userId = JArrayHelper::getValue($data, "user_id", 0, "int");
        
        $query
            ->select("COUNT(*)")
            ->from($db->quoteName("#__uideas_votes") . " AS a")
            ->where("a.item_id = ". (int)$itemId)
            ->where("a.user_id = ". (int)$userId);
            
        $db->setQuery($query, 0, 1);
        $result = $db->loadResult();
        
        if(!$result) { // User vote is not recorded. Return true
            
            $result = array(
                "success" => true
            );
            
        } else { // User vote is recorded. Return false.
            
            $this->loadLanguage();
            $result = array(
                "success" => false,
                "message" => JText::_("PLG_USERIDEAS_VOTE_YOU_HAVE_VOTED")
            );
            
        }
        
        return $result;
        
    }
    
    /**
     * Store user vote
     * 
     * @param string 	$context
     * @param array 	$data	This is a data about user and his vote
     * @param JRegistry $params	The parameters of the component
     */
    public function onVote($context, $data, $params) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        if($app->isAdmin()) {
            return;
        }

        $doc     = JFactory::getDocument();
        /**  @var $doc JDocumentHtml **/
        
        // Check document type
        $docType = $doc->getType();
        
        if(strcmp("raw", $docType) != 0){
            return;
        }
       
        if(strcmp("com_userideas.vote", $context) != 0){
            return;
        }
        
        jimport("userideas.item");
        $db = JFactory::getDbo();
        
        $itemId = JArrayHelper::getValue($data, "id", 0, "int");
        $userId = JArrayHelper::getValue($data, "user_id", 0, "int");
        
        // Save vote
        $item = new UserIdeasItem($db);
        $item->load($itemId);
        
        if(!$item->id) {
            return null;
        }
        
        $item->vote();
        
        // Add record to history table
        jimport("userideas.vote");
        $history = new UserIdeasVote($db);
        
        $history->set("user_id", $userId);
        $history->set("item_id", $itemId);
        $history->set("votes", 1);
        $history->store();
        
        // Prepare response data
        $data["response_data"] = array(
            "user_votes" => 1,
            "votes"      => $item->votes
        );
        
    }
    
    
}