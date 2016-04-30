<?php
/**
 * @package      Userideas
 * @subpackage   Comments
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Comment\Command;

use Prism\Helper\HelperInterface;
use Userideas\Comment\Comments;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality for handling a command and loading specific data.
 *
 * @package      Userideas
 * @subpackage   Comments
 */
class PrepareCommentsHelper implements HelperInterface
{
    /**
     * User object.
     *
     * @var \JUser
     */
    protected $db;

    /**
     * Initialize the object.
     *
     * @param \JDatabaseDriver $db
     */
    public function __construct(\JDatabaseDriver $db)
    {
        $this->db = $db;
    }

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
     * @param mixed $data
     * @param array $options
     */
    public function handle(&$data, array $options = array())
    {
        $orderDirection = (!array_key_exists('order_direction', $options)) ? 'ASC' : $options['order_direction'];
        $orderDirection = (strcmp('DESC', $orderDirection) === 0) ? 'DESC' : 'ASC';

        $start          = (!array_key_exists('start', $options)) ? 0 : (int)$options['start'];
        $limit          = (!array_key_exists('limit', $options)) ? 10 : (int)$options['limit'];
        $usersIds       = (!array_key_exists('user_ids', $options)) ? array() : $options['user_ids'];

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
            $data->setItems($results);
            $data->flagMultidimensional();
        }

    }
}
