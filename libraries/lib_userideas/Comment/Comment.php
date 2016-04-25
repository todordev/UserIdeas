<?php
/**
 * @package      Userideas
 * @subpackage   Comments
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Comment;

use Prism\Database;
use Prism\Constants;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality for managing a comment.
 *
 * @package      Userideas
 * @subpackage   Comments
 */
class Comment extends Database\TableImmutable
{
    protected $id;
    protected $comment;
    protected $record_date;
    protected $published;
    protected $item_id;
    protected $user_id;

    /**
     * This method loads data about a comment from a database.
     *
     * <code>
     * $commentId = 1;
     *
     * $comment   = new Userideas\Comment\Comment(\JFactory::getDbo());
     * $comment->load($commentId);
     * </code>
     *
     * @param int|array $keys
     * @param array $options
     */
    public function load($keys, array $options = array())
    {
        $query = $this->db->getQuery(true);

        $query
            ->select('a.id, a.comment, a.record_date, a.published, a.item_id, a.user_id')
            ->from($this->db->quoteName('#__uideas_comments', 'a'));

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
     * Returns comment ID.
     *
     * <code>
     * $commentId = 1;
     *
     * $comment   = new Userideas\Comment\Comment(\JFactory::getDbo());
     * $comment->load($commentId);
     *
     * if (!$comment->getId()) {
     * ...
     * }
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Returns the content of the comment.
     *
     * <code>
     * $commentId = 1;
     *
     * $comment   = new Userideas\Comment\Comment(\JFactory::getDbo());
     * $comment->load($commentId);
     *
     * echo $comment->getComment();
     * </code>
     *
     * @return string
     */
    public function getComment()
    {
        return (string)$this->comment;
    }

    /**
     * Check if the comments is published.
     *
     * <code>
     * $commentId = 1;
     *
     * $comment   = new Userideas\Comment\Comment(\JFactory::getDbo());
     * $comment->load($commentId);
     *
     * if ($comment->isPublished()) {
     * ...
     * }
     * </code>
     *
     * @return bool
     */
    public function isPublished()
    {
        return ((int)$this->published === Constants::PUBLISHED);
    }

    /**
     * Returns item ID.
     *
     * <code>
     * $commentId = 1;
     *
     * $comment   = new Userideas\Comment\Comment(\JFactory::getDbo());
     * $comment->load($commentId);
     *
     * echo $comment->getItemId()
     * </code>
     *
     * @return int
     */
    public function getItemId()
    {
        return (int)$this->item_id;
    }

    /**
     * Returns user ID.
     *
     * <code>
     * $commentId = 1;
     *
     * $comment   = new Userideas\Comment\Comment(\JFactory::getDbo());
     * $comment->load($commentId);
     *
     * echo $comment->getUserId();
     * </code>
     *
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }
}
