<?php
/**
* @package      ITPrism Components
* @subpackage   CrowdFunding
* @author       Todor Iliev
* @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
* @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
* CrowdFunding is free software. This vpversion may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

defined('JPATH_PLATFORM') or die;

JLoader::register("UserFeedbackTableItem", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_userfeedback".DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR."item.php");

/**
 * This class provieds functionality that can be used by developers 
 * who wants to develop extensions based on CrowdFunding
 */
class UserFeedbackItem extends UserFeedbackTableItem {
    
    public function __construct( $db ) {
        parent::__construct( $db );
    }

	/**
     * Increase number of votes.
     * @param float $value
     */
    public function vote($value = 1) {
        
        $this->votes += (int)$value;
        $this->store();
        
    }
    
    /**
     * Check the owner of the item.
     * 
     * @param integer $itemId
     * @param integer $userId
     */
    public function isValid($itemId = null, $userId = null) {
        
        if( ($this->id != $itemId) OR ($this->user_id != $userId) ) {
            return false;
        }  
        
        return true;
        
    }
    
}
