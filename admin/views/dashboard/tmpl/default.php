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
<?php if(!empty( $this->sidebar)): ?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
    <div id="j-main-container">
    <?php endif;?>
    <?php echo $this->loadTemplate('statistics'); ?>
	<div class="span4">
        <a href="http://itprism.com/free-joomla-extensions/ecommerce-gamification/feedbacks-ideas-suggestions" target="_blank"><img src="../media/com_userideas/images/logo.png" alt="<?php echo JText::_("COM_USERIDEAS");?>" /></a>
        <a href="http://itprism.com" target="_blank" title="<?php echo JText::_('COM_USERIDEAS_ITPRISM_PRODUCT');?>"><img src="../media/com_userideas/images/product_of_itprism.png" alt="<?php echo JText::_("COM_USERIDEAS_PRODUCT");?>" /></a>
        <p><?php echo JText::_('COM_USERIDEAS_YOUR_VOTE'); ?></p>
        <p><?php echo JText::_('COM_USERIDEAS_SUBSCRIPTION'); ?></p>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td><?php echo JText::_('COM_USERIDEAS_INSTALLED_VERSION');?></td>
                    <td><?php echo $this->version->getShortVersion();?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('COM_USERIDEAS_RELEASE_DATE');?></td>
                    <td><?php echo $this->version->releaseDate?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('COM_USERIDEAS_PRISM_LIBRARY_VERSION');?></td>
                    <td><?php echo $this->prismVersion;?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('COM_USERIDEAS_COPYRIGHT');?></td>
                    <td><?php echo $this->version->copyright;?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('COM_USERIDEAS_LICENSE');?></td>
                    <td><?php echo $this->version->license;?></td>
                </tr>
            </tbody>
        </table>

        <?php if (!empty($this->prismVersionLowerMessage)) {?>
            <p class="alert alert-warning cf-upgrade-info"><i class="icon-warning"></i> <?php echo $this->prismVersionLowerMessage; ?></p>
        <?php } ?>
        <div class="alert alert-info cf-upgrade-info"><i class="icon-info"></i> <?php echo JText::_('COM_USERIDEAS_HOW_TO_UPGRADE'); ?></div>
        <div class="alert alert-info cf-upgrade-info"><i class="icon-comment"></i> <?php echo JText::_('COM_USERIDEAS_FEEDBACK_INFO'); ?></div>
	</div>
</div>