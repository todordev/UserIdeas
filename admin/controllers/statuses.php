<?php
/**
 * @package      Userideas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die();

/**
 * Userideas statuses controller class
 *
 * @package     Userideas
 * @subpackage  Components
 */
class UserideasControllerStatuses extends Prism\Controller\Admin
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param string $name
     * @param string $prefix
     * @param array  $config
     *
     * @return UserideasModelStatus
     */
    public function getModel($name = 'Status', $prefix = 'UserideasModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Method to set the status as default.
     *
     * @since   1.6
     */
    public function setDefault()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $pks = $this->input->post->get('cid', array(), 'array');
        $pks = Joomla\Utilities\ArrayHelper::toInteger($pks);

        $redirectOptions = array(
            'view' => $this->view_list,
        );

        // Check for errors
        if (!$pks) {
            $this->displayNotice(JText::_('COM_USERIDEAS_ERROR_NO_ITEM_SELECTED'), $redirectOptions);
            return;
        }

        try {
            // Pop off the first element.
            $id = array_shift($pks);

            $model = $this->getModel();

            $model->setDefault($id);

        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, $this->option);
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_USERIDEAS_STATUS_SET_DEFAULT'), $redirectOptions);

    }

    /**
     * Method to unset the default status.
     *
     * @since   1.6
     */
    public function unsetDefault()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $pks = $this->input->post->get('cid', array(), 'array');
        $pks = Joomla\Utilities\ArrayHelper::toInteger($pks);

        $redirectOptions = array(
            'view' => $this->view_list,
        );

        // Check for errors
        if (!$pks) {
            $this->displayNotice(JText::_('COM_USERIDEAS_ERROR_NO_ITEM_SELECTED'), $redirectOptions);
            return;
        }

        try {
            // Pop off the first element.
            $id = array_shift($pks);

            $model = $this->getModel();

            $model->unsetDefault($id);

        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, $this->option);
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_USERIDEAS_STATUS_SET_NOT_DEFAULT'), $redirectOptions);
    }
}
