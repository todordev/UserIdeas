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
use Userideas\Status\Statuses;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that returns objects from the container.
 * This class uses helper traits of the container to prepare and fetch the objects.
 *
 * @package      Userideas
 * @subpackage   Helpers
 */
class Helper
{
    use StatusesHelper;

    /**
     * Return object Statuses.
     *
     * <code>
     * $helper   = new Userideas\Container\Helper();
     * $statuses = $this->fetchStatuses($container);
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
    public function fetchStatuses($container)
    {
        return $this->getStatuses($container);
    }
}
