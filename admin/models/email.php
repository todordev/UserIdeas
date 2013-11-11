<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class UserIdeasModelEmail extends JModelAdmin {
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   type    The table type to instantiate
     * @param   string  A prefix for the table class name. Optional.
     * @param   array   Configuration array for model. Optional.
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Email', $prefix = 'UserIdeasTable', $config = array()){
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
        $form = $this->loadForm($this->option.'.email', 'email', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState($this->option.'.edit.email.data', array());
        if(empty($data)){
            $data = $this->getItem();
        }
        return $data;
    }
    
    /**
     * Save data into the DB
     * @param $data   The data of item
     * @return     	  Item ID
     */
    public function save($data){
        
        $id          = JArrayHelper::getValue($data, "id");
        $subject     = JArrayHelper::getValue($data, "subject");
        $senderName  = JArrayHelper::getValue($data, "sender_name");
        $senderEmail = JArrayHelper::getValue($data, "sender_email");
        $body        = JArrayHelper::getValue($data, "body");
        
        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);
        
        $row->set("subject",        $subject);
        $row->set("sender_name",    $senderName);
        $row->set("sender_email",   $senderEmail);
        $row->set("body",           $body);
        
        $this->prepareTable($row);
        
        $row->store(true);
        
        return $row->id;
    
    }
    
    /**
     * Prepare and sanitise the table prior to saving.
     * @since	1.6
     */
    protected function prepareTable(&$table) {
         
        // Fix magic qutoes
        if(get_magic_quotes_gpc()) {
            $table->subject = stripcslashes($table->subject);
            $table->body    = stripcslashes($table->body);
        }
        
        if(empty($table->sender_name)) {
            $table->sender_name = null;
        }
        
        if(empty($table->sender_email)) {
            $table->sender_email = null;
        }
    
    }
    
    
    
}