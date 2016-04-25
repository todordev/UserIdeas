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

<form action="<?php echo JRoute::_('index.php?option=com_userideas'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" >
    <div class="form-horizontal">

        <?php echo JHtml::_('bootstrap.startTabSet', 'userideasStatus', array('active' => 'details')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'userideasStatus', 'details', JText::_('COM_USERIDEAS_CONTENT')); ?>
            <div class="row-fluid">
                <div class="span12">
                    <?php echo $this->form->getControlGroup('name'); ?>
                    <?php echo $this->form->getControlGroup('default'); ?>
                    <?php echo $this->form->getControlGroup('id'); ?>
                </div>
            </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'userideasStatus', 'options', JText::_('COM_USERIDEAS_OPTIONS')); ?>
            <div class="row-fluid">
                <div class="span12">

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