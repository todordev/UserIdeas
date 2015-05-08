<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('itprism.controller.admin');

/**
 * UserIdeas items controller class.
 *
 * @package        UserIdeas
 * @subpackage     Component
 * @since          1.6
 */
class UserIdeasControllerItems extends ITPrismControllerAdmin
{
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Item', $prefix = 'UserIdeasModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }
}
