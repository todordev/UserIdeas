<?php
/**
 * @package      Userideas
 * @subpackage   Categories
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Category;

defined('_JEXEC') or die;

class Categories extends \JCategories
{
    /**
     * @var \JDatabaseDriver
     */
    protected $db;

    /**
     * Initialize the object.
     *
     * <code>
     * $options = array(
     *     "published" => '1',
     *     "key" => 'id'
     * );
     *
     * $categories   = new Userideas\Category\Categories($options);
     * </code>
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        $options['table']     = '#__uideas_items';
        $options['extension'] = 'com_userideas';

        parent::__construct($options);
    }

    /**
     * This method sets a database driver.
     *
     * <code>
     * $categories   = new Userideas\Category\Categories();
     * $categories->setDb(\JFactory::getDbo());
     * </code>
     *
     * @param \JDatabaseDriver $db
     *
     * @return self
     */
    public function setDb(\JDatabaseDriver $db)
    {
        $this->db = $db;

        return $this;
    }
}
