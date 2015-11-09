<?php
/**
 * @package      UserIdeas
 * @subpackage   Statistic\Items
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Statistic\Items;

use Userideas\Statistic\Items;

defined('JPATH_PLATFORM') or die;

/**
 * This class loads latest items.
 *
 * @package         Userideas\Statistic
 * @subpackage      Items
 */
class Latest extends Items
{
    protected $data = array();

    protected $position = 0;

    /**
     * Load latest items ordering by creation date.
     *
     * <code>
     * $options = array(
     *     "type"  => 'array', // array or object
     *     "limit" => 5
     * );
     *
     * $latest = new Userideas\Statistics\Items\Latest(\JFactory::getDbo());
     * $latest->load($options);
     * </code>
     *
     * @param array $options
     */
    public function load($options = array())
    {
        $type  = (array_key_exists('type', $options)) ? $options['type'] : 'array';
        $limit = (array_key_exists('limit', $options)) ? (int)$options['limit'] : 5;

        $query = $this->getQuery();

        $query
            ->where('a.published = 1')
            ->order('a.record_date DESC');

        $this->db->setQuery($query, 0, (int)$limit);

        if (strcmp('array', $type) === 0) {
            $this->items = (array)$this->db->loadAssocList();
        } else {
            $this->items = (array)$this->db->loadObjectList();
        }

        // Prepare params.
        $this->prepareParams();
    }
}
