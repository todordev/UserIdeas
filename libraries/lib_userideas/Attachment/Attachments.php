<?php
/**
 * @package         Userideas
 * @subpackage      Statuses
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Attachment;

use Joomla\Utilities\ArrayHelper;
use Prism\Database;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods used for managing attachments as collection.
 *
 * @package      Userideas
 * @subpackage   Statuses
 */
class Attachments extends Database\Collection
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
     *    'ids'             => array(1,2,3,4),
     *    'item_id'         => 1,
     *    'items_ids'       => array(1,2,3,4)
     * );
     *
     * $attachments = new Userideas\Attachment\Attachments(\JFactory::getDbo());
     * $attachments->load($options);
     * </code>
     *
     * @param array $options
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function load(array $options = array())
    {
        $allowedSources = array('item', 'comment');

        $orderColumn    = $this->getOptionOrderColumn($options);
        $orderDirection = $this->getOptionOrderDirection($options);
        $start          = $this->getOptionStart($options);
        $limit          = $this->getOptionLimit($options);
        $itemId         = $this->getOptionId($options, 'item_id');
        $commentId      = $this->getOptionId($options, 'comment_id');
        $itemsIds       = $this->getOptionIds($options, 'items_ids');
        $commentsIds    = $this->getOptionIds($options, 'comments_ids');
        $ids            = $this->getOptionIds($options);
        $index          = ArrayHelper::getValue($options, 'index', null, 'cmd');
        $source         = ArrayHelper::getValue($options, 'source', null, 'cmd');
        if ($source !== null and !in_array($source, $allowedSources, true)) {
            $source = null;
        }

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select('a.id, a.filename, a.filesize, a.record_date, a.attributes, a.item_id, a.comment_id, a.user_id, a.source, a.mime')
            ->from($this->db->quoteName('#__uideas_attachments', 'a'));

        // Filter by ID.
        if ($itemId > 0) { // Item ID
            $query->where('a.item_id = ' . (int)$itemId);
        } elseif (count($itemsIds) > 0) { // Items IDs
            $query->where('a.item_id IN (' . implode(',', $itemsIds) .')');
        } elseif ($commentId > 0) { // Comment ID
            $query->where('a.comment_id = ' . (int)$commentId);
        } elseif (count($commentsIds) > 0) { // Comments IDs
            $query->where('a.comment_id IN (' . implode(',', $commentsIds) .')');
        } elseif (count($ids) > 0) { // Attachment IDs
            $query->where('a.id IN (' . explode(',', $ids) .')');
        }

        // Filter by source value.
        if ($source) {
            $query->where('a.source = ' . $this->db->quote($source));
        }
        
        if ($orderColumn) {
            $query->order($this->db->quoteName($orderColumn). ' ' . $orderDirection);
        }

        if ($limit > 0) {
            $this->db->setQuery($query, $start, $limit);
        } else {
            $this->db->setQuery($query);
        }

        if ($index) {
            $this->items = (array)$this->db->loadAssocList($index);
        } else {
            $this->items = (array)$this->db->loadAssocList();
        }
    }

    /**
     * Create an object Attachment and return it.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     *
     * $attachments   = new Userideas\Attachment\Attachments(\JFactory::getDbo());
     * $attachments->load($options);
     *
     * $attachmentId = 1;
     * $attachment   = $attachments->getAttachment($attachmentId);
     * </code>
     *
     * @param int $id
     *
     * @return null|Attachment
     *
     * @throws \UnexpectedValueException
     */
    public function getAttachment($id)
    {
        if (!$id) {
            throw new \UnexpectedValueException(\JText::_('COM_USERIDEAS_ERROR_INVALID_ID'));
        }

        $attachment = null;

        foreach ($this->items as $item) {
            if ((int)$item['id'] === (int)$id) {
                $attachment = new Attachment($this->db);
                $attachment->bind($item);
                break;
            }
        }

        return $attachment;
    }

    /**
     * Return the attachments as array with objects.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     *
     * $attachments   = new Userideas\Attachment\Attachments(\JFactory::getDbo());
     * $attachments->load($options);
     *
     * $attachments = $attachments->getAttachments();
     * </code>
     *
     * @param bool $resetKeys
     *
     * @return array
     */
    public function getAttachments($resetKeys = true)
    {
        $results = array();

        foreach ($this->items as $key => $item) {
            $attachment = new Attachment($this->db);
            $attachment->bind($item);

            if (!$resetKeys) {
                $results[$key] = $attachment;
            } else {
                $results[] = $attachment;
            }
        }

        return $results;
    }

    /**
     * Count the attachments assigned to an item and comments.
     *
     * <code>
     * $ids = array(1,2,3,4);
     *
     * $attachments = new Userideas\Attachment\Attachments(\JFactory::getDbo());
     * $attachmentsNumber = $attachments->countAttachments($ids, 'item');
     * </code>
     *
     * @param array $itemIds
     * @param string $type It could be "item" or "comment".
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    public function countAttachments(array $itemIds, $type)
    {
        $results = array();

        if (count($itemIds) > 0) {
            $query = $this->db->getQuery(true);

            if (strcmp('item', $type) === 0) {
                $query
                    ->select('a.id, COUNT(*) AS number, a.item_id')
                    ->from($this->db->quoteName('#__uideas_attachments', 'a'))
                    ->where('a.item_id IN (' . implode(',', $itemIds) . ')')
                    ->where('a.source = ' . $this->db->quote('item'))
                    ->group($this->db->quoteName('item_id'));

                $this->db->setQuery($query);
                $results = (array)$this->db->loadObjectList('item_id');
            } else {
                $query
                    ->select('a.id, COUNT(*) AS number, a.comment_id')
                    ->from($this->db->quoteName('#__uideas_attachments', 'a'))
                    ->where('a.comment_id IN (' . implode(',', $itemIds) . ')')
                    ->where('a.source = ' . $this->db->quote('comment'))
                    ->group($this->db->quoteName('comment_id'));

                $this->db->setQuery($query);
                $results = (array)$this->db->loadObjectList('comment_id');
            }
        }

        return $results;
    }
}
