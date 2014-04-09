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
 * This class provides functionality for managing an item.
 */
class UserIdeasItem {

    protected $id;
    protected $title;
    protected $alias;
    protected $description;
    protected $votes;
    protected $record_date;
    protected $ordering;
    protected $published;
    protected $status_id;
    protected $catid;
    protected $user_id;

    protected $category;
    protected $username;
    protected $status;
    protected $slug;
    protected $catslug;

    /**
     * @var JDatabaseDriver
     */
    protected $db;

    /**
     * This method initializes the object.
     *
     * @param int $id Item ID.
     */
    public function __construct($id = 0) {
        $this->id = $id;
    }

    /**
     * This method sets a database driver.
     *
     * @param JDatabaseDriver $db
     * @return self
     */
    public function setDb(JDatabaseDriver $db) {
        $this->db = $db;
        return $this;
    }

    /**
     * This method loads data about an item from a database.
     *
     * <code>
     * $db     = JFactory::getDbo();
     * $itemId = 1;
     *
     * $item   = new UserIdeasItem($itemId);
     * $item->setDb($db);
     * $item->load($itemId);
     * </code>
     */
    public function load() {

        $query = $this->db->getQuery(true);

        $query
            ->select(
                "a.id, a.title, a.alias, a.description, a.votes, a.record_date, " .
                "a.ordering, a.published, a.status_id, a.catid, a.user_id, " .
                "b.title AS category, " .
                "c.name AS username, " .
                "d.name AS status, " .
                $query->concatenate(array("a.id", "a.alias"), ":") . " AS slug, " .
                $query->concatenate(array("b.id", "b.alias"), ":") . " AS catslug"
            )
            ->from($this->db->quoteName("#__uideas_items", "a"))
            ->leftJoin($this->db->quoteName("#__categories", "b") . " ON a.catid = b.id")
            ->leftJoin($this->db->quoteName("#__users", "c") . " ON a.user_id = c.id")
            ->leftJoin($this->db->quoteName("#__uideas_statuses", "d") . " ON a.status_id = d.id")
            ->where("a.id = " . (int)$this->id);

        $this->db->setQuery($query);

        $result = $this->db->loadAssoc();
        if(!empty($result)) {
            $this->bind($result);
        }

    }

    /**
     * This method sets data to object parameters.
     *
     * <code>
     * $data = array(
     *      "title"         => "My title",
     *      "description"   => "My description"
     *      "votes"         => 0
     *      "caitid"        => 1
     * );
     *
     * $item   = new UserIdeasItem();
     * $item->bind($data);
     * </code>
     */
    public function bind($data, $ignored = array()) {

        foreach($data as $key => $value) {
            if(!in_array($key, $ignored)) {
                $this->$key = $value;
            }
        }

    }

    /**
     * This method save the data about item to database.
     *
     * <code>
     * $data = array(
     *      "title"         => "My title",
     *      "description"   => "My description"
     *      "votes"         => 0
     *      "caitid"        => 1
     * );
     *
     * $item   = new UserIdeasItem();
     * $item->bind($data);
     * $item->store();
     * </code>
     */
    public function store() {

        $query = $this->db->getQuery(true);

        $query
            ->set($this->db->quoteName("title")       ."=". $this->db->quote($this->title))
            ->set($this->db->quoteName("alias")       ."=". $this->db->quote($this->alias))
            ->set($this->db->quoteName("description") ."=". $this->db->quote($this->description))
            ->set($this->db->quoteName("votes")       ."=". $this->db->quote($this->votes))
            ->set($this->db->quoteName("ordering")    ."=". $this->db->quote($this->ordering))
            ->set($this->db->quoteName("published")   ."=". $this->db->quote($this->published))
            ->set($this->db->quoteName("status_id")   ."=". $this->db->quote($this->status_id))
            ->set($this->db->quoteName("catid")       ."=". $this->db->quote($this->catid))
            ->set($this->db->quoteName("user_id")     ."=". $this->db->quote($this->user_id));

        if(!empty($this->id)) { // Update
            $query
                ->update($this->db->quoteName("#__uideas_items"))
                ->where($this->db->quoteName("id") ."=". (int)$this->id);
        } else {
            $query->insert($this->db->quoteName("#__uideas_items"));
        }

        $this->db->setQuery($query);
        $this->db->execute();
    }
    
	/**
     * Increase number of votes.
     *
     * <code>
     * $db     = JFactory::getDbo();
     * $itemId = 1;
     *
     * $item   = new UserIdeasItem($itemId);
     * $item->setDb($db);
     * $item->load($itemId);
     * $item->vote(1);
     * </code>
     *
     * @param int $value
     */
    public function vote($value = 1) {
        
        $this->votes += (int)$value;
        $this->storeVotes();
        
    }
    
    /**
     * Decrease number of votes.
     *
     * <code>
     * $db     = JFactory::getDbo();
     * $itemId = 1;
     *
     * $item   = new UserIdeasItem($itemId);
     * $item->setDb($db);
     * $item->load($itemId);
     * $item->decreaseVote(1);
     * </code>
     *
     * @param int $value
     */
    public function decreaseVote($value) {
    
        $this->votes -= (int)$value;
        if($this->votes < 0) {
            $this->votes = 0;
        }
        
        $this->storeVotes();
    
    }

    protected function storeVotes() {

        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName("#__uideas_items"))
            ->set($this->db->quoteName("votes") ."=". (int)$this->votes)
            ->where($this->db->quoteName("id")  ."=". (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();

    }
    
    
    /**
     * It checks the owner of the item.
     *
     * <code>
     * $db     = JFactory::getDbo();
     * $itemId = 1;
     * $userId = 2;
     *
     * $item   = new UserIdeasItem($itemId);
     * $item->setDb($db);
     * $item->load($itemId);
     *
     * if(!$item->isValid($itemId, $userId) {
     * ...
     * }
     * </code>
     *
     * @param int $itemId
     * @param int $userId
     *
     * @return bool
     */
    public function isValid($itemId = null, $userId = null) {
        
        if( ($this->id != $itemId) OR ($this->user_id != $userId) ) {
            return false;
        }  
        
        return true;
        
    }

    /**
     * Returns item ID.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Returns the title of the item.
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Returns the number of votes.
     *
     * @return int
     */
    public function getVotes() {
        return (int)$this->votes;
    }

    /**
     * Returns the id and alias in a slug string. It is the slug of the item.
     *
     * @return string
     */
    public function getSlug() {
       return $this->slug;
    }

    /**
     * Returns the id and alias in a slug string. It is a category slug of the item.
     *
     * @return string
     */
    public function getCategorySlug() {
       return $this->catslug;
    }

    /**
     * Returns a category ID.
     *
     * @return integer
     */
    public function getCategoryId() {
        return $this->catid;
    }

    /**
     * Returns a category name.
     *
     * @return string
     */
    public function getCategory() {
       return $this->category;
    }

    /**
     * Return a name of the user, which is the creator of the item.
     *
     * @return string
     */
    public function getUserName() {
        return $this->username;
    }
}
