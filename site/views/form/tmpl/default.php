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
        <form action="<?php echo JRoute::_('index.php?option=com_userideas'); ?>" method="post" name="itemForm" id="uf-item-form" class="form-validate">
            
                <div class="form-group">
                    <?php echo $this->form->getLabel('title'); ?>
					<?php echo $this->form->getInput('title'); ?>
                </div>
                <div class="form-group">
                    <?php echo $this->form->getLabel('catid'); ?>
					<?php echo $this->form->getInput('catid'); ?>
                </div>
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