<?php
/**
 * @package      Userideas
 * @subpackage   Constants
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas;

defined('JPATH_PLATFORM') or die;

/**
 * Userideas constants
 *
 * @package      Userideas
 * @subpackage   Constants
 */
class Constants
{
    // Caching
    const CACHE_CATEGORIES = 'categories';
    const CACHE_CATEGORY_OPTIONS = 'category.options';
    const CACHE_STATUSES = 'statuses';
    const CACHE_STATUS_OPTIONS = 'status.options';

    // Container
    const CONTAINER_STATUSES = 'com_userideas.statuses';
    const CONTAINER_CATEGORIES = 'com_userideas.categories';
    const CONTAINER_FILTER_ORDER_CONTEXT = 'com_userideas.filter_order_context';

    // Media Folders
    const ITEM_FOLDER = 'item';
}
