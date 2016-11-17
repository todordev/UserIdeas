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

class UserideasModelForm extends JModelForm
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
    public function getTable($type = 'Item', $prefix = 'UserideasTable', $config = array())
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
        $value = $app->input->getInt('id', 0);
        $this->setState('item.id', $value);

        // Get category ID
        $value = $app->getUserState($this->option . '.items.catid');
        $this->setState('item.catid', $value);

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
        $form = $this->loadForm($this->option . '.form', 'form', array('control' => 'jform', 'load_data' => $loadData));
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

        $data = $app->getUserState($this->option . '.edit.form.data', array());
        if (!$data) {
            $itemId = (int)$this->getState('item.id');
            if ($itemId > 0) {
                $data = $this->getItem($itemId);
            }
        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param   int $itemId The id of the primary key.
     *
     * @return  stdClass|null
     * @since   11.1
     */
    public function getItem($itemId = 0)
    {
        if (!$itemId) {
            $itemId = (int)$this->getState('item.id');
        }

        if (!$this->item and $itemId > 0) {
            $db = $this->getDbo();
            /** @var $db JDatabaseDriver */

            $query = $db->getQuery(true);

            // Select the required fields from the table.
            $query->select(
                $this->getState(
                    'item.select',
                    'a.id, a.title, a.description, a.catid, a.user_id, a.params, a.access, ' .
                    'c.access AS category_access'
                )
            );
            $query->from($db->quoteName('#__uideas_items', 'a'));
            $query->leftJoin($db->quoteName('#__categories', 'c') . ' ON a.catid = c.id');

            // Filter by item ID.
            if ((int)$itemId > 0) {
                $query->where('a.id = ' . (int)$itemId);
            }

            $db->setQuery($query);
            $this->item = $db->loadObject();

            if ($this->item !== null) {
                if ($this->item->params === null) {
                    $this->item->params = '{}';
                }

                if ($this->item->params !== '') {
                    $params = new \Joomla\Registry\Registry();
                    $params->loadString($this->item->params);

                    $this->item->params = $params;
                }

                $this->prepareAccess($this->item);
            }
        }

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
     *
     * @return    mixed        The record id on success, null on failure.
     * @since    1.6
     */
    public function save($data)
    {
        $id          = ArrayHelper::getValue($data, 'id', 0, 'int');
        $title       = ArrayHelper::getValue($data, 'title');
        $description = ArrayHelper::getValue($data, 'description');
        $categoryId  = ArrayHelper::getValue($data, 'catid', 0, 'int');
        $userId      = ArrayHelper::getValue($data, 'user_id', 0, 'int');
        $attachment  = ArrayHelper::getValue($data, 'attachment', array(), 'array');

        /** @var $row UserideasTableItem */
        $row = $this->getTable();
        $row->load($id);

        // If there is an ID, the item is not new
        $isNew = true;
        if ($row->get('id')) {
            $isNew = false;
        }

        $row->set('title', $title);
        $row->set('description', $description);
        $row->set('catid', $categoryId);

        if ($isNew) {
            $recordDate = new JDate();
            $row->set('record_date', $recordDate->toSql());
            $row->set('user_id', $userId);

            // Set status
            $statuses      = Userideas\Status\Statuses::getInstance(JFactory::getDbo());
            $defaultStatus = $statuses->getDefault();

            if ($defaultStatus !== null and $defaultStatus->id > 0) {
                $row->set('status_id', (int)$defaultStatus->id);
            }

            // Auto publishing
            $params = JComponentHelper::getParams('com_userideas');
            /** @var  $params Joomla\Registry\Registry */

            $published = $params->get('security_item_auto_publish', 0);
            $row->set('published', $published);

            $access = $params->get('default_access', JFactory::getApplication()->get('access'));
            $row->set('access', $access);
        } else {
            $tags   = new JHelperTags;
            $tagIds = $tags->getTagIds($row->get('id'), 'com_userideas.item');
            if ($tagIds !== '') {
                $tagIds = explode(',', $tagIds);
                $tagIds = ArrayHelper::toInteger($tagIds);
            }

            // Set the tags.
            if (is_array($tagIds) and count($tagIds) > 0) {
                $row->set('newTags', $tagIds);
            }
        }

        $this->prepareTable($row);
        $row->store();

        if (count($attachment) > 0) {
            $this->prepareAttachment($row, $attachment);
        }

        $this->triggerAfterSaveEvent($row, $isNew);

        $this->cleanCache();

        return $row->get('id');
    }

    protected function triggerAfterSaveEvent($row, $isNew)
    {
        // Trigger the event

        $context = $this->option . '.' . $this->getName();

        // Include the content plugins.
        $dispatcher = JEventDispatcher::getInstance();
        JPluginHelper::importPlugin('content');

        // Trigger the onContentAfterSave event.
        $results = $dispatcher->trigger('onContentAfterSave', array($context, &$row, $isNew));
        if (in_array(false, $results, true)) {
            throw new RuntimeException(JText::_('COM_USERIDEAS_ERROR_DURING_ITEM_POSTING_PROCESS'));
        }
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param UserideasTableItem $table
     *
     * @since    1.6
     */
    protected function prepareTable(&$table)
    {
        // get maximum order number
        if (!$table->get('id') and !$table->get('ordering')) {
            // Set ordering to the last item if not set
            $db    = $this->getDbo();
            $query = $db->getQuery(true);
            $query
                ->select('MAX(a.ordering)')
                ->from($db->quoteName('#__uideas_items', 'a'));

            $db->setQuery($query, 0, 1);
            $max = $db->loadResult();

            $table->set('ordering', $max + 1);
        }

        // If does not exist alias, I will generate the new one from the title
        if (!$table->get('alias')) {
            $table->set('alias', $table->get('title'));
        }

        $table->set('alias', Prism\Utilities\StringHelper::stringUrlSafe($table->get('alias')));
    }

    /**
     * Store the data about attachment in database.
     * Move the file from temporary folder to the media folder.
     *
     * @param UserideasTableItem $table
     * @param array              $attachmentData
     *
     * @throws \Exception
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    protected function prepareAttachment($table, array $attachmentData)
    {
        if ($table->get('id')) {
            $app = JFactory::getApplication();
            /** @var $app JApplicationSite */

            $params = JComponentHelper::getParams('com_userideas');

            $filesystemHelper = new Prism\Filesystem\Helper($params);

            // Get the filename from the session.
            $temporaryFolder = JPath::clean($app->get('tmp_path'));
            $mediaFolder     = $filesystemHelper->getMediaFolder($table->get('id'), Userideas\Constants::ITEM_FOLDER);
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
                'item_id' => $table->get('id'),
                'source'  => 'item'
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
                ->setItemId($table->get('id'))
                ->setCommentId(0)
                ->setUserId($table->get('user_id'))
                ->setSource('item');

            $attachment->store();

            // Check for valid file.
            if (!$manager->has('local://' . $attachmentData['filename'])) {
                throw new RuntimeException(JText::sprintf('COM_USERIDEAS_ERROR_FILE_NOT_FOUND_S', $attachmentData['filename']));
            }

            $manager->move('local://' . $attachmentData['filename'], 'storage://' . $newFile);
        }
    }

    /**
     * Delete the profile picture of the user.
     *
     * @param int $itemId
     *
     * @throws Exception
     */
    public function removeFile($itemId)
    {
        $item = new Userideas\Item\Item(JFactory::getDbo());
        $item->load($itemId);

        if ($item->getId() > 0) {
            $attachment = $item->getAttachment();

            if ($attachment !== null and $attachment->getId()) {
                $params = JComponentHelper::getParams('com_userideas');

                $filesystemHelper = new Prism\Filesystem\Helper($params);

                $storageFilesystem = $filesystemHelper->getFilesystem();
                $file              = $filesystemHelper->getMediaFolder($itemId, Userideas\Constants::ITEM_FOLDER) . '/'.$attachment->getFilename();

                // Delete the profile pictures.
                if ($storageFilesystem->has($file)) {
                    $storageFilesystem->delete($file);
                }

                $attachment->remove();
            }
        }
    }

    /**
     * Method to prepare access data.
     *
     * @param   stdClass $item
     */
    public function prepareAccess($item)
    {
        // Compute selected asset permissions.
        $user   = JFactory::getUser();
        $userId = (int)$user->get('id');
        $itemId = (int)$item->id;
        $asset  = 'com_userideas.item.' . $itemId;

        // Check general edit permission first.
        if ($userId > 0 and $user->authorise('core.edit', $asset)) {
            $item->params->set('access-edit', true);

            // Now check if edit.own is available.
        } elseif ($userId > 0 and $user->authorise('core.edit.own', $asset)) {
            // Check for a valid user and that they are the owner.
            if ($userId === (int)$item->user_id) {
                $item->params->set('access-edit', true);
            }
        }

        // Check edit state permission.
        if ($itemId > 0) {
            // Existing item
            $item->params->set('access-change', $user->authorise('core.edit.state', $asset));
        } else {
            // New item.
            $catId = (int)$this->getState('item.catid');

            // Set the new category if it is selected and there is enough permissions to be selected.
            if ($catId) {
                $item->params->set('access-change', $user->authorise('core.edit.state', 'com_userideas.category.' . $catId));
                $item->catid = $catId;
            } else {
                $item->params->set('access-change', $user->authorise('core.edit.state', 'com_userideas'));
            }
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
