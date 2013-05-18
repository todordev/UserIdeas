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
defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.modellist' );

/**
 * Get a list of items
 */
class UserIdeasModelItems extends JModelList {
    
	 /**
     * Constructor.
     *
     * @param   array   An optional associative array of configuration settings.
     * @see     JController
     * @since   1.6
     */
    public function  __construct($config = array()) {
        
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
            	'record_date', 'a.record_date'
            );
        }

        parent::__construct($config);
		
    }
    
    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since   1.6
     */
    protected function populateState($ordering = null, $direction = null) {
        
        // List state information.
        parent::populateState('a.record_date', 'asc');
        
        $app       = JFactory::getApplication();
        /** @var $app JSite **/

        $value = $app->getUserStateFromRequest($this->option.".items.catid", "id", 0, "int");
        $this->setState($this->getName().'.id', $value);
        
        // Load the component parameters.
        $params = $app->getParams($this->option);
        $this->setState('params', $params);

        // Ordering
        $value = $params->get("items_ordering", 0);
        $this->prepareOrderingState($value);
        
        // Pagination
        $value = $params->get("items_display_results_number", 0);
        if(!$value) {
            $value = $app->input->getInt('limit', $app->getCfg('list_limit', 0));
        }
        $this->setState('list.limit', $value);
        
        $value = $app->input->getInt('limitstart', 0);
        $this->setState('list.start', $value);
        
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string      $id A prefix for the store id.
     * @return  string      A store id.
     * @since   1.6
     */
    protected function getStoreId($id = '') {
        
        // Compile the store id.
        $id .= ':' . $this->getState($this->getName().'.id');

        return parent::getStoreId($id);
    }
    
   /**
     * Build an SQL query to load the list data.
     *
     * @return  JDatabaseQuery
     * @since   1.6
     */
    protected function getListQuery() {
        
        // Create a new query object.
        $db     = $this->getDbo();
        /** @var $db JDatabaseMySQLi **/
        $query  = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.title, a.description, a.votes, a.record_date, a.catid, a.user_id, ' .
                $query->concatenate(array("a.id", "a.alias"), "-") . " AS slug, " .
                'b.name'
            )
        );
        $query->from($db->quoteName('#__uideas_items') .' AS a');
        $query->innerJoin($db->quoteName('#__users') .' AS b ON a.user_id = b.id');

        // Category filter
        $categoryId = $this->getState($this->getName().".id");
        $query->where('a.catid = '. (int)$categoryId);
        $query->where('a.published = 1');
        
        // Add the list ordering clause.
        $orderString = $this->getOrderString();
        $query->order($db->escape($orderString));

        return $query;
    }
    
	/**
     * 
     * Prepare a string used for ordering results
     * @param integer $filterOrdering
     */
    protected function prepareOrderingState($filterOrdering) {
        
        $listOrder = 'ASC';
        
        switch($filterOrdering) {
            case 1:
                $orderCol  = "a.title";
                break;

            case 2:
                $orderCol  = "a.record_date";
                $listOrder = "DESC";
                break;

            default:
                $orderCol = "a.ordering";
                break;
        }
        
        // Set the column using for ordering
        $this->setState('list.ordering', $orderCol);
        
        // Set the type of ordering
        if(!in_array(strtoupper($listOrder), array('ASC', 'DESC'))){
            $listOrder = 'ASC';
        }
        $this->setState('list.direction', $listOrder);
        
    }
    
    protected function getOrderString() {
        
        $orderCol   = $this->getState('list.ordering');
        $orderDirn  = $this->getState('list.direction');
        
        return $orderCol.' '.$orderDirn;
    }
    
    public function getComments() {
        
        $db     = $this->getDbo();
        /** @var $db JDatabaseMySQLi **/
        
        $query  = $db->getQuery(true);

        $query
            ->select("a.item_id, COUNT(*) AS number")
            ->from($db->quoteName('#__uideas_comments') .' AS a')
            ->group("a.item_id");

        $db->setQuery($query);
        $results = $db->loadAssocList("item_id", "number");
         
        return $results;
    }
    
}