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
    <th width="1%" class="nowrap center hidden-phone">
		<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $this->listDirn, $this->listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
	</th>
	<th width="1%" class="hidden-phone">
		<?php echo JHtml::_('grid.checkall'); ?>
	</th>
	<th width="1%" style="min-width: 55px" class="nowrap center">
		<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $this->listDirn, $this->listOrder); ?>
	</th>
	<th class="title">
        <?php echo JHtml::_('grid.sort',  'COM_USERIDEAS_TITLE', 'a.title', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="center hidden-phone">
    	<?php echo JHtml::_('grid.sort',  'COM_USERIDEAS_VOTES', 'a.votes', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="center hidden-phone">
        <?php echo JHtml::_('grid.sort',  'COM_USERIDEAS_CREATED', 'a.record_date', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="center hidden-phone">
    	<?php echo JHtml::_('grid.sort',  'COM_USERIDEAS_USER', 'b.name', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="center hidden-phone">
        <?php echo JHtml::_('grid.sort',  'COM_USERIDEAS_CATEGORY', 'c.title', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="hidden-phone">
        <?php echo JHtml::_('grid.sort',  'COM_USERIDEAS_ITEM_STATUS', 'd.name', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="3%" class="center hidden-phone">
        <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
	  