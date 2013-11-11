<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
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
     * Generate a link to an user image of a social platform.
     * 
     * @param object $socialProfiles	Social profiles object.
     * @param integer $user		        User ID
     * @param string $default		    A link to default picture.
     * @param array $options		    Options that will be used to integration. 
     * 
     * @return string
     * 
     * <code>
     * 
     * $options = array(
     *      "avatar_size" => 50
     * );
     * $avatar  = JHtml::_("userideas.avatar", $socialProfiles, $userId, "media/com_userideas/images/no-profile.png", $options);
     * 
     * </code>
     * 
     */    
    public static function avatar($socialProfiles, $userId, $default = null, $options = array()) {
        
        $avatarSize = JArrayHelper::getValue($options, "avatar_size", 50);
        
        $link = (!$socialProfiles) ? null : $socialProfiles->getAvatar($userId, $avatarSize);
        
        // Set the linke to default picture
        if(!$link AND !empty($default)) {
            $link = $default;
        }
        
		return $link;
		
    }
    
	/**
     * Generate a link to an user image of a social platform
     * 
     * @param object  $socialProfiles	Social profiles object.
     * @param integer $user		        User ID
     * @param string  $default		    A link to default profile.
     * 
     * @return string 
     * 
     * 
     * <code>
     * 
     * $options = array(
     *      "avatar_size" => 50
     * );
     * $avatar  = JHtml::_("userideas.profile", $socialProfiles, $userId, "javascript: void(0);");
     * 
     * </code>
     */ 
    public static function profile($socialProfiles, $userId, $default = null) {
        
        $link     =  (!$socialProfiles) ? null : $socialProfiles->getLink($userId);
        
        // Set the linke to default picture
        if(!$link AND !empty($default)) {
            $link = $default;
        }
        
		return $link;
		
    }
    
    public static function publishedBy($name, $date, $link = null) {
    
        if(!empty($link)) {
            $profile = '<a href="'.$link.'" rel="nofollow">'.htmlspecialchars($name, ENT_QUOTES, "utf-8").'</a>';
        } else {
            $profile = $name;
        }
    
        $date = JHTML::_('date', $date, JText::_('DATE_FORMAT_LC3'));
        $html = JText::sprintf("COM_USERIDEAS_PUBLISHED_BY_ON", $profile, $date);
         
        return $html;
    }
    
    public static function category($name, $catSlug = "") {
    
        if(!$name) { return ""; }
        
        if(!empty($catSlug)) {
            $html = '<a href="'.UserIdeasHelperRoute::getCategoryRoute($catSlug).'" class="ui-category-label">'.htmlspecialchars($name, ENT_QUOTES, "utf-8").'</a>';
        } else {
            $html = '<span class="ui-category-label">'.htmlspecialchars($name, ENT_QUOTES, "utf-8").'</span>';
        }
    
        return $html;
    }
    
    public static function status($name, $statusId = 0) {
    
        if(!$name) { return ""; }
        
        if(!empty($statusId)) {
            $html = '<a href="'.UserIdeasHelperRoute::getItemsRoute($statusId).'" class="ui-status-label"><span class="label">'.htmlspecialchars($name, ENT_QUOTES, "utf-8").'</span></a>';
        } else {
            $html = '<span class="label ui-status-label">'.htmlspecialchars($name, ENT_QUOTES, "utf-8").'</span>';
        }
    
        return $html;
    }
    
}
