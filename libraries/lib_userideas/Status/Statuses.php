<?php
/**
 * @package         UserIdeas
 * @subpackage      Statuses
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/licenses/gpl-3.0.en.html
 */

namespace UserIdeas\Status;

use Prism\Database\ArrayObject;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing statuses.
 *
 * @package      UserIdeas
 * @subpackage   Statuses
 */
class Statuses extends ArrayObject
{
    protected $options = array();

    protected static $instance;

    /**
     * Create an instance of the object.
     *
     * <code>
     * $options = array(
     *    "limit"          => 10,
     *    "sort_direction" => "DESC"
     * );
     *
     * $statuses     = UserIdeas\Status\Statuses::getInstance(\JFactory::getDbo(), $options);
     * </code>
     *
     * @param \JDatabaseDriver $db
     * @param array $options
     *
     * @return null|Statuses
     */
    public static function getInstance(\JDatabaseDriver $db, $options = array())
    {
        if (is_null(self::$instance)) {
            $item           = new Statuses($db);
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
     * $statuses = new UserIdeas\Status\Statuses(\JFactory::getDbo());
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

        $this->items = (array)$this->db->loadObjectList();
    }

    /**
     * Return default status.
     *
     * <code>
     * $statuses = new UserIdeas\Status\Statuses(\JFactory::getDbo());
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
     * $statuses = new UserIdeas\Status\Statuses(\JFactory::getDbo());
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
