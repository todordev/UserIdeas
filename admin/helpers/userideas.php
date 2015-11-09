<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * It is UserIdeas helper class
 */
class UserIdeasHelper
{
    protected static $extension = 'com_userideas';

    /**
     * Configure the Linkbar.
     *
     * @param    string  $vName  The name of the active view.
     *
     * @since    1.6
     */
    public static function addSubmenu($vName = 'dashboard')
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_USERIDEAS_DASHBOARD'),
            'index.php?option=' . self::$extension . '&view=dashboard',
            $vName === 'dashboard'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_USERIDEAS_CATEGORIES'),
            'index.php?option=com_categories&extension=' . self::$extension . '',
            $vName === 'categories'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_USERIDEAS_ITEMS'),
            'index.php?option=' . self::$extension . '&view=items',
            $vName === 'items'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_USERIDEAS_VOTES'),
            'index.php?option=' . self::$extension . '&view=votes',
            $vName === 'votes'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_USERIDEAS_COMMENTS'),
            'index.php?option=' . self::$extension . '&view=comments',
            $vName === 'comments'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_USERIDEAS_STATUSES'),
            'index.php?option=' . self::$extension . '&view=statuses',
            $vName === 'statuses'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_USERIDEAS_PLUGINS'),
            'index.php?option=com_plugins&view=plugins&filter_search=' . rawurlencode('user ideas'),
            $vName === 'plugins'
        );

    }

    /**
     * Prepare an array with status data.
     *
     * @param $items
     *
     * @return Prism\Database\ArrayObject
     */
    public static function prepareStatuses($items)
    {
        foreach ($items as $key => $item) {

            if (JString::strlen($item->status_params) > 0) {
                $statusParams = json_decode($item->status_params, true);
                $item->status_params = (!is_array($statusParams)) ? null : $statusParams;
            }

            $statusData = array(
                'id'      => $item->status_id,
                'name'    => $item->status_name,
                'default' => $item->status_default,
                'params'  => $item->status_params
            );

            $item->status = new Userideas\Status\Status();
            $item->status->bind($statusData);

            $items[$key] = $item;
        }

        return $items;
    }

    /**
     * This method check for valid user ID and item owner.
     *
     * @param int $userId
     * @param int $itemOwnerId
     *
     * @return bool
     */
    public static function isValidOwner($userId, $itemOwnerId)
    {
        return (bool)((int)$userId > 0 and ((int)$userId === (int)$itemOwnerId));
    }

    public static function shouldDisplayFootbar(Joomla\Registry\Registry $params, Joomla\Registry\Registry $itemParams, $hasTags)
    {
        if ($params->get('show_author', $itemParams->get('show_author'))) {
            return true;
        }

        if ($params->get('show_create_date', $itemParams->get('show_create_date'))) {
            return true;
        }

        if ($params->get('show_category', $itemParams->get('show_category'))) {
            return true;
        }

        if ($params->get('show_hits', $itemParams->get('show_hits'))) {
            return true;
        }

        if ($params->get('show_status', $itemParams->get('show_status'))) {
            return true;
        }

        return (bool)($params->get('show_tags', $itemParams->get('show_tags')) and $hasTags);
    }

    public static function countComments($ids)
    {
        $result = array();

        if (count($ids) > 0) {
            $db  = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query
                ->select('a.item_id, COUNT(*) AS number')
                ->from($db->quoteName('#__uideas_comments', 'a'))
                ->where($db->quoteName('a.item_id') . ' IN (' . implode(',', $ids) . ')')
                ->group('a.item_id');

            $db->setQuery($query);

            $result = (array)$db->loadAssocList('item_id', 'number');
        }

        return $result;
    }
}
