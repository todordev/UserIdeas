<?php
/**
 * @package      ITPrism components
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * These class contains methods using for upgrading the extension
 */
class UserideasInstallHelper
{
    public static function startTable()
    {
        echo '
        <div style="width: 600px;">
        <table class="table table-bordered table-striped">';
    }

    public static function endTable()
    {
        echo "</table></div>";
    }

    public static function addRowHeading($heading)
    {
        echo '
	    <tr class="info">
            <td colspan="3">' . $heading . '</td>
        </tr>';
    }

    /**
     * Display an HTML code for a row
     *
     * @param string $title
     * @param array  $result
     * @param string  $info
     *
     * <code>
     * $result = array(
     *    type => success, important, warning,
     *    text => yes, no, off, on, warning,...
     * )
     *
     * </code>
     */
    public static function addRow($title, $result, $info)
    {

        $outputType = Joomla\Utilities\ArrayHelper::getValue($result, 'type');
        $outputText = Joomla\Utilities\ArrayHelper::getValue($result, 'text');

        $output = '';
        if (JString::strlen($outputType) > 0 and JString::strlen($outputText) > 0) {
            $output = '<span class="label label-' . $outputType . '">' . $outputText . '</span>';
        }

        echo '
	    <tr>
            <td>' . $title . '</td>
            <td>' . $output . '</td>
            <td>' . $info . '</td>
        </tr>';
    }

    public static function createFolder($imagesPath)
    {
        // Create image folder
        if (true !== JFolder::create($imagesPath)) {
            JLog::add(JText::sprintf('COM_USERIDEAS_ERROR_CANNOT_CREATE_FOLDER', $imagesPath));
        } else {

            // Copy index.html
            $indexFile = JPath::clean($imagesPath . DIRECTORY_SEPARATOR . 'index.html');
            $html      = '<html><body style="background-color: #fff;"></body></html>';
            if (true !== JFile::write($indexFile, $html)) {
                JLog::add(JText::sprintf('COM_USERIDEAS_ERROR_CANNOT_SAVE_FILE', $indexFile));
            }

        }
    }

    public static function createContentType()
    {
        $typeCategory = array('type_title' => 'Userideas Category','type_alias' => 'com_userideas.category','table' => '{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}','rules' => '','field_mappings' => '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description", "core_hits":"hits","core_publish_up":"null","core_publish_down":"null","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"null", "core_urls":"null", "core_version":"version", "core_ordering":"null", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"parent_id", "core_xreference":"null", "asset_id":"asset_id"}, "special": {"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}}','router' => 'UserideasHelperRoute::getCategoryRoute','content_history_options' => '{"formFile":"administrator\\/components\\/com_categories\\/models\\/forms\\/category.xml", "hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path","extension"], "ignoreChanges":["modified_user_id", "modified_time", "checked_out", "checked_out_time", "version", "hits", "path"],"convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"parent_id","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}');
        $typeItem     = array('type_title' => 'Userideas Item','type_alias' => 'com_userideas.item','table' => '{"special":{"dbtable":"#__uideas_items","key":"id","type":"Item","prefix":"UserideasTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}','rules' => '','field_mappings' => '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"record_date","core_modified_time":"null","core_body":"description","core_hits":"hits","core_publish_up":"null","core_publish_down":"null","core_access":"null","core_params":"null","core_featured":"null","core_metadata":"null","core_language":"null","core_images":"null","core_urls":"null","core_version":"null","core_ordering":"ordering","core_metakey":"null","core_metadesc":"null","core_catid":"catid","core_xreference":"null","asset_id":"null"},"special":{"votes":"votes","status_id":"status_id","user_id":"user_id"}}','router' => 'UserideasHelperRoute::getDetailsRoute','content_history_options' => '{"formFile":"administrator\\/components\\/com_userideas\\/models\\/forms\\/item.xml","hideFields":["votes","hits","record_date"],"ignoreChanges":["votes","hits","record_date",],"convertToInt":["votes","hits","ordering",	"published",	"status_id",	"catid",	"user_id"],"displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"status_id","targetTable":"#__uideas_statuses","targetColumn":"id","displayColumn":"name"}]}');

        $db = JFactory::getDbo();

        // Insert com_userideas.category
        $query = $db->getQuery(true);
        $query
            ->select('a.type_id')
            ->from($db->quoteName('#__content_types', 'a'))
            ->where('a.type_alias = ' . $db->quote('com_userideas.category'));

        $db->setQuery($query, 0, 1);
        $result = $db->loadResult();

        if (!$result) {
            $typeCategory = Joomla\Utilities\ArrayHelper::toObject($typeCategory);
            $db->insertObject('#__content_types', $typeCategory);
        }

        // Insert com_userideas.item
        $query = $db->getQuery(true);
        $query
            ->select('a.type_id')
            ->from($db->quoteName('#__content_types', 'a'))
            ->where('a.type_alias = ' . $db->quote('com_userideas.item'));

        $db->setQuery($query, 0, 1);
        $result = $db->loadResult();

        if (!$result) {
            $typeItem = Joomla\Utilities\ArrayHelper::toObject($typeItem);
            $db->insertObject('#__content_types', $typeItem);
        }
    }
}
