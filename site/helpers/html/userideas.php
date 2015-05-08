<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * UserIdeas Html Helper
 *
 * @package        ITPrism Components
 * @subpackage     UserIdeas
 * @since          1.6
 */
abstract class JHtmlUserIdeas
{
    /**
     * Generate a link to an user image of a social platform.
     *
     * @param object  $socialProfiles Social profiles object.
     * @param integer $userId         User ID
     * @param array   $options        Options that will be used to integration.
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
    public static function avatar($socialProfiles, $userId, $options = array())
    {
        $avatarSize = JArrayHelper::getValue($options, "size", 50);
        $default = JArrayHelper::getValue($options, "default");

        $link = (!$socialProfiles) ? null : $socialProfiles->getAvatar($userId, $avatarSize);

        // Set the link to default picture
        if (!$link and !empty($default)) {
            $link = $default;
        }

        return $link;
    }

    /**
     * Generate a link to an user profile of a social platform.
     *
     * @param object  $socialProfiles Social profiles object.
     * @param integer $userId         User ID
     * @param string  $default        A link to default profile.
     *
     * @return string
     *
     *
     * <code>
     * $avatar  = JHtml::_("userideas.profile", $socialProfiles, $userId, "javascript: void(0);");
     *
     * </code>
     */
    public static function profile($socialProfiles, $userId, $default = null)
    {
        $link = (!$socialProfiles) ? null : $socialProfiles->getLink($userId);

        // Set the link to default picture
        if (!$link and !empty($default)) {
            $link = $default;
        }

        return $link;
    }

    public static function publishedBy($name, $link = null)
    {
        if (!empty($link)) {
            $profile = '<a href="' . $link . '" rel="nofollow">' . htmlspecialchars($name, ENT_QUOTES, "utf-8") . '</a>';
        } else {
            $profile = ($name) ?: JText::_("COM_USERIDEAS_ANONYMOUS");
        }

        $html = JText::sprintf("COM_USERIDEAS_PUBLISHED_BY", $profile);

        return $html;
    }

    public static function publishedByOn($name, $date, $link = null, $profileAvatar = null, $options = array())
    {
        $name = htmlspecialchars($name, ENT_QUOTES, "utf-8");

        if (!empty($link)) {
            $profile = '<a href="' . $link . '" rel="nofollow">' . $name . '</a>';
        } else {
            $profile = ($name) ?: JText::_("COM_USERIDEAS_ANONYMOUS");
        }

        if (!empty($profileAvatar)) {
            $width = JArrayHelper::getValue($options, "width", 24);
            $height = JArrayHelper::getValue($options, "height", 24);

            $profile = '<img src="' . $profileAvatar . '" width="'.$width.'" height="'.$height.'" alt="'.$name.'" /> ' . $profile;
        }

        $date = JHTML::_('date', $date, JText::_('DATE_FORMAT_LC3'));
        $html = JText::sprintf("COM_USERIDEAS_PUBLISHED_BY_ON", $profile, $date);

        return $html;
    }

    public static function publishedOn($date)
    {
        $date = JHTML::_('date', $date, JText::_('DATE_FORMAT_LC3'));
        $html = JText::sprintf("COM_USERIDEAS_PUBLISHED_ON", $date);

        return $html;
    }

    public static function category($name, $catSlug = "")
    {
        if (!$name) {
            return "";
        }

        if (!empty($catSlug)) {
            $html = '<a href="' . UserIdeasHelperRoute::getCategoryRoute($catSlug) . '" class="ui-category-label">' . htmlspecialchars($name, ENT_QUOTES, "utf-8") . '</a>';
        } else {
            $html = '<span class="ui-category-label">' . htmlspecialchars($name, ENT_QUOTES, "utf-8") . '</span>';
        }

        return $html;
    }

    public static function status(UserIdeasStatus $status, $displayLink = true)
    {
        if (!$status->getId()) {
            return "";
        }

        $styles = $status->getParam("style_class", "");

        if (!empty($displayLink)) {
            $html = '
            <a href="' . UserIdeasHelperRoute::getItemsRoute($status->getId()) . '" class="ui-status-label">
            <span class="label' . $styles . '">' . htmlspecialchars($status->getName(), ENT_QUOTES, "utf-8") . '</span>
            </a>';
        } else {
            $html = '<span class="label ui-status-label ' . $styles . '">' . htmlspecialchars($status->getName(), ENT_QUOTES, "utf-8") . '</span>';
        }

        return $html;
    }

    public static function styleClass($name)
    {
        if (!$name) {
            return "---";
        }

        return '<div class="label '.$name.'">'.$name.'</div>';
    }
}
