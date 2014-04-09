<?php
/**
* @package      UserIdeas
* @subpackage   Library
* @author       Todor Iliev
* @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
* @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

defined('JPATH_PLATFORM') or die;


/**
 * This class generates basic statistics.
 *
 * @package		 Statistics
 */
class UserIdeasStatisticsBasic {
    
    /**
     * Database driver
     *
     * @var JDatabaseMySQLi
     */
    protected $db;
    
    /**
     * Initialize the object.
     *
     * @param JDatabaseDriver   $db
     */
    public function __construct(JDatabaseDriver $db) {
        $this->db = $db;
    }

    /**
     * This method returns a number of all items.
     *
     * @return int
     */
    public function getTotalItems() {
        
        $query = $this->db->getQuery(true);
        
        $query
            ->select("COUNT(*)")
            ->from($this->db->quoteName("#__uideas_items", "a"));
        
        $this->db->setQuery($query);
        $result = $this->db->loadResult();
        
        if(!$result) {
            $result = 0;
        }
        
        return $result;
        
    }

    /**
     * This method returns a number a sum of all votes.
     *
     * @return int
     */
    public function getTotalVotes() {
    
        $query = $this->db->getQuery(true);
    
        $query
            ->select("SUM(a.votes)")
            ->from($this->db->quoteName("#__uideas_items", "a"));
    
        $this->db->setQuery($query);
        $result = $this->db->loadResult();
    
        if(!$result) {
            $result = 0;
        }
    
        return $result;
    
    }

    /**
     * This method returns a number of all comments.
     *
     * @return int
     */
    public function getTotalComments() {
    
        $query = $this->db->getQuery(true);
    
        $query
            ->select("COUNT(*)")
            ->from($this->db->quoteName("#__uideas_comments", "a"));
    
        $this->db->setQuery($query);
        $result = $this->db->loadResult();
    
        if(!$result) {
            $result = 0;
        }
    
        return $result;
    
    }


}
