<?php
/**
 * @package      Userideas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Registry\Registry;

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
            JText::_('COM_USERIDEAS_ATTACHMENTS'),
            'index.php?option=' . self::$extension . '&view=attachments',
            $vName === 'attachments'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_USERIDEAS_PLUGINS'),
            'index.php?option=com_plugins&view=plugins&filter_search=' . rawurlencode('userideas'),
            $vName === 'plugins'
        );
    }

    public static function shouldDisplayFootbar(Registry $params, Registry $itemParams, $hasTags)
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

    /**
     * Generates a link that will be used for sorting results.
     *
     * @param string $label
     * @param string $type
     * @param array $options
     * @param string $class
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function sortByLink($label, $type, $options, $class = '')
    {
        $html = array();

        $url            = Joomla\Utilities\ArrayHelper::getValue($options, 'url');
        $orderedBy      = Joomla\Utilities\ArrayHelper::getValue($options, 'ordered_by', '', 'cmd');
        $sortItemClass  = trim(Joomla\Utilities\ArrayHelper::getValue($options, 'item_class'));

        $elementClass   = (strlen($class) > 0) ? ' class="'.$class.'"' : '';


        $linkClass = '';
        if ($sortItemClass !== '') {
            $linkClass = 'class="'.$sortItemClass.'"';
        }

        switch ($type) {
            case 'alpha':
                $orderOptions = array(Prism\Constants::ORDER_TITLE_REVERSE_ALPHABETICAL, Prism\Constants::ORDER_TITLE_ALPHABETICAL);
                if ($orderedBy === 'alpha') {
                    $iconClass  = 'fa fa-sort-alpha-desc';
                    $orderBy    = Prism\Constants::ORDER_TITLE_REVERSE_ALPHABETICAL;
                } else {
                    $iconClass  = 'fa fa-sort-alpha-asc';
                    $orderBy    = Prism\Constants::ORDER_TITLE_ALPHABETICAL;
                }
                break;
            case 'votes':
                $orderOptions = array('rvotes', 'votes');
                if ($orderedBy === 'votes') {
                    $iconClass  = 'fa fa-sort-numeric-desc';
                    $orderBy    = 'rvotes';
                } else {
                    $iconClass  = 'fa fa-sort-numeric-asc';
                    $orderBy    = 'votes';
                }
                break;
            case 'date':
                $orderOptions = array(Prism\Constants::ORDER_MOST_RECENT_FIRST, Prism\Constants::ORDER_OLDEST_FIRST);
                if ($orderedBy === 'rdate') {
                    $iconClass  = 'fa fa-sort-desc';
                    $orderBy    = Prism\Constants::ORDER_OLDEST_FIRST;
                } else {
                    $iconClass  = 'fa fa-sort-asc';
                    $orderBy    = Prism\Constants::ORDER_MOST_RECENT_FIRST;
                }
                break;
            case 'hits':
                $orderOptions = array(Prism\Constants::ORDER_MOST_HITS, Prism\Constants::ORDER_LEAST_HITS);
                if ($orderedBy === 'hits') {
                    $orderBy    = Prism\Constants::ORDER_LEAST_HITS;
                    $iconClass  = 'fa fa-sort-amount-desc';
                } else {
                    $iconClass  = 'fa fa-sort-amount-asc';
                    $orderBy    = Prism\Constants::ORDER_MOST_HITS;
                }
                break;

            default:
                $iconClass = '';
                $orderBy = '';
                $orderOptions = array();
                break;
        }

        $parameters = array(
            'filter_order'     => $orderBy
        );

        $url .= '?'.http_build_query($parameters);

        $html[] = '<li '.$elementClass.'>';
        $html[] = '<a href="'.$url.'" '.$linkClass.' role="button">'.$label;
        if (in_array($orderedBy, $orderOptions, true)) {
            $html[] = '<span class="' . $iconClass . '"></span>';
        }
        $html[] = '</a>';
        $html[] = '</li>';

        return implode("\n", $html);
    }
}
