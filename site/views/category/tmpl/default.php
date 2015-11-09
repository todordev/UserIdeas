<?php
/**
 * @package      UserIdeas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die; ?>
<div class="ui-items<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>

    <?php
    if ($this->params->get('show_cat_description', 0)) {
        echo $this->category->getDescription();
    }

    if ($this->params->get('show_cat_tags', 0) and count($this->tags) > 0) {
        $tagLayout = new JLayoutFile('joomla.content.tags');
        echo $tagLayout->render($this->tags);
        echo '<br />';
    }
    ?>

    <?php if ($this->params->get('items_display_button', 1)) { ?>
        <a href="<?php echo JRoute::_(UserIdeasHelperRoute::getFormRoute(0)); ?>" class="btn btn-default">
            <span class="fa fa-plus"></span>
            <?php echo JText::_('COM_USERIDEAS_POST_ITEM'); ?>
        </a>
    <?php } ?>

    <?php foreach ($this->items as $item) {

        // Load parameters.
        $registry = new Joomla\Registry\Registry;
        $registry->loadString($item->params);
        $item->params = $registry;

        $commentsNumber = 0;
        if (array_key_exists($item->id, $this->comments)) {
            $commentsNumber = (int)$this->comments[$item->id];
        }
        ?>
        <div class="media ui-item">
            <div class="ui-vote pull-left">
                <div class="ui-vote-counter" id="js-ui-vote-counter-<?php echo $item->id; ?>"><?php echo $item->votes; ?></div>
                <a class="btn btn-default btn-small ui-btn-vote js-ui-btn-vote" href="javascript: void(0);" data-id="<?php echo $item->id; ?>"><?php echo JText::_('COM_USERIDEAS_VOTE'); ?></a>
            </div>
            <div class="media-body">
                <?php if ($this->params->get('show_title', $item->params->get('show_title', 1))) {?>
                <h4 class="media-heading">
                    <a href="<?php echo JRoute::_(UserIdeasHelperRoute::getDetailsRoute($item->slug, $item->catid)); ?>">
                        <?php echo $this->escape($item->title); ?>
                    </a>
                </h4>
                <?php } ?>

                <?php
                if ($this->params->get('show_intro', $item->params->get('show_intro', 1))) { ?>
                    <?php echo JHtmlString::truncate($item->description, $this->params->get("items_description_length", 255), true, $this->params->get('items_description_html', 0)); ?>
                <?php } ?>
            </div>
            <?php
            $this->canEditResult = UserIdeasHelper::isValidOwner($this->userId, $item->user_id) and $this->canEdit;

            if (UserIdeasHelper::shouldDisplayFootbar($item->params, $item->params, false) or $this->canEditResult or $this->commentsEnabled) {
                echo '<div class="clearfix"></div>';
                $layoutData = new stdClass;
                $layoutData->item  = $item;
                $layoutData->socialProfiles  = $this->socialProfiles;
                $layoutData->integrationOptions  = $this->integrationOptions;
                $layoutData->commentsEnabled  = $this->commentsEnabled;
                $layoutData->canEditResult  = $this->canEditResult;
                $layoutData->params  = $this->params;
                $layoutData->commentsNumber  = $commentsNumber;

                $layout      = new JLayoutFile('footbar');
                echo $layout->render($layoutData);
            }?>
        </div>
    <?php } ?>

    <?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) { ?>
        <div class="pagination">
            <?php if ($this->params->def('show_pagination_results', 1)) { ?>
                <p class="counter pull-right"> <?php echo $this->pagination->getPagesCounter(); ?> </p>
            <?php } ?>
            <?php echo $this->pagination->getPagesLinks(); ?> </div>
    <?php } ?>
</div>