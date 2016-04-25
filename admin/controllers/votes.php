<?php
/**
 * @package      Userideas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Userideas votes controller
 *
 * @package     Userideas
 * @subpackage  Components
 */
class UserideasControllerVotes extends Prism\Controller\Admin
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param string $name
     * @param string $prefix
     * @param array  $config
     *
     * @return UserideasModelVote
     */
    public function getModel($name = 'Vote', $prefix = 'UserideasModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}
