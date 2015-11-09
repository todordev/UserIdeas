<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class UserIdeasModelForm extends JModelForm
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
    public function getTable($type = 'Item', $prefix = 'UserIdeasTable', $config = array())
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
        $this->setState($this->getName() . '.id', $value);

        // Get category ID
        $value = $app->getUserState($this->option . '.items.catid');
        $this->setState('category_id', $value);

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
        if (empty($form)) {
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

            $itemId = (int)$this->getState($this->getName() . '.id');
            if ($itemId > 0) {
                $userId = JFactory::getUser()->get('id');
                $data   = $this->getItem($itemId, $userId);
            }

            if (!$data) {
                $data = array(
                    'caitid' => $this->getState('category_id')
                );
            }

        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer $pk     The id of the primary key.
     * @param   integer $userId The user Id
     *
     * @return  object
     * @since   11.1
     */
    public function getItem($pk, $userId)
    {
        if ($this->item) {
            return $this->item;
        }

        // Initialise variables.
        $table = $this->getTable();

        if ($pk > 0 and $userId > 0) {

            $keys = array(
                'id'      => $pk,
                'user_id' => $userId
            );

            // Attempt to load the row.
            $table->load($keys);

            // Convert to the JObject before adding other data.
            $this->item = $table->getProperties();

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
        /** @var $row UserIdeasTableItem */

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
            $params    = JComponentHelper::getParams($this->option);
            /** @var  $params Joomla\Registry\Registry */

            $published = $params->get('security_item_auto_publish', 0);
            $row->set('published', $published);
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
     * @param UserIdeasTableItem $table
     * @since    1.6
     */
    protected function prepareTable(&$table)
    {
        // get maximum order number
        if (!$table->get('id')) {

            // Set ordering to the last item if not set
            if (empty($table->ordering)) {
                $db    = $this->getDbo();
                $query = $db->getQuery(true);
                $query
                    ->select('MAX(a.ordering)')
                    ->from($db->quoteName('#__uideas_items', 'a'));

                $db->setQuery($query, 0, 1);
                $max = $db->loadResult();

                $table->set('ordering', $max + 1);
            }
        }

        // Fix magic quotes
        if (get_magic_quotes_gpc()) {
            $table->set('alias', stripcslashes($table->title));
            $table->set('description', stripcslashes($table->description));
        }

        // If does not exist alias, I will generate the new one from the title
        if (!$table->get('alias')) {
            $table->set('alias', $table->get('title'));
        }
        $table->set('alias', JApplicationHelper::stringURLSafe($table->get('alias')));
    }

    /**
     * Method to test whether a record can be created or edited.
     *
     * @param   int  $itemId  Item ID/
     * @param   int  $userId  User ID.
     *
     * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
     *
     * @since   12.2
     */
    public function canEditOwn($itemId, $userId)
    {
        $user = JFactory::getUser();

        if (!$user->authorise('core.edit.own', 'com_userideas')) {
            return false;
        }

        // Validate item owner.
        $itemValidator = new Userideas\Validator\Item\Owner(JFactory::getDbo(), $itemId, $userId);
        if (!$itemValidator->isValid()) {
            return false;
        }

        return true;
    }
}
