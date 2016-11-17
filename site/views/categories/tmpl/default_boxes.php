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
?>
<div class="row">
    <?php
    foreach ($this->items as $category) {
        $numberHtml  = $this->showItemsNumber ? ' (0)':'';
        $itemsNumber = 0;
        if ($this->showItemsNumber and array_key_exists($category->id, $this->numberOfItems)) {
            $numberHtml = ' ('.(int)$this->numberOfItems[$category->id].')';
        }

        $title       = htmlspecialchars($category->title, ENT_COMPAT, 'UTF-8');
        ?>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <div class="thumbnail height-300px">
                <?php if ($this->showImages) {?>
                    <a href="<?php echo JRoute::_(UserideasHelperRoute::getCategoryRoute($category->slug));?>">
                        <img src="<?php echo $category->params->get('image') ?: 'media/com_userideas/images/no_image_100x100.png';?>" alt="<?php echo $title; ?>">
                    </a>
                <?php } ?>
                <div class="caption">
                    <?php if ($this->showTitle) {
                        echo '<h3><a href="'.JRoute::_(UserideasHelperRoute::getCategoryRoute($category->slug)).'">'.$title.'</a>'.$numberHtml.'</h3>';
                    }
                    if ($this->showDescription) {
                        $description = htmlspecialchars(strip_tags($category->description), ENT_COMPAT, 'UTF-8');
                        echo '<p>' . $description . '</p>';
                    } ?>

                    <?php if ($this->showSubcategories) {?>
                        <a href="<?php echo JRoute::_(UserideasHelperRoute::getCategoriesRoute($category->id));?>">
                            <?php echo JText::_('COM_USERIDEAS_SHOW_SUBCATEGORIES_'); ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>