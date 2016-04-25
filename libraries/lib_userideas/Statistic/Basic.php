<?php
/**
 * @package      Userideas
 * @subpackage   Statistic\Basic
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Statistic;

defined('JPATH_PLATFORM') or die;

/**
 * This class generates basic statistics.
 *
 * @package         Userideas\Statistic
 * @subpackage      Basic
 */
class Basic
{
    /**
     * Database driver
     *
     * @var \JDatabaseDriver
     */
    protected $db;

    /**
     * Initialize the object.
     *
     * <code>
     * $item   = new Userideas\Statistics\Items(\JFactory::getDbo());
     * </code>
     *
     * @param \JDatabaseDriver $db
     */
    public function __construct(\JDatabaseDriver $db)
    {
        $this->db = $db;
    }

    /**
     * This method returns a number of all items.
     *
     * <code>
     * $item   = new Userideas\Statistics\Items(\JFactory::getDbo());
     *
     * $totalItems = $item->getTotalItems();
     * </code>
     *
     * @return int
     */
    public function getTotalItems()
    {
        $query = $this->db->getQuery(true);

        $query
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__uideas_items', 'a'));

        $this->db->setQuery($query);
        $result = $this->db->loadResult();

        if (!$result) {
            $result = 0;
        }

        return $result;
    }

    /**
     * This method returns a number a sum of all votes.
     *
     * <code>
     * $item   = new Userideas\Statistics\Items(\JFactory::getDbo());
     *
     * $totalVotes = $item->getTotalVotes();
     * </code>
     *
     * @return int
     */
    public function getTotalVotes()
    {
        $query = $this->db->getQuery(true);

        $query
            ->select('SUM(a.votes)')
            ->from($this->db->quoteName('#__uideas_items', 'a'));

        $this->db->setQuery($query);
        $result = $this->db->loadResult();

        if (!$result) {
            $result = 0;
        }

        return $result;
    }

    /**
     * This method returns a number of all comments.
     *
     * <code>
     * $item   = new Userideas\Statistics\Items(\JFactory::getDbo());
     *
     * $totalComments = $item->getTotalComments();
     * </code>
     *
     * @return int
     */
    public function getTotalComments()
    {
        $query = $this->db->getQuery(true);

        $query
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__uideas_comments', 'a'));

        $this->db->setQuery($query);
        $result = $this->db->loadResult();

        if (!$result) {
            $result = 0;
        }

        return $result;
    }
}
