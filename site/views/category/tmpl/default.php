<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die; ?>
<div class="ui-items<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>

    <?php
    if ($this->params->get('items_display_description', 0)) {
        echo $this->category->getDescription();
    }
    ?>

    <?php if ($this->params->get('items_display_button', 1)) { ?>
        <a href="<?php echo JRoute::_(UserIdeasHelperRoute::getFormRoute(0)); ?>" class="btn btn-default">
            <span class="fa fa-plus"></span>
            <?php echo JText::_('COM_USERIDEAS_POST_ITEM'); ?>
        </a>
    <?php } ?>

    <?php foreach ($this->items as $item) {
        if (isset($this->comments[$item->id])) {
            $commentsNumber = (int)$this->comments[$item->id];
        } else {
            $commentsNumber = 0;
        }
        ?>
        <div class="media ui-item">
            <div class="ui-vote pull-left">
                <div class="ui-vote-counter" id="js-ui-vote-counter-<?php echo $item->id; ?>"><?php echo $item->votes; ?></div>
                <a class="btn btn-default btn-small ui-btn-vote js-ui-btn-vote" href="javascript: void(0);" data-id="<?php echo $item->id; ?>"><?php echo JText::_('COM_USERIDEAS_VOTE'); ?></a>
            </div>
            <div class="media-body">
                <h4 class="media-heading">
                    <a href="<?php echo JRoute::_(UserIdeasHelperRoute::getDetailsRoute($item->slug, $item->catid)); ?>">
                        <?php echo $this->escape($item->title); ?>
                    </a>
                </h4>
                <?php if ($this->params->get('items_display_description', 1)) { ?>
                    <?php echo JHtmlString::truncate($item->description, $this->params->get("items_description_length", 255), true, $this->params->get('items_description_html', 0)); ?>
                <?php } ?>
            </div>
            <div class="clearfix"></div>
            <div class="well well-sm clearfix">
                <div class="pull-left">
                    <?php

                    $name = (strcmp('name', $this->params->get('name_type')) === 0) ? $item->name : $item->username;

                    $profile = JHtml::_('userideas.profile', $this->socialProfiles, $item->user_id);

                    // Prepare item owner avatar.
                    $profileAvatar = null;
                    if ($this->params->get('integration_display_owner_avatar', 0)) {
                        $profileAvatar = JHtml::_('userideas.avatar', $this->socialProfiles, $item->user_id, $this->integrationOptions);
                    }

                    echo JHtml::_('userideas.publishedByOn', $name, $item->record_date, $profile, $profileAvatar, $this->integrationOptions);
                    echo JHtml::_('userideas.category', $item->category, $item->catslug);
                    echo JHtml::_('userideas.status', $item->status);
                    ?>
                </div>
                <div class="pull-right">
                    <?php if ($this->commentsEnabled) { ?>
                        <a class="btn btn-default btn-small" href="<?php echo JRoute::_(UserIdeasHelperRoute::getDetailsRoute($item->slug, $item->catid)) . '#comments'; ?>'>
                            <span class="fa fa-comment"></span>
                            <?php echo JText::_('COM_USERIDEAS_COMMENTS'); ?>
                            <?php echo '( ' . $commentsNumber . ' )'; ?>
                        </a>
                    <?php } ?>

                    <?php if (UserIdeasHelper::isValidOwner($this->userId, $item->user_id) and $this->canEdit) { ?>
                        <a class="btn btn-default btn-sm" href="<?php echo JRoute::_(UserIdeasHelperRoute::getFormRoute($item->id)); ?>">
                            <span class="fa fa-edit"></span>
                            <?php echo JText::_('COM_USERIDEAS_EDIT'); ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) { ?>
        <div class="pagination">
            <?php if ($this->params->def('show_pagination_results', 1)) { ?>
                <p class="counter pull-right"> <?php echo $this->pagination->getPagesCounter(); ?> </p>
            <?php } ?>
            <?php echo $this->pagination->getPagesLinks(); ?> </div>
    <?php } ?>
</div>