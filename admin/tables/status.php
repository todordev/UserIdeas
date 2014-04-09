<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;
class UserIdeasTableStatus extends JTable {

    public function __construct(JDatabaseDriver $db) {
        parent::__construct('#__uideas_statuses', 'id', $db);
    }
    
}