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

jimport('joomla.application.component.controller');

/**
 * @package		UserIdeas
 * @subpackage	Item
 * @since		2.5
 */
class UserIdeasControllerItem extends JController {
   
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
    public function getModel($name = 'Item', $prefix = '', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * This method store user vote
     */
    public function vote() {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
        $params    = $app->getParams("com_userideas");
        
        // Check for disabled payment functionality
        if($params->get("debug_item_adding_disabled", 0)) {
            $error  = JText::_("COM_USERIDEAS_ERROR_VOTING_HAS_BEEN_DISABLED");
			JLog::add($error);
			return null;
        }
        
        $requestMethod = $app->input->getMethod();
        if("POST" != $requestMethod) {
            $error  = "COM_USERIDEAS_ERROR_INVALID_REQUEST_METHOD (" .$requestMethod . "):\n";
            $error .= "INPUT: " . var_export($app->input, true) . "\n";
            JLog::add($error);
            return;
        }
        
        $userId = JFactory::getUser()->id;
        if(!$userId) {
            $response = array(
            	"success" => false,
                "title"=> JText::_( 'COM_USERIDEAS_FAIL' ),
                "text" => JText::_( 'COM_USERIDEAS_ERROR_NOT_LOG_IN' ),
            );
    
            echo json_encode($response);
            JFactory::getApplication()->close();
            
        }
        
        $data = array(
            "id"      => $app->input->post->getInt("id"),
            "user_id" => $userId
        );
        
        // Save data
        try {
            
            // Events
            $dispatcher	       = JDispatcher::getInstance();
            
            // Execute event onBeforeVote
            JPluginHelper::importPlugin('userideas');
            $results     = $dispatcher->trigger('onBeforeVote', array('com_userideas.beforevote', &$data, $params));
            
            // Check for error.
            foreach($results as $result) {
                $success = JArrayHelper::getValue($result, "success");
                
                if(false === $success) {
                    
                    $message = JArrayHelper::getValue($result, "message", JText::_('COM_USERIDEAS_VOTED_UNSUCCESSFULY'));
                    
                    $response = array(
                    	"success" => false,
                        "title"   => JText::_('COM_USERIDEAS_FAIL'),
                        "text"    => $message
                    );
            
                    echo json_encode($response);
                    JFactory::getApplication()->close();
                }
            }
            
            // Execute event onVote
            $dispatcher->trigger('onVote', array('com_userideas.vote', &$data, $params));
            
            // Execute event onAfterVote
            $dispatcher->trigger('onAfterVote', array('com_userideas.aftervote', &$data, $params));
        		
            $model = $this->getModel();
            $item  = $model->getItem($data["id"]);
            
            // Send email to administrator
            if($params->get("security_send_mail_admin")) {
                $model->sendMailToAdministrator($item);
            }
                
            // Send email to user
            if($params->get("security_send_mail_user")) {
                $model->sendMailToUser($item);
            }
            
        } catch (Exception $e) {
            JLog::add($e->getMessage());
            return;
        }
        
        $responseData = JArrayHelper::getValue($data, "response_data", 0);
        $userVotes    = JArrayHelper::getValue($responseData, "user_votes", 0);
        $votes        = JArrayHelper::getValue($responseData, "votes", 0);
        
        $response = array(
        	"success" => true,
            "title"   => JText::_('COM_USERIDEAS_SUCCESS'),
            "text"    => JText::plural('COM_USERIDEAS_VOTED_SUCCESSFULY',  $userVotes),
            "data"    => array(
                "votes" => $votes
            )
        );

        echo json_encode($response);
        JFactory::getApplication()->close();
        
    }
    
}
