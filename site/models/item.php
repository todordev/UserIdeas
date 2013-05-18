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

jimport('joomla.application.component.modelitem');

class UserIdeasModelItem extends JModelItem {
    
    protected $item;
    
	/**
	 * Method to get an ojbect.
	 *
	 * @param	integer	The id of the object to get.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($id = null) {
	    
		if ($this->item === null) {
		    
			if (empty($id)) {
				$id = $this->getState('item.id');
			}

			// Get a level row instance.
			$table = JTable::getInstance('Item', 'UserIdeasTable');

			// Attempt to load the row.
			if ($table->load($id)) {
			    
			    if (!$table->published) {
					return $this->item;
				}

				// Convert the JTable to a clean JObject.
				$properties = $table->getProperties(true);
				$this->item = JArrayHelper::toObject($properties, 'JObject');
			}
		}

		return $this->item;
	}
    
	/**
     * Send mail to administrator and notify him about new item.
     * 
     * @param array  $data		
     */
    public function sendMailToAdministrator($item) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
         // Send email to the administrator
        $subject = JText::sprintf("COM_USERIDEAS_MAIL_ADMIN_SUBJECT", $app->getCfg("sitename"));
        $body    = JText::sprintf("COM_USERIDEAS_MAIL_ADMIN_BODY", $item->title);
        $return  = JFactory::getMailer()->sendMail($app->getCfg("mailfrom"), $app->getCfg("fromname"), $app->getCfg("mailfrom"), $subject, $body);
		
		// Check for an error.
		if ($return !== true) {
		    $error = JText::sprintf("COM_USERIDEAS_ERROR_MAIL_SENDING_ADMIN");
			JLog::add($error);
		}
        
    }
    
	/**
     * Send mail to user
     * 
     * @param array  $data
     */
    public function sendMailToUser($item) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/
        
         // Send email to the administrator
        $subject = JText::sprintf("COM_USERIDEAS_NEW_POST_USER_SUBJECT", $app->getCfg("sitename"));
        $body    = JText::sprintf("COM_USERIDEAS_NEW_POST_USER_BODY", $item->title, $app->getCfg("sitename") );
        $return  = JFactory::getMailer()->sendMail($app->getCfg("mailfrom"), $app->getCfg("fromname"), $app->getCfg("mailfrom"), $subject, $body);
		
		// Check for an error.
		if ($return !== true) {
		    $error = JText::_("COM_USERIDEAS_ERROR_MAIL_SENDING_ADMIN");
			JLog::add($error);
		}
        
    }
    
}