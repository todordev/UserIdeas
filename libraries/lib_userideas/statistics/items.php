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
 * This is a base class for items statistics.
 *
 * @package		 Statistics
 */
abstract class UserIdeasStatisticsItems {
    
    /**
     * Database driver
     * 
     * @var JDatabaseDriver
     */
    protected $db;
    
    /**
     * This method initializes the object.
     * 
     * @param JDatabaseDriver   $db
     */
    public function __construct(JDatabaseDriver $db) {
        $this->db = $db;
    }

    protected function getQuery() {
        
        $query = $this->db->getQuery(true);
        
        $query
            ->select(
                "a.id, a.title, a.alias, a.description, a.votes, a.hits, " .
                "a.record_date, a.ordering, a.published, a.status_id, a.catid, a.user_id, " .
                "c.name, " .
                $query->concatenate(array("a.id", "a.alias"), ":") . " AS slug, " .
                $query->concatenate(array("b.id", "b.alias"), ":") . " AS catslug"
            )
            ->from($this->db->quoteName("#__uideas_items", "a"))
            ->leftJoin($this->db->quoteName("#__categories", "b") . " ON a.catid = b.id")
            ->leftJoin($this->db->quoteName("#__users", "c") . " ON a.user_id = c.id")
        ;
        
        return $query;
        
    }

    abstract public function load();
}
