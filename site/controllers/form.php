<?php
/**
 * @package      ITPrism Components
 * @subpackage   UserIdeas
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * UserIdeas is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('itprism.controller.form.frontend');

/**
 * UserIdeas form controller
 *
 * @package     ITPrism Components
 * @subpackage  UserIdeas
  */
class UserIdeasControllerForm extends ITPrismControllerFormFrontend {
    
    protected $defaultLink = "index.php?option=com_userideas";
    
	/**
     * Method to get a model object, loading it if required.
     *
     * @param	string	$name	The model name. Optional.
     * @param	string	$prefix	The class prefix. Optional.
     * @param	array	$config	Configuration array for model. Optional.
     *
     * @return	object	The model.
     * @since	1.5
     */
    public function getModel($name = 'Form', $prefix = 'UserIdeasModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    public function save() {
        
        // Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
 
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
		// Get the data from the form POST
		$data    = $app->input->post->get('jform', array(), 'array');
        $itemId  = JArrayHelper::getValue($data, "id", 0, "int");
        $catId   = JArrayHelper::getValue($data, "catid", 0, "int");
        
        $redirectOptions = array(
		    "view"    => "form",
            "id"      => $itemId
		);
		
		// Check for valid user
        $userId = JFactory::getUser()->id;
        if(!$userId) {
            
            $redirectOptions = array(
    		    "force_direction" => "login_form"
    		);
    		
            $this->displayNotice(JText::_('COM_USERIDEAS_ERROR_NOT_LOG_IN'), $redirectOptions);
            return;
        }
        
        // Check for valid owner of the item
        if(!empty($itemId)) {
            
            $db   = JFactory::getDbo();
            
            jimport("userideas.item");
            $item = new UserIdeasItem($db);
            $item->load($itemId);
            
            if(!$item->isValid($itemId, $userId)) {
                
                $redirectOptions = array(
        		    "view" => "items"
        		);
        		
                $this->displayNotice(JText::_('COM_USERIDEAS_ERROR_INVALID_ITEM'), $redirectOptions);
                return;
            
            }
            
        }
        
        $model   = $this->getModel();
        /** @var $model UserIdeasModelForm **/
        
        $form    = $model->getForm($data, false);
        /** @var $form JForm **/
        
        if(!$form){
            throw new Exception($model->getError(), 500);
        }
            
        // Test if the data is valid.
        $validData = $model->validate($form, $data);
        
        // Check for validation errors.
        if($validData === false){
            $this->displayNotice($form->getErrors(), $redirectOptions);
            return;
        }
        
        try {
            
            $validData["user_id"]  = $userId;
            $itemId = $model->save($validData);
            
            $redirectOptions["id"] = $itemId;
            
        } catch(Exception $e){
            
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_SYSTEM'), ITPrismErrors::CODE_ERROR);
            
        }
        
        // Redirect to next page
        $this->displayMessage(JText::_('COM_USERIDEAS_ITEM_SAVED_SUCCESSFULY'), $redirectOptions);
			
    }
    
}