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

/**
 * UserIdeas Html Helper
 *
 * @package		ITPrism Components
 * @subpackage	UserIdeas
 * @since		1.6
 */
abstract class JHtmlUserIdeas {
    
    /**
	 * @var   array   array containing information for loaded files
	 */
	protected static $loaded = array();
	
    public static function pnotify() {
        
        // Only load once
		if (!empty(self::$loaded[__METHOD__])) {
			return;
		}

		JHtml::_('stylesheet', 'media/com_userideas/css/jquery.pnotify.default.css', array(), false, false, false, false);
		JHtml::_('script', 'media/com_userideas/js/jquery.pnotify.min.js', false, false, false, false, false);
		
		self::$loaded[__METHOD__] = true;

		return;
		
    }
	
    public static function helper() {
        
        // Only load once
		if (!empty(self::$loaded[__METHOD__])) {
			return;
		}

		JHtml::_('script', 'media/com_userideas/js/helper.js', false, false, false, false, false);
		self::$loaded[__METHOD__] = true;

		return;
		
    }
    
    /**
     * 
     * Generate a link to an user image of a social platform
     * @param string $socialPlatform	The name of the social platform
     * @param string $defaultAvatar		A link to default picture
     * 
     * @todo Add option for setting image size
     */    
    public static function avatar($socialPlatform, JUser $user, $default = null) {
        
        $link = "";

        switch($socialPlatform) {

            case "com_socialcommunity":
                
                jimport("itprism.integrate.profile.socialcommunity");
                $profile = new ITPrismIntegrateProfileSocialCommunity($user);
                $link    = $profile->getAvatar();
                break;
                
            case "com_kunena":
                
                jimport("itprism.integrate.profile.kunena");
                $profile = new ITPrismIntegrateProfileKunena($user);
                $link    = $profile->getAvatar();
                
                break;
                
            case "gravatar":
                
                jimport("itprism.integrate.profile.gravatar");
                $profile = new ITPrismIntegrateProfileGravatar($user);
                $profile->setSize(50);
                $link    = $profile->getAvatar();
                
                break;
            
            default:
                $link = "";
                break;
        }
        
        // Set the linke to default picture
        if(!$link AND !empty($default)) {
            $link = $default;
        }
        
		return $link;
		
    }
    
	/**
     * 
     * Generate a link to an user image of a social platform
     * @param string $socialPlatform	The name of the social platform
     * @param string $defaultAvatar		A link to default picture
     * 
     * @todo Add option for setting image size
     */    
    public static function profile($socialPlatform, JUser $user, $default = null) {
        
        $link = "";

        switch($socialPlatform) {

            case "com_socialcommunity":
                
                jimport("itprism.integrate.profile.socialcommunity");
                $profile = new ITPrismIntegrateProfileSocialCommunity($user);
                $link    = $profile->getLink();
                
                break;
                
            case "com_kunena":
                
                jimport("itprism.integrate.profile.kunena");
                $profile = new ITPrismIntegrateProfileKunena($user);
                $link    = $profile->getLink();
                
                break;
                
            case "gravatar":
                
                jimport("itprism.integrate.profile.gravatar");
                $profile = new ITPrismIntegrateProfileGravatar($user);
                $link    = $profile->getLink();
                
                break;
            
            default:
                $link = "";
                break;
        }
        
        // Set the linke to default picture
        if(!$link AND !empty($default)) {
            $link = $default;
        }
        
		return $link;
		
    }
    
}
