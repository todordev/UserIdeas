<?php
/**
 * @package      ITPrism Libraries
 * @subpackage   UserIdeas
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * UserIdeas Library is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

if(!defined("USERIDEAS_PATH_COMPONENT_ADMINISTRATOR")) {
    define("USERIDEAS_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR. "components" . DIRECTORY_SEPARATOR ."com_userideas");
}

if(!defined("USERIDEAS_PATH_COMPONENT_SITE")) {
    define("USERIDEAS_PATH_COMPONENT_SITE", JPATH_SITE . DIRECTORY_SEPARATOR. "components" . DIRECTORY_SEPARATOR ."com_userideas");
}

if(!defined("USERIDEAS_PATH_LIBRARY")) {
    define("USERIDEAS_PATH_LIBRARY", JPATH_LIBRARIES . DIRECTORY_SEPARATOR. "userideas");
}

if(!defined("ITPRISM_PATH_LIBRARY")) {
    define("ITPRISM_PATH_LIBRARY", JPATH_LIBRARIES . DIRECTORY_SEPARATOR. "itprism");
}

jimport('joomla.utilities.arrayhelper');

// Register ITPrism libraries
JLoader::register("ITPrismErrors", ITPRISM_PATH_LIBRARY . DIRECTORY_SEPARATOR . "errors.php");

// Register Component libraries
JLoader::register("UserIdeasVersion", USERIDEAS_PATH_LIBRARY . DIRECTORY_SEPARATOR . "version.php");

// Register helpers
JLoader::register("UserIdeasHelper", USERIDEAS_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "userideas.php");
JLoader::register("UserIdeasCategories", USERIDEAS_PATH_COMPONENT_SITE . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "category.php");
JLoader::register("UserIdeasHelperRoute", USERIDEAS_PATH_COMPONENT_SITE . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "route.php");
