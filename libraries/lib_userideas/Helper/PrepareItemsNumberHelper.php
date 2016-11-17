<?php
/**
 * @package      Userideas
 * @subpackage   Helpers
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Userideas\Helper;

use Prism\Helper\HelperInterface;
use Prism\Utilities\ArrayHelper;
use Userideas\Statistic\Basic;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that counts comments of the items.
 *
 * @package      Userideas
 * @subpackage   Helpers
 */
class PrepareItemsNumberHelper implements HelperInterface
{
    /**
     * Count items assigned to categories.
     *
     * @param array $data
     * @param array $options
     *
     * @throws \RuntimeException
     */
    public function handle(&$data, array $options = array())
    {
        if (count($data) > 0) {
            $ids = ArrayHelper::getIds($data);

            $statistics    = new Basic(\JFactory::getDbo());
            $numberOfItems = $statistics->getCategoryItems($ids);

            if (count($numberOfItems) > 0) {
                foreach ($data as $key => $item) {
                    if (array_key_exists($item->id, $numberOfItems)) {
                        $item->items_number = $numberOfItems[$item->id];
                    }
                }
            }
        }
    }
}
