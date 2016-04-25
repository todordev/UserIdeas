<?php
/**
 * @package      Userideas
 * @subpackage   Votes
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Vote;

use Prism\Database\Table;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality for managing votes.
 *
 * @package      Userideas
 * @subpackage   Votes
 */
class Vote extends Table
{
    protected $id;
    protected $title;
    protected $user;
    protected $item_id;
    protected $user_id;
    protected $hash;
    protected $votes;
    protected $record_date;

    /**
     * This method loads data about vote from database.
     *
     * <code>
     * $voteId = 1;
     *
     * $vote   = new Userideas\Vote\Vote(\JFactory::getDbo());
     * $vote->load($voteId);
     * </code>
     *
     * @param int|array $keys
     * @param array $options
     */
    public function load($keys, array $options = array())
    {
        $query = $this->db->getQuery(true);

        $query
            ->select(
                'a.id, a.item_id, a.user_id, a.votes, a.record_date, ' .
                'b.title, c.name AS user '
            )
            ->from($this->db->quoteName('#__uideas_votes', 'a'))
            ->leftJoin($this->db->quoteName('#__uideas_statuses', 'b') . ' ON a.item_id = b.id')
            ->leftJoin($this->db->quoteName('#__users', 'c') . ' ON a.user_id = c.id');

        if (is_array($keys)) {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName('a.'.$key) .' = '. $this->db->quote($value));
            }
        } else {
            $query->where('a.id = ' . (int)$keys);
        }

        $this->db->setQuery($query);

        $result = (array)$this->db->loadAssoc();

        $this->bind($result);
    }

    /**
     * This method saves the data about a vote to database.
     *
     * <code>
     * $data = array(
     *      "item_id"    => 1,
     *      "user_id"    => 2,
     *      "hash"       => "HASH1JD92J09",
     *      "votes"      => 1
     * );
     *
     * $vote   = new Userideas\Vote\Vote(\JFactory::getDbo());
     * $vote->bind($data);
     * $vote->store();
     * </code>
     */
    public function store()
    {
        $query = $this->db->getQuery(true);

        $query
            ->set($this->db->quoteName('item_id') . '=' . $this->db->quote($this->item_id))
            ->set($this->db->quoteName('user_id') . '=' . $this->db->quote($this->user_id))
            ->set($this->db->quoteName('hash') . '=' . $this->db->quote($this->hash))
            ->set($this->db->quoteName('votes') . '=' . $this->db->quote($this->votes));

        if (!empty($this->id)) { // Update
            $query
                ->update($this->db->quoteName('#__uideas_votes'))
                ->where($this->db->quoteName('id') . '=' . (int)$this->id);
        } else {
            $query->insert($this->db->quoteName('#__uideas_votes'));
        }

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Return the ID of the vote.
     *
     * <code>
     * $vote   = new Userideas\Vote\Vote(\JFactory::getDbo());
     * $vote->load(array('hash' => 'HASH1234'));
     *
     * if (!$vote->getId()) {
     * ...
     * }
     * </code>
     *
     * @return string
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Return the title of the item that a user has voted for it.
     *
     * <code>
     * $voteId = 1;
     *
     * $vote   = new Userideas\Vote\Vote(\JFactory::getDbo());
     * $vote->load($voteId);
     *
     * echo $vote->getTitle();
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Return the name of the user who has voted for the item.
     *
     * <code>
     * $voteId = 1;
     *
     * $vote   = new Userideas\Vote\Vote(\JFactory::getDbo());
     * $vote->load($voteId);
     *
     * echo $vote->getUser();
     * </code>
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Return the number of votes stored to this record.
     *
     * <code>
     * $voteId = 1;
     *
     * $vote   = new Userideas\Vote\Vote(\JFactory::getDbo());
     * $vote->load($voteId);
     *
     * echo $vote->getVotes();
     * </code>
     *
     * @return string
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Return user ID.
     *
     * <code>
     * $voteId = 1;
     *
     * $vote   = new Userideas\Vote\Vote(\JFactory::getDbo());
     * $vote->load($voteId);
     *
     * echo $vote->getUserId();
     * </code>
     *
     * @return string
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }

    /**
     * Return item ID.
     *
     * <code>
     * $voteId = 1;
     *
     * $vote   = new Userideas\Vote\Vote(\JFactory::getDbo());
     * $vote->load($voteId);
     *
     * echo $vote->getItemId();
     * </code>
     *
     * @return string
     */
    public function getItemId()
    {
        return (int)$this->item_id;
    }

    /**
     * This method sets a number of votes.
     *
     * <code>
     * $vote   = new Userideas\Vote\Vote(\JFactory::getDbo());
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
     * $vote   = new Userideas\Vote\Vote(\JFactory::getDbo());
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
     * $vote   = new Userideas\Vote\Vote(\JFactory::getDbo());
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
     * $vote   = new Userideas\Vote\Vote(\JFactory::getDbo());
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
