<?php
/**
 * @package      UserIdeas
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

JLoader::register("UserIdeasTableVote", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_userideas".DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR."vote.php");
JLoader::register("UserIdeasInterfaceTable", JPATH_LIBRARIES .DIRECTORY_SEPARATOR."userideas".DIRECTORY_SEPARATOR."interface".DIRECTORY_SEPARATOR."table.php");

/**
 * This class provides functionality for managing votes.
 */
class UserIdeasVote implements UserIdeasInterfaceTable  {
    
    protected $table;
    
    public function __construct() {
        $this->table = new UserIdeasTableVote(JFactory::getDbo());
    }
    
    public function load($keys, $reset = true) {
        $this->table->load($keys, $reset);
    }
    
    public function bind($src, $ignore = array()) {
        $this->table->bind($src, $ignore);
    }
    
    public function store($updateNulls = false) {
        $this->table->store($updateNulls);
    }
    
    public function setVote($votes) {
        $this->table->votes = (int)$votes;
    }
    
    public function setUserId($userId) {
        $this->table->user_id = (int)$userId;
    }
    
    public function setItemId($itemId) {
        $this->table->item_id = (int)$itemId;
    }
}
