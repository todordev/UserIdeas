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

jimport('Prism.init');
jimport('Userideas.init');

// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('Userideas');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();
