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

JLoader::import('Prism.libs.Aws.init');
JLoader::import('Prism.libs.GuzzleHttp.init');

JLoader::register('UserideasObserverItem', USERIDEAS_PATH_COMPONENT_ADMINISTRATOR .'/tables/observers/item.php');
JObserverMapper::addObserverClassToClass('UserideasObserverItem', 'UserideasTableItem', array('typeAlias' => 'com_userideas.item'));

class UserideasModelItem extends JModelAdmin
{
    /**
     * The type alias for this content type (for example, 'com_content.article').
     *
     * @var      string
     * @since    3.2
     */
    public $typeAlias = 'com_userideas.item';

    /**
     * The context used for the associations table
     *
     * @var      string
     * @since    3.4.4
     */
    protected $associationsContext = 'com_userideas.item';

    /**
     * Method to test whether a record can be deleted.
     *
     * @param   stdClass  $record  A record object.
     *
     * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
     *
     * @since   1.6
     */
    protected function canDelete($record)
    {
        if ((int)$record->id > 0) {
            if ((int)$record->published !== -2) {
                return false;
            }

            $user = JFactory::getUser();

            return $user->authorise('core.delete', $this->option.'.item.' . (int)$record->id);
        }

        return false;
    }

    /**
     * Method to test whether a record can have its state edited.
     *
     * @param   stdClass $record A record object.
     *
     * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
     *
     * @since   1.6
     */
    protected function canEditState($record)
    {
        $user = JFactory::getUser();

        // Check for existing article.
        if ((int)$record->id > 0) {
            return $user->authorise('core.edit.state', $this->option . '.item.' . (int)$record->id);
        } elseif ((int)$record->catid > 0) { // New article, so check against the category.
            return $user->authorise('core.edit.state', $this->option . '.category.' . (int)$record->catid);
        } else { // Default to component settings if neither article nor category known.
            return parent::canEditState($this->option);
        }
    }

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
     * Method to get the record form.
     *
     * @param   array   $data     An optional array of data for the form to interrogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm|bool   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.item', 'item', array('control' => 'jform', 'load_data' => $loadData));
        if (!$form) {
            return false;
        }

        $user = JFactory::getUser();
        $id   = (int)$this->getState('item.id');

        if ($id) {
            // Existing record. Can only edit in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.edit');

            // Existing record. Can only edit own articles in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.edit.own');
        } else {
            // New record. Can only create in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.create');
        }

