<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="row-fluid">
    <div class="span12">
        <form action="<?php echo JRoute::_('index.php?option=com_userideas'); ?>" method="post" name="itemForm" id="uf-item-form" class="form-validate">
            
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('catid'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('catid'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
                </div>
            
            <?php echo $this->form->getInput('id'); ?>
            <input type="hidden" name="task" value="form.save" />
            <?php echo JHtml::_('form.token'); ?>
            
            <div class="clearfix"></div>
            <button type="submit" class="btn btn-primary" <?php echo $this->disabledButton;?>>
            	<i class="icon-ok icon-white"></i>
                <?php echo JText::_("JSUBMIT")?>
            </button>
        </form>
    </div>
</div>
<?php echo $this->version->backlink;?>