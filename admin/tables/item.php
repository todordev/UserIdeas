<?php
/**
 * @package      Userideas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class UserideasTableItem extends JTable
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

    /**
     * Method to compute the default name of the asset.
     * The default name is in the form table_name.id
     * where id is the value of the primary key of the table.
     *
     * @return  string
     *
     * @since   11.1
     */
    protected function _getAssetName()
    {
        return 'com_userideas.item.' .(int)$this->id;
    }

    /**
     * Method to get the parent asset under which to register this one.
     * By default, all assets are registered to the ROOT node with ID,
     * which will default to 1 if none exists.
     * The extended class can define a table and id to lookup.  If the
     * asset does not exist it will be created.
     *
     * @param   JTable  $table A JTable object for the asset parent.
     * @param   integer $id    Id to look up
     *
     * @return  integer
     *
     * @since   11.1
     */
    protected function _getAssetParentId(JTable $table = null, $id = null)
    {
        // For simple cases, parent to the asset root.
        $assets = self::getInstance('Asset', 'JTable', array('dbo' => $this->getDbo()));
        /** @var JTableAsset $assets */

        $assets->loadByName('com_userideas');

        return (int)$assets->id;
    }
}
