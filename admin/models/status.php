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

jimport('joomla.application.component.modeladmin');

class UserIdeasModelStatus extends JModelAdmin {
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string  $type    The table type to instantiate
     * @param   string  $prefix  A prefix for the table class name. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Status', $prefix = 'UserIdeasTable', $config = array()){
        return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
     * Method to get the record form.
     *
     * @param   array   $data       An optional array of data for the form to interogate.
     * @param   boolean $loadData   True if the form is to load its own data (default case), false if not.
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true){
        
        // Get the form.
        $form = $this->loadForm($this->option.'.status', 'status', array('control' => 'jform', 'load_data' => $loadData));
        if(empty($form)){
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
    protected function loadFormData(){
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState($this->option.'.edit.status.data', array());
        if(empty($data)){
            $data = $this->getItem();
        }
        return $data;
    }
    
    /**
     * Save data into the DB.
     *
     * @param array $data   The data of item
     * @return  int
     */
    public function save($data){
        
        $id        = JArrayHelper::getValue($data, "id");
        $name      = JArrayHelper::getValue($data, "name");
        $default   = JArrayHelper::getValue($data, "default");
        $params    = JArrayHelper::getValue($data, "params");

        // Encode parameters to JSON format.
        if(!empty($params)) {
            $params = json_encode($params);
        } else {
            $params = null;
        }

        // Load a record from the database
        $row = $this->getTable();
        /** @var $row UserIdeasTableStatus */

        $row->load($id);
        
        $row->set("name",    $name);
        $row->set("default", $default);
        $row->set("params",  $params);

        $this->prepareTable($row);
        
        $row->store(true);
        
        // Set the item as default.
        if(!$row->get("default")) {
            $this->setDefault($row->get("id"));
        }
        
        return $row->get("id");
    
    }
    
    /**
     * Prepare and sanitise the table prior to saving.
     * @since	1.6
     */
    protected function prepareTable($table) {
         
        // Fix magic quotes.
        if(get_magic_quotes_gpc()) {
            $table->set("name", stripcslashes($table->get("name")));
        }
    
    }
    
    /**
     * Method to set a status default.
     *
     * @param   integer  $id The primary key ID for the style.
     *
     * @return  boolean  True if successful.
     * @throws	Exception
     */
    public function setDefault($id) {
        
        $status  = $this->getTable();
        /** @var $status UserIdeasTableStatus */

        $status->load($id);
        
        if (!$status->get("id")) {
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_INVALID_ITEM'));
        }
    
        $db		= $this->getDbo();
        
        // Reset the default fields.
        $query  = $db->getQuery(true);
        $query
            ->update($db->quoteName("#__uideas_statuses"))
            ->set($db->quoteName("default") ." = 0");
        
        $db->setQuery($query);
        $db->execute();
        
        // Set the item as default
        $status->set("default", 1);
        $status->store();
        
    }
    
    /**
     * Method to unset default status.
     *
     * @param   integer  $id The primary key ID for the style.
     *
     * @return  boolean  True if successful.
     * @throws	Exception
     */
    public function unsetDefault($id) {
    
        $status  = $this->getTable();
        /** @var $status UserIdeasTableStatus */

        $status->load($id);
    
        if (!$status->get("id")) {
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_INVALID_ITEM'));
        }
    
        // Set the item as default
        $status->set("default", 0);
        $status->store();
    
    }
    
}