<?php
/**
 * @package      UserIdeas
 * @subpackage   Statuses
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality for managing a status.
 *
 * @package      UserIdeas
 * @subpackage   Statuses
 */
class UserIdeasStatus
{
    protected $id;
    protected $name;
    protected $default;
    protected $params;

    /**
     * @var JDatabaseDriver
     */
    protected $db;

    /**
     * This method initializes the object.
     *
     * <code>
     * $status   = new UserIdeasStatus(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver $db
     */
    public function __construct(JDatabaseDriver $db = null)
    {
        $this->db = $db;
    }

    /**
     * This method sets a database driver.
     *
     * <code>
     * $status   = new UserIdeasStatus();
     * $status->setDb(JFactory::getDbo());
     * </code>
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
     * This method loads data about a status from a database.
     *
     * <code>
     * $statusId = 1;
     *
     * $status   = new UserIdeasStatus(JFactory::getDbo());
     * $status->load($statusId);
     * </code>
     */
    public function load()
    {
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.name, a.default, a.params")
            ->from($this->db->quoteName("#__uideas_statuses", "a"))
            ->where("a.id = " . (int)$this->id);

        $this->db->setQuery($query);

        $result = $this->db->loadAssoc();
        if (!empty($result)) {

            if (!empty($result["params"])) {
                $params = json_decode($result["params"]);
                if (!empty($params)) {
                    $result["params"] = $params;
                }
            }
            $this->bind($result);
        }
    }

    /**
     * This method set data to object parameters.
     *
     * <code>
     * $data = array(
     *      "name"          => "My status name",
     *      "default"       => 1
     * );
     *
     * $status   = new UserIdeasStatus(JFactory::getDbo());
     * $status->bind($data);
     * $status->store();
     * </code>
     */
    public function bind($data, $ignored = array())
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $ignored)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Returns status ID.
     *
     * <code>
     * $statusId = 1;
     *
     * $status   = new UserIdeasStatus(JFactory::getDbo());
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
     * $status   = new UserIdeasStatus(JFactory::getDbo());
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
     * This method returns a value of a status parameter.
     *
     * <code>
     * $statusId = 1;
     *
     * $status   = new UserIdeasStatus(JFactory::getDbo());
     * $status->load($statusId);
     *
     * $styleClass = $status->getParam("style_class");
     * </code>
     *
     * @param  string $key     A name of a parameter.
     * @param  mixed  $default A default option if the parameter does not exists.
     *
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        return (!isset($this->params[$key])) ? $default : $this->params[$key];
    }

    /**
     * This method checks the status if it is default state.
     *
     * <code>
     * $statusId = 1;
     *
     * $status   = new UserIdeasStatus(JFactory::getDbo());
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
