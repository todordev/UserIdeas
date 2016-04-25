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

    <?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <div class="form-horizontal">

        <?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_USERIDEAS_CONTENT', true)); ?>

        <div class="row-fluid">
            <div class="span9">
                <?php echo $this->form->getInput('description'); ?>
            </div>
            <div class="span3">
                <fieldset class="form-vertical">
                    <?php echo $this->form->renderField('catid'); ?>
                    <?php echo $this->form->renderField('status_id'); ?>
                    <?php echo $this->form->renderField('access'); ?>
                    <?php echo $this->form->renderField('published'); ?>
                    <?php echo $this->form->renderField('tags'); ?>
                </fieldset>
            </div>
        </div>

        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('COM_USERIDEAS_PUBLISHING', true)); ?>
        <div class="row-fluid form-horizontal-desktop">
            <div class="span6">
                <?php echo $this->form->renderField('record_date'); ?>
                <?php echo $this->form->renderField('user_id'); ?>
                <?php echo $this->form->renderField('id'); ?>
            </div>
        </div>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'options', JText::_('COM_USERIDEAS_OPTIONS', true)); ?>
        <?php echo $this->loadTemplate('options'); ?>
        <?php echo JHtml::_('bootstrap.endTab'); ?>

        <?php if ($this->canDo->get('core.admin')) : ?>
            <?php echo JHtml::_('bootstrap.addTab', 'myTab', 'permissions', JText::_('COM_USERIDEAS_FIELDSET_RULES')); ?>
            <?php echo $this->form->getInput('rules'); ?>
            <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php endif; ?>

        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
    </div>

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>