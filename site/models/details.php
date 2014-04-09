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

jimport('joomla.application.component.modelitem');

class UserIdeasModelDetails extends JModelItem {
    
    protected $item = null;
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string  $type    The table type to instantiate
     * @param   string  $prefix A prefix for the table class name. Optional.
     * @param   array   $config Configuration array for model. Optional.
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Item', $prefix = 'UserIdeasTable', $config = array()){
        return JTable::getInstance($type, $prefix, $config);
    }
    
	/**
     * Method to auto-populate the model state.
     * Note. Calling getState in this method will result in recursion.
     * @since	1.6
     */
    protected function populateState() {
        
        parent::populateState();
        
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite **/
        
		// Get the pk of the record from the request.
		$value = $app->input->getInt("id");
		$this->setState($this->getName() . '.id', $value);

		// Load the parameters.
		$value = JComponentHelper::getParams($this->option);
		$this->setState('params', $value);
		
    }
    
	/**
	 * Method to get an ojbect.
	 *
	 * @param	integer	$id The id of the object to get.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($id = null) {
	    
	    if(!$id) {
	        $id = $this->getState($this->getName() . '.id');
	    }
	    
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select(
            'a.id, a.title, a.description, a.votes, a.record_date, a.catid, a.user_id, a.status_id, ' . 
            $query->concatenate(array("a.id", "a.alias"), "-") . " AS slug, " . 
            'b.name, ' . 'c.title AS category, ' . 
            $query->concatenate(array("c.id", "c.alias"), "-") . " AS catslug, " . 
            'd.name AS status_name, d.params AS status_params, d.default AS status_default'
        );
        
        $query->from($db->quoteName('#__uideas_items', "a"));
        $query->innerJoin($db->quoteName('#__users', "b") .' ON a.user_id = b.id');
        $query->leftJoin($db->quoteName('#__categories', "c") .' ON a.catid = c.id');
        $query->leftJoin($db->quoteName('#__uideas_statuses', "d") .' ON a.status_id = d.id');
		$query->where("a.id = " .(int)$id);
		    
		$db->setQuery($query);
		
		$this->item = $db->loadObject();

        // Prepare status object
        if(!empty($this->item)) {
            $this->prepareStatus($this->item);
        }
		return $this->item;
	}

    protected function prepareStatus(&$item) {

        if(!empty($item->status_params)) {
            $statusParams = json_decode($item->status_params, true);

            if(!empty($statusParams)) {
                $item->status_params = $statusParams;
            } else {
                $item->status_params = null;
            }
        }

        $statusData = array(
            "id"        => $item->status_id,
            "name"      => $item->status_name,
            "default"   => $item->status_default,
            "params"    => $item->status_params
        );

        jimport("userideas.status");
        $item->status = new UserIdeasStatus();
        $item->status->bind($statusData);

    }

    /**
     * Increase number of hits.
     *
     * @param integer $id
     */
    public function hit($id) {

        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        $query
            ->update($db->quoteName("#__uideas_items"))
            ->set($db->quoteName("hits") ." = hits + 1")
            ->where($db->quoteName("id") ."=". (int)$id);

        $db->setQuery($query);
        $db->execute();

    }
}