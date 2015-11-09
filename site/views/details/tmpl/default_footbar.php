<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="clearfix"></div>
<div class="well well-sm clearfix">
    <div class="pull-left">
        <?php
        if ($this->item->params->get('show_author', 1) or $this->item->params->get('show_create_date', 1)) {
            $name = (strcmp('name', $this->params->get('name_type')) === 0) ? $this->item->name : $this->item->username;

            $profile = JHtml::_('userideas.profile', $this->socialProfiles, $this->item->user_id);

            // Prepare item owner avatar.
            $profileAvatar = null;
            if ($this->params->get('integration_display_owner_avatar', 0)) {
                $profileAvatar = JHtml::_('userideas.avatar', $this->socialProfiles, $this->item->user_id, $this->integrationOptions);
            }

            if ($this->item->params->get('show_author', 1) and $this->item->params->get('show_create_date', 1)) {
                echo JHtml::_('userideas.publishedByOn', $name, $this->item->record_date, $profile, $profileAvatar, $this->integrationOptions);
            } elseif ($this->item->params->get('show_author', 1) and !$this->item->params->get('show_create_date', 1)) {
                echo JHtml::_('userideas.publishedBy', $name, $profile, $profileAvatar, $this->integrationOptions);
            } elseif ($this->item->params->get('show_create_date', 1)) {
                echo JHtml::_('userideas.publishedOn', $this->item->record_date);
            }
        }

        if ($this->item->params->get('show_category', 1)) {
            echo JHtml::_('userideas.category', $this->item->category, $this->item->catslug);
        }

        if ($this->item->params->get('show_hits', 0)) {
            echo JText::sprintf('COM_USERIDEAS_HITS_D', $this->item->hits);
        }

        if ($this->item->params->get('show_status', 1)) {
            echo JHtml::_('userideas.status', $this->item->status);
        }

        ?>
    </div>
    <div class="pull-right">
        <?php if ($this->canEditResult) { ?>
            <a class="btn btn-default btn-sm" href="<?php echo JRoute::_(UserIdeasHelperRoute::getFormRoute($this->item->id)); ?>">
                <span class="fa fa-edit"></span>
                <?php echo JText::_('COM_USERIDEAS_EDIT'); ?>
            </a>
        <?php } ?>
    </div>

    <?php 
    if ($this->params->get('show_tags', $this->item->params->get('show_tags')) and !empty($this->item->tags)) { ?>
        <div class="clearfix"></div>
        <?php
        $tagLayout = new JLayoutFile('joomla.content.tags');
        echo $tagLayout->render($this->item->tags);
        ?>
    <?php } ?>
</div>