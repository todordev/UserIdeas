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
 * This class provides functionality to prepare an item status.
 *
 * @package      Userideas
 * @subpackage   Helpers
 */
class PrepareItemStatusHelper implements HelperInterface
{
    /**
     * Prepare an item status.
     *
     * @param \stdClass $data
     * @param array $options
     */
    public function handle(&$data, array $options = array())
    {
        if (is_object($data)) {
            if ($data->status_params !== '') {
                $statusParams        = json_decode($data->status_params, true);
                $data->status_params = (!is_array($statusParams)) ? null : $statusParams;
            }

            $statusData = array(
                'id'      => $data->status_id,
                'name'    => $data->status_name,
                'default' => $data->status_default,
                'params'  => $data->status_params
            );

            $data->status = new Status();
            $data->status->bind($statusData);
        }
    }
}
