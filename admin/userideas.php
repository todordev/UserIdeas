<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport("itprism.init");
jimport('userideas.init');

// Include dependencies
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance("UserIdeas");

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();
