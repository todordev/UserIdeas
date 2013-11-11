<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('itprism.controller.admin');

/**
 * UserIdeas items controller class.
 *
 * @package		UserIdeas
 * @subpackage	Component
 * @since		1.6
 */
class UserIdeasControllerItems extends JControllerAdmin {
    
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Item', $prefix = 'UserIdeasModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    /**
     * Method to save the submitted ordering values for records via AJAX.
     * @return  void
     * @since   3.0
     */
    public function saveOrderAjax() {
         
        // Get the input
        $app     = JFactory::getApplication();
        $pks     = $app->input->post->get('cid', array(), 'array');
        $order   = $app->input->post->get('order', array(), 'array');
    
        // Sanitize the input
        JArrayHelper::toInteger($pks);
        JArrayHelper::toInteger($order);
    
        // Get the model
        $model = $this->getModel();
    
        try {
            
            $model->saveorder($pks, $order);
        
        } catch ( Exception $e ) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_SYSTEM'));
        }
    
        $response = array(
            "success" => true,
            "title"=> JText::_( 'COM_USERIDEAS_SUCCESS' ),
            "text" => JText::_( 'JLIB_APPLICATION_SUCCESS_ORDERING_SAVED' ),
            "data" => array()
        );
    
        echo json_encode($response);
        JFactory::getApplication()->close();
    
    }
    
}