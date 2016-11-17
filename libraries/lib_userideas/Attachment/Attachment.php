<?php
/**
 * @package      Userideas
 * @subpackage   Attachments
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Attachment;

use Prism\Database;
use Joomla\Registry\Registry;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality for managing a attachment.
 *
 * @package      Userideas
 * @subpackage   Attachments
 */
class Attachment extends Database\Table
{
    protected $id;
    protected $filename;
    protected $filesize;
    protected $mime;
    protected $record_date;
    protected $item_id;
    protected $comment_id;
    protected $user_id;
    protected $source;

    /**
     * @var Registry
     */
    protected $attributes;

    /**
     * This method loads data about a attachment from a database.
     *
     * <code>
     * $attachmentId = 1;
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     * </code>
     *
     * @param int|array $keys
     * @param array $options
     *
     * @throws \RuntimeException
     */
    public function load($keys, array $options = array())
    {
        $query = $this->db->getQuery(true);

        $query
            ->select('a.id, a.filename, a.filesize, a.record_date, a.attributes, a.item_id, a.user_id, a.comment_id, a.source, a.mime')
            ->from($this->db->quoteName('#__uideas_attachments', 'a'));

        if (is_array($keys)) {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName('a.'.$key) .' = '. $this->db->quote($value));
            }
        } else {
            $query->where('a.id = ' . (int)$keys);
        }
        
        $this->db->setQuery($query);

        $result = (array)$this->db->loadAssoc();

        if (array_key_exists('attributes', $result)) {
            $this->setAttributes($result['attributes']);
            unset($result['attributes']);
        }

        $this->bind($result);
    }

    /**
     * This method saves the data of the item to database.
     *
     * <code>
     * $data = array(
     *      "filename"      => 'picture.png',
     *      "filesize"      => 10000
     *      "mime"          => 'image/png'
     *      "item_id"       => 1,
     *      "user_id"       => 2,
     *      "source"        => 'item'
     * );
     *
     * $item   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $item->bind($data);
     * $item->store();
     * </code>
     */
    public function store()
    {
        if (!$this->id) {
            $this->insertObject();
        } else {
            $this->updateObject();
        }
    }

    protected function updateObject()
    {
        if (!$this->attributes) {
            $this->attributes = '{}';
        }

        $attributes =  is_string($this->attributes) ? $this->db->quote($this->attributes) : $this->db->quote($this->attributes->toString());

        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName('#__uideas_attachments'))
            ->set($this->db->quoteName('filename') . '=' . $this->db->quote($this->filename))
            ->set($this->db->quoteName('filesize') . '=' . (int)$this->filesize)
            ->set($this->db->quoteName('attributes') . '=' . $attributes)
            ->set($this->db->quoteName('mime') . '=' . $this->db->quote($this->mime))
            ->set($this->db->quoteName('item_id') . '=' . (int)$this->item_id)
            ->set($this->db->quoteName('comment_id') . '=' . (int)$this->comment_id)
            ->set($this->db->quoteName('user_id') . '=' . (int)$this->user_id)
            ->set($this->db->quoteName('source') . '=' . $this->db->quote($this->source))
            ->where($this->db->quoteName('id') . '=' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName('#__uideas_attachments'))
            ->set($this->db->quoteName('filename') . '=' . $this->db->quote($this->filename))
            ->set($this->db->quoteName('filesize') . '=' . (int)$this->filesize)
            ->set($this->db->quoteName('mime') . '=' . $this->db->quote($this->mime))
            ->set($this->db->quoteName('item_id') . '=' . (int)$this->item_id)
            ->set($this->db->quoteName('comment_id') . '=' . (int)$this->comment_id)
            ->set($this->db->quoteName('user_id') . '=' . (int)$this->user_id)
            ->set($this->db->quoteName('source') . '=' . $this->db->quote($this->source));

        if (!$this->attributes) {
            $this->attributes = '{}';
        }

        $attributes =  is_string($this->attributes) ? $this->db->quote($this->attributes) : $this->db->quote($this->attributes->toString());
        $query->set($this->db->quoteName('attributes') . '=' . $attributes);

        $this->db->setQuery($query);
        $this->db->execute();

        $this->id = $this->db->insertid();
    }

    /**
     * Remove the record from database and reset the object.
     *
     * <code>
     * $attachmentId = 1;
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * $attachment->remove();
     * </code>
     *
     * @throws \RuntimeException
     */
    public function remove()
    {
        $query = $this->db->getQuery(true);

        $query
            ->delete($this->db->quoteName('#__uideas_attachments'))
            ->where($this->db->quoteName('id') . '=' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();

        // Reset the object properties.
        $this->reset();
    }

    /**
     * Returns attachment ID.
     *
     * <code>
     * $attachmentId = 1;
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * if (!$attachment->getId()) {
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
     * Returns the filename of the attachment.
     *
     * <code>
     * $attachmentId = 1;
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * echo $attachment->getFilename();
     * </code>
     *
     * @return string
     */
    public function getFilename()
    {
        return (string)$this->filename;
    }

    /**
     * Set the filename of the attachment.
     *
     * <code>
     * $attachmentId = 1;
     * $filename = 'filename';
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * $attachment->setFilename($filename);
     * </code>
     *
     * @param string $filename
     *
     * @return self
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Returns the filesize of the attachment.
     *
     * <code>
     * $attachmentId = 1;
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * echo $attachment->getAttachment();
     * </code>
     *
     * @return int
     */
    public function getFilesize()
    {
        return (int)$this->filesize;
    }

    /**
     * Set the filesize of the attachment.
     *
     * <code>
     * $attachmentId = 1;
     * $filesize = 123456;
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * $attachment->setFilesize($filesize);
     * </code>
     *
     * @param int $filesize
     *
     * @return self
     */
    public function setFilesize($filesize)
    {
        $this->filesize = (int)$filesize;

        return $this;
    }

    /**
     * Returns the mime type of the attachment.
     *
     * <code>
     * $attachmentId = 1;
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * echo $attachment->getMime();
     * </code>
     *
     * @return int
     */
    public function getMime()
    {
        return (string)$this->mime;
    }

    /**
     * Set the mime type of the attachment.
     *
     * <code>
     * $attachmentId = 1;
     * $mime = 'image/png';
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * $attachment->setMime($mime);
     * </code>
     *
     * @param string $mime
     *
     * @return self
     */
    public function setMime($mime)
    {
        $this->mime = $mime;

        return $this;
    }

    /**
     * Check if the attachments belongs to item.
     *
     * <code>
     * $attachmentId = 1;
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * if ($attachment->belongsToItem()) {
     * ...
     * }
     * </code>
     *
     * @return bool
     */
    public function belongsToItem()
    {
        return (strcmp($this->source, 'item') === 0);
    }

    /**
     * Check if the attachments belongs to comment.
     *
     * <code>
     * $attachmentId = 1;
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * if ($attachment->belongsToComment()) {
     * ...
     * }
     * </code>
     *
     * @return bool
     */
    public function belongsToComment()
    {
        return (strcmp($this->source, 'comment') === 0);
    }

    /**
     * Return the attributes of the attachment.
     *
     * <code>
     * $attachmentId = 1;
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * $attributes = $attachment->getAttributes();
     * </code>
     *
     * @return Registry
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set the attributes of the attachment.
     *
     * <code>
     * $attachmentId = 1;
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * $attachment->setAttributes();
     *
     * </code>
     *
     * @param string|array|Registry $attributes
     *
     * @return self
     */
    public function setAttributes($attributes)
    {
        if (is_string($attributes) or is_array($attributes)) {
            $this->attributes = new Registry($attributes);
        } elseif ($attributes instanceof Registry) {
            $this->attributes = $attributes;
        }

        return $this;
    }

    /**
     * Returns the item ID to which belongs the attachment.
     *
     * <code>
     * $attachmentId = 1;
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * echo $attachment->getItemId()
     * </code>
     *
     * @return int
     */
    public function getItemId()
    {
        return (int)$this->item_id;
    }

    /**
     * Set the item ID on which the attachment belongs.
     *
     * <code>
     * $attachmentId = 1;
     * $itemId = 2;
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * $attachment->setItemId($itemId);
     * </code>
     *
     * @param int $itemId
     *
     * @return self
     */
    public function setItemId($itemId)
    {
        $this->item_id = $itemId;

        return $this;
    }

    /**
     * Set the comment ID on which the attachment belongs.
     *
     * <code>
     * $attachmentId = 1;
     * $commentId = 2;
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * $attachment->setCommentId($commentId);
     * </code>
     *
     * @param int $commentId
     *
     * @return self
     */
    public function setCommentId($commentId)
    {
        $this->comment_id = $commentId;

        return $this;
    }

    /**
     * Returns comment ID.
     *
     * <code>
     * $attachmentId = 1;
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * echo $attachment->getCommentId();
     * </code>
     *
     * @return int
     */
    public function getCommentId()
    {
        return (int)$this->comment_id;
    }

    /**
     * Returns user ID.
     *
     * <code>
     * $attachmentId = 1;
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * echo $attachment->getUserId();
     * </code>
     *
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }

    /**
     * Set the user ID on which the attachment belongs.
     *
     * <code>
     * $attachmentId = 1;
     * $userId = 2;
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * $attachment->setUserId($userId);
     * </code>
     *
     * @param int $userId
     *
     * @return self
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Returns the source of the item.
     *
     * <code>
     * $attachmentId = 1;
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * echo $attachment->getSource();
     * </code>
     *
     * @return string
     */
    public function getSource()
    {
        return (string)$this->source;
    }

    /**
     * Set the source of the attachment.
     * You can only set the following sources - item or comment.
     *
     * <code>
     * $attachmentId = 1;
     * $source = 'item';
     *
     * $attachment   = new Userideas\Attachment\Attachment(\JFactory::getDbo());
     * $attachment->load($attachmentId);
     *
     * $attachment->setSource($source);
     * </code>
     *
     * @param string $source
     *
     * @throws \InvalidArgumentException
     * @return self
     */
    public function setSource($source)
    {
        $allowedSources = array('item', 'comment');
        if (!in_array($source, $allowedSources, true)) {
            throw new \InvalidArgumentException('You can only set the following sources - item or comment');
        }

        $this->source = $source;

        return $this;
    }
}
