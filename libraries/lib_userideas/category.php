<?php
/**
 * @package      UserIdeas
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality for managing a category.
 */
class UserIdeasCategory {
    
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
     * @param integer $id Category ID
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * This method sets a database driver.
     *
     * @param JDatabaseDriver $db
     * @return self
     */
    public function setDb(JDatabaseDriver $db) {
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
     * $category   = new UserIdeasCategory($categoryId);
     * $category->setDb($db);
     * $category->load();
     * </code>
     */
    public function load() {

        $query = $this->db->getQuery(true);
        $query
            ->select(
                "a.title, a.description," .
                $query->concatenate(array("a.id", "a.alias"), ":") . " AS slug")
            ->from($this->db->quoteName("#__categories", "a"))
            ->where("a.id = ". (int)$this->id)
            ->where("a.extension = ". $this->db->quote("com_userideas"));

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if(!empty($result)) {
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
     * $category   = new UserIdeasCategory();
     * $category->bind($data);
     * </code>
     */
    public function bind($data, $ignored = array()) {

        foreach($data as $key => $value) {
            if(!in_array($key, $ignored)) {
                $this->$key = $value;
            }
        }

    }

    /**
     * This method returns category title.
     *
     * <code>
     * $db         = JFactory::getDbo();
     * $categoryId = 1;
     *
     * $category   = new UserIdeasCategory($categoryId);
     * $category->setDb($db);
     * $category->load();
     *
     * $title = $category->getTitle();
     * </code>
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * This method returns category description.
     *
     * <code>
     * $db         = JFactory::getDbo();
     * $categoryId = 1;
     *
     * $category   = new UserIdeasCategory($categoryId);
     * $category->setDb($db);
     * $category->load();
     *
     * $description = $category->getDescription();
     * </code>
     */
    public function getDescription() {
        return $this->description;
    }
    
}