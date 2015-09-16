<?php
/**
 * @package      UserIdeas
 * @subpackage   Categories
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace UserIdeas\Category;

use Prism\Database\TableImmutable;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality for managing a category.
 *
 * @package      UserIdeas
 * @subpackage   Categories
 */
class Category extends TableImmutable
{
    protected $id;
    protected $title;
    protected $description;

    /**
     * This method loads data about category from a database.
     *
     * <code>
     * $categoryId = 1;
     *
     * $category   = new UserIdeas\Category\Category(\JFactory::getDbo());
     * $category->load($categoryId);
     * </code>
     *
     * @param array $keys
     * @param array $options
     */
    public function load($keys, $options = array())
    {
        $query = $this->db->getQuery(true);
        $query
            ->select(
                "a.title, a.description," .
                $query->concatenate(array("a.id", "a.alias"), ":") . " AS slug"
            )
            ->from($this->db->quoteName("#__categories", "a"));

        if (is_array($keys)) {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName("a.".$key) ." = ". $this->db->quote($value));
            }
        } else {
            $query->where("a.id = " . (int)$keys);
        }

        $query->where("a.extension = " . $this->db->quote("com_userideas"));

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!empty($result)) {
            $this->bind($result);
        }
    }

    /**
     * This method returns category title.
     *
     * <code>
     * $categoryId = 1;
     *
     * $category   = new UserIdeas\Category\Category(\JFactory::getDbo());
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
     * $category   = new UserIdeas\Category\Category(\JFactory::getDbo());
     * $category->load($categoryId);
     *
     * $description = $category->getDescription();
     * </code>
     */
    public function getDescription()
    {
        return $this->description;
    }
}
