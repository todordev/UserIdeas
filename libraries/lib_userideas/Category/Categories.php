<?php
/**
 * @package      Userideas
 * @subpackage   Categories
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Category;

defined('_JEXEC') or die;

class Categories extends \JCategories
{
    protected $items;

    /**
     * @var \JDatabaseDriver
     */
    protected $db;

    /**
     * Initialize the object.
     *
     * <code>
     * $options = array(
     *     "published" => '1',
     *     "key" => 'id'
     * );
     *
     * $categories   = new Userideas\Category\Categories($options);
     * </code>
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        $options['table']     = '#__uideas_items';
        $options['extension'] = 'com_userideas';

        parent::__construct($options);
    }

    /**
     * This method sets a database driver.
     *
     * <code>
     * $categories   = new Userideas\Category\Categories();
     * $categories->setDb(\JFactory::getDbo());
     * </code>
     *
     * @param \JDatabaseDriver $db
     *
     * @return self
     */
    public function setDb(\JDatabaseDriver $db)
    {
        $this->db = $db;

        return $this;
    }

    /**
     * Load data of items from a database.
     *
     * <code>
     * $options = array(
     *    'type'  => 'array', // array or object
     *    'limit'          => 10,
     *    'sort_direction' => 'DESC'
     * );
     *
     * $items = new Userideas\Item\Items(\JFactory::getDbo());
     * $items->load($options);
     * </code>
     *
     * @param array $options
     *
     * @throws \RuntimeException
     */
    public function load(array $options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select(
                'a.id, a.title, a.alias, a.description, a.access, a.params, ' .
                $query->concatenate(array('a.id', 'a.alias'), ':') . ' AS slug'
            )
            ->from($this->db->quoteName('#__categories', 'a'));

        // Filter by IDs.
        $ids    = array_key_exists('ids', $options) ? (array)$options['ids'] : array();
        if (count($ids) > 0) {
            $query->where($this->db->quoteName('a.id') . ' IN (' . implode(',', $ids) . ')');
        }

        // Filter by extension.
        $query->where('a.extension = ' . $this->db->quote('com_userideas'));

        // Filter by Parent ID.
        $parentId    = array_key_exists('parent_id', $options) ? (int)$options['parent_id'] : 0;
        if ($parentId > 0) {
            $query->where($this->db->quoteName('a.parent_id') .'='. (int)$parentId);
        }

        // Filter by state.
        $state  = array_key_exists('state', $options) ? (int)$options['state'] : null;
        if ($state !== null) {
            $query->where('a.published = ' . (int)$state);
        }

        // Filter by access level.
        $groups  = array_key_exists('access', $options) ? (array)$options['access'] : array();
        if (is_array($groups) and count($groups) > 0) {
            $groups = implode(',', $groups);
            $query->where('a.access IN (' . $groups . ')');
        }

        // Order by column.
        $orderColumn  = array_key_exists('order_column', $options) ? $options['order_column'] : 'title';
        if ($orderColumn) {
            $orderDirection = array_key_exists('order_direction', $options) ? $options['order_direction'] : 'ASC';
            $query->order($this->db->quoteName('a.'.$orderColumn) . ' ' . $orderDirection);
        }

        // Order limit.
        $limit = array_key_exists('limit', $options) ? $options['limit'] : 20;
        if ($limit > 0) {
            $start = 0;
            $this->db->setQuery($query, $start, $limit);
        } else {
            $this->db->setQuery($query);
        }

        $this->items = (array)$this->db->loadObjectList();
    }

    /**
     * Return items as array.
     *
     * @param bool $resetKeys
     *
     * @return array
     */
    public function toArray($resetKeys = false)
    {
        return (!$resetKeys) ? (array)$this->items : (array)array_values($this->items);
    }
}
