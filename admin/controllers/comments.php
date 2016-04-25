<?php
/**
 * @package      Userideas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Userideas comment controller class
 *
 * @package     Userideas
 * @subpackage  Components
 */
class UserideasControllerComments extends Prism\Controller\Admin
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param string $name
     * @param string $prefix
     * @param array  $config
     *
     * @return UserideasModelComment
     */
    public function getModel($name = 'Comment', $prefix = 'UserideasModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}
