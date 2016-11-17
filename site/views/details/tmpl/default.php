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
 * @var Userideas\Attachment\Attachment $attachment
 */
if ($this->item->event->beforeDisplayContent) {
    echo $this->item->event->beforeDisplayContent;
} ?>
<div class="row">
    <div class="col-md-12">
        <div class="media ui-item">
            <div class="ui-vote pull-left">
                <div class="ui-vote-counter js-ui-item-counter" id="js-ui-vote-counter-<?php echo $this->item->id; ?>" data-id="<?php echo $this->item->id; ?>"><?php echo $this->item->votes; ?></div>
                <a class="btn btn-primary ui-btn-vote js-ui-btn-vote" href="javascript: void(0);" data-id="<?php echo $this->item->id; ?>">
                    <?php echo JText::_('COM_USERIDEAS_VOTE'); ?>
                </a>
            </div>
            <div class="media-body">
                <?php if ($this->params->get('show_title', $this->item->params->get('show_title'))) {?>
                <h4 class="media-heading">
                    <?php echo $this->escape($this->item->title); ?>
                </h4>
                <?php } ?>

                <?php if ($this->params->get('show_intro', $this->item->params->get('show_intro', 1))) {?>
                <p><?php echo $this->item->description; ?></p>
                <?php } ?>

                <?php
                if ($this->params->get('allow_attachment', Prism\Constants::DISABLED) and $this->hasAttachment) {
                    $layout = new JLayoutFile('attachment');
                    echo $layout->render($this->itemLayoutData);
                } ?>
            </div>

            <?php
            $hasTags = (bool)(isset($this->item->tags) and is_array($this->item->tags) and count($this->item->tags) > 0);
            if (UserideasHelper::shouldDisplayFootbar($this->params, $this->item->params, $hasTags)) {
                echo '<div class="clearfix"></div>';

                $layoutData = new stdClass;
                $layoutData->item                = $this->item;
                $layoutData->socialProfiles      = $this->socialProfiles;
                $layoutData->integrationOptions  = $this->integrationOptions;
                $layoutData->commentsEnabled     = $this->commentsEnabled;
                $layoutData->params              = $this->params;
                $layoutData->commentsNumber      = count($this->comments);

                $layout      = new JLayoutFile('footbar');
                echo $layout->render($layoutData);
            }?>

        </div>
    </div>
</div>

<?php
if (!empty($this->item->event->onContentAfterDisplay)) {
    echo $this->item->event->onContentAfterDisplay;
} ?>

<?php if ($this->commentsEnabled) { ?>
    <div class="row" id="comments">
        <div class="col-md-12">

            <h4><?php echo JText::_('COM_USERIDEAS_COMMENTS'); ?></h4>
            <hr/>
            <?php foreach ($this->comments as $comment) { ?>
                <div class="media ui-comment">
                    <?php

                    // Get the profile and avatar.
                    $profileLink = JHtml::_('userideas.profile', $this->socialProfiles, $comment->user_id, 'javascript: void(0);');
                    $avatar      = JHtml::_('userideas.avatar', $this->socialProfiles, $comment->user_id, $this->integrationOptions);
                    $name        = (strcmp('name', $this->params->get('integration_name_type', 'name')) === 0) ? $comment->author : $comment->author_username;

                    if (!empty($avatar)) { ?>
                    <div class="media-left">
                        <a href="<?php echo $profileLink; ?>" rel="nofollow">
                            <img class="media-object" src="<?php echo $avatar; ?>"/>
                        </a>
                    </div>
                    <?php } ?>
                    <div class="media-body">
                        <?php echo $this->escape($comment->comment); ?>
                        <?php
                        $isOwner    = ($this->userId > 0 and $this->userId === (int)$comment->user_id);
                        if ($this->commentsAttachmentsEnabled and array_key_exists($comment->id, $this->commentsAttachments)) {
                            // Prepare layout data.
                            $attachment = $this->commentsAttachments[$comment->id];
                            $fileUrl    = $this->mediaFolder .'/'. $attachment->getFilename();

                            $this->commentLayoutData->attachment = $attachment;
                            $this->commentLayoutData->fileUrl    = $fileUrl;
                            $this->commentLayoutData->canEdit    = ($isOwner and $this->canEditComment);

                            $layout = new JLayoutFile('attachment');
                            echo $layout->render($this->commentLayoutData);
                        } ?>
                    </div>

                    <div class="clearfix"></div>
                    <div class="well well-sm clearfix">
                        <div class="pull-left">
                            <?php echo JHtml::_('userideas.publishedByOn', $name, $comment->record_date, $profileLink); ?>
                        </div>
                        <div class="pull-right">
                            <?php if ($isOwner and $this->canEditComment) { ?>
                                <a class="btn btn-default btn-sm" href="<?php echo JRoute::_(UserideasHelperRoute::getDetailsRoute($this->item->slug, $this->item->catid) . '&comment_id=' . (int)$comment->id); ?>#ui-comment-form">
                                    <span class="fa fa-edit"></span>
                                    <?php echo JText::_('COM_USERIDEAS_EDIT'); ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>

                </div>
            <?php } ?>

            <div class="clearfix"></div>

            <?php if ($this->canComment) { ?>
            <div class="well well-sm mt-10">
                <form action="<?php echo JRoute::_('index.php?option=com_userideas'); ?>" method="post" name="commentForm" id="ui-comment-form" class="form-validate" <?php echo $this->formEncrypt; ?>>
                    <div class="form-group">
                        <?php echo $this->form->getLabel('comment'); ?>
                        <?php echo $this->form->getInput('comment'); ?>
                    </div>

                    <?php if ($this->commentsAttachmentsEnabled) { ?>
                        <div class="form-group">
                            <?php echo $this->form->getLabel('attachment'); ?>
                            <?php echo $this->form->getInput('attachment'); ?>

                            <p class="help-block fs"><?php echo JText::sprintf('COM_USERIDEAS_FILE_SIZE_NOTE_', $this->maxFileSize); ?> <?php echo JText::sprintf('COM_USERIDEAS_FILE_TYPES_NOTE_', $this->params->get('legal_extensions', 'PDF, RTF, DOC, PPS, PPT, XLS, PNG, JPG, JPEG, BMP, GIF')); ?></p>
                        </div>
                        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $this->maxFileSizeBites; ?>" />
                    <?php } ?>

                    <div class="form-group">
                        <?php echo $this->form->getLabel('captcha'); ?>
                        <?php echo $this->form->getInput('captcha'); ?>
                    </div>

                    <?php echo $this->form->getInput('id'); ?>
                    <?php echo $this->form->getInput('item_id'); ?>

                    <input type="hidden" name="task" value="comment.save"/>
                    <?php echo JHtml::_('form.token'); ?>

                    <button type="submit" class="btn btn-primary" <?php echo $this->disabledButton; ?>>
                        <span class="fa fa-check"></span>
                        <?php echo JText::_('COM_USERIDEAS_SUBMIT') ?>
                    </button>
                </form>
            </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>