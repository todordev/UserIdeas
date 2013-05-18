<?php
/**
 * @package      ITPrism Components
 * @subpackage   UserIdeas
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * UserIdeas is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

class UserIdeasModelDetails extends JModelItem {
    
    protected $item = null;
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   type    The table type to instantiate
     * @param   string  A prefix for the table class name. Optional.
     * @param   array   Configuration array for model. Optional.
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
        /** @var $app JSite **/
        
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
	 * @param	integer	The id of the object to get.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($id = null) {
	    
	    if(!$id) {
	        $id = $this->getState($this->getName() . '.id');
	    }
	    
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
		    ->select(
		    	"a.id, a.title, a.description, a.votes, " .
		        "a.record_date, a.published, a.user_id, a.catid, " .
		        $query->concatenate(array("a.id", "a.alias"), "-") . " AS slug, " .
		        "b.name" 
		    )
		    ->from($db->quoteName("#__uideas_items") . " AS a")
		    ->innerJoin($db->quoteName("#__users") . " AS b")
		    ->where("a.id = " .(int)$id);
		    
		$db->setQuery($query);
		
		$this->item = $db->loadObject();    
//	    var_dump($this->item);exit;
		return $this->item;
	}
}