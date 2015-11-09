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
 * This class loads most voted items.
 *
 * @package         Userideas\Statistic
 * @subpackage      Items
 */
class MostVoted extends Items
{
    /**
     * Load items ordering by number of votes.
     *
     * <code>
     * $options = array(
     *     "type"  => 'array', // array or object
     *     "limit" => 5
     * );
     *
     * $mostVoted = new Userideas\Statistics\Items\MostVoted(\JFactory::getDbo());
     * $mostVoted->load($options);
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
            ->order('a.votes DESC');

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
