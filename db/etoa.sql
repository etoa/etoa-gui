-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server-Version:               8.0.46 - MySQL Community Server - GPL
-- Server-Betriebssystem:        Linux
-- HeidiSQL Version:             12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Exportiere Struktur von Tabelle etoa_test.accesslog
CREATE TABLE IF NOT EXISTS `accesslog` (
  `target` varchar(255) NOT NULL,
  `sub` varchar(255) NOT NULL,
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `sid` varchar(32) NOT NULL,
  `domain` varchar(255) NOT NULL,
  KEY `target` (`target`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.admin_notes
CREATE TABLE IF NOT EXISTS `admin_notes` (
  `notes_id` int unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int unsigned NOT NULL,
  `titel` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `text` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `date` int unsigned NOT NULL,
  PRIMARY KEY (`notes_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.admin_users
CREATE TABLE IF NOT EXISTS `admin_users` (
  `user_id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(30) NOT NULL,
  `user_email` varchar(30) NOT NULL,
  `user_nick` varchar(30) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `tfa_secret` varchar(255) NOT NULL,
  `user_last_login` int unsigned NOT NULL DEFAULT '0',
  `user_acttime` int unsigned NOT NULL DEFAULT '0',
  `user_locked` int unsigned NOT NULL DEFAULT '0',
  `user_session_key` varchar(250) DEFAULT NULL,
  `user_ip` varchar(20) DEFAULT NULL,
  `user_hostname` varchar(150) DEFAULT NULL,
  `user_board_url` char(250) NOT NULL,
  `user_force_pwchange` tinyint unsigned NOT NULL DEFAULT '0',
  `user_theme` varchar(40) NOT NULL,
  `ticketmail` tinyint unsigned NOT NULL DEFAULT '1',
  `player_id` int unsigned DEFAULT NULL,
  `roles` varchar(255) NOT NULL,
  `is_contact` tinyint unsigned NOT NULL,
  PRIMARY KEY (`user_id`),
  FULLTEXT KEY `user_password` (`user_password`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.admin_user_log
CREATE TABLE IF NOT EXISTS `admin_user_log` (
  `log_id` int unsigned NOT NULL AUTO_INCREMENT,
  `log_user_id` int unsigned NOT NULL DEFAULT '0',
  `log_session_key` varchar(250) NOT NULL,
  `log_logintime` int unsigned NOT NULL DEFAULT '0',
  `log_logouttime` int unsigned NOT NULL DEFAULT '0',
  `log_acttime` int unsigned NOT NULL DEFAULT '0',
  `log_ip` varchar(20) NOT NULL,
  `log_hostname` varchar(150) NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `log_user_id` (`log_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.admin_user_sessionlog
CREATE TABLE IF NOT EXISTS `admin_user_sessionlog` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `session_id` char(40) NOT NULL,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `ip_addr` varchar(40) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `time_login` int unsigned NOT NULL DEFAULT '0',
  `time_action` int unsigned NOT NULL DEFAULT '0',
  `time_logout` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.admin_user_sessions
CREATE TABLE IF NOT EXISTS `admin_user_sessions` (
  `id` varchar(128) NOT NULL,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `ip_addr` varchar(40) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `time_login` int unsigned NOT NULL DEFAULT '0',
  `time_action` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.allianceboard_cat
CREATE TABLE IF NOT EXISTS `allianceboard_cat` (
  `cat_id` int unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(50) NOT NULL,
  `cat_desc` text NOT NULL,
  `cat_order` tinyint unsigned NOT NULL DEFAULT '0',
  `cat_bullet` varchar(255) NOT NULL,
  `cat_alliance_id` int unsigned NOT NULL,
  PRIMARY KEY (`cat_id`),
  KEY `cat_order` (`cat_order`),
  KEY `cat_name` (`cat_name`),
  KEY `cat_alliance_id` (`cat_alliance_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.allianceboard_catranks
CREATE TABLE IF NOT EXISTS `allianceboard_catranks` (
  `cr_id` int unsigned NOT NULL AUTO_INCREMENT,
  `cr_rank_id` int unsigned NOT NULL,
  `cr_cat_id` int unsigned NOT NULL,
  `cr_bnd_id` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cr_id`),
  KEY `cr_rank_id` (`cr_rank_id`),
  KEY `cr_cat_id` (`cr_cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.allianceboard_posts
CREATE TABLE IF NOT EXISTS `allianceboard_posts` (
  `post_id` int unsigned NOT NULL AUTO_INCREMENT,
  `post_topic_id` int unsigned NOT NULL DEFAULT '0',
  `post_user_id` int unsigned NOT NULL DEFAULT '0',
  `post_user_nick` varchar(15) NOT NULL,
  `post_text` text NOT NULL,
  `post_timestamp` int unsigned NOT NULL DEFAULT '0',
  `post_changed` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`post_id`),
  KEY `post_topic_id` (`post_topic_id`),
  KEY `post_user_id` (`post_user_id`),
  KEY `post_timestamp` (`post_timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.allianceboard_topics
CREATE TABLE IF NOT EXISTS `allianceboard_topics` (
  `topic_id` int unsigned NOT NULL AUTO_INCREMENT,
  `topic_cat_id` int unsigned NOT NULL DEFAULT '0',
  `topic_bnd_id` int unsigned NOT NULL DEFAULT '0',
  `topic_user_id` int unsigned NOT NULL DEFAULT '0',
  `topic_user_nick` varchar(15) NOT NULL,
  `topic_timestamp` int unsigned NOT NULL DEFAULT '0',
  `topic_subject` varchar(100) NOT NULL,
  `topic_count` int unsigned NOT NULL DEFAULT '0',
  `topic_top` tinyint unsigned NOT NULL DEFAULT '0',
  `topic_closed` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`topic_id`),
  KEY `topic_cat_id` (`topic_cat_id`),
  KEY `topic_timestamp` (`topic_timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.alliances
CREATE TABLE IF NOT EXISTS `alliances` (
  `alliance_id` int unsigned NOT NULL AUTO_INCREMENT,
  `alliance_tag` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `alliance_name` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `alliance_text` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `alliance_img` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `alliance_img_check` tinyint unsigned NOT NULL DEFAULT '0',
  `alliance_url` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `alliance_mother` int unsigned DEFAULT '0',
  `alliance_mother_request` int unsigned DEFAULT '0',
  `alliance_accept_applications` tinyint unsigned NOT NULL DEFAULT '1',
  `alliance_accept_bnd` tinyint unsigned NOT NULL DEFAULT '1',
  `alliance_public_memberlist` tinyint unsigned NOT NULL DEFAULT '0',
  `alliance_points` int unsigned NOT NULL DEFAULT '0',
  `alliance_rank_current` smallint unsigned NOT NULL DEFAULT '0',
  `alliance_rank_last` smallint unsigned NOT NULL DEFAULT '0',
  `alliance_founder_id` int unsigned NOT NULL DEFAULT '0',
  `alliance_foundation_date` int unsigned NOT NULL DEFAULT '0',
  `alliance_architect_id` int unsigned NOT NULL DEFAULT '0',
  `alliance_technican_id` int unsigned NOT NULL DEFAULT '0',
  `alliance_diplomat_id` int unsigned NOT NULL DEFAULT '0',
  `alliance_visits` int unsigned DEFAULT '0',
  `alliance_visits_ext` int unsigned DEFAULT '0',
  `alliance_application_template` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `alliance_res_metal` bigint NOT NULL DEFAULT '0',
  `alliance_res_crystal` bigint NOT NULL DEFAULT '0',
  `alliance_res_plastic` bigint NOT NULL DEFAULT '0',
  `alliance_res_fuel` bigint NOT NULL DEFAULT '0',
  `alliance_res_food` bigint NOT NULL DEFAULT '0',
  `alliance_objects_for_members` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`alliance_id`),
  KEY `alliance_tag` (`alliance_tag`),
  KEY `alliance_name` (`alliance_name`),
  KEY `alliance_points` (`alliance_points`),
  KEY `alliance_founder_id` (`alliance_founder_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='Allianz-Daten';

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.alliance_applications
CREATE TABLE IF NOT EXISTS `alliance_applications` (
  `user_id` smallint unsigned NOT NULL DEFAULT '0',
  `alliance_id` smallint unsigned NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `user_id` (`user_id`),
  KEY `alliance_id` (`alliance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.alliance_bnd
CREATE TABLE IF NOT EXISTS `alliance_bnd` (
  `alliance_bnd_name` varchar(30) NOT NULL,
  `alliance_bnd_id` int unsigned NOT NULL AUTO_INCREMENT,
  `alliance_bnd_alliance_id1` int unsigned NOT NULL DEFAULT '0',
  `alliance_bnd_alliance_id2` int unsigned NOT NULL DEFAULT '0',
  `alliance_bnd_level` int unsigned NOT NULL DEFAULT '0',
  `alliance_bnd_text` text NOT NULL,
  `alliance_bnd_date` int unsigned NOT NULL DEFAULT '0',
  `alliance_bnd_text_pub` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `alliance_bnd_points` int unsigned NOT NULL DEFAULT '0',
  `alliance_bnd_diplomat_id` int unsigned NOT NULL,
  PRIMARY KEY (`alliance_bnd_id`),
  KEY `alliance_bnd_alliance_id1` (`alliance_bnd_alliance_id1`),
  KEY `alliance_bnd_alliance_id2` (`alliance_bnd_alliance_id2`),
  KEY `bnd1` (`alliance_bnd_level`,`alliance_bnd_alliance_id1`),
  KEY `bnd2` (`alliance_bnd_level`,`alliance_bnd_alliance_id2`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.alliance_buildings
CREATE TABLE IF NOT EXISTS `alliance_buildings` (
  `alliance_building_id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `alliance_building_name` varchar(30) NOT NULL,
  `alliance_building_shortcomment` text NOT NULL,
  `alliance_building_longcomment` text NOT NULL,
  `alliance_building_costs_metal` int unsigned NOT NULL DEFAULT '0',
  `alliance_building_costs_crystal` int unsigned NOT NULL DEFAULT '0',
  `alliance_building_costs_plastic` int unsigned NOT NULL DEFAULT '0',
  `alliance_building_costs_fuel` int unsigned NOT NULL DEFAULT '0',
  `alliance_building_costs_food` int unsigned NOT NULL DEFAULT '0',
  `alliance_building_build_time` mediumint unsigned NOT NULL DEFAULT '0',
  `alliance_building_costs_factor` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `alliance_building_last_level` tinyint unsigned NOT NULL DEFAULT '0',
  `alliance_building_show` tinyint unsigned NOT NULL DEFAULT '1',
  `alliance_building_needed_id` tinyint unsigned NOT NULL DEFAULT '0',
  `alliance_building_needed_level` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`alliance_building_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.alliance_building_cooldown
CREATE TABLE IF NOT EXISTS `alliance_building_cooldown` (
  `cooldown_user_id` int unsigned NOT NULL,
  `cooldown_alliance_building_id` int unsigned NOT NULL,
  `cooldown_end` int unsigned NOT NULL,
  UNIQUE KEY `cooldown_user_id` (`cooldown_user_id`,`cooldown_alliance_building_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.alliance_buildlist
CREATE TABLE IF NOT EXISTS `alliance_buildlist` (
  `alliance_buildlist_id` int unsigned NOT NULL AUTO_INCREMENT,
  `alliance_buildlist_alliance_id` smallint unsigned NOT NULL DEFAULT '0',
  `alliance_buildlist_building_id` tinyint unsigned NOT NULL DEFAULT '0',
  `alliance_buildlist_current_level` tinyint unsigned NOT NULL DEFAULT '0',
  `alliance_buildlist_build_start_time` int unsigned NOT NULL DEFAULT '0',
  `alliance_buildlist_build_end_time` int unsigned NOT NULL DEFAULT '0',
  `alliance_buildlist_cooldown` int unsigned NOT NULL DEFAULT '0',
  `alliance_buildlist_member_for` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`alliance_buildlist_id`),
  KEY `alliance_buildlist_alliance_id` (`alliance_buildlist_alliance_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.alliance_history
CREATE TABLE IF NOT EXISTS `alliance_history` (
  `history_id` int unsigned NOT NULL AUTO_INCREMENT,
  `history_timestamp` int unsigned NOT NULL DEFAULT '0',
  `history_text` text NOT NULL,
  `history_alliance_id` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`history_id`),
  KEY `latest` (`history_alliance_id`,`history_timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=825 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.alliance_news
CREATE TABLE IF NOT EXISTS `alliance_news` (
  `alliance_news_id` int unsigned NOT NULL AUTO_INCREMENT,
  `alliance_news_alliance_id` int unsigned NOT NULL DEFAULT '0',
  `alliance_news_user_id` int unsigned NOT NULL DEFAULT '0',
  `alliance_news_title` varchar(255) NOT NULL,
  `alliance_news_text` text NOT NULL,
  `alliance_news_date` int unsigned NOT NULL DEFAULT '0',
  `alliance_news_alliance_to_id` int unsigned DEFAULT '0',
  `alliance_news_changed_date` int unsigned DEFAULT NULL,
  `alliance_news_changed_counter` int unsigned DEFAULT NULL,
  `alliance_news_show` tinyint unsigned NOT NULL DEFAULT '1',
  `alliance_news_ip` char(15) DEFAULT NULL,
  PRIMARY KEY (`alliance_news_id`),
  KEY `alliance_news_alliance_id` (`alliance_news_alliance_id`),
  KEY `alliance_news_user_id` (`alliance_news_user_id`),
  KEY `alliance_news_date` (`alliance_news_date`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.alliance_points
CREATE TABLE IF NOT EXISTS `alliance_points` (
  `point_id` int unsigned NOT NULL AUTO_INCREMENT,
  `point_alliance_id` int unsigned NOT NULL DEFAULT '0',
  `point_timestamp` int unsigned NOT NULL DEFAULT '0',
  `point_points` bigint unsigned NOT NULL DEFAULT '0',
  `point_avg` bigint unsigned NOT NULL DEFAULT '0',
  `point_cnt` bigint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`point_id`),
  KEY `point_user_id` (`point_alliance_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COMMENT='Speichert den Punkteverlauf der Allianz';

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.alliance_polls
CREATE TABLE IF NOT EXISTS `alliance_polls` (
  `poll_id` int unsigned NOT NULL AUTO_INCREMENT,
  `poll_alliance_id` int unsigned NOT NULL DEFAULT '0',
  `poll_title` varchar(150) NOT NULL,
  `poll_question` varchar(150) NOT NULL,
  `poll_timestamp` int unsigned NOT NULL DEFAULT '0',
  `poll_a1_text` varchar(150) NOT NULL,
  `poll_a2_text` varchar(150) NOT NULL,
  `poll_a3_text` varchar(150) NOT NULL,
  `poll_a4_text` varchar(150) NOT NULL,
  `poll_a5_text` varchar(150) NOT NULL,
  `poll_a6_text` varchar(150) NOT NULL,
  `poll_a7_text` varchar(150) NOT NULL,
  `poll_a8_text` varchar(150) NOT NULL,
  `poll_a1_count` tinyint unsigned NOT NULL DEFAULT '0',
  `poll_a2_count` tinyint unsigned NOT NULL DEFAULT '0',
  `poll_a3_count` tinyint unsigned NOT NULL DEFAULT '0',
  `poll_a4_count` tinyint unsigned NOT NULL DEFAULT '0',
  `poll_a5_count` tinyint unsigned NOT NULL DEFAULT '0',
  `poll_a6_count` tinyint unsigned NOT NULL DEFAULT '0',
  `poll_a7_count` tinyint unsigned NOT NULL DEFAULT '0',
  `poll_a8_count` tinyint unsigned NOT NULL DEFAULT '0',
  `poll_active` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`poll_id`),
  KEY `poll_alliance_id` (`poll_alliance_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.alliance_poll_votes
CREATE TABLE IF NOT EXISTS `alliance_poll_votes` (
  `vote_id` int unsigned NOT NULL AUTO_INCREMENT,
  `vote_poll_id` int unsigned NOT NULL DEFAULT '0',
  `vote_user_id` int unsigned NOT NULL DEFAULT '0',
  `vote_alliance_id` int unsigned NOT NULL DEFAULT '0',
  `vote_number` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`vote_id`),
  KEY `vote_poll_id` (`vote_poll_id`),
  KEY `vote_user_id` (`vote_user_id`),
  KEY `vote_alliance_id` (`vote_alliance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.alliance_rankrights
CREATE TABLE IF NOT EXISTS `alliance_rankrights` (
  `rr_id` int unsigned NOT NULL AUTO_INCREMENT,
  `rr_rank_id` int unsigned NOT NULL DEFAULT '0',
  `rr_right_id` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rr_id`),
  KEY `rr_rank_id` (`rr_rank_id`),
  KEY `rr_right_id` (`rr_right_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.alliance_ranks
CREATE TABLE IF NOT EXISTS `alliance_ranks` (
  `rank_id` int unsigned NOT NULL AUTO_INCREMENT,
  `rank_alliance_id` int unsigned NOT NULL DEFAULT '0',
  `rank_name` varchar(30) DEFAULT NULL,
  `rank_level` tinyint unsigned NOT NULL DEFAULT '0',
  `rank_points` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rank_id`),
  KEY `rank_alliance_id` (`rank_alliance_id`,`rank_level`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.alliance_rights
CREATE TABLE IF NOT EXISTS `alliance_rights` (
  `right_id` int unsigned NOT NULL AUTO_INCREMENT,
  `right_key` varchar(30) NOT NULL,
  `right_desc` text NOT NULL,
  PRIMARY KEY (`right_id`),
  KEY `right_key` (`right_key`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.alliance_spends
CREATE TABLE IF NOT EXISTS `alliance_spends` (
  `alliance_spend_id` int unsigned NOT NULL AUTO_INCREMENT,
  `alliance_spend_alliance_id` int unsigned NOT NULL DEFAULT '0',
  `alliance_spend_user_id` int unsigned NOT NULL DEFAULT '0',
  `alliance_spend_metal` bigint unsigned NOT NULL DEFAULT '0',
  `alliance_spend_crystal` bigint unsigned NOT NULL DEFAULT '0',
  `alliance_spend_plastic` bigint unsigned NOT NULL DEFAULT '0',
  `alliance_spend_fuel` bigint unsigned NOT NULL DEFAULT '0',
  `alliance_spend_food` bigint unsigned NOT NULL DEFAULT '0',
  `alliance_spend_time` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`alliance_spend_id`),
  KEY `alliance_spend_alliance_id` (`alliance_spend_alliance_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.alliance_stats
CREATE TABLE IF NOT EXISTS `alliance_stats` (
  `alliance_id` int unsigned NOT NULL,
  `alliance_tag` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `alliance_name` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cnt` smallint unsigned NOT NULL DEFAULT '0',
  `points` bigint unsigned NOT NULL DEFAULT '0',
  `upoints` bigint unsigned NOT NULL DEFAULT '0',
  `apoints` bigint unsigned NOT NULL DEFAULT '0',
  `bpoints` bigint unsigned NOT NULL DEFAULT '0',
  `tpoints` bigint unsigned NOT NULL DEFAULT '0',
  `spoints` bigint unsigned NOT NULL DEFAULT '0',
  `uavg` bigint unsigned NOT NULL DEFAULT '0',
  `alliance_rank_current` smallint unsigned NOT NULL DEFAULT '0',
  `alliance_rank_last` smallint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`alliance_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.alliance_techlist
CREATE TABLE IF NOT EXISTS `alliance_techlist` (
  `alliance_techlist_id` int unsigned NOT NULL AUTO_INCREMENT,
  `alliance_techlist_alliance_id` smallint unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_tech_id` tinyint unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_current_level` tinyint unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_build_start_time` int unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_build_end_time` int unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_member_for` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`alliance_techlist_id`),
  KEY `alliance_techlist_alliance_id` (`alliance_techlist_alliance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.alliance_technologies
CREATE TABLE IF NOT EXISTS `alliance_technologies` (
  `alliance_tech_id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `alliance_tech_name` varchar(30) NOT NULL,
  `alliance_tech_shortcomment` text NOT NULL,
  `alliance_tech_longcomment` text NOT NULL,
  `alliance_tech_costs_metal` int unsigned NOT NULL DEFAULT '0',
  `alliance_tech_costs_crystal` int unsigned NOT NULL DEFAULT '0',
  `alliance_tech_costs_plastic` int unsigned NOT NULL DEFAULT '0',
  `alliance_tech_costs_fuel` int unsigned NOT NULL DEFAULT '0',
  `alliance_tech_costs_food` int unsigned NOT NULL DEFAULT '0',
  `alliance_tech_build_time` mediumint unsigned NOT NULL DEFAULT '0',
  `alliance_tech_costs_factor` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `alliance_tech_last_level` tinyint unsigned NOT NULL DEFAULT '0',
  `alliance_tech_show` tinyint unsigned NOT NULL DEFAULT '1',
  `alliance_tech_needed_id` tinyint unsigned NOT NULL DEFAULT '0',
  `alliance_tech_needed_level` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`alliance_tech_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.asteroids
CREATE TABLE IF NOT EXISTS `asteroids` (
  `id` int unsigned NOT NULL,
  `res_metal` bigint unsigned NOT NULL DEFAULT '0',
  `res_crystal` bigint unsigned NOT NULL DEFAULT '0',
  `res_plastic` bigint unsigned NOT NULL DEFAULT '0',
  `res_fuel` bigint unsigned NOT NULL DEFAULT '0',
  `res_food` bigint unsigned NOT NULL DEFAULT '0',
  `res_power` bigint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.backend_message_queue
CREATE TABLE IF NOT EXISTS `backend_message_queue` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cmd` varchar(255) NOT NULL,
  `arg` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cmd` (`cmd`,`arg`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.bookmarks
CREATE TABLE IF NOT EXISTS `bookmarks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `entity_id` int unsigned NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bookmark_user_id` (`user_id`),
  KEY `absindex` (`user_id`,`entity_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.buddylist
CREATE TABLE IF NOT EXISTS `buddylist` (
  `bl_user_id` int unsigned NOT NULL DEFAULT '0',
  `bl_buddy_id` int unsigned NOT NULL DEFAULT '0',
  `bl_allow` int unsigned NOT NULL DEFAULT '0',
  `bl_id` int unsigned NOT NULL AUTO_INCREMENT,
  `bl_comment` text,
  `bl_comment_buddy` text,
  PRIMARY KEY (`bl_id`),
  KEY `bl_buddy_id` (`bl_buddy_id`),
  KEY `bl_user_id` (`bl_user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.buildings
CREATE TABLE IF NOT EXISTS `buildings` (
  `building_id` int unsigned NOT NULL AUTO_INCREMENT,
  `building_name` varchar(255) NOT NULL,
  `building_type_id` tinyint unsigned NOT NULL DEFAULT '1',
  `building_shortcomment` text NOT NULL,
  `building_longcomment` text NOT NULL,
  `building_costs_metal` int unsigned NOT NULL DEFAULT '0',
  `building_costs_crystal` int unsigned NOT NULL DEFAULT '0',
  `building_costs_fuel` int unsigned NOT NULL DEFAULT '0',
  `building_costs_plastic` int unsigned NOT NULL DEFAULT '0',
  `building_costs_food` int unsigned NOT NULL DEFAULT '0',
  `building_costs_power` int unsigned NOT NULL DEFAULT '0',
  `building_build_costs_factor` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `building_demolish_costs_factor` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `building_power_use` int unsigned NOT NULL DEFAULT '0',
  `building_power_req` int unsigned NOT NULL DEFAULT '0',
  `building_fuel_use` int unsigned NOT NULL DEFAULT '0',
  `building_prod_metal` int unsigned NOT NULL DEFAULT '0',
  `building_prod_crystal` int unsigned NOT NULL DEFAULT '0',
  `building_prod_plastic` int unsigned NOT NULL DEFAULT '0',
  `building_prod_fuel` int unsigned NOT NULL DEFAULT '0',
  `building_prod_food` int unsigned NOT NULL DEFAULT '0',
  `building_prod_power` int unsigned NOT NULL DEFAULT '0',
  `building_production_factor` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `building_store_metal` int unsigned NOT NULL DEFAULT '0',
  `building_store_crystal` int unsigned NOT NULL DEFAULT '0',
  `building_store_plastic` int unsigned NOT NULL DEFAULT '0',
  `building_store_fuel` int unsigned NOT NULL DEFAULT '0',
  `building_store_food` int unsigned NOT NULL DEFAULT '0',
  `building_store_factor` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `building_people_place` int unsigned NOT NULL DEFAULT '0',
  `building_last_level` tinyint unsigned NOT NULL DEFAULT '99',
  `building_fields` smallint unsigned NOT NULL DEFAULT '1',
  `building_show` tinyint unsigned NOT NULL DEFAULT '1',
  `building_order` tinyint unsigned NOT NULL DEFAULT '0',
  `building_fieldsprovide` smallint unsigned NOT NULL DEFAULT '0',
  `building_workplace` tinyint unsigned NOT NULL DEFAULT '0',
  `building_bunker_res` int unsigned NOT NULL DEFAULT '0',
  `building_bunker_fleet_count` int unsigned NOT NULL DEFAULT '0',
  `building_bunker_fleet_space` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`building_id`),
  KEY `building_name` (`building_name`,`building_order`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.building_points
CREATE TABLE IF NOT EXISTS `building_points` (
  `bp_id` int unsigned NOT NULL AUTO_INCREMENT,
  `bp_building_id` int unsigned NOT NULL DEFAULT '0',
  `bp_level` tinyint unsigned NOT NULL DEFAULT '0',
  `bp_points` decimal(20,3) unsigned NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`bp_id`),
  KEY `bp_building_id` (`bp_building_id`),
  KEY `bp_level` (`bp_level`),
  KEY `bp_points` (`bp_points`)
) ENGINE=MyISAM AUTO_INCREMENT=1082 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.building_queue
CREATE TABLE IF NOT EXISTS `building_queue` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `entity_id` int unsigned NOT NULL DEFAULT '0',
  `item_id` int unsigned NOT NULL DEFAULT '0',
  `time_start` int unsigned NOT NULL DEFAULT '0',
  `time_end` int unsigned NOT NULL DEFAULT '0',
  `targetlevel` smallint unsigned NOT NULL DEFAULT '0',
  `res_metal` bigint unsigned NOT NULL DEFAULT '0',
  `res_crystal` bigint unsigned NOT NULL DEFAULT '0',
  `res_plastic` bigint unsigned NOT NULL DEFAULT '0',
  `res_fuel` bigint unsigned NOT NULL DEFAULT '0',
  `res_food` bigint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `entity_id` (`entity_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.building_requirements
CREATE TABLE IF NOT EXISTS `building_requirements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int unsigned NOT NULL,
  `req_building_id` int unsigned DEFAULT NULL,
  `req_tech_id` int unsigned DEFAULT NULL,
  `req_level` smallint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `obj_building` (`obj_id`,`req_building_id`),
  UNIQUE KEY `obj_tech` (`obj_id`,`req_tech_id`),
  KEY `IDX_EB479F2566093344` (`obj_id`),
  KEY `IDX_EB479F257E57261C` (`req_building_id`),
  KEY `IDX_EB479F2568C70794` (`req_tech_id`),
  CONSTRAINT `FK_EB479F2566093344` FOREIGN KEY (`obj_id`) REFERENCES `buildings` (`building_id`),
  CONSTRAINT `FK_EB479F2568C70794` FOREIGN KEY (`req_tech_id`) REFERENCES `technologies` (`tech_id`),
  CONSTRAINT `FK_EB479F257E57261C` FOREIGN KEY (`req_building_id`) REFERENCES `buildings` (`building_id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.building_types
CREATE TABLE IF NOT EXISTS `building_types` (
  `type_id` int unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL,
  `type_order` smallint unsigned NOT NULL DEFAULT '0',
  `type_color` char(7) NOT NULL DEFAULT '#ffffff',
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.buildlist
CREATE TABLE IF NOT EXISTS `buildlist` (
  `buildlist_id` int unsigned NOT NULL AUTO_INCREMENT,
  `buildlist_user_id` int unsigned DEFAULT NULL,
  `buildlist_building_id` int unsigned NOT NULL DEFAULT '0',
  `buildlist_entity_id` int unsigned NOT NULL DEFAULT '0',
  `buildlist_current_level` tinyint unsigned NOT NULL DEFAULT '0',
  `buildlist_build_start_time` int unsigned NOT NULL DEFAULT '0',
  `buildlist_build_end_time` int unsigned NOT NULL DEFAULT '0',
  `buildlist_build_type` tinyint unsigned NOT NULL DEFAULT '0',
  `buildlist_prod_percent` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `buildlist_people_working` int unsigned NOT NULL DEFAULT '0',
  `buildlist_people_working_status` tinyint unsigned NOT NULL DEFAULT '0',
  `buildlist_deactivated` int unsigned NOT NULL DEFAULT '0',
  `buildlist_cooldown` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`buildlist_id`),
  UNIQUE KEY `entity_user_building` (`buildlist_entity_id`,`buildlist_user_id`,`buildlist_building_id`),
  KEY `buildlist_user_id` (`buildlist_user_id`),
  KEY `buildlist_building_id` (`buildlist_building_id`),
  KEY `buildlist_planet_id` (`buildlist_entity_id`),
  KEY `buildlist_build_end_time` (`buildlist_build_end_time`),
  KEY `buildlist_build_type` (`buildlist_build_type`),
  CONSTRAINT `fk_building_id` FOREIGN KEY (`buildlist_building_id`) REFERENCES `buildings` (`building_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.cells
CREATE TABLE IF NOT EXISTS `cells` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `sx` tinyint unsigned NOT NULL DEFAULT '0',
  `sy` tinyint unsigned NOT NULL DEFAULT '0',
  `cx` tinyint unsigned NOT NULL DEFAULT '0',
  `cy` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `cell` (`cx`,`cy`),
  KEY `sector` (`sx`,`sy`),
  KEY `coordinates` (`sx`,`sy`,`cx`,`cy`),
  KEY `cy` (`cy`),
  KEY `cx` (`cx`),
  KEY `sy` (`sy`),
  KEY `sx` (`sx`)
) ENGINE=MyISAM AUTO_INCREMENT=401 DEFAULT CHARSET=utf8mb3 DELAY_KEY_WRITE=1;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.chat
CREATE TABLE IF NOT EXISTS `chat` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int unsigned NOT NULL,
  `nick` varchar(50) DEFAULT NULL,
  `text` varchar(255) NOT NULL,
  `color` varchar(15) DEFAULT NULL,
  `user_id` smallint unsigned DEFAULT NULL,
  `admin` tinyint unsigned NOT NULL DEFAULT '0',
  `channel_id` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.chat_banns
CREATE TABLE IF NOT EXISTS `chat_banns` (
  `user_id` varchar(50) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `timestamp` int NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.chat_channels
CREATE TABLE IF NOT EXISTS `chat_channels` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('public','alliance','private','') NOT NULL,
  `permanent` int NOT NULL,
  `topic` varchar(255) NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `alliance_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.chat_log
CREATE TABLE IF NOT EXISTS `chat_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int unsigned NOT NULL,
  `nick` varchar(50) NOT NULL,
  `text` varchar(255) NOT NULL,
  `color` varchar(15) NOT NULL,
  `user_id` smallint unsigned NOT NULL DEFAULT '0',
  `admin` tinyint unsigned NOT NULL DEFAULT '0',
  `private` tinyint unsigned NOT NULL DEFAULT '0',
  `channel` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.chat_users
CREATE TABLE IF NOT EXISTS `chat_users` (
  `nick` varchar(30) NOT NULL,
  `user_id` smallint unsigned NOT NULL DEFAULT '0',
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `kick` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.config
CREATE TABLE IF NOT EXISTS `config` (
  `config_id` int unsigned NOT NULL AUTO_INCREMENT,
  `config_name` varchar(50) NOT NULL,
  `config_value` text NOT NULL,
  `config_param1` text NOT NULL,
  `config_param2` text NOT NULL,
  PRIMARY KEY (`config_id`),
  UNIQUE KEY `config_name_2` (`config_name`)
) ENGINE=MyISAM AUTO_INCREMENT=421 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.default_items
CREATE TABLE IF NOT EXISTS `default_items` (
  `item_id` int NOT NULL AUTO_INCREMENT,
  `item_set_id` int NOT NULL DEFAULT '0',
  `item_cat` char(1) NOT NULL,
  `item_object_id` int NOT NULL,
  `item_count` int NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `FK_default_items_default_item_sets` (`item_set_id`)
) ENGINE=MyISAM AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.default_item_sets
CREATE TABLE IF NOT EXISTS `default_item_sets` (
  `set_id` int NOT NULL AUTO_INCREMENT,
  `set_name` varchar(50) NOT NULL,
  `set_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`set_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.defense
CREATE TABLE IF NOT EXISTS `defense` (
  `def_id` int unsigned NOT NULL AUTO_INCREMENT,
  `def_name` varchar(50) NOT NULL,
  `def_shortcomment` text NOT NULL,
  `def_longcomment` text NOT NULL,
  `def_costs_metal` int unsigned NOT NULL DEFAULT '0',
  `def_costs_crystal` int unsigned NOT NULL DEFAULT '0',
  `def_costs_fuel` int unsigned NOT NULL DEFAULT '0',
  `def_costs_plastic` int unsigned NOT NULL DEFAULT '0',
  `def_costs_food` int unsigned NOT NULL DEFAULT '0',
  `def_costs_power` int unsigned NOT NULL DEFAULT '0',
  `def_power_use` int unsigned NOT NULL DEFAULT '0',
  `def_fuel_use` int unsigned NOT NULL DEFAULT '0',
  `def_prod_power` int unsigned NOT NULL DEFAULT '0',
  `def_fields` smallint unsigned NOT NULL DEFAULT '0',
  `def_show` tinyint unsigned NOT NULL DEFAULT '1',
  `def_buildable` tinyint unsigned NOT NULL DEFAULT '1',
  `def_order` tinyint unsigned NOT NULL DEFAULT '0',
  `def_structure` int unsigned NOT NULL DEFAULT '0',
  `def_shield` int unsigned NOT NULL DEFAULT '0',
  `def_weapon` int unsigned NOT NULL DEFAULT '0',
  `def_heal` int unsigned NOT NULL DEFAULT '0',
  `def_jam` tinyint unsigned NOT NULL DEFAULT '0',
  `def_race_id` int unsigned NOT NULL DEFAULT '0',
  `def_cat_id` tinyint unsigned NOT NULL DEFAULT '1',
  `def_max_count` int unsigned NOT NULL DEFAULT '999999',
  `def_points` decimal(20,3) unsigned NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`def_id`),
  KEY `def_name` (`def_name`),
  KEY `def_order` (`def_order`),
  KEY `def_max_count` (`def_max_count`),
  KEY `def_battlepoints` (`def_points`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.deflist
CREATE TABLE IF NOT EXISTS `deflist` (
  `deflist_id` int unsigned NOT NULL AUTO_INCREMENT,
  `deflist_user_id` int unsigned NOT NULL DEFAULT '0',
  `deflist_def_id` int unsigned NOT NULL DEFAULT '0',
  `deflist_entity_id` int unsigned NOT NULL DEFAULT '0',
  `deflist_count` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`deflist_id`),
  UNIQUE KEY `deflist_all` (`deflist_user_id`,`deflist_entity_id`,`deflist_def_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.def_cat
CREATE TABLE IF NOT EXISTS `def_cat` (
  `cat_id` int unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cat_order` smallint unsigned NOT NULL DEFAULT '0',
  `cat_color` char(7) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '#ffffff',
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.def_queue
CREATE TABLE IF NOT EXISTS `def_queue` (
  `queue_id` int unsigned NOT NULL AUTO_INCREMENT,
  `queue_user_id` int unsigned NOT NULL DEFAULT '0',
  `queue_def_id` int unsigned NOT NULL DEFAULT '0',
  `queue_entity_id` int unsigned NOT NULL DEFAULT '0',
  `queue_cnt` int unsigned NOT NULL DEFAULT '0',
  `queue_starttime` int unsigned NOT NULL DEFAULT '0',
  `queue_endtime` int unsigned NOT NULL DEFAULT '0',
  `queue_objtime` int unsigned NOT NULL DEFAULT '0',
  `queue_build_type` tinyint unsigned NOT NULL DEFAULT '0',
  `queue_user_click_time` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`queue_id`),
  KEY `queue_user_id` (`queue_user_id`),
  KEY `queue_planet_id` (`queue_entity_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.def_requirements
CREATE TABLE IF NOT EXISTS `def_requirements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int unsigned NOT NULL,
  `req_building_id` int unsigned DEFAULT NULL,
  `req_tech_id` int unsigned DEFAULT NULL,
  `req_level` smallint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `obj_building` (`obj_id`,`req_building_id`),
  UNIQUE KEY `obj_tech` (`obj_id`,`req_tech_id`),
  KEY `IDX_21FC302366093344` (`obj_id`),
  KEY `IDX_21FC30237E57261C` (`req_building_id`),
  KEY `IDX_21FC302368C70794` (`req_tech_id`),
  CONSTRAINT `FK_21FC302366093344` FOREIGN KEY (`obj_id`) REFERENCES `defense` (`def_id`),
  CONSTRAINT `FK_21FC302368C70794` FOREIGN KEY (`req_tech_id`) REFERENCES `technologies` (`tech_id`),
  CONSTRAINT `FK_21FC30237E57261C` FOREIGN KEY (`req_building_id`) REFERENCES `buildings` (`building_id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.entities
CREATE TABLE IF NOT EXISTS `entities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cell_id` int unsigned NOT NULL,
  `code` char(1) DEFAULT NULL,
  `pos` int unsigned NOT NULL DEFAULT '0',
  `lastvisited` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cell_id` (`cell_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7337 DEFAULT CHARSET=utf8mb3 DELAY_KEY_WRITE=1 COMMENT='Entities in Space, acts as fleet targets';

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.fleet
CREATE TABLE IF NOT EXISTS `fleet` (
  `id` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` smallint unsigned NOT NULL DEFAULT '0',
  `leader_id` mediumint unsigned DEFAULT '0',
  `entity_from` smallint unsigned NOT NULL DEFAULT '0',
  `entity_to` smallint unsigned NOT NULL DEFAULT '0',
  `next_id` smallint unsigned NOT NULL DEFAULT '0',
  `launchtime` int unsigned NOT NULL DEFAULT '0',
  `landtime` int unsigned NOT NULL DEFAULT '0',
  `nextactiontime` int unsigned NOT NULL DEFAULT '0',
  `action` char(15) NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0: Departure, 1: Arrival, 2: Cancelled',
  `pilots` bigint unsigned NOT NULL DEFAULT '0',
  `usage_fuel` int unsigned NOT NULL DEFAULT '0',
  `usage_food` int unsigned NOT NULL DEFAULT '0',
  `usage_power` int unsigned NOT NULL DEFAULT '0',
  `support_usage_fuel` int unsigned NOT NULL DEFAULT '0',
  `support_usage_food` int unsigned NOT NULL DEFAULT '0',
  `res_metal` bigint unsigned NOT NULL DEFAULT '0',
  `res_crystal` bigint unsigned NOT NULL DEFAULT '0',
  `res_plastic` bigint unsigned NOT NULL DEFAULT '0',
  `res_fuel` bigint unsigned NOT NULL DEFAULT '0',
  `res_food` bigint unsigned NOT NULL DEFAULT '0',
  `res_power` bigint unsigned NOT NULL DEFAULT '0',
  `res_people` bigint unsigned NOT NULL DEFAULT '0',
  `fetch_metal` bigint unsigned NOT NULL DEFAULT '0',
  `fetch_crystal` bigint unsigned NOT NULL DEFAULT '0',
  `fetch_plastic` bigint unsigned NOT NULL DEFAULT '0',
  `fetch_fuel` bigint unsigned NOT NULL DEFAULT '0',
  `fetch_food` bigint unsigned NOT NULL DEFAULT '0',
  `fetch_power` bigint unsigned NOT NULL DEFAULT '0',
  `fetch_people` bigint unsigned NOT NULL DEFAULT '0',
  `flag` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'UpdateTestFlag',
  PRIMARY KEY (`id`),
  KEY `fleet_user_id` (`user_id`),
  KEY `fleet_landtime` (`landtime`),
  KEY `entity_from` (`entity_from`),
  KEY `entity_to` (`entity_to`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.fleet_bookmarks
CREATE TABLE IF NOT EXISTS `fleet_bookmarks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `target_id` mediumint unsigned NOT NULL DEFAULT '0',
  `ships` json NOT NULL,
  `res` text NOT NULL,
  `resfetch` text NOT NULL,
  `action` char(15) NOT NULL,
  `speed` mediumint unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.fleet_ships
CREATE TABLE IF NOT EXISTS `fleet_ships` (
  `fs_id` int unsigned NOT NULL AUTO_INCREMENT,
  `fs_fleet_id` int unsigned NOT NULL DEFAULT '0',
  `fs_ship_id` int unsigned NOT NULL DEFAULT '0',
  `fs_ship_cnt` int unsigned NOT NULL DEFAULT '0',
  `fs_ship_faked` int unsigned NOT NULL DEFAULT '0',
  `fs_special_ship` tinyint unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_level` tinyint unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_exp` int unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_weapon` tinyint unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_structure` tinyint unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_shield` tinyint unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_heal` tinyint unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_capacity` tinyint unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_speed` tinyint unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_pilots` tinyint unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_tarn` tinyint unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_antrax` tinyint unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_forsteal` tinyint unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_build_destroy` tinyint unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_antrax_food` tinyint unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_deactivade` tinyint unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_readiness` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fs_id`),
  KEY `fs_fleet_id` (`fs_fleet_id`),
  KEY `fs_ship_id` (`fs_ship_id`),
  KEY `fs_fleet_id_2` (`fs_fleet_id`,`fs_ship_faked`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.hostname_cache
CREATE TABLE IF NOT EXISTS `hostname_cache` (
  `addr` char(39) NOT NULL,
  `host` varchar(100) NOT NULL,
  `timestamp` int NOT NULL,
  PRIMARY KEY (`addr`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.login_failures
CREATE TABLE IF NOT EXISTS `login_failures` (
  `failure_id` int unsigned NOT NULL AUTO_INCREMENT,
  `failure_time` int unsigned NOT NULL DEFAULT '0',
  `failure_ip` varchar(15) NOT NULL,
  `failure_host` varchar(50) DEFAULT NULL,
  `failure_user_id` int unsigned NOT NULL DEFAULT '0',
  `failure_client` varchar(255) NOT NULL,
  PRIMARY KEY (`failure_id`),
  KEY `failure_user_id` (`failure_user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 DELAY_KEY_WRITE=1;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.logs
CREATE TABLE IF NOT EXISTS `logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `facility` tinyint unsigned NOT NULL DEFAULT '0',
  `severity` tinyint unsigned NOT NULL DEFAULT '1',
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `ip` varchar(39) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `facility` (`facility`),
  KEY `logview` (`facility`,`severity`,`timestamp`),
  KEY `severity` (`severity`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=2171 DEFAULT CHARSET=utf8mb3 DELAY_KEY_WRITE=1;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.logs_alliance
CREATE TABLE IF NOT EXISTS `logs_alliance` (
  `logs_alliance_id` int NOT NULL AUTO_INCREMENT,
  `logs_alliance_timestamp` int unsigned NOT NULL DEFAULT '0',
  `logs_alliance_text` text NOT NULL,
  `logs_alliance_alliance_id` int unsigned NOT NULL DEFAULT '0',
  `logs_alliance_alliance_tag` varchar(10) NOT NULL DEFAULT '0',
  `logs_alliance_alliance_name` varchar(30) NOT NULL,
  `logs_alliance_user_id` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`logs_alliance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 DELAY_KEY_WRITE=1;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.logs_battle
CREATE TABLE IF NOT EXISTS `logs_battle` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `facility` tinyint unsigned NOT NULL DEFAULT '0',
  `severity` tinyint unsigned NOT NULL DEFAULT '1',
  `fleet_id` int unsigned NOT NULL DEFAULT '0',
  `user_id` text NOT NULL,
  `entity_user_id` text NOT NULL,
  `user_alliance_id` int unsigned NOT NULL DEFAULT '0',
  `entity_user_alliance_id` int unsigned NOT NULL DEFAULT '0',
  `war` tinyint unsigned NOT NULL DEFAULT '0',
  `entity_id` int unsigned NOT NULL DEFAULT '1',
  `action` char(15) NOT NULL,
  `landtime` int unsigned NOT NULL DEFAULT '1',
  `result` tinyint unsigned NOT NULL,
  `fleet_ships_cnt` int unsigned NOT NULL DEFAULT '0',
  `entity_ships_cnt` int unsigned NOT NULL DEFAULT '0',
  `entity_defs_cnt` int unsigned NOT NULL DEFAULT '0',
  `fleet_weapon` bigint unsigned NOT NULL DEFAULT '10',
  `fleet_shield` bigint unsigned NOT NULL DEFAULT '0',
  `fleet_structure` bigint unsigned NOT NULL DEFAULT '0',
  `fleet_weapon_bonus` smallint unsigned NOT NULL DEFAULT '0',
  `fleet_shield_bonus` smallint unsigned NOT NULL DEFAULT '0',
  `fleet_structure_bonus` smallint unsigned NOT NULL DEFAULT '0',
  `entity_weapon` bigint unsigned NOT NULL DEFAULT '0',
  `entity_shield` bigint unsigned NOT NULL DEFAULT '0',
  `entity_structure` bigint unsigned NOT NULL DEFAULT '0',
  `entity_weapon_bonus` smallint unsigned NOT NULL DEFAULT '0',
  `entity_shield_bonus` smallint unsigned NOT NULL DEFAULT '0',
  `entity_structure_bonus` smallint unsigned NOT NULL DEFAULT '0',
  `fleet_win_exp` int unsigned NOT NULL DEFAULT '0',
  `entity_win_exp` int unsigned NOT NULL DEFAULT '0',
  `win_metal` bigint unsigned NOT NULL DEFAULT '0',
  `win_crystal` bigint unsigned NOT NULL DEFAULT '0',
  `win_pvc` bigint unsigned NOT NULL DEFAULT '0',
  `win_tritium` bigint unsigned NOT NULL DEFAULT '0',
  `win_food` bigint unsigned NOT NULL DEFAULT '0',
  `tf_metal` bigint unsigned NOT NULL DEFAULT '0',
  `tf_crystal` bigint unsigned NOT NULL DEFAULT '0',
  `tf_pvc` bigint unsigned NOT NULL DEFAULT '0',
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `logs_battle_fleet_landtime` (`landtime`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 DELAY_KEY_WRITE=1;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.logs_battle_queue
CREATE TABLE IF NOT EXISTS `logs_battle_queue` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `facility` tinyint unsigned NOT NULL DEFAULT '0',
  `severity` tinyint unsigned NOT NULL DEFAULT '1',
  `fleet_id` int unsigned NOT NULL DEFAULT '0',
  `user_id` text NOT NULL,
  `entity_user_id` text NOT NULL,
  `user_alliance_id` int unsigned NOT NULL DEFAULT '0',
  `entity_user_alliance_id` int unsigned NOT NULL DEFAULT '0',
  `war` tinyint unsigned NOT NULL DEFAULT '0',
  `entity_id` int unsigned NOT NULL DEFAULT '1',
  `action` char(15) NOT NULL,
  `landtime` int unsigned NOT NULL DEFAULT '1',
  `result` tinyint unsigned NOT NULL,
  `fleet_ships_cnt` int unsigned NOT NULL DEFAULT '0',
  `entity_ships_cnt` int unsigned NOT NULL DEFAULT '0',
  `entity_defs_cnt` int unsigned NOT NULL DEFAULT '0',
  `fleet_weapon` bigint unsigned NOT NULL DEFAULT '10',
  `fleet_shield` bigint unsigned NOT NULL DEFAULT '0',
  `fleet_structure` bigint unsigned NOT NULL DEFAULT '0',
  `fleet_weapon_bonus` smallint unsigned NOT NULL DEFAULT '0',
  `fleet_shield_bonus` smallint unsigned NOT NULL DEFAULT '0',
  `fleet_structure_bonus` smallint unsigned NOT NULL DEFAULT '0',
  `entity_weapon` bigint unsigned NOT NULL DEFAULT '0',
  `entity_shield` bigint unsigned NOT NULL DEFAULT '0',
  `entity_structure` bigint unsigned NOT NULL DEFAULT '0',
  `entity_weapon_bonus` smallint unsigned NOT NULL DEFAULT '0',
  `entity_shield_bonus` smallint unsigned NOT NULL DEFAULT '0',
  `entity_structure_bonus` smallint unsigned NOT NULL DEFAULT '0',
  `fleet_win_exp` int NOT NULL DEFAULT '-1',
  `entity_win_exp` int NOT NULL DEFAULT '-1',
  `win_metal` bigint unsigned NOT NULL DEFAULT '0',
  `win_crystal` bigint unsigned NOT NULL DEFAULT '0',
  `win_pvc` bigint unsigned NOT NULL DEFAULT '0',
  `win_tritium` bigint unsigned NOT NULL DEFAULT '0',
  `win_food` bigint unsigned NOT NULL DEFAULT '0',
  `tf_metal` bigint unsigned NOT NULL DEFAULT '0',
  `tf_crystal` bigint unsigned NOT NULL DEFAULT '0',
  `tf_pvc` bigint unsigned NOT NULL DEFAULT '0',
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 DELAY_KEY_WRITE=1;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.logs_debris
CREATE TABLE IF NOT EXISTS `logs_debris` (
  `id` int NOT NULL AUTO_INCREMENT,
  `time` int NOT NULL,
  `admin_id` int NOT NULL,
  `user_id` int NOT NULL,
  `metal` bigint NOT NULL,
  `crystal` bigint NOT NULL,
  `plastic` bigint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.logs_fleet
CREATE TABLE IF NOT EXISTS `logs_fleet` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `facility` tinyint unsigned NOT NULL DEFAULT '0',
  `severity` tinyint unsigned NOT NULL DEFAULT '1',
  `fleet_id` int unsigned NOT NULL DEFAULT '0',
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `entity_user_id` int unsigned NOT NULL DEFAULT '0',
  `entity_from` int unsigned NOT NULL DEFAULT '0',
  `entity_to` int unsigned NOT NULL DEFAULT '0',
  `launchtime` int unsigned NOT NULL DEFAULT '0',
  `landtime` int unsigned NOT NULL DEFAULT '0',
  `action` char(15) NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '0',
  `fleet_res_start` text NOT NULL,
  `fleet_res_end` text NOT NULL,
  `fleet_ships_start` text NOT NULL,
  `fleet_ships_end` text NOT NULL,
  `entity_res_start` text NOT NULL,
  `entity_res_end` text NOT NULL,
  `entity_ships_start` text NOT NULL,
  `entity_ships_end` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb3 DELAY_KEY_WRITE=1;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.logs_fleet_queue
CREATE TABLE IF NOT EXISTS `logs_fleet_queue` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `facility` tinyint unsigned NOT NULL DEFAULT '0',
  `severity` tinyint unsigned NOT NULL DEFAULT '1',
  `fleet_id` int unsigned NOT NULL DEFAULT '0',
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `entity_user_id` int unsigned NOT NULL DEFAULT '0',
  `entity_from` int unsigned NOT NULL DEFAULT '0',
  `entity_to` int unsigned NOT NULL DEFAULT '0',
  `launchtime` int unsigned NOT NULL DEFAULT '0',
  `landtime` int unsigned NOT NULL DEFAULT '0',
  `action` char(15) NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '0',
  `fleet_res_start` text NOT NULL,
  `fleet_res_end` text NOT NULL,
  `fleet_ships_start` text NOT NULL,
  `fleet_ships_end` text NOT NULL,
  `entity_res_start` text NOT NULL,
  `entity_res_end` text NOT NULL,
  `entity_ships_start` text NOT NULL,
  `entity_ships_end` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 DELAY_KEY_WRITE=1;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.logs_game
CREATE TABLE IF NOT EXISTS `logs_game` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `facility` tinyint unsigned NOT NULL DEFAULT '0',
  `severity` tinyint unsigned NOT NULL DEFAULT '1',
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `ip` varchar(39) NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `alliance_id` int unsigned DEFAULT NULL,
  `entity_id` int unsigned DEFAULT NULL,
  `object_id` int unsigned DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '0',
  `level` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `facility` (`facility`,`severity`,`timestamp`),
  KEY `severity` (`severity`,`facility`,`timestamp`),
  KEY `logs_game_user_facility_timestamp_idx` (`user_id`,`facility`),
  KEY `logs_game_facility_object_idx` (`facility`,`object_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 DELAY_KEY_WRITE=1;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.logs_game_queue
CREATE TABLE IF NOT EXISTS `logs_game_queue` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `facility` tinyint unsigned NOT NULL DEFAULT '0',
  `severity` tinyint unsigned NOT NULL DEFAULT '1',
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `ip` varchar(39) NOT NULL,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `alliance_id` int unsigned NOT NULL DEFAULT '0',
  `entity_id` int unsigned NOT NULL DEFAULT '0',
  `object_id` int unsigned NOT NULL DEFAULT '0',
  `status` tinyint unsigned NOT NULL DEFAULT '0',
  `level` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 DELAY_KEY_WRITE=1;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.logs_queue
CREATE TABLE IF NOT EXISTS `logs_queue` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `facility` tinyint unsigned NOT NULL DEFAULT '0',
  `severity` tinyint unsigned NOT NULL DEFAULT '1',
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `ip` varchar(39) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 DELAY_KEY_WRITE=1;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.market_auction
CREATE TABLE IF NOT EXISTS `market_auction` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `entity_id` int unsigned NOT NULL DEFAULT '0',
  `date_start` int unsigned NOT NULL DEFAULT '0',
  `date_end` int unsigned NOT NULL DEFAULT '0',
  `date_delete` int unsigned NOT NULL DEFAULT '0',
  `sell_0` bigint unsigned NOT NULL DEFAULT '0',
  `sell_1` bigint unsigned NOT NULL DEFAULT '0',
  `sell_2` bigint unsigned NOT NULL DEFAULT '0',
  `sell_3` bigint unsigned NOT NULL DEFAULT '0',
  `sell_4` bigint unsigned NOT NULL DEFAULT '0',
  `ship_id` int unsigned DEFAULT NULL,
  `ship_count` int unsigned NOT NULL DEFAULT '0',
  `text` varchar(255) NOT NULL,
  `currency_0` tinyint unsigned NOT NULL DEFAULT '1',
  `currency_1` tinyint unsigned NOT NULL DEFAULT '1',
  `currency_2` tinyint unsigned NOT NULL DEFAULT '1',
  `currency_3` tinyint unsigned NOT NULL DEFAULT '1',
  `currency_4` tinyint unsigned NOT NULL DEFAULT '1',
  `current_buyer_id` int unsigned DEFAULT NULL,
  `current_buyer_entity_id` int unsigned DEFAULT NULL,
  `current_buyer_date` int unsigned DEFAULT NULL,
  `buy_0` bigint unsigned NOT NULL DEFAULT '0',
  `buy_1` bigint unsigned NOT NULL DEFAULT '0',
  `buy_2` bigint unsigned NOT NULL DEFAULT '0',
  `buy_3` bigint unsigned NOT NULL DEFAULT '0',
  `buy_4` bigint unsigned NOT NULL DEFAULT '0',
  `bidcount` smallint unsigned NOT NULL DEFAULT '0',
  `buyable` tinyint unsigned NOT NULL DEFAULT '1',
  `sent` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `auction_end` (`date_end`),
  KEY `auction_planet_id` (`entity_id`),
  KEY `auction_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.market_rates
CREATE TABLE IF NOT EXISTS `market_rates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `supply_0` int unsigned NOT NULL DEFAULT '0',
  `supply_1` int unsigned NOT NULL DEFAULT '0',
  `supply_2` int unsigned NOT NULL DEFAULT '0',
  `supply_3` int unsigned NOT NULL DEFAULT '0',
  `supply_4` int unsigned NOT NULL DEFAULT '0',
  `supply_5` int unsigned NOT NULL DEFAULT '0',
  `demand_0` int unsigned NOT NULL DEFAULT '0',
  `demand_1` int unsigned NOT NULL DEFAULT '0',
  `demand_2` int unsigned NOT NULL DEFAULT '0',
  `demand_3` int unsigned NOT NULL DEFAULT '0',
  `demand_4` int unsigned NOT NULL DEFAULT '0',
  `demand_5` int unsigned NOT NULL DEFAULT '0',
  `rate_0` float unsigned NOT NULL DEFAULT '1',
  `rate_1` float unsigned NOT NULL DEFAULT '1',
  `rate_2` float unsigned NOT NULL DEFAULT '1',
  `rate_3` float unsigned NOT NULL DEFAULT '1',
  `rate_4` float unsigned NOT NULL DEFAULT '1',
  `rate_5` float unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.market_ressource
CREATE TABLE IF NOT EXISTS `market_ressource` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `entity_id` int unsigned NOT NULL DEFAULT '0',
  `sell_0` bigint unsigned NOT NULL DEFAULT '0',
  `sell_1` bigint unsigned NOT NULL DEFAULT '0',
  `sell_2` bigint unsigned NOT NULL DEFAULT '0',
  `sell_3` bigint unsigned NOT NULL DEFAULT '0',
  `sell_4` bigint unsigned NOT NULL DEFAULT '0',
  `buy_0` bigint unsigned NOT NULL DEFAULT '0',
  `buy_1` bigint unsigned NOT NULL DEFAULT '0',
  `buy_2` bigint unsigned NOT NULL DEFAULT '0',
  `buy_3` bigint unsigned NOT NULL DEFAULT '0',
  `buy_4` bigint unsigned NOT NULL DEFAULT '0',
  `buyer_id` int unsigned DEFAULT NULL,
  `buyer_entity_id` int unsigned DEFAULT NULL,
  `for_user` int unsigned DEFAULT NULL,
  `for_alliance` int unsigned DEFAULT NULL,
  `buyable` tinyint unsigned NOT NULL DEFAULT '1',
  `text` varchar(255) NOT NULL,
  `datum` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `datum` (`datum`),
  KEY `planet_id` (`entity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.market_ship
CREATE TABLE IF NOT EXISTS `market_ship` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `entity_id` int unsigned NOT NULL DEFAULT '0',
  `ship_id` int unsigned NOT NULL DEFAULT '0',
  `count` int unsigned NOT NULL DEFAULT '0',
  `costs_0` bigint unsigned NOT NULL DEFAULT '0',
  `costs_1` bigint unsigned NOT NULL DEFAULT '0',
  `costs_2` bigint unsigned NOT NULL DEFAULT '0',
  `costs_3` bigint unsigned NOT NULL DEFAULT '0',
  `costs_4` bigint unsigned NOT NULL DEFAULT '0',
  `buyer_id` int unsigned DEFAULT NULL,
  `buyer_entity_id` int DEFAULT NULL,
  `for_user` int unsigned DEFAULT NULL,
  `for_alliance` int unsigned DEFAULT NULL,
  `buyable` tinyint unsigned NOT NULL DEFAULT '1',
  `text` varchar(255) NOT NULL,
  `datum` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ship_id` (`ship_id`),
  KEY `datum` (`datum`),
  KEY `planet_id` (`entity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.messages
CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `message_user_from` smallint unsigned DEFAULT '0',
  `message_user_to` smallint unsigned NOT NULL DEFAULT '0',
  `message_timestamp` int unsigned NOT NULL DEFAULT '0',
  `message_cat_id` tinyint unsigned NOT NULL DEFAULT '0',
  `message_read` tinyint unsigned NOT NULL DEFAULT '0',
  `message_archived` tinyint unsigned NOT NULL DEFAULT '0',
  `message_massmail` tinyint unsigned NOT NULL DEFAULT '0',
  `message_deleted` tinyint unsigned NOT NULL DEFAULT '0',
  `message_forwarded` tinyint unsigned NOT NULL DEFAULT '0',
  `message_replied` tinyint unsigned NOT NULL DEFAULT '0',
  `message_mailed` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`message_id`),
  KEY `message_user_from` (`message_user_from`),
  KEY `message_user_to` (`message_user_to`),
  KEY `message_read` (`message_read`),
  KEY `message_timestamp` (`message_timestamp`),
  KEY `list` (`message_user_to`,`message_read`,`message_timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 DELAY_KEY_WRITE=1;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.message_cat
CREATE TABLE IF NOT EXISTS `message_cat` (
  `cat_id` int unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(50) NOT NULL,
  `cat_order` tinyint unsigned NOT NULL DEFAULT '0',
  `cat_desc` text NOT NULL,
  `cat_sender` varchar(50) NOT NULL,
  PRIMARY KEY (`cat_id`),
  KEY `cat_order` (`cat_order`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.message_data
CREATE TABLE IF NOT EXISTS `message_data` (
  `id` mediumint unsigned NOT NULL,
  `subject` varchar(200) NOT NULL,
  `text` text NOT NULL,
  `entity_id` int unsigned DEFAULT '0',
  `fleet_id` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.message_ignore
CREATE TABLE IF NOT EXISTS `message_ignore` (
  `ignore_id` int unsigned NOT NULL AUTO_INCREMENT,
  `ignore_owner_id` int unsigned NOT NULL DEFAULT '0',
  `ignore_target_id` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ignore_id`),
  KEY `ignore_owner_id` (`ignore_owner_id`),
  KEY `ignore_target_id` (`ignore_target_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.messenger_messages
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.missilelist
CREATE TABLE IF NOT EXISTS `missilelist` (
  `missilelist_id` int unsigned NOT NULL AUTO_INCREMENT,
  `missilelist_user_id` int unsigned NOT NULL DEFAULT '0',
  `missilelist_entity_id` int unsigned NOT NULL DEFAULT '0',
  `missilelist_missile_id` int unsigned NOT NULL DEFAULT '0',
  `missilelist_count` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`missilelist_id`),
  KEY `missilelist_missile_id` (`missilelist_missile_id`),
  KEY `missilelist_user_id` (`missilelist_user_id`,`missilelist_entity_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.missiles
CREATE TABLE IF NOT EXISTS `missiles` (
  `missile_id` int unsigned NOT NULL AUTO_INCREMENT,
  `missile_name` varchar(50) NOT NULL,
  `missile_sdesc` text NOT NULL,
  `missile_ldesc` text NOT NULL,
  `missile_costs_metal` int unsigned NOT NULL DEFAULT '0',
  `missile_costs_crystal` int unsigned NOT NULL DEFAULT '0',
  `missile_costs_plastic` int unsigned NOT NULL DEFAULT '0',
  `missile_costs_fuel` int unsigned NOT NULL DEFAULT '0',
  `missile_costs_food` int unsigned NOT NULL DEFAULT '0',
  `missile_damage` int unsigned NOT NULL,
  `missile_speed` int unsigned NOT NULL,
  `missile_range` int unsigned NOT NULL,
  `missile_deactivate` smallint unsigned NOT NULL DEFAULT '0',
  `missile_def` tinyint unsigned NOT NULL DEFAULT '0',
  `missile_launchable` tinyint unsigned NOT NULL DEFAULT '1',
  `missile_show` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`missile_id`),
  KEY `missile_name` (`missile_name`),
  KEY `missile_damage` (`missile_damage`),
  KEY `missile_show` (`missile_show`),
  KEY `missile_launchable` (`missile_launchable`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.missile_flights
CREATE TABLE IF NOT EXISTS `missile_flights` (
  `flight_id` int unsigned NOT NULL AUTO_INCREMENT,
  `flight_entity_from` int unsigned NOT NULL DEFAULT '0',
  `flight_entity_to` int unsigned NOT NULL DEFAULT '0',
  `flight_starttime` int unsigned NOT NULL DEFAULT '0',
  `flight_landtime` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`flight_id`),
  KEY `flight_planet_from` (`flight_entity_from`),
  KEY `flight_planet_to` (`flight_entity_to`),
  KEY `flight_user_id` (`flight_starttime`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.missile_flights_obj
CREATE TABLE IF NOT EXISTS `missile_flights_obj` (
  `obj_id` int unsigned NOT NULL AUTO_INCREMENT,
  `obj_flight_id` int unsigned NOT NULL DEFAULT '0',
  `obj_missile_id` int unsigned NOT NULL DEFAULT '0',
  `obj_cnt` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`obj_id`),
  KEY `obj_flight_id` (`obj_flight_id`),
  KEY `obj_missile_id` (`obj_missile_id`),
  KEY `obj_cnt` (`obj_cnt`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.missile_requirements
CREATE TABLE IF NOT EXISTS `missile_requirements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int unsigned NOT NULL,
  `req_building_id` int unsigned DEFAULT NULL,
  `req_tech_id` int unsigned DEFAULT NULL,
  `req_level` smallint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `obj_building` (`obj_id`,`req_building_id`),
  UNIQUE KEY `obj_tech` (`obj_id`,`req_tech_id`),
  KEY `IDX_F991BD9A66093344` (`obj_id`),
  KEY `IDX_F991BD9A7E57261C` (`req_building_id`),
  KEY `IDX_F991BD9A68C70794` (`req_tech_id`),
  CONSTRAINT `FK_F991BD9A66093344` FOREIGN KEY (`obj_id`) REFERENCES `missiles` (`missile_id`),
  CONSTRAINT `FK_F991BD9A68C70794` FOREIGN KEY (`req_tech_id`) REFERENCES `technologies` (`tech_id`),
  CONSTRAINT `FK_F991BD9A7E57261C` FOREIGN KEY (`req_building_id`) REFERENCES `buildings` (`building_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.nebulas
CREATE TABLE IF NOT EXISTS `nebulas` (
  `id` int unsigned NOT NULL,
  `res_metal` bigint unsigned NOT NULL DEFAULT '0',
  `res_crystal` bigint unsigned NOT NULL DEFAULT '0',
  `res_plastic` bigint unsigned NOT NULL DEFAULT '0',
  `res_fuel` bigint unsigned NOT NULL DEFAULT '0',
  `res_food` bigint unsigned NOT NULL DEFAULT '0',
  `res_power` bigint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.notepad
CREATE TABLE IF NOT EXISTS `notepad` (
  `id` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` smallint unsigned NOT NULL DEFAULT '0',
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `list` (`user_id`,`timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.notepad_data
CREATE TABLE IF NOT EXISTS `notepad_data` (
  `id` mediumint unsigned NOT NULL,
  `subject` varchar(100) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.obj_transforms
CREATE TABLE IF NOT EXISTS `obj_transforms` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `def_id` int unsigned NOT NULL DEFAULT '0',
  `ship_id` int unsigned NOT NULL DEFAULT '0',
  `costs_metal` int unsigned NOT NULL DEFAULT '0',
  `costs_crystal` int unsigned NOT NULL DEFAULT '0',
  `costs_plastic` int unsigned NOT NULL DEFAULT '0',
  `costs_fuel` int unsigned NOT NULL DEFAULT '0',
  `costs_food` int unsigned NOT NULL DEFAULT '0',
  `costs_power` int unsigned NOT NULL DEFAULT '0',
  `costs_factor_sd` decimal(2,1) unsigned NOT NULL DEFAULT '0.0',
  `costs_factor_ds` decimal(2,1) unsigned NOT NULL DEFAULT '1.0',
  `num_def` int unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `def_id` (`def_id`),
  UNIQUE KEY `ship_id` (`ship_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.planets
CREATE TABLE IF NOT EXISTS `planets` (
  `id` int unsigned NOT NULL,
  `planet_user_id` int unsigned DEFAULT NULL,
  `planet_user_main` tinyint unsigned NOT NULL DEFAULT '0',
  `planet_user_changed` int unsigned NOT NULL DEFAULT '0',
  `planet_last_user_id` int unsigned DEFAULT NULL,
  `planet_name` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `planet_type_id` tinyint unsigned NOT NULL DEFAULT '0',
  `planet_fields` int unsigned NOT NULL DEFAULT '0',
  `planet_fields_extra` int unsigned NOT NULL DEFAULT '0',
  `planet_fields_used` int unsigned NOT NULL DEFAULT '0',
  `planet_image` varchar(50) NOT NULL DEFAULT '0',
  `planet_temp_from` int NOT NULL DEFAULT '0',
  `planet_temp_to` int NOT NULL DEFAULT '0',
  `planet_semi_major_axis` decimal(5,3) unsigned NOT NULL DEFAULT '1.000',
  `planet_ecccentricity` decimal(4,3) unsigned NOT NULL DEFAULT '0.000',
  `planet_mass` int unsigned NOT NULL DEFAULT '1',
  `planet_res_metal` decimal(18,6) unsigned NOT NULL DEFAULT '0.000000',
  `planet_res_crystal` decimal(18,6) unsigned NOT NULL DEFAULT '0.000000',
  `planet_res_fuel` decimal(18,6) unsigned NOT NULL DEFAULT '0.000000',
  `planet_res_plastic` decimal(18,6) unsigned NOT NULL DEFAULT '0.000000',
  `planet_res_food` decimal(18,6) unsigned NOT NULL DEFAULT '0.000000',
  `planet_use_power` int unsigned NOT NULL DEFAULT '0',
  `planet_last_updated` int unsigned NOT NULL DEFAULT '0',
  `planet_bunker_metal` int unsigned NOT NULL DEFAULT '0',
  `planet_bunker_crystal` int unsigned NOT NULL DEFAULT '0',
  `planet_bunker_plastic` int unsigned NOT NULL DEFAULT '0',
  `planet_bunker_fuel` int unsigned NOT NULL DEFAULT '0',
  `planet_bunker_food` int unsigned NOT NULL DEFAULT '0',
  `planet_prod_metal` int unsigned NOT NULL DEFAULT '0',
  `planet_prod_crystal` int unsigned NOT NULL DEFAULT '0',
  `planet_prod_plastic` int unsigned NOT NULL DEFAULT '0',
  `planet_prod_fuel` int unsigned NOT NULL DEFAULT '0',
  `planet_prod_food` int unsigned NOT NULL DEFAULT '0',
  `planet_prod_power` int unsigned NOT NULL DEFAULT '0',
  `planet_prod_people` int unsigned NOT NULL DEFAULT '0',
  `planet_store_metal` bigint unsigned NOT NULL DEFAULT '0',
  `planet_store_crystal` bigint unsigned NOT NULL DEFAULT '0',
  `planet_store_plastic` bigint unsigned NOT NULL DEFAULT '0',
  `planet_store_fuel` bigint unsigned NOT NULL DEFAULT '0',
  `planet_store_food` bigint unsigned NOT NULL DEFAULT '0',
  `planet_wf_metal` bigint unsigned NOT NULL DEFAULT '0',
  `planet_wf_crystal` bigint unsigned NOT NULL DEFAULT '0',
  `planet_wf_plastic` bigint unsigned NOT NULL DEFAULT '0',
  `planet_people` decimal(18,6) unsigned NOT NULL DEFAULT '0.000000',
  `planet_people_place` int unsigned NOT NULL DEFAULT '0',
  `planet_desc` text,
  `invadedby` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `planet_name` (`planet_name`),
  KEY `planet_last_updated` (`planet_last_updated`),
  KEY `mainplanet` (`planet_user_id`,`planet_user_main`,`planet_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 DELAY_KEY_WRITE=1;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.planet_types
CREATE TABLE IF NOT EXISTS `planet_types` (
  `type_id` int unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `type_habitable` int unsigned NOT NULL DEFAULT '1',
  `type_comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `type_f_metal` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `type_f_crystal` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `type_f_plastic` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `type_f_fuel` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `type_f_food` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `type_f_power` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `type_f_population` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `type_f_researchtime` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `type_f_buildtime` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `type_collect_gas` tinyint unsigned NOT NULL DEFAULT '0',
  `type_consider` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`type_id`),
  KEY `type_name` (`type_name`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.quests
CREATE TABLE IF NOT EXISTS `quests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `quest_data_id` int NOT NULL,
  `slot_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `state` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_state_idx` (`user_id`,`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.quest_log
CREATE TABLE IF NOT EXISTS `quest_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quest_id` int NOT NULL,
  `user_id` int NOT NULL,
  `quest_data_id` int NOT NULL,
  `slot_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `previous_state` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `transition` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `date` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.quest_tasks
CREATE TABLE IF NOT EXISTS `quest_tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quest_id` int NOT NULL,
  `task_id` int NOT NULL,
  `progress` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `quest_fk` (`quest_id`),
  CONSTRAINT `quest_fk` FOREIGN KEY (`quest_id`) REFERENCES `quests` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.races
CREATE TABLE IF NOT EXISTS `races` (
  `race_id` int unsigned NOT NULL AUTO_INCREMENT,
  `race_name` varchar(50) NOT NULL,
  `race_comment` text NOT NULL,
  `race_short_comment` text NOT NULL,
  `race_adj1` varchar(50) NOT NULL,
  `race_adj2` varchar(50) NOT NULL,
  `race_adj3` varchar(50) NOT NULL,
  `race_leadertitle` varchar(30) NOT NULL,
  `race_f_researchtime` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `race_f_buildtime` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `race_f_fleettime` decimal(4,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Actualli this means speed rather than time',
  `race_f_metal` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `race_f_crystal` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `race_f_plastic` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `race_f_fuel` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `race_f_food` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `race_f_power` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `race_f_population` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `race_active` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`race_id`),
  KEY `race_name` (`race_name`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.reports
CREATE TABLE IF NOT EXISTS `reports` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `type` enum('battle','spy','explore','market','crypto','other') NOT NULL DEFAULT 'other',
  `read` tinyint unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint unsigned NOT NULL DEFAULT '0',
  `archived` tinyint unsigned NOT NULL DEFAULT '0',
  `user_id` smallint unsigned NOT NULL,
  `alliance_id` int unsigned DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `content` text,
  `entity1_id` int unsigned NOT NULL DEFAULT '0',
  `entity2_id` int unsigned DEFAULT NULL,
  `opponent1_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `reports_user_type_entity1_idx` (`user_id`,`type`,`entity1_id`),
  KEY `reports_user_read_deleted_idx` (`user_id`,`read`,`deleted`),
  KEY `reports_user_archived_type_idx` (`user_id`,`archived`,`type`),
  KEY `reports_user_deleted_archived_idx` (`user_id`,`deleted`,`archived`,`timestamp`),
  CONSTRAINT `reports_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9207492 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.reports_battle
CREATE TABLE IF NOT EXISTS `reports_battle` (
  `id` int unsigned NOT NULL,
  `subtype` char(20) NOT NULL DEFAULT 'other',
  `fleet_id` int unsigned NOT NULL DEFAULT '0',
  `user` text NOT NULL,
  `entity_user` text NOT NULL,
  `ships` text NOT NULL,
  `entity_ships` text NOT NULL,
  `entity_def` text NOT NULL,
  `weapon_tech` smallint unsigned NOT NULL DEFAULT '100',
  `shield_tech` smallint unsigned NOT NULL DEFAULT '100',
  `structure_tech` smallint unsigned NOT NULL DEFAULT '100',
  `weapon_1` bigint unsigned NOT NULL DEFAULT '0',
  `weapon_2` bigint unsigned NOT NULL DEFAULT '0',
  `weapon_3` bigint unsigned NOT NULL DEFAULT '0',
  `weapon_4` bigint unsigned NOT NULL DEFAULT '0',
  `weapon_5` bigint unsigned NOT NULL DEFAULT '0',
  `shield` bigint unsigned NOT NULL DEFAULT '0',
  `structure` bigint unsigned NOT NULL DEFAULT '0',
  `heal_1` bigint unsigned NOT NULL DEFAULT '0',
  `heal_2` bigint unsigned NOT NULL DEFAULT '0',
  `heal_3` bigint unsigned NOT NULL DEFAULT '0',
  `heal_4` bigint unsigned NOT NULL DEFAULT '0',
  `heal_5` bigint unsigned NOT NULL DEFAULT '0',
  `count_1` int unsigned NOT NULL DEFAULT '0',
  `count_2` int unsigned NOT NULL DEFAULT '0',
  `count_3` int unsigned NOT NULL DEFAULT '0',
  `count_4` int unsigned NOT NULL DEFAULT '0',
  `count_5` int unsigned NOT NULL DEFAULT '0',
  `exp` int NOT NULL DEFAULT '-1',
  `entity_weapon_tech` smallint unsigned NOT NULL DEFAULT '100',
  `entity_shield_tech` smallint unsigned NOT NULL DEFAULT '100',
  `entity_structure_tech` smallint unsigned NOT NULL DEFAULT '100',
  `entity_weapon_1` bigint unsigned NOT NULL DEFAULT '0',
  `entity_weapon_2` bigint unsigned NOT NULL DEFAULT '0',
  `entity_weapon_3` bigint unsigned NOT NULL DEFAULT '0',
  `entity_weapon_4` bigint unsigned NOT NULL DEFAULT '0',
  `entity_weapon_5` bigint unsigned NOT NULL DEFAULT '0',
  `entity_shield` bigint unsigned NOT NULL DEFAULT '0',
  `entity_structure` bigint unsigned NOT NULL DEFAULT '0',
  `entity_heal_1` bigint unsigned NOT NULL DEFAULT '0',
  `entity_heal_2` bigint unsigned NOT NULL DEFAULT '0',
  `entity_heal_3` bigint unsigned NOT NULL DEFAULT '0',
  `entity_heal_4` bigint unsigned NOT NULL DEFAULT '0',
  `entity_heal_5` bigint unsigned NOT NULL DEFAULT '0',
  `entity_count_1` int unsigned NOT NULL DEFAULT '0',
  `entity_count_2` int unsigned NOT NULL DEFAULT '0',
  `entity_count_3` int unsigned NOT NULL DEFAULT '0',
  `entity_count_4` int unsigned NOT NULL DEFAULT '0',
  `entity_count_5` int unsigned NOT NULL DEFAULT '0',
  `entity_exp` int NOT NULL DEFAULT '-1',
  `res_0` bigint unsigned NOT NULL DEFAULT '0',
  `res_1` bigint unsigned NOT NULL DEFAULT '0',
  `res_2` bigint unsigned NOT NULL DEFAULT '0',
  `res_3` bigint unsigned NOT NULL DEFAULT '0',
  `res_4` bigint unsigned NOT NULL DEFAULT '0',
  `res_5` bigint unsigned NOT NULL DEFAULT '0',
  `wf_0` bigint unsigned NOT NULL DEFAULT '0',
  `wf_1` bigint unsigned NOT NULL DEFAULT '0',
  `wf_2` bigint unsigned NOT NULL DEFAULT '0',
  `ships_end` text NOT NULL,
  `entity_ships_end` text NOT NULL,
  `entity_def_end` text NOT NULL,
  `restore` smallint unsigned NOT NULL DEFAULT '0',
  `result` tinyint unsigned NOT NULL DEFAULT '0',
  `restore_civil_ships` smallint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_REPORTS_BATTLE` FOREIGN KEY (`id`) REFERENCES `reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.reports_market
CREATE TABLE IF NOT EXISTS `reports_market` (
  `id` int unsigned NOT NULL,
  `subtype` char(20) NOT NULL DEFAULT 'other',
  `record_id` int unsigned NOT NULL DEFAULT '0',
  `sell_0` bigint unsigned NOT NULL DEFAULT '0',
  `sell_1` bigint unsigned NOT NULL DEFAULT '0',
  `sell_2` bigint unsigned NOT NULL DEFAULT '0',
  `sell_3` bigint unsigned NOT NULL DEFAULT '0',
  `sell_4` bigint unsigned NOT NULL DEFAULT '0',
  `sell_5` bigint unsigned NOT NULL DEFAULT '0',
  `buy_0` bigint unsigned NOT NULL DEFAULT '0',
  `buy_1` bigint unsigned NOT NULL DEFAULT '0',
  `buy_2` bigint unsigned NOT NULL DEFAULT '0',
  `buy_3` bigint unsigned NOT NULL DEFAULT '0',
  `buy_4` bigint unsigned NOT NULL DEFAULT '0',
  `buy_5` bigint unsigned NOT NULL DEFAULT '0',
  `factor` float NOT NULL DEFAULT '1',
  `fleet1_id` int unsigned DEFAULT NULL,
  `fleet2_id` int unsigned DEFAULT NULL,
  `ship_id` int unsigned DEFAULT NULL,
  `ship_count` int unsigned NOT NULL DEFAULT '0',
  `timestamp2` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_REPORTS_MARKET` FOREIGN KEY (`id`) REFERENCES `reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.reports_other
CREATE TABLE IF NOT EXISTS `reports_other` (
  `id` int unsigned NOT NULL,
  `subtype` char(20) NOT NULL DEFAULT 'other',
  `fleet_id` int unsigned NOT NULL DEFAULT '0',
  `res_0` bigint unsigned NOT NULL DEFAULT '0',
  `res_1` bigint unsigned NOT NULL DEFAULT '0',
  `res_2` bigint unsigned NOT NULL DEFAULT '0',
  `res_3` bigint unsigned NOT NULL DEFAULT '0',
  `res_4` bigint unsigned NOT NULL DEFAULT '0',
  `res_5` bigint unsigned NOT NULL DEFAULT '0',
  `ships` text NOT NULL,
  `action` char(20) NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_REPORTS_OTHER` FOREIGN KEY (`id`) REFERENCES `reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.reports_spy
CREATE TABLE IF NOT EXISTS `reports_spy` (
  `id` int unsigned NOT NULL,
  `subtype` char(20) NOT NULL DEFAULT 'other',
  `buildings` text NOT NULL,
  `technologies` text NOT NULL,
  `ships` text NOT NULL,
  `defense` text NOT NULL,
  `res_0` bigint unsigned NOT NULL DEFAULT '0',
  `res_1` bigint unsigned NOT NULL DEFAULT '0',
  `res_2` bigint unsigned NOT NULL DEFAULT '0',
  `res_3` bigint unsigned NOT NULL DEFAULT '0',
  `res_4` bigint unsigned NOT NULL DEFAULT '0',
  `res_5` bigint unsigned NOT NULL DEFAULT '0',
  `spydefense` smallint unsigned NOT NULL DEFAULT '0',
  `coverage` smallint unsigned NOT NULL,
  `fleet_id` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_REPORTS_SPY` FOREIGN KEY (`id`) REFERENCES `reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.runtime_data
CREATE TABLE IF NOT EXISTS `runtime_data` (
  `data_key` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `data_value` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`data_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.schema_migrations
CREATE TABLE IF NOT EXISTS `schema_migrations` (
  `version` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.shiplist
CREATE TABLE IF NOT EXISTS `shiplist` (
  `shiplist_id` int unsigned NOT NULL AUTO_INCREMENT,
  `shiplist_user_id` int unsigned NOT NULL DEFAULT '0',
  `shiplist_ship_id` int unsigned NOT NULL DEFAULT '0',
  `shiplist_entity_id` int unsigned NOT NULL DEFAULT '0',
  `shiplist_bot_id` int unsigned NOT NULL DEFAULT '0',
  `shiplist_count` int unsigned NOT NULL DEFAULT '0',
  `shiplist_bunkered` int unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship` tinyint unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_level` tinyint unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_exp` int unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_weapon` tinyint unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_structure` tinyint unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_shield` tinyint unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_heal` tinyint unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_capacity` tinyint unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_speed` tinyint unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_pilots` tinyint unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_tarn` tinyint unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_antrax` tinyint unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_forsteal` tinyint unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_build_destroy` tinyint unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_antrax_food` tinyint unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_deactivade` tinyint unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_readiness` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`shiplist_id`),
  UNIQUE KEY `user_entity_ship_id` (`shiplist_user_id`,`shiplist_entity_id`,`shiplist_ship_id`)
) ENGINE=MyISAM AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.ships
CREATE TABLE IF NOT EXISTS `ships` (
  `ship_id` int unsigned NOT NULL AUTO_INCREMENT,
  `ship_name` varchar(50) NOT NULL,
  `ship_type_id` tinyint unsigned NOT NULL DEFAULT '1',
  `ship_shortcomment` text NOT NULL,
  `ship_longcomment` text NOT NULL,
  `ship_costs_metal` int unsigned NOT NULL DEFAULT '0',
  `ship_costs_crystal` int unsigned NOT NULL DEFAULT '0',
  `ship_costs_fuel` int unsigned NOT NULL DEFAULT '0',
  `ship_costs_plastic` int unsigned NOT NULL DEFAULT '0',
  `ship_costs_food` int unsigned NOT NULL DEFAULT '0',
  `ship_costs_power` int unsigned NOT NULL DEFAULT '0',
  `ship_power_use` int unsigned NOT NULL DEFAULT '0',
  `ship_fuel_use` int unsigned NOT NULL DEFAULT '0',
  `ship_fuel_use_launch` int unsigned NOT NULL DEFAULT '0',
  `ship_fuel_use_landing` int unsigned NOT NULL DEFAULT '0',
  `ship_prod_power` int unsigned NOT NULL DEFAULT '0',
  `ship_capacity` int unsigned NOT NULL DEFAULT '0',
  `ship_people_capacity` int unsigned NOT NULL DEFAULT '0',
  `ship_pilots` int unsigned NOT NULL DEFAULT '1',
  `ship_speed` int unsigned NOT NULL DEFAULT '1',
  `ship_time2start` int unsigned NOT NULL DEFAULT '0',
  `ship_time2land` int unsigned NOT NULL DEFAULT '0',
  `ship_show` tinyint unsigned NOT NULL DEFAULT '1',
  `ship_buildable` tinyint unsigned NOT NULL DEFAULT '1',
  `ship_order` tinyint unsigned NOT NULL DEFAULT '0',
  `ship_actions` text NOT NULL,
  `ship_bounty_bonus` decimal(4,2) unsigned NOT NULL DEFAULT '0.50',
  `ship_heal` int unsigned NOT NULL DEFAULT '0',
  `ship_structure` bigint unsigned NOT NULL DEFAULT '0',
  `ship_shield` bigint unsigned NOT NULL DEFAULT '0',
  `ship_weapon` bigint unsigned NOT NULL DEFAULT '0',
  `ship_race_id` tinyint unsigned NOT NULL DEFAULT '0',
  `ship_launchable` tinyint unsigned NOT NULL DEFAULT '1',
  `ship_fieldsprovide` tinyint unsigned NOT NULL DEFAULT '0',
  `ship_cat_id` tinyint unsigned NOT NULL DEFAULT '0',
  `ship_fakeable` tinyint unsigned NOT NULL DEFAULT '0',
  `special_ship` tinyint unsigned NOT NULL DEFAULT '0',
  `ship_max_count` int unsigned NOT NULL DEFAULT '0',
  `special_ship_max_level` tinyint unsigned NOT NULL DEFAULT '0',
  `special_ship_need_exp` int unsigned NOT NULL DEFAULT '0',
  `special_ship_exp_factor` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `special_ship_bonus_weapon` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `special_ship_bonus_structure` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `special_ship_bonus_shield` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `special_ship_bonus_heal` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `special_ship_bonus_capacity` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `special_ship_bonus_speed` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `special_ship_bonus_pilots` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `special_ship_bonus_tarn` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `special_ship_bonus_antrax` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `special_ship_bonus_forsteal` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `special_ship_bonus_build_destroy` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `special_ship_bonus_antrax_food` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `special_ship_bonus_deactivade` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `special_ship_bonus_readiness` decimal(4,2) NOT NULL,
  `ship_points` decimal(18,3) unsigned NOT NULL DEFAULT '0.000',
  `ship_alliance_shipyard_level` tinyint unsigned NOT NULL DEFAULT '0',
  `ship_alliance_costs` mediumint unsigned NOT NULL DEFAULT '0',
  `ship_tradable` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`ship_id`),
  KEY `ship_order` (`ship_order`),
  KEY `ship_battlepoints` (`ship_points`),
  KEY `ship_name` (`ship_name`)
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.ship_cat
CREATE TABLE IF NOT EXISTS `ship_cat` (
  `cat_id` int unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(50) NOT NULL,
  `cat_order` smallint unsigned NOT NULL DEFAULT '0',
  `cat_color` char(7) NOT NULL DEFAULT '#ffffff',
  PRIMARY KEY (`cat_id`),
  KEY `cat_name` (`cat_name`),
  KEY `cat_order` (`cat_order`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.ship_queue
CREATE TABLE IF NOT EXISTS `ship_queue` (
  `queue_id` int unsigned NOT NULL AUTO_INCREMENT,
  `queue_user_id` int unsigned NOT NULL DEFAULT '0',
  `queue_ship_id` int unsigned NOT NULL DEFAULT '0',
  `queue_entity_id` int unsigned NOT NULL DEFAULT '0',
  `queue_cnt` int unsigned NOT NULL DEFAULT '0',
  `queue_starttime` int unsigned NOT NULL DEFAULT '0',
  `queue_endtime` int unsigned NOT NULL DEFAULT '0',
  `queue_objtime` int unsigned NOT NULL DEFAULT '0',
  `queue_build_type` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`queue_id`),
  KEY `queue_user_id` (`queue_user_id`),
  KEY `queue_planet_id` (`queue_entity_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.ship_requirements
CREATE TABLE IF NOT EXISTS `ship_requirements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int unsigned NOT NULL,
  `req_building_id` int unsigned DEFAULT NULL,
  `req_tech_id` int unsigned DEFAULT NULL,
  `req_level` smallint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `obj_building` (`obj_id`,`req_building_id`),
  UNIQUE KEY `obj_tech` (`obj_id`,`req_tech_id`),
  KEY `IDX_4F2112CA66093344` (`obj_id`),
  KEY `IDX_4F2112CA7E57261C` (`req_building_id`),
  KEY `IDX_4F2112CA68C70794` (`req_tech_id`),
  CONSTRAINT `FK_4F2112CA66093344` FOREIGN KEY (`obj_id`) REFERENCES `ships` (`ship_id`),
  CONSTRAINT `FK_4F2112CA68C70794` FOREIGN KEY (`req_tech_id`) REFERENCES `technologies` (`tech_id`),
  CONSTRAINT `FK_4F2112CA7E57261C` FOREIGN KEY (`req_building_id`) REFERENCES `buildings` (`building_id`)
) ENGINE=InnoDB AUTO_INCREMENT=454 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.sol_types
CREATE TABLE IF NOT EXISTS `sol_types` (
  `sol_type_id` int unsigned NOT NULL AUTO_INCREMENT,
  `sol_type_name` varchar(50) NOT NULL,
  `sol_type_f_metal` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `sol_type_f_crystal` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `sol_type_f_plastic` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `sol_type_f_fuel` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `sol_type_f_food` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `sol_type_f_power` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `sol_type_f_population` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `sol_type_f_buildtime` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `sol_type_comment` text NOT NULL,
  `sol_type_f_researchtime` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `sol_type_consider` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`sol_type_id`),
  KEY `type_name` (`sol_type_name`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.space
CREATE TABLE IF NOT EXISTS `space` (
  `id` int unsigned NOT NULL,
  `lastvisited` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.specialists
CREATE TABLE IF NOT EXISTS `specialists` (
  `specialist_id` int unsigned NOT NULL AUTO_INCREMENT,
  `specialist_name` char(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `specialist_desc` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `specialist_enabled` tinyint unsigned NOT NULL DEFAULT '1',
  `specialist_points_req` int unsigned NOT NULL DEFAULT '0',
  `specialist_costs_metal` int unsigned NOT NULL DEFAULT '0',
  `specialist_costs_crystal` int unsigned NOT NULL DEFAULT '0',
  `specialist_costs_plastic` int unsigned NOT NULL DEFAULT '0',
  `specialist_costs_fuel` int unsigned NOT NULL DEFAULT '0',
  `specialist_costs_food` int unsigned NOT NULL DEFAULT '0',
  `specialist_days` tinyint unsigned NOT NULL DEFAULT '14',
  `specialist_prod_metal` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_prod_crystal` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_prod_plastic` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_prod_fuel` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_prod_food` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_power` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_population` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_time_tech` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_time_buildings` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_time_defense` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_time_ships` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_costs_buildings` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_costs_defense` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_costs_ships` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_costs_tech` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_fleet_speed` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_fleet_max` tinyint unsigned NOT NULL DEFAULT '0',
  `specialist_def_repair` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_spy_level` tinyint unsigned NOT NULL DEFAULT '0',
  `specialist_tarn_level` tinyint unsigned NOT NULL DEFAULT '0',
  `specialist_trade_time` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_trade_bonus` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  PRIMARY KEY (`specialist_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.stars
CREATE TABLE IF NOT EXISTS `stars` (
  `id` int unsigned NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `type_id` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.techlist
CREATE TABLE IF NOT EXISTS `techlist` (
  `techlist_id` int unsigned NOT NULL AUTO_INCREMENT,
  `techlist_user_id` int unsigned NOT NULL DEFAULT '0',
  `techlist_tech_id` int unsigned NOT NULL DEFAULT '0',
  `techlist_entity_id` int unsigned NOT NULL DEFAULT '0',
  `techlist_current_level` tinyint unsigned NOT NULL DEFAULT '0',
  `techlist_build_type` tinyint unsigned NOT NULL DEFAULT '0',
  `techlist_build_start_time` int unsigned NOT NULL DEFAULT '0',
  `techlist_build_end_time` int unsigned NOT NULL DEFAULT '0',
  `techlist_prod_percent` int unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`techlist_id`),
  UNIQUE KEY `user_technology` (`techlist_user_id`,`techlist_tech_id`),
  KEY `techlist_user_id` (`techlist_user_id`),
  KEY `techlist_tech_id` (`techlist_tech_id`),
  KEY `techlist_planet_id` (`techlist_entity_id`),
  KEY `techlist_build_end_time` (`techlist_build_end_time`)
) ENGINE=MyISAM AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.technologies
CREATE TABLE IF NOT EXISTS `technologies` (
  `tech_id` int unsigned NOT NULL AUTO_INCREMENT,
  `tech_name` varchar(50) NOT NULL,
  `tech_type_id` tinyint unsigned NOT NULL DEFAULT '1',
  `tech_shortcomment` text NOT NULL,
  `tech_longcomment` text NOT NULL,
  `tech_costs_metal` int unsigned NOT NULL DEFAULT '0',
  `tech_costs_crystal` int unsigned NOT NULL DEFAULT '0',
  `tech_costs_fuel` int unsigned NOT NULL DEFAULT '0',
  `tech_costs_plastic` int unsigned NOT NULL DEFAULT '0',
  `tech_costs_food` int unsigned NOT NULL DEFAULT '0',
  `tech_costs_power` int unsigned NOT NULL DEFAULT '0',
  `tech_build_costs_factor` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `tech_last_level` tinyint unsigned NOT NULL DEFAULT '99',
  `tech_show` tinyint unsigned NOT NULL DEFAULT '1',
  `tech_order` tinyint unsigned NOT NULL DEFAULT '0',
  `tech_stealable` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`tech_id`),
  KEY `tech_name` (`tech_name`),
  KEY `tech_order` (`tech_order`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.tech_points
CREATE TABLE IF NOT EXISTS `tech_points` (
  `bp_id` int unsigned NOT NULL AUTO_INCREMENT,
  `bp_tech_id` int unsigned NOT NULL,
  `bp_level` tinyint unsigned NOT NULL,
  `bp_points` decimal(20,3) unsigned NOT NULL,
  PRIMARY KEY (`bp_id`),
  KEY `bp_tech_id` (`bp_tech_id`),
  KEY `bp_level` (`bp_level`),
  KEY `bp_points` (`bp_points`)
) ENGINE=MyISAM AUTO_INCREMENT=11786 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.tech_requirements
CREATE TABLE IF NOT EXISTS `tech_requirements` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int unsigned NOT NULL,
  `req_building_id` int unsigned DEFAULT NULL,
  `req_tech_id` int unsigned DEFAULT NULL,
  `req_level` smallint NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `obj_building` (`obj_id`,`req_building_id`),
  UNIQUE KEY `obj_tech` (`obj_id`,`req_tech_id`),
  KEY `IDX_541D739466093344` (`obj_id`),
  KEY `IDX_541D73947E57261C` (`req_building_id`),
  KEY `IDX_541D739468C70794` (`req_tech_id`),
  CONSTRAINT `FK_541D739466093344` FOREIGN KEY (`obj_id`) REFERENCES `technologies` (`tech_id`),
  CONSTRAINT `FK_541D739468C70794` FOREIGN KEY (`req_tech_id`) REFERENCES `technologies` (`tech_id`),
  CONSTRAINT `FK_541D73947E57261C` FOREIGN KEY (`req_building_id`) REFERENCES `buildings` (`building_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.tech_types
CREATE TABLE IF NOT EXISTS `tech_types` (
  `type_id` int unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL,
  `type_order` smallint unsigned NOT NULL DEFAULT '0',
  `type_color` char(7) NOT NULL DEFAULT '#ffffff',
  PRIMARY KEY (`type_id`),
  KEY `type_name` (`type_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.texts
CREATE TABLE IF NOT EXISTS `texts` (
  `text_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `text_content` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `text_updated` int unsigned NOT NULL,
  `text_enabled` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`text_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.tickets
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(6) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `cat_id` tinyint unsigned NOT NULL DEFAULT '1',
  `admin_id` int unsigned DEFAULT '0',
  `c_user_id` int unsigned NOT NULL DEFAULT '0',
  `c_alliance_id` int unsigned NOT NULL DEFAULT '0',
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `status` enum('new','assigned','closed') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'new',
  `solution` enum('open','solved','duplicate','invalid') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'open',
  `admin_comment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `abuse_user_id` (`user_id`),
  KEY `abuse_status` (`status`),
  KEY `abuse_admin_id` (`admin_id`),
  KEY `abuse_timestamp` (`timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.ticket_cat
CREATE TABLE IF NOT EXISTS `ticket_cat` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `sort` smallint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.ticket_msg
CREATE TABLE IF NOT EXISTS `ticket_msg` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int unsigned NOT NULL DEFAULT '0',
  `user_id` int unsigned DEFAULT '0',
  `admin_id` int unsigned DEFAULT '0',
  `message` text NOT NULL,
  `timestamp` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.tips
CREATE TABLE IF NOT EXISTS `tips` (
  `tip_id` int unsigned NOT NULL AUTO_INCREMENT,
  `tip_text` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tip_active` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`tip_id`),
  KEY `tip_active` (`tip_active`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.tutorial
CREATE TABLE IF NOT EXISTS `tutorial` (
  `tutorial_id` int unsigned NOT NULL AUTO_INCREMENT,
  `tutorial_title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`tutorial_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.tutorial_texts
CREATE TABLE IF NOT EXISTS `tutorial_texts` (
  `text_id` int unsigned NOT NULL AUTO_INCREMENT,
  `text_tutorial_id` int NOT NULL,
  `text_title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `text_content` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `text_step` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`text_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.tutorial_user_progress
CREATE TABLE IF NOT EXISTS `tutorial_user_progress` (
  `tup_user_id` int unsigned NOT NULL,
  `tup_tutorial_id` int unsigned NOT NULL,
  `tup_text_step` tinyint unsigned NOT NULL DEFAULT '0',
  `tup_closed` tinyint unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `tup_user_id` (`tup_user_id`,`tup_tutorial_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.users
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_nick` varchar(15) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_password` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_password_temp` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user_last_online` int unsigned NOT NULL DEFAULT '0',
  `user_last_login` int unsigned NOT NULL DEFAULT '0',
  `user_logintime` int unsigned NOT NULL DEFAULT '0',
  `user_acttime` int unsigned NOT NULL DEFAULT '0',
  `user_logouttime` int unsigned NOT NULL DEFAULT '0',
  `user_session_key` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user_email` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_email_fix` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `user_ip` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user_hostname` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user_blocked_from` int unsigned NOT NULL DEFAULT '0',
  `user_blocked_to` int unsigned NOT NULL DEFAULT '0',
  `user_ban_reason` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `user_attack_bans` tinyint unsigned NOT NULL DEFAULT '0',
  `user_ban_admin_id` int unsigned DEFAULT NULL,
  `user_hmode_from` int unsigned NOT NULL DEFAULT '0',
  `user_hmode_to` int unsigned NOT NULL DEFAULT '0',
  `user_race_id` tinyint unsigned DEFAULT NULL,
  `user_alliance_id` int unsigned DEFAULT NULL,
  `user_alliance_shippoints` mediumint unsigned NOT NULL DEFAULT '0',
  `user_alliance_shippoints_used` mediumint unsigned NOT NULL DEFAULT '0',
  `user_alliance_leave` int unsigned NOT NULL DEFAULT '0',
  `user_sitting_days` tinyint unsigned NOT NULL DEFAULT '20',
  `user_multi_delets` tinyint unsigned NOT NULL DEFAULT '0',
  `user_setup` tinyint unsigned NOT NULL DEFAULT '0',
  `user_points` bigint unsigned NOT NULL DEFAULT '0',
  `user_rank` smallint unsigned NOT NULL DEFAULT '0',
  `user_rank_highest` smallint unsigned NOT NULL DEFAULT '0',
  `user_alliance_rank_id` int unsigned DEFAULT NULL,
  `user_registered` int unsigned NOT NULL DEFAULT '1097597003',
  `user_profile_text` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `user_ghost` tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'Determines wether the user is hidden in rankings',
  `admin` tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'Determines wether the user and his planets are marked as admin items',
  `user_chatadmin` tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'Determines wether the user is a chat admin',
  `user_visits` int unsigned NOT NULL DEFAULT '0',
  `user_avatar` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user_signature` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `user_client` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user_res_from_raid` bigint unsigned NOT NULL DEFAULT '0',
  `user_res_from_tf` bigint unsigned NOT NULL DEFAULT '0',
  `user_res_from_asteroid` bigint unsigned NOT NULL DEFAULT '0',
  `user_res_from_nebula` bigint unsigned NOT NULL DEFAULT '0',
  `user_main_planet_changed` tinyint unsigned NOT NULL DEFAULT '0',
  `user_profile_board_url` char(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user_profile_img` char(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `user_profile_img_check` tinyint unsigned NOT NULL DEFAULT '0',
  `user_specialist_id` tinyint unsigned DEFAULT '0',
  `user_specialist_time` int unsigned NOT NULL DEFAULT '0',
  `user_deleted` int unsigned NOT NULL DEFAULT '0',
  `user_observe` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `lastinvasion` int unsigned NOT NULL DEFAULT '0',
  `spyattack_counter` int unsigned NOT NULL DEFAULT '0',
  `discoverymask` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `discoverymask_last_updated` int unsigned NOT NULL DEFAULT '0',
  `boost_bonus_production` decimal(5,2) unsigned NOT NULL DEFAULT '0.00',
  `boost_bonus_building` decimal(5,2) unsigned NOT NULL DEFAULT '0.00',
  `dual_email` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `dual_name` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `verification_key` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `npc` tinyint NOT NULL DEFAULT '0',
  `user_changed_main_planet` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  KEY `user_name` (`user_name`),
  KEY `user_nick` (`user_nick`),
  KEY `user_rank_current` (`user_rank`),
  KEY `user_points` (`user_points`),
  KEY `user_session_key` (`user_session_key`),
  KEY `user_acttime` (`user_acttime`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.user_comments
CREATE TABLE IF NOT EXISTS `user_comments` (
  `comment_id` int unsigned NOT NULL AUTO_INCREMENT,
  `comment_timestamp` int unsigned NOT NULL DEFAULT '0',
  `comment_user_id` int unsigned NOT NULL DEFAULT '0',
  `comment_admin_id` int unsigned NOT NULL DEFAULT '0',
  `comment_text` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `comment_user_id` (`comment_user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='Admin comments on users';

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.user_log
CREATE TABLE IF NOT EXISTS `user_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` mediumint NOT NULL,
  `timestamp` int NOT NULL,
  `zone` varchar(30) NOT NULL,
  `message` text NOT NULL,
  `host` varchar(50) NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.user_multi
CREATE TABLE IF NOT EXISTS `user_multi` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned DEFAULT '0',
  `multi_id` int unsigned NOT NULL DEFAULT '0',
  `connection` varchar(50) NOT NULL DEFAULT '0',
  `activ` tinyint unsigned NOT NULL DEFAULT '1',
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_multi_user_id` (`user_id`),
  KEY `user_multi_multi_id` (`multi_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.user_onlinestats
CREATE TABLE IF NOT EXISTS `user_onlinestats` (
  `stats_id` int unsigned NOT NULL AUTO_INCREMENT,
  `stats_timestamp` int unsigned NOT NULL,
  `stats_count` int unsigned NOT NULL,
  `stats_regcount` int unsigned NOT NULL,
  PRIMARY KEY (`stats_id`),
  KEY `stats_count` (`stats_count`)
) ENGINE=MyISAM AUTO_INCREMENT=28271 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.user_points
CREATE TABLE IF NOT EXISTS `user_points` (
  `point_id` int unsigned NOT NULL AUTO_INCREMENT,
  `point_user_id` int unsigned NOT NULL DEFAULT '0',
  `point_timestamp` int unsigned NOT NULL DEFAULT '0',
  `point_points` bigint unsigned NOT NULL DEFAULT '0',
  `point_ship_points` bigint unsigned NOT NULL DEFAULT '0',
  `point_tech_points` bigint unsigned NOT NULL DEFAULT '0',
  `point_building_points` bigint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`point_id`),
  KEY `point_user_id` (`point_user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2250 DEFAULT CHARSET=utf8mb3 COMMENT='Speichert den Punkteverlauf der Spieler';

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.user_properties
CREATE TABLE IF NOT EXISTS `user_properties` (
  `id` int NOT NULL,
  `css_style` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `game_width` tinyint unsigned NOT NULL DEFAULT '90',
  `planet_circle_width` smallint unsigned NOT NULL DEFAULT '450',
  `item_show` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'full',
  `item_order_ship` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'name',
  `item_order_def` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'name',
  `item_order_bookmark` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'bookmarks.id',
  `item_order_way` varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'ASC',
  `image_filter` tinyint unsigned NOT NULL DEFAULT '1',
  `msgsignature` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `msgcreation_preview` tinyint unsigned NOT NULL DEFAULT '1',
  `msg_preview` tinyint unsigned NOT NULL DEFAULT '1',
  `helpbox` tinyint unsigned NOT NULL DEFAULT '1',
  `notebox` tinyint unsigned NOT NULL DEFAULT '1',
  `msg_copy` tinyint unsigned NOT NULL DEFAULT '1',
  `msg_blink` tinyint unsigned NOT NULL DEFAULT '1',
  `spyship_id` int unsigned DEFAULT '0',
  `spyship_count` int unsigned NOT NULL DEFAULT '1',
  `analyzeship_id` int unsigned DEFAULT '0',
  `analyzeship_count` int unsigned NOT NULL DEFAULT '1',
  `exploreship_id` int unsigned DEFAULT '0',
  `exploreship_count` int unsigned NOT NULL DEFAULT '1',
  `show_cellreports` tinyint unsigned NOT NULL DEFAULT '1',
  `havenships_buttons` tinyint unsigned NOT NULL DEFAULT '1',
  `show_adds` tinyint unsigned NOT NULL DEFAULT '1',
  `fleet_rtn_msg` tinyint unsigned NOT NULL DEFAULT '0',
  `small_res_box` tinyint unsigned NOT NULL DEFAULT '0',
  `startup_chat` tinyint unsigned NOT NULL DEFAULT '0',
  `chat_color` varchar(7) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'ffffff',
  `keybinds_enable` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.user_ratings
CREATE TABLE IF NOT EXISTS `user_ratings` (
  `id` smallint unsigned NOT NULL,
  `battles_fought` smallint unsigned DEFAULT '0',
  `battles_won` smallint unsigned DEFAULT '0',
  `battles_lost` smallint unsigned DEFAULT '0',
  `battle_rating` smallint unsigned DEFAULT '0',
  `trades_sell` smallint unsigned DEFAULT '0',
  `trades_buy` smallint unsigned NOT NULL DEFAULT '0',
  `trade_rating` smallint unsigned DEFAULT '0',
  `diplomacy_rating` smallint unsigned DEFAULT '0',
  `elorating` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`battle_rating`),
  KEY `id_2` (`id`,`trade_rating`),
  KEY `id_3` (`id`,`diplomacy_rating`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.user_sessionlog
CREATE TABLE IF NOT EXISTS `user_sessionlog` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `session_id` char(40) NOT NULL,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `ip_addr` varchar(40) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `time_login` int unsigned NOT NULL DEFAULT '0',
  `time_action` int unsigned NOT NULL DEFAULT '0',
  `time_logout` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=864 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.user_sessions
CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` char(40) NOT NULL,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `ip_addr` varchar(40) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `time_login` int unsigned NOT NULL DEFAULT '0',
  `time_action` int unsigned NOT NULL DEFAULT '0',
  `last_span` int NOT NULL DEFAULT '0',
  `bot_count` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.user_sitting
CREATE TABLE IF NOT EXISTS `user_sitting` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `sitter_id` int unsigned NOT NULL DEFAULT '0',
  `password` varchar(255) NOT NULL DEFAULT '0',
  `date_from` int unsigned NOT NULL DEFAULT '0',
  `date_to` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_sitting_sitter_user_id` (`sitter_id`),
  KEY `user_sitting_user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.user_stats
CREATE TABLE IF NOT EXISTS `user_stats` (
  `id` int unsigned NOT NULL DEFAULT '0',
  `points` bigint unsigned NOT NULL DEFAULT '0',
  `points_ships` bigint unsigned NOT NULL DEFAULT '0',
  `points_tech` bigint unsigned NOT NULL DEFAULT '0',
  `points_buildings` bigint unsigned NOT NULL DEFAULT '0',
  `points_exp` int unsigned NOT NULL DEFAULT '0',
  `rank` smallint unsigned NOT NULL DEFAULT '0',
  `rank_ships` smallint unsigned NOT NULL DEFAULT '0',
  `rank_tech` smallint unsigned NOT NULL DEFAULT '0',
  `rank_buildings` smallint unsigned NOT NULL DEFAULT '0',
  `rank_exp` smallint unsigned NOT NULL DEFAULT '0',
  `rankshift` tinyint unsigned NOT NULL DEFAULT '0',
  `rankshift_ships` tinyint unsigned NOT NULL DEFAULT '0',
  `rankshift_tech` tinyint unsigned NOT NULL DEFAULT '0',
  `rankshift_buildings` tinyint unsigned NOT NULL DEFAULT '0',
  `rankshift_exp` tinyint unsigned NOT NULL DEFAULT '0',
  `nick` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `alliance_tag` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `alliance_id` smallint unsigned DEFAULT '0',
  `race_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `sx` tinyint unsigned NOT NULL DEFAULT '1',
  `sy` tinyint unsigned NOT NULL DEFAULT '1',
  `blocked` tinyint unsigned NOT NULL DEFAULT '0',
  `inactive` tinyint unsigned NOT NULL DEFAULT '0',
  `hmod` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `rank` (`rank`,`nick`),
  KEY `rank_ships` (`rank_ships`,`nick`),
  KEY `rank_tech` (`rank_tech`,`nick`),
  KEY `rank_buildings` (`rank_buildings`,`nick`),
  KEY `rank_exp` (`rank_exp`,`nick`),
  KEY `points_ships` (`points_ships`),
  KEY `points_tech` (`points_tech`),
  KEY `points_buildings` (`points_buildings`),
  KEY `points_exp` (`points_exp`),
  KEY `points` (`points`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.user_surveillance
CREATE TABLE IF NOT EXISTS `user_surveillance` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `page` varchar(50) NOT NULL,
  `request` text NOT NULL,
  `request_raw` text NOT NULL,
  `post` text NOT NULL,
  `session` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 DELAY_KEY_WRITE=1;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.user_warnings
CREATE TABLE IF NOT EXISTS `user_warnings` (
  `warning_id` int unsigned NOT NULL AUTO_INCREMENT,
  `warning_user_id` int unsigned NOT NULL,
  `warning_date` int unsigned NOT NULL,
  `warning_text` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `warning_admin_id` int unsigned NOT NULL,
  PRIMARY KEY (`warning_id`),
  KEY `warning_user_id` (`warning_user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle etoa_test.wormholes
CREATE TABLE IF NOT EXISTS `wormholes` (
  `id` int unsigned NOT NULL,
  `target_id` int unsigned DEFAULT NULL,
  `changed` int unsigned NOT NULL DEFAULT '0',
  `persistent` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `target_id` (`target_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Daten-Export vom Benutzer nicht ausgewählt

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
