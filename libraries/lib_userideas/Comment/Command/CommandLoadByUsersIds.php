<?php
/**
 * @package      Userideas
 * @subpackage   Comments
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Comment\Command;

use Prism\Database\Command\CommandAbstract;
use Userideas\Comment\Comments;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality for handling a command and loading specific data.
 *
 * @package      Userideas
 * @subpackage   Comments
 */
class CommandLoadByUsersIds extends CommandAbstract
{
    /**
     * Handle command that will load items by users IDs.
     *
     * <code>
     * $options = array(
     *    'start'           => 0,
     *    'limit'           => 10,
     *    'order_column'    => 'DESC',
     *    'order_direction' => 'DESC',
     *    'users_ids'       => array(1,2,3,4)
     * );
     *
     * $command  = new Userideas\Comment\Command\CommandLoadByUsersIds(\JFactory::getDbo());
     * $comments = new Userideas\Comment\Comments(\JFactory::getDbo());
     * $comments->addCommand($command);
     * $comments->handle($options);
     * </code>
     *
     * @param Comments $object
     * @param array $options
     */
    public function handle(&$object, array $options = array())
    {
        $orderDirection = $this->getOptionOrderDirection($options);
        $start          = $this->getOptionStart($options);
        $limit          = $this->getOptionLimit($options);
        $usersIds       = $this->getOptionIds($options, 'users_ids');

        if (count($usersIds) > 0) {
            // Create a new query object.
            $query = $this->db->getQuery(true);
            $query
                ->select('a.id, a.comment, a.record_date, a.published, a.item_id, a.user_id')
                ->from($this->db->quoteName('#__uideas_comments', 'a'))
                ->where('a.user_id IN (' . implode(',', $usersIds) . ')')
                ->order('a.record_date ' . $orderDirection);

            if ($limit > 0) {
                $this->db->setQuery($query, $start, $limit);
            } else {
                $this->db->setQuery($query);
            }

            $results = (array)$this->db->loadAssocList('user_id');
            $object->setItems($results);
            $object->flagMultidimensional();
        }

    }
}
