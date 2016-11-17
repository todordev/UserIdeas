<?php
/**
 * @package      Userideas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Utilities\ArrayHelper;
use Joomla\Registry\Registry;

// no direct access
defined('_JEXEC') or die;

JLoader::import('Prism.libs.Aws.init');
JLoader::import('Prism.libs.GuzzleHttp.init');

class UserideasModelComment extends JModelForm
{
    protected $item;

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
    public function getTable($type = 'Comment', $prefix = 'UserideasTable', $config = array())
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

        // Get the pk of the record from the request.
        $value = $app->input->getInt('id');
        $this->setState('item_id', $value);

        $value = $app->input->getInt('comment_id');
        $this->setState('comment_id', $value);

        // Load the parameters.
        $value = $app->getParams($this->option);
        $this->setState('params', $value);
    }

    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML and then an event is fired
     * for users plugins to extend the form with extra fields.
     *
     * @param    array   $data     An optional array of data for the form to interrogate.
     * @param    boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return    JForm|bool    A JForm object on success, false on failure
     * @since    1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.comment', 'comment', array('control' => 'jform', 'load_data' => $loadData));
        if (!$form) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return    mixed    The data for the form.
     * @since    1.6
     */
    protected function loadFormData()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $data = $app->getUserState($this->option . '.edit.comment.data', array());
        if (!$data) {
            $commentId = (int)$this->getState('comment_id');
            $userId    = (int)JFactory::getUser()->get('id');

            // Get comment data
            $data = $this->getItem($commentId, $userId);

            $itemId = (int)$this->getState('item_id');
            if (!isset($data->item_id) or !$data->item_id) {
                $data->item_id = $itemId;
            }
        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer $commentId The id of the primary key.
     * @param   integer $userId    The user Id
     *
     * @return  JObject
     *
     * @throw   Exception
     * @since   11.1
     */
    public function getItem($commentId, $userId)
    {
        if ($this->item !== null) {
            return $this->item;
        }

        // Initialise variables.
        $table = $this->getTable();

        if ($commentId > 0 and $userId > 0) {
            $keys = array(
                'id'      => $commentId,
                'user_id' => $userId
            );

            // Attempt to load the row.
            $table->load($keys);
        }

        // Convert to the JObject before adding other data.
        $properties = $table->getProperties();
        $this->item = ArrayHelper::toObject($properties);

        return $this->item;
    }

    /**
     * Method to save the form data.
     *
     * @param    array $data The form data.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     * @throws \Exception
     *
     * @return  integer
     * @since    1.6
     */
    public function save($data)
    {
        $id      = ArrayHelper::getValue($data, 'id', 0, 'int');
        $comment = ArrayHelper::getValue($data, 'comment');
        $itemId  = ArrayHelper::getValue($data, 'item_id', 0, 'int');
        $userId      = (int)JFactory::getUser()->get('id');
        $attachment  = ArrayHelper::getValue($data, 'attachment', array(), 'array');

        $isNew  = false;
        $params = JComponentHelper::getParams($this->option);
        /** @var  $params Joomla\Registry\Registry */

        // Load a record from the database
        $row = $this->getTable();
        /** @var UserideasTableComment $row */

        if ($id > 0) {
            $keys = array(
                'id'      => $id,
                'user_id' => $userId
            );

            $row->load($keys);
        }

        $row->set('comment', $comment);

        // If there is no ID we are adding a new comment
        if (!$row->get('id')) {
            $isNew = true;

            $row->set('record_date', null);
            $row->set('item_id', $itemId);
            $row->set('user_id', $userId);

            $published = (!$params->get('security_comment_auto_publish', 0)) ? Prism\Constants::UNPUBLISHED : Prism\Constants::PUBLISHED;

            $row->set('published', $published);
        }

        $row->store(true);

        if (count($attachment) > 0) {
            $this->prepareAttachment($row, $attachment, $params);
        }

        $this->triggerAfterSaveEvent($row, $isNew);

        return $row->get('id');
    }

    protected function triggerAfterSaveEvent($row, $isNew)
    {
        // Trigger the event
        $context = $this->option . '.' . $this->getName();

        // Include the content plugins.
        $dispatcher = JEventDispatcher::getInstance();
        JPluginHelper::importPlugin('content');

        // Trigger the onCommentAfterSave event.
        $results = $dispatcher->trigger('onCommentAfterSave', array($context, &$row, $isNew));
        if (in_array(false, $results, true)) {
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_DURING_ITEM_POSTING_COMMENT'));
        }
    }

    /**
     * Store the data about attachment in database.
     * Move the file from temporary folder to the media folder.
     *
     * @param UserideasTableComment $table
     * @param array              $attachmentData
     * @param Registry              $params
     *
     * @throws \Exception
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    protected function prepareAttachment($table, array $attachmentData, Registry $params)
    {
        if ($table->get('id')) {
            $app = JFactory::getApplication();
            /** @var $app JApplicationSite */

            $filesystemHelper = new Prism\Filesystem\Helper($params);

            // Get the filename from the session.
            $temporaryFolder = JPath::clean($app->get('tmp_path'));
            $mediaFolder     = $filesystemHelper->getMediaFolder($table->get('item_id'), Userideas\Constants::ITEM_FOLDER);
            $newFile         = $mediaFolder . '/' . $attachmentData['filename'];

            $localAdapter      = new League\Flysystem\Adapter\Local($temporaryFolder);
            $localFilesystem   = new League\Flysystem\Filesystem($localAdapter);
            $storageFilesystem = $filesystemHelper->getFilesystem();

            $manager = new League\Flysystem\MountManager([
                'local'   => $localFilesystem,
                'storage' => $storageFilesystem
            ]);

            // Load attachment data from database.
            $keys       = array(
                'item_id'    => $table->get('item_id'),
                'comment_id' => $table->get('id'),
                'source'     => 'comment'
            );
            $attachment = new Userideas\Attachment\Attachment(JFactory::getDbo());
            $attachment->load($keys);

            // Remove old attachment.
            $oldFile = $mediaFolder . '/' . $attachment->getFilename();
            if ($attachment->getId() and $manager->has('storage://' . $oldFile)) {
                $manager->delete('storage://' . $oldFile);
            }

            $attachment
                ->setFilename($attachmentData['filename'])
                ->setFilesize($attachmentData['filesize'])
                ->setMime($attachmentData['mime'])
                ->setAttributes($attachmentData['attributes'])
                ->setItemId($table->get('item_id'))
                ->setCommentId($table->get('id'))
                ->setUserId($table->get('user_id'))
                ->setSource('comment');

            $attachment->store();

            // Check for valid file.
            if (!$manager->has('local://' . $attachmentData['filename'])) {
                throw new RuntimeException(JText::sprintf('COM_USERIDEAS_ERROR_FILE_NOT_FOUND_S', $attachmentData['filename']));
            }

            $manager->move('local://' . $attachmentData['filename'], 'storage://' . $newFile);
        }
    }

    /**
     * Upload a file
     *
     * @param  array $uploadedFileData
     *
     * @throws Exception
     * @return array
     */
    public function uploadFile($uploadedFileData)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $uploadedFile = ArrayHelper::getValue($uploadedFileData, 'tmp_name');
        $uploadedName = ArrayHelper::getValue($uploadedFileData, 'name');
        $errorCode    = ArrayHelper::getValue($uploadedFileData, 'error');

        // Joomla! media extension parameters
        $mediaParams = JComponentHelper::getParams('com_media');
        /** @var  $mediaParams Joomla\Registry\Registry */

        // Prepare size validator.
        $KB            = pow(1024, 2);
        $fileSize      = ArrayHelper::getValue($uploadedFileData, 'size', 0, 'int');
        $uploadMaxSize = $mediaParams->get('upload_maxsize') * $KB;

        $sizeValidator = new Prism\File\Validator\Size($fileSize, $uploadMaxSize);

        // Prepare server validator.
        $serverValidator = new Prism\File\Validator\Server($errorCode);

        $file = new Prism\File\File($uploadedFile);
        $file
            ->addValidator($sizeValidator)
            ->addValidator($serverValidator);

        // Prepare image validator.
        if ($file->hasImageExtension()) {
            $imageValidator = new Prism\File\Validator\Image($uploadedFile, $uploadedName);

            // Get allowed mime types from media manager options
            $mimeTypes = explode(',', $mediaParams->get('upload_mime'));
            $imageValidator->setMimeTypes($mimeTypes);

            // Get allowed image extensions from media manager options
            $imageExtensions = explode(',', $mediaParams->get('image_extensions'));
            $imageValidator->setImageExtensions($imageExtensions);

            $file->addValidator($imageValidator);
        }

        // Validate the file
        if (!$file->isValid()) {
            throw new RuntimeException($file->getError());
        }

        // Upload the file in temporary folder.
        $temporaryFolder = JPath::clean($app->get('tmp_path'), '/');
        $filesystemLocal = new Prism\Filesystem\Adapter\Local($temporaryFolder);
        $sourceFile      = $filesystemLocal->upload($uploadedFileData);

        if (!is_file($sourceFile)) {
            throw new RuntimeException('COM_USERIDEAS_ERROR_FILE_CANT_BE_UPLOADED');
        }

        $file = new Prism\File\File($sourceFile);

        return $file->extractFileData();
    }
}
