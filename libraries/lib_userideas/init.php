<?php
/**
 * @package      UserIdeas
 * @subpackage   Initialization
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

if (!defined("USERIDEAS_PATH_COMPONENT_ADMINISTRATOR")) {
    define("USERIDEAS_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR ."/components/com_userideas");
}

if (!defined("USERIDEAS_PATH_COMPONENT_SITE")) {
    define("USERIDEAS_PATH_COMPONENT_SITE", JPATH_SITE ."/components/com_userideas");
}

if (!defined("USERIDEAS_PATH_LIBRARY")) {
    define("USERIDEAS_PATH_LIBRARY", JPATH_LIBRARIES ."/userideas");
}

jimport('joomla.utilities.arrayhelper');

// Register Component libraries
JLoader::register("UserIdeasConstants", USERIDEAS_PATH_LIBRARY ."/constants.php");
JLoader::register("UserIdeasVersion", USERIDEAS_PATH_LIBRARY ."/version.php");
JLoader::register("UserIdeasCategories", USERIDEAS_PATH_LIBRARY ."/categories.php");

// Register helpers
JLoader::register("UserIdeasHelper", USERIDEAS_PATH_COMPONENT_ADMINISTRATOR ."/helpers/userideas.php");
JLoader::register("UserIdeasHelperRoute", USERIDEAS_PATH_COMPONENT_SITE ."/helpers/route.php");

// Register Observers
JLoader::register("UserIdeasObserverVote", USERIDEAS_PATH_COMPONENT_ADMINISTRATOR ."/tables/observers/vote.php");
JObserverMapper::addObserverClassToClass('UserIdeasObserverVote', 'UserIdeasTableVote', array('typeAlias' => 'com_userideas.vote'));

// Include HTML helpers
JHtml::addIncludePath(USERIDEAS_PATH_COMPONENT_SITE . "/helpers/html");
