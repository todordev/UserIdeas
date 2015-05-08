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
        <td class="title">
			<a href="<?php echo JRoute::_("index.php?option=com_userideas&view=email&layout=edit&id=".$item->id); ?>" >
		        <?php echo $this->escape($item->title); ?>
	        </a>
	    </td>
        <td class="hidden-phone">
            <?php echo $this->escape($item->subject); ?>
	    </td>
		<td class="center hidden-phone"><?php echo $this->escape($item->sender_name); ?></td>
		<td class="center hidden-phone"><?php echo $this->escape($item->sender_email); ?></td>
        <td class="center hidden-phone"><?php echo $item->id;?></td>
	</tr>
<?php } ?>
	  