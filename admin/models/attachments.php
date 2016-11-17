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
class UserideasModelAttachments extends JModelList
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
                'source', 'a.source',
                'filesize', 'a.filesize',
                'mime', 'a.mime',
                'record_date', 'a.record_date'
            );
        }

        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();

        // Load the component parameters.
        $params = JComponentHelper::getParams($this->option);
        $this->setState('params', $params);

        $value = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $value);

        $value = $this->getUserStateFromRequest($this->context . '.filter.source', 'filter_source');
        $this->setState('filter.source', $value);

        $value = $app->input->getInt('id');
        $this->setState('item.id', $value);

        // List state information.
        parent::populateState('a.record_date', 'asc');
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
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.source');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @throws \RuntimeException
     * @return  JDatabaseQuery
     * @since   1.6
     */
    protected function getListQuery()
    {
        $itemId = $this->getState('item.id');

        $db     = $this->getDbo();
        /** @var $db JDatabaseDriver */

        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.filename, a.filesize, a.record_date, a.item_id, a.comment_id, a.user_id, a.mime, a.source, ' .
                'u.name AS user'
            )
        );
        $query->from($db->quoteName('#__uideas_attachments', 'a'))
        ->leftJoin($db->quoteName('#__users', 'u') . ' ON a.user_id = u.id')
        ->where('a.item_id = ' . (int)$itemId);

        // Filter by source.
        $source = (string)$this->getState('filter.source');
        if ($source !== '') {
            $query->where('a.source = ' . $db->quote($source));
        }

        // Filter by search in title
        $search = (string)$this->getState('filter.search');
        if ($search !== '') {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int)substr($search, 3));
            } else {
                $escaped = $db->escape($search, true);
                $quoted  = $db->quote('%' . $escaped . '%', false);
                $query->where('a.filename LIKE ' . $quoted);
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

        return $orderCol . ' ' . $orderDirn;
    }
}
