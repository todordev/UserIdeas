<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
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
 * UserIdeas comment controller
 *
 * @package     ITPrism Components
 * @subpackage  UserIdeas
  */
class UserIdeasControllerComment extends ITPrismControllerFormFrontend {
    
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
    public function getModel($name = 'Comment', $prefix = 'UserIdeasModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }
    
    public function save() {
        
        // Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
 
		// Check for valid user id
        $userId = JFactory::getUser()->id;
        if(!$userId) {
            $redirectOptions = array(
    		    "force_direction" => "index.php?option=com_users&view=login"
    		);
            $this->displayNotice(JText::_('COM_USERIDEAS_ERROR_NOT_LOG_IN'), $redirectOptions);
            return;
        }
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
		// Get the data from the form POST
		$data    = $app->input->post->get('jform', array(), 'array');
        $itemId  = JArrayHelper::getValue($data, "item_id");
        
        // Prepare response data
        $redirectOptions = array(
            "view" => "details",
            "id"   => $itemId,
        );
        
        $model   = $this->getModel();
        /** @var $model UserIdeasModelComment **/
        
        $form    = $model->getForm($data, false);
        /** @var $form JForm **/
        
        if(!$form){
            throw new Exception($model->getError(), 500);
        }
            
        // Test if the data is valid.
        $validData = $model->validate($form, $data);
        
        // Check for validation errors.
        if($validData === false){
            
            $redirectOptions = array(
                "view" => "details",
                "id"   => $itemId
            );
            
            $this->displayNotice($form->getErrors(), $redirectOptions);
            return;
        }
        
        try {

            $model->save($validData);

            jimport("userideas.item");
            $item = UserIdeasItem::getInstance($itemId);
            
        } catch (Exception $e){
            
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_SYSTEM'), ITPrismErrors::CODE_ERROR);
            
        }
        
        $redirectOptions = array(
            "force_direction" => UserIdeasHelperRoute::getDetailsRoute($item->getSlug(), $item->getCatSlug())
        );
        // Redirect to next page
        $this->displayMessage(JText::_("COM_USERIDEAS_COMMENT_SENT_SUCCESSFULLY"), $redirectOptions);
			
    }
    
}