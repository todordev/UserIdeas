<?php
/**
 * @package      UserIdeas
 * @subpackage   Categories
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;

class UserIdeasCategories extends JCategories
{
    /**
     * @var JDatabaseDriver
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
     * $categories   = new UserIdeasCategories($options);
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
     * $categories   = new UserIdeasCategories();
     * $categories->setDb(JFactory::getDbo());
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
}
