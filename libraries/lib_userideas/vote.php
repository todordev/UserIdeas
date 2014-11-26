<?php
/**
 * @package      UserIdeas
 * @subpackage   Votes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality for managing votes.
 *
 * @package      UserIdeas
 * @subpackage   Votes
 */
class UserIdeasVote
{
    protected $id;
    protected $item_id;
    protected $user_id;
    protected $hash;
    protected $votes;
    protected $record_date;

    /**
     * @var JDatabaseDriver
     */
    protected $db;

    /**
     * Initialize the object.
     *
     * <code>
     * $vote   = new UserIdeasVote(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver $db
     */
    public function __construct(JDatabaseDriver $db = null)
    {
        $this->db = $db;
    }

    /**
     * This method sets a database driver.
     *
     * <code>
     * $vote   = new UserIdeasVote();
     * $vote->setDb(JFactory::getDbo());
     * </code>
     *
     * @param $db JDatabaseDriver
     *
     * @return self
     */
    public function setDb(JDatabaseDriver $db)
    {
        $this->db = $db;

        return $this;
    }

    /**
     * This method sets data to object parameters.
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
    public function bind($data, $ignored = array())
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $ignored)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * This method saves the data about a vote to database.
     *
     * <code>
     * $data = array(
     *      "item_id"    => 1,
     *      "user_id"    => 2,
     *      "hash"    => "HASH1JD92J09",
     *      "votes"      => 1
     * );
     *
     * $vote   = new UserIdeasVote(JFactory::getDbo());
     * $vote->bind($data);
     * $vote->store();
     * </code>
     */
    public function store()
    {
        $query = $this->db->getQuery(true);

        $query
            ->set($this->db->quoteName("item_id") . "=" . $this->db->quote($this->item_id))
            ->set($this->db->quoteName("user_id") . "=" . $this->db->quote($this->user_id))
            ->set($this->db->quoteName("hash") . "=" . $this->db->quote($this->hash))
            ->set($this->db->quoteName("votes") . "=" . $this->db->quote($this->votes));

        if (!empty($this->id)) { // Update
            $query
                ->update($this->db->quoteName("#__uideas_votes"))
                ->where($this->db->quoteName("id") . "=" . (int)$this->id);
        } else {
            $query->insert($this->db->quoteName("#__uideas_votes"));
        }

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * This method sets a number of votes.
     *
     * <code>
     * $vote   = new UserIdeasVote(JFactory::getDbo());
     * $vote->setVotes(5);
     * </code>
     *
     * @param int $votes
     *
     * @return self
     */
    public function setVotes($votes)
    {
        $this->votes = (int)$votes;

        return $this;
    }

    /**
     * This method sets an user ID.
     *
     * <code>
     * $vote   = new UserIdeasVote(JFactory::getDbo());
     * $vote->setUserId(1);
     * </code>
     *
     * @param int $userId
     *
     * @return self
     */
    public function setUserId($userId)
    {
        $this->user_id = (int)$userId;

        return $this;
    }

    /**
     * This method sets an hash used to recognize votes from anonymous users.
     *
     * <code>
     * $hash = "HASHHF0HQ30SF09";
     *
     * $vote   = new UserIdeasVote(JFactory::getDbo());
     * $vote->setHash($hash);
     * </code>
     *
     * @param string $hash
     *
     * @return self
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * This method sets an item ID.
     *
     * <code>
     * $vote   = new UserIdeasVote(JFactory::getDbo());
     * $vote->setItemId(2);
     * </code>
     *
     * @param int $itemId
     *
     * @return self
     */
    public function setItemId($itemId)
    {
        $this->item_id = (int)$itemId;

        return $this;
    }
}
