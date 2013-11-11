<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2013 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
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
	<th width="1%" style="min-width: 55px" class="nowrap center">
		<?php echo JText::_("COM_USERIDEAS_DEFAULT"); ?>
	</th>
    <th width="3%" class="center nowrap hidden-phone">
        <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
	  