<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('itprism.controller.admin');

/**
 * UserIdeas votes controller
 *
 * @package     UserIdeas
 * @subpackage  Components
 */
class UserIdeasControllerVotes extends ITPrismControllerAdmin
{
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Vote', $prefix = 'UserIdeasModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }
}