        // Check for existing article.
        // Modify the form based on Edit State access controls.
        if ($id !== 0 and (!$user->authorise('core.edit.state', 'com_userideas.item.' . (int)$id)) or ($id === 0 && !$user->authorise('core.edit.state', 'com_userideas'))) {
            // Disable fields for display.
            $form->setFieldAttribute('published', 'disabled', 'true');
            $form->setFieldAttribute('status_id', 'disabled', 'true');
            $form->setFieldAttribute('access', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is an article you can edit.
            $form->setFieldAttribute('published', 'filter', 'unset');
            $form->setFieldAttribute('status_id', 'filter', 'unset');
            $form->setFieldAttribute('access', 'filter', 'unset');
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed   The data for the form.
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app  = JFactory::getApplication();
        $data = $app->getUserState($this->option . '.edit.item.data', array());

        if (!$data) {
            $data = $this->getItem();

            if ((int)$this->getState('item.id') === 0) {
                $filters = (array)$app->getUserState('com_userideas.items.filter');

                $state           = (array_key_exists('state', $filters) and $filters['state'] !== '') ? $filters['state'] : null;
                $data->published = $app->input->getInt('published', $state);

                $data->catid     = $app->input->getInt('catid', (!empty($filters['category']) ? $filters['category'] : null));
                $data->access    = $app->input->getInt('access', (!empty($filters['access']) ? $filters['access'] : JFactory::getConfig()->get('access')));
                $data->status_id = $app->input->getInt('status_id', (!empty($filters['status_id']) ? $filters['status_id'] : null));
            }
        }

        return $data;
    }

    /**
     * Save data into the DB
     *
     * @param array $data The data about item
     *
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return  int
     */
    public function save($data)
    {
        $id          = Joomla\Utilities\ArrayHelper::getValue($data, 'id', 0, 'int');
        $title       = Joomla\Utilities\ArrayHelper::getValue($data, 'title');
        $alias       = Joomla\Utilities\ArrayHelper::getValue($data, 'alias');
        $description = Joomla\Utilities\ArrayHelper::getValue($data, 'description');
        $statusId    = Joomla\Utilities\ArrayHelper::getValue($data, 'status_id', 0, 'int');
        $catId       = Joomla\Utilities\ArrayHelper::getValue($data, 'catid', 0, 'int');
        $published   = Joomla\Utilities\ArrayHelper::getValue($data, 'published');
        $created     = Joomla\Utilities\ArrayHelper::getValue($data, 'record_date');
        $params      = Joomla\Utilities\ArrayHelper::getValue($data, 'params');
        $rules       = Joomla\Utilities\ArrayHelper::getValue($data, 'rules', array(), 'array');
        $userId      = Joomla\Utilities\ArrayHelper::getValue($data, 'user_id', JFactory::getUser()->get('id'), 'int');

        // Get value of access option.
        $app           = JFactory::getApplication();
        $filters       = (array)$app->getUserState($this->option.'.items.filter');
        $defaultAccess = (!empty($filters['access']) ? $filters['access'] : $app->get('access'));
        $access        = Joomla\Utilities\ArrayHelper::getValue($data, 'access', $defaultAccess, 'int');

        // Encode parameters to JSON format.
        $params        = ($params !== null and is_array($params)) ? json_encode($params) : null;

        // Load a record from the database
        $row = $this->getTable();
        /** @var $row UserideasTableItem */

        $row->load($id);

        $row->set('title', $title);
        $row->set('alias', $alias);
        $row->set('description', $description);
        $row->set('status_id', $statusId);
        $row->set('user_id', $userId);
        $row->set('catid', $catId);
        $row->set('published', $published);
        $row->set('record_date', $created);
        $row->set('params', $params);
        $row->set('access', $access);

        // Set the rules.
        $row->setRules($rules);

        // Set the tags.
        if (array_key_exists('tags', $data) and is_array($data['tags']) and count($data['tags']) > 0) {
            $row->set('newTags', $data['tags']);
        }

        $this->prepareTable($row);

        $row->store();

        $this->cleanCache();

        return $row->get('id');
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param UserideasTableItem $table
     *
     * @since    1.6
     */
    protected function prepareTable($table)
    {
        if (!$table->get('params')) {
            $table->get('params', null);
        }

        if (!$table->get('description')) {
            $table->get('description', null);
        }

        // get maximum order number
        if (!$table->get('id') and !$table->get('ordering')) {
            // Set ordering to the last item if not set
            $db    = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query
                ->select('MAX(a.ordering)')
                ->from($db->quoteName('#__uideas_items', 'a'))
                ->where('a.catid = ' . (int)$table->catid);

            $db->setQuery($query, 0, 1);
            $max = (int)$db->loadResult();

            $table->set('ordering', $max + 1);
        }

        // If does not exist alias, I will generate the new one from the title
        if (!$table->get('alias')) {
            $table->set('alias', $table->get('title'));
        }

        $table->set('alias', Prism\Utilities\StringHelper::stringUrlSafe($table->get('alias')));
    }

    /**
     * A protected method to get a set of ordering conditions.
     *
     * @param    UserideasTableItem $table
     *
     * @return    array    An array of conditions to add to add to ordering queries.
     * @since    1.6
     */
    protected function getReorderConditions($table)
    {
        $condition   = array();
        $condition[] = 'catid = ' . (int)$table->get('catid');

        return $condition;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer $pk The id of the primary key.
     *
     * @return  mixed    Object on success, false on failure.
     *
     * @since   12.2
     */
    public function getItem($pk = null)
    {
        $pk    = ($pk !== null) ? (int)$pk : (int)$this->getState($this->getName() . '.id');
        $table = $this->getTable();

        if ($pk > 0) {
            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false) {
                throw new RuntimeException(JText::_('COM_USERIDEAS_ERROR_INVALID_ITEM'));
            }
        }

        // Convert to the JObject before adding other data.
        $properties = $table->getProperties(1);
        $item       = Joomla\Utilities\ArrayHelper::toObject($properties);

        if (property_exists($item, 'params')) {
            $registry = new Joomla\Registry\Registry;
            $registry->loadString($item->params);
            $item->params = $registry->toArray();
        }

        $item->tags = new JHelperTags;
        $item->tags->getTagIds($item->id, 'com_userideas.item');

        return $item;
    }

    /**
     * Custom clean the cache of com_content and content modules
     *
     * @param   string   $group      The cache group
     * @param   integer  $client_id  The ID of the client
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function cleanCache($group = null, $client_id = 0)
    {
        parent::cleanCache('com_userideas');
        parent::cleanCache('mod_userideasitems');
    }
}
