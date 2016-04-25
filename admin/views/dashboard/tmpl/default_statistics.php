<?php
/**
 * @package      Crowdfunding
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="span8">
    <!--  Row 1 -->
    <div class="row-fluid">
        <div class="span6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="icon-list"></i>
                    <?php echo JText::_('COM_USERIDEAS_LATEST'); ?>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo JText::_("COM_USERIDEAS_ITEM");?></th>
                            <th class="nowrap" style="max-width: 50px;"><?php echo JText::_("COM_USERIDEAS_CREATED_ON");?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php for($i = 0, $max = count($this->latest); $i < $max; $i++) {?>
                            <tr>
                                <td><?php echo $i + 1;?></td>
                                <td>
                                    <a href="<?php echo JRoute::_("index.php?option=com_userideas&view=items&filter_search=id:".(int)$this->latest[$i]->id);?>" >
                                        <?php echo JHtmlString::truncate(strip_tags($this->latest[$i]->title), 53); ?>
                                    </a>
                                </td>
                                <td style="min-width: 100px;">
                                    <?php echo JHtml::_('date', $this->latest[$i]->record_date, JText::_('DATE_FORMAT_LC3'));?>
                                </td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <div class="span6">
            <div class="panel panel-default">
                <div class="panel-heading bgcolor-yellow-light">
                    <i class="icon-list"></i>
                    <?php echo JText::_('COM_USERIDEAS_POPULAR'); ?>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo JText::_("COM_USERIDEAS_ITEM");?></th>
                            <th class="center nowrap" style="max-width: 50px;"><?php echo JText::_("COM_USERIDEAS_HITS");?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php for($i = 0, $max = count($this->popular); $i < $max; $i++) {?>
                            <tr>
                                <td><?php echo $i + 1;?></td>
                                <td>
                                    <a href="<?php echo JRoute::_("index.php?option=com_userideas&view=items&filter_search=id:".(int)$this->popular[$i]->id);?>" >
                                        <?php echo JHtmlString::truncate(strip_tags($this->popular[$i]->title), 53); ?>
                                    </a>
                                </td>
                                <td class="center">
                                    <?php echo (int)$this->popular[$i]->hits;?>
                                </td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /Row 1 -->

    <!--  Row 2 -->
    <div class="row-fluid">
        <div class="span6">
            <div class="panel panel-default">
                <div class="panel-heading bgcolor-violet-light">
                    <i class="icon-list"></i>
                    <?php echo JText::_('COM_USERIDEAS_MOST_VOTED'); ?>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th><?php echo JText::_("COM_USERIDEAS_ITEM");?></th>
                            <th class="center nowrap" style="max-width: 50px;"><?php echo JText::_("COM_USERIDEAS_VOTES");?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php for($i = 0, $max = count($this->mostVoted); $i < $max; $i++) {?>
                            <tr>
                                <td><?php echo $i + 1;?></td>
                                <td>
                                    <a href="<?php echo JRoute::_("index.php?option=com_userideas&view=items&filter_search=id:".(int)$this->mostVoted[$i]->id);?>" >
                                        <?php echo JHtmlString::truncate(strip_tags($this->mostVoted[$i]->title), 53); ?>
                                    </a>
                                </td>
                                <td class="center">
                                    <?php echo $this->mostVoted[$i]->votes;?>
                                </td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="span6">
            <div class="panel panel-default">
                <div class="panel-heading bgcolor-blue-light">
                    <i class="icon-list"></i>
                    <?php echo JText::_('COM_USERIDEAS_BASIC_INFORMATION'); ?>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <tbody>
                        <tr>
                            <th><?php echo JText::_('COM_USERIDEAS_TOTAL_IDEAS');?></th>
                            <td><?php echo $this->totalItems; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo JText::_('COM_USERIDEAS_TOTAL_VOTES');?></th>
                            <td><?php echo $this->totalVotes; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo JText::_('COM_USERIDEAS_TOTAL_COMMENTS');?></th>
                            <td><?php echo $this->totalComments; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /Row 2 -->
</div>