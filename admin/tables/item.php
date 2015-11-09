<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class UserIdeasTableItem extends JTable
{
    public $id;
    public $title;
    public $alias;
    public $description;
    public $votes;
    public $record_date;
    public $ordering;
    public $published;
    public $status_id;
    public $catid;
    public $user_id;

    protected $catslug;

    public function __construct(JDatabaseDriver $db)
    {
        parent::__construct('#__uideas_items', 'id', $db);

        JTableObserverTags::createObserver($this, array('typeAlias' => 'com_userideas.item'));
    }

    public function getSlug()
    {
        return $this->id . ':' . $this->alias;
    }

    public function getCategorySlug()
    {
        if ($this->catslug === null) {
            $db    = $this->getDbo();
            $query = $db->getQuery(true);

            $query
                ->select($query->concatenate(array('a.id', 'a.alias'), ':') . ' AS catslug')
                ->from($db->quoteName('#__categories', 'a'))
                ->where('a.id = ' . (int)$this->catid);

            $db->setQuery($query);
            $this->catslug = (string)$db->loadResult();
        }

        return $this->catslug;
    }
}
