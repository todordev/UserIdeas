<?php
/**
 * @package      Userideas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Userideas Html Helper
 *
 * @package        ITPrism Components
 * @subpackage     Userideas
 * @since          1.6
 */
abstract class JHtmlUserideas
{
    /**
     * @var   array   array containing information for loaded files
     */
    protected static $loaded = array();

    /**
     * Generate a link to an user image of a social platform.
     *
     * <code>
     * $options = array(
     *      "size" => "small",
     *      "default" => "media/com_userideas/images/no-profile.png"
     * );
     *
     * $avatar  = JHtml::_("userideas.avatar", $socialProfiles, $userId, $options);
     * </code>
     *
     * @param stdClass  $socialProfiles Social profiles object.
     * @param integer $userId         User ID
     * @param array   $options        Options that will be used to integration.
     *
     * @return string
     */
    public static function avatar($socialProfiles, $userId, $options = array())
    {
        $avatarSize = Joomla\Utilities\ArrayHelper::getValue($options, 'size', 'small');
        $default    = Joomla\Utilities\ArrayHelper::getValue($options, 'default');

        $link = (!$socialProfiles) ? null : $socialProfiles->getAvatar($userId, $avatarSize);

        // Set the link to default picture
        if (!$link and $default !== null) {
            $link = $default;
        }

        return $link;
    }

    /**
     * Generate a link to an user profile of a social platform.
     *
     * <code>
     * $avatar  = JHtml::_("userideas.profile", $socialProfiles, $userId, "javascript: void(0);");
     * </code>
     *
     * @param stdClass  $socialProfiles Social profiles object.
     * @param integer $userId         User ID
     * @param string  $default        A link to default profile.
     *
     * @return string
     */
    public static function profile($socialProfiles, $userId, $default = null)
    {
        $link = (!$socialProfiles) ? null : $socialProfiles->getLink($userId);

        // Set the link to default picture
        if (!$link and $default !== null) {
            $link = $default;
        }

        return $link;
    }

    public static function publishedBy($name, $link = null, $profileAvatar = null, array $options = array())
    {
        $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

        if ($link !== null and $link !== '') {
            $profile = '<a href="' . $link . '" rel="nofollow">' . $name . '</a>';
        } else {
            $profile = $name ?: JText::_('COM_USERIDEAS_ANONYMOUS');
        }

        if ($profileAvatar !== null and $profileAvatar !== '') {
            $width = Joomla\Utilities\ArrayHelper::getValue($options, 'width', 24);
            $height = Joomla\Utilities\ArrayHelper::getValue($options, 'height', 24);

            $profile = '<img src="' . $profileAvatar . '" width="'.$width.'" height="'.$height.'" alt="'.$name.'" /> ' . $profile;
        }

        return JText::sprintf('COM_USERIDEAS_PUBLISHED_BY', $profile);
    }

    public static function publishedByOn($name, $date, $link = null, $profileAvatar = null, array $options = array())
    {
        $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $link = (string)$link;

        if ($link !== '' and $name !== '') {
            $profile = '<a href="' . $link . '" rel="nofollow">' . $name . '</a>';
        } else {
            $profile = $name ?: JText::_('COM_USERIDEAS_ANONYMOUS');
        }

        if ($profileAvatar !== null and $profileAvatar !== '') {
            $width = Joomla\Utilities\ArrayHelper::getValue($options, 'width', 24);
            $height = Joomla\Utilities\ArrayHelper::getValue($options, 'height', 24);

            $profile = '<img src="' . $profileAvatar . '" width="'.$width.'" height="'.$height.'" alt="'.$name.'" /> ' . $profile;
        }

        $date = JHtml::_('date', $date, JText::_('DATE_FORMAT_LC3'));

        return JText::sprintf('COM_USERIDEAS_PUBLISHED_BY_ON', $profile, $date);
    }

    public static function publishedOn($date)
    {
        $date = JHtml::_('date', $date, JText::_('DATE_FORMAT_LC3'));

        return JText::sprintf('COM_USERIDEAS_PUBLISHED_ON', $date);
    }

    public static function category($name, $catSlug = '')
    {
        if (!$name) {
            return '';
        }

        if ((string)$catSlug !== '') {
            $html = '<a href="' . UserideasHelperRoute::getCategoryRoute($catSlug) . '" class="ui-category-label"><span class="fa fa-folder"></span> ' . htmlspecialchars($name, ENT_QUOTES, 'utf-8') . '</a>';
        } else {
            $html = '<span class="ui-category-label"><span class="fa fa-folder"></span> ' . htmlspecialchars($name, ENT_QUOTES, 'utf-8') . '</span>';
        }

        return $html;
    }

    public static function hits($number)
    {
        return '<span class="label label-default"><span class="fa fa-eye"></span> ' . JText::sprintf('COM_USERIDEAS_HITS_D', $number) . '</span>';
    }

    public static function votes($number)
    {
        return '<span class="label label-default"><span class="fa fa-thumbs-up"></span> ' . JText::sprintf('COM_USERIDEAS_VOTES_D', $number) . '</span>';
    }

    public static function categoryFilter($category, $categoryId)
    {
        $html = '--';

        if ($category !== '' and $categoryId > 0) {
            $html = JText::sprintf('COM_USERIDEAS_CATEGORY_S', '<a href="' . JRoute::_('index.php?option=com_userideas&view=items&filter_category='.(int)$categoryId). '">' . $category . '</a>');
        }

        return $html;
    }

    public static function commentsNumber($item)
    {
        $html = '';
        if (isset($item->comments)) {
            $html = JText::_('COM_USERIDEAS_COMMENTS') .': <a href="' . JRoute::_('index.php?option=com_userideas&view=comments&filter_search=iid:'.(int)$item->id). '">( ' . $item->comments . ' )</a>';
        }

        return $html;
    }

    public static function attachmentNumber($item, $type)
    {
        $html = '';
        if (isset($item->attachment)) {
            $itemId = 0;
            if (strcmp('item', $type) === 0) {
                $itemId = (int)$item->id;
            } elseif (strcmp('comment', $type) === 0) {
                $itemId = (int)$item->item_id;
            }

            $html = JText::_('COM_USERIDEAS_ATTACHMENTS') . ': <a href="' . JRoute::_('index.php?option=com_userideas&view=attachments&id='.$itemId.'&filter_source='.$type.'&filter_search=id:'.$item->attachment->id). '">(' .$item->attachment->number. ')</a>';
        }

        return $html;
    }

    public static function source($item)
    {
        if (strcmp('comment', $item->source) === 0) {
            return $item->source .' <a href="' . JRoute::_('index.php?option=com_userideas&view=comments&filter_search=id:'.$item->comment_id). '">(#' .$item->comment_id. ')</a>';
        } else {
            return $item->source.' <a href="' . JRoute::_('index.php?option=com_userideas&view=items&filter_search=id:'.$item->item_id). '">(#' .$item->item_id. ')</a>';
        }
    }

    public static function status(Userideas\Status\Status $status, $displayLink = true)
    {
        if (!$status->getId()) {
            return '';
        }

        $styles = $status->getParam('style_class', '');

        if ($displayLink === true) {
            $html = '
            <a href="' . UserideasHelperRoute::getItemsRoute($status->getId()) . '" class="ui-status-label">
            <span class="label ' . $styles . '">' . htmlspecialchars($status->getTitle(), ENT_QUOTES, 'utf-8') . '</span>
            </a>';
        } else {
            $html = '<span class="label ui-status-label ' . $styles . '">' . htmlspecialchars($status->getTitle(), ENT_QUOTES, 'utf-8') . '</span>';
        }

        return $html;
    }

    public static function styleClass($name)
    {
        if (!$name) {
            return '---';
        }

        return '<div class="label '.$name.'">'.$name.'</div>';
    }

    public static function filterStatus($value, array $status, $url)
    {
        $active = '';
        if ((int)$value === (int)$status['value']) {
            $active = 'class="active"';
        }

        return '<li '.$active.'><a href="'.$url.'?filter_status='.$status['value'].'">'.$status['text'].'</a></li>';
    }

    /**
     * Load the script that initialize vote system.
     *
     * @param int $counterButton
     */
    public static function loadVoteScript($counterButton = 0)
    {
        // Only load once
        if (!empty(self::$loaded[__METHOD__])) {
            return;
        }

        $counterButton = $counterButton ? 1 : 0;
        
        $document      = JFactory::getDocument();

        $document->addScriptDeclaration('
            var userIdeas = {
                url: "'.JUri::root().'index.php?option=com_userideas&task=item.vote&format=raw",
                token: {
                    "'.JSession::getFormToken().'": 1
                },
                counter_button: '. $counterButton .'
            };
        ');

        $document->addScript('plugins/system/userideasvote/votebutton.js');

        self::$loaded[__METHOD__] = true;
    }
}
