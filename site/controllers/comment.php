<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('itprism.controller.form.frontend');

/**
 * UserIdeas comment controller
 *
 * @package     ITPrism Components
 * @subpackage  UserIdeas
 */
class UserIdeasControllerComment extends ITPrismControllerFormFrontend
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return    object    The model.
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

        // Check for valid user id
        $userId = JFactory::getUser()->get("id");
        if (!$userId) {
            $redirectOptions = array(
                "force_direction" => "index.php?option=com_users&view=login"
            );
            $this->displayNotice(JText::_('COM_USERIDEAS_ERROR_NOT_LOG_IN'), $redirectOptions);

            return;
        }

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Get the data from the form POST
        $data   = $app->input->post->get('jform', array(), 'array');
        $itemId = JArrayHelper::getValue($data, "item_id");

        // Prepare response data
        $redirectOptions = array(
            "view" => "details",
            "id"   => $itemId,
        );

        $model = $this->getModel();
        /** @var $model UserIdeasModelComment */

        $form = $model->getForm($data, false);
        /** @var $form JForm * */

        if (!$form) {
            throw new Exception(JText::_("COM_USERIDEAS_ERROR_FORM_CANNOT_BE_LOADED"), 500);
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

            jimport("userideas.item");
            $item = new UserIdeasItem(JFactory::getDbo());
            $item->load($itemId);

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_SYSTEM'));
        }

        $redirectOptions = array(
            "force_direction" => UserIdeasHelperRoute::getDetailsRoute($item->getSlug(), $item->getCategorySlug())
        );

        // Redirect to next page
        $this->displayMessage(JText::_("COM_USERIDEAS_COMMENT_SENT_SUCCESSFULLY"), $redirectOptions);

    }
}
