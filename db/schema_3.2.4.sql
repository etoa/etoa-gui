-- phpMyAdmin SQL Dump
-- version 3.3.7deb7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 18. Oktober 2012 um 20:48
-- Server Version: 5.1.63
-- PHP-Version: 5.3.3-7+squeeze14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `etoa_round11`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `accesslog`
--

CREATE TABLE IF NOT EXISTS `accesslog` (
  `target` varchar(255) NOT NULL,
  `sub` varchar(255) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` varchar(32) NOT NULL,
  `domain` varchar(255) NOT NULL,
  KEY `target` (`target`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin_notes`
--

CREATE TABLE IF NOT EXISTS `admin_notes` (
  `notes_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned NOT NULL,
  `titel` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `date` int(10) unsigned NOT NULL,
  PRIMARY KEY (`notes_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin_surveillance`
--

CREATE TABLE IF NOT EXISTS `admin_surveillance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `page` varchar(50) NOT NULL,
  `request` text NOT NULL,
  `post` text NOT NULL,
  `session` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin_users`
--

CREATE TABLE IF NOT EXISTS `admin_users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(30) NOT NULL,
  `user_email` varchar(30) NOT NULL,
  `user_nick` varchar(30) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_last_login` int(10) unsigned NOT NULL DEFAULT '0',
  `user_acttime` int(10) unsigned NOT NULL DEFAULT '0',
  `user_locked` int(10) unsigned NOT NULL DEFAULT '0',
  `user_session_key` varchar(250) NOT NULL,
  `user_ip` varchar(20) NOT NULL,
  `user_hostname` varchar(150) NOT NULL,
  `user_board_url` char(250) NOT NULL,
  `user_force_pwchange` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_theme` varchar(40) NOT NULL,
  `ticketmail` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `player_id` int(10) unsigned NOT NULL DEFAULT '0',
  `roles` varchar(255) NOT NULL,
  `is_contact` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`user_id`),
  FULLTEXT KEY `user_password` (`user_password`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin_user_log`
--

CREATE TABLE IF NOT EXISTS `admin_user_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `log_session_key` varchar(250) NOT NULL,
  `log_logintime` int(10) unsigned NOT NULL DEFAULT '0',
  `log_logouttime` int(10) unsigned NOT NULL DEFAULT '0',
  `log_acttime` int(10) unsigned NOT NULL DEFAULT '0',
  `log_ip` varchar(20) NOT NULL,
  `log_hostname` varchar(150) NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `log_user_id` (`log_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin_user_sessionlog`
--

CREATE TABLE IF NOT EXISTS `admin_user_sessionlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` char(40) NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_addr` varchar(40) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `time_login` int(10) unsigned NOT NULL DEFAULT '0',
  `time_action` int(10) unsigned NOT NULL DEFAULT '0',
  `time_logout` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin_user_sessions`
--

CREATE TABLE IF NOT EXISTS `admin_user_sessions` (
  `id` char(40) NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_addr` varchar(40) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `time_login` int(11) unsigned NOT NULL DEFAULT '0',
  `time_action` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `allianceboard_cat`
--

CREATE TABLE IF NOT EXISTS `allianceboard_cat` (
  `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(50) NOT NULL,
  `cat_desc` text NOT NULL,
  `cat_order` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `cat_bullet` varchar(255) NOT NULL,
  `cat_alliance_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`cat_id`),
  KEY `cat_order` (`cat_order`),
  KEY `cat_name` (`cat_name`),
  KEY `cat_alliance_id` (`cat_alliance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `allianceboard_catranks`
--

CREATE TABLE IF NOT EXISTS `allianceboard_catranks` (
  `cr_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cr_rank_id` int(10) unsigned NOT NULL,
  `cr_cat_id` int(10) unsigned NOT NULL,
  `cr_bnd_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cr_id`),
  KEY `cr_rank_id` (`cr_rank_id`),
  KEY `cr_cat_id` (`cr_cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `allianceboard_posts`
--

CREATE TABLE IF NOT EXISTS `allianceboard_posts` (
  `post_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_topic_id` int(10) unsigned NOT NULL DEFAULT '0',
  `post_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `post_user_nick` varchar(15) NOT NULL,
  `post_text` text NOT NULL,
  `post_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `post_changed` varchar(30) NOT NULL,
  PRIMARY KEY (`post_id`),
  KEY `post_topic_id` (`post_topic_id`),
  KEY `post_user_id` (`post_user_id`),
  KEY `post_timestamp` (`post_timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `allianceboard_topics`
--

CREATE TABLE IF NOT EXISTS `allianceboard_topics` (
  `topic_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `topic_cat_id` int(10) unsigned NOT NULL DEFAULT '0',
  `topic_bnd_id` int(10) unsigned NOT NULL DEFAULT '0',
  `topic_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `topic_user_nick` varchar(15) NOT NULL,
  `topic_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `topic_subject` varchar(100) NOT NULL,
  `topic_count` int(10) unsigned NOT NULL DEFAULT '0',
  `topic_top` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `topic_closed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`topic_id`),
  KEY `topic_cat_id` (`topic_cat_id`),
  KEY `topic_timestamp` (`topic_timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliances`
--

CREATE TABLE IF NOT EXISTS `alliances` (
  `alliance_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alliance_tag` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `alliance_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `alliance_text` text COLLATE utf8_unicode_ci NOT NULL,
  `alliance_img` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `alliance_img_check` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `alliance_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `alliance_mother` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_mother_request` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_accept_applications` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `alliance_accept_bnd` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `alliance_public_memberlist` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `alliance_points` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_rank_current` smallint(5) unsigned NOT NULL DEFAULT '0',
  `alliance_rank_last` smallint(5) unsigned NOT NULL DEFAULT '0',
  `alliance_founder_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_foundation_date` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_architect_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_technican_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_diplomat_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_visits` int(10) unsigned NOT NULL,
  `alliance_visits_ext` int(10) unsigned NOT NULL,
  `alliance_application_template` text COLLATE utf8_unicode_ci NOT NULL,
  `alliance_res_metal` bigint(20) NOT NULL DEFAULT '0',
  `alliance_res_crystal` bigint(20) NOT NULL DEFAULT '0',
  `alliance_res_plastic` bigint(20) NOT NULL DEFAULT '0',
  `alliance_res_fuel` bigint(20) NOT NULL DEFAULT '0',
  `alliance_res_food` bigint(20) NOT NULL DEFAULT '0',
  `alliance_objects_for_members` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`alliance_id`),
  KEY `alliance_tag` (`alliance_tag`),
  KEY `alliance_name` (`alliance_name`),
  KEY `alliance_points` (`alliance_points`),
  KEY `alliance_founder_id` (`alliance_founder_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Allianz-Daten' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_applications`
--

CREATE TABLE IF NOT EXISTS `alliance_applications` (
  `user_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `alliance_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `user_id` (`user_id`),
  KEY `alliance_id` (`alliance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_bnd`
--

CREATE TABLE IF NOT EXISTS `alliance_bnd` (
  `alliance_bnd_name` varchar(30) NOT NULL,
  `alliance_bnd_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alliance_bnd_alliance_id1` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_bnd_alliance_id2` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_bnd_level` int(1) unsigned NOT NULL DEFAULT '0',
  `alliance_bnd_text` text NOT NULL,
  `alliance_bnd_date` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_bnd_text_pub` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `alliance_bnd_points` int(2) unsigned NOT NULL DEFAULT '0',
  `alliance_bnd_diplomat_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`alliance_bnd_id`),
  KEY `alliance_bnd_alliance_id1` (`alliance_bnd_alliance_id1`),
  KEY `alliance_bnd_alliance_id2` (`alliance_bnd_alliance_id2`),
  KEY `bnd1` (`alliance_bnd_level`,`alliance_bnd_alliance_id1`),
  KEY `bnd2` (`alliance_bnd_level`,`alliance_bnd_alliance_id2`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_buildings`
--

CREATE TABLE IF NOT EXISTS `alliance_buildings` (
  `alliance_building_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `alliance_building_name` varchar(30) NOT NULL,
  `alliance_building_shortcomment` text NOT NULL,
  `alliance_building_longcomment` text NOT NULL,
  `alliance_building_costs_metal` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_building_costs_crystal` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_building_costs_plastic` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_building_costs_fuel` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_building_costs_food` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_building_build_time` mediumint(10) unsigned NOT NULL DEFAULT '0',
  `alliance_building_costs_factor` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `alliance_building_last_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `alliance_building_show` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `alliance_building_needed_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `alliance_building_needed_level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`alliance_building_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_buildlist`
--

CREATE TABLE IF NOT EXISTS `alliance_buildlist` (
  `alliance_buildlist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alliance_buildlist_alliance_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `alliance_buildlist_building_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `alliance_buildlist_current_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `alliance_buildlist_build_start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_buildlist_build_end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_buildlist_cooldown` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_buildlist_member_for` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`alliance_buildlist_id`),
  KEY `alliance_buildlist_alliance_id` (`alliance_buildlist_alliance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_history`
--

CREATE TABLE IF NOT EXISTS `alliance_history` (
  `history_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `history_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `history_text` text NOT NULL,
  `history_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`history_id`),
  KEY `latest` (`history_alliance_id`,`history_timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_news`
--

CREATE TABLE IF NOT EXISTS `alliance_news` (
  `alliance_news_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alliance_news_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_news_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_news_title` varchar(255) NOT NULL,
  `alliance_news_text` text NOT NULL,
  `alliance_news_date` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_news_alliance_to_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_news_changed_date` int(10) unsigned NOT NULL,
  `alliance_news_changed_counter` int(3) unsigned NOT NULL,
  `alliance_news_show` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `alliance_news_ip` char(15) NOT NULL,
  PRIMARY KEY (`alliance_news_id`),
  KEY `alliance_news_alliance_id` (`alliance_news_alliance_id`),
  KEY `alliance_news_user_id` (`alliance_news_user_id`),
  KEY `alliance_news_date` (`alliance_news_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_points`
--

CREATE TABLE IF NOT EXISTS `alliance_points` (
  `point_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `point_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `point_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `point_points` bigint(12) unsigned NOT NULL DEFAULT '0',
  `point_avg` bigint(12) unsigned NOT NULL DEFAULT '0',
  `point_cnt` bigint(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`point_id`),
  KEY `point_user_id` (`point_alliance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Speichert den Punkteverlauf der Allianz' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_polls`
--

CREATE TABLE IF NOT EXISTS `alliance_polls` (
  `poll_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poll_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `poll_title` varchar(150) NOT NULL,
  `poll_question` varchar(150) NOT NULL,
  `poll_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `poll_a1_text` varchar(150) NOT NULL,
  `poll_a2_text` varchar(150) NOT NULL,
  `poll_a3_text` varchar(150) NOT NULL,
  `poll_a4_text` varchar(150) NOT NULL,
  `poll_a5_text` varchar(150) NOT NULL,
  `poll_a6_text` varchar(150) NOT NULL,
  `poll_a7_text` varchar(150) NOT NULL,
  `poll_a8_text` varchar(150) NOT NULL,
  `poll_a1_count` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `poll_a2_count` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `poll_a3_count` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `poll_a4_count` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `poll_a5_count` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `poll_a6_count` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `poll_a7_count` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `poll_a8_count` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `poll_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`poll_id`),
  KEY `poll_alliance_id` (`poll_alliance_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_poll_votes`
--

CREATE TABLE IF NOT EXISTS `alliance_poll_votes` (
  `vote_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vote_poll_id` int(10) unsigned NOT NULL DEFAULT '0',
  `vote_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `vote_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `vote_number` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`vote_id`),
  KEY `vote_poll_id` (`vote_poll_id`),
  KEY `vote_user_id` (`vote_user_id`),
  KEY `vote_alliance_id` (`vote_alliance_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_rankrights`
--

CREATE TABLE IF NOT EXISTS `alliance_rankrights` (
  `rr_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rr_rank_id` int(10) unsigned NOT NULL DEFAULT '0',
  `rr_right_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rr_id`),
  KEY `rr_rank_id` (`rr_rank_id`),
  KEY `rr_right_id` (`rr_right_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_ranks`
--

CREATE TABLE IF NOT EXISTS `alliance_ranks` (
  `rank_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rank_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `rank_name` varchar(30) NOT NULL,
  `rank_level` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `rank_points` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rank_id`),
  KEY `rank_alliance_id` (`rank_alliance_id`,`rank_level`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_rights`
--

CREATE TABLE IF NOT EXISTS `alliance_rights` (
  `right_id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `right_key` varchar(30) NOT NULL,
  `right_desc` text NOT NULL,
  PRIMARY KEY (`right_id`),
  KEY `right_key` (`right_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_spends`
--

CREATE TABLE IF NOT EXISTS `alliance_spends` (
  `alliance_spend_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alliance_spend_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_spend_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_spend_metal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `alliance_spend_crystal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `alliance_spend_plastic` bigint(20) unsigned NOT NULL DEFAULT '0',
  `alliance_spend_fuel` bigint(20) unsigned NOT NULL DEFAULT '0',
  `alliance_spend_food` bigint(20) unsigned NOT NULL DEFAULT '0',
  `alliance_spend_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`alliance_spend_id`),
  KEY `alliance_spend_alliance_id` (`alliance_spend_alliance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_stats`
--

CREATE TABLE IF NOT EXISTS `alliance_stats` (
  `alliance_id` int(10) unsigned NOT NULL,
  `alliance_tag` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `alliance_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `cnt` smallint(4) unsigned NOT NULL DEFAULT '0',
  `points` bigint(20) unsigned NOT NULL DEFAULT '0',
  `upoints` bigint(20) unsigned NOT NULL DEFAULT '0',
  `apoints` bigint(20) unsigned NOT NULL DEFAULT '0',
  `bpoints` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tpoints` bigint(20) unsigned NOT NULL DEFAULT '0',
  `spoints` bigint(20) unsigned NOT NULL DEFAULT '0',
  `uavg` bigint(20) unsigned NOT NULL DEFAULT '0',
  `alliance_rank_current` smallint(5) unsigned NOT NULL DEFAULT '0',
  `alliance_rank_last` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`alliance_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_techlist`
--

CREATE TABLE IF NOT EXISTS `alliance_techlist` (
  `alliance_techlist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alliance_techlist_alliance_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_tech_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_current_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_build_start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_build_end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_member_for` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`alliance_techlist_id`),
  KEY `alliance_techlist_alliance_id` (`alliance_techlist_alliance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_technologies`
--

CREATE TABLE IF NOT EXISTS `alliance_technologies` (
  `alliance_tech_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `alliance_tech_name` varchar(30) NOT NULL,
  `alliance_tech_shortcomment` text NOT NULL,
  `alliance_tech_longcomment` text NOT NULL,
  `alliance_tech_costs_metal` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_tech_costs_crystal` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_tech_costs_plastic` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_tech_costs_fuel` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_tech_costs_food` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_tech_build_time` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `alliance_tech_costs_factor` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `alliance_tech_last_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `alliance_tech_show` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `alliance_tech_needed_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `alliance_tech_needed_level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`alliance_tech_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `asteroids`
--

CREATE TABLE IF NOT EXISTS `asteroids` (
  `id` int(10) unsigned NOT NULL,
  `res_metal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_crystal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_plastic` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_fuel` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_food` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_power` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `attack_ban`
--

CREATE TABLE IF NOT EXISTS `attack_ban` (
  `attack_ban_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `attack_ban_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `attack_ban_reason` text NOT NULL,
  `attack_ban_time` int(10) unsigned NOT NULL DEFAULT '0',
  `attack_ban_deleted` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`attack_ban_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bookmarks`
--

CREATE TABLE IF NOT EXISTS `bookmarks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bookmark_user_id` (`user_id`),
  KEY `absindex` (`user_id`,`entity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `buddylist`
--

CREATE TABLE IF NOT EXISTS `buddylist` (
  `bl_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `bl_buddy_id` int(10) unsigned NOT NULL DEFAULT '0',
  `bl_allow` int(10) unsigned NOT NULL DEFAULT '0',
  `bl_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bl_comment` text NOT NULL,
  `bl_comment_buddy` text NOT NULL,
  PRIMARY KEY (`bl_id`),
  KEY `bl_buddy_id` (`bl_buddy_id`),
  KEY `bl_user_id` (`bl_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=238 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `buildings`
--

CREATE TABLE IF NOT EXISTS `buildings` (
  `building_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `building_name` varchar(255) NOT NULL,
  `building_type_id` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `building_shortcomment` text NOT NULL,
  `building_longcomment` text NOT NULL,
  `building_costs_metal` int(10) unsigned NOT NULL DEFAULT '0',
  `building_costs_crystal` int(10) unsigned NOT NULL DEFAULT '0',
  `building_costs_fuel` int(10) unsigned NOT NULL DEFAULT '0',
  `building_costs_plastic` int(10) unsigned NOT NULL DEFAULT '0',
  `building_costs_food` int(10) unsigned NOT NULL DEFAULT '0',
  `building_costs_power` int(10) unsigned NOT NULL DEFAULT '0',
  `building_build_costs_factor` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `building_demolish_costs_factor` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `building_power_use` int(10) unsigned NOT NULL DEFAULT '0',
  `building_power_req` int(10) unsigned NOT NULL DEFAULT '0',
  `building_fuel_use` int(10) unsigned NOT NULL DEFAULT '0',
  `building_prod_metal` int(10) unsigned NOT NULL DEFAULT '0',
  `building_prod_crystal` int(10) unsigned NOT NULL DEFAULT '0',
  `building_prod_plastic` int(10) unsigned NOT NULL DEFAULT '0',
  `building_prod_fuel` int(10) unsigned NOT NULL DEFAULT '0',
  `building_prod_food` int(10) unsigned NOT NULL DEFAULT '0',
  `building_prod_power` int(10) unsigned NOT NULL DEFAULT '0',
  `building_production_factor` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `building_store_metal` int(10) unsigned NOT NULL DEFAULT '0',
  `building_store_crystal` int(10) unsigned NOT NULL DEFAULT '0',
  `building_store_plastic` int(10) unsigned NOT NULL DEFAULT '0',
  `building_store_fuel` int(10) unsigned NOT NULL DEFAULT '0',
  `building_store_food` int(10) unsigned NOT NULL DEFAULT '0',
  `building_store_factor` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `building_people_place` int(10) unsigned NOT NULL DEFAULT '0',
  `building_last_level` tinyint(3) unsigned NOT NULL DEFAULT '99',
  `building_fields` smallint(3) unsigned NOT NULL DEFAULT '1',
  `building_show` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `building_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `building_fieldsprovide` smallint(5) unsigned NOT NULL DEFAULT '0',
  `building_workplace` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `building_bunker_res` int(10) unsigned NOT NULL DEFAULT '0',
  `building_bunker_fleet_count` int(10) unsigned NOT NULL DEFAULT '0',
  `building_bunker_fleet_space` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`building_id`),
  KEY `building_name` (`building_name`,`building_order`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `building_points`
--

CREATE TABLE IF NOT EXISTS `building_points` (
  `bp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bp_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `bp_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bp_points` decimal(20,3) unsigned NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`bp_id`),
  KEY `bp_building_id` (`bp_building_id`),
  KEY `bp_level` (`bp_level`),
  KEY `bp_points` (`bp_points`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `building_queue`
--

CREATE TABLE IF NOT EXISTS `building_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `item_id` int(10) unsigned NOT NULL DEFAULT '0',
  `time_start` int(10) unsigned NOT NULL DEFAULT '0',
  `time_end` int(10) unsigned NOT NULL DEFAULT '0',
  `targetlevel` smallint(5) unsigned NOT NULL DEFAULT '0',
  `res_metal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_crystal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_plastic` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_fuel` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_food` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `entity_id` (`entity_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `building_requirements`
--

CREATE TABLE IF NOT EXISTS `building_requirements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `obj_id` (`obj_id`,`req_building_id`,`req_tech_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=66 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `building_types`
--

CREATE TABLE IF NOT EXISTS `building_types` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL,
  `type_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type_color` char(7) NOT NULL DEFAULT '#ffffff',
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `buildlist`
--

CREATE TABLE IF NOT EXISTS `buildlist` (
  `buildlist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `buildlist_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `buildlist_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `buildlist_entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `buildlist_current_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `buildlist_build_start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `buildlist_build_end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `buildlist_build_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `buildlist_prod_percent` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `buildlist_people_working` int(10) unsigned NOT NULL DEFAULT '0',
  `buildlist_people_working_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `buildlist_deactivated` int(10) unsigned NOT NULL DEFAULT '0',
  `buildlist_cooldown` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`buildlist_id`),
  UNIQUE KEY `entity_user_building` (`buildlist_entity_id`,`buildlist_user_id`,`buildlist_building_id`),
  KEY `buildlist_user_id` (`buildlist_user_id`),
  KEY `buildlist_building_id` (`buildlist_building_id`),
  KEY `buildlist_planet_id` (`buildlist_entity_id`),
  KEY `buildlist_build_end_time` (`buildlist_build_end_time`),
  KEY `buildlist_build_type` (`buildlist_build_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cells`
--

CREATE TABLE IF NOT EXISTS `cells` (
  `id` smallint(10) unsigned NOT NULL AUTO_INCREMENT,
  `sx` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sy` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `cx` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `cy` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `cell` (`cx`,`cy`),
  KEY `sector` (`sx`,`sy`),
  KEY `coordinates` (`sx`,`sy`,`cx`,`cy`),
  KEY `cy` (`cy`),
  KEY `cx` (`cx`),
  KEY `sy` (`sy`),
  KEY `sx` (`sx`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chat`
--

CREATE TABLE IF NOT EXISTS `chat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL,
  `nick` varchar(50) NOT NULL,
  `text` varchar(255) NOT NULL,
  `color` varchar(15) NOT NULL,
  `user_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `admin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `private` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `channel` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chat_banns`
--

CREATE TABLE IF NOT EXISTS `chat_banns` (
  `user_id` varchar(50) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chat_log`
--

CREATE TABLE IF NOT EXISTS `chat_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL,
  `nick` varchar(50) NOT NULL,
  `text` varchar(255) NOT NULL,
  `color` varchar(15) NOT NULL,
  `user_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `admin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `private` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `channel` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chat_users`
--

CREATE TABLE IF NOT EXISTS `chat_users` (
  `nick` varchar(30) NOT NULL,
  `user_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `kick` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `config_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `config_name` varchar(50) NOT NULL,
  `config_value` text NOT NULL,
  `config_param1` text NOT NULL,
  `config_param2` text NOT NULL,
  PRIMARY KEY (`config_id`),
  UNIQUE KEY `config_name_2` (`config_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=185 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `default_items`
--

CREATE TABLE IF NOT EXISTS `default_items` (
  `item_id` int(10) NOT NULL AUTO_INCREMENT,
  `item_set_id` int(20) NOT NULL DEFAULT '0',
  `item_cat` char(1) NOT NULL,
  `item_object_id` int(10) NOT NULL,
  `item_count` int(10) NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `item_set_id` (`item_set_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=133 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `default_item_sets`
--

CREATE TABLE IF NOT EXISTS `default_item_sets` (
  `set_id` int(10) NOT NULL AUTO_INCREMENT,
  `set_name` varchar(50) NOT NULL,
  `set_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`set_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `defense`
--

CREATE TABLE IF NOT EXISTS `defense` (
  `def_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `def_name` varchar(50) NOT NULL,
  `def_shortcomment` text NOT NULL,
  `def_longcomment` text NOT NULL,
  `def_costs_metal` int(10) unsigned NOT NULL DEFAULT '0',
  `def_costs_crystal` int(10) unsigned NOT NULL DEFAULT '0',
  `def_costs_fuel` int(10) unsigned NOT NULL DEFAULT '0',
  `def_costs_plastic` int(10) unsigned NOT NULL DEFAULT '0',
  `def_costs_food` int(10) unsigned NOT NULL DEFAULT '0',
  `def_costs_power` int(10) unsigned NOT NULL DEFAULT '0',
  `def_power_use` int(10) unsigned NOT NULL DEFAULT '0',
  `def_fuel_use` int(10) unsigned NOT NULL DEFAULT '0',
  `def_prod_power` int(10) unsigned NOT NULL DEFAULT '0',
  `def_fields` smallint(3) unsigned NOT NULL DEFAULT '0',
  `def_show` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `def_buildable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `def_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `def_structure` int(10) unsigned NOT NULL DEFAULT '0',
  `def_shield` int(10) unsigned NOT NULL DEFAULT '0',
  `def_weapon` int(10) unsigned NOT NULL DEFAULT '0',
  `def_heal` int(10) unsigned NOT NULL DEFAULT '0',
  `def_jam` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `def_race_id` int(2) unsigned NOT NULL DEFAULT '0',
  `def_cat_id` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `def_max_count` int(10) unsigned NOT NULL DEFAULT '999999',
  `def_points` decimal(20,3) unsigned NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`def_id`),
  KEY `def_name` (`def_name`),
  KEY `def_order` (`def_order`),
  KEY `def_max_count` (`def_max_count`),
  KEY `def_battlepoints` (`def_points`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `deflist`
--

CREATE TABLE IF NOT EXISTS `deflist` (
  `deflist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `deflist_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `deflist_def_id` int(10) unsigned NOT NULL DEFAULT '0',
  `deflist_entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `deflist_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`deflist_id`),
  UNIQUE KEY `deflist_all` (`deflist_user_id`,`deflist_entity_id`,`deflist_def_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `def_cat`
--

CREATE TABLE IF NOT EXISTS `def_cat` (
  `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `cat_order` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `cat_color` char(7) COLLATE utf8_unicode_ci NOT NULL DEFAULT '#ffffff',
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `def_queue`
--

CREATE TABLE IF NOT EXISTS `def_queue` (
  `queue_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `queue_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_def_id` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_cnt` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_objtime` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_build_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `queue_user_click_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`queue_id`),
  KEY `queue_user_id` (`queue_user_id`),
  KEY `queue_planet_id` (`queue_entity_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `def_requirements`
--

CREATE TABLE IF NOT EXISTS `def_requirements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `obj_id` (`obj_id`,`req_building_id`,`req_tech_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=79 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `entities`
--

CREATE TABLE IF NOT EXISTS `entities` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `cell_id` int(6) unsigned NOT NULL,
  `code` char(1) DEFAULT NULL,
  `pos` int(2) unsigned NOT NULL DEFAULT '0',
  `lastvisited` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cell_id` (`cell_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 COMMENT='Entities in Space, acts as fleet targets' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_execrate` int(10) unsigned NOT NULL DEFAULT '100',
  `event_title` varchar(100) NOT NULL,
  `event_text` text NOT NULL,
  `event_ask` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `event_answer_pos` text NOT NULL,
  `event_answer_neg` text NOT NULL,
  `event_reward_p_rate` decimal(4,4) unsigned NOT NULL DEFAULT '0.0100',
  `event_reward_p_metal_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_metal_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_crystal_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_crystal_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_plastic_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_plastic_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_fuel_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_fuel_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_food_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_food_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_people_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_people_max` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_ship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_ship_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_ship_max` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_def_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_def_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_def_max` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_building_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_p_tech_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_rate` decimal(4,4) unsigned NOT NULL DEFAULT '0.0100',
  `event_costs_p_metal_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_metal_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_crystal_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_crystal_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_plastic_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_plastic_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_fuel_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_fuel_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_food_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_food_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_people_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_people_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_ship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_ship_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_ship_max` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_def_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_def_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_def_max` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_building_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_p_tech_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_rate` decimal(4,4) unsigned NOT NULL DEFAULT '0.0100',
  `event_reward_n_metal_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_metal_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_crystal_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_crystal_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_plastic_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_plastic_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_fuel_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_fuel_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_food_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_food_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_people_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_people_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_ship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_ship_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_ship_max` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_def_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_def_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_def_max` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_building_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_n_tech_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_rate` decimal(4,4) unsigned NOT NULL DEFAULT '0.0100',
  `event_costs_n_metal_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_metal_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_crystal_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_crystal_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_plastic_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_plastic_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_fuel_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_fuel_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_food_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_food_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_people_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_people_max` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_ship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_ship_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_ship_max` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_def_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_def_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_def_max` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_building_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_n_tech_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `event_alien_rate` decimal(4,4) unsigned NOT NULL DEFAULT '0.0100',
  `event_alien_ship1_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_alien_ship1_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_alien_ship1_max` int(10) unsigned NOT NULL DEFAULT '0',
  `event_alien_ship2_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_alien_ship2_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_alien_ship2_max` int(10) unsigned NOT NULL DEFAULT '0',
  `event_alien_ship3_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_alien_ship3_min` int(10) unsigned NOT NULL DEFAULT '0',
  `event_alien_ship3_max` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `events_exec`
--

CREATE TABLE IF NOT EXISTS `events_exec` (
  `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_planet_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_cell_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_title` varchar(100) NOT NULL,
  `event_text` text NOT NULL,
  `event_ask` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `event_answer_pos` text NOT NULL,
  `event_answer_neg` text NOT NULL,
  `event_reward_metal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_reward_crystal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_reward_plastic` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_reward_fuel` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_reward_food` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_reward_people` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_reward_shid_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_ship_num` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_def_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_def_num` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_building_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `event_reward_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_reward_tech_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `event_costs_metal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_costs_crystal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_costs_plastic` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_costs_fuel` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_costs_food` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_costs_people` bigint(12) unsigned NOT NULL DEFAULT '0',
  `event_costs_ship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_ship_num` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_def_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_def_num` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_building_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `event_costs_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_costs_tech_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `event_alien_ship1_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_alien_ship1` int(10) unsigned NOT NULL DEFAULT '0',
  `event_alien_ship2_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_alien_ship2` int(10) unsigned NOT NULL DEFAULT '0',
  `event_alien_ship3_id` int(10) unsigned NOT NULL DEFAULT '0',
  `event_alien_ship3` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fleet`
--

CREATE TABLE IF NOT EXISTS `fleet` (
  `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` smallint(10) unsigned NOT NULL DEFAULT '0',
  `leader_id` mediumint(10) unsigned NOT NULL DEFAULT '0',
  `entity_from` smallint(10) unsigned NOT NULL DEFAULT '0',
  `entity_to` smallint(10) unsigned NOT NULL DEFAULT '0',
  `next_id` smallint(10) unsigned NOT NULL DEFAULT '0',
  `launchtime` int(10) unsigned NOT NULL DEFAULT '0',
  `landtime` int(10) unsigned NOT NULL DEFAULT '0',
  `nextactiontime` int(10) unsigned NOT NULL DEFAULT '0',
  `action` char(15) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0: Departure, 1: Arrival, 2: Cancelled',
  `pilots` bigint(12) unsigned NOT NULL DEFAULT '0',
  `usage_fuel` int(10) unsigned NOT NULL DEFAULT '0',
  `usage_food` int(10) unsigned NOT NULL DEFAULT '0',
  `usage_power` int(10) unsigned NOT NULL,
  `support_usage_fuel` int(10) unsigned NOT NULL DEFAULT '0',
  `support_usage_food` int(10) unsigned NOT NULL DEFAULT '0',
  `res_metal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `res_crystal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `res_plastic` bigint(12) unsigned NOT NULL DEFAULT '0',
  `res_fuel` bigint(12) unsigned NOT NULL DEFAULT '0',
  `res_food` bigint(12) unsigned NOT NULL DEFAULT '0',
  `res_power` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_people` bigint(12) unsigned NOT NULL DEFAULT '0',
  `fetch_metal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `fetch_crystal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `fetch_plastic` bigint(12) unsigned NOT NULL DEFAULT '0',
  `fetch_fuel` bigint(12) unsigned NOT NULL DEFAULT '0',
  `fetch_food` bigint(12) unsigned NOT NULL DEFAULT '0',
  `fetch_power` bigint(20) unsigned NOT NULL DEFAULT '0',
  `fetch_people` bigint(12) unsigned NOT NULL DEFAULT '0',
  `flag` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'UpdateTestFlag',
  PRIMARY KEY (`id`),
  KEY `fleet_user_id` (`user_id`),
  KEY `fleet_landtime` (`landtime`),
  KEY `entity_from` (`entity_from`),
  KEY `entity_to` (`entity_to`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=610 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fleet_bookmarks`
--

CREATE TABLE IF NOT EXISTS `fleet_bookmarks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `target_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `ships` text NOT NULL,
  `res` text NOT NULL,
  `resfetch` text NOT NULL,
  `action` char(15) NOT NULL,
  `speed` mediumint(8) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fleet_ships`
--

CREATE TABLE IF NOT EXISTS `fleet_ships` (
  `fs_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fs_fleet_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fs_ship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fs_ship_cnt` int(10) unsigned NOT NULL DEFAULT '0',
  `fs_ship_faked` int(10) unsigned NOT NULL DEFAULT '0',
  `fs_special_ship` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_exp` int(10) unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_weapon` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_structure` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_shield` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_heal` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_capacity` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_speed` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_pilots` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_tarn` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_antrax` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_forsteal` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_build_destroy` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_antrax_food` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `fs_special_ship_bonus_deactivade` tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fs_id`),
  KEY `fs_fleet_id` (`fs_fleet_id`),
  KEY `fs_ship_id` (`fs_ship_id`),
  KEY `fs_fleet_id_2` (`fs_fleet_id`,`fs_ship_faked`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1044 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hostname_cache`
--

CREATE TABLE IF NOT EXISTS `hostname_cache` (
  `addr` char(39) NOT NULL,
  `host` varchar(100) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`addr`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ip_ban`
--

CREATE TABLE IF NOT EXISTS `ip_ban` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `msg` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `login_failures`
--

CREATE TABLE IF NOT EXISTS `login_failures` (
  `failure_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `failure_time` int(10) unsigned NOT NULL DEFAULT '0',
  `failure_ip` varchar(15) NOT NULL,
  `failure_host` varchar(50) NOT NULL,
  `failure_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `failure_pw` char(50) NOT NULL,
  `failure_client` varchar(255) NOT NULL,
  PRIMARY KEY (`failure_id`),
  KEY `failure_user_id` (`failure_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facility` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `severity` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `ip` varchar(39) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `facility` (`facility`),
  KEY `logview` (`facility`,`severity`,`timestamp`),
  KEY `severity` (`severity`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs_alliance`
--

CREATE TABLE IF NOT EXISTS `logs_alliance` (
  `logs_alliance_id` int(10) NOT NULL AUTO_INCREMENT,
  `logs_alliance_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `logs_alliance_text` text NOT NULL,
  `logs_alliance_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `logs_alliance_alliance_tag` varchar(10) NOT NULL DEFAULT '0',
  `logs_alliance_alliance_name` varchar(30) NOT NULL,
  `logs_alliance_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`logs_alliance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs_battle`
--

CREATE TABLE IF NOT EXISTS `logs_battle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facility` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `severity` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `fleet_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` text NOT NULL,
  `entity_user_id` text NOT NULL,
  `user_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_user_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `war` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '1',
  `action` char(15) NOT NULL,
  `landtime` int(10) unsigned NOT NULL DEFAULT '1',
  `result` tinyint(1) unsigned NOT NULL,
  `fleet_ships_cnt` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_ships_cnt` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_defs_cnt` int(10) unsigned NOT NULL DEFAULT '0',
  `fleet_weapon` bigint(12) unsigned NOT NULL DEFAULT '10',
  `fleet_shield` bigint(12) unsigned NOT NULL DEFAULT '0',
  `fleet_structure` bigint(12) unsigned NOT NULL DEFAULT '0',
  `fleet_weapon_bonus` smallint(4) unsigned NOT NULL DEFAULT '0',
  `fleet_shield_bonus` smallint(4) unsigned NOT NULL DEFAULT '0',
  `fleet_structure_bonus` smallint(4) unsigned NOT NULL DEFAULT '0',
  `entity_weapon` bigint(12) unsigned NOT NULL DEFAULT '0',
  `entity_shield` bigint(12) unsigned NOT NULL DEFAULT '0',
  `entity_structure` bigint(12) unsigned NOT NULL DEFAULT '0',
  `entity_weapon_bonus` smallint(4) unsigned NOT NULL DEFAULT '0',
  `entity_shield_bonus` smallint(4) unsigned NOT NULL DEFAULT '0',
  `entity_structure_bonus` smallint(4) unsigned NOT NULL DEFAULT '0',
  `fleet_win_exp` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_win_exp` int(10) unsigned NOT NULL DEFAULT '0',
  `win_metal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `win_crystal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `win_pvc` bigint(12) unsigned NOT NULL DEFAULT '0',
  `win_tritium` bigint(12) unsigned NOT NULL DEFAULT '0',
  `win_food` bigint(12) unsigned NOT NULL DEFAULT '0',
  `tf_metal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `tf_crystal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `tf_pvc` bigint(12) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `logs_battle_fleet_landtime` (`landtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs_battle_queue`
--

CREATE TABLE IF NOT EXISTS `logs_battle_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facility` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `severity` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `fleet_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` text NOT NULL,
  `entity_user_id` text NOT NULL,
  `user_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_user_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `war` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '1',
  `action` char(15) NOT NULL,
  `landtime` int(10) unsigned NOT NULL DEFAULT '1',
  `result` tinyint(1) unsigned NOT NULL,
  `fleet_ships_cnt` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_ships_cnt` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_defs_cnt` int(10) unsigned NOT NULL DEFAULT '0',
  `fleet_weapon` bigint(12) unsigned NOT NULL DEFAULT '10',
  `fleet_shield` bigint(12) unsigned NOT NULL DEFAULT '0',
  `fleet_structure` bigint(12) unsigned NOT NULL DEFAULT '0',
  `fleet_weapon_bonus` smallint(4) unsigned NOT NULL DEFAULT '0',
  `fleet_shield_bonus` smallint(4) unsigned NOT NULL DEFAULT '0',
  `fleet_structure_bonus` smallint(4) unsigned NOT NULL DEFAULT '0',
  `entity_weapon` bigint(12) unsigned NOT NULL DEFAULT '0',
  `entity_shield` bigint(12) unsigned NOT NULL DEFAULT '0',
  `entity_structure` bigint(12) unsigned NOT NULL DEFAULT '0',
  `entity_weapon_bonus` smallint(4) unsigned NOT NULL DEFAULT '0',
  `entity_shield_bonus` smallint(4) unsigned NOT NULL DEFAULT '0',
  `entity_structure_bonus` smallint(4) unsigned NOT NULL DEFAULT '0',
  `fleet_win_exp` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_win_exp` int(10) unsigned NOT NULL DEFAULT '0',
  `win_metal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `win_crystal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `win_pvc` bigint(12) unsigned NOT NULL DEFAULT '0',
  `win_tritium` bigint(12) unsigned NOT NULL DEFAULT '0',
  `win_food` bigint(12) unsigned NOT NULL DEFAULT '0',
  `tf_metal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `tf_crystal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `tf_pvc` bigint(12) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs_fleet`
--

CREATE TABLE IF NOT EXISTS `logs_fleet` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facility` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `severity` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `fleet_id` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_from` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_to` int(10) unsigned NOT NULL DEFAULT '0',
  `launchtime` int(10) unsigned NOT NULL DEFAULT '0',
  `landtime` int(10) unsigned NOT NULL DEFAULT '0',
  `action` char(15) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fleet_res_start` text NOT NULL,
  `fleet_res_end` text NOT NULL,
  `fleet_ships_start` text NOT NULL,
  `fleet_ships_end` text NOT NULL,
  `entity_res_start` text NOT NULL,
  `entity_res_end` text NOT NULL,
  `entity_ships_start` text NOT NULL,
  `entity_ships_end` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs_fleet_queue`
--

CREATE TABLE IF NOT EXISTS `logs_fleet_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facility` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `severity` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `fleet_id` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_from` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_to` int(10) unsigned NOT NULL DEFAULT '0',
  `launchtime` int(10) unsigned NOT NULL DEFAULT '0',
  `landtime` int(10) unsigned NOT NULL DEFAULT '0',
  `action` char(15) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `fleet_res_start` text NOT NULL,
  `fleet_res_end` text NOT NULL,
  `fleet_ships_start` text NOT NULL,
  `fleet_ships_end` text NOT NULL,
  `entity_res_start` text NOT NULL,
  `entity_res_end` text NOT NULL,
  `entity_ships_start` text NOT NULL,
  `entity_ships_end` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs_game`
--

CREATE TABLE IF NOT EXISTS `logs_game` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facility` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `severity` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `ip` varchar(39) NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `object_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `facility` (`facility`,`severity`,`timestamp`),
  KEY `severity` (`severity`,`facility`,`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs_game_queue`
--

CREATE TABLE IF NOT EXISTS `logs_game_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facility` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `severity` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `ip` varchar(39) NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `object_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs_queue`
--

CREATE TABLE IF NOT EXISTS `logs_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facility` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `severity` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `ip` varchar(39) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=98 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `market_auction`
--

CREATE TABLE IF NOT EXISTS `market_auction` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date_start` int(10) unsigned NOT NULL DEFAULT '0',
  `date_end` int(10) unsigned NOT NULL DEFAULT '0',
  `date_delete` int(10) unsigned NOT NULL DEFAULT '0',
  `sell_0` bigint(12) unsigned NOT NULL DEFAULT '0',
  `sell_1` bigint(12) unsigned NOT NULL DEFAULT '0',
  `sell_2` bigint(12) unsigned NOT NULL DEFAULT '0',
  `sell_3` bigint(12) unsigned NOT NULL DEFAULT '0',
  `sell_4` bigint(12) unsigned NOT NULL DEFAULT '0',
  `ship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_count` int(10) unsigned NOT NULL DEFAULT '0',
  `text` varchar(255) NOT NULL,
  `currency_0` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `currency_1` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `currency_2` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `currency_3` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `currency_4` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `current_buyer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `current_buyer_entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `current_buyer_date` int(10) unsigned NOT NULL DEFAULT '0',
  `buy_0` bigint(12) unsigned NOT NULL DEFAULT '0',
  `buy_1` bigint(12) unsigned NOT NULL DEFAULT '0',
  `buy_2` bigint(12) unsigned NOT NULL DEFAULT '0',
  `buy_3` bigint(12) unsigned NOT NULL DEFAULT '0',
  `buy_4` bigint(12) unsigned NOT NULL DEFAULT '0',
  `bidcount` smallint(5) unsigned NOT NULL DEFAULT '0',
  `buyable` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `sent` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `auction_end` (`date_end`),
  KEY `auction_planet_id` (`entity_id`),
  KEY `auction_user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `market_rates`
--

CREATE TABLE IF NOT EXISTS `market_rates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `supply_0` int(11) unsigned NOT NULL DEFAULT '0',
  `supply_1` int(11) unsigned NOT NULL DEFAULT '0',
  `supply_2` int(11) unsigned NOT NULL DEFAULT '0',
  `supply_3` int(11) unsigned NOT NULL DEFAULT '0',
  `supply_4` int(11) unsigned NOT NULL DEFAULT '0',
  `supply_5` int(11) unsigned NOT NULL DEFAULT '0',
  `demand_0` int(11) unsigned NOT NULL DEFAULT '0',
  `demand_1` int(11) unsigned NOT NULL DEFAULT '0',
  `demand_2` int(11) unsigned NOT NULL DEFAULT '0',
  `demand_3` int(11) unsigned NOT NULL DEFAULT '0',
  `demand_4` int(11) unsigned NOT NULL DEFAULT '0',
  `demand_5` int(11) unsigned NOT NULL DEFAULT '0',
  `rate_0` float unsigned NOT NULL DEFAULT '1',
  `rate_1` float unsigned NOT NULL DEFAULT '1',
  `rate_2` float unsigned NOT NULL DEFAULT '1',
  `rate_3` float unsigned NOT NULL DEFAULT '1',
  `rate_4` float unsigned NOT NULL DEFAULT '1',
  `rate_5` float unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `market_ressource`
--

CREATE TABLE IF NOT EXISTS `market_ressource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sell_0` bigint(12) unsigned NOT NULL DEFAULT '0',
  `sell_1` bigint(12) unsigned NOT NULL DEFAULT '0',
  `sell_2` bigint(12) unsigned NOT NULL DEFAULT '0',
  `sell_3` bigint(12) unsigned NOT NULL DEFAULT '0',
  `sell_4` bigint(12) unsigned NOT NULL DEFAULT '0',
  `buy_0` bigint(12) unsigned NOT NULL DEFAULT '0',
  `buy_1` bigint(12) unsigned NOT NULL DEFAULT '0',
  `buy_2` bigint(12) unsigned NOT NULL DEFAULT '0',
  `buy_3` bigint(12) unsigned NOT NULL DEFAULT '0',
  `buy_4` bigint(12) unsigned NOT NULL DEFAULT '0',
  `buyer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `buyer_entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `for_alliance` int(10) unsigned NOT NULL DEFAULT '0',
  `buyable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `text` varchar(255) NOT NULL,
  `datum` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `datum` (`datum`),
  KEY `planet_id` (`entity_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `market_ship`
--

CREATE TABLE IF NOT EXISTS `market_ship` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `count` int(10) unsigned NOT NULL DEFAULT '0',
  `costs_0` bigint(12) unsigned NOT NULL DEFAULT '0',
  `costs_1` bigint(12) unsigned NOT NULL DEFAULT '0',
  `costs_2` bigint(12) unsigned NOT NULL DEFAULT '0',
  `costs_3` bigint(12) unsigned NOT NULL DEFAULT '0',
  `costs_4` bigint(12) unsigned NOT NULL DEFAULT '0',
  `buyer_id` int(10) unsigned NOT NULL DEFAULT '0',
  `buyer_entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `for_alliance` int(10) unsigned NOT NULL DEFAULT '0',
  `buyable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `text` varchar(255) NOT NULL,
  `datum` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `ship_id` (`ship_id`),
  KEY `datum` (`datum`),
  KEY `planet_id` (`entity_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT,
  `message_user_from` smallint(5) unsigned NOT NULL DEFAULT '0',
  `message_user_to` smallint(5) unsigned NOT NULL DEFAULT '0',
  `message_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `message_cat_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `message_read` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `message_archived` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `message_massmail` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `message_deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `message_forwarded` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `message_replied` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `message_mailed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`message_id`),
  KEY `message_user_from` (`message_user_from`),
  KEY `message_user_to` (`message_user_to`),
  KEY `message_read` (`message_read`),
  KEY `message_timestamp` (`message_timestamp`),
  KEY `list` (`message_user_to`,`message_read`,`message_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `message_cat`
--

CREATE TABLE IF NOT EXISTS `message_cat` (
  `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(50) NOT NULL,
  `cat_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cat_desc` text NOT NULL,
  `cat_sender` varchar(50) NOT NULL,
  PRIMARY KEY (`cat_id`),
  KEY `cat_order` (`cat_order`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `message_data`
--

CREATE TABLE IF NOT EXISTS `message_data` (
  `id` mediumint(8) unsigned NOT NULL,
  `subject` varchar(200) NOT NULL,
  `text` text NOT NULL,
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fleet_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `message_ignore`
--

CREATE TABLE IF NOT EXISTS `message_ignore` (
  `ignore_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ignore_owner_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ignore_target_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ignore_id`),
  KEY `ignore_owner_id` (`ignore_owner_id`),
  KEY `ignore_target_id` (`ignore_target_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `minimap`
--

CREATE TABLE IF NOT EXISTS `minimap` (
  `field_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `field_x` int(3) unsigned NOT NULL DEFAULT '0',
  `field_y` int(3) unsigned NOT NULL DEFAULT '0',
  `field_typ_id` int(3) unsigned NOT NULL DEFAULT '0',
  `field_event_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `minimap_field_types`
--

CREATE TABLE IF NOT EXISTS `minimap_field_types` (
  `field_typ_id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `field_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `field_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `field_blocked` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`field_typ_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `missilelist`
--

CREATE TABLE IF NOT EXISTS `missilelist` (
  `missilelist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `missilelist_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `missilelist_entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `missilelist_missile_id` int(10) unsigned NOT NULL DEFAULT '0',
  `missilelist_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`missilelist_id`),
  KEY `missilelist_missile_id` (`missilelist_missile_id`),
  KEY `missilelist_user_id` (`missilelist_user_id`,`missilelist_entity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `missiles`
--

CREATE TABLE IF NOT EXISTS `missiles` (
  `missile_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `missile_name` varchar(50) NOT NULL,
  `missile_sdesc` text NOT NULL,
  `missile_ldesc` text NOT NULL,
  `missile_costs_metal` int(10) unsigned NOT NULL DEFAULT '0',
  `missile_costs_crystal` int(10) unsigned NOT NULL DEFAULT '0',
  `missile_costs_plastic` int(10) unsigned NOT NULL DEFAULT '0',
  `missile_costs_fuel` int(10) unsigned NOT NULL DEFAULT '0',
  `missile_costs_food` int(10) unsigned NOT NULL DEFAULT '0',
  `missile_damage` int(10) unsigned NOT NULL,
  `missile_speed` int(10) unsigned NOT NULL,
  `missile_range` int(10) unsigned NOT NULL,
  `missile_deactivate` smallint(3) unsigned NOT NULL DEFAULT '0',
  `missile_def` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `missile_launchable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `missile_show` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`missile_id`),
  KEY `missile_name` (`missile_name`),
  KEY `missile_damage` (`missile_damage`),
  KEY `missile_show` (`missile_show`),
  KEY `missile_launchable` (`missile_launchable`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `missile_flights`
--

CREATE TABLE IF NOT EXISTS `missile_flights` (
  `flight_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `flight_entity_from` int(10) unsigned NOT NULL DEFAULT '0',
  `flight_entity_to` int(10) unsigned NOT NULL DEFAULT '0',
  `flight_starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `flight_landtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`flight_id`),
  KEY `flight_planet_from` (`flight_entity_from`),
  KEY `flight_planet_to` (`flight_entity_to`),
  KEY `flight_user_id` (`flight_starttime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `missile_flights_obj`
--

CREATE TABLE IF NOT EXISTS `missile_flights_obj` (
  `obj_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `obj_flight_id` int(10) unsigned NOT NULL DEFAULT '0',
  `obj_missile_id` int(10) unsigned NOT NULL DEFAULT '0',
  `obj_cnt` int(15) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`obj_id`),
  KEY `obj_flight_id` (`obj_flight_id`),
  KEY `obj_missile_id` (`obj_missile_id`),
  KEY `obj_cnt` (`obj_cnt`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `missile_requirements`
--

CREATE TABLE IF NOT EXISTS `missile_requirements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `obj_id` (`obj_id`,`req_building_id`,`req_tech_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `multifire`
--

CREATE TABLE IF NOT EXISTS `multifire` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source_ship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `source_def_id` int(10) unsigned NOT NULL DEFAULT '0',
  `target_ship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `value` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `target_def_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `source_ship_id_2` (`source_ship_id`,`target_def_id`),
  KEY `source_ship_id` (`source_ship_id`,`target_ship_id`),
  KEY `source_def_id` (`source_def_id`,`target_ship_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `nebulas`
--

CREATE TABLE IF NOT EXISTS `nebulas` (
  `id` int(10) unsigned NOT NULL,
  `res_metal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_crystal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_plastic` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_fuel` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_food` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_power` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ng_ships`
--

CREATE TABLE IF NOT EXISTS `ng_ships` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `short_desc` varchar(255) NOT NULL,
  `long_desc` text NOT NULL,
  `type_id` int(10) unsigned NOT NULL DEFAULT '1',
  `race_id` int(10) unsigned NOT NULL DEFAULT '0',
  `custom_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `buildable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `tradeable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `launchable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `helpvisible` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `hull` smallint(5) unsigned NOT NULL DEFAULT '1',
  `regeneration` smallint(5) unsigned NOT NULL,
  `shield0` smallint(5) unsigned NOT NULL DEFAULT '0',
  `shield1` smallint(5) unsigned NOT NULL DEFAULT '0',
  `shield2` smallint(5) unsigned NOT NULL DEFAULT '0',
  `damage0` smallint(5) unsigned NOT NULL DEFAULT '0',
  `damage1` smallint(5) unsigned NOT NULL DEFAULT '0',
  `damage2` smallint(5) unsigned NOT NULL DEFAULT '0',
  `multifire0` smallint(5) unsigned NOT NULL DEFAULT '0',
  `multifire1` smallint(5) unsigned NOT NULL DEFAULT '0',
  `multifire2` smallint(5) unsigned NOT NULL DEFAULT '0',
  `agility` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `costs0` int(10) unsigned NOT NULL DEFAULT '0',
  `costs1` int(10) unsigned NOT NULL DEFAULT '0',
  `costs2` int(10) unsigned NOT NULL DEFAULT '0',
  `costs3` int(10) unsigned NOT NULL DEFAULT '0',
  `costs4` int(10) unsigned NOT NULL DEFAULT '0',
  `costs5` int(10) unsigned NOT NULL DEFAULT '0',
  `costs6` int(10) unsigned NOT NULL DEFAULT '0',
  `propulsion_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fuel` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 't per 100 ae',
  `base_fuel` int(10) unsigned NOT NULL DEFAULT '0',
  `speed` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ae per hour',
  `pilots` smallint(5) unsigned NOT NULL DEFAULT '0',
  `delay` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'launch/landing delay in seconds',
  `capacity` int(10) unsigned NOT NULL DEFAULT '0',
  `capacity_fuel` int(10) unsigned NOT NULL DEFAULT '0',
  `capacity_people` int(10) unsigned NOT NULL DEFAULT '0',
  `xp_base` int(10) unsigned NOT NULL DEFAULT '0',
  `xp_factor` float unsigned NOT NULL DEFAULT '2',
  `points` int(10) unsigned NOT NULL DEFAULT '0',
  `actions` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `notepad`
--

CREATE TABLE IF NOT EXISTS `notepad` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `list` (`user_id`,`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `notepad_data`
--

CREATE TABLE IF NOT EXISTS `notepad_data` (
  `id` mediumint(8) unsigned NOT NULL,
  `subject` varchar(100) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `obj_transforms`
--

CREATE TABLE IF NOT EXISTS `obj_transforms` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `def_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `costs_metal` int(10) unsigned NOT NULL DEFAULT '0',
  `costs_crystal` int(10) unsigned NOT NULL DEFAULT '0',
  `costs_plastic` int(10) unsigned NOT NULL DEFAULT '0',
  `costs_fuel` int(10) unsigned NOT NULL DEFAULT '0',
  `costs_food` int(10) unsigned NOT NULL DEFAULT '0',
  `costs_power` int(10) unsigned NOT NULL DEFAULT '0',
  `costs_factor_sd` decimal(2,1) unsigned NOT NULL DEFAULT '0.0',
  `costs_factor_ds` decimal(2,1) unsigned NOT NULL DEFAULT '1.0',
  `num_def` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `def_id` (`def_id`),
  UNIQUE KEY `ship_id` (`ship_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `planets`
--

CREATE TABLE IF NOT EXISTS `planets` (
  `id` int(10) unsigned NOT NULL,
  `planet_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_user_main` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `planet_user_changed` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_last_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_name` varchar(30) NOT NULL,
  `planet_type_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `planet_fields` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_fields_extra` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_fields_used` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_image` varchar(50) NOT NULL DEFAULT '0',
  `planet_temp_from` int(10) NOT NULL DEFAULT '0',
  `planet_temp_to` int(10) NOT NULL DEFAULT '0',
  `planet_semi_major_axis` decimal(5,3) unsigned NOT NULL DEFAULT '1.000',
  `planet_ecccentricity` decimal(4,3) unsigned NOT NULL DEFAULT '0.000',
  `planet_mass` int(8) unsigned NOT NULL DEFAULT '1',
  `planet_res_metal` decimal(18,6) unsigned NOT NULL DEFAULT '0.000000',
  `planet_res_crystal` decimal(18,6) unsigned NOT NULL DEFAULT '0.000000',
  `planet_res_fuel` decimal(18,6) unsigned NOT NULL DEFAULT '0.000000',
  `planet_res_plastic` decimal(18,6) unsigned NOT NULL DEFAULT '0.000000',
  `planet_res_food` decimal(18,6) unsigned NOT NULL DEFAULT '0.000000',
  `planet_use_power` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_last_updated` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_bunker_metal` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_bunker_crystal` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_bunker_plastic` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_bunker_fuel` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_bunker_food` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_prod_metal` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_prod_crystal` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_prod_plastic` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_prod_fuel` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_prod_food` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_prod_power` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_prod_people` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_store_metal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `planet_store_crystal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `planet_store_plastic` bigint(12) unsigned NOT NULL DEFAULT '0',
  `planet_store_fuel` bigint(12) unsigned NOT NULL DEFAULT '0',
  `planet_store_food` bigint(12) unsigned NOT NULL DEFAULT '0',
  `planet_wf_metal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `planet_wf_crystal` bigint(12) unsigned NOT NULL DEFAULT '0',
  `planet_wf_plastic` bigint(12) unsigned NOT NULL DEFAULT '0',
  `planet_people` decimal(18,6) unsigned NOT NULL DEFAULT '0.000000',
  `planet_people_place` int(10) unsigned NOT NULL DEFAULT '0',
  `planet_desc` text NOT NULL,
  `invadedby` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `planet_name` (`planet_name`),
  KEY `planet_last_updated` (`planet_last_updated`),
  KEY `mainplanet` (`planet_user_id`,`planet_user_main`,`planet_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `planet_types`
--

CREATE TABLE IF NOT EXISTS `planet_types` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `type_habitable` int(1) unsigned NOT NULL DEFAULT '1',
  `type_comment` text COLLATE utf8_unicode_ci NOT NULL,
  `type_f_metal` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `type_f_crystal` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `type_f_plastic` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `type_f_fuel` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `type_f_food` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `type_f_power` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `type_f_population` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `type_f_researchtime` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `type_f_buildtime` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `type_collect_gas` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `type_consider` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`type_id`),
  KEY `type_name` (`type_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `races`
--

CREATE TABLE IF NOT EXISTS `races` (
  `race_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `race_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`race_id`),
  KEY `race_name` (`race_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `type` enum('battle','spy','explore','market','crypto','other') NOT NULL DEFAULT 'other',
  `read` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `archived` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `entity1_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity2_id` int(10) unsigned NOT NULL DEFAULT '0',
  `opponent1_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `reports_battle`
--

CREATE TABLE IF NOT EXISTS `reports_battle` (
  `id` int(10) unsigned NOT NULL,
  `subtype` char(20) NOT NULL DEFAULT 'other',
  `fleet_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user` text NOT NULL,
  `entity_user` text NOT NULL,
  `ships` text NOT NULL,
  `entity_ships` text NOT NULL,
  `entity_def` text NOT NULL,
  `weapon_tech` smallint(4) unsigned NOT NULL DEFAULT '100',
  `shield_tech` smallint(4) unsigned NOT NULL DEFAULT '100',
  `structure_tech` smallint(4) unsigned NOT NULL DEFAULT '100',
  `weapon_1` bigint(12) unsigned NOT NULL DEFAULT '0',
  `weapon_2` bigint(12) unsigned NOT NULL DEFAULT '0',
  `weapon_3` bigint(12) unsigned NOT NULL DEFAULT '0',
  `weapon_4` bigint(12) unsigned NOT NULL DEFAULT '0',
  `weapon_5` bigint(12) unsigned NOT NULL DEFAULT '0',
  `shield` bigint(12) unsigned NOT NULL DEFAULT '0',
  `structure` bigint(12) unsigned NOT NULL DEFAULT '0',
  `heal_1` bigint(12) unsigned NOT NULL DEFAULT '0',
  `heal_2` bigint(12) unsigned NOT NULL DEFAULT '0',
  `heal_3` bigint(12) unsigned NOT NULL DEFAULT '0',
  `heal_4` bigint(12) unsigned NOT NULL DEFAULT '0',
  `heal_5` bigint(12) unsigned NOT NULL DEFAULT '0',
  `count_1` int(10) unsigned NOT NULL DEFAULT '0',
  `count_2` int(10) unsigned NOT NULL DEFAULT '0',
  `count_3` int(10) unsigned NOT NULL DEFAULT '0',
  `count_4` int(10) unsigned NOT NULL DEFAULT '0',
  `count_5` int(10) unsigned NOT NULL DEFAULT '0',
  `exp` int(10) NOT NULL DEFAULT '-1',
  `entity_weapon_tech` smallint(4) unsigned NOT NULL DEFAULT '100',
  `entity_shield_tech` smallint(4) unsigned NOT NULL DEFAULT '100',
  `entity_structure_tech` smallint(4) unsigned NOT NULL DEFAULT '100',
  `entity_weapon_1` bigint(12) unsigned NOT NULL DEFAULT '0',
  `entity_weapon_2` bigint(12) unsigned NOT NULL DEFAULT '0',
  `entity_weapon_3` bigint(12) unsigned NOT NULL DEFAULT '0',
  `entity_weapon_4` bigint(12) unsigned NOT NULL DEFAULT '0',
  `entity_weapon_5` bigint(12) unsigned NOT NULL DEFAULT '0',
  `entity_shield` bigint(12) unsigned NOT NULL DEFAULT '0',
  `entity_structure` bigint(12) unsigned NOT NULL DEFAULT '0',
  `entity_heal_1` bigint(12) unsigned NOT NULL DEFAULT '0',
  `entity_heal_2` bigint(12) unsigned NOT NULL DEFAULT '0',
  `entity_heal_3` bigint(12) unsigned NOT NULL DEFAULT '0',
  `entity_heal_4` bigint(12) unsigned NOT NULL DEFAULT '0',
  `entity_heal_5` bigint(12) unsigned NOT NULL DEFAULT '0',
  `entity_count_1` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_count_2` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_count_3` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_count_4` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_count_5` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_exp` int(10) NOT NULL DEFAULT '-1',
  `res_0` bigint(12) unsigned NOT NULL DEFAULT '0',
  `res_1` bigint(12) unsigned NOT NULL DEFAULT '0',
  `res_2` bigint(12) unsigned NOT NULL DEFAULT '0',
  `res_3` bigint(12) unsigned NOT NULL DEFAULT '0',
  `res_4` bigint(12) unsigned NOT NULL DEFAULT '0',
  `res_5` bigint(12) unsigned NOT NULL DEFAULT '0',
  `wf_0` bigint(12) unsigned NOT NULL DEFAULT '0',
  `wf_1` bigint(12) unsigned NOT NULL DEFAULT '0',
  `wf_2` bigint(12) unsigned NOT NULL DEFAULT '0',
  `ships_end` text NOT NULL,
  `entity_ships_end` text NOT NULL,
  `entity_def_end` text NOT NULL,
  `restore` smallint(4) unsigned NOT NULL DEFAULT '0',
  `result` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `reports_market`
--

CREATE TABLE IF NOT EXISTS `reports_market` (
  `id` int(10) unsigned NOT NULL,
  `subtype` char(20) NOT NULL DEFAULT 'other',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sell_0` bigint(12) unsigned NOT NULL DEFAULT '0',
  `sell_1` bigint(12) unsigned NOT NULL DEFAULT '0',
  `sell_2` bigint(12) unsigned NOT NULL DEFAULT '0',
  `sell_3` bigint(12) unsigned NOT NULL DEFAULT '0',
  `sell_4` bigint(12) unsigned NOT NULL DEFAULT '0',
  `sell_5` bigint(12) unsigned NOT NULL DEFAULT '0',
  `buy_0` bigint(12) unsigned NOT NULL DEFAULT '0',
  `buy_1` bigint(12) unsigned NOT NULL DEFAULT '0',
  `buy_2` bigint(12) unsigned NOT NULL DEFAULT '0',
  `buy_3` bigint(12) unsigned NOT NULL DEFAULT '0',
  `buy_4` bigint(12) unsigned NOT NULL DEFAULT '0',
  `buy_5` bigint(12) unsigned NOT NULL DEFAULT '0',
  `factor` float NOT NULL DEFAULT '1',
  `fleet1_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fleet2_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_count` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp2` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `reports_other`
--

CREATE TABLE IF NOT EXISTS `reports_other` (
  `id` int(10) unsigned NOT NULL,
  `subtype` char(20) NOT NULL DEFAULT 'other',
  `fleet_id` int(10) unsigned NOT NULL DEFAULT '0',
  `res_0` int(10) unsigned NOT NULL DEFAULT '0',
  `res_1` int(10) unsigned NOT NULL DEFAULT '0',
  `res_2` int(10) unsigned NOT NULL DEFAULT '0',
  `res_3` int(10) unsigned NOT NULL DEFAULT '0',
  `res_4` int(10) unsigned NOT NULL DEFAULT '0',
  `res_5` int(10) unsigned NOT NULL DEFAULT '0',
  `ships` text NOT NULL,
  `action` char(20) NOT NULL,
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `reports_spy`
--

CREATE TABLE IF NOT EXISTS `reports_spy` (
  `id` int(10) unsigned NOT NULL,
  `subtype` char(20) NOT NULL DEFAULT 'other',
  `buildings` text NOT NULL,
  `technologies` text NOT NULL,
  `ships` text NOT NULL,
  `defense` text NOT NULL,
  `res_0` int(10) unsigned NOT NULL DEFAULT '0',
  `res_1` int(10) unsigned NOT NULL DEFAULT '0',
  `res_2` int(10) unsigned NOT NULL DEFAULT '0',
  `res_3` int(10) unsigned NOT NULL DEFAULT '0',
  `res_4` int(10) unsigned NOT NULL DEFAULT '0',
  `res_5` int(10) unsigned NOT NULL DEFAULT '0',
  `spydefense` smallint(5) unsigned NOT NULL DEFAULT '0',
  `coverage` smallint(5) unsigned NOT NULL,
  `fleet_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `shiplist`
--

CREATE TABLE IF NOT EXISTS `shiplist` (
  `shiplist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shiplist_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `shiplist_ship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `shiplist_entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `shiplist_bot_id` int(10) unsigned NOT NULL DEFAULT '0',
  `shiplist_count` int(10) unsigned NOT NULL DEFAULT '0',
  `shiplist_bunkered` int(10) unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_exp` int(10) unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_weapon` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_structure` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_shield` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_heal` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_capacity` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_speed` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_pilots` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_tarn` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_antrax` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_forsteal` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_build_destroy` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_antrax_food` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `shiplist_special_ship_bonus_deactivade` tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`shiplist_id`),
  UNIQUE KEY `user_entity_ship_id` (`shiplist_user_id`,`shiplist_entity_id`,`shiplist_ship_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ships`
--

CREATE TABLE IF NOT EXISTS `ships` (
  `ship_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ship_name` varchar(50) NOT NULL,
  `ship_type_id` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ship_shortcomment` text NOT NULL,
  `ship_longcomment` text NOT NULL,
  `ship_costs_metal` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_costs_crystal` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_costs_fuel` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_costs_plastic` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_costs_food` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_costs_power` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_power_use` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_fuel_use` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_fuel_use_launch` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_fuel_use_landing` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_prod_power` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_capacity` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_people_capacity` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_pilots` int(5) unsigned NOT NULL DEFAULT '1',
  `ship_speed` int(10) unsigned NOT NULL DEFAULT '1',
  `ship_time2start` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_time2land` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_show` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ship_buildable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ship_order` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ship_actions` text NOT NULL,
  `ship_bounty_bonus` decimal(4,2) unsigned NOT NULL DEFAULT '0.50',
  `ship_heal` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_structure` bigint(20) unsigned NOT NULL DEFAULT '0',
  `ship_shield` bigint(20) unsigned NOT NULL DEFAULT '0',
  `ship_weapon` bigint(20) unsigned NOT NULL DEFAULT '0',
  `ship_race_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ship_launchable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ship_fieldsprovide` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ship_cat_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ship_fakeable` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `special_ship` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ship_max_count` int(11) unsigned NOT NULL DEFAULT '0',
  `special_ship_max_level` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `special_ship_need_exp` int(10) unsigned NOT NULL DEFAULT '0',
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
  `ship_points` decimal(18,3) unsigned NOT NULL DEFAULT '0.000',
  `ship_alliance_shipyard_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ship_alliance_costs` mediumint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ship_id`),
  KEY `ship_order` (`ship_order`),
  KEY `ship_battlepoints` (`ship_points`),
  KEY `ship_name` (`ship_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=92 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ship_cat`
--

CREATE TABLE IF NOT EXISTS `ship_cat` (
  `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(50) NOT NULL,
  `cat_order` int(2) unsigned NOT NULL DEFAULT '0',
  `cat_color` char(7) NOT NULL,
  PRIMARY KEY (`cat_id`),
  KEY `cat_name` (`cat_name`),
  KEY `cat_order` (`cat_order`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ship_queue`
--

CREATE TABLE IF NOT EXISTS `ship_queue` (
  `queue_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `queue_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_ship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_cnt` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_objtime` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_build_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`queue_id`),
  KEY `queue_user_id` (`queue_user_id`),
  KEY `queue_planet_id` (`queue_entity_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=88 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ship_requirements`
--

CREATE TABLE IF NOT EXISTS `ship_requirements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `obj_id` (`obj_id`,`req_building_id`,`req_tech_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=304 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sol_types`
--

CREATE TABLE IF NOT EXISTS `sol_types` (
  `sol_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `sol_type_consider` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`sol_type_id`),
  KEY `type_name` (`sol_type_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `space`
--

CREATE TABLE IF NOT EXISTS `space` (
  `id` int(10) unsigned NOT NULL,
  `lastvisited` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `specialists`
--

CREATE TABLE IF NOT EXISTS `specialists` (
  `specialist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `specialist_name` char(20) COLLATE utf8_unicode_ci NOT NULL,
  `specialist_desc` text COLLATE utf8_unicode_ci NOT NULL,
  `specialist_enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `specialist_points_req` int(10) unsigned NOT NULL DEFAULT '0',
  `specialist_costs_metal` int(10) unsigned NOT NULL DEFAULT '0',
  `specialist_costs_crystal` int(10) unsigned NOT NULL DEFAULT '0',
  `specialist_costs_plastic` int(10) unsigned NOT NULL DEFAULT '0',
  `specialist_costs_fuel` int(10) unsigned NOT NULL DEFAULT '0',
  `specialist_costs_food` int(10) unsigned NOT NULL DEFAULT '0',
  `specialist_days` tinyint(3) unsigned NOT NULL DEFAULT '14',
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
  `specialist_fleet_max` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `specialist_def_repair` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_spy_level` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `specialist_tarn_level` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `specialist_trade_time` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  `specialist_trade_bonus` decimal(4,2) unsigned NOT NULL DEFAULT '1.00',
  PRIMARY KEY (`specialist_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `stars`
--

CREATE TABLE IF NOT EXISTS `stars` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(30) NOT NULL,
  `type_id` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `techlist`
--

CREATE TABLE IF NOT EXISTS `techlist` (
  `techlist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `techlist_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `techlist_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `techlist_entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `techlist_current_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `techlist_build_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `techlist_build_start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `techlist_build_end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `techlist_prod_percent` int(4) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`techlist_id`),
  KEY `techlist_user_id` (`techlist_user_id`),
  KEY `techlist_tech_id` (`techlist_tech_id`),
  KEY `techlist_planet_id` (`techlist_entity_id`),
  KEY `techlist_build_end_time` (`techlist_build_end_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `technologies`
--

CREATE TABLE IF NOT EXISTS `technologies` (
  `tech_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tech_name` varchar(50) NOT NULL,
  `tech_type_id` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `tech_shortcomment` text NOT NULL,
  `tech_longcomment` text NOT NULL,
  `tech_costs_metal` int(10) unsigned NOT NULL DEFAULT '0',
  `tech_costs_crystal` int(10) unsigned NOT NULL DEFAULT '0',
  `tech_costs_fuel` int(10) unsigned NOT NULL DEFAULT '0',
  `tech_costs_plastic` int(10) unsigned NOT NULL DEFAULT '0',
  `tech_costs_food` int(10) unsigned NOT NULL DEFAULT '0',
  `tech_costs_power` int(10) unsigned NOT NULL DEFAULT '0',
  `tech_build_costs_factor` decimal(4,2) unsigned NOT NULL DEFAULT '0.00',
  `tech_last_level` tinyint(3) unsigned NOT NULL DEFAULT '99',
  `tech_show` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `tech_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `tech_stealable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`tech_id`),
  KEY `tech_name` (`tech_name`),
  KEY `tech_order` (`tech_order`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tech_points`
--

CREATE TABLE IF NOT EXISTS `tech_points` (
  `bp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bp_tech_id` int(10) unsigned NOT NULL,
  `bp_level` tinyint(3) unsigned NOT NULL,
  `bp_points` decimal(20,3) unsigned NOT NULL,
  PRIMARY KEY (`bp_id`),
  KEY `bp_tech_id` (`bp_tech_id`),
  KEY `bp_level` (`bp_level`),
  KEY `bp_points` (`bp_points`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tech_requirements`
--

CREATE TABLE IF NOT EXISTS `tech_requirements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `obj_id` (`obj_id`,`req_building_id`,`req_tech_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=49 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tech_types`
--

CREATE TABLE IF NOT EXISTS `tech_types` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL,
  `type_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type_color` char(7) NOT NULL DEFAULT '#ffffff',
  PRIMARY KEY (`type_id`),
  KEY `type_name` (`type_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tickets`
--

CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(6) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `cat_id` tinyint(10) unsigned NOT NULL DEFAULT '1',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `status` enum('new','assigned','closed') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'new',
  `solution` enum('open','solved','duplicate','invalid') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'open',
  `admin_comment` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `abuse_user_id` (`user_id`),
  KEY `abuse_status` (`status`),
  KEY `abuse_admin_id` (`admin_id`),
  KEY `abuse_timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ticket_cat`
--

CREATE TABLE IF NOT EXISTS `ticket_cat` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `sort` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ticket_msg`
--

CREATE TABLE IF NOT EXISTS `ticket_msg` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tips`
--

CREATE TABLE IF NOT EXISTS `tips` (
  `tip_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tip_text` text COLLATE utf8_unicode_ci NOT NULL,
  `tip_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`tip_id`),
  KEY `tip_active` (`tip_active`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `user_nick` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `user_password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_password_temp` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `user_last_online` int(10) unsigned NOT NULL DEFAULT '0',
  `user_last_login` int(10) unsigned NOT NULL DEFAULT '0',
  `user_logintime` int(10) unsigned NOT NULL DEFAULT '0',
  `user_acttime` int(10) unsigned NOT NULL DEFAULT '0',
  `user_logouttime` int(10) unsigned NOT NULL DEFAULT '0',
  `user_session_key` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `user_email_fix` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `user_ip` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `user_hostname` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `user_blocked_from` int(10) unsigned NOT NULL DEFAULT '0',
  `user_blocked_to` int(10) unsigned NOT NULL DEFAULT '0',
  `user_ban_reason` text COLLATE utf8_unicode_ci NOT NULL,
  `user_attack_bans` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `user_ban_admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_hmode_from` int(10) unsigned NOT NULL DEFAULT '0',
  `user_hmode_to` int(10) unsigned NOT NULL DEFAULT '0',
  `user_race_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `user_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_alliace_shippoints` mediumint(10) unsigned NOT NULL DEFAULT '0',
  `user_alliace_shippoints_used` mediumint(10) unsigned NOT NULL DEFAULT '0',
  `user_sitting_days` tinyint(3) unsigned NOT NULL DEFAULT '20',
  `user_multi_delets` tinyint(10) unsigned NOT NULL DEFAULT '0',
  `user_setup` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_points` bigint(12) unsigned NOT NULL DEFAULT '0',
  `user_rank` smallint(5) unsigned NOT NULL DEFAULT '0',
  `user_rank_highest` smallint(5) unsigned NOT NULL DEFAULT '0',
  `user_alliance_rank_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_registered` int(10) unsigned NOT NULL DEFAULT '1097597003',
  `user_profile_text` text COLLATE utf8_unicode_ci NOT NULL,
  `user_ghost` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Determines wether the user is hidden in rankings',
  `admin` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Determines wether the user and his planets are marked as admin items',
  `user_chatadmin` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Determines wether the user is a chat admin',
  `user_visits` int(10) unsigned NOT NULL DEFAULT '0',
  `user_avatar` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_signature` text COLLATE utf8_unicode_ci NOT NULL,
  `user_client` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_res_from_raid` bigint(12) unsigned NOT NULL DEFAULT '0',
  `user_res_from_tf` bigint(12) unsigned NOT NULL DEFAULT '0',
  `user_res_from_asteroid` bigint(12) unsigned NOT NULL DEFAULT '0',
  `user_res_from_nebula` bigint(12) unsigned NOT NULL DEFAULT '0',
  `user_main_planet_changed` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `user_profile_board_url` char(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_profile_img` char(250) COLLATE utf8_unicode_ci NOT NULL,
  `user_profile_img_check` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `user_specialist_id` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `user_specialist_time` int(10) unsigned NOT NULL DEFAULT '0',
  `user_deleted` int(10) unsigned NOT NULL DEFAULT '0',
  `user_observe` text COLLATE utf8_unicode_ci NOT NULL,
  `lastinvasion` int(10) unsigned NOT NULL DEFAULT '0',
  `spyattack_counter` int(10) unsigned NOT NULL DEFAULT '0',
  `discoverymask` text COLLATE utf8_unicode_ci NOT NULL,
  `discoverymask_last_updated` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  KEY `user_name` (`user_name`),
  KEY `user_nick` (`user_nick`),
  KEY `user_rank_current` (`user_rank`),
  KEY `user_points` (`user_points`),
  KEY `user_session_key` (`user_session_key`),
  KEY `user_acttime` (`user_acttime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_comments`
--

CREATE TABLE IF NOT EXISTS `user_comments` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `comment_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `comment_admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `comment_text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Admin comments on users' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_log`
--

CREATE TABLE IF NOT EXISTS `user_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(9) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `zone` varchar(30) NOT NULL,
  `message` text NOT NULL,
  `host` varchar(50) NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_minimap`
--

CREATE TABLE IF NOT EXISTS `user_minimap` (
  `minimap_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `minimap_sx` int(3) unsigned NOT NULL DEFAULT '1',
  `minimap_sy` int(3) unsigned NOT NULL DEFAULT '1',
  `minimap_cx` int(3) unsigned NOT NULL DEFAULT '1',
  `minimap_cy` int(3) unsigned NOT NULL DEFAULT '1',
  `minimap_user_fly_points` int(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`minimap_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_multi`
--

CREATE TABLE IF NOT EXISTS `user_multi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `multi_id` int(10) unsigned NOT NULL DEFAULT '0',
  `connection` varchar(50) NOT NULL DEFAULT '0',
  `activ` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_multi_user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_onlinestats`
--

CREATE TABLE IF NOT EXISTS `user_onlinestats` (
  `stats_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stats_timestamp` int(10) unsigned NOT NULL,
  `stats_count` int(5) unsigned NOT NULL,
  `stats_regcount` int(5) unsigned NOT NULL,
  PRIMARY KEY (`stats_id`),
  KEY `stats_count` (`stats_count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_points`
--

CREATE TABLE IF NOT EXISTS `user_points` (
  `point_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `point_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `point_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `point_points` bigint(12) unsigned NOT NULL DEFAULT '0',
  `point_ship_points` bigint(12) unsigned NOT NULL DEFAULT '0',
  `point_tech_points` bigint(12) unsigned NOT NULL DEFAULT '0',
  `point_building_points` bigint(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`point_id`),
  KEY `point_user_id` (`point_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Speichert den Punkteverlauf der Spieler' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_properties`
--

CREATE TABLE IF NOT EXISTS `user_properties` (
  `id` int(11) NOT NULL,
  `image_ext` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `image_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `css_style` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `game_width` tinyint(3) unsigned NOT NULL DEFAULT '90',
  `planet_circle_width` smallint(4) unsigned NOT NULL DEFAULT '450',
  `item_show` varchar(6) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'full',
  `item_order_ship` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'name',
  `item_order_def` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'name',
  `item_order_bookmark` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT 'bookmarks.id',
  `item_order_way` varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ASC',
  `image_filter` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `msgsignature` text COLLATE utf8_unicode_ci NOT NULL,
  `msgcreation_preview` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `msg_preview` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `helpbox` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `notebox` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `msg_copy` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `msg_blink` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `spyship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `spyship_count` int(5) unsigned NOT NULL DEFAULT '1',
  `analyzeship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `analyzeship_count` int(5) unsigned NOT NULL DEFAULT '1',
  `show_cellreports` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `havenships_buttons` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `show_adds` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `fleet_rtn_msg` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `small_res_box` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `startup_chat` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `chat_color` varchar(6) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ffffff',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_ratings`
--

CREATE TABLE IF NOT EXISTS `user_ratings` (
  `id` smallint(5) unsigned NOT NULL,
  `battles_fought` smallint(5) unsigned NOT NULL,
  `battles_won` smallint(5) unsigned NOT NULL,
  `battles_lost` smallint(5) unsigned NOT NULL,
  `battle_rating` smallint(5) unsigned NOT NULL,
  `trades_sell` smallint(5) unsigned NOT NULL,
  `trades_buy` smallint(5) unsigned NOT NULL DEFAULT '0',
  `trade_rating` smallint(5) unsigned NOT NULL,
  `diplomacy_rating` smallint(5) unsigned NOT NULL,
  `elorating` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`battle_rating`),
  KEY `id_2` (`id`,`trade_rating`),
  KEY `id_3` (`id`,`diplomacy_rating`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_sessionlog`
--

CREATE TABLE IF NOT EXISTS `user_sessionlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` char(40) NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_addr` varchar(40) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `time_login` int(10) unsigned NOT NULL DEFAULT '0',
  `time_action` int(10) unsigned NOT NULL DEFAULT '0',
  `time_logout` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_sessions`
--

CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` char(40) NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_addr` varchar(40) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `time_login` int(11) unsigned NOT NULL DEFAULT '0',
  `time_action` int(11) unsigned NOT NULL DEFAULT '0',
  `last_span` int(10) NOT NULL DEFAULT '0',
  `bot_count` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_sitting`
--

CREATE TABLE IF NOT EXISTS `user_sitting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sitter_id` int(10) unsigned NOT NULL DEFAULT '0',
  `password` varchar(255) NOT NULL DEFAULT '0',
  `date_from` int(10) unsigned NOT NULL DEFAULT '0',
  `date_to` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_sitting_sitter_user_id` (`sitter_id`),
  KEY `user_sitting_user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_stats`
--

CREATE TABLE IF NOT EXISTS `user_stats` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `points` bigint(12) unsigned NOT NULL DEFAULT '0',
  `points_ships` bigint(12) unsigned NOT NULL DEFAULT '0',
  `points_tech` bigint(12) unsigned NOT NULL DEFAULT '0',
  `points_buildings` bigint(12) unsigned NOT NULL DEFAULT '0',
  `points_exp` int(10) unsigned NOT NULL DEFAULT '0',
  `rank` smallint(5) unsigned NOT NULL DEFAULT '0',
  `rank_ships` smallint(6) unsigned NOT NULL DEFAULT '0',
  `rank_tech` smallint(6) unsigned NOT NULL DEFAULT '0',
  `rank_buildings` smallint(6) unsigned NOT NULL DEFAULT '0',
  `rank_exp` smallint(6) unsigned NOT NULL DEFAULT '0',
  `rankshift` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `rankshift_ships` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `rankshift_tech` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `rankshift_buildings` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `rankshift_exp` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `nick` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `alliance_tag` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `alliance_id` smallint(5) unsigned DEFAULT '0',
  `race_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `sx` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `sy` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `blocked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `inactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hmod` tinyint(1) unsigned NOT NULL DEFAULT '0',
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
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_surveillance`
--

CREATE TABLE IF NOT EXISTS `user_surveillance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `page` varchar(50) NOT NULL,
  `request` text NOT NULL,
  `post` text NOT NULL,
  `session` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_warnings`
--

CREATE TABLE IF NOT EXISTS `user_warnings` (
  `warning_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warning_user_id` int(10) unsigned NOT NULL,
  `warning_date` int(10) unsigned NOT NULL,
  `warning_text` text COLLATE utf8_unicode_ci NOT NULL,
  `warning_admin_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`warning_id`),
  KEY `warning_user_id` (`warning_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wormholes`
--

CREATE TABLE IF NOT EXISTS `wormholes` (
  `id` int(10) unsigned NOT NULL,
  `target_id` int(10) unsigned NOT NULL DEFAULT '0',
  `changed` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `target_id` (`target_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `message_data`
--
ALTER TABLE `message_data`
  ADD CONSTRAINT `message_data_ibfk_1` FOREIGN KEY (`id`) REFERENCES `messages` (`message_id`) ON DELETE CASCADE;
