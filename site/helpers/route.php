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

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Component Route Helper that help to find a menu item.
 * IMPORTANT: It help us to find right MENU ITEM.
 *
 * Use router ...BuildRoute to build a link
 *
 * @static
 * @package        ITPrism Components
 * @subpackage     UserIdeas
 * @since          1.5
 */
abstract class UserIdeasHelperRoute
{
    protected static $items = array();
    protected static $categories;
    protected static $lookup;

    /**
     * This method route quote in the view "category".
     *
     * @param    int $id    The id of the item.
     * @param    int $catid The id of the category.
     *
     * @return string
     */
    public static function getDetailsRoute($id, $catid)
    {
        /**
         *
         * # category
         * We will check for view category first. If find a menu item with view "category" and "id" eqallity of the key,
         * we will get that menu item ( Itemid ).
         *
         * # categories view
         * If miss a menu item with view "category" we continue with searchin but now for view "categories".
         * It is assumed view "categories" will be in the first level of the menu.
         * The view "categories" won't contain category ID so it has to contain 0 for ID key.
         */
        $needles = array(
            'details'  => array((int)$id),
            'category' => array((int)$catid),
            'items'    => array(0),
        );

        //Create the link
        $link = 'index.php?option=com_userideas&view=details&id=' . $id;
        if ($catid > 1) {
            $categories = JCategories::getInstance('userideas');
            $category   = $categories->get($catid);

            if ($category) {
                $needles['category'] = array_reverse($category->getPath());
                $needles['items']    = array_reverse($category->getPath());
                $link .= '&catid=' . $catid;
            }
        }

        // Looking for menu item (Itemid)
        if ($item = self::_findItem($needles)) {
            $link .= '&Itemid=' . $item;
        } elseif ($item = self::_findItem()) { // Get the menu item (Itemid) from the active (current) item.
            $link .= '&Itemid=' . $item;
        }

        return $link;
    }

    /**
     * Prepare the link to the form page
     *
     * @param    int $id The id of the item.
     *
     * @return string
     */
    public static function getFormRoute($id = 0)
    {
        $needles = array(
            'form' => array(0)
        );

        //Create the link
        $link = 'index.php?option=com_userideas&view=form';

        if ($id > 0) {
            $link .= '&id=' . $id;
        }

        // Looking for menu item (Itemid)
        if ($item = self::_findItem($needles)) {
            $link .= '&Itemid=' . $item;
        } elseif ($item = self::_findItem()) { // Get the menu item (Itemid) from the active (current) item.
            $link .= '&Itemid=' . $item;
        }

        return $link;
    }

    /**
     * Routing a link for category or categories view.
     *
     * @param integer $catid
     *
     * @return string
     */
    public static function getCategoryRoute($catid)
    {
        if ($catid instanceof JCategoryNode) {
            $id       = $catid->id;
            $category = $catid;
        } else {
            $id       = (int)$catid;
            $category = JCategories::getInstance('UserIdeas')->get($id);
        }

        if ($id < 1) {
            $link = '';
        } else {
            $needles = array(
                'category' => array($id)
            );

            // Get menu item ( Itemid )
            if ($item = self::_findItem($needles)) {
                $link = 'index.php?Itemid=' . $item;

            } else { // Continue to search and deep inside

                //Create the link
                $link = 'index.php?option=com_userideas&view=category&id=' . $catid;

                if ($category) {
                    $catids = array_reverse($category->getPath());

                    $needles = array(
                        'category'   => $catids,
                        'items'      => array(0)
                    );

                    // Looking for menu item (Itemid)
                    if ($item = self::_findItem($needles)) {
                        $link .= '&Itemid=' . $item;
                    } elseif ($item = self::_findItem()) { // Get the menu item (Itemid) from the active (current) item.
                        $link .= '&Itemid=' . $item;
                    }
                }
            }
        }

        return $link;
    }

