<?php
/**
 * @package      Userideas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class UserideasModelComment extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type   The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Comment', $prefix = 'UserideasTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array   $data     An optional array of data for the form to interogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.comment', 'comment', array('control' => 'jform', 'load_data' => $loadData));
        if (!$form) {
            return false;
        }

        $canDo  = JHelperContent::getActions('com_userideas');

        $id     = (int)$this->getState('comment.id');

        // Check for existing article.
        // Modify the form based on Edit State access controls.
        if (($id !== 0 and !$canDo->get('core.edit')) or ($id === 0 and !$canDo->get('core.edit.state'))) {
            // Disable fields for display.
            $form->setFieldAttribute('comment', 'disabled', 'true');
            $form->setFieldAttribute('published', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is an article you can edit.
            $form->setFieldAttribute('comment', 'filter', 'unset');
            $form->setFieldAttribute('published', 'filter', 'unset');
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed   The data for the form.
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.comment.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Save data into the DB.
     *
     * @param array $data
     *
     * @return  int
     */
    public function save($data)
    {
        $id        = Joomla\Utilities\ArrayHelper::getValue($data, 'id');
        $comment   = Joomla\Utilities\ArrayHelper::getValue($data, 'comment');
        $published = Joomla\Utilities\ArrayHelper::getValue($data, 'published');

        // Load a record from the database
        $row = $this->getTable();
        /** @var $row UserideasTableComment */

        $row->load($id);

        $row->set('comment', $comment);
        $row->set('published', $published);

        $row->store();

        return $row->get('id');
    }
}
