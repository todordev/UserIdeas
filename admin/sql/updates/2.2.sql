ALTER TABLE `#__uideas_items` ADD `params` VARCHAR(2048) NULL DEFAULT NULL AFTER `published`;
ALTER TABLE `#__uideas_statuses` ENGINE = MYISAM;
DROP TABLE IF EXISTS `#__uideas_emails`;
