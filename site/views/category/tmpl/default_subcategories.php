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
$itemSpan = ($this->subcategoriesPerRow > 0) ? round(12 / $this->subcategoriesPerRow) : 4;
?>
<div id="ui-category-subcategories">
    <div class="row">
        <?php foreach ($this->subcategories as $category) {
            $itemsNumber = 0;
            $numberHtml  = $this->showCategoriesItemNumber ? ' (0)':'';
            $itemsNumber = 0;
            if ($this->showCategoriesItemNumber and array_key_exists($category->id, $this->numberOfItems)) {
                $numberHtml = ' ('.(int)$this->numberOfItems[$category->id].')';
            }

            $title = $this->escape($category->title);
            ?>
        <div class="col-sm-<?php echo $itemSpan; ?>">
            <div class="thumbnail height-300px">
                <?php if ($this->showCategoriesImages) {?>
                <a href="<?php echo JRoute::_(UserideasHelperRoute::getCategoryRoute($category->slug)); ?>">
                    <?php if ($category->params->get('image')) { ?>
                        <img src="<?php echo $category->params->get('image'); ?>" alt="<?php echo $title; ?>" />
                    <?php } else { ?>
                        <img src="<?php echo 'media/com_userideas/images/no_image_100x100.png'; ?>" alt="<?php echo $title; ?>" />
                    <?php } ?>
                </a>
                <?php } ?>

                <div class="caption ">
                    <?php if ($this->showCategoriesTitle) {?>
                    <h3>
                        <a href="<?php echo JRoute::_(UserideasHelperRoute::getCategoryRoute($category->slug)); ?>">
                            <?php echo $title; ?>
                        </a>
                    </h3>
                    <?php } ?>
                    <?php if ($this->showCategoriesDescription) { ?>
                        <p><?php echo $this->escape(strip_tags($category->description)); ?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>