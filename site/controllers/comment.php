<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * UserIdeas comment controller
 *
 * @package     ITPrism Components
 * @subpackage  UserIdeas
 */
class UserIdeasControllerComment extends Prism\Controller\Form\Frontend
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return    UserIdeasModelComment    The model.
     * @since    1.5
     */
    public function getModel($name = 'Comment', $prefix = 'UserIdeasModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Get the data from the form POST
        $data   = $app->input->post->get('jform', array(), 'array');
        $itemId = Joomla\Utilities\ArrayHelper::getValue($data, 'item_id');

        // Prepare response data
        $redirectOptions = array(
            'view' => 'details',
            'id'   => $itemId,
        );

        // Check for valid user id
        if (!$this->allowSave($data)) {
            $redirectOptions = array(
                'force_direction' => 'index.php?option=com_users&view=login'
            );
            $this->displayNotice(JText::_('COM_USERIDEAS_ERROR_NO_PERMISSIONS_TO_DO_ACTION'), $redirectOptions);

            return;
        }

        $model = $this->getModel();
        /** @var $model UserIdeasModelComment */

        $form = $model->getForm($data, false);
        /** @var $form JForm */

        if (!$form) {
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_FORM_CANNOT_BE_LOADED'), 500);
        }

        // Test if the data is valid.
        $validData = $model->validate($form, $data);

        // Check for validation errors.
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);

            return;
        }

        try {

            $model->save($validData);

            $item = new UserIdeas\Item\Item(JFactory::getDbo());
            $item->load($itemId);

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_SYSTEM'));
        }

        $redirectOptions = array(
            'force_direction' => UserIdeasHelperRoute::getDetailsRoute($item->getSlug(), $item->getCategorySlug())
        );

        // Redirect to next page
        $this->displayMessage(JText::_('COM_USERIDEAS_COMMENT_SENT_SUCCESSFULLY'), $redirectOptions);

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

        if ($user->authorise('userideas.comment.create', 'com_userideas')) {
            return true;
        }

        return false;
    }

    /**
     * Method override to check if you can edit an existing record.
     *
     * @param   array  $data An array of input data.
     * @param   string $key  The name of the key for the primary key; default is comment_id.
     *
     * @return  boolean
     *
     * @since   1.6
     */
    protected function allowEdit($data = array(), $key = 'id')
    {
        $user     = JFactory::getUser();

        // Validate action role.
        if (!$user->authorise('userideas.comment.edit.own', 'com_userideas')) {
            return false;
        }

        // Validate item owner.
        $itemId = Joomla\Utilities\ArrayHelper::getValue($data, $key);
        $userId = $user->get('id');

        // Validate item owner.
        $itemValidator = new UserIdeas\Validator\Comment\Owner(JFactory::getDbo(), $itemId, $userId);
        if (!$itemValidator->isValid()) {
            return false;
        }

        return true;
    }
}
