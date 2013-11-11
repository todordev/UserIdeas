<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * UserIdeas is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

class UserIdeasModelItem extends JModelItem {
    
    protected $item;
    
    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since   1.6
     */
    protected function populateState($ordering = null, $direction = null) {
    
        $app       = JFactory::getApplication();
        /** @var $app JSite **/
    
        $value = $app->input->getInt("id");
        $this->setState($this->getName().'.id', $value);
        
        // Load the component parameters.
        $params = $app->getParams($this->option);
        $this->setState('params', $params);
    
    }
    
	/**
	 * Method to get an ojbect.
	 *
	 * @param	integer	The id of the object to get.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($id = null) {
	    
		if ($this->item === null) {
		    
			if (empty($id)) {
				$id = $this->getState($this->getName().'.id');
			}

			// Get a level row instance.
			$table = JTable::getInstance('Item', 'UserIdeasTable');

			// Attempt to load the row.
			if ($table->load($id)) {
			    
			    if (!$table->published) {
					return $this->item;
				}

				// Convert the JTable to a clean JObject.
				$properties = $table->getProperties(true);
				$this->item = JArrayHelper::toObject($properties, 'JObject');
			}
		}

		return $this->item;
	}
    
}