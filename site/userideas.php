<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;

jimport("itprism.init");
jimport('userideas.init');

$controller = JControllerLegacy::getInstance('UserIdeas');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();
