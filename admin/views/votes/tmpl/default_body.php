<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach ($this->items as $i => $item) {?>
	<tr class="row<?php echo $i % 2;?>">
        <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td>
			<?php echo (!empty($item->name)) ? $this->escape($item->name) : JText::_("COM_USERIDEAS_ANONYMOUS"); ?>
		</td>
        <td class="hidden-phone">
		    <?php echo $this->escape($item->title); ?>
		</td>
        <td class="center">
            <?php echo $item->votes;?>
        </td>
        <td class="hidden-phone">
            <?php echo JHtml::_('date', $item->record_date, JText::_('DATE_FORMAT_LC2')); ?>
        </td>
        <td class="center hidden-phone">
            <?php echo $item->id;?>
        </td>
	</tr>
<?php }?>
	  