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
<tr>
    <th width="1%" class="hidden-phone">
        <?php echo JHtml::_('grid.checkall'); ?>
    </th>
    <th width="20%" class="title nowrap">
	    <?php echo JHtml::_('grid.sort',  'COM_USERIDEAS_USER', 'c.name', $this->listDirn, $this->listOrder); ?>
	</th>
    <th width="40%" class="nowrap hidden-phone" >
	     <?php echo JHtml::_('grid.sort',  'COM_USERIDEAS_ITEM', 'b.title', $this->listDirn, $this->listOrder); ?>
	</th>
    <th width="10%" class="nowrap center">
        <?php echo JText::_('COM_USERIDEAS_VOTES'); ?>
    </th>
    <th class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort',  'COM_USERIDEAS_DATE', 'a.record_date', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="1%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
	  