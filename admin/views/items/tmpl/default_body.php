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
$user   = JFactory::getUser();
$userId = $user->get('id');
?>
<?php foreach ($this->items as $i => $item) {
    $ordering = ($this->listOrder === 'a.ordering');

    $accessAlias = 'com_userideas.item.' . $item->id;

    $canCreate  = $user->authorise('core.create', 'com_userideas.category.' . $item->catid);
    $canEdit    = $user->authorise('core.edit', $accessAlias);
    $canEditOwn = $user->authorise('core.edit.own', $accessAlias) and (int)$item->user_id === (int)$userId;
    $canChange  = $user->authorise('core.edit.state', $accessAlias);

    $iconClass  = '';
    if (!$canChange) {
        $iconClass = ' inactive';
    } elseif (!$this->saveOrder) {
        $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
    }
    ?>
    <tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid ?>">
        <td class="order nowrap center hidden-phone">
    		<span class="sortable-handler <?php echo $iconClass ?>">
    			<i class="icon-menu"></i>
    		</span>
            <?php if ($canChange and $this->saveOrder) { ?>
                <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order "/>
            <?php } ?>
        </td>
        <td class="hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="center">
            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'items.', $canChange); ?>
        </td>
        <td class="has-context">
            <?php if ($canEdit || $canEditOwn) { ?>
                <a href="<?php echo JRoute::_('index.php?option=com_userideas&view=item&layout=edit&id=' . $item->id); ?>"><?php echo $this->escape($item->title); ?></a>
            <?php } else { ?>
                <span title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', $this->escape($item->alias)); ?>"><?php echo $this->escape($item->title); ?></span>
            <?php } ?>
            <div class="small">
                <?php echo JText::sprintf('COM_USERIDEAS_ALIAS_S', $this->escape($item->alias)); ?>
            </div>
            <div class="small">
                <?php echo JHtml::_('userideas.categoryFilter', $this->escape($item->category), $item->catid); ?>
            </div>
            <div class="small">
                <?php echo JHtml::_('userideas.attachmentNumber', $item, 'item'); ?>
            </div>
            <div class="small">
                <?php echo JHtml::_('userideas.commentsNumber', $item); ?>
            </div>
        </td>
        <td class="center hidden-phone"><?php echo (int)$item->votes; ?></td>
        <td class="center hidden-phone"><?php echo (int)$item->hits; ?></td>
        <td class="hidden-phone">
            <?php echo JHtml::_('date', $item->record_date, JText::_('DATE_FORMAT_LC3')); ?>
        </td>
        <td class="hidden-phone"><?php echo ($item->user) ?: JText::_('COM_USERIDEAS_ANONYMOUS'); ?></td>
        <td class="hidden-phone"><?php echo JHtml::_('userideas.status', $item->status, false); ?></td>
        <td class="small hidden-phone">
            <?php echo $this->escape($item->access_level); ?>
        </td>
        <td class="center hidden-phone"><?php echo $item->id; ?></td>
    </tr>
<?php } ?>
