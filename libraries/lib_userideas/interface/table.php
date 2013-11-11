<?php
/**
 * @package		 UserIdeas
 * @subpackage	 Libraries
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * An interface that is used from classes, 
 * which are based on Data Access Object Pattern.
 *
 */
interface UserIdeasInterfaceTable {
    
    public function load($keys, $reset = true);
    public function bind($data, $ignore = array());
    public function store($updateNulls = false);
    
}