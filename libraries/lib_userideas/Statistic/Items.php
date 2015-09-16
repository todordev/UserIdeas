<?php
/**
 * @package      UserIdeas
 * @subpackage   Statistic\Items
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace UserIdeas\Statistic;

use Prism\Database\ArrayObject;

defined('JPATH_PLATFORM') or die;

/**
 * This is a base class for items statistics.
 *
 * @package         UserIdeas\Statistic
 * @subpackage      Items
 */
abstract class Items extends ArrayObject
{
    protected function getQuery()
    {
        $query = $this->db->getQuery(true);

        $query
            ->select(
                "a.id, a.title, a.alias, a.description, a.votes, a.hits, " .
                "a.record_date, a.ordering, a.published, a.status_id, a.catid, a.user_id, " .
                "c.name, " .
                $query->concatenate(array("a.id", "a.alias"), ":") . " AS slug, " .
                $query->concatenate(array("b.id", "b.alias"), ":") . " AS catslug"
            )
            ->from($this->db->quoteName("#__uideas_items", "a"))
            ->leftJoin($this->db->quoteName("#__categories", "b") . " ON a.catid = b.id")
            ->leftJoin($this->db->quoteName("#__users", "c") . " ON a.user_id = c.id");

        return $query;
    }
}
