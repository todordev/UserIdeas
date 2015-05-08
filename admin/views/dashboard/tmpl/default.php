<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php if(!empty( $this->sidebar)): ?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
    <div id="j-main-container">
    <?php endif;?>
        <div class="span8">

            <!--  Row 1 -->
            <div class="row-fluid dashboard-stats">
                <div class="span6">
                    <h3 class="latest-started">
                        <?php echo JText::_("COM_USERIDEAS_LATEST");?>
                    </h3>
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
                                    <a href="<?php echo JRoute::_("index.php?option=com_userideas&view=items&filter_search=id:".(int)$this->latest[$i]["id"]);?>" >
                                        <?php echo JHtmlString::truncate(strip_tags($this->latest[$i]["title"]), 53); ?>
                                    </a>
                                </td>
                                <td style="min-width: 100px;">
                                    <?php echo JHtml::_('date', $this->latest[$i]["record_date"], JText::_('DATE_FORMAT_LC3'));?>
                                </td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
                <div class="span6">
                    <h3 class="popular">
                        <?php echo JText::_("COM_USERIDEAS_POPULAR");?>
                    </h3>
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
                                    <a href="<?php echo JRoute::_("index.php?option=com_userideas&view=items&filter_search=id:".(int)$this->popular[$i]["id"]);?>" >
                                        <?php echo JHtmlString::truncate(strip_tags($this->popular[$i]["title"]), 53); ?>
                                    </a>
                                </td>
                                <td class="center">
                                    <?php echo (int)$this->popular[$i]["hits"];?>
                                </td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /Row 1 -->
            <!--  Row 2 -->
            <div class="row-fluid dashboard-stats">
                <div class="span6">
                    <h3 class="mostfunded">
                        <?php echo JText::_("COM_USERIDEAS_MOST_VOTED");?>
                    </h3>
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
                                    <a href="<?php echo JRoute::_("index.php?option=com_userideas&view=items&filter_search=id:".(int)$this->mostVoted[$i]["id"]);?>" >
                                        <?php echo JHtmlString::truncate(strip_tags($this->mostVoted[$i]["title"]), 53); ?>
                                    </a>
                                </td>
                                <td class="center">
                                    <?php echo $this->mostVoted[$i]["votes"];?>
                                </td>
                            </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
                <div class="span6">
                    <h3 class="basic-stats">
                        <?php echo JText::_("COM_USERIDEAS_BASIC_INFORMATION");?>
                    </h3>
                    <table class="table">
                        <tbody>
                        <tr>
                            <th><?php echo JText::_("COM_USERIDEAS_TOTAL_IDEAS");?></th>
                            <td><?php echo $this->totalItems; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo JText::_("COM_USERIDEAS_TOTAL_VOTES");?></th>
                            <td><?php echo $this->totalVotes; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo JText::_("COM_USERIDEAS_TOTAL_COMMENTS");?></th>
                            <td><?php echo $this->totalComments; ?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /Row 2 -->
        </div>
	
	<div class="span4">
        <a href="http://itprism.com/free-joomla-extensions/ecommerce-gamification/feedbacks-ideas-suggestions" target="_blank"><img src="../media/com_userideas/images/logo.png" alt="<?php echo JText::_("COM_USERIDEAS");?>" /></a>
        <a href="http://itprism.com" target="_blank" title="<?php echo JText::_("COM_USERIDEAS_ITPRISM_PRODUCT");?>"><img src="../media/com_userideas/images/product_of_itprism.png" alt="<?php echo JText::_("COM_USERIDEAS_PRODUCT");?>" /></a>
        <p><?php echo JText::_("COM_USERIDEAS_YOUR_VOTE"); ?></p>
        <p><?php echo JText::_("COM_USERIDEAS_SPONSORSHIP"); ?></p>
        <p><?php echo JText::_("COM_USERIDEAS_SUBSCRIPTION"); ?></p>
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td><?php echo JText::_("COM_USERIDEAS_INSTALLED_VERSION");?></td>
                    <td><?php echo $this->version->getMediumVersion();?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_("COM_USERIDEAS_RELEASE_DATE");?></td>
                    <td><?php echo $this->version->releaseDate?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_("COM_USERIDEAS_ITPRISM_LIBRARY_VERSION");?></td>
                    <td><?php echo $this->itprismVersion;?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_("COM_USERIDEAS_COPYRIGHT");?></td>
                    <td><?php echo $this->version->copyright;?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_("COM_USERIDEAS_LICENSE");?></td>
                    <td><?php echo $this->version->license;?></td>
                </tr>
            </tbody>
        </table>
	</div>
</div>