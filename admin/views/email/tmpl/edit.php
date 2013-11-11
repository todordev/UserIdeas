<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="row-fluid">
    <div class="span8 form-horizontal">
        <form action="<?php echo JRoute::_('index.php?option=com_userideas'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" >
            
            <fieldset>
            
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('subject'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('subject'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('sender_name'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('sender_name'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('sender_email'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('sender_email'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('body'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('body'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
                </div>
                
            </fieldset>
            
            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
    <div class="span4">
        <h3><?php echo JText::_("COM_USERIDEAS_INDICATORS_LIST");?></h3>
        <p class="small"><?php echo JText::_("COM_USERIDEAS_INDICATORS_INFO");?></p>
        <dl class="dl-horizontal">
            <dt>{SITE_NAME}</dt>
            <dd><?php echo JText::_("COM_USERIDEAS_EMAIL_SITE_NAME");?></dd>
            <dt>{SITE_URL}</dt>
            <dd><?php echo JText::_("COM_USERIDEAS_EMAIL_SITE_URL");?></dd>
            <dt>{ITEM_TITLE}</dt>
            <dd><?php echo JText::_("COM_USERIDEAS_EMAIL_ITEM_TITLE");?></dd>
            <dt>{ITEM_URL}</dt>
            <dd><?php echo JText::_("COM_USERIDEAS_EMAIL_ITEM_URL");?></dd>
            <dt>{SENDER_NAME}</dt>
            <dd><?php echo JText::_("COM_USERIDEAS_EMAIL_SENDER_NAME");?></dd>
            <dt>{SENDER_EMAIL}</dt>
            <dd><?php echo JText::_("COM_USERIDEAS_EMAIL_SENDER_EMAIL");?></dd>
            <dt>{RECIPIENT_NAME}</dt>
            <dd><?php echo JText::_("COM_USERIDEAS_EMAIL_RECIPIENT_NAME");?></dd>
            <dt>{RECIPIENT_EMAIL}</dt>
            <dd><?php echo JText::_("COM_USERIDEAS_EMAIL_RECIPIENT_EMAIL");?></dd>
        </dl>
        
        <p class="small"><?php echo JText::_("COM_USERIDEAS_EMAIL_EXTRA_LINE");?></p>
    </div>
</div>