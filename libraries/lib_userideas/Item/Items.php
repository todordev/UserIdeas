<?php
/**
 * @package         UserIdeas
 * @subpackage      Items
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Item;

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Prism\Database\ArrayObject;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing items.
 *
 * @package      UserIdeas
 * @subpackage   Items
 */
class Items extends ArrayObject
{
    protected $options = array();

    protected static $instance;

    /**
     * Create an instance of the object.
     *
     * <code>
     * $options = array(
     *    'limit'          => 10,
     *    'sort_direction' => 'DESC'
     * );
     *
     * $items     = Userideas\Status\Statuses::getInstance(\JFactory::getDbo(), $options);
     * </code>
     *
     * @param \JDatabaseDriver $db
     * @param array $options
     *
     * @return null|Items
     */
    public static function getInstance(\JDatabaseDriver $db, array $options = array())
    {
        if (self::$instance === null) {
            $item           = new Items($db);
            $item->load($options);
            self::$instance = $item;
        }

        return self::$instance;
    }

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
     */
    public function load($options = array())
    {
        $type  = (array_key_exists('type', $options)) ? $options['type'] : 'array';

        $ids   = (!array_key_exists('ids', $options)) ? array() : (array)$options['ids'];
        $ids   = ArrayHelper::toInteger($ids);

        $limit = (!array_key_exists('limit', $options)) ? 0 : (int)$options['limit'];

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select(
                'a.id, a.title, a.alias, a.description, a.votes, a.hits, a.record_date, ' .
                'a.ordering, a.published, a.params, a.status_id, a.catid, a.user_id, ' .
                'b.title AS category, ' .
                'c.name, c.username, ' .
                'd.name AS status_name, d.params AS status_params, d.default AS status_default, ' .
                $query->concatenate(array('a.id', 'a.alias'), ':') . ' AS slug, ' .
                $query->concatenate(array('b.id', 'b.alias'), ':') . ' AS catslug'
            )
            ->from($this->db->quoteName('#__uideas_items', 'a'))
            ->leftJoin($this->db->quoteName('#__categories', 'b') . ' ON a.catid = b.id')
            ->leftJoin($this->db->quoteName('#__users', 'c') . ' ON a.user_id = c.id')
            ->leftJoin($this->db->quoteName('#__uideas_statuses', 'd') . ' ON a.status_id = d.id');

        // Filter by IDs.
        if (count($ids) > 0) {
            $query->where($this->db->quoteName('a.id') . ' IN (' . implode(',', $ids) . ')');
        }

        // Sort by column.
        if (array_key_exists('sort_column', $options)) {
            $sortColumn = $options['sort_direction'];

            $sortDir = (!array_key_exists('sort_direction', $options)) ? 'DESC' : $options['sort_direction'];
            $sortDir = (strcmp('DESC', $sortDir) === 0) ? 'DESC' : 'ASC';

            $query->order($this->db->quoteName('a.'.$sortColumn) . ' ' . $sortDir);
        }

        if ($limit > 0) {
            $this->db->setQuery($query, 0, $limit);
        } else {
            $this->db->setQuery($query);
        }

        if (strcmp('array', $type) === 0) {
            $this->items = (array)$this->db->loadAssocList();
        } else {
            $this->items = (array)$this->db->loadObjectList();
        }

        // Prepare params.
        foreach ($this->items as $key => $item) {
            if (\JString::strlen($item->params) > 0) {
                $this->items[$key]->params = new Registry($item->params);
            } else {
                $this->items[$key]->params = new Registry;
            }
        }
    }
}
