<?php
/**
 * @package		 UserIdeas
 * @subpackage	 Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing statuses.
 * 
 * @package		 UserIdeas
 * @subpackage	 Library
 */
class UserIdeasStatuses implements Iterator, Countable, ArrayAccess {

    protected $statuses = array();
    
    protected $db;
    
    protected $position = 0;
    
    protected static $instance;
    
    /**
     * Initialize the object and load user statuses.
     *
     * <code>
     *
     * $options = array(
     *      "limit" 		 => 10,
     *      "sort_direction" => "DESC"
     * );
     *
     * $statuses = new UserIdeasStatuses($options);
     *
     * </code>
     *
     * @param array Options that will be used for filtering results.
     */
    public function __construct($options = array()) {
        
        $this->db 		= JFactory::getDbo();
        $this->load($options);
        
    }
    
    /**
     *
     * Create an instance of the object and load data.
     *
     * <code>
     *
     * // Create an object of the statuses.
     * $statuses     = UserIdeasStatuses::getInstance();
     *
     * </code>
     *
     * @return null|UserIdeasStatuses
     */
    public static function getInstance()  {
    
        if (empty(self::$instance)){
            $item = new UserIdeasStatuses();
            self::$instance = $item;
        }
    
        return self::$instance;
    }
    
    /**
     * Load all statuses.
     * 
     * <code>
     * 
     * $options = array(
     * 		"limit" 		 => 10,
     * 		"sort_direction" => "DESC"
     * );
     * 
     * $statuses = new UserIdeasStatuses();
     * $statuses->load($options);
     * 
     * </code>
     * 
     * @param array   Options that will be used for filtering results.
     */
    public function load($options = array()) {
        
        $sortDir  = JArrayHelper::getValue($options, "sort_direction", "DESC");
        $sortDir  = (strcmp("DESC", $sortDir) == 0) ? "DESC" : "ASC";
        
        $limit    = JArrayHelper::getValue($options, "limit", 0, "int");
        
        // Create a new query object.
        $query  = $this->db->getQuery(true);
        $query
            ->select("a.id, a.name, a.default")
            ->from($this->db->quoteName("#__uideas_statuses", "a"));
        
        $query->order("a.name ". $sortDir);
        
        if(!empty($limit)) {
            $this->db->setQuery($query, 0, $limit);
        } else {
            $this->db->setQuery($query);
        }
        
        $results = $this->db->loadObjectList();
        
        if(!empty($results)) {
            $this->statuses = $results;
        } 
        
    }
    
    /**
     * Rewind the Iterator to the first element.
     *
     * @see Iterator::rewind()
     */
    public function rewind() {
        $this->position = 0;
    }
    
    /**
     * Return the current element.
     *
     * @see Iterator::current()
     */
    public function current() {
        return (!isset($this->statuses[$this->position])) ? null : $this->statuses[$this->position];
    }
    
    /**
     * Return the key of the current element.
     *
     * @see Iterator::key()
     */
    public function key() {
        return $this->position;
    }
    
    /**
     * Move forward to next element.
     *
     * @see Iterator::next()
     */
    public function next() {
        ++$this->position;
    }
    
    /**
     * Checks if current position is valid.
     *
     * @see Iterator::valid()
     */
    public function valid() {
        return isset($this->statuses[$this->position]);
    }
    
    /**
     * Count elements of an object.
     *
     * @see Countable::count()
     */
    public function count() {
        return (int)count($this->statuses);
    }

    /**
     * Offset to set.
     *
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->statuses[] = $value;
        } else {
            $this->statuses[$offset] = $value;
        }
    }
    
    /**
     * Whether a offset exists.
     *
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset) {
        return isset($this->statuses[$offset]);
    }
    
    /**
     * Offset to unset.
     *
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset) {
        unset($this->statuses[$offset]);
    }
    
    /**
     * Offset to retrieve.
     *
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset) {
        return isset($this->statuses[$offset]) ? $this->statuses[$offset] : null;
    }
    
    /**
     * Return default status.
     * 
     * @return null|object
     */
    public function getDefault() {
        foreach($this->statuses as $status) {
            if(!empty($status->default)) {
                return $status;
            }
        }
        
        return null;
    }
    
    public function getStatusesOptions() {
        
        $options = array();
        
        foreach($this->statuses as $status) {
            $options[] = array("text" => $status->name, "value" => $status->id);
        }
        
        return $options;
    }
    
}

