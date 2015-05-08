<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Script file of the component
 */
class pkg_userIdeasInstallerScript
{
    /**
     * Method to install the component.
     *
     * @param string $parent
     *
     * @return void
     */
    public function install($parent)
    {
    }

    /**
     * Method to uninstall the component.
     *
     * @param string $parent
     *
     * @return void
     */
    public function uninstall($parent)
    {
    }

    /**
     * Method to update the component.
     *
     * @param string $parent
     *
     * @return void
     */
    public function update($parent)
    {
    }

    /**
     * Method to run before an install/update/uninstall method.
     *
     * @param string $type
     * @param string $parent
     *
     * @return void
     */
    public function preflight($type, $parent)
    {
    }

    /**
     * Method to run after an install/update/uninstall method.
     *
     * @param string $type
     * @param string $parent
     *
     * @return void
     */
    public function postflight($type, $parent)
    {
        if (!defined("COM_USERIDEAS_PATH_COMPONENT_ADMINISTRATOR")) {
            define("COM_USERIDEAS_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_userideas");
        }

        // Register Component helpers
        JLoader::register("UserIdeasInstallHelper", COM_USERIDEAS_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "install.php");

        // Start table with the information
        UserIdeasInstallHelper::startTable();

        // Requirements
        UserIdeasInstallHelper::addRowHeading(JText::_("COM_USERIDEAS_MINIMUM_REQUIREMENTS"));

        // Display result about verification for GD library
        $title = JText::_("COM_USERIDEAS_GD_LIBRARY");
        $info  = "";
        if (!extension_loaded('gd') and function_exists('gd_info')) {
            $result = array("type" => "important", "text" => JText::_("COM_USERIDEAS_WARNING"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JON"));
        }
        UserIdeasInstallHelper::addRow($title, $result, $info);

        // Display result about verification for cURL library
        $title = JText::_("COM_USERIDEAS_CURL_LIBRARY");
        $info  = "";
        if (!extension_loaded('curl')) {
            $info   = JText::_("COM_USERIDEAS_CURL_INFO");
            $result = array("type" => "important", "text" => JText::_("JOFF"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JON"));
        }
        UserIdeasInstallHelper::addRow($title, $result, $info);

        // Display result about verification Magic Quotes
        $title = JText::_("COM_USERIDEAS_MAGIC_QUOTES");
        $info  = "";
        if (get_magic_quotes_gpc()) {
            $info   = JText::_("COM_USERIDEAS_MAGIC_QUOTES_INFO");
            $result = array("type" => "important", "text" => JText::_("JON"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JOFF"));
        }
        UserIdeasInstallHelper::addRow($title, $result, $info);

        // Display result about PHP version.
        $title = JText::_("COM_USERIDEAS_PHP_VERSION");
        $info  = "";
        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
            $result = array("type" => "important", "text" => JText::_("COM_USERIDEAS_WARNING"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        UserIdeasInstallHelper::addRow($title, $result, $info);

        // Display result about verification of installed ITPrism Library
        jimport("itprism.version");
        $title = JText::_("COM_USERIDEAS_ITPRISM_LIBRARY");
        $info  = "";
        if (!class_exists("ITPrismVersion")) {
            $info   = JText::_("COM_USERIDEAS_ITPRISM_LIBRARY_DOWNLOAD");
            $result = array("type" => "important", "text" => JText::_("JNO"));
        } else {
            $result = array("type" => "success", "text" => JText::_("JYES"));
        }
        UserIdeasInstallHelper::addRow($title, $result, $info);

        // Installed extensions

        UserIdeasInstallHelper::addRowHeading(JText::_("COM_USERIDEAS_INSTALLED_EXTENSIONS"));

        // UserIdeas Library
        $result = array("type" => "success", "text" => JText::_("COM_USERIDEAS_INSTALLED"));
        UserIdeasInstallHelper::addRow(JText::_("COM_USERIDEAS_USERIDEAS_LIBRARY"), $result, JText::_("COM_USERIDEAS_LIBRARY"));

        // System - UserIdeasVote
        $result = array("type" => "success", "text" => JText::_("COM_USERIDEAS_INSTALLED"));
        UserIdeasInstallHelper::addRow(JText::_("COM_USERIDEAS_SYSTEM_USERIDEASVOTE"), $result, JText::_("COM_USERIDEAS_PLUGIN"));

        // UserIdeas - Vote
        $result = array("type" => "success", "text" => JText::_("COM_USERIDEAS_INSTALLED"));
        UserIdeasInstallHelper::addRow(JText::_("COM_USERIDEAS_USERIDEAS_VOTE"), $result, JText::_("COM_USERIDEAS_PLUGIN"));

        // Content - User Ideas - Admin Mail
        $result = array("type" => "success", "text" => JText::_("COM_USERIDEAS_INSTALLED"));
        UserIdeasInstallHelper::addRow(JText::_("COM_USERIDEAS_USERIDEAS_ADMIN_MAIL"), $result, JText::_("COM_USERIDEAS_PLUGIN"));

        // End table
        UserIdeasInstallHelper::endTable();

        echo JText::sprintf("COM_USERIDEAS_MESSAGE_REVIEW_SAVE_SETTINGS", JRoute::_("index.php?option=com_userideas"));

        jimport("itprism.version");
        if (!class_exists("ITPrismVersion")) {
            echo JText::_("COM_USERIDEAS_MESSAGE_INSTALL_ITPRISM_LIBRARY");
        }
    }
}
