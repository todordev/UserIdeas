<?php
/**
 * @package      UserIdeas
 * @subpackage   Statuses
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace UserIdeas\Status;

use Prism\Database\TableImmutable;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality for managing a status.
 *
 * @package      UserIdeas
 * @subpackage   Statuses
 */
class Status extends TableImmutable
{
    protected $id;
    protected $name;
    protected $default;

    /**
     * This method loads data about a status from a database.
     *
     * <code>
     * $statusId = 1;
     *
     * $status   = new UserIdeas\Status\Status(\JFactory::getDbo());
     * $status->load($statusId);
     * </code>
     * 
     * @param int|array $keys
     * @param array $options
     */
    public function load($keys, $options = array())
    {
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.name, a.default, a.params")
            ->from($this->db->quoteName("#__uideas_statuses", "a"));

        if (is_array($keys)) {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName("a.".$key) ." = ". $this->db->quote($value));
            }
        } else {
            $query->where("a.id = " . (int)$keys);
        }
        
        $this->db->setQuery($query);

        $result = (array)$this->db->loadAssoc();

        if (!empty($result["params"])) {
            $params = json_decode($result["params"]);
            if (!empty($params)) {
                $result["params"] = $params;
            }
        }
        $this->bind($result);
    }

    /**
     * Returns status ID.
     *
     * <code>
     * $statusId = 1;
     *
     * $status   = new UserIdeas\Status\Status(\JFactory::getDbo());
     * $status->load($statusId);
     *
     * if (!$status->getId()) {
     * ...
     * }
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns status name.
     *
     * <code>
     * $statusId = 1;
     *
     * $status   = new UserIdeas\Status\Status(\JFactory::getDbo());
     * $status->load($statusId);
     *
     * $name = $status->getName();
     * </code>
     *
     * @return string
     */
    public function getName()
    {
        return (string)$this->name;
    }

    /**
     * This method checks the status if it is default state.
     *
     * <code>
     * $statusId = 1;
     *
     * $status   = new UserIdeas\Status\Status(\JFactory::getDbo());
     * $status->load($statusId);
     *
     * if ($status->isDefault()) {
     * ...
     * }
     * </code>
     *
     * @return bool
     */
    public function isDefault()
    {
        return (!$this->default) ? false : true;
    }
}
