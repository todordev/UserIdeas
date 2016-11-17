<?php
/**
 * @package         Userideas
 * @subpackage      Statuses
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Status;

use Prism\Database;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing statuses.
 *
 * @package      Userideas
 * @subpackage   Statuses
 */
class Statuses extends Database\Collection
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
     *
     * @throws \RuntimeException
     */
    public function load(array $options = array())
    {
        $orderDirection = $this->getOptionOrderDirection($options);
        $limit          = $this->getOptionLimit($options);

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select('a.id, a.title, a.default')
            ->from($this->db->quoteName('#__uideas_statuses', 'a'))
            ->order('a.title ' . $orderDirection);

        if ($limit > 0) {
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
     * $statusOptions = $statuses->getStatusOptions();
     * </code>
     *
     * @return array
     */
    public function getStatusOptions()
    {
        $options = array();

        foreach ($this->items as $status) {
            $options[] = array('text' => $status->title, 'value' => $status->id);
        }

        return $options;
    }
}
