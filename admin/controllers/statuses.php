<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die();

jimport('itprism.controller.admin');

/**
 * UserIdeas statuses controller class
 *
 * @package     UserIdeas
 * @subpackage  Components
  */
class UserIdeasControllerStatuses extends ITPrismControllerAdmin {

    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Status', $prefix = 'UserIdeasModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Method to set the status as default.
     *
     * @since   1.6
     */
    public function setDefault() {
        
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $pks = $this->input->post->get('cid', array(), 'array');
        JArrayHelper::toInteger($pks);
        
        $redirectOptions = array(
            "view" => $this->view_list,
        );
        
        // Check for errors
        if(!$pks){
            $this->displayNotice(JText::_("COM_USERIDEAS_ERROR_NO_ITEM_SELECTED"), $redirectOptions);
            return;
        }
        
        try {
        
            // Pop off the first element.
            $id     = array_shift($pks);
            
            $model  = $this->getModel();
            
            $model->setDefault($id);
        
        }catch(Exception $e){
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_SYSTEM'));
        }
        
        $this->displayMessage(JText::_('COM_USERIDEAS_STATUS_SET_DEFAULT'), $redirectOptions);
       
    }

    /**
     * Method to unset the default status.
     *
     * @since   1.6
     */
    public function unsetDefault() {
        
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        
        $pks = $this->input->post->get('cid', array(), 'array');
        JArrayHelper::toInteger($pks);
        
        $redirectOptions = array(
            "view" => $this->view_list,
        );
        
        // Check for errors
        if(!$pks){
            $this->displayNotice(JText::_("COM_USERIDEAS_ERROR_NO_ITEM_SELECTED"), $redirectOptions);
            return;
        }
        
        try {
        
            // Pop off the first element.
            $id     = array_shift($pks);
            
            $model  = $this->getModel();
            
            $model->unsetDefault($id);
        
        }catch(Exception $e){
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_SYSTEM'));
        }
        
        $this->displayMessage(JText::_('COM_USERIDEAS_STATUS_SET_NOT_DEFAULT'), $redirectOptions);
        
    }
}