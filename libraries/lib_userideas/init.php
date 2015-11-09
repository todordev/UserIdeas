<?php
/**
 * @package      UserIdeas
 * @subpackage   Initialization
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

if (!defined('USERIDEAS_PATH_COMPONENT_ADMINISTRATOR')) {
    define('USERIDEAS_PATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR .'/components/com_userideas');
}

if (!defined('USERIDEAS_PATH_COMPONENT_SITE')) {
    define('USERIDEAS_PATH_COMPONENT_SITE', JPATH_SITE .'/components/com_userideas');
}

if (!defined('USERIDEAS_PATH_LIBRARY')) {
    define('USERIDEAS_PATH_LIBRARY', JPATH_LIBRARIES .'/Userideas');
}

JLoader::registerNamespace('Userideas', JPATH_LIBRARIES);

// Register helpers
JLoader::register('UserIdeasHelper', USERIDEAS_PATH_COMPONENT_ADMINISTRATOR .'/helpers/userideas.php');
JLoader::register('UserIdeasHelperRoute', USERIDEAS_PATH_COMPONENT_SITE .'/helpers/route.php');

// Register Observers
JLoader::register('UserIdeasObserverVote', USERIDEAS_PATH_COMPONENT_ADMINISTRATOR .'/tables/observers/vote.php');
JObserverMapper::addObserverClassToClass('UserIdeasObserverVote', 'UserIdeasTableVote', array('typeAlias' => 'com_userideas.vote'));

// Include HTML helpers
JHtml::addIncludePath(USERIDEAS_PATH_COMPONENT_SITE . '/helpers/html');

// Register class aliases.
JLoader::registerAlias('UserideasCategories', '\\Userideas\\Category\\Categories');
