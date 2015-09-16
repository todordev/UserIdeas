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
 * This class loads most voted items.
 *
 * @package         UserIdeas\Statistic
 * @subpackage      Items
 */
class MostVoted extends Items
{
    /**
     * Load items ordering by number of votes.
     *
     * <code>
     * $options = array(
     *     "limit" => 5
     * );
     *
     * $mostVoted = new UserIdeas\Statistics\Items\MostVoted(\JFactory::getDbo());
     * $mostVoted->load($options);
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
            ->order("a.votes DESC");

        $this->db->setQuery($query, 0, (int)$limit);

        $this->items = (array)$this->db->loadAssocList();
    }
}
