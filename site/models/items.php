<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class UserIdeasModelItems extends JModelList
{
    /**
     * Constructor.
     *
     * @param   array $config  An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'filter_status', 'a.status_id',
                'filter_category', 'a.catid',
                'filter_search', 'a.title',
            );
        }

        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null)
    {
        // List state information.
        parent::populateState('a.record_date', 'asc');

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Load the component parameters.
        $params = $app->getParams($this->option);
        $this->setState('params', $params);

        // Get category id
        $value = $app->input->getString('filter_search');
        $this->setState('filter.search', $value);

        // Get category id
        $value = $app->input->getInt('filter_category');
        $this->setState('filter.category_id', $value);

        // Get status id
        $value = $app->input->getInt('filter_status');
        $this->setState('filter.status_id', $value);

        // Ordering
        $order    = $params->get('items_ordering', 0);
        $orderDir = $params->get('items_ordering_direction', 'ASC');
        $this->prepareOrderingState($order, $orderDir);

        // Pagination
        $value = $params->get('items_display_results_number', 0);
        if (!$value) {
            $value = $app->input->getInt('limit', $app->get('list_limit', 0));
        }
        $this->setState('list.limit', $value);

        $value = $app->input->getInt('limitstart', 0);
        $this->setState('list.start', $value);
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
        // Create a new query object.
        $db = $this->getDbo();
        /** @var $db JDatabaseMySQLi */

        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.title, a.description, a.votes, a.record_date, a.catid, a.user_id, a.status_id, ' .
                $query->concatenate(array('a.id', 'a.alias'), '-') . ' AS slug, ' .
                'b.name, b.username, ' .
                'c.title AS category, ' .
                $query->concatenate(array('c.id', 'c.alias'), '-') . ' AS catslug, ' .
                'd.name AS status_name, d.params AS status_params, d.default AS status_default'
            )
        );
        $query->from($db->quoteName('#__uideas_items', 'a'));
        $query->leftJoin($db->quoteName('#__users', 'b') . ' ON a.user_id = b.id');
        $query->leftJoin($db->quoteName('#__categories', 'c') . ' ON a.catid = c.id');
        $query->leftJoin($db->quoteName('#__uideas_statuses', 'd') . ' ON a.status_id = d.id');

        // Filter by category
        $categoryId = (int)$this->getState('filter.category_id');
        if ($categoryId > 0) {
            $query->where('a.catid = ' . (int)$categoryId);
        }

        // Filter by status
        $statusId = (int)$this->getState('filter.status_id');
        if ($statusId > 0) {
            $query->where('a.status_id = ' . (int)$statusId);
        }

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (JString::strlen($search) > 0) {
            $escaped = $db->escape($search, true);
            $quoted  = $db->quote('%' . $escaped . '%', false);
            $query->where('a.title LIKE ' . $quoted);
        }

        $query->where('a.published = 1');

        // Add the list ordering clause.
        $orderString = $this->getOrderString();
        $query->order($db->escape($orderString));

        return $query;
    }

    /**
     *
     * Prepare a string used for ordering results.
     *
     * @param integer $order
     * @param integer $orderDir
     */
    protected function prepareOrderingState($order, $orderDir)
    {
        switch ($order) {
            case 1:
                $orderCol = 'a.title';
                break;

            case 2:
                $orderCol  = 'a.record_date';
                $orderDir  = 'DESC';
                break;

            case 3:
                $orderCol  = 'a.votes';
                break;

            default:
                $orderCol = 'a.ordering';
                break;
        }

        // Set the column using for ordering
        $this->setState('list.ordering', $orderCol);

        // Set the type of ordering
        if (!in_array(JString::strtoupper($orderDir), array('ASC', 'DESC'), true)) {
            $orderDir = 'ASC';
        }
        $this->setState('list.direction', $orderDir);
    }

    protected function getOrderString()
    {
        $orderCol  = $this->getState('list.ordering');
        $orderDirn = $this->getState('list.direction');

        return $orderCol . ' ' . $orderDirn;
    }

    public function getComments()
    {
        $db = $this->getDbo();
        /** @var $db JDatabaseDriver */

        $query = $db->getQuery(true);

        $query
            ->select('a.item_id, COUNT(*) AS number')
            ->from($db->quoteName('#__uideas_comments', 'a'))
            ->group('a.item_id');

        $db->setQuery($query);
        $results = $db->loadAssocList('item_id', 'number');

        return $results;
    }
}
