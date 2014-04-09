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

class UserIdeasModelItem extends JModelAdmin {
    
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
     * @param   string  $type   The table type to instantiate
     * @param   string  $prefix A prefix for the table class name. Optional.
     * @param   array   $config Configuration array for model. Optional.
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Item', $prefix = 'UserIdeasTable', $config = array()){
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
        $form = $this->loadForm($this->option.'.item', 'item', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState($this->option.'.edit.item.data', array());
        
        if(empty($data)){
            $data = $this->getItem();
        }
        
        return $data;
    }
    
    /**
     * Save data into the DB
     * 
     * @param array $data   The data about item
     * @return  int
     */
    public function save($data){
        
        $id           = JArrayHelper::getValue($data, "id");
        $title        = JArrayHelper::getValue($data, "title");
        $alias        = JArrayHelper::getValue($data, "alias");
        $description  = JArrayHelper::getValue($data, "description");
        $statusId     = JArrayHelper::getValue($data, "status_id");
        $catId        = JArrayHelper::getValue($data, "catid");
        $userId       = JArrayHelper::getValue($data, "user_id");
        $published    = JArrayHelper::getValue($data, "published");
        
        if(!$userId) {
            $userId = JFactory::getUser()->get("id");
        }
        
        // Load a record from the database
        $row = $this->getTable();
        /** @var $row UserIdeasTableItem */

        $row->load($id);
        
        $row->set("title",        $title);
        $row->set("alias",        $alias);
        $row->set("description",  $description);
        $row->set("status_id",    $statusId);
        $row->set("catid",        $catId);
        $row->set("user_id",      $userId);
        $row->set("published",    $published);
        
        $this->prepareTable($row);
        
        $row->store();
        
        return $row->get("id");
    
    }
    
	/**
	 * Prepare and sanitise the table prior to saving.
     *
     * @param UserIdeasTableItem $table
	 * @since	1.6
	 */
	protected function prepareTable($table) {
	    
        // get maximum order number
		if (!$table->get("id")) {

			// Set ordering to the last item if not set
			if (!$table->get("ordering")) {
				$db     = JFactory::getDbo();
				$query  = $db->getQuery(true);
				$query
				    ->select("MAX(ordering)")
				    ->from("#__uideas_items");
				
			    $db->setQuery($query, 0, 1);
				$max   = $db->loadResult();

				$table->set("ordering", $max + 1);
			}
		}
		
	    // Fix magic quotes.
	    if( get_magic_quotes_gpc() ) {
            $table->set("title", stripcslashes($table->get("title")) );
            $table->set("description", stripcslashes($table->get("description")) );
        }
        
		// If does not exist alias, I will generate the new one from the title
        if(!$table->get("alias")) {
            $table->set("alias", $table->get("title"));
        }
        $table->set("alias", JApplicationHelper::stringURLSafe($table->get("alias")) );
        
	}
	
	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	UserIdeasTableItem	$table
	 *
	 * @return	array	An array of conditions to add to add to ordering queries.
	 * @since	1.6
	 */
	protected function getReorderConditions($table) {
	    $condition   = array();
	    $condition[] = 'catid = ' .(int)$table->get("catid");
	    return $condition;
	}
}