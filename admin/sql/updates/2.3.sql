ALTER TABLE `#__uideas_items` CHANGE `published` `published` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `#__uideas_items` ADD `asset_id` INT(10) NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.' AFTER `user_id`;
ALTER TABLE `#__uideas_items` ADD `access` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `asset_id`, ADD INDEX `idx_uiitems_access` (`access`);

UPDATE `#__uideas_items` SET `access` = 1;