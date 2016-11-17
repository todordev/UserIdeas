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

/**
 * Userideas form controller
 *
 * @package     Userideas
 * @subpackage  Component
 */
class UserideasControllerForm extends Prism\Controller\Form\Frontend
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return    UserideasModelForm    The model.
     * @since    1.5
     */
    public function getModel($name = 'Form', $prefix = 'UserideasModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get the data from the form POST
        $data   = $this->input->post->get('jform', array(), 'array');
        $itemId = Joomla\Utilities\ArrayHelper::getValue($data, 'id', 0, 'int');

        $redirectOptions = array(
            'view' => 'form',
            'id'   => $itemId
        );

        // Check for valid user
        $user   = JFactory::getUser();
        $userId = $user->get('id');

        if (!$this->allowSave($data)) {
            $redirectOptions = array(
                'force_direction' => JRoute::_('index.php?option=com_users&view=login', false)
            );
            $this->displayNotice(JText::_('COM_USERIDEAS_ERROR_NO_PERMISSIONS_TO_DO_ACTION'), $redirectOptions);
            return;
        }

        // Get params
        $params = JComponentHelper::getParams('com_userideas');

        $model = $this->getModel();
        /** @var $model UserideasModelForm */

        $form = $model->getForm($data, false);
        /** @var $form JForm */

        if (!$form) {
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_FORM_CANNOT_BE_LOADED'));
        }

        // Test if the data is valid.
        $validData = $model->validate($form, $data);

        // Check for validation errors.
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);
            return;
        }

        try {
            // Upload image
            $file = $this->input->files->get('jform', array(), 'array');
            $file = Joomla\Utilities\ArrayHelper::getValue($file, 'attachment');

            if (!empty($file['name'])) {
                $attachment = $model->uploadFile($file);
                if ($attachment !== null) {
                    $validData['attachment'] = $attachment;
                }
            }

            $validData['user_id']  = (int)$userId;
            $itemId                = $model->save($validData);
            $redirectOptions['id'] = $itemId;
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_userideas');
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_SYSTEM'));

        }

        $redirectOptions = array(
            'force_direction' => (!$userId or (strcmp('list', $params->get('redirect_when_post')) === 0)) ?
                    UserideasHelperRoute::getItemsRoute() :
                    UserideasHelperRoute::getFormRoute($itemId)
        );

        // Redirect to next page
        $this->displayMessage(JText::_('COM_USERIDEAS_ITEM_SAVED_SUCCESSFULLY'), $redirectOptions);
    }

    /**
     * Method override to check if you can add a new record.
     *
     * @param   array $data An array of input data.
     *
     * @return  boolean
     *
     * @since   1.6
     */
    protected function allowAdd($data = array())
    {
        $user  = JFactory::getUser();

        if ($user->authorise('core.create', 'com_userideas')) {
            return true;
        }

        return false;
    }

    /**
     * Method override to check if you can edit an existing record.
     *
     * @param   array  $data An array of input data.
     * @param   string $key  The name of the key for the primary key; default is id.
     *
     * @return  boolean
     *
     * @since   1.6
     */
    protected function allowEdit($data = array(), $key = 'id')
    {
        $user     = JFactory::getUser();

        if ($user->authorise('core.edit', 'com_userideas')) {
            return true;
        }

        // Validate item owner.
        if ($user->authorise('core.edit.own', 'com_userideas')) {
            $itemId = Joomla\Utilities\ArrayHelper::getValue($data, $key);
            $userId = $user->get('id');

            // Validate item owner.
            $itemValidator = new Userideas\Validator\Item\Owner(JFactory::getDbo(), $itemId, $userId);
            if ($itemValidator->isValid()) {
                return true;
            }
        }

        return false;
    }
}
