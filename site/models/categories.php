<?php
/**
 * @package      Userideas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class UserideasModelCategories extends JModelList
{
    /**
     * Constructor.
     *
     * @param   array $config An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array();
        }

        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Load the component parameters.
        $params = $app->getParams();
        $this->setState('params', $params);

        parent::populateState('title', 'asc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string $id A prefix for the store id.
     *
     * @return  string      A store id.
     * @since   1.6
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.category_id');
        $id .= ':' . $this->getState('filter.status_id');
        $id .= ':' . $this->getState('filter.search');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  JDatabaseQuery
     * @since   1.6
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        /** @var $db JDatabaseDriver */

        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.title, a.description, a.params ' .
                $query->concatenate(array('a.id', 'a.alias'), ':') . ' AS slug'
            )
        );
        $query
            ->from($db->quoteName('#__categories', 'a'))
            ->where('a.extension = ' . $db->quote('com_userideas'));

        // Filter by category
        $categoryId = (int)$this->getState('filter.category_id');
        if ($categoryId > 0) {
            $query->where('a.catid = ' . (int)$categoryId);
        }

        // Filter by access level.
        $user   = JFactory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());
        if (count($groups) > 0) {
            $query->where('a.access IN (' . $groups . ')');
        }

        // Filter by string in title.
        $search = trim($this->getState('filter.search'));
        if ($search !== '') {
            $escaped = $db->escape($search, true);
            $quoted  = $db->quote('%' . $escaped . '%', false);
            $query->where('a.title LIKE ' . $quoted);
        }

        // Add the list ordering clause.
        $orderString = $this->getOrderString();
        $query->order($db->escape($orderString));

        return $query;
    }

    protected function getOrderString()
    {
        $orderCol  = $this->getState('list.ordering');
        $orderDirn = $this->getState('list.direction');

        return $orderCol . ' ' . $orderDirn;
    }
}
