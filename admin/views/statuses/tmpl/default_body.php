<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach ($this->items as $i => $item) {
    $itemParams = (!empty($item->params)) ? json_decode($item->params, true) : array();
    $styleClass = JArrayHelper::getValue($itemParams, "style_class");
    ?>
	<tr class="row<?php echo $i % 2;?>">
		<td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="title">
			<a href="<?php echo JRoute::_("index.php?option=com_userideas&view=status&layout=edit&id=".$item->id); ?>" >
		        <?php echo $this->escape($item->name); ?>
	        </a>
	    </td>
        <td class="nowrap">
            <?php echo JHtml::_('userideas.styleClass', $styleClass);?>
        </td>
		<td class="nowrap center">
		  <?php echo JHtml::_('jgrid.isdefault', $item->default, $i, 'statuses.');?>
        </td>
        <td class="center hidden-phone"><?php echo $item->id;?></td>
	</tr>
<?php } ?>
	  