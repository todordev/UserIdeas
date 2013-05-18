<?php
/**
 * @package      ITPrism Components
 * @subpackage   UserIdeas
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * UserIdeas is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach ($this->items as $i => $item) {
    $ordering  = ($this->listOrder == 'a.ordering');
    ?>
	<tr class="row<?php echo $i % 2; ?>">
		<td class="center">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
		<td>
			<a href="<?php echo JRoute::_("index.php?option=com_userideas&view=item&layout=edit&id=".$item->id); ?>" ><?php echo $item->title; ?></a>
		</td>
		<td class="center"><?php echo $item->votes; ?></td>
		<td class="center"><?php echo $item->record_date; ?></td>
		<td class="center"><?php echo $item->user; ?></td>
		<td class="center"><?php echo $item->category; ?></td>
		<td class="order">
		 <?php
            if($this->saveOrder) {
                if ($this->listDirn == 'asc') {?>
                    <span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid), 'items.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->catid == @$this->items[$i+1]->catid), 'items.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                <?php } elseif ($this->listDirn == 'desc') {?>
                    <span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid), 'items.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                    <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->catid == @$this->items[$i+1]->catid), 'items.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                <?php } 
            }
            $disabled = $this->saveOrder ?  '' : 'disabled="disabled"';?>
            <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
        </td>
        
		<td class="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, "items."); ?></td>
        <td align="center"><?php echo $item->id;?></td>
	</tr>
<?php } ?>
	  