<?php
/**
 * @package      UserIdeas
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

JLoader::register("UserIdeasTableItem", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_userideas".DIRECTORY_SEPARATOR."tables".DIRECTORY_SEPARATOR."item.php");
JLoader::register("UserIdeasInterfaceTable", JPATH_LIBRARIES .DIRECTORY_SEPARATOR."userideas".DIRECTORY_SEPARATOR."interface".DIRECTORY_SEPARATOR."table.php");

class UserIdeasItem implements UserIdeasInterfaceTable {
    
    protected $table;
    
    protected static $instances = array();
    
    public function __construct($id) {
        
        $this->table = new UserIdeasTableItem(JFactory::getDbo());
        
        if(!empty($id)) {
            $this->load($id);
        }
    }
    
    /**
     *
     * Create an instance of the object and load data.
     *
     * <code>
     *
     * $itemId  = 1;
     * $item    = UserIdeasItem::getInstance($itemId);
     *
     * </code>
     *
     * @param number $id
     *
     * @return null|UserIdeasItem
     */
    public static function getInstance($id)  {
    
        if (empty(self::$instances[$id])){
            $item = new UserIdeasItem($id);
            self::$instances[$id] = $item;
        }
    
        return self::$instances[$id];
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
    
	/**
     * Increase number of votes.
     * @param integer $value
     */
    public function vote($value = 1) {
        
        $this->table->votes += (int)$value;
        $this->store();
        
    }
    
    /**
     * Decrease number of votes.
     * 
     * @param integer $value
     */
    public function decreaseVote($value) {
    
        $this->table->votes -= (int)$value;
        if($this->table->votes < 0) {
            $this->table->votes = 0;
        }
        
        $this->store();
    
    }
    
    
    /**
     * Check the owner of the item.
     * 
     * @param integer $itemId
     * @param integer $userId
     */
    public function isValid($itemId = null, $userId = null) {
        
        if( ($this->table->id != $itemId) OR ($this->table->user_id != $userId) ) {
            return false;
        }  
        
        return true;
        
    }
    
    public function getId() {
        return $this->table->id;
    }
    
    public function getVotes() {
        return (int)$this->table->votes;
    }
    
    public function getSlug() {
       return $this->table->getSlug(); 
    }
    
    public function getCatSlug() {
       return $this->table->getCatSlug(); 
    }
    
    public function getCategoryName() {
       return $this->table->getCategoryName(); 
    }
}
