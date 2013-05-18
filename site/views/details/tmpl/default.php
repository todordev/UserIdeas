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
defined('_JEXEC') or die;
?>
<div class="row-fluid">

	<div class="span12">
	
		<div class="media uf-item">
        	<div class="uf-vote pull-left">
        		<div class="uf-vote-counter" id="uf-vote-counter-<?php echo $this->item->id; ?>"><?php echo $this->item->votes; ?></div>
        		<a class="btn btn-small uf-btn-vote" href="javascript: void(0);" data-id="<?php echo $this->item->id; ?>"><?php echo JText::_("COM_USERIDEAS_VOTE"); ?></a>
        	</div>
            <div class="media-body">
            	<h4 class="media-heading">
        	        <?php echo $this->escape($this->item->title);?>
        	    </h4>
             	<p><?php echo $this->escape($this->item->description);?></p>
            </div>
            <div class="clearfix"></div>
            <div class="well well-small">
            	<div class="pull-left">
                <?php 
                $date = JHtml::_('date', $this->item->record_date, JText::_('DATE_FORMAT_LC3'));
                echo JText::sprintf("COM_USERIDEAS_PUBLISHED_BY_ON", $this->item->name, $date);
                ?>
                </div>
                <div class="pull-right">
                	<?php if($this->userId == $this->item->user_id){?>
                	<a class="btn btn-small" href="<?php echo JRoute::_(UserIdeasHelperRoute::getFormRoute($this->item->id));?>" >
                		<i class="icon-edit"></i>
                		<?php echo JText::_("COM_USERIDEAS_EDIT");?>
                	</a>
                	<?php }?>
                </div>
            </div>
        </div>
        
	</div>
    
</div>

<div class="row-fluid" id="comments">
	<div class="span12">
        <form action="<?php echo JRoute::_('index.php?option=com_userideas'); ?>" method="post" name="commentForm" id="ui-comment-form" class="form-validate">
            
            <?php echo $this->form->getLabel('comment'); ?>
            <?php echo $this->form->getInput('comment'); ?>
            
            <?php echo $this->form->getInput('id'); ?>
            <?php echo $this->form->getInput('item_id'); ?>
            
            <input type="hidden" name="task" value="comment.save" />
            <?php echo JHtml::_('form.token'); ?>
            
            <div class="clearfix"></div>
            <button type="submit" class="button button-large margin-tb-15px" <?php echo $this->disabledButton;?>>
            	<i class="icon-ok icon-white"></i>
                <?php echo JText::_("COM_USERIDEAS_SEND")?>
            </button>
        </form>
        
        <div class="clearfix"></div>
        <h4><?php echo JText::_("COM_USERIDEAS_COMMENTS");?></h4>
        <hr />
        <?php foreach($this->comments as $comment) {?>
        <div class="media ui-comment">
        	<?php
        	    
        	    // Check for integration
        	    if(!$this->socialPlatform) {
        	        $profile = "javascript: void(0);";
        	        $avatar  = "media/com_userideas/images/no-profile.png";
        	    } else {
        	        $user    = JFactory::getUser($comment->user_id);
            	    $profile = JHtml::_("userideas.profile", $this->socialPlatform, $user, "javascript: void(0);"); 
                	$avatar  = JHtml::_("userideas.avatar", $this->socialPlatform, $user, "media/com_userideas/images/no-profile.png");
        	    }
        	    
            	if(!empty($avatar)) {?>
            	<a class="pull-left" href="<?php echo $profile; ?>">
            	    <img class="media-object" src="<?php echo $avatar;?>" />
        		</a>
    		<?php } ?>
            <div class="media-body">
                <div class="media">
                	<?php echo $this->escape($comment->comment);?>
                </div>
            </div>
            
            <div class="clearfix"></div>
            <div class="well well-small">
            	<div class="pull-left">
                <?php 
                $date = JHtml::_('date', $comment->record_date, JText::_('DATE_FORMAT_LC3'));
                echo JText::sprintf("COM_USERIDEAS_PUBLISHED_BY_ON", $comment->author, $date);
                ?>
                </div>
                <div class="pull-right">
                	<?php if($this->userId == $comment->user_id){?>
                	<a class="btn btn-small" href="<?php echo JRoute::_(UserIdeasHelperRoute::getDetailsRoute($this->item->slug, $this->item->catid)."&comment_id=".(int)$comment->id);?>" >
                		<i class="icon-edit"></i>
                		<?php echo JText::_("COM_USERIDEAS_EDIT");?>
                	</a>
                	<?php }?>
                </div>
            </div>
            
        </div>
        <?php }?>
    </div>
</div>
<?php echo $this->version->backlink;?>