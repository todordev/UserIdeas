<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;?>
<?php foreach ($this->items as $i => $item) {
    $ordering  = ($this->listOrder == 'a.ordering');

    $disableClassName = '';
    $disabledLabel	  = '';
    if (!$this->saveOrder) {
        $disabledLabel    = JText::_('JORDERINGDISABLED');
        $disableClassName = 'inactive tip-top';
    }
?>
	<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid?>">
	   <td class="order nowrap center hidden-phone">
    		<span class="sortable-handler hasTooltip <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>">
    			<i class="icon-menu"></i>
    		</span>
    		<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
    	</td>
		<td class="hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="center">
		    <?php echo JHtml::_('jgrid.published', $item->published, $i, "items."); ?>
		</td>
		<td>
			<a href="<?php echo JRoute::_("index.php?option=com_userideas&view=item&layout=edit&id=".$item->id); ?>" ><?php echo $this->escape($item->title); ?></a>
		</td>
		<td class="center hidden-phone"><?php echo $item->votes; ?></td>
		<td class="hidden-phone">
            <?php echo JHtml::_('date', $item->record_date, JText::_('DATE_FORMAT_LC3')) ; ?>
        </td>
		<td class="hidden-phone"><?php echo ($item->user) ?: JText::_("COM_USERIDEAS_ANONYMOUS"); ?></td>
		<td class="hidden-phone"><?php echo (!empty($item->category)) ? $this->escape($item->category) : "--"; ?></td>
		<td class="center hidden-phone"><?php echo JHtml::_("userideas.status", $item->status, false); ?></td>
        <td class="center hidden-phone"><?php echo $item->id;?></td>
	</tr>
<?php } ?>
	  