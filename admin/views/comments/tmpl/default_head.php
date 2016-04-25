<?php
/**
 * @package      Userideas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;?>
<tr>
	<th width="1%" class="hidden-phone">
		<?php echo JHtml::_('grid.checkall'); ?>
	</th>
	<th width="1%" style="min-width: 55px" class="nowrap center">
		<?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $this->listDirn, $this->listOrder); ?>
	</th>
    <th class="title">
    	<?php echo JText::_('COM_USERIDEAS_COMMENT'); ?>
    </th>
    <th width="10%" class="nowrap hidden-phone">
        <?php echo JHtml::_('searchtools.sort',  'COM_USERIDEAS_CREATED', 'a.record_date', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="nowrap hidden-phone">
    	<?php echo JHtml::_('searchtools.sort',  'COM_USERIDEAS_AUTHOR', 'c.name', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="3%" class="center nowrap hidden-phone">
        <?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
