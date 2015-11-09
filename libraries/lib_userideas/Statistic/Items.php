<?php
/**
 * @package      UserIdeas
 * @subpackage   Statistic\Items
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Statistic;

use Prism\Database\ArrayObject;
use Joomla\Registry\Registry;

defined('JPATH_PLATFORM') or die;

/**
 * This is a base class for items statistics.
 *
 * @package         Userideas\Statistic
 * @subpackage      Items
 */
abstract class Items extends ArrayObject
{
    protected function getQuery()
    {
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

        return $query;
    }

    protected function prepareParams()
    {
        foreach ($this->items as $key => $item) {

            if (is_object($item)) {
                if (\JString::strlen($item->params) > 0) {
                    $this->items[$key]->params = new Registry($item->params);
                } else {
                    $this->items[$key]->params = new Registry;
                }
            } else {
                if (\JString::strlen($item['params']) > 0) {
                    $this->items[$key]['params'] = new Registry($item['params']);
                } else {
                    $this->items[$key]['params'] = new Registry;
                }
            }
        }
    }
}
