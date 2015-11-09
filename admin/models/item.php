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

class UserIdeasModelItem extends JModelAdmin
{
    /**
     * The type alias for this content type (for example, 'com_content.article').
     *
     * @var      string
     * @since    3.2
     */
    public $typeAlias = 'com_userideas.item';

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
     * Method to get the record form.
     *
     * @param   array   $data     An optional array of data for the form to interrogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.item', 'item', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
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
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.item.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Save data into the DB
     *
     * @param array $data The data about item
     *
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
        $userId      = Joomla\Utilities\ArrayHelper::getValue($data, 'user_id', 0, 'int');
        $published   = Joomla\Utilities\ArrayHelper::getValue($data, 'published');
        $created     = Joomla\Utilities\ArrayHelper::getValue($data, 'record_date');
        $params      = Joomla\Utilities\ArrayHelper::getValue($data, 'params');

        // Encode parameters to JSON format.
        $params  = ($params !== null and is_array($params)) ? json_encode($params) : null;

        // Load a record from the database
        $row = $this->getTable();
        /** @var $row UserIdeasTableItem */

        $row->load($id);

        $row->set('title', $title);
        $row->set('alias', $alias);
        $row->set('description', $description);
        $row->set('status_id', $statusId);
        $row->set('catid', $catId);
        $row->set('user_id', $userId);
        $row->set('published', $published);
        $row->set('record_date', $created);
        $row->set('params', $params);

        // Set the tags.
        if (array_key_exists('tags', $data) and is_array($data['tags']) and count($data['tags']) > 0) {
            $row->set('newTags', $data['tags']);
        }
        
        $this->prepareTable($row);

        $row->store();

        return $row->get('id');
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param UserIdeasTableItem $table
     *
     * @since    1.6
     */
    protected function prepareTable($table)
    {
        // get maximum order number
        if (!$table->get('id') and !$table->get('ordering')) {

            // Set ordering to the last item if not set
            $db    = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query
                ->select('MAX(a.ordering)')
                ->from($db->quoteName('#__uideas_items', 'a'))
                ->where('a.catid = ' .(int)$table->catid);

            $db->setQuery($query, 0, 1);
            $max = (int)$db->loadResult();

            $table->set('ordering', $max + 1);
        }

        // Fix magic quotes.
        if (get_magic_quotes_gpc()) {
            $table->set('title', stripcslashes($table->get('title')));
            $table->set('description', stripcslashes($table->get('description')));
        }

        // If does not exist alias, I will generate the new one from the title
        if (!$table->get('alias')) {
            $table->set('alias', $table->get('title'));
        }
        $table->set('alias', JApplicationHelper::stringURLSafe($table->get('alias')));
    }

    /**
     * A protected method to get a set of ordering conditions.
     *
     * @param    UserIdeasTableItem $table
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
        $item       = Joomla\Utilities\ArrayHelper::toObject($properties, 'JObject');

        if (property_exists($item, 'params')) {
            $registry = new Joomla\Registry\Registry;
            $registry->loadString($item->params);
            $item->params = $registry->toArray();
        }

        $item->tags = new JHelperTags;
        $item->tags->getTagIds($item->id, 'com_userideas.item');

        return $item;
    }
}
