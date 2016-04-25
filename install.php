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
        if (!defined('COM_USERIDEAS_PATH_COMPONENT_ADMINISTRATOR')) {
            define('COM_USERIDEAS_PATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_userideas');
        }

        jimport('Prism.init');
        jimport('Userideas.init');

        // Register Component helpers
        JLoader::register('UserideasInstallHelper', COM_USERIDEAS_PATH_COMPONENT_ADMINISTRATOR . '/helpers/install.php');
        
        // Start table with the information
        UserideasInstallHelper::startTable();

        // Requirements
        UserideasInstallHelper::addRowHeading(JText::_('COM_USERIDEAS_MINIMUM_REQUIREMENTS'));

        // Display result about verification for GD library
        $title = JText::_('COM_USERIDEAS_GD_LIBRARY');
        $info  = '';
        if (!extension_loaded('gd') and function_exists('gd_info')) {
            $result = array('type' => 'important', 'text' => JText::_('COM_USERIDEAS_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JON'));
        }
        UserideasInstallHelper::addRow($title, $result, $info);

        // Display result about verification for cURL library
        $title = JText::_('COM_USERIDEAS_CURL_LIBRARY');
        $info  = '';
        if (!extension_loaded('curl')) {
            $info   = JText::_('COM_USERIDEAS_CURL_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JOFF'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JON'));
        }
        UserideasInstallHelper::addRow($title, $result, $info);

        // Display result about verification Magic Quotes
        $title = JText::_('COM_USERIDEAS_MAGIC_QUOTES');
        $info  = '';
        if (get_magic_quotes_gpc()) {
            $info   = JText::_('COM_USERIDEAS_MAGIC_QUOTES_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JON'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JOFF'));
        }
        UserideasInstallHelper::addRow($title, $result, $info);

        // Display result about PHP version.
        $title = JText::_('COM_USERIDEAS_PHP_VERSION');
        $info  = '';
        if (version_compare(PHP_VERSION, '5.5.0') < 0) {
            $result = array('type' => 'important', 'text' => JText::_('COM_USERIDEAS_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        UserideasInstallHelper::addRow($title, $result, $info);

        // Display result about MySQL Version.
        $title = JText::_('COM_USERIDEAS_MYSQL_VERSION');
        $info  = '';
        $dbVersion = JFactory::getDbo()->getVersion();
        if (version_compare($dbVersion, '5.5.3', '<')) {
            $result = array('type' => 'important', 'text' => JText::_('COM_USERIDEAS_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        UserideasInstallHelper::addRow($title, $result, $info);

        // Display result about verification of installed Prism Library
        $info  = '';
        if (!class_exists('Prism\\Version')) {
            $title  = JText::_('COM_USERIDEAS_PRISM_LIBRARY');
            $info   = JText::_('COM_USERIDEAS_PRISM_LIBRARY_DOWNLOAD');
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $prismVersion   = new Prism\Version();
            $text           = JText::sprintf('COM_USERIDEAS_CURRENT_V_S', $prismVersion->getShortVersion());

            if (class_exists('Userideas\\Version')) {
                $componentVersion = new Userideas\Version();
                $title            = JText::sprintf('COM_USERIDEAS_PRISM_LIBRARY_S', $componentVersion->requiredPrismVersion);

                if (version_compare($prismVersion->getShortVersion(), $componentVersion->requiredPrismVersion, '<')) {
                    $info   = JText::_('COM_USERIDEAS_PRISM_LIBRARY_DOWNLOAD');
                    $result = array('type' => 'warning', 'text' => $text);
                }

            } else {
                $title  = JText::_('COM_USERIDEAS_PRISM_LIBRARY');
                $result = array('type' => 'success', 'text' => $text);
            }
        }
        UserideasInstallHelper::addRow($title, $result, $info);

        // Installed extensions

        UserideasInstallHelper::addRowHeading(JText::_('COM_USERIDEAS_INSTALLED_EXTENSIONS'));

        // Userideas Library
        $result = array('type' => 'success', 'text' => JText::_('COM_USERIDEAS_INSTALLED'));
        UserideasInstallHelper::addRow(JText::_('COM_USERIDEAS_USERIDEAS_LIBRARY'), $result, JText::_('COM_USERIDEAS_LIBRARY'));

        // System - UserideasVote
        $result = array('type' => 'success', 'text' => JText::_('COM_USERIDEAS_INSTALLED'));
        UserideasInstallHelper::addRow(JText::_('COM_USERIDEAS_SYSTEM_USERIDEASVOTE'), $result, JText::_('COM_USERIDEAS_PLUGIN'));

        // Userideas - Vote
        $result = array('type' => 'success', 'text' => JText::_('COM_USERIDEAS_INSTALLED'));
        UserideasInstallHelper::addRow(JText::_('COM_USERIDEAS_USERIDEAS_VOTE'), $result, JText::_('COM_USERIDEAS_PLUGIN'));

        // Content - User Ideas - Admin Mail
        $result = array('type' => 'success', 'text' => JText::_('COM_USERIDEAS_INSTALLED'));
        UserideasInstallHelper::addRow(JText::_('COM_USERIDEAS_USERIDEAS_ADMIN_MAIL'), $result, JText::_('COM_USERIDEAS_PLUGIN'));

        // End table
        UserideasInstallHelper::endTable();

        echo JText::sprintf('COM_USERIDEAS_MESSAGE_REVIEW_SAVE_SETTINGS', JRoute::_('index.php?option=com_userideas'));

        if (!class_exists('Prism\\Version')) {
            echo JText::_('COM_USERIDEAS_MESSAGE_INSTALL_PRISM_LIBRARY');
        } else {
            if (class_exists('Userideas\\Version')) {
                $prismVersion     = new Prism\Version();
                $componentVersion = new Userideas\Version();
                if (version_compare($prismVersion->getShortVersion(), $componentVersion->requiredPrismVersion, '<')) {
                    echo JText::_('COM_USERIDEAS_MESSAGE_INSTALL_PRISM_LIBRARY');
                }
            }
        }

        // Create content type for used by tags.
        UserideasInstallHelper::createContentType();
    }
}
