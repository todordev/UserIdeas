<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;?>
<tr>
	<th width="1%" class="hidden-phone">
		<?php echo JHtml::_('grid.checkall'); ?>
	</th>
    <th class="title">
        <?php echo JHtml::_('grid.sort',  'COM_USERIDEAS_TITLE', 'a.title', $this->listDirn, $this->listOrder); ?>
    </th>
    <th class="nowrap hidden-phone">
        <?php echo JHtml::_('grid.sort',  'COM_USERIDEAS_SUBJECT', 'a.subject', $this->listDirn, $this->listOrder); ?>
    </th>
    <th  width="20%" class="center nowrap hidden-phone">
        <?php echo JHtml::_('grid.sort',  'COM_USERIDEAS_SENDER_NAME', 'a.sender_name', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="center nowrap hidden-phone">
        <?php echo JHtml::_('grid.sort',  'COM_USERIDEAS_SENDER_EMAIL', 'a.sender_email', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="3%" class="center nowrap hidden-phone">
        <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
	  