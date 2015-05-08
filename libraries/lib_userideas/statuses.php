<?php
/**
 * @package         UserIdeas
 * @subpackage      Statuses
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing statuses.
 *
 * @package      UserIdeas
 * @subpackage   Statuses
 */
class UserIdeasStatuses implements Iterator, Countable, ArrayAccess
{
    protected $options = array();

    protected $items = array();

    protected $position = 0;

    /**
     * @var JDatabaseDriver
     */
    protected $db;

    protected static $instance;

    /**
     * This method initializes the object.
     *
     * @param JDatabaseDriver $db
     */
    public function __construct($db = null)
    {
        $this->db = $db;
    }

    /**
     * This method sets a database driver.
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
     * Create an instance of the object.
     *
     * <code>
     * $options = array(
     *    "limit"          => 10,
     *    "sort_direction" => "DESC"
     * );
     *
     * $statuses     = UserIdeasStatuses::getInstance(JFactory::getDbo(), $options);
     * </code>
     *
     * @param JDatabaseDriver $db
     * @param array $options
     *
     * @return null|UserIdeasStatuses
     */
    public static function getInstance(JDatabaseDriver $db, $options = array())
    {
        if (is_null(self::$instance)) {
            $item           = new UserIdeasStatuses($db);
            $item->load($options);
            self::$instance = $item;
        }

        return self::$instance;
    }

    /**
     * Load data of statuses from a database.
     *
     * <code>
     * $options = array(
     *    "limit"          => 10,
     *    "sort_direction" => "DESC"
     * );
     *
     * $statuses = new UserIdeasStatuses(JFactory::getDbo());
     * $statuses->load($options);
     * </code>
     *
     * @param array $options
     */
    public function load($options = array())
    {
        $sortDir = (!isset($options["sort_direction"])) ? "DESC" : $options["sort_direction"];
        $sortDir = (strcmp("DESC", $sortDir) == 0) ? "DESC" : "ASC";

        $limit   = (int)(!isset($options["limit"])) ? 0 : $options["limit"];

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select("a.id, a.name, a.default")
            ->from($this->db->quoteName("#__uideas_statuses", "a"))
            ->order("a.name " . $sortDir);

        if (!empty($limit)) {
            $this->db->setQuery($query, 0, $limit);
        } else {
            $this->db->setQuery($query);
        }

        $results = $this->db->loadObjectList();

        if (!empty($results)) {
            $this->items = $results;
        }

    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Return the current element.
     *
     * @see Iterator::current()
     */
    public function current()
    {
        return (!isset($this->items[$this->position])) ? null : $this->items[$this->position];
    }

    /**
     * Return the key of the current element.
     *
     * @see Iterator::key()
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Move forward to next element.
     *
     * @see Iterator::next()
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Checks if current position is valid.
     *
     * @see Iterator::valid()
     */
    public function valid()
    {
        return isset($this->items[$this->position]);
    }

    /**
     * Count elements of an object.
     *
     * @see Countable::count()
     */
    public function count()
    {
        return (int)count($this->items);
    }

    /**
     * Offset to set.
     *
     * @see ArrayAccess::offsetSet()
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * Whether a offset exists.
     *
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    /**
     * Offset to unset.
     *
     * @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * Offset to retrieve.
     *
     * @see ArrayAccess::offsetGet()
     */
    public function offsetGet($offset)
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    /**
     * Return default status.
     *
     * <code>
     * $statuses = new UserIdeasStatuses();
     * $statuses->setDb(JFactory::getDbo());
     * $statuses->load($options);
     * </code>
     *
     * @return null|object
     */
    public function getDefault()
    {
        foreach ($this->items as $status) {
            if (!empty($status->default)) {
                return $status;
            }
        }

        return null;
    }

    /**
     * This method prepares and returns the statuses as an array,
     * which can be used as options.
     *
     * <code>
     * $statuses = new UserIdeasStatuses(JFactory::getDbo());
     * $statuses->load($options);
     *
     * $statusesOptions = $statuses->getStatusesOptions();
     * </code>
     *
     * @return array
     */
    public function getStatusesOptions()
    {
        $options = array();

        foreach ($this->items as $status) {
            $options[] = array("text" => $status->name, "value" => $status->id);
        }

        return $options;
    }
}
