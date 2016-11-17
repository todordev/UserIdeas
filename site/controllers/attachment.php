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
class UserideasControllerAttachment extends Prism\Controller\DefaultController
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return    UserideasModelAttachment    The model.
     * @since    1.5
     */
    public function getModel($name = 'Attachment', $prefix = 'UserideasModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
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

    /**
     * Method override to check if you can edit an existing record.
     *
     * @param   array  $data An array of input data.
     *
     * @return  boolean
     *
     * @since   1.6
     */
    protected function allowEditComment($data = array())
    {
        $user     = JFactory::getUser();

        if ($user->authorise('userideas.comment.edit', 'com_userideas')) {
            return true;
        }

        // Validate item owner.
        if ($user->authorise('userideas.comment.edit.own', 'com_userideas')) {
            $commentId = Joomla\Utilities\ArrayHelper::getValue($data, 'id');
            $userId    = $user->get('id');

            // Validate item owner.
            $itemValidator = new Userideas\Validator\Comment\Owner(JFactory::getDbo(), $commentId, $userId);
            if ($itemValidator->isValid()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Delete file.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function removeFile()
    {
        // Check for request forgeries.
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        $redirectOptions = array(
            'force_direction' => 'index.php?option=com_users&view=login'
        );

        $user   = JFactory::getUser();
        $userId = $user->get('id');
        if (!$userId) {
            $this->displayNotice(JText::_('COM_USERIDEAS_ERROR_NO_PERMISSIONS_TO_DO_ACTION'), $redirectOptions);
            return;
        }

        $id         = $this->input->getInt('id');
        $attachment = new \Userideas\Attachment\Attachment(JFactory::getDbo());
        $attachment->load($id);
        if (!$attachment->getId()) {
            $this->displayNotice(JText::_('COM_USERIDEAS_ERROR_NO_PERMISSIONS_TO_DO_ACTION'), $redirectOptions);
            return;
        }

        $source = $attachment->getSource();
        if (strcmp($source, 'item') === 0) {
            if (!$this->allowEdit(['id' => $attachment->getItemId()])) {
                $this->displayNotice(JText::_('COM_USERIDEAS_ERROR_NO_PERMISSIONS_TO_DO_ACTION'), $redirectOptions);
                return;
            }
        } elseif (strcmp($source, 'comment') === 0) {
            if (!$this->allowEditComment(['id' => $attachment->getCommentId()])) {
                $this->displayNotice(JText::_('COM_USERIDEAS_ERROR_NO_PERMISSIONS_TO_DO_ACTION'), $redirectOptions);
                return;
            }
        } else {
            $this->displayNotice(JText::_('COM_USERIDEAS_ERROR_NO_PERMISSIONS_TO_DO_ACTION'), $redirectOptions);
            return;
        }

        // Prepare redirect URL.
        $returnUrl = $this->input->get->getBase64('return');
        $returnUrl = base64_decode($returnUrl);
        $redirectOptions = array(
            'force_direction' => $returnUrl
        );

        try {
            $model = $this->getModel();
            $model->removeFile($attachment);
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_userideas');
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_USERIDEAS_FILE_DELETED'), $redirectOptions);
    }
}
