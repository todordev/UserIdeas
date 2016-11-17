<?php
/**
 * @package      Userideas
 * @subpackage   Helpers
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Helper;

use Userideas\Status\Statuses;
use Userideas\Constants;
use Prism\Constants as PrismConstants;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality to prepare and extract data from the cache.
 *
 * @package      Userideas
 * @subpackage   Helpers
 */
abstract class CacheHelper
{
    /**
     * Return categories as options.
     * If cache enabled, the data will be extracted from cache.
     *
     * <code>
     * $categories = $this->getCategoryOptions($cache);
     * </code>
     *
     * @param \JCacheController $cache
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     *
     * @return array|null
     */
    public static function getCategoryOptions(\JCacheController $cache = null)
    {
        $categories = null;

        // Get the categories from the cache.
        if ($cache !== null) {
            $categories = $cache->get(Constants::CACHE_CATEGORY_OPTIONS);
            $categories = is_array($categories) ? $categories : null;
        }

        // Load categories from database.
        if ($categories === null) {
            $config = array(
                'filter.published' => PrismConstants::PUBLISHED
            );
            $categories = \JHtml::_('category.options', 'com_userideas', $config);
            $categories = is_array($categories) ? $categories : array();

            // Store the categories in the cache.
            if ($cache !== null and count($categories) > 0) {
                $cache->store($categories, Constants::CACHE_CATEGORY_OPTIONS);
            }
        }

        return $categories;
    }

    /**
     * Return statuses as options.
     * If cache enabled, the data will be extracted from cache.
     *
     * <code>
     * $statuses = $this->getStatusOptions($cache);
     * </code>
     *
     * @param \JCacheController $cache
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     *
     * @return array|null
     */
    public static function getStatusOptions(\JCacheController $cache = null)
    {
        $statuses = null;

        // Get the categories from the cache.
        if ($cache !== null) {
            $statuses = $cache->get(Constants::CACHE_STATUS_OPTIONS);
            $statuses = is_array($statuses) ? $statuses : null;
        }

        // Load statuses from database.
        if ($statuses === null) {
            $statusCollection = new Statuses(\JFactory::getDbo());
            $statusCollection->load();

            $statuses = $statusCollection->getStatusOptions();

            // Store the categories in the cache.
            if ($cache !== null and count($statuses) > 0) {
                $cache->store($statuses, Constants::CACHE_STATUS_OPTIONS);
            }
        }

        return $statuses;
    }
}
