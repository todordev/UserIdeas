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
 * This class loads latest items.
 *
 * @package         UserIdeas\Statistic
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
     *     "limit" => 5
     * );
     *
     * $latest = new UserIdeas\Statistics\Items\Latest(\JFactory::getDbo());
     * $latest->load($options);
     * </code>
     *
     * @param array $options
     */
    public function load($options = array())
    {
        $limit = (isset($options["limit"])) ? (int)$options["limit"] : 5;

        $query = $this->getQuery();

        $query
            ->where("a.published = 1")
            ->order("a.record_date DESC");

        $this->db->setQuery($query, 0, (int)$limit);

        $this->items = (array)$this->db->loadAssocList();
    }
}