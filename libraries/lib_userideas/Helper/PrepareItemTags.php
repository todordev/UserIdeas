<?php
/**
 * @package      Userideas
 * @subpackage   Helpers
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Helper;

use Joomla\Registry\Registry;
use Prism\Helper\HelperInterface;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality to prepare an item tags.
 *
 * @package      Userideas
 * @subpackage   Helpers
 */
class PrepareItemTags implements HelperInterface
{
    /**
     * Prepare an item tags.
     *
     * @param \stdClass $data
     * @param array $options
     */
    public function handle(&$data, array $options = array())
    {
        $tags = new \JHelperTags;
        $data->tags = $tags->getItemTags('com_userideas.item', $data->id);
    }
}
