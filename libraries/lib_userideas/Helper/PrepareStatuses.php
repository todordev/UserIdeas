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
use Userideas\Status\Status;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality to prepare the statuses of the items.
 *
 * @package      Userideas
 * @subpackage   Helpers
 */
class PrepareStatuses implements HelperInterface
{
    /**
     * Prepare the statuses of the items.
     *
     * @param array $data
     * @param array $options
     */
    public function handle(&$data, array $options = array())
    {
        foreach ($data as $key => $item) {
            if ($item->status_params !== '') {
                $statusParams        = json_decode($item->status_params, true);
                $item->status_params = (!is_array($statusParams)) ? null : $statusParams;
            }

            $statusData = array(
                'id'      => $item->status_id,
                'name'    => $item->status_name,
                'default' => $item->status_default,
                'params'  => $item->status_params
            );

            $item->status = new Status();
            $item->status->bind($statusData);
        }
    }
}
