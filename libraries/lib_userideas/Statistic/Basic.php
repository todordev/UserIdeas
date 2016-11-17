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
     * @throws \RuntimeException
     * @return int
     */
    public function getTotalItems()
    {
        $query = $this->db->getQuery(true);

        $query
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__uideas_items', 'a'));

        $this->db->setQuery($query);

        return (int)$this->db->loadResult();
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
     * @throws \RuntimeException
     * @return int
     */
    public function getTotalVotes()
    {
        $query = $this->db->getQuery(true);

        $query
            ->select('SUM(a.votes)')
            ->from($this->db->quoteName('#__uideas_items', 'a'));

        $this->db->setQuery($query);

        return (int)$this->db->loadResult();
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
     * @throws \RuntimeException
     * @return int
     */
    public function getTotalComments()
    {
        $query = $this->db->getQuery(true);

        $query
            ->select('COUNT(*)')
            ->from($this->db->quoteName('#__uideas_comments', 'a'));

        $this->db->setQuery($query);

        return (int)$this->db->loadResult();
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
     * @param array $ids
     * @param array $options
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    public function getCategoryItems(array $ids, array $options = array())
    {
        $results = array();

        if (count($ids) > 0) {
            $query = $this->db->getQuery(true);

            $query
                ->select('a.catid, COUNT(*) as number')
                ->from($this->db->quoteName('#__uideas_items', 'a'))
                ->where('a.catid IN (' . implode(',', $ids) . ')')
                ->group($this->db->quoteName('catid'));

            $state = array_key_exists('state', $options) ? $options['state'] : null;

            // Filter by state
            if ($state !== null) {
                $query->where('a.published = ' .(int)$state);
            }

            $this->db->setQuery($query);
            $results = (array)$this->db->loadAssocList('catid', 'number');
        }

        return $results;
    }
}
