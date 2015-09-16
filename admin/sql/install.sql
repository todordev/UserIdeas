CREATE TABLE IF NOT EXISTS `#__uideas_comments` (
  `id` int(10) unsigned NOT NULL,
  `comment` text,
  `record_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `item_id` smallint(5) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__uideas_items` (
  `id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL,
  `description` text,
  `votes` smallint(5) unsigned NOT NULL DEFAULT '0',
  `hits` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `record_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ordering` smallint(5) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `status_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `catid` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__uideas_statuses` (
  `id` tinyint(3) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `params` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__uideas_votes` (
  `id` int(10) unsigned NOT NULL,
  `item_id` smallint(5) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `hash` varchar(32) DEFAULT NULL COMMENT 'Anonymous users hash.',
  `votes` tinyint(3) unsigned NOT NULL,
  `record_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `#__uideas_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ui_comment_record_date` (`record_date`),
  ADD KEY `idx_ui_comment_item_published` (`item_id`,`published`);

ALTER TABLE `#__uideas_items`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#__uideas_statuses`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `#__uideas_votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ui_history_record_date` (`record_date`),
  ADD KEY `idx_ui_history_item_user` (`item_id`,`user_id`);


ALTER TABLE `#__uideas_comments`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__uideas_items`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__uideas_statuses`
  MODIFY `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__uideas_votes`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
