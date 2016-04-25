<?php
/**
 * @package      Userideas
 * @subpackage   Statistic\Items
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Statistic;

use Prism\Database;
use Joomla\Registry\Registry;

defined('JPATH_PLATFORM') or die;

/**
 * This is a base class for items statistics.
 *
 * @package         Userideas\Statistic
 * @subpackage      Items
 */
abstract class Items extends Database\Collection
{
    protected function getQuery()
    {
        $query = $this->db->getQuery(true);

        $query
            ->select(
                'a.id, a.title, a.alias, a.description, a.votes, a.hits, a.record_date, ' .
                'a.ordering, a.published, a.params, a.status_id, a.catid, a.user_id, a.access, ' .
                'c.title AS category, c.access AS category_access, ' .
                'b.name, b.username, ' .
                'd.name AS status_name, d.params AS status_params, d.default AS status_default, ' .
                $query->concatenate(array('a.id', 'a.alias'), ':') . ' AS slug, ' .
                $query->concatenate(array('c.id', 'c.alias'), ':') . ' AS catslug'
            )
            ->from($this->db->quoteName('#__uideas_items', 'a'))
            ->leftJoin($this->db->quoteName('#__users', 'b') . ' ON a.user_id = b.id')
            ->leftJoin($this->db->quoteName('#__categories', 'c') . ' ON a.catid = c.id')
            ->leftJoin($this->db->quoteName('#__uideas_statuses', 'd') . ' ON a.status_id = d.id');

        return $query;
    }
}
