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

/**
 * Get a list of items
 */
class UserideasModelItems extends JModelList
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
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'votes', 'a.votes',
                'hits', 'a.hits',
                'record_date', 'a.record_date',
                'ordering', 'a.ordering',
                'published', 'a.published',
                'user', 'b.name',
                'category', 'c.title',
                'access_title', 'ag.title',
                'status_title', 'd.title'
            );
        }

        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null)
    {
        // Load the filter state.
        $value = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $value);

        // Get filter state
        $value = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', 0, 'int');
        $this->setState('filter.state', $value);

        // Get filter category
        $value = $this->getUserStateFromRequest($this->context . '.filter.category', 'filter_category', 0, 'int');
        $this->setState('filter.category', $value);

        // Get filter status
        $value = $this->getUserStateFromRequest($this->context . '.filter.status_id', 'filter_status_id', 0, 'int');
        $this->setState('filter.status_id', $value);

        // Get filter author
        $value = $this->getUserStateFromRequest($this->context . '.filter.author', 'filter_author', null);
        $this->setState('filter.author', $value);

        // Get filter author
        $value = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access');
        $this->setState('filter.access', $value);

        // Load the component parameters.
        $params = JComponentHelper::getParams($this->option);
        $this->setState('params', $params);

        // List state information.
        parent::populateState('a.record_date', 'desc');
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
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.category');
        $id .= ':' . $this->getState('filter.status_id');
        $id .= ':' . $this->getState('filter.state');
        $id .= ':' . $this->getState('filter.author');
        $id .= ':' . $this->getState('filter.access');

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
        // Create a new query object.
        $db = $this->getDbo();
        /** @var $db JDatabaseDriver */

        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.title, a.alias, a.votes, a.record_date, a.catid, ' .
                'a.ordering, a.published, a.status_id, a.hits, a.user_id, ' .
                'b.name AS user, ' .
                'c.title AS category, ' .
                'd.title AS status_title, d.params AS status_params, d.default AS status_default,' .
                'ag.title AS access_level'
            )
        );
        $query->from($db->quoteName('#__uideas_items', 'a'));
        $query->leftJoin($db->quoteName('#__users', 'b') . ' ON a.user_id = b.id');
        $query->leftJoin($db->quoteName('#__categories', 'c') . ' ON a.catid = c.id');
        $query->leftJoin($db->quoteName('#__uideas_statuses', 'd') . ' ON a.status_id = d.id');

        // Join over the asset groups.
        $query->leftJoin($db->quoteName('#__viewlevels', 'ag') .' ON ag.id = a.access');

        // Filter by category
        $categoryId = $this->getState('filter.category');
        if ($categoryId > 0) {
            $query->where('a.catid = ' . (int)$categoryId);
        }

        // Filter by status
        $statusId = (int)$this->getState('filter.status_id');
        if ($statusId > 0) {
            $query->where('a.status_id = ' . (int)$statusId);
        }

        // Filter by state
        $state = $this->getState('filter.state');
        if (is_numeric($state)) {
            $query->where('a.published = ' . (int)$state);
        } elseif ($state === '') {
            $query->where('(a.published IN (0, 1))');
        }

        // Filter by author
        $author = $this->getState('filter.author');
        if ($author !== null and is_numeric($author)) {
            $query->where('a.user_id = ' . (int)$author);
        }

        // Filter by access level.
        $access = (int)$this->getState('filter.access');
        if ($access > 0) {
            $query->where('a.access = ' . (int)$access);
        }

        // Implement View Level Access
        $user = JFactory::getUser();
        if (!$user->authorise('core.admin')) {
            $groups = implode(',', $user->getAuthorisedViewLevels());
            $query->where('a.access IN (' . $groups . ')');
        }

        // Filter by search in title
        $search = (string)$this->getState('filter.search');
        if ($search !== '') {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int)substr($search, 3));
            } else {
                $escaped = $db->escape($search, true);
                $quoted  = $db->quote('%' . $escaped . '%', false);
                $query->where('a.title LIKE ' . $quoted);
            }
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

        if ($orderCol === 'a.ordering') {
            $orderCol = 'a.catid ' . $orderDirn . ', a.ordering';
        }

        return $orderCol . ' ' . $orderDirn;
    }
}