    /**
     * Routing a link for items view.
     *
     * @param integer $statusId
     *
     * @return string
     */
    public static function getItemsRoute($statusId = 0)
    {
        $needles = array(
            'items' => array(0),
        );

        // Get menu item ( Itemid )
        if ($item = self::_findItem($needles)) {
            $link = 'index.php?Itemid=' . $item;
        } else { // Continue to search and deep inside

            //Create the link
            $link = 'index.php?option=com_userideas&view=items';

        }

        if (!empty($statusId)) {
            $link .= '&filter_status=' . (int)$statusId;
        }

        return $link;
    }

    protected static function _findItem($needles = null)
    {
        $app   = JFactory::getApplication();
        $menus = $app->getMenu('site');

        // Prepare the reverse lookup array.
        // Collect all menu items and creat an array that contains
        // the ID from the query string of the menu item as a key,
        // and the menu item id (Itemid) as a value
        // Example:
        // array( "category" =>
        //     1(id) => 100 (Itemid),
        //     2(id) => 101 (Itemid)
        // );
        if (self::$lookup === null) {
            self::$lookup = array();

            $component = JComponentHelper::getComponent('com_userideas');
            $items     = $menus->getItems('component_id', $component->id);

            if ($items) {
                foreach ($items as $item) {
                    if (isset($item->query) and isset($item->query['view'])) {
                        $view = $item->query['view'];

                        if (!isset(self::$lookup[$view])) {
                            self::$lookup[$view] = array();
                        }

                        if (isset($item->query['id'])) {
                            self::$lookup[$view][$item->query['id']] = $item->id;
                        } else { // If it is a root element that have no a request parameter ID ( categories, authors ), we set 0 for an key
                            self::$lookup[$view][0] = $item->id;
                        }
                    }
                }
            }
        }

        if ($needles) {

            foreach ($needles as $view => $ids) {
                if (isset(self::$lookup[$view])) {

                    foreach ($ids as $id) {
                        if (isset(self::$lookup[$view][(int)$id])) {
                            return self::$lookup[$view][(int)$id];
                        }
                    }

                }
            }

        } else {
            $active = $menus->getActive();
            if ($active) {
                return $active->id;
            }
        }

        return null;
    }

    /**
     *
     * Prepare categories path to the segments.
     * We use this method in the router "UserIdeasParseRoute".
     *
     * @param integer $catId Category Id
     * @param array   $segments
     * @param integer $mId   Id parameter from the menu item query
     */
    public static function prepareCategoriesSegments($catId, &$segments, $mId = null)
    {
        $categories = JCategories::getInstance('UserIdeas');
        $category   = $categories->get($catId);

        if ($category) {
            //TODO Throw error that the category either not exists or is unpublished
            $path = $category->getPath();
            $path = array_reverse($path);

            $array = array();
            foreach ($path as $id) {
                if ((int)$id === (int)$mId) {
                    break;
                }

                $array[] = $id;
            }

            $segments = array_merge($segments, array_reverse($array));
        }
    }

    /**
     * Load data about item.
     * We use this method in the router "UserIdeasParseRoute".
     *
     * @param int $id
     *
     * @return array
     */
    public static function getItem($id)
    {
        if (!array_key_exists($id, self::$items)) {
            $db    = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query
                ->select(
                    'a.id, a.alias, a.catid, ' .
                    $query->concatenate(array('a.id', 'a.alias'), ':') . ' AS slug'
                )
                ->from($query->quoteName('#__uideas_items', 'a'))
                ->where('a.id = ' . (int)$id);

            $db->setQuery($query);
            self::$items[$id] = (array)$db->loadAssoc();
        }

        return self::$items[$id];
    }

    /**
     * Load data about category.
     * We use this method in the router "UserIdeasParseRoute".
     *
     * @param int $id
     *
     * @return array
     */
    public static function getCategory($id)
    {
        // Load all categories.
        if (self::$categories === null) {
            $db    = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query
                ->select(
                    'a.id, a.alias, ' .
                    $query->concatenate(array('a.id', 'a.alias'), ':') . ' AS slug'
                )
                ->from($query->quoteName('#__categories', 'a'))
                ->where('a.extension = ' . $db->quote('com_userideas'));

            $db->setQuery($query);

            self::$categories = (array)$db->loadAssocList('id');
        }

        return (!array_key_exists($id, self::$categories)) ? array() : self::$categories[$id];
    }
}
