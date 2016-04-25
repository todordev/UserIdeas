<?php
/**
 * @package      Userideas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * It is Userideas helper class
 */
class UserideasHelper
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
}
