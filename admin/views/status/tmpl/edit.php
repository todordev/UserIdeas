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

<form action="<?php echo JRoute::_('index.php?option=com_userideas'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" >
    <div class="form-horizontal">

        <?php echo JHtml::_('bootstrap.startTabSet', 'userideasStatus', array('active' => 'details')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'userideasStatus', 'details', JText::_('COM_USERIDEAS_CONTENT')); ?>
            <div class="row-fluid">
                <div class="span6">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('name'); ?></div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('default'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('default'); ?></div>
                    </div>

                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
                    </div>
                </div>
            </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'userideasStatus', 'options', JText::_('COM_USERIDEAS_OPTIONS')); ?>
            <div class="row-fluid">
                <div class="span6">

                <?php foreach ($this->form->getFieldset("basic") as $field) { ?>
                    <div class="control-group">
                        <div class="control-label"><?php echo $field->label; ?></div>
                        <div class="controls"><?php echo $field->input; ?></div>
                    </div>
                <?php } ?>
                </div>
            </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.endTabSet'); ?>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>