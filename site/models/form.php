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
     * @param    array   $data     An optional array of data for the form to interogate.
     * @param    boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return    JForm    A JForm object on success, false on failure
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
                $userId = JFactory::getUser()->get('id');
                $data   = $this->getItem($itemId, $userId);
            }
        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param   int $pk     The id of the primary key.
     * @param   int $userId The user Id
     *
     * @return  stdClass
     * @since   11.1
     */
    public function getItem($pk = 0, $userId = 0)
    {
        if (!$this->item) {
            if (!$pk or !$userId) {
                return null;
            }

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
            if ((int)$pk > 0) {
                $query->where('a.id = ' . (int)$pk);
            }

            // Filter by user ID.
            if ($userId > 0) {
                $query->where('a.user_id = ' . (int)$userId);
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
     * @return    mixed        The record id on success, null on failure.
     * @since    1.6
     */
    public function save($data)
    {
        $id          = Joomla\Utilities\ArrayHelper::getValue($data, 'id', 0, 'int');
        $title       = Joomla\Utilities\ArrayHelper::getValue($data, 'title');
        $description = Joomla\Utilities\ArrayHelper::getValue($data, 'description');
        $categoryId  = Joomla\Utilities\ArrayHelper::getValue($data, 'catid', 0, 'int');
        $userId      = Joomla\Utilities\ArrayHelper::getValue($data, 'user_id', 0, 'int');

        $keys = array(
            'id'      => $id,
            'user_id' => $userId
        );

        // Load a record from the database
        $row = $this->getTable();
        /** @var $row UserideasTableItem */

        $row->load($keys);

        // If there is an id, the item is not new
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
        }

        $this->prepareTable($row);

        $row->store();

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
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_DURING_ITEM_POSTING_PROCESS'));
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
            if ((int)JFactory::getConfig()->get('unicodeslugs') === 1) {
                $alias = JFilterOutput::stringURLUnicodeSlug($table->get('title'));
            } else {
                $alias = JFilterOutput::stringURLSafe($table->get('title'));
            }
            $table->set('alias', $alias);
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
        if ($user->authorise('core.edit', $asset)) {
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
            $catId         = (int)$this->getState('item.catid');

            // Set the new category if it is selected and there is enough permissions to be selected.
            if ($catId) {
                $item->params->set('access-change', $user->authorise('core.edit.state', 'com_userideas.category.' . $catId));
                $item->catid = $catId;
            } else {
                $item->params->set('access-change', $user->authorise('core.edit.state', 'com_userideas'));
            }
        }
    }
}
