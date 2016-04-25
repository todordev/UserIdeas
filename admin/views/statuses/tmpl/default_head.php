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
    <th class="nowrap">
		<?php echo JHtml::_('grid.sort',  'COM_USERIDEAS_NAME', 'a.name', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="nowrap">
        <?php echo JText::_("COM_USERIDEAS_STYLE_CLASS"); ?>
    </th>
	<th width="3%" style="min-width: 55px" class="nowrap center">
		<?php echo JText::_("COM_USERIDEAS_DEFAULT"); ?>
	</th>
    <th width="3%" class="center nowrap hidden-phone">
        <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
	  