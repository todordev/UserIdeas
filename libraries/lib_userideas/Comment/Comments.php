<?php
/**
 * @package         Userideas
 * @subpackage      Statuses
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Comment;

use Prism\Database;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods used for managing comments as collection.
 *
 * @package      Userideas
 * @subpackage   Statuses
 */
class Comments extends Database\Collection
{
    protected $counts   = array();

    /**
     * Load the records from the database.
     *
     * <code>
     * $options = array(
     *    'start'           => 0,
     *    'limit'           => 10,
     *    'order_column'    => 'a.name',
     *    'order_direction' => 'DESC',
     *    'item_id'         => 1
     * );
     *
     * $comments = new Userideas\Comment\Comments(\JFactory::getDbo());
     * $comments->load($options);
     * </code>
     *
     * @param array $options
     */
    public function load(array $options = array())
    {
        $orderColumn    = $this->getOptionOrderColumn($options);
        $orderDirection = $this->getOptionOrderDirection($options);
        $start          = $this->getOptionStart($options);
        $limit          = $this->getOptionLimit($options);
        $id             = $this->getOptionId($options, 'item_id');

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select('a.id, a.comment, a.record_date, a.published, a.user_id, a.item_id')
            ->from($this->db->quoteName('#__uideas_comments', 'a'))
            ->where('a.item_id = '. (int)$id);
        
        if ($orderColumn) {
            $query->order($this->db->quoteName($orderColumn). ' ' . $orderDirection);
        }

        if ($limit > 0) {
            $this->db->setQuery($query, $start, $limit);
        } else {
            $this->db->setQuery($query);
        }
        
        $this->items = (array)$this->db->loadObjectList();
    }

    /**
     * Create an object Comment and return it.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     *
     * $comments   = new Userideas\Comment\Comments(\JFactory::getDbo());
     * $comments->load($options);
     *
     * $commentId = 1;
     * $comment   = $comments->getComment($commentId);
     * </code>
     *
     * @param int|string $id Country ID or Country code.
     *
     * @return null|Comment
     *
     * @throws \UnexpectedValueException
     */
    public function getComment($id)
    {
        if (!$id) {
            throw new \UnexpectedValueException(\JText::_('COM_USERIDEAS_ERROR_INVALID_ID'));
        }

        $comment = null;

        foreach ($this->items as $item) {
            if ($this->isMultidimensional) {
                foreach ($item as $value) {
                    if ((int)$value['id'] === (int)$id) {
                        $comment = new Comment($this->db);
                        $comment->bind($item);
                        break;
                    }
                }
            } else {
                if ((int)$item['id'] === (int)$id) {
                    $comment = new Comment($this->db);
                    $comment->bind($item);
                    break;
                }
            }
        }

        return $comment;
    }

    /**
     * Return the comments as array with objects.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     *
     * $comments   = new Userideas\Comment\Comments(\JFactory::getDbo());
     * $comments->load($options);
     *
     * $comments = $comments->getComments();
     * </code>
     *
     * @return array
     */
    public function getComments()
    {
        $results = array();

        if ($this->isMultidimensional) {
            foreach ($this->items as $key => $item) {
                $i = 0;
                foreach ($item as $item2) {
                    $comment = new Comment($this->db);
                    $comment->bind($item2);
                    $results[$key][$i] = $comment;
                    $i++;
                }
            }
        } else {
            $i = 0;
            foreach ($this->items as $key => $item) {
                $comment = new Comment($this->db);
                $comment->bind($item);

                $results[$i] = $comment;
                $i++;
            }
        }

        return $results;
    }

    /**
     * Count the number of elements by items IDs or users IDs.
     *
     * <code>
     * $options = array(
     *     "items_ids" => array(1,2,3,4,5),
     *     "users_ids" => array(1,2,3,4,5)
     * );
     *
     * $comments   = new Userideas\Comment\Comments(\JFactory::getDbo());
     * $comments->load($options);
     *
     * $results = $comments->advancedCount();
     * </code>
     *
     * @param array $options
     *
     * @return array
     */
    public function advancedCount(array $options = array())
    {
        $itemsIds = $this->getOptionIds($options, 'items_ids');
        $usersIds = $this->getOptionIds($options, 'users_ids');

        $results   = array();

        if (count($itemsIds) > 0) {
            if (!array_key_exists('items', $this->counts)) {
                $query = $this->db->getQuery(true);

                $query
                    ->select('a.item_id, COUNT(*) AS number')
                    ->from($this->db->quoteName('#__uideas_comments', 'a'))
                    ->where($this->db->quoteName('a.item_id') . ' IN (' . implode(',', $itemsIds) . ')')
                    ->group('a.item_id');

                $this->db->setQuery($query);
                $this->counts['items'] = (array)$this->db->loadAssocList('item_id', 'number');
            }

            $results = $this->counts['items'];
        } elseif (count($usersIds) > 0) {
            if (!array_key_exists('users', $this->counts)) {
                $query = $this->db->getQuery(true);

                $query
                    ->select('a.user_id, COUNT(*) AS number')
                    ->from($this->db->quoteName('#__uideas_comments', 'a'))
                    ->where($this->db->quoteName('a.user_id') . ' IN (' . implode(',', $usersIds) . ')')
                    ->group('a.user_id');

                $this->db->setQuery($query);
                $this->counts['users'] = (array)$this->db->loadAssocList('user_id', 'number');
            }

            $results = $this->counts['users'];
        }

        return $results;
    }
}
