<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;

jimport('userideas.init');

jimport('joomla.application.component.controller');

$controller = JControllerLegacy::getInstance('UserIdeas');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();