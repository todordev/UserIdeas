ALTER TABLE `#__uideas_statuses`CHANGE `name` `title` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

CREATE TABLE IF NOT EXISTS `#__uideas_attachments` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `filename` varchar(32) NOT NULL,
  `filesize` int(10) UNSIGNED NOT NULL,
  `mime` varchar(64) NOT NULL,
  `attributes` varchar(1024) NOT NULL DEFAULT '{}',
  `record_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `item_id` int(11) NOT NULL,
  `comment_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `source` enum('item','comment') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uideas_attachments_iid` (`item_id`),
  KEY `idx_uideas_attachments_uid` (`user_id`),
  KEY `idx_uideas_attachments_cid` (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;