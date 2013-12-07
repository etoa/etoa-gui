CREATE TABLE `alliance_building_cooldown` (
 `cooldown_user_id` int(10) unsigned NOT NULL,
 `cooldown_alliance_building_id` int(10) unsigned NOT NULL,
 `cooldown_end` int(10) unsigned NOT NULL,
 UNIQUE KEY `cooldown_user_id` (`cooldown_user_id`,`cooldown_alliance_building_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
