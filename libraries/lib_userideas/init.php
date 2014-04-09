<?php
/**
 * @package      ITPrism Libraries
 * @subpackage   Library
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

if(!defined("USERIDEAS_PATH_COMPONENT_ADMINISTRATOR")) {
    define("USERIDEAS_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. "components" .DIRECTORY_SEPARATOR. "com_userideas");
}

if(!defined("USERIDEAS_PATH_COMPONENT_SITE")) {
    define("USERIDEAS_PATH_COMPONENT_SITE", JPATH_SITE .DIRECTORY_SEPARATOR. "components" .DIRECTORY_SEPARATOR. "com_userideas");
}

if(!defined("USERIDEAS_PATH_LIBRARY")) {
    define("USERIDEAS_PATH_LIBRARY", JPATH_LIBRARIES .DIRECTORY_SEPARATOR. "userideas");
}

jimport('joomla.utilities.arrayhelper');

// Register Component libraries
JLoader::register("UserIdeasVersion", USERIDEAS_PATH_LIBRARY .DIRECTORY_SEPARATOR. "version.php");

// Register helpers
JLoader::register("UserIdeasHelper", USERIDEAS_PATH_COMPONENT_ADMINISTRATOR .DIRECTORY_SEPARATOR. "helpers" .DIRECTORY_SEPARATOR. "userideas.php");
JLoader::register("UserIdeasCategories", USERIDEAS_PATH_COMPONENT_SITE .DIRECTORY_SEPARATOR. "helpers" .DIRECTORY_SEPARATOR. "category.php");
JLoader::register("UserIdeasHelperRoute", USERIDEAS_PATH_COMPONENT_SITE .DIRECTORY_SEPARATOR. "helpers" .DIRECTORY_SEPARATOR. "route.php");

// Load observers
JLoader::register("UserIdeasObserverVote", USERIDEAS_PATH_COMPONENT_ADMINISTRATOR .DIRECTORY_SEPARATOR. "tables" .DIRECTORY_SEPARATOR. "observers" .DIRECTORY_SEPARATOR. "vote.php");

// Register Observers
JObserverMapper::addObserverClassToClass('UserIdeasObserverVote', 'UserIdeasTableVote', array('typeAlias' => 'com_userideas.vote'));

// Include HTML helpers
JHtml::addIncludePath(USERIDEAS_PATH_COMPONENT_SITE .DIRECTORY_SEPARATOR. 'helpers' .DIRECTORY_SEPARATOR. 'html');