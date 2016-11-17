<?php
/**
 * @package      Userideas
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
/**
 * @var stdClass $category
 */
?>
<div class="ui-items<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>

    <?php // Display dynamically generated code from plugins.
    if ($this->event->onContentBeforeDisplay) {
        echo $this->event->onContentBeforeDisplay;
    } ?>

    <?php echo $this->loadTemplate($this->templateLayout); ?>

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