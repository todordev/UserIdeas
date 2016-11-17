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
/**
 * @var Userideas\Attachment\Attachment $attachment
 */
$attachment  = $displayData->attachment;
?>
<div class="panel panel-default">
    <div class="panel-heading"><span class="fa fa-file"></span> <?php echo JText::_('COM_USERIDEAS_ATTACHMENT');?></div>
    <div class="panel-body">
        <a href="<?php echo $displayData->fileUrl; ?>" download><?php echo $attachment->getFilename();?></a>
        <a href="<?php echo $displayData->fileUrl; ?>" class="btn btn-default btn-xs" role="button" download title="<?php echo JText::_('COM_USERIDEAS_DOWNLOAD');?>"><span class="fa fa-download"></span></a>

        <?php if ($displayData->canEdit) {?>
        <a href="javascript: void(0);" data-href="<?php echo JRoute::_('index.php?option=com_userideas&task=attachment.removeFile&id='.(int)$attachment->getId().'&'.JSession::getFormToken().'=1&return='.base64_encode($displayData->returnUrl)); ?>" class="btn btn-danger btn-xs js-ui-btn-attachment-remove" role="button" title="<?php echo JText::_('COM_USERIDEAS_REMOVE');?>"><span class="fa fa-trash"></span></a>
        <?php } ?>

        <div>
            <span class="fa fa-info-circle"></span>
            <?php echo '<strong>'.JText::_('COM_USERIDEAS_FILESIZE') .'</strong>: <span title="'. Prism\Utilities\MathHelper::convertFromBytes($attachment->getFilesize()).'">'. $attachment->getFilesize(). '</span> '.JText::_('COM_USERIDEAS_BYTES');?> | <?php echo '<strong>'.JText::_('COM_USERIDEAS_MIMETYPE') .'</strong>: '. $attachment->getMime();?>
        </div>
    </div>
</div>