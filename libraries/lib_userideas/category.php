<?php
/**
 * @package      UserIdeas
 * @subpackage   Categories
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality for managing a category.
 *
 * @package      UserIdeas
 * @subpackage   Categories
 */
class UserIdeasCategory
{
    protected $id;
    protected $title;
    protected $description;

    /**
     * @var JDatabaseDriver
     */
    protected $db;

    /**
     * This method initializes the object.
     *
     * <code>
     * $category   = new UserIdeasCategory(JFactory::getDbo());
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
     * $category   = new UserIdeasCategory();
     * $category->setDb(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver $db
     *
     * @return self
     */
    public function setDb(JDatabaseDriver $db)
    {
        $this->db = $db;

        return $this;
    }

    /**
     * This method loads data about category from a database.
     *
     * <code>
     * $db         = JFactory::getDbo();
     * $categoryId = 1;
     *
     * $category   = new UserIdeasCategory();
     * $category->setDb($db);
     * $category->load($categoryId);
     * </code>
     */
    public function load($id)
    {
        $query = $this->db->getQuery(true);
        $query
            ->select(
                "a.title, a.description," .
                $query->concatenate(array("a.id", "a.alias"), ":") . " AS slug"
            )
            ->from($this->db->quoteName("#__categories", "a"))
            ->where("a.id = " . (int)$id)
            ->where("a.extension = " . $this->db->quote("com_userideas"));

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!empty($result)) {
            $this->bind($result);
        }
    }

    /**
     * This method sets data to object parameters.
     *
     * <code>
     * $data = array(
     *      "id"    => 1,
     *      "title" => "My title"
     * );
     *
     * $category   = new UserIdeasCategory(JFactory::getDbo());
     * $category->bind($data);
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
     * This method returns category title.
     *
     * <code>
     * $categoryId = 1;
     *
     * $category   = new UserIdeasCategory(JFactory::getDbo());
     * $category->load($categoryId);
     *
     * $title = $category->getTitle();
     * </code>
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * This method returns category description.
     *
     * <code>
     * $categoryId = 1;
     *
     * $category   = new UserIdeasCategory(JFactory::getDbo());
     * $category->load($category);
     *
     * $description = $category->getDescription();
     * </code>
     */
    public function getDescription()
    {
        return $this->description;
    }
}
