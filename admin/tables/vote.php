<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;
class UserIdeasTableVote extends JTable {
    
	public function __construct(JDatabaseDriver $db) {
        parent::__construct('#__uideas_votes', 'id', $db);
    }
    
}