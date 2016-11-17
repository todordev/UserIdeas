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
use Userideas\Comment\Comments;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that counts comments of the items.
 *
 * @package      Userideas
 * @subpackage   Helpers
 */
class PrepareCommentsNumberHelper implements HelperInterface
{
    /**
     * Count comments assigned to the items.
     *
     * @param array $data
     * @param array $options
     *
     * @throws \RuntimeException
     */
    public function handle(&$data, array $options = array())
    {
        if (count($data) > 0) {
            $options = ['items_ids' => ArrayHelper::getIds($data)];

            $comments         = new Comments(\JFactory::getDbo());
            $numberOfComments = $comments->advancedCount($options);

            if (count($numberOfComments) > 0) {
                foreach ($data as $key => $item) {
                    if (array_key_exists($item->id, $numberOfComments)) {
                        $item->comments = $numberOfComments[$item->id];
                    }
                }
            }
        }
    }
}
