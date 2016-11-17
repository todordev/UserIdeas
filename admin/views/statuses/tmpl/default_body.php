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
<?php foreach ($this->items as $i => $item) {
    $itemParams = !empty($item->params) ? json_decode($item->params, true) : array();
    $styleClass = Joomla\Utilities\ArrayHelper::getValue($itemParams, 'style_class');
    ?>
	<tr class="row<?php echo $i % 2;?>">
		<td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="title">
            <?php if ($this->canDo->get('core.edit')) { ?>
                <a href="<?php echo JRoute::_('index.php?option=com_userideas&view=status&layout=edit&id='.$item->id); ?>" >
                    <?php echo $this->escape($item->title); ?>
                </a>
            <?php } else { ?>
                <?php echo $this->escape($item->title); ?>
            <?php } ?>
	    </td>
        <td class="nowrap">
            <?php echo JHtml::_('userideas.styleClass', $styleClass);?>
        </td>
		<td class="nowrap center">
		  <?php echo JHtml::_('jgrid.isdefault', $item->default, $i, 'statuses.', $this->canDo->get('core.edit.state'));?>
        </td>
        <td class="center hidden-phone"><?php echo $item->id;?></td>
	</tr>
<?php } ?>
