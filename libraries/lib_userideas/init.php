<?php
/**
 * @package      ITPrism Libraries
 * @subpackage   User Feedback Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * User Feedback Library is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

if(!defined("USERFEEDBACK_PATH_COMPONENT_ADMINISTRATOR")) {
    define("USERFEEDBACK_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR. "components" . DIRECTORY_SEPARATOR ."com_userfeedback");
}

if(!defined("USERFEEDBACK_PATH_LIBRARY")) {
    define("USERFEEDBACK_PATH_LIBRARY", JPATH_LIBRARIES . DIRECTORY_SEPARATOR. "userfeedback");
}

if(!defined("ITPRISM_PATH_LIBRARY")) {
    define("ITPRISM_PATH_LIBRARY", JPATH_LIBRARIES . DIRECTORY_SEPARATOR. "itprism");
}

jimport('joomla.utilities.arrayhelper');

// Register ITPrism libraries
JLoader::register("ITPrismErrors", ITPRISM_PATH_LIBRARY . DIRECTORY_SEPARATOR . "errors.php");

// Register Component libraries
JLoader::register("UserFeedbackVersion", USERFEEDBACK_PATH_LIBRARY . DIRECTORY_SEPARATOR . "version.php");

// Register helpers
JLoader::register("UserFeedbackCategories", USERFEEDBACK_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "category.php");
JLoader::register("UserFeedbackHelper", USERFEEDBACK_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "userfeedback.php");
JLoader::register("UserFeedbackHelperRoute", USERFEEDBACK_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "route.php");
