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
 * This class provides functionality to prepare an item params.
 *
 * @package      Userideas
 * @subpackage   Helpers
 */
class PrepareItemParamsHelper implements HelperInterface
{
    /**
     * Prepare an item parameters.
     *
     * @param \stdClass $data
     * @param array $options
     */
    public function handle(&$data, array $options = array())
    {
        if (is_object($data)) {
            if ($data->params === null) {
                $data->params = '{}';
            }

            if (is_string($data->params) and $data->params !== '') {
                $params = new Registry;
                $params->loadString($data->params);
                $data->params = $params;
            }
        }
    }
}
