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
    <th class="title">
    	<?php echo JText::_('COM_USERIDEAS_FILENAME'); ?>
    </th>
    <th width="10%" class="nowrap hidden-phone">
        <?php echo JHtml::_('searchtools.sort',  'COM_USERIDEAS_FILESIZE', 'a.filesize', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="nowrap hidden-phone">
        <?php echo JHtml::_('searchtools.sort',  'COM_USERIDEAS_MIMETYPE', 'a.mime', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="nowrap hidden-phone">
    	<?php echo JHtml::_('searchtools.sort',  'COM_USERIDEAS_CREATED', 'a.record_date', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="nowrap hidden-phone">
        <?php echo JHtml::_('searchtools.sort',  'COM_USERIDEAS_SOURCE', 'a.source', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="3%" class="center nowrap hidden-phone">
        <?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
