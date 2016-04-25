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
 * Userideas items controller class.
 *
 * @package        Userideas
 * @subpackage     Component
 * @since          1.6
 */
class UserideasControllerItems extends Prism\Controller\Admin
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param string $name
     * @param string $prefix
     * @param array  $config
     *
     * @return UserideasModelItem
     */
    public function getModel($name = 'Item', $prefix = 'UserideasModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
}
