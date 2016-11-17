<?php
/**
 * @package      Userideas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Utilities\ArrayHelper;

// no direct access
defined('_JEXEC') or die;

JLoader::import('Prism.libs.Aws.init');
JLoader::import('Prism.libs.GuzzleHttp.init');

class UserideasModelAttachment extends JModelLegacy
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type   The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Attachment', $prefix = 'UserideasTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to auto-populate the model state.
     * Note. Calling getState in this method will result in recursion.
     * @since    1.6
     */
    protected function populateState()
    {
        parent::populateState();

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Load the parameters.
        $value = $app->getParams($this->option);
        $this->setState('params', $value);
    }

    /**
     * Delete the attachment file.
     *
     * @param Userideas\Attachment\Attachment $attachment
     *
     * @throws Exception
     */
    public function removeFile($attachment)
    {
        if ($attachment->getId() > 0) {
            $params            = JComponentHelper::getParams('com_userideas');
            $filesystemHelper  = new Prism\Filesystem\Helper($params);

            $storageFilesystem = $filesystemHelper->getFilesystem();
            $file              = $filesystemHelper->getMediaFolder($attachment->getItemId(), Userideas\Constants::ITEM_FOLDER) . '/'.$attachment->getFilename();

            // Delete the profile pictures.
            if ($storageFilesystem->has($file)) {
                $storageFilesystem->delete($file);
            }

            $attachment->remove();
        }
    }
}
