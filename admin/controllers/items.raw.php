<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

/**
 * UserIdeas items controller class.
 *
 * @package        UserIdeas
 * @subpackage     Component
 * @since          1.6
 */
class UserIdeasControllerItems extends JControllerAdmin
{
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Item', $prefix = 'UserIdeasModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    /**
     * Method to save the submitted ordering values for records via AJAX.
     *
     * @return  void
     * @throws Exception
     *
     * @since   3.0
     */
    public function saveOrderAjax()
    {
        jimport("itprism.response.json");
        $response = new ITPrismResponseJson();

        // Get the input
        $pks   = $this->input->post->get('cid', array(), 'array');
        $order = $this->input->post->get('order', array(), 'array');

        // Sanitize the input
        JArrayHelper::toInteger($pks);
        JArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        try {

            $model->saveorder($pks, $order);

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_SYSTEM'));
        }

        $response
            ->setTitle(JText::_('COM_USERIDEAS_SUCCESS'))
            ->setText(JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'))
            ->success();

        echo $response;
        JFactory::getApplication()->close();

    }
}
