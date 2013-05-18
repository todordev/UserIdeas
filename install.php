<?php
/**
 * @package      ITPrism Components
 * @subpackage   UserIdeas
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * UserIdeas is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Script file of the component
 */
class pkg_userIdeasInstallerScript {
    
    /**
     * method to install the component
     *
     * @return void
     */
    public function install($parent) {
    }
    
    /**
     * method to uninstall the component
     *
     * @return void
     */
    public function uninstall($parent) {
    }
    
    /**
     * method to update the component
     *
     * @return void
     */
    public function update($parent) {
    }
    
    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    public function preflight($type, $parent) {
    }
    
    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    public function postflight($type, $parent) {
        
        if(strcmp($type, "install") == 0) {
            
            if(!defined("COM_USERIDEAS_PATH_COMPONENT_ADMINISTRATOR")) {
                define("COM_USERIDEAS_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR ."com_userideas");
            }
            
            // Register Component helpers
            JLoader::register("UserIdeasInstallHelper", COM_USERIDEAS_PATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."installer.php");
        
            $this->bootstrap    = JPath::clean( JPATH_SITE.DIRECTORY_SEPARATOR."media".DIRECTORY_SEPARATOR."com_userideas".DIRECTORY_SEPARATOR."css".DIRECTORY_SEPARATOR. "admin".DIRECTORY_SEPARATOR."bootstrap.min.css" );
        
            $style = '<style>'.file_get_contents($this->bootstrap).'</style>';
            echo $style;
            
            // Start table with the information
            UserIdeasInstallHelper::startTable();
        
            // Requirements
            UserIdeasInstallHelper::addRowHeading(JText::_("COM_USERIDEAS_MINIMUM_REQUIREMENTS"));
            
            // Display result about verification for GD library
            $title  = JText::_("COM_USERIDEAS_GD_LIBRARY");
            $info   = "";
            if(!extension_loaded('gd') AND function_exists('gd_info')) {
                $result = array("type" => "important", "text" => JText::_("COM_USERIDEAS_WARNING"));
            } else {
                $result = array("type" => "success"  , "text" => JText::_("JON"));
            }
            UserIdeasInstallHelper::addRow($title, $result, $info);
            
            // Display result about verification for cURL library
            $title  = JText::_("COM_USERIDEAS_CURL_LIBRARY");
            $info   = "";
            if( !extension_loaded('curl') ) {
                $info   = JText::_("COM_USERIDEAS_CURL_INFO");
                $result = array("type" => "important", "text" => JText::_("JOFF"));
            } else {
                $result = array("type" => "success"  , "text" => JText::_("JON"));
            }
            UserIdeasInstallHelper::addRow($title, $result, $info);
            
            // Display result about verification Magic Quotes
            $title  = JText::_("COM_USERIDEAS_MAGIC_QUOTES");
            $info   = "";
            if( get_magic_quotes_gpc() ) {
                $info   = JText::_("COM_USERIDEAS_MAGIC_QUOTES_INFO");
                $result = array("type" => "important", "text" => JText::_("JON"));
            } else {
                $result = array("type" => "success"  , "text" => JText::_("JOFF"));
            }
            UserIdeasInstallHelper::addRow($title, $result, $info);
            
            // Display result about verification of installed ITPrism Library
            jimport("itprism.version");
            $title  = JText::_("COM_USERIDEAS_ITPRISM_LIBRARY");
            $info   = "";
            if( !class_exists("ITPrismVersion") ) {
                $info   = JText::_("COM_USERIDEAS_ITPRISM_LIBRARY_DOWNLOAD");
                $result = array("type" => "important", "text" => JText::_("JNO"));
            } else {
                $result = array("type" => "success", "text" => JText::_("JYES"));
            }
            UserIdeasInstallHelper::addRow($title, $result, $info);
            
            // Installed extensions
            
            UserIdeasInstallHelper::addRowHeading(JText::_("COM_USERIDEAS_INSTALLED_EXTENSIONS"));
            
            // CrowdFunding Library
            $result = array("type" => "success"  , "text" => JText::_("COM_USERIDEAS_INSTALLED"));
            UserIdeasInstallHelper::addRow(JText::_("COM_USERIDEAS_USERIDEAS_LIBRARY"), $result, JText::_("COM_USERIDEAS_LIBRARY"));
            
            // System - UserIdeasVote
            $result = array("type" => "success"  , "text" => JText::_("COM_USERIDEAS_INSTALLED"));
            UserIdeasInstallHelper::addRow(JText::_("COM_USERIDEAS_SYSTEM_USERIDEASVOTE"), $result, JText::_("COM_USERIDEAS_PLUGIN"));
            
            // UserIdeas - Vote
            $result = array("type" => "success"  , "text" => JText::_("COM_USERIDEAS_INSTALLED"));
            UserIdeasInstallHelper::addRow(JText::_("COM_USERIDEAS_USERIDEAS_VOTE"), $result, JText::_("COM_USERIDEAS_PLUGIN"));
            
            // End table
            UserIdeasInstallHelper::endTable();
            
        }
        
        echo JText::sprintf("COM_USERIDEAS_MESSAGE_REVIEW_SAVE_SETTINGS", JRoute::_("index.php?option=com_userideas"));
        
        jimport("itprism.version");
        if(!class_exists("ITPrismVersion")) {
            echo JText::_("COM_USERIDEAS_MESSAGE_INSTALL_ITPRISM_LIBRARY");
        }
        
    }
}
