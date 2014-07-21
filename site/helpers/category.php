<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;
jimport('joomla.application.categories');

class UserIdeasCategories extends JCategories
{
    public function __construct($options = array())
    {
        $options['table']     = '#__uideas_items';
        $options['extension'] = 'com_userideas';
        parent::__construct($options);
    }
}
