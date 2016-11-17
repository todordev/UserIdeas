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
if (count($this->subcategories) < $this->subcategoriesPerRow) {
    $this->subcategoriesPerRow = count($this->subcategories);
}
?>
<div id="ui-category-subcategories">
    <div class="row">
        <?php
        $i = 0;
        foreach ($this->subcategories as $category) {
            $i++;
            $itemsNumber = 0;
            $numberHtml  = $this->showCategoriesItemNumber ? ' (0)':'';
            $itemsNumber = 0;
            if ($this->showCategoriesItemNumber and array_key_exists($category->id, $this->numberOfItems)) {
                $numberHtml = ' ('.(int)$this->numberOfItems[$category->id].')';
            }
        ?>
        <?php if ($i === 1) {?>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <ul class="ui-cat-subcatnav">
            <?php } ?>
                <li class="ui-cat-subcatnav-li">
                    <a href="<?php echo JRoute::_(UserideasHelperRoute::getCategoryRoute($category->slug)); ?>">
                        <?php echo $this->escape($category->title); ?>
                    </a>
                </li>
            <?php
            if ($i === $this->subcategoriesPerRow) {
                $i = 0;
            ?>
            </ul>
        </div>
        <?php } ?>
        <?php } ?>
    </div>
</div>