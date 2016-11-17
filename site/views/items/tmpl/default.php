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
<div class="ui-items<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>

	<?php if($this->params->get('show_post_button') and $this->canCreate) {?>
    <a href="<?php echo JRoute::_(UserideasHelperRoute::getFormRoute(0));?>" class="btn btn-default mb-10" role="button">
    	<span class="fa fa-plus"></span>
        <?php echo JText::_('COM_USERIDEAS_POST_ITEM');?>
    </a>
    <?php }?>
    <?php // Display dynamically generated code from plugins.
    if ($this->event->onContentBeforeDisplay) {
        echo $this->event->onContentBeforeDisplay;
    } ?>
	<?php foreach($this->items as $item) {
        $commentsNumber = 0;
        if (is_array($this->comments) and array_key_exists($item->id, $this->comments)) {
            $commentsNumber = (int)$this->comments[$item->id];
        }
    ?>
    <div class="media ui-item">
    	<div class="ui-vote pull-left">
    		<div class="ui-vote-counter js-ui-item-counter" id="js-ui-vote-counter-<?php echo $item->id; ?>" data-id="<?php echo $item->id; ?>"><?php echo $item->votes; ?></div>
    		<a class="btn btn-primary ui-btn-vote js-ui-btn-vote" href="javascript: void(0);" data-id="<?php echo $item->id; ?>"><?php echo JText::_('COM_USERIDEAS_VOTE'); ?></a>
    	</div>
        <div class="media-body">
            <?php if ($this->params->get('show_title', $item->params->get('show_title'))) {?>
        	<h4 class="media-heading">
        		<a href="<?php echo JRoute::_(UserideasHelperRoute::getDetailsRoute($item->slug, $item->catid));?>" >
        	        <?php echo $this->escape($item->title);?>
        	    </a>
    	    </h4>
            <?php } ?>

            <?php if ($this->params->get('show_intro', $item->params->get('show_intro'))) { ?>
         	<?php echo JHtmlString::truncate($item->description, $this->params->get('intro_length'), true, $this->params->get('allow_html'));?>
            <?php } ?>
        </div>
        <?php
        if (UserideasHelper::shouldDisplayFootbar($item->params, $item->params, false) or $this->commentsEnabled) {
            echo '<div class="clearfix"></div>';
            $layoutData = new stdClass;
            $layoutData->item                = $item;
            $layoutData->socialProfiles      = $this->socialProfiles;
            $layoutData->integrationOptions  = $this->integrationOptions;
            $layoutData->commentsEnabled     = $this->commentsEnabled;
            $layoutData->params              = $this->params;
            $layoutData->commentsNumber      = $commentsNumber;

            $layout      = new JLayoutFile('footbar');
            echo $layout->render($layoutData);
        }?>
    </div>
    <?php }?>

    <?php // Display dynamically generated code from plugins.
    if ($this->event->onContentAfterDisplay) {
        echo $this->event->onContentAfterDisplay;
    } ?>

    <?php if (($this->params->def('show_pagination') == 1 or ($this->params->get('show_pagination') == 2)) and ($this->pagination->get('pages.total') > 1)) { ?>
        <div class="pagination">
            <?php if ($this->params->def('show_pagination_results', 1)) { ?>
                <p class="counter pull-right"> <?php echo $this->pagination->getPagesCounter(); ?> </p>
            <?php } ?>
            <?php echo $this->pagination->getPagesLinks(); ?> </div>
    <?php } ?>

</div>