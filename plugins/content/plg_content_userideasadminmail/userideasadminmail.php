<?php
/**
 * @package		 UserIdeas
 * @subpackage	 Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * This plugin send notification mails to the administrator. 
 *
 * @package		UserIdeas
 * @subpackage	Plugins
 */
class plgContentUserIdeasAdminMail extends JPlugin {
    
    /**
     * This method is executed when someone create an item.
     * 
     * @param string                      $context
     * @param UserIdeasTableItem          $row
     * @param boolean                     $isNew
     * @return void|boolean
     */
    public function onContentAfterSave($context, $row, $isNew) {
        
        $app = JFactory::getApplication();
        /** @var $app JSite **/

        if($app->isAdmin()) {
            return;
        }

        if(strcmp("com_userideas.form", $context) != 0){
            return;
        }
        
        $sendWhenPostEmailId = $this->params->get("send_when_post_email_id", 0);
        
        // Check for enabled option for sending mail 
        // when user create a item.
        if(!empty($sendWhenPostEmailId)) {
            
            if($isNew AND !empty($row->id)) {
                
                $this->loadLanguage();

                $app      = JFactory::getApplication();
                
                jimport("userideas.email");
                $email    = new UserIdeasEmail($sendWhenPostEmailId);
                
                // Set sender name
                if(!$email->getSenderName()) {
                    $email->setSenderName($app->getCfg("fromname"));
                }
                
                $fromMail = (!$email->getSenderEmail()) ? $app->getCfg("mailfrom") : $email->getSenderEmail(); 
                $fromName = (!$email->getSenderName()) ? $app->getCfg("fromname") : $email->getSenderName(); 
                
                $recipientMail = $fromMail;
                
                $uri     = JUri::getInstance();
                $website = $uri->toString(array("scheme", "host"));
                
                $data = array(
                    "site_name"         => $app->getCfg("sitename"),
                    "site_url"          => JUri::root(),
                    "item_title"        => $row->title,
                    "item_url"          => $website.JRoute::_(UserIdeasHelperRoute::getDetailsRoute($row->getSlug(), $row->getSlug())),
                    "sender_name"       => $fromName,
                    "sender_email"      => $fromMail,
                    "recipient_name"    => $fromName,
                );
                
                $emailMode   = $this->params->get("email_mode", "plain");
                
                // Parse data
                $email->parse($data);
                $subject    = $email->getSubject();
                $body       = $email->getBody($emailMode);

                $mailer  = JFactory::getMailer();
                if(strcmp("html", $emailMode) == 0) { // Send as HTML message
                    
                    $return  = $mailer->sendMail($fromMail, $fromName, $recipientMail, $subject, $body, UserIdeasEmail::MAIL_MODE_HTML);
                
                } else { // Send as plain text.
                    
                    $return  = $mailer->sendMail($fromMail, $fromName, $recipientMail, $subject, $body, UserIdeasEmail::MAIL_MODE_PLAIN);
                    
                }
                
                // Check for an error.
                if ($return !== true) {
                    $error = JText::_("PLG_CONTENT_USERIDEASADMINMAIL_ERROR_MAIL_SENDING_USER");
                    JLog::add($error);
                    return false;
                }
                
            }
            
        }
        
        return true;
        
    }
    
    
}