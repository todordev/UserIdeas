<?php
/**
 * @package      UserIdeas
 * @subpackage   Statistic\Items
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace UserIdeas\Statistic\Items;

use UserIdeas\Statistic\Items;

defined('JPATH_PLATFORM') or die;

/**
 * This class loads most popular items.
 *
 * @package         UserIdeas\Statistic
 * @subpackage      Items
 */
class Popular extends Items
{
    /**
     * Load items ordering by number of hits.
     *
     * <code>
     * $options = array(
     *     "limit" => 5
     * );
     *
     * $popular = new UserIdeas\Statistics\Items\Popular(\JFactory::getDbo());
     * $popular->load($options);
     * </code>
     *
     * @param array $options
     */
    public function load($options = array())
    {
        $limit = (array_key_exists('limit', $options)) ? (int)$options['limit'] : 5;

        $query = $this->getQuery();

        $query
            ->where('a.published')
            ->order('a.hits DESC');

        $this->db->setQuery($query, 0, (int)$limit);

        $this->items = (array)$this->db->loadAssocList();
    }
}
