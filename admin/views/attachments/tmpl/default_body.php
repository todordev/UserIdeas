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
        <td class="title has-context">
			<?php echo $this->escape($item->filename); ?>
			<a href="<?php echo $this->mediaFolder.'/'.$item->filename; ?>" class="btn btn-mini" role="button" download title="<?php echo JText::_('COM_USERIDEAS_DOWNLOAD');?>">
				<span class="icon icon-download"></span>
			</a>
	    </td>
		<td class="hidden-phone">
			<?php echo Prism\Utilities\MathHelper::convertFromBytes($item->filesize); ?>
        </td>
		<td class="hidden-phone">
			<?php echo $this->escape($item->mime); ?>
		</td>
		<td class="hidden-phone">
            <?php echo JHtml::_('date', $item->record_date, JText::_('DATE_FORMAT_LC3')) ; ?>
        </td>
		<td class="hidden-phone">
			<?php echo JHtml::_('userideas.source', $item); ?>
		</td>
        <td class="center hidden-phone"><?php echo $item->id;?></td>
	</tr>
<?php } ?>
