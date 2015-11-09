<?php
/**
 * @package         UserIdeas
 * @subpackage      Statuses
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Status;

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
     *    'limit'          => 10,
     *    'sort_direction' => 'DESC'
     * );
     *
     * $statuses     = Userideas\Status\Statuses::getInstance(\JFactory::getDbo(), $options);
     * </code>
     *
     * @param \JDatabaseDriver $db
     * @param array $options
     *
     * @return null|Statuses
     */
    public static function getInstance(\JDatabaseDriver $db, array $options = array())
    {
        if (self::$instance === null) {
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
     *    'limit'          => 10,
     *    'sort_direction' => 'DESC'
     * );
     *
     * $statuses = new Userideas\Status\Statuses(\JFactory::getDbo());
     * $statuses->load($options);
     * </code>
     *
     * @param array $options
     */
    public function load($options = array())
    {
        $sortDir = (!array_key_exists('sort_direction', $options)) ? 'DESC' : $options['sort_direction'];
        $sortDir = (strcmp('DESC', $sortDir) === 0) ? 'DESC' : 'ASC';

        $limit   = (!array_key_exists('limit', $options)) ? 0 : (int)$options['limit'];

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select('a.id, a.name, a.default')
            ->from($this->db->quoteName('#__uideas_statuses', 'a'))
            ->order('a.name ' . $sortDir);

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
     * $statuses = new Userideas\Status\Statuses(\JFactory::getDbo());
     * $statuses->load($options);
     * </code>
     *
     * @return null|\stdClass
     */
    public function getDefault()
    {
        foreach ($this->items as $status) {
            if ((int)$status->default === 1) {
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
     * $statuses = new Userideas\Status\Statuses(\JFactory::getDbo());
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
            $options[] = array('text' => $status->name, 'value' => $status->id);
        }

        return $options;
    }
}
