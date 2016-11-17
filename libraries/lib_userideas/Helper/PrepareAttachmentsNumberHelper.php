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
use Userideas\Attachment\Attachments;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that counts attachments of the items.
 *
 * @package      Userideas
 * @subpackage   Helpers
 */
class PrepareAttachmentsNumberHelper implements HelperInterface
{
    /**
     * Count attachments assigned to the items.
     *
     * @param array $data
     * @param array $options
     *
     * @throws \RuntimeException
     */
    public function handle(&$data, array $options = array())
    {
        $type = array_key_exists('type', $options) ? $options['type'] : null;

        if (count($data) > 0 and in_array($type, ['item', 'comment'], true)) {
            $ids                 = ArrayHelper::getIds($data);

            $attachments         = new Attachments(\JFactory::getDbo());
            $numberOfAttachments = $attachments->countAttachments($ids, $type);

            if (count($numberOfAttachments) > 0) {
                foreach ($data as $key => $item) {
                    if (array_key_exists($item->id, $numberOfAttachments)) {
                        $item->attachment = $numberOfAttachments[$item->id];
                    }
                }
            }
        }
    }
}
