<?php
/**
 * @package      ITPrism Components
 * @subpackage   UserIdeas
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * UserIdeas is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;?>
<div class="uf-items<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>

	<?php 
	if($this->params->get("items_display_description", 0)) {
		echo $this->category->description;
	}
	?>
	    
	<?php if($this->params->get("items_display_button", 1)) {?>
    <a href="<?php echo JRoute::_( UserIdeasHelperRoute::getFormRoute(0));?>" class="btn">
    	<i class="icon-plus-sign"></i>
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
    <div class="media uf-item">
    	<div class="uf-vote pull-left">
    		<div class="uf-vote-counter" id="uf-vote-counter-<?php echo $item->id; ?>"><?php echo $item->votes; ?></div>
    		<a class="btn btn-small uf-btn-vote" href="javascript: void(0);" data-id="<?php echo $item->id; ?>"><?php echo JText::_("COM_USERIDEAS_VOTE"); ?></a>
    	</div>
        <div class="media-body">
        	<h4 class="media-heading">
        		<a href="<?php echo JRoute::_(UserIdeasHelperRoute::getDetailsRoute($item->slug, $item->catid));?>" >
        	        <?php echo $this->escape($item->title);?>
        	    </a>
    	    </h4>
         	<p><?php echo $this->escape($item->description);?></p>
        </div>
        <div class="clearfix"></div>
        <div class="well well-small">
        	<div class="pull-left">
            <?php 
            $date = JHtml::_('date', $item->record_date, JText::_('DATE_FORMAT_LC3'));
            echo JText::sprintf("COM_USERIDEAS_PUBLISHED_BY_ON", $item->name, $date);
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