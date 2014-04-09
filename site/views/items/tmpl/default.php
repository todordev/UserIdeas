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
<div class="ui-items<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>

	<?php if($this->params->get("items_display_button", 1)) {?>
    <a href="<?php echo JRoute::_(UserIdeasHelperRoute::getFormRoute(0));?>" class="btn">
    	<i class="icon-plus"></i>
        <?php echo JText::_("COM_USERIDEAS_POST_ITEM");?>
    </a>
    <?php }?>
    
	<?php foreach($this->items as $item) { 
        if(isset($this->comments[$item->id])) {
            $commentsNumber = (int)$this->comments[$item->id];
        } else {
            $commentsNumber = 0;
        } 
    ?>
    <div class="media ui-item">
    	<div class="ui-vote pull-left">
    		<div class="ui-vote-counter" id="js-ui-vote-counter-<?php echo $item->id; ?>"><?php echo $item->votes; ?></div>
    		<a class="btn btn-small ui-btn-vote js-ui-btn-vote" href="javascript: void(0);" data-id="<?php echo $item->id; ?>"><?php echo JText::_("COM_USERIDEAS_VOTE"); ?></a>
    	</div>
        <div class="media-body">
        	<h4 class="media-heading">
        		<a href="<?php echo JRoute::_(UserIdeasHelperRoute::getDetailsRoute($item->slug, $item->catid));?>" >
        	        <?php echo $this->escape($item->title);?>
        	    </a>
    	    </h4>
         	<p><?php echo $item->description;?></p>
        </div>
        <div class="clearfix"></div>
        <div class="well well-small">
        	<div class="pull-left">
            <?php 
            
            $profile = JHtml::_("userideas.profile", $this->socialProfiles, $item->user_id);
            
            echo JHtml::_("userideas.publishedByOn", $item->name, $item->record_date, $profile);
            echo JHtml::_("userideas.category", $item->category, $item->catslug);
            echo JHtml::_("userideas.status", $item->status);
            ?>
            </div>
            <div class="pull-right">
            	<a class="btn btn-small" href="<?php echo JRoute::_(UserIdeasHelperRoute::getDetailsRoute($item->slug, $item->catid))."#comments";?>" >
            		<i class="icon-comment"></i>
            		<?php echo JText::_("COM_USERIDEAS_COMMENTS");?>
            		<?php echo "( ".$commentsNumber." )";?>
            	</a> 
            	
            	<?php if($this->userId == $item->user_id){?>
            	<a class="btn btn-small" href="<?php echo JRoute::_(UserIdeasHelperRoute::getFormRoute($item->id));?>" >
            		<i class="icon-edit"></i>
            		<?php echo JText::_("COM_USERIDEAS_EDIT");?>
            	</a>
            	<?php }?>
            </div>
        </div>
    </div>
    <?php }?>
    
    <div class="pagination">

        <?php if ($this->params->def('show_pagination_results', 1)) : ?>
            <p class="counter">
                <?php echo $this->pagination->getPagesCounter(); ?>
            </p>
        <?php endif; ?>
    
        <?php echo $this->pagination->getPagesLinks(); ?>
    </div>

</div>
<?php echo $this->version->backlink;?>