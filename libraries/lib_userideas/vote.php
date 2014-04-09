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
 * This class provides functionality for managing votes.
 */
class UserIdeasVote  {

    protected $id;
    protected $item_id;
    protected $user_id;
    protected $votes;
    protected $record_date;

    /**
     * @var JDatabaseDriver
     */
    protected $db;

    /**
     * This method sets a database driver.
     *
     * @param $db JDatabaseDriver
     *
     * @return self
     */
    public function setDb(JDatabaseDriver $db) {
        $this->db = $db;
        return $this;
    }

    /**
     * This method set data to object parameters.
     *
     * <code>
     * $data = array(
     *      "item_id"    => 1,
     *      "user_id"    => 2,
     *      "votes"      => 1
     * );
     *
     * $vote   = new UserIdeasVote();
     * $vote->bind($data);
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
     * This method save the data about vote to database.
     *
     * <code>
     * $data = array(
     *      "item_id"    => 1,
     *      "user_id"    => 2,
     *      "votes"      => 1
     * );
     *
     * $vote   = new UserIdeasVote();
     * $vote->bind($data);
     * $vote->store();
     * </code>
     *
     */
    public function store() {

        $query = $this->db->getQuery(true);

        $query
            ->set($this->db->quoteName("item_id")   ."=". $this->db->quote($this->item_id))
            ->set($this->db->quoteName("user_id")   ."=". $this->db->quote($this->user_id))
            ->set($this->db->quoteName("votes")     ."=". $this->db->quote($this->votes));

        if(!empty($this->id)) { // Update
            $query
                ->update($this->db->quoteName("#__uideas_votes"))
                ->where($this->db->quoteName("id") ."=". (int)$this->id);
        } else {
            $query->insert($this->db->quoteName("#__uideas_votes"));
        }

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * This method sets a number of votes.
     *
     * @param $votes
     * @return $this
     */
    public function setVotes($votes) {
        $this->votes = (int)$votes;
        return $this;
    }

    /**
     * This method sets an user ID.
     *
     * @param $userId
     * @return $this
     */
    public function setUserId($userId) {
        $this->user_id = (int)$userId;
        return $this;
    }

    /**
     * This method sets an item ID.
     *
     * @param $itemId
     * @return $this
     */
    public function setItemId($itemId) {
        $this->item_id = (int)$itemId;
        return $this;
    }
}
