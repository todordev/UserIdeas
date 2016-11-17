<?php
/**
 * @package      Userideas
 * @subpackage   Helpers
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Container;

use Joomla\DI\Container;
use Userideas\Constants;
use Userideas\Status\Statuses;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality to prepare and inject object Statuses in the container.
 *
 * @package      Userideas
 * @subpackage   Helpers
 */
trait StatusesHelper
{
    /**
     * Prepare object Statuses and inject it in the container.
     *
     * <code>
     * $this->prepareStatuses($container);
     * </code>
     *
     * @param Container $container
     *
     * @throws \RuntimeException
     * @throws \OutOfBoundsException
     *
     * @return Statuses
     */
    protected function prepareStatuses($container)
    {
        if (!$container->exists(Constants::CONTAINER_STATUSES)) {
            $statuses = new Statuses(\JFactory::getDbo());
            $statuses->load();

            $container->set(Constants::CONTAINER_STATUSES, $statuses);
        }
    }

    /**
     * Return user profile.
     *
     * <code>
     * $userId = 1;
     *
     * $this->prepareProfile($container, $params, $userId);
     * $profile = $this->getProfile($container, $params, $userId);
     * </code>
     *
     * @param Container $container
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     *
     * @return Statuses
     */
    protected function getStatuses($container)
    {
        if (!$container->exists(Constants::CONTAINER_STATUSES)) {
            $this->prepareStatuses($container);
        }

        return $container->get(Constants::CONTAINER_STATUSES);
    }
}
