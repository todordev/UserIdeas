<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package      UserIdeas
 * @subpackage   Component
 * @since        1.6
 */
class JFormFieldUiStatuses extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var     string
     * @since   1.6
     */
    protected $type = 'UiStatuses';

    /**
     * Method to get the field options.
     *
     * @return  array   The field option objects.
     * @since   1.6
     */
    protected function getOptions()
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('a.id AS value, a.name AS text')
            ->from($db->quoteName("#__uideas_statuses", "a"))
            ->order("a.name ASC");

        // Get the options.
        $db->setQuery($query);

        $options = $db->loadAssocList();

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
