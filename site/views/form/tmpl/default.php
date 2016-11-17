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
?>
<div class="row">
    <div class="col-md-12">
        <form action="<?php echo JRoute::_('index.php?option=com_userideas'); ?>" method="post" name="itemForm" id="uf-item-form" class="form-validate" <?php echo $this->formEncrypt; ?>>
            
            <div class="form-group">
                <?php echo $this->form->getLabel('title'); ?>
                <?php echo $this->form->getInput('title'); ?>
            </div>
            <div class="form-group">
                <?php echo $this->form->getLabel('catid'); ?>
                <?php echo $this->form->getInput('catid'); ?>
            </div>

            <?php if ($this->params->get('allow_attachment', Prism\Constants::DISABLED)) { ?>
            <div class="form-group">
                <?php echo $this->form->getLabel('attachment'); ?>
                <?php echo $this->form->getInput('attachment'); ?>

                <p class="help-block fs"><?php echo JText::sprintf('COM_USERIDEAS_FILE_SIZE_NOTE_', $this->maxFileSize); ?> <?php echo JText::sprintf('COM_USERIDEAS_FILE_TYPES_NOTE_', $this->params->get('legal_extensions', 'PDF, RTF, DOC, PPS, PPT, XLS, PNG, JPG, JPEG, BMP, GIF')); ?></p>
            </div>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $this->maxFileSizeBites; ?>" />

            <?php
            if ($this->hasAttachment) {
                $layout = new JLayoutFile('attachment');
                echo $layout->render($this->layoutData);
            } ?>

            <?php } ?>

            <div class="form-group">
                <?php echo $this->form->getLabel('description'); ?>
                <?php echo $this->form->getInput('description'); ?>
            </div>

            <div class="form-group">
                <?php echo $this->form->getLabel('captcha'); ?>
                <?php echo $this->form->getInput('captcha'); ?>
            </div>

            <?php echo $this->form->getInput('id'); ?>
            <input type="hidden" name="task" value="form.save" />
            <?php echo JHtml::_('form.token'); ?>
            
            <button type="submit" class="btn btn-primary" <?php echo $this->disabledButton;?>>
            	<span class="fa fa-check"></span>
                <?php echo JText::_('JSUBMIT')?>
            </button>
        </form>
    </div>
</div>