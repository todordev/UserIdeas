<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
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
            <?php echo JHtml::_('jgrid.published', $item->published, $i, "comments."); ?>
        </td>
        <td class="title">
			<a href="<?php echo JRoute::_("index.php?option=com_userideas&view=comment&layout=edit&id=".$item->id); ?>" >
		        <?php echo $this->escape($item->comment); ?>
	        </a>
	    </td>
		<td>
			<?php echo $item->item; ?>
		</td>
		<td class="hidden-phone">
            <?php echo JHtml::_('date', $item->record_date, JText::_('DATE_FORMAT_LC3')) ; ?>
        </td>
		<td class="hidden-phone"><?php echo ($item->user) ?: JText::_("COM_USERIDEAS_ANONYMOUS"); ?></td>
        <td class="center hidden-phone"><?php echo $item->id;?></td>
	</tr>
<?php } ?>
	  