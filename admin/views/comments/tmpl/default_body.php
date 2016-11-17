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
<?php foreach ($this->items as $i => $item) {?>
	<tr class="row<?php echo $i % 2;?>">
		<td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="center">
            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'comments.', $this->canDo->get('core.edit.state')); ?>
        </td>
        <td class="title has-context">
			<?php if ($this->canDo->get('core.edit')) { ?>
				<a href="<?php echo JRoute::_('index.php?option=com_userideas&view=comment&layout=edit&id='.$item->id); ?>" >
					<?php echo $this->escape($item->comment); ?>
				</a>
			<?php } else { ?>
				<?php echo $this->escape($item->comment); ?>
			<?php } ?>
			<div class="small">
				<?php echo JText::sprintf('COM_USERIDEAS_ITEM_S', $this->escape($item->item)); ?>
			</div>
			<div class="small">
				<?php echo JHtml::_('userideas.attachmentNumber', $item, 'comment'); ?>
			</div>
	    </td>
		<td class="hidden-phone">
            <?php echo JHtml::_('date', $item->record_date, JText::_('DATE_FORMAT_LC3')) ; ?>
        </td>
		<td class="hidden-phone"><?php echo ($item->user) ?: JText::_('COM_USERIDEAS_ANONYMOUS'); ?></td>
        <td class="center hidden-phone"><?php echo $item->id;?></td>
	</tr>
<?php } ?>
