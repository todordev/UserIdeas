<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
?>

<?php 
if($this->item->event->beforeDisplayContent) {
	echo $this->item->event->beforeDisplayContent;
}?>
<div class="row-fluid">
	<div class="span12">
		<div class="media ui-item">
        	<div class="ui-vote pull-left">
        		<div class="ui-vote-counter" id="js-ui-vote-counter-<?php echo $this->item->id; ?>"><?php echo $this->item->votes; ?></div>
        		<a class="btn btn-small ui-btn-vote js-ui-btn-vote" href="javascript: void(0);" data-id="<?php echo $this->item->id; ?>"><?php echo JText::_("COM_USERIDEAS_VOTE"); ?></a>
        	</div>
            <div class="media-body">
            	<h4 class="media-heading">
        	        <?php echo $this->escape($this->item->title);?>
        	    </h4>
             	<p><?php echo $this->item->description;?></p>
            </div>
            <div class="clearfix"></div>
            <div class="well well-small">
            	<div class="pull-left">
                <?php 
                
                $profile = JHtml::_("userideas.profile", $this->socialProfiles, $this->item->user_id);
                
                echo JHtml::_("userideas.publishedByOn", $this->item->name, $this->item->record_date, $profile);
                echo JHtml::_("userideas.category", $this->item->category, $this->item->catslug);
                echo JHtml::_("userideas.status", $this->item->status);
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

<?php 
if(!empty($this->item->event->onContentAfterDisplay)) {
    echo $this->item->event->onContentAfterDisplay; 
}?>

<div class="row-fluid" id="comments">
	<div class="span12">
	
        <h4><?php echo JText::_("COM_USERIDEAS_COMMENTS");?></h4>
        <hr />
        <?php foreach($this->comments as $comment) {?>
        <div class="media ui-comment">
        	<?php
        	    
        	    // Get the profile and avatar.
        	    $profile = JHtml::_("userideas.profile", $this->socialProfiles, $comment->user_id, "javascript: void(0);");
        	    $avatar  = JHtml::_("userideas.avatar", $this->socialProfiles, $comment->user_id, "media/com_userideas/images/no-profile.png", $options=array("avatar_size" => $this->avatarsSize));
        	    
            	if(!empty($avatar)) {?>
            	<a class="pull-left" href="<?php echo $profile; ?>" rel="nofollow">
            	    <img class="media-object" src="<?php echo $avatar;?>" />
        		</a>
    		<?php } ?>
            <div class="media-body pull-left">
                <div class="media">
                	<?php echo $this->escape($comment->comment);?>
                </div>
            </div>
            
            <div class="clearfix"></div>
            <div class="well well-small">
            	<div class="pull-left">
                <?php 
                $profile = JHtml::_("userideas.profile", $this->socialProfiles, $comment->user_id);
                echo JHtml::_("userideas.publishedByOn", $comment->author, $comment->record_date, $profile);
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
        
        <div class="clearfix"></div>
        
        <?php if(!$this->userId) {?>
        <a href="<?php echo JRoute::_("index.php?option=com_users&view=login&return=".urlencode(base64_encode($this->item->link)));?>" class="btn margin-top-10" ><?php echo JText::_("COM_USERIDEAS_LOGIN_AND_COMMENT")?></a>
        <?php } else {?>
        <form action="<?php echo JRoute::_('index.php?option=com_userideas'); ?>" method="post" name="commentForm" id="ui-comment-form" class="form-validate">
            
            <?php echo $this->form->getLabel('comment'); ?>
            <?php echo $this->form->getInput('comment'); ?>
            
            <?php echo $this->form->getInput('id'); ?>
            <?php echo $this->form->getInput('item_id'); ?>
            
            <input type="hidden" name="task" value="comment.save" />
            <?php echo JHtml::_('form.token'); ?>
            
            <div class="clearfix"></div>
            <button type="submit" class="btn btn-primary" <?php echo $this->disabledButton;?>>
            	<i class="icon-ok icon-white"></i>
                <?php echo JText::_("COM_USERIDEAS_SUBMIT")?>
            </button>
        </form>
        <?php } ?>
        
    </div>
</div>
<?php echo $this->version->backlink;?>