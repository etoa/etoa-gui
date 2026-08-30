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
DROP TABLE IF EXISTS `accesslog`;
CREATE TABLE IF NOT EXISTS `accesslog` (
  `target` varchar(255) NOT NULL,
  `sub` varchar(255) NOT NULL,
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `sid` varchar(32) NOT NULL,
  `domain` varchar(255) NOT NULL,
  KEY `target` (`target`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.accesslog: 0 rows
DELETE FROM `accesslog`;
/*!40000 ALTER TABLE `accesslog` DISABLE KEYS */;
/*!40000 ALTER TABLE `accesslog` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.admin_notes
DROP TABLE IF EXISTS `admin_notes`;
CREATE TABLE IF NOT EXISTS `admin_notes` (
  `notes_id` int unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int unsigned NOT NULL,
  `titel` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `text` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `date` int unsigned NOT NULL,
  PRIMARY KEY (`notes_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Exportiere Daten aus Tabelle etoa_test.admin_notes: 0 rows
DELETE FROM `admin_notes`;
/*!40000 ALTER TABLE `admin_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_notes` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.admin_users
DROP TABLE IF EXISTS `admin_users`;
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

-- Exportiere Daten aus Tabelle etoa_test.admin_users: 0 rows
DELETE FROM `admin_users`;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.admin_user_log
DROP TABLE IF EXISTS `admin_user_log`;
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

-- Exportiere Daten aus Tabelle etoa_test.admin_user_log: ~0 rows (ungefähr)
DELETE FROM `admin_user_log`;

-- Exportiere Struktur von Tabelle etoa_test.admin_user_sessionlog
DROP TABLE IF EXISTS `admin_user_sessionlog`;
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

-- Exportiere Daten aus Tabelle etoa_test.admin_user_sessionlog: ~0 rows (ungefähr)
DELETE FROM `admin_user_sessionlog`;

-- Exportiere Struktur von Tabelle etoa_test.admin_user_sessions
DROP TABLE IF EXISTS `admin_user_sessions`;
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

-- Exportiere Daten aus Tabelle etoa_test.admin_user_sessions: 0 rows
DELETE FROM `admin_user_sessions`;
/*!40000 ALTER TABLE `admin_user_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_user_sessions` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.allianceboard_cat
DROP TABLE IF EXISTS `allianceboard_cat`;
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

-- Exportiere Daten aus Tabelle etoa_test.allianceboard_cat: 0 rows
DELETE FROM `allianceboard_cat`;
/*!40000 ALTER TABLE `allianceboard_cat` DISABLE KEYS */;
/*!40000 ALTER TABLE `allianceboard_cat` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.allianceboard_catranks
DROP TABLE IF EXISTS `allianceboard_catranks`;
CREATE TABLE IF NOT EXISTS `allianceboard_catranks` (
  `cr_id` int unsigned NOT NULL AUTO_INCREMENT,
  `cr_rank_id` int unsigned NOT NULL,
  `cr_cat_id` int unsigned NOT NULL,
  `cr_bnd_id` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cr_id`),
  KEY `cr_rank_id` (`cr_rank_id`),
  KEY `cr_cat_id` (`cr_cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.allianceboard_catranks: 0 rows
DELETE FROM `allianceboard_catranks`;
/*!40000 ALTER TABLE `allianceboard_catranks` DISABLE KEYS */;
/*!40000 ALTER TABLE `allianceboard_catranks` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.allianceboard_posts
DROP TABLE IF EXISTS `allianceboard_posts`;
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

-- Exportiere Daten aus Tabelle etoa_test.allianceboard_posts: 0 rows
DELETE FROM `allianceboard_posts`;
/*!40000 ALTER TABLE `allianceboard_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `allianceboard_posts` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.allianceboard_topics
DROP TABLE IF EXISTS `allianceboard_topics`;
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

-- Exportiere Daten aus Tabelle etoa_test.allianceboard_topics: 0 rows
DELETE FROM `allianceboard_topics`;
/*!40000 ALTER TABLE `allianceboard_topics` DISABLE KEYS */;
/*!40000 ALTER TABLE `allianceboard_topics` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.alliances
DROP TABLE IF EXISTS `alliances`;
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

-- Exportiere Daten aus Tabelle etoa_test.alliances: 0 rows
DELETE FROM `alliances`;
/*!40000 ALTER TABLE `alliances` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliances` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.alliance_applications
DROP TABLE IF EXISTS `alliance_applications`;
CREATE TABLE IF NOT EXISTS `alliance_applications` (
  `user_id` smallint unsigned NOT NULL DEFAULT '0',
  `alliance_id` smallint unsigned NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `user_id` (`user_id`),
  KEY `alliance_id` (`alliance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.alliance_applications: 0 rows
DELETE FROM `alliance_applications`;
/*!40000 ALTER TABLE `alliance_applications` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_applications` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.alliance_bnd
DROP TABLE IF EXISTS `alliance_bnd`;
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

-- Exportiere Daten aus Tabelle etoa_test.alliance_bnd: 0 rows
DELETE FROM `alliance_bnd`;
/*!40000 ALTER TABLE `alliance_bnd` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_bnd` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.alliance_buildings
DROP TABLE IF EXISTS `alliance_buildings`;
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

-- Exportiere Daten aus Tabelle etoa_test.alliance_buildings: 6 rows
DELETE FROM `alliance_buildings`;
/*!40000 ALTER TABLE `alliance_buildings` DISABLE KEYS */;
INSERT INTO `alliance_buildings` (`alliance_building_id`, `alliance_building_name`, `alliance_building_shortcomment`, `alliance_building_longcomment`, `alliance_building_costs_metal`, `alliance_building_costs_crystal`, `alliance_building_costs_plastic`, `alliance_building_costs_fuel`, `alliance_building_costs_food`, `alliance_building_build_time`, `alliance_building_costs_factor`, `alliance_building_last_level`, `alliance_building_show`, `alliance_building_needed_id`, `alliance_building_needed_level`) VALUES
	(6, 'Kryptocenter', '', 'Das Kryptocenter ermöglicht das Entschlüsseln und Mithören gegnerischer Flottensignale.', 250000, 2250000, 250000, 3250000, 0, 20000, 3.00, 10, 1, 1, 2),
	(4, 'Flottenkontrolle', '', 'Koordiniert die Flotten einer Allianz. Je weiter die Allianzflottenkontrolle ausgebaut ist, desto mehr Teilflotten können an einem Allianzangriff teilnehmen.', 100000, 75000, 50000, 25000, 0, 15000, 2.01, 99, 1, 1, 1),
	(5, 'Forschungslabor', '', 'Ermöglicht das Erforschen von Allianztechnologien.', 60000, 90000, 45000, 35000, 0, 15000, 2.00, 99, 1, 1, 1),
	(3, 'Schiffswerft', '', 'Die Allianzschiffswerft produziert einzelne Schiffsteile, mit welchen ein ganzes Schiff hergestellt werden kann. Je weiter die Werft ausgebaut ist, desto schneller können die Teile hergestellt werden und desto mehr Baupläne für Schiffstypen werden konstruiert.', 145000, 102000, 117000, 80000, 0, 15000, 2.50, 99, 1, 4, 1),
	(1, 'Zentrale', '', 'Die Zentrale ist das Hauptgebäude der Allianzbasis. Baut dieses aus um weitere Objekte zu erhalten.', 100000, 100000, 70000, 35000, 50000, 3600, 2.00, 4, 1, 0, 0),
	(2, 'Handelszentrum', '', 'Das Handelszentrum ermöglicht den risikofreien Handel unter den Allianzmitgliedern. Dieser erlaubt es die Angebote auf einem abgeschotteten Markt anzubieten, auf welchen nur Allianzmitglieder zutritt haben.', 300000, 250000, 350000, 35000, 0, 18000, 2.00, 10, 1, 1, 1);
/*!40000 ALTER TABLE `alliance_buildings` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.alliance_building_cooldown
DROP TABLE IF EXISTS `alliance_building_cooldown`;
CREATE TABLE IF NOT EXISTS `alliance_building_cooldown` (
  `cooldown_user_id` int unsigned NOT NULL,
  `cooldown_alliance_building_id` int unsigned NOT NULL,
  `cooldown_end` int unsigned NOT NULL,
  UNIQUE KEY `cooldown_user_id` (`cooldown_user_id`,`cooldown_alliance_building_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Exportiere Daten aus Tabelle etoa_test.alliance_building_cooldown: 0 rows
DELETE FROM `alliance_building_cooldown`;
/*!40000 ALTER TABLE `alliance_building_cooldown` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_building_cooldown` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.alliance_buildlist
DROP TABLE IF EXISTS `alliance_buildlist`;
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

-- Exportiere Daten aus Tabelle etoa_test.alliance_buildlist: 0 rows
DELETE FROM `alliance_buildlist`;
/*!40000 ALTER TABLE `alliance_buildlist` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_buildlist` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.alliance_history
DROP TABLE IF EXISTS `alliance_history`;
CREATE TABLE IF NOT EXISTS `alliance_history` (
  `history_id` int unsigned NOT NULL AUTO_INCREMENT,
  `history_timestamp` int unsigned NOT NULL DEFAULT '0',
  `history_text` text NOT NULL,
  `history_alliance_id` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`history_id`),
  KEY `latest` (`history_alliance_id`,`history_timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=825 DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.alliance_history: 0 rows
DELETE FROM `alliance_history`;
/*!40000 ALTER TABLE `alliance_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_history` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.alliance_news
DROP TABLE IF EXISTS `alliance_news`;
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

-- Exportiere Daten aus Tabelle etoa_test.alliance_news: 0 rows
DELETE FROM `alliance_news`;
/*!40000 ALTER TABLE `alliance_news` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_news` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.alliance_points
DROP TABLE IF EXISTS `alliance_points`;
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

-- Exportiere Daten aus Tabelle etoa_test.alliance_points: 0 rows
DELETE FROM `alliance_points`;
/*!40000 ALTER TABLE `alliance_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_points` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.alliance_polls
DROP TABLE IF EXISTS `alliance_polls`;
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

-- Exportiere Daten aus Tabelle etoa_test.alliance_polls: 0 rows
DELETE FROM `alliance_polls`;
/*!40000 ALTER TABLE `alliance_polls` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_polls` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.alliance_poll_votes
DROP TABLE IF EXISTS `alliance_poll_votes`;
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

-- Exportiere Daten aus Tabelle etoa_test.alliance_poll_votes: 0 rows
DELETE FROM `alliance_poll_votes`;
/*!40000 ALTER TABLE `alliance_poll_votes` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_poll_votes` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.alliance_rankrights
DROP TABLE IF EXISTS `alliance_rankrights`;
CREATE TABLE IF NOT EXISTS `alliance_rankrights` (
  `rr_id` int unsigned NOT NULL AUTO_INCREMENT,
  `rr_rank_id` int unsigned NOT NULL DEFAULT '0',
  `rr_right_id` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rr_id`),
  KEY `rr_rank_id` (`rr_rank_id`),
  KEY `rr_right_id` (`rr_right_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.alliance_rankrights: 0 rows
DELETE FROM `alliance_rankrights`;
/*!40000 ALTER TABLE `alliance_rankrights` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_rankrights` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.alliance_ranks
DROP TABLE IF EXISTS `alliance_ranks`;
CREATE TABLE IF NOT EXISTS `alliance_ranks` (
  `rank_id` int unsigned NOT NULL AUTO_INCREMENT,
  `rank_alliance_id` int unsigned NOT NULL DEFAULT '0',
  `rank_name` varchar(30) DEFAULT NULL,
  `rank_level` tinyint unsigned NOT NULL DEFAULT '0',
  `rank_points` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rank_id`),
  KEY `rank_alliance_id` (`rank_alliance_id`,`rank_level`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.alliance_ranks: 0 rows
DELETE FROM `alliance_ranks`;
/*!40000 ALTER TABLE `alliance_ranks` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_ranks` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.alliance_rights
DROP TABLE IF EXISTS `alliance_rights`;
CREATE TABLE IF NOT EXISTS `alliance_rights` (
  `right_id` int unsigned NOT NULL AUTO_INCREMENT,
  `right_key` varchar(30) NOT NULL,
  `right_desc` text NOT NULL,
  PRIMARY KEY (`right_id`),
  KEY `right_key` (`right_key`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.alliance_rights: 0 rows
DELETE FROM `alliance_rights`;
/*!40000 ALTER TABLE `alliance_rights` DISABLE KEYS */;
INSERT INTO `alliance_rights` (`right_id`, `right_key`, `right_desc`) VALUES
	(13, 'polls', 'Umfrage erstellen'),
	(12, 'applications', 'Bewerbungen bearbeiten'),
	(11, 'editmembers', 'Mitglieder verwalten'),
	(10, 'allianceboard', 'Forum verwalten'),
	(8, 'relations', 'Allianzbeziehungen (Bündnisse / Kriege) verwalten'),
	(7, 'alliancenews', 'Allianznews (Rathaus) verfassen'),
	(6, 'ranks', 'Allianzränge bearbeiten'),
	(5, 'massmail', 'Allianzinternes Rundmail versenden'),
	(4, 'history', 'Allianzgeschichte betrachten'),
	(3, 'applicationtemplate', 'Bewerbungsvorlage bearbeiten'),
	(2, 'viewmembers', 'Mitglieder anschauen'),
	(1, 'editdata', 'Allianzdaten (Name, Tag, Beschreibung, Bild, Link) ändern'),
	(14, 'fleetminister', 'Allianzflotten bearbeiten'),
	(15, 'wings', 'Wings hinzufügen und entfernen'),
	(16, 'buildminister', 'Allianzbasis ausbauen (Gebäude, Technologien)'),
	(17, 'cryptominister', 'Kryptocenter benutzen');
/*!40000 ALTER TABLE `alliance_rights` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.alliance_spends
DROP TABLE IF EXISTS `alliance_spends`;
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

-- Exportiere Daten aus Tabelle etoa_test.alliance_spends: 0 rows
DELETE FROM `alliance_spends`;
/*!40000 ALTER TABLE `alliance_spends` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_spends` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.alliance_stats
DROP TABLE IF EXISTS `alliance_stats`;
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

-- Exportiere Daten aus Tabelle etoa_test.alliance_stats: 0 rows
DELETE FROM `alliance_stats`;
/*!40000 ALTER TABLE `alliance_stats` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_stats` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.alliance_techlist
DROP TABLE IF EXISTS `alliance_techlist`;
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

-- Exportiere Daten aus Tabelle etoa_test.alliance_techlist: 0 rows
DELETE FROM `alliance_techlist`;
/*!40000 ALTER TABLE `alliance_techlist` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_techlist` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.alliance_technologies
DROP TABLE IF EXISTS `alliance_technologies`;
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

-- Exportiere Daten aus Tabelle etoa_test.alliance_technologies: 6 rows
DELETE FROM `alliance_technologies`;
/*!40000 ALTER TABLE `alliance_technologies` DISABLE KEYS */;
INSERT INTO `alliance_technologies` (`alliance_tech_id`, `alliance_tech_name`, `alliance_tech_shortcomment`, `alliance_tech_longcomment`, `alliance_tech_costs_metal`, `alliance_tech_costs_crystal`, `alliance_tech_costs_plastic`, `alliance_tech_costs_fuel`, `alliance_tech_costs_food`, `alliance_tech_build_time`, `alliance_tech_costs_factor`, `alliance_tech_last_level`, `alliance_tech_show`, `alliance_tech_needed_id`, `alliance_tech_needed_level`) VALUES
	(6, 'Schutzschilder', '', '', 0, 0, 0, 0, 0, 0, 1.00, 50, 1, 5, 2),
	(7, 'Panzerung', '', '', 0, 0, 0, 0, 0, 0, 1.00, 50, 1, 5, 2),
	(8, 'Spionagetechnik', '', 'Durch den Zusammenschluss verschiedenster Forscher aus jeglichen Galaxien ergaben sich neue Möglichkeiten der Spionage.', 50000, 25000, 75000, 25000, 25000, 12600, 1.70, 50, 1, 4, 3),
	(4, 'Tarntechnologie', 'In Zeiten einer neuen Ära mit grösseren Flottenverbänden bestehend aus mehreren Teilflotten, reichte die gewöhnliche Tarntechnik nicht mehr aus. So setzten sich Spieler zusammen und teilten ihr Wissen und ihre Ressourcen, um auch diese Hürde zu überwinden.\r\nJe höher diese Technologie erforscht ist, desto länger bleiben Allianzverbände für den Gegner unentdeckt.', 'Durch die immer weiter fortschreitende Entwicklung bei Quantencomputern und und Intergalaktischen Computersystemen, wurden Sicherheitslücken zu einem einem grossen Risikofaktor. Durch Lücken in den Sicherheitssystemen konnten feindliche Mächte die Systeme manipulieren und Daten entwenden.\r\n\r\nDie Allianzverbände Andromedas entwickelten darauf spezielle Verschlüsselungstechnologien.\r\nMit dieser Technologie konnte man die feindlichen System-Zugriffe erheblich stören, jedoch nie komplett ausmerzen.\r\n\r\nJe höher die Technologie erforscht wird, desto geringer ist die Chance genaue Flotteninformationen zu entwenden.', 75000, 25000, 50000, 50000, 50000, 900, 1.60, 50, 1, 0, 0),
	(5, 'Waffentechnik', '', '', 0, 0, 0, 0, 0, 0, 1.00, 50, 1, 5, 2),
	(9, 'Antriebstechnologie', '', '', 0, 0, 0, 0, 0, 0, 1.00, 0, 1, 5, 11);
/*!40000 ALTER TABLE `alliance_technologies` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.asteroids
DROP TABLE IF EXISTS `asteroids`;
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

-- Exportiere Daten aus Tabelle etoa_test.asteroids: 0 rows
DELETE FROM `asteroids`;
/*!40000 ALTER TABLE `asteroids` DISABLE KEYS */;
/*!40000 ALTER TABLE `asteroids` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.backend_message_queue
DROP TABLE IF EXISTS `backend_message_queue`;
CREATE TABLE IF NOT EXISTS `backend_message_queue` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cmd` varchar(255) NOT NULL,
  `arg` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cmd` (`cmd`,`arg`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.backend_message_queue: 0 rows
DELETE FROM `backend_message_queue`;
/*!40000 ALTER TABLE `backend_message_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `backend_message_queue` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.bookmarks
DROP TABLE IF EXISTS `bookmarks`;
CREATE TABLE IF NOT EXISTS `bookmarks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL DEFAULT '0',
  `entity_id` int unsigned NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bookmark_user_id` (`user_id`),
  KEY `absindex` (`user_id`,`entity_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.bookmarks: 0 rows
DELETE FROM `bookmarks`;
/*!40000 ALTER TABLE `bookmarks` DISABLE KEYS */;
/*!40000 ALTER TABLE `bookmarks` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.buddylist
DROP TABLE IF EXISTS `buddylist`;
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

-- Exportiere Daten aus Tabelle etoa_test.buddylist: 0 rows
DELETE FROM `buddylist`;
/*!40000 ALTER TABLE `buddylist` DISABLE KEYS */;
/*!40000 ALTER TABLE `buddylist` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.buildings
DROP TABLE IF EXISTS `buildings`;
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

-- Exportiere Daten aus Tabelle etoa_test.buildings: ~27 rows (ungefähr)
DELETE FROM `buildings`;
INSERT INTO `buildings` (`building_id`, `building_name`, `building_type_id`, `building_shortcomment`, `building_longcomment`, `building_costs_metal`, `building_costs_crystal`, `building_costs_fuel`, `building_costs_plastic`, `building_costs_food`, `building_costs_power`, `building_build_costs_factor`, `building_demolish_costs_factor`, `building_power_use`, `building_power_req`, `building_fuel_use`, `building_prod_metal`, `building_prod_crystal`, `building_prod_plastic`, `building_prod_fuel`, `building_prod_food`, `building_prod_power`, `building_production_factor`, `building_store_metal`, `building_store_crystal`, `building_store_plastic`, `building_store_fuel`, `building_store_food`, `building_store_factor`, `building_people_place`, `building_last_level`, `building_fields`, `building_show`, `building_order`, `building_fieldsprovide`, `building_workplace`, `building_bunker_res`, `building_bunker_fleet_count`, `building_bunker_fleet_space`) VALUES
	(1, 'Titanmine', 2, 'Produziert Titan.', 'Produziert Titan.', 100, 45, 0, 0, 0, 10, 1.90, 0.20, 10, 0, 0, 104, 0, 0, 0, 0, 0, 1.50, 0, 0, 0, 0, 0, 0.00, 0, 50, 2, 1, 0, 0, 0, 0, 0, 0),
	(2, 'Siliziummine', 2, 'Produziert Silizium.', 'Produziert Silizium.', 150, 50, 0, 0, 0, 20, 1.90, 0.20, 20, 0, 0, 0, 79, 0, 0, 0, 0, 1.50, 0, 0, 0, 0, 0, 0.00, 0, 50, 2, 1, 1, 0, 0, 0, 0, 0),
	(3, 'Chemiefabrik', 2, 'Produziert PVC.', 'Produziert PVC.', 100, 80, 0, 0, 0, 20, 1.90, 0.20, 20, 0, 0, 0, 0, 64, 0, 0, 0, 1.50, 0, 0, 0, 0, 0, 0.00, 0, 50, 3, 1, 3, 0, 0, 0, 0, 0),
	(4, 'Tritiumsynthesizer', 2, 'Produziert Tritium.', 'Produziert Tritium.', 100, 70, 0, 10, 0, 50, 1.90, 0.20, 50, 0, 0, 0, 0, 0, 86, 0, 0, 1.50, 0, 0, 0, 0, 0, 0.00, 0, 50, 3, 1, 4, 0, 0, 0, 0, 0),
	(5, 'Gewächshaus', 2, 'Produziert Nahrung.', 'Produziert Nahrung.', 80, 100, 0, 0, 0, 5, 1.90, 0.20, 5, 0, 0, 0, 0, 0, 0, 60, 0, 1.50, 0, 0, 0, 0, 0, 0.00, 0, 50, 2, 1, 5, 0, 0, 0, 0, 0),
	(6, 'Planetenbasis', 1, 'Das Grundgebäude jedes Planeten bietet Platz für Bewohner, Lagerräume und produziert Rohstoffe.', 'Die Planetenbasis ist die Schaltzentrale aller Aktivitäten auf deinem Planeten. Du musst zuerst eine Planetenbasis bauen, danach kannst du alle weiteren Gebäude errichten. Die Planetenbasis liefert ein Grundeinkommen an Rohstoffen und eine minimale Energieversorgung durch ein integriertes Erdwärmekraftwerk. Es ist jedoch sinnvoll, Minen und Fabriken zu bauen, um die Rohstoffproduktion zu steigern.', 500, 250, 0, 300, 0, 50, 2.00, 0.00, 50, 0, 0, 50, 20, 10, 5, 15, 200, 1.00, 100000, 100000, 100000, 100000, 100000, 1.00, 300, 1, 5, 1, 0, 0, 1, 0, 0, 0),
	(7, 'Wohnmodul', 1, 'Mit einem Wohnmodul wird die Kapazität für Bewohner erhöht.', 'Mit steigendem Wachstum eines Planeten werden immer mehr Gebäude errichtet und ausgebaut, wofür mehr Arbeiter benötigt werden.\r\nEin Ausbau des Wohnmoduls ist deshalb wichtig, welches die Kapazität der Bewohner erhöht und so potenzielle Arbeiter freigibt.', 100, 60, 0, 150, 0, 0, 1.65, 0.40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0, 0, 0, 0, 1.80, 300, 50, 1, 1, 1, 0, 1, 0, 0, 0),
	(8, 'Forschungslabor', 1, 'Im Labor werden neue Techniken entwickelt. Höhere Stufen senken die Forschungszeit.', 'Damit Schiffe und Spezialgebäude errichten werden können, braucht es ein Forschungslabor, in dem die Wissenschaftler neue Technologien entwickeln. Je höher das Forschungslabor ausgebaut ist, desto mehr Technologien können entwickelt werden. Erforschte Technologien gelten automatisch auf allen Planeten deines Reiches.\r\nAusserdem senkt das Forschungslabor die Forschungszeit, jedoch erst ab einer bestimmten Stufe!\r\nUm zur Elite auf dem Gebiet der Technologien zu gehören, ist ein guter Ausbau des Forschungslabors unverzichtbar. ', 598, 836, 247, 418, 0, 0, 1.80, 0.40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0, 0, 0, 0, 0.00, 0, 50, 4, 1, 2, 0, 1, 0, 0, 0),
	(9, 'Schiffswerft', 1, 'In der Werft werden alle Raumschiffe gebaut.Höhere Stufen senken die Bauzeit.', 'In der Schiffswerft werden Schiffe gebaut, die im Krieg oder für den Handel mit anderen Völkern eingesetzt werden können. Je höher die Werft, desto mehr Schiffe können gebaut werden.\r\nAusserdem senkt die Schiffswerft die Bauzeit der Schiffe, jedoch erst ab einer bestimmten Stufe!', 900, 680, 510, 780, 0, 0, 1.80, 0.40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0, 0, 0, 0, 0.00, 0, 50, 6, 1, 3, 0, 1, 0, 0, 0),
	(10, 'Waffenfabrik', 1, 'In der Waffenfabrik werden Verteidigungsanlagen gebaut. Höhere Stufen senken die Bauzeit.', 'Die Waffenfabrik bietet jedem Volk die Möglichkeit, Verteidigungsanlagen gegen feindliche Angriffe zu errichten.\r\nVerteidigungsanlagen funktionieren, wenn sie mal gebaut sind, selbstständig und eröffnen das Feuer gegen angreifende Flotten. \r\nAusserdem senkt der Ausbau der Waffenfabrik die Bauzeit der Verteidigungsanlagen, jedoch erst ab einer bestimmten Stufe!', 750, 480, 320, 500, 0, 0, 1.70, 0.40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0, 0, 0, 0, 0.00, 0, 50, 5, 1, 4, 0, 1, 0, 0, 0),
	(11, 'Flottenkontrolle', 1, 'Koordiniert deine Flotten. Je weiter die Flottenkontrolle ausgebaut ist, desto mehr Flotten können starten.', 'Die Flottenkontrolle ist ein Gebäude voller Überwachungscomputer, Leitsystemen, Empfänger- sowie Sendeanlagen. Mit Hilfe der Flottenkontrolle werden Flotten gesteuert. Sie ist ebenfalls Voraussetzung für den Bau von Schiffen. Je weiter die Flottenkontrolle ausgebaut ist, desto mehr Flotten können vom Planeten gestartet werden.', 1100, 750, 0, 500, 0, 0, 1.80, 0.40, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0, 0, 0, 0, 0.00, 0, 50, 5, 1, 5, 0, 0, 0, 0, 0),
	(12, 'Windkraftwerk', 3, 'Nicht sehr effizientes und relativ teures Kraftwerk, welches Energie mit Hilfe des Windes gewinnt.', 'Windenergieanlagen wandeln mit Hilfe des Rotors die Windenergie in eine Drehbewegung um. Mit Hilfe von Generatoren wird diese Drehbewegung in eine elektrische Energie umgewandelt, welche dann in das Stromnetz des Planeten eingespeist wird.\r\nWindenergie ist eine alternative Energie, jedoch noch nicht sehr effizient. Der Bau ist relativ teuer und die Produktion nur mittelmässig.', 250, 50, 5, 80, 0, 0, 1.90, 0.20, 0, 0, 0, 0, 0, 0, 0, 0, 80, 1.65, 0, 0, 0, 0, 0, 0.00, 0, 50, 1, 1, 0, 0, 0, 0, 0, 0),
	(13, 'Solarkraftwerk', 3, 'Solarkraftwerke gewinnen Energie durch Sonnenlicht. ', 'In einer Solarstromanlage findet die Umwandlung von Sonnenenergie in elektrische Energie statt. Eine Solarstromanlage besteht aus mehreren Komponenten. Der Generator empfängt und wandelt die Lichtenergie in elektrische Energie um. Als Empfänger dient die Solarzelle. Hierbei kommen Spiegel oder Linsensysteme zum Einsatz, die die Strahlung auf die Zellen umleiten und konzentrieren.\r\nEiner der wichtigsten Bestandteile einer Solarzelle ist das Metal Silizium. Dieses hat die Eigenschaft, unter Bestrahlung von Licht eine elektrische Spannung erzeugen zu können.\r\nDiese Methode für die Energieerzeugung ist noch sehr jung und unerforscht. Wegen den grossen Mengen an Silizium die es benötigt, wird das Solarkraftwerk oft als unrentabel bezeichnet, jedoch kann sich die Energiegewinnung daraus sehen lassen.', 150, 250, 0, 160, 0, 0, 1.90, 0.20, 0, 0, 0, 0, 0, 0, 0, 0, 100, 1.70, 0, 0, 0, 0, 0, 0.00, 0, 50, 1, 1, 2, 0, 0, 0, 0, 0),
	(14, 'Fusionskraftwerk', 3, 'Durch die Fusion von Tritium und Deuterium werden im Fusionskraftwerk riesige Energiemengen gewonnen. ', 'Als Kernfusion wird der Prozess des Verschmelzens zweier Atomkerne zu einem schwereren Kern bezeichnet. Besonders viel Energie wird frei, wenn Deuterium und Tritium miteinander verschmelzen. Hier beträgt der Massendefekt fast 4 Promille. Die fehlende Masse wird aufgrund der Äquivalenz von Masse und Energie aus Einsteins Gleichung E=mc^2 als kinetische Energie auf die Reaktionsprodukte übertragen. Da c^2 eine sehr grosse Zahl ist, setzt schon die Fusion kleiner Mengen von Deuterium und Tritium gewaltige Energiemengen frei.\r\nDie Effizienz dieses Kraftwerkes wird pro Stufe immer wie grösser! Die Energie, welche das Kraftwerk in den ersten Stufen freisetzt, wird oft als normal angesehen, jedoch stellt sich schon sehr früh heraus, dass beim weiteren Ausbau des Fusionskraftwerkes die Effizient beachtlich gesteigert wird!', 3000, 4900, 8300, 1500, 0, 0, 1.90, 0.20, 0, 0, 0, 0, 0, 0, 0, 0, 1500, 1.95, 0, 0, 0, 0, 0, 0.00, 0, 50, 2, 1, 5, 0, 0, 0, 0, 0),
	(15, 'Gezeitenkraftwerk', 3, 'Dieses Kraftwerk gewinnt Energie durch den Hubunterschied der Gezeiten.', 'Ein Gezeitenkraftwerk ist ein Kraftwerk zur Produktion von elektrischem Strom, das durch die Tide angetrieben wird. Sie sind eine Sonderform der Wasserkraftwerke.\r\nGezeitenkraftwerke werden an Meeresbuchten und in Ästuaren errichtet, die einen besonders hohen Tidenhub haben. Dazu wird die entsprechende Bucht durch einen Deich abgedämmt. Dadurch kann das Wasser der Tidenströme durch die Turbinen strömen, die aufgrund der Gezeitenströme, welche viermal am Tag die Richtung wechseln, auf Zweirichtungsbetrieb eingestellt sind.', 2100, 1000, 500, 2000, 0, 0, 1.85, 0.20, 0, 0, 0, 0, 0, 0, 0, 0, 750, 1.75, 0, 0, 0, 0, 0, 0.00, 0, 50, 3, 1, 3, 0, 0, 0, 0, 0),
	(16, 'Titanspeicher', 4, 'Lagert Titan.', 'Lagert Titan. Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.', 4000, 100, 0, 100, 0, 0, 1.80, 0.20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 100000, 0, 0, 0, 0, 1.80, 0, 50, 1, 1, 0, 0, 0, 0, 0, 0),
	(17, 'Siliziumspeicher', 4, 'Lagert Silizium.', 'Lagert Silizium. Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.', 100, 3500, 0, 100, 0, 0, 1.80, 0.20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 100000, 0, 0, 0, 1.80, 0, 50, 1, 1, 1, 0, 0, 0, 0, 0),
	(18, 'Lagerhalle', 4, 'Lagert Plastik.', 'Lagert Plastik. Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.', 50, 50, 0, 3750, 0, 0, 1.80, 0.20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0, 100000, 0, 0, 1.80, 0, 50, 1, 1, 2, 0, 0, 0, 0, 0),
	(19, 'Nahrungssilo', 4, 'Lagert Nahrung.', 'Lagert Nahrung.Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.', 1000, 1000, 0, 1000, 0, 0, 1.80, 0.20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0, 0, 0, 100000, 1.80, 0, 50, 1, 1, 4, 0, 0, 0, 0, 0),
	(20, 'Tritiumsilo', 4, 'Lagert Tritium.', 'Lagert Tritium. Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.', 500, 500, 3000, 0, 0, 0, 1.80, 0.20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0, 0, 100000, 0, 1.80, 0, 50, 1, 1, 3, 0, 0, 0, 0, 0),
	(21, 'Marktplatz', 1, 'Auf dem Marktplatz können Schiffe und Rohstoffe gehandelt und ersteigert werden.', 'Der Marktplatz bildet das Zentrum aller Händler in Andromeda.\r\nEs können Rohstoffe und Schiffe gehandelt und versteigert werden.\r\nJe höher der Marktplatz ausgebaut ist, desto mehr Waren können gleichzeitig angeboten werden.\r\nAusserdem werden mehr Waren zurück erstattet, wenn ein Angebot zurückgezogen wird.\r\nDer Markt kann aber nicht beliebig weit ausgebaut werden, sondern ist durch ein Maximallevel beschränkt.', 15000, 12500, 1750, 17500, 0, 0, 1.50, 1.50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0, 0, 0, 0, 0.00, 0, 10, 4, 1, 6, 0, 0, 0, 0, 0),
	(22, 'Orbitalplattform', 1, 'Die Orbitalplattform erhöht den Platz auf einem Planeten und bietet Lagerräume für Ressourcen.', 'Die Orbitalplattform erhöht die Anzahl verfügbarer Felder auf einem Planeten. Dies wird besonders wichtig, wenn ein Planet nicht allzu viele Felder besitzt, oder viele Verteidigungsanlagen errichtet wurden. Ebenfalls befinden sich auf der Plattform zusätzliche Lagerräume für diverse Ressourcen.\r\nPro Ausbaustufe erhöht sich die Anzahl der Felder, ebenso die Grösse der Lagerräume.', 30000, 60000, 50000, 55000, 0, 100, 1.90, 0.00, 100, 0, 0, 0, 0, 0, 0, 0, 0, 1.80, 10000, 15000, 20000, 0, 0, 2.00, 0, 50, 0, 1, 7, 60, 0, 0, 0, 0),
	(23, 'Multimine', 2, 'Dieses riesige Mine fördert Titan und Silizium zu Tage und kann auch eine gewisse Menge an Rohstoffen speichern. Allerdings verbraucht sie enorm viel Energie!', 'Dieses riesige Mine fördert Titan und Silizium zu Tage und kann auch eine gewisse Menge an Rohstoffen speichern. Allerdings verbraucht sie enorm viel Energie! Da sie so enorm gross ist, braucht sie viele Felder und kann nur bis zu Stufe 15 gebaut werden.', 5100, 7200, 160, 1100, 0, 0, 2.00, 0.00, 100, 0, 0, 100, 70, 0, 0, 0, 0, 1.80, 50000, 50000, 0, 0, 0, 1.50, 0, 15, 8, 0, 20, 0, 0, 0, 0, 0),
	(24, 'Kryptocenter', 1, 'Das Kryptocenter analysiert Kommunikationskanäle um Infos über fremde Flottenbewegungen zu erhalten. ', 'Das Kryptocenter analysiert Kommunikationskanäle zwischen Flotten und Bodenstationen, um Aufschluss über fremde Flottenbewegungen zu erhalten. Mit Hilfe eines riesigen unterirdischen Rechenzentrums werden die gewonnenen Daten analysiert, entschlüsselt und ausgewertet, deshalb braucht diese Anlage enorm viel Energie zum Bau und zum  Betrieb. Je höher der Level dieser Anlage, desto grösser ist auch die Reichweite des Scanners.', 50000, 450000, 650000, 50000, 0, 1000000, 1.50, 0.10, 0, 1000000, 0, 0, 0, 0, 0, 0, 0, 1.50, 0, 0, 0, 0, 0, 0.00, 0, 10, 5, 0, 11, 0, 0, 0, 0, 0),
	(25, 'Raketensilo', 1, 'Im Raketensilo werden Raketen gebaut und gestartet, um gegnerische Verteidigungsanlagen zu beschädigen.', 'Im Raketensilo werden Raketen gelagert und gestartet, mit denen man gegnerische Verteidigungsanlagen beschädigen oder ausser Gefecht setzen kann, sowie Raketen um gegnerische Raketen abzufangen. Je grösser das Silo ist, desto mehr Raketen können darin gelagert werden.', 100000, 50000, 70000, 20000, 0, 50000, 1.40, 0.00, 50000, 0, 300, 0, 0, 0, 0, 0, 0, 1.40, 0, 0, 0, 0, 0, 0.00, 0, 20, 2, 1, 10, 0, 0, 0, 0, 0),
	(26, 'Rohstoffbunker', 1, 'In diesem Bunker kann im Falle eines Angriffs ein Teil der Rohstoffe versteckt werden.', 'In diesem Bunker kann im Falle eines Angriffs ein Teil der Rohstoffe versteckt werden, so dass sie nicht geklaut werden können. Das Verstecken geschieht automatisch. Auf Stufe 1 können 9000 Resourcen versteckt werden, pro Stufe verdoppelt sich diese Anzahl.', 5750, 1150, 0, 2300, 0, 0, 2.00, 0.20, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0, 0, 0, 0, 2.00, 0, 13, 0, 1, 8, 0, 0, 9000, 0, 0),
	(27, 'Flottenbunker', 1, 'Der Flottenbunker kann Schiffe vor Angriffen schützen und die Kosten für Allianzschiffe wieder gesenkt werden, wenn diese eingebunkert sind.', 'Der Flottenbunker ist ein imposantes Bauwerk von beeindruckender Größe und Robustheit. Seine massiven Wände und strukturellen Verstärkungen machen ihn zu einer unüberwindbaren Festung im Weltraum. Dieses Bollwerk wurde speziell entwickelt, um Schiffe vor feindlichen Angriffen zu schützen. Darüber hinaus verfügt der Flottenbunker über eine einzigartige Funktion: Wenn Allianzschiffe in ihn eingebunkert werden, können die Produktionskosten wieder gesenkt werden. Dies macht den Flottenbunker nicht nur zu einem Verteidigungsbollwerk, sondern auch zu einer strategisch wertvollen Ressource für die Raumfahrtindustrie.', 20000, 10000, 0, 5000, 0, 0, 2.00, 0.50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0.00, 0, 0, 0, 0, 0, 2.00, 0, 12, 0, 1, 9, 0, 0, 0, 5, 2500);

-- Exportiere Struktur von Tabelle etoa_test.building_points
DROP TABLE IF EXISTS `building_points`;
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

-- Exportiere Daten aus Tabelle etoa_test.building_points: 0 rows
DELETE FROM `building_points`;
/*!40000 ALTER TABLE `building_points` DISABLE KEYS */;
INSERT INTO `building_points` (`bp_id`, `bp_building_id`, `bp_level`, `bp_points`) VALUES
	(1081, 27, 12, 143325.000),
	(1080, 27, 11, 71645.000),
	(1079, 27, 10, 35805.000),
	(1078, 27, 9, 17885.000),
	(1077, 27, 8, 8925.000),
	(1076, 27, 7, 4445.000),
	(1075, 27, 6, 2205.000),
	(1074, 27, 5, 1085.000),
	(1073, 27, 4, 525.000),
	(1072, 27, 3, 245.000),
	(1071, 27, 2, 105.000),
	(1070, 27, 1, 35.000),
	(1069, 26, 13, 75357.200),
	(1068, 26, 12, 37674.000),
	(1067, 26, 11, 18832.400),
	(1066, 26, 10, 9411.600),
	(1065, 26, 9, 4701.200),
	(1064, 26, 8, 2346.000),
	(1063, 26, 7, 1168.400),
	(1062, 26, 6, 579.600),
	(1061, 26, 5, 285.200),
	(1060, 26, 4, 138.000),
	(1059, 26, 3, 64.400),
	(1058, 26, 2, 27.600),
	(1057, 26, 1, 9.200),
	(1056, 25, 20, 501409.533),
	(1055, 25, 19, 357978.238),
	(1054, 25, 18, 255527.313),
	(1053, 25, 17, 182348.080),
	(1052, 25, 16, 130077.200),
	(1051, 25, 15, 92740.857),
	(1050, 25, 14, 66072.041),
	(1049, 25, 13, 47022.886),
	(1048, 25, 12, 33416.347),
	(1047, 25, 11, 23697.391),
	(1046, 25, 10, 16755.279),
	(1045, 25, 9, 11796.628),
	(1044, 25, 8, 8254.734),
	(1043, 25, 7, 5724.810),
	(1042, 25, 6, 3917.722),
	(1041, 25, 5, 2626.944),
	(1040, 25, 4, 1704.960),
	(1039, 25, 3, 1046.400),
	(1038, 25, 2, 576.000),
	(1037, 25, 1, 240.000),
	(1036, 24, 10, 135996.094),
	(1035, 24, 9, 89864.063),
	(1034, 24, 8, 59109.375),
	(1033, 24, 7, 38606.250),
	(1032, 24, 6, 24937.500),
	(1031, 24, 5, 15825.000),
	(1030, 24, 4, 9750.000),
	(1029, 24, 3, 5700.000),
	(1028, 24, 2, 3000.000),
	(1027, 24, 1, 1200.000),
	(1026, 23, 15, 444320.520),
	(1025, 23, 14, 222153.480),
	(1024, 23, 13, 111069.960),
	(1023, 23, 12, 55528.200),
	(1022, 23, 11, 27757.320),
	(1021, 23, 10, 13871.880),
	(1020, 23, 9, 6929.160),
	(1019, 23, 8, 3457.800),
	(1018, 23, 7, 1722.120),
	(1017, 23, 6, 854.280),
	(1016, 23, 5, 420.360),
	(1015, 23, 4, 203.400),
	(1014, 23, 3, 94.920),
	(1013, 23, 2, 40.680),
	(1012, 23, 1, 13.560),
	(1011, 22, 50, 18770340440813000.000),
	(1010, 22, 49, 9879126547796000.000),
	(1009, 22, 48, 5199540288313600.000),
	(1008, 22, 47, 2736600151743900.000),
	(1007, 22, 46, 1440315869338800.000),
	(1006, 22, 45, 758060983862420.000),
	(1005, 22, 44, 398979465190640.000),
	(1004, 22, 43, 209989192205500.000),
	(1003, 22, 42, 110520627476480.000),
	(1002, 22, 41, 58168751303306.000),
	(1001, 22, 40, 30615132264795.000),
	(1000, 22, 39, 16113227507684.000),
	(999, 22, 38, 8480646056573.300),
	(998, 22, 37, 4463497924409.600),
	(997, 22, 36, 2349209433797.200),
	(996, 22, 35, 1236426017685.400),
	(995, 22, 34, 650750535521.240),
	(994, 22, 33, 342500281750.650),
	(993, 22, 32, 180263306081.920),
	(992, 22, 31, 94875424151.012),
	(991, 22, 30, 49934433661.059),
	(990, 22, 29, 26281280771.610),
	(989, 22, 28, 13832252935.058),
	(988, 22, 27, 7280133021.083),
	(987, 22, 26, 3831648855.833),
	(986, 22, 25, 2016657189.912),
	(985, 22, 24, 1061398418.375),
	(984, 22, 23, 558630643.882),
	(983, 22, 22, 294016025.727),
	(982, 22, 21, 154745174.067),
	(981, 22, 20, 81444725.825),
	(980, 22, 19, 42865542.539),
	(979, 22, 18, 22560709.231),
	(978, 22, 17, 11873954.859),
	(977, 22, 16, 6249347.294),
	(976, 22, 15, 3289027.523),
	(975, 22, 14, 1730964.486),
	(974, 22, 13, 910931.308),
	(973, 22, 12, 479334.899),
	(972, 22, 11, 252178.894),
	(971, 22, 10, 132623.102),
	(970, 22, 9, 69699.001),
	(969, 22, 8, 36581.053),
	(968, 22, 7, 19150.554),
	(967, 22, 6, 9976.608),
	(966, 22, 5, 5148.215),
	(965, 22, 4, 2606.955),
	(964, 22, 3, 1269.450),
	(963, 22, 2, 565.500),
	(962, 22, 1, 195.000),
	(961, 21, 10, 5298.181),
	(960, 21, 9, 3500.954),
	(959, 21, 8, 2302.803),
	(958, 21, 7, 1504.035),
	(957, 21, 6, 971.523),
	(956, 21, 5, 616.516),
	(955, 21, 4, 379.844),
	(954, 21, 3, 222.063),
	(953, 21, 2, 116.875),
	(952, 21, 1, 46.750),
	(951, 20, 50, 29013175129043.000),
	(950, 20, 49, 16118430627244.000),
	(949, 20, 48, 8954683681799.900),
	(948, 20, 47, 4974824267664.400),
	(947, 20, 46, 2763791259811.300),
	(946, 20, 45, 1535439588781.800),
	(945, 20, 44, 853021993765.470),
	(944, 20, 43, 473901107645.260),
	(943, 20, 42, 263278393134.030),
	(942, 20, 41, 146265773961.130),
	(941, 20, 40, 81258763309.516),
	(940, 20, 39, 45143757391.954),
	(939, 20, 38, 25079865215.530),
	(938, 20, 37, 13933258450.850),
	(937, 20, 36, 7740699137.139),
	(936, 20, 35, 4300388407.299),
	(935, 20, 34, 2389104668.500),
	(934, 20, 33, 1327280369.167),
	(933, 20, 32, 737377980.648),
	(932, 20, 31, 409654431.471),
	(931, 20, 30, 227585793.040),
	(930, 20, 29, 126436549.466),
	(929, 20, 28, 70242525.259),
	(928, 20, 27, 39023622.922),
	(927, 20, 26, 21679788.290),
	(926, 20, 25, 12044324.605),
	(925, 20, 24, 6691289.225),
	(924, 20, 23, 3717380.681),
	(923, 20, 22, 2065209.267),
	(922, 20, 21, 1147336.259),
	(921, 20, 20, 637406.811),
	(920, 20, 19, 354112.673),
	(919, 20, 18, 196727.040),
	(918, 20, 17, 109290.578),
	(917, 20, 16, 60714.766),
	(916, 20, 15, 33728.203),
	(915, 20, 14, 18735.668),
	(914, 20, 13, 10406.482),
	(913, 20, 12, 5779.157),
	(912, 20, 11, 3208.421),
	(911, 20, 10, 1780.234),
	(910, 20, 9, 986.796),
	(909, 20, 8, 545.998),
	(908, 20, 7, 301.110),
	(907, 20, 6, 165.061),
	(906, 20, 5, 89.478),
	(905, 20, 4, 47.488),
	(904, 20, 3, 24.160),
	(903, 20, 2, 11.200),
	(902, 20, 1, 4.000),
	(901, 19, 50, 21759881346782.000),
	(900, 19, 49, 12088822970433.000),
	(899, 19, 48, 6716012761349.900),
	(898, 19, 47, 3731118200748.300),
	(897, 19, 46, 2072843444858.500),
	(896, 19, 45, 1151579691586.400),
	(895, 19, 44, 639766495324.100),
	(894, 19, 43, 355425830733.940),
	(893, 19, 42, 197458794850.520),
	(892, 19, 41, 109699330470.850),
	(891, 19, 40, 60944072482.137),
	(890, 19, 39, 33857818043.965),
	(889, 19, 38, 18809898911.647),
	(888, 19, 37, 10449943838.137),
	(887, 19, 36, 5805524352.854),
	(886, 19, 35, 3225291305.475),
	(885, 19, 34, 1791828501.375),
	(884, 19, 33, 995460276.875),
	(883, 19, 32, 553033485.486),
	(882, 19, 31, 307240823.603),
	(881, 19, 30, 170689344.780),
	(880, 19, 29, 94827412.100),
	(879, 19, 28, 52681893.944),
	(878, 19, 27, 29267717.191),
	(877, 19, 26, 16259841.217),
	(876, 19, 25, 9033243.454),
	(875, 19, 24, 5018466.919),
	(874, 19, 23, 2788035.511),
	(873, 19, 22, 1548906.950),
	(872, 19, 21, 860502.195),
	(871, 19, 20, 478055.108),
	(870, 19, 19, 265584.505),
	(869, 19, 18, 147545.280),
	(868, 19, 17, 81967.933),
	(867, 19, 16, 45536.074),
	(866, 19, 15, 25296.152),
	(865, 19, 14, 14051.751),
	(864, 19, 13, 7804.862),
	(863, 19, 12, 4334.368),
	(862, 19, 11, 2406.315),
	(861, 19, 10, 1335.175),
	(860, 19, 9, 740.097),
	(859, 19, 8, 409.499),
	(858, 19, 7, 225.833),
	(857, 19, 6, 123.796),
	(856, 19, 5, 67.109),
	(855, 19, 4, 35.616),
	(854, 19, 3, 18.120),
	(853, 19, 2, 8.400),
	(852, 19, 1, 3.000),
	(851, 18, 50, 27925181061704.000),
	(850, 18, 49, 15513989478722.000),
	(849, 18, 48, 8618883043732.400),
	(848, 18, 47, 4788268357627.000),
	(847, 18, 46, 2660149087568.400),
	(846, 18, 45, 1477860604202.500),
	(845, 18, 44, 821033668999.260),
	(844, 18, 43, 456129816108.560),
	(843, 18, 42, 253405453391.510),
	(842, 18, 41, 140780807437.590),
	(841, 18, 40, 78211559685.410),
	(840, 18, 39, 43450866489.755),
	(839, 18, 38, 24139370269.947),
	(838, 18, 37, 13410761258.943),
	(837, 18, 36, 7450422919.496),
	(836, 18, 35, 4139123842.026),
	(835, 18, 34, 2299513243.431),
	(834, 18, 33, 1277507355.323),
	(833, 18, 32, 709726306.374),
	(832, 18, 31, 394292390.291),
	(831, 18, 30, 219051325.801),
	(830, 18, 29, 121695178.861),
	(829, 18, 28, 67608430.562),
	(828, 18, 27, 37560237.062),
	(827, 18, 26, 20866796.229),
	(826, 18, 25, 11592662.433),
	(825, 18, 24, 6440365.879),
	(824, 18, 23, 3577978.905),
	(823, 18, 22, 1987763.920),
	(822, 18, 21, 1104311.150),
	(821, 18, 20, 613504.055),
	(820, 18, 19, 340833.447),
	(819, 18, 18, 189349.776),
	(818, 18, 17, 105192.181),
	(817, 18, 16, 58437.962),
	(816, 18, 15, 32463.395),
	(815, 18, 14, 18033.081),
	(814, 18, 13, 10016.239),
	(813, 18, 12, 5562.439),
	(812, 18, 11, 3088.105),
	(811, 18, 10, 1713.475),
	(810, 18, 9, 949.792),
	(809, 18, 8, 525.523),
	(808, 18, 7, 289.818),
	(807, 18, 6, 158.871),
	(806, 18, 5, 86.123),
	(805, 18, 4, 45.707),
	(804, 18, 3, 23.254),
	(803, 18, 2, 10.780),
	(802, 18, 1, 3.850),
	(801, 17, 50, 26837186994365.000),
	(800, 17, 49, 14909548330200.000),
	(799, 17, 48, 8283082405664.900),
	(798, 17, 47, 4601712447589.500),
	(797, 17, 46, 2556506915325.500),
	(796, 17, 45, 1420281619623.200),
	(795, 17, 44, 789045344233.060),
	(794, 17, 43, 438358524571.870),
	(793, 17, 42, 243532513648.980),
	(792, 17, 41, 135295840914.040),
	(791, 17, 40, 75164356061.303),
	(790, 17, 39, 41757975587.557),
	(789, 17, 38, 23198875324.365),
	(788, 17, 37, 12888264067.036),
	(787, 17, 36, 7160146701.853),
	(786, 17, 35, 3977859276.752),
	(785, 17, 34, 2209921818.362),
	(784, 17, 33, 1227734341.479),
	(783, 17, 32, 682074632.099),
	(782, 17, 31, 378930349.111),
	(781, 17, 30, 210516858.562),
	(780, 17, 29, 116953808.256),
	(779, 17, 28, 64974335.865),
	(778, 17, 27, 36096851.203),
	(777, 17, 26, 20053804.168),
	(776, 17, 25, 11141000.260),
	(775, 17, 24, 6189442.533),
	(774, 17, 23, 3438577.130),
	(773, 17, 22, 1910318.572),
	(772, 17, 21, 1061286.040),
	(771, 17, 20, 589601.300),
	(770, 17, 19, 327554.222),
	(769, 17, 18, 181972.512),
	(768, 17, 17, 101093.785),
	(767, 17, 16, 56161.158),
	(766, 17, 15, 31198.588),
	(765, 17, 14, 17330.493),
	(764, 17, 13, 9625.996),
	(763, 17, 12, 5345.720),
	(762, 17, 11, 2967.789),
	(761, 17, 10, 1646.716),
	(760, 17, 9, 912.787),
	(759, 17, 8, 505.048),
	(758, 17, 7, 278.527),
	(757, 17, 6, 152.682),
	(756, 17, 5, 82.768),
	(755, 17, 4, 43.926),
	(754, 17, 3, 22.348),
	(753, 17, 2, 10.360),
	(752, 17, 1, 3.700),
	(751, 16, 50, 30463833885495.000),
	(750, 16, 49, 16924352158606.000),
	(749, 16, 48, 9402417865889.800),
	(748, 16, 47, 5223565481047.600),
	(747, 16, 46, 2901980822801.900),
	(746, 16, 45, 1612211568220.900),
	(745, 16, 44, 895673093453.740),
	(744, 16, 43, 497596163027.520),
	(743, 16, 42, 276442312790.730),
	(742, 16, 41, 153579062659.190),
	(741, 16, 40, 85321701474.992),
	(740, 16, 39, 47400945261.551),
	(739, 16, 38, 26333858476.306),
	(738, 16, 37, 14629921373.392),
	(737, 16, 36, 8127734093.996),
	(736, 16, 35, 4515407827.664),
	(735, 16, 34, 2508559901.925),
	(734, 16, 33, 1393644387.625),
	(733, 16, 32, 774246879.680),
	(732, 16, 31, 430137153.045),
	(731, 16, 30, 238965082.691),
	(730, 16, 29, 132758376.940),
	(729, 16, 28, 73754651.522),
	(728, 16, 27, 40974804.068),
	(727, 16, 26, 22763777.704),
	(726, 16, 25, 12646540.836),
	(725, 16, 24, 7025853.687),
	(724, 16, 23, 3903249.715),
	(723, 16, 22, 2168469.730),
	(722, 16, 21, 1204703.072),
	(721, 16, 20, 669277.151),
	(720, 16, 19, 371818.306),
	(719, 16, 18, 206563.392),
	(718, 16, 17, 114755.107),
	(717, 16, 16, 63750.504),
	(716, 16, 15, 35414.613),
	(715, 16, 14, 19672.452),
	(714, 16, 13, 10926.807),
	(713, 16, 12, 6068.115),
	(712, 16, 11, 3368.842),
	(711, 16, 10, 1869.245),
	(710, 16, 9, 1036.136),
	(709, 16, 8, 573.298),
	(708, 16, 7, 316.166),
	(707, 16, 6, 173.314),
	(706, 16, 5, 93.952),
	(705, 16, 4, 49.862),
	(704, 16, 3, 25.368),
	(703, 16, 2, 11.760),
	(702, 16, 1, 4.200),
	(701, 15, 50, 150437296951430.000),
	(700, 15, 49, 81317457811583.000),
	(699, 15, 48, 43955382600853.000),
	(698, 15, 47, 23759666270728.000),
	(697, 15, 46, 12843062849039.000),
	(696, 15, 45, 6942196134612.800),
	(695, 15, 44, 3752538451139.000),
	(694, 15, 43, 2028399162774.800),
	(693, 15, 42, 1096431979875.300),
	(692, 15, 41, 592665935064.680),
	(691, 15, 40, 320359964896.800),
	(690, 15, 39, 173167548589.840),
	(689, 15, 38, 93604080315.804),
	(688, 15, 37, 50596800167.678),
	(687, 15, 36, 27349621709.231),
	(686, 15, 35, 14783579299.260),
	(685, 15, 34, 7991123942.519),
	(684, 15, 33, 4319526452.389),
	(683, 15, 32, 2334879160.426),
	(682, 15, 31, 1262096840.447),
	(681, 15, 30, 682214505.323),
	(680, 15, 29, 368764594.445),
	(679, 15, 28, 199332210.186),
	(678, 15, 27, 107747137.614),
	(677, 15, 26, 58241692.981),
	(676, 15, 25, 31481993.179),
	(675, 15, 24, 17017290.583),
	(674, 15, 23, 9198532.423),
	(673, 15, 22, 4972176.661),
	(672, 15, 21, 2687660.033),
	(671, 15, 20, 1452786.180),
	(670, 15, 19, 785286.800),
	(669, 15, 18, 424476.324),
	(668, 15, 17, 229443.635),
	(667, 15, 16, 124020.559),
	(666, 15, 15, 67035.113),
	(665, 15, 14, 36232.169),
	(664, 15, 13, 19581.929),
	(663, 15, 12, 10581.800),
	(662, 15, 11, 5716.865),
	(661, 15, 10, 3087.170),
	(660, 15, 9, 1665.714),
	(659, 15, 8, 897.359),
	(658, 15, 7, 482.032),
	(657, 15, 6, 257.531),
	(656, 15, 5, 136.179),
	(655, 15, 4, 70.583),
	(654, 15, 3, 35.126),
	(653, 15, 2, 15.960),
	(652, 15, 1, 5.600),
	(651, 14, 50, 1703769363089100.000),
	(650, 14, 49, 896720717415330.000),
	(649, 14, 48, 471958272323850.000),
	(648, 14, 47, 248399090696750.000),
	(647, 14, 46, 130736363524600.000),
	(646, 14, 45, 68808612381358.000),
	(645, 14, 44, 36215059148074.000),
	(644, 14, 43, 19060557446345.000),
	(643, 14, 42, 10031872340172.000),
	(642, 14, 41, 5279932810607.800),
	(641, 14, 40, 2778912005573.700),
	(640, 14, 39, 1462585266082.100),
	(639, 14, 38, 769781718981.270),
	(638, 14, 37, 405148273138.720),
	(637, 14, 36, 213235933221.590),
	(636, 14, 35, 112229438528.360),
	(635, 14, 34, 59068125531.928),
	(634, 14, 33, 31088487112.752),
	(633, 14, 32, 16362361628.975),
	(632, 14, 31, 8611769269.092),
	(631, 14, 30, 4532510132.312),
	(630, 14, 29, 2385531639.269),
	(629, 14, 28, 1255542958.721),
	(628, 14, 27, 660812074.221),
	(627, 14, 26, 347795819.222),
	(626, 14, 25, 183050421.854),
	(625, 14, 24, 96342317.976),
	(624, 14, 23, 50706473.829),
	(623, 14, 22, 26687608.489),
	(622, 14, 21, 14046100.415),
	(621, 14, 20, 7392675.113),
	(620, 14, 19, 3890872.323),
	(619, 14, 18, 2047818.223),
	(618, 14, 17, 1077789.749),
	(617, 14, 16, 567248.447),
	(616, 14, 15, 298542.498),
	(615, 14, 14, 157118.315),
	(614, 14, 13, 82684.534),
	(613, 14, 12, 43508.860),
	(612, 14, 11, 22890.084),
	(611, 14, 10, 12038.097),
	(610, 14, 9, 6326.525),
	(609, 14, 8, 3320.434),
	(608, 14, 7, 1738.281),
	(607, 14, 6, 905.569),
	(606, 14, 5, 467.299),
	(605, 14, 4, 236.631),
	(604, 14, 3, 115.227),
	(603, 14, 2, 51.330),
	(602, 14, 1, 17.700),
	(601, 13, 50, 53904567419770.000),
	(600, 13, 49, 28370824957773.000),
	(599, 13, 48, 14932013135670.000),
	(598, 13, 47, 7858954281931.200),
	(597, 13, 46, 4136291727331.900),
	(596, 13, 45, 2176995645963.900),
	(595, 13, 44, 1145787182086.000),
	(594, 13, 43, 603045885308.100),
	(593, 13, 42, 317392571214.500),
	(592, 13, 41, 167048721691.540),
	(591, 13, 40, 87920379837.360),
	(590, 13, 39, 46273884124.632),
	(589, 13, 38, 24354675854.775),
	(588, 13, 37, 12818250449.587),
	(587, 13, 36, 6746447604.751),
	(586, 13, 35, 3550761896.943),
	(585, 13, 34, 1868822050.728),
	(584, 13, 33, 983590552.720),
	(583, 13, 32, 517679237.979),
	(582, 13, 31, 272462756.536),
	(581, 13, 30, 143401450.514),
	(580, 13, 29, 75474447.344),
	(579, 13, 28, 39723393.044),
	(578, 13, 27, 20907048.676),
	(577, 13, 26, 11003709.535),
	(576, 13, 25, 5791425.776),
	(575, 13, 24, 3048118.535),
	(574, 13, 23, 1604272.618),
	(573, 13, 22, 844353.715),
	(572, 13, 21, 444396.397),
	(571, 13, 20, 233892.546),
	(570, 13, 19, 123101.045),
	(569, 13, 18, 64789.729),
	(568, 13, 17, 34099.563),
	(567, 13, 16, 17946.844),
	(566, 13, 15, 9445.412),
	(565, 13, 14, 4970.975),
	(564, 13, 13, 2616.008),
	(563, 13, 12, 1376.552),
	(562, 13, 11, 724.206),
	(561, 13, 10, 380.866),
	(560, 13, 9, 200.161),
	(559, 13, 8, 105.053),
	(558, 13, 7, 54.996),
	(557, 13, 6, 28.651),
	(556, 13, 5, 14.785),
	(555, 13, 4, 7.487),
	(554, 13, 3, 3.646),
	(553, 13, 2, 1.624),
	(552, 13, 1, 0.560),
	(551, 12, 50, 37059390101092.000),
	(550, 12, 49, 19504942158469.000),
	(549, 12, 48, 10265759030773.000),
	(548, 12, 47, 5403031068827.700),
	(547, 12, 46, 2843700562540.700),
	(546, 12, 45, 1496684506600.200),
	(545, 12, 44, 787728687684.090),
	(544, 12, 43, 414594046149.320),
	(543, 12, 42, 218207392709.970),
	(542, 12, 41, 114845996162.940),
	(541, 12, 40, 60445261138.185),
	(540, 12, 39, 31813295335.684),
	(539, 12, 38, 16743839650.158),
	(538, 12, 37, 8812547184.091),
	(537, 12, 36, 4638182728.266),
	(536, 12, 35, 2441148804.148),
	(535, 12, 34, 1284815159.875),
	(534, 12, 33, 676218504.995),
	(533, 12, 32, 355904476.110),
	(532, 12, 31, 187318145.119),
	(531, 12, 30, 98588497.228),
	(530, 12, 29, 51888682.549),
	(529, 12, 28, 27309832.718),
	(528, 12, 27, 14373595.965),
	(527, 12, 26, 7565050.305),
	(526, 12, 25, 3981605.221),
	(525, 12, 24, 2095581.493),
	(524, 12, 23, 1102937.425),
	(523, 12, 22, 580493.179),
	(522, 12, 21, 305522.523),
	(521, 12, 20, 160801.125),
	(520, 12, 19, 84631.969),
	(519, 12, 18, 44542.939),
	(518, 12, 17, 23443.449),
	(517, 12, 16, 12338.455),
	(516, 12, 15, 6493.721),
	(515, 12, 14, 3417.545),
	(514, 12, 13, 1798.505),
	(513, 12, 12, 946.379),
	(512, 12, 11, 497.892),
	(511, 12, 10, 261.846),
	(510, 12, 9, 137.611),
	(509, 12, 8, 72.224),
	(508, 12, 7, 37.810),
	(507, 12, 6, 19.697),
	(506, 12, 5, 10.164),
	(505, 12, 4, 5.147),
	(504, 12, 3, 2.506),
	(503, 12, 2, 1.117),
	(502, 12, 1, 0.385),
	(501, 11, 50, 17045240388313.000),
	(500, 11, 49, 9469577993505.700),
	(499, 11, 48, 5260876663057.400),
	(498, 11, 47, 2922709257252.800),
	(497, 11, 46, 1623727365139.100),
	(496, 11, 45, 902070758409.330),
	(495, 11, 44, 501150421337.210),
	(494, 11, 43, 278416900741.590),
	(493, 11, 42, 154676055966.240),
	(492, 11, 41, 85931142202.164),
	(491, 11, 40, 47739523444.341),
	(490, 11, 39, 26521957467.773),
	(489, 11, 38, 14734420814.124),
	(488, 11, 37, 8185789339.874),
	(487, 11, 36, 4547660743.069),
	(486, 11, 35, 2526478189.288),
	(485, 11, 34, 1403598992.744),
	(484, 11, 33, 779777216.885),
	(483, 11, 32, 433209563.631),
	(482, 11, 31, 240671978.489),
	(481, 11, 30, 133706653.411),
	(480, 11, 29, 74281472.812),
	(479, 11, 28, 41267483.590),
	(478, 11, 27, 22926378.467),
	(477, 11, 26, 12736875.620),
	(476, 11, 25, 7076040.706),
	(475, 11, 24, 3931132.420),
	(474, 11, 23, 2183961.150),
	(473, 11, 22, 1213310.444),
	(472, 11, 21, 674060.052),
	(471, 11, 20, 374476.501),
	(470, 11, 19, 208041.195),
	(469, 11, 18, 115577.136),
	(468, 11, 17, 64208.215),
	(467, 11, 16, 35669.925),
	(466, 11, 15, 19815.319),
	(465, 11, 14, 11007.205),
	(464, 11, 13, 6113.808),
	(463, 11, 12, 3395.255),
	(462, 11, 11, 1884.947),
	(461, 11, 10, 1045.887),
	(460, 11, 9, 579.743),
	(459, 11, 8, 320.774),
	(458, 11, 7, 176.902),
	(457, 11, 6, 96.973),
	(456, 11, 5, 52.569),
	(455, 11, 4, 27.899),
	(454, 11, 3, 14.194),
	(453, 11, 2, 6.580),
	(452, 11, 1, 2.350),
	(451, 10, 50, 975218407152.800),
	(450, 10, 49, 573657886559.260),
	(449, 10, 48, 337445815621.890),
	(448, 10, 47, 198497538599.910),
	(447, 10, 46, 116763257998.740),
	(446, 10, 45, 68684269409.817),
	(445, 10, 44, 40402511416.334),
	(444, 10, 43, 23766183184.873),
	(443, 10, 42, 13980107754.602),
	(442, 10, 41, 8223592795.619),
	(441, 10, 40, 4837407525.629),
	(440, 10, 39, 2845533837.399),
	(439, 10, 38, 1673843432.558),
	(438, 10, 37, 984613782.652),
	(437, 10, 36, 579184576.825),
	(436, 10, 35, 340696808.691),
	(435, 10, 34, 200409886.259),
	(434, 10, 33, 117888167.182),
	(433, 10, 32, 69345979.489),
	(432, 10, 31, 40791751.435),
	(431, 10, 30, 23995146.697),
	(430, 10, 29, 14114790.969),
	(429, 10, 28, 8302817.011),
	(428, 10, 27, 4884008.801),
	(427, 10, 26, 2872945.147),
	(426, 10, 25, 1689966.528),
	(425, 10, 24, 994096.752),
	(424, 10, 23, 584761.589),
	(423, 10, 22, 343976.200),
	(422, 10, 21, 202337.735),
	(421, 10, 20, 119020.991),
	(420, 10, 19, 70011.142),
	(419, 10, 18, 41181.819),
	(418, 10, 17, 24223.393),
	(417, 10, 16, 14247.849),
	(416, 10, 15, 8379.882),
	(415, 10, 14, 4928.136),
	(414, 10, 13, 2897.698),
	(413, 10, 12, 1703.322),
	(412, 10, 11, 1000.748),
	(411, 10, 10, 587.470),
	(410, 10, 9, 344.364),
	(409, 10, 8, 201.361),
	(408, 10, 7, 117.242),
	(407, 10, 6, 67.760),
	(406, 10, 5, 38.653),
	(405, 10, 4, 21.531),
	(404, 10, 3, 11.460),
	(403, 10, 2, 5.535),
	(402, 10, 1, 2.050),
	(401, 9, 50, 20816953155088.000),
	(400, 9, 49, 11564973975047.000),
	(399, 9, 48, 6424985541691.400),
	(398, 9, 47, 3569436412049.200),
	(397, 9, 46, 1983020228914.600),
	(396, 9, 45, 1101677904951.000),
	(395, 9, 44, 612043280526.720),
	(394, 9, 43, 340024044735.470),
	(393, 9, 42, 188902247073.670),
	(392, 9, 41, 104945692817.110),
	(391, 9, 40, 58303162674.578),
	(390, 9, 39, 32390645928.727),
	(389, 9, 38, 17994803292.143),
	(388, 9, 37, 9997112938.485),
	(387, 9, 36, 5553951630.897),
	(386, 9, 35, 3085528682.237),
	(385, 9, 34, 1714182599.649),
	(384, 9, 33, 952323664.877),
	(383, 9, 32, 529068701.115),
	(382, 9, 31, 293927054.581),
	(381, 9, 30, 163292806.506),
	(380, 9, 29, 90718224.242),
	(379, 9, 28, 50399011.873),
	(378, 9, 27, 27999449.446),
	(377, 9, 26, 15555248.098),
	(376, 9, 25, 8641802.904),
	(375, 9, 24, 4801000.019),
	(374, 9, 23, 2667220.638),
	(373, 9, 22, 1481787.649),
	(372, 9, 21, 823213.766),
	(371, 9, 20, 457339.387),
	(370, 9, 19, 254075.843),
	(369, 9, 18, 141151.651),
	(368, 9, 17, 78415.990),
	(367, 9, 16, 43562.844),
	(366, 9, 15, 24199.986),
	(365, 9, 14, 13442.842),
	(364, 9, 13, 7466.651),
	(363, 9, 12, 4146.545),
	(362, 9, 11, 2302.042),
	(361, 9, 10, 1277.318),
	(360, 9, 9, 708.026),
	(359, 9, 8, 391.754),
	(358, 9, 7, 216.046),
	(357, 9, 6, 118.431),
	(356, 9, 5, 64.201),
	(355, 9, 4, 34.073),
	(354, 9, 3, 17.335),
	(353, 9, 2, 8.036),
	(352, 9, 1, 2.870),
	(351, 8, 50, 15224663648965.000),
	(350, 8, 49, 8458146471646.200),
	(349, 8, 48, 4698970262024.500),
	(348, 8, 47, 2610539034456.900),
	(347, 8, 46, 1450299463586.000),
	(346, 8, 45, 805721924213.270),
	(345, 8, 44, 447623291228.430),
	(344, 8, 43, 248679606236.850),
	(343, 8, 42, 138155336797.080),
	(342, 8, 41, 76752964886.103),
	(341, 8, 40, 42640536046.669),
	(340, 8, 39, 23689186691.428),
	(339, 8, 38, 13160659271.849),
	(338, 8, 37, 7311477372.084),
	(337, 8, 36, 4061931872.214),
	(336, 8, 35, 2256628816.730),
	(335, 8, 34, 1253682674.795),
	(334, 8, 33, 696490373.720),
	(333, 8, 32, 386939095.345),
	(332, 8, 31, 214966162.914),
	(331, 8, 30, 119425644.897),
	(330, 8, 29, 66347579.332),
	(329, 8, 28, 36859765.130),
	(328, 8, 27, 20477646.128),
	(327, 8, 26, 11376468.905),
	(326, 8, 25, 6320259.337),
	(325, 8, 24, 3511254.021),
	(324, 8, 23, 1950695.512),
	(323, 8, 22, 1083718.563),
	(322, 8, 21, 602064.702),
	(321, 8, 20, 334479.224),
	(320, 8, 19, 185820.625),
	(319, 8, 18, 103232.514),
	(318, 8, 17, 57350.231),
	(317, 8, 16, 31860.073),
	(316, 8, 15, 17698.875),
	(315, 8, 14, 9831.542),
	(314, 8, 13, 5460.802),
	(313, 8, 12, 3032.613),
	(312, 8, 11, 1683.619),
	(311, 8, 10, 934.178),
	(310, 8, 9, 517.821),
	(309, 8, 8, 286.512),
	(308, 8, 7, 158.007),
	(307, 8, 6, 86.616),
	(306, 8, 5, 46.954),
	(305, 8, 4, 24.919),
	(304, 8, 3, 12.678),
	(303, 8, 2, 5.877),
	(302, 8, 1, 2.099),
	(301, 7, 50, 35698136668.158),
	(300, 7, 49, 21635234344.151),
	(299, 7, 48, 13112263238.691),
	(298, 7, 47, 7946826205.080),
	(297, 7, 46, 4816258305.921),
	(296, 7, 45, 2918944427.643),
	(295, 7, 44, 1769057228.687),
	(294, 7, 43, 1072155895.986),
	(293, 7, 42, 649791451.925),
	(292, 7, 41, 393813000.979),
	(291, 7, 40, 238674545.860),
	(290, 7, 39, 144651239.727),
	(289, 7, 38, 87667417.829),
	(288, 7, 37, 53131768.193),
	(287, 7, 36, 32201071.444),
	(286, 7, 35, 19515800.687),
	(285, 7, 34, 11827757.805),
	(284, 7, 33, 7168337.875),
	(283, 7, 32, 4344447.009),
	(282, 7, 31, 2632998.000),
	(281, 7, 30, 1595756.176),
	(280, 7, 29, 967124.767),
	(279, 7, 28, 586136.035),
	(278, 7, 27, 355233.772),
	(277, 7, 26, 215293.008),
	(276, 7, 25, 130480.423),
	(275, 7, 24, 79078.856),
	(274, 7, 23, 47926.392),
	(273, 7, 22, 29046.110),
	(272, 7, 21, 17603.515),
	(271, 7, 20, 10668.609),
	(270, 7, 19, 6465.636),
	(269, 7, 18, 3918.379),
	(268, 7, 17, 2374.587),
	(267, 7, 16, 1438.956),
	(266, 7, 15, 871.907),
	(265, 7, 14, 528.240),
	(264, 7, 13, 319.958),
	(263, 7, 12, 193.726),
	(262, 7, 11, 117.222),
	(261, 7, 10, 70.856),
	(260, 7, 9, 42.755),
	(259, 7, 8, 25.724),
	(258, 7, 7, 15.403),
	(257, 7, 6, 9.147),
	(256, 7, 5, 5.356),
	(255, 7, 4, 3.058),
	(254, 7, 3, 1.665),
	(253, 7, 2, 0.822),
	(252, 7, 1, 0.310),
	(251, 6, 1, 1.050),
	(250, 5, 50, 17326468099212.000),
	(249, 5, 49, 9119193736427.100),
	(248, 5, 48, 4799575650751.000),
	(247, 5, 47, 2526092447763.600),
	(246, 5, 46, 1329522340928.100),
	(245, 5, 45, 699748600488.390),
	(244, 5, 44, 368288737099.060),
	(243, 5, 43, 193836177420.460),
	(242, 5, 42, 102019040747.520),
	(241, 5, 41, 53694231972.282),
	(240, 5, 40, 28260122090.580),
	(239, 5, 39, 14873748468.632),
	(238, 5, 38, 7828288667.606),
	(237, 5, 37, 4120151930.224),
	(236, 5, 36, 2168501015.813),
	(235, 5, 35, 1141316324.017),
	(234, 5, 34, 600692802.020),
	(233, 5, 33, 316154106.231),
	(232, 5, 32, 166396897.922),
	(231, 5, 31, 87577314.601),
	(230, 5, 30, 46093323.379),
	(229, 5, 29, 24259643.789),
	(228, 5, 28, 12768233.479),
	(227, 5, 27, 6720122.789),
	(226, 5, 26, 3536906.636),
	(225, 5, 25, 1861529.714),
	(224, 5, 24, 979752.386),
	(223, 5, 23, 515659.056),
	(222, 5, 22, 271399.408),
	(221, 5, 21, 142841.699),
	(220, 5, 20, 75179.747),
	(219, 5, 19, 39568.193),
	(218, 5, 18, 20825.270),
	(217, 5, 17, 10960.574),
	(216, 5, 16, 5768.628),
	(215, 5, 15, 3036.025),
	(214, 5, 14, 1597.813),
	(213, 5, 13, 840.860),
	(212, 5, 12, 442.463),
	(211, 5, 11, 232.781),
	(210, 5, 10, 122.421),
	(209, 5, 9, 64.338),
	(208, 5, 8, 33.767),
	(207, 5, 7, 17.677),
	(206, 5, 6, 9.209),
	(205, 5, 5, 4.752),
	(204, 5, 4, 2.406),
	(203, 5, 3, 1.172),
	(202, 5, 2, 0.522),
	(201, 5, 1, 0.180),
	(200, 4, 50, 17326468099212.000),
	(199, 4, 49, 9119193736427.100),
	(198, 4, 48, 4799575650751.000),
	(197, 4, 47, 2526092447763.600),
	(196, 4, 46, 1329522340928.100),
	(195, 4, 45, 699748600488.390),
	(194, 4, 44, 368288737099.060),
	(193, 4, 43, 193836177420.460),
	(192, 4, 42, 102019040747.520),
	(191, 4, 41, 53694231972.282),
	(190, 4, 40, 28260122090.580),
	(189, 4, 39, 14873748468.632),
	(188, 4, 38, 7828288667.606),
	(187, 4, 37, 4120151930.224),
	(186, 4, 36, 2168501015.813),
	(185, 4, 35, 1141316324.017),
	(184, 4, 34, 600692802.020),
	(183, 4, 33, 316154106.231),
	(182, 4, 32, 166396897.922),
	(181, 4, 31, 87577314.601),
	(180, 4, 30, 46093323.379),
	(179, 4, 29, 24259643.789),
	(178, 4, 28, 12768233.479),
	(177, 4, 27, 6720122.789),
	(176, 4, 26, 3536906.636),
	(175, 4, 25, 1861529.714),
	(174, 4, 24, 979752.386),
	(173, 4, 23, 515659.056),
	(172, 4, 22, 271399.408),
	(171, 4, 21, 142841.699),
	(170, 4, 20, 75179.747),
	(169, 4, 19, 39568.193),
	(168, 4, 18, 20825.270),
	(167, 4, 17, 10960.574),
	(166, 4, 16, 5768.628),
	(165, 4, 15, 3036.025),
	(164, 4, 14, 1597.813),
	(163, 4, 13, 840.860),
	(162, 4, 12, 442.463),
	(161, 4, 11, 232.781),
	(160, 4, 10, 122.421),
	(159, 4, 9, 64.338),
	(158, 4, 8, 33.767),
	(157, 4, 7, 17.677),
	(156, 4, 6, 9.209),
	(155, 4, 5, 4.752),
	(154, 4, 4, 2.406),
	(153, 4, 3, 1.172),
	(152, 4, 2, 0.522),
	(151, 4, 1, 0.180),
	(150, 3, 50, 17326468099212.000),
	(149, 3, 49, 9119193736427.100),
	(148, 3, 48, 4799575650751.000),
	(147, 3, 47, 2526092447763.600),
	(146, 3, 46, 1329522340928.100),
	(145, 3, 45, 699748600488.390),
	(144, 3, 44, 368288737099.060),
	(143, 3, 43, 193836177420.460),
	(142, 3, 42, 102019040747.520),
	(141, 3, 41, 53694231972.282),
	(140, 3, 40, 28260122090.580),
	(139, 3, 39, 14873748468.632),
	(138, 3, 38, 7828288667.606),
	(137, 3, 37, 4120151930.224),
	(136, 3, 36, 2168501015.813),
	(135, 3, 35, 1141316324.017),
	(134, 3, 34, 600692802.020),
	(133, 3, 33, 316154106.231),
	(132, 3, 32, 166396897.922),
	(131, 3, 31, 87577314.601),
	(130, 3, 30, 46093323.379),
	(129, 3, 29, 24259643.789),
	(128, 3, 28, 12768233.479),
	(127, 3, 27, 6720122.789),
	(126, 3, 26, 3536906.636),
	(125, 3, 25, 1861529.714),
	(124, 3, 24, 979752.386),
	(123, 3, 23, 515659.056),
	(122, 3, 22, 271399.408),
	(121, 3, 21, 142841.699),
	(120, 3, 20, 75179.747),
	(119, 3, 19, 39568.193),
	(118, 3, 18, 20825.270),
	(117, 3, 17, 10960.574),
	(116, 3, 16, 5768.628),
	(115, 3, 15, 3036.025),
	(114, 3, 14, 1597.813),
	(113, 3, 13, 840.860),
	(112, 3, 12, 442.463),
	(111, 3, 11, 232.781),
	(110, 3, 10, 122.421),
	(109, 3, 9, 64.338),
	(108, 3, 8, 33.767),
	(107, 3, 7, 17.677),
	(106, 3, 6, 9.209),
	(105, 3, 5, 4.752),
	(104, 3, 4, 2.406),
	(103, 3, 3, 1.172),
	(102, 3, 2, 0.522),
	(101, 3, 1, 0.180),
	(100, 2, 50, 19251631221346.000),
	(99, 2, 49, 10132437484919.000),
	(98, 2, 48, 5332861834167.800),
	(97, 2, 47, 2806769386404.000),
	(96, 2, 46, 1477247045475.700),
	(95, 2, 45, 777498444987.100),
	(94, 2, 44, 409209707887.840),
	(93, 2, 43, 215373530467.180),
	(92, 2, 42, 113354489719.460),
	(91, 2, 41, 59660257746.980),
	(90, 2, 40, 31400135656.200),
	(89, 2, 39, 16526387187.369),
	(88, 2, 38, 8698098519.562),
	(87, 2, 37, 4577946589.138),
	(86, 2, 36, 2409445573.125),
	(85, 2, 35, 1268129248.908),
	(84, 2, 34, 667436446.688),
	(83, 2, 33, 351282340.257),
	(82, 2, 32, 184885442.135),
	(81, 2, 31, 97308127.334),
	(80, 2, 30, 51214803.755),
	(79, 2, 29, 26955159.766),
	(78, 2, 28, 14186926.087),
	(77, 2, 27, 7466803.099),
	(76, 2, 26, 3929896.262),
	(75, 2, 25, 2068366.349),
	(74, 2, 24, 1088613.762),
	(73, 2, 23, 572954.507),
	(72, 2, 22, 301554.898),
	(71, 2, 21, 158712.999),
	(70, 2, 20, 83533.052),
	(69, 2, 19, 43964.659),
	(68, 2, 18, 23139.189),
	(67, 2, 17, 12178.415),
	(66, 2, 16, 6409.587),
	(65, 2, 15, 3373.362),
	(64, 2, 14, 1775.348),
	(63, 2, 13, 934.289),
	(62, 2, 12, 491.626),
	(61, 2, 11, 258.645),
	(60, 2, 10, 136.024),
	(59, 2, 9, 71.486),
	(58, 2, 8, 37.519),
	(57, 2, 7, 19.642),
	(56, 2, 6, 10.232),
	(55, 2, 5, 5.280),
	(54, 2, 4, 2.674),
	(53, 2, 3, 1.302),
	(52, 2, 2, 0.580),
	(51, 2, 1, 0.200),
	(50, 1, 50, 13957432635476.000),
	(49, 1, 49, 7346017176566.300),
	(48, 1, 48, 3866324829771.600),
	(47, 1, 47, 2034907805142.900),
	(46, 1, 46, 1071004107969.900),
	(45, 1, 45, 563686372615.640),
	(44, 1, 44, 296677038218.680),
	(43, 1, 43, 156145809588.700),
	(42, 1, 42, 82182005046.610),
	(41, 1, 41, 43253686866.561),
	(40, 1, 40, 22765098350.745),
	(39, 1, 39, 11981630710.842),
	(38, 1, 38, 6306121426.683),
	(37, 1, 37, 3319011277.125),
	(36, 1, 36, 1746848040.516),
	(35, 1, 35, 919393705.458),
	(34, 1, 34, 483891423.849),
	(33, 1, 33, 254679696.686),
	(32, 1, 32, 134041945.548),
	(31, 1, 31, 70548392.317),
	(30, 1, 30, 37130732.722),
	(29, 1, 29, 19542490.830),
	(28, 1, 28, 10285521.413),
	(27, 1, 27, 5413432.246),
	(26, 1, 26, 2849174.790),
	(25, 1, 25, 1499565.603),
	(24, 1, 24, 789244.978),
	(23, 1, 23, 415392.017),
	(22, 1, 22, 218627.301),
	(21, 1, 21, 115066.924),
	(20, 1, 20, 60561.463),
	(19, 1, 19, 31874.378),
	(18, 1, 18, 16775.912),
	(17, 1, 17, 8829.351),
	(16, 1, 16, 4646.951),
	(15, 1, 15, 2445.687),
	(14, 1, 14, 1287.127),
	(13, 1, 13, 677.359),
	(12, 1, 12, 356.429),
	(11, 1, 11, 187.518),
	(10, 1, 10, 98.617),
	(9, 1, 9, 51.827),
	(8, 1, 8, 27.201),
	(7, 1, 7, 14.240),
	(6, 1, 6, 7.419),
	(5, 1, 5, 3.828),
	(4, 1, 4, 1.939),
	(3, 1, 3, 0.944),
	(2, 1, 2, 0.421),
	(1, 1, 1, 0.145);
/*!40000 ALTER TABLE `building_points` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.building_queue
DROP TABLE IF EXISTS `building_queue`;
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

-- Exportiere Daten aus Tabelle etoa_test.building_queue: 0 rows
DELETE FROM `building_queue`;
/*!40000 ALTER TABLE `building_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `building_queue` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.building_requirements
DROP TABLE IF EXISTS `building_requirements`;
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

-- Exportiere Daten aus Tabelle etoa_test.building_requirements: ~51 rows (ungefähr)
DELETE FROM `building_requirements`;
INSERT INTO `building_requirements` (`id`, `obj_id`, `req_building_id`, `req_tech_id`, `req_level`) VALUES
	(1, 1, 6, NULL, 1),
	(2, 2, 6, NULL, 1),
	(5, 5, 6, NULL, 1),
	(6, 7, 6, NULL, 1),
	(11, 12, 6, NULL, 1),
	(12, 3, 1, NULL, 2),
	(13, 3, 12, NULL, 3),
	(14, 4, 12, NULL, 4),
	(15, 4, 2, NULL, 3),
	(16, 8, 12, NULL, 5),
	(17, 10, 8, NULL, 2),
	(18, 9, 10, NULL, 2),
	(19, 9, 8, NULL, 4),
	(20, 11, 9, NULL, 1),
	(21, 13, NULL, 3, 3),
	(22, 14, NULL, 3, 8),
	(23, 15, NULL, 3, 5),
	(29, 13, 6, NULL, 1),
	(30, 14, 6, NULL, 1),
	(31, 15, 6, NULL, 1),
	(32, 4, 1, NULL, 1),
	(35, 22, NULL, 3, 10),
	(36, 22, 10, 10, 8),
	(37, 23, 1, NULL, 10),
	(38, 23, 2, NULL, 9),
	(39, 23, 14, NULL, 6),
	(40, 23, NULL, 3, 8),
	(41, 23, NULL, 16, 3),
	(50, 25, NULL, 24, 1),
	(51, 25, 11, NULL, 12),
	(52, 25, 10, NULL, 10),
	(53, 24, 11, NULL, 11),
	(54, 24, 14, NULL, 5),
	(55, 24, NULL, 7, 13),
	(56, 24, NULL, 25, 7),
	(57, 26, 16, NULL, 1),
	(58, 26, 17, NULL, 1),
	(59, 26, 18, NULL, 1),
	(60, 26, 19, NULL, 1),
	(61, 26, 20, NULL, 1),
	(62, 27, 11, NULL, 5),
	(63, 27, NULL, 3, 5),
	(64, 27, NULL, 25, 3),
	(65, 27, NULL, 11, 5),
	(66, 21, 11, NULL, 5),
	(67, 7, 3, NULL, 5),
	(68, 16, 1, NULL, 10),
	(69, 17, 2, NULL, 10),
	(70, 18, 3, NULL, 10),
	(71, 20, 4, NULL, 10),
	(72, 19, 5, NULL, 10);

-- Exportiere Struktur von Tabelle etoa_test.building_types
DROP TABLE IF EXISTS `building_types`;
CREATE TABLE IF NOT EXISTS `building_types` (
  `type_id` int unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL,
  `type_order` smallint unsigned NOT NULL DEFAULT '0',
  `type_color` char(7) NOT NULL DEFAULT '#ffffff',
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.building_types: ~0 rows (ungefähr)
DELETE FROM `building_types`;
INSERT INTO `building_types` (`type_id`, `type_name`, `type_order`, `type_color`) VALUES
	(1, 'Allgemeine Gebäude', 1, '#ffffff'),
	(2, 'Rohstoffgebäude', 2, '#ffffff'),
	(3, 'Kraftwerke', 3, '#ffffff'),
	(4, 'Speicher', 4, '#ffffff');

-- Exportiere Struktur von Tabelle etoa_test.buildlist
DROP TABLE IF EXISTS `buildlist`;
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

-- Exportiere Daten aus Tabelle etoa_test.buildlist: ~0 rows (ungefähr)
DELETE FROM `buildlist`;

-- Exportiere Struktur von Tabelle etoa_test.cells
DROP TABLE IF EXISTS `cells`;
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

-- Exportiere Daten aus Tabelle etoa_test.cells: 0 rows
DELETE FROM `cells`;
/*!40000 ALTER TABLE `cells` DISABLE KEYS */;
/*!40000 ALTER TABLE `cells` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.chat
DROP TABLE IF EXISTS `chat`;
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

-- Exportiere Daten aus Tabelle etoa_test.chat: 0 rows
DELETE FROM `chat`;
/*!40000 ALTER TABLE `chat` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.chat_banns
DROP TABLE IF EXISTS `chat_banns`;
CREATE TABLE IF NOT EXISTS `chat_banns` (
  `user_id` varchar(50) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `timestamp` int NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.chat_banns: 0 rows
DELETE FROM `chat_banns`;
/*!40000 ALTER TABLE `chat_banns` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_banns` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.chat_channels
DROP TABLE IF EXISTS `chat_channels`;
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

-- Exportiere Daten aus Tabelle etoa_test.chat_channels: ~0 rows (ungefähr)
DELETE FROM `chat_channels`;

-- Exportiere Struktur von Tabelle etoa_test.chat_log
DROP TABLE IF EXISTS `chat_log`;
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

-- Exportiere Daten aus Tabelle etoa_test.chat_log: 0 rows
DELETE FROM `chat_log`;
/*!40000 ALTER TABLE `chat_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_log` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.chat_users
DROP TABLE IF EXISTS `chat_users`;
CREATE TABLE IF NOT EXISTS `chat_users` (
  `nick` varchar(30) NOT NULL,
  `user_id` smallint unsigned NOT NULL DEFAULT '0',
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `kick` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.chat_users: 0 rows
DELETE FROM `chat_users`;
/*!40000 ALTER TABLE `chat_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_users` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.config
DROP TABLE IF EXISTS `config`;
CREATE TABLE IF NOT EXISTS `config` (
  `config_id` int unsigned NOT NULL AUTO_INCREMENT,
  `config_name` varchar(50) NOT NULL,
  `config_value` text NOT NULL,
  `config_param1` text NOT NULL,
  `config_param2` text NOT NULL,
  PRIMARY KEY (`config_id`),
  UNIQUE KEY `config_name_2` (`config_name`)
) ENGINE=MyISAM AUTO_INCREMENT=421 DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.config: 231 rows
DELETE FROM `config`;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` (`config_id`, `config_name`, `config_value`, `config_param1`, `config_param2`) VALUES
	(50, 'user_planet_name', 'Startplanet', '', ''),
	(51, 'user_min_fields', '1200', '', ''),
	(52, 'user_max_planets', '15', '', ''),
	(53, 'asteroid_ress', '', '10000', '1000000'),
	(54, 'nebula_ress', '', '100000', '3000000'),
	(55, 'wh_update', '172800', '1', ''),
	(56, 'gasplanet', '7', '3600', '500'),
	(57, 'solsys_percent_planet', '85', '', ''),
	(58, 'solsys_percent_asteroids', '2', '', ''),
	(59, 'user_attack_min_points', '100', '', ''),
	(60, 'user_attack_percentage', '0.4', '', ''),
	(61, 'invade_possibility', '0.5', '1', '0.1'),
	(62, 'invade_ship_destroy', '0.3', '', ''),
	(63, 'def_restore_percent', '0.4', '', ''),
	(64, 'def_wf_percent', '0.5', '', ''),
	(65, 'civil_ship_restore_percent', '0.8', '', ''),
	(66, 'civil_ship_categories', '2,7', '', ''),
	(67, 'ship_wf_percent', '0.65', '', ''),
	(69, 'ship_bomb_factor', '5', '10', ''),
	(68, 'deactivate_fleet', '0', '', ''),
	(71, 'max_heal', '1', '', ''),
	(70, 'battle_rounds', '5', '', ''),
	(72, 'gasattack_action', '25', '95', ''),
	(73, 'elorating', '1600', '15', ''),
	(74, 'battle_rebuildable', '0', '0.75', '1'),
	(75, 'rebuildable_costs', '0.25', '', ''),
	(76, 'invade_active_users', '0', '', ''),
	(77, 'abs_enabled', '1', '0', ''),
	(78, 'alliance_fleets_max_players', '1', '3', ''),
	(79, 'res_update', '300', '', ''),
	(80, 'def_store_capacity', '200000', '', ''),
	(81, 'user_start_metal', '16000', '', ''),
	(82, 'user_start_crystal', '8000', '', ''),
	(83, 'user_start_plastic', '2500', '', ''),
	(85, 'user_start_food', '500', '', ''),
	(84, 'user_start_fuel', '200', '', ''),
	(86, 'user_start_people', '200', '250', ''),
	(87, 'people_food_require', '12', '', ''),
	(88, 'people_multiply', '1.1', '', ''),
	(89, 'people_work_done', '3', '', ''),
	(90, 'specialistconfig', '0.3', '10', '100000'),
	(91, 'energy_tech_power_bonus_required_level', '10', '', ''),
	(92, 'energy_tech_power_bonus_percent_per_level', '5', '', ''),
	(93, 'market_enabled', '1', '', ''),
	(94, 'market_response_time', '14', '', ''),
	(95, 'market_ship_action_ress', 'market', '', ''),
	(101, 'ship_price_factor_max', '2', '', ''),
	(100, 'ship_price_factor_min', '1.35', '', ''),
	(99, 'market_user_reservation_active', '1', '', ''),
	(98, 'market_auction_delay_time', '24', '', ''),
	(97, 'market_ship_flight_time', '', '15', '180'),
	(96, 'market_ship_action_ship', 'market', '', ''),
	(102, 'res_price_factor_min', '0.5', '', ''),
	(103, 'res_price_factor_max', '2', '', ''),
	(104, 'auction_price_factor_min', '0.333', '', ''),
	(105, 'auction_price_factor_max', '3', '', ''),
	(106, 'auction_overbid', '0.01', '', ''),
	(107, 'market_sell_tax', '1.005', '', ''),
	(108, 'auction_min_duration', '2', '', ''),
	(109, 'min_market_level_res', '1', '', ''),
	(110, 'min_market_level_ship', '3', '', ''),
	(111, 'min_market_level_auction', '5', '', ''),
	(112, 'market_rates_count', '200', '', ''),
	(113, 'market_rate_min', '0.1', '', ''),
	(114, 'market_rate_max', '10', '', ''),
	(115, 'default_image_path', 'images/imagepacks/ReDiscovery', '', ''),
	(116, 'default_css_style', 'Rediscovery', '', ''),
	(117, 'imagepack_zip_format', 'zip', '', ''),
	(118, 'imagepack_predirectory', '', '', ''),
	(119, 'imagesize', '220', '120', '40'),
	(120, 'num_nebula_images', '9', '', ''),
	(121, 'num_asteroid_images', '5', '', ''),
	(122, 'num_space_images', '10', '', ''),
	(123, 'num_wormhole_images', '1', '', ''),
	(124, 'wordbanlist', '', '', ''),
	(125, 'msg_flood_control', '10', '', ''),
	(126, 'msg_ban_hours', '0', '', ''),
	(127, 'mailqueue', '50', '', ''),
	(128, 'msg_max_store', '200', '20', ''),
	(129, 'password_minlength', '6', '30', ''),
	(130, 'hmode_days', '2', '42', '1'),
	(131, 'user_inactive_days', '7', '21', '8'),
	(132, 'user_ban_min_length', '1', '', ''),
	(133, 'user_umod_min_length', '2', '', ''),
	(134, 'user_sitting_days', '12', '2', ''),
	(135, 'online_threshold', '5', '', ''),
	(136, 'nick_length', '', '3', '15'),
	(137, 'main_planet_changetime', '7', '', ''),
	(138, 'name_length', '30', '', ''),
	(139, 'user_delete_days', '5', '', ''),
	(140, 'profileimagecheck_done', '1209569797', '', ''),
	(141, 'email_verification_required', '1', '', ''),
	(143, 'admin_dateformat', 'Y-m-d H:i:s', '', ''),
	(142, 'admin_timeout', '1200', '', ''),
	(145, 'battleban', '1', 'EtoA-Updatedienst Problem ', ''),
	(144, 'flightban', '0', '', ''),
	(146, 'battleban_time', '', '1780738200', '1780761600'),
	(147, 'flightban_time', '', '1704913200', '1704956400'),
	(148, 'battleban_arrival_text', '', 'Die ankommenden Schiffe sind auf dem Planeten gelandet. Nach einer kurzen Kaffeepause der Piloten kehrten sie wieder um und machten sich auf den Rückflug.', 'Auf dem Weg zu ihrem Ziel flogen deine Raketen in ein intergalaktisches Warpfeld. Sie wurden deaktiviert und in ihr Lager gebeamt.'),
	(149, 'asteroid_action', '30', '40', '0'),
	(150, 'gascollect_action', '30', '20', '1000'),
	(151, 'nebula_action', '30', '50', '1000'),
	(152, 'antrax_action', '30', '90', ''),
	(153, 'spyattack_action', '10', '10', '10'),
	(154, 'emp_action', '1', '', ''),
	(155, 'userrank_total', 'Imperator von Andromeda', '', ''),
	(156, 'userrank_buildings', 'Grossbaumeister von Andromeda', '', ''),
	(157, 'userrank_tech', 'Hochtechnokrat von Andromeda', '', ''),
	(158, 'userrank_fleet', 'Flottenadmiral von Andromeda', '', ''),
	(159, 'userrank_battle', 'Generalfeldmarschall von Andromeda', '', ''),
	(160, 'userrank_trade', 'Handelsfürst von Andromeda', '', ''),
	(161, 'userrank_diplomacy', 'Botschafter von Andromeda', '', ''),
	(162, 'userrank_exp', 'Kriegsheld von Andromeda', '', ''),
	(163, 'alliance_allow', '1', '', ''),
	(164, 'alliance_max_member_count', '5', '', ''),
	(165, 'alliance_membercosts_factor', '0.9', '', ''),
	(166, 'alliance_leave_cooldown', '86400', '', ''),
	(167, 'alliance_shippoints_per_hour', '10', '', ''),
	(168, 'alliance_shipcosts_factor', '1.02', '', ''),
	(169, 'alliance_tech_bonus', '10', '', ''),
	(170, 'alliance_war_time', '48', '48', ''),
	(171, 'alliance_shippoints_base', '1.4', '', ''),
	(172, 'allow_wings', '0', '', ''),
	(173, 'townhall_ban', '86400', 'Nichtbeachtung der Rathaus-Regeln', ''),
	(174, 'discoverymask', '0.5', '', ''),
	(175, 'explor_radius', '2', '', ''),
	(176, 'shipyard_min_build_time', '1', '', ''),
	(177, 'shipqueue_cancel_min_level', '5', '', ''),
	(178, 'shipqueue_cancel_start', '0.3', '', ''),
	(179, 'shipqueue_cancel_factor', '0.03', '', ''),
	(180, 'shipqueue_cancel_end', '0.8', '', ''),
	(181, 'defense_min_build_time', '1', '', ''),
	(182, 'defqueue_cancel_min_level', '5', '', ''),
	(192, 'daemon_pidfile', 'tmp/eventhandler.pid', '', ''),
	(191, 'daemon_logfile', 'log/eventhandler.log', '', ''),
	(190, 'daemon_instance', 'round23', '', ''),
	(189, 'daemon_exe', '', '', ''),
	(188, 'sessionlog_store_days', '', '30', '60'),
	(187, 'log_threshold_days', '28', '', ''),
	(186, 'recyc_max_payback', '0.9', '', ''),
	(185, 'defqueue_cancel_end', '0.8', '', ''),
	(184, 'defqueue_cancel_factor', '0.03', '', ''),
	(183, 'defqueue_cancel_start', '0.3', '', ''),
	(193, 'backend_offline_mail', 'mail@etoa.ch', '', ''),
	(194, 'update_enabled', '1', '', ''),
	(195, 'backup_dir', '', '', ''),
	(196, 'backup_retention_time', '7', '', ''),
	(197, 'backup_use_gzip', '1', '', ''),
	(198, 'accesslog', '0', '', ''),
	(200, 'crypto_default_cooldown', '86400', '', ''),
	(199, 'crypto_enable', '1', '', ''),
	(201, 'crypto_cooldown_reduction_per_level', '7200', '', ''),
	(203, 'crypto_range_per_level', '700', '', ''),
	(202, 'crypto_min_cooldown', '21600', '', ''),
	(204, 'crypto_fuel_costs_per_scan', '1500000', '', ''),
	(206, 'chat_user_timeout', '180', '', ''),
	(205, 'chat_recent_messages', '200', '', ''),
	(207, 'boost_system_enable', '1', '', ''),
	(208, 'boost_system_max_res_prod_bonus', '0.66', '', ''),
	(209, 'boost_system_max_building_speed_bonus', '0.33', '', ''),
	(49, 'map_init_sector', '', '1', '1'),
	(48, 'cell_length', '300', '', ''),
	(47, 'field_squarekm', '11694', '', ''),
	(46, 'planet_temp', '20', '-155', '166'),
	(45, 'planet_fields', '', '850', '2500'),
	(44, 'num_planet_images', '5', '', ''),
	(43, 'persistent_wormholes_ratio', '15', '', ''),
	(42, 'space_percent_wormholes', '8', '', ''),
	(41, 'space_percent_nebulas', '11', '', ''),
	(40, 'space_percent_asteroids', '11', '', ''),
	(39, 'space_percent_solsys', '64', '', ''),
	(38, 'num_planets', '', '20', '35'),
	(37, 'num_of_cells', '', '10', '10'),
	(36, 'num_of_sectors', '', '2', '2'),
	(35, 'res_build_time', '0.75', '', ''),
	(34, 'flight_land_time', '1', '', ''),
	(33, 'flight_start_time', '1', '', ''),
	(32, 'flight_flight_time', '1', '', ''),
	(31, 'build_build_time', '0.95', '', ''),
	(30, 'def_build_time', '0.75', '', ''),
	(29, 'ship_build_time', '0.75', '', ''),
	(28, 'build_time_boni_waffenfabrik', '5', '10', ''),
	(27, 'build_time_boni_schiffswerft', '5', '10', ''),
	(26, 'build_time_boni_forschungslabor', '5', '10', '0.2'),
	(25, 'shipdefbuild_cancel_time', '15', '', ''),
	(24, 'global_time', '12', '', ''),
	(23, 'bot_max_count', '5', '', ''),
	(22, 'register_key', '', '', ''),
	(21, 'offline_ips_allow', '', '', ''),
	(20, 'offline_message', '', '', ''),
	(19, 'offline', '0', '', ''),
	(18, 'under_construction', '', '', ''),
	(17, 'referers', 'https://round24.etoa.net\r\nhttps://etoa.ch', '', ''),
	(16, 'reports_threshold_days', '42', '42', ''),
	(15, 'messages_threshold_days', '28', '14', ''),
	(14, 'url_teamspeak', 'https://discord.gg/RjDQae4wMT', '', ''),
	(13, 'url_rules', 'https://etoa.ch/rules', '', ''),
	(12, 'stats_num_rows', '50', '', ''),
	(11, 'show_hmod_users_stats', '1', '', ''),
	(10, 'points_update', '3600', '1000', '100'),
	(9, 'round_end', '1', '1790891999', ''),
	(8, 'enable_login', '1', '1760119200', ''),
	(7, 'enable_register', '1', '1759773600', '1000'),
	(6, 'mail_reply', 'mail@etoa.ch', '', ''),
	(5, 'mail_sender', 'no-reply@etoa.ch', '', ''),
	(4, 'user_timeout', '2400', '', ''),
	(3, 'loginurl', 'https://etoa.ch', '', ''),
	(1, 'roundname', 'Runde 24', '', ''),
	(2, 'roundurl', 'https://round24.etoa.net', '', ''),
	(210, 'missile_silo_missiles_per_level', '5', '', ''),
	(211, 'missile_silo_missiles_algo_base', '1.4', '', ''),
	(212, 'missile_silo_flights_per_level', '0.5', '', ''),
	(213, 'missile_battle_shield_factor', '0.3', '', ''),
	(214, 'quest_system_enable', '0', '', ''),
	(215, 'crypto_number_of_fleets_level', '0', '', ''),
	(216, 'crypto_fleets_incoming_level', '2.5', '', ''),
	(217, 'crypto_fleets_send_level', '7.5', '', ''),
	(218, 'crypto_time_60_level', '5', '', ''),
	(219, 'crypto_time_30_level', '10', '', ''),
	(220, 'crypto_time_15_level', '10', '', ''),
	(221, 'crypto_time_min_level', '15', '', ''),
	(222, 'crypto_time_sec_level', '17.5', '', ''),
	(223, 'crypto_ships_type_level', '12.5', '', ''),
	(224, 'crypto_ships_count_all_level', '12.5', '', ''),
	(225, 'crypto_ships_count_single_level', '12.5', '', ''),
	(226, 'crypto_action_level', '20', '', ''),
	(227, 'crypto_resources_level', '22.5', '', ''),
	(228, 'crypto_level_rand_mod_min', '-5', '', ''),
	(229, 'crypto_level_rand_mod_max', '0', '', ''),
	(230, 'crypto_chance_rand_mod_min', '-1', '', ''),
	(231, 'crypto_chance_rand_mod_max', '1', '', '');
/*!40000 ALTER TABLE `config` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.default_items
DROP TABLE IF EXISTS `default_items`;
CREATE TABLE IF NOT EXISTS `default_items` (
  `item_id` int NOT NULL AUTO_INCREMENT,
  `item_set_id` int NOT NULL DEFAULT '0',
  `item_cat` char(1) NOT NULL,
  `item_object_id` int NOT NULL,
  `item_count` int NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `FK_default_items_default_item_sets` (`item_set_id`)
) ENGINE=MyISAM AUTO_INCREMENT=139 DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.default_items: 0 rows
DELETE FROM `default_items`;
/*!40000 ALTER TABLE `default_items` DISABLE KEYS */;
INSERT INTO `default_items` (`item_id`, `item_set_id`, `item_cat`, `item_object_id`, `item_count`) VALUES
	(114, 7, 't', 12, 10),
	(113, 7, 't', 25, 10),
	(112, 7, 't', 16, 10),
	(111, 7, 't', 7, 20),
	(110, 7, 't', 3, 20),
	(109, 7, 't', 24, 1),
	(108, 7, 't', 23, 1),
	(107, 7, 't', 22, 1),
	(106, 7, 't', 19, 10),
	(105, 7, 't', 18, 10),
	(104, 7, 't', 17, 10),
	(103, 7, 't', 15, 10),
	(102, 7, 't', 11, 20),
	(101, 7, 't', 10, 20),
	(100, 7, 't', 9, 20),
	(99, 7, 't', 8, 20),
	(98, 7, 't', 20, 15),
	(97, 7, 't', 21, 15),
	(96, 7, 't', 6, 15),
	(95, 7, 't', 14, 20),
	(94, 7, 't', 5, 20),
	(93, 7, 't', 4, 20),
	(92, 7, 'b', 19, 25),
	(91, 7, 'b', 20, 25),
	(90, 7, 'b', 18, 25),
	(89, 7, 'b', 17, 25),
	(88, 7, 'b', 16, 25),
	(87, 7, 'b', 14, 20),
	(86, 7, 'b', 15, 6),
	(85, 7, 'b', 13, 12),
	(84, 7, 'b', 12, 12),
	(83, 7, 'b', 5, 25),
	(82, 7, 'b', 4, 25),
	(81, 7, 'b', 3, 25),
	(80, 7, 'b', 2, 25),
	(79, 7, 'b', 1, 25),
	(78, 7, 'b', 25, 10),
	(77, 7, 'b', 24, 1),
	(76, 7, 'b', 22, 1),
	(75, 7, 'b', 11, 20),
	(74, 7, 'b', 10, 20),
	(73, 7, 'b', 9, 20),
	(72, 7, 'b', 21, 10),
	(71, 7, 'b', 8, 20),
	(70, 7, 'b', 7, 15),
	(69, 7, 'b', 6, 1),
	(115, 7, 's', 9, 10),
	(116, 7, 's', 60, 1),
	(117, 7, 's', 4, 20),
	(118, 7, 's', 8, 20),
	(119, 7, 's', 27, 20),
	(120, 7, 's', 42, 20),
	(121, 7, 's', 36, 20),
	(122, 7, 's', 13, 1),
	(123, 7, 's', 20, 10),
	(124, 7, 's', 46, 20),
	(125, 7, 's', 68, 20),
	(126, 7, 's', 31, 20),
	(127, 7, 's', 24, 20),
	(128, 7, 's', 69, 20),
	(129, 7, 'd', 10, 1),
	(130, 7, 'd', 11, 1),
	(131, 7, 'd', 1, 10),
	(132, 7, 's', 71, 10);
/*!40000 ALTER TABLE `default_items` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.default_item_sets
DROP TABLE IF EXISTS `default_item_sets`;
CREATE TABLE IF NOT EXISTS `default_item_sets` (
  `set_id` int NOT NULL AUTO_INCREMENT,
  `set_name` varchar(50) NOT NULL,
  `set_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`set_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.default_item_sets: 0 rows
DELETE FROM `default_item_sets`;
/*!40000 ALTER TABLE `default_item_sets` DISABLE KEYS */;
INSERT INTO `default_item_sets` (`set_id`, `set_name`, `set_active`) VALUES
	(7, 'All Objects', 0),
	(5, 'Standard', 1);
/*!40000 ALTER TABLE `default_item_sets` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.defense
DROP TABLE IF EXISTS `defense`;
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

-- Exportiere Daten aus Tabelle etoa_test.defense: ~17 rows (ungefähr)
DELETE FROM `defense`;
INSERT INTO `defense` (`def_id`, `def_name`, `def_shortcomment`, `def_longcomment`, `def_costs_metal`, `def_costs_crystal`, `def_costs_fuel`, `def_costs_plastic`, `def_costs_food`, `def_costs_power`, `def_power_use`, `def_fuel_use`, `def_prod_power`, `def_fields`, `def_show`, `def_buildable`, `def_order`, `def_structure`, `def_shield`, `def_weapon`, `def_heal`, `def_jam`, `def_race_id`, `def_cat_id`, `def_max_count`, `def_points`) VALUES
	(1, 'SPICA Flakkanone', 'Einfache und billige Abwehrwaffe.', 'Einfache und billige Abwehrwaffe.\r\nSie wird auf Gebäuden befestigt und braucht daher keine Felder. Sie ist aber nicht sehr effektiv. Darum ist es besser, sie nur am Anfang und auch dann nur in grossen Mengen zu bauen.', 800, 475, 0, 425, 0, 0, 1, 0, 0, 0, 1, 1, 0, 300, 150, 250, 0, 0, 0, 2, 1000000, 1.700),
	(2, 'POLARIS Raketengeschütz', 'Die Raketen dieses Geschützes verfolgen ihr Ziel mittels Lasersteuerung.', 'Um den gegnerischen Schiffen mit Raketen beizukommen, wurde dieses Raketengeschütz entwickelt. Es schiesst kleinere Raketen ab, welche dann das Ziel bis zur Zerstörung verfolgen. Es ist jedoch nicht sehr stark und dient vor allem zu Beginn als gute und billige Verteidigungswaffe.', 1000, 700, 300, 500, 0, 0, 3, 0, 0, 0, 1, 1, 2, 450, 325, 350, 0, 0, 0, 2, 1000000, 2.500),
	(3, 'ZIBAL Laserturm', 'Dieses Geschütz richtet einen gebündelten und starken Energiestrahl auf ihr Ziel.', 'Dieses Geschütz richtet einen gebündelten und starken Energiestrahl auf ihr Ziel. Es ist eine weiterentwickelte Verteidigungsanlage, welche es auch mit grösseren Schiffen aufnehmen kann.', 3900, 3100, 2100, 1500, 0, 0, 8, 0, 0, 0, 1, 1, 3, 1500, 2000, 1800, 0, 0, 0, 2, 100000, 10.600),
	(4, 'OMEGA Geschütz', 'Diese mächtige Abwehrwaffe beschützt deinen Planeten auch vor grösseren Angriffen.', 'Diese mächtige Abwehrwaffe beschützt deinen Planeten auch vor grösseren Angriffen. Da es aber eine starke Waffe ist, können maximal 1\'000 Stück gebaut werden.', 650000, 425000, 265000, 425000, 0, 0, 15, 0, 0, 1, 1, 1, 4, 300000, 350000, 275000, 0, 0, 0, 2, 1000, 1765.000),
	(5, 'VEGA Hochenergieschild', 'Dieser kleine Hochenergieschild schützt deine Verteidigungsanlagen und Schiffe vor feindlichem Beschuss.', 'Dieser kleine Hochenergieschild schützt deine Verteidigungsanlagen und Schiffe vor feindlichem Beschuss. Es ist jedoch nicht sehr gut und kann nur wenig Beschuss abhalten.', 3000, 1200, 1800, 600, 0, 0, 0, 0, 0, 1, 1, 1, 0, 1200, 3500, 0, 0, 0, 0, 1, 1, 6.600),
	(6, 'CASTOR Hochenergieschild', 'Dieser grosse Hochenergieschild schützt deine Verteidigungsanlagen und Schiffe vor feindlichem Beschuss.', 'Dieser grosse Hochenergieschild schützt deine Verteidigungsanlagen und Schiffe vor feindlichem Beschuss.', 95000, 40000, 45000, 25000, 0, 0, 0, 0, 0, 2, 1, 1, 1, 52500, 105000, 0, 0, 0, 0, 1, 1, 205.000),
	(7, 'NEKKAR Plasmawerfer', 'Die stärkste Verteidigung in ganz Andromeda.', 'Die stärkste Verteidigung in ganz Andromeda. Dieser Plasmawerfer kann es sogar mit einem Andromeda Kampfstern aufnehmen! Dabei schiesst er hochenergetische Teilchen auf das Ziel.\r\nBedingt durch seine Grösse und Stärke ist die maximale Anzahl pro Planet auf 15 limitiert.', 25000000, 20000000, 11500000, 12000000, 0, 0, 0, 0, 0, 2, 1, 1, 5, 14000000, 9500000, 14500000, 0, 0, 0, 2, 15, 68500.000),
	(8, 'SIGMA Hochenergieschild', 'Dies ist der grösste Schild in ganz Andromeda.', 'Dies ist der grösste Schild in ganz Andromeda. Dieser Schild nutzt hochenergetische Teilchen, um die Angriffe der Gegner abzufangen. Beim Bau dieses Schildes wird gleich noch ein Kraftwerk nur für diesen Schild gebaut, damit die Energieversorgung gesichert ist. Deshalb ist er so unglaublich teuer.', 250000000, 20000000, 25000000, 5000000, 0, 0, 0, 0, 0, 100, 1, 1, 3, 25000000, 225000000, 0, 0, 0, 0, 1, 1, 300000.000),
	(9, 'KAPPA Minen', 'Diese Minen schweben im Orbit und können gegnerische Schiffe zerstören.', 'Diese Minen schweben im Orbit und können gegnerische Schiffe zerstören. Sie sind mit Tritium gefüllt und explodieren bei einer Kollision mit feindlichen Schiffen. Da ein kleiner Korridor für eigene Schiffe und Handelsschiffe frei bleiben muss, kann maximal eine Million dieser Minen gebaut werden.', 500, 300, 400, 100, 0, 0, 0, 0, 0, 0, 1, 1, 1, 5, 5, 500, 0, 0, 0, 3, 1000000, 1.300),
	(10, 'MAGNETRON Störsender', 'Diese defensive Anlage kann zufällige Signale in den Raum abgeben und so das Auffinden und Entschlüsseln der eigenen Flottenkommunikation durch gegnerische Spione erschweren.', 'Durch die Verfügbarkeit von grossen Rechenzentren ist in letzter Zeit die Bedrohung durch kryptographische Angriffe auf die eigenen Flottenfunkverbindungen stark angestiegen. Viele Generäle fühlten sich nicht mehr sicher, da ihre Feinde anscheinend plötzlich sehr genau wussten, wann und wo ihre Flotten landen würden. Dies führte zur Erfindung des MAGNETRON Störsenders. Die riesigen Sendeanlagen erzeugen zufällige Funksignale, die sie in den Raum abgeben. Eine gegnerische Analyse der Funksignale eines Planeten findet so viel zu viele Signale und hat Mühe, die richtigen herauszufiltern. ', 20000, 50000, 10000, 15000, 0, 0, 0, 0, 0, 5, 1, 1, 10, 15000, 1200, 0, 0, 1, 0, 3, 10, 95.000),
	(11, 'PHOENIX Reparaturplattform', 'Diese Anlage repariert bei einem Kampf jede Runde eine gewisse Anzahl an Strukturpunkten, kann jedoch selbst auch zerstört werden.', 'Diese Anlage repariert bei einem Kampf jede Runde eine gewisse Anzahl an Strukturpunkten, kann jedoch selbst auch zerstört werden.\r\nDie grundlegende Idee, welche zur Entwicklung dieser Reparaturplattform führte, fanden die Serrakin in den Mutterschiffen der Cardassianer.', 6000, 3000, 3000, 2900, 0, 0, 0, 0, 0, 0, 1, 1, 10, 3150, 1900, 1700, 825, 0, 10, 3, 1000000, 14.900),
	(12, 'SAGITTARIUS Plasmaschild', 'Dieser spezielle Schild wurde schon oft zu kopieren versucht, doch bisher gelang es keiner anderen Rasse als den Serrakin, ihn so effizient herzustellen.', 'Dieser spezielle Schild wurde schon oft zu kopieren versucht, doch bisher gelang es keiner anderen Rasse als den Serrakin, ihn so effizient herzustellen.', 1350000, 1000000, 1050000, 625000, 0, 0, 0, 0, 0, 20, 1, 1, 2, 1400000, 2100000, 0, 0, 0, 10, 1, 1, 4025.000),
	(14, 'ZIBAL Laserturm M', 'Dieses Geschütz richtet einen gebündelten und starken Energiestrahl auf ihr Ziel. Mobile Version.', 'Dieses Geschütz richtet einen gebündelten und starken Energiestrahl auf ihr Ziel. Es ist eine weiterentwickelte Verteidigungsanlage, welche es auch mit grösseren Schiffen aufnehmen kann. Kann auf andere Planeten transportiert werden.', 3900, 3100, 2100, 1500, 0, 0, 8, 0, 0, 0, 1, 1, 3, 1500, 2000, 1800, 0, 0, 10, 2, 100000, 10.600),
	(15, 'POLARIS Raketengeschütz M', 'Die Raketen dieses Geschützes verfolgen ihr Ziel mittels Lasersteuerung. Mobile Version.', 'Um den gegnerischen Schiffen mit Raketen beizukommen, wurde dieses Raketengeschütz entwickelt. Es schiesst kleinere Raketen ab, welche dann das Ziel bis zur Zerstörung verfolgen. Es ist jedoch nicht sehr stark und dient vor allem zu Beginn als gute und billige Verteidigungswaffe. Kann auf andere Planeten transportiert werden.', 1000, 700, 300, 500, 0, 0, 3, 0, 0, 0, 1, 1, 2, 450, 325, 350, 0, 0, 10, 2, 1000000, 2.500),
	(16, 'SPICA Flakkanone M', 'Einfache und billige Abwehrwaffe. Mobile Version.', 'Einfache und billige Abwehrwaffe.\r\nSie wird auf Gebäuden befestigt und braucht daher keine Felder. Sie ist aber nicht sehr effektiv. Darum ist es besser, sie nur am Anfang und auch dann nur in grossen Mengen zu bauen. Kann auf andere Planeten transportiert werden.', 800, 475, 0, 425, 0, 0, 1, 0, 0, 0, 1, 1, 0, 300, 150, 250, 0, 0, 10, 2, 1000000, 1.700),
	(17, 'PHOENIX Reparaturplattform M', 'Diese Anlage repariert bei einem Kampf jede Runde eine gewisse Anzahl an Strukturpunkten, kann jedoch selbst auch zerstört werden. Mobile Version.', 'Diese Anlage repariert bei einem Kampf jede Runde eine gewisse Anzahl an Strukturpunkten, kann jedoch selbst auch zerstört werden.\r\nDie grundlegende Idee, welche zur Entwicklung dieser Reparaturplattform führte, fanden die Serrakin in den Mutterschiffen der Cardassianer. Kann auf andere Planeten transportiert werden.', 6000, 3000, 3000, 2900, 0, 0, 0, 0, 0, 0, 1, 1, 10, 3150, 1900, 1700, 825, 0, 10, 3, 1000000, 14.900),
	(18, 'OMEGA Geschütz M', 'Diese mächtige Abwehrwaffe beschützt deinen Planeten auch vor grösseren Angriffen.', 'Diese mächtige Abwehrwaffe beschützt deinen Planeten auch vor grösseren Angriffen. Da es aber eine starke Waffe ist, können maximal 1\'000 Stück gebaut werden.', 650000, 425000, 265000, 425000, 0, 0, 0, 0, 0, 1, 1, 1, 0, 300000, 350000, 275000, 0, 0, 10, 2, 0, 1765.000);

-- Exportiere Struktur von Tabelle etoa_test.deflist
DROP TABLE IF EXISTS `deflist`;
CREATE TABLE IF NOT EXISTS `deflist` (
  `deflist_id` int unsigned NOT NULL AUTO_INCREMENT,
  `deflist_user_id` int unsigned NOT NULL DEFAULT '0',
  `deflist_def_id` int unsigned NOT NULL DEFAULT '0',
  `deflist_entity_id` int unsigned NOT NULL DEFAULT '0',
  `deflist_count` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`deflist_id`),
  UNIQUE KEY `deflist_all` (`deflist_user_id`,`deflist_entity_id`,`deflist_def_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.deflist: 0 rows
DELETE FROM `deflist`;
/*!40000 ALTER TABLE `deflist` DISABLE KEYS */;
/*!40000 ALTER TABLE `deflist` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.def_cat
DROP TABLE IF EXISTS `def_cat`;
CREATE TABLE IF NOT EXISTS `def_cat` (
  `cat_id` int unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `cat_order` smallint unsigned NOT NULL DEFAULT '0',
  `cat_color` char(7) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '#ffffff',
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Exportiere Daten aus Tabelle etoa_test.def_cat: ~0 rows (ungefähr)
DELETE FROM `def_cat`;
INSERT INTO `def_cat` (`cat_id`, `cat_name`, `cat_order`, `cat_color`) VALUES
	(1, 'Schilder', 1, '#0080FF'),
	(2, 'Geschütze', 0, '#00ff00'),
	(3, 'Spezialanlagen', 2, '#B048F8');

-- Exportiere Struktur von Tabelle etoa_test.def_queue
DROP TABLE IF EXISTS `def_queue`;
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

-- Exportiere Daten aus Tabelle etoa_test.def_queue: 0 rows
DELETE FROM `def_queue`;
/*!40000 ALTER TABLE `def_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `def_queue` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.def_requirements
DROP TABLE IF EXISTS `def_requirements`;
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

-- Exportiere Daten aus Tabelle etoa_test.def_requirements: ~54 rows (ungefähr)
DELETE FROM `def_requirements`;
INSERT INTO `def_requirements` (`id`, `obj_id`, `req_building_id`, `req_tech_id`, `req_level`) VALUES
	(2, 2, 10, NULL, 3),
	(3, 3, 10, NULL, 6),
	(4, 3, NULL, 3, 5),
	(5, 4, NULL, 3, 7),
	(6, 4, 10, NULL, 8),
	(8, 5, 10, NULL, 3),
	(10, 5, 8, NULL, 2),
	(11, 5, NULL, 3, 4),
	(12, 6, 10, NULL, 6),
	(13, 6, NULL, 3, 6),
	(14, 6, 8, NULL, 5),
	(15, 7, 10, NULL, 10),
	(16, 7, NULL, 8, 11),
	(17, 7, NULL, 3, 10),
	(19, 8, NULL, 3, 8),
	(20, 8, 10, 10, 12),
	(21, 8, NULL, 9, 6),
	(22, 8, 22, NULL, 3),
	(23, 3, NULL, 8, 5),
	(24, 4, 8, 8, 7),
	(25, 9, 10, NULL, 4),
	(26, 9, NULL, 8, 3),
	(27, 9, NULL, 4, 2),
	(28, 11, 10, NULL, 8),
	(29, 11, NULL, 25, 3),
	(30, 11, NULL, 16, 4),
	(32, 12, 10, NULL, 9),
	(33, 12, 8, NULL, 7),
	(34, 12, NULL, 3, 10),
	(35, 12, NULL, 16, 8),
	(36, 10, NULL, 25, 5),
	(37, 10, NULL, 11, 8),
	(38, 10, NULL, 3, 10),
	(39, 10, 13, NULL, 5),
	(41, 14, 10, NULL, 6),
	(42, 14, NULL, 3, 5),
	(43, 14, NULL, 8, 5),
	(68, 1, 10, NULL, 1),
	(69, 16, 10, NULL, 1),
	(71, 15, 10, NULL, 3),
	(73, 17, 10, NULL, 8),
	(74, 17, NULL, 25, 3),
	(75, 17, NULL, 16, 4),
	(79, 16, NULL, 12, 3),
	(80, 15, NULL, 12, 4),
	(82, 14, NULL, 12, 6),
	(83, 17, NULL, 12, 8),
	(85, 11, NULL, 19, 9),
	(86, 17, NULL, 19, 9),
	(87, 18, 8, NULL, 7),
	(88, 18, 10, NULL, 8),
	(89, 18, NULL, 3, 7),
	(90, 18, NULL, 12, 10),
	(91, 18, NULL, 8, 7);

-- Exportiere Struktur von Tabelle etoa_test.entities
DROP TABLE IF EXISTS `entities`;
CREATE TABLE IF NOT EXISTS `entities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cell_id` int unsigned NOT NULL,
  `code` char(1) DEFAULT NULL,
  `pos` int unsigned NOT NULL DEFAULT '0',
  `lastvisited` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cell_id` (`cell_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7337 DEFAULT CHARSET=utf8mb3 DELAY_KEY_WRITE=1 COMMENT='Entities in Space, acts as fleet targets';

-- Exportiere Daten aus Tabelle etoa_test.entities: 0 rows
DELETE FROM `entities`;
/*!40000 ALTER TABLE `entities` DISABLE KEYS */;
/*!40000 ALTER TABLE `entities` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.fleet
DROP TABLE IF EXISTS `fleet`;
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

-- Exportiere Daten aus Tabelle etoa_test.fleet: 0 rows
DELETE FROM `fleet`;
/*!40000 ALTER TABLE `fleet` DISABLE KEYS */;
/*!40000 ALTER TABLE `fleet` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.fleet_bookmarks
DROP TABLE IF EXISTS `fleet_bookmarks`;
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

-- Exportiere Daten aus Tabelle etoa_test.fleet_bookmarks: 0 rows
DELETE FROM `fleet_bookmarks`;
/*!40000 ALTER TABLE `fleet_bookmarks` DISABLE KEYS */;
/*!40000 ALTER TABLE `fleet_bookmarks` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.fleet_ships
DROP TABLE IF EXISTS `fleet_ships`;
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

-- Exportiere Daten aus Tabelle etoa_test.fleet_ships: 0 rows
DELETE FROM `fleet_ships`;
/*!40000 ALTER TABLE `fleet_ships` DISABLE KEYS */;
/*!40000 ALTER TABLE `fleet_ships` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.hostname_cache
DROP TABLE IF EXISTS `hostname_cache`;
CREATE TABLE IF NOT EXISTS `hostname_cache` (
  `addr` char(39) NOT NULL,
  `host` varchar(100) NOT NULL,
  `timestamp` int NOT NULL,
  PRIMARY KEY (`addr`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.hostname_cache: 0 rows
DELETE FROM `hostname_cache`;
/*!40000 ALTER TABLE `hostname_cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `hostname_cache` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.login_failures
DROP TABLE IF EXISTS `login_failures`;
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

-- Exportiere Daten aus Tabelle etoa_test.login_failures: 0 rows
DELETE FROM `login_failures`;
/*!40000 ALTER TABLE `login_failures` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_failures` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.logs
DROP TABLE IF EXISTS `logs`;
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

-- Exportiere Daten aus Tabelle etoa_test.logs: 0 rows
DELETE FROM `logs`;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.logs_alliance
DROP TABLE IF EXISTS `logs_alliance`;
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

-- Exportiere Daten aus Tabelle etoa_test.logs_alliance: 0 rows
DELETE FROM `logs_alliance`;
/*!40000 ALTER TABLE `logs_alliance` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_alliance` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.logs_battle
DROP TABLE IF EXISTS `logs_battle`;
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

-- Exportiere Daten aus Tabelle etoa_test.logs_battle: 0 rows
DELETE FROM `logs_battle`;
/*!40000 ALTER TABLE `logs_battle` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_battle` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.logs_battle_queue
DROP TABLE IF EXISTS `logs_battle_queue`;
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

-- Exportiere Daten aus Tabelle etoa_test.logs_battle_queue: 0 rows
DELETE FROM `logs_battle_queue`;
/*!40000 ALTER TABLE `logs_battle_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_battle_queue` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.logs_debris
DROP TABLE IF EXISTS `logs_debris`;
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

-- Exportiere Daten aus Tabelle etoa_test.logs_debris: ~0 rows (ungefähr)
DELETE FROM `logs_debris`;

-- Exportiere Struktur von Tabelle etoa_test.logs_fleet
DROP TABLE IF EXISTS `logs_fleet`;
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

-- Exportiere Daten aus Tabelle etoa_test.logs_fleet: 0 rows
DELETE FROM `logs_fleet`;
/*!40000 ALTER TABLE `logs_fleet` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_fleet` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.logs_fleet_queue
DROP TABLE IF EXISTS `logs_fleet_queue`;
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

-- Exportiere Daten aus Tabelle etoa_test.logs_fleet_queue: 0 rows
DELETE FROM `logs_fleet_queue`;
/*!40000 ALTER TABLE `logs_fleet_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_fleet_queue` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.logs_game
DROP TABLE IF EXISTS `logs_game`;
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

-- Exportiere Daten aus Tabelle etoa_test.logs_game: 0 rows
DELETE FROM `logs_game`;
/*!40000 ALTER TABLE `logs_game` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_game` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.logs_game_queue
DROP TABLE IF EXISTS `logs_game_queue`;
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

-- Exportiere Daten aus Tabelle etoa_test.logs_game_queue: 0 rows
DELETE FROM `logs_game_queue`;
/*!40000 ALTER TABLE `logs_game_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_game_queue` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.logs_queue
DROP TABLE IF EXISTS `logs_queue`;
CREATE TABLE IF NOT EXISTS `logs_queue` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `facility` tinyint unsigned NOT NULL DEFAULT '0',
  `severity` tinyint unsigned NOT NULL DEFAULT '1',
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `ip` varchar(39) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 DELAY_KEY_WRITE=1;

-- Exportiere Daten aus Tabelle etoa_test.logs_queue: 0 rows
DELETE FROM `logs_queue`;
/*!40000 ALTER TABLE `logs_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_queue` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.market_auction
DROP TABLE IF EXISTS `market_auction`;
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

-- Exportiere Daten aus Tabelle etoa_test.market_auction: 0 rows
DELETE FROM `market_auction`;
/*!40000 ALTER TABLE `market_auction` DISABLE KEYS */;
/*!40000 ALTER TABLE `market_auction` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.market_rates
DROP TABLE IF EXISTS `market_rates`;
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

-- Exportiere Daten aus Tabelle etoa_test.market_rates: 0 rows
DELETE FROM `market_rates`;
/*!40000 ALTER TABLE `market_rates` DISABLE KEYS */;
/*!40000 ALTER TABLE `market_rates` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.market_ressource
DROP TABLE IF EXISTS `market_ressource`;
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

-- Exportiere Daten aus Tabelle etoa_test.market_ressource: 0 rows
DELETE FROM `market_ressource`;
/*!40000 ALTER TABLE `market_ressource` DISABLE KEYS */;
/*!40000 ALTER TABLE `market_ressource` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.market_ship
DROP TABLE IF EXISTS `market_ship`;
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

-- Exportiere Daten aus Tabelle etoa_test.market_ship: 0 rows
DELETE FROM `market_ship`;
/*!40000 ALTER TABLE `market_ship` DISABLE KEYS */;
/*!40000 ALTER TABLE `market_ship` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.messages
DROP TABLE IF EXISTS `messages`;
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

-- Exportiere Daten aus Tabelle etoa_test.messages: 0 rows
DELETE FROM `messages`;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.message_cat
DROP TABLE IF EXISTS `message_cat`;
CREATE TABLE IF NOT EXISTS `message_cat` (
  `cat_id` int unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(50) NOT NULL,
  `cat_order` tinyint unsigned NOT NULL DEFAULT '0',
  `cat_desc` text NOT NULL,
  `cat_sender` varchar(50) NOT NULL,
  PRIMARY KEY (`cat_id`),
  KEY `cat_order` (`cat_order`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.message_cat: 0 rows
DELETE FROM `message_cat`;
/*!40000 ALTER TABLE `message_cat` DISABLE KEYS */;
INSERT INTO `message_cat` (`cat_id`, `cat_name`, `cat_order`, `cat_desc`, `cat_sender`) VALUES
	(1, 'Persönliche Nachrichten', 0, '', ''),
	(2, 'Spionageberichte', 1, '', 'Flottenkontrolle'),
	(3, 'Kriegsberichte', 2, '', 'Flottenkontrolle'),
	(4, 'Überwachungsberichte', 3, '', 'Raumüberwachung'),
	(5, 'Sonstige Nachrichten', 5, '', 'System'),
	(6, 'Allianz', 4, '', 'Allianzverwaltung'),
	(7, 'Account', 5, '', 'EtoA Administration');
/*!40000 ALTER TABLE `message_cat` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.message_data
DROP TABLE IF EXISTS `message_data`;
CREATE TABLE IF NOT EXISTS `message_data` (
  `id` mediumint unsigned NOT NULL,
  `subject` varchar(200) NOT NULL,
  `text` text NOT NULL,
  `entity_id` int unsigned DEFAULT '0',
  `fleet_id` int unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.message_data: 0 rows
DELETE FROM `message_data`;
/*!40000 ALTER TABLE `message_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_data` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.message_ignore
DROP TABLE IF EXISTS `message_ignore`;
CREATE TABLE IF NOT EXISTS `message_ignore` (
  `ignore_id` int unsigned NOT NULL AUTO_INCREMENT,
  `ignore_owner_id` int unsigned NOT NULL DEFAULT '0',
  `ignore_target_id` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ignore_id`),
  KEY `ignore_owner_id` (`ignore_owner_id`),
  KEY `ignore_target_id` (`ignore_target_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Exportiere Daten aus Tabelle etoa_test.message_ignore: 0 rows
DELETE FROM `message_ignore`;
/*!40000 ALTER TABLE `message_ignore` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_ignore` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.messenger_messages
DROP TABLE IF EXISTS `messenger_messages`;
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

-- Exportiere Daten aus Tabelle etoa_test.messenger_messages: ~0 rows (ungefähr)
DELETE FROM `messenger_messages`;

-- Exportiere Struktur von Tabelle etoa_test.missilelist
DROP TABLE IF EXISTS `missilelist`;
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

-- Exportiere Daten aus Tabelle etoa_test.missilelist: 0 rows
DELETE FROM `missilelist`;
/*!40000 ALTER TABLE `missilelist` DISABLE KEYS */;
/*!40000 ALTER TABLE `missilelist` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.missiles
DROP TABLE IF EXISTS `missiles`;
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

-- Exportiere Daten aus Tabelle etoa_test.missiles: ~4 rows (ungefähr)
DELETE FROM `missiles`;
INSERT INTO `missiles` (`missile_id`, `missile_name`, `missile_sdesc`, `missile_ldesc`, `missile_costs_metal`, `missile_costs_crystal`, `missile_costs_plastic`, `missile_costs_fuel`, `missile_costs_food`, `missile_damage`, `missile_speed`, `missile_range`, `missile_deactivate`, `missile_def`, `missile_launchable`, `missile_show`) VALUES
	(1, 'PHOBOS Rakete', 'Zerstört gegnerische Verteidigung.', 'Diese Rakete kann auf Verteidigungsanlagen eines feindlichen Planeten abgefeuert werden und verursacht an diesen einen gewissen Schaden, so dass einige Anlagen unter Umständen zerstört werden. Diese Raketen haben eine begrenzte Reichweite, treffen ihr Ziel aber immer.', 18000, 6000, 5000, 15000, 0, 25000, 100000, 3000, 0, 0, 1, 1),
	(2, 'GEMINI Abfangrakete', 'Abfangraketen schiessen selbstständig gegnerische Raketen ab, die diesen Planeten anfliegen.', 'Bei einem Raketenangriff können diese Raketen jeweils eine fremde Rakete zerstören. Sie lösen sich selbständig aus und bieten so einen guten Schutz gegen anfliegende Raketen. Gegen feindliche Flotten können sie jedoch nichts ausrichten. Ausserdem ist die Rakete nach dem Abfangen verbraucht und muss jeweils wieder neu gekauft werden.', 9000, 18000, 6000, 4000, 2000, 0, 0, 0, 0, 1, 0, 1),
	(3, 'VEGA EMP-Rakete', 'Kann ein gegnerisches Gebäude temporär deaktivieren.', 'Diese Rakete kann angreifen um ein gegnerisches Gebäude temporär ausser Kraft zu setzen. Sie richtet an der Verteidigung aber keinen Schaden an und kann ein Gebäude auch nicht vollständig zerstören! Die Rakete wird beim EMP-Angriff verbraucht und hat auch nur eine begrenzte Reichweite.', 18000, 6000, 5000, 15000, 0, 0, 90000, 3000, 240, 0, 1, 1),
	(4, 'VIRGO Abfangrakete', 'Verbesserte Abfangrakete; schiesst selbstständig zwei gegnerische Raketen ab.', 'Bei einem Raketenangriff können diese Raketen jeweils zwei fremde Rakete zerstören. Sie lösen sich  selbständig aus und bieten so einen guten Schutz. Gegen feindliche Flotten können sie jedoch nichts ausrichten. Ausserdem ist die Rakete nach dem Abfangen verbraucht und muss jeweils wieder neu gekauft werden.', 15000, 23000, 9000, 4000, 2000, 0, 0, 0, 0, 2, 0, 1);

-- Exportiere Struktur von Tabelle etoa_test.missile_flights
DROP TABLE IF EXISTS `missile_flights`;
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

-- Exportiere Daten aus Tabelle etoa_test.missile_flights: 0 rows
DELETE FROM `missile_flights`;
/*!40000 ALTER TABLE `missile_flights` DISABLE KEYS */;
/*!40000 ALTER TABLE `missile_flights` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.missile_flights_obj
DROP TABLE IF EXISTS `missile_flights_obj`;
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

-- Exportiere Daten aus Tabelle etoa_test.missile_flights_obj: 0 rows
DELETE FROM `missile_flights_obj`;
/*!40000 ALTER TABLE `missile_flights_obj` DISABLE KEYS */;
/*!40000 ALTER TABLE `missile_flights_obj` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.missile_requirements
DROP TABLE IF EXISTS `missile_requirements`;
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

-- Exportiere Daten aus Tabelle etoa_test.missile_requirements: ~0 rows (ungefähr)
DELETE FROM `missile_requirements`;
INSERT INTO `missile_requirements` (`id`, `obj_id`, `req_building_id`, `req_tech_id`, `req_level`) VALUES
	(1, 2, 25, NULL, 1),
	(2, 2, NULL, 25, 1),
	(4, 1, 25, NULL, 3),
	(5, 1, NULL, 24, 3),
	(6, 3, 25, NULL, 4),
	(7, 3, NULL, 24, 5),
	(8, 4, 25, NULL, 5),
	(9, 4, NULL, 24, 6),
	(10, 4, NULL, 25, 5),
	(11, 1, NULL, 25, 2),
	(12, 3, NULL, 25, 4),
	(13, 3, NULL, 17, 8),
	(14, 1, NULL, 8, 8);

-- Exportiere Struktur von Tabelle etoa_test.nebulas
DROP TABLE IF EXISTS `nebulas`;
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

-- Exportiere Daten aus Tabelle etoa_test.nebulas: 0 rows
DELETE FROM `nebulas`;
/*!40000 ALTER TABLE `nebulas` DISABLE KEYS */;
/*!40000 ALTER TABLE `nebulas` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.notepad
DROP TABLE IF EXISTS `notepad`;
CREATE TABLE IF NOT EXISTS `notepad` (
  `id` mediumint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` smallint unsigned NOT NULL DEFAULT '0',
  `timestamp` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `list` (`user_id`,`timestamp`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.notepad: 0 rows
DELETE FROM `notepad`;
/*!40000 ALTER TABLE `notepad` DISABLE KEYS */;
/*!40000 ALTER TABLE `notepad` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.notepad_data
DROP TABLE IF EXISTS `notepad_data`;
CREATE TABLE IF NOT EXISTS `notepad_data` (
  `id` mediumint unsigned NOT NULL,
  `subject` varchar(100) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.notepad_data: 0 rows
DELETE FROM `notepad_data`;
/*!40000 ALTER TABLE `notepad_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `notepad_data` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.obj_transforms
DROP TABLE IF EXISTS `obj_transforms`;
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

-- Exportiere Daten aus Tabelle etoa_test.obj_transforms: 0 rows
DELETE FROM `obj_transforms`;
/*!40000 ALTER TABLE `obj_transforms` DISABLE KEYS */;
INSERT INTO `obj_transforms` (`id`, `def_id`, `ship_id`, `costs_metal`, `costs_crystal`, `costs_plastic`, `costs_fuel`, `costs_food`, `costs_power`, `costs_factor_sd`, `costs_factor_ds`, `num_def`) VALUES
	(1, 14, 79, 0, 0, 0, 0, 0, 0, 0.0, 1.0, 1),
	(2, 15, 81, 0, 0, 0, 0, 0, 0, 0.0, 1.0, 1),
	(3, 16, 80, 0, 0, 0, 0, 0, 0, 0.0, 1.0, 1),
	(4, 17, 82, 0, 0, 0, 0, 0, 0, 0.0, 1.0, 1),
	(5, 18, 100, 0, 0, 0, 0, 0, 0, 0.0, 1.0, 1);
/*!40000 ALTER TABLE `obj_transforms` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.planets
DROP TABLE IF EXISTS `planets`;
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

-- Exportiere Daten aus Tabelle etoa_test.planets: 0 rows
DELETE FROM `planets`;
/*!40000 ALTER TABLE `planets` DISABLE KEYS */;
/*!40000 ALTER TABLE `planets` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.planet_types
DROP TABLE IF EXISTS `planet_types`;
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

-- Exportiere Daten aus Tabelle etoa_test.planet_types: 7 rows
DELETE FROM `planet_types`;
/*!40000 ALTER TABLE `planet_types` DISABLE KEYS */;
INSERT INTO `planet_types` (`type_id`, `type_name`, `type_habitable`, `type_comment`, `type_f_metal`, `type_f_crystal`, `type_f_plastic`, `type_f_fuel`, `type_f_food`, `type_f_power`, `type_f_population`, `type_f_researchtime`, `type_f_buildtime`, `type_collect_gas`, `type_consider`) VALUES
	(2, 'Wasserplanet', 1, 'Die Oberfläche dieses Planeten besteht zum grössten Teil aus Ozeanen. Die wenigen Landteile sind nicht wirklich geeignet für grossflächigen Abbau von Mineralen, dafür kann aus dem Wasser Tritium gewonnen werden. Ebenfalls ist durch das viele vorhandene Wasser die Hauptgrundlage für Nahrungsabbau gelegt, ausserdem ist der Planet bestens geeignet, mit Hilfe von Wasserkraftwerken grosse Mengen an Energie zu erzeugen.', 1.00, 1.00, 1.05, 0.85, 1.10, 1.20, 1.00, 1.00, 0.85, 0, 1),
	(1, 'Erdähnlicher Planet', 1, 'Dieser Planet hat eine sehr ausgeglichene Umwelt und ähnelt unseren ehemaligen Erde am meisten. Da der Mensch ein Gewohnheitstier ist, sind erdähnliche Planeten ideal für das Heranwachsen einer Zivilisation geeignet, da die notwendigen Voraussetzungen für alle Bereiche gegeben sind.', 1.20, 0.90, 1.30, 1.05, 1.40, 1.10, 1.20, 1.00, 1.00, 0, 1),
	(3, 'Wüstenplanet', 1, 'Wüste, Sand, Trockenheit und ein unwirtliches Klima zeichnet diesen Planetentyp aus. Der allgegenwärtige Sand hat aber auch etwas positives, denn aus ihm können grosse Mengen von wertvollem Silizium gewonnen werden.', 0.95, 1.45, 1.00, 0.90, 1.00, 0.90, 0.85, 0.85, 0.95, 0, 1),
	(4, 'Eisplanet', 1, 'Auf diesem unwirtlichen Planeten lockt einzig der Abbau von Tritium, welches sich aus den Eisschichen herausextrahieren lässt.\r\nVor kurzem haben Forscher eine neue chemische Methode entwickelt, aus Eismassen Silizium zu gewinnen. Diese neuartige Abbaumöglichkeit macht die Eisplaneten für Silizium-Anwender interessanter.', 1.10, 1.30, 1.25, 1.30, 0.95, 0.90, 1.00, 1.00, 1.05, 0, 1),
	(7, 'Gasplanet', 0, 'Dieser Planet ist unbewohnbar, da er keine feste Oberfläche hat, sondern aus lauter gasartigen Nebeln besteht. Seine Gase lassen sich jedoch mit Hilfe von Gassaugern zu Tritium umwandeln.', 0.50, 0.60, 0.40, 3.00, 0.30, 1.20, 0.30, 1.20, 1.20, 1, 1),
	(5, 'Dschungelplanet', 1, 'Riesige Wälder wachsen auf diesem Planeten, dessen Klima sehr gut für das Wachstum der Umwelt ist. Daher kann viel Nahrung für die Bevölkerung geerntet werden, welche sich auf einem Dschungelplaneten auch sonst sehr wohl fühlt.', 0.90, 1.20, 1.15, 1.20, 1.40, 1.00, 1.10, 1.00, 1.00, 0, 1),
	(6, 'Gebirgsplanet', 1, 'Den Namen hat dieser Planetentyp durch seine felsige Oberfläche erhalten. Ein Abbau von Erzen bietet sich optimalerweise an, hingegen ist der Abbau von Nahrung und die Herstellung von PVC mit Aufwand verbunden, da die Umgebung deren Anforderungen nicht gerecht wird.', 1.55, 1.00, 0.90, 0.90, 0.90, 1.10, 0.90, 1.00, 0.95, 0, 1);
/*!40000 ALTER TABLE `planet_types` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.quests
DROP TABLE IF EXISTS `quests`;
CREATE TABLE IF NOT EXISTS `quests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `quest_data_id` int NOT NULL,
  `slot_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `state` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_state_idx` (`user_id`,`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Exportiere Daten aus Tabelle etoa_test.quests: ~0 rows (ungefähr)
DELETE FROM `quests`;

-- Exportiere Struktur von Tabelle etoa_test.quest_log
DROP TABLE IF EXISTS `quest_log`;
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

-- Exportiere Daten aus Tabelle etoa_test.quest_log: ~0 rows (ungefähr)
DELETE FROM `quest_log`;

-- Exportiere Struktur von Tabelle etoa_test.quest_tasks
DROP TABLE IF EXISTS `quest_tasks`;
CREATE TABLE IF NOT EXISTS `quest_tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quest_id` int NOT NULL,
  `task_id` int NOT NULL,
  `progress` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `quest_fk` (`quest_id`),
  CONSTRAINT `quest_fk` FOREIGN KEY (`quest_id`) REFERENCES `quests` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Exportiere Daten aus Tabelle etoa_test.quest_tasks: ~0 rows (ungefähr)
DELETE FROM `quest_tasks`;

-- Exportiere Struktur von Tabelle etoa_test.races
DROP TABLE IF EXISTS `races`;
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

-- Exportiere Daten aus Tabelle etoa_test.races: 10 rows
DELETE FROM `races`;
/*!40000 ALTER TABLE `races` DISABLE KEYS */;
INSERT INTO `races` (`race_id`, `race_name`, `race_comment`, `race_short_comment`, `race_adj1`, `race_adj2`, `race_adj3`, `race_leadertitle`, `race_f_researchtime`, `race_f_buildtime`, `race_f_fleettime`, `race_f_metal`, `race_f_crystal`, `race_f_plastic`, `race_f_fuel`, `race_f_food`, `race_f_power`, `race_f_population`, `race_active`) VALUES
	(1, 'Terraner', 'Die Terraner sind eine eher jüngere Rasse, deren Vorfahren ursprünglich vom Planeten Erde kamen. Die Menschen sind besonders gut in der Herstellung von Plastik und dem Anbau von Nahrung. Ihre Schwächen liegen im Abbau von Kristallen. ', 'Terraner bauen Ressourcen gleichmässig gut ab und haben relativ starke Schiffe.', 'terranischer', 'terranisches', 'terranische', 'Präsident der Terraner', 0.95, 1.00, 1.00, 1.15, 0.90, 1.25, 0.95, 1.40, 1.00, 1.00, 1),
	(2, 'Andorianer', 'Die Andorianer sind zugleich humanoid und insektoid. Sie haben graublaue Haut und weisses Haar. Auf ihrem Kopf haben sie zwei Fühler, die ihnen zur feinfühligen sinnlichen Wahrnehmung dienen. Ihre Stärke ist die Produktion künstlicher Stoffe wie Plastik. Ihre Schwäche ist der schlechte Umgang mit Energie.', 'Der Andorianer verfügt über grosses Wissen in der PVC-Herstellung', '', '', '', 'Schwarmführer der Andorianer', 1.00, 1.00, 1.00, 0.95, 1.10, 1.60, 1.00, 1.00, 1.00, 1.40, 1),
	(3, 'Rigelianer', 'Die Rigelianer stammen aus dem Rigel-System. Ihre Stärke liegt im Abbau von Kristallen, die für Steuereinheiten in Gebäuden und Schiffen verwendet werden. Da sie lange nur auf den Handel mit Silizium gesetzt haben, sind ihre Kenntnisse beim Abbau anderer Stoffe eher schlecht.', 'Spezialist in der Siliziumherstellung', '', '', '', 'Kaiser der Rigelianer', 1.00, 1.00, 1.00, 0.85, 1.80, 0.95, 1.00, 0.90, 1.00, 1.10, 1),
	(4, 'Orioner', 'Die Orioner sind eine humanoide Rasse aus der Nähe des Orions. Die Gesellschaft der Orioner besteht hauptsächlich aus Schmugglern und Piraten. Ihre Schiffe sind bekannt für ihre Schnelligkeit.', 'Orioner haben sich auf die Schnelligkeit der Schiffe spezialisiert', '', '', '', 'Kapitän der Orioner', 1.00, 1.00, 1.80, 1.15, 1.00, 0.90, 1.10, 1.00, 1.20, 1.10, 1),
	(5, 'Minbari', 'Die Minbari sind eine humanoide Rasse. Dadurch, dass sie den Rohstoff Erdöl nie gekannt haben, sind sie seit Ewigkeiten auf den Abbau von Tritium spezialisiert. Durch ihre enormen Treibstoffreserven und ihre grossen Anwendungskenntnisse von Tritium haben sie relativ schnelle Raumschiffe.', 'Eine Rasse mit schnellen Schiffen und grossem Wissen über Tritium.', 'minbarischer', '', 'minbarische', 'Vorsteher des Minbarikonzils', 1.00, 1.00, 1.20, 1.00, 1.00, 1.00, 1.60, 1.00, 1.00, 1.00, 1),
	(8, 'Centauri', 'Der Centauri ist spezialisiert auf zivile Schiffe. Schnelle kleine Transportsonden sowie Hochentwickelte Gassauger, haben sie zu einem Vorreiter ziviler Wirtschaftimperien gemacht.', 'Haben die grössten Gassauger im Spiel.', '', '', '', 'Professor der Centauri', 1.05, 1.00, 1.00, 1.20, 1.15, 0.90, 1.20, 0.90, 1.10, 1.10, 1),
	(6, 'Ferengi', 'Die Ferengi sind eine humanoide Rasse. Sie sind etwas kleinwüchsiger als Menschen und  haben grosse Ohren. Die Stärke der Ferengi liegt beim Abbau von Metall.', 'Profi in der Titanherstellung', '', '', '', 'Grosser Nagus der Ferengi', 1.00, 1.00, 1.00, 1.75, 0.90, 0.90, 1.00, 1.05, 1.00, 1.00, 1),
	(7, 'Vorgonen', 'Die Vorgonen sind eine Rasse die auf den Schiffbau ausgelegt ist. Entsprechend sind sie vor allem im Beschaffen von Treibstoff und Kunststoff für den Bau der Schiffe gut.', 'Vorgonen können Schiffe ohne Start und Landezeit bauen.', '', '', '', 'Architekt der Vorgonen', 1.00, 0.90, 1.10, 0.85, 0.85, 1.30, 1.20, 1.10, 1.10, 1.00, 1),
	(9, 'Cardassianer', 'Seit einer grossen Hungersnot haben sich die Cardassianer auf die Nahrungsherstellung spezialisiert, haben aber den Abbau von Erzen vernachlässigt.\r\nIhre andere Stärke liegt in der Fähigkeit der Mutterschiffe zur Regeneration von ganzen Flottenverbänden.', 'Spezialist in der Nahrungsherstellung', '', '', '', 'Zentralrat der Cardassianer', 1.00, 1.00, 1.00, 0.90, 0.90, 1.20, 1.00, 1.60, 1.10, 1.30, 1),
	(10, 'Serrakin', 'Die Serrakin sind eine sehr friedliche Rasse, welche sich nicht gerne in grosse Auseinandersetzungen einmischt. Sie weiss sich aber bei Angriffen sehr gut zu wehren, da die Verteidigungstechnologie ihr Spezialgebiet ist.', 'Der Serrakin setzt auf die Verteidigung seiner Kolonien', 'serrakinischer', '', 'serrakinische', 'Beschützer der Serrakin', 1.00, 0.95, 0.90, 1.10, 1.15, 1.05, 0.95, 1.05, 1.40, 1.00, 1);
/*!40000 ALTER TABLE `races` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.reports
DROP TABLE IF EXISTS `reports`;
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

-- Exportiere Daten aus Tabelle etoa_test.reports: ~0 rows (ungefähr)
DELETE FROM `reports`;

-- Exportiere Struktur von Tabelle etoa_test.reports_battle
DROP TABLE IF EXISTS `reports_battle`;
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

-- Exportiere Daten aus Tabelle etoa_test.reports_battle: ~0 rows (ungefähr)
DELETE FROM `reports_battle`;

-- Exportiere Struktur von Tabelle etoa_test.reports_market
DROP TABLE IF EXISTS `reports_market`;
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

-- Exportiere Daten aus Tabelle etoa_test.reports_market: ~0 rows (ungefähr)
DELETE FROM `reports_market`;

-- Exportiere Struktur von Tabelle etoa_test.reports_other
DROP TABLE IF EXISTS `reports_other`;
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

-- Exportiere Daten aus Tabelle etoa_test.reports_other: ~0 rows (ungefähr)
DELETE FROM `reports_other`;

-- Exportiere Struktur von Tabelle etoa_test.reports_spy
DROP TABLE IF EXISTS `reports_spy`;
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

-- Exportiere Daten aus Tabelle etoa_test.reports_spy: ~0 rows (ungefähr)
DELETE FROM `reports_spy`;

-- Exportiere Struktur von Tabelle etoa_test.runtime_data
DROP TABLE IF EXISTS `runtime_data`;
CREATE TABLE IF NOT EXISTS `runtime_data` (
  `data_key` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `data_value` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`data_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Exportiere Daten aus Tabelle etoa_test.runtime_data: 0 rows
DELETE FROM `runtime_data`;
/*!40000 ALTER TABLE `runtime_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `runtime_data` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.schema_migrations
DROP TABLE IF EXISTS `schema_migrations`;
CREATE TABLE IF NOT EXISTS `schema_migrations` (
  `version` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle etoa_test.schema_migrations: 0 rows
DELETE FROM `schema_migrations`;
/*!40000 ALTER TABLE `schema_migrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `schema_migrations` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.shiplist
DROP TABLE IF EXISTS `shiplist`;
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

-- Exportiere Daten aus Tabelle etoa_test.shiplist: 0 rows
DELETE FROM `shiplist`;
/*!40000 ALTER TABLE `shiplist` DISABLE KEYS */;
/*!40000 ALTER TABLE `shiplist` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.ships
DROP TABLE IF EXISTS `ships`;
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

-- Exportiere Daten aus Tabelle etoa_test.ships: ~113 rows (ungefähr)
DELETE FROM `ships`;
INSERT INTO `ships` (`ship_id`, `ship_name`, `ship_type_id`, `ship_shortcomment`, `ship_longcomment`, `ship_costs_metal`, `ship_costs_crystal`, `ship_costs_fuel`, `ship_costs_plastic`, `ship_costs_food`, `ship_costs_power`, `ship_power_use`, `ship_fuel_use`, `ship_fuel_use_launch`, `ship_fuel_use_landing`, `ship_prod_power`, `ship_capacity`, `ship_people_capacity`, `ship_pilots`, `ship_speed`, `ship_time2start`, `ship_time2land`, `ship_show`, `ship_buildable`, `ship_order`, `ship_actions`, `ship_bounty_bonus`, `ship_heal`, `ship_structure`, `ship_shield`, `ship_weapon`, `ship_race_id`, `ship_launchable`, `ship_fieldsprovide`, `ship_cat_id`, `ship_fakeable`, `special_ship`, `ship_max_count`, `special_ship_max_level`, `special_ship_need_exp`, `special_ship_exp_factor`, `special_ship_bonus_weapon`, `special_ship_bonus_structure`, `special_ship_bonus_shield`, `special_ship_bonus_heal`, `special_ship_bonus_capacity`, `special_ship_bonus_speed`, `special_ship_bonus_pilots`, `special_ship_bonus_tarn`, `special_ship_bonus_antrax`, `special_ship_bonus_forsteal`, `special_ship_bonus_build_destroy`, `special_ship_bonus_antrax_food`, `special_ship_bonus_deactivade`, `special_ship_bonus_readiness`, `ship_points`, `ship_alliance_shipyard_level`, `ship_alliance_costs`, `ship_tradable`) VALUES
	(1, 'UNUKALHAI Transportschiff', 1, 'Dies ist ein grosses Transportschiff, dessen Lagerräume alle Arten von Waren aufnehmen können. ', 'Nachdem die Algol Transportschiffe sich mit einem ungeahnten Erfolg im ganzen Universum verbreitet hatten, wurde das Unukalhai Transportschiff konzipiert, welches eine grössere Lagerkapazität aufweist. Da man die Konvois mit Antares schützte, war auch für die Unukalhais keine grössere Bewaffnung nötig; man konzentrierte sich ausserdem vor allem auf die grössere Sicherheit für die Navigationssysteme, weil diese bei den Algols oft wegen kosmischer Strahlung ausgefallen sind.', 6000, 1400, 0, 2100, 0, 0, 0, 23, 35, 5, 0, 65000, 0, 1, 3449, 450, 225, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', 0.33, 0, 400, 100, 27, 0, 1, 0, 2, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 9.500, 0, 0, 1),
	(2, 'ANTARES Jäger', 1, 'Kleines Kampfschiff, ideal für die Begleitung kleinerer Konvois. Auch geeignet für Raubzüge und Übergriffe auf schwach befestigte Planeten.', 'Der Antares Jäger wurde als erster kampftauglicher Jäger hergestellt, um die Rohstoffkonvois vor Piraten zu schützen. Sie eignen sich zu Beginn als Begleitschutz, aber ihre Technologie ist nicht sehr weit entwickelt, deshalb sind die Herstellungskosten im Vergleich mit ihrer Leistung relativ hoch. Die Antares wurden nicht für grössere Angriffe auf befestigte Planeten konzipiert, auch deshalb werden sie von den wenigsten Armeen in grösseren Mengen genutzt.', 750, 575, 50, 420, 0, 0, 0, 3, 2, 1, 0, 500, 0, 1, 770, 150, 150, 1, 1, 0, 'transport,position,attack,flight,support,alliance', 0.33, 0, 330, 60, 153, 0, 1, 0, 1, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1.795, 0, 0, 0),
	(3, 'ZAVIJAH Spionagesonde', 1, 'Diese Sonde erkundet fremde Planeten und sendet die Daten an dein Kontrollzentrum zurück.', 'Nachdem die Raumpiraten wegen den schnell konstruierten planetaren Verteidigungsanlagen nicht mehr jedes System gefahrlos ausrauben konnten, erfanden sie dieses kleine, nützliche Schiff. Es kann in Frage kommende Planeten ausspionieren und detaillierte Informationen über die stationierte Flotte liefern. Dank seiner Geschwindigkeit wird es dabei äusserst selten abgeschossen. Um diese Geschwindigkeit erreichen zu können, müssen sie sehr leicht gebaut sein und können keine Bewaffnung tragen. Ausserdem haben sie einen sehr kleinen Laderaum und können deshalb nur über kürzere Distanzen verwendet werden.', 100, 300, 0, 80, 0, 0, 0, 1, 1, 0, 0, 150, 0, 0, 25000, 2, 1, 1, 1, 9, 'position,spy,flight,support', 0.33, 0, 10, 1, 1, 0, 1, 0, 1, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.480, 0, 0, 0),
	(4, 'TAURUS Besiedlungsschiff', 1, 'Das TAURUS Besiedlungsschiff ist ein Schiff, mit dem andere Planeten besiedelt werden können. Es kann Rohstoffe für die Startgebäude aufnehmen, ist aber auch langsam.', 'Sobald auf dem Heimatplaneten die grundlegende Infrastruktur aufgebaut war, waren die Herrscher mit nur einem Planeten nicht mehr zufrieden. Also baute man die Taurus Besiedlungsschiffe, die andere Planeten für das eigene Imperium besiedeln können. Da sie die ganze Lebenserhaltung für die Kolonialisten in einer lebensfeindlichen Umwelt gewährleisten müssen, gestaltet sich ihre Herstellung als langwierig und teuer, und das Schiff kann wegen seiner Masse nur langsam bewegt werden.', 8000, 10500, 1200, 5000, 0, 0, 0, 7, 8, 3, 0, 10000, 0, 5, 968, 375, 188, 1, 1, 8, 'transport,position,attack,colonize,flight,support,alliance', 0.33, 0, 1000, 500, 100, 0, 1, 0, 2, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 24.700, 0, 0, 1),
	(6, 'HADAR Schlachtschiff', 1, 'Das HADAR Schlachtschiff ist ein gut gepanzertes und stark bewaffnetes Kriegsschiff. Mit ihm können auch grössere Verteidigungsstellungen ausgeschaltet, oder die eigenen Planeten vor Angriffen geschützt werden.', 'Nachdem jede noch so kleine Nation eine Verteidigung errichtet hatte, welche mit Antares ohne tragbare Verluste nicht geknackt werden konnte, entschlossen sich die grösseren Nationen, ein neues Kampfschiff zu konstruieren. Man nahm den Rumpf eines Besiedlungsschiffes, baute Waffen und eine Panzerung ein, und das Hadar Schlachtschiff war geboren.', 50000, 31500, 19500, 12500, 0, 0, 0, 4, 5, 40, 0, 8500, 0, 4, 4400, 338, 200, 1, 1, 3, 'transport,position,attack,flight,support,alliance', 0.33, 0, 28200, 7100, 11700, 0, 1, 0, 1, 1, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 113.500, 0, 0, 0),
	(7, 'POLLUX Bomber', 1, 'Dieses Raumschiff ist sehr effektiv gegen gegnerische Verteidigungsanlagen.', 'Trotz allen Erfolgen, die die Hadar Schlachtschiffe bei der Zerstörung gegnerischer Flotten und Verteidigung erzielten, war man damit noch nicht zufrieden. Deshalb konstruierte man ein neues, bis an die Zähne bewaffnetes Schiff, den Pollux Bomber. Nachdem man das Schiff mit Waffen beladen hatte, erwies es sich, dass dadurch die Angriffsgeschwindigkeit eingeschränkt wurde. Wegen diesem Nachteil konnte der Bomber sich in grossen Flotten nicht etablieren, er ist aber trotzdem in allem eine nicht zu unterschätzende Waffe, welche grosse Zerstörung anrichten kann.', 9700, 21000, 11500, 8500, 0, 0, 0, 2, 25, 5, 0, 2000, 0, 2, 3080, 338, 169, 1, 1, 4, 'transport,position,attack,flight,support,alliance', 0.33, 0, 2600, 500, 16200, 0, 1, 0, 1, 1, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 50.700, 0, 0, 0),
	(8, 'SIRIUS Invasionsschiff', 1, 'Mit Hilfe dieses Raumschiffes können Planeten von inaktiven Imperatoren übernommen werden.', 'Es gab einmal ein florierendes Wirtschaftsimperium und die Infrastruktur ihrer Kolonien wurde von den anderen Völkern beneidet. Einer dieser bösen Nachbaren hatte die Idee, dass er so einen Planeten wirklich gut gebrauchen könnte. So wurde unter strengster Geheimhaltung dieses Invasionsschiff gebaut, welches die Planeten von inaktiven Herrschern übernehmen kann. Das Schiff hat aber nicht die grössten Erfolgschancen und es kann keine Hauptplaneten übernehmen. ', 80000, 35000, 55000, 40500, 0, 0, 0, 8, 400, 250, 0, 20000, 0, 20, 1800, 1200, 900, 1, 1, 9, 'transport,position,attack,invade,flight,support,alliance', 0.33, 0, 2000, 3000, 162, 0, 1, 0, 1, 1, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 210.500, 0, 0, 0),
	(9, 'ALGOL Transportschiff', 1, 'Dies ist ein kleines Transportschiff, dessen Lagerräume alle Arten von Waren aufnehmen können. ', 'Das Algol Transportschiff war das erste wirkliche Raumschiff, welches in Serienproduktion ging. Man wollte damit vor allem Rohstoffe zu anderen Planeten transportieren, damit man die natürlichen Ressourcen der verschiedenen Planeten besser ausnutzen kann. Deshalb hat man bei der Ausrüstung auf eine Bewaffnung weitestgehend verzichtet. Obwohl Algols mittlerweile veraltet sind, hat man dieses beliebte Schiff immer wieder mit neuen Motoren modifiziert, deshalb sieht man auch heute noch viele Transporter ähnlichen Typs.', 700, 180, 0, 500, 0, 0, 0, 13, 5, 2, 0, 15000, 0, 1, 968, 375, 188, 1, 1, 7, 'transport,fetch,position,attack,flight,support,alliance', 0.33, 0, 50, 50, 9, 0, 1, 0, 2, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1.380, 0, 0, 1),
	(10, 'REGULUS Trümmersammler', 1, 'Mit diesem Schiff können die Trümmer der nach einer Schlacht zerstörten Schiffe eingesammelt, in grosse Tranporter geladen und damit wiederverwendet werden.', 'Nachdem die Piraten durch die Entwicklung der mächtigen Kampfschiffe nicht mehr die unbewaffneten Transportkonvois überfallen konnten, entwickelten sie dieses Schiff, um mit ihm nach den grösseren Schlachten zwischen den kriegslustigen Imperien aufzutauchen, und ihren Lebensunterhalt aus den Überresten der zerstörten Schiffe zu gewinnen. Der Wert dieser Trümmersammler wurde schon bald erkannt, und ab dann führte niemand mehr Krieg, ohne sich nicht die Überreste der Schiffe zurück zu holen. Die Trümmersammler sammeln die Trümmer ein und laden sie auf die mitgeschickten Transportschiffe.', 300, 200, 100, 800, 0, 0, 5, 1, 1, 0, 0, 1500, 0, 2, 2750, 216, 72, 1, 1, 10, 'transport,collectdebris,position,attack,flight,support,alliance', 0.33, 0, 80, 120, 2, 0, 1, 0, 8, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1.400, 0, 0, 1),
	(11, 'RIGEL Dreadnought', 1, 'Dieses Schiff ist eine riesige fliegende Festung. ', 'Aus der Erfahrungen, die man mit den Hadar und den Pollux gewonnen hatte, wurde ein neues Superschiff kreiert, der Rigel Dreadnought. Optimierungen in der Herstellung und bei den Antrieben verliehen dem Schiff eine aussergewöhnliche Kampfkraft, Effizienz und Geschwindigkeit zu erstaunlich niedrigen Preisen. Zusätzlich erhöhte man die Transportkapazität, so dass die Rigel eigenständig praktisch aus dem Nichts heraus Raubzüge unternehmen können, ohne sich mit langsamen Transportern zu belasten. ', 3350000, 2975000, 1750000, 750000, 0, 0, 0, 100, 1100, 1500, 0, 600000, 0, 400, 6050, 480, 300, 1, 1, 5, 'transport,position,attack,flight,support,alliance', 0.33, 0, 1000000, 1350000, 1575000, 0, 1, 0, 1, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 8825.000, 0, 0, 0),
	(12, 'ELNATH Gassauger', 1, 'Dieses Schiff kann Wasserstoff aus der Atmosphäre von Gasplaneten einsaugen und daraus Tritium gewinnen.', 'Nachdem die Flotten immer grösser wurden, hatte man nicht mehr genug Tritium auf den Planeten zur Verfügung, um sie zu bewegen. Deshalb kam man auf die Idee, Wasserstoff von den unbewohnbaren Gasplaneten abzusaugen und es in Tritium umzuwandeln. Genau dafür wurde dieses Schiff konstruiert. Es wurde schnell klar, dass dieses Saugen äusserst rentabel ist und deshalb wurde der Gassauger soweit verbessert, dass heute eine grössere Flotte ohne ihn praktisch undenkbar ist.', 20000, 7500, 22200, 15000, 0, 0, 0, 28, 80, 65, 0, 15000, 0, 3, 880, 3225, 645, 1, 1, 12, 'transport,position,collectcrystal,collectfuel,flight,support', 0.33, 0, 650, 800, 0, 0, 1, 0, 8, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 64.700, 0, 0, 1),
	(13, 'ANDROMEDA Kampfstern', 1, 'Dieses Schiff ist das mächtigste Schiff der Galaxien.', 'Ein verrückter Wissenschaftler war von der Idee besessen, ein Kampfschiff zu bauen, welches so gross wie ein ganzer Trabant wäre. Er wurde so lange ausgelacht, bis er einen anderen Verrückten traf, der zufällig nebenberuflich Imperator war und der ihn unterstützte. Danach wurde Wissenschaftler allgemein als Genius bekannt, welcher die ultimative Waffe erschaffen hatte: den Andromeda Kampfstern. Seine Waffen und Schilder sind bis heute noch unübertroffen!\r\nDer einzige Nachteil dieses monströsen Kampfschiffes ist, dass es wegen seiner Masse lange Start- und Landezeiten hat, und eine zahlreiche Besatzung benötigt wird.', 20000000, 10000000, 12000000, 12000000, 0, 0, 0, 400, 4000, 2000, 0, 6000000, 0, 990, 9900, 600, 600, 1, 1, 7, 'transport,position,attack,flight,support,alliance', 0.33, 0, 8500000, 9000000, 8550000, 0, 1, 0, 1, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 54000.000, 0, 0, 0),
	(14, 'STARLIGHT Jäger', 1, 'Weiterentwicklung des ANTARES Jäger.', 'Parallel zu den Antares Jägern wurde der STARLIGHT Jäger entwickelt, welcher besser gepanzert war und auch die bessere Bewaffnung aufwies. Er nutzte auch einen neuartigen Antrieb, welcher aber noch nicht ganz serienreif war, da er andauernd ausfiel, und selten wie geplant lief. Nach einigen Untersuchungen fand man heraus, dass dies daran lag, dass beim Bau des Motors billiges Material verwendet wurde. Das stellte den viel gelobten Jäger in ein anderes Licht, aber andererseits erwies er sich in Raumschlachten als zuverlässiger Mitstreiter.', 4900, 3400, 2400, 2100, 0, 0, 0, 1, 3, 3, 0, 800, 0, 1, 1430, 160, 200, 1, 1, 1, 'transport,position,attack,flight,support,alliance', 0.33, 0, 2100, 1100, 1710, 0, 1, 0, 1, 1, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 12.800, 0, 0, 0),
	(15, 'ONEFIGHT Kampfdrohne', 1, 'Die Einweg-Kampfdrohne ist sehr nützlich, um vor einem Angriff die gegnerische Kampfflotte zu zerstören.', 'Es gab zwei Nachbarn, die lange Zeit friedlich miteinander lebten, aus dem einfachen Grund, dass die Flotten beider Kontrahenten etwa gleich gross war und niemand den anderen ohne Verluste hätte angreifen können. Das änderte sich, als der erste die Kampfdrohne entwickelte, ein billiges Schiff, welches aber eine äusserst grosse Kampfkraft aufweist, aber sobald es von einer Waffe getroffen wird, explodiert. Als die Flotte des einen zerstört war, hatte man der Invasion nichts mehr entgegenzusetzen, und jetzt leben sie als eine Rasse wieder friedlich miteinander.', 200, 700, 300, 300, 0, 0, 0, 1, 8, 1, 0, 300, 0, 0, 13200, 100, 120, 1, 1, 2, 'position,attack,flight,support,alliance', 0.33, 0, 0, 0, 585, 0, 1, 0, 1, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1.500, 0, 0, 0),
	(16, 'Handelsschiff', 1, 'Ein Schiff der neutralen Handelsgilde.', 'Ein Schiff der neutralen Handelsgilde. Es wird benutzt um Einkäufe im Markt zu den Käufern zu transportieren.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 100000, 0, 0, 15000, 60, 60, 0, 0, 0, 'market', 0.37, 0, 0, 0, 0, 0, 0, 0, 5, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 0, 0, 1),
	(17, 'TERRANIA Zerstörer', 1, 'Das Kriegsschiff der Terraner.', 'Eine Weiterentwicklung des Polluxbombers.\r\nSchneller, stärker und besser.', 85000, 40000, 50000, 40000, 0, 0, 0, 6, 55, 45, 0, 50000, 0, 7, 7150, 450, 360, 1, 1, 0, 'transport,position,attack,flight,support,alliance', 0.33, 0, 20000, 20000, 55000, 1, 1, 0, 4, 1, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 215.000, 0, 0, 0),
	(18, 'PROMETHEUS TerraFormer', 1, 'Ein Besiedlungsschiff und Trümmersammler, weil Terraner gerne Land erobern und die nötigen Rohstoffe dafür so gewinnen können, sofern sie genügend Transportschiffe mitschicken.', 'Dieser Recycler wurde nach Prometheus dem Titanen, welcher gegen Zeus rebellierte, und den Menschen das Feuer brachte, benannt, da mit den Rohstoffen, welche die Terraner mit seiner Hilfe gewinnen, deren Flotten gebaut werden. Früher brachte Prometheus ihnen mit dem Feuer die Möglichkeit, eine Kultur zu entwickeln. Heute bringen viele Tausend Prometheus den Menschen mit ihren Rohstoffen die Grundlage, ihre Kultur weiterzuentwickeln und ihre Kultur im weiten All zu verbreiten.\r\nDer Prometheus Recycler ist in der Lage, verschiedene Transporter mit den abgebauten Trümmern zu befüllen.', 9000, 8000, 1500, 4200, 0, 0, 0, 10, 12, 20, 0, 50000, 0, 5, 1870, 135, 90, 1, 1, 0, 'transport,collectdebris,position,attack,colonize,flight,support,alliance', 0.33, 0, 800, 1000, 5, 1, 1, 0, 4, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 22.700, 0, 0, 1),
	(19, 'GAIA Transporter', 1, 'Beliebtester Bewohnertransporter der Andromedabevölkerung, solange auf dem Bestimmungsort genug Platz vorhanden ist.', 'Als die Planeten wegen Überbevölkerung einen vollständigen Kollaps erlitt, musste sie schleunigst evakuiert werden, und dafür wurde dieser Transporter entwickelt. Die Bewohner wurden zu Zehntausenden bei normalerweise untragbaren Bedingungen in diese Kolosse gesteckt und verfrachtet. Nach dieser Katastrophe etablierte dieser Transporter sich zu einem beliebten Fährschiff, mit welchem die Leute zu den Vergnügungsplaneten flogen, um sich vom täglichen Arbeitsstress zu erholen.', 3500, 1000, 750, 1250, 0, 0, 0, 23, 50, 40, 0, 3000, 10000, 1, 2420, 405, 203, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', 0.33, 0, 750, 300, 50, 0, 1, 0, 2, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 6.500, 0, 0, 1),
	(20, 'ANDREIA Bomber', 1, 'Dieser Bomber ermöglicht Angriffe auf Gebäude sowie einen Antraxangriff.', 'Den Andorianer war die Infrastruktur der Gegner ein Dorn im Auge, so erschufen sie einen Bomber, der sogar aus dem Orbit zielgenau Bomben abwerfen kann und somit Gebäude zerstören kann.', 85000, 40000, 50000, 40000, 0, 0, 0, 47, 263, 325, 0, 15000, 0, 25, 1000, 1200, 1320, 0, 0, 0, 'transport,position,attack,bombard,antrax,flight,support,alliance', 0.33, 0, 25000, 9000, 9000, 2, 0, 0, 4, 1, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 215.000, 0, 0, 0),
	(21, 'ATLAS Transporter', 1, 'Grosser Transporter', 'Auch die Andorianer entwickelten einen grösseren Transporter, da sie nicht wollten, dass andere Rassen mit ihren Transportern ihre Ressourcen herumschippern konnten und sie von diesen abhängig wären. Die Atlas entwickelten sich zu viel genutzten Transportern im Andorianischen Imperium. Sie erwiesen sich als viel nützlicher, als es sich die Regierungsmitglieder jemals erhofft hätten.', 2000, 2000, 2000, 23000, 0, 0, 0, 18, 50, 5, 0, 325000, 0, 1, 8168, 288, 216, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', 0.33, 0, 300, 500, 27, 2, 1, 0, 7, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 29.000, 0, 0, 1),
	(22, 'ZELOS Kreuzer', 1, 'Weiterentwicklung des HADAR Schlachtschiffes.', 'Nachdem sich der Bau von Hadar Schlachtschiffen durchgesetzt hatte, wollten die Andorianer diese noch übertreffen. So wurde der Zelos Kreuzer entwickelt.\r\nDieses Schiff hat ungeheuer starke Schilde und ist sehr gut für die Verteidigung von Planeten geeignet, macht aber auch ordentlich Schaden bei einem Angriff.', 121000, 44000, 50000, 45400, 0, 0, 0, 8, 75, 40, 0, 16000, 0, 10, 5280, 450, 315, 1, 1, 2, 'transport,position,attack,flight,support,alliance', 0.33, 0, 15000, 56500, 45000, 2, 1, 0, 4, 1, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 260.400, 0, 0, 0),
	(23, 'CENTAURUS Spioschiff', 1, 'Kann von einem anderen Spieler eine Technologie klauen.', 'Die Centauri waren äusserst stolz darauf, dass sie die höchsten Technologien aller Völker besassen. Entsprechend gross war der Neid, als sie von einem andern Volk in einer von ihnen vernachlässigten Technologie übertrumpft wurden. Also erfanden sie dieses Spionageschiff, mit dessen Hilfe sie den anderen Völkern etwaige höher entwickelte Technologien klauen können.', 85000, 40000, 50000, 40000, 0, 0, 0, 4, 63, 125, 0, 7500, 0, 20, 600, 2905, 2359, 0, 0, 0, 'transport,position,attack,spyattack,flight,support,alliance', 0.33, 0, 3250, 2250, 500, 8, 0, 0, 4, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 215.000, 0, 0, 1),
	(24, 'PEGASUS Gassauger', 1, 'Grosser Gassauger der Centauri.', 'Um ihre teuren Forschungen zu betreiben, mussten die Centauri einen neuen Gassauger entwerfen, welcher eine grössere Kapazität hat, da die normalen Sauger die Bedürfnisse ihrer Forschungslabore nicht stillen konnten. Der Pegasus hat  eine wesentlich grössere Saugkapazität als herkömmliche Sauger, und durch seine hoch entwickelten Saugarme hat er die grössere Effizienz. Dies ist die Antwort der Centauri auf Tritiumknappheit.', 130000, 60000, 60000, 100000, 0, 0, 0, 30, 10, 16, 0, 120000, 0, 15, 1100, 2700, 2700, 1, 1, 0, 'transport,position,attack,collectfuel,flight,support,alliance', 0.33, 0, 4000, 4000, 50, 8, 1, 0, 4, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 350.000, 0, 0, 1),
	(25, 'EUROPA Fighter', 1, 'Mittelgrosses, für seine Verhältnisse jedoch sehr starkes Kriegsschiff der Centauri.', 'Die Centauri suchten ihren Vorteil in der Überlegenheit der Technologien, aber als die Rigel die Herrschaft über die Schlachtfelder übernahmen, entwickelten sie ihren eigenen Prototypen, den Europa Fighter. Heutzutage eines der stärksten Raumschiffe der mittleren Kampfklasse. Die Europas sind bei weitem nicht so stark wie Rigel, jedoch haben sie eine sehr kurze Startzeit, was sie sehr gefährlich macht.', 20000, 11000, 18000, 8000, 0, 0, 0, 2, 15, 10, 0, 22000, 0, 3, 6050, 263, 478, 1, 1, 0, 'transport,position,attack,flight,support,alliance', 0.33, 0, 6250, 12500, 6750, 8, 1, 0, 4, 1, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 57.000, 0, 0, 0),
	(26, 'VORGONIA Vernichter', 1, 'Sehr grosses Kampfschiff der Vorgonen.', 'Den Vorgonen ging es gehörig gegen den Strich, dass sie nicht mehr als Gefahr angesehen wurden, sobald ihr Feinde auf allen Planeten eine halbwegs vernünftige Verteidigung gebaut hatten. Um sich den verdienten Respekt zurückzuholen, erschufen sie den mächtigen Vorgonia Vernichter, welcher es sogar mit einem Imperialen Kreuzer aufnehmen kann.', 943000, 543000, 570000, 760000, 0, 0, 0, 63, 365, 270, 0, 350000, 0, 115, 4950, 563, 315, 1, 1, 0, 'transport,position,attack,flight,support,alliance', 0.33, 0, 295000, 520000, 409000, 7, 1, 0, 4, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 2816.000, 0, 0, 0),
	(27, 'ALAMAK Trümmersammler', 1, 'Trümmersammler der Orioner - sammelt Trümmerfelder und belädt Transporter mit den gewonnenen Rohstoffen.', 'Die Orioner konnten selbst durch ihr Wissen, mehr Geschwindigkeit aus ihren Schiffen zu ziehen, nicht vermeiden, dass ihre Trümmersammler stets zu spät nach einem Kampf eintrafen. Um diese Lücke in ihrem System zu korrigieren, erschufen sie den Alamak Trümmersammler, der sich insbesondere für Orioner als eine echte Alternative zu den bestehenden Trümmersammlern bewähren sollte und der mitgeschickte Transporter schnell und effizient mit den Rohstoffen beladen konnte.', 20000, 13000, 10000, 7000, 0, 0, 0, 25, 25, 50, 0, 150000, 0, 10, 4950, 432, 288, 1, 1, 0, 'transport,collectdebris,position,attack,flight,support,alliance', 0.33, 0, 3000, 2000, 20, 4, 1, 0, 4, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 50.000, 0, 0, 1),
	(28, 'IKAROS Jäger', 1, 'Schwebt in der Atmosphäre und hat somit keine Start- und Landezeit.', 'Die Vorgonen raubten alle ihre direkten Nachbarn mit Jägern aus, und bald einmal gab es erste Piloten, die gar nie mehr richtig auf dem Heimatplaneten landeten, sondern im Dauereinsatz waren. Dank ihren unerwarteten Raubzügen konnten sie viele Rohstoffe erbeuten. Diese Elitepiloten waren aber bald nicht mehr zufrieden mit den normalen Schiffen, also entwickelten sie ihre Jäger weiter, bis die Ikaros entstanden, die im Orbit des Planeten stationiert sind, so dass sie sofort und ohne Treibstoffverbrauch starten und landen können.', 4000, 2000, 1000, 2000, 0, 0, 0, 1, 0, 0, 0, 6000, 0, 1, 2200, 0, 0, 1, 1, 0, 'transport,position,attack,flight,support,alliance', 0.33, 0, 350, 2750, 1125, 7, 1, 0, 4, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 9.000, 0, 0, 0),
	(29, 'MARAUDER Transporter', 1, 'Grosser Transporter', 'Auch die Ferengi sahen sich genötigt, grosse Transporter zu entwickeln, wenn auch nicht aus denselben Gründen wie die anderen Rassen. Die Ferengi hatten wegen ihrer Titanproduktion alle ihre Lager längstens überfüllt und keinen Platz mehr, um grössere zu bauen. Also erschufen sie mit den Marauder Transportern eine Art fliegendes Lager, damit sie ihr Titan im Weltraum zwischenlagern konnten, wo es genug Platz dafür hat.', 23000, 2000, 2000, 2000, 0, 0, 0, 18, 50, 5, 0, 325000, 0, 1, 8168, 288, 216, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', 0.33, 0, 300, 500, 27, 6, 1, 0, 7, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 29.000, 0, 0, 1),
	(30, 'BELL Zerstörer', 1, 'Dieses Schiff wird mit Unmengen an Titan gepanzert, was es zu einer fast unzerstörbaren Waffe macht.', 'Die Ferengi waren an einem Punkt angelagt, wo sie ihr Titan nicht mehr verbrauchen konnten. Es musste etwas erfunden werden, das mit möglichst viel Titan und wenig Zusatzstoffen gebaut werden konnte. Aus diesem Bedürfnis entstand der Bell Zerstörer.\r\nDieses Schiff wird mit einer Titanlegierung gepanzert, die kaum überwunden werden kann. Aufgrund der Masse des Schiffes, dessen Antrieben und der dicken Panzerschicht gehört der Bell Zerstörer nicht zu den schnellsten Schiffen der Galaxien, jedoch zu den stärksten im Kampf.\r\nEin Nachteil des Bell Zerstörers ist sein immenser Tritiumverbrauch, der aus dem grossen Gewicht und der tiefen Fluggeschwindigkeit resultiert.', 800000, 300000, 450000, 250000, 0, 0, 0, 40, 175, 225, 0, 225000, 0, 45, 4950, 466, 428, 1, 1, 0, 'transport,position,attack,flight,support,alliance', 0.33, 0, 380000, 180000, 235000, 6, 1, 0, 4, 1, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1800.000, 0, 0, 0),
	(31, 'RUTICULUS Sammler', 1, 'Ein multifunktionales Schiff der Andorianer, das Asteroiden und Sternennebel sammeln kann.', 'Ein erstklassiger Wissenschaftler der Andorianer namens Herkules war viel herumgereist und hatte viele Schiffe analysiert. Dabei kroch er so viel auf den Knien herum, um das Schiff von allen Seiten zu untersuchen, dass er im Alter immer schlimme Schmerzen hatte und seine Zeit fast nur noch liegend verbringen konnte. Doch das hielt ihn nicht davon ab, weitere Schiffe zu entwickeln. Durch seine Erfahrung gelang es ihm, Berechnungen für einen Sammler zu finden, der sowohl Asteroiden wie auch Sternennebel besuchen konnte, um Rohstoffe von ihnen zu gewinnen. Er benannte es dann nach seinem Lieblingsstern.', 12800, 4800, 10000, 8000, 0, 0, 0, 4, 1, 1, 0, 18000, 0, 1, 4950, 1800, 3600, 1, 1, 0, 'transport,position,attack,collectmetal,collectcrystal,flight,support,alliance', 0.33, 0, 50, 2, 0, 2, 1, 0, 4, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 35.600, 0, 0, 1),
	(32, 'RIGELIA Bomber', 1, 'Kann ein Gebäude für eine bestimmte Zeit mittels EMP-Technologie ausser Kraft setzen und hat noch weitere Fähigkeiten.', 'Der Rigelia Bomber kann mit seinen EMP-Angriffen die feindlichen Gebäude für eine kurze Zeit ausser Kraft setzen, was in einem Krieg schwerwiegende Folgen haben kann. Obwohl der Bomber sehr teuer ist, und von seiner Kampfkraft her gesehen kaum genutzt werden sollte, sind viele Generäle der Meinung, dass seine Bomben genug effektiv sind, so dass man diese Möglichkeit in einem Krieg immer einsetzen sollte. Rigelianer können mit diesem Schiff auch zu noch drastischeren Mitteln greifen, und Nahrung und Bewohner mittels Antrax- und Gasangriffen dezimieren. ', 85000, 40000, 50000, 40000, 0, 0, 0, 28, 163, 125, 0, 15000, 0, 41, 1100, 1140, 1500, 1, 1, 0, 'transport,position,attack,emp,antrax,gasattack,flight,support,alliance', 0.33, 0, 25000, 6500, 12500, 3, 1, 0, 4, 1, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 215.000, 0, 0, 0),
	(33, 'EOS Transporter', 1, 'Grosser Transporter', 'Als die Cardassianer und die Minbari die grossen Transporter entwickelt hatten, konnten die Rigelianer dem nicht nachstehen und fertigten sofort ihre eigene Version eines grossen Transporters an. Vom Prinzip her ist es genau dasselbe Schiff wie der Saiph Transporter der Minbari. Die Rigelianer passten einfach das Design und die Steuergeräte ihren Bedürfnisse an.', 2000, 23000, 2000, 2000, 0, 0, 0, 18, 50, 5, 0, 325000, 0, 1, 8168, 288, 216, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', 0.33, 0, 300, 500, 27, 3, 1, 0, 7, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 29.000, 0, 0, 1),
	(34, 'HELIOS Drohne', 1, 'Weiterentwicklung der Onefight Kampfdrohne. Rassenschiff der Rigelianer.', 'Die Rigelianer waren von den Onefight Kampfdrohnen begeistert. Sie steckten deshalb ihre ganzen Forschungsmittel in deren Weiterentwicklung. So entstand die Helios Drohne: Diese Drohne ist noch effizienter als die Onefight und kann in genügend grosser Anzahl den Gegner empfindlich treffen. Ausserdem können die Helios im Gegensatz zu den Onefights einen Kampf auch überleben.\r\nDie Helios sind überall wegen ihrer Kampfkraft gefürchtet, und da sie auf dem Standardantrieb der Drohnen aufbauen, haben sie auch eine hohe Geschwindigkeit, weshalb man sich nie vor einem Angriff sicher fühlen kann.', 2500, 6200, 2000, 2300, 0, 0, 0, 1, 3, 3, 0, 500, 0, 0, 13200, 120, 180, 1, 1, 0, 'position,attack,flight,support,alliance', 0.33, 0, 1, 0, 5400, 3, 1, 0, 4, 1, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 13.000, 0, 0, 0),
	(36, 'CARDASSIA Mutterschiff', 1, 'Heilt während dem Kampf eine gewisse Anzahl Schild- und Strukturpunkte.', 'Nachdem die Cardassianer mit ihren Nilams die ganze Galaxie in Angst und Schrecken versetzt hatten, schlossen sich alle anderen Rassen zu einem Bund zusammen, um die Cardassianer zu vernichten. Trotzdem hatten sie nicht mit dem neuen Geniestreich der Cardassianer gerechnet: Den Mutterschiffen. Mit diesem hoch entwickelten Raumschiff können die Cardassianer ihre Flotte während dem Kampf reparieren, um so Verluste auszugleichen. Nur dank der Hilfe dieses Schiffes konnten die Cardassianer den immerwährenden Angriffen standhalten.', 42000, 27500, 16500, 16000, 0, 0, 0, 3, 35, 5, 0, 1500, 0, 5, 5500, 450, 405, 1, 1, 0, 'transport,position,attack,flight,support,alliance', 0.33, 16000, 10000, 6000, 113, 9, 1, 0, 4, 1, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 102.000, 0, 0, 0),
	(37, 'DEMETER Transporter', 1, 'Grosser Transporter', 'Die Cardassianer waren allgemein wegen ihren vielen Rohstoffen beneidet, vor allem wegen ihrer Nahrung, die sie wie keine anderen herstellen können. Um sich vor Übergriffen zu schützen und um ihre Gegner im Unklaren über ihre wahren Rohstoffmengen zu lassen, entwickelten sie diese Transporter, welche mit den Rohstoffen irgendwo in der Ewigkeit des Alls herumfliegen, damit sie nicht gefunden werden. Die Cardassianer sind die einzigen, deren Organisation solch perfekte Nachschublinien zustande bringt.', 23000, 8300, 1200, 1500, 0, 0, 0, 5, 50, 5, 0, 375000, 0, 1, 6353, 351, 237, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', 0.33, 0, 300, 500, 27, 9, 1, 0, 7, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 34.000, 0, 0, 1),
	(38, 'NILAM Fighter', 1, 'Ein starkes Kampfschiff aus der Mittelschweren Klasse, entwickelt von den Cardassianern.', 'Den Cardassianern waren Starlights von Anfang an zu langsam und Drohnen zu schwach. So erfanden sie die Nilam, welche sie zu gefürchteten Jägern entwickelten, da sie spezielle Antriebe haben, die ausserordentlich schnell sind. Die Cardassianer benutzen die Jäger vor allem, um ihre Militärdiktatur aufrechtzuerhalten. Sie wollen schnell reagieren und überall bereitstehen können. Dafür eignen sich die Nilams am besten. Sie kommen aus dem Nichts und verschwinden sofort wieder, nachdem sie die Schlacht gewonnen haben.', 7150, 4000, 2000, 3000, 0, 0, 0, 1, 3, 3, 0, 5000, 0, 1, 6050, 400, 270, 1, 1, 0, 'transport,position,attack,flight,support,alliance', 0.33, 0, 2900, 2000, 2250, 9, 1, 0, 4, 1, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 16.150, 0, 0, 0),
	(40, 'SERA Kreuzer', 1, 'Dieses Schiff ist ein passabler Kriegsschiff, das in grossen Mengen eine ordentliche Zerstörungskraft hat.', 'Den Ferengi gefiel es nicht, dass andere Rassen besondere Kriegsschiffe hatten, also gestalteten sie ihre eigene Version. Der führende Forscher entwickelte ein Kriegsschiff, dass neben den Bell Zerstörern einen guten Eindruck machen konnte. Für eine abschreckende Wirkung wurde es in Wildrosenrot angemalt. ', 3750, 6000, 3250, 2000, 0, 0, 0, 1, 2, 2, 0, 8000, 0, 1, 3080, 413, 83, 1, 1, 0, 'transport,position,attack,flight,support,alliance', 0.33, 0, 1500, 1000, 3500, 6, 1, 0, 4, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 15.000, 0, 0, 0),
	(41, 'HYPOS Drohne', 1, 'Kann ein Trümmerfeld beim Gegner erstellen, ohne dass dieser etwas merkt, ausser er überprüft genau zu diesem Zeitpunkt die Raumkarte.', 'Die Hypos Drohne wurde entwickelt, damit kein Angriff mehr für ein Trümmerfeld notwendig ist. Deshalb werden diese Drohnen vor grossen Schlachten losgeschickt, um beim Gegner ein klitzekleines Trümmerfeld zu erstellen. Zu diesem Zweck muss sich die Drohne beim Gegner in die Luft sprengen, was selten für mehr als eine Sternschnuppe wahrgenommen wird. Durch diese Aktion kann den Navigationscomputern der Trümmerfeldsammler ein gültiges Ziel zugewiesen werden und der Angriff und das Sammeln getimed werden. ', 500, 300, 50, 200, 0, 0, 0, 1, 5, 5, 0, 300, 0, 0, 30000, 1, 1, 1, 1, 0, 'position,createdebris,flight,support,alliance', 0.33, 0, 10, 0, 1, 0, 1, 0, 1, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1.050, 0, 0, 0),
	(42, 'MINBARI Jäger', 1, 'Wenn er alleine in oder in einem Flottenverband aus lauter Minbari Jägern fliegt, ist er für die gegnerische Flottenkontrolle nicht sichtbar.', 'Die Minbari sahen es gar nicht gerne, als man ihre Flotten schon im Anflug entdeckte und eine entsprechende Verteidigung bereitstellte. Deshalb liessen sie die besten Köpfe der Galaxie zusammenkommen, um dieses Schiff zu entwickeln, welches durch seine perfekte Tarnung erst im allerletzten Moment entdeckt werden kann. Und dann ist es bereits zu spät, da der Kampf bereits stattfand...', 20500, 13500, 13500, 10000, 0, 0, 0, 2, 25, 5, 0, 15000, 0, 3, 6600, 900, 375, 1, 1, 0, 'transport,position,attack,stealthattack,flight,support,alliance', 0.33, 0, 12000, 4500, 5500, 5, 1, 0, 4, 1, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 57.500, 0, 0, 0),
	(43, 'SAIPH Transporter', 1, 'Grosser Transporter der Minbari.', 'Die Minbari entwickelten diese grossen Transporter, um ihren steigenden Rohstofftransport-Bedürfnissen nachzukommen. Die Rohstoffmengen stiegen immer weiter an, und irgendwann war auch die Kapazität der Unukalhai ausgeschöpft. Nun musste eine neue Lösung gefunden werden, und die Ingenieure der Minbari entwickelten diesen grossen Transporter.', 2000, 2000, 23000, 2000, 0, 0, 0, 18, 50, 5, 0, 325000, 0, 1, 8168, 288, 216, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', 0.33, 0, 300, 500, 27, 5, 1, 0, 7, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 29.000, 0, 0, 1),
	(44, 'WEZEA Fighter', 1, 'Kampfschiff der Minbari.', 'Die Minbari liebten es seit eh und je, über die Gasplaneten zu fliegen, da sie von den unbeschreiblich schönen Polarlichtern fasziniert sind, welche man dort beobachten kann. \r\nEs ist ihnen sogar gelungen, den neuartigen Solarantrieb zu integrieren. Damit verbraucht der WEZEA Fighter nur Tritium für den Start und die Landung.', 2800, 1400, 1800, 1600, 0, 0, 0, 0, 1, 1, 0, 2500, 0, 1, 2500, 450, 270, 1, 1, 0, 'transport,position,attack,flight,support,alliance', 0.33, 0, 1450, 700, 1144, 5, 1, 0, 4, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 7.600, 0, 0, 0),
	(45, 'ORION Fighter', 1, 'Kampfschiff der Mittelschweren Klasse, entworfen von den Orionern. Kann 50% mehr Rohstoffe von einem fremden Planeten mitnehmen als alle anderen Schiffe.', 'Den Orionern war die Menge, welche sie normalerweise mit ihren Schiffen von gegnerischen Planeten erbeuten konnten, viel zu wenig. Der Orion Fighter ist ihre Antwort auf dieses Problem. Ein starkes Raumschiff, welches so konzipiert ist, dass es 50% mehr Rohstoffe als Beute mitnehmen kann als alle anderen Schiffe. Zusätzlich hat der Orion schlagkräftige Waffen, was den Fighter zum optimalen Schiff für Piraterie macht.', 35000, 10500, 12500, 5500, 0, 0, 0, 2, 25, 8, 0, 17500, 0, 3, 6600, 480, 330, 1, 1, 0, 'transport,position,attack,flight,support,alliance', 0.50, 0, 7500, 7000, 12600, 4, 1, 0, 4, 1, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 63.500, 0, 0, 0),
	(46, 'FORNAX Asteroidensammler', 1, 'Kann Asteroidenfelder anfliegen und dort Rohstoffe sammeln.', 'Da die Gassauger grossen Erfolg hatten, dachte man, dass man das auch mit Asteroidenfeldern versuchen könne, so dass man auch die anderen Rohstoffe aus dem Weltraum gewinnen konnte. Leider war die praktische Umsetzung schwieriger, da eine sichere  Navigation innerhalb der Asteroidenfelder sich als praktisch unmöglich erwies. Deshalb ist dieses Konzept fehlgeschlagen, da die Sammler schneller von Asteroiden getroffen werden, als dass sie genug Rohstoffe holen können, um ihre Herstellungskosten zurückzugewinnen.', 15000, 5000, 25000, 9000, 0, 0, 0, 33, 50, 60, 0, 17500, 0, 8, 1815, 3263, 788, 1, 1, 11, 'transport,position,attack,collectmetal,flight,support,alliance', 0.33, 0, 250, 1000, 50, 0, 1, 0, 8, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 54.000, 0, 0, 1),
	(47, 'TITAN Transporter', 1, 'Dies ist ein relativ billiger und sehr schneller, grosser Transporter.', 'Dies ist ein relativ billiger und sehr schneller, grosser Transporter, allerdings zeigt sich der Preis in seiner Qualität. Er ist sehr schwach. Dieser Transporter setzt auf den Solarantrieb, wodurch er durch ein Sonnensegel unglaublich schnell ohne Treibstoffverbrauch fliegen kann. \r\n', 14000, 4000, 1500, 3000, 0, 0, 0, 14, 15, 28, 0, 225000, 0, 1, 6292, 413, 345, 1, 1, 5, 'transport,fetch,position,attack,flight,support,alliance', 0.33, 0, 50, 20, 1, 0, 1, 0, 2, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 22.500, 0, 0, 1),
	(50, 'ASTERIO Recycler', 1, 'Ein effizienter Trümmersammler für ganz Andromeda.', 'Der Sternenkönig Asterios hat die Galaxie mit diesem Raumschiff gesegnet, um im Kampf verlorene Rohstoffe im All wiederzugewinnen. Er hat die Völker der Galaxie dazu aufgefordert, die entstandenen Trümmerfelder um alle Planeten herum wieder wegzuräumen, damit die Galaxie frei von Weltraumschrott ist. Der Sammler ist in der Lage, mit einer Start- und Landezeit von 25 Minuten und einer passablen Geschwindigkeit auch weit entfernte Trümmerfelder in einer annehmbaren Zeit zu sammeln und dabei die Rohstoffe auf die mitgeschickten Transporter zu laden.', 3200, 1200, 2500, 2000, 0, 0, 0, 4, 1, 1, 0, 11000, 0, 1, 3300, 450, 300, 1, 1, 3, 'transport,collectdebris,position,attack,flight,support,alliance', 0.33, 0, 50, 2, 1, 0, 1, 0, 8, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 8.900, 0, 0, 1),
	(51, 'HAARP Spionagesonde', 1, 'Diese Sonde ist die Weiterentwicklung der ZAVIJAH Spionagesonde.', 'Diese Sonde ist die Weiterentwicklung der ZAVIJAH Spionagesonde. Sie ist enorm schnell und gut geeignet für das Ausspionieren weit entfernter Galaxien. Zudem kann sie genutzt werden, um sie in eine gegnerische Flotte reinzujagen, sodass der Kampfbericht einem die Werte der gegnerischen Flotte verrät. ', 1000, 1000, 1000, 500, 0, 0, 0, 0, 1, 0, 0, 5, 0, 0, 60000, 5, 4, 1, 1, 4, 'position,attack,spy,flight,support,alliance', 0.33, 0, 0, 1, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 3.500, 0, 0, 0),
	(52, 'AURORA Sonde', 1, 'Diese Sonde wurde entwickelt, um Rohstoffe innert kurzer Zeit von einem Ort zum anderen zu transportieren. ', 'Diese Sonde wurde entwickelt, um Rohstoffe innert kurzer Zeit von einem Ort zum anderen zu transportieren.  Deshalb hat diese sehr schwache und teure Sonde einen grossen Laderaum, in dem sie den Treibstoff und andere Rohstoffe für die mitfliegenden Schiffe bereit halten kann. Sie besteht praktisch nur aus dünnbeschichteten Tanks und dem notwendigen Antrieb. \r\nVor kurzem bekam die Sonde eine Generalüberholung, was ihr nun höhere Geschwindigkeiten und einen etwas grösseren Laderaum ermöglichte.', 20000, 18000, 10000, 9000, 0, 0, 0, 8, 5, 3, 0, 90000, 0, 0, 36300, 25, 15, 1, 1, 2, 'transport,fetch,position,attack,flight,support,alliance', 0.00, 0, 1, 1, 1, 0, 1, 0, 2, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 57.000, 0, 0, 1),
	(53, 'IMPERIALER Kreuzer', 1, 'Der Imperiale Kreuzer gehört zu den grösseren Schiffe in Andromeda und ist ein Kampfschiff mit akzeptablem Kosten-Nutzen-Verhältnis.', 'Dies ist eines der besseren Kampfschiffe in Andromeda. Es ist enorm stark gepanzert, hat allerdings einen relativ schwachen Schild. Seine Waffen sind nicht zu verachten. Es ist das grösste Schiff, das Sonnensegel zur Antriebsunterstützung nutzt. \r\nVor allem die Minbari und die Vorgonen stellen dieses Schiff zu Tausenden her, da die Start- und Landezeit für sie kein Problem darstellt.', 750000, 600000, 415000, 365000, 0, 0, 0, 23, 395, 280, 0, 230000, 0, 35, 5500, 587, 351, 1, 1, 6, 'transport,position,attack,flight,support,alliance', 0.33, 0, 355000, 135000, 401500, 0, 1, 0, 1, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 2130.000, 0, 0, 0),
	(54, 'Alien-Jäger', 1, 'Niemand weiss genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.', 'Niemand weiss genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.', 0, 0, 99999999, 0, 0, 0, 0, 1, 0, 0, 0, 1000, 0, 1, 5000, 0, 0, 0, 0, 0, 'flight', 0.33, 0, 500, 700, 50, 0, 1, 0, 5, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 99999.999, 0, 0, 0),
	(55, 'Alien-Kampschiff', 1, 'Kriegsbeute aus dem Kampf gegen die Aliens.', 'Niemand weiss genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.', 0, 0, 99999999, 0, 0, 0, 0, 1, 0, 0, 0, 1000000, 0, 1, 150000, 0, 0, 0, 0, 0, 'transport,fetch,collectdebris,position,attack,collectmetal,collectcrystal,collectfuel,analyze,explore,flight,support,alliance', 0.33, 0, 50000000, 70000000, 50000000, 0, 1, 0, 5, 0, 1, 0, 0, 350, 2.00, 0.03, 0.03, 0.03, 0.00, 0.03, 0.00, 0.10, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 99999.999, 99, 16777215, 1),
	(56, 'Alien-Mutterschiff', 1, 'Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.', 'Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.', 0, 0, 99999999, 0, 0, 0, 0, 1, 0, 0, 0, 10000, 0, 1, 5000, 0, 0, 0, 0, 0, 'flight', 0.33, 0, 50000, 70000, 5000, 0, 1, 0, 5, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 99999.999, 0, 0, 1),
	(57, 'ANDROMEDA Mysticum', 1, 'Ein einmaliges Schiff mit speziellen Fähigkeiten.', 'Ein einmaliges Schiff mit speziellen Fähigkeiten.', 58000, 67000, 43600, 37500, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 30000, 0, 0, 0, 1, 0, 'position,flight', 0.33, 0, 5000, 5000, 0, 0, 1, 0, 3, 0, 1, 1, 0, 350, 2.00, 0.03, 0.03, 0.03, 0.00, 0.03, 0.00, 0.10, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 206.100, 0, 0, 0),
	(59, 'MINBARI Mysticum', 1, 'Das Spezialschiff für die Minbari.', 'Das Spezialschiff für die Minbari.', 670000, 500000, 350000, 480000, 100000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 30000, 0, 0, 0, 1, 0, 'position,flight', 0.33, 0, 110000, 86000, 0, 5, 1, 0, 3, 0, 1, 1, 0, 180, 1.70, 0.01, 0.02, 0.01, 0.00, 0.00, 0.00, 0.05, 0.04, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 2100.000, 0, 0, 0),
	(60, 'ANDORIA Mysticum', 1, 'Das Spezialschiff für die Andorianer.', 'Das Spezialschiff für die Andorianer.', 670000, 500000, 350000, 480000, 100000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 30000, 0, 0, 0, 1, 0, 'position,flight', 0.33, 0, 110000, 86000, 0, 2, 1, 0, 3, 0, 1, 1, 0, 180, 1.70, 0.02, 0.02, 0.02, 0.00, 0.00, 0.08, 0.05, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 2100.000, 0, 0, 0),
	(61, 'CARDASSIA Mysticum', 1, 'Das Spezialschiff für die Cardassianer.', 'Das Spezialschiff für die Cardassianer.', 670000, 500000, 350000, 480000, 100000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 30000, 0, 0, 0, 1, 0, 'position,flight', 0.33, 0, 110000, 86000, 0, 9, 1, 0, 3, 0, 1, 1, 0, 180, 1.70, 0.02, 0.02, 0.02, 0.05, 0.00, 0.00, 0.06, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 2100.000, 0, 0, 0),
	(62, 'CENTAURI Mysticum', 1, 'Das Spezialschiff für die Centauri.', 'Das Spezialschiff für die Centauri.', 670000, 500000, 350000, 480000, 100000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 30000, 0, 0, 0, 1, 0, 'position,flight', 0.33, 0, 110000, 86000, 0, 8, 1, 0, 3, 0, 1, 1, 0, 180, 1.70, 0.03, 0.03, 0.03, 0.00, 0.10, 0.00, 0.05, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 2100.000, 0, 0, 0),
	(63, 'FERENGI Mysticum', 1, 'Das Spezialschiff für die Ferengi.', 'Das Spezialschiff für die Ferengi.', 670000, 500000, 350000, 480000, 100000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 30000, 0, 0, 0, 1, 0, 'position,flight', 0.33, 0, 110000, 86000, 0, 6, 1, 0, 3, 0, 1, 1, 0, 180, 1.70, 0.01, 0.05, 0.02, 0.00, 0.07, 0.00, 0.05, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 2100.000, 0, 0, 0),
	(64, 'ORION Mysticum', 1, 'Das Spezialschiff für den Orioner.', 'Das Spezialschiff für den Orioner.', 670000, 500000, 350000, 480000, 100000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 30000, 0, 0, 0, 1, 0, 'position,flight', 0.33, 0, 110000, 86000, 0, 4, 1, 0, 3, 0, 1, 1, 0, 180, 1.70, 0.02, 0.01, 0.01, 0.00, 0.00, 0.16, 0.05, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 2100.000, 0, 0, 0),
	(65, 'RIGELIA Mysticum', 1, 'Das Spezialschiff für die Rigelianer.', 'Das Spezialschiff für die Rigelianer.', 670000, 500000, 350000, 480000, 100000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 30000, 0, 0, 0, 1, 0, 'position,flight', 0.33, 0, 110000, 86000, 0, 3, 1, 0, 3, 0, 1, 1, 0, 180, 1.70, 0.04, 0.01, 0.01, 0.00, 0.05, 0.00, 0.05, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 2100.000, 0, 0, 0),
	(66, 'TERRANIA Mysticum', 1, 'Das Spezialschiff für die Terraner.', 'Das Spezialschiff für die Terraner.', 670000, 500000, 350000, 480000, 100000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 30000, 0, 0, 0, 1, 0, 'position,flight', 0.33, 0, 110000, 86000, 0, 1, 1, 0, 3, 0, 1, 1, 0, 180, 1.70, 0.02, 0.02, 0.02, 0.00, 0.05, 0.00, 0.15, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 2100.000, 0, 0, 0),
	(67, 'VORGONIA Mysticum', 1, 'Das Spezialschiff für die Vorgonen.', 'Das Spezialschiff für die Vorgonen.', 670000, 500000, 350000, 480000, 100000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 30000, 0, 0, 0, 1, 0, 'position,flight', 0.33, 0, 110000, 86000, 0, 7, 1, 0, 3, 0, 1, 1, 0, 180, 1.70, 0.01, 0.02, 0.02, 0.00, 0.00, 0.00, 0.05, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.03, 2100.000, 0, 0, 0),
	(68, 'ENERGIJA Solarsatellit', 1, 'Ein Satellit, der im Orbit schwebt und durch Solarpanels Energie gewinnt, welche dann auf dem Planeten verwendet werden kann.', 'Da einige (neu entwickelte) Gebäude enorme Energiemengen verschlingen, wurde der Solarsatellit entwickelt. Diese Sonde wird im Orbit stationiert und erzeugt Energie mit Hilfe der Sonne. Die Energieausbeute pro Solarsatellit ist jedoch abhängig von der jeweiligen Planetentemperatur und der jeweiligen Entfernung zur Sonne. Je näher der Planet der Sonne war, umso mehr Energie konnte der Satellit produzieren.', 300, 1500, 100, 100, 0, 0, 0, 0, 0, 0, 300, 0, 0, 0, 0, 0, 0, 1, 1, 13, 'flight', 0.33, 0, 100, 50, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 2.000, 0, 0, 1),
	(69, 'TEREBELLUM Analysator', 1, 'Diese kleine Sonde wurde dafür geschaffen, um Staub- und Gasvorkommen im All zu analysieren und festzustellen, ob sich deren Abbau lohnt.', 'Nachdem verschiedene Schiffe entwickelt wurden, um von Asteroidenfeldern, Sternennebel und Gasplaneten Rohstoffe zu gewinnen, wollte man den Prozess optimieren. Dazu wurde eine kleine Sonde entwickelt, die in kurzer Zeit zu verschiedenen Vorkommen geschickt werden konnte, um diese zu analysieren und festzustellen, ob sich deren Abbau lohnt. Besonders bei Gasplaneten war diese Analyse wichtig, da das Eintreffen einer solchen Sonde verschiedene chemische Prozesse auslöste, welche die Tritiumherstellung beschleunigte.', 2000, 4500, 3000, 3000, 0, 0, 0, 1, 25, 1, 0, 500, 0, 0, 70000, 10, 1, 1, 1, 1, 'position,analyze,flight,support', 0.33, 0, 100, 200, 1, 0, 1, 0, 2, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 12.500, 0, 0, 1),
	(70, 'LORIAL Transportschiff', 1, 'Dieser Transporter der Serrakin kann extrem viel transportieren und verbraucht wenig Sprit, ist dafür aber auch ziemlich langsam.', 'Dieser Transporter der Serrakin kann extrem viel transportieren und verbraucht wenig Sprit, ist dafür aber auch ziemlich langsam.', 15000, 10000, 5000, 5000, 0, 0, 0, 5, 25, 5, 0, 600000, 0, 1, 3630, 360, 300, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', 0.33, 0, 200, 500, 45, 10, 1, 0, 7, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 35.000, 0, 0, 1),
	(71, 'AURIGA Explorer', 1, 'Dient zur Erkundung der unbekannten Weiten der Galaxie.', 'Als das Zeitalter der Dunkelheit über die Andromeda Galaxie kam, fielen sämtliche Sensoren der Planeten aus. Niemand wusste mehr, wo die nächsten Planeten zum Besiedeln waren. \r\nSchnell musste eine Lösung her. Somit entwickelten Forscher den Auriga Explorer, der in der Lage war, die  Galaxiekarte von der Dunkelheit zu befreien. Die Sensoren konnten im Koordinatensystem ein Feld von 5x5 Feldern aufdecken, um das vom Auriga Explorer erkundete Feld herum.', 1000, 800, 0, 0, 0, 0, 0, 1, 3, 0, 0, 200, 0, 0, 3630, 10, 0, 1, 1, 6, 'position,explore,flight,support', 0.33, 0, 50, 20, 0, 0, 1, 0, 2, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1.800, 0, 0, 1),
	(72, 'SERRAKIN Mysticum', 1, 'Das Spezialschiff für die Serrakin', 'Das Spezialschiff für die Serrakin', 670000, 500000, 350000, 480000, 100000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 30000, 0, 0, 0, 1, 0, 'position,flight', 0.33, 0, 110000, 86000, 0, 10, 1, 0, 3, 0, 1, 1, 0, 180, 1.70, 0.02, 0.02, 0.05, 0.02, 0.00, 0.00, 0.05, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 2100.000, 0, 0, 0),
	(73, 'SUPRANALIS Jäger', 1, 'Weiterentwicklung des ANTARES Jäger.', 'Parallel zu den Antares Jägern wurde der STARLIGHT Jäger für Allianzmitglieder entwickelt, welcher besser gepanzert war und auch die bessere Bewaffnung aufwies. Er nutzte auch einen neuartigen Antrieb, welcher aber noch nicht ganz serienreif war, da er andauernd ausfiel, und selten wie geplant lief. Nach einigen Untersuchungen fand man heraus, dass dies daran lag, dass beim Bau des Motors billiges Material verwendet wurde. Das stellte den viel gelobten Jäger in ein anderes Licht, aber andererseits erwies er sich in Raumschlachten als zuverlässiger Mitstreiter.', 2450000, 1700000, 1050000, 1200000, 0, 0, 0, 1, 3, 3, 0, 800, 0, 1, 9750, 22, 20, 1, 0, 2, 'transport,position,attack,flight,support,alliance', 0.33, 0, 1050000, 500000, 855000, 0, 1, 0, 6, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 6400.000, 1, 2500, 1),
	(74, 'SUPRANALIS Bomber', 1, 'Dieses Raumschiff ist sehr effektiv gegen gegnerische Verteidigungsanlagen.', 'Trotz allen Erfolgen, die die Hadar Schlachtschiffe bei der Zerstörung gegnerischer Flotten und Verteidigung erzielten, war man damit noch nicht zufrieden. Deshalb konstruierte man ein neues, bis an die Zähne bewaffnetes Schiff, den Pollux Bomber für Allianzmitglieder. Nachdem man das Schiff mit Waffen beladen hatte, erwies es sich, dass deshalb die Angriffsgeschwindigkeit eingeschränkt wurde. Wegen diesem Nachteil konnte der Bomber sich in grossen Flotten nicht etablieren, er ist aber trotzdem in allem eine nicht zu unterschätzende Waffe, welche grosse Zerstörung anrichten kann.', 48500000, 10500000, 4250000, 5750000, 0, 0, 0, 275, 400, 350, 0, 2000, 0, 2, 2400, 300, 60, 1, 0, 5, 'transport,position,attack,flight,support,alliance', 0.33, 0, 1300000, 250000, 8100000, 0, 1, 0, 6, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 69000.000, 4, 8000, 1),
	(76, 'SUPRANALIS Dreadnought', 1, 'Dieses Schiff ist eine riesige fliegende Festung. ', 'Aus der Erfahrungen, die man mit den Hadar und den Pollux gewonnen hatte, wurde ein neues Superschiff kreiert, der Rigel Dreadnought für Allianzmitglieder. Optimierungen in der Herstellung und bei den Antrieben verliehen dem Schiff eine aussergewöhnliche Kampfkraft, Effizienz und Geschwindigkeit zu erstaunlich niedrigen Preisen. Zusätzlich erhöhte man die Transportkapazität, so dass die Rigel eigenständig praktisch aus dem Nichts heraus Raubzüge unternehmen können, ohne sich mit langsamen Transportern zu belasten. ', 33500000, 29750000, 7500000, 17500000, 0, 0, 0, 1400, 11750, 17000, 0, 600000, 0, 560, 9600, 310, 200, 1, 0, 9, 'transport,position,attack,flight,support,alliance', 0.33, 0, 10000000, 13500000, 15750000, 0, 1, 0, 6, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 88250.000, 5, 25000, 1),
	(77, 'SUPRANALIS Kampfstern', 1, 'Dieses Schiff ist das mächtigste Schiff der Galaxien.', 'Ein verrückter Wissenschaftler war von der Idee besessen, ein Kampfschiff für Allianzmitglieder zu bauen, welches so gross wie ein ganzer Trabant wäre. Er wurde so lange ausgelacht, bis er einen anderen Verrückten traf, der zufällig nebenberuflich Imperator war und der ihn unterstützte. Danach wurde dieser Wissenschaftler allgemein als Genius bekannt, welcher die ultimative Waffe erschaffen hatte: den Andromeda Kampfstern. Seine Waffen und Schilder sind bis heute noch unübertroffen!\r\nDer einzige Nachteil dieses monströsen Kampfschiffes ist nur, dass es wegen seiner Masse lange Start- und Landezeiten hat, und eine zahlreiche Besatzung benötigt wird.', 110000000, 55000000, 66000000, 66000000, 0, 0, 0, 4000, 40000, 20000, 0, 6000000, 0, 990, 20000, 1750, 1250, 1, 0, 13, 'transport,position,attack,flight,support,alliance', 0.33, 0, 46750000, 49500000, 47025000, 0, 1, 0, 6, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 297000.000, 7, 75000, 1),
	(78, 'SUPRANALIS Ultra', 1, 'Dieses Schiff ist das mächtigste Schiff der Galaxien \r\n(nun aber wirklich ^^)', 'Der Andromeda Kampfstern galt lange als DAS Kampfschiff schlechthin und nicht wenige behaupten, dass es nicht möglich sei, seine Grösse und Stärke zu übertreffen, doch genau dieses Ziel hatten diverse Imperatoren einer mächtigen Allianz Namens "Supranalis Ultra".\r\nNach vielen Jahren, unzähligen Arbeitsstunden und diversen Todesopfern war der Prototyp dieses Superschiffs fertig.\r\nEtwas noch nie Dagewesenes wurde erschaffen um die Kontrolle eines ganzen Universums an sich zu reissen...', 1000000000, 500000000, 600000000, 600000000, 0, 0, 0, 5000, 50000, 50000, 0, 50000000, 0, 100000, 20000, 5000, 3000, 1, 0, 14, 'transport,position,attack,flight,support,alliance', 0.33, 0, 500000000, 500000000, 450000000, 0, 1, 0, 6, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 2700000.000, 10, 356342, 1),
	(79, 'SCORPIUS ZIBAL Trägerschiff', 1, 'Transportiert mobile Verteidigungsanlagen.', 'Transportiert mobile Verteidigungsanlagen.', 3900, 3100, 2100, 1500, 0, 0, 0, 5, 5, 5, 0, 1000, 0, 0, 11000, 60, 60, 1, 1, 14, 'position', 0.33, 0, 1, 1, 1, 10, 1, 0, 4, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 10.600, 0, 0, 0),
	(80, 'SCORPIUS SPICA Trägerschiff', 1, 'Transportiert mobile Verteidigungsanlagen.', 'Transportiert mobile Verteidigungsanlagen.', 800, 475, 0, 425, 0, 0, 0, 5, 5, 5, 0, 1000, 0, 0, 11000, 60, 60, 1, 1, 14, 'position', 0.33, 0, 1, 1, 1, 10, 1, 0, 4, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1.700, 0, 0, 0),
	(81, 'SCORPIUS POLARIS Trägerschiff', 1, 'Transportiert mobile Verteidigungsanlagen.', 'Transportiert mobile Verteidigungsanlagen.', 1000, 700, 300, 500, 0, 0, 0, 5, 5, 5, 0, 1000, 0, 0, 11000, 60, 60, 1, 1, 14, 'position', 0.33, 0, 1, 1, 1, 10, 1, 0, 4, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 2.500, 0, 0, 0),
	(82, 'SCORPIUS PHOENIX Trägerschiff', 1, 'Transportiert mobile Verteidigungsanlagen.', 'Transportiert mobile Verteidigungsanlagen.', 6000, 3000, 3000, 2900, 0, 0, 0, 5, 5, 5, 0, 1000, 0, 0, 11000, 60, 60, 1, 1, 14, 'position', 0.33, 0, 1, 1, 1, 10, 1, 0, 4, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 14.900, 0, 0, 0),
	(85, 'KRISTNAS Explorer', 1, 'Aus dem Weihnachtsschlitten mit Rentieren als Antrieb entwickelten die Nachfahren der Elfen den KRISTNAS Explorer. Umhertreibende Ressourcen verstaut dieser wie der Weihnachtsmann in seinem Sack. Fröhliche Weihnachten!', 'Aus dem Weihnachtsschlitten mit Rentieren als Antrieb entwickelten die Nachfahren der Elfen den KRISTNAS Explorer. Umhertreibende Ressourcen verstaut dieser wie der Weihnachtsmann in seinem Sack. Fröhliche Weihnachten!', 0, 0, 0, 0, 0, 0, 0, 35, 70, 10, 0, 500000, 0, 1, 30000, 300, 150, 0, 0, 0, 'transport,fetch,collectdebris,position,attack,collectmetal,collectcrystal,collectfuel,analyze,explore,flight,support,alliance', 0.33, 0, 10000, 6000, 100, 0, 1, 0, 3, 0, 1, 1, 1, 1, 10.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 0, 0, 0),
	(91, 'DAEDALUS Jäger', 1, 'Kampfschiff der Terraner.', 'Die Terraner haderten schon lange mit ihrer Effektivität im Kampf. Ihre Zerstörer waren im Schwarm zwar in der Lage, sämtlichen Schiffen gefährlich zu werden, allerdings brachten sie durch ihre geringe Struktur stets erhebliche Verluste mit sich. Um dem entgegen zu wirken, entwickelten sie ein stark gepanzertes Schiff, den Daedalus Jäger.', 13900, 7000, 7000, 7000, 0, 0, 0, 1, 10, 5, 0, 12000, 0, 3, 5720, 495, 270, 1, 1, 0, 'transport,position,attack,flight,support,alliance', 0.33, 0, 8000, 5000, 2000, 1, 1, 0, 4, 1, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 34.900, 0, 0, 0),
	(94, 'SIRRAH Schlachter', 1, 'Grosses Kampfschiff der Orioner.', 'Die Orioner waren schon immer eine gefürchtete Rasse durch ihre schnellen Angriffe. Dies hat den Orionern aber noch nicht gereicht. Zusätzlich zum Orion Fighter entwickelten sie ein weiteres Kriegsschiff, den Sirrah Schlachter. Trotz seiner enormen Grösse hat der Sirrah Schlachter eine hohe Geschwindigkeit und eine kurze Startzeit, was ihn zu einer grossen Bedrohung macht.', 275000, 190000, 160000, 90000, 0, 0, 0, 20, 98, 128, 0, 150000, 0, 25, 7700, 525, 371, 1, 1, 0, 'transport,position,attack,flight,support,alliance', 0.33, 0, 125000, 80500, 108000, 4, 1, 0, 4, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 715.000, 0, 0, 0),
	(95, 'GOMEISA Nebelsammler', 1, 'Nebelsammler der Vorgonen.', 'Die Vorgonen bemerkten bald, dass in Andromeda der Siliziumverbrauch durch die Gewächshäuser sowie der Flottenproduktion extrem gestiegen ist. Um sich entscheidende Vorteile in der Siliziumproduktion zu verschaffen, erfanden sie den GOMEISA Nebelsammler.', 60000, 28000, 25000, 60000, 0, 0, 0, 19, 3, 4, 0, 40000, 0, 3, 1650, 713, 1088, 1, 1, 0, 'transport,position,attack,collectcrystal,flight,support,alliance', 0.33, 0, 800, 1000, 5, 7, 1, 0, 4, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 173.000, 0, 0, 1),
	(96, 'Weihnachtskreuzer', 1, 'Beschreibung folgt...', 'Beschreibung folgt...', 0, 0, 0, 0, 0, 0, 0, 8000, 80000, 40000, 0, 60000000, 0, 990, 20000, 480, 480, 0, 0, 13, 'position,attack,flight', 0.33, 0, 850000000, 900000000, 850000000, 0, 1, 0, 5, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 0, 16777215, 1),
	(97, 'AIN Sonde', 1, 'Diese kleine Sonde wurde für den schnellen Transport von kleineren Mengen Ressourcen entwickelt.', 'Beim Aufbau neuer Imperien war der Ressourcenfluss meist das größte Problem aller Völker. Die AURORA Sonde wäre für dieses Zeitalter das beste Schiff, doch zu hoch entwickelt. So erfanden die hoch intelligenten Centauri ein besseres Schiff - die Artifical Intelligence Neurologic - kurz AIN - Sonde. Durch eine extrem leichte Bauweise, der Verzicht von Piloten und einem intelligenten automatischen internen Logistiksystem fanden große Lagerräume und schnelle Antriebe Platz.', 15000, 7500, 5000, 10000, 0, 0, 0, 1, 1, 1, 0, 75000, 0, 0, 48400, 20, 10, 1, 1, 0, 'transport,fetch,position,flight', 0.00, 0, 1, 1, 1, 8, 1, 0, 7, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 37.500, 0, 0, 1),
	(98, 'AUREA wawa', 1, 'Ein goldenes Schiff, das deine Flotte verschönert und eine echte Errungenschaft ist.', 'Das Aurea wawa kann man nicht bauen, man kann es sich nur verdienen. Es ist ein wunderschönes goldenes Schiff, ähnlich einem Mysticum, nur viel schöner. Wenn du ein solches Schiff besitzt, darfst du immer stolz auf die Sternenzeit zurückblicken, als du die beste Forschung besessen hattest.rnDas Schiff ist nach wawa benannt, ein grossartiger Herrscher, der in der Galaxie der Schnelligkeit der mächtigste war.', 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 0, 1000, 0, 1, 10000, 600, 600, 0, 0, 0, 'position,attack,flight', 0.37, 0, 1, 0, 0, 0, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 0, 0, 1),
	(99, 'AUREA Chupacabra', 1, 'Ein goldenes Schiff, das deine Flotte verschönert und eine echte Errungenschaft ist.', 'Das Aurea Chupacabra kann man nicht bauen, man kann es sich nur verdienen. Es ist ein wunderschönes goldenes Schiff, ähnlich einem Mysticum, nur viel schöner. Wenn du ein solches Schiff besitzt, darfst du immer stolz auf die Sternenzeit zurückblicken, als du die höchste Kampferfahrung besessen hast.rnDas Schiff wurde Chupacabra zu Ehren nach ihm benannt, um an die glorreiche Zeit der Erfahrungsjagd in der schnellsten Runde zu erinnern.', 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 0, 1000, 0, 1, 10000, 600, 600, 0, 0, 0, 'position,attack,flight', 0.33, 0, 1, 0, 0, 0, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 0, 0, 1),
	(100, 'SCORPIUS OMEGA Trägerschiff', 1, 'Transportiert mobile Verteidigungsanlagen.', 'Transportiert mobile Verteidigungsanlagen.', 650000, 425000, 265000, 425000, 0, 0, 0, 5, 5, 5, 0, 1000, 0, 0, 11000, 60, 60, 1, 1, 14, 'position', 0.33, 0, 7500, 1, 1, 10, 1, 0, 4, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1765.000, 0, 0, 0),
	(101, 'AMYNA Drohne', 1, 'Eine verteidigungsbasierte Kampfdrohne.', 'Die Rigelianer waren extrem stolz auf ihre HELIOS Drohne, welche aufgrund ihrer Waffenstärke im ganzen Universum gefürchtet wurde. Sie hatten jedoch Probleme mit der Verteidigung. Deshalb entwickelten sie eine modifizierte Version der HELIOS Drohne. Die Geschütztürme wurden durch Schild- und Strukturgeneratoren ersetzt. Es entstand die AMYNA Drohne.', 4750, 2000, 1750, 3500, 0, 0, 0, 1, 3, 3, 0, 5000, 0, 1, 4750, 200, 200, 1, 1, 0, 'transport,position,attack,flight,support,alliance', 0.33, 0, 3400, 1700, 1, 3, 1, 0, 4, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 12.000, 0, 0, 0),
	(102, 'PUMPKIN Explorer', 1, 'Ist es ein Vogel?\r\nIst es ein Flugzeug?\r\nNein! Es ist ein fliegender Kürbis!\r\nWer kam bloß auf diese verrückte Idee?', '', 0, 0, 0, 0, 0, 0, 0, 35, 70, 10, 0, 40000, 0, 1, 22000, 300, 150, 0, 0, 0, 'transport,fetch,collectdebris,position,attack,collectmetal,collectcrystal,collectfuel,analyze,explore,flight,support,alliance', 0.33, 0, 7000, 5500, 100, 0, 1, 0, 3, 0, 0, 1, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 0, 16777215, 1),
	(104, 'AUREA Andoria', 1, 'Goldenes Schiff der Andorianer', 'Eines von nur zwei existierenden goldenen Schiffe der Andorianer. Das Aurea Andoria wird an einen aussergewöhnlichen Imperator verliehen, dessen Reich alle anderen überragt.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 2, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(105, 'AUREA Andreia', 1, 'Goldenes Schiff der Andorianer', 'Eines von nur zwei existierenden goldenen Schiffe der Andorianer. Das Aurea Andreia wird an einen aussergewöhnlichen Imperator verliehen, der in seinen Schlachten am siegreichsten war.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 2, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(106, 'AUREA Cardassia', 1, 'Goldenes Schiff der Cardassianer', 'Eines von nur zwei existierenden goldenen Schiffe der Cardassianer. Das Aurea Cardassia wird an einen aussergewöhnlichen Imperator verliehen, dessen Reich alle anderen überragt.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 9, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(107, 'AUREA Centauri', 1, 'Goldenes Schiff der Centauri', 'Eines von nur zwei existierenden goldenen Schiffe der Centauri. Das Aurea Centauri wird an einen aussergewöhnlichen Imperator verliehen, dessen Reich alle anderen überragt.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 8, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(108, 'AUREA Ferengi', 1, 'Goldenes Schiff der Ferengi', 'Eines von nur zwei existierenden goldenen Schiffe der Ferengi. Das Aurea Ferengi wird an einen aussergewöhnlichen Imperator verliehen, dessen Reich alle anderen überragt.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 6, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(109, 'AUREA Minbari', 1, 'Goldenes Schiff der Minbari', 'Eines von nur zwei existierenden goldenen Schiffe der Minbari. Das Aurea Minbari wird an einen aussergewöhnlichen Imperator verliehen, dessen Reich alle anderen überragt.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 5, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(110, 'AUREA Orion', 1, 'Goldenes Schiff der Orioner', 'Eines von nur zwei existierenden goldenen Schiffe der Orioner. Das Aurea Orion wird an einen aussergewöhnlichen Imperator verliehen, dessen Reich alle anderen überragt.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.38, 0, 1, 1, 1, 4, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(111, 'AUREA Rigelia', 1, 'Goldenes Schiff der Rigelianer', 'Eines von nur zwei existierenden goldenen Schiffe der Rigelianer. Das Aurea Rigelia wird an einen aussergewöhnlichen Imperator verliehen, dessen Reich alle anderen überragt.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 3, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 0),
	(112, 'AUREA Serrakin', 1, 'Goldenes Schiff der Serrakin', 'Das einzige reine goldene Schiff der Serrakin. Das Aurea Serrakin wird an einen aussergewöhnlichen Imperator verliehen, dessen Reich alle anderen überragt.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 10, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(113, 'AUREA Terrania', 1, 'Goldenes Schiff der Terraner', 'Eines von nur zwei existierenden goldenen Schiffe der Terraner. Das Aurea Terrania wird an einen aussergewöhnlichen Imperator verliehen, dessen Reich alle anderen überragt.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 1, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(114, 'AUREA Vorgonia', 1, 'Goldenes Schiff der Vorgonen', 'Eines von nur zwei existierenden goldenen Schiffe der Vorgonen. Das Aurea Vorgonia wird an einen aussergewöhnlichen Imperator verliehen, dessen Reich alle anderen überragt.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 7, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(115, 'AUREA Nilam', 1, 'Goldenes Schiff der Cardassianer', 'Eines von nur zwei existierenden goldenen Schiffe der Cardassianer. Das Aurea Nilam wird an einen aussergewöhnlichen Imperator verliehen, der in seinen Schlachten am siegreichsten war.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 9, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(116, 'AUREA Europa', 1, 'Goldenes Schiff der Centauri', 'Eines von nur zwei existierenden goldenen Schiffe der Centauri. Das Aurea Europa wird an einen aussergewöhnlichen Imperator verliehen, der in seinen Schlachten am siegreichsten war.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 8, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(117, 'AUREA Bell', 1, 'Goldenes Schiff der Ferengi', 'Eines von nur zwei existierenden goldenen Schiffe der Ferengi. Das Aurea Bell wird an einen aussergewöhnlichen Imperator verliehen, der in seinen Schlachten am siegreichsten war.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 6, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(118, 'AUREA Wezea', 1, 'Goldenes Schiff der Minbari', 'Eines von nur zwei existierenden goldenen Schiffe der Minbari. Das Aurea Wezea wird an einen aussergewöhnlichen Imperator verliehen, der in seinen Schlachten am siegreichsten war.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 5, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(119, 'AUREA Sirrah', 1, 'Goldenes Schiff der Orioner', 'Eines von nur zwei existierenden goldenen Schiffe der Orioner. Das Aurea Sirrah wird an einen aussergewöhnlichen Imperator verliehen, der in seinen Schlachten am siegreichsten war.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 4, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(120, 'AUREA Helios', 1, 'Goldenes Schiff der Rigelianer', 'Eines von nur zwei existierenden goldenen Schiffe der Rigelianer. Das Aurea Helios wird an einen aussergewöhnlichen Imperator verliehen, der in seinen Schlachten am siegreichsten war.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 3, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(121, 'AUREA Scorpius', 1, 'Goldenes Transportschiff der Serrakin', 'Die mobile Version der goldenen Verteidigungsanlage der Serrakin. Das Aurea Scorpius wird an einen aussergewöhnlichen Imperator verliehen, der in seinen Schlachten am siegreichsten war.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 10, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(122, 'AUREA Daedalus', 1, 'Goldenes Schiff der Terraner', 'Eines von nur zwei existierenden goldenen Schiffe der Terraner. Das Aurea Daedalus wird an einen aussergewöhnlichen Imperator verliehen, der in seinen Schlachten am siegreichsten war.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 1, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(123, 'AUREA Ikaros', 1, 'Goldenes Schiff der Vorgonen', 'Eines von nur zwei existierenden goldenen Schiffe der Vorgonen. Das Aurea Ikaros wird an einen aussergewöhnlichen Imperator verliehen, der in seinen Schlachten am siegreichsten war.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1000, 0, 0, 50000, 0, 0, 0, 0, 0, 'position,flight,support', 0.33, 0, 1, 1, 1, 7, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 99, 9999999, 1),
	(124, 'EASTER Hunter', 1, 'Der EASTER Hunter wurde von Osterhasen entwickelt, die es satt hatten, Eier zu verstecken und gejagt zu werden. Mit diesem Schiff gehen sie selbst auf die Jagd.', 'Der EASTER Hunter wurde von Osterhasen entwickelt, die es satt hatten, Eier zu verstecken und gejagt zu werden. Mit diesem Schiff gehen sie selbst auf die Jagd.', 0, 0, 0, 0, 10, 0, 0, 35, 70, 10, 0, 500000, 0, 1, 30000, 300, 150, 0, 0, 0, 'transport,fetch,collectdebris,position,attack,collectmetal,collectcrystal,collectfuel,analyze,explore,flight,support,alliance', 0.33, 0, 10000, 6000, 100, 0, 1, 0, 6, 0, 0, 1, 1, 1, 10.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.010, 0, 16777215, 0),
	(125, 'ARAKNA Albtraumjäger', 1, 'Furcht hat einen Namen: ARAKNA!', 'Allen Bemühungen zum Trotz taucht eine lange vergrabene Technologie wieder auf. Selbständig zur hässlichen Fratze des ARAKNA Albtraumjägers gewandelt, macht sie Jagd auf ihresgleichen.', 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 10, 0, 0, 13000, 13, 13, 0, 0, 0, 'transport,fetch,position,attack,spyattack,flight,support,alliance', 0.00, 0, 31000, 10000, 21000, 0, 1, 0, 2, 0, 0, 1, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 0, 0, 0),
	(132, 'SUPRANALIS Spioschiff', 1, 'Kann von einem anderen Spieler eine Technologie klauen.', 'Die galaktischen Hochprofessoren waren äusserst stolz darauf, dass sie die höchsten Technologien aller Völker besassen. Entsprechend gross war der Neid, als sie von einem andern Volk in einer von ihnen vernachlässigten Technologie übertrumpft wurden. Also erfanden sie dieses Spionageschiff, mit dessen Hilfe sie den anderen Völkern etwaige höher entwickelte Technologien klauen können.', 32500000, 25000000, 25000000, 20000000, 0, 0, 0, 500, 800, 800, 0, 95000, 0, 20, 5000, 1800, 1800, 1, 0, 0, 'position,spyattack,flight,support', 0.33, 0, 50000, 50000, 25000, 0, 1, 0, 6, 0, 0, 0, 0, 0, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 102500.000, 5, 35000, 1),
	(134, 'EGG-0A0D', 1, 'Das EGG-0A0D ist das ultimative Raumschiff der Easter EGG Cooperation, mit einem revolutionären Reaktor, der alle Schiffsfähigkeiten aktiviert.', 'Das EGG-0A0D von der Easter EGG Cooperation ist das neueste Flaggschiff der Raumfahrttechnologie. Ausgestattet mit einem revolutionären Reaktor bietet es unerreichte Leistung und Vielseitigkeit. Mit seiner eleganten Bauweise, hochmodernen Steuerung und innovativen Funktionen ist das EGG-0A0D bereit, die Grenzen der Raumfahrt zu überschreiten und neue Horizonte zu erkunden.', 0, 0, 0, 0, 0, 0, 0, 1, 4, 2024, 0, 1042024, 0, 1, 1042024, 64, 1224, 0, 0, 0, '', 0.33, 0, 1, 1, 0, 0, 0, 0, 5, 1, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 0, 0, 0),
	(135, 'NOCTURNUS Dunklerjäger', 1, 'NOCTURNUS ein Jäger der aus der Dunkelheit kommt.', 'Das Raumschiff NOCTURNUS war einst das bestgehütete Geheimnis der Schattenflotte. Es wurde in den dunklen Tiefen des Weltraums entworfen, nach dem Vorbild einer Fledermaus – ein lautloser Schiff der Nacht. NOCTURNUS konnte lautlos durch die Leere gleiten und seine Feinde überraschen, bevor sie es bemerkten. Als der rote Riesenstern Hadrion explodierte, war es NOCTURNUS, das die letzte Rettungsmission anführte, während um es herum die Galaxie in Flammen aufging. Die Legende besagt, dass nur die furchtlosesten Piloten das Schiff beherrschen können, denn es scheint fast lebendig zu sein – als ob die Dunkelheit selbst es durch die Sterne lenkt.\r\n\r\nMaximal Level 1.', 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 3110, 0, 1, 311024, 0, 0, 0, 0, 0, 'transport,fetch,position,attack,stealthattack,flight,support,alliance', 0.33, 0, 3110, 3110, 3110, 0, 0, 0, 2, 0, 1, 1, 1, 1000, 99.99, 0.15, 0.15, 0.15, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 0, 0, 0),
	(136, 'VORATH Sammler', 1, 'VORATH ein lautloser Sammler der Leere, geschaffen, um Gasnebel zu verschlingen und die Überreste toter Asteroiden einzusammeln.', 'Eventschiff - Nicht Baubar\r\n\r\nDer VORATH ist ein Schiff, erschaffen aus dunklen, kosmischen Kräften, deren Ursprung im Nebel der Zeit verloren ging. Niemand weiß, wer sie gebaut hat oder wie ihre Systeme funktionieren. Wenn sie aktiviert wird, erwacht sie nur langsam, ihre unbekannten Mechanismen benötigen lange Bootzeiten, als müsse sich das Schiff erst an die Realität gewöhnen. Doch sobald sie vollständig erwacht ist, erreicht der VORATH eine beängstigende Geschwindigkeit und gleitet lautlos durch die Sterne, als würde sie von der Dunkelheit selbst getragen.', 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 311025, 0, 1, 311025, 3000, 3000, 1, 0, 0, 'transport,fetch,position,attack,collectmetal,collectcrystal,collectfuel,flight,support,alliance', 0.33, 0, 3110, 3110, 3110, 0, 1, 0, 2, 0, 0, 1, 1, 1000, 99.99, 0.15, 0.15, 0.15, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.000, 0, 0, 0),
	(137, 'MORVEX Sammler', 1, 'Der MORVEX Sammler ist ein hochentwickeltes Ressourcenschiff, basierend auf VORATH-Technologie, aktuell nur in begrenzter Stückzahl verfügbar.', 'Eventschiff - Nicht Baubar\r\n\r\nDer MORVEX Sammler ist das Ergebnis einer bahnbrechenden Forschungsreihe, die nach der erfolgreichen Studie moderner Sammelschiffe ihren Höhepunkt fand. Aufbauend auf den gewonnenen Erkenntnissen aus der Analyse des mystischen Schiffes VORATH, konnten bislang unerklärliche Technologien teilweise entschlüsselt und in ein kontrollierbares System überführt werden.\r\n\r\nDer MORVEX vereint diese fremdartigen Mechaniken mit bewährter Ingenieurskunst und ist speziell darauf ausgelegt, Asteroiden effizient zu bergen sowie Gas- und Nebelvorkommen gezielt abzusaugen. Seine zahlreichen Greif- und Saugsysteme arbeiten dabei in perfekter Abstimmung und ermöglichen eine bisher unerreichte Ausbeute. Trotz seiner komplexen Systeme gilt der MORVEX als Meilenstein moderner Ressourcengewinnung und als erstes Schiff, das das Wissen der VORATH für die Imperatoren nutzbar macht.\r\n\r\nDa sich die Massenproduktion dieser komplexen Technologie derzeit als äußerst schwierig erweist, wurde zunächst nur eine begrenzte Charge gefertigt und an die Imperatoren verteilt. Die verbleibenden Prototypen befinden sich weiterhin in den Händen der Forschung, um die zugrunde liegende Technologie weiter zu analysieren und künftig besser nutzbar zu machen.', 1, 1, 1, 1, 0, 0, 0, 4, 1, 1, 0, 210326, 0, 1, 2103, 2103, 2103, 0, 0, 0, 'transport,collectdebris,position,attack,collectmetal,collectcrystal,collectfuel,flight,support,alliance', 0.33, 0, 2103, 2103, 0, 0, 1, 0, 2, 0, 0, 0, 0, 0, 1.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.004, 0, 0, 0);

-- Exportiere Struktur von Tabelle etoa_test.ship_cat
DROP TABLE IF EXISTS `ship_cat`;
CREATE TABLE IF NOT EXISTS `ship_cat` (
  `cat_id` int unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(50) NOT NULL,
  `cat_order` smallint unsigned NOT NULL DEFAULT '0',
  `cat_color` char(7) NOT NULL DEFAULT '#ffffff',
  PRIMARY KEY (`cat_id`),
  KEY `cat_name` (`cat_name`),
  KEY `cat_order` (`cat_order`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.ship_cat: ~8 rows (ungefähr)
DELETE FROM `ship_cat`;
INSERT INTO `ship_cat` (`cat_id`, `cat_name`, `cat_order`, `cat_color`) VALUES
	(1, 'Kriegsschiff', 2, '#0080FF'),
	(2, 'Ziviles Schiff', 1, '#00FF00'),
	(3, 'Episches Schiff', 5, '#B048F8'),
	(4, 'Rassenspezifisches Schiff', 4, '#f00'),
	(5, 'NPC-Schiff', 7, '#F07902'),
	(6, 'Allianzschiff', 6, '#ffffff'),
	(7, 'Ziviles Rassenschiff', 3, '#ffffff'),
	(8, 'Sammlerschiffe', 1, '#ffffff');

-- Exportiere Struktur von Tabelle etoa_test.ship_queue
DROP TABLE IF EXISTS `ship_queue`;
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

-- Exportiere Daten aus Tabelle etoa_test.ship_queue: 0 rows
DELETE FROM `ship_queue`;
/*!40000 ALTER TABLE `ship_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `ship_queue` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.ship_requirements
DROP TABLE IF EXISTS `ship_requirements`;
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

-- Exportiere Daten aus Tabelle etoa_test.ship_requirements: ~294 rows (ungefähr)
DELETE FROM `ship_requirements`;
INSERT INTO `ship_requirements` (`id`, `obj_id`, `req_building_id`, `req_tech_id`, `req_level`) VALUES
	(1, 1, 9, NULL, 2),
	(2, 1, 11, NULL, 1),
	(3, 2, 9, NULL, 2),
	(4, 3, 9, NULL, 1),
	(5, 4, 9, NULL, 5),
	(6, 4, 11, NULL, 2),
	(7, 6, 9, NULL, 7),
	(8, 6, 11, NULL, 7),
	(9, 7, 9, NULL, 8),
	(10, 7, 11, NULL, 5),
	(11, 8, 9, NULL, 10),
	(12, 8, 11, NULL, 8),
	(13, 8, 8, NULL, 7),
	(14, 1, NULL, 4, 4),
	(15, 2, NULL, 4, 1),
	(16, 3, NULL, 7, 1),
	(17, 3, NULL, 4, 1),
	(18, 4, NULL, 5, 3),
	(19, 7, NULL, 5, 5),
	(20, 6, NULL, 6, 4),
	(22, 9, NULL, 4, 2),
	(25, 11, NULL, 6, 11),
	(26, 11, 9, NULL, 9),
	(27, 11, 11, NULL, 10),
	(33, 12, NULL, 3, 6),
	(34, 12, NULL, 10, 5),
	(35, 13, 9, NULL, 12),
	(36, 13, 11, NULL, 10),
	(38, 13, NULL, 6, 13),
	(42, 14, 9, NULL, 4),
	(43, 14, 11, NULL, 4),
	(46, 15, NULL, 5, 7),
	(47, 15, 9, NULL, 4),
	(49, 15, NULL, 8, 12),
	(50, 15, NULL, 3, 9),
	(51, 8, NULL, 5, 8),
	(52, 12, NULL, 4, 7),
	(53, 42, 9, NULL, 10),
	(54, 42, NULL, 16, 7),
	(55, 42, NULL, 11, 11),
	(57, 43, NULL, 20, 5),
	(58, 42, NULL, 6, 6),
	(60, 43, NULL, 16, 2),
	(64, 40, 9, NULL, 5),
	(68, 41, 9, NULL, 3),
	(70, 41, NULL, 20, 5),
	(71, 45, 9, NULL, 7),
	(72, 45, NULL, 20, 7),
	(73, 45, NULL, 16, 6),
	(78, 37, 9, NULL, 3),
	(79, 37, NULL, 16, 2),
	(80, 37, NULL, 20, 5),
	(81, 38, 9, NULL, 5),
	(82, 38, NULL, 16, 4),
	(83, 38, NULL, 6, 5),
	(84, 33, 9, NULL, 3),
	(85, 33, NULL, 16, 2),
	(86, 33, NULL, 20, 5),
	(87, 34, 9, NULL, 5),
	(88, 34, NULL, 16, 4),
	(89, 34, NULL, 5, 10),
	(90, 34, NULL, 8, 13),
	(91, 32, 9, NULL, 7),
	(92, 32, NULL, 16, 6),
	(93, 32, NULL, 20, 6),
	(94, 32, NULL, 17, 3),
	(95, 30, 9, NULL, 5),
	(96, 30, NULL, 16, 6),
	(98, 30, NULL, 5, 6),
	(99, 29, 9, NULL, 3),
	(100, 29, NULL, 16, 2),
	(101, 29, NULL, 20, 5),
	(106, 28, 9, NULL, 5),
	(107, 28, NULL, 16, 4),
	(109, 28, 11, NULL, 6),
	(113, 26, 9, NULL, 8),
	(114, 26, NULL, 16, 10),
	(117, 23, 9, NULL, 7),
	(118, 23, NULL, 16, 6),
	(119, 23, NULL, 6, 5),
	(120, 23, NULL, 7, 15),
	(121, 25, 9, NULL, 3),
	(122, 25, 11, NULL, 7),
	(123, 25, NULL, 20, 5),
	(124, 25, NULL, 16, 2),
	(125, 24, 9, NULL, 8),
	(126, 24, NULL, 16, 6),
	(127, 24, NULL, 5, 9),
	(129, 20, 9, NULL, 7),
	(130, 20, NULL, 16, 6),
	(131, 20, NULL, 6, 6),
	(133, 21, 9, NULL, 3),
	(134, 21, NULL, 16, 2),
	(135, 21, NULL, 20, 5),
	(136, 22, 9, NULL, 7),
	(137, 22, NULL, 6, 7),
	(138, 22, NULL, 10, 7),
	(139, 22, NULL, 16, 6),
	(140, 19, 9, NULL, 3),
	(142, 19, NULL, 20, 5),
	(143, 18, 9, NULL, 5),
	(147, 17, 9, NULL, 7),
	(148, 17, NULL, 16, 6),
	(149, 17, NULL, 6, 6),
	(151, 46, 9, NULL, 6),
	(152, 46, NULL, 12, 3),
	(153, 46, NULL, 4, 6),
	(163, 50, 9, NULL, 6),
	(164, 50, NULL, 14, 5),
	(167, 9, 11, NULL, 1),
	(169, 51, 9, NULL, 1),
	(170, 51, NULL, 4, 3),
	(171, 51, NULL, 14, 9),
	(172, 51, NULL, 7, 8),
	(173, 51, NULL, 11, 5),
	(174, 52, 9, NULL, 6),
	(176, 52, NULL, 3, 4),
	(178, 22, 11, NULL, 5),
	(180, 37, NULL, 21, 3),
	(181, 23, NULL, 21, 5),
	(182, 53, 11, NULL, 8),
	(184, 53, NULL, 14, 10),
	(185, 53, NULL, 21, 8),
	(186, 53, 9, NULL, 10),
	(187, 53, NULL, 9, 4),
	(188, 13, NULL, 10, 7),
	(189, 13, NULL, 9, 6),
	(190, 13, NULL, 8, 7),
	(191, 6, NULL, 9, 6),
	(193, 2, NULL, 5, 1),
	(194, 43, 9, NULL, 3),
	(195, 57, 9, NULL, 5),
	(196, 57, NULL, 4, 3),
	(214, 63, 9, NULL, 9),
	(215, 63, NULL, 5, 10),
	(216, 63, NULL, 16, 8),
	(220, 57, 11, NULL, 4),
	(221, 59, NULL, 11, 12),
	(222, 59, 9, NULL, 9),
	(224, 59, NULL, 16, 8),
	(225, 64, NULL, 20, 7),
	(227, 64, 9, NULL, 9),
	(228, 64, NULL, 16, 8),
	(229, 65, NULL, 17, 5),
	(231, 65, NULL, 20, 7),
	(232, 65, NULL, 16, 8),
	(233, 65, 9, NULL, 9),
	(236, 66, NULL, 20, 7),
	(237, 66, NULL, 16, 8),
	(238, 66, 9, NULL, 9),
	(242, 67, 9, NULL, 9),
	(244, 68, NULL, 3, 2),
	(245, 68, NULL, 5, 2),
	(247, 69, 11, NULL, 6),
	(248, 69, NULL, 5, 5),
	(249, 69, NULL, 9, 4),
	(250, 69, NULL, 25, 2),
	(251, 70, NULL, 20, 5),
	(252, 70, 9, NULL, 3),
	(253, 70, NULL, 16, 2),
	(268, 72, 9, NULL, 9),
	(274, 71, NULL, 4, 1),
	(275, 71, 9, NULL, 1),
	(279, 72, NULL, 16, 8),
	(280, 68, 9, NULL, 1),
	(281, 9, 9, NULL, 1),
	(282, 79, NULL, 5, 4),
	(286, 82, NULL, 5, 5),
	(287, 81, NULL, 5, 2),
	(288, 80, NULL, 5, 1),
	(304, 1, NULL, 5, 1),
	(305, 70, NULL, 21, 3),
	(306, 20, NULL, 15, 4),
	(307, 14, NULL, 5, 5),
	(308, 14, NULL, 4, 5),
	(309, 28, NULL, 20, 7),
	(311, 80, 9, NULL, 1),
	(315, 81, 9, NULL, 3),
	(317, 82, 9, NULL, 8),
	(318, 82, NULL, 19, 3),
	(321, 82, NULL, 16, 4),
	(322, 82, NULL, 25, 3),
	(323, 82, NULL, 12, 8),
	(324, 79, 9, NULL, 6),
	(325, 79, NULL, 3, 5),
	(326, 79, 10, NULL, 5),
	(329, 91, 9, NULL, 6),
	(330, 91, NULL, 16, 4),
	(331, 24, NULL, 3, 15),
	(332, 24, NULL, 21, 8),
	(333, 27, NULL, 14, 4),
	(334, 27, 9, NULL, 5),
	(335, 27, NULL, 9, 5),
	(336, 27, NULL, 16, 4),
	(337, 94, NULL, 6, 8),
	(338, 94, 9, NULL, 9),
	(339, 94, NULL, 9, 10),
	(340, 94, NULL, 16, 9),
	(341, 26, NULL, 21, 10),
	(342, 26, NULL, 20, 9),
	(343, 26, 11, NULL, 9),
	(344, 95, NULL, 5, 7),
	(345, 95, 9, NULL, 5),
	(346, 95, NULL, 16, 2),
	(347, 95, NULL, 10, 8),
	(350, 60, 9, NULL, 9),
	(351, 60, NULL, 20, 7),
	(352, 60, NULL, 5, 10),
	(353, 60, NULL, 16, 8),
	(354, 61, NULL, 19, 4),
	(355, 61, 9, NULL, 9),
	(356, 61, NULL, 20, 7),
	(357, 61, NULL, 5, 10),
	(358, 61, NULL, 16, 8),
	(359, 62, NULL, 7, 14),
	(360, 62, 9, NULL, 9),
	(361, 62, NULL, 20, 7),
	(362, 62, NULL, 5, 10),
	(363, 62, NULL, 16, 8),
	(364, 63, NULL, 20, 7),
	(365, 59, NULL, 20, 7),
	(366, 59, NULL, 5, 10),
	(367, 64, NULL, 5, 10),
	(368, 64, NULL, 8, 10),
	(369, 65, NULL, 5, 10),
	(370, 72, NULL, 12, 5),
	(371, 72, NULL, 20, 7),
	(372, 72, NULL, 5, 10),
	(373, 66, NULL, 5, 10),
	(376, 67, NULL, 25, 15),
	(377, 67, NULL, 16, 8),
	(378, 67, NULL, 20, 7),
	(379, 67, NULL, 5, 10),
	(380, 66, NULL, 9, 10),
	(381, 52, NULL, 6, 5),
	(382, 47, 9, NULL, 4),
	(383, 47, 11, NULL, 5),
	(384, 47, NULL, 21, 5),
	(385, 47, NULL, 3, 6),
	(386, 12, 9, NULL, 6),
	(387, 10, 9, NULL, 3),
	(388, 10, NULL, 4, 1),
	(390, 31, NULL, 3, 5),
	(391, 31, NULL, 14, 5),
	(393, 31, NULL, 16, 7),
	(394, 36, 9, NULL, 7),
	(395, 36, NULL, 16, 6),
	(396, 36, NULL, 20, 6),
	(397, 36, NULL, 19, 10),
	(402, 91, NULL, 20, 4),
	(404, 44, NULL, 5, 5),
	(405, 97, 9, NULL, 3),
	(406, 97, NULL, 16, 2),
	(408, 69, 9, NULL, 5),
	(409, 60, NULL, 10, 10),
	(410, 63, NULL, 9, 10),
	(411, 101, 9, NULL, 5),
	(412, 101, NULL, 9, 12),
	(413, 101, NULL, 16, 6),
	(414, 101, NULL, 20, 8),
	(415, 52, NULL, 14, 5),
	(416, 32, NULL, 18, 3),
	(417, 40, NULL, 4, 7),
	(418, 44, 9, NULL, 5),
	(419, 44, NULL, 16, 2),
	(420, 44, NULL, 4, 6),
	(421, 44, 11, NULL, 4),
	(422, 31, 9, NULL, 7),
	(423, 100, 9, NULL, 8),
	(424, 100, NULL, 3, 7),
	(425, 100, NULL, 5, 9),
	(427, 100, NULL, 12, 10),
	(428, 100, NULL, 8, 7),
	(429, 134, 6, NULL, 2),
	(430, 134, NULL, 23, 10),
	(431, 134, NULL, 22, 2),
	(432, 50, NULL, 10, 5),
	(433, 18, NULL, 16, 1),
	(434, 18, NULL, 9, 2),
	(435, 18, NULL, 5, 2),
	(436, 18, 11, NULL, 2),
	(437, 30, NULL, 9, 12),
	(438, 31, NULL, 12, 7),
	(440, 40, 11, NULL, 4),
	(441, 40, NULL, 16, 2),
	(442, 79, NULL, 12, 4),
	(443, 80, NULL, 12, 1),
	(444, 81, NULL, 12, 2),
	(445, 97, NULL, 20, 5),
	(446, 136, NULL, 4, 1),
	(447, 136, NULL, 14, 1),
	(450, 137, NULL, 4, 1),
	(451, 137, NULL, 14, 1),
	(453, 137, NULL, 6, 1);

-- Exportiere Struktur von Tabelle etoa_test.sol_types
DROP TABLE IF EXISTS `sol_types`;
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

-- Exportiere Daten aus Tabelle etoa_test.sol_types: 7 rows
DELETE FROM `sol_types`;
/*!40000 ALTER TABLE `sol_types` DISABLE KEYS */;
INSERT INTO `sol_types` (`sol_type_id`, `sol_type_name`, `sol_type_f_metal`, `sol_type_f_crystal`, `sol_type_f_plastic`, `sol_type_f_fuel`, `sol_type_f_food`, `sol_type_f_power`, `sol_type_f_population`, `sol_type_f_buildtime`, `sol_type_comment`, `sol_type_f_researchtime`, `sol_type_consider`) VALUES
	(1, 'Gelber Stern', 1.35, 1.30, 1.10, 1.00, 0.90, 1.10, 1.10, 1.10, 'Die gelben Sterne gehören zu der Kategorie "mittelgrosse Sterne". Das Alter solcher Gelben Sterne kann extrem variieren; sie können zwischen einigen Jahrtausenden bis hin zu Jahrmillionen alt sein.<br>Generell gilt jedoch, dass auf Gelben Sternen gemässigte und gute Lebensbedingungen herrschen. Ausserdem ist die Geodiversität relativ gross, was den Abbau von Metallen genauso fördert wie die Entwicklung von Chemikalien. Dank dem mineralhaltigen Boden ist sogar ein gewisser Kristallabbau möglich.<br>Einzig der Nahrung scheint der mineralienhaltige Boden nicht ganz so gut zu bekommen...', 1.00, 1),
	(2, 'Blauer Stern', 1.00, 1.30, 1.00, 1.00, 1.05, 0.90, 1.00, 1.00, 'Diese Art von Sternen erscheint dem Beobachter meist blau; das liegt daran, dass im Innern des Sterns eine gewaltige Hitze herrscht, vergleichbar mit der blauen Färbung einer Flamme beim Schweissen.<br>Durch die gigantischen Hitzewellen sind die Lebensbedingungen im Umfeld Blauer Sterne für die verschiedenen Völker nicht optimal. Einige jedoch haben sich inzwischen dem heissen Klima anpassen können und nutzen genau dieses zur Verschmelzung von Kristallinem Material, um qualitativ hochstehende Kristallite herzustellen.<br>Bisher wollte es jedoch noch keinem Volk so richtig gelingen, aus dem heissen Klima einen weiteren Nutzen in Sachen Industrie zu ziehen. Im Gegenteil, meist ist die Stromproduktion und das Wachstum der Bevölkerung tiefer als in anderen Sternsystemen.', 1.00, 1),
	(4, 'Weisser Stern', 0.90, 1.20, 1.25, 1.30, 0.95, 1.10, 1.00, 1.00, 'Weisse Sterne sind stark energiehaltige Sterne, deren Energieausstösse für das extrem helle Licht verantwortlich sind.<br>Dadurch lässt sich in der Nähe von Weissen Sternen mit relativ wenig Aufwand Tritium und Strom herstellen. Ebenfalls positiv wirkt sich die Energiestrahlung auf die Kristallisation aus, jedoch nicht auf die Menschen. Jene ertragen die gewaltigen Energiemengen nicht zu lange, weshalb der Bevölkerungswachstum in Weissen Sternen meist kleiner als in anderen Sternen ist.', 0.90, 1),
	(3, 'Roter Stern', 0.95, 0.95, 1.00, 0.85, 0.95, 0.90, 1.10, 0.85, 'Rote Sterne sind eher klein und schon recht alt. Dadurch ist ihre Energieaustrahlung nicht mehr ganz so gross, was wiederum eine gute Klimabedingung für die meisten Völker ist. Deshalb verwundert es nicht, dass man in vielen Roten Sternen alle möglichen Völker antrifft, welche dort seit ewigen Zeiten eine neue Heimat gefunden haben.<br>Ebenfalls positiv wirkt sich die gemässigte Energieabgabe der Roten Sterne auf verschiedenste Produktionen aus, was dann wiederum den dort wohnhaften Völkern zugute kommt.', 1.00, 1),
	(7, 'Grüner Stern', 1.40, 1.10, 1.00, 1.00, 1.30, 1.00, 1.00, 1.10, 'Grüne Sterne wirken auf den ersten Blick giftig - und so ganz unrecht ist das auch nicht. Durch Gase aus dem Inneren der Sterne werden immer wieder Epidemien ausgelöst, die Teile der Bewohner von Grünen Sternen dahinraffen.<br>Entgegen den unwirtlichen Lebensbedingungen wirken sich die Gase und die Geostruktur positiv auf die Steingefüge der Sterne aus.<br>Es verwundert daher nicht, dass in Grünen Sternen oftmals Raffinerien, Erzwerke und Metallverarbeitungsanlagen anzutreffen sind.', 1.00, 1),
	(5, 'Violetter Stern', 1.20, 0.90, 1.10, 1.00, 1.20, 1.10, 1.00, 1.00, 'Violette Sterne sind sehr junge Sterne, die sich meistens innerhalb von Gaswolken befinden. Die für den Betrachter violette Färbung der Sonne entsteht durch die vielen verschiedenen Nebel, welche das Sonnenlicht jeweils verschieden brechen.<br>Weil die Sterne noch ziemlich jung sind, ist noch nicht viel über sie bekannt; die Beobachtungen der verschiedenen Völker haben erst begonnen.', 0.90, 1),
	(6, 'Schwarzer Stern', 1.00, 1.05, 1.05, 0.90, 1.00, 1.00, 1.00, 0.90, 'Praktisch keiner weiss etwas über schwarze Sterne, da sie erst vor kurzem durch eine neuartige Objektivtechnologie sichtbar gemacht werden konnten.<br>Erst einzelne überragende Forscher haben angefangen, sich an diese Mysterien im All heranzuwagen.<br>Ungenannte Quellen munkeln jedoch, dass die schwarze Färbung durch aktive schwarze Löcher auftritt, was die Völker natürlich davor abschreckt, mehr über die Schwarzen Sterne rauszufinden.', 1.00, 1);
/*!40000 ALTER TABLE `sol_types` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.space
DROP TABLE IF EXISTS `space`;
CREATE TABLE IF NOT EXISTS `space` (
  `id` int unsigned NOT NULL,
  `lastvisited` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.space: 0 rows
DELETE FROM `space`;
/*!40000 ALTER TABLE `space` DISABLE KEYS */;
/*!40000 ALTER TABLE `space` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.specialists
DROP TABLE IF EXISTS `specialists`;
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

-- Exportiere Daten aus Tabelle etoa_test.specialists: 0 rows
DELETE FROM `specialists`;
/*!40000 ALTER TABLE `specialists` DISABLE KEYS */;
INSERT INTO `specialists` (`specialist_id`, `specialist_name`, `specialist_desc`, `specialist_enabled`, `specialist_points_req`, `specialist_costs_metal`, `specialist_costs_crystal`, `specialist_costs_plastic`, `specialist_costs_fuel`, `specialist_costs_food`, `specialist_days`, `specialist_prod_metal`, `specialist_prod_crystal`, `specialist_prod_plastic`, `specialist_prod_fuel`, `specialist_prod_food`, `specialist_power`, `specialist_population`, `specialist_time_tech`, `specialist_time_buildings`, `specialist_time_defense`, `specialist_time_ships`, `specialist_costs_buildings`, `specialist_costs_defense`, `specialist_costs_ships`, `specialist_costs_tech`, `specialist_fleet_speed`, `specialist_fleet_max`, `specialist_def_repair`, `specialist_spy_level`, `specialist_tarn_level`, `specialist_trade_time`, `specialist_trade_bonus`) VALUES
	(1, 'Admiral', 'Der Flottenadmiral ist ein kriegserfahrener Veteran und meisterhafter Stratege. Auch im heissesten Gefecht behält er im Gefechtsleitstand den Überblick und hält Kontakt mit den ihm unterstellten Admirälen. Ein weiser Herrscher kann sich auf seine Unterstützung im Kampf absolut verlassen und somit mehr Raumflotten gleichzeitig und schneller ins Gefecht führen.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.40, 3, 1.00, 0, 0, 1.00, 1.00),
	(2, 'Ingenieur', 'Der Ingenieur ist ein Spezialist für besonders durchdachte und stabile Verteidigungssysteme. Durch seine Mithilfe können Verteidigungsanlagen schneller und günstiger produziert werden. Nach einem Kampf kann er auch schwer beschädigte Anlagen wieder reparieren.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 0.80, 1.00, 1.00, 0.90, 1.00, 1.00, 1.00, 0, 1.40, 0, 0, 1.00, 1.00),
	(3, 'Geologe', 'Der Geologe ist ein anerkannter Experte in Astromineralogie und -kristallographie. Mithilfe seines Teams aus Metallurgen und Chemieingenieuren unterstützt er interplanetarische Regierungen bei der Erschließung neuer Rohstoffquellen und der Optimierung ihrer Raffination.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, 1.15, 1.15, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 0, 1.00, 0, 0, 1.00, 1.00),
	(4, 'Professor', 'Die Gilde der Technokraten sind geniale Wissenschaftler, und man findet sie immer dort, wo die Grenzen des technisch Machbaren gesprengt werden. Durch seine reine Anwesenheit inspiriert er die Forscher des Imperiums.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 0.80, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 0.90, 1.00, 0, 1.00, 0, 0, 1.00, 1.00),
	(5, 'Biologe', 'Der Biologe steigert durch seine gentechnischen Experimente den Ertrag deiner Gewächshäuser und sorgt für ein rascheres Bevölkerungswachstum.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, 1.00, 1.00, 1.00, 1.00, 1.30, 1.00, 1.30, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 0, 1.00, 0, 0, 1.00, 1.00),
	(6, 'Spion', 'Der Spion ist ein Meister der Tarnung und Informationsbeschaffung. Durch seine Tricks ist es möglich, mehr Informationen über den Gegner herauszufinden und die eigenen Schiffe besser zu tarnen.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 0.90, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 0, 1.00, 3, 2, 1.00, 1.00),
	(7, 'Meisterhändler', 'Durch das Verhandlungsgeschick des Meisterhändlers fallen im Markt keine zusätzlichen Kosten an, er hat weniger Handelsbeschränkungen und seine Handelsschiffe fliegen schneller als alle anderen.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 0, 1.00, 0, 0, 6.00, 0.00),
	(8, 'Chemiker', 'Der Chemiker kennt sich mit der Herstellung synthetischer und radioaktiver Rohstoffe aus. Durch sein Wissen im Bereich chemischer Herstellungsverfahren kann die Produktion von PVC und Tritium merklich gesteigert werden.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, 1.00, 1.00, 1.25, 1.15, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 0, 1.00, 0, 0, 1.00, 1.00),
	(9, 'Nulldummy', 'Nicht löschen', 0, 0, 0, 0, 0, 0, 0, 14, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 0, 1.00, 0, 0, 1.00, 1.00),
	(10, 'Architekt', 'Der Architekt hilft mit seinem Wissen bei der Planung und Konstruktion komplexer Bauprojekte. Aufgrund seiner langjährigen Erfahrung können Bauten unter seiner Leitung schneller realisiert werden.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 0.90, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 1.00, 0, 1.00, 0, 0, 1.00, 1.00);
/*!40000 ALTER TABLE `specialists` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.stars
DROP TABLE IF EXISTS `stars`;
CREATE TABLE IF NOT EXISTS `stars` (
  `id` int unsigned NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `type_id` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.stars: 0 rows
DELETE FROM `stars`;
/*!40000 ALTER TABLE `stars` DISABLE KEYS */;
/*!40000 ALTER TABLE `stars` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.techlist
DROP TABLE IF EXISTS `techlist`;
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

-- Exportiere Daten aus Tabelle etoa_test.techlist: 0 rows
DELETE FROM `techlist`;
/*!40000 ALTER TABLE `techlist` DISABLE KEYS */;
/*!40000 ALTER TABLE `techlist` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.technologies
DROP TABLE IF EXISTS `technologies`;
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

-- Exportiere Daten aus Tabelle etoa_test.technologies: ~23 rows (ungefähr)
DELETE FROM `technologies`;
INSERT INTO `technologies` (`tech_id`, `tech_name`, `tech_type_id`, `tech_shortcomment`, `tech_longcomment`, `tech_costs_metal`, `tech_costs_crystal`, `tech_costs_fuel`, `tech_costs_plastic`, `tech_costs_food`, `tech_costs_power`, `tech_build_costs_factor`, `tech_last_level`, `tech_show`, `tech_order`, `tech_stealable`) VALUES
	(3, 'Energietechnik', 4, 'Diese Technologie dient zur Erforschung neuer Arten der Energiegewinnung.', 'Durch die Unterstützung der Energietechnik können neue Arten der Energiegewinnung erforscht werden.', 300, 250, 30, 50, 0, 0, 1.50, 50, 1, 0, 1),
	(4, 'Wasserstoffantrieb', 1, 'Einfacher Antrieb für Raumschiffe', 'Ein Wasserstoffantrieb nutzt Wasserstoff als Treibstoff. Dieser wird durch Elektrolyse von Wasser, Reformation von Methanol oder durch Dampfreformation von Erdgas gewonnen.', 500, 300, 250, 50, 0, 0, 1.50, 50, 1, 0, 1),
	(5, 'Ionenantrieb', 1, 'Hoch entwickelter Antrieb für Spezialschiffe. Er ist weniger schnell als der Wasserstoffantrieb, dafür kostensparend.', 'Ein Ionenantrieb ist ein Antrieb für Raumfahrzeuge, bei dem die Abstossung von einem Ionenstrahl zur Fortbewegung genutzt wird. Es werden auch je nach Energiequelle die Begriffe \\"solar-elektrischer Antrieb\\" bzw. \\"Solar Electric Propulsion\\" \n(SEP) und \\"nuklear-elektrischer Antrieb\\" bzw. \\"Nuclear Electric Propulsion\\" \n(NEP) verwendet.\r\nDer Ionenstrahl besteht aus einem elektrisch geladenen Gas \n(z.B. Xenon). Erzeugt wird der Ionenstrahl durch ionisierte Gasteilchen, die in einem elektrischen Feld oder mittels einer Kombination eines elektrischen Feldes und eines Magnetfeldes unter Ausnutzung des Hall-Effektes beschleunigt und dann in Form eines Strahls ausgestossen werden. Die Energie zur Erzeugung der Felder wird üblicherweise mit Hilfe von Solarzellen gewonnen. Als Treibstoff des Ionenantriebs dient sowohl das Gas als auch die zusätzlich benötigte elektrische Energie.\r\nDer Vorteil des Ionenantriebs gegenüber dem chemischen Antrieb liegt darin, dass er weniger Treibstoff verbraucht, weil die Geschwindigkeit der austretenden Teilchen wesentlich grösser ist.', 1000, 1500, 800, 300, 0, 0, 1.50, 50, 1, 1, 1),
	(6, 'Hyperraumantrieb', 1, 'Sehr schneller Antrieb für grosse Schiffe, der den Hyperraum als Transportmedium benutzt.', 'Der Hyperraumantrieb schafft eine technisch hervorgerufene Abkürzung zwischen weit entfernten Punkten in der Raumzeit. Die Idee ist dabei folgende: Um den Weg vom Nordpol zum Südpol abzukürzen, reise man quer durch die Erde, anstatt entlang der Oberfläche. Der Weg durch die Erde \n(in die dritte Dimension) ist kürzer als der Weg auf der \n(zweidimensionalen) Erdoberfläche. Genauso könnte man sich vorstellen, dass unsere Raumzeit auch in einen höherdimensionalen Hyperraum eingebettet ist \n(wie die Erdoberfläche in den Raum), und man daher durch den Hyperraum abkürzen könnte. Auch hier würde man \n(im Hyperraum) nicht schneller als Lichtgeschwindigkeit fliegen müssen, um schneller als das Licht im Normalraum am Ziel anzukommen.\r\nDiese Antriebstechnologie wird heute für fast jedes grosse und träge Schiff eingesetzt.', 4000, 6000, 1500, 5500, 0, 0, 1.80, 50, 1, 3, 1),
	(7, 'Spionagetechnik', 4, 'Je höher die Spionagetechnik ist, desto mehr können Spionagesonden über gegnerische Planeten herausfinden.', 'Spionage ist die Auskundschaftung und Erlangung von fremden, wohlgehüteten Geheimnissen oder Wissen von fremden Planeten. Die erlangten Informationen werden dann in den eigenen wirtschaftlichen, politischen oder militärischen Machtbereich eingeführt, ohne dass eine eigenständige Erforschung erfolgen müsste. Annähernd sämtliche Imperien bedienen sich der Spionage oder "nachrichtendienstlicher Mittel", um andere Völker \r\n(unabhängig der feindseligen oder freundlichen Einstellung zum eigenen Volk) auszuspionieren.\r\nEine weitere nützliche Eigenschaft der Spionagetechnik ist das Enttarnen von feindlichen Angriffen, welche mit höherer Spionagetechnik schneller vonstatten geht.', 750, 370, 150, 520, 0, 0, 1.50, 50, 1, 1, 1),
	(8, 'Waffentechnik', 2, 'Jede Ausbaustufe erhöht die Stärke der Waffen bei Raumschiffen und Verteidigungsanlagen.', 'Durch die Erforschung der Waffentechnik können neue und stärkere Waffen für Raumschiffe und Verteidigungsanlagen gebaut werden.\r\nPro Ausbaustufe erhöht sich die Angriffskraft deiner Schiffe und Verteidigungsanlagen um 10%.', 250, 800, 550, 200, 0, 0, 1.80, 50, 1, 1, 1),
	(9, 'Panzerung', 2, 'Jede Ausbaustufe erhöht die Stärke der Panzerung bei Raumschiffen und Verteidigungsanlagen.', 'Jedes Schiff und jede Verteidigungsanlage besitzen eine Panzerung zum Schutz vor feindlichen Angriffen. Pro Ausbaustufe erhöht diese Technologie die Panzerung, auch genannt Struktur, um 10%.', 1000, 150, 320, 270, 0, 0, 1.80, 50, 1, 2, 1),
	(10, 'Schutzschilder', 2, 'Jede Ausbaustufe erhöht die Stärke der Schutzschilder bei Raumschiffen und Verteidigungsanlagen.', 'Ein Schutzschild schützt deine Raumschiffe und Verteidigungsanlagen vor feindlichem Beschuss.\r\nPro Ausbaustufe erhöht sich die Effizienz von den Schutzschildern um 10%.', 290, 330, 250, 950, 0, 0, 1.80, 50, 1, 3, 1),
	(11, 'Tarntechnik', 2, 'Durch eine hohe Tarntechnik können deine Flotten eine gewisse Zeit vor dem Gegner verborgen bleiben.', 'Die Kriegsära hat begonnen; die Völker erforschen Technologien, mit welchen sie dem Gegner in einem allfälligen Kampf überlegen sind. Die Tarntechnik ist eigentlich schon eine uralte Waffe, welche den Überraschungseffekt ausnutzt, um so eine bessere Ausgangsposition zu haben; doch erst jetzt ist es wirklich möglich, seine Schiffe von der gegnerischen Flottenkontrolle zu verstecken.\r\nJe höher diese Technologie erforscht ist, desto länger bleiben die Schiffe für den Gegner unentdeckt.', 1500, 750, 250, 800, 0, 0, 1.50, 50, 1, 4, 1),
	(12, 'Recyclingtechnologie', 4, 'Ermöglicht eine effiziente Wiederverwertung von alten Verteidigungsanlagen und Schiffen.', 'Lange Zeit hatte man eine Technik gesucht, welche verbaute Rohstoffe wieder verwerten kann. Nach jahrelanger Forschung wurde ein Verfahren entwickelt, das Schiffe und Verteidigungsanlagen recyceln kann. Jedoch ist diese Technologie in der Anfangsphase noch sehr ineffizient.\r\nDies kann aber mit der Weiterentwicklung ein wenig eingedämpft werden. Man weiss jedoch, dass die Materialien nie zu 100% recycelt werden können.', 6000, 10000, 1000, 4000, 0, 0, 1.90, 50, 1, 2, 1),
	(13, 'Rettungskapseln', 2, 'Je höher die Rettungskapseln entwickelt sind, desto mehr Piloten können sich retten, wenn ihr Schiff bei einem Kampf zerstört wird. ', 'Je höher die Rettungskapseln entwickelt sind, desto mehr Piloten können sich retten, wenn ihr Schiff bei einem Kampf zerstört wird.\r\nEinige Schiffe können nur gebaut werden, wenn gute Rettungskapseln an Bord sind.\r\nUm Grosse Schiffe zu bauen, muss man die Rettungskapseln entwickelt haben.', 12000, 2000, 3000, 8000, 2000, 0, 1.90, 50, 0, 5, 1),
	(14, 'Kraftstoffantrieb', 1, 'Verbesserter Wasserstoffantrieb, der mit einer Mischung aus Tritium und Asteroidenteilchen arbeitet. ', 'Verbesserter Wasserstoffantrieb, der mit einer Mischung aus Tritium und Asteroidenteilchen arbeitet. Dieser Antrieb ermöglicht es grösseren Schiffen, sich schneller fortzubewegen.', 25500, 7752, 19347, 10474, 0, 0, 1.30, 50, 1, 2, 1),
	(15, 'Bombentechnik', 3, 'Mit Hilfe dieser Technik wird die Effektivität von Bombenangriffen gesteigert.', 'Längst hat man rausgefunden, dass das alleinige Zerstören von gegnerischen Flotten nicht mehr unbedingt den gewünschten Effekt hat.\r\nForscher haben aus diesem Grund eine neuartige Waffe entwickelt, mit der es möglich ist, fremde Gebäude zu bombardieren und so den Gegner wieder ins industrielle Mittelalter zu befördern.\r\nDiese Methode der Kriegsführung ist aber noch sehr jung, und deshalb ist die Chance auf eine erfolgreiche Bombardierung noch nicht allzu hoch.\r\nDurch die Erforschung der Bombentechnik wird diese aber deutlich gesteigert.', 13000, 26000, 8000, 13000, 0, 0, 1.75, 50, 0, 0, 1),
	(16, 'Rassentechnik', 4, 'Mit der Rassentechnologie kann jede Rasse ihre rassenspezifischen Objekte bauen.', 'Mit der Rassentechnologie kann jede Rasse ihre rassenspezifischen Objekte bauen. Je höher sie erforscht ist, desto bessere und stärkere Rassenobjekte können gebaut werden.', 1000, 1000, 1000, 1000, 1000, 0, 1.50, 50, 1, 3, 1),
	(17, 'EMP-Technik', 3, 'EMP-Bomben löst einen Elektromagnetischen Impuls aus, welcher elektrische Einrichtungen ausser Betrieb setzen kann.', 'Je länger je mehr schützen die Völker ihre Schiffe, indem sie sie ständig auf Erkundungsflüge schicken und so für den Gegner unerreichbar machen.\r\nEin Forschungsteam der Rigelianer hat es sich zur Aufgabe gemacht, diese Strategie zu vernichten.\r\nNach langen Forschungen haben sie ein Schiff entwickelt, mit dem es möglich ist, ganze Einrichtungen unbrauchbar zu machen.\r\nEin elektromagnetischer Impuls setzt alle elektronischen Geräte ausser Gefecht. Mit Hilfe dieser brillianten Waffe kann man nun dem Gegner beispielsweise die Flottenkontrolle lahm legen und den Schiffen den Abflug vom Planeten verweigern.\r\nJedoch ist auch diese Technologie noch nicht ganz ausgereift; so muss man sich beispielsweise mit einer kurzfristigen Deaktivierung zufrieden geben. Durch die Weiterentwicklung der EMP Technologie erhöht sich jedoch die Effizienz des Angriffes.', 15000, 15000, 10000, 15000, 0, 0, 1.70, 50, 1, 1, 1),
	(18, 'Gifttechnik', 3, 'Diese Technologie wird für B- und C- Waffen gebraucht.', 'Die Gifttechnologie ist eine Massenvernichtungswaffe für Bewohner. Durch Zerstörung der Nervenbahnen und allmähliches Verringern der Wahrnehmungsfähigkeit lässt das Gift die Bewohner erkranken und kurze Zeit später an den Folgen sterben. Eine grausame, aber sehr effektive Waffe.\r\nDie Weiterentwicklung ermöglicht einen noch präziseren Einsatz der Gifte.', 10000, 10000, 5000, 20000, 0, 0, 1.50, 50, 1, 2, 1),
	(19, 'Regenatechnik', 2, 'Neuartige Materialien ermöglichen gewissen Schiffen, sich während dem Kampf teilweise zu reparieren.', 'Das Heilen von Schiffen war schon immer sehr schwierig und wird sich wohl erst in Zukunft bei einer neuen Generation von Schiffen durchsetzen.\r\nBisher ist es nur einer einzigen Rasse gelungen, ein Schiff herzustellen, welches die eigene Flotte im Kampf heilen kann.\r\nEiner anderen Rasse ist es inzwischen gelungen, diesselbe Technologie für ihre Verteidigungsanlagen anzuwenden.\r\nDurch die Erhöhung der Technologie kann deren Effizienz gesteigert werden.', 600, 600, 300, 300, 0, 0, 1.80, 50, 1, 3, 1),
	(20, 'Warpantrieb', 1, 'Die Warpgondeln eines Raumschiffes erzeugen ein Feld, welches den Raum krümmt und so das Schiff extrem beschleunigt.', 'Jede Rasse hat nach einer gewissen Zeit angefangen, ihre eigenen Schiffe zu bauen. Eine uns unbekannte Rasse hat den Warpantrieb entwickelt. Die uns bekannten Rassen konnten ihn jedoch nur bedingt anwenden. So sind ihre Schiffe nicht ganz so schnell wie sie eigentlich sein könnten. Die Warpgondeln eines Raumschiffes erzeugen ein Feld, welches den Raum krümmt und so das Schiff extrem beschleunigt.', 6000, 4500, 2000, 5500, 0, 0, 1.70, 50, 1, 5, 1),
	(21, 'Solarantrieb', 1, 'Hinter dem unspektakulären Namen steckt eine sehr sparsame und interessante Technik. ', 'Hinter dem unspektakulären Namen steckt eine sehr sparsame und interessante Technik. Schiffe mit einem Solarantrieb können während dem Flug ihr Triebwerk ausschalten und ein riesiges Sonnensegel ausfahren, wodurch sie vom Sonnenwind mit unglaublicher Geschwindigkeit durchs All getragen werden.\r\nDie Erforschung ist nicht sehr billig, jedoch birgt es einen unschlagbaren Vorteil. Die Schiffe verbrauchen viel weniger Treibstoff für den Flug. Es soll sogar Schiffe geben, die allein mit den Solarzellen die benötigte Energie zum Flug aufbringen und so ohne Tritiumverbrauch fliegen können.', 2100, 1300, 1100, 300, 0, 0, 1.80, 50, 1, 4, 1),
	(22, 'Wurmlochforschung', 3, 'Ermöglicht einer Flotte das Reisen durch Wurmlöcher. Dadurch wird die Flugzeit einer Flotte enorm verkürzt.', 'Wurmlöcher sind topologische Konstrukte, die weit voneinander entfernt liegende Bereiche des Universums durch eine \\\'Abkürzung\\\' verbinden. Ein Ende eines Wurmlochs erscheint dem Beobachter als Kugel, die ihm die Umgebung des anderen Endes zeigt. Obwohl ein durch ein Wurmloch Reisender nie die Lichtgeschwindigkeit überschreiten würde, hätte in Bezug auf die betreffenden Start- und Zielbereiche eine Reise mit Überlichtgeschwindigkeit stattgefunden. Durch die Erforschung der Wurmlöcher gelang es Wissenschaftlern, Technologien für das Reisen durch Wurmlöcher zu entwickeln und somit die Flugzeit enorm zu verkürzen. Ob die zwei Wurmlochenden eines Lochs immer miteinander verknüpft bleiben oder ob  die Verknüpfungen von Zeit zu Zeit ändern, ist Gegenstand aktueller Untersuchungen.\r\nBisher ist es den Forschern jedoch noch nicht gelungen, ein solches Wurmloch länger als ein paar Tage offen zu halten.', 100000, 120000, 175000, 290000, 250000, 0, 1.60, 1, 1, 5, 1),
	(23, 'Gentechnik', 3, 'Durch die Manipulierung der Gene ist es möglich, die Leistung der Arbeiter zu steigern und so die Bauzeit zu verringern.', 'Den Forschern ist ein absoluter Durchbruch im Bereich Genforschung gelungen. Bisher waren alle genmanipulierten Arbeiterversuche fehlgeschlagen und die meisten Versuchsobjekte überlebten dieses Experiment nicht. Doch nun gelang mit Hilfe von Hochpräzisionsmaschinen eine genetische Veränderung, sodass die Arbeiter zu höheren Leistungen fähig sind.\r\nDies hat zur Folge, dass die Bauzeit von jeglichen Produkten nochmals gesenkt werden kann.\r\nDiese revolutionäre Errungenschaft hat aber ihren Preis, denn der Eingriff ist extrem zeit- und kostenaufwändig. Viele Wissenschaftler sind sich aber dennoch einig, dass es sich allemal lohnt, diese Technologie zu verbessern und zu perfektionieren.', 95000000, 57000000, 36100000, 38000000, 19000000, 0, 2.00, 8, 1, 6, 0),
	(24, 'Raketentechnik', 3, 'Das Wissen um diese Technologie in Verbindung mit dem Raketensilo ermöglichen es, Raketen zu konstruieren.', 'Damit Raketen eingesetzt werden können, muss zuerst die Raketetechnik erforscht sein. Je höher die Raketentechnik erforscht ist, desto bessere und effektivere Raketen können gebaut werden.', 30000, 60000, 400000, 20000, 0, 0, 1.20, 10, 1, 6, 1),
	(25, 'Computertechnik', 4, 'Mit Computern können Forscher komplexe Gleichungssysteme lösen, um genauere Flugbahnen zu berechnen.', 'Mit Hilfe der Computerwissenschaft können Forscher komplexe Gleichungssysteme lösen, um damit zum Beispiel genaue Flugbahnen zu berechnen. Dies kann zu einem Vorteil in der gegnerischen Flottenüberwachung führen oder eine bessere Steuerbarkeit von Raketen ermöglichen.', 500, 5000, 0, 3000, 0, 0, 1.50, 50, 1, 4, 1);

-- Exportiere Struktur von Tabelle etoa_test.tech_points
DROP TABLE IF EXISTS `tech_points`;
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

-- Exportiere Daten aus Tabelle etoa_test.tech_points: 0 rows
DELETE FROM `tech_points`;
/*!40000 ALTER TABLE `tech_points` DISABLE KEYS */;
INSERT INTO `tech_points` (`bp_id`, `bp_tech_id`, `bp_level`, `bp_points`) VALUES
	(10767, 3, 1, 0.630),
	(10768, 3, 2, 1.575),
	(10769, 3, 3, 2.993),
	(10770, 3, 4, 5.119),
	(10771, 3, 5, 8.308),
	(10772, 3, 6, 13.092),
	(10773, 3, 7, 20.268),
	(10774, 3, 8, 31.032),
	(10775, 3, 9, 47.179),
	(10776, 3, 10, 71.398),
	(10777, 3, 11, 107.727),
	(10778, 3, 12, 162.220),
	(10779, 3, 13, 243.961),
	(10780, 3, 14, 366.571),
	(10781, 3, 15, 550.486),
	(10782, 3, 16, 826.359),
	(10783, 3, 17, 1240.169),
	(10784, 3, 18, 1860.884),
	(10785, 3, 19, 2791.956),
	(10786, 3, 20, 4188.563),
	(10787, 3, 21, 6283.475),
	(10788, 3, 22, 9425.843),
	(10789, 3, 23, 14139.394),
	(10790, 3, 24, 21209.721),
	(10791, 3, 25, 31815.212),
	(10792, 3, 26, 47723.448),
	(10793, 3, 27, 71585.802),
	(10794, 3, 28, 107379.333),
	(10795, 3, 29, 161069.630),
	(10796, 3, 30, 241605.075),
	(10797, 3, 31, 362408.242),
	(10798, 3, 32, 543612.993),
	(10799, 3, 33, 815420.119),
	(10800, 3, 34, 1223130.809),
	(10801, 3, 35, 1834696.844),
	(10802, 3, 36, 2752045.895),
	(10803, 3, 37, 4128069.473),
	(10804, 3, 38, 6192104.840),
	(10805, 3, 39, 9288157.890),
	(10806, 3, 40, 13932237.464),
	(10807, 3, 41, 20898356.827),
	(10808, 3, 42, 31347535.870),
	(10809, 3, 43, 47021304.435),
	(10810, 3, 44, 70531957.282),
	(10811, 3, 45, 105797936.553),
	(10812, 3, 46, 158696905.460),
	(10813, 3, 47, 238045358.820),
	(10814, 3, 48, 357068038.860),
	(10815, 3, 49, 535602058.920),
	(10816, 3, 50, 803403089.010),
	(10817, 4, 1, 1.100),
	(10818, 4, 2, 2.750),
	(10819, 4, 3, 5.225),
	(10820, 4, 4, 8.938),
	(10821, 4, 5, 14.506),
	(10822, 4, 6, 22.859),
	(10823, 4, 7, 35.389),
	(10824, 4, 8, 54.184),
	(10825, 4, 9, 82.375),
	(10826, 4, 10, 124.663),
	(10827, 4, 11, 188.095),
	(10828, 4, 12, 283.242),
	(10829, 4, 13, 425.963),
	(10830, 4, 14, 640.044),
	(10831, 4, 15, 961.167),
	(10832, 4, 16, 1442.850),
	(10833, 4, 17, 2165.375),
	(10834, 4, 18, 3249.162),
	(10835, 4, 19, 4874.843),
	(10836, 4, 20, 7313.365),
	(10837, 4, 21, 10971.147),
	(10838, 4, 22, 16457.821),
	(10839, 4, 23, 24687.831),
	(10840, 4, 24, 37032.847),
	(10841, 4, 25, 55550.370),
	(10842, 4, 26, 83326.655),
	(10843, 4, 27, 124991.083),
	(10844, 4, 28, 187487.725),
	(10845, 4, 29, 281232.687),
	(10846, 4, 30, 421850.130),
	(10847, 4, 31, 632776.295),
	(10848, 4, 32, 949165.543),
	(10849, 4, 33, 1423749.415),
	(10850, 4, 34, 2135625.222),
	(10851, 4, 35, 3203438.933),
	(10852, 4, 36, 4805159.500),
	(10853, 4, 37, 7207740.350),
	(10854, 4, 38, 10811611.625),
	(10855, 4, 39, 16217418.537),
	(10856, 4, 40, 24326128.906),
	(10857, 4, 41, 36489194.459),
	(10858, 4, 42, 54733792.789),
	(10859, 4, 43, 82100690.283),
	(10860, 4, 44, 123151036.524),
	(10861, 4, 45, 184726555.887),
	(10862, 4, 46, 277089834.930),
	(10863, 4, 47, 415634753.495),
	(10864, 4, 48, 623452131.343),
	(10865, 4, 49, 935178198.114),
	(10866, 4, 50, 1402767298.271),
	(10867, 5, 1, 3.600),
	(10868, 5, 2, 9.000),
	(10869, 5, 3, 17.100),
	(10870, 5, 4, 29.250),
	(10871, 5, 5, 47.475),
	(10872, 5, 6, 74.813),
	(10873, 5, 7, 115.819),
	(10874, 5, 8, 177.328),
	(10875, 5, 9, 269.592),
	(10876, 5, 10, 407.988),
	(10877, 5, 11, 615.582),
	(10878, 5, 12, 926.974),
	(10879, 5, 13, 1394.060),
	(10880, 5, 14, 2094.691),
	(10881, 5, 15, 3145.636),
	(10882, 5, 16, 4722.054),
	(10883, 5, 17, 7086.681),
	(10884, 5, 18, 10633.622),
	(10885, 5, 19, 15954.032),
	(10886, 5, 20, 23934.648),
	(10887, 5, 21, 35905.573),
	(10888, 5, 22, 53861.959),
	(10889, 5, 23, 80796.539),
	(10890, 5, 24, 121198.408),
	(10891, 5, 25, 181801.212),
	(10892, 5, 26, 272705.418),
	(10893, 5, 27, 409061.726),
	(10894, 5, 28, 613596.190),
	(10895, 5, 29, 920397.884),
	(10896, 5, 30, 1380600.426),
	(10897, 5, 31, 2070904.240),
	(10898, 5, 32, 3106359.960),
	(10899, 5, 33, 4659543.539),
	(10900, 5, 34, 6989318.909),
	(10901, 5, 35, 10483981.964),
	(10902, 5, 36, 15725976.545),
	(10903, 5, 37, 23588968.418),
	(10904, 5, 38, 35383456.227),
	(10905, 5, 39, 53075187.941),
	(10906, 5, 40, 79612785.511),
	(10907, 5, 41, 119419181.866),
	(10908, 5, 42, 179128776.399),
	(10909, 5, 43, 268693168.199),
	(10910, 5, 44, 403039755.898),
	(10911, 5, 45, 604559637.447),
	(10912, 5, 46, 906839459.771),
	(10913, 5, 47, 1360259193.257),
	(10914, 5, 48, 2040388793.485),
	(10915, 5, 49, 3060583193.827),
	(10916, 5, 50, 4590874794.341),
	(10917, 6, 1, 17.000),
	(10918, 6, 2, 47.600),
	(10919, 6, 3, 102.680),
	(10920, 6, 4, 201.824),
	(10921, 6, 5, 380.283),
	(10922, 6, 6, 701.510),
	(10923, 6, 7, 1279.718),
	(10924, 6, 8, 2320.492),
	(10925, 6, 9, 4193.885),
	(10926, 6, 10, 7565.993),
	(10927, 6, 11, 13635.787),
	(10928, 6, 12, 24561.417),
	(10929, 6, 13, 44227.550),
	(10930, 6, 14, 79626.591),
	(10931, 6, 15, 143344.863),
	(10932, 6, 16, 258037.754),
	(10933, 6, 17, 464484.956),
	(10934, 6, 18, 836089.922),
	(10935, 6, 19, 1504978.859),
	(10936, 6, 20, 2708978.946),
	(10937, 6, 21, 4876179.103),
	(10938, 6, 22, 8777139.385),
	(10939, 6, 23, 15798867.893),
	(10940, 6, 24, 28437979.207),
	(10941, 6, 25, 51188379.573),
	(10942, 6, 26, 92139100.232),
	(10943, 6, 27, 165850397.417),
	(10944, 6, 28, 298530732.351),
	(10945, 6, 29, 537355335.232),
	(10946, 6, 30, 967239620.418),
	(10947, 6, 31, 1741031333.752),
	(10948, 6, 32, 3133856417.754),
	(10949, 6, 33, 5640941568.958),
	(10950, 6, 34, 10153694841.123),
	(10951, 6, 35, 18276650731.022),
	(10952, 6, 36, 32897971332.840),
	(10953, 6, 37, 59216348416.112),
	(10954, 6, 38, 106589427166.000),
	(10955, 6, 39, 191860968915.800),
	(10956, 6, 40, 345349744065.440),
	(10957, 6, 41, 621629539334.800),
	(10958, 6, 42, 1118933170819.600),
	(10959, 6, 43, 2014079707492.400),
	(10960, 6, 44, 3625343473503.200),
	(10961, 6, 45, 6525618252322.800),
	(10962, 6, 46, 11746112854198.000),
	(10963, 6, 47, 21143003137574.000),
	(10964, 6, 48, 38057405647649.000),
	(10965, 6, 49, 68503330165786.000),
	(10966, 6, 50, 123305994298430.000),
	(10967, 7, 1, 1.790),
	(10968, 7, 2, 4.475),
	(10969, 7, 3, 8.503),
	(10970, 7, 4, 14.544),
	(10971, 7, 5, 23.606),
	(10972, 7, 6, 37.198),
	(10973, 7, 7, 57.588),
	(10974, 7, 8, 88.171),
	(10975, 7, 9, 134.047),
	(10976, 7, 10, 202.861),
	(10977, 7, 11, 306.081),
	(10978, 7, 12, 460.912),
	(10979, 7, 13, 693.158),
	(10980, 7, 14, 1041.527),
	(10981, 7, 15, 1564.080),
	(10982, 7, 16, 2347.910),
	(10983, 7, 17, 3523.655),
	(10984, 7, 18, 5287.273),
	(10985, 7, 19, 7932.699),
	(10986, 7, 20, 11900.839),
	(10987, 7, 21, 17853.049),
	(10988, 7, 22, 26781.363),
	(10989, 7, 23, 40173.834),
	(10990, 7, 24, 60262.542),
	(10991, 7, 25, 90395.602),
	(10992, 7, 26, 135595.194),
	(10993, 7, 27, 203394.581),
	(10994, 7, 28, 305093.661),
	(10995, 7, 29, 457642.281),
	(10996, 7, 30, 686465.212),
	(10997, 7, 31, 1029699.608),
	(10998, 7, 32, 1544551.202),
	(10999, 7, 33, 2316828.593),
	(11000, 7, 34, 3475244.680),
	(11001, 7, 35, 5212868.810),
	(11002, 7, 36, 7819305.004),
	(11003, 7, 37, 11728959.297),
	(11004, 7, 38, 17593440.735),
	(11005, 7, 39, 26390162.893),
	(11006, 7, 40, 39585246.129),
	(11007, 7, 41, 59377870.983),
	(11008, 7, 42, 89066808.265),
	(11009, 7, 43, 133600214.188),
	(11010, 7, 44, 200400323.072),
	(11011, 7, 45, 300600486.397),
	(11012, 7, 46, 450900731.386),
	(11013, 7, 47, 676351098.869),
	(11014, 7, 48, 1014526650.094),
	(11015, 7, 49, 1521789976.931),
	(11016, 7, 50, 2282684967.186),
	(11017, 8, 1, 1.800),
	(11018, 8, 2, 5.040),
	(11019, 8, 3, 10.872),
	(11020, 8, 4, 21.370),
	(11021, 8, 5, 40.265),
	(11022, 8, 6, 74.278),
	(11023, 8, 7, 135.500),
	(11024, 8, 8, 245.699),
	(11025, 8, 9, 444.058),
	(11026, 8, 10, 801.105),
	(11027, 8, 11, 1443.789),
	(11028, 8, 12, 2600.621),
	(11029, 8, 13, 4682.917),
	(11030, 8, 14, 8431.051),
	(11031, 8, 15, 15177.691),
	(11032, 8, 16, 27321.644),
	(11033, 8, 17, 49180.760),
	(11034, 8, 18, 88527.168),
	(11035, 8, 19, 159350.703),
	(11036, 8, 20, 286833.065),
	(11037, 8, 21, 516301.317),
	(11038, 8, 22, 929344.170),
	(11039, 8, 23, 1672821.306),
	(11040, 8, 24, 3011080.151),
	(11041, 8, 25, 5419946.072),
	(11042, 8, 26, 9755904.730),
	(11043, 8, 27, 17560630.315),
	(11044, 8, 28, 31609136.367),
	(11045, 8, 29, 56896447.260),
	(11046, 8, 30, 102413606.868),
	(11047, 8, 31, 184344494.162),
	(11048, 8, 32, 331820091.292),
	(11049, 8, 33, 597276166.125),
	(11050, 8, 34, 1075097100.825),
	(11051, 8, 35, 1935174783.285),
	(11052, 8, 36, 3483314611.713),
	(11053, 8, 37, 6269966302.882),
	(11054, 8, 38, 11285939346.988),
	(11055, 8, 39, 20314690826.379),
	(11056, 8, 40, 36566443489.282),
	(11057, 8, 41, 65819598282.508),
	(11058, 8, 42, 118475276910.310),
	(11059, 8, 43, 213255498440.370),
	(11060, 8, 44, 383859897194.460),
	(11061, 8, 45, 690947814951.830),
	(11062, 8, 46, 1243706066915.100),
	(11063, 8, 47, 2238670920449.000),
	(11064, 8, 48, 4029607656809.900),
	(11065, 8, 49, 7253293782259.700),
	(11066, 8, 50, 13055928808069.000),
	(11067, 9, 1, 1.740),
	(11068, 9, 2, 4.872),
	(11069, 9, 3, 10.510),
	(11070, 9, 4, 20.657),
	(11071, 9, 5, 38.923),
	(11072, 9, 6, 71.802),
	(11073, 9, 7, 130.983),
	(11074, 9, 8, 237.509),
	(11075, 9, 9, 429.256),
	(11076, 9, 10, 774.402),
	(11077, 9, 11, 1395.663),
	(11078, 9, 12, 2513.933),
	(11079, 9, 13, 4526.820),
	(11080, 9, 14, 8150.016),
	(11081, 9, 15, 14671.768),
	(11082, 9, 16, 26410.923),
	(11083, 9, 17, 47541.401),
	(11084, 9, 18, 85576.263),
	(11085, 9, 19, 154039.013),
	(11086, 9, 20, 277271.963),
	(11087, 9, 21, 499091.273),
	(11088, 9, 22, 898366.031),
	(11089, 9, 23, 1617060.596),
	(11090, 9, 24, 2910710.813),
	(11091, 9, 25, 5239281.203),
	(11092, 9, 26, 9430707.906),
	(11093, 9, 27, 16975275.971),
	(11094, 9, 28, 30555498.488),
	(11095, 9, 29, 54999899.018),
	(11096, 9, 30, 98999819.972),
	(11097, 9, 31, 178199677.690),
	(11098, 9, 32, 320759421.582),
	(11099, 9, 33, 577366960.587),
	(11100, 9, 34, 1039260530.797),
	(11101, 9, 35, 1870668957.175),
	(11102, 9, 36, 3367204124.655),
	(11103, 9, 37, 6060967426.120),
	(11104, 9, 38, 10909741368.755),
	(11105, 9, 39, 19637534465.500),
	(11106, 9, 40, 35347562039.640),
	(11107, 9, 41, 63625611673.091),
	(11108, 9, 42, 114526101013.300),
	(11109, 9, 43, 206146981825.690),
	(11110, 9, 44, 371064567287.980),
	(11111, 9, 45, 667916221120.100),
	(11112, 9, 46, 1202249198017.900),
	(11113, 9, 47, 2164048556434.000),
	(11114, 9, 48, 3895287401582.900),
	(11115, 9, 49, 7011517322851.000),
	(11116, 9, 50, 12620731181134.000),
	(11117, 10, 1, 1.820),
	(11118, 10, 2, 5.096),
	(11119, 10, 3, 10.993),
	(11120, 10, 4, 21.607),
	(11121, 10, 5, 40.713),
	(11122, 10, 6, 75.103),
	(11123, 10, 7, 137.005),
	(11124, 10, 8, 248.429),
	(11125, 10, 9, 448.992),
	(11126, 10, 10, 810.006),
	(11127, 10, 11, 1459.831),
	(11128, 10, 12, 2629.516),
	(11129, 10, 13, 4734.950),
	(11130, 10, 14, 8524.729),
	(11131, 10, 15, 15346.332),
	(11132, 10, 16, 27625.218),
	(11133, 10, 17, 49727.213),
	(11134, 10, 18, 89510.803),
	(11135, 10, 19, 161121.266),
	(11136, 10, 20, 290020.099),
	(11137, 10, 21, 522037.998),
	(11138, 10, 22, 939670.217),
	(11139, 10, 23, 1691408.210),
	(11140, 10, 24, 3044536.597),
	(11141, 10, 25, 5480167.695),
	(11142, 10, 26, 9864303.672),
	(11143, 10, 27, 17755748.429),
	(11144, 10, 28, 31960348.993),
	(11145, 10, 29, 57528630.007),
	(11146, 10, 30, 103551535.833),
	(11147, 10, 31, 186392766.319),
	(11148, 10, 32, 335506981.195),
	(11149, 10, 33, 603912567.971),
	(11150, 10, 34, 1087042624.167),
	(11151, 10, 35, 1956676725.321),
	(11152, 10, 36, 3522018107.398),
	(11153, 10, 37, 6339632595.137),
	(11154, 10, 38, 11411338673.066),
	(11155, 10, 39, 20540409613.339),
	(11156, 10, 40, 36972737305.830),
	(11157, 10, 41, 66550927152.314),
	(11158, 10, 42, 119791668875.990),
	(11159, 10, 43, 215625003978.590),
	(11160, 10, 44, 388125007163.290),
	(11161, 10, 45, 698625012895.740),
	(11162, 10, 46, 1257525023214.100),
	(11163, 10, 47, 2263545041787.300),
	(11164, 10, 48, 4074381075218.900),
	(11165, 10, 49, 7333885935395.900),
	(11166, 10, 50, 13200994683714.000),
	(11167, 11, 1, 3.300),
	(11168, 11, 2, 8.250),
	(11169, 11, 3, 15.675),
	(11170, 11, 4, 26.813),
	(11171, 11, 5, 43.519),
	(11172, 11, 6, 68.578),
	(11173, 11, 7, 106.167),
	(11174, 11, 8, 162.551),
	(11175, 11, 9, 247.126),
	(11176, 11, 10, 373.989),
	(11177, 11, 11, 564.284),
	(11178, 11, 12, 849.726),
	(11179, 11, 13, 1277.889),
	(11180, 11, 14, 1920.133),
	(11181, 11, 15, 2883.500),
	(11182, 11, 16, 4328.550),
	(11183, 11, 17, 6496.124),
	(11184, 11, 18, 9747.486),
	(11185, 11, 19, 14624.530),
	(11186, 11, 20, 21940.094),
	(11187, 11, 21, 32913.442),
	(11188, 11, 22, 49373.462),
	(11189, 11, 23, 74063.494),
	(11190, 11, 24, 111098.540),
	(11191, 11, 25, 166651.111),
	(11192, 11, 26, 249979.966),
	(11193, 11, 27, 374973.249),
	(11194, 11, 28, 562463.174),
	(11195, 11, 29, 843698.061),
	(11196, 11, 30, 1265550.391),
	(11197, 11, 31, 1898328.886),
	(11198, 11, 32, 2847496.630),
	(11199, 11, 33, 4271248.244),
	(11200, 11, 34, 6406875.667),
	(11201, 11, 35, 9610316.800),
	(11202, 11, 36, 14415478.500),
	(11203, 11, 37, 21623221.050),
	(11204, 11, 38, 32434834.875),
	(11205, 11, 39, 48652255.612),
	(11206, 11, 40, 72978386.718),
	(11207, 11, 41, 109467583.377),
	(11208, 11, 42, 164201378.366),
	(11209, 11, 43, 246302070.849),
	(11210, 11, 44, 369453109.573),
	(11211, 11, 45, 554179667.660),
	(11212, 11, 46, 831269504.790),
	(11213, 11, 47, 1246904260.485),
	(11214, 11, 48, 1870356394.028),
	(11215, 11, 49, 2805534594.342),
	(11216, 11, 50, 4208301894.813),
	(11217, 12, 1, 21.000),
	(11218, 12, 2, 60.900),
	(11219, 12, 3, 136.710),
	(11220, 12, 4, 280.749),
	(11221, 12, 5, 554.423),
	(11222, 12, 6, 1074.404),
	(11223, 12, 7, 2062.367),
	(11224, 12, 8, 3939.498),
	(11225, 12, 9, 7506.046),
	(11226, 12, 10, 14282.488),
	(11227, 12, 11, 27157.727),
	(11228, 12, 12, 51620.681),
	(11229, 12, 13, 98100.295),
	(11230, 12, 14, 186411.560),
	(11231, 12, 15, 354202.964),
	(11232, 12, 16, 673006.632),
	(11233, 12, 17, 1278733.600),
	(11234, 12, 18, 2429614.840),
	(11235, 12, 19, 4616289.197),
	(11236, 12, 20, 8770970.473),
	(11237, 12, 21, 16664864.900),
	(11238, 12, 22, 31663264.309),
	(11239, 12, 23, 60160223.187),
	(11240, 12, 24, 114304445.056),
	(11241, 12, 25, 217178466.606),
	(11242, 12, 26, 412639107.551),
	(11243, 12, 27, 784014325.347),
	(11244, 12, 28, 1489627239.160),
	(11245, 12, 29, 2830291775.404),
	(11246, 12, 30, 5377554394.268),
	(11247, 12, 31, 10217353370.109),
	(11248, 12, 32, 19412971424.207),
	(11249, 12, 33, 36884645726.993),
	(11250, 12, 34, 70080826902.287),
	(11251, 12, 35, 133153571135.350),
	(11252, 12, 36, 252991785178.160),
	(11253, 12, 37, 480684391859.500),
	(11254, 12, 38, 913300344554.050),
	(11255, 12, 39, 1735270654673.700),
	(11256, 12, 40, 3297014243901.000),
	(11257, 12, 41, 6264327063432.900),
	(11258, 12, 42, 11902221420544.000),
	(11259, 12, 43, 22614220699054.000),
	(11260, 12, 44, 42967019328223.000),
	(11261, 12, 45, 81637336723645.000),
	(11262, 12, 46, 155110939774950.000),
	(11263, 12, 47, 294710785572420.000),
	(11264, 12, 48, 559950492587620.000),
	(11265, 12, 49, 1063905935916500.000),
	(11266, 12, 50, 2021421278241400.000),
	(11267, 13, 1, 27.000),
	(11268, 13, 2, 78.300),
	(11269, 13, 3, 175.770),
	(11270, 13, 4, 360.963),
	(11271, 13, 5, 712.830),
	(11272, 13, 6, 1381.376),
	(11273, 13, 7, 2651.615),
	(11274, 13, 8, 5065.069),
	(11275, 13, 9, 9650.631),
	(11276, 13, 10, 18363.199),
	(11277, 13, 11, 34917.078),
	(11278, 13, 12, 66369.448),
	(11279, 13, 13, 126128.950),
	(11280, 13, 14, 239672.006),
	(11281, 13, 15, 455403.811),
	(11282, 13, 16, 865294.241),
	(11283, 13, 17, 1644086.057),
	(11284, 13, 18, 3123790.509),
	(11285, 13, 19, 5935228.967),
	(11286, 13, 20, 11276962.037),
	(11287, 13, 21, 21426254.871),
	(11288, 13, 22, 40709911.255),
	(11289, 13, 23, 77348858.384),
	(11290, 13, 24, 146962857.929),
	(11291, 13, 25, 279229457.065),
	(11292, 13, 26, 530535995.423),
	(11293, 13, 27, 1008018418.304),
	(11294, 13, 28, 1915235021.777),
	(11295, 13, 29, 3638946568.377),
	(11296, 13, 30, 6913998506.916),
	(11297, 13, 31, 13136597190.140),
	(11298, 13, 32, 24959534688.266),
	(11299, 13, 33, 47423115934.706),
	(11300, 13, 34, 90103920302.941),
	(11301, 13, 35, 171197448602.590),
	(11302, 13, 36, 325275152371.920),
	(11303, 13, 37, 618022789533.640),
	(11304, 13, 38, 1174243300140.900),
	(11305, 13, 39, 2231062270294.700),
	(11306, 13, 40, 4239018313587.000),
	(11307, 13, 41, 8054134795842.300),
	(11308, 13, 42, 15302856112127.000),
	(11309, 13, 43, 29075426613069.000),
	(11310, 13, 44, 55243310564858.000),
	(11311, 13, 45, 104962290073260.000),
	(11312, 13, 46, 199428351139220.000),
	(11313, 13, 47, 378913867164540.000),
	(11314, 13, 48, 719936347612650.000),
	(11315, 13, 49, 1367879060464100.000),
	(11316, 13, 50, 2598970214881800.000),
	(11317, 14, 1, 63.073),
	(11318, 14, 2, 145.068),
	(11319, 14, 3, 251.661),
	(11320, 14, 4, 390.233),
	(11321, 14, 5, 570.375),
	(11322, 14, 6, 804.561),
	(11323, 14, 7, 1109.002),
	(11324, 14, 8, 1504.776),
	(11325, 14, 9, 2019.282),
	(11326, 14, 10, 2688.140),
	(11327, 14, 11, 3557.654),
	(11328, 14, 12, 4688.024),
	(11329, 14, 13, 6157.504),
	(11330, 14, 14, 8067.828),
	(11331, 14, 15, 10551.249),
	(11332, 14, 16, 13779.697),
	(11333, 14, 17, 17976.679),
	(11334, 14, 18, 23432.756),
	(11335, 14, 19, 30525.656),
	(11336, 14, 20, 39746.426),
	(11337, 14, 21, 51733.427),
	(11338, 14, 22, 67316.528),
	(11339, 14, 23, 87574.559),
	(11340, 14, 24, 113910.000),
	(11341, 14, 25, 148146.073),
	(11342, 14, 26, 192652.968),
	(11343, 14, 27, 250511.931),
	(11344, 14, 28, 325728.584),
	(11345, 14, 29, 423510.232),
	(11346, 14, 30, 550626.374),
	(11347, 14, 31, 715877.359),
	(11348, 14, 32, 930703.640),
	(11349, 14, 33, 1209977.805),
	(11350, 14, 34, 1573034.220),
	(11351, 14, 35, 2045007.559),
	(11352, 14, 36, 2658572.899),
	(11353, 14, 37, 3456207.842),
	(11354, 14, 38, 4493133.268),
	(11355, 14, 39, 5841136.321),
	(11356, 14, 40, 7593540.290),
	(11357, 14, 41, 9871665.450),
	(11358, 14, 42, 12833228.158),
	(11359, 14, 43, 16683259.679),
	(11360, 14, 44, 21688300.656),
	(11361, 14, 45, 28194853.925),
	(11362, 14, 46, 36653373.176),
	(11363, 14, 47, 47649448.201),
	(11364, 14, 48, 61944345.735),
	(11365, 14, 49, 80527712.528),
	(11366, 14, 50, 104686089.360),
	(11367, 15, 1, 60.000),
	(11368, 15, 2, 165.000),
	(11369, 15, 3, 348.750),
	(11370, 15, 4, 670.313),
	(11371, 15, 5, 1233.047),
	(11372, 15, 6, 2217.832),
	(11373, 15, 7, 3941.206),
	(11374, 15, 8, 6957.111),
	(11375, 15, 9, 12234.944),
	(11376, 15, 10, 21471.151),
	(11377, 15, 11, 37634.515),
	(11378, 15, 12, 65920.401),
	(11379, 15, 13, 115420.701),
	(11380, 15, 14, 202046.227),
	(11381, 15, 15, 353640.897),
	(11382, 15, 16, 618931.569),
	(11383, 15, 17, 1083190.246),
	(11384, 15, 18, 1895642.931),
	(11385, 15, 19, 3317435.129),
	(11386, 15, 20, 5805571.475),
	(11387, 15, 21, 10159810.082),
	(11388, 15, 22, 17779727.643),
	(11389, 15, 23, 31114583.375),
	(11390, 15, 24, 54450580.906),
	(11391, 15, 25, 95288576.586),
	(11392, 15, 26, 166755069.025),
	(11393, 15, 27, 291821430.794),
	(11394, 15, 28, 510687563.890),
	(11395, 15, 29, 893703296.807),
	(11396, 15, 30, 1563980829.412),
	(11397, 15, 31, 2736966511.471),
	(11398, 15, 32, 4789691455.074),
	(11399, 15, 33, 8381960106.379),
	(11400, 15, 34, 14668430246.163),
	(11401, 15, 35, 25669752990.784),
	(11402, 15, 36, 44922067793.873),
	(11403, 15, 37, 78613618699.277),
	(11404, 15, 38, 137573832783.740),
	(11405, 15, 39, 240754207431.540),
	(11406, 15, 40, 421319863065.190),
	(11407, 15, 41, 737309760424.080),
	(11408, 15, 42, 1290292080802.100),
	(11409, 15, 43, 2258011141463.800),
	(11410, 15, 44, 3951519497621.600),
	(11411, 15, 45, 6915159120897.700),
	(11412, 15, 46, 12101528461631.000),
	(11413, 15, 47, 21177674807914.000),
	(11414, 15, 48, 37060930913910.000),
	(11415, 15, 49, 64856629099403.000),
	(11416, 15, 50, 113499100924010.000),
	(11417, 16, 1, 5.000),
	(11418, 16, 2, 12.500),
	(11419, 16, 3, 23.750),
	(11420, 16, 4, 40.625),
	(11421, 16, 5, 65.938),
	(11422, 16, 6, 103.906),
	(11423, 16, 7, 160.859),
	(11424, 16, 8, 246.289),
	(11425, 16, 9, 374.434),
	(11426, 16, 10, 566.650),
	(11427, 16, 11, 854.976),
	(11428, 16, 12, 1287.463),
	(11429, 16, 13, 1936.195),
	(11430, 16, 14, 2909.293),
	(11431, 16, 15, 4368.939),
	(11432, 16, 16, 6558.408),
	(11433, 16, 17, 9842.613),
	(11434, 16, 18, 14768.919),
	(11435, 16, 19, 22158.378),
	(11436, 16, 20, 33242.567),
	(11437, 16, 21, 49868.851),
	(11438, 16, 22, 74808.276),
	(11439, 16, 23, 112217.415),
	(11440, 16, 24, 168331.122),
	(11441, 16, 25, 252501.683),
	(11442, 16, 26, 378757.524),
	(11443, 16, 27, 568141.287),
	(11444, 16, 28, 852216.930),
	(11445, 16, 29, 1278330.395),
	(11446, 16, 30, 1917500.592),
	(11447, 16, 31, 2876255.888),
	(11448, 16, 32, 4314388.833),
	(11449, 16, 33, 6471588.249),
	(11450, 16, 34, 9707387.374),
	(11451, 16, 35, 14561086.060),
	(11452, 16, 36, 21841634.091),
	(11453, 16, 37, 32762456.136),
	(11454, 16, 38, 49143689.204),
	(11455, 16, 39, 73715538.806),
	(11456, 16, 40, 110573313.209),
	(11457, 16, 41, 165859974.814),
	(11458, 16, 42, 248789967.221),
	(11459, 16, 43, 373184955.832),
	(11460, 16, 44, 559777438.748),
	(11461, 16, 45, 839666163.121),
	(11462, 16, 46, 1259499249.682),
	(11463, 16, 47, 1889248879.523),
	(11464, 16, 48, 2833873324.285),
	(11465, 16, 49, 4250809991.427),
	(11466, 16, 50, 6376214992.141),
	(11467, 17, 1, 55.000),
	(11468, 17, 2, 148.500),
	(11469, 17, 3, 307.450),
	(11470, 17, 4, 577.665),
	(11471, 17, 5, 1037.031),
	(11472, 17, 6, 1817.952),
	(11473, 17, 7, 3145.518),
	(11474, 17, 8, 5402.381),
	(11475, 17, 9, 9239.047),
	(11476, 17, 10, 15761.381),
	(11477, 17, 11, 26849.347),
	(11478, 17, 12, 45698.890),
	(11479, 17, 13, 77743.113),
	(11480, 17, 14, 132218.292),
	(11481, 17, 15, 224826.097),
	(11482, 17, 16, 382259.365),
	(11483, 17, 17, 649895.920),
	(11484, 17, 18, 1104878.064),
	(11485, 17, 19, 1878347.709),
	(11486, 17, 20, 3193246.105),
	(11487, 17, 21, 5428573.379),
	(11488, 17, 22, 9228629.744),
	(11489, 17, 23, 15688725.565),
	(11490, 17, 24, 26670888.460),
	(11491, 17, 25, 45340565.383),
	(11492, 17, 26, 77079016.151),
	(11493, 17, 27, 131034382.456),
	(11494, 17, 28, 222758505.175),
	(11495, 17, 29, 378689513.798),
	(11496, 17, 30, 643772228.457),
	(11497, 17, 31, 1094412843.377),
	(11498, 17, 32, 1860501888.740),
	(11499, 17, 33, 3162853265.859),
	(11500, 17, 34, 5376850606.960),
	(11501, 17, 35, 9140646086.831),
	(11502, 17, 36, 15539098402.613),
	(11503, 17, 37, 26416467339.443),
	(11504, 17, 38, 44907994532.053),
	(11505, 17, 39, 76343590759.490),
	(11506, 17, 40, 129784104346.130),
	(11507, 17, 41, 220632977443.430),
	(11508, 17, 42, 375076061708.820),
	(11509, 17, 43, 637629304960.000),
	(11510, 17, 44, 1083969818487.000),
	(11511, 17, 45, 1842748691482.900),
	(11512, 17, 46, 3132672775575.900),
	(11513, 17, 47, 5325543718534.100),
	(11514, 17, 48, 9053424321562.900),
	(11515, 17, 49, 15390821346712.000),
	(11516, 17, 50, 26164396289465.000),
	(11517, 18, 1, 45.000),
	(11518, 18, 2, 112.500),
	(11519, 18, 3, 213.750),
	(11520, 18, 4, 365.625),
	(11521, 18, 5, 593.438),
	(11522, 18, 6, 935.156),
	(11523, 18, 7, 1447.734),
	(11524, 18, 8, 2216.602),
	(11525, 18, 9, 3369.902),
	(11526, 18, 10, 5099.854),
	(11527, 18, 11, 7694.780),
	(11528, 18, 12, 11587.170),
	(11529, 18, 13, 17425.756),
	(11530, 18, 14, 26183.633),
	(11531, 18, 15, 39320.450),
	(11532, 18, 16, 59025.675),
	(11533, 18, 17, 88583.513),
	(11534, 18, 18, 132920.269),
	(11535, 18, 19, 199425.404),
	(11536, 18, 20, 299183.106),
	(11537, 18, 21, 448819.659),
	(11538, 18, 22, 673274.488),
	(11539, 18, 23, 1009956.732),
	(11540, 18, 24, 1514980.098),
	(11541, 18, 25, 2272515.146),
	(11542, 18, 26, 3408817.720),
	(11543, 18, 27, 5113271.580),
	(11544, 18, 28, 7669952.369),
	(11545, 18, 29, 11504973.554),
	(11546, 18, 30, 17257505.331),
	(11547, 18, 31, 25886302.996),
	(11548, 18, 32, 38829499.495),
	(11549, 18, 33, 58244294.242),
	(11550, 18, 34, 87366486.363),
	(11551, 18, 35, 131049774.544),
	(11552, 18, 36, 196574706.817),
	(11553, 18, 37, 294862105.225),
	(11554, 18, 38, 442293202.838),
	(11555, 18, 39, 663439849.256),
	(11556, 18, 40, 995159818.885),
	(11557, 18, 41, 1492739773.327),
	(11558, 18, 42, 2239109704.990),
	(11559, 18, 43, 3358664602.486),
	(11560, 18, 44, 5037996948.728),
	(11561, 18, 45, 7556995468.092),
	(11562, 18, 46, 11335493247.139),
	(11563, 18, 47, 17003239915.708),
	(11564, 18, 48, 25504859918.562),
	(11565, 18, 49, 38257289922.843),
	(11566, 18, 50, 57385934929.264),
	(11567, 19, 1, 1.800),
	(11568, 19, 2, 5.040),
	(11569, 19, 3, 10.872),
	(11570, 19, 4, 21.370),
	(11571, 19, 5, 40.265),
	(11572, 19, 6, 74.278),
	(11573, 19, 7, 135.500),
	(11574, 19, 8, 245.699),
	(11575, 19, 9, 444.058),
	(11576, 19, 10, 801.105),
	(11577, 19, 11, 1443.789),
	(11578, 19, 12, 2600.621),
	(11579, 19, 13, 4682.917),
	(11580, 19, 14, 8431.051),
	(11581, 19, 15, 15177.691),
	(11582, 19, 16, 27321.644),
	(11583, 19, 17, 49180.760),
	(11584, 19, 18, 88527.168),
	(11585, 19, 19, 159350.703),
	(11586, 19, 20, 286833.065),
	(11587, 19, 21, 516301.317),
	(11588, 19, 22, 929344.170),
	(11589, 19, 23, 1672821.306),
	(11590, 19, 24, 3011080.151),
	(11591, 19, 25, 5419946.072),
	(11592, 19, 26, 9755904.730),
	(11593, 19, 27, 17560630.315),
	(11594, 19, 28, 31609136.367),
	(11595, 19, 29, 56896447.260),
	(11596, 19, 30, 102413606.868),
	(11597, 19, 31, 184344494.162),
	(11598, 19, 32, 331820091.292),
	(11599, 19, 33, 597276166.125),
	(11600, 19, 34, 1075097100.825),
	(11601, 19, 35, 1935174783.285),
	(11602, 19, 36, 3483314611.713),
	(11603, 19, 37, 6269966302.882),
	(11604, 19, 38, 11285939346.988),
	(11605, 19, 39, 20314690826.379),
	(11606, 19, 40, 36566443489.282),
	(11607, 19, 41, 65819598282.508),
	(11608, 19, 42, 118475276910.310),
	(11609, 19, 43, 213255498440.370),
	(11610, 19, 44, 383859897194.460),
	(11611, 19, 45, 690947814951.830),
	(11612, 19, 46, 1243706066915.100),
	(11613, 19, 47, 2238670920449.000),
	(11614, 19, 48, 4029607656809.900),
	(11615, 19, 49, 7253293782259.700),
	(11616, 19, 50, 13055928808069.000),
	(11617, 20, 1, 18.000),
	(11618, 20, 2, 48.600),
	(11619, 20, 3, 100.620),
	(11620, 20, 4, 189.054),
	(11621, 20, 5, 339.392),
	(11622, 20, 6, 594.966),
	(11623, 20, 7, 1029.442),
	(11624, 20, 8, 1768.052),
	(11625, 20, 9, 3023.688),
	(11626, 20, 10, 5158.270),
	(11627, 20, 11, 8787.059),
	(11628, 20, 12, 14956.000),
	(11629, 20, 13, 25443.201),
	(11630, 20, 14, 43271.441),
	(11631, 20, 15, 73579.450),
	(11632, 20, 16, 125103.065),
	(11633, 20, 17, 212693.210),
	(11634, 20, 18, 361596.457),
	(11635, 20, 19, 614731.977),
	(11636, 20, 20, 1045062.362),
	(11637, 20, 21, 1776624.015),
	(11638, 20, 22, 3020278.825),
	(11639, 20, 23, 5134492.003),
	(11640, 20, 24, 8728654.405),
	(11641, 20, 25, 14838730.489),
	(11642, 20, 26, 25225859.831),
	(11643, 20, 27, 42883979.713),
	(11644, 20, 28, 72902783.512),
	(11645, 20, 29, 123934749.970),
	(11646, 20, 30, 210689092.950),
	(11647, 20, 31, 358171476.014),
	(11648, 20, 32, 608891527.224),
	(11649, 20, 33, 1035115614.281),
	(11650, 20, 34, 1759696562.278),
	(11651, 20, 35, 2991484173.872),
	(11652, 20, 36, 5085523113.583),
	(11653, 20, 37, 8645389311.090),
	(11654, 20, 38, 14697161846.854),
	(11655, 20, 39, 24985175157.651),
	(11656, 20, 40, 42474797786.007),
	(11657, 20, 41, 72207156254.212),
	(11658, 20, 42, 122752165650.160),
	(11659, 20, 43, 208678681623.270),
	(11660, 20, 44, 354753758777.560),
	(11661, 20, 45, 603081389939.860),
	(11662, 20, 46, 1025238362915.800),
	(11663, 20, 47, 1742905216974.800),
	(11664, 20, 48, 2962938868875.100),
	(11665, 20, 49, 5036996077105.700),
	(11666, 20, 50, 8562893331097.700),
	(11667, 21, 1, 4.800),
	(11668, 21, 2, 13.440),
	(11669, 21, 3, 28.992),
	(11670, 21, 4, 56.986),
	(11671, 21, 5, 107.374),
	(11672, 21, 6, 198.073),
	(11673, 21, 7, 361.332),
	(11674, 21, 8, 655.198),
	(11675, 21, 9, 1184.156),
	(11676, 21, 10, 2136.280),
	(11677, 21, 11, 3850.105),
	(11678, 21, 12, 6934.988),
	(11679, 21, 13, 12487.779),
	(11680, 21, 14, 22482.802),
	(11681, 21, 15, 40473.844),
	(11682, 21, 16, 72857.719),
	(11683, 21, 17, 131148.694),
	(11684, 21, 18, 236072.448),
	(11685, 21, 19, 424935.207),
	(11686, 21, 20, 764888.173),
	(11687, 21, 21, 1376803.511),
	(11688, 21, 22, 2478251.120),
	(11689, 21, 23, 4460856.817),
	(11690, 21, 24, 8029547.070),
	(11691, 21, 25, 14453189.527),
	(11692, 21, 26, 26015745.948),
	(11693, 21, 27, 46828347.506),
	(11694, 21, 28, 84291030.311),
	(11695, 21, 29, 151723859.360),
	(11696, 21, 30, 273102951.647),
	(11697, 21, 31, 491585317.765),
	(11698, 21, 32, 884853576.778),
	(11699, 21, 33, 1592736443.000),
	(11700, 21, 34, 2866925602.200),
	(11701, 21, 35, 5160466088.759),
	(11702, 21, 36, 9288838964.567),
	(11703, 21, 37, 16719910141.020),
	(11704, 21, 38, 30095838258.636),
	(11705, 21, 39, 54172508870.344),
	(11706, 21, 40, 97510515971.420),
	(11707, 21, 41, 175518928753.360),
	(11708, 21, 42, 315934071760.840),
	(11709, 21, 43, 568681329174.310),
	(11710, 21, 44, 1023626392518.600),
	(11711, 21, 45, 1842527506538.200),
	(11712, 21, 46, 3316549511773.600),
	(11713, 21, 47, 5969789121197.200),
	(11714, 21, 48, 10745620418160.000),
	(11715, 21, 49, 19342116752692.000),
	(11716, 21, 50, 34815810154851.000),
	(11717, 22, 1, 935.000),
	(11718, 23, 1, 245100.000),
	(11719, 23, 2, 735300.000),
	(11720, 23, 3, 1715700.000),
	(11721, 23, 4, 3676500.000),
	(11722, 23, 5, 7598100.000),
	(11723, 23, 6, 15441300.000),
	(11724, 23, 7, 31127700.000),
	(11725, 23, 8, 62500500.000),
	(11726, 24, 1, 510.000),
	(11727, 24, 2, 1122.000),
	(11728, 24, 3, 1856.400),
	(11729, 24, 4, 2737.680),
	(11730, 24, 5, 3795.216),
	(11731, 24, 6, 5064.259),
	(11732, 24, 7, 6587.111),
	(11733, 24, 8, 8414.533),
	(11734, 24, 9, 10607.440),
	(11735, 24, 10, 13238.928),
	(11736, 25, 1, 8.500),
	(11737, 25, 2, 21.250),
	(11738, 25, 3, 40.375),
	(11739, 25, 4, 69.063),
	(11740, 25, 5, 112.094),
	(11741, 25, 6, 176.641),
	(11742, 25, 7, 273.461),
	(11743, 25, 8, 418.691),
	(11744, 25, 9, 636.537),
	(11745, 25, 10, 963.306),
	(11746, 25, 11, 1453.458),
	(11747, 25, 12, 2188.688),
	(11748, 25, 13, 3291.532),
	(11749, 25, 14, 4945.797),
	(11750, 25, 15, 7427.196),
	(11751, 25, 16, 11149.294),
	(11752, 25, 17, 16732.441),
	(11753, 25, 18, 25107.162),
	(11754, 25, 19, 37669.243),
	(11755, 25, 20, 56512.364),
	(11756, 25, 21, 84777.047),
	(11757, 25, 22, 127174.070),
	(11758, 25, 23, 190769.605),
	(11759, 25, 24, 286162.907),
	(11760, 25, 25, 429252.861),
	(11761, 25, 26, 643887.791),
	(11762, 25, 27, 965840.187),
	(11763, 25, 28, 1448768.781),
	(11764, 25, 29, 2173161.671),
	(11765, 25, 30, 3259751.007),
	(11766, 25, 31, 4889635.010),
	(11767, 25, 32, 7334461.016),
	(11768, 25, 33, 11001700.023),
	(11769, 25, 34, 16502558.535),
	(11770, 25, 35, 24753846.303),
	(11771, 25, 36, 37130777.954),
	(11772, 25, 37, 55696175.431),
	(11773, 25, 38, 83544271.647),
	(11774, 25, 39, 125316415.971),
	(11775, 25, 40, 187974632.456),
	(11776, 25, 41, 281961957.184),
	(11777, 25, 42, 422942944.276),
	(11778, 25, 43, 634414424.914),
	(11779, 25, 44, 951621645.871),
	(11780, 25, 45, 1427432477.306),
	(11781, 25, 46, 2141148724.460),
	(11782, 25, 47, 3211723095.189),
	(11783, 25, 48, 4817584651.284),
	(11784, 25, 49, 7226376985.426),
	(11785, 25, 50, 10839565486.639);
/*!40000 ALTER TABLE `tech_points` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.tech_requirements
DROP TABLE IF EXISTS `tech_requirements`;
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

-- Exportiere Daten aus Tabelle etoa_test.tech_requirements: ~42 rows (ungefähr)
DELETE FROM `tech_requirements`;
INSERT INTO `tech_requirements` (`id`, `obj_id`, `req_building_id`, `req_tech_id`, `req_level`) VALUES
	(2, 4, 8, NULL, 4),
	(3, 4, 9, NULL, 2),
	(4, 5, 8, NULL, 5),
	(5, 5, 9, NULL, 4),
	(6, 6, 8, NULL, 8),
	(7, 6, 11, NULL, 6),
	(8, 6, 9, NULL, 6),
	(11, 9, 8, NULL, 4),
	(12, 10, 8, NULL, 4),
	(17, 12, 8, NULL, 7),
	(18, 12, NULL, 3, 5),
	(19, 13, 11, NULL, 4),
	(20, 13, NULL, 5, 2),
	(21, 14, NULL, 4, 6),
	(22, 14, 8, NULL, 6),
	(23, 11, 8, NULL, 5),
	(24, 11, NULL, 7, 6),
	(25, 15, 8, NULL, 8),
	(26, 17, 8, NULL, 8),
	(27, 20, 9, NULL, 5),
	(28, 20, 8, NULL, 4),
	(29, 18, 8, NULL, 8),
	(30, 19, 8, NULL, 8),
	(31, 16, 8, NULL, 5),
	(32, 21, 8, NULL, 6),
	(33, 21, 9, NULL, 5),
	(34, 21, NULL, 3, 6),
	(35, 22, 8, NULL, 10),
	(36, 22, NULL, 6, 9),
	(37, 22, NULL, 3, 10),
	(38, 22, NULL, 10, 11),
	(39, 23, 8, NULL, 12),
	(40, 23, 7, NULL, 15),
	(41, 24, 8, NULL, 10),
	(42, 24, NULL, 3, 9),
	(44, 24, NULL, 4, 10),
	(45, 24, NULL, 14, 10),
	(46, 25, NULL, 3, 5),
	(47, 25, 13, NULL, 6),
	(48, 3, 8, NULL, 1),
	(49, 7, 8, NULL, 3),
	(50, 8, 8, NULL, 4);

-- Exportiere Struktur von Tabelle etoa_test.tech_types
DROP TABLE IF EXISTS `tech_types`;
CREATE TABLE IF NOT EXISTS `tech_types` (
  `type_id` int unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL,
  `type_order` smallint unsigned NOT NULL DEFAULT '0',
  `type_color` char(7) NOT NULL DEFAULT '#ffffff',
  PRIMARY KEY (`type_id`),
  KEY `type_name` (`type_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.tech_types: ~0 rows (ungefähr)
DELETE FROM `tech_types`;
INSERT INTO `tech_types` (`type_id`, `type_name`, `type_order`, `type_color`) VALUES
	(1, 'Antriebstechniken', 1, '#ffffff'),
	(2, 'Kriegstechnologien', 2, '#ffffff'),
	(3, 'Hi - Technologien', 3, '#ffffff'),
	(4, 'Allgemeine Technologien', 0, '#ffffff');

-- Exportiere Struktur von Tabelle etoa_test.texts
DROP TABLE IF EXISTS `texts`;
CREATE TABLE IF NOT EXISTS `texts` (
  `text_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `text_content` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `text_updated` int unsigned NOT NULL,
  `text_enabled` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`text_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Exportiere Daten aus Tabelle etoa_test.texts: 0 rows
DELETE FROM `texts`;
/*!40000 ALTER TABLE `texts` DISABLE KEYS */;
INSERT INTO `texts` (`text_id`, `text_content`, `text_updated`, `text_enabled`) VALUES
	('contact_message', 'Bitte bei Problemen im Zusammenhang mit dem Spielablauf (Namenswechsel, Regelverstössen, Cheater/Buguser melden) die Game-Admins kontaktieren (via Supportticket, E-Mail oder PN im Forum).\n\nBei Bugs bitte zuerst im [url https://forum.etoa.ch]Forum[/url] nachschauen, dann erst einen Admin oder Entwickler kontaktieren.\n\nIn dringenden Fällen (schwerwiegende Fehler, Sicherheitsprobleme, Systemfehler des Servers) kannst du auch eine E-Mail an die Projektleitung senden ([mailurl]mail@etoa.ch[/mailurl]). Achtung: Belanglose Mails und Spam werden stillschweigend ignoriert!', 1760212393, 1),
	('info', '[size=9][color=black] [/color]\r\n[center][color=#F5ECCE](¯`·.¸¸.->[b]Willkommen, Herrscher:in der Galaxien[/b]<-.¸¸.·´¯)[/color]\r\n[color=#E1F5A9][b]Eure Game-Admins sind:\r\n[/b] BungLer, Eldracor und Wutanfall [/color]\r\n[color=#BCF5A9][b]Euer Chat-Admin ist:[/b] \r\nRaffsack[/color]\r\n[color=#A9F5E1][b]Eure Entwickler sind:\r\n[/b] mrcage, coo1ness (und viele mehr!)[/color]\r\n[color=black] [/color]\r\n\r\n[color=blue][b]Aktuelle Ausgabe der EtoA-Zeitung & Kontaktmöglichkeit[/b][/color]\r\n[color=blue][b][url=https://andromeda-post.ch/]Zum Zeitungsständer[/url][/b][/color]\r\n[color=blue][b][url=https://round24.etoa.net/popup.php?page=userinfo&id=12]Zum Andromeda Post Profil[/url][/b][/color]\r\n\r\n\r\n[color=red][b]Beachtet die mit Runde 21 eingeführten Änderungen der Angriffsregeln![/b][/color]\r\n[color=red][b][url=https://forum.etoa.ch/forum/thread/10700-neue-regel-missbrauch-des-l%C3%B6schmodus-um-angriff-zu-verhindern/]Beachtet die neue Regel 7.1[/url][/b][/color]\r\n\r\n[url=https://forum.etoa.ch/forum/thread/10766-balancing-runde-24/]Balancing Runde 24[/url] - [url=https://round24.etoa.net/?page=ticket]Ticketsystem[/url] - [url=https://round24.etoa.net/chat.php]Handy-Chat[/url] - [url=https://discord.gg/7d2ndEU]Chat auf Discord [/url]\r\n[url=https://kbsim.andromedatools.ch/kbsim/round22]Kampf-Simulator (ACHTUNG: Externer Link!)[/url]\r\n[color=black] [/color]\r\n[color=yellow]♦[/color] [url=https://www.browsergames.fm/escapetoandromeda/][b]Vote für EtoA auf Browsergames [/b][/url] [color=yellow]♦[/color] [url=http://de.mmofacts.com/etoa-escape-to-andromeda-610#track][b] mmo Facts [/b][/url] [color=yellow]♦[/color]\r\n[color=yellow]♦[/color] Dein Klick zählt!\r\n\r\n [/center][/size]', 1765188680, 1),
	('welcome_message', 'Seid gegrüsst, Imperator!\n\nIch beglückwünsche Euch zum Antritt Eurer Regentschaft. Die Zukunft Eurer Rasse liegt nun in Euren Händen. Eure Heimatwelt hat sich soweit entwickelt dass ihre Bewohner sich danach sehnen die Galaxie um sie herum zu erkunden und fremde Welten zu besiedeln.\n\nLinks seht ihr die Navigation, mit der ihr Euer Reich verwalten könnt. Baut zuerst einige Gebäude um Rohstoffe zu fördern. Danach solltet ihr Forschungslabors und Werften errichten, damit ihr Raumschiffe bauen könnt um die Weiten von Andromeda zu erkunden. Bedenkt dass einige Gebäude Energie benötigen, vernachlässigt also den Bau von geeigneten Kraftwerken nicht.\n\nAnsonsten schaut Euch einfach um, zweifellos werdet Ihr Euch rasch zurechtfinden.\n\nWeitere Hilfen und Tipps findet ihr hier:\n\nHilfe: [url ?page=help]Umfangreiche InGame-Hilfe[/url]\nKontakt: [url ?page=contact]Game-Admin kontaktieren[/url]\nForum: [url https://forum.etoa.ch]Offizielles Forum[/url]\nFAQ: [url https://help.etoa.ch/faq]Häufig gestellte Fragen und Antworten dazu[/url]\nWiki: [url https://help.etoa.ch/?page=article]How-To\'s und hilfreiche Artikel[/url]\n\nIch wünsche Euch nun viel Erfolg in der Galaxie von Andromeda. Mögen Euer Imperium gross und Eure Schlachten erfolgreich sein!\n\nAnmerkung: Eine Kopie dieser Nachricht wird in Eurer Nachrichten-Box hinterlegt. Mit dem Schliessen dieser Nachricht leuchtet das Briefsymbol am oberen Bildschirmrand grün. Klickt darauf, um die Nachrichtenbox anzuzeigen.', 1760212410, 1),
	('chat_welcome_message', 'Hallo %nick%, willkommen im EtoA-Chat. Bitte beachte, dass wir Spam nicht dulden und eine gepflegte Ausdrucksweise erwarten. Bei Verstössen gegen diese Regeln werden wir mit Banns und/oder Accountsperrungen vorgehen!', 1760212420, 1),
	('admininfo', '', 1760212431, 1);
/*!40000 ALTER TABLE `texts` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.tickets
DROP TABLE IF EXISTS `tickets`;
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

-- Exportiere Daten aus Tabelle etoa_test.tickets: 0 rows
DELETE FROM `tickets`;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.ticket_cat
DROP TABLE IF EXISTS `ticket_cat`;
CREATE TABLE IF NOT EXISTS `ticket_cat` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `sort` smallint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.ticket_cat: ~0 rows (ungefähr)
DELETE FROM `ticket_cat`;
INSERT INTO `ticket_cat` (`id`, `name`, `sort`) VALUES
	(1, 'Beleidigung in Nachricht', 6),
	(2, 'Rathaus-Missbrauch', 7),
	(3, 'Missachtung der Angriffsregeln', 2),
	(4, 'Pushing-Verdacht', 3),
	(5, 'Cheat-Verdacht', 4),
	(6, 'Bugusing-Verdacht', 5),
	(7, 'Anstössiges Bild', 8),
	(8, 'Sonstiger Regelverstoss', 9),
	(9, 'Änderung meiner fixen E-Mail-Adresse', 11),
	(10, 'Änderung meines Namens (Accountübergabe)', 12),
	(11, 'Probleme mit einer Flotte (Ungültige Koordinaten, hängenbleibende Flotte)', 13),
	(12, 'Problem mit der Allianz (Ränge, Forum, Bündnisse, Auslösung etc)', 14),
	(14, 'Anderes Problem', 20),
	(15, 'Probleme mit den Account-Einstellungen (Design, Urlaubsmodus etc)', 15),
	(16, 'Verdacht auf Accounthacking', 16),
	(17, 'Probleme mit meinem Passwort', 17),
	(18, 'Änderung meines Dualspielers', 10),
	(19, 'Aufteilen eines Trümmerfeldes', 1),
	(20, 'Melden eines Bugs (Fehler im Spiel)', 0);

-- Exportiere Struktur von Tabelle etoa_test.ticket_msg
DROP TABLE IF EXISTS `ticket_msg`;
CREATE TABLE IF NOT EXISTS `ticket_msg` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int unsigned NOT NULL DEFAULT '0',
  `user_id` int unsigned DEFAULT '0',
  `admin_id` int unsigned DEFAULT '0',
  `message` text NOT NULL,
  `timestamp` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.ticket_msg: 0 rows
DELETE FROM `ticket_msg`;
/*!40000 ALTER TABLE `ticket_msg` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_msg` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.tips
DROP TABLE IF EXISTS `tips`;
CREATE TABLE IF NOT EXISTS `tips` (
  `tip_id` int unsigned NOT NULL AUTO_INCREMENT,
  `tip_text` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tip_active` tinyint unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`tip_id`),
  KEY `tip_active` (`tip_active`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Exportiere Daten aus Tabelle etoa_test.tips: 0 rows
DELETE FROM `tips`;
/*!40000 ALTER TABLE `tips` DISABLE KEYS */;
INSERT INTO `tips` (`tip_id`, `tip_text`, `tip_active`) VALUES
	(1, 'Gib niemals dein Passwort an andere Leute, auch nicht an Moderatoren und Admins. Logge dich nur über www.etoa.ch ein und niemals über eine andere Seite. Akzeptiere keine Dateien von fremden Spielern und sorge dafür, dass dein Passwort sicher ist und niemand Zugriff auf deinen Account bekommt.', 1),
	(2, 'Gründet Allianzen oder schliesst euch einer bestehen Allianz an, um gemeinsam gegen Feinde zu kämpfen und spezielle Allianzgebäude und -schiffe bauen zu können.', 1);
/*!40000 ALTER TABLE `tips` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.tutorial
DROP TABLE IF EXISTS `tutorial`;
CREATE TABLE IF NOT EXISTS `tutorial` (
  `tutorial_id` int unsigned NOT NULL AUTO_INCREMENT,
  `tutorial_title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`tutorial_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Exportiere Daten aus Tabelle etoa_test.tutorial: 0 rows
DELETE FROM `tutorial`;
/*!40000 ALTER TABLE `tutorial` DISABLE KEYS */;
INSERT INTO `tutorial` (`tutorial_id`, `tutorial_title`) VALUES
	(1, 'Rassenauswahl'),
	(2, 'Bauweise');
/*!40000 ALTER TABLE `tutorial` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.tutorial_texts
DROP TABLE IF EXISTS `tutorial_texts`;
CREATE TABLE IF NOT EXISTS `tutorial_texts` (
  `text_id` int unsigned NOT NULL AUTO_INCREMENT,
  `text_tutorial_id` int NOT NULL,
  `text_title` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `text_content` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `text_step` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`text_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Exportiere Daten aus Tabelle etoa_test.tutorial_texts: 0 rows
DELETE FROM `tutorial_texts`;
/*!40000 ALTER TABLE `tutorial_texts` DISABLE KEYS */;
INSERT INTO `tutorial_texts` (`text_id`, `text_tutorial_id`, `text_title`, `text_content`, `text_step`) VALUES
	(1, 1, 'Willkommen', 'Willkommen, werter neuer Imperator in den Galaxien Andromedas!\r\n\r\nDer Grundgedanke des Spieles (wie bei fast allen Aufbau-Browsergames) liegt darin, durch Rohstoffe Gebäude und Schiffe zu bauen. Bei EtoA jedoch sind die Möglichkeiten ungleich größer und vielfältiger. Dieses Tutorial soll euch ein wenig Entscheidungshilfe geben, erfolgreich den Wirren des Universums zu trotzen.\r\n\r\nDie Gliederung ist wie folgt:\r\n\r\n- Einführung in die Rohstoffe\r\n- Einführung in grundsätzliche Spielweisen\r\n- Entscheidungshilfe zu den Rassen\r\n- Entscheidungshilfe zu den Planeten\r\n- Grundsätzliches zum Aufbau deines Startplaneten\r\n\r\n', 0),
	(2, 1, 'Die Rohstoffe (1/2)', 'Es gibt in EtoA fünf verschiedene Arten von Rohstoffen:\r\n\r\n[list][*][b]Titan[/b], Grundstoff zum Bau von fast Allem. Wird mit fortschreitendem Spiel zum Massenprodukt[*][b]Silizium[/b], für Forschung und Schiffe. Zu Beginn eher rar, wird aber im weiteren Verlauf auch zum Massenprodukt.[*][b]PVC[/b], Man meint man hat genug, aber wenn man es dann braucht (vor allem für den Schiffsbau) hat man immer zu wenig.[*][b]Tritium[/b], als Treibstoff überlebensnotwendig. Aber auch zur Forschung wichtig. Ist selten reichlich vorhanden, auch im späteren Spiel keine Massenware. Gut zum Handeln.[*][b]Nahrung[/b], wichtig zum schnelleren Bauen von Minen, Schiffen, Forschung und natürlich zum Verschicken von Schiffen. Denn ohne Nahrung wird kein Pilot ein Schiff besteigen.[/list]\r\nDiese Rohstoffe können grundsätzlich auf jedem Planeten produziert werden. Durch geschickte Wahl des Sternensystems und des Planeten kann die Produktion einiger Rohstoffe stark erhöht werden, allerdings meist zu Lasten eines anderen Rohstoffes. Genaueres liefert die Hilfe.', 1),
	(3, 1, 'Die Rohstoffe (2/2)', 'Mit den passenden Schiffen kann man Rohstoffe auch im Weltall sammeln, grundsätzlich kann jede Rasse sammeln gehen. Einige Rassen haben jedoch Spezialschiffe, welche dafür besser geeignet sind. Auch hier hilft die Hilfe.\r\nSammeln kann man\r\n[list][*][b]Asteroiden[/b] (Titan, Silizium, PVC)[*][b]Sternennebel[/b] (Silizium)[*][b]Gasplaneten[/b] (Tritium)[*][b]Trümmerfelder[/b] durch Kämpfe (Titan, Silizium, PVC)[/list]', 2),
	(4, 1, 'Die grundsätzlichen Spielweisen', 'Das ganze Sammeln ist natürlich zeitaufwendig und reicht auf keinen Fall aus um einen Account ausbauen zu können. Dies bringt uns zum nächsten Teil des Tutorials:\r\n\r\nEs gibt drei grundsätzliche Spielweisen: der Miner, der Fleeter und der Händler.\r\n\r\nNatürlich kann man (und wird man wohl) einen Mix spielen, je nachdem wieviel Zeit man hat oder wo die eigenen Ziele liegen.', 3),
	(18, 1, 'Sterne und Planeten (2/2)', 'Grundsätzlich zu empfehlen ist ein gelber Stern, da er folgende Boni mitbringt:\r\n\r\n+35% Titan\r\n+30% Silizium\r\n+10% PVC\r\n\r\nDazu nehmen wir evtl. einen Eisplaneten:\r\n\r\n+10% Titan\r\n+30% Silizium\r\n+25% PVC\r\n+30% Tritium\r\n\r\nDie Kombination Gelb/Eis ergibt somit:\r\n\r\n+45% Titan\r\n+60% Silizium\r\n+35% PVC\r\n+30% Tritium\r\n\r\nDamit läßt sich zu Beginn ganz gut leben. Nicht verschweigen darf man jedoch den Malus von -25% auf Nahrung und die um 10% erhöhte Bauzeit. Zu Beginn sind diese Werte vernachlässigbar, im Laufe des Spieles kann das ganz anders aussehen.\r\nFerner kann man durch die Wahl der Rasse die Werte ebenfalls noch verändern. Nimmt man zb den Cardassianer mit +60% Nahrung bekommt man einen Bonus von +35% Nahrung bei obiger Kombo. Allerdings auch 10% weniger an Titan/Silizium. Und man hat die Mali/Boni der Rasse dann bei jedem Planeten.\r\n', 9),
	(5, 1, 'Die Wahl der Rasse', 'Sofern ihr euch jetzt in einem der Profile wiedergefunden habt, solltet ihr euch der Rassenwahl zuwenden.\r\n\r\nEs gibt in EtoA zehn verschiedene Rassen, alle haben einen Bonus oder Malus auf die Produktion bestimmter Rohstoffe. Eine Tabelle findest du in der Hilfe unter Rassen. Alternativ gibt es hier: [url=https://forum.etoa.ch/index.php?page=Thread&postID=109262#post109262]im Forum[/url] einen Rechner, der dich dabei unterstützen kann. Ferner hat jede Rasse spezielle Schiffe, welche nur von dieser Rasse gebaut werden können. Auch hier bitte die Hilfe aufrufen.\r\n\r\nEine kleine Entscheidungshilfe mit Beispiel:\r\n\r\nLiegt einem eher die Spielweise des Händlers, sollte man eine Rasse wählen, die einen Bonus auf einen eher seltenen Rohstoff hat oder die Schiffe besitzt, mit denen man effektiver im Weltall sammeln gehen kann. (Bsp. Vorgone, Terraner)\r\nIst man eher der Fleeter, sollte man eine Rasse wählen, die schnelle/günstige oder Schiffe mit Spezialfunktionen bauen kann. (Bsp. Minbari, Cardassianer)\r\nAls Miner ist evtl. die Rasse Serrakin interessant, da sie effektive Verteidigungsanlagen bauen kann. Oder man sucht eine Rasse mit hohem Silizium/Titan Bonus.\r\nNatürlich gibt es für jeden Zweck auch andere Rassen, hier sollte jeder schauen welche Rasse ihm am besten liegt, um seine Spielweise am ehesten zu unterstützen (Grundsätzlich ist es am Anfang aber eh ein Ausprobieren und nach Bauchgefühl spielen). Durch geschicktes Kombinieren der Rasse und der Planeten kann man sogar gute Rohstoff-Boni + besondere Schiffe bekommen. Zwar wird man nie ein Top-Fleeter mit Top-Boni sein, aber man kann auf jeden Fall näher herankommen.', 7),
	(6, 1, 'Sterne und Planeten (1/2)', 'Das bringt uns zum nächsten Punkt für einen erfolgreichen Einstieg: Die Sterne und Planeten\r\n\r\nIn EtoA gibt es 7 Arten von Sternen und 6 Arten von Planeten. Ein Sternensystem kann verschiedene Planeten beinhalten. Dabei wird jeder Planet den Einflüssen des Sternes ausgesetzt, dh. Die Boni/Mali des Sternes werden in die Berechnung der Boni/Mali der Planeten im Sternensystem mit einbezogen.\r\nJedes Imperium kann aus max. 15 Planeten bestehen. Jeder Planet gehört dir alleine. Welche Kombination du dir im weiteren Verlauf aussuchst und besiedelst hängt von deiner Spielweise ab. So kann man zb. die Kolonien nahe beieinander legen (kurze Flugzeit für Ressourcen-Transport), oder man nimmt nur eine Kombo und muss dann vll. weiter fliegen weil es diese nicht überall gibt.\r\n\r\nViel entscheidender ist die Wahl der Startkombination, auch wenn sie vielleicht nicht die Optimale für die weitere Spielweise ist. Ausgleichen kann man sie ja durch die Kolonien.\r\nGerade als Neuling solltest du eine Kombination auswählen, die es dir ermöglicht, zügig deinen Planeten ausbauen zu können. Boni auf Titan und Silizium sind zu Beginn sehr wichtig. Aber auch Tritium sollte nicht vergessen werden, denn ohne Treibstoff fliegt auch kein Besiedlungsschiff.\r\nEher nebensächlich sind zu Beginn Bau- bzw. Forschungs-Zeit Boni. Eine Tabelle findest du in der Hilfe. ', 8),
	(7, 1, 'Deine Entscheidung!', 'Hier nochmals eine Zusammenfassung der wichtigen Fragen:\r\n[list][*]Wieviel Zeit will/kann ich aufbringen[*]Welche Spielweise liegt mir am ehesten[*]Welche Rasse wähle ich dafür (Schiffe, Ressourcen)[*]Welche Stern/Planeten-Kombination unterstützt meine Spielweise und kann evtl. Nachteile meiner Rasse ausgleichen.[/list]\r\nNeulingen ist angeraten, den Startplaneten so zu wählen das kein Malus bei Titan oder Silizium vorhanden ist. Bei der Wahl der Kolonien kann das wieder anders aussehen.\r\n\r\nViel Erfolg, mein Imperator, möge dein Reich groß und mächtig werden und lange bestehen !\r\n\r\nDein EtoA-Team.', 10),
	(8, 2, 'Auf gehts!', 'Du hast deine Kombo gefunden? Dann geht es hier weiter mit einer kleinen Anleitung zu einem erfolgreichen Start.\r\n\r\nWICHTIG: Solltest du aus Versehen die falsche Kombination ausgewählt haben und hast du noch nichts darauf gebaut melde es einem Administrator. Er kann dir vll aus dieser misslichen Lage heraus helfen. Das ist auf jeden Fall besser als mit einer Kombination weiter zu spielen die schlechte Startmöglichkeiten hat.\r\n', 0),
	(9, 2, 'Ressourcen', 'Grundstoffe sind [b]Titan[/b](Tit) und [b]Silizium[/b](Sil). Weiterführend kommt [b]PVC[/b] dazu, am Ende benötigt man [b]Tritium[/b](Trit). [b]Nahrung[/b](Nah) dient der Bauzeitverkürzung und wird zum Fliegen benötigt. Nebenbei benötigt jede Mine auch [b]Energie[/b]. Man muss also auch diese Sparte mit ausbauen.\r\n\r\nJede Ressource baut auf die andere auf. Ohne Tit/Sili kein pvc. Ohne pvc kein Trit. Ohne Trit keine Besiedlungsschiffe.\r\nDaher ist es gerade zu Beginn sehr wichtig mit den Ressourcen sparsam umzugehen und sie sinnvoll zu verbauen.', 1),
	(10, 2, 'Die Baureihenfolge', 'Hier scheiden sich die Geister. Jeder Spieler wird eine andere Vorgehensweise haben die ihn am ehesten zu seinem Ziel führt, welches er sich zum Ziel gesetzt hat. (Profil) Auch die Boni sind ein bedeutender Faktor. Daher folgt hier eine allgemeingültige Vorgehensweise.\r\n\r\nZu Beginn habt ihr einen Grundstock an allen Ressourcen die es euch ermöglichen soweit bauen zu können, dass ihr Tit/Sil/PVC selbstständig produzieren könnt.\r\n[list][*]Zuerst Titan- und Silizium-Minen ausbauen. Wobei hier zuerst Titan, danach Sili. Sie sollten immer 2 Stufen Unterschied haben. Bsp: Titan 5, Sili 3[*]Bei 5/3 solltet ihr an die Grenze eurer Energie gestoßen sein. Daher muß jetzt ein Windkraftwerk gebaut werden. Baut dieses aber immer erst wenn die Energie nahe bei 0 ist. Das gilt auch für die weiteren Stufen.[*]Nach dem Kraftwerk sollte die PVC-Produktion um 1-2 Stufen erhöht werden, je nachdem wieviel tit/sil über ist. Ohne PVC gibt es keine Kraftwerke. Ohne Kraftwerke keine Energie. Ohne Energie wird die Produktion sämtlicher Ressourcen gedrosselt.[*]Sobald ihr Wind auf Stufe 5 habt könnt ihr mit dem Bau von Tritium-Anlagen beginnen. Dies sollte auch direkt geschehen denn ohne Trit keine Forschung, ohne Forschung keine Antriebe, ohne Antriebe kein Besiedlungsschiff. Oder aber es dauert ewig, weil ihr zu spät mit dem Ausbau von Trit begonnen habt.[*]Bewährt hat sich auch, früh die Nahrung auf Stufe 2 oder 3 zu bringen, wenn man Ress über hat. Man braucht sie zwar nicht zu Beginn, aber später kann man vll schneller forschen um sein Besiedlungsschiff zu bauen.[*]Sollte eure Bevölkerung keinen Platz mehr zum Wachsen haben, baut das Wohnmodul aus. Hier reichen aber 2 Stufen locker aus.[*]Sinnlos ist das Bauen von Speichern. Zu Beginn werdet ihr immer zuwenig als zuviel Ressourcen haben.[/list]\r\nNun sollte eure Rohstoffproduktion auf soliden Füßen stehen.', 2),
	(11, 2, 'Verteidigung', 'Leider gibt es auch in EtoA Spieler, die nach euren Ressourcen trachten. Daher ist es wichtig, nach ca. 48-60h eine Verteidigungsanlage auf eurem Planeten aufgestellt zu haben. Dazu benötigt ihr eine [b]Waffenfabrik[/b] und eine [b]Spica Flakkanone[/b]. Beides muss in der Ressourcenplanung und somit beim Ausbau bedacht werden.', 3),
	(12, 2, 'Schiffe', 'Sobald die Voraussetzungen für den Bau von Schiffen da sind, baut euch einen [b]AURIGA Explorer[/b], mit dem ihr die Sternenkarte aufdecken könnt. So findet ihr die passenden Kombos für euer TAURUS Besiedlungsschiff, das Ziel eurer Bemühungen.\r\nAllerdings braucht es dafür eine Schiffswerft, eine Flottenkontrolle und einen Ionenantrieb welcher in einem Forschungslabor erforscht werden muss.\r\n\r\nUm dies alles sinnvoll zu erreichen und auch um zügig [b]TAURUS[/b] bauen zu können hat sich folgender Ausbau bewährt:\r\n\r\nTitan 11(+1)\r\nSilizium 11(+1)\r\nPVC 10(+1)\r\nTritium 8(+1)\r\n\r\nJe nach Rasse/Stern/Planeten-Kombo kann das natürlich anders aussehen. So wird einem Rigelianer Sil 9 reichen, braucht aber tit 12 oder 13.', 4),
	(13, 2, 'Abschluss', 'Die richtige Kombo ist gefunden? Das 1. TAURUS ist unterwegs? Hoffentlich habt ihr nicht vergessen Tit/Sili zum Bau von Minen sowie soviel PVC mitzuschicken damit ihr Wind 3 bauen könnt. Denn ohne Wind 3 keine PVC-Fabrik, ohne PVC kein Kraftwerke, ohne Kraftwerk keine Ressourcen.....ihr kennt das ja.\r\n\r\nPVC ist dabei? Dann alles Gute und viel Glück beim Vergrößern deines Einflussbereiches, werter Imperator.\r\n\r\nDein EtoA- Team', 5),
	(15, 1, 'Der Miner', 'Der [b]Miner[/b] spielt sein Spiel gemütlich und lebt fast ausschließlich von seiner eigenen Ressourcen-Produktion. Nebenbei wird er auch im Weltall Ressourcen sammeln gehen. Dabei werden die Ressourcen meist in größere Minen investiert. Nur ein kleiner Teil ihrer Ressourcen wandert in Schiffe oder Verteidigung. Vor dem Miner braucht man sich grundsätzlich nicht zu fürchten, jedoch sollte man nicht denken, es seien leichte Opfer, denn sie haben oft Freunde unter den 24/7 Spielern (ständig online); welche ihnen gerne helfen. Oder sie haben sich einer Allianz angeschlossen, die sie beschützen kann.\r\n\r\n[i]Zeitaufwand: Gering/Mittel[/i]', 4),
	(16, 1, 'Der Fleeter', 'Der [b]Fleeter[/b] gehört zu den aggressiven Spielern in EtoA. Er deckt seinen zusätzlichen Bedarf an Ressourcen durch Raiden (Stehlen von Ressourcen) oder aber durch Kämpfe mit anderen Spielern. Dafür wandert ein Großteil seiner Produktion in Schiffe. Der Vorteil ist dabei die abschreckende Wirkung einer großen Flotte. (Der Nachteil ist, wenn die Flotte mal zerstört werden sollte, ist es für ihn wesentlich schwieriger, wieder an neue Flotte zu kommen, da seine Eigen-Produktion an Ressourcen nicht ausreicht). Das Einzige, das gegen den Fleeter hilft, ist Ressourcen und Schiffe in Sicherheit zu bringen (Saven: d.h. seine Schiffe beispielsweise über Nacht auf "Flug" zu schicken) oder ihm mittels einer Verteidigung, welche ihm ordentliche Verluste beibringt, die Lust zu nehmen. Sollte man öfters von dem gleichen Fleeter geraidet werden, kann es durchaus helfen, eine freundliche Anfrage zu schicken. Meist wird darauf positiv reagiert.\r\n\r\n[i]Zeitaufwand: Hoch[/i]', 5),
	(17, 1, 'Der Händler', 'Der [b]Händler[/b] hat sich auf das Handeln von Waren und Schiffen spezialisiert. Er baut seine Markplätze hoch aus, um immer genug große oder kleine Angebote in den Marktplatz stellen zu können. Er ist meistens ein ruhiger Geselle, der sein Ressourcen-Extra mit dem Verkauf von Waren realisiert. Hier ist die Wahl der Rasse ein entscheidender Faktor. Nahrung oder Tritium oder PVC sind gern genommene Rohstoffe, auch einige Schiffe lassen sich gut verkaufen, sei es, sie sind schnell oder haben eine Spezialfunktion. Mehr dazu findet ihr in der Hilfe.\r\n\r\n[i]Zeitaufwand Mittel[/i]', 6);
/*!40000 ALTER TABLE `tutorial_texts` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.tutorial_user_progress
DROP TABLE IF EXISTS `tutorial_user_progress`;
CREATE TABLE IF NOT EXISTS `tutorial_user_progress` (
  `tup_user_id` int unsigned NOT NULL,
  `tup_tutorial_id` int unsigned NOT NULL,
  `tup_text_step` tinyint unsigned NOT NULL DEFAULT '0',
  `tup_closed` tinyint unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `tup_user_id` (`tup_user_id`,`tup_tutorial_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Exportiere Daten aus Tabelle etoa_test.tutorial_user_progress: 0 rows
DELETE FROM `tutorial_user_progress`;
/*!40000 ALTER TABLE `tutorial_user_progress` DISABLE KEYS */;
/*!40000 ALTER TABLE `tutorial_user_progress` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.users
DROP TABLE IF EXISTS `users`;
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

-- Exportiere Daten aus Tabelle etoa_test.users: ~0 rows (ungefähr)
DELETE FROM `users`;

-- Exportiere Struktur von Tabelle etoa_test.user_comments
DROP TABLE IF EXISTS `user_comments`;
CREATE TABLE IF NOT EXISTS `user_comments` (
  `comment_id` int unsigned NOT NULL AUTO_INCREMENT,
  `comment_timestamp` int unsigned NOT NULL DEFAULT '0',
  `comment_user_id` int unsigned NOT NULL DEFAULT '0',
  `comment_admin_id` int unsigned NOT NULL DEFAULT '0',
  `comment_text` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `comment_user_id` (`comment_user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci COMMENT='Admin comments on users';

-- Exportiere Daten aus Tabelle etoa_test.user_comments: 0 rows
DELETE FROM `user_comments`;
/*!40000 ALTER TABLE `user_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_comments` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.user_log
DROP TABLE IF EXISTS `user_log`;
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

-- Exportiere Daten aus Tabelle etoa_test.user_log: 0 rows
DELETE FROM `user_log`;
/*!40000 ALTER TABLE `user_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_log` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.user_multi
DROP TABLE IF EXISTS `user_multi`;
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

-- Exportiere Daten aus Tabelle etoa_test.user_multi: 0 rows
DELETE FROM `user_multi`;
/*!40000 ALTER TABLE `user_multi` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_multi` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.user_onlinestats
DROP TABLE IF EXISTS `user_onlinestats`;
CREATE TABLE IF NOT EXISTS `user_onlinestats` (
  `stats_id` int unsigned NOT NULL AUTO_INCREMENT,
  `stats_timestamp` int unsigned NOT NULL,
  `stats_count` int unsigned NOT NULL,
  `stats_regcount` int unsigned NOT NULL,
  PRIMARY KEY (`stats_id`),
  KEY `stats_count` (`stats_count`)
) ENGINE=MyISAM AUTO_INCREMENT=28271 DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.user_onlinestats: 0 rows
DELETE FROM `user_onlinestats`;
/*!40000 ALTER TABLE `user_onlinestats` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_onlinestats` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.user_points
DROP TABLE IF EXISTS `user_points`;
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

-- Exportiere Daten aus Tabelle etoa_test.user_points: 0 rows
DELETE FROM `user_points`;
/*!40000 ALTER TABLE `user_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_points` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.user_properties
DROP TABLE IF EXISTS `user_properties`;
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

-- Exportiere Daten aus Tabelle etoa_test.user_properties: 0 rows
DELETE FROM `user_properties`;
/*!40000 ALTER TABLE `user_properties` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_properties` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.user_ratings
DROP TABLE IF EXISTS `user_ratings`;
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

-- Exportiere Daten aus Tabelle etoa_test.user_ratings: 0 rows
DELETE FROM `user_ratings`;
/*!40000 ALTER TABLE `user_ratings` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_ratings` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.user_sessionlog
DROP TABLE IF EXISTS `user_sessionlog`;
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

-- Exportiere Daten aus Tabelle etoa_test.user_sessionlog: ~0 rows (ungefähr)
DELETE FROM `user_sessionlog`;

-- Exportiere Struktur von Tabelle etoa_test.user_sessions
DROP TABLE IF EXISTS `user_sessions`;
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

-- Exportiere Daten aus Tabelle etoa_test.user_sessions: 0 rows
DELETE FROM `user_sessions`;
/*!40000 ALTER TABLE `user_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_sessions` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.user_sitting
DROP TABLE IF EXISTS `user_sitting`;
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

-- Exportiere Daten aus Tabelle etoa_test.user_sitting: 0 rows
DELETE FROM `user_sitting`;
/*!40000 ALTER TABLE `user_sitting` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_sitting` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.user_stats
DROP TABLE IF EXISTS `user_stats`;
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

-- Exportiere Daten aus Tabelle etoa_test.user_stats: 0 rows
DELETE FROM `user_stats`;
/*!40000 ALTER TABLE `user_stats` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_stats` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.user_surveillance
DROP TABLE IF EXISTS `user_surveillance`;
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

-- Exportiere Daten aus Tabelle etoa_test.user_surveillance: 0 rows
DELETE FROM `user_surveillance`;
/*!40000 ALTER TABLE `user_surveillance` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_surveillance` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.user_warnings
DROP TABLE IF EXISTS `user_warnings`;
CREATE TABLE IF NOT EXISTS `user_warnings` (
  `warning_id` int unsigned NOT NULL AUTO_INCREMENT,
  `warning_user_id` int unsigned NOT NULL,
  `warning_date` int unsigned NOT NULL,
  `warning_text` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `warning_admin_id` int unsigned NOT NULL,
  PRIMARY KEY (`warning_id`),
  KEY `warning_user_id` (`warning_user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- Exportiere Daten aus Tabelle etoa_test.user_warnings: 0 rows
DELETE FROM `user_warnings`;
/*!40000 ALTER TABLE `user_warnings` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_warnings` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle etoa_test.wormholes
DROP TABLE IF EXISTS `wormholes`;
CREATE TABLE IF NOT EXISTS `wormholes` (
  `id` int unsigned NOT NULL,
  `target_id` int unsigned DEFAULT NULL,
  `changed` int unsigned NOT NULL DEFAULT '0',
  `persistent` tinyint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `target_id` (`target_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- Exportiere Daten aus Tabelle etoa_test.wormholes: 0 rows
DELETE FROM `wormholes`;
/*!40000 ALTER TABLE `wormholes` DISABLE KEYS */;
/*!40000 ALTER TABLE `wormholes` ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
