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

CREATE TABLE IF NOT EXISTS `#__uideas_comments` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `comment` text,
  `record_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `item_id` smallint(5) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ui_comment_record_date` (`record_date`),
  KEY `idx_ui_comment_item_published` (`item_id`,`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__uideas_items` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `description` text,
  `votes` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `hits` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
  `record_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ordering` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `params` varchar(2048) DEFAULT NULL,
  `status_id` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `catid` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `asset_id` int(10) NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `access` int(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uiitems_access` (`access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__uideas_statuses` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `default` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `params` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__uideas_votes` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `item_id` smallint(5) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `hash` varchar(32) DEFAULT NULL COMMENT 'Anonymous users hash.',
  `votes` tinyint(3) UNSIGNED NOT NULL,
  `record_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ui_history_record_date` (`record_date`),
  KEY `idx_ui_history_item_user` (`item_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
