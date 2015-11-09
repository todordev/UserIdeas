<?php
/**
 * @package      UserIdeas
 * @subpackage   Categories
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Category;

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
     * @var \JHelperTags
     */
    protected $tagsHelper;

    protected $tags;

    protected $contentAlias;

    /**
     * Initialize the object.
     *
     * @param \JDatabaseDriver $db
     */
    public function __construct(\JDatabaseDriver $db = null)
    {
        parent::__construct($db);
        
        $this->contentAlias = 'com_userideas.category';
    }
    
    /**
     * This method loads data about category from a database.
     *
     * <code>
     * $categoryId = 1;
     *
     * $category   = new Userideas\Category\Category(\JFactory::getDbo());
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
                'a.id, a.title, a.description,' .
                $query->concatenate(array('a.id', 'a.alias'), ':') . ' AS slug'
            )
            ->from($this->db->quoteName('#__categories', 'a'));

        if (is_array($keys)) {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName('a.'.$key) .' = '. $this->db->quote($value));
            }
        } else {
            $query->where('a.id = ' . (int)$keys);
        }

        $query->where('a.extension = ' . $this->db->quote('com_userideas'));

        $this->db->setQuery($query);
        $result = (array)$this->db->loadAssoc();

        $this->bind($result);
    }

    /**
     * This method returns category title.
     *
     * <code>
     * $categoryId = 1;
     *
     * $category   = new Userideas\Category\Category(\JFactory::getDbo());
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
     * $category   = new Userideas\Category\Category(\JFactory::getDbo());
     * $category->load($categoryId);
     *
     * $description = $category->getDescription();
     * </code>
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * This method returns category description.
     *
     * <code>
     * $categoryId = 1;
     *
     * $category   = new Userideas\Category\Category(\JFactory::getDbo());
     * $category->load($categoryId);
     *
     * $category->setTagsHelper(new \JHelperTags);
     * </code>
     *
     * @param \JHelperTags $tags
     *
     * @return self
     */
    public function setTagsHelper(\JHelperTags $tags)
    {
        $this->tagsHelper = $tags;

        return $this;
    }

    /**
     * Return tags helper.
     *
     * <code>
     * $category   = new Userideas\Category\Category(\JFactory::getDbo());
     *
     * $tagsHelper = $category->getTagsHelper();
     * </code>
     *
     * @return null|\JHelperTags
     */
    public function getTagsHelper()
    {
        return $this->tagsHelper;
    }

    /**
     * This method returns category description.
     *
     * <code>
     * $categoryId = 1;
     *
     * $category   = new Userideas\Category\Category(\JFactory::getDbo());
     * $category->load($categoryId);
     *
     * $category->setTagsHelper(new \JHelperTags);
     * </code>
     *
     * @param bool $getTagData If true, data from the tags table will be included, defaults to true.
     *
     * @return null|array
     */
    public function getTags($getTagData = true)
    {
        if (($this->tags === null) and ($this->tagsHelper instanceof \JHelperTags)) {
            $this->tags = $this->tagsHelper->getItemTags($this->contentAlias, $this->id, $getTagData);
        }
        
        return $this->tags;
    }
}
