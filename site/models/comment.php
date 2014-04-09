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

jimport('joomla.application.component.modelform');

class UserIdeasModelComment extends JModelForm {
    
    protected $item = null;
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string  $type The table type to instantiate
     * @param   string  $prefix A prefix for the table class name. Optional.
     * @param   array   $config Configuration array for model. Optional.
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Comment', $prefix = 'UserIdeasTable', $config = array()){
        return JTable::getInstance($type, $prefix, $config);
    }
    
	/**
     * Method to auto-populate the model state.
     * Note. Calling getState in this method will result in recursion.
     * @since	1.6
     */
    protected function populateState() {
        
        parent::populateState();
        
        $app = JFactory::getApplication("Site");
        /** @var $app JApplicationSite **/
        
		// Get the pk of the record from the request.
		$value = $app->input->getInt("id");
		$this->setState('item_id', $value);
		
		$value = $app->input->getInt("comment_id");
		$this->setState('comment_id', $value);

		// Load the parameters.
		$value = JComponentHelper::getParams($this->option);
		$this->setState('params', $value);
		
    }
    
    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML and then an event is fired
     * for users plugins to extend the form with extra fields.
     *
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {
        // Get the form.
        $form = $this->loadForm($this->option.'.comment', 'comment', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        
        return $form;
    }
    
    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite **/
        
		$data	    = $app->getUserState($this->option.'.edit.comment.data', array());
		if(!$data) {
		    
		    $commentId = (int)$this->getState("comment_id");
		    $userId    = JFactory::getUser()->id;
		    
		    // Get comment data
		    $data      = $this->getItem($commentId, $userId);
		    
		    $itemId    = $this->getState("item_id");
		    if(empty($data->item_id)) {
		        $data->item_id = $itemId;
		    }
		}

		return $data;
    }
    
	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $commentId The id of the primary key.
	 * @param   integer  $userId    The user Id
	 *
     * @return  object
     *
     * @throw   Exception
	 * @since   11.1
	 */
	public function getItem($commentId, $userId) {
	    
	    if(!is_null($this->item)) {
	        return $this->item;
	    }
	    
		// Initialise variables.
		$table = $this->getTable();

		if ($commentId > 0 AND $userId > 0) {
		    
		    $keys = array(
		    	"id"        => $commentId, 
		    	"user_id"   => $userId
		    );
		    
			// Attempt to load the row.
			$table->load($keys);

		}

		// Convert to the JObject before adding other data.
		$properties = $table->getProperties();
		$this->item = JArrayHelper::toObject($properties, 'JObject');

		return $this->item;
	}
	
    /**
     * Method to save the form data.
     *
     * @param	array		$data The form data.
     *
     * @return  integer
     * @since	1.6
     */
    public function save($data) {
        
        $id          = JArrayHelper::getValue($data, "id");
        $comment     = JArrayHelper::getValue($data, "comment");
        $itemId      = JArrayHelper::getValue($data, "item_id");
        $userId      = JFactory::getUser()->id;

        $isNew       = false;

        $keys = array(
	    	"id"      => $id, 
	    	"user_id" => $userId
	    );
	    
        // Load a record from the database
        $row = $this->getTable();
        $row->load($keys);
        
        $row->set("comment", $comment);

        // If there is no userId we are adding a new comment
        if(!$row->get("user_id")) {

            $isNew = true;

            $row->set("record_date",   null);
            $row->set("item_id",       $itemId);
            $row->set("user_id",       $userId);
            
            $params    = JComponentHelper::getParams($this->option);
            $published = ( !$params->get("security_comment_auto_publish", 0) ) ? 0 : 1;
            
            $row->set("published", $published);

        }
        
        $row->store(true);

        $this->triggerAfterSaveEvent($row, $isNew);

        return $row->get("id");

    }

    protected function triggerAfterSaveEvent($row, $isNew) {

        // Trigger the event

        $context = $this->option.'.'.$this->getName();

        // Include the content plugins.
        $dispatcher = JEventDispatcher::getInstance();
        JPluginHelper::importPlugin('content');

        // Trigger the onCommentAfterSave event.
        $results    = $dispatcher->trigger("onCommentAfterSave", array($context, &$row, $isNew));
        if (in_array(false, $results, true)) {
            throw new Exception(JText::_("COM_USERIDEAS_ERROR_DURING_ITEM_POSTING_COMMENT"));
        }

    }

}