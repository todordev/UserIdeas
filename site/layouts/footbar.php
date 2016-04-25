<?php
/**
 * @package      Crowdfunding
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$item   = $displayData->item;
$socialProfiles = $displayData->socialProfiles;
$integrationOptions = $displayData->integrationOptions;
$params = $displayData->params;
/** @var Joomla\Registry\Registry $params */

$showAuthor = $params->get('show_author', $item->params->get('show_author'));
$showCreateDate = $params->get('show_create_date', $item->params->get('show_create_date'))
?>
<div class="well well-sm clearfix">
    <div class="pull-left">
        <?php

        if ($showAuthor or $showCreateDate) {
            $name = (strcmp('name', $params->get('integration_name_type')) === 0) ? $item->name : $item->username;

            if ($socialProfiles !== null) {
                $profile = JHtml::_('userideas.profile', $socialProfiles, $item->user_id);

                // Prepare item owner avatar.
                $profileAvatar = null;
                if ($params->get('integration_display_owner_avatar', 0)) {
                    $profileAvatar = JHtml::_('userideas.avatar', $socialProfiles, $item->user_id, $integrationOptions);
                }
            }

            if ($showAuthor and $showCreateDate and $socialProfiles !== null) {
                echo JHtml::_('userideas.publishedByOn', $name, $item->record_date, $profile, $profileAvatar, $integrationOptions);
            } elseif ($showAuthor and !$showCreateDate and $socialProfiles !== null) {
                echo JHtml::_('userideas.publishedBy', $name, $profile, $profileAvatar, $integrationOptions);
            } elseif ($showCreateDate) {
                echo JHtml::_('userideas.publishedOn', $item->record_date);
            }

        }

        if ($params->get('show_category', $item->params->get('show_category'))) {
            echo JHtml::_('userideas.category', $item->category, $item->catslug);
        }

        if ($params->get('show_hits', $item->params->get('show_hits'))) {
            echo JText::sprintf('COM_USERIDEAS_HITS_D', $item->hits);
        }

        if ($params->get('show_status', $item->params->get('show_status'))) {
            echo JHtml::_('userideas.status', $item->status);
        }
        ?>
    </div>
    <div class="pull-right">
        <?php if ($displayData->commentsEnabled) { ?>
            <a class="btn btn-default btn-sm" href="<?php echo JRoute::_(UserideasHelperRoute::getDetailsRoute($item->slug, $item->catid)) . '#comments'; ?>">
                <span class="fa fa-comment"></span>
                <?php echo JText::_('COM_USERIDEAS_COMMENTS'); ?>
                <?php echo ($displayData->commentsNumber !== null) ? '( ' . $displayData->commentsNumber . ' )' : ''; ?>
            </a>
        <?php } ?>

        <?php
        if ($item->params->get('access-edit')) { ?>
            <a class="btn btn-default btn-sm" href="<?php echo JRoute::_(UserideasHelperRoute::getFormRoute($item->id)); ?>">
                <span class="fa fa-edit"></span>
                <?php echo JText::_('COM_USERIDEAS_EDIT'); ?>
            </a>
        <?php } ?>
    </div>
    <?php
    if ($params->get('show_tags', $item->params->get('show_tags')) and !empty($item->tags)) { ?>
        <?php
        $tagLayout = new JLayoutFile('joomla.content.tags');
        echo $tagLayout->render($item->tags);
        ?>
    <?php } ?>
</div>