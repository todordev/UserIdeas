<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

class UserIdeasModelForm extends JModelForm
{
    protected $item = null;

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
        $value = $app->input->getInt("id", 0);
        $this->setState($this->getName() . '.id', $value);

        // Get category ID
        $value = $app->getUserState($this->option . ".items.catid");
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
            if (!empty($itemId)) {
                $userId = JFactory::getUser()->get("id");
                $data   = $this->getItem($itemId, $userId);
            }

            if (empty($data)) {
                $data = array(
                    "caitid" => $this->getState('category_id')
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
                "id"      => $pk,
                "user_id" => $userId
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
        $id          = JArrayHelper::getValue($data, "id", 0, "int");
        $title       = JArrayHelper::getValue($data, "title");
        $description = JArrayHelper::getValue($data, "description");
        $categoryId  = JArrayHelper::getValue($data, "catid", 0, "int");
        $userId      = JArrayHelper::getValue($data, "user_id", 0, "int");

        $keys = array(
            "id"      => $id,
            "user_id" => $userId
        );

        // Load a record from the database
        $row = $this->getTable();
        /** @var $row UserIdeasTableItem */

        $row->load($keys);

        // If there is an id, the item is not new
        $isNew = true;
        if (!empty($row->id)) {
            $isNew = false;
        }

        $row->set("title", $title);
        $row->set("description", $description);
        $row->set("catid", $categoryId);

        if ($isNew) {

            $recordDate = new JDate();
            $row->set("record_date", $recordDate->toSql());
            $row->set("user_id", $userId);

            // Set status
            jimport("userideas.statuses");
            $statuses      = UserIdeasStatuses::getInstance(JFactory::getDbo());
            $defaultStatus = $statuses->getDefault();

            if (!empty($defaultStatus->id)) {
                $row->set("status_id", (int)$defaultStatus->id);
            }

            // Auto publishing
            $params    = JComponentHelper::getParams($this->option);
            /** @var  $params Joomla\Registry\Registry */

            $published = $params->get("security_item_auto_publish", 0);
            $row->set("published", $published);
        }

        $this->prepareTable($row);

        $row->store();

        $this->triggerAfterSaveEvent($row, $isNew);

        return $row->get("id");
    }

    protected function triggerAfterSaveEvent($row, $isNew)
    {
        // Trigger the event

        $context = $this->option . '.' . $this->getName();

        // Include the content plugins.
        $dispatcher = JEventDispatcher::getInstance();
        JPluginHelper::importPlugin('content');

        // Trigger the onContentAfterSave event.
        $results = $dispatcher->trigger("onContentAfterSave", array($context, &$row, $isNew));
        if (in_array(false, $results, true)) {
            throw new Exception(JText::_("COM_USERIDEAS_ERROR_DURING_ITEM_POSTING_PROCESS"));
        }
    }

    /**
     * Prepare and sanitise the table prior to saving.
     * @since    1.6
     */
    protected function prepareTable(&$table)
    {
        // get maximum order number
        if (empty($table->id)) {

            // Set ordering to the last item if not set
            if (empty($table->ordering)) {
                $db    = $this->getDbo();
                $query = $db->getQuery(true);
                $query
                    ->select("MAX(a.ordering)")
                    ->from($db->quoteName("#__uideas_items", "a"));

                $db->setQuery($query, 0, 1);
                $max = $db->loadResult();

                $table->ordering = $max + 1;
            }
        }

        // Fix magic quotes
        if (get_magic_quotes_gpc()) {
            $table->alias       = stripcslashes($table->title);
            $table->description = stripcslashes($table->description);
        }

        // If does not exist alias, I will generate the new one from the title
        if (!$table->alias) {
            $table->alias = $table->title;
        }
        $table->alias = JApplicationHelper::stringURLSafe($table->alias);
    }
}
