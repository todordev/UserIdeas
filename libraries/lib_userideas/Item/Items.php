<?php
/**
 * @package         Userideas
 * @subpackage      Items
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Item;

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Prism\Database;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing items.
 *
 * @package      Userideas
 * @subpackage   Items
 */
class Items extends Database\Collection
{
    protected $options = array();

    /**
     * Load data of items from a database.
     *
     * <code>
     * $options = array(
     *    'type'  => 'array', // array or object
     *    'limit'          => 10,
     *    'sort_direction' => 'DESC'
     * );
     *
     * $items = new Userideas\Item\Items(\JFactory::getDbo());
     * $items->load($options);
     * </code>
     *
     * @param array $options
     *
     * @throws \RuntimeException
     */
    public function load(array $options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select(
                'a.id, a.title, a.alias, a.description, a.votes, a.hits, a.record_date, ' .
                'a.ordering, a.published, a.params, a.status_id, a.catid, a.user_id, a.access, ' .
                'b.name AS author, b.username, ' .
                'c.title AS category, c.access AS category_access, ' .
                'd.title AS status_title, d.params AS status_params, d.default AS status_default, ' .
                $query->concatenate(array('a.id', 'a.alias'), ':') . ' AS slug, ' .
                $query->concatenate(array('c.id', 'c.alias'), ':') . ' AS catslug'
            )
            ->from($this->db->quoteName('#__uideas_items', 'a'))
            ->leftJoin($this->db->quoteName('#__users', 'b') . ' ON a.user_id = b.id')
            ->leftJoin($this->db->quoteName('#__categories', 'c') . ' ON a.catid = c.id')
            ->leftJoin($this->db->quoteName('#__uideas_statuses', 'd') . ' ON a.status_id = d.id');

        // Filter by IDs.
        $ids    = $this->getOptionIds($options, 'ids');
        if (count($ids) > 0) {
            $query->where($this->db->quoteName('a.id') . ' IN (' . implode(',', $ids) . ')');
        }

        // Filter by state.
        $state  = $this->getOptionState($options);
        if ($state !== null) {
            $query->where('a.published = ' . (int)$state);
        }

        // Filter by access level.
        $groups  = $this->getOptionAccessGroups($options);
        if (is_array($groups) and count($groups) > 0) {
            $groups = implode(',', $groups);
            $query
                ->where('a.access IN (' . $groups . ')')
                ->where('c.access IN (' . $groups . ')');
        }

        // Order by column.
        $orderColumn  = $this->getOptionOrderColumn($options);
        if ($orderColumn) {
            $orderDirection = $this->getOptionOrderDirection($options);
            $query->order($this->db->quoteName('a.'.$orderColumn) . ' ' . $orderDirection);
        }

        // Order limit.
        $limit = $this->getOptionLimit($options);
        if ($limit > 0) {
            $start = $this->getOptionStart($options);
            $this->db->setQuery($query, $start, $limit);
        } else {
            $this->db->setQuery($query);
        }

        $this->items = (array)$this->db->loadObjectList();
    }
}
