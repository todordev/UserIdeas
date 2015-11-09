<?php
/**
 * @package      MagicGallery
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach ($this->form->getGroup('params') as $field) { ?>
    <div class="control-group">
        <?php if (!$field->hidden) : ?>
            <div class="control-label">
                <?php echo $field->label; ?>
            </div>
        <?php endif; ?>
        <div class="controls">
            <?php echo $field->input; ?>
        </div>
    </div>
<?php } ?>