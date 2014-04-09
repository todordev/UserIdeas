ALTER TABLE `#__uideas_emails` ADD `title` VARCHAR( 128 ) NOT NULL AFTER `id` ;
ALTER TABLE `#__uideas_statuses` ADD `params` VARCHAR( 512 ) NULL DEFAULT NULL AFTER `default` ;
ALTER TABLE `#__uideas_items` ADD `hits` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `votes` ;