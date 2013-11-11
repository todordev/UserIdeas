<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;
class UserIdeasTableItem extends JTable {
    
    protected $category_name;
    protected $slug;
    protected $catslug;
    
    public function __construct($db) {
        parent::__construct('#__uideas_items', 'id', $db);
    }

    public function load($keys = null, $reset = true) {
        
        parent::load($keys, $reset);
        
        if(!empty($this->id)) {

            $db    = $this->getDbo();
            $query = $db->getQuery(true);
            
            $query
                ->select(
                    "b.title AS category_name, " . 
                    $query->concatenate(array("a.id", "a.alias"), "-") . " AS slug, " .
                    $query->concatenate(array("b.id", "b.alias"), "-") . " AS catslug" 
                )
                ->from($db->quoteName("#__uideas_items", "a"))
                ->leftJoin($db->quoteName("#__categories", "b") . " ON a.catid = b.id")
                ->where("a.id = ". (int)$this->id);
            
            $db->setQuery($query);
            $result = $db->loadAssoc();
            
            $this->bind($result);
        }
        
    }
    
    public function getSlug(){
        return $this->slug;
    }
    
    public function getCatSlug(){
        return $this->catslug;
    }
    
    public function getCategoryName(){
        return $this->category_name;
    }
    
}