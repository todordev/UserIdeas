<?php
/**
 * @package      Userideas
 * @subpackage   Initialization
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
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
JLoader::register('UserideasHelper', USERIDEAS_PATH_COMPONENT_ADMINISTRATOR .'/helpers/userideas.php');
JLoader::register('UserideasHelperRoute', USERIDEAS_PATH_COMPONENT_SITE .'/helpers/route.php');

// Register Observers
JLoader::register('UserideasObserverVote', USERIDEAS_PATH_COMPONENT_ADMINISTRATOR .'/tables/observers/vote.php');
JObserverMapper::addObserverClassToClass('UserideasObserverVote', 'UserideasTableVote', array('typeAlias' => 'com_userideas.vote'));

// Include HTML helpers
JHtml::addIncludePath(USERIDEAS_PATH_COMPONENT_SITE . '/helpers/html');

// Register class aliases.
JLoader::registerAlias('UserideasCategories', '\\Userideas\\Category\\Categories');

JLog::addLogger(
    array(
        // Sets file name
        'text_file' => 'com_userideas.errors.php'
    ),
    // Sets messages of all log levels to be sent to the file
    JLog::CRITICAL + JLog::EMERGENCY + JLog::ERROR,
    // The log category/categories which should be recorded in this file
    // In this case, it's just the one category from our extension, still
    // we need to put it inside an array
    array('com_userideas')
);
