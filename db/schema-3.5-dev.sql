-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 13. Nov 2014 um 07:55
-- Server Version: 5.6.20
-- PHP-Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `etoa_test`
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
  `domain` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin_notes`
--

CREATE TABLE IF NOT EXISTS `admin_notes` (
`notes_id` int(10) unsigned NOT NULL,
  `admin_id` int(10) unsigned NOT NULL,
  `titel` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `date` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin_users`
--

CREATE TABLE IF NOT EXISTS `admin_users` (
`user_id` int(10) unsigned NOT NULL,
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
  `is_contact` tinyint(3) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin_user_log`
--

CREATE TABLE IF NOT EXISTS `admin_user_log` (
`log_id` int(10) unsigned NOT NULL,
  `log_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `log_session_key` varchar(250) NOT NULL,
  `log_logintime` int(10) unsigned NOT NULL DEFAULT '0',
  `log_logouttime` int(10) unsigned NOT NULL DEFAULT '0',
  `log_acttime` int(10) unsigned NOT NULL DEFAULT '0',
  `log_ip` varchar(20) NOT NULL,
  `log_hostname` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin_user_sessionlog`
--

CREATE TABLE IF NOT EXISTS `admin_user_sessionlog` (
`id` int(10) unsigned NOT NULL,
  `session_id` char(40) NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_addr` varchar(40) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `time_login` int(10) unsigned NOT NULL DEFAULT '0',
  `time_action` int(10) unsigned NOT NULL DEFAULT '0',
  `time_logout` int(10) unsigned NOT NULL DEFAULT '0'
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
  `time_action` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `allianceboard_cat`
--

CREATE TABLE IF NOT EXISTS `allianceboard_cat` (
`cat_id` int(10) unsigned NOT NULL,
  `cat_name` varchar(50) NOT NULL,
  `cat_desc` text NOT NULL,
  `cat_order` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `cat_bullet` varchar(255) NOT NULL,
  `cat_alliance_id` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `allianceboard_catranks`
--

CREATE TABLE IF NOT EXISTS `allianceboard_catranks` (
`cr_id` int(10) unsigned NOT NULL,
  `cr_rank_id` int(10) unsigned NOT NULL,
  `cr_cat_id` int(10) unsigned NOT NULL,
  `cr_bnd_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `allianceboard_posts`
--

CREATE TABLE IF NOT EXISTS `allianceboard_posts` (
`post_id` int(10) unsigned NOT NULL,
  `post_topic_id` int(10) unsigned NOT NULL DEFAULT '0',
  `post_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `post_user_nick` varchar(15) NOT NULL,
  `post_text` text NOT NULL,
  `post_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `post_changed` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `allianceboard_topics`
--

CREATE TABLE IF NOT EXISTS `allianceboard_topics` (
`topic_id` int(10) unsigned NOT NULL,
  `topic_cat_id` int(10) unsigned NOT NULL DEFAULT '0',
  `topic_bnd_id` int(10) unsigned NOT NULL DEFAULT '0',
  `topic_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `topic_user_nick` varchar(15) NOT NULL,
  `topic_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `topic_subject` varchar(100) NOT NULL,
  `topic_count` int(10) unsigned NOT NULL DEFAULT '0',
  `topic_top` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `topic_closed` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliances`
--

CREATE TABLE IF NOT EXISTS `alliances` (
`alliance_id` int(10) unsigned NOT NULL,
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
  `alliance_objects_for_members` tinyint(3) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Allianz-Daten' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_applications`
--

CREATE TABLE IF NOT EXISTS `alliance_applications` (
  `user_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `alliance_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_bnd`
--

CREATE TABLE IF NOT EXISTS `alliance_bnd` (
  `alliance_bnd_name` varchar(30) NOT NULL,
`alliance_bnd_id` int(10) unsigned NOT NULL,
  `alliance_bnd_alliance_id1` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_bnd_alliance_id2` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_bnd_level` int(1) unsigned NOT NULL DEFAULT '0',
  `alliance_bnd_text` text NOT NULL,
  `alliance_bnd_date` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_bnd_text_pub` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `alliance_bnd_points` int(2) unsigned NOT NULL DEFAULT '0',
  `alliance_bnd_diplomat_id` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_buildings`
--

CREATE TABLE IF NOT EXISTS `alliance_buildings` (
`alliance_building_id` tinyint(3) unsigned NOT NULL,
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
  `alliance_building_needed_level` tinyint(3) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Daten für Tabelle `alliance_buildings`
--

INSERT INTO `alliance_buildings` (`alliance_building_id`, `alliance_building_name`, `alliance_building_shortcomment`, `alliance_building_longcomment`, `alliance_building_costs_metal`, `alliance_building_costs_crystal`, `alliance_building_costs_plastic`, `alliance_building_costs_fuel`, `alliance_building_costs_food`, `alliance_building_build_time`, `alliance_building_costs_factor`, `alliance_building_last_level`, `alliance_building_show`, `alliance_building_needed_id`, `alliance_building_needed_level`) VALUES
(1, 'Zentrale', '', 'Die Zentrale ist das Hauptgebäude der Allianzbasis. Baut dieses aus um weitere Objekte zu erhalten.', 100000, 100000, 70000, 35000, 50000, 3600, '2.00', 4, 1, 0, 0),
(2, 'Handelszentrum', '', 'Das Handelszentrum ermöglicht den risikofreien Handel unter den Allianzmitgliedern. Dieser erlaubt es die Angebote auf einem abgeschotteten Markt anzubieten, auf welchen nur Allianzmitglieder zutritt haben.', 300000, 250000, 350000, 35000, 0, 18000, '2.00', 10, 1, 1, 1),
(3, 'Schiffswerft', '', 'Die Allianzschiffswerft produziert einzelne Schiffsteile, mit welchen ein ganzes Schiff hergestellt werden kann. Je weiter die Werft ausgebaut ist, desto schneller können die Teile hergestellt werden und desto mehr Baupläne für Schiffstypen werden konstruiert.', 145000, 102000, 117000, 80000, 0, 15000, '2.50', 99, 1, 4, 1),
(4, 'Flottenkontrolle', '', 'Flottenkontrolletext hierrein', 100000, 75000, 50000, 25000, 0, 15000, '2.01', 99, 1, 1, 1),
(5, 'Forschungslabor', '', 'Bau dir was', 60000, 90000, 45000, 35000, 0, 15000, '2.00', 99, 1, 1, 1),
(6, 'Kryptocenter', '', '', 250000, 2250000, 250000, 3250000, 0, 20000, '3.00', 10, 1, 1, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_building_cooldown`
--

CREATE TABLE IF NOT EXISTS `alliance_building_cooldown` (
  `cooldown_user_id` int(10) unsigned NOT NULL,
  `cooldown_alliance_building_id` int(10) unsigned NOT NULL,
  `cooldown_end` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_buildlist`
--

CREATE TABLE IF NOT EXISTS `alliance_buildlist` (
`alliance_buildlist_id` int(10) unsigned NOT NULL,
  `alliance_buildlist_alliance_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `alliance_buildlist_building_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `alliance_buildlist_current_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `alliance_buildlist_build_start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_buildlist_build_end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_buildlist_cooldown` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_buildlist_member_for` tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_history`
--

CREATE TABLE IF NOT EXISTS `alliance_history` (
`history_id` int(10) unsigned NOT NULL,
  `history_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `history_text` text NOT NULL,
  `history_alliance_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_news`
--

CREATE TABLE IF NOT EXISTS `alliance_news` (
`alliance_news_id` int(10) unsigned NOT NULL,
  `alliance_news_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_news_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_news_title` varchar(255) NOT NULL,
  `alliance_news_text` text NOT NULL,
  `alliance_news_date` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_news_alliance_to_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_news_changed_date` int(10) unsigned NOT NULL,
  `alliance_news_changed_counter` int(3) unsigned NOT NULL,
  `alliance_news_show` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `alliance_news_ip` char(15) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_points`
--

CREATE TABLE IF NOT EXISTS `alliance_points` (
`point_id` int(10) unsigned NOT NULL,
  `point_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `point_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `point_points` bigint(12) unsigned NOT NULL DEFAULT '0',
  `point_avg` bigint(12) unsigned NOT NULL DEFAULT '0',
  `point_cnt` bigint(12) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Speichert den Punkteverlauf der Allianz' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_polls`
--

CREATE TABLE IF NOT EXISTS `alliance_polls` (
`poll_id` int(10) unsigned NOT NULL,
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
  `poll_active` tinyint(1) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_poll_votes`
--

CREATE TABLE IF NOT EXISTS `alliance_poll_votes` (
`vote_id` int(10) unsigned NOT NULL,
  `vote_poll_id` int(10) unsigned NOT NULL DEFAULT '0',
  `vote_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `vote_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `vote_number` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_rankrights`
--

CREATE TABLE IF NOT EXISTS `alliance_rankrights` (
`rr_id` int(10) unsigned NOT NULL,
  `rr_rank_id` int(10) unsigned NOT NULL DEFAULT '0',
  `rr_right_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_ranks`
--

CREATE TABLE IF NOT EXISTS `alliance_ranks` (
`rank_id` int(10) unsigned NOT NULL,
  `rank_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `rank_name` varchar(30) NOT NULL,
  `rank_level` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `rank_points` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_rights`
--

CREATE TABLE IF NOT EXISTS `alliance_rights` (
`right_id` int(3) unsigned NOT NULL,
  `right_key` varchar(30) NOT NULL,
  `right_desc` text NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Daten für Tabelle `alliance_rights`
--

INSERT INTO `alliance_rights` (`right_id`, `right_key`, `right_desc`) VALUES
(1, 'editdata', 'Allianzdaten (Name, Tag, Beschreibung, Bild, Link) ändern'),
(2, 'viewmembers', 'Mitglieder anschauen'),
(3, 'applicationtemplate', 'Bewerbungsvorlage bearbeiten'),
(4, 'history', 'Allianzgeschichte betrachten'),
(5, 'massmail', 'Allianzinternes Rundmail versenden'),
(6, 'ranks', 'Allianzränge bearbeiten'),
(7, 'alliancenews', 'Allianznews (Rathaus) verfassen'),
(8, 'relations', 'Allianzbeziehungen (Bündnisse / Kriege) verwalten'),
(10, 'allianceboard', 'Forum verwalten'),
(11, 'editmembers', 'Mitglieder verwalten'),
(12, 'applications', 'Bewerbungen bearbeiten'),
(13, 'polls', 'Umfrage erstellen'),
(14, 'fleetminister', 'Allianzflotten bearbeiten'),
(15, 'wings', 'Wings hinzufügen und entfernen'),
(16, 'buildminister', 'Allianzbasis ausbauen (Gebäude, Technologien)'),
(17, 'cryptominister', 'Kryptocenter benutzen');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_spends`
--

CREATE TABLE IF NOT EXISTS `alliance_spends` (
`alliance_spend_id` int(10) unsigned NOT NULL,
  `alliance_spend_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_spend_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_spend_metal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `alliance_spend_crystal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `alliance_spend_plastic` bigint(20) unsigned NOT NULL DEFAULT '0',
  `alliance_spend_fuel` bigint(20) unsigned NOT NULL DEFAULT '0',
  `alliance_spend_food` bigint(20) unsigned NOT NULL DEFAULT '0',
  `alliance_spend_time` int(10) unsigned NOT NULL DEFAULT '0'
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
  `alliance_rank_last` smallint(5) unsigned NOT NULL DEFAULT '0'
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_techlist`
--

CREATE TABLE IF NOT EXISTS `alliance_techlist` (
`alliance_techlist_id` int(10) unsigned NOT NULL,
  `alliance_techlist_alliance_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_tech_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_current_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_build_start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_build_end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_member_for` tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `alliance_technologies`
--

CREATE TABLE IF NOT EXISTS `alliance_technologies` (
`alliance_tech_id` tinyint(3) unsigned NOT NULL,
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
  `alliance_tech_needed_level` tinyint(3) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Daten für Tabelle `alliance_technologies`
--

INSERT INTO `alliance_technologies` (`alliance_tech_id`, `alliance_tech_name`, `alliance_tech_shortcomment`, `alliance_tech_longcomment`, `alliance_tech_costs_metal`, `alliance_tech_costs_crystal`, `alliance_tech_costs_plastic`, `alliance_tech_costs_fuel`, `alliance_tech_costs_food`, `alliance_tech_build_time`, `alliance_tech_costs_factor`, `alliance_tech_last_level`, `alliance_tech_show`, `alliance_tech_needed_id`, `alliance_tech_needed_level`) VALUES
(4, 'Tarntechnik', 'In Zeiten einer neuen Ära mit grösseren Flottenverbänden bestehend aus mehreren Teilflotten, reichte die gewöhnliche Tarntechnik nicht mehr aus. So setzten sich Spieler zusammen und teilten ihr Wissen und ihre Ressourcen, um auch diese Hürde zu überwinden.\r\nJe höher diese Technologie erforscht ist, desto länger bleiben Allianzverbände für den Gegner unentdeckt.', 'In Zeiten einer neuen Ära mit grösseren Flottenverbänden bestehend aus mehreren Teilflotten, reichte die gewöhnliche Tarntechnik nicht mehr aus. So setzten sich Spieler zusammen und teilten ihr Wissen und ihre Ressourcen, um auch diese Hürde zu überwinden.\r\nJe höher diese Technologie erforscht ist, desto länger bleiben Allianzverbände für den Gegner unentdeckt.', 75000, 25000, 50000, 50000, 50000, 900, '1.60', 50, 1, 0, 0),
(5, 'Waffentechnik', '', '', 0, 0, 0, 0, 0, 0, '1.00', 50, 1, 0, 0),
(6, 'Schutzschilder', '', '', 0, 0, 0, 0, 0, 0, '1.00', 50, 1, 5, 2),
(7, 'Panzerung', '', '', 0, 0, 0, 0, 0, 0, '1.00', 50, 1, 5, 2),
(8, 'Spionagetechnik', '', '', 0, 0, 0, 0, 0, 0, '1.00', 0, 1, 5, 1),
(9, 'Antriebstechnologie', '', '', 0, 0, 0, 0, 0, 0, '1.00', 0, 1, 0, 0);

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
  `res_power` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `attack_ban`
--

CREATE TABLE IF NOT EXISTS `attack_ban` (
`attack_ban_id` int(10) unsigned NOT NULL,
  `attack_ban_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `attack_ban_reason` text NOT NULL,
  `attack_ban_time` int(10) unsigned NOT NULL DEFAULT '0',
  `attack_ban_deleted` tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `backend_message_queue`
--

CREATE TABLE IF NOT EXISTS `backend_message_queue` (
`id` int(10) unsigned NOT NULL,
  `cmd` varchar(255) NOT NULL,
  `arg` varchar(255) NOT NULL
) ENGINE=MEMORY DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `bookmarks`
--

CREATE TABLE IF NOT EXISTS `bookmarks` (
`id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `comment` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `buddylist`
--

CREATE TABLE IF NOT EXISTS `buddylist` (
  `bl_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `bl_buddy_id` int(10) unsigned NOT NULL DEFAULT '0',
  `bl_allow` int(10) unsigned NOT NULL DEFAULT '0',
`bl_id` int(10) unsigned NOT NULL,
  `bl_comment` text NOT NULL,
  `bl_comment_buddy` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `buildings`
--

CREATE TABLE IF NOT EXISTS `buildings` (
`building_id` int(10) unsigned NOT NULL,
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
  `building_bunker_fleet_space` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- Daten für Tabelle `buildings`
--

INSERT INTO `buildings` (`building_id`, `building_name`, `building_type_id`, `building_shortcomment`, `building_longcomment`, `building_costs_metal`, `building_costs_crystal`, `building_costs_fuel`, `building_costs_plastic`, `building_costs_food`, `building_costs_power`, `building_build_costs_factor`, `building_demolish_costs_factor`, `building_power_use`, `building_power_req`, `building_fuel_use`, `building_prod_metal`, `building_prod_crystal`, `building_prod_plastic`, `building_prod_fuel`, `building_prod_food`, `building_prod_power`, `building_production_factor`, `building_store_metal`, `building_store_crystal`, `building_store_plastic`, `building_store_fuel`, `building_store_food`, `building_store_factor`, `building_people_place`, `building_last_level`, `building_fields`, `building_show`, `building_order`, `building_fieldsprovide`, `building_workplace`, `building_bunker_res`, `building_bunker_fleet_count`, `building_bunker_fleet_space`) VALUES
(1, 'Titanmine', 2, 'Produziert Titan.', 'Produziert Titan.', 100, 45, 0, 0, 0, 0, '1.90', '0.20', 10, 0, 0, 65, 0, 0, 0, 0, 0, '1.50', 0, 0, 0, 0, 0, '0.00', 0, 50, 2, 1, 0, 0, 0, 0, 0, 0),
(2, 'Siliziummine', 2, 'Produziert Silizium.', 'Produziert Silizium.', 150, 50, 0, 0, 0, 0, '1.90', '0.20', 20, 0, 0, 0, 50, 0, 0, 0, 0, '1.50', 0, 0, 0, 0, 0, '0.00', 0, 50, 2, 1, 1, 0, 0, 0, 0, 0),
(3, 'Chemiefabrik', 2, 'Produziert PVC.', 'Produziert PVC.', 100, 80, 0, 0, 0, 0, '1.90', '0.20', 20, 0, 0, 0, 0, 40, 0, 0, 0, '1.50', 0, 0, 0, 0, 0, '0.00', 0, 50, 3, 1, 3, 0, 0, 0, 0, 0),
(4, 'Tritiumsynthesizer', 2, 'Produziert Tritium.', 'Produziert Tritium.', 160, 110, 0, 50, 0, 0, '2.00', '0.20', 50, 0, 0, 0, 0, 0, 28, 0, 0, '1.50', 0, 0, 0, 0, 0, '0.00', 0, 50, 3, 1, 4, 0, 0, 0, 0, 0),
(5, 'Gewächshaus', 2, 'Produziert Nahrung.', 'Produziert Nahrung.', 80, 100, 0, 0, 0, 0, '1.90', '0.20', 5, 0, 0, 0, 0, 0, 0, 40, 0, '1.50', 0, 0, 0, 0, 0, '0.00', 0, 50, 2, 1, 5, 0, 0, 0, 0, 0),
(6, 'Planetenbasis', 1, 'Das Grundgebäude jedes Planeten bietet Platz für Bewohner, Lagerräume und produziert Rohstoffe.', 'Die Planetenbasis ist die Schaltzentrale aller Aktivitäten auf deinem Planeten. Du musst zuerst eine Planetenbasis bauen, danach kannst du alle weiteren Gebäude errichten. Die Planetenbasis liefert ein Grundeinkommen an Rohstoffen und eine minimale Energieversorgung durch ein integriertes Erdwärmekraftwerk. Es ist jedoch sinnvoll, Minen und Fabriken zu bauen, um die Rohstoffproduktion zu steigern.', 500, 250, 0, 300, 0, 0, '2.00', '0.00', 50, 0, 0, 50, 20, 10, 5, 15, 200, '1.00', 100000, 100000, 100000, 100000, 100000, '1.00', 300, 1, 5, 1, 0, 0, 1, 0, 0, 0),
(7, 'Wohnmodul', 1, 'Mit einem Wohnmodul wird die Kapazität für Bewohner erhöht.', 'Mit steigendem Wachstum eines Planeten werden immer mehr Gebäude errichtet und ausgebaut, wofür mehr Arbeiter benötigt werden.\r\nEin Ausbau des Wohnmoduls ist deshalb wichtig, welches die Kapazität der Bewohner erhöht und so potenzielle Arbeiter freigibt.', 50, 30, 0, 150, 0, 0, '2.00', '0.40', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 0, 0, '1.80', 300, 50, 1, 1, 1, 0, 0, 0, 0, 0),
(8, 'Forschungslabor', 1, 'Im Labor werden neue Techniken entwickelt. Höhere Stufen senken die Forschungszeit.', 'Damit Schiffe und Spezialgebäude errichten werden können, braucht es ein Forschungslabor, in dem die Wissenschaftler neue Technologien entwickeln. Je höher das Forschungslabor ausgebaut ist, desto mehr Technologien können entwickelt werden. Erforschte Technologien gelten automatisch auf allen Planeten deines Reiches.\r\nAusserdem senkt das Forschungslabor die Forschungszeit, jedoch erst ab einer bestimmten Stufe!\r\nUm zur Elite auf dem Gebiet der Technologien zu gehören, ist ein guter Ausbau des Forschungslabors unverzichtbar. ', 500, 700, 210, 350, 0, 0, '2.00', '0.40', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 0, 0, '0.00', 0, 50, 4, 1, 2, 0, 1, 0, 0, 0),
(9, 'Schiffswerft', 1, 'In der Werft werden alle Raumschiffe gebaut.Höhere Stufen senken die Bauzeit.', 'In der Schiffswerft werden Schiffe gebaut, die im Krieg oder für den Handel mit anderen Völkern eingesetzt werden können. Je höher die Werft, desto mehr Schiffe können gebaut werden.\r\nAusserdem senkt die Schiffswerft die Bauzeit der Schiffe, jedoch erst ab einer bestimmten Stufe!', 900, 680, 510, 780, 0, 0, '1.80', '0.40', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 0, 0, '0.00', 0, 50, 6, 1, 3, 0, 1, 0, 0, 0),
(10, 'Waffenfabrik', 1, 'In der Waffenfabrik werden Verteidigungsanlagen gebaut. Höhere Stufen senken die Bauzeit.', 'Die Waffenfabrik bietet jedem Volk die Möglichkeit, Verteidigungsanlagen gegen feindliche Angriffe zu errichten.\r\nVerteidigungsanlagen funktionieren, wenn sie mal gebaut sind, selbstständig und eröffnen das Feuer gegen angreifende Flotten. \r\nAusserdem senkt der Ausbau der Waffenfabrik die Bauzeit der Verteidigungsanlagen, jedoch erst ab einer bestimmten Stufe!', 750, 480, 320, 500, 0, 0, '1.80', '0.40', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 0, 0, '0.00', 0, 50, 5, 1, 4, 0, 1, 0, 0, 0),
(11, 'Flottenkontrolle', 1, 'Koordiniert deine Flotten. Je weiter die Flottenkontrolle ausgebaut ist, desto mehr Flotten können starten.', 'Die Flottenkontrolle ist ein Gebäude voller Überwachungscomputer, Leitsystemen, Empfänger- sowie Sendeanlagen. Mit Hilfe der Flottenkontrolle werden Flotten gesteuert. Sie ist ebenfalls Voraussetzung für den Bau von Schiffen. Je weiter die Flottenkontrolle ausgebaut ist, desto mehr Flotten können vom Planeten gestartet werden.', 1100, 750, 0, 500, 0, 0, '1.80', '0.40', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 0, 0, '0.00', 0, 50, 5, 1, 5, 0, 0, 0, 0, 0),
(12, 'Windkraftwerk', 3, 'Nicht sehr effizientes und relativ teures Kraftwerk, welches Energie mit Hilfe des Windes gewinnt.', 'Windenergieanlagen wandeln mit Hilfe des Rotors die Windenergie in eine Drehbewegung um. Mit Hilfe von Generatoren wird diese Drehbewegung in eine elektrische Energie umgewandelt, welche dann in das Stromnetz des Planeten eingespeist wird.\r\nWindenergie ist eine alternative Energie, jedoch noch nicht sehr effizient. Der Bau ist relativ teuer und die Produktion nur mittelmässig.', 250, 50, 5, 80, 0, 0, '1.90', '0.20', 0, 0, 0, 0, 0, 0, 0, 0, 80, '1.65', 0, 0, 0, 0, 0, '0.00', 0, 50, 1, 1, 0, 0, 0, 0, 0, 0),
(13, 'Solarkraftwerk', 3, 'Solarkraftwerke gewinnen Energie durch Sonnenlicht. ', 'In einer Solarstromanlage findet die Umwandlung von Sonnenenergie in elektrische Energie statt. Eine Solarstromanlage besteht aus mehreren Komponenten. Der Generator empfängt und wandelt die Lichtenergie in elektrische Energie um. Als Empfänger dient die Solarzelle. Hierbei kommen Spiegel oder Linsensysteme zum Einsatz, die die Strahlung auf die Zellen umleiten und konzentrieren.\r\nEiner der wichtigsten Bestandteile einer Solarzelle ist das Metal Silizium. Dieses hat die Eigenschaft, unter Bestrahlung von Licht eine elektrische Spannung erzeugen zu können.\r\nDiese Methode für die Energieerzeugung ist noch sehr jung und unerforscht. Wegen den grossen Mengen an Silizium die es benötigt, wird das Solarkraftwerk oft als unrentabel bezeichnet, jedoch kann sich die Energiegewinnung daraus sehen lassen.', 150, 250, 0, 160, 0, 0, '1.90', '0.20', 0, 0, 0, 0, 0, 0, 0, 0, 100, '1.70', 0, 0, 0, 0, 0, '0.00', 0, 50, 1, 1, 2, 0, 0, 0, 0, 0),
(14, 'Fusionskraftwerk', 3, 'Durch die Fusion von Tritium und Deuterium werden im Fusionskraftwerk riesige Energiemengen gewonnen. ', 'Als Kernfusion wird der Prozess des Verschmelzens zweier Atomkerne zu einem schwereren Kern bezeichnet. Besonders viel Energie wird frei, wenn Deuterium und Tritium miteinander verschmelzen. Hier beträgt der Massendefekt fast 4 Promille. Die fehlende Masse wird aufgrund der Äquivalenz von Masse und Energie aus Einsteins Gleichung E=mc^2 als kinetische Energie auf die Reaktionsprodukte übertragen. Da c^2 eine sehr grosse Zahl ist, setzt schon die Fusion kleiner Mengen von Deuterium und Tritium gewaltige Energiemengen frei.\r\nDie Effizienz dieses Kraftwerkes wird pro Stufe immer wie grösser! Die Energie, welche das Kraftwerk in den ersten Stufen freisetzt, wird oft als normal angesehen, jedoch stellt sich schon sehr früh heraus, dass beim weiteren Ausbau des Fusionskraftwerkes die Effizient beachtlich gesteigert wird!', 3000, 4900, 8300, 1500, 0, 0, '1.90', '0.20', 0, 0, 0, 0, 0, 0, 0, 0, 1500, '1.95', 0, 0, 0, 0, 0, '0.00', 0, 50, 2, 1, 5, 0, 0, 0, 0, 0),
(15, 'Gezeitenkraftwerk', 3, 'Dieses Kraftwerk gewinnt Energie durch den Hubunterschied der Gezeiten.', 'Ein Gezeitenkraftwerk ist ein Kraftwerk zur Produktion von elektrischem Strom, das durch die Tide angetrieben wird. Sie sind eine Sonderform der Wasserkraftwerke.\r\nGezeitenkraftwerke werden an Meeresbuchten und in Ästuaren errichtet, die einen besonders hohen Tidenhub haben. Dazu wird die entsprechende Bucht durch einen Deich abgedämmt. Dadurch kann das Wasser der Tidenströme durch die Turbinen strömen, die aufgrund der Gezeitenströme, welche viermal am Tag die Richtung wechseln, auf Zweirichtungsbetrieb eingestellt sind.', 2100, 1000, 500, 2000, 0, 0, '1.85', '0.20', 0, 0, 0, 0, 0, 0, 0, 0, 750, '1.75', 0, 0, 0, 0, 0, '0.00', 0, 50, 3, 1, 3, 0, 0, 0, 0, 0),
(16, 'Titanspeicher', 4, 'Lagert Titan.', 'Lagert Titan. Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.', 4000, 100, 0, 100, 0, 0, '2.00', '0.20', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 100000, 0, 0, 0, 0, '1.80', 0, 50, 1, 1, 0, 0, 0, 0, 0, 0),
(17, 'Siliziumspeicher', 4, 'Lagert Silizium.', 'Lagert Silizium. Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.', 100, 3500, 0, 100, 0, 0, '2.00', '0.20', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 100000, 0, 0, 0, '1.80', 0, 50, 1, 1, 1, 0, 0, 0, 0, 0),
(18, 'Lagerhalle', 4, 'Lagert Plastik.', 'Lagert Plastik. Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.', 50, 50, 0, 3750, 0, 0, '2.00', '0.20', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 100000, 0, 0, '1.80', 0, 50, 1, 1, 2, 0, 0, 0, 0, 0),
(19, 'Nahrungssilo', 4, 'Lagert Nahrung.', 'Lagert Nahrung.Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.', 1000, 1000, 0, 1000, 0, 0, '2.00', '0.20', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 0, 100000, '1.80', 0, 50, 1, 1, 4, 0, 0, 0, 0, 0),
(20, 'Tritiumsilo', 4, 'Lagert Tritium.', 'Lagert Tritium. Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.', 500, 500, 3000, 0, 0, 0, '2.00', '0.20', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 100000, 0, '1.80', 0, 50, 1, 1, 3, 0, 0, 0, 0, 0),
(21, 'Marktplatz', 1, 'Auf dem Marktplatz können Schiffe und Rohstoffe gehandelt und ersteigert werden.', 'Der Marktplatz bildet das Zentrum aller Händler in Andromeda.\r\nHand mit Schiffen, Rohstoffen, \r\nJe höher der Marktplatz ausgebaut ist, desto mehr Waren können gleichzeitig angeboten werden.\r\nAusserdem werden mehr Waren zurück erstattet, wenn ein Angebot zurückgezogen wird.\r\nDer Markt kann aber nicht beliebig weit ausgebaut werden, sondern ist durch ein Maximallevel beschränkt.', 30000, 25000, 3500, 35000, 0, 0, '1.50', '1.50', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 0, 0, '0.00', 0, 10, 4, 1, 6, 0, 0, 0, 0, 0),
(22, 'Orbitalplattform', 1, 'Die Orbitalplattform erhöht den Platz auf einem Planeten und bietet Lagerräume für Ressourcen.', 'Die Orbitalplattform erhöht die Anzahl verfügbarer Felder auf einem Planeten. Dies wird besonders wichtig, wenn ein Planet nicht allzu viele Felder besitzt, oder viele Verteidigungsanlagen errichtet wurden. Ebenfalls befinden sich auf der Plattform zusätzliche Lagerräume für diverse Ressourcen.\r\nPro Ausbaustufe erhöht sich die Anzahl der Felder, ebenso die Grösse der Lagerräume.', 30000, 60000, 50000, 55000, 0, 0, '1.90', '0.00', 100, 0, 0, 0, 0, 0, 0, 0, 0, '1.80', 10000, 15000, 20000, 0, 0, '2.00', 0, 50, 0, 1, 7, 60, 0, 0, 0, 0),
(23, 'Multimine', 2, 'Dieses riesige Mine fördert Titan und Silizium zu Tage und kann auch eine gewisse Menge an Rohstoffen speichern. Allerdings verbraucht sie enorm viel Energie!', 'Dieses riesige Mine fördert Titan und Silizium zu Tage und kann auch eine gewisse Menge an Rohstoffen speichern. Allerdings verbraucht sie enorm viel Energie! Da sie so enorm gross ist, braucht sie viele Felder und kann nur bis zu Stufe 15 gebaut werden.', 5100, 7200, 160, 1100, 0, 0, '2.00', '0.00', 100, 0, 0, 100, 70, 0, 0, 0, 0, '1.80', 50000, 50000, 0, 0, 0, '1.50', 0, 15, 8, 0, 20, 0, 0, 0, 0, 0),
(24, 'Kryptocenter', 1, 'Das Kryptocenter analysiert Kommunikationskanäle um Infos über fremde Flottenbewegungen zu erhalten. ', 'Das Kryptocenter analysiert Kommunikationskanäle zwischen Flotten und Bodenstationen, um Aufschluss über fremde Flottenbewegungen zu erhalten. Mit Hilfe eines riesigen unterirdischen Rechenzentrums werden die gewonnenen Daten analysiert, entschlüsselt und ausgewertet, deshalb braucht diese Anlage enorm viel Energie zum Bau und zum  Betrieb. Je höher der Level dieser Anlage, desto grösser ist auch die Reichweite des Scanners.', 50000, 450000, 650000, 50000, 0, 1000000, '1.50', '0.10', 0, 1000000, 0, 0, 0, 0, 0, 0, 0, '1.50', 0, 0, 0, 0, 0, '0.00', 0, 10, 5, 0, 11, 0, 0, 0, 0, 0),
(25, 'Raketensilo', 1, 'Im Raketensilo werden Raketen gebaut unt gestartet, um gegnerische Verteidigungsanlagen zu beschädigen.', 'Im Raketensilo werden Raketen gelagert und gestartet, mit denen man gegnerische Verteidigungsanlagen beschädigen oder ausser Gefecht setzen kann, sowie Raketen um gegnerische Raketen abzufangen. Je grösser das Silo ist, desto mehr Raketen können darin gelagert werden.', 100000, 50000, 70000, 20000, 0, 20000, '1.40', '0.00', 50000, 0, 300, 0, 0, 0, 0, 0, 0, '1.10', 0, 0, 0, 0, 0, '0.00', 0, 20, 2, 1, 10, 0, 0, 0, 0, 0),
(26, 'Rohstoffbunker', 1, 'In diesem Bunker kann im Falle eines Angriffs ein Teil der Rohstoffe versteckt werden.', 'In diesem Bunker kann im Falle eines Angriffs ein Teil der Rohstoffe versteckt werden, so dass sie nicht geklaut werden können. Das Verstecken geschieht automatisch. Auf Stufe 1 können 5000 Resourcen versteckt werden, pro Stufe verdoppelt sich diese Anzahl.', 5000, 1000, 0, 2000, 0, 0, '2.00', '0.50', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 0, 0, '2.00', 0, 10, 0, 1, 8, 0, 0, 5000, 0, 0),
(27, 'Flottenbunker', 1, '', '', 20000, 10000, 0, 5000, 0, 0, '2.00', '0.50', 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.00', 0, 0, 0, 0, 0, '2.00', 0, 10, 0, 1, 9, 0, 0, 0, 5, 2500);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `building_points`
--

CREATE TABLE IF NOT EXISTS `building_points` (
`bp_id` int(10) unsigned NOT NULL,
  `bp_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `bp_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bp_points` decimal(20,3) unsigned NOT NULL DEFAULT '0.000'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `building_queue`
--

CREATE TABLE IF NOT EXISTS `building_queue` (
`id` int(10) unsigned NOT NULL,
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
  `res_food` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `building_requirements`
--

CREATE TABLE IF NOT EXISTS `building_requirements` (
`id` int(10) unsigned NOT NULL,
  `obj_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_level` tinyint(3) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=66 ;

--
-- Daten für Tabelle `building_requirements`
--

INSERT INTO `building_requirements` (`id`, `obj_id`, `req_building_id`, `req_tech_id`, `req_level`) VALUES
(1, 1, 6, 0, 1),
(2, 2, 6, 0, 1),
(5, 5, 6, 0, 1),
(6, 7, 6, 0, 1),
(11, 12, 6, 0, 1),
(12, 3, 1, 0, 2),
(13, 3, 12, 0, 3),
(14, 4, 12, 0, 4),
(15, 4, 2, 0, 3),
(16, 8, 12, 0, 5),
(17, 10, 8, 0, 2),
(18, 9, 10, 0, 2),
(19, 9, 8, 0, 4),
(20, 11, 9, 0, 1),
(21, 13, 0, 3, 3),
(22, 14, 0, 3, 8),
(23, 15, 0, 3, 5),
(24, 16, 1, 0, 3),
(25, 17, 2, 0, 3),
(26, 18, 3, 0, 4),
(27, 19, 5, 0, 3),
(28, 20, 4, 0, 4),
(29, 13, 6, 0, 1),
(30, 14, 6, 0, 1),
(31, 15, 6, 0, 1),
(32, 4, 1, 0, 1),
(33, 21, 8, 0, 7),
(35, 22, 0, 3, 10),
(36, 22, 10, 10, 8),
(37, 23, 1, 0, 10),
(38, 23, 2, 0, 9),
(39, 23, 14, 0, 6),
(40, 23, 0, 3, 8),
(41, 23, 0, 16, 3),
(50, 25, 0, 24, 1),
(51, 25, 11, 0, 12),
(52, 25, 10, 0, 10),
(53, 24, 11, 0, 11),
(54, 24, 14, 0, 5),
(55, 24, 0, 7, 13),
(56, 24, 0, 25, 7),
(57, 26, 16, 0, 1),
(58, 26, 17, 0, 1),
(59, 26, 18, 0, 1),
(60, 26, 19, 0, 1),
(61, 26, 20, 0, 1),
(62, 27, 11, 0, 5),
(63, 27, 0, 3, 5),
(64, 27, 0, 25, 3),
(65, 27, 0, 11, 5);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `building_types`
--

CREATE TABLE IF NOT EXISTS `building_types` (
`type_id` int(10) unsigned NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `type_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type_color` char(7) NOT NULL DEFAULT '#ffffff'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `building_types`
--

INSERT INTO `building_types` (`type_id`, `type_name`, `type_order`, `type_color`) VALUES
(1, 'Allgemeine Gebäude', 1, '#ffffff'),
(2, 'Rohstoffgebäude', 2, '#ffffff'),
(3, 'Kraftwerke', 3, '#ffffff'),
(4, 'Speicher', 4, '#ffffff');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `buildlist`
--

CREATE TABLE IF NOT EXISTS `buildlist` (
`buildlist_id` int(10) unsigned NOT NULL,
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
  `buildlist_cooldown` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cells`
--

CREATE TABLE IF NOT EXISTS `cells` (
`id` smallint(10) unsigned NOT NULL,
  `sx` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sy` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `cx` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `cy` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chat`
--

CREATE TABLE IF NOT EXISTS `chat` (
`id` int(10) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `nick` varchar(50) NOT NULL,
  `text` varchar(255) NOT NULL,
  `color` varchar(15) NOT NULL,
  `user_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `admin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `channel_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MEMORY DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chat_banns`
--

CREATE TABLE IF NOT EXISTS `chat_banns` (
  `user_id` varchar(50) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chat_channels`
--

CREATE TABLE IF NOT EXISTS `chat_channels` (
`id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('public','alliance','private','') NOT NULL,
  `permanent` int(11) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `alliance_id` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chat_log`
--

CREATE TABLE IF NOT EXISTS `chat_log` (
`id` int(10) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL,
  `nick` varchar(50) NOT NULL,
  `text` varchar(255) NOT NULL,
  `color` varchar(15) NOT NULL,
  `user_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `admin` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `private` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `channel` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `chat_users`
--

CREATE TABLE IF NOT EXISTS `chat_users` (
  `nick` varchar(30) NOT NULL,
  `user_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `kick` varchar(255) NOT NULL
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `config`
--

CREATE TABLE IF NOT EXISTS `config` (
`config_id` int(10) unsigned NOT NULL,
  `config_name` varchar(50) NOT NULL,
  `config_value` text NOT NULL,
  `config_param1` text NOT NULL,
  `config_param2` text NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=188 ;

--
-- Daten für Tabelle `config`
--

INSERT INTO `config` (`config_id`, `config_name`, `config_value`, `config_param1`, `config_param2`) VALUES
(1, 'roundname', 'Runde X', '', ''),
(2, 'roundurl', 'http://roundx.live.etoa.net', '', ''),
(3, 'loginurl', 'http://etoa.ch', '', ''),
(4, 'user_timeout', '2400', '', ''),
(5, 'mail_sender', 'no-reply@etoa.ch', '', ''),
(6, 'mail_reply', 'mail@etoa.ch', '', ''),
(7, 'enable_register', '1', '1190446200', '15'),
(8, 'enable_login', '1', '1205485200', ''),
(9, 'round_end', '0', '1205485200', ''),
(10, 'points_update', '3600', '1000', '100'),
(11, 'statsupdate', '1240063202', '0', ''),
(12, 'stats_num_rows', '50', '', ''),
(13, 'url_rules', 'http://etoa.ch/rules', '', ''),
(14, 'url_teamspeak', 'http://etoa.ch/404.html', '', ''),
(15, 'messages_threshold_days', '28', '14', ''),
(16, 'reports_threshold_days', '42', '42', ''),
(17, 'referers', 'http://roundx.live.etoa.net\r\nhttp://dev.etoa.net\r\nhttp://etoa.ch\r\nhttp://www.etoa.ch', '', ''),
(18, 'under_construction', '', '', ''),
(19, 'offline', '0', '', ''),
(20, 'offline_message', '', '', ''),
(21, 'offline_ips_allow', '', '', ''),
(22, 'register_key', '', '', ''),
(23, 'bot_max_count', '5', '', ''),
(24, 'global_time', '12', '', ''),
(25, 'shipdefbuild_cancel_time', '15', '', ''),
(26, 'build_time_boni_forschungslabor', '5', '10', '0.2'),
(27, 'build_time_boni_schiffswerft', '5', '10', ''),
(28, 'build_time_boni_waffenfabrik', '5', '10', ''),
(29, 'ship_build_time', '0.8', '', ''),
(30, 'def_build_time', '0.8', '', ''),
(31, 'build_build_time', '1', '', ''),
(32, 'flight_flight_time', '1', '', ''),
(33, 'flight_start_time', '1', '', ''),
(34, 'flight_land_time', '1', '', ''),
(35, 'res_build_time', '1', '', ''),
(36, 'num_of_sectors', '', '2', '2'),
(37, 'num_of_cells', '', '10', '10'),
(38, 'num_planets', '', '5', '20'),
(39, 'space_percent_solsys', '50', '', ''),
(40, 'space_percent_asteroids', '12', '', ''),
(41, 'space_percent_nebulas', '12', '', ''),
(42, 'space_percent_wormholes', '12', '', ''),
(43, 'num_planet_images', '5', '', ''),
(44, 'planet_fields', '', '600', '2500'),
(45, 'planet_temp', '20', '-155', '166'),
(46, 'field_squarekm', '11694', '', ''),
(47, 'cell_length', '300', '', ''),
(48, 'map_init_sector', '', '1', '1'),
(49, 'user_planet_name', 'Startplanet', '', ''),
(50, 'user_min_fields', '1200', '', ''),
(51, 'user_max_planets', '15', '', ''),
(52, 'asteroid_ress', '', '10000', '1000000'),
(53, 'nebula_ress', '', '100000', '3000000'),
(54, 'wh_update', '172800', '1', ''),
(55, 'gasplanet', '7', '3600', '500'),
(56, 'solsys_percent_planet', '85', '', ''),
(57, 'solsys_percent_asteroids', '5', '', ''),
(58, 'user_attack_min_points', '5000', '', ''),
(59, 'user_attack_percentage', '0.2', '', ''),
(60, 'invade_possibility', '0.5', '1', '0.1'),
(61, 'invade_ship_destroy', '0.3', '', ''),
(62, 'def_restore_percent', '0.4', '', ''),
(63, 'def_wf_percent', '0.4', '', ''),
(64, 'ship_wf_percent', '0.5', '', ''),
(65, 'deactivate_fleet', '', '', ''),
(66, 'ship_bomb_factor', '5', '10', ''),
(67, 'battle_rounds', '5', '', ''),
(68, 'gasattack_action', '25', '95', ''),
(69, 'elorating', '1600', '15', ''),
(70, 'battle_rebuildable', '0', '0.75', '1'),
(71, 'rebuildable_costs', '0.25', '', ''),
(72, 'invade_active_users', '0', '', ''),
(73, 'abs_enabled', '1', '0', ''),
(74, 'alliance_fleets_max_players', '1', '3', ''),
(75, 'res_update', '300', '', ''),
(76, 'def_store_capacity', '200000', '', ''),
(77, 'user_start_metal', '4000', '', ''),
(78, 'user_start_crystal', '3000', '', ''),
(79, 'user_start_plastic', '2500', '', ''),
(80, 'user_start_fuel', '200', '', ''),
(81, 'user_start_food', '500', '', ''),
(82, 'user_start_people', '200', '250', ''),
(83, 'people_food_require', '12', '', ''),
(84, 'people_multiply', '1.1', '', ''),
(85, 'people_work_done', '3', '', ''),
(86, 'specialistconfig', '0.3', '10', '100000'),
(87, 'market_enabled', '1', '', ''),
(88, 'market_response_time', '14', '', ''),
(89, 'market_ship_action_ress', 'market', '', ''),
(90, 'market_ship_action_ship', 'market', '', ''),
(91, 'market_ship_flight_time', '', '15', '180'),
(92, 'market_auction_delay_time', '24', '', ''),
(93, 'market_rate_0', '1', '', ''),
(94, 'market_rate_1', '1', '', ''),
(95, 'market_rate_2', '1', '', ''),
(96, 'market_rate_3', '1', '', ''),
(97, 'market_rate_4', '1', '', ''),
(98, 'default_image_path', 'images/imagepacks/Discovery', '', ''),
(99, 'default_css_style', 'Graphite', '', ''),
(100, 'imagepack_zip_format', 'zip', '', ''),
(101, 'imagepack_predirectory', '', '', ''),
(102, 'imagesize', '220', '120', '40'),
(103, 'num_nebula_images', '9', '', ''),
(104, 'num_asteroid_images', '5', '', ''),
(105, 'num_space_images', '10', '', ''),
(106, 'num_wormhole_images', '1', '', ''),
(107, 'wordbanlist', '', '', ''),
(108, 'msg_flood_control', '10', '', ''),
(109, 'msg_ban_hours', '0', '', ''),
(110, 'mailqueue', '50', '', ''),
(111, 'msg_max_store', '200', '20', ''),
(112, 'password_minlength', '6', '30', ''),
(113, 'hmode_days', '2', '42', '1'),
(114, 'user_inactive_days', '7', '21', '8'),
(115, 'user_ban_min_length', '1', '', ''),
(116, 'user_umod_min_length', '2', '', ''),
(117, 'user_sitting_days', '12', '2', ''),
(118, 'online_threshold', '5', '', ''),
(119, 'nick_length', '', '3', '15'),
(120, 'main_planet_changetime', '7', '', ''),
(121, 'name_length', '30', '', ''),
(122, 'user_delete_days', '5', '', ''),
(123, 'profileimagecheck_done', '1209569797', '', ''),
(124, 'admin_timeout', '1200', '', ''),
(125, 'admin_dateformat', 'Y-m-d H:i:s', '', ''),
(126, 'flightban', '0', '', ''),
(127, 'battleban', '0', '', ''),
(128, 'battleban_time', '', '1234165500', '1234174500'),
(129, 'flightban_time', '', '1199293080', '1199552280'),
(130, 'battleban_arrival_text', '', 'Die ankommenden Schiffe sind auf dem Planeten gelandet. Nach einer kurzen Kaffeepause der Piloten kehrten sie wieder um und machten sich auf den Rückflug.', 'Auf dem Weg zu ihrem Ziel flogen deine Raketen in ein intergalaktisches Warpfeld. Sie wurden deaktiviert und in ihr Lager gebeamt.'),
(131, 'asteroid_action', '30', '20', '0'),
(132, 'gascollect_action', '20', '10', '1000'),
(133, 'nebula_action', '30', '50', '1000'),
(134, 'antrax_action', '30', '90', ''),
(135, 'spyattack_action', '3', '1', '10'),
(136, 'userrank_total', 'Imperator von Andromeda', '', ''),
(137, 'userrank_buildings', 'Grossbaumeister von Andromeda', '', ''),
(138, 'userrank_tech', 'Hochtechnokrat von Andromeda', '', ''),
(139, 'userrank_fleet', 'Flottenadmiral von Andromeda', '', ''),
(140, 'userrank_battle', 'Generalfeldmarschall von Andromeda', '', ''),
(141, 'userrank_trade', 'Handelsfürst von Andromeda', '', ''),
(142, 'userrank_diplomacy', 'Botschafter von Andromeda', '', ''),
(143, 'userrank_exp', 'Kriegsheld von Andromeda', '', ''),
(144, 'alliance_max_member_count', '7', '', ''),
(145, 'alliance_membercosts_factor', '0.9', '', ''),
(146, 'alliance_shippoints_per_hour', '5', '', ''),
(147, 'alliance_shipcosts_factor', '1.02', '', ''),
(148, 'alliance_tech_bonus', '10', '', ''),
(149, 'alliance_war_time', '48', '48', ''),
(150, 'alliance_shippoints_base', '1.4', '', ''),
(151, 'allow_wings', '0', '', ''),
(152, 'townhall_ban', '86400', 'Nichtbeachtung der Rathaus-Regeln', ''),
(153, 'discoverymask', '0.4', '10', '5'),
(154, 'discover_percent_pirates', '10', 'a,e,n,s,w', '7'),
(155, 'discover_percent_aliens', '5', 'a,e,n,s,w', '1'),
(156, 'discover_percent_resources', '35', 'a,e,n,s,w', ''),
(157, 'discover_percent_ships', '25', 'a,e,n,s,w', ''),
(158, 'discover_percent_total_lost', '1', 'a,e,n,s,w', ''),
(159, 'discover_percent_fast_flight', '8', 'a,e,n,s,w', '80'),
(160, 'discover_percent_slow_fight', '5', 'a,e,n,s,w', '1.80'),
(161, 'discover_percent_sheet', '3', 'a,e,n,s,w', ''),
(162, 'discover_pirates', '1', '5', '1.5'),
(163, 'discover_aliens', '5', '10', '1.75'),
(164, 'discover_resources', '5000', '50', '5'),
(165, 'discover_fleet', '5', '1', '15'),
(166, 'random_event_hits', '0', '', ''),
(167, 'random_event_misses', '0', '', ''),
(168, 'log_threshold_days', '28', '', ''),
(169, 'sessionlog_store_days', '', '30', '60'),
(170, 'daemon_exe', '/usr/local/bin/etoad', '', ''),
(171, 'daemon_logfile', '/var/log/etoad/roundx.log', '', ''),
(172, 'daemon_pidfile', '/var/run/etoad/roundx.pid', '', ''),
(173, 'backend_status', '0', '', ''),
(174, 'backend_offline_mail', 'river@etoa.ch;mrcage@etoa.ch', '', ''),
(175, 'update_enabled', '1', '', ''),
(176, 'backup_dir', '/home/etoa/backup/', '', ''),
(177, 'backup_retention_time', '14', '', ''),
(178, 'backup_use_gzip', '0', '', ''),
(179, 'debug', '0', '', ''),
(180, 'accesslog', '0', '', ''),
(181, 'crypto_enable', '1', '', ''),
(182, 'cryptocenter', '86400', '7200', '21600'),
(183, 'chat_recent_messages', '200', '', ''),
(184, 'chat_user_timeout', '180', '', ''),
(185, 'boost_system_enable', '0', '', ''),
(186, 'boost_system_max_res_prod_bonus', '2', '', ''),
(187, 'boost_system_max_building_speed_bonus', '2', '', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `default_items`
--

CREATE TABLE IF NOT EXISTS `default_items` (
`item_id` int(10) NOT NULL,
  `item_set_id` int(20) NOT NULL DEFAULT '0',
  `item_cat` char(1) NOT NULL,
  `item_object_id` int(10) NOT NULL,
  `item_count` int(10) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=133 ;

--
-- Daten für Tabelle `default_items`
--

INSERT INTO `default_items` (`item_id`, `item_set_id`, `item_cat`, `item_object_id`, `item_count`) VALUES
(132, 7, 's', 71, 10),
(131, 7, 'd', 1, 10),
(130, 7, 'd', 11, 1),
(129, 7, 'd', 10, 1),
(128, 7, 's', 69, 20),
(127, 7, 's', 24, 20),
(126, 7, 's', 31, 20),
(125, 7, 's', 68, 20),
(124, 7, 's', 46, 20),
(123, 7, 's', 20, 10),
(122, 7, 's', 13, 1),
(121, 7, 's', 36, 20),
(120, 7, 's', 42, 20),
(119, 7, 's', 27, 20),
(118, 7, 's', 8, 20),
(117, 7, 's', 4, 20),
(116, 7, 's', 60, 1),
(115, 7, 's', 9, 10),
(69, 7, 'b', 6, 1),
(70, 7, 'b', 7, 15),
(71, 7, 'b', 8, 20),
(72, 7, 'b', 21, 10),
(73, 7, 'b', 9, 20),
(74, 7, 'b', 10, 20),
(75, 7, 'b', 11, 20),
(76, 7, 'b', 22, 1),
(77, 7, 'b', 24, 1),
(78, 7, 'b', 25, 10),
(79, 7, 'b', 1, 25),
(80, 7, 'b', 2, 25),
(81, 7, 'b', 3, 25),
(82, 7, 'b', 4, 25),
(83, 7, 'b', 5, 25),
(84, 7, 'b', 12, 12),
(85, 7, 'b', 13, 12),
(86, 7, 'b', 15, 6),
(87, 7, 'b', 14, 20),
(88, 7, 'b', 16, 25),
(89, 7, 'b', 17, 25),
(90, 7, 'b', 18, 25),
(91, 7, 'b', 20, 25),
(92, 7, 'b', 19, 25),
(93, 7, 't', 4, 20),
(94, 7, 't', 5, 20),
(95, 7, 't', 14, 20),
(96, 7, 't', 6, 15),
(97, 7, 't', 21, 15),
(98, 7, 't', 20, 15),
(99, 7, 't', 8, 20),
(100, 7, 't', 9, 20),
(101, 7, 't', 10, 20),
(102, 7, 't', 11, 20),
(103, 7, 't', 15, 10),
(104, 7, 't', 17, 10),
(105, 7, 't', 18, 10),
(106, 7, 't', 19, 10),
(107, 7, 't', 22, 1),
(108, 7, 't', 23, 1),
(109, 7, 't', 24, 1),
(110, 7, 't', 3, 20),
(111, 7, 't', 7, 20),
(112, 7, 't', 16, 10),
(113, 7, 't', 25, 10),
(114, 7, 't', 12, 10);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `default_item_sets`
--

CREATE TABLE IF NOT EXISTS `default_item_sets` (
`set_id` int(10) NOT NULL,
  `set_name` varchar(50) NOT NULL,
  `set_active` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Daten für Tabelle `default_item_sets`
--

INSERT INTO `default_item_sets` (`set_id`, `set_name`, `set_active`) VALUES
(5, 'Standard', 1),
(7, 'All Objects', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `defense`
--

CREATE TABLE IF NOT EXISTS `defense` (
`def_id` int(10) unsigned NOT NULL,
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
  `def_points` decimal(20,3) unsigned NOT NULL DEFAULT '0.000'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Daten für Tabelle `defense`
--

INSERT INTO `defense` (`def_id`, `def_name`, `def_shortcomment`, `def_longcomment`, `def_costs_metal`, `def_costs_crystal`, `def_costs_fuel`, `def_costs_plastic`, `def_costs_food`, `def_costs_power`, `def_power_use`, `def_fuel_use`, `def_prod_power`, `def_fields`, `def_show`, `def_buildable`, `def_order`, `def_structure`, `def_shield`, `def_weapon`, `def_heal`, `def_jam`, `def_race_id`, `def_cat_id`, `def_max_count`, `def_points`) VALUES
(1, 'SPICA Flakkanone', 'Einfache und billige Abwehrwaffe.', 'Einfache und billige Abwehrwaffe.\r\nSie wird auf Gebäuden befestigt und braucht daher keine Felder. Sie ist aber nicht sehr effektiv. Darum ist es besser, sie nur am Anfang und auch dann nur in grossen Mengen zu bauen.', 800, 475, 0, 425, 0, 0, 1, 0, 0, 0, 1, 1, 0, 300, 150, 250, 0, 0, 0, 2, 1000000, '1.700'),
(2, 'POLARIS Raketengeschütz', 'Die Raketen dieses Geschützes verfolgen ihr Ziel mittels Lasersteuerung.', 'Um den gegnerischen Schiffen mit Raketen beizukommen, wurde dieses Raketengeschütz entwickelt. Es schiesst kleinere Raketen ab, welche dann das Ziel bis zur Zerstörung verfolgen. Es ist jedoch nicht sehr stark und dient vor allem zu Beginn als gute und billige Verteidigungswaffe.', 1000, 700, 300, 500, 0, 0, 3, 0, 0, 0, 1, 1, 2, 450, 325, 350, 0, 0, 0, 2, 1000000, '2.500'),
(3, 'ZIBAL Laserturm', 'Dieses Geschütz richtet einen gebündelten und starken Energiestrahl auf ihr Ziel.', 'Dieses Geschütz richtet einen gebündelten und starken Energiestrahl auf ihr Ziel. Es ist eine weiterentwickelte Verteidigungsanlage, welche es auch mit grösseren Schiffen aufnehmen kann.', 3900, 3100, 2100, 1500, 0, 0, 8, 0, 0, 0, 1, 1, 3, 1500, 2000, 1800, 0, 0, 0, 2, 100000, '10.600'),
(4, 'OMEGA Geschütz', 'Diese mächtige Abwehrwaffe beschützt deinen Planeten auch vor grösseren Angriffen.', 'Diese mächtige Abwehrwaffe beschützt deinen Planeten auch vor grösseren Angriffen. Da es aber eine starke Waffe ist, können maximal 1\\''000 Stück gebaut werden.', 750000, 525000, 165000, 325000, 0, 0, 15, 0, 0, 1, 1, 1, 4, 300000, 350000, 275000, 0, 0, 0, 2, 1000, '1765.000'),
(5, 'VEGA Hochenergieschild', 'Dieser kleine Hochenergieschild schützt deine Verteidigungsanlagen und Schiffe vor feindlichem Beschuss.', 'Dieser kleine Hochenergieschild schützt deine Verteidigungsanlagen und Schiffe vor feindlichem Beschuss. Es ist jedoch nicht sehr gut und kann nur wenig Beschuss abhalten.', 3000, 1200, 1800, 600, 0, 0, 0, 0, 0, 1, 1, 1, 0, 1200, 3500, 0, 0, 0, 0, 1, 1, '6.600'),
(6, 'CASTOR Hochenergieschild', 'Dieser grosse Hochenergieschild schützt deine Verteidigungsanlagen und Schiffe vor feindlichem Beschuss.', 'Dieser grosse Hochenergieschild schützt deine Verteidigungsanlagen und Schiffe vor feindlichem Beschuss.', 95000, 40000, 45000, 25000, 0, 0, 0, 0, 0, 2, 1, 1, 1, 52500, 105000, 0, 0, 0, 0, 1, 1, '205.000'),
(7, 'NEKKAR Plasmawerfer', 'Die stärkste Verteidigung in ganz Andromeda.', 'Die stärkste Verteidigung in ganz Andromeda. Dieser Plasmawerfer kann es sogar mit einem Andromeda Kampfstern aufnehmen! Dabei schiesst er hochenergetische Teilchen auf das Ziel.\r\nBedingt durch seine Grösse und Stärke ist die maximale Anzahl pro Planet auf 15 limitiert.', 25000000, 20000000, 11500000, 12000000, 0, 0, 0, 0, 0, 2, 1, 1, 5, 14000000, 9500000, 14500000, 0, 0, 0, 2, 15, '68500.000'),
(8, 'SIGMA Hochenergieschild', 'Dies ist der grösste Schild in ganz Andromeda.', 'Dies ist der grösste Schild in ganz Andromeda. Dieser Schild nutzt hochenergetische Teilchen, um die Angriffe der Gegner abzufangen. Beim Bau dieses Schildes wird gleich noch ein Kraftwerk nur für diesen Schild gebaut, damit die Energieversorgung gesichert ist. Deshalb ist er so unglaublich teuer.', 250000000, 20000000, 25000000, 5000000, 0, 0, 0, 0, 0, 100, 1, 1, 3, 25000000, 225000000, 0, 0, 0, 0, 1, 1, '300000.000'),
(9, 'KAPPA Minen', 'Diese Minen schweben im Orbit und können gegnerische Schiffe zerstören.', 'Diese Minen schweben im Orbit und können gegnerische Schiffe zerstören. Sie sind mit Tritium gefüllt und explodieren bei einer Kollision mit feindlichen Schiffen. Da ein kleiner Korridor für eigene Schiffe und Handelsschiffe frei bleiben muss, kann maximal eine Million dieser Minen gebaut werden.', 25, 10, 18, 5, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 20, 0, 0, 0, 3, 1000000, '0.058'),
(11, 'PHOENIX Reparaturplattform', 'Diese Anlage repariert bei einem Kampf jede Runde eine gewisse Anzahl an Strukturpunkten, kann jedoch selbst auch zerstört werden.', 'Diese Anlage repariert bei einem Kampf jede Runde eine gewisse Anzahl an Strukturpunkten, kann jedoch selbst auch zerstört werden.\r\nDie grundlegende Idee, welche zur Entwicklung dieser Reparaturplattform führte, fanden die Serrakin in den Mutterschiffen der Cardassianer.', 6500, 3500, 3000, 1900, 0, 0, 0, 0, 0, 1, 1, 1, 10, 3750, 2500, 2500, 1000, 0, 10, 3, 1000000, '14.900'),
(12, 'SAGITTARIUS Plasmaschild', 'Dieser spezielle Schild wurde schon oft zu kopieren versucht, doch bisher gelang es keiner anderen Rasse als den Serrakin, ihn so effizient herzustellen.', 'Dieser spezielle Schild wurde schon oft zu kopieren versucht, doch bisher gelang es keiner anderen Rasse als den Serrakin, ihn so effizient herzustellen.', 1350000, 1000000, 1050000, 625000, 0, 0, 0, 0, 0, 20, 1, 1, 2, 1400000, 2100000, 0, 0, 0, 10, 1, 1, '4025.000'),
(10, 'MAGNETRON Störsender', 'Diese defensive Anlage kann zufällige Signale in den Raum abgeben und so das Auffinden und Entschlüsseln der eigenen Flottenkommunikation durch gegnerische Spione erschweren.', 'Durch die Verfügbarkeit von grossen Rechenzentren ist in letzter Zeit die Bedrohung durch kryptographische Angriffe auf die eigenen Flottenfunkverbindungen stark angestiegen. Viele Generäle fühlten sich nicht mehr sicher, da ihre Feinde anscheinend plötzlich sehr genau wussten, wann und wo ihre Flotten landen würden. Dies führte zur Erfindung des MAGNETRON Störsenders. Die riesigen Sendeanlagen erzeugen zufällige Funksignale, die sie in den Raum abgeben. Eine gegnerische Analyse der Funksignale eines Planeten findet so viel zu viele Signale und hat Mühe, die richtigen herauszufiltern. ', 20000, 50000, 10000, 15000, 0, 0, 0, 0, 0, 5, 1, 1, 10, 15000, 1200, 0, 0, 1, 0, 3, 10, '95.000'),
(14, 'ZIBAL Laserturm M', 'Dieses Geschütz richtet einen gebündelten und starken Energiestrahl auf ihr Ziel. Mobile Version.', 'Dieses Geschütz richtet einen gebündelten und starken Energiestrahl auf ihr Ziel. Es ist eine weiterentwickelte Verteidigungsanlage, welche es auch mit grösseren Schiffen aufnehmen kann. Kann auf andere Planeten transportiert werden.', 3900, 3100, 2100, 1500, 0, 0, 8, 0, 0, 0, 1, 1, 3, 1500, 2000, 1800, 0, 0, 10, 2, 100000, '10.600'),
(15, 'POLARIS Raketengeschütz M', 'Die Raketen dieses Geschützes verfolgen ihr Ziel mittels Lasersteuerung. Mobile Version.', 'Um den gegnerischen Schiffen mit Raketen beizukommen, wurde dieses Raketengeschütz entwickelt. Es schiesst kleinere Raketen ab, welche dann das Ziel bis zur Zerstörung verfolgen. Es ist jedoch nicht sehr stark und dient vor allem zu Beginn als gute und billige Verteidigungswaffe. Kann auf andere Planeten transportiert werden.', 1000, 700, 300, 500, 0, 0, 3, 0, 0, 0, 1, 1, 2, 450, 325, 350, 0, 0, 10, 2, 1000000, '2.500'),
(16, 'SPICA Flakkanone M', 'Einfache und billige Abwehrwaffe. Mobile Version.', 'Einfache und billige Abwehrwaffe.\r\nSie wird auf Gebäuden befestigt und braucht daher keine Felder. Sie ist aber nicht sehr effektiv. Darum ist es besser, sie nur am Anfang und auch dann nur in grossen Mengen zu bauen. Kann auf andere Planeten transportiert werden.', 800, 475, 0, 425, 0, 0, 1, 0, 0, 0, 1, 1, 0, 300, 150, 250, 0, 0, 10, 2, 1000000, '1.700'),
(17, 'PHOENIX Reparaturplattform M', 'Diese Anlage repariert bei einem Kampf jede Runde eine gewisse Anzahl an Strukturpunkten, kann jedoch selbst auch zerstört werden. Mobile Version.', 'Diese Anlage repariert bei einem Kampf jede Runde eine gewisse Anzahl an Strukturpunkten, kann jedoch selbst auch zerstört werden.\r\nDie grundlegende Idee, welche zur Entwicklung dieser Reparaturplattform führte, fanden die Serrakin in den Mutterschiffen der Cardassianer. Kann auf andere Planeten transportiert werden.', 6500, 3500, 3000, 1900, 0, 0, 0, 0, 0, 1, 1, 1, 10, 3750, 2500, 2500, 1000, 0, 10, 3, 1000000, '14.900');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `deflist`
--

CREATE TABLE IF NOT EXISTS `deflist` (
`deflist_id` int(10) unsigned NOT NULL,
  `deflist_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `deflist_def_id` int(10) unsigned NOT NULL DEFAULT '0',
  `deflist_entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `deflist_count` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `def_cat`
--

CREATE TABLE IF NOT EXISTS `def_cat` (
`cat_id` int(10) unsigned NOT NULL,
  `cat_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `cat_order` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `cat_color` char(7) COLLATE utf8_unicode_ci NOT NULL DEFAULT '#ffffff'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `def_cat`
--

INSERT INTO `def_cat` (`cat_id`, `cat_name`, `cat_order`, `cat_color`) VALUES
(1, 'Schilder', 1, '#0080FF'),
(2, 'Geschütze', 0, '#00ff00'),
(3, 'Spezialanlagen', 2, '#B048F8');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `def_queue`
--

CREATE TABLE IF NOT EXISTS `def_queue` (
`queue_id` int(10) unsigned NOT NULL,
  `queue_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_def_id` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_cnt` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_objtime` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_build_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `queue_user_click_time` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `def_requirements`
--

CREATE TABLE IF NOT EXISTS `def_requirements` (
`id` int(10) unsigned NOT NULL,
  `obj_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_level` tinyint(3) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=78 ;

--
-- Daten für Tabelle `def_requirements`
--

INSERT INTO `def_requirements` (`id`, `obj_id`, `req_building_id`, `req_tech_id`, `req_level`) VALUES
(35, 12, 0, 16, 8),
(2, 2, 10, 0, 3),
(3, 3, 10, 0, 6),
(4, 3, 0, 3, 5),
(5, 4, 0, 3, 7),
(6, 4, 10, 0, 8),
(7, 4, 8, 0, 5),
(8, 5, 10, 0, 3),
(11, 5, 0, 3, 4),
(10, 5, 8, 0, 2),
(12, 6, 10, 0, 6),
(13, 6, 0, 3, 6),
(14, 6, 8, 0, 5),
(15, 7, 10, 0, 10),
(16, 7, 0, 8, 11),
(17, 7, 0, 3, 10),
(18, 8, 10, 0, 10),
(19, 8, 0, 3, 8),
(20, 8, 10, 10, 12),
(21, 8, 0, 9, 6),
(22, 8, 22, 0, 3),
(23, 3, 0, 8, 5),
(24, 4, 8, 8, 7),
(25, 9, 10, 0, 4),
(26, 9, 0, 8, 3),
(27, 9, 0, 4, 2),
(28, 11, 10, 0, 8),
(29, 11, 0, 25, 3),
(30, 11, 0, 16, 4),
(31, 11, 0, 19, 3),
(32, 12, 10, 0, 9),
(33, 12, 8, 0, 7),
(34, 12, 0, 3, 10),
(36, 10, 0, 25, 5),
(37, 10, 0, 11, 8),
(38, 10, 0, 3, 10),
(39, 10, 13, 0, 5),
(41, 14, 10, 0, 6),
(42, 14, 0, 3, 5),
(43, 14, 0, 8, 5),
(44, 14, 0, 12, 9),
(72, 15, 0, 12, 7),
(71, 15, 10, 0, 3),
(69, 16, 10, 0, 1),
(75, 17, 0, 16, 4),
(68, 1, 10, 0, 1),
(73, 17, 10, 0, 8),
(70, 16, 0, 12, 5),
(74, 17, 0, 25, 3),
(76, 17, 0, 19, 3),
(77, 17, 0, 12, 11);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `entities`
--

CREATE TABLE IF NOT EXISTS `entities` (
`id` int(8) unsigned NOT NULL,
  `cell_id` int(6) unsigned NOT NULL,
  `code` char(1) DEFAULT NULL,
  `pos` int(2) unsigned NOT NULL DEFAULT '0',
  `lastvisited` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 COMMENT='Entities in Space, acts as fleet targets' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `events`
--

CREATE TABLE IF NOT EXISTS `events` (
`event_id` int(10) unsigned NOT NULL,
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
  `event_alien_ship3_max` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Daten für Tabelle `events`
--

INSERT INTO `events` (`event_id`, `event_execrate`, `event_title`, `event_text`, `event_ask`, `event_answer_pos`, `event_answer_neg`, `event_reward_p_rate`, `event_reward_p_metal_min`, `event_reward_p_metal_max`, `event_reward_p_crystal_min`, `event_reward_p_crystal_max`, `event_reward_p_plastic_min`, `event_reward_p_plastic_max`, `event_reward_p_fuel_min`, `event_reward_p_fuel_max`, `event_reward_p_food_min`, `event_reward_p_food_max`, `event_reward_p_people_min`, `event_reward_p_people_max`, `event_reward_p_ship_id`, `event_reward_p_ship_min`, `event_reward_p_ship_max`, `event_reward_p_def_id`, `event_reward_p_def_min`, `event_reward_p_def_max`, `event_reward_p_building_id`, `event_reward_p_building_level`, `event_reward_p_tech_id`, `event_reward_p_tech_level`, `event_costs_p_rate`, `event_costs_p_metal_min`, `event_costs_p_metal_max`, `event_costs_p_crystal_min`, `event_costs_p_crystal_max`, `event_costs_p_plastic_min`, `event_costs_p_plastic_max`, `event_costs_p_fuel_min`, `event_costs_p_fuel_max`, `event_costs_p_food_min`, `event_costs_p_food_max`, `event_costs_p_people_min`, `event_costs_p_people_max`, `event_costs_p_ship_id`, `event_costs_p_ship_min`, `event_costs_p_ship_max`, `event_costs_p_def_id`, `event_costs_p_def_min`, `event_costs_p_def_max`, `event_costs_p_building_id`, `event_costs_p_building_level`, `event_costs_p_tech_id`, `event_costs_p_tech_level`, `event_reward_n_rate`, `event_reward_n_metal_min`, `event_reward_n_metal_max`, `event_reward_n_crystal_min`, `event_reward_n_crystal_max`, `event_reward_n_plastic_min`, `event_reward_n_plastic_max`, `event_reward_n_fuel_min`, `event_reward_n_fuel_max`, `event_reward_n_food_min`, `event_reward_n_food_max`, `event_reward_n_people_min`, `event_reward_n_people_max`, `event_reward_n_ship_id`, `event_reward_n_ship_min`, `event_reward_n_ship_max`, `event_reward_n_def_id`, `event_reward_n_def_min`, `event_reward_n_def_max`, `event_reward_n_building_id`, `event_reward_n_building_level`, `event_reward_n_tech_id`, `event_reward_n_tech_level`, `event_costs_n_rate`, `event_costs_n_metal_min`, `event_costs_n_metal_max`, `event_costs_n_crystal_min`, `event_costs_n_crystal_max`, `event_costs_n_plastic_min`, `event_costs_n_plastic_max`, `event_costs_n_fuel_min`, `event_costs_n_fuel_max`, `event_costs_n_food_min`, `event_costs_n_food_max`, `event_costs_n_people_min`, `event_costs_n_people_max`, `event_costs_n_ship_id`, `event_costs_n_ship_min`, `event_costs_n_ship_max`, `event_costs_n_def_id`, `event_costs_n_def_min`, `event_costs_n_def_max`, `event_costs_n_building_id`, `event_costs_n_building_level`, `event_costs_n_tech_id`, `event_costs_n_tech_level`, `event_alien_rate`, `event_alien_ship1_id`, `event_alien_ship1_min`, `event_alien_ship1_max`, `event_alien_ship2_id`, `event_alien_ship2_min`, `event_alien_ship2_max`, `event_alien_ship3_id`, `event_alien_ship3_min`, `event_alien_ship3_max`) VALUES
(1, 100, 'Bruchlandung Marauder', 'Ein Flotte mit {reward:p:ship} der Handelsföderation ist auf deinem Planeten {planet} abgestürzt, es würde dich {costs:p:metal}, {costs:p:crystal} und {costs:p:plastic} kosten das Schiff zu bergen. Möchtest du es bergen?', 1, 'Deine Bergungsmannschaft konnte {reward:p:shipcnt} {reward:p:ship} bergen!', 'Leider hast du deiner Bergungsmannschaft keine Ressourcen gegeben um {reward:p:shipcnt} {reward:p:ship} zu bergen!', '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 29, 1, 10, 0, 0, 0, 0, 0, 0, 0, '0.0100', 200, 400, 150, 300, 150, 300, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.9999', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(2, 100, 'Intergalaktischer Sturm', 'Ein intergalaktischer Sturm, ist über deinen Planeten {planet} gefegt, und hat dabei {reward:p:metal} und {reward:p:crystal} auf deinem Planeten hinterlassen. ', 0, '', '', '0.0100', 1, 200, 1, 200, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.9999', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(3, 100, 'Vulkanausbruch', 'Ein Vulkan ist auf deinem Planeten {planet} ausgebrochen, und hat {reward:p:metal} aus dem Erdinneren hervor gebracht. Das Abbauen kostet dich {costs:p:fuel} und {costs:p:food}. Sollen das Titan abgebaut werden?', 1, 'Es wurde {reward:p:metal} abgebaut!', '', '0.0100', 200, 400, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 20, 200, 30, 150, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(4, 100, 'Zusammenstoss von Gasplaneten', 'Nach dem Zusammenstoss von zwei Gasplaneten sind {reward:p:fuel} auf deinen Planeten {planet} gefallen.', 0, '', '', '0.0100', 0, 0, 0, 0, 0, 0, 10, 80, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(5, 100, 'Supernova', 'Bei einer Supernova sind {reward:p:crystal} ins Weltall geschleudert worden und nun auf deinem Planeten {planet} angekommen.', 0, '', '', '0.0100', 0, 0, 20, 70, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(6, 100, 'Unabhängige Bürger', 'Eine Gruppe von {reward:p:people} unabhängigen Bürgern ist auf deinem Planeten gelandet, möchtes du ihnen eine Unterkunft anbieten?', 1, '{reward:p:people} Bürger schliessen sich deiner Zivilisation an!', 'Die unabhängigen Bürger sind empört dass du ihnen keine Unterkunft gewährt hast und stürmen deine {costs:n:building}, dabei geht ein Teil kaputt und die Stufe des Silos verringert sich um {costs:n:buildinglevel}.', '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 100, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 19, 1, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0),
(7, 100, 'Defekter Antrieb', 'Ein Imperialisches Schlachtschiff hat einen defekten Antrieb, möchtest du der Besatzung {costs:p:crystal} geben, damit sie ihren Antrieb wieder benutzen können?', 0, 'Die Besatzung des Schlachtschiffes ist auf ihren Planeten zurück geflogen und hat sich dann entschieden, dir ein Geschenk zu machen: {reward:p:shipcnt} {reward:p:ship}', 'Die Piloten des fremden Schiffes versucht, das Silizium zu stehlen, dabei kommt es zu Aueinandersetzungen mit deiner Armee. Du verlierst {costs:n:people} Bürger und {costs:n:crystal}', '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2, 1, 11, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 350, 841, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 50, 0, 0, 0, 0, 0, 0, 0, 50, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0.0100', 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `events_exec`
--

CREATE TABLE IF NOT EXISTS `events_exec` (
`event_id` int(10) unsigned NOT NULL,
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
  `event_alien_ship3` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fleet`
--

CREATE TABLE IF NOT EXISTS `fleet` (
`id` mediumint(10) unsigned NOT NULL,
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
  `flag` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'UpdateTestFlag'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fleet_bookmarks`
--

CREATE TABLE IF NOT EXISTS `fleet_bookmarks` (
`id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `target_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `ships` text NOT NULL,
  `res` text NOT NULL,
  `resfetch` text NOT NULL,
  `action` char(15) NOT NULL,
  `speed` mediumint(8) unsigned NOT NULL DEFAULT '100'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fleet_ships`
--

CREATE TABLE IF NOT EXISTS `fleet_ships` (
`fs_id` int(10) unsigned NOT NULL,
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
  `fs_special_ship_bonus_readiness` tinyint(2) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hostname_cache`
--

CREATE TABLE IF NOT EXISTS `hostname_cache` (
  `addr` char(39) NOT NULL,
  `host` varchar(100) NOT NULL,
  `timestamp` int(11) NOT NULL
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ip_ban`
--

CREATE TABLE IF NOT EXISTS `ip_ban` (
`id` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `msg` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `login_failures`
--

CREATE TABLE IF NOT EXISTS `login_failures` (
`failure_id` int(10) unsigned NOT NULL,
  `failure_time` int(10) unsigned NOT NULL DEFAULT '0',
  `failure_ip` varchar(15) NOT NULL,
  `failure_host` varchar(50) NOT NULL,
  `failure_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `failure_pw` char(50) NOT NULL,
  `failure_client` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs`
--

CREATE TABLE IF NOT EXISTS `logs` (
`id` int(10) unsigned NOT NULL,
  `facility` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `severity` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `ip` varchar(39) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs_alliance`
--

CREATE TABLE IF NOT EXISTS `logs_alliance` (
`logs_alliance_id` int(10) NOT NULL,
  `logs_alliance_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `logs_alliance_text` text NOT NULL,
  `logs_alliance_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `logs_alliance_alliance_tag` varchar(10) NOT NULL DEFAULT '0',
  `logs_alliance_alliance_name` varchar(30) NOT NULL,
  `logs_alliance_user_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs_battle`
--

CREATE TABLE IF NOT EXISTS `logs_battle` (
`id` int(10) unsigned NOT NULL,
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
  `win_metal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `win_crystal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `win_pvc` bigint(20) unsigned NOT NULL DEFAULT '0',
  `win_tritium` bigint(20) unsigned NOT NULL DEFAULT '0',
  `win_food` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tf_metal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tf_crystal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tf_pvc` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs_battle_queue`
--

CREATE TABLE IF NOT EXISTS `logs_battle_queue` (
`id` int(10) unsigned NOT NULL,
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
  `win_metal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `win_crystal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `win_pvc` bigint(20) unsigned NOT NULL DEFAULT '0',
  `win_tritium` bigint(20) unsigned NOT NULL DEFAULT '0',
  `win_food` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tf_metal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tf_crystal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tf_pvc` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs_fleet`
--

CREATE TABLE IF NOT EXISTS `logs_fleet` (
`id` int(10) unsigned NOT NULL,
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
  `entity_ships_end` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs_fleet_queue`
--

CREATE TABLE IF NOT EXISTS `logs_fleet_queue` (
`id` int(10) unsigned NOT NULL,
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
  `entity_ships_end` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs_game`
--

CREATE TABLE IF NOT EXISTS `logs_game` (
`id` int(10) unsigned NOT NULL,
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
  `level` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs_game_queue`
--

CREATE TABLE IF NOT EXISTS `logs_game_queue` (
`id` int(10) unsigned NOT NULL,
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
  `level` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `logs_queue`
--

CREATE TABLE IF NOT EXISTS `logs_queue` (
`id` int(10) unsigned NOT NULL,
  `facility` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `severity` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `ip` varchar(39) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `market_auction`
--

CREATE TABLE IF NOT EXISTS `market_auction` (
`id` int(10) unsigned NOT NULL,
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
  `sent` tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `market_rates`
--

CREATE TABLE IF NOT EXISTS `market_rates` (
`id` int(10) unsigned NOT NULL,
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
  `rate_5` float unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `market_ressource`
--

CREATE TABLE IF NOT EXISTS `market_ressource` (
`id` int(10) unsigned NOT NULL,
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
  `datum` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `market_ship`
--

CREATE TABLE IF NOT EXISTS `market_ship` (
`id` int(10) unsigned NOT NULL,
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
  `datum` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
`message_id` mediumint(10) unsigned NOT NULL,
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
  `message_mailed` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `message_cat`
--

CREATE TABLE IF NOT EXISTS `message_cat` (
`cat_id` int(10) unsigned NOT NULL,
  `cat_name` varchar(50) NOT NULL,
  `cat_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cat_desc` text NOT NULL,
  `cat_sender` varchar(50) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Daten für Tabelle `message_cat`
--

INSERT INTO `message_cat` (`cat_id`, `cat_name`, `cat_order`, `cat_desc`, `cat_sender`) VALUES
(1, 'Persönliche Nachrichten', 0, '', ''),
(2, 'Spionageberichte', 1, '', 'Flottenkontrolle'),
(3, 'Kriegsberichte', 2, '', 'Flottenkontrolle'),
(4, 'Überwachungsberichte', 3, '', 'Raumüberwachung'),
(5, 'Sonstige Nachrichten', 5, '', 'System'),
(6, 'Allianz', 4, '', 'Allianzverwaltung'),
(7, 'Account', 5, '', 'EtoA Administration');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `message_data`
--

CREATE TABLE IF NOT EXISTS `message_data` (
  `id` mediumint(8) unsigned NOT NULL,
  `subject` varchar(200) NOT NULL,
  `text` text NOT NULL,
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fleet_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `message_ignore`
--

CREATE TABLE IF NOT EXISTS `message_ignore` (
`ignore_id` int(10) unsigned NOT NULL,
  `ignore_owner_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ignore_target_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `minimap`
--

CREATE TABLE IF NOT EXISTS `minimap` (
`field_id` int(10) unsigned NOT NULL,
  `field_x` int(3) unsigned NOT NULL DEFAULT '0',
  `field_y` int(3) unsigned NOT NULL DEFAULT '0',
  `field_typ_id` int(3) unsigned NOT NULL DEFAULT '0',
  `field_event_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `minimap_field_types`
--

CREATE TABLE IF NOT EXISTS `minimap_field_types` (
`field_typ_id` int(3) unsigned NOT NULL,
  `field_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `field_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `field_blocked` int(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `missilelist`
--

CREATE TABLE IF NOT EXISTS `missilelist` (
`missilelist_id` int(10) unsigned NOT NULL,
  `missilelist_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `missilelist_entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `missilelist_missile_id` int(10) unsigned NOT NULL DEFAULT '0',
  `missilelist_count` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `missiles`
--

CREATE TABLE IF NOT EXISTS `missiles` (
`missile_id` int(10) unsigned NOT NULL,
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
  `missile_show` tinyint(1) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `missiles`
--

INSERT INTO `missiles` (`missile_id`, `missile_name`, `missile_sdesc`, `missile_ldesc`, `missile_costs_metal`, `missile_costs_crystal`, `missile_costs_plastic`, `missile_costs_fuel`, `missile_costs_food`, `missile_damage`, `missile_speed`, `missile_range`, `missile_deactivate`, `missile_def`, `missile_launchable`, `missile_show`) VALUES
(1, 'PHOBOS Rakete', 'Zerstört gegnerische Verteidigung.', 'Diese Rakete kann auf Verteidigungsanlagen eines feindlichen Planeten abgefeuert werden und verursacht an diesen einen gewissen Schaden, so dass einige Anlagen unter Umständen zerstört werden. Diese Raketen haben eine begrenzte Reichweite, treffen ihr Ziel aber immer.', 18000, 6000, 5000, 15000, 0, 25000, 100000, 3000, 0, 0, 1, 1),
(2, 'GEMINI Abfangrakete', 'Abfangraketen schiessen selbstständig gegnerische Raketen ab, die diesen Planeten anfliegen.', 'Bei einem Raketenangriff können diese Raketen jeweils eine fremde Rakete zerstören. Sie lösen sich selbständig aus und bieten so einen guten Schutz gegen anfliegende Raketen. Gegen feindliche Flotten können sie jedoch nichts ausrichten. Ausserdem ist die Rakete nach dem Abfangen verbraucht und muss jeweils wieder neu gekauft werden.', 9000, 18000, 6000, 4000, 2000, 0, 0, 0, 0, 1, 0, 1),
(3, 'VEGA EMP-Rakete', 'Kann ein gegnerisches Gebäude temporär deaktivieren.', 'Diese Rakete kann angreifen um ein gegnerisches Gebäude temporär ausser Kraft zu setzen. Sie richtet an der Verteidigung aber keinen Schaden an und kann ein Gebäude auch nicht vollständig zerstören! Die Rakete wird beim EMP-Angriff verbraucht und hat auch nur eine begrenzte Reichweite.', 18000, 6000, 5000, 15000, 0, 0, 90000, 3000, 300, 0, 1, 1),
(4, 'VIRGO Abfangrakete', 'Verbesserte Abfangrakete; schiesst selbstständig zwei gegnerische Raketen ab.', 'Bei einem Raketenangriff können diese Raketen jeweils zwei fremde Rakete zerstören. Sie lösen sich  selbständig aus und bieten so einen guten Schutz. Gegen feindliche Flotten können sie jedoch nichts ausrichten. Ausserdem ist die Rakete nach dem Abfangen verbraucht und muss jeweils wieder neu gekauft werden.', 15000, 23000, 9000, 4000, 2000, 0, 0, 0, 0, 2, 0, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `missile_flights`
--

CREATE TABLE IF NOT EXISTS `missile_flights` (
`flight_id` int(10) unsigned NOT NULL,
  `flight_entity_from` int(10) unsigned NOT NULL DEFAULT '0',
  `flight_entity_to` int(10) unsigned NOT NULL DEFAULT '0',
  `flight_starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `flight_landtime` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `missile_flights_obj`
--

CREATE TABLE IF NOT EXISTS `missile_flights_obj` (
`obj_id` int(10) unsigned NOT NULL,
  `obj_flight_id` int(10) unsigned NOT NULL DEFAULT '0',
  `obj_missile_id` int(10) unsigned NOT NULL DEFAULT '0',
  `obj_cnt` int(15) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `missile_requirements`
--

CREATE TABLE IF NOT EXISTS `missile_requirements` (
`id` int(10) unsigned NOT NULL,
  `obj_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_level` tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Daten für Tabelle `missile_requirements`
--

INSERT INTO `missile_requirements` (`id`, `obj_id`, `req_building_id`, `req_tech_id`, `req_level`) VALUES
(1, 2, 25, 0, 1),
(2, 2, 0, 25, 1),
(4, 1, 25, 0, 3),
(5, 1, 0, 24, 3),
(6, 3, 25, 0, 4),
(7, 3, 0, 24, 5),
(8, 4, 25, 0, 5),
(9, 4, 0, 24, 6),
(10, 4, 0, 25, 5),
(11, 1, 0, 25, 2),
(12, 3, 0, 25, 4),
(13, 3, 0, 17, 8),
(14, 1, 0, 8, 8);

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
  `res_power` bigint(20) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `notepad`
--

CREATE TABLE IF NOT EXISTS `notepad` (
`id` mediumint(8) unsigned NOT NULL,
  `user_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `notepad_data`
--

CREATE TABLE IF NOT EXISTS `notepad_data` (
  `id` mediumint(8) unsigned NOT NULL,
  `subject` varchar(100) NOT NULL,
  `text` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `obj_transforms`
--

CREATE TABLE IF NOT EXISTS `obj_transforms` (
`id` smallint(5) unsigned NOT NULL,
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
  `num_def` int(10) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `obj_transforms`
--

INSERT INTO `obj_transforms` (`id`, `def_id`, `ship_id`, `costs_metal`, `costs_crystal`, `costs_plastic`, `costs_fuel`, `costs_food`, `costs_power`, `costs_factor_sd`, `costs_factor_ds`, `num_def`) VALUES
(1, 14, 79, 0, 0, 0, 0, 0, 0, '0.0', '1.0', 1),
(2, 15, 81, 0, 0, 0, 0, 0, 0, '0.0', '1.0', 1),
(3, 16, 80, 0, 0, 0, 0, 0, 0, '0.0', '1.0', 1),
(4, 17, 82, 0, 0, 0, 0, 0, 0, '0.0', '1.0', 1);

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
  `invadedby` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `planet_types`
--

CREATE TABLE IF NOT EXISTS `planet_types` (
`type_id` int(10) unsigned NOT NULL,
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
  `type_consider` tinyint(3) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Daten für Tabelle `planet_types`
--

INSERT INTO `planet_types` (`type_id`, `type_name`, `type_habitable`, `type_comment`, `type_f_metal`, `type_f_crystal`, `type_f_plastic`, `type_f_fuel`, `type_f_food`, `type_f_power`, `type_f_population`, `type_f_researchtime`, `type_f_buildtime`, `type_collect_gas`, `type_consider`) VALUES
(1, 'Erdähnlicher Planet', 1, 'Dieser Planet hat eine sehr ausgeglichene Umwelt und ähnelt unseren ehemaligen Erde am meisten. Da der Mensch ein Gewohnheitstier ist, sind erdähnliche Planeten ideal für das Heranwachsen einer Zivilisation geeignet, da die notwendigen Voraussetzungen für alle Bereiche gegeben sind.', '1.35', '1.10', '1.40', '1.20', '1.60', '1.30', '1.40', '0.80', '0.80', 0, 1),
(2, 'Wasserplanet', 1, 'Die Oberfläche dieses Planeten besteht zum grössten Teil aus Ozeanen. Die wenigen Landteile sind nicht wirklich geeignet für grossflächigen Abbau von Mineralen, dafür kann aus dem Wasser Tritium gewonnen werden. Ebenfalls ist durch das viele vorhandene Wasser die Hauptgrundlage für Nahrungsabbau gelegt, ausserdem ist der Planet bestens geeignet, mit Hilfe von Wasserkraftwerken grosse Mengen an Energie zu erzeugen.', '1.20', '1.20', '1.20', '1.35', '1.20', '1.40', '1.00', '0.80', '0.65', 0, 1),
(3, 'Wüstenplanet', 1, 'Wüste, Sand, Trockenheit und ein unwirtliches Klima zeichnet diesen Planetentyp aus. Der allgegenwärtige Sand hat aber auch etwas positives, denn aus ihm können grosse Mengen von wertvollem Silizium gewonnen werden.', '1.10', '1.55', '1.20', '1.10', '1.20', '1.10', '1.05', '0.65', '0.90', 0, 1),
(4, 'Eisplanet', 1, 'Auf diesem unwirtlichen Planeten lockt einzig der Abbau von Tritium, welches sich aus den Eisschichen herausextrahieren lässt. Vor kurzem haben Forscher eine neue chemische Methode entwickelt, aus Eismassen Silizium zu gewinnen. Diese neuartige Abbaumöglichkeit macht die Eisplaneten für Silizium-Anwender interessanter.', '1.30', '1.50', '1.45', '1.40', '1.15', '1.10', '0.90', '0.80', '0.80', 0, 1),
(5, 'Dschungelplanet', 1, 'Riesige Wälder wachsen auf diesem Planeten, dessen Klima sehr gut für das Wachstum der Umwelt ist. Daher kann viel Nahrung für die Bevölkerung geerntet werden, welche sich auf einem Dschungelplaneten auch sonst sehr wohl fühlt.', '1.10', '1.35', '1.40', '1.20', '1.60', '1.20', '1.20', '0.80', '0.80', 0, 1),
(6, 'Gebirgsplanet', 1, 'Den Namen hat dieser Planetentyp durch seine felsige Oberfläche erhalten. Ein Abbau von Erzen bietet sich optimalerweise an, hingegen ist der Abbau von Nahrung und die Herstellung von PVC mit Aufwand verbunden, da die Umgebung deren Anforderungen nicht gerecht wird.', '1.46', '1.20', '1.10', '1.20', '1.10', '1.30', '1.10', '0.80', '0.70', 0, 1),
(7, 'Gasplanet', 0, 'Dieser Planet ist unbewohnbar, da er keine feste Oberfläche hat, sondern aus lauter gasartigen Nebeln besteht. Seine Gase lassen sich jedoch mit Hilfe von Gassaugern zu Tritium umwandeln.', '0.70', '0.80', '0.60', '3.20', '0.50', '1.40', '0.50', '1.00', '1.00', 1, 1),
(8, 'Alienplanet', 0, 'Sagen und Mythen ranken sich um diesen Planeten. ', '1.50', '1.50', '1.50', '1.50', '1.50', '1.50', '1.50', '1.00', '1.00', 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `races`
--

CREATE TABLE IF NOT EXISTS `races` (
`race_id` int(10) unsigned NOT NULL,
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
  `race_active` tinyint(1) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Daten für Tabelle `races`
--

INSERT INTO `races` (`race_id`, `race_name`, `race_comment`, `race_short_comment`, `race_adj1`, `race_adj2`, `race_adj3`, `race_leadertitle`, `race_f_researchtime`, `race_f_buildtime`, `race_f_fleettime`, `race_f_metal`, `race_f_crystal`, `race_f_plastic`, `race_f_fuel`, `race_f_food`, `race_f_power`, `race_f_population`, `race_active`) VALUES
(1, 'Terraner', 'Die Terraner sind eine eher jüngere Rasse, deren Vorfahren ursprünglich vom Planeten Erde kamen. Die Menschen sind besonders gut in Forschung, der Herstellung von Plastik und dem Anbau von Nahrung. Ihre Schwächen liegen im Abbau von Erzen und Kristallen. Da sie ihre ganzen Ressourcen in die Forschung steckten, sind ihre Schiffe relativ langsam.', '', 'terranischer', 'terranisches', 'terranische', 'Präsident der Terraner', '0.85', '1.00', '0.95', '0.90', '0.90', '1.30', '1.00', '1.30', '1.00', '1.00', 1),
(2, 'Andorianer', 'Die Andorianer sind zugleich humanoid und insektoid. Sie haben graublaue Haut und weisses Haar. Auf ihrem Kopf haben sie zwei Fühler, die ihnen zur feinfühligen sinnlichen Wahrnehmung dienen. Ihre Stärke ist die Produktion künstlicher Stoffe wie Plastik. Ihre Schwäche ist der schlechte Umgang mit Energie.', '', '', '', '', 'Schwarmführer der Andorianer', '1.00', '1.00', '1.00', '1.00', '1.10', '1.60', '1.00', '1.00', '0.90', '1.00', 1),
(3, 'Rigelianer', 'Die Rigelianer stammen aus dem Rigel-System. Ihre Stärke liegt im Abbau von Kristallen, die für Steuereinheiten in Gebäuden und Schiffen verwendet werden. Da sie lange nur auf den Handel mit Silizium gesetzt haben, sind ihre Kenntnisse beim Abbau anderer Stoffe eher schlecht.', '', '', '', '', 'Kaiser der Rigelianer', '1.00', '1.00', '1.00', '0.80', '1.80', '0.90', '0.90', '0.90', '1.00', '1.10', 1),
(4, 'Orioner', 'Die Orioner sind eine humanoide Rasse aus der Nähe des Orions. Die Gesellschaft der Orioner besteht hauptsächlich aus Schmugglern und Piraten. Ihre Schiffe sind bekannt für ihre Schnelligkeit.', '', '', '', '', 'Kapitän der Orioner', '1.00', '1.00', '2.00', '1.10', '1.10', '0.80', '0.90', '1.00', '1.00', '1.10', 1),
(5, 'Minbari', 'Die Minbari sind eine humanoide Rasse. Dadurch, dass sie den Rohstoff Erdöl nie gekannt haben, sind sie seit Ewigkeiten auf den Abbau von Tritium spezialisiert. Durch ihre enormen Treibstoffreserven und ihre grossen Anwendungskenntnisse von Tritium haben sie relativ schnelle Raumschiffe.', 'Eine Rasse mit schnellen Schiffen und grossem Wissen über Tritium.', 'minbarischer', '', 'minbarische', 'Vorsteher des Minbarikonzils', '1.00', '1.00', '1.20', '0.90', '0.90', '1.10', '1.80', '1.00', '1.00', '1.00', 1),
(8, 'Centauri', 'Die Centauri haben die besten Wissenschaftler des Universums, darum können sie auch schneller Technologien erforschen. Allerdings verbrauchen sie für ihre Labore sehr viel Strom.', '', '', '', '', 'Professor der Centauri', '0.60', '1.00', '1.00', '0.90', '0.90', '1.10', '0.90', '1.00', '0.85', '1.00', 1),
(6, 'Ferengi', 'Die Ferengi sind eine humanoide Rasse. Sie sind etwas kleinwüchsiger als Menschen und  haben grosse Ohren. Die Stärke der Ferengi liegt beim Abbau von Metall.', '', '', '', '', 'Grosser Nagus der Ferengi', '1.00', '1.00', '1.20', '1.60', '0.90', '1.00', '0.90', '1.00', '1.00', '1.00', 1),
(7, 'Vorgonen', 'Die Vorgonen sind eine Rasse, die vor allem gut bauen kann. Sie können ihre Gebäude viel schneller fertig stellen als alle Anderen. Dafür lassen ihre Schiffe und ihre Produktionsrate zu wünschen übrig.', '', '', '', '', 'Architekt der Vorgonen', '1.00', '0.60', '0.95', '0.90', '0.80', '0.80', '1.10', '0.70', '1.20', '0.90', 1),
(9, 'Cardassianer', 'Seit einer grossen Hungersnot haben sich die Cardassianer auf die Nahrungsherstellung spezialisiert, haben aber den Abbau von Erzen vernachlässigt.\r\nIhre andere Stärke liegt in der Fähigkeit der Mutterschiffe zur Regeneration von ganzen Flottenverbänden.', '', '', '', '', 'Zentralrat der Cardassianer', '1.00', '1.00', '1.00', '0.80', '0.90', '1.20', '1.00', '1.60', '1.10', '1.10', 1),
(10, 'Serrakin', 'Die Serrakin sind eine sehr friedliche Rasse, welche sich nicht gerne in grosse Auseinandersetzungen einmischt. Sie weiss sich aber bei Angriffen sehr gut zu wehren, da die Verteidigungstechnologie ihr Spezialgebiet ist.', '', 'serrakinischer', '', 'serrakinische', 'Beschützer der Serrakin', '1.00', '1.00', '0.90', '1.15', '1.15', '1.10', '0.90', '1.10', '1.10', '0.80', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
`id` int(11) NOT NULL,
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
  `opponent1_id` int(10) unsigned NOT NULL DEFAULT '0'
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
  `res_0` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_1` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_2` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_3` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_4` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_5` bigint(20) unsigned NOT NULL DEFAULT '0',
  `wf_0` bigint(20) unsigned NOT NULL DEFAULT '0',
  `wf_1` bigint(20) unsigned NOT NULL DEFAULT '0',
  `wf_2` bigint(20) unsigned NOT NULL DEFAULT '0',
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
  `sell_0` bigint(20) unsigned NOT NULL DEFAULT '0',
  `sell_1` bigint(20) unsigned NOT NULL DEFAULT '0',
  `sell_2` bigint(20) unsigned NOT NULL DEFAULT '0',
  `sell_3` bigint(20) unsigned NOT NULL DEFAULT '0',
  `sell_4` bigint(20) unsigned NOT NULL DEFAULT '0',
  `sell_5` bigint(20) unsigned NOT NULL DEFAULT '0',
  `buy_0` bigint(20) unsigned NOT NULL DEFAULT '0',
  `buy_1` bigint(20) unsigned NOT NULL DEFAULT '0',
  `buy_2` bigint(20) unsigned NOT NULL DEFAULT '0',
  `buy_3` bigint(20) unsigned NOT NULL DEFAULT '0',
  `buy_4` bigint(20) unsigned NOT NULL DEFAULT '0',
  `buy_5` bigint(20) unsigned NOT NULL DEFAULT '0',
  `factor` float NOT NULL DEFAULT '1',
  `fleet1_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fleet2_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ship_count` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp2` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `reports_other`
--

CREATE TABLE IF NOT EXISTS `reports_other` (
  `id` int(10) unsigned NOT NULL,
  `subtype` char(20) NOT NULL DEFAULT 'other',
  `fleet_id` int(10) unsigned NOT NULL DEFAULT '0',
  `res_0` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_1` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_2` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_3` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_4` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_5` bigint(20) unsigned NOT NULL DEFAULT '0',
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
  `res_0` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_1` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_2` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_3` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_4` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_5` bigint(20) unsigned NOT NULL DEFAULT '0',
  `spydefense` smallint(5) unsigned NOT NULL DEFAULT '0',
  `coverage` smallint(5) unsigned NOT NULL,
  `fleet_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `shiplist`
--

CREATE TABLE IF NOT EXISTS `shiplist` (
`shiplist_id` int(10) unsigned NOT NULL,
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
  `shiplist_special_ship_bonus_readiness` tinyint(2) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ships`
--

CREATE TABLE IF NOT EXISTS `ships` (
`ship_id` int(10) unsigned NOT NULL,
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
  `special_ship_bonus_readiness` decimal(4,2) NOT NULL,
  `ship_points` decimal(18,3) unsigned NOT NULL DEFAULT '0.000',
  `ship_alliance_shipyard_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ship_alliance_costs` mediumint(5) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=91 ;

--
-- Daten für Tabelle `ships`
--

INSERT INTO `ships` (`ship_id`, `ship_name`, `ship_type_id`, `ship_shortcomment`, `ship_longcomment`, `ship_costs_metal`, `ship_costs_crystal`, `ship_costs_fuel`, `ship_costs_plastic`, `ship_costs_food`, `ship_costs_power`, `ship_power_use`, `ship_fuel_use`, `ship_fuel_use_launch`, `ship_fuel_use_landing`, `ship_prod_power`, `ship_capacity`, `ship_people_capacity`, `ship_pilots`, `ship_speed`, `ship_time2start`, `ship_time2land`, `ship_show`, `ship_buildable`, `ship_order`, `ship_actions`, `ship_bounty_bonus`, `ship_heal`, `ship_structure`, `ship_shield`, `ship_weapon`, `ship_race_id`, `ship_launchable`, `ship_fieldsprovide`, `ship_cat_id`, `ship_fakeable`, `special_ship`, `ship_max_count`, `special_ship_max_level`, `special_ship_need_exp`, `special_ship_exp_factor`, `special_ship_bonus_weapon`, `special_ship_bonus_structure`, `special_ship_bonus_shield`, `special_ship_bonus_heal`, `special_ship_bonus_capacity`, `special_ship_bonus_speed`, `special_ship_bonus_pilots`, `special_ship_bonus_tarn`, `special_ship_bonus_antrax`, `special_ship_bonus_forsteal`, `special_ship_bonus_build_destroy`, `special_ship_bonus_antrax_food`, `special_ship_bonus_deactivade`, `special_ship_bonus_readiness`, `ship_points`, `ship_alliance_shipyard_level`, `ship_alliance_costs`) VALUES
(1, 'UNUKALHAI Transportschiff', 1, 'Dies ist ein grosses Transportschiff, dessen riesige Lagerräume alle Arten von Waren aufnehmen können. ', 'Nachdem die Algol Transportschiffe sich mit einem ungeahnten Erfolg im ganzen Universum verbreitet hatten, wurde das Unukalhai Transportschiff konzipiert, welches eine grössere Lagerkapzität aufweist. Da man die Konvois mit Antares schützte, war auch für die Unukalhais keine grössere Bewaffnung nötig; man konzentrierte sich ausserdem vor allem auch auf die grössere Sicherheit für die Navigationssysteme, weil diese bei den Algols viel wegen kosmischer Strahlung ausgefallen sind.', 6000, 1400, 0, 2100, 0, 0, 0, 45, 70, 10, 0, 65000, 0, 1, 2850, 600, 300, 1, 1, 0, 'transport,position,fetch,attack,flight,support,alliance', '0.50', 0, 400, 100, 30, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '9.500', 0, 0),
(2, 'ANTARES Jäger', 1, 'Kleines Kampfschiff, ideal für die Begleitung kleinerer Konvois. Auch geeignet für Raubzüge und Übergriffe auf schwach befestigte Planeten.', 'Der Antares Jäger wurde als erster kampftauglicher Jäger hergestellt, um die Rohstoffkonvois vor Piraten zu schützen. Sie eignen sich zu Beginn als Begleitschutz, aber ihre Technologie ist nicht sehr weit entwickelt, deshalb sind die Herstellungskosten im Vergleich mit ihrer Leistung relativ hoch. Die Antares wurden nicht für grössere Angriffe auf befestigte Planeten konzipiert, auch deshalb werden sie von den wenigsten Armeen in grösseren Mengen genutzt.', 750, 575, 50, 420, 0, 0, 0, 5, 4, 1, 0, 500, 0, 1, 380, 15, 13, 1, 1, 0, 'transport,position,attack,flight,support,alliance', '0.50', 0, 330, 60, 170, 0, 1, 0, 1, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.795', 0, 0),
(3, 'ZAVIJAH Spionagesonde', 1, 'Diese Sonde erkundet fremde Planeten und sendet die Daten an dein Kontrollzentrum zurück.', 'Nachdem die Raumpiraten wegen den schnell konstruierten planetaren Verteidigungsanlagen nicht mehr jedes System gefahrlos ausrauben konnten, erfanden sie dieses kleine, nützliche Schiff. Es kann wegen seiner Grösse praktisch unbemerkt in Frage kommende Planeten ausspionieren und detaillierte Informationen über die stationierte Flotte liefern. Dank seiner Geschwindigkeit wird es dabei äusserst selten abgeschossen. Um diese Geschwindigkeit erreichen zu können, müssen sie sehr leicht gebaut sein und können keine Bewaffnung tragen. Ausserdem haben sie einen sehr kleinen Laderaum und können deshalb nur über kürzere Distanzen verwendet werden.', 100, 300, 0, 80, 0, 0, 0, 1, 1, 0, 0, 150, 0, 0, 25000, 2, 1, 1, 1, 9, 'position,spy,flight,support', '0.50', 0, 10, 1, 0, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.480', 0, 0),
(4, 'TAURUS Besiedlungsschiff', 1, 'Das TAURUS Besiedlungsschiff ist ein Schiff, mit dem andere Planeten besiedelt werden können. Es kann Rohstoffe und Passagiere aufnehmen, ist aber auch dementsprechend langsam.', 'Sobald auf dem Heimatplaneten die grundlegende Infrastruktur aufgebaut war, waren die Herrscher mit nur einem Planeten nicht mehr zufrieden. Also baute man die Taurus Besiedlungsschiffe, die andere Planeten für das eigene Imperium annektieren können. Da sie die ganze Lebenserhaltung für die Kolonialisten in einer lebensfeindlichen Umwelt gewährleisten müssen, gestaltet sich ihre Herstellung als langwierig und teuer, und das Schiff kann wegen seiner Masse nur langsam bewegt werden.', 8000, 10500, 1200, 5000, 0, 0, 0, 13, 15, 5, 0, 10000, 0, 5, 750, 600, 660, 1, 1, 8, 'transport,position,attack,colonize,flight,support,alliance', '0.50', 0, 1000, 500, 100, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '24.700', 0, 0),
(6, 'HADAR Schlachtschiff', 1, 'Das HADAR-Schlachtschiff ist ein gut gepanzertes und stark bewaffnetes Kriegsschiff. Mit ihm können auch grössere Verteidigungsstellungen ausgeschaltet, oder die eigenen Planeten vor Angriffen geschützt werden.', 'Nachdem jede noch so kleine Nation eine Verteidigung errichtet hatte, welche mit Antares ohne tragbare Verluste nicht geknackt werden konnte, entschlossen sich die grösseren Nationen, ein neues Kampfschiff zu konstruieren. Man nahm den Rumpf eines Besiedlungsschiffes, baute Waffen und eine Panzerung ein, und das Hadar Schlachtschiff war geboren.', 50000, 31500, 19500, 12500, 0, 0, 0, 45, 90, 80, 0, 8500, 0, 4, 3200, 1260, 220, 1, 1, 3, 'transport,position,attack,flight,support,alliance', '0.50', 0, 28200, 7100, 13000, 0, 1, 0, 1, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '113.500', 0, 0),
(7, 'POLLUX Bomber', 1, 'Dieses Raumschiff ist sehr effektiv gegen gegnerische Verteidigungsanlagen.', 'Trotz allen Erfolgen, die die Hadar Schlachtschiffe bei der Zerstörung gegnerischer Flotten und Verteidigung erzielten, war man damit noch nicht zufrieden. Deshalb konstruierte man ein neues, bis an die Zähne bewaffnetes Schiff, den Pollux Bomber. Nachdem man das Schiff mit Waffen beladen hatte, erwies es sich, dass deshalb die Angriffsgeschwindigkeit eingeschränkt wurde. Wegen diesem Nachteil konnte der Bomber sich in grossen Flotten nicht etablieren, er ist aber trotzdem in allem eine nicht zu unterschätzende Waffe, welche grosse Zerstörung anrichten kann.', 9700, 21000, 11500, 8500, 0, 0, 0, 55, 80, 70, 0, 2000, 0, 2, 1200, 600, 120, 1, 1, 4, 'transport,position,attack,flight,support,alliance', '0.50', 0, 2600, 500, 18000, 0, 1, 0, 1, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '50.700', 0, 0),
(8, 'SIRIUS Invasionsschiff', 1, 'Mit Hilfe dieses Raumschiffes können Planeten von anderen Spielern übernommen werden.', 'Es gab einmal ein florierendes Wirtschaftsimperium und die Infrastruktur ihrer Kolonien wurde von den anderen Völkern beneidet. Einer dieser bösen Nachbaren hatte die Idee, dass er so einen Planeten wirklich gut gebrauchen könnte. So wurde unter strengster Geheimhaltung dieses Invasionsschiff gebaut, welches die Planeten anderer Spieler übernehmen kann. Das Schiff hat aber nicht die grössten Erfolgschancen und es kann keine Hauptplaneten übernehmen. Trotzdem stellt dieses Schiff eine Bedrohung dar, deshalb sollte man seine Planeten nie unbewacht lassen.', 80000, 35000, 55000, 40500, 0, 0, 0, 15, 800, 500, 0, 20000, 0, 20, 600, 800, 500, 1, 1, 9, 'transport,position,attack,invade,flight,support,alliance', '0.50', 0, 2000, 3000, 180, 0, 1, 0, 1, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '210.500', 0, 0),
(9, 'ALGOL Transportschiff', 1, 'Dies ist ein kleines Transportschiff, dessen Lagerräume alle Arten von Waren aufnehmen können. ', 'Das Algol Transportschiff war das erste wirkliche Raumschiff, welches in Serienproduktion ging. Man wollte damit vor allem Rohstoffe zu anderen Planeten transportieren, damit man die natürlichen Ressourcen der verschiedenen Planeten besser ausnutzen kann. Deshalb hat man bei der Ausrüstung auf eine Bewaffnung weitestgehend verzichtet. Obwohl Algols mittlerweile veraltet sind, hat man dieses beliebte Schiff immer wieder mit neuen Motoren modifiziert, deshalb sieht man auch heute noch viele Transporter ähnlichen Typs.', 700, 180, 0, 500, 0, 0, 0, 25, 9, 4, 0, 15000, 0, 1, 800, 500, 250, 1, 1, 7, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 300, 500, 10, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.380', 0, 0),
(10, 'REGULUS Trümmersammler', 1, 'Mit diesem Schiff können die Trümmer der nach einer Schlacht zerstörten Schiffe eingesammelt und wiederverwendet werden.', 'Nachdem die Piraten durch die Entwicklung der mächtigen Kampfschiffe nicht mehr die unbewaffneten Transportkonvois überfallen konnten, entwickelten sie dieses Schiff, um mit ihm nach den grösseren Schlachten zwischen den kriegslustigen Imperien aufzutauchen, und ihren Lebensunterhalt aus den Überresten der zerstörten Schiffe zu gewinnen. Der Wert dieser Trümmersammler wurde schon bald erkannt, und ab dann führte niemand mehr Krieg, ohne sich nicht die Überreste der Schiffe zurück zu holen, um daraus neue Schiffe zu bauen.', 3000, 2000, 1000, 8000, 0, 0, 5, 33, 20, 20, 0, 15000, 0, 2, 600, 600, 200, 1, 1, 10, 'transport,collectdebris,position,attack,flight,support,alliance', '0.50', 0, 800, 1200, 20, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '14.000', 0, 0),
(11, 'RIGEL Dreadnought', 1, 'Dieses Schiff ist eine riesige fliegende Festung. ', 'Aus der Erfahrungen, die man mit den Hadar und den Pollux gewonnen hatte, wurde ein neues Superschiff kreiert, der Rigel Dreadnought. Optimierungen in der Herstellung und bei den Antrieben verliehen dem Schiff eine aussergewöhnliche Kampfkraft, Effizienz und Geschwindigkeit zu erstaunlich niedrigen Preisen. Zusätzlich erhöhte man die Transportkapazität, so dass die Rigel eigenständig praktisch aus dem Nichts heraus Raubzüge unternehmen können, ohne sich mit langsamen Transportern zu belasten. ', 3350000, 2975000, 1750000, 750000, 0, 0, 0, 280, 2350, 3400, 0, 600000, 0, 560, 4800, 620, 400, 1, 1, 5, 'transport,position,attack,flight,support,alliance', '0.50', 0, 1000000, 1350000, 1750000, 0, 1, 0, 1, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '8825.000', 0, 0),
(12, 'ELNATH Gassauger', 1, 'Dieses Schiff kann Wasserstoff aus der Atmosphäre von Gasplaneten einsaugen und daraus Tritium gewinnen.', 'Nachdem die Flotten immer grösser wurden, hatte man nicht mehr genug Tritium auf den Planeten zur Verfügung, um sie zu bewegen. Deshalb kam man auf die Idee, Wasserstoff von den unbewohnbaren Gasplaneten abzusaugen und es in Tritium umzuwandeln. Genau dafür wurde dieses Schiff konstruiert. Es wurde schnell klar, dass dieses Saugen äusserst rentabel ist und deshalb wurde der Gassauger soweit verbessert, dass heute eine grössere Flotte ohne ihn praktisch undenkbar ist.', 20000, 7500, 22200, 15000, 0, 0, 0, 55, 160, 130, 0, 9000, 0, 3, 600, 4300, 860, 1, 1, 12, 'transport,position,collectcrystal,collectfuel,flight,support', '0.50', 0, 650, 800, 0, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '64.700', 0, 0),
(13, 'ANDROMEDA Kampfstern', 1, 'Dieses Schiff ist das mächtigste Schiff der Galaxien.', 'Ein verrückter Wissenschaftler war von der Idee besessen, ein Kampfschiff zu bauen, welches so gross wie ein ganzer Trabant wäre. Er wurde so lange ausgelacht, bis er einen anderen Verrückten traf, der zufällig nebenberuflich Imperator war und der ihn unterstützte. Danach wurde Wissenschaftler allgemein als Genius bekannt, welcher die ultimative Waffe erschaffen hatte: den Andromeda Kampfstern. Seine Waffen und Schilder sind bis heute noch unübertroffen!\r\nDer einzige Nachteil dieses monströsen Kampfschiffes ist, dass es wegen seiner Masse lange Start- und Landezeiten hat, und eine zahlreiche Besatzung benötigt wird.', 20000000, 10000000, 12000000, 12000000, 0, 0, 0, 800, 8000, 4000, 0, 6000000, 0, 990, 10000, 3501, 2450, 1, 1, 7, 'transport,position,attack,flight,support,alliance', '0.50', 0, 8500000, 9000000, 9500000, 0, 1, 0, 1, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '54000.000', 0, 0),
(14, 'STARLIGHT Jäger', 1, 'Weiterentwicklung des ANTARES Jäger.', 'Parallel zu den Antares Jägern wurde der STARLIGHT Jäger entwickelt, welcher besser gepanzert war und auch die bessere Bewaffnung aufwies. Er nutzte auch einen neuartigen Antrieb, welcher aber noch nicht ganz serienreif war, da er andauernd ausfiel, und selten wie geplant lief. Nach einigen Untersuchungen fand man heraus, dass dies daran lag, dass beim Bau des Motors billiges Material verwendet wurde. Das stellte den viel gelobten Jäger in ein anderes Licht, aber andererseits erwies er sich in Raumschlachten als zuverlässiger Mitstreiter.', 4900, 3400, 2400, 2100, 0, 0, 0, 2, 5, 6, 0, 800, 0, 1, 975, 22, 20, 1, 1, 1, 'transport,position,attack,flight,support,alliance', '0.50', 0, 2100, 1100, 1900, 0, 1, 0, 1, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '12.800', 0, 0),
(15, 'ONEFIGHT Kampfdrohne', 1, 'Die Kampfdrohne ist sehr nützlich, um zuerst die gegnerische Flotte zu zerstören und danach mit Transportern die Rohstoffe abzusahnen. Wie der Name schon sagt, sind diese Drohnen Einweg-Sonden; sie werden bei einem Angriff immer verbraucht.', 'Es gab zwei Nachbarn, die lange Zeit friedlich miteinander lebten, aus dem einfachen Grund, dass die Flotten beider Kontrahenten etwa gleich gross war; und niemand den anderen ohne Verluste hätte angreifen können. Das änderte sich, als der erste die Kampfdrohnen entwickelte, ein billiges Schiff, welches aber eine äusserst grosse Kampfkraft aufweist, aber sobald es von einer Waffe getroffen wird, explodiert. Als die Flotte des einen zerstört war, hatte man der Invasion nichts mehr entgegenzusetzen, und jetzt leben sie als eine Rasse wieder friedlich miteinander.', 200, 700, 300, 300, 0, 0, 0, 1, 15, 2, 0, 300, 0, 0, 17000, 20, 30, 1, 1, 2, 'position,attack,flight,support,alliance', '0.50', 0, 0, 0, 650, 0, 1, 0, 1, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.500', 0, 0),
(16, 'Handelsschiff', 1, 'Ein Schiff der neutralen Handelsgilde.', 'Ein Schiff der neutralen Handelsgilde. Es wird benutzt um Einkäufe im Markt zu den Käufern zu transportieren.', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 100000, 0, 0, 10000, 60, 60, 0, 0, 0, 'market', '0.50', 0, 0, 0, 0, 0, 0, 0, 5, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.000', 0, 0),
(17, 'TERRANIA Zerstörer', 1, 'Kann ein Gebäude bombadieren.', 'Den Terranern war die Infrastruktur ihrer Feinde ein Dorn im Auge, also entwickelten sie diesen Zerstörer, um den Gegner durch die Zerstörung seiner Infrastruktur zur Kapitulation zu zwingen. Der Terrania Zerstörer ist ein gutes Schiff, obwohl der Angriff nicht immer erfolgreich ist, da sich herausstellte, dass das Zielen vom Orbit aus nicht die leichteste Übung ist. Dafür kann ein erfolgreicher Bombenabwurf enormen Schaden hervorrufen.', 85000, 40000, 50000, 40000, 0, 0, 0, 100, 651, 525, 0, 50000, 0, 25, 3000, 1950, 1919, 1, 1, 0, 'transport,position,attack,bombard,flight,support,alliance', '0.50', 0, 20000, 19000, 60000, 1, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '215.000', 0, 0),
(18, 'PROMETHEUS Recycler', 1, 'Grosser Recycler.', 'Dieser Recycler wurde nach Prometheus dem Titanen, welcher gegen Zeus rebellierte, und den Menschen das Feuer brachte, benannt, da mit den Rohstoffen, welche die Terraner mit seiner Hilfe gewinnen, deren Flotten gebaut werden. Früher brachte Prometheus ihnen mit dem Feuer die Möglichkeit, eine Kultur zu entwickeln. Heute bringen viele Tausend Prometheus den Menschen mit ihren Rohstoffen die Grundlage, ihre Kultur weiterzuentwickeln.', 10000, 8000, 4000, 25000, 0, 0, 0, 42, 50, 80, 0, 90000, 0, 3, 1500, 360, 182, 1, 1, 0, 'transport,collectdebris,position,attack,flight,support,alliance', '0.50', 0, 800, 1000, 5, 1, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '47.000', 0, 0),
(19, 'GAIA Transporter', 1, 'Bewohnertransporter der Terraner.', 'Als die Erde wegen Überbevölkerung einen vollständigen Kollaps erlitt, musste sie schleunigst evakuiert werden, und dafür wurde dieser Transporter entwickelt. Die Bewohner wurden zu Zehntausenden bei normalerweise untragbaren Bedingungen in diese Kolosse gesteckt und verfrachtet. Nach dieser Katastrophe etablierte dieser Transporter sich zu einem beliebten Fährschiff, mit welchem die Leute zu den Vergnügungsplaneten flogen, um sich vom täglichen Arbeitsstress zu erholen.', 3500, 1000, 750, 1250, 0, 0, 0, 55, 100, 80, 0, 3000, 10000, 1, 900, 720, 360, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 750, 300, 50, 1, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '6.500', 0, 0),
(20, 'ANDREIA Bomber', 1, 'Dieser Bomber ermöglicht Giftgasangriffe.', 'Die Andorianer sind sehr invasionsfreudig, aber sie wollten die invasierten Planeten nicht der ursprünglichen Bevölkerung überlassen, da sie sich selber genug schnell vermehren können. Deshalb fliegen sie vorher mit den Andreia Bombern über die Planeten, welche dann die Bevölkerung mit Giftgas auslöschen. Brutal, aber effizient.', 85000, 40000, 50000, 40000, 0, 0, 0, 93, 525, 650, 0, 15000, 0, 25, 1000, 1200, 1320, 1, 1, 0, 'transport,position,attack,gasattack,flight,support,alliance', '0.50', 0, 25000, 9000, 10000, 2, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '215.000', 0, 0),
(21, 'ATLAS Transporter', 1, 'Grosser Transporter', 'Auch die Andorianer entwickelten einen grösseren Transporter, da sie nicht wollten, dass andere Rassen mit ihren Transportern ihre Ressourcen herumschippern konnten und sie von diesen abhängig wären. Die Atlas entwickelten sich zu viel genutzten Transportern im Andorianischen Imperium. Sie erwiesen sich als viel nützlicher, als es sich die Regierungsmitglieder jemals erhofft hätten.', 30000, 6000, 1000, 12500, 0, 0, 0, 55, 100, 10, 0, 325000, 0, 1, 3100, 720, 300, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 300, 500, 30, 2, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '49.500', 0, 0),
(22, 'ZELOS Kreuzer', 1, 'Weiterentwicklung des HADAR Schlachtschiffes.', 'Nachdem sich der Bau von Hadar Schlachtschiffen durchgesetzt hatte, wollten die Andorianer dieses noch übertreffen. So wurde der Zelos Kreuzer entwickelt.\r\nDieses Schiff hat ungeheuer starke Schilde und ist sehr gut für die Verteidigung Planeten geeignet.', 121000, 44000, 50000, 45400, 0, 0, 0, 45, 160, 100, 0, 16000, 0, 10, 5500, 350, 320, 1, 1, 2, 'transport,position,attack,flight,support,alliance', '0.50', 0, 15000, 56500, 50000, 2, 1, 0, 4, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '260.400', 0, 0),
(23, 'CENTAURUS Spioschiff', 1, 'Kann von einem anderen Spieler eine Technologie klauen.', 'Die Centauri waren äusserst stolz darauf, dass sie die höchsten Technologien aller Völker besassen. Entsprechend gross war der Neid, als sie von einem andern Volk in einer von ihnen vernachlässigten Technologie übertrumpft wurden. Also erfanden sie dieses Spionageschiff, mit dessen Hilfe sie den anderen Völkern etwaige höher entwickelte Technologien klauen können.', 85000, 40000, 50000, 40000, 0, 0, 0, 7, 125, 250, 0, 7500, 0, 45, 600, 2905, 2359, 1, 1, 0, 'transport,position,attack,spy,spyattack,flight,support,alliance', '0.50', 0, 3250, 2250, 500, 8, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '215.000', 0, 0),
(24, 'PEGASUS Gassauger', 1, 'Grosser Gassauger der Centauri.', 'Um ihre teuren Forschungen zu betreiben, mussten die Centauri einen neuen Gassauger entwerfen, welcher eine grössere Kapazität hat, da die normalen Sauger die Bedürfnisse ihrer Forschungslabore nicht stillen konnten. Der Pegasus hat  eine wesentlich grössere Saugkapazität als herkömmliche Sauger, und durch seine hoch entwickelten Saugarme hat er die grössere Effizienz. Dies ist die Antwort der Centauri auf Tritiumknappheit.', 60000, 28000, 25000, 60000, 0, 0, 0, 38, 5, 8, 0, 40000, 0, 3, 1500, 950, 1450, 1, 1, 0, 'transport,position,attack,collectcrystal,collectfuel,flight,support,alliance', '0.50', 0, 800, 1000, 5, 8, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '173.000', 0, 0),
(25, 'EUROPA Fighter', 1, 'Mittelgrosses, für seine Verhältnisse jedoch sehr starkes Kriegsschiff der Centauri.', 'Die Centauri suchten ihren Vorteil in der Überlegenheit der Technologien, aber als die Rigel die Herrschaft über die Schlachtfelder übernahmen, entwickelten sie ihren eigenen Prototypen, den Europa Fighter. Heutzutage eines der stärksten Raumschiffe der mittleren Kampfklasse. Die Europas sind bei weitem nicht so stark wie Rigel, jedoch haben sie eine sehr kurze Startzeit, was sie sehr gefährlich macht.', 20000, 11000, 18000, 8000, 0, 0, 0, 85, 35, 25, 0, 22000, 0, 5, 6900, 280, 870, 1, 1, 0, 'transport,position,attack,flight,support,alliance', '0.50', 0, 6250, 12500, 7500, 8, 1, 0, 4, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '57.000', 0, 0),
(26, 'VORGONIA Bomber', 1, 'Kann einen Antraxangriff ausführen', 'Die Vorgonen verachten alle Rassen, die sich von ihrer eigenen unterscheiden, und entschieden sich deshalb dafür, dieses Übel mit allen Mitteln auszumerzen. Dafür bauten sie die Antraxbomber, um mit dem Kampfstoff Antrax die systematische Vernichtung feindlicher Völker zu beginnen. Zusätzlich wird die Nahrung auf dem Planeten vergiftet und dadurch unbrauchbar. Da viele Völker danach von Hungersnöten heimgesucht wurden, haben viele Bewohner Angst vor diesen Bombern und sind deshalb gegen Kriege mit Vorgonen.', 85000, 40000, 50000, 40000, 0, 0, 0, 80, 625, 550, 0, 13500, 0, 42, 1000, 1358, 1989, 1, 1, 0, 'transport,position,attack,antrax,flight,support,alliance', '0.50', 0, 28000, 8000, 8000, 7, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '215.000', 0, 0),
(27, 'PAN Transporter', 1, 'Bewohnertransporter der Vorgonen.', 'Nachdem die Vorgonen die feindliche Bevölkerung mit ihren Antraxbombern eliminiert hatten, mussten sie ihre eigene Bevölkerung auf diese Planeten transportieren. Dazu entwickelten sie die Pan Transporter.', 3250, 1000, 750, 1250, 0, 0, 0, 55, 100, 50, 0, 2000, 6750, 1, 1000, 720, 360, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 300, 500, 30, 7, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '6.250', 0, 0),
(28, 'IKAROS Jäger', 1, 'Schwebt in der Atmosphäre und hat somit keine Start- und Landezeit.', 'Die Vorgonen raubten alle ihre direkten Nachbarn mit Jägern aus, und bald einmal gab es erste Piloten, die gar nie mehr richtig auf dem Heimatplaneten landeten, sondern im Dauereinsatz waren. Dank ihren unerwarteten Raubzügen konnten sie viele Rohstoffe erbeuten. Diese Elitepiloten waren aber bald nicht mehr zufrieden mit den normalen Schiffen, also entwickelten sie ihre Jäger weiter, bis die Ikaros entstanden, die im Orbit des Planeten stationiert sind, so dass sie sofort und ohne Treibstoffverbrauch starten und landen können.', 4000, 2000, 1000, 2000, 0, 0, 0, 30, 0, 0, 0, 20000, 0, 2, 2400, 0, 0, 1, 1, 0, 'transport,position,attack,flight,support,alliance', '0.50', 0, 350, 2750, 1250, 7, 1, 0, 4, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '9.000', 0, 0),
(29, 'MARAUDER Transporter', 1, 'Grosser Transporter', 'Auch die Ferengi sahen sich genötigt, grosse Transporter zu entwickeln, wenn auch nicht aus denselben Gründen wie die anderen Rassen. Die Ferengi hatten wegen ihrer Titanproduktion alle ihre Lager längstens überfüllt und keinen Platz mehr, um grössere zu bauen. Also erschufen sie mit den Marauder Transportern eine Art fliegendes Lager, damit sie ihr Titan im Weltraum zwischenlagern konnten, wo es genug Platz dafür hat.', 33000, 6000, 1000, 4000, 0, 0, 0, 60, 100, 50, 0, 325000, 0, 1, 3700, 720, 333, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 300, 500, 30, 6, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '44.000', 0, 0),
(30, 'BELL Zerstörer', 1, 'Dieses Schiff hat einen riesigen Schutzschildgenerator an Bord.', 'Die Ferengi waren an einem Punkt angelagt, wo sie ihr Titan nicht mehr verbrauchen konnten. Es musste etwas erfunden werden, das mit möglichst viel Titan und wenig Zusatzstoffen gebaut werden konnte. Aus diesem Bedürfnis entstand der Bell Zerstörer.\r\nDieses Schiff hat einen riesigen Schutzschildgenerator an Bord, welcher einen Schutzschild erzeugt, der kaum überwunden werden kann. Aufgrund der Masse des Schiffes, dessen Antrieben und dem eingebauten Generator gehört der Bell Zerstörer nicht zu den schnellsten Schiffen der Galaxien, jedoch zu den Stärksten im Kampf.\r\nEin Nachteil des Bell Zerstörers ist sein immenser Tritiumverbrauch, der aus dem grossen Gewicht und der tiefen Fluggeschwindigkeit resultiert.', 60000, 5000, 21250, 5000, 0, 0, 0, 100, 150, 80, 0, 40000, 0, 30, 4125, 1230, 890, 1, 1, 0, 'transport,position,attack,flight,support,alliance', '0.50', 0, 6000, 35000, 1500, 6, 1, 0, 4, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '91.250', 0, 0),
(31, 'RUTICULUS Sammler', 1, 'Allessammler der Ferengi.', 'Den Ferengi war es zu umständlich, Trümmersammler, Asteroidensammler und Gassauger zu haben, also konzipierten sie kurzerhand ein Schiff, welches alles kann. Der Ruticulus Sammler ist deshalb ein äusserst praktisches Schiff, da jeder sie nach seinem Wunsch und entsprechend der jeweiligen Situation anwenden kann.', 20000, 10000, 15000, 30000, 0, 0, 0, 40, 20, 20, 0, 15000, 0, 1, 600, 640, 1800, 1, 1, 0, 'transport,collectdebris,position,attack,collectmetal,collectcrystal,collectfuel,flight,support,alliance', '0.50', 0, 800, 1000, 50, 6, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '75.000', 0, 0),
(32, 'RIGELIA Bomber', 1, 'Kann ein Gebäude für eine bestimmte Zeit mittels EMP-Technologie ausser Kraft setzten.', 'Der Rigelia Bomber kann mit seinen EMP-Angriffen die feindlichen Gebäude für eine kurze Zeit ausser Kraft setzen, was in einem Krieg schwerwiegende Folgen haben kann. Obwohl der Bomber sehr teuer ist, und von seiner Kampfkraft her gesehen kaum genutzt werden sollte, sind viele Generäle der Meinung, dass seine Bomben genug effektiv sind, so dass man diese Möglichkeit in einem Krieg immer einsetzen sollte.', 85000, 40000, 50000, 40000, 0, 0, 0, 55, 325, 250, 0, 15000, 0, 41, 1000, 1520, 2001, 1, 1, 0, 'transport,position,attack,emp,flight,support,alliance', '0.50', 0, 25000, 6500, 12500, 3, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '215.000', 0, 0),
(33, 'EOS Transporter', 1, 'Grosser Transporter', 'Als die Cardassianer und die Minbari die grossen Transporter entwickelt hatten, konnten die Rigelianer dem nicht nachstehen und fertigten sofort ihre eigene Version eines grossen Transporters an. Vom Prinzip her ist es genau dasselbe Schiff wie der Saiph Transporter der Minbari. Die Rigelianer passten einfach das Design und die Steuergeräte ihren Bedürfnisse an.', 25000, 7000, 1000, 3000, 0, 0, 0, 55, 100, 50, 0, 325000, 0, 1, 3800, 252, 435, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 300, 500, 30, 3, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '36.000', 0, 0),
(34, 'HELIOS Drohne', 1, 'Weiterentwicklung der Onefight Kampfdrohne. Rassenschiff der Rigelianer.', 'Die Rigelianer waren von den Onefight Kampfdrohnen begeistert. Sie steckten deshalb ihre ganzen Forschungsmittel in deren Weiterentwicklung. So entstand die Helios Drohne: Diese Drohne ist noch effizienter als die Onefight und kann in genügend grosser Anzahl den Gegner empfindlich treffen. Ausserdem können die Helios im Gegensatz zu den Onefights einen Kampf auch überleben.\r\nDie Helios sind überall wegen ihrer Kampfkraft gefürchtet, und da sie auf dem Standardantrieb der Drohnen aufbauen, haben sie auch eine hohe Geschwindigkeit, weshalb man sich nie vor einem Angriff sicher fühlen kann.', 2500, 6200, 2000, 2300, 0, 0, 0, 1, 5, 5, 0, 500, 0, 0, 15000, 40, 60, 1, 1, 0, 'position,attack,flight,support,alliance', '0.50', 0, 1, 0, 6000, 3, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '13.000', 0, 0),
(36, 'CARDASSIA Mutterschiff', 1, 'Heilt während dem Kampf eine gewisse Anzahl Schild- und Strukturpunkte.', 'Nachdem die Cardassianer mit ihren Nilams die ganze Galaxie in Angst und Schrecken versetzt hatten, schlossen sich alle anderen Rassen zu einem Bund zusammen, um die Cardassianer zu vernichten. Trotzdem hatten sie nicht mit dem neuen Geniestreich der Cardassianer gerechnet: Den Mutterschiffen. Mit diesem hoch entwickelten Raumschiff können die Cardassianer ihre Flotte während dem Kampf reparieren, um so Verluste auszugleichen. Nur dank der Hilfe dieses Schiffes konnten die Cardassianer den immerwährenden Angriffen standhalten.', 70000, 27500, 27500, 35000, 0, 0, 0, 60, 70, 10, 0, 1500, 0, 5, 1800, 726, 333, 1, 1, 0, 'transport,position,attack,flight,support,alliance', '0.50', 10000, 7000, 3000, 125, 9, 1, 0, 4, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '160.000', 0, 0),
(37, 'DEMETER Transporter', 1, 'Grosser Transporter', 'Die Cardassianer waren allgemein wegen ihren vielen Rohstoffen beneidet, vor allem wegen ihrer Nahrung, die sie wie keine anderen herstellen können. Um sich vor Übergriffen zu schützen und um ihre Gegner im Unklaren über ihre wahren Rohstoffmengen zu lassen, entwickelten sie diese Transporter, welche mit den Rohstoffen irgendwo in der Ewigkeit des Alls herumfliegen, damit sie nicht gefunden werden. Die Cardassianer sind die einzigen, deren Organisation solch perfekte Nachschublinien zustande bringt.', 23000, 8300, 1200, 1500, 0, 0, 0, 10, 100, 10, 0, 350000, 0, 1, 3500, 585, 395, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 300, 500, 30, 9, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '34.000', 0, 0),
(38, 'NILAM Fighter', 1, 'Ein starkes Kampfschiff aus der Mittelschweren Klasse, entwickelt von den Cardassianern.', 'Den Cardassianern waren Starlights von Anfang an zu langsam und Drohnen zu schwach. So erfanden sie die Nilam, welche sie zu gefürchteten Jägern entwickelten, da sie spezielle Antriebe haben, die ausserordentlich schnell sind. Die Cardassianer benutzen die Jäger vor allem, um ihre Militärdiktatur aufrechtzuerhalten. Sie wollen schnell reagieren und überall bereitstehen können. Dafür eignen sich die Nilams am besten. Sie kommen aus dem Nichts und verschwinden sofort wieder, nachdem sie die Schlacht gewonnen haben.', 7150, 4000, 2000, 3000, 0, 0, 0, 40, 100, 100, 0, 5000, 0, 4, 6250, 456, 325, 1, 1, 0, 'transport,position,attack,flight,support,alliance', '0.50', 0, 2900, 2000, 2500, 9, 1, 0, 4, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '16.150', 0, 0),
(40, 'SERA Kreuzer', 1, 'Dieses Schiff kann dem Angegriffen eine Flotte vortäuschen, die gar nicht vorhanden ist.', 'Den räuberischen Orionern gefiel es nicht, dass sich die Feinde auf ihre Angriffe vorbereiten konnten. Die Lösung fanden sie in der Konstruktion vom Sera Kreuzer. Dieser hat die Fähigkeit, Hologramme anderer Schiffe zu erstellen und dem Gegner damit eine grosse angreifende Flotte vortäuschen. Ziel ist es, schwächere Flotten zu vertreiben, so dass die Rohstoffe ungeschützt rumliegen.', 15500, 10500, 6000, 5500, 0, 0, 0, 30, 65, 45, 0, 6000, 0, 2, 8500, 640, 156, 1, 1, 0, 'transport,position,attack,fakeattack,flight,support,alliance', '0.50', 0, 500, 5500, 1500, 4, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '37.500', 0, 0),
(41, 'HYPOS Drohne', 1, 'Kann ein Trümmerfeld beim Gegner erstellen, ohne dass dieser etwas merkt.', 'Die Orioner wollten nicht mehr angreifen müssen, um ein Trümmerfeld zu erstellen. Deshalb schicken sie diese Drohnen vor grossen Schlachten los, um beim Gegner ein klitzekleines Trümmerfeld zu erstellen. Damit konnte den Navigationscomputern der Sammler ein gültiges Ziel zugewiesen werden. Zu diesem Zweck muss sich die Drohne beim Gegner in die Luft sprengen, was selten für mehr als eine Sternschnuppe wahrgenommen wird.', 500, 300, 50, 200, 0, 0, 0, 6, 10, 10, 0, 2000, 0, 0, 17000, 1, 1, 1, 1, 0, 'position,attack,createdebris,flight,support,alliance', '0.50', 0, 10, 0, 1, 4, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.050', 0, 0),
(42, 'MINBARI Jäger', 1, 'Wenn er alleine in oder in einem Flottenverband aus lauter Minbari Jägern fliegt, ist er für die gegnerische Flottenkontrolle nicht sichtbar.', 'Die Minbari sahen es gar nicht gerne, als man ihre Flotten schon im Anflug entdeckte und eine entsprechende Verteidigung bereitstellte. Deshalb liessen sie die besten Köpfe der Galaxie zusammenkommen, um dieses Schiff zu entwickeln, welches durch seine perfekte Tarnung erst im allerletzten Moment entdeckt werden kann. Und dann ist es bereits zu spät...', 20500, 13500, 13500, 10000, 0, 0, 0, 20, 120, 130, 0, 15000, 0, 10, 6500, 1189, 125, 1, 1, 0, 'transport,position,attack,stealthattack,flight,support,alliance', '0.50', 0, 13000, 4500, 5000, 5, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '57.500', 0, 0),
(43, 'SAIPH Transporter', 1, 'Grosser Transporter der Minbari.', 'Die Minbari entwickelten diese grossen Transporter, um ihren steigenden Rohstofftransport-Bedürfnissen nachzukommen. Die Rohstoffmengen stiegen immer weiter an, und irgendwann war auch die Kapazität der Unukalhai ausgeschöpft. Nun musste eine neue Lösung gefunden werden, und die Ingenieure der Minbari entwickelten diesen grossen Transporter.', 26000, 6000, 3000, 9000, 0, 0, 0, 35, 100, 50, 0, 350000, 0, 1, 5200, 721, 326, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 300, 500, 30, 5, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '44.000', 0, 0),
(44, 'WEZEA Fighter', 1, 'Kampfschiff der Minbari, welches auch Gassaugen kann.', 'Die Minbari liebten es seit eh und je, über die Gasplaneten zu fliegen, da sie von den unbeschreiblich schönen Polarlichtern fasziniert sind, welche man dort beobachten kann. Deshalb gingen sie so weit, sogar ihre Jäger so zu konstruieren, dass sie zu Gasplaneten fliegen und auch Gas saugen können.\r\nEs ist ihnen sogar gelungen, den neuartigen Solarantrieb zu integrieren. Damit verbraucht der WEZEA Fighter nur Tritium für den Start und die Landung.', 14000, 7000, 9000, 8000, 0, 0, 0, 0, 700, 400, 0, 12500, 0, 3, 5100, 1750, 540, 1, 1, 0, 'transport,position,attack,collectfuel,flight,support,alliance', '0.50', 0, 7250, 3500, 5800, 5, 1, 0, 4, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '38.000', 0, 0),
(45, 'ORION Fighter', 1, 'Kampfschiff der Mittelschweren Klasse, entworfen von den Orionern. Kann bis zu 75% der Rohstoffe von einem fremden Planeten mitnehmen. ', 'Den Orionern war die Menge, welche sie normalerweise mit ihren Schiffen von gegnerischen Planeten erbeuten konnten, viel zu wenig. Der Orion Fighter ist ihre Antwort auf dieses Problem. Ein starkes Raumschiff, welches so konzipiert ist, dass es 50% mehr Rohstoffe als Beute mitnehmen kann als alle anderen Schiffe. Zusätzlich hat der Orion schlagkräftige Waffen, was den Fighter zum optimalen Schiff für Piraterie macht.', 35000, 10500, 12500, 5500, 0, 0, 0, 65, 100, 90, 0, 17500, 0, 4, 8000, 451, 352, 1, 1, 0, 'transport,position,attack,flight,support,alliance', '0.75', 0, 7500, 7000, 14000, 4, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '63.500', 0, 0),
(46, 'FORNAX Asteroidensammler', 1, 'Kann Asteroidenfelder anfliegen und dort Rohstoffe sammeln.', 'Da die Gassauger grossen Erfolg hatten, dachte man, dass man das auch mit Asteroidenfeldern versuchen könne, so dass man auch die anderen Rohstoffe aus dem Weltraum gewinnen konnte. Leider war die praktische Umsetzung schwieriger, da eine sichere  Navigation innerhalb der Asteroidenfelder sich als praktisch unmöglich erwies. Deshalb ist dieses Konzept fehlgeschlagen, da die Sammler schneller von Asteroiden getroffen werden, als dass sie genug Rohstoffe holen können, um ihre Herstellungskosten zurückzugewinnen.', 15000, 5000, 25000, 9000, 0, 0, 0, 65, 100, 120, 0, 45000, 0, 8, 600, 4350, 1050, 1, 1, 11, 'transport,position,attack,collectmetal,flight,support,alliance', '0.50', 0, 250, 1000, 50, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '54.000', 0, 0),
(47, 'TITAN Transporter', 1, 'Dies ist ein relativ billiger und sehr schneller, grosser Transporter.', 'Dies ist ein relativ billiger und sehr schneller, grosser Transporter, allerdings zeigt sich der Preis in seiner Qualität. Er ist sehr schwach. Dieser Transporter setzt auf den Solarantrieb, wodurch er durch ein Sonnensegel unglaublich schnell ohne Treibstoffverbrauch fliegen kann.', 35000, 7000, 5000, 10000, 0, 0, 0, 0, 1000, 600, 0, 150000, 0, 45, 4100, 550, 460, 1, 1, 5, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 50, 20, 1, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '57.000', 0, 0),
(50, 'ASTERIO Sammler', 1, 'Das effizienteste Asteroidensammlerschiff in ganz Andromeda.', 'Auch wenn die Fornax Asteroidensammler mehr oder weniger erfolglos waren, hiess das noch lange nicht, dass das Konzept unbrauchbar war. Es wurde weiterentwickelt und so entstand der Asterio Sammler, welcher zwar eine kleinere Ladefläche als der Fornax aufweist, dafür aber wesentlich schneller unterwegs\r\nist. Bis jetzt ist es das effizienteste Asteroidensammelschiff in ganz Andromeda.\r\nDieses Schiff ist auch dazu geeignet, um Trümmerfelder anzusteuern und diese zu recyclen.', 3200, 1200, 2500, 2000, 0, 0, 0, 4, 1, 1, 0, 11000, 0, 1, 4500, 3230, 560, 1, 1, 3, 'transport,collectdebris,position,attack,collectmetal,flight,support,alliance', '0.50', 0, 50, 2, 0, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '8.900', 0, 0),
(51, 'HAARP Spionagesonde', 1, 'Diese Sonde ist die Weiterentwicklung der ZAVIJAH Spionagesonde.', 'Diese Sonde ist die Weiterentwicklung der ZAVIJAH Spionagesonde. Sie ist enorm schnell und gut geeignet zum Ausräumen verteidigungsloser Planeten sowie zum Ausspionieren weit entfernter Galaxien. ', 1000, 1000, 1000, 500, 0, 0, 0, 1, 1, 1, 0, 800, 0, 0, 60000, 5, 4, 1, 1, 4, 'position,attack,spy,flight,support,alliance', '0.50', 0, 0, 1, 0, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '3.500', 0, 0),
(52, 'AURORA Sonde', 1, 'Diese Sonde wurde entwickelt, da viele schnelle Schiffe nicht genug Treibstoff für lange Strecken mitnehmen konnten.', 'Diese Sonde wurde entwickelt, da viele schnelle Schiffe nicht genug Treibstoff für lange Strecken mitnehmen konnten. Deshalb hat diese sehr schwache und teure Sonde einen riesigen Laderaum, in dem sie den Treibstoff für die mitfliegenden Schiffe bereit halten kann. \r\n\r\nSie wird bei einem Kampf sehr schnell zerstört, da sie praktisch nur aus dünnbeschichteten Tanks & den notwendigen Antrieb besteht.', 20000, 18000, 10000, 9000, 0, 0, 0, 25, 10, 5, 0, 35000, 0, 0, 15000, 20, 15, 1, 1, 2, 'transport,position,attack,flight,support,alliance', '0.50', 0, 1, 1, 1, 0, 1, 0, 2, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '57.000', 0, 0),
(53, 'IMPERIALER Kreuzer', 1, 'Dies ist eines der grössten Schiffe in Andromeda.', 'Dies ist eines der grössten Schiffe in Andromeda. Es ist enorm stark gepanzert, hat allerdings einen relativ schwachen Schild. Seine Waffen sind aber nicht zu verachten. Es ist das grösste Schiff mit einem Sonnensegel zur Antriebsunterstützung.', 750000, 600000, 415000, 365000, 0, 0, 0, 45, 790, 560, 0, 230000, 0, 35, 5800, 860, 420, 1, 1, 6, 'transport,position,attack,flight,support,alliance', '0.50', 0, 505000, 85000, 335000, 0, 1, 0, 1, 1, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2130.000', 0, 0),
(54, 'Alien-Jäger', 1, 'Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.', 'Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.', 99999999, 99999999, 99999999, 99999999, 99999999, 0, 0, 1, 0, 0, 0, 1000, 0, 1, 5000, 0, 0, 0, 0, 0, 'flight', '0.50', 0, 500, 700, 50, 0, 1, 0, 5, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '499999.995', 0, 0),
(55, 'Alien-Kampschiff', 1, 'Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.', 'Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.', 99999999, 99999999, 99999999, 99999999, 99999999, 0, 0, 1, 0, 0, 0, 5000, 0, 1, 5000, 0, 0, 0, 0, 0, 'flight', '0.50', 0, 5000, 7000, 500, 0, 1, 0, 5, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '499999.995', 0, 0),
(56, 'Alien-Mutterschiff', 1, 'Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.', 'Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.', 99999999, 99999999, 99999999, 99999999, 99999999, 0, 0, 1, 0, 0, 0, 10000, 0, 1, 5000, 0, 0, 0, 0, 0, 'flight', '0.50', 0, 50000, 70000, 5000, 0, 1, 0, 5, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '499999.995', 0, 0),
(57, 'ANDROMEDA Mysticum', 1, 'Ein einmaliges Schiff mit speziellen Fähigkeiten.', 'Ein einmaliges Schiff mit speziellen Fähigkeiten.', 58000, 67000, 43600, 37500, 0, 0, 0, 75, 400, 400, 0, 50000, 0, 10, 5000, 950, 1350, 1, 1, 0, 'position,flight', '0.50', 0, 50000, 50000, 0, 0, 1, 0, 3, 0, 1, 1, 0, 350, '2.00', '0.03', '0.03', '0.03', '0.00', '0.03', '0.00', '0.07', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '206.100', 0, 0),
(59, 'MINBARI Mysticum', 1, 'Das Spezialschiff für die Minbari.', 'Das Spezialschiff für die Minbari.', 700000, 550000, 390000, 480000, 120000, 0, 0, 63, 850, 360, 0, 65000, 0, 33, 4100, 1300, 710, 1, 1, 0, 'position,flight', '0.50', 0, 70000, 70000, 0, 5, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.01', '0.01', '0.02', '0.00', '0.00', '0.00', '0.05', '0.05', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2240.000', 0, 0),
(60, 'ANDORIA Mysticum', 1, 'Das Spezialschiff für die Andorianer.', 'Das Spezialschiff für die Andorianer.', 670000, 500000, 350000, 480000, 0, 0, 0, 60, 600, 520, 0, 68000, 0, 30, 5300, 600, 1020, 1, 1, 0, 'position,flight', '0.50', 0, 110000, 86000, 0, 2, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.02', '0.02', '0.02', '0.00', '0.00', '0.10', '0.00', '0.00', '0.14', '0.00', '0.00', '0.00', '0.00', '0.00', '2000.000', 0, 0),
(61, 'CARDASSIA Mysticum', 1, 'Das Spezialschiff für die Cardassianer.', 'Das Spezialschiff für die Cardassianer.', 750000, 530000, 320000, 450000, 250000, 0, 0, 75, 450, 300, 0, 65000, 0, 55, 5300, 840, 1150, 1, 1, 0, 'position,flight', '0.50', 0, 1500000, 50000, 0, 9, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.01', '0.01', '0.01', '0.07', '0.00', '0.00', '0.05', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2300.000', 0, 0),
(62, 'CENTAURI Mysticum', 1, 'Das Spezialschiff für die Centauri.', 'Das Spezialschiff für die Centauri.', 850000, 630000, 360000, 450000, 60000, 0, 0, 65, 300, 360, 0, 120000, 0, 45, 3500, 1080, 780, 1, 1, 0, 'position,flight', '0.50', 0, 65000, 25000, 0, 8, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.02', '0.01', '0.01', '0.00', '0.00', '0.00', '0.05', '0.00', '0.00', '0.03', '0.00', '0.00', '0.00', '0.00', '2350.000', 0, 0),
(63, 'FERENGI Mysticum', 1, 'Das Spezialschiff für die Ferengi.', 'Das Spezialschiff für die Ferengi.', 930000, 400000, 360000, 520000, 0, 0, 0, 78, 600, 450, 0, 120000, 0, 50, 5000, 870, 980, 1, 1, 0, 'position,flight', '0.50', 0, 200000, 0, 0, 6, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.01', '0.03', '0.03', '0.00', '0.05', '0.00', '0.05', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2210.000', 0, 0),
(64, 'ORION Mysticum', 1, 'Das Spezialschiff für den Orioner.', 'Das Spezialschiff für den Orioner.', 500000, 450000, 680000, 460000, 50000, 0, 0, 80, 500, 400, 0, 175000, 0, 60, 6000, 850, 1320, 1, 1, 0, 'position,flight', '0.50', 0, 80000, 110000, 0, 4, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.02', '0.01', '0.01', '0.00', '0.00', '0.20', '0.05', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2140.000', 0, 0),
(65, 'RIGELIA Mysticum', 1, 'Das Spezialschiff für die Rigelianer.', 'Das Spezialschiff für die Rigelianer.', 450000, 760000, 390000, 330000, 100000, 0, 0, 65, 720, 250, 0, 95000, 0, 52, 4500, 750, 1120, 1, 1, 0, 'position,flight', '0.50', 0, 75000, 120000, 0, 3, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.02', '0.01', '0.01', '0.00', '0.00', '0.00', '0.05', '0.00', '0.00', '0.00', '0.00', '0.00', '0.12', '0.00', '2030.000', 0, 0),
(66, 'TERRANIA Mysticum', 1, 'Das Spezialschiff für die Terraner.', 'Das Spezialschiff für die Terraner.', 650000, 420000, 350000, 530000, 100000, 0, 0, 75, 650, 760, 0, 120000, 0, 56, 3800, 1050, 860, 1, 1, 0, 'position,flight', '0.50', 0, 115000, 85000, 0, 1, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.02', '0.02', '0.01', '0.00', '0.00', '0.00', '0.05', '0.00', '0.00', '0.00', '0.10', '0.00', '0.00', '0.00', '2050.000', 0, 0),
(67, 'VORGONIA Mysticum', 1, 'Das Spezialschiff für die Voronen.', 'Das Spezialschiff für die Voronen.', 550000, 550000, 550000, 550000, 200000, 0, 0, 80, 850, 490, 0, 130000, 0, 60, 5200, 1230, 750, 1, 1, 0, 'position,flight', '0.50', 0, 100000, 100000, 0, 7, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.01', '0.02', '0.02', '0.00', '0.00', '0.00', '0.05', '0.00', '0.00', '0.00', '0.00', '0.10', '0.00', '0.02', '2400.000', 0, 0);
INSERT INTO `ships` (`ship_id`, `ship_name`, `ship_type_id`, `ship_shortcomment`, `ship_longcomment`, `ship_costs_metal`, `ship_costs_crystal`, `ship_costs_fuel`, `ship_costs_plastic`, `ship_costs_food`, `ship_costs_power`, `ship_power_use`, `ship_fuel_use`, `ship_fuel_use_launch`, `ship_fuel_use_landing`, `ship_prod_power`, `ship_capacity`, `ship_people_capacity`, `ship_pilots`, `ship_speed`, `ship_time2start`, `ship_time2land`, `ship_show`, `ship_buildable`, `ship_order`, `ship_actions`, `ship_bounty_bonus`, `ship_heal`, `ship_structure`, `ship_shield`, `ship_weapon`, `ship_race_id`, `ship_launchable`, `ship_fieldsprovide`, `ship_cat_id`, `ship_fakeable`, `special_ship`, `ship_max_count`, `special_ship_max_level`, `special_ship_need_exp`, `special_ship_exp_factor`, `special_ship_bonus_weapon`, `special_ship_bonus_structure`, `special_ship_bonus_shield`, `special_ship_bonus_heal`, `special_ship_bonus_capacity`, `special_ship_bonus_speed`, `special_ship_bonus_pilots`, `special_ship_bonus_tarn`, `special_ship_bonus_antrax`, `special_ship_bonus_forsteal`, `special_ship_bonus_build_destroy`, `special_ship_bonus_antrax_food`, `special_ship_bonus_deactivade`, `special_ship_bonus_readiness`, `ship_points`, `ship_alliance_shipyard_level`, `ship_alliance_costs`) VALUES
(68, 'ENERGIJA Solarsatellit', 1, 'Ein Satellit, der im Orbit schwebt und durch Solarpanels Energie gewinnt, welche dann auf dem Planeten verwendet werden kann.', 'Da einige (neu entwickelte) Gebäude enorme Energiemengen verschlingen, wurde der Solarsatellit entwickelt. Diese Sonde wird im Orbit stationiert und erzeugt Energie mit Hilfe der Sonne. Die Energieausbeute pro Solarsatellit ist jedoch abhängig von der jeweiligen Planetentemperatur und der jeweiligen Entfernung zur Sonne.\r\nDie Sonden werden bei einem feindlichen Angriff abgeschossen.', 300, 1500, 100, 100, 0, 0, 0, 0, 0, 0, 300, 0, 0, 0, 0, 0, 0, 1, 1, 13, 'flight', '0.50', 0, 100, 50, 0, 0, 0, 0, 2, 0, 0, 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2.000', 0, 0),
(69, 'TEREBELLUM Analysator', 1, 'Diese kleine Sonde wurde dafür geschaffen, um Staub- und Gasvorkommen im All zu analysieren und festzustellen, ob sich deren Abbau lohnt.', 'Diese kleine Sonde wurde dafür geschaffen, um Staub- und Gasvorkommen im All zu analysieren und festzustellen, ob sich deren Abbau lohnt.', 2000, 4500, 3000, 3000, 0, 0, 0, 2, 50, 2, 0, 500, 0, 0, 70000, 10, 1, 1, 1, 1, 'position,analyze,flight,support', '0.50', 0, 100, 200, 0, 0, 1, 0, 2, 0, 0, 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '12.500', 0, 0),
(70, 'LORIAL Transportschiff', 1, 'Dieser Transporter der Serrakin kann extrem viel transportieren und verbraucht wenig Sprit, ist dafür aber auch ziemlich langsam.', 'Dieser Transporter der Serrakin kann extrem viel transportieren und verbraucht wenig Sprit, ist dafür aber auch ziemlich langsam.', 17000, 11000, 15000, 7000, 0, 0, 0, 10, 50, 10, 0, 475000, 0, 1, 800, 600, 500, 1, 1, 0, 'transport,fetch,position,attack,flight,support,alliance', '0.50', 0, 200, 500, 50, 10, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '50.000', 0, 0),
(71, 'AURIGA Explorer', 1, 'Dient zur Erkundung der unbekannten Weiten der Galaxie.', 'Dient zur Erkundung der unbekannten Weiten der Galaxie.', 1000, 800, 0, 0, 0, 0, 0, 1, 5, 0, 0, 100, 0, 0, 1500, 10, 0, 1, 1, 6, 'position,explore,flight,support', '0.50', 0, 50, 20, 0, 0, 1, 0, 2, 0, 0, 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.800', 0, 0),
(72, 'SERRAKIN Mysticum', 1, 'Das Spezialschiff für die Serrakin', 'Das Spezialschiff für die Serrakin', 800000, 580000, 350000, 450000, 150000, 0, 0, 60, 850, 500, 0, 85000, 0, 45, 5300, 800, 1000, 1, 1, 0, 'position', '0.50', 0, 85000, 95000, 0, 10, 1, 0, 3, 0, 1, 1, 0, 180, '1.70', '0.00', '0.03', '0.00', '0.05', '0.05', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2330.000', 0, 0),
(73, 'SUPRANALIS Jäger', 1, 'Weiterentwicklung des ANTARES Jäger.', 'Parallel zu den Antares Jägern wurde der STARLIGHT Jäger entwickelt, welcher besser gepanzert war und auch die bessere Bewaffnung aufwies. Er nutzte auch einen neuartigen Antrieb, welcher aber noch nicht ganz serienreif war, da er andauernd ausfiel, und selten wie geplant lief. Nach einigen Untersuchungen fand man heraus, dass dies daran lag, dass beim Bau des Motors billiges Material verwendet wurde. Das stellte den viel gelobten Jäger in ein anderes Licht, aber andererseits erwies er sich in Raumschlachten als zuverlässiger Mitstreiter.', 24500, 17000, 10500, 12000, 0, 0, 0, 2, 5, 6, 0, 800, 0, 1, 9750, 22, 20, 0, 0, 2, 'transport,position,attack,flight,support,alliance', '0.50', 0, 21000, 11000, 19000, 0, 1, 0, 6, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '64.000', 1, 3000),
(74, 'SUPRANALIS Bomber', 1, 'Dieses Raumschiff ist sehr effektiv gegen gegnerische Verteidigungsanlagen.', 'Trotz allen Erfolgen, die die Hadar Schlachtschiffe bei der Zerstörung gegnerischer Flotten und Verteidigung erzielten, war man damit noch nicht zufrieden. Deshalb konstruierte man ein neues, bis an die Zähne bewaffnetes Schiff, den Pollux Bomber. Nachdem man das Schiff mit Waffen beladen hatte, erwies es sich, dass deshalb die Angriffsgeschwindigkeit eingeschränkt wurde. Wegen diesem Nachteil konnte der Bomber sich in grossen Flotten nicht etablieren, er ist aber trotzdem in allem eine nicht zu unterschätzende Waffe, welche grosse Zerstörung anrichten kann.', 48500, 105000, 42500, 57500, 0, 0, 0, 550, 800, 700, 0, 2000, 0, 2, 2400, 300, 60, 0, 0, 5, 'transport,position,attack,flight,support,alliance', '0.50', 0, 26000, 5000, 180000, 0, 1, 0, 6, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '253.500', 3, 10000),
(76, 'SUPRANALIS Dreadnought', 1, 'Dieses Schiff ist eine riesige fliegende Festung. ', 'Aus der Erfahrungen, die man mit den Hadar und den Pollux gewonnen hatte, wurde ein neues Superschiff kreiert, der Rigel Dreadnought. Optimierungen in der Herstellung und bei den Antrieben verliehen dem Schiff eine aussergewöhnliche Kampfkraft, Effizienz und Geschwindigkeit zu erstaunlich niedrigen Preisen. Zusätzlich erhöhte man die Transportkapazität, so dass die Rigel eigenständig praktisch aus dem Nichts heraus Raubzüge unternehmen können, ohne sich mit langsamen Transportern zu belasten. ', 16750000, 14875000, 3750000, 8750000, 0, 0, 0, 2800, 23500, 34000, 0, 600000, 0, 560, 9600, 310, 200, 0, 0, 9, 'transport,position,attack,flight,support,alliance', '0.50', 0, 10000000, 13500000, 17500000, 0, 1, 0, 6, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '44125.000', 5, 17000),
(77, 'SUPRANALIS Kampfstern', 1, 'Dieses Schiff ist das mächtigste Schiff der Galaxien.', 'Ein verrückter Wissenschaftler war von der Idee besessen, ein Kampfschiff zu bauen, welches so gross wie ein ganzer Trabant wäre. Er wurde so lange ausgelacht, bis er einen anderen Verrückten traf, der zufällig nebenberuflich Imperator war und der ihn unterstützte. Danach wurde dieser Wissenschaftler allgemein als Genius bekannt, welcher die ultimative Waffe erschaffen hatte: den Andromeda Kampfstern. Seine Waffen und Schilder sind bis heute noch unübertroffen!\r\nDer einzige Nachteil dieses monströsen Kampfschiffes ist nur, dass es wegen seiner Masse lange Start- und Landezeiten hat, und eine zahlreiche Besatzung benötigt wird.', 100000000, 50000000, 60000000, 60000000, 0, 0, 0, 8000, 80000, 40000, 0, 6000000, 0, 990, 20000, 1750, 1250, 0, 0, 13, 'transport,position,attack,flight,support,alliance', '0.50', 0, 85000000, 90000000, 95000000, 0, 1, 0, 6, 0, 0, 0, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '270000.000', 7, 27000),
(78, 'SUPRANALIS Ultra', 1, 'Dieses Schiff ist das mächtigste Schiff der Galaxien (nun aber wirklich ^^)', 'Der Andromeda Kampfstern galt lange als DAS Kampfschiff schlechthin und nicht wenige behaupten, dass es nicht möglich sei, seine Grösse und Stärke zu übetreffen, doch genau dieses Ziel hatten diverse Imperatoren einer mächtigen Allianz Namens \\"Supranalis Ultra\\".\r\nNach vielen Jahren, unzähligen Arbeitsstunden und diversen Todesopfern war der Prototyp dieses Superschiffs fertig.\r\nEtwas noch nie Dagewesenes wurde erschaffen um die Kontrolle eines ganzen Universums an sich zu reissen...', 1000000000, 500000000, 600000000, 600000000, 0, 0, 0, 10000, 100000, 100000, 0, 50000000, 0, 100000, 20000, 5000, 3000, 0, 0, 14, 'transport,position,attack,flight,support,alliance', '0.50', 0, 10000000, 10000000, 10000000, 0, 1, 0, 6, 0, 0, 1, 0, 0, '1.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2700000.000', 10, 150000),
(79, 'SCORPIUS ZIBAL Trägerschiff', 1, 'Transportiert mobile Verteidigungsanlagen.', 'Transportiert mobile Verteidigungsanlagen.', 3900, 3100, 2100, 1500, 0, 0, 0, 20, 10, 10, 0, 1000, 0, 0, 10000, 60, 60, 1, 0, 14, 'position', '0.50', 0, 1000, 50, 0, 10, 1, 0, 2, 0, 0, 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '10.600', 0, 0),
(80, 'SCORPIUS SPICA Trägerschiff', 1, 'Transportiert mobile Verteidigungsanlagen.', 'Transportiert mobile Verteidigungsanlagen.', 800, 475, 0, 425, 0, 0, 0, 20, 10, 10, 0, 1000, 0, 0, 10000, 60, 60, 1, 0, 14, 'position', '0.50', 0, 1000, 50, 0, 10, 1, 0, 2, 0, 0, 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '1.700', 0, 0),
(81, 'SCORPIUS POLARIS Trägerschiff', 1, 'Transportiert mobile Verteidigungsanlagen.', 'Transportiert mobile Verteidigungsanlagen.', 1000, 700, 300, 500, 0, 0, 0, 20, 10, 10, 0, 1000, 0, 0, 10000, 60, 60, 1, 0, 14, 'position', '0.50', 0, 1000, 50, 0, 10, 1, 0, 2, 0, 0, 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '2.500', 0, 0),
(82, 'SCORPIUS PHOENIX Trägerschiff', 1, 'Transportiert mobile Verteidigungsanlagen.', 'Transportiert mobile Verteidigungsanlagen.', 6500, 3500, 3000, 1900, 0, 0, 0, 20, 10, 10, 0, 1000, 0, 0, 10000, 60, 60, 1, 0, 14, 'position', '0.50', 0, 1000, 50, 0, 10, 1, 0, 2, 0, 0, 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '14.900', 0, 0),
(83, 'SERPENS Kommandoschiff', 1, 'Fürht Kommandoaktionen aus.', 'Fürht Kommandoaktionen aus.', 3000, 5000, 5000, 2000, 0, 0, 0, 10, 30, 5, 0, 500, 0, 10, 5000, 60, 20, 0, 0, 8, 'position,attack,flight,hijack', '0.50', 0, 2000, 10, 10, 0, 1, 0, 1, 0, 0, 0, 0, 0, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', '15.000', 0, 0),
(85, 'Weltenvernichter V2', 1, '', '', 1000000, 1000000, 1000000, 1000000, 1000000, 0, 0, 0, 1, 1, 0, 1000000000, 1000000000, 1, 1000000000, 1, 1, 1, 1, 11, 'transport,position,attack,stealthattack,emp', '0.50', 80000000, 10000000, 10000000, 10000000, 0, 1, 0, 1, 0, 0, 0, 0, 0, '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '0.00', '5000.000', 0, 0),
(90, 'Weltenverteidiger', 1, 'bla', 'bla', 1000000, 1000000, 1000000, 1000000, 0, 0, 0, 0, 1, 1, 0, 1000000000, 1000000000, 1, 1000000, 1, 1, 1, 0, 12, 'transport,fetch,position,attack,invade,spyattack,stealthattack,fakeattack,bombard,emp,antrax,gasattack,collectcrystal,flight,support,alliance', '0.50', 4294967295, 1000000000, 1000000000, 1000000000, 5, 1, 0, 4, 0, 0, 0, 0, 0, '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '0.00', '4000.000', 0, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ship_cat`
--

CREATE TABLE IF NOT EXISTS `ship_cat` (
`cat_id` int(10) unsigned NOT NULL,
  `cat_name` varchar(50) NOT NULL,
  `cat_order` int(2) unsigned NOT NULL DEFAULT '0',
  `cat_color` char(7) NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Daten für Tabelle `ship_cat`
--

INSERT INTO `ship_cat` (`cat_id`, `cat_name`, `cat_order`, `cat_color`) VALUES
(1, 'Kriegsschiff', 2, '#0080FF'),
(2, 'Ziviles Schiff', 1, '#00FF00'),
(3, 'Episches Schiff', 4, '#B048F8'),
(4, 'Rassenspezifisches Schiff', 3, '#f00'),
(5, 'NPC-Schiff', 6, '#F07902'),
(6, 'Allianzschiff', 5, '#ffffff');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ship_queue`
--

CREATE TABLE IF NOT EXISTS `ship_queue` (
`queue_id` int(10) unsigned NOT NULL,
  `queue_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_ship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_cnt` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_endtime` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_objtime` int(10) unsigned NOT NULL DEFAULT '0',
  `queue_build_type` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ship_requirements`
--

CREATE TABLE IF NOT EXISTS `ship_requirements` (
`id` int(10) unsigned NOT NULL,
  `obj_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_level` tinyint(3) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=304 ;

--
-- Daten für Tabelle `ship_requirements`
--

INSERT INTO `ship_requirements` (`id`, `obj_id`, `req_building_id`, `req_tech_id`, `req_level`) VALUES
(1, 1, 9, 0, 2),
(2, 1, 11, 0, 1),
(3, 2, 9, 0, 2),
(4, 3, 9, 0, 1),
(5, 4, 9, 0, 5),
(6, 4, 11, 0, 2),
(7, 6, 9, 0, 7),
(8, 6, 11, 0, 7),
(9, 7, 9, 0, 8),
(10, 7, 11, 0, 5),
(11, 8, 9, 0, 10),
(12, 8, 11, 0, 8),
(13, 8, 8, 0, 7),
(14, 1, 0, 4, 4),
(15, 2, 0, 4, 1),
(16, 3, 0, 7, 1),
(17, 3, 0, 4, 1),
(18, 4, 0, 5, 3),
(19, 7, 0, 5, 5),
(20, 6, 0, 6, 4),
(22, 9, 0, 4, 2),
(23, 10, 9, 0, 5),
(24, 10, 0, 5, 5),
(25, 11, 0, 6, 11),
(26, 11, 9, 0, 9),
(27, 11, 11, 0, 10),
(28, 10, 0, 10, 5),
(32, 12, 9, 0, 8),
(33, 12, 0, 3, 6),
(34, 12, 0, 10, 5),
(35, 13, 9, 0, 12),
(36, 13, 11, 0, 10),
(38, 13, 0, 6, 13),
(42, 14, 9, 0, 4),
(43, 14, 11, 0, 4),
(44, 14, 0, 14, 3),
(47, 15, 9, 0, 4),
(46, 15, 0, 5, 7),
(49, 15, 0, 8, 12),
(50, 15, 0, 3, 9),
(51, 8, 0, 5, 8),
(52, 12, 0, 4, 7),
(53, 42, 9, 0, 10),
(54, 42, 0, 16, 7),
(55, 42, 0, 11, 10),
(194, 43, 9, 0, 3),
(57, 43, 0, 20, 5),
(58, 42, 0, 6, 6),
(60, 43, 0, 16, 2),
(61, 44, 9, 0, 8),
(62, 44, 0, 16, 4),
(64, 40, 9, 0, 5),
(65, 40, 0, 16, 4),
(66, 40, 0, 11, 10),
(67, 40, 0, 5, 6),
(68, 41, 9, 0, 3),
(69, 41, 0, 16, 2),
(70, 41, 0, 20, 5),
(71, 45, 9, 0, 7),
(72, 45, 0, 20, 7),
(73, 45, 0, 16, 6),
(74, 36, 9, 0, 7),
(75, 36, 0, 16, 6),
(76, 36, 0, 19, 4),
(77, 36, 0, 20, 6),
(78, 37, 9, 0, 3),
(79, 37, 0, 16, 2),
(80, 37, 0, 20, 5),
(81, 38, 9, 0, 5),
(82, 38, 0, 16, 4),
(83, 38, 0, 6, 5),
(84, 33, 9, 0, 3),
(85, 33, 0, 16, 2),
(86, 33, 0, 20, 5),
(87, 34, 9, 0, 5),
(88, 34, 0, 16, 4),
(89, 34, 0, 5, 10),
(90, 34, 0, 8, 13),
(91, 32, 9, 0, 7),
(92, 32, 0, 16, 6),
(93, 32, 0, 20, 6),
(94, 32, 0, 17, 3),
(95, 30, 9, 0, 5),
(96, 30, 0, 16, 6),
(97, 30, 0, 10, 12),
(98, 30, 0, 5, 6),
(99, 29, 9, 0, 3),
(100, 29, 0, 16, 2),
(101, 29, 0, 20, 5),
(102, 31, 9, 0, 5),
(103, 31, 0, 16, 4),
(104, 31, 0, 10, 8),
(105, 31, 0, 20, 6),
(106, 28, 9, 0, 5),
(107, 28, 0, 16, 4),
(108, 28, 0, 20, 6),
(109, 28, 11, 0, 6),
(110, 27, 9, 0, 3),
(111, 27, 0, 16, 2),
(112, 27, 0, 20, 5),
(113, 26, 9, 0, 7),
(114, 26, 0, 16, 6),
(115, 26, 0, 6, 6),
(116, 26, 0, 18, 4),
(117, 23, 9, 0, 7),
(118, 23, 0, 16, 6),
(119, 23, 0, 6, 5),
(120, 23, 0, 7, 15),
(121, 25, 9, 0, 3),
(122, 25, 11, 0, 7),
(123, 25, 0, 20, 5),
(124, 25, 0, 16, 2),
(125, 24, 9, 0, 5),
(126, 24, 0, 16, 2),
(127, 24, 0, 5, 7),
(128, 24, 0, 10, 8),
(129, 20, 9, 0, 7),
(130, 20, 0, 16, 6),
(131, 20, 0, 6, 6),
(132, 20, 0, 18, 4),
(133, 21, 9, 0, 3),
(134, 21, 0, 16, 2),
(135, 21, 0, 20, 5),
(136, 22, 9, 0, 7),
(137, 22, 0, 6, 7),
(138, 22, 0, 10, 7),
(139, 22, 0, 16, 6),
(140, 19, 9, 0, 3),
(141, 19, 0, 16, 2),
(142, 19, 0, 20, 5),
(143, 18, 9, 0, 5),
(144, 18, 0, 16, 4),
(145, 18, 0, 10, 7),
(146, 18, 0, 5, 7),
(147, 17, 9, 0, 7),
(148, 17, 0, 16, 6),
(149, 17, 0, 6, 6),
(150, 17, 0, 15, 3),
(151, 46, 9, 0, 6),
(152, 46, 0, 12, 3),
(153, 46, 0, 4, 6),
(157, 47, 9, 0, 11),
(158, 47, 11, 0, 7),
(160, 47, 0, 21, 9),
(162, 47, 0, 3, 6),
(163, 50, 9, 0, 6),
(164, 50, 0, 14, 5),
(165, 50, 0, 12, 6),
(166, 50, 0, 3, 5),
(167, 9, 11, 0, 1),
(169, 51, 9, 0, 1),
(170, 51, 0, 4, 3),
(171, 51, 0, 14, 9),
(172, 51, 0, 7, 8),
(173, 51, 0, 11, 5),
(174, 52, 9, 0, 6),
(175, 52, 0, 6, 8),
(176, 52, 0, 3, 4),
(178, 22, 11, 0, 5),
(179, 44, 0, 21, 6),
(180, 37, 0, 21, 3),
(181, 23, 0, 21, 5),
(182, 53, 11, 0, 8),
(184, 53, 0, 14, 10),
(185, 53, 0, 21, 8),
(186, 53, 9, 0, 10),
(187, 53, 0, 9, 4),
(188, 13, 0, 10, 7),
(189, 13, 0, 9, 6),
(190, 13, 0, 8, 7),
(191, 6, 0, 9, 6),
(193, 2, 0, 5, 1),
(195, 57, 9, 0, 5),
(196, 57, 0, 4, 3),
(197, 58, 8, 0, 4),
(199, 60, 0, 5, 5),
(201, 60, 0, 7, 7),
(202, 60, 9, 0, 9),
(203, 61, 0, 19, 5),
(204, 60, 0, 4, 4),
(205, 61, 0, 6, 7),
(206, 61, 9, 0, 9),
(207, 61, 0, 16, 5),
(208, 61, 0, 20, 4),
(209, 62, 0, 21, 6),
(210, 62, 0, 6, 8),
(211, 62, 0, 16, 5),
(212, 62, 9, 0, 9),
(213, 62, 0, 7, 15),
(214, 63, 9, 0, 9),
(215, 63, 0, 5, 10),
(216, 63, 0, 16, 5),
(217, 63, 0, 6, 9),
(218, 63, 0, 10, 10),
(220, 57, 11, 0, 4),
(221, 59, 0, 11, 14),
(222, 59, 9, 0, 9),
(223, 59, 0, 6, 8),
(224, 59, 0, 16, 8),
(225, 64, 0, 20, 8),
(226, 64, 0, 6, 7),
(227, 64, 9, 0, 9),
(228, 64, 0, 16, 5),
(229, 65, 0, 17, 4),
(230, 65, 0, 6, 7),
(231, 65, 0, 20, 6),
(232, 65, 0, 16, 5),
(233, 65, 9, 0, 9),
(234, 66, 0, 15, 4),
(235, 66, 0, 6, 8),
(236, 66, 0, 20, 5),
(237, 66, 0, 16, 5),
(238, 66, 9, 0, 9),
(239, 67, 0, 18, 5),
(240, 67, 0, 6, 8),
(241, 67, 0, 20, 6),
(242, 67, 9, 0, 9),
(243, 67, 0, 16, 5),
(244, 68, 0, 3, 2),
(245, 68, 0, 5, 2),
(246, 69, 9, 0, 8),
(247, 69, 11, 0, 6),
(248, 69, 0, 5, 5),
(249, 69, 0, 9, 4),
(250, 69, 0, 25, 2),
(251, 70, 0, 20, 5),
(252, 70, 9, 0, 3),
(253, 70, 0, 16, 2),
(278, 72, 0, 6, 8),
(275, 71, 9, 0, 1),
(277, 72, 0, 19, 6),
(271, 72, 0, 10, 10),
(279, 72, 0, 16, 5),
(268, 72, 9, 0, 9),
(274, 71, 0, 4, 1),
(280, 68, 9, 0, 1),
(281, 9, 9, 0, 1),
(282, 79, 0, 5, 4),
(286, 82, 0, 5, 5),
(287, 81, 0, 5, 2),
(288, 80, 0, 5, 1),
(289, 83, 24, 0, 3),
(290, 83, 0, 11, 15),
(291, 83, 0, 20, 7),
(292, 83, 0, 25, 3),
(303, 2, 0, 15, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sol_types`
--

CREATE TABLE IF NOT EXISTS `sol_types` (
`sol_type_id` int(10) unsigned NOT NULL,
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
  `sol_type_consider` tinyint(3) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Daten für Tabelle `sol_types`
--

INSERT INTO `sol_types` (`sol_type_id`, `sol_type_name`, `sol_type_f_metal`, `sol_type_f_crystal`, `sol_type_f_plastic`, `sol_type_f_fuel`, `sol_type_f_food`, `sol_type_f_power`, `sol_type_f_population`, `sol_type_f_buildtime`, `sol_type_comment`, `sol_type_f_researchtime`, `sol_type_consider`) VALUES
(1, 'Gelber Stern', '1.30', '1.30', '1.10', '1.00', '0.90', '1.10', '1.30', '1.10', 'Die gelben Sterne gehören zu der Kategorie "mittelgrosse Sterne". Das Alter solcher Gelben Sterne kann extrem variieren; sie können zwischen einigen Jahrtausenden bis hin zu Jahrmillionen alt sein.<br>Generell gilt jedoch, dass auf Gelben Sternen gemässigte und gute Lebensbedingungen herrschen. Ausserdem ist die Geodiversität relativ gross, was den Abbau von Metallen genauso fördert wie die Entwicklung von Chemikalien. Dank dem mineralhaltigen Boden ist sogar ein gewisser Kristallabbau möglich.<br>Einzig der Nahrung scheint der mineralienhaltige Boden nicht ganz so gut zu bekommen...', '1.10', 1),
(2, 'Blauer Stern', '1.00', '1.30', '1.00', '1.00', '1.00', '0.90', '0.80', '1.00', 'Diese Art von Sternen erscheint dem Beobachter meist blau; das liegt daran, dass im Innern des Sterns eine gewaltige Hitze herrscht, vergleichbar mit der blauen Färbung einer Flamme beim Schweissen.<br>Durch die gigantischen Hitzewellen sind die Lebensbedingungen im Umfeld Blauer Sterne für die verschiedenen Völker nicht optimal. Einige jedoch haben sich inzwischen dem heissen Klima anpassen können und nutzen genau dieses zur Verschmelzung von Kristallinem Material, um qualitativ hochstehende Kristallite herzustellen.<br>Bisher wollte es jedoch noch keinem Volk so richtig gelingen, aus dem heissen Klima einen weiteren Nutzen in Sachen Industrie zu ziehen. Im Gegenteil, meist ist die Stromproduktion und das Wachstum der Bevölkerung tiefer als in anderen Sternsystemen.', '1.10', 1),
(3, 'Roter Stern', '0.90', '1.20', '1.00', '1.20', '1.10', '0.80', '1.30', '1.00', 'Rote Sterne sind eher klein und schon recht alt. Dadurch ist ihre Energieaustrahlung nicht mehr ganz so gross, was wiederum eine gute Klimabedingung für die meisten Völker ist. Deshalb verwundert es nicht, dass man in vielen Roten Sternen alle möglichen Völker antrifft, welche dort seit ewigen Zeiten eine neue Heimat gefunden haben.<br>Ebenfalls positiv wirkt sich die gemässigte Energieabgabe der Roten Sterne auf verschiedenste Produktionen aus, was dann wiederum den dort wohnhaften Völkern zugute kommt.', '1.00', 1),
(4, 'Weisser Stern', '0.90', '1.10', '1.00', '1.60', '1.00', '1.60', '0.95', '1.00', 'Weisse Sterne sind stark energiehaltige Sterne, deren Energieausstösse für das extrem helle Licht verantwortlich sind.<br>Dadurch lässt sich in der Nähe von Weissen Sternen mit relativ wenig Aufwand Tritium und Strom herstellen. Ebenfalls positiv wirkt sich die Energiestrahlung auf die Kristallisation aus, jedoch nicht auf die Menschen. Jene ertragen die gewaltigen Energiemengen nicht zu lange, weshalb der Bevölkerungswachstum in Weissen Sternen meist kleiner als in anderen Sternen ist.', '1.00', 1),
(5, 'Violetter Stern', '1.00', '0.90', '1.00', '0.90', '1.00', '1.00', '1.05', '0.90', 'Violette Sterne sind sehr junge Sterne, die sich meistens innerhalb von Gaswolken befinden. Die für den Betrachter violette Färbung der Sonne entsteht durch die vielen verschiedenen Nebel, welche das Sonnenlicht jeweils verschieden brechen.<br>Weil die Sterne noch ziemlich jung sind, ist noch nicht viel über sie bekannt; die Beobachtungen der verschiedenen Völker haben erst begonnen.', '0.90', 1),
(6, 'Schwarzer Stern', '0.90', '1.00', '1.20', '1.10', '1.00', '0.80', '0.60', '0.85', 'Praktisch keiner weiss etwas über schwarze Sterne, da sie erst vor kurzem durch eine neuartige Objektivtechnologie sichtbar gemacht werden konnten.<br>Erst einzelne überragende Forscher haben angefangen, sich an diese Mysterien im All heranzuwagen.<br>Ungenannte Quellen munkeln jedoch, dass die schwarze Färbung durch aktive schwarze Löcher auftritt, was die Völker natürlich davor abschreckt, mehr über die Schwarzen Sterne rauszufinden.', '1.00', 1),
(7, 'Grüner Stern', '1.40', '1.10', '1.00', '1.00', '1.20', '0.90', '0.90', '1.10', 'Grüne Sterne wirken auf den ersten Blick giftig - und so ganz unrecht ist das auch nicht. Durch Gase aus dem Inneren der Sterne werden immer wieder Epidemien ausgelöst, die Teile der Bewohner von Grünen Sternen dahinraffen.<br>Entgegen den unwirtlichen Lebensbedingungen wirken sich die Gase und die Geostruktur positiv auf die Steingefüge der Sterne aus.<br>Es verwundert daher nicht, dass in Grünen Sternen oftmals Raffinerien, Erzwerke und Metallverarbeitungsanlagen anzutreffen sind.', '1.00', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `space`
--

CREATE TABLE IF NOT EXISTS `space` (
  `id` int(10) unsigned NOT NULL,
  `lastvisited` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `specialists`
--

CREATE TABLE IF NOT EXISTS `specialists` (
`specialist_id` int(10) unsigned NOT NULL,
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
  `specialist_trade_bonus` decimal(4,2) unsigned NOT NULL DEFAULT '1.00'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Daten für Tabelle `specialists`
--

INSERT INTO `specialists` (`specialist_id`, `specialist_name`, `specialist_desc`, `specialist_enabled`, `specialist_points_req`, `specialist_costs_metal`, `specialist_costs_crystal`, `specialist_costs_plastic`, `specialist_costs_fuel`, `specialist_costs_food`, `specialist_days`, `specialist_prod_metal`, `specialist_prod_crystal`, `specialist_prod_plastic`, `specialist_prod_fuel`, `specialist_prod_food`, `specialist_power`, `specialist_population`, `specialist_time_tech`, `specialist_time_buildings`, `specialist_time_defense`, `specialist_time_ships`, `specialist_costs_buildings`, `specialist_costs_defense`, `specialist_costs_ships`, `specialist_costs_tech`, `specialist_fleet_speed`, `specialist_fleet_max`, `specialist_def_repair`, `specialist_spy_level`, `specialist_tarn_level`, `specialist_trade_time`, `specialist_trade_bonus`) VALUES
(1, 'Admiral', 'Der Flottenadmiral ist ein kriegserfahrener Veteran und meisterhafter Stratege. Auch im heissesten Gefecht behält er im Gefechtsleitstand den Überblick und hält Kontakt mit den ihm unterstellten Admirälen. Ein weiser Herrscher kann sich auf seine Unterstützung im Kampf absolut verlassen und somit mehr Raumflotten gleichzeitig und schneller ins Gefecht führen.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.10', 3, '1.00', 0, 0, '1.00', '1.00'),
(2, 'Ingenieur', 'Der Ingenieur ist ein Spezialist für besonders durchdachte und stabile Verteidigungssysteme. Durch seine Mithilfe können Verteidigungsanlagen schneller und günstiger produziert werden. Nach einem Kampf kann er auch schwer beschädigte Anlagen wieder reparieren.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '0.80', '1.00', '1.00', '0.90', '1.00', '1.00', '1.00', 0, '1.40', 0, 0, '1.00', '1.00'),
(3, 'Geologe', 'Der Geologe ist ein anerkannter Experte in Astromineralogie und -kristallographie. Mithilfe seines Teams aus Metallurgen und Chemieingenieuren unterstützt er interplanetarische Regierungen bei der Erschließung neuer Rohstoffquellen und der Optimierung ihrer Raffination.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, '1.10', '1.10', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', 0, '1.00', 0, 0, '1.00', '1.00'),
(4, 'Professor', 'Die Gilde der Technokraten sind geniale Wissenschaftler, und man findet sie immer dort, wo die Grenzen des technisch Machbaren gesprengt werden. Durch seine reine Anwesenheit inspiriert er die Forscher des Imperiums.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '0.80', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '0.90', '1.00', 0, '1.00', 0, 0, '1.00', '1.00'),
(5, 'Biologe', 'Der Biologe steigert durch seine gentechnischen Experimente den Ertrag deiner Gewächshäuser und sorgt für ein rascheres Bevölkerungswachstum.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, '1.00', '1.00', '1.00', '1.00', '1.30', '1.00', '1.30', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', 0, '1.00', 0, 0, '1.00', '1.00'),
(6, 'Spion', 'Der Spion ist ein Meister der Tarnung und Informationsbeschaffung. Durch seine Tricks ist es möglich, mehr Informationen über den Gegner herauszufinden und die eigenen Schiffe besser zu tarnen.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '0.90', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', 0, '1.00', 3, 2, '1.00', '1.00'),
(7, 'Meisterhändler', 'Durch das Verhandlungsgeschick des Meisterhändlers fallen im Markt keine zusätzlichen Kosten an, er hat weniger Handelsbeschränkungen und seine Handelsschiffe fliegen schneller als alle anderen.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', 0, '1.00', 0, 0, '6.00', '0.00'),
(8, 'Energieminister', 'Der Energieminister kennt sich auf dem Gebiet der Energieförderung bestens aus. Durch seine vorausschauende Planung ist es möglich, die Produktion der Kraftwerke drastisch zu steigern. Dadurch kann auch die stromintensive Synthetisierung von Tritium merklich gesteigert werden.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, '1.00', '1.00', '1.00', '1.10', '1.00', '1.40', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', 0, '1.00', 0, 0, '1.00', '1.00'),
(0, 'Nulldummy', 'Nicht löschen', 0, 0, 0, 0, 0, 0, 0, 14, '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', 0, '1.00', 0, 0, '1.00', '1.00');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `stars`
--

CREATE TABLE IF NOT EXISTS `stars` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(30) NOT NULL,
  `type_id` tinyint(1) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `techlist`
--

CREATE TABLE IF NOT EXISTS `techlist` (
`techlist_id` int(10) unsigned NOT NULL,
  `techlist_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `techlist_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `techlist_entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `techlist_current_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `techlist_build_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `techlist_build_start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `techlist_build_end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `techlist_prod_percent` int(4) unsigned NOT NULL DEFAULT '100'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `technologies`
--

CREATE TABLE IF NOT EXISTS `technologies` (
`tech_id` int(10) unsigned NOT NULL,
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
  `tech_stealable` tinyint(1) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Daten für Tabelle `technologies`
--

INSERT INTO `technologies` (`tech_id`, `tech_name`, `tech_type_id`, `tech_shortcomment`, `tech_longcomment`, `tech_costs_metal`, `tech_costs_crystal`, `tech_costs_fuel`, `tech_costs_plastic`, `tech_costs_food`, `tech_costs_power`, `tech_build_costs_factor`, `tech_last_level`, `tech_show`, `tech_order`, `tech_stealable`) VALUES
(7, 'Spionagetechnik', 4, 'Je höher die Spionagetechnik ist, desto mehr können Spionagesonden über gegnerische Planeten herausfinden.', 'Spionage ist die Auskundschaftung und Erlangung von fremden, wohlgehüteten Geheimnissen oder Wissen von fremden Planeten. Die erlangten Informationen werden dann in den eigenen wirtschaftlichen, politischen oder militärischen Machtbereich eingeführt, ohne dass eine eigenständige Erforschung erfolgen müsste. Annähernd sämtliche Imperien bedienen sich der Spionage oder \\"nachrichtendienstlicher Mittel\\", um andere Völker (unabhängig der feindseligen oder freundlichen Einstellung zum eigenen Volk) auszuspionieren.\r\nEine weitere nützliche Eigenschaft der Spionagetechnik ist das Enttarnen von feindlichen Angriffen, welche mit höherer Spionagetechnik schneller vonstatten geht.', 750, 370, 150, 520, 0, 0, '1.50', 50, 1, 1, 1),
(8, 'Waffentechnik', 2, 'Jede Ausbaustufe erhöht die Stärke der Waffen bei Raumschiffen und Verteidigungsanlagen.', 'Durch die Erforschung der Waffentechnik können neue und stärkere Waffen für Raumschiffe und Verteidigungsanlagen gebaut werden.\r\nPro Ausbaustufe erhöht sich die Angriffskraft deiner Schiffe und Verteidigungsanlagen um 10%.', 250, 800, 550, 200, 0, 0, '1.80', 50, 1, 1, 1),
(4, 'Wasserstoffantrieb', 1, 'Einfacher Antrieb für Raumschiffe', 'Ein Wasserstoffantrieb nutzt Wasserstoff als Treibstoff. Dieser wird durch Elektrolyse von Wasser, Reformation von Methanol oder durch Dampfreformation von Erdgas gewonnen.', 500, 300, 250, 50, 0, 0, '1.50', 50, 1, 0, 1),
(5, 'Ionenantrieb', 1, 'Hoch entwickelter Antrieb für Spezialschiffe. Er ist weniger schnell als der Wasserstoffantrieb, dafür kostensparend.', 'Ein Ionenantrieb ist ein Antrieb für Raumfahrzeuge, bei dem die Abstossung von einem Ionenstrahl zur Fortbewegung genutzt wird. Es werden auch je nach Energiequelle die Begriffe \\"solar-elektrischer Antrieb\\" bzw. \\"Solar Electric Propulsion\\" (SEP) und \\"nuklear-elektrischer Antrieb\\" bzw. \\"Nuclear Electric Propulsion\\" (NEP) verwendet.\r\nDer Ionenstrahl besteht aus einem elektrisch geladenen Gas (z.B. Xenon). Erzeugt wird der Ionenstrahl durch ionisierte Gasteilchen, die in einem elektrischen Feld oder mittels einer Kombination eines elektrischen Feldes und eines Magnetfeldes unter Ausnutzung des Hall-Effektes beschleunigt und dann in Form eines Strahls ausgestossen werden. Die Energie zur Erzeugung der Felder wird üblicherweise mit Hilfe von Solarzellen gewonnen. Als Treibstoff des Ionenantriebs dient sowohl das Gas als auch die zusätzlich benötigte elektrische Energie.\r\nDer Vorteil des Ionenantriebs gegenüber dem chemischen Antrieb liegt darin, dass er weniger Treibstoff verbraucht, weil die Geschwindigkeit der austretenden Teilchen wesentlich grösser ist.', 1000, 1500, 800, 300, 0, 0, '1.50', 50, 1, 1, 1),
(6, 'Hyperraumantrieb', 1, 'Sehr schneller Antrieb für grosse Schiffe, der den Hyperraum als Transportmedium benutzt.', 'Der Hyperraumantrieb schafft eine technisch hervorgerufene Abkürzung zwischen weit entfernten Punkten in der Raumzeit. Die Idee ist dabei folgende: Um den Weg vom Nordpol zum Südpol abzukürzen, reise man quer durch die Erde, anstatt entlang der Oberfläche. Der Weg durch die Erde (in die dritte Dimension) ist kürzer als der Weg auf der (zweidimensionalen) Erdoberfläche. Genauso könnte man sich vorstellen, dass unsere Raumzeit auch in einen höherdimensionalen Hyperraum eingebettet ist (wie die Erdoberfläche in den Raum), und man daher durch den Hyperraum abkürzen könnte. Auch hier würde man (im Hyperraum) nicht schneller als Lichtgeschwindigkeit fliegen müssen, um schneller als das Licht im Normalraum am Ziel anzukommen.\r\nDiese Antriebstechnologie wird heute für fast jedes grosse und träge Schiff eingesetzt.', 4000, 6000, 1500, 5500, 0, 0, '1.80', 50, 1, 3, 1),
(3, 'Energietechnik', 4, 'Diese Technologie dient zur Erforschung neuer Arten der Energiegewinnung.', 'Durch die Unterstützung der Energietechnik können neue Arten der Energiegewinnung erforscht werden.', 300, 250, 30, 50, 0, 0, '1.50', 50, 1, 0, 1),
(9, 'Panzerung', 2, 'Jede Ausbaustufe erhöht die Stärke der Panzerung bei Raumschiffen und Verteidigungsanlagen.', 'Jedes Schiff und jede Verteidigungsanlage besitzen eine Panzerung zum Schutz vor feindlichen Angriffen. Pro Ausbaustufe erhöht diese Technologie die Panzerung, auch genannt Struktur, um 10%.', 1000, 150, 320, 270, 0, 0, '1.80', 50, 1, 2, 1),
(10, 'Schutzschilder', 2, 'Jede Ausbaustufe erhöht die Stärke der Schutzschilder bei Raumschiffen und Verteidigungsanlagen.', 'Ein Schutzschild schützt deine Raumschiffe und Verteidigungsanlagen vor feindlichem Beschuss.\r\nPro Ausbaustufe erhöht sich die Effizienz von den Schutzschildern um 10%.', 290, 330, 250, 950, 0, 0, '1.80', 50, 1, 3, 1),
(11, 'Tarntechnik', 2, 'Durch eine hohe Tarntechnik können deine Flotten eine gewisse Zeit vor dem Gegner verborgen bleiben.', 'Die Kriegsära hat begonnen; die Völker erforschen Technologien, mit welchen sie dem Gegner in einem allfälligen Kampf überlegen sind. Die Tarntechnik ist eigentlich schon eine uralte Waffe, welche den Überraschungseffekt ausnutzt, um so eine bessere Ausgangsposition zu haben; doch erst jetzt ist es wirklich möglich, seine Schiffe von der gegnerischen Flottenkontrolle zu verstecken.\r\nJe höher diese Technologie erforscht ist, desto länger bleiben die Schiffe für den Gegner unentdeckt.', 1500, 750, 250, 800, 0, 0, '1.60', 50, 1, 4, 1),
(12, 'Recyclingtechnologie', 4, 'Ermöglicht eine effiziente Wiederverwertung von alten Verteidigungsanlagen und Schiffen.', 'Lange Zeit hatte man eine Technik gesucht, welche verbaute Rohstoffe wieder verwerten kann. Nach jahrelanger Forschung wurde ein Verfahren entwickelt, das Schiffe und Verteidigungsanlagen recyceln kann. Jedoch ist diese Technologie in der Anfangsphase noch sehr ineffizient.\r\nDies kann aber mit der Weiterentwicklung ein wenig eingedämpft werden. Man weiss jedoch, dass die Materialien nie zu 100% recycelt werden können.', 12000, 20000, 2000, 8000, 0, 0, '1.90', 50, 1, 2, 1),
(13, 'Rettungskapseln', 2, 'Je höher die Rettungskapseln entwickelt sind, desto mehr Piloten können sich retten, wenn ihr Schiff bei einem Kampf zerstört wird. ', 'Je höher die Rettungskapseln entwickelt sind, desto mehr Piloten können sich retten, wenn ihr Schiff bei einem Kampf zerstört wird.\r\nEinige Schiffe können nur gebaut werden, wenn gute Rettungskapseln an Bord sind.\r\nUm Grosse Schiffe zu bauen, muss man die Rettungskapseln entwickelt haben.', 12000, 2000, 3000, 8000, 2000, 0, '1.90', 50, 0, 5, 1),
(14, 'Kraftstoffantrieb', 1, 'Verbesserter Wasserstoffantrieb, der mit einer Mischung aus Tritium und Asteroidenteilchen arbeitet. ', 'Verbesserter Wasserstoffantrieb, der mit einer Mischung aus Tritium und Asteroidenteilchen arbeitet. Dieser Antrieb ermöglicht es grösseren Schiffen, sich schneller fortzubewegen.', 25500, 7752, 19347, 10474, 0, 0, '1.30', 50, 1, 2, 1),
(15, 'Bombentechnik', 3, 'Mit Hilfe dieser Technik wird die Effektivität von Bombenangriffen gesteigert.', 'Längst hat man rausgefunden, dass das alleinige Zerstören von gegnerischen Flotten nicht mehr unbedingt den gewünschten Effekt hat.\r\nForscher haben aus diesem Grund eine neuartige Waffe entwickelt, mit der es möglich ist, fremde Gebäude zu bombardieren und so den Gegner wieder ins industrielle Mittelalter zu befördern.\r\nDiese Methode der Kriegsführung ist aber noch sehr jung, und deshalb ist die Chance auf eine erfolgreiche Bombardierung noch nicht allzu hoch.\r\nDurch die Erforschung der Bombentechnik wird diese aber deutlich gesteigert.', 13000, 26000, 8000, 13000, 0, 0, '1.75', 50, 1, 0, 1),
(16, 'Rassentechnik', 4, 'Mit der Rassentechnologie kann jede Rasse ihre rassenspezifischen Objekte bauen.', 'Mit der Rassentechnologie kann jede Rasse ihre rassenspezifischen Objekte bauen. Je höher sie erforscht ist, desto bessere und stärkere Rassenobjekte können gebaut werden.', 1000, 1000, 1000, 1000, 1000, 0, '1.50', 50, 1, 3, 1),
(17, 'EMP-Technik', 3, 'EMP-Bomben löst einen Elektromagnetischen Impuls aus, welcher elektrische Einrichtungen ausser Betrieb setzen kann.', 'Je länger je mehr schützen die Völker ihre Schiffe, indem sie sie ständig auf Erkundungsflüge schicken und so für den Gegner unerreichbar machen.\r\nEin Forschungsteam der Rigelianer hat es sich zur Aufgabe gemacht, diese Strategie zu vernichten.\r\nNach langen Forschungen haben sie ein Schiff entwickelt, mit dem es möglich ist, ganze Einrichtungen unbrauchbar zu machen.\r\nEin elektromagnetischer Impuls setzt alle elektronischen Geräte ausser Gefecht. Mit Hilfe dieser brillianten Waffe kann man nun dem Gegner beispielsweise die Flottenkontrolle lahm legen und den Schiffen den Abflug vom Planeten verweigern.\r\nJedoch ist auch diese Technologie noch nicht ganz ausgereift; so muss man sich beispielsweise mit einer kurzfristigen Deaktivierung zufrieden geben. Durch die Weiterentwicklung der EMP Technologie erhöht sich jedoch die Effizienz des Angriffes.', 15000, 15000, 10000, 15000, 0, 0, '1.70', 50, 1, 1, 1),
(18, 'Gifttechnik', 3, 'Diese Technologie wird für B- und C- Waffen gebraucht.', 'Die Gifttechnologie ist eine Massenvernichtungswaffe für Bewohner. Durch Zerstörung der Nervenbahnen und allmähliches Verringern der Wahrnehmungsfähigkeit lässt das Gift die Bewohner erkranken und kurze Zeit später an den Folgen sterben. Eine grausame, aber sehr effektive Waffe.\r\nDie Weiterentwicklung ermöglicht einen noch präziseren Einsatz der Gifte.', 10000, 10000, 5000, 20000, 0, 0, '1.50', 50, 1, 2, 1),
(19, 'Regenatechnik', 3, 'Neuartige Materialien ermöglichen gewissen Schiffen, sich während dem Kampf teilweise zu reparieren.', 'Das Heilen von Schiffen war schon immer sehr schwierig und wird sich wohl erst in Zukunft bei einer neuen Generation von Schiffen durchsetzen.\r\nBisher ist es nur einer einzigen Rasse gelungen, ein Schiff herzustellen, welches die eigene Flotte im Kampf heilen kann.\r\nEiner anderen Rasse ist es inzwischen gelungen, diesselbe Technologie für ihre Verteidigungsanlagen anzuwenden.\r\nDurch die Erhöhung der Technologie kann deren Effizienz gesteigert werden.', 30000, 17500, 12500, 17500, 0, 0, '1.90', 50, 1, 3, 1),
(20, 'Warpantrieb', 1, 'Die Warpgondeln eines Raumschiffes erzeugen ein Feld, welches den Raum krümmt und so das Schiff extrem beschleunigt.', 'Jede Rasse hat nach einer gewissen Zeit angefangen, ihre eigenen Schiffe zu bauen. Eine uns unbekannte Rasse hat den Warpantrieb entwickelt. Die uns bekannten Rassen konnten ihn jedoch nur bedingt anwenden. So sind ihre Schiffe nicht ganz so schnell wie sie eigentlich sein könnten. Die Warpgondeln eines Raumschiffes erzeugen ein Feld, welches den Raum krümmt und so das Schiff extrem beschleunigt.', 6000, 4500, 2000, 5500, 0, 0, '1.70', 50, 1, 5, 1),
(21, 'Solarantrieb', 1, 'Hinter dem unspektakulären Namen steckt eine sehr sparsame und interessante Technik. ', 'Hinter dem unspektakulären Namen steckt eine sehr sparsame und interessante Technik. Schiffe mit einem Solarantrieb können während dem Flug ihr Triebwerk ausschalten und ein riesiges Sonnensegel ausfahren, wodurch sie vom Sonnenwind mit unglaublicher Geschwindigkeit durchs All getragen werden.\r\nDie Erforschung ist nicht sehr billig, jedoch birgt es einen unschlagbaren Vorteil. Die Schiffe verbrauchen viel weniger Treibstoff für den Flug. Es soll sogar Schiffe geben, die allein mit den Solarzellen die benötigte Energie zum Flug aufbringen und so ohne Tritiumverbrauch fliegen können.', 2100, 1300, 1100, 300, 0, 0, '1.80', 50, 1, 4, 1),
(22, 'Wurmlochforschung', 3, 'Ermöglicht einer Flotte das Reisen durch Wurmlöcher. Dadurch wird die Flugzeit einer Flotte enorm verkürzt.', 'Wurmlöcher sind topologische Konstrukte, die weit voneinander entfernt liegende Bereiche des Universums durch eine \\''Abkürzung\\'' verbinden. Ein Ende eines Wurmlochs erscheint dem Beobachter als Kugel, die ihm die Umgebung des anderen Endes zeigt. Obwohl ein durch ein Wurmloch Reisender nie die Lichtgeschwindigkeit überschreiten würde, hätte in Bezug auf die betreffenden Start- und Zielbereiche eine Reise mit Überlichtgeschwindigkeit stattgefunden. Durch die Erforschung der Wurmlöcher gelang es Wissenschaftlern, Technologien für das Reisen durch Wurmlöcher zu entwickeln und somit die Flugzeit enorm zu verkürzen. Ob die zwei Wurmlochenden eines Lochs immer miteinander verknüpft bleiben oder ob  die Verknüpfungen von Zeit zu Zeit ändern, ist Gegenstand aktueller Untersuchungen.\r\nBisher ist es den Forschern jedoch noch nicht gelungen, ein solches Wurmloch länger als ein paar Tage offen zu halten.', 100000, 120000, 175000, 290000, 250000, 0, '1.60', 1, 1, 5, 1),
(23, 'Gentechnik', 3, 'Durch die Manipulierung der Gene ist es möglich, die Leistung der Arbeiter zu steigern und so die Bauzeit zu verringern.', 'Den Forschern ist ein absoluter Durchbruch im Bereich Genforschung gelungen. Bisher waren alle genmanipulierten Arbeiterversuche fehlgeschlagen und die meisten Versuchsobjekte überlebten dieses Experiment nicht. Doch nun gelang mit Hilfe von Hochpräzisionsmaschinen eine genetische Veränderung, sodass die Arbeiter zu höheren Leistungen fähig sind.\r\nDies hat zur Folge, dass die Bauzeit von jeglichen Produkten nochmals gesenkt werden kann.\r\nDiese revolutionäre Errungenschaft hat aber ihren Preis, denn der Eingriff ist extrem zeit- und kostenaufwändig. Viele Wissenschaftler sind sich aber dennoch einig, dass es sich allemal lohnt, diese Technologie zu verbessern und zu perfektionieren.', 100000000, 60000000, 38000000, 40000000, 20000000, 0, '1.40', 8, 1, 6, 0),
(24, 'Raketentechnik', 3, 'Das Wissen um diese Technologie in Verbindung mit dem Raketensilo ermöglichen es, Raketen zu konstruieren.', 'Damit Raketen eingesetzt werden können, muss zuerst die Raketetechnik erforscht sein. Je höher die Raketentechnik erforscht ist, desto bessere und effektivere Raketen können gebaut werden.', 30000, 60000, 400000, 20000, 0, 0, '1.20', 10, 1, 6, 1),
(25, 'Computertechnik', 4, 'Mit Computern können Forscher komplexe Gleichungssysteme lösen, um genauere Flugbahnen zu berechnen.', 'Mit Hilfe der Computerwissenschaft können Forscher komplexe Gleichungssysteme lösen, um damit zum Beispiel genaue Flugbahnen zu berechnen. Dies kann zu einem Vorteil in der gegnerischen Flottenüberwachung führen oder eine bessere Steuerbarkeit von Raketen ermöglichen.', 500, 5000, 0, 3000, 0, 0, '1.30', 15, 1, 4, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tech_points`
--

CREATE TABLE IF NOT EXISTS `tech_points` (
`bp_id` int(10) unsigned NOT NULL,
  `bp_tech_id` int(10) unsigned NOT NULL,
  `bp_level` tinyint(3) unsigned NOT NULL,
  `bp_points` decimal(20,3) unsigned NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10767 ;

--
-- Daten für Tabelle `tech_points`
--

INSERT INTO `tech_points` (`bp_id`, `bp_tech_id`, `bp_level`, `bp_points`) VALUES
(9783, 7, 1, '1.790'),
(9784, 7, 2, '4.475'),
(9785, 7, 3, '8.503'),
(9786, 7, 4, '14.544'),
(9787, 7, 5, '23.606'),
(9788, 7, 6, '37.198'),
(9789, 7, 7, '57.588'),
(9790, 7, 8, '88.171'),
(9791, 7, 9, '134.047'),
(9792, 7, 10, '202.861'),
(9793, 7, 11, '306.081'),
(9794, 7, 12, '460.912'),
(9795, 7, 13, '693.158'),
(9796, 7, 14, '1041.527'),
(9797, 7, 15, '1564.080'),
(9798, 7, 16, '2347.910'),
(9799, 7, 17, '3523.655'),
(9800, 7, 18, '5287.273'),
(9801, 7, 19, '7932.699'),
(9802, 7, 20, '11900.839'),
(9803, 7, 21, '17853.049'),
(9804, 7, 22, '26781.363'),
(9805, 7, 23, '40173.834'),
(9806, 7, 24, '60262.542'),
(9807, 7, 25, '90395.602'),
(9808, 7, 26, '135595.194'),
(9809, 7, 27, '203394.581'),
(9810, 7, 28, '305093.661'),
(9811, 7, 29, '457642.281'),
(9812, 7, 30, '686465.212'),
(9813, 7, 31, '1029699.608'),
(9814, 7, 32, '1544551.202'),
(9815, 7, 33, '2316828.593'),
(9816, 7, 34, '3475244.680'),
(9817, 7, 35, '5212868.810'),
(9818, 7, 36, '7819305.004'),
(9819, 7, 37, '11728959.297'),
(9820, 7, 38, '17593440.735'),
(9821, 7, 39, '26390162.893'),
(9822, 7, 40, '39585246.129'),
(9823, 7, 41, '59377870.983'),
(9824, 7, 42, '89066808.265'),
(9825, 7, 43, '133600214.188'),
(9826, 7, 44, '200400323.072'),
(9827, 7, 45, '300600486.397'),
(9828, 7, 46, '450900731.386'),
(9829, 7, 47, '676351098.869'),
(9830, 7, 48, '1014526650.090'),
(9831, 7, 49, '1521789976.930'),
(9832, 7, 50, '2282684967.190'),
(9833, 8, 1, '1.800'),
(9834, 8, 2, '5.040'),
(9835, 8, 3, '10.872'),
(9836, 8, 4, '21.370'),
(9837, 8, 5, '40.265'),
(9838, 8, 6, '74.278'),
(9839, 8, 7, '135.500'),
(9840, 8, 8, '245.699'),
(9841, 8, 9, '444.058'),
(9842, 8, 10, '801.105'),
(9843, 8, 11, '1443.789'),
(9844, 8, 12, '2600.621'),
(9845, 8, 13, '4682.917'),
(9846, 8, 14, '8431.051'),
(9847, 8, 15, '15177.691'),
(9848, 8, 16, '27321.644'),
(9849, 8, 17, '49180.760'),
(9850, 8, 18, '88527.168'),
(9851, 8, 19, '159350.703'),
(9852, 8, 20, '286833.065'),
(9853, 8, 21, '516301.317'),
(9854, 8, 22, '929344.170'),
(9855, 8, 23, '1672821.306'),
(9856, 8, 24, '3011080.151'),
(9857, 8, 25, '5419946.072'),
(9858, 8, 26, '9755904.730'),
(9859, 8, 27, '17560630.315'),
(9860, 8, 28, '31609136.367'),
(9861, 8, 29, '56896447.260'),
(9862, 8, 30, '102413606.868'),
(9863, 8, 31, '184344494.162'),
(9864, 8, 32, '331820091.292'),
(9865, 8, 33, '597276166.125'),
(9866, 8, 34, '1075097100.820'),
(9867, 8, 35, '1935174783.280'),
(9868, 8, 36, '3483314611.710'),
(9869, 8, 37, '6269966302.880'),
(9870, 8, 38, '11285939347.000'),
(9871, 8, 39, '20314690826.400'),
(9872, 8, 40, '36566443489.300'),
(9873, 8, 41, '65819598282.500'),
(9874, 8, 42, '118475276910.000'),
(9875, 8, 43, '213255498440.000'),
(9876, 8, 44, '383859897194.000'),
(9877, 8, 45, '690947814952.000'),
(9878, 8, 46, '1243706066920.000'),
(9879, 8, 47, '2238670920450.000'),
(9880, 8, 48, '4029607656810.000'),
(9881, 8, 49, '7253293782260.000'),
(9882, 8, 50, '13055928808100.000'),
(9883, 4, 1, '1.100'),
(9884, 4, 2, '2.750'),
(9885, 4, 3, '5.225'),
(9886, 4, 4, '8.938'),
(9887, 4, 5, '14.506'),
(9888, 4, 6, '22.859'),
(9889, 4, 7, '35.389'),
(9890, 4, 8, '54.184'),
(9891, 4, 9, '82.375'),
(9892, 4, 10, '124.663'),
(9893, 4, 11, '188.095'),
(9894, 4, 12, '283.242'),
(9895, 4, 13, '425.963'),
(9896, 4, 14, '640.044'),
(9897, 4, 15, '961.167'),
(9898, 4, 16, '1442.850'),
(9899, 4, 17, '2165.375'),
(9900, 4, 18, '3249.162'),
(9901, 4, 19, '4874.843'),
(9902, 4, 20, '7313.365'),
(9903, 4, 21, '10971.147'),
(9904, 4, 22, '16457.821'),
(9905, 4, 23, '24687.831'),
(9906, 4, 24, '37032.847'),
(9907, 4, 25, '55550.370'),
(9908, 4, 26, '83326.655'),
(9909, 4, 27, '124991.083'),
(9910, 4, 28, '187487.725'),
(9911, 4, 29, '281232.687'),
(9912, 4, 30, '421850.130'),
(9913, 4, 31, '632776.295'),
(9914, 4, 32, '949165.543'),
(9915, 4, 33, '1423749.415'),
(9916, 4, 34, '2135625.222'),
(9917, 4, 35, '3203438.933'),
(9918, 4, 36, '4805159.500'),
(9919, 4, 37, '7207740.350'),
(9920, 4, 38, '10811611.625'),
(9921, 4, 39, '16217418.537'),
(9922, 4, 40, '24326128.906'),
(9923, 4, 41, '36489194.459'),
(9924, 4, 42, '54733792.789'),
(9925, 4, 43, '82100690.283'),
(9926, 4, 44, '123151036.524'),
(9927, 4, 45, '184726555.887'),
(9928, 4, 46, '277089834.930'),
(9929, 4, 47, '415634753.495'),
(9930, 4, 48, '623452131.343'),
(9931, 4, 49, '935178198.114'),
(9932, 4, 50, '1402767298.270'),
(9933, 5, 1, '3.600'),
(9934, 5, 2, '9.000'),
(9935, 5, 3, '17.100'),
(9936, 5, 4, '29.250'),
(9937, 5, 5, '47.475'),
(9938, 5, 6, '74.813'),
(9939, 5, 7, '115.819'),
(9940, 5, 8, '177.328'),
(9941, 5, 9, '269.592'),
(9942, 5, 10, '407.988'),
(9943, 5, 11, '615.582'),
(9944, 5, 12, '926.974'),
(9945, 5, 13, '1394.060'),
(9946, 5, 14, '2094.691'),
(9947, 5, 15, '3145.636'),
(9948, 5, 16, '4722.054'),
(9949, 5, 17, '7086.681'),
(9950, 5, 18, '10633.622'),
(9951, 5, 19, '15954.032'),
(9952, 5, 20, '23934.648'),
(9953, 5, 21, '35905.573'),
(9954, 5, 22, '53861.959'),
(9955, 5, 23, '80796.539'),
(9956, 5, 24, '121198.408'),
(9957, 5, 25, '181801.212'),
(9958, 5, 26, '272705.418'),
(9959, 5, 27, '409061.726'),
(9960, 5, 28, '613596.190'),
(9961, 5, 29, '920397.884'),
(9962, 5, 30, '1380600.426'),
(9963, 5, 31, '2070904.240'),
(9964, 5, 32, '3106359.960'),
(9965, 5, 33, '4659543.539'),
(9966, 5, 34, '6989318.909'),
(9967, 5, 35, '10483981.964'),
(9968, 5, 36, '15725976.545'),
(9969, 5, 37, '23588968.418'),
(9970, 5, 38, '35383456.227'),
(9971, 5, 39, '53075187.941'),
(9972, 5, 40, '79612785.511'),
(9973, 5, 41, '119419181.866'),
(9974, 5, 42, '179128776.399'),
(9975, 5, 43, '268693168.199'),
(9976, 5, 44, '403039755.898'),
(9977, 5, 45, '604559637.447'),
(9978, 5, 46, '906839459.771'),
(9979, 5, 47, '1360259193.260'),
(9980, 5, 48, '2040388793.480'),
(9981, 5, 49, '3060583193.830'),
(9982, 5, 50, '4590874794.340'),
(9983, 6, 1, '17.000'),
(9984, 6, 2, '47.600'),
(9985, 6, 3, '102.680'),
(9986, 6, 4, '201.824'),
(9987, 6, 5, '380.283'),
(9988, 6, 6, '701.510'),
(9989, 6, 7, '1279.718'),
(9990, 6, 8, '2320.492'),
(9991, 6, 9, '4193.885'),
(9992, 6, 10, '7565.993'),
(9993, 6, 11, '13635.787'),
(9994, 6, 12, '24561.417'),
(9995, 6, 13, '44227.550'),
(9996, 6, 14, '79626.591'),
(9997, 6, 15, '143344.863'),
(9998, 6, 16, '258037.754'),
(9999, 6, 17, '464484.956'),
(10000, 6, 18, '836089.922'),
(10001, 6, 19, '1504978.859'),
(10002, 6, 20, '2708978.946'),
(10003, 6, 21, '4876179.103'),
(10004, 6, 22, '8777139.385'),
(10005, 6, 23, '15798867.893'),
(10006, 6, 24, '28437979.207'),
(10007, 6, 25, '51188379.573'),
(10008, 6, 26, '92139100.232'),
(10009, 6, 27, '165850397.417'),
(10010, 6, 28, '298530732.351'),
(10011, 6, 29, '537355335.232'),
(10012, 6, 30, '967239620.418'),
(10013, 6, 31, '1741031333.750'),
(10014, 6, 32, '3133856417.750'),
(10015, 6, 33, '5640941568.960'),
(10016, 6, 34, '10153694841.100'),
(10017, 6, 35, '18276650731.000'),
(10018, 6, 36, '32897971332.800'),
(10019, 6, 37, '59216348416.100'),
(10020, 6, 38, '106589427166.000'),
(10021, 6, 39, '191860968916.000'),
(10022, 6, 40, '345349744065.000'),
(10023, 6, 41, '621629539335.000'),
(10024, 6, 42, '1118933170820.000'),
(10025, 6, 43, '2014079707490.000'),
(10026, 6, 44, '3625343473500.000'),
(10027, 6, 45, '6525618252320.000'),
(10028, 6, 46, '11746112854200.000'),
(10029, 6, 47, '21143003137600.000'),
(10030, 6, 48, '38057405647600.000'),
(10031, 6, 49, '68503330165800.000'),
(10032, 6, 50, '123305994298000.000'),
(10033, 3, 1, '0.630'),
(10034, 3, 2, '1.575'),
(10035, 3, 3, '2.993'),
(10036, 3, 4, '5.119'),
(10037, 3, 5, '8.308'),
(10038, 3, 6, '13.092'),
(10039, 3, 7, '20.268'),
(10040, 3, 8, '31.032'),
(10041, 3, 9, '47.179'),
(10042, 3, 10, '71.398'),
(10043, 3, 11, '107.727'),
(10044, 3, 12, '162.220'),
(10045, 3, 13, '243.961'),
(10046, 3, 14, '366.571'),
(10047, 3, 15, '550.486'),
(10048, 3, 16, '826.359'),
(10049, 3, 17, '1240.169'),
(10050, 3, 18, '1860.884'),
(10051, 3, 19, '2791.956'),
(10052, 3, 20, '4188.563'),
(10053, 3, 21, '6283.475'),
(10054, 3, 22, '9425.843'),
(10055, 3, 23, '14139.394'),
(10056, 3, 24, '21209.721'),
(10057, 3, 25, '31815.212'),
(10058, 3, 26, '47723.448'),
(10059, 3, 27, '71585.802'),
(10060, 3, 28, '107379.333'),
(10061, 3, 29, '161069.630'),
(10062, 3, 30, '241605.075'),
(10063, 3, 31, '362408.242'),
(10064, 3, 32, '543612.993'),
(10065, 3, 33, '815420.119'),
(10066, 3, 34, '1223130.809'),
(10067, 3, 35, '1834696.844'),
(10068, 3, 36, '2752045.895'),
(10069, 3, 37, '4128069.473'),
(10070, 3, 38, '6192104.840'),
(10071, 3, 39, '9288157.890'),
(10072, 3, 40, '13932237.464'),
(10073, 3, 41, '20898356.827'),
(10074, 3, 42, '31347535.870'),
(10075, 3, 43, '47021304.435'),
(10076, 3, 44, '70531957.282'),
(10077, 3, 45, '105797936.553'),
(10078, 3, 46, '158696905.460'),
(10079, 3, 47, '238045358.820'),
(10080, 3, 48, '357068038.860'),
(10081, 3, 49, '535602058.920'),
(10082, 3, 50, '803403089.010'),
(10083, 9, 1, '1.740'),
(10084, 9, 2, '4.872'),
(10085, 9, 3, '10.510'),
(10086, 9, 4, '20.657'),
(10087, 9, 5, '38.923'),
(10088, 9, 6, '71.802'),
(10089, 9, 7, '130.983'),
(10090, 9, 8, '237.509'),
(10091, 9, 9, '429.256'),
(10092, 9, 10, '774.402'),
(10093, 9, 11, '1395.663'),
(10094, 9, 12, '2513.933'),
(10095, 9, 13, '4526.820'),
(10096, 9, 14, '8150.016'),
(10097, 9, 15, '14671.768'),
(10098, 9, 16, '26410.923'),
(10099, 9, 17, '47541.401'),
(10100, 9, 18, '85576.263'),
(10101, 9, 19, '154039.013'),
(10102, 9, 20, '277271.963'),
(10103, 9, 21, '499091.273'),
(10104, 9, 22, '898366.031'),
(10105, 9, 23, '1617060.596'),
(10106, 9, 24, '2910710.813'),
(10107, 9, 25, '5239281.203'),
(10108, 9, 26, '9430707.906'),
(10109, 9, 27, '16975275.971'),
(10110, 9, 28, '30555498.488'),
(10111, 9, 29, '54999899.018'),
(10112, 9, 30, '98999819.972'),
(10113, 9, 31, '178199677.690'),
(10114, 9, 32, '320759421.582'),
(10115, 9, 33, '577366960.587'),
(10116, 9, 34, '1039260530.800'),
(10117, 9, 35, '1870668957.180'),
(10118, 9, 36, '3367204124.660'),
(10119, 9, 37, '6060967426.120'),
(10120, 9, 38, '10909741368.800'),
(10121, 9, 39, '19637534465.500'),
(10122, 9, 40, '35347562039.600'),
(10123, 9, 41, '63625611673.100'),
(10124, 9, 42, '114526101013.000'),
(10125, 9, 43, '206146981826.000'),
(10126, 9, 44, '371064567288.000'),
(10127, 9, 45, '667916221120.000'),
(10128, 9, 46, '1202249198020.000'),
(10129, 9, 47, '2164048556430.000'),
(10130, 9, 48, '3895287401580.000'),
(10131, 9, 49, '7011517322850.000'),
(10132, 9, 50, '12620731181100.000'),
(10133, 10, 1, '1.820'),
(10134, 10, 2, '5.096'),
(10135, 10, 3, '10.993'),
(10136, 10, 4, '21.607'),
(10137, 10, 5, '40.713'),
(10138, 10, 6, '75.103'),
(10139, 10, 7, '137.005'),
(10140, 10, 8, '248.429'),
(10141, 10, 9, '448.992'),
(10142, 10, 10, '810.006'),
(10143, 10, 11, '1459.831'),
(10144, 10, 12, '2629.516'),
(10145, 10, 13, '4734.950'),
(10146, 10, 14, '8524.729'),
(10147, 10, 15, '15346.332'),
(10148, 10, 16, '27625.218'),
(10149, 10, 17, '49727.213'),
(10150, 10, 18, '89510.803'),
(10151, 10, 19, '161121.266'),
(10152, 10, 20, '290020.099'),
(10153, 10, 21, '522037.998'),
(10154, 10, 22, '939670.217'),
(10155, 10, 23, '1691408.210'),
(10156, 10, 24, '3044536.597'),
(10157, 10, 25, '5480167.695'),
(10158, 10, 26, '9864303.672'),
(10159, 10, 27, '17755748.429'),
(10160, 10, 28, '31960348.993'),
(10161, 10, 29, '57528630.007'),
(10162, 10, 30, '103551535.833'),
(10163, 10, 31, '186392766.319'),
(10164, 10, 32, '335506981.195'),
(10165, 10, 33, '603912567.971'),
(10166, 10, 34, '1087042624.170'),
(10167, 10, 35, '1956676725.320'),
(10168, 10, 36, '3522018107.400'),
(10169, 10, 37, '6339632595.140'),
(10170, 10, 38, '11411338673.100'),
(10171, 10, 39, '20540409613.300'),
(10172, 10, 40, '36972737305.800'),
(10173, 10, 41, '66550927152.300'),
(10174, 10, 42, '119791668876.000'),
(10175, 10, 43, '215625003979.000'),
(10176, 10, 44, '388125007163.000'),
(10177, 10, 45, '698625012896.000'),
(10178, 10, 46, '1257525023210.000'),
(10179, 10, 47, '2263545041790.000'),
(10180, 10, 48, '4074381075220.000'),
(10181, 10, 49, '7333885935400.000'),
(10182, 10, 50, '13200994683700.000'),
(10183, 11, 1, '3.300'),
(10184, 11, 2, '8.580'),
(10185, 11, 3, '17.028'),
(10186, 11, 4, '30.545'),
(10187, 11, 5, '52.172'),
(10188, 11, 6, '86.775'),
(10189, 11, 7, '142.140'),
(10190, 11, 8, '230.723'),
(10191, 11, 9, '372.457'),
(10192, 11, 10, '599.231'),
(10193, 11, 11, '962.070'),
(10194, 11, 12, '1542.612'),
(10195, 11, 13, '2471.480'),
(10196, 11, 14, '3957.668'),
(10197, 11, 15, '6335.568'),
(10198, 11, 16, '10140.209'),
(10199, 11, 17, '16227.635'),
(10200, 11, 18, '25967.516'),
(10201, 11, 19, '41551.325'),
(10202, 11, 20, '66485.420'),
(10203, 11, 21, '106379.972'),
(10204, 11, 22, '170211.255'),
(10205, 11, 23, '272341.309'),
(10206, 11, 24, '435749.394'),
(10207, 11, 25, '697202.330'),
(10208, 11, 26, '1115527.028'),
(10209, 11, 27, '1784846.545'),
(10210, 11, 28, '2855757.772'),
(10211, 11, 29, '4569215.736'),
(10212, 11, 30, '7310748.477'),
(10213, 11, 31, '11697200.863'),
(10214, 11, 32, '18715524.681'),
(10215, 11, 33, '29944842.789'),
(10216, 11, 34, '47911751.763'),
(10217, 11, 35, '76658806.120'),
(10218, 11, 36, '122654093.092'),
(10219, 11, 37, '196246552.247'),
(10220, 11, 38, '313994486.895'),
(10221, 11, 39, '502391182.332'),
(10222, 11, 40, '803825895.032'),
(10223, 11, 41, '1286121435.350'),
(10224, 11, 42, '2057794299.860'),
(10225, 11, 43, '3292470883.080'),
(10226, 11, 44, '5267953416.230'),
(10227, 11, 45, '8428725469.260'),
(10228, 11, 46, '13485960754.100'),
(10229, 11, 47, '21577537209.900'),
(10230, 11, 48, '34524059539.100'),
(10231, 11, 49, '55238495265.900'),
(10232, 11, 50, '88381592428.700'),
(10233, 12, 1, '42.000'),
(10234, 12, 2, '121.800'),
(10235, 12, 3, '273.420'),
(10236, 12, 4, '561.498'),
(10237, 12, 5, '1108.846'),
(10238, 12, 6, '2148.808'),
(10239, 12, 7, '4124.735'),
(10240, 12, 8, '7878.996'),
(10241, 12, 9, '15012.093'),
(10242, 12, 10, '28564.976'),
(10243, 12, 11, '54315.454'),
(10244, 12, 12, '103241.363'),
(10245, 12, 13, '196200.589'),
(10246, 12, 14, '372823.120'),
(10247, 12, 15, '708405.928'),
(10248, 12, 16, '1346013.263'),
(10249, 12, 17, '2557467.200'),
(10250, 12, 18, '4859229.681'),
(10251, 12, 19, '9232578.393'),
(10252, 12, 20, '17541940.947'),
(10253, 12, 21, '33329729.799'),
(10254, 12, 22, '63326528.618'),
(10255, 12, 23, '120320446.374'),
(10256, 12, 24, '228608890.112'),
(10257, 12, 25, '434356933.212'),
(10258, 12, 26, '825278215.103'),
(10259, 12, 27, '1568028650.690'),
(10260, 12, 28, '2979254478.320'),
(10261, 12, 29, '5660583550.810'),
(10262, 12, 30, '10755108788.500'),
(10263, 12, 31, '20434706740.200'),
(10264, 12, 32, '38825942848.400'),
(10265, 12, 33, '73769291454.000'),
(10266, 12, 34, '140161653805.000'),
(10267, 12, 35, '266307142271.000'),
(10268, 12, 36, '505983570356.000'),
(10269, 12, 37, '961368783719.000'),
(10270, 12, 38, '1826600689110.000'),
(10271, 12, 39, '3470541309350.000'),
(10272, 12, 40, '6594028487800.000'),
(10273, 12, 41, '12528654126900.000'),
(10274, 12, 42, '23804442841100.000'),
(10275, 12, 43, '45228441398100.000'),
(10276, 12, 44, '85934038656400.000'),
(10277, 12, 45, '163274673447000.000'),
(10278, 12, 46, '310221879550000.000'),
(10279, 12, 47, '589421571145000.000'),
(10280, 12, 48, '1119900985180000.000'),
(10281, 12, 49, '2127811871830000.000'),
(10282, 12, 50, '4042842556480000.000'),
(10283, 13, 1, '27.000'),
(10284, 13, 2, '78.300'),
(10285, 13, 3, '175.770'),
(10286, 13, 4, '360.963'),
(10287, 13, 5, '712.830'),
(10288, 13, 6, '1381.376'),
(10289, 13, 7, '2651.615'),
(10290, 13, 8, '5065.069'),
(10291, 13, 9, '9650.631'),
(10292, 13, 10, '18363.199'),
(10293, 13, 11, '34917.078'),
(10294, 13, 12, '66369.448'),
(10295, 13, 13, '126128.950'),
(10296, 13, 14, '239672.006'),
(10297, 13, 15, '455403.811'),
(10298, 13, 16, '865294.241'),
(10299, 13, 17, '1644086.057'),
(10300, 13, 18, '3123790.509'),
(10301, 13, 19, '5935228.967'),
(10302, 13, 20, '11276962.037'),
(10303, 13, 21, '21426254.871'),
(10304, 13, 22, '40709911.255'),
(10305, 13, 23, '77348858.384'),
(10306, 13, 24, '146962857.929'),
(10307, 13, 25, '279229457.065'),
(10308, 13, 26, '530535995.423'),
(10309, 13, 27, '1008018418.300'),
(10310, 13, 28, '1915235021.780'),
(10311, 13, 29, '3638946568.380'),
(10312, 13, 30, '6913998506.920'),
(10313, 13, 31, '13136597190.100'),
(10314, 13, 32, '24959534688.300'),
(10315, 13, 33, '47423115934.700'),
(10316, 13, 34, '90103920302.900'),
(10317, 13, 35, '171197448603.000'),
(10318, 13, 36, '325275152372.000'),
(10319, 13, 37, '618022789534.000'),
(10320, 13, 38, '1174243300140.000'),
(10321, 13, 39, '2231062270290.000'),
(10322, 13, 40, '4239018313590.000'),
(10323, 13, 41, '8054134795840.000'),
(10324, 13, 42, '15302856112100.000'),
(10325, 13, 43, '29075426613100.000'),
(10326, 13, 44, '55243310564900.000'),
(10327, 13, 45, '104962290073000.000'),
(10328, 13, 46, '199428351139000.000'),
(10329, 13, 47, '378913867165000.000'),
(10330, 13, 48, '719936347613000.000'),
(10331, 13, 49, '1367879060460000.000'),
(10332, 13, 50, '2598970214880000.000'),
(10333, 14, 1, '63.073'),
(10334, 14, 2, '145.068'),
(10335, 14, 3, '251.661'),
(10336, 14, 4, '390.233'),
(10337, 14, 5, '570.375'),
(10338, 14, 6, '804.561'),
(10339, 14, 7, '1109.002'),
(10340, 14, 8, '1504.776'),
(10341, 14, 9, '2019.282'),
(10342, 14, 10, '2688.140'),
(10343, 14, 11, '3557.654'),
(10344, 14, 12, '4688.024'),
(10345, 14, 13, '6157.504'),
(10346, 14, 14, '8067.828'),
(10347, 14, 15, '10551.249'),
(10348, 14, 16, '13779.697'),
(10349, 14, 17, '17976.679'),
(10350, 14, 18, '23432.756'),
(10351, 14, 19, '30525.656'),
(10352, 14, 20, '39746.426'),
(10353, 14, 21, '51733.427'),
(10354, 14, 22, '67316.528'),
(10355, 14, 23, '87574.559'),
(10356, 14, 24, '113910.000'),
(10357, 14, 25, '148146.073'),
(10358, 14, 26, '192652.968'),
(10359, 14, 27, '250511.931'),
(10360, 14, 28, '325728.584'),
(10361, 14, 29, '423510.232'),
(10362, 14, 30, '550626.374'),
(10363, 14, 31, '715877.359'),
(10364, 14, 32, '930703.640'),
(10365, 14, 33, '1209977.805'),
(10366, 14, 34, '1573034.220'),
(10367, 14, 35, '2045007.559'),
(10368, 14, 36, '2658572.899'),
(10369, 14, 37, '3456207.842'),
(10370, 14, 38, '4493133.268'),
(10371, 14, 39, '5841136.321'),
(10372, 14, 40, '7593540.290'),
(10373, 14, 41, '9871665.450'),
(10374, 14, 42, '12833228.158'),
(10375, 14, 43, '16683259.679'),
(10376, 14, 44, '21688300.656'),
(10377, 14, 45, '28194853.925'),
(10378, 14, 46, '36653373.176'),
(10379, 14, 47, '47649448.202'),
(10380, 14, 48, '61944345.735'),
(10381, 14, 49, '80527712.528'),
(10382, 14, 50, '104686089.360'),
(10383, 15, 1, '60.000'),
(10384, 15, 2, '165.000'),
(10385, 15, 3, '348.750'),
(10386, 15, 4, '670.313'),
(10387, 15, 5, '1233.047'),
(10388, 15, 6, '2217.832'),
(10389, 15, 7, '3941.206'),
(10390, 15, 8, '6957.111'),
(10391, 15, 9, '12234.944'),
(10392, 15, 10, '21471.151'),
(10393, 15, 11, '37634.515'),
(10394, 15, 12, '65920.401'),
(10395, 15, 13, '115420.701'),
(10396, 15, 14, '202046.227'),
(10397, 15, 15, '353640.897'),
(10398, 15, 16, '618931.569'),
(10399, 15, 17, '1083190.246'),
(10400, 15, 18, '1895642.931'),
(10401, 15, 19, '3317435.129'),
(10402, 15, 20, '5805571.475'),
(10403, 15, 21, '10159810.082'),
(10404, 15, 22, '17779727.643'),
(10405, 15, 23, '31114583.375'),
(10406, 15, 24, '54450580.906'),
(10407, 15, 25, '95288576.586'),
(10408, 15, 26, '166755069.025'),
(10409, 15, 27, '291821430.794'),
(10410, 15, 28, '510687563.890'),
(10411, 15, 29, '893703296.807'),
(10412, 15, 30, '1563980829.410'),
(10413, 15, 31, '2736966511.470'),
(10414, 15, 32, '4789691455.070'),
(10415, 15, 33, '8381960106.380'),
(10416, 15, 34, '14668430246.200'),
(10417, 15, 35, '25669752990.800'),
(10418, 15, 36, '44922067793.900'),
(10419, 15, 37, '78613618699.300'),
(10420, 15, 38, '137573832784.000'),
(10421, 15, 39, '240754207432.000'),
(10422, 15, 40, '421319863065.000'),
(10423, 15, 41, '737309760424.000'),
(10424, 15, 42, '1290292080800.000'),
(10425, 15, 43, '2258011141460.000'),
(10426, 15, 44, '3951519497620.000'),
(10427, 15, 45, '6915159120900.000'),
(10428, 15, 46, '12101528461600.000'),
(10429, 15, 47, '21177674807900.000'),
(10430, 15, 48, '37060930913900.000'),
(10431, 15, 49, '64856629099400.000'),
(10432, 15, 50, '113499100924000.000'),
(10433, 16, 1, '5.000'),
(10434, 16, 2, '12.500'),
(10435, 16, 3, '23.750'),
(10436, 16, 4, '40.625'),
(10437, 16, 5, '65.938'),
(10438, 16, 6, '103.906'),
(10439, 16, 7, '160.859'),
(10440, 16, 8, '246.289'),
(10441, 16, 9, '374.434'),
(10442, 16, 10, '566.650'),
(10443, 16, 11, '854.976'),
(10444, 16, 12, '1287.463'),
(10445, 16, 13, '1936.195'),
(10446, 16, 14, '2909.293'),
(10447, 16, 15, '4368.939'),
(10448, 16, 16, '6558.408'),
(10449, 16, 17, '9842.613'),
(10450, 16, 18, '14768.919'),
(10451, 16, 19, '22158.378'),
(10452, 16, 20, '33242.567'),
(10453, 16, 21, '49868.851'),
(10454, 16, 22, '74808.276'),
(10455, 16, 23, '112217.415'),
(10456, 16, 24, '168331.122'),
(10457, 16, 25, '252501.683'),
(10458, 16, 26, '378757.524'),
(10459, 16, 27, '568141.287'),
(10460, 16, 28, '852216.930'),
(10461, 16, 29, '1278330.395'),
(10462, 16, 30, '1917500.592'),
(10463, 16, 31, '2876255.888'),
(10464, 16, 32, '4314388.833'),
(10465, 16, 33, '6471588.249'),
(10466, 16, 34, '9707387.374'),
(10467, 16, 35, '14561086.061'),
(10468, 16, 36, '21841634.091'),
(10469, 16, 37, '32762456.136'),
(10470, 16, 38, '49143689.204'),
(10471, 16, 39, '73715538.806'),
(10472, 16, 40, '110573313.209'),
(10473, 16, 41, '165859974.814'),
(10474, 16, 42, '248789967.221'),
(10475, 16, 43, '373184955.832'),
(10476, 16, 44, '559777438.748'),
(10477, 16, 45, '839666163.121'),
(10478, 16, 46, '1259499249.680'),
(10479, 16, 47, '1889248879.520'),
(10480, 16, 48, '2833873324.280'),
(10481, 16, 49, '4250809991.430'),
(10482, 16, 50, '6376214992.140'),
(10483, 17, 1, '55.000'),
(10484, 17, 2, '148.500'),
(10485, 17, 3, '307.450'),
(10486, 17, 4, '577.665'),
(10487, 17, 5, '1037.031'),
(10488, 17, 6, '1817.952'),
(10489, 17, 7, '3145.518'),
(10490, 17, 8, '5402.381'),
(10491, 17, 9, '9239.047'),
(10492, 17, 10, '15761.381'),
(10493, 17, 11, '26849.347'),
(10494, 17, 12, '45698.890'),
(10495, 17, 13, '77743.113'),
(10496, 17, 14, '132218.292'),
(10497, 17, 15, '224826.097'),
(10498, 17, 16, '382259.365'),
(10499, 17, 17, '649895.920'),
(10500, 17, 18, '1104878.064'),
(10501, 17, 19, '1878347.709'),
(10502, 17, 20, '3193246.105'),
(10503, 17, 21, '5428573.379'),
(10504, 17, 22, '9228629.744'),
(10505, 17, 23, '15688725.565'),
(10506, 17, 24, '26670888.460'),
(10507, 17, 25, '45340565.383'),
(10508, 17, 26, '77079016.151'),
(10509, 17, 27, '131034382.456'),
(10510, 17, 28, '222758505.175'),
(10511, 17, 29, '378689513.798'),
(10512, 17, 30, '643772228.457'),
(10513, 17, 31, '1094412843.380'),
(10514, 17, 32, '1860501888.740'),
(10515, 17, 33, '3162853265.860'),
(10516, 17, 34, '5376850606.960'),
(10517, 17, 35, '9140646086.830'),
(10518, 17, 36, '15539098402.600'),
(10519, 17, 37, '26416467339.400'),
(10520, 17, 38, '44907994532.100'),
(10521, 17, 39, '76343590759.500'),
(10522, 17, 40, '129784104346.000'),
(10523, 17, 41, '220632977443.000'),
(10524, 17, 42, '375076061709.000'),
(10525, 17, 43, '637629304960.000'),
(10526, 17, 44, '1083969818490.000'),
(10527, 17, 45, '1842748691480.000'),
(10528, 17, 46, '3132672775580.000'),
(10529, 17, 47, '5325543718530.000'),
(10530, 17, 48, '9053424321560.000'),
(10531, 17, 49, '15390821346700.000'),
(10532, 17, 50, '26164396289500.000'),
(10533, 18, 1, '45.000'),
(10534, 18, 2, '112.500'),
(10535, 18, 3, '213.750'),
(10536, 18, 4, '365.625'),
(10537, 18, 5, '593.438'),
(10538, 18, 6, '935.156'),
(10539, 18, 7, '1447.734'),
(10540, 18, 8, '2216.602'),
(10541, 18, 9, '3369.902'),
(10542, 18, 10, '5099.854'),
(10543, 18, 11, '7694.780'),
(10544, 18, 12, '11587.170'),
(10545, 18, 13, '17425.756'),
(10546, 18, 14, '26183.633'),
(10547, 18, 15, '39320.450'),
(10548, 18, 16, '59025.675'),
(10549, 18, 17, '88583.513'),
(10550, 18, 18, '132920.269'),
(10551, 18, 19, '199425.404'),
(10552, 18, 20, '299183.106'),
(10553, 18, 21, '448819.659'),
(10554, 18, 22, '673274.488'),
(10555, 18, 23, '1009956.732'),
(10556, 18, 24, '1514980.098'),
(10557, 18, 25, '2272515.146'),
(10558, 18, 26, '3408817.720'),
(10559, 18, 27, '5113271.580'),
(10560, 18, 28, '7669952.369'),
(10561, 18, 29, '11504973.554'),
(10562, 18, 30, '17257505.331'),
(10563, 18, 31, '25886302.996'),
(10564, 18, 32, '38829499.495'),
(10565, 18, 33, '58244294.242'),
(10566, 18, 34, '87366486.363'),
(10567, 18, 35, '131049774.544'),
(10568, 18, 36, '196574706.817'),
(10569, 18, 37, '294862105.225'),
(10570, 18, 38, '442293202.838'),
(10571, 18, 39, '663439849.256'),
(10572, 18, 40, '995159818.885'),
(10573, 18, 41, '1492739773.330'),
(10574, 18, 42, '2239109704.990'),
(10575, 18, 43, '3358664602.490'),
(10576, 18, 44, '5037996948.730'),
(10577, 18, 45, '7556995468.090'),
(10578, 18, 46, '11335493247.100'),
(10579, 18, 47, '17003239915.700'),
(10580, 18, 48, '25504859918.600'),
(10581, 18, 49, '38257289922.800'),
(10582, 18, 50, '57385934929.300'),
(10583, 19, 1, '77.500'),
(10584, 19, 2, '224.750'),
(10585, 19, 3, '504.525'),
(10586, 19, 4, '1036.098'),
(10587, 19, 5, '2046.085'),
(10588, 19, 6, '3965.062'),
(10589, 19, 7, '7611.118'),
(10590, 19, 8, '14538.624'),
(10591, 19, 9, '27700.885'),
(10592, 19, 10, '52709.182'),
(10593, 19, 11, '100224.945'),
(10594, 19, 12, '190504.896'),
(10595, 19, 13, '362036.802'),
(10596, 19, 14, '687947.424'),
(10597, 19, 15, '1307177.605'),
(10598, 19, 16, '2483714.950'),
(10599, 19, 17, '4719135.905'),
(10600, 19, 18, '8966435.720'),
(10601, 19, 19, '17036305.368'),
(10602, 19, 20, '32369057.700'),
(10603, 19, 21, '61501287.129'),
(10604, 19, 22, '116852523.045'),
(10605, 19, 23, '222019871.286'),
(10606, 19, 24, '421837832.944'),
(10607, 19, 25, '801491960.093'),
(10608, 19, 26, '1522834801.680'),
(10609, 19, 27, '2893386200.690'),
(10610, 19, 28, '5497433858.810'),
(10611, 19, 29, '10445124409.200'),
(10612, 19, 30, '19845736455.000'),
(10613, 19, 31, '37706899342.100'),
(10614, 19, 32, '71643108827.400'),
(10615, 19, 33, '136121906850.000'),
(10616, 19, 34, '258631623092.000'),
(10617, 19, 35, '491400083952.000'),
(10618, 19, 36, '933660159586.000'),
(10619, 19, 37, '1773954303290.000'),
(10620, 19, 38, '3370513176330.000'),
(10621, 19, 39, '6403975035110.000'),
(10622, 19, 40, '12167552566800.000'),
(10623, 19, 41, '23118349877000.000'),
(10624, 19, 42, '43924864766300.000'),
(10625, 19, 43, '83457243056000.000'),
(10626, 19, 44, '158568761807000.000'),
(10627, 19, 45, '301280647432000.000'),
(10628, 19, 46, '572433230122000.000'),
(10629, 19, 47, '1087623137230000.000'),
(10630, 19, 48, '2066483960740000.000'),
(10631, 19, 49, '3926319525410000.000'),
(10632, 19, 50, '7460007098270000.000'),
(10633, 20, 1, '18.000'),
(10634, 20, 2, '48.600'),
(10635, 20, 3, '100.620'),
(10636, 20, 4, '189.054'),
(10637, 20, 5, '339.392'),
(10638, 20, 6, '594.966'),
(10639, 20, 7, '1029.442'),
(10640, 20, 8, '1768.052'),
(10641, 20, 9, '3023.688'),
(10642, 20, 10, '5158.270'),
(10643, 20, 11, '8787.059'),
(10644, 20, 12, '14956.000'),
(10645, 20, 13, '25443.201'),
(10646, 20, 14, '43271.441'),
(10647, 20, 15, '73579.450'),
(10648, 20, 16, '125103.065'),
(10649, 20, 17, '212693.210'),
(10650, 20, 18, '361596.457'),
(10651, 20, 19, '614731.977'),
(10652, 20, 20, '1045062.362'),
(10653, 20, 21, '1776624.015'),
(10654, 20, 22, '3020278.825'),
(10655, 20, 23, '5134492.003'),
(10656, 20, 24, '8728654.405'),
(10657, 20, 25, '14838730.489'),
(10658, 20, 26, '25225859.831'),
(10659, 20, 27, '42883979.713'),
(10660, 20, 28, '72902783.512'),
(10661, 20, 29, '123934749.970'),
(10662, 20, 30, '210689092.950'),
(10663, 20, 31, '358171476.014'),
(10664, 20, 32, '608891527.224'),
(10665, 20, 33, '1035115614.280'),
(10666, 20, 34, '1759696562.280'),
(10667, 20, 35, '2991484173.870'),
(10668, 20, 36, '5085523113.580'),
(10669, 20, 37, '8645389311.090'),
(10670, 20, 38, '14697161846.900'),
(10671, 20, 39, '24985175157.700'),
(10672, 20, 40, '42474797786.000'),
(10673, 20, 41, '72207156254.200'),
(10674, 20, 42, '122752165650.000'),
(10675, 20, 43, '208678681623.000'),
(10676, 20, 44, '354753758778.000'),
(10677, 20, 45, '603081389940.000'),
(10678, 20, 46, '1025238362920.000'),
(10679, 20, 47, '1742905216970.000'),
(10680, 20, 48, '2962938868880.000'),
(10681, 20, 49, '5036996077110.000'),
(10682, 20, 50, '8562893331100.000'),
(10683, 21, 1, '4.800'),
(10684, 21, 2, '13.440'),
(10685, 21, 3, '28.992'),
(10686, 21, 4, '56.986'),
(10687, 21, 5, '107.374'),
(10688, 21, 6, '198.073'),
(10689, 21, 7, '361.332'),
(10690, 21, 8, '655.198'),
(10691, 21, 9, '1184.156'),
(10692, 21, 10, '2136.280'),
(10693, 21, 11, '3850.105'),
(10694, 21, 12, '6934.988'),
(10695, 21, 13, '12487.779'),
(10696, 21, 14, '22482.802'),
(10697, 21, 15, '40473.844'),
(10698, 21, 16, '72857.719'),
(10699, 21, 17, '131148.694'),
(10700, 21, 18, '236072.448'),
(10701, 21, 19, '424935.207'),
(10702, 21, 20, '764888.173'),
(10703, 21, 21, '1376803.511'),
(10704, 21, 22, '2478251.120'),
(10705, 21, 23, '4460856.817'),
(10706, 21, 24, '8029547.070'),
(10707, 21, 25, '14453189.527'),
(10708, 21, 26, '26015745.948'),
(10709, 21, 27, '46828347.506'),
(10710, 21, 28, '84291030.311'),
(10711, 21, 29, '151723859.360'),
(10712, 21, 30, '273102951.647'),
(10713, 21, 31, '491585317.765'),
(10714, 21, 32, '884853576.778'),
(10715, 21, 33, '1592736443.000'),
(10716, 21, 34, '2866925602.200'),
(10717, 21, 35, '5160466088.760'),
(10718, 21, 36, '9288838964.570'),
(10719, 21, 37, '16719910141.000'),
(10720, 21, 38, '30095838258.600'),
(10721, 21, 39, '54172508870.300'),
(10722, 21, 40, '97510515971.400'),
(10723, 21, 41, '175518928753.000'),
(10724, 21, 42, '315934071761.000'),
(10725, 21, 43, '568681329174.000'),
(10726, 21, 44, '1023626392520.000'),
(10727, 21, 45, '1842527506540.000'),
(10728, 21, 46, '3316549511770.000'),
(10729, 21, 47, '5969789121200.000'),
(10730, 21, 48, '10745620418200.000'),
(10731, 21, 49, '19342116752700.000'),
(10732, 21, 50, '34815810154900.000'),
(10733, 22, 1, '935.000'),
(10734, 23, 1, '258000.000'),
(10735, 23, 2, '619200.000'),
(10736, 23, 3, '1124880.000'),
(10737, 23, 4, '1832832.000'),
(10738, 23, 5, '2823964.800'),
(10739, 23, 6, '4211550.720'),
(10740, 23, 7, '6154171.008'),
(10741, 23, 8, '8873839.411'),
(10742, 24, 1, '510.000'),
(10743, 24, 2, '1122.000'),
(10744, 24, 3, '1856.400'),
(10745, 24, 4, '2737.680'),
(10746, 24, 5, '3795.216'),
(10747, 24, 6, '5064.259'),
(10748, 24, 7, '6587.111'),
(10749, 24, 8, '8414.533'),
(10750, 24, 9, '10607.440'),
(10751, 24, 10, '13238.928'),
(10752, 25, 1, '8.500'),
(10753, 25, 2, '19.550'),
(10754, 25, 3, '33.915'),
(10755, 25, 4, '52.590'),
(10756, 25, 5, '76.866'),
(10757, 25, 6, '108.426'),
(10758, 25, 7, '149.454'),
(10759, 25, 8, '202.790'),
(10760, 25, 9, '272.127'),
(10761, 25, 10, '362.266'),
(10762, 25, 11, '479.445'),
(10763, 25, 12, '631.779'),
(10764, 25, 13, '829.813'),
(10765, 25, 14, '1087.257'),
(10766, 25, 15, '1421.934');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tech_requirements`
--

CREATE TABLE IF NOT EXISTS `tech_requirements` (
`id` int(10) unsigned NOT NULL,
  `obj_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_level` tinyint(3) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=48 ;

--
-- Daten für Tabelle `tech_requirements`
--

INSERT INTO `tech_requirements` (`id`, `obj_id`, `req_building_id`, `req_tech_id`, `req_level`) VALUES
(1, 3, 8, 0, 3),
(2, 4, 8, 0, 4),
(3, 4, 9, 0, 2),
(4, 5, 8, 0, 5),
(5, 5, 9, 0, 4),
(6, 6, 8, 0, 8),
(7, 6, 11, 0, 6),
(8, 6, 9, 0, 6),
(9, 7, 8, 0, 4),
(10, 8, 8, 0, 3),
(11, 9, 8, 0, 4),
(12, 10, 8, 0, 4),
(17, 12, 8, 0, 7),
(18, 12, 0, 3, 5),
(19, 13, 11, 0, 4),
(20, 13, 0, 5, 2),
(21, 14, 0, 4, 6),
(22, 14, 8, 0, 6),
(23, 11, 8, 0, 5),
(24, 11, 0, 7, 6),
(25, 15, 8, 0, 8),
(26, 17, 8, 0, 8),
(27, 20, 9, 0, 5),
(28, 20, 8, 0, 4),
(29, 18, 8, 0, 8),
(30, 19, 8, 0, 8),
(31, 16, 8, 0, 5),
(32, 21, 8, 0, 6),
(33, 21, 9, 0, 5),
(34, 21, 0, 3, 6),
(35, 22, 8, 0, 10),
(36, 22, 0, 6, 9),
(37, 22, 0, 3, 10),
(38, 22, 0, 10, 11),
(39, 23, 8, 0, 12),
(40, 23, 7, 0, 15),
(41, 24, 8, 0, 10),
(42, 24, 0, 3, 9),
(44, 24, 0, 4, 10),
(45, 24, 0, 14, 10),
(46, 25, 0, 3, 5),
(47, 25, 13, 0, 6);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tech_types`
--

CREATE TABLE IF NOT EXISTS `tech_types` (
`type_id` int(10) unsigned NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `type_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type_color` char(7) NOT NULL DEFAULT '#ffffff'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `tech_types`
--

INSERT INTO `tech_types` (`type_id`, `type_name`, `type_order`, `type_color`) VALUES
(1, 'Antriebstechniken', 1, '#ffffff'),
(2, 'Kriegstechnologien', 2, '#ffffff'),
(4, 'Allgemeine Technologien', 0, '#ffffff'),
(3, 'Hi - Technologien', 3, '#ffffff');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `texts`
--

CREATE TABLE IF NOT EXISTS `texts` (
  `text_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text_content` text COLLATE utf8_unicode_ci NOT NULL,
  `text_updated` int(10) unsigned NOT NULL,
  `text_enabled` tinyint(1) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tickets`
--

CREATE TABLE IF NOT EXISTS `tickets` (
`id` int(6) unsigned zerofill NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `cat_id` tinyint(10) unsigned NOT NULL DEFAULT '1',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `c_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `status` enum('new','assigned','closed') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'new',
  `solution` enum('open','solved','duplicate','invalid') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'open',
  `admin_comment` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ticket_cat`
--

CREATE TABLE IF NOT EXISTS `ticket_cat` (
`id` smallint(5) unsigned NOT NULL,
  `name` varchar(80) NOT NULL,
  `sort` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Daten für Tabelle `ticket_cat`
--

INSERT INTO `ticket_cat` (`id`, `name`, `sort`) VALUES
(1, 'Beleidigung in Nachricht', 0),
(2, 'Rathaus-Missbrauch', 1),
(3, 'Missachtung der Angriffsregeln', 2),
(4, 'Pushing-Verdach', 3),
(5, 'Cheat-Verdach', 4),
(6, 'Bugusing-Verdacht', 5),
(7, 'Anstössiges Bild', 6),
(8, 'Sonstiger Regelverstoss', 7),
(9, 'Änderung meiner fixen E-Mail-Adresse', 9),
(10, 'Änderung meines Namens (Accountübergabe)', 10),
(11, 'Probleme mit einer Flotte (Ungültige Koordinaten, hängenbleibende Flotte)', 11),
(12, 'Problem mit der Allianz (Ränge, Forum, Bündnisse, Auslösung etc)', 12),
(15, 'Probleme mit den Account-Einstellungen (Design, Urlaubsmodus etc)', 13),
(14, 'Anderes Problem', 20),
(16, 'Verdacht auf Accounthacking', 14),
(17, 'Probleme mit meinem Passwort', 15);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ticket_msg`
--

CREATE TABLE IF NOT EXISTS `ticket_msg` (
`id` int(10) unsigned NOT NULL,
  `ticket_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tips`
--

CREATE TABLE IF NOT EXISTS `tips` (
`tip_id` int(10) unsigned NOT NULL,
  `tip_text` text COLLATE utf8_unicode_ci NOT NULL,
  `tip_active` tinyint(1) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `tips`
--

INSERT INTO `tips` (`tip_id`, `tip_text`, `tip_active`) VALUES
(1, 'Gib niemals dein Passwort an andere Leute, auch nicht an Moderatoren und Admins. Logge dich nur über www.etoa.ch ein und niemals über eine andere Seite. Akzeptiere keine Dateien von fremden Spielern und sorge dafür, dass dein Passwort sicher ist und niemand Zugriff auf deinen Account bekommt.', 1),
(2, 'Gründet Allianzen oder schliesst euch einer bestehen Allianz an, um gemeinsam gegen Feinde zu kämpfen und spezielle Allianzgebäude und -schiffe bauen zu können.', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tutorial`
--

CREATE TABLE IF NOT EXISTS `tutorial` (
`tutorial_id` int(10) unsigned NOT NULL,
  `tutorial_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `tutorial`
--

INSERT INTO `tutorial` (`tutorial_id`, `tutorial_title`) VALUES
(1, 'Rassenauswahl'),
(2, 'Bauweise');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tutorial_texts`
--

CREATE TABLE IF NOT EXISTS `tutorial_texts` (
`text_id` int(10) unsigned NOT NULL,
  `text_tutorial_id` int(11) NOT NULL,
  `text_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text_content` text COLLATE utf8_unicode_ci NOT NULL,
  `text_step` tinyint(2) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=19 ;

--
-- Daten für Tabelle `tutorial_texts`
--

INSERT INTO `tutorial_texts` (`text_id`, `text_tutorial_id`, `text_title`, `text_content`, `text_step`) VALUES
(1, 1, 'Willkommen', 'Willkommen, werter neuer Imperator in den Galaxien Andromedas!\r\n\r\nDer Grundgedanke des Spieles (wie bei fast allen Aufbau-BG´s) liegt darin, durch Rohstoffe Gebäude und Schiffe zu bauen. Bei EtoA jedoch sind die Möglichkeiten ungleich größer und vielfältiger. Dieses Tutorial soll euch ein wenig Entscheidungshilfe geben, erfolgreich den Wirren des Universums zu trotzen.\r\n\r\nDie Gliederung ist wie folgt:\r\n\r\n- Einführung in die Rohstoffe\r\n- Einführung in grundsätzliche Spielweisen\r\n- Entscheidungshilfe zu den Rassen\r\n- Entscheidungshilfe zu den Planeten\r\n- Grundsätzlies zum Aufbau deines Startplaneten\r\n\r\n', 0),
(2, 1, 'Die Rohstoffe (1/2)', 'Es gibt in EtoA 5 verschiedene Arten von Rohstoffen:\r\n\r\n[list][*][b]Titan[/b], Grundstoff zum Bau von fast Allem. Wird mit fortschreitendem Spiel zur Massenware. zum Massenprodukt[*][b]Silizium[/b], für Forschung und Schiffe. Zu Beginn sehr rar, wird aber im weiteren Verlauf auch zum Massenprodukt.[*][b]PVC[/b], Man meint man hat genug, wenn man es dann braucht (vor allem für den Schiffsbau) hat man immer zu wenig.[*][b]Tritium[/b], als Treibstoff überlebensnotwendig. Aber auch zur Forschung wichtig. Ist selten reichlich vorhanden, auch im späteren Spiel keine Massenware. Gut zum Handeln.[*][b]Nahrung[/b], wichig zum schnelleren Bauen von Minen, Schiffen, Forschung und natürlich zum Verschicken von Schiffen. Denn ohne Nahrung wird kein Pilot ein Schiff besteigen.[/list]\r\nDiese Rohstoffe können grundsätzlich auf jedem Planeten produziert werden. Durch geschickte Wahl des Sternensystems und des Planeten kann die Produktion einiger Rohstoffe stark erhöht werden, allerdings meist zu Lasten eines anderen Rohstoffes. Genaueres liefert die Hilfe.', 1),
(3, 1, 'Die Rohstoffe (2/2)', 'Mit den passenden Schiffen kann man Rohstoffe auch im Weltall sammeln, grundsätzlich kann jede Rasse sammeln gehen. Einige Rassen haben jedoch Spezialschiffe, welche dafür besser geeignet sind. Auch hier hilft die Hilfe.\r\nSammeln kann man\r\n[list][*][b]Asteroiden[/b] (Titan, Silizium, PVC)[*][b]Sternennebel[/b] (Silizium)[*][b]Gasplaneten[/b] (Tritium)[*][b]Trümmerfelder[/b] durch Kämpfe (Titan, Silizium, PVC)[/list]', 2),
(4, 1, 'Die grundsätzlichen Spielweisen', 'Das Ganze ist natürlich zeitaufwendig und reicht auf keinen Fall aus um einen Account ausbauen zu können. Dies bringt uns zum nächsten Teil des Tutorials:\r\n\r\nEs gibt drei grundsätzliche Spielweisen: der Miner, der Fleeter und der Händler.\r\n\r\nNatürlich kann (und wird wohl) man einen Mix spielen, je nachdem wieviel Zeit man hat oder wo die eigenen Ziele liegen.', 3),
(18, 1, 'Sterne und Planeten (2/2)', 'Grundsätzlich zu empfehlen ist ein gelber Stern, da er folgende Boni mitbringt:\r\n\r\n+35% Titan\r\n+30% Silizium\r\n+10% PVC\r\n\r\nDazu nehmen wir evtl. einen Eisplaneten:\r\n\r\n+10% Titan\r\n+30% Silizium\r\n+25% PVC\r\n+30% Tritium\r\n\r\nDie Kombination Gelb/Eis ergibt somit:\r\n\r\n+45% Titan\r\n+60% Silizium\r\n+35% PVC\r\n+30% Tritium\r\n\r\nDamit läßt sich zu Beginn ganz gut leben. Nicht verschweigen darf man jedoch den Malus von -25% auf Nahrung und die um 10% erhöhte Bauzeit. Zu Beginn sind diese Werte vernachlässigbar, im Laufe des Spieles kann das ganz anders aussehen.\r\nFerner kann man durch die Wahl der Rasse die Werte ebenfalls noch verändern. Nimmt man zb den Cardassianer mit +60% Nahrung bekommt man einen Bonus von +35% Nahrung bei obiger Kombo. Allerdings auch 10% weniger an Titan/Silizium. Und man hat die Mali/Boni der Rasse dann bei jedem Planeten.\r\n', 9),
(5, 1, 'Die Wahl der Rasse', 'Sofern ihr euch jetzt in einem der Profile wiedergefunden habt solltet ihr euch jetzt der Rassenwahl zuwenden.\r\n\r\nEs gibt in EtoA zehn verschiedene Rassen, alle haben einen Bonus oder Malus auf die Produktion bestimmter Rohstoffe. Eine Tabelle findest du in der Hilfe unter Rassen. Alternativ gibt es hier: LINK EINFÜGEN einen Rechner der dich dabei unterstützen kann. Ferner hat jede Rasse spezielle Schiffe, welche nur von dieser Rasse gebaut werden kann. Auch hier bitte die Hilfe aufrufen.\r\n\r\nEine kleine Entscheidungshilfe mit Beispiel:\r\n\r\nLiegt einem eher die Spielweise des Händlers sollte man eine Rasse wählen die einen Bonus auf einen eher seltenen Rohstoff hat, oder die Schiffe besitzt mit denen man effektiver im Weltall sammeln gehen kann. (Bsp. Vorgone)\r\nIst man eher der Fleeter sollte man eine Rasse wählen die schnelle/günstige oder Schiffe mit Spezialfunktionen bauen kann. (Bsp. Minbari)\r\nAls Miner ist evtl. die Rasse Serrakin interessant da sie effektive Verteidigungsanlagen bauen kann. Oder man sucht eine Rasse mit hohem Silizium/Titan Bonus.\r\nNatürlich gibt es für jeden Zweck auch andere Rassen, hier sollte jeder schauen welche Rasse ihm am besten liegt um seine Spielweise am ehesten zu unterstützen. Denn durch geschicktes Kombinieren der Rasse und der Planeten kann man sogar gute Rohstoff-Boni + besondere Schiffe bekommen. Zwar wird man nie ein Top-Fleeter mit Top-Boni bekommen, aber man kann auf jeden Fall näher herankommen.', 7),
(6, 1, 'Sterne und Planeten (1/2)', 'Das bringt uns zum nächsten Punkt für einen erfolgreichen Einstieg: Die Sterne und Planeten\r\n\r\nIn EtoA gibt es 7 Arten von Sternen und 6 Arten von Planeten. Ein Sternensystem kann verschiedene Planeten beinhalten. Dabei wird jeder Planet den Einflüssen des Sternes ausgesetzt, dh. Die Boni/Mali des Sternes werden in die Berechnung der Boni/Mali der Planeten im Sternensystem mit einbezogen.\r\nJedes Imperium kann aus max. 15 Planeten bestehen. Jeder Planet gehört dir alleine. Welche Kombination du dir im weiteren Verlauf aussuchst und besiedelst hängt von deiner Spielweise ab. So kann man zb. die Kolonien nahe beieinander legen, oder man nimmt nur eine Kombo und muss dann vll. weiter fliegen weil es diese nicht überall gibt.\r\n\r\nViel entscheidender ist die Wahl der Startkombination, auch wenn sie vielleicht nicht die Optimale für die weitere Spielweise ist. Ausgleichen kann man sie ja durch die Kolonien.\r\nGerade als Neuling solltest du eine Kombination auswählen die es dir ermöglicht zügig deinen Planeten ausbauen zu können. Boni auf Titan und Silizium sind zu Beginn sehr wichtig. Aber auch Tritium sollte nicht vergessen werden, denn ohne Treibstoff fliegt auch kein Besiedelungsschiff.\r\nEher nebensächlich sind zu Beginn Bau- bzw. Forschungs-Zeit Boni. Eine Tabelle findest du in der Hilfe. ', 8),
(7, 1, 'Deine Entscheidung!', 'Hier nochmals eine Zusammenfassung der wichtigen Fragen:\r\n[list][*]Wieviel Zeit will/kann ich aufbringen[*]Welche Spielweise liegt mir am ehesten[*]Welche Rasse wähle ich dafür (Schiffe, Ressourcen)[*]Welche Stern/Planeten-Kombination unterstützt meine Spielweise und kann evtl. Nachteile meiner Rasse ausgleichen.[/list]\r\nNeulingen ist angeraten, den Startplaneten so zu wählen das kein Malus bei Titan oder Silizium vorhanden ist. Bei der Wahl der Kolonien kann das wieder anders aussehen.\r\n\r\nViel Erfolg, mein Imperator, möge dein Reich groß und mächtig werden und lange bestehen !\r\n\r\nDein EtoA-Team.', 10),
(8, 2, 'Auf gehts!', 'Du hast deine Kombo gefunden? Dann geht es hier weiter mit einer kleinen Anleitung zu einem erfolgreichen Start.\r\n\r\nWICHTIG: Solltest du aus Versehen die falsche Kombination ausgewählt haben und hast du noch nichts darauf gebaut melde es einem Administrator. Er kann dir vll aus dieser misslichen Lage heraus helfen. Das ist auf jeden Fall besser als mit einer Kombination weiter zu spielen die schlechte Startmöglichkeiten hat.\r\n', 0),
(9, 2, 'Ressourcen', 'Grundstoffe sind [b]Titan[/b](Tit) und [b]Silizium[/b](Sil). Weiterführend kommt [b]PVC[/b] dazu, am Ende benötigt man [b]Tritium[/b](Trit). [b]Nahrung[/b](Nah) dient der Bauzeitverkürzung und wird zum Fliegen benötigt. Nebenbei benötigt jede Mine auch [b]Energie[/b]. Man muss also auch diese Sparte mit ausbauen.\r\n\r\nJede Ressource baut auf die andere auf. Ohne Tit/Sili kein pvc. Ohne pvc kein Trit. Ohne Trit keine Besiedlungsschiffe.\r\nDaher ist es gerade zu Beginn sehr wichtig mit den Ressourcen sparsam umzugehen und sie sinnvoll zu verbauen.', 1),
(10, 2, 'Die Baureihenfolge', 'Hier scheiden sich die Geister. Jeder Spieler wird eine andere Vorgehensweise haben die ihn am ehesten zu seinem Ziel führt, welches er sich zum Ziel gesetzt hat. (Profil) Auch die Boni sind ein bedeutender Faktor. Daher folgt hier eine allgemeingültige Vorgehensweise.\r\n\r\nZu Beginn habt ihr einen Grundstock an allen Ressourcen die es euch ermöglichen soweit bauen zu können, dass ihr Tit/Sil/PVC selbstständig produzieren könnt.\r\n[list][*]Zuerst Titan- und Silizium-Minen ausbauen. Wobei hier zuerst Titan, danach Sili. Sie sollten immer 2 Stufen Unterschied haben. Bsp: Titan 5, Sili 3[*]Bei 5/3 solltet ihr an die Grenze eurer Energie gestoßen sein. Daher muß jetzt ein Windkraftwerk gebaut werden. Baut dieses aber immer erst wenn die Energie nahe bei 0 ist. Das gilt auch für die weiteren Stufen.[*]Nach dem Kraftwerk sollte die PVC-Produktion um 1-2 Stufen erhöht werden, je nachdem wieviel tit/sil über ist. Ohne PVC gibt es keine Kraftwerke. Ohne Kraftwerke keine Energie. Ohne Energie wird die Produktion sämtlicher Ressourcen gedrosselt.[*]Sobald ihr Wind auf Stufe 5 habt könnt ihr mit dem Bau von Tritium-Anlagen beginnen. Dies sollte auch direkt geschehen denn ohne Trit keine Forschung, ohne Forschung keine Antriebe, ohne Antriebe kein Besiedlungsschiff. Oder aber es dauert ewig, weil ihr zu spät mit dem Ausbau von Trit begonnen habt.[*]Bewährt hat sich auch, früh die Nahrung auf Stufe 2 oder 3 zu bringen, wenn man Ress über hat. Man braucht sie zwar nicht zu Beginn, aber später kann man vll schneller forschen um sein Besiedlungsschiff zu bauen.[*]Sollte eure Bevölkerung keinen Platz mehr zum Wachsen haben, baut das Wohnmodul aus. Hier reichen aber 2 Stufen locker aus.[*]Sinnlos ist das Bauen von Speichern. Zu Beginn werdet ihr immer zuwenig als zuviel Ressourcen haben.[/list]\r\nNun sollte eure Rohstoffproduktion auf soliden Füßen stehen.', 2),
(11, 2, 'Verteidigung', 'Leider gibt es auch in EtoA Spieler, die nach euren Ressourcen trachten. Daher ist es wichtig, nach ca. 48-60h eine Verteidigungsanlage auf eurem Planeten aufgestellt zu haben. Dazu benötigt ihr eine [b]Waffenfabrik[/b] und eine [b]Spica Flakkanone[/b]. Beides muss in der Ressourcenplanung und somit beim Ausbau bedacht werden.', 3),
(12, 2, 'Schiffe', 'Sobald die Voraussetzungen für den Bau von Schiffen da sind, baut euch einen [b]AURIGA Explorer[/b], mit dem ihr die Sternenkarte aufdecken könnt. So findet ihr die passenden Kombos für euer TAURUS Besiedlungsschiff, das Ziel eurer Bemühungen.\r\nAllerdings braucht es dafür eine Schiffswerft, eine Flottenkontrolle und einen Ionenantrieb welcher in einem Forschungslabor erforscht werden muss.\r\n\r\nUm dies alles sinnvoll zu erreichen und auch um zügig [b]TAURUS[/b] bauen zu können hat sich folgender Ausbau bewährt:\r\n\r\nTitan 11(+1)\r\nSilizium 11(+1)\r\nPVC 10(+1)\r\nTritium 8(+1)\r\n\r\nJe nach Rasse/Stern/Planeten-Kombo kann das natürlich anders aussehen. So wird einem Rigelianer Sil 9 reichen, braucht aber tit 12 oder 13.', 4),
(13, 2, 'Abschluss', 'Die richtige Kombo ist gefunden? Das 1. TAURUS ist unterwegs? Hoffentlich habt ihr nicht vergessen Tit/Sili zum Bau von Minen sowie soviel PVC mitzuschicken damit ihr Wind 3 bauen könnt. Denn ohne Wind 3 keine PVC-Fabrik, ohne PVC kein Kraftwerke, ohne Kraftwerk keine Ressourcen.....ihr kennt das ja.\r\n\r\nPVC ist dabei? Dann alles Gute und viel Glück beim Vergrößern deines Einflussbereiches, werter Imperator.\r\n\r\nDein EtoA- Team', 5),
(15, 1, 'Der Miner', 'Der [b]Miner[/b] spielt sein Spiel gemütlich und lebt fast ausschließlich von seiner eigenen Ressourcen-Produktion. Nebenbei wird er auch im Weltall Ressourcen sammeln gehen. Dabei werden die Ressourcen meist in größere Minen investiert. Nur ein kleiner Teil ihrer Ressourcen wandert in Schiffe oder Verteidigung. Vor dem Miner braucht man sich grundsätzlich nicht zu fürchten, jedoch sollte man nicht denken es sind leichte Opfer, denn sie haben oft Freunde unter den 24/7 Spielern (ständig online); welche ihnen gerne helfen. Oder sie haben sich einer Allianz angeschlossen, die sie beschützen kann.\r\n\r\n[i]Zeitaufwand: Gering/Mittel[/i]', 4),
(16, 1, 'Der Fleeter', 'Der [b]Fleeter[/b] gehört zu den aggressiven Spielern in EtoA. Er deckt seinen zusätzlichen Bedarf an Ressourcen durch Raiden (Stehlen von Ressourcen) oder aber durch Kämpfe mit anderen Spielern. Dafür wandert ein Großteil seiner Produktion in Schiffe. Der Vorteil ist dabei die abschreckende Wirkung einer großen Flotte. (Der Nachteil ist, wenn die Flotte mal zerstört werden sollte ist es für ihn wesentlich schwieriger, wieder an neue Flotte zu kommen, da seine Eigen-Produktion an Ressourcen nicht ausreicht). Das Einzige was gegen den Fleeter hilft ist Ressourcen und Schiffe in Sicherheit zu bringen (Saven) oder ihm mittels einer Verteidigung welche ihm ordentliche Verluste beibringt, die Lust zu nehmen. Sollten man öfters von dem gleichen Fleeter geraidet werden so kann es durchaus von Nutzen sein, eine freundliche Anfrage zu schicken. Meist wird darauf positiv reagiert.\r\n\r\n[i]Zeitaufwand: Hoch[/i]', 5),
(17, 1, 'Der Händler', 'Der [b]Händler[/b] hat sich auf das Handeln von Waren und Schiffen spezialisiert. Er baut seine Markplätze hoch aus um immer genug große oder kleine Angebote in den Marktplatz stellen zu können. Er ist meistens ein ruhiger Geselle der sein Ressourcen-Extra mit dem Verkauf von Waren realisiert. Hier ist die Wahl der Rasse ein entscheidender Faktor. Nahrung oder Tritium oder PVC sind gern genommene Rohstoffe, auch einige Schiffe lassen sich gut verkaufen, sei es sie sind schnell oder haben eine Spezialfunktion. Mehr dazu findet ihr in der Hilfe.\r\n\r\n[i]Zeitaufwand Mittel[/i]', 6);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tutorial_user_progress`
--

CREATE TABLE IF NOT EXISTS `tutorial_user_progress` (
  `tup_user_id` int(10) unsigned NOT NULL,
  `tup_tutorial_id` int(10) unsigned NOT NULL,
  `tup_text_step` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `tup_closed` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `tutorial_user_progress`
--

INSERT INTO `tutorial_user_progress` (`tup_user_id`, `tup_tutorial_id`, `tup_text_step`, `tup_closed`) VALUES
(69, 1, 1, 0),
(105, 1, 8, 0),
(106, 1, 10, 1),
(106, 2, 5, 1),
(107, 1, 0, 1),
(107, 2, 0, 1),
(108, 1, 0, 1),
(108, 2, 0, 1),
(77, 1, 0, 1),
(77, 2, 0, 1),
(109, 1, 0, 1),
(109, 2, 0, 1),
(103, 1, 0, 1),
(103, 2, 0, 1),
(110, 1, 0, 0),
(112, 1, 0, 1),
(112, 2, 0, 1),
(113, 1, 0, 1),
(113, 2, 0, 1),
(114, 1, 0, 1),
(114, 2, 0, 1),
(115, 1, 0, 1),
(115, 2, 0, 1),
(116, 1, 0, 1),
(116, 2, 0, 0),
(117, 1, 1, 0),
(118, 1, 0, 0),
(119, 1, 0, 0),
(120, 1, 0, 1),
(120, 2, 0, 1),
(121, 1, 0, 1),
(122, 1, 10, 1),
(122, 2, 5, 1),
(123, 1, 0, 1),
(123, 2, 0, 1),
(124, 1, 0, 1),
(124, 2, 0, 1),
(125, 1, 10, 1),
(121, 2, 1, 1),
(125, 2, 0, 1),
(126, 1, 0, 1),
(126, 2, 3, 1),
(127, 1, 0, 0),
(128, 1, 0, 1),
(128, 2, 0, 1),
(129, 1, 1, 1),
(129, 2, 0, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`user_id` smallint(5) unsigned NOT NULL,
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
  `boost_bonus_production` decimal(5,2) unsigned NOT NULL DEFAULT '0.00',
  `boost_bonus_building` decimal(5,2) unsigned NOT NULL DEFAULT '0.00'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_comments`
--

CREATE TABLE IF NOT EXISTS `user_comments` (
`comment_id` int(10) unsigned NOT NULL,
  `comment_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `comment_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `comment_admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `comment_text` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Admin comments on users' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_log`
--

CREATE TABLE IF NOT EXISTS `user_log` (
`id` int(11) NOT NULL,
  `user_id` mediumint(9) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `zone` varchar(30) NOT NULL,
  `message` text NOT NULL,
  `host` varchar(50) NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '1'
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
  `minimap_user_fly_points` int(4) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_multi`
--

CREATE TABLE IF NOT EXISTS `user_multi` (
`id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `multi_id` int(10) unsigned NOT NULL DEFAULT '0',
  `connection` varchar(50) NOT NULL DEFAULT '0',
  `activ` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_onlinestats`
--

CREATE TABLE IF NOT EXISTS `user_onlinestats` (
`stats_id` int(10) unsigned NOT NULL,
  `stats_timestamp` int(10) unsigned NOT NULL,
  `stats_count` int(5) unsigned NOT NULL,
  `stats_regcount` int(5) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_points`
--

CREATE TABLE IF NOT EXISTS `user_points` (
`point_id` int(10) unsigned NOT NULL,
  `point_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `point_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `point_points` bigint(12) unsigned NOT NULL DEFAULT '0',
  `point_ship_points` bigint(12) unsigned NOT NULL DEFAULT '0',
  `point_tech_points` bigint(12) unsigned NOT NULL DEFAULT '0',
  `point_building_points` bigint(12) unsigned NOT NULL DEFAULT '0'
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
  `keybinds_enable` tinyint(4) NOT NULL DEFAULT '0'
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
  `elorating` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_sessionlog`
--

CREATE TABLE IF NOT EXISTS `user_sessionlog` (
`id` int(10) unsigned NOT NULL,
  `session_id` char(40) NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_addr` varchar(40) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `time_login` int(10) unsigned NOT NULL DEFAULT '0',
  `time_action` int(10) unsigned NOT NULL DEFAULT '0',
  `time_logout` int(10) unsigned NOT NULL DEFAULT '0'
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
  `bot_count` tinyint(3) NOT NULL DEFAULT '0'
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_sitting`
--

CREATE TABLE IF NOT EXISTS `user_sitting` (
`id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sitter_id` int(10) unsigned NOT NULL DEFAULT '0',
  `password` varchar(255) NOT NULL DEFAULT '0',
  `date_from` int(10) unsigned NOT NULL DEFAULT '0',
  `date_to` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `hmod` tinyint(1) unsigned NOT NULL DEFAULT '0'
) ENGINE=MEMORY DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_surveillance`
--

CREATE TABLE IF NOT EXISTS `user_surveillance` (
`id` int(10) unsigned NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `page` varchar(50) NOT NULL,
  `request` text NOT NULL,
  `request_raw` text NOT NULL,
  `post` text NOT NULL,
  `session` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_warnings`
--

CREATE TABLE IF NOT EXISTS `user_warnings` (
`warning_id` int(10) unsigned NOT NULL,
  `warning_user_id` int(10) unsigned NOT NULL,
  `warning_date` int(10) unsigned NOT NULL,
  `warning_text` text COLLATE utf8_unicode_ci NOT NULL,
  `warning_admin_id` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `wormholes`
--

CREATE TABLE IF NOT EXISTS `wormholes` (
  `id` int(10) unsigned NOT NULL,
  `target_id` int(10) unsigned NOT NULL DEFAULT '0',
  `changed` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accesslog`
--
ALTER TABLE `accesslog`
 ADD KEY `target` (`target`);

--
-- Indexes for table `admin_notes`
--
ALTER TABLE `admin_notes`
 ADD PRIMARY KEY (`notes_id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
 ADD PRIMARY KEY (`user_id`), ADD FULLTEXT KEY `user_password` (`user_password`);

--
-- Indexes for table `admin_user_log`
--
ALTER TABLE `admin_user_log`
 ADD PRIMARY KEY (`log_id`), ADD KEY `log_user_id` (`log_user_id`);

--
-- Indexes for table `admin_user_sessionlog`
--
ALTER TABLE `admin_user_sessionlog`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_user_sessions`
--
ALTER TABLE `admin_user_sessions`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `allianceboard_cat`
--
ALTER TABLE `allianceboard_cat`
 ADD PRIMARY KEY (`cat_id`), ADD KEY `cat_order` (`cat_order`), ADD KEY `cat_name` (`cat_name`), ADD KEY `cat_alliance_id` (`cat_alliance_id`);

--
-- Indexes for table `allianceboard_catranks`
--
ALTER TABLE `allianceboard_catranks`
 ADD PRIMARY KEY (`cr_id`), ADD KEY `cr_rank_id` (`cr_rank_id`), ADD KEY `cr_cat_id` (`cr_cat_id`);

--
-- Indexes for table `allianceboard_posts`
--
ALTER TABLE `allianceboard_posts`
 ADD PRIMARY KEY (`post_id`), ADD KEY `post_topic_id` (`post_topic_id`), ADD KEY `post_user_id` (`post_user_id`), ADD KEY `post_timestamp` (`post_timestamp`);

--
-- Indexes for table `allianceboard_topics`
--
ALTER TABLE `allianceboard_topics`
 ADD PRIMARY KEY (`topic_id`), ADD KEY `topic_cat_id` (`topic_cat_id`), ADD KEY `topic_timestamp` (`topic_timestamp`);

--
-- Indexes for table `alliances`
--
ALTER TABLE `alliances`
 ADD PRIMARY KEY (`alliance_id`), ADD KEY `alliance_tag` (`alliance_tag`), ADD KEY `alliance_name` (`alliance_name`), ADD KEY `alliance_points` (`alliance_points`), ADD KEY `alliance_founder_id` (`alliance_founder_id`);

--
-- Indexes for table `alliance_applications`
--
ALTER TABLE `alliance_applications`
 ADD UNIQUE KEY `user_id` (`user_id`), ADD KEY `alliance_id` (`alliance_id`);

--
-- Indexes for table `alliance_bnd`
--
ALTER TABLE `alliance_bnd`
 ADD PRIMARY KEY (`alliance_bnd_id`), ADD KEY `alliance_bnd_alliance_id1` (`alliance_bnd_alliance_id1`), ADD KEY `alliance_bnd_alliance_id2` (`alliance_bnd_alliance_id2`), ADD KEY `bnd1` (`alliance_bnd_level`,`alliance_bnd_alliance_id1`), ADD KEY `bnd2` (`alliance_bnd_level`,`alliance_bnd_alliance_id2`);

--
-- Indexes for table `alliance_buildings`
--
ALTER TABLE `alliance_buildings`
 ADD PRIMARY KEY (`alliance_building_id`);

--
-- Indexes for table `alliance_building_cooldown`
--
ALTER TABLE `alliance_building_cooldown`
 ADD UNIQUE KEY `cooldown_user_id` (`cooldown_user_id`,`cooldown_alliance_building_id`);

--
-- Indexes for table `alliance_buildlist`
--
ALTER TABLE `alliance_buildlist`
 ADD PRIMARY KEY (`alliance_buildlist_id`), ADD KEY `alliance_buildlist_alliance_id` (`alliance_buildlist_alliance_id`);

--
-- Indexes for table `alliance_history`
--
ALTER TABLE `alliance_history`
 ADD PRIMARY KEY (`history_id`), ADD KEY `latest` (`history_alliance_id`,`history_timestamp`);

--
-- Indexes for table `alliance_news`
--
ALTER TABLE `alliance_news`
 ADD PRIMARY KEY (`alliance_news_id`), ADD KEY `alliance_news_alliance_id` (`alliance_news_alliance_id`), ADD KEY `alliance_news_user_id` (`alliance_news_user_id`), ADD KEY `alliance_news_date` (`alliance_news_date`);

--
-- Indexes for table `alliance_points`
--
ALTER TABLE `alliance_points`
 ADD PRIMARY KEY (`point_id`), ADD KEY `point_user_id` (`point_alliance_id`);

--
-- Indexes for table `alliance_polls`
--
ALTER TABLE `alliance_polls`
 ADD PRIMARY KEY (`poll_id`), ADD KEY `poll_alliance_id` (`poll_alliance_id`);

--
-- Indexes for table `alliance_poll_votes`
--
ALTER TABLE `alliance_poll_votes`
 ADD PRIMARY KEY (`vote_id`), ADD KEY `vote_poll_id` (`vote_poll_id`), ADD KEY `vote_user_id` (`vote_user_id`), ADD KEY `vote_alliance_id` (`vote_alliance_id`);

--
-- Indexes for table `alliance_rankrights`
--
ALTER TABLE `alliance_rankrights`
 ADD PRIMARY KEY (`rr_id`), ADD KEY `rr_rank_id` (`rr_rank_id`), ADD KEY `rr_right_id` (`rr_right_id`);

--
-- Indexes for table `alliance_ranks`
--
ALTER TABLE `alliance_ranks`
 ADD PRIMARY KEY (`rank_id`), ADD KEY `rank_alliance_id` (`rank_alliance_id`,`rank_level`);

--
-- Indexes for table `alliance_rights`
--
ALTER TABLE `alliance_rights`
 ADD PRIMARY KEY (`right_id`), ADD KEY `right_key` (`right_key`);

--
-- Indexes for table `alliance_spends`
--
ALTER TABLE `alliance_spends`
 ADD PRIMARY KEY (`alliance_spend_id`), ADD KEY `alliance_spend_alliance_id` (`alliance_spend_alliance_id`);

--
-- Indexes for table `alliance_stats`
--
ALTER TABLE `alliance_stats`
 ADD PRIMARY KEY (`alliance_id`);

--
-- Indexes for table `alliance_techlist`
--
ALTER TABLE `alliance_techlist`
 ADD PRIMARY KEY (`alliance_techlist_id`), ADD KEY `alliance_techlist_alliance_id` (`alliance_techlist_alliance_id`);

--
-- Indexes for table `alliance_technologies`
--
ALTER TABLE `alliance_technologies`
 ADD PRIMARY KEY (`alliance_tech_id`);

--
-- Indexes for table `asteroids`
--
ALTER TABLE `asteroids`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attack_ban`
--
ALTER TABLE `attack_ban`
 ADD PRIMARY KEY (`attack_ban_id`);

--
-- Indexes for table `backend_message_queue`
--
ALTER TABLE `backend_message_queue`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `cmd` (`cmd`,`arg`);

--
-- Indexes for table `bookmarks`
--
ALTER TABLE `bookmarks`
 ADD PRIMARY KEY (`id`), ADD KEY `bookmark_user_id` (`user_id`), ADD KEY `absindex` (`user_id`,`entity_id`);

--
-- Indexes for table `buddylist`
--
ALTER TABLE `buddylist`
 ADD PRIMARY KEY (`bl_id`), ADD KEY `bl_buddy_id` (`bl_buddy_id`), ADD KEY `bl_user_id` (`bl_user_id`);

--
-- Indexes for table `buildings`
--
ALTER TABLE `buildings`
 ADD PRIMARY KEY (`building_id`), ADD KEY `building_name` (`building_name`,`building_order`);

--
-- Indexes for table `building_points`
--
ALTER TABLE `building_points`
 ADD PRIMARY KEY (`bp_id`), ADD KEY `bp_building_id` (`bp_building_id`), ADD KEY `bp_level` (`bp_level`), ADD KEY `bp_points` (`bp_points`);

--
-- Indexes for table `building_queue`
--
ALTER TABLE `building_queue`
 ADD PRIMARY KEY (`id`), ADD KEY `entity_id` (`entity_id`,`user_id`);

--
-- Indexes for table `building_requirements`
--
ALTER TABLE `building_requirements`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `obj_id` (`obj_id`,`req_building_id`,`req_tech_id`);

--
-- Indexes for table `building_types`
--
ALTER TABLE `building_types`
 ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `buildlist`
--
ALTER TABLE `buildlist`
 ADD PRIMARY KEY (`buildlist_id`), ADD UNIQUE KEY `entity_user_building` (`buildlist_entity_id`,`buildlist_user_id`,`buildlist_building_id`), ADD KEY `buildlist_user_id` (`buildlist_user_id`), ADD KEY `buildlist_building_id` (`buildlist_building_id`), ADD KEY `buildlist_planet_id` (`buildlist_entity_id`), ADD KEY `buildlist_build_end_time` (`buildlist_build_end_time`), ADD KEY `buildlist_build_type` (`buildlist_build_type`);

--
-- Indexes for table `cells`
--
ALTER TABLE `cells`
 ADD PRIMARY KEY (`id`) USING BTREE, ADD KEY `cell` (`cx`,`cy`), ADD KEY `sector` (`sx`,`sy`), ADD KEY `coordinates` (`sx`,`sy`,`cx`,`cy`), ADD KEY `cy` (`cy`), ADD KEY `cx` (`cx`), ADD KEY `sy` (`sy`), ADD KEY `sx` (`sx`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
 ADD PRIMARY KEY (`id`), ADD KEY `timestamp` (`timestamp`);

--
-- Indexes for table `chat_banns`
--
ALTER TABLE `chat_banns`
 ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `chat_channels`
--
ALTER TABLE `chat_channels`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_log`
--
ALTER TABLE `chat_log`
 ADD PRIMARY KEY (`id`), ADD KEY `timestamp` (`timestamp`);

--
-- Indexes for table `chat_users`
--
ALTER TABLE `chat_users`
 ADD PRIMARY KEY (`user_id`), ADD KEY `timestamp` (`timestamp`);

--
-- Indexes for table `config`
--
ALTER TABLE `config`
 ADD PRIMARY KEY (`config_id`), ADD UNIQUE KEY `config_name_2` (`config_name`);

--
-- Indexes for table `default_items`
--
ALTER TABLE `default_items`
 ADD PRIMARY KEY (`item_id`), ADD KEY `item_set_id` (`item_set_id`);

--
-- Indexes for table `default_item_sets`
--
ALTER TABLE `default_item_sets`
 ADD PRIMARY KEY (`set_id`);

--
-- Indexes for table `defense`
--
ALTER TABLE `defense`
 ADD PRIMARY KEY (`def_id`), ADD KEY `def_name` (`def_name`), ADD KEY `def_order` (`def_order`), ADD KEY `def_max_count` (`def_max_count`), ADD KEY `def_battlepoints` (`def_points`);

--
-- Indexes for table `deflist`
--
ALTER TABLE `deflist`
 ADD PRIMARY KEY (`deflist_id`), ADD UNIQUE KEY `deflist_all` (`deflist_user_id`,`deflist_entity_id`,`deflist_def_id`);

--
-- Indexes for table `def_cat`
--
ALTER TABLE `def_cat`
 ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `def_queue`
--
ALTER TABLE `def_queue`
 ADD PRIMARY KEY (`queue_id`), ADD KEY `queue_user_id` (`queue_user_id`), ADD KEY `queue_planet_id` (`queue_entity_id`);

--
-- Indexes for table `def_requirements`
--
ALTER TABLE `def_requirements`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `obj_id` (`obj_id`,`req_building_id`,`req_tech_id`);

--
-- Indexes for table `entities`
--
ALTER TABLE `entities`
 ADD PRIMARY KEY (`id`), ADD KEY `cell_id` (`cell_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
 ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `events_exec`
--
ALTER TABLE `events_exec`
 ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `fleet`
--
ALTER TABLE `fleet`
 ADD PRIMARY KEY (`id`), ADD KEY `fleet_user_id` (`user_id`), ADD KEY `fleet_landtime` (`landtime`), ADD KEY `entity_from` (`entity_from`), ADD KEY `entity_to` (`entity_to`);

--
-- Indexes for table `fleet_bookmarks`
--
ALTER TABLE `fleet_bookmarks`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fleet_ships`
--
ALTER TABLE `fleet_ships`
 ADD PRIMARY KEY (`fs_id`), ADD KEY `fs_fleet_id` (`fs_fleet_id`), ADD KEY `fs_ship_id` (`fs_ship_id`), ADD KEY `fs_fleet_id_2` (`fs_fleet_id`,`fs_ship_faked`);

--
-- Indexes for table `hostname_cache`
--
ALTER TABLE `hostname_cache`
 ADD PRIMARY KEY (`addr`);

--
-- Indexes for table `ip_ban`
--
ALTER TABLE `ip_ban`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_failures`
--
ALTER TABLE `login_failures`
 ADD PRIMARY KEY (`failure_id`), ADD KEY `failure_user_id` (`failure_user_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
 ADD PRIMARY KEY (`id`), ADD KEY `facility` (`facility`), ADD KEY `logview` (`facility`,`severity`,`timestamp`), ADD KEY `severity` (`severity`), ADD KEY `timestamp` (`timestamp`);

--
-- Indexes for table `logs_alliance`
--
ALTER TABLE `logs_alliance`
 ADD PRIMARY KEY (`logs_alliance_id`);

--
-- Indexes for table `logs_battle`
--
ALTER TABLE `logs_battle`
 ADD PRIMARY KEY (`id`), ADD KEY `logs_battle_fleet_landtime` (`landtime`);

--
-- Indexes for table `logs_battle_queue`
--
ALTER TABLE `logs_battle_queue`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs_fleet`
--
ALTER TABLE `logs_fleet`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs_fleet_queue`
--
ALTER TABLE `logs_fleet_queue`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs_game`
--
ALTER TABLE `logs_game`
 ADD PRIMARY KEY (`id`), ADD KEY `facility` (`facility`,`severity`,`timestamp`), ADD KEY `severity` (`severity`,`facility`,`timestamp`);

--
-- Indexes for table `logs_game_queue`
--
ALTER TABLE `logs_game_queue`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs_queue`
--
ALTER TABLE `logs_queue`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `market_auction`
--
ALTER TABLE `market_auction`
 ADD PRIMARY KEY (`id`), ADD KEY `auction_end` (`date_end`), ADD KEY `auction_planet_id` (`entity_id`), ADD KEY `auction_user_id` (`user_id`);

--
-- Indexes for table `market_rates`
--
ALTER TABLE `market_rates`
 ADD PRIMARY KEY (`id`), ADD KEY `timestamp` (`timestamp`);

--
-- Indexes for table `market_ressource`
--
ALTER TABLE `market_ressource`
 ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`), ADD KEY `datum` (`datum`), ADD KEY `planet_id` (`entity_id`);

--
-- Indexes for table `market_ship`
--
ALTER TABLE `market_ship`
 ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`), ADD KEY `ship_id` (`ship_id`), ADD KEY `datum` (`datum`), ADD KEY `planet_id` (`entity_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
 ADD PRIMARY KEY (`message_id`), ADD KEY `message_user_from` (`message_user_from`), ADD KEY `message_user_to` (`message_user_to`), ADD KEY `message_read` (`message_read`), ADD KEY `message_timestamp` (`message_timestamp`), ADD KEY `list` (`message_user_to`,`message_read`,`message_timestamp`);

--
-- Indexes for table `message_cat`
--
ALTER TABLE `message_cat`
 ADD PRIMARY KEY (`cat_id`), ADD KEY `cat_order` (`cat_order`);

--
-- Indexes for table `message_data`
--
ALTER TABLE `message_data`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message_ignore`
--
ALTER TABLE `message_ignore`
 ADD PRIMARY KEY (`ignore_id`), ADD KEY `ignore_owner_id` (`ignore_owner_id`), ADD KEY `ignore_target_id` (`ignore_target_id`);

--
-- Indexes for table `minimap`
--
ALTER TABLE `minimap`
 ADD PRIMARY KEY (`field_id`);

--
-- Indexes for table `minimap_field_types`
--
ALTER TABLE `minimap_field_types`
 ADD PRIMARY KEY (`field_typ_id`);

--
-- Indexes for table `missilelist`
--
ALTER TABLE `missilelist`
 ADD PRIMARY KEY (`missilelist_id`), ADD KEY `missilelist_missile_id` (`missilelist_missile_id`), ADD KEY `missilelist_user_id` (`missilelist_user_id`,`missilelist_entity_id`);

--
-- Indexes for table `missiles`
--
ALTER TABLE `missiles`
 ADD PRIMARY KEY (`missile_id`), ADD KEY `missile_name` (`missile_name`), ADD KEY `missile_damage` (`missile_damage`), ADD KEY `missile_show` (`missile_show`), ADD KEY `missile_launchable` (`missile_launchable`);

--
-- Indexes for table `missile_flights`
--
ALTER TABLE `missile_flights`
 ADD PRIMARY KEY (`flight_id`), ADD KEY `flight_planet_from` (`flight_entity_from`), ADD KEY `flight_planet_to` (`flight_entity_to`), ADD KEY `flight_user_id` (`flight_starttime`);

--
-- Indexes for table `missile_flights_obj`
--
ALTER TABLE `missile_flights_obj`
 ADD PRIMARY KEY (`obj_id`), ADD KEY `obj_flight_id` (`obj_flight_id`), ADD KEY `obj_missile_id` (`obj_missile_id`), ADD KEY `obj_cnt` (`obj_cnt`);

--
-- Indexes for table `missile_requirements`
--
ALTER TABLE `missile_requirements`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `obj_id` (`obj_id`,`req_building_id`,`req_tech_id`);

--
-- Indexes for table `nebulas`
--
ALTER TABLE `nebulas`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notepad`
--
ALTER TABLE `notepad`
 ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`), ADD KEY `list` (`user_id`,`timestamp`);

--
-- Indexes for table `notepad_data`
--
ALTER TABLE `notepad_data`
 ADD PRIMARY KEY (`id`), ADD KEY `subject` (`subject`);

--
-- Indexes for table `obj_transforms`
--
ALTER TABLE `obj_transforms`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `def_id` (`def_id`), ADD UNIQUE KEY `ship_id` (`ship_id`);

--
-- Indexes for table `planets`
--
ALTER TABLE `planets`
 ADD PRIMARY KEY (`id`), ADD KEY `planet_name` (`planet_name`), ADD KEY `planet_last_updated` (`planet_last_updated`), ADD KEY `mainplanet` (`planet_user_id`,`planet_user_main`,`planet_name`);

--
-- Indexes for table `planet_types`
--
ALTER TABLE `planet_types`
 ADD PRIMARY KEY (`type_id`), ADD KEY `type_name` (`type_name`);

--
-- Indexes for table `races`
--
ALTER TABLE `races`
 ADD PRIMARY KEY (`race_id`), ADD KEY `race_name` (`race_name`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
 ADD PRIMARY KEY (`id`), ADD KEY `type` (`type`);

--
-- Indexes for table `reports_market`
--
ALTER TABLE `reports_market`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shiplist`
--
ALTER TABLE `shiplist`
 ADD PRIMARY KEY (`shiplist_id`), ADD UNIQUE KEY `user_entity_ship_id` (`shiplist_user_id`,`shiplist_entity_id`,`shiplist_ship_id`);

--
-- Indexes for table `ships`
--
ALTER TABLE `ships`
 ADD PRIMARY KEY (`ship_id`), ADD KEY `ship_order` (`ship_order`), ADD KEY `ship_battlepoints` (`ship_points`), ADD KEY `ship_name` (`ship_name`);

--
-- Indexes for table `ship_cat`
--
ALTER TABLE `ship_cat`
 ADD PRIMARY KEY (`cat_id`), ADD KEY `cat_name` (`cat_name`), ADD KEY `cat_order` (`cat_order`);

--
-- Indexes for table `ship_queue`
--
ALTER TABLE `ship_queue`
 ADD PRIMARY KEY (`queue_id`), ADD KEY `queue_user_id` (`queue_user_id`), ADD KEY `queue_planet_id` (`queue_entity_id`);

--
-- Indexes for table `ship_requirements`
--
ALTER TABLE `ship_requirements`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `obj_id` (`obj_id`,`req_building_id`,`req_tech_id`);

--
-- Indexes for table `sol_types`
--
ALTER TABLE `sol_types`
 ADD PRIMARY KEY (`sol_type_id`), ADD KEY `type_name` (`sol_type_name`);

--
-- Indexes for table `space`
--
ALTER TABLE `space`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `specialists`
--
ALTER TABLE `specialists`
 ADD PRIMARY KEY (`specialist_id`);

--
-- Indexes for table `stars`
--
ALTER TABLE `stars`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `techlist`
--
ALTER TABLE `techlist`
 ADD PRIMARY KEY (`techlist_id`), ADD KEY `techlist_user_id` (`techlist_user_id`), ADD KEY `techlist_tech_id` (`techlist_tech_id`), ADD KEY `techlist_planet_id` (`techlist_entity_id`), ADD KEY `techlist_build_end_time` (`techlist_build_end_time`);

--
-- Indexes for table `technologies`
--
ALTER TABLE `technologies`
 ADD PRIMARY KEY (`tech_id`), ADD KEY `tech_name` (`tech_name`), ADD KEY `tech_order` (`tech_order`);

--
-- Indexes for table `tech_points`
--
ALTER TABLE `tech_points`
 ADD PRIMARY KEY (`bp_id`), ADD KEY `bp_tech_id` (`bp_tech_id`), ADD KEY `bp_level` (`bp_level`), ADD KEY `bp_points` (`bp_points`);

--
-- Indexes for table `tech_requirements`
--
ALTER TABLE `tech_requirements`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `obj_id` (`obj_id`,`req_building_id`,`req_tech_id`);

--
-- Indexes for table `tech_types`
--
ALTER TABLE `tech_types`
 ADD PRIMARY KEY (`type_id`), ADD KEY `type_name` (`type_name`);

--
-- Indexes for table `texts`
--
ALTER TABLE `texts`
 ADD PRIMARY KEY (`text_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
 ADD PRIMARY KEY (`id`), ADD KEY `abuse_user_id` (`user_id`), ADD KEY `abuse_status` (`status`), ADD KEY `abuse_admin_id` (`admin_id`), ADD KEY `abuse_timestamp` (`timestamp`);

--
-- Indexes for table `ticket_cat`
--
ALTER TABLE `ticket_cat`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ticket_msg`
--
ALTER TABLE `ticket_msg`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tips`
--
ALTER TABLE `tips`
 ADD PRIMARY KEY (`tip_id`), ADD KEY `tip_active` (`tip_active`);

--
-- Indexes for table `tutorial`
--
ALTER TABLE `tutorial`
 ADD PRIMARY KEY (`tutorial_id`);

--
-- Indexes for table `tutorial_texts`
--
ALTER TABLE `tutorial_texts`
 ADD PRIMARY KEY (`text_id`);

--
-- Indexes for table `tutorial_user_progress`
--
ALTER TABLE `tutorial_user_progress`
 ADD UNIQUE KEY `tup_user_id` (`tup_user_id`,`tup_tutorial_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`user_id`), ADD KEY `user_name` (`user_name`), ADD KEY `user_nick` (`user_nick`), ADD KEY `user_rank_current` (`user_rank`), ADD KEY `user_points` (`user_points`), ADD KEY `user_session_key` (`user_session_key`), ADD KEY `user_acttime` (`user_acttime`);

--
-- Indexes for table `user_comments`
--
ALTER TABLE `user_comments`
 ADD PRIMARY KEY (`comment_id`);

--
-- Indexes for table `user_log`
--
ALTER TABLE `user_log`
 ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`), ADD KEY `timestamp` (`timestamp`);

--
-- Indexes for table `user_minimap`
--
ALTER TABLE `user_minimap`
 ADD PRIMARY KEY (`minimap_user_id`);

--
-- Indexes for table `user_multi`
--
ALTER TABLE `user_multi`
 ADD PRIMARY KEY (`id`), ADD KEY `user_multi_user_id` (`user_id`);

--
-- Indexes for table `user_onlinestats`
--
ALTER TABLE `user_onlinestats`
 ADD PRIMARY KEY (`stats_id`), ADD KEY `stats_count` (`stats_count`);

--
-- Indexes for table `user_points`
--
ALTER TABLE `user_points`
 ADD PRIMARY KEY (`point_id`), ADD KEY `point_user_id` (`point_user_id`);

--
-- Indexes for table `user_properties`
--
ALTER TABLE `user_properties`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_ratings`
--
ALTER TABLE `user_ratings`
 ADD PRIMARY KEY (`id`), ADD KEY `id` (`id`,`battle_rating`), ADD KEY `id_2` (`id`,`trade_rating`), ADD KEY `id_3` (`id`,`diplomacy_rating`);

--
-- Indexes for table `user_sessionlog`
--
ALTER TABLE `user_sessionlog`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `user_sitting`
--
ALTER TABLE `user_sitting`
 ADD PRIMARY KEY (`id`), ADD KEY `user_sitting_sitter_user_id` (`sitter_id`), ADD KEY `user_sitting_user_id` (`user_id`);

--
-- Indexes for table `user_stats`
--
ALTER TABLE `user_stats`
 ADD PRIMARY KEY (`id`), ADD KEY `rank` (`rank`,`nick`), ADD KEY `rank_ships` (`rank_ships`,`nick`), ADD KEY `rank_tech` (`rank_tech`,`nick`), ADD KEY `rank_buildings` (`rank_buildings`,`nick`), ADD KEY `rank_exp` (`rank_exp`,`nick`), ADD KEY `points_ships` (`points_ships`), ADD KEY `points_tech` (`points_tech`), ADD KEY `points_buildings` (`points_buildings`), ADD KEY `points_exp` (`points_exp`), ADD KEY `points` (`points`);

--
-- Indexes for table `user_surveillance`
--
ALTER TABLE `user_surveillance`
 ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`,`timestamp`);

--
-- Indexes for table `user_warnings`
--
ALTER TABLE `user_warnings`
 ADD PRIMARY KEY (`warning_id`), ADD KEY `warning_user_id` (`warning_user_id`);

--
-- Indexes for table `wormholes`
--
ALTER TABLE `wormholes`
 ADD PRIMARY KEY (`id`), ADD KEY `target_id` (`target_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_notes`
--
ALTER TABLE `admin_notes`
MODIFY `notes_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
MODIFY `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `admin_user_log`
--
ALTER TABLE `admin_user_log`
MODIFY `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `admin_user_sessionlog`
--
ALTER TABLE `admin_user_sessionlog`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `allianceboard_cat`
--
ALTER TABLE `allianceboard_cat`
MODIFY `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `allianceboard_catranks`
--
ALTER TABLE `allianceboard_catranks`
MODIFY `cr_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `allianceboard_posts`
--
ALTER TABLE `allianceboard_posts`
MODIFY `post_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `allianceboard_topics`
--
ALTER TABLE `allianceboard_topics`
MODIFY `topic_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `alliances`
--
ALTER TABLE `alliances`
MODIFY `alliance_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `alliance_bnd`
--
ALTER TABLE `alliance_bnd`
MODIFY `alliance_bnd_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `alliance_buildings`
--
ALTER TABLE `alliance_buildings`
MODIFY `alliance_building_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `alliance_buildlist`
--
ALTER TABLE `alliance_buildlist`
MODIFY `alliance_buildlist_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `alliance_history`
--
ALTER TABLE `alliance_history`
MODIFY `history_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `alliance_news`
--
ALTER TABLE `alliance_news`
MODIFY `alliance_news_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `alliance_points`
--
ALTER TABLE `alliance_points`
MODIFY `point_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `alliance_polls`
--
ALTER TABLE `alliance_polls`
MODIFY `poll_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `alliance_poll_votes`
--
ALTER TABLE `alliance_poll_votes`
MODIFY `vote_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `alliance_rankrights`
--
ALTER TABLE `alliance_rankrights`
MODIFY `rr_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `alliance_ranks`
--
ALTER TABLE `alliance_ranks`
MODIFY `rank_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `alliance_rights`
--
ALTER TABLE `alliance_rights`
MODIFY `right_id` int(3) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `alliance_spends`
--
ALTER TABLE `alliance_spends`
MODIFY `alliance_spend_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `alliance_techlist`
--
ALTER TABLE `alliance_techlist`
MODIFY `alliance_techlist_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `alliance_technologies`
--
ALTER TABLE `alliance_technologies`
MODIFY `alliance_tech_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `attack_ban`
--
ALTER TABLE `attack_ban`
MODIFY `attack_ban_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `backend_message_queue`
--
ALTER TABLE `backend_message_queue`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bookmarks`
--
ALTER TABLE `bookmarks`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `buddylist`
--
ALTER TABLE `buddylist`
MODIFY `bl_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `buildings`
--
ALTER TABLE `buildings`
MODIFY `building_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `building_points`
--
ALTER TABLE `building_points`
MODIFY `bp_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `building_queue`
--
ALTER TABLE `building_queue`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `building_requirements`
--
ALTER TABLE `building_requirements`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=66;
--
-- AUTO_INCREMENT for table `building_types`
--
ALTER TABLE `building_types`
MODIFY `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `buildlist`
--
ALTER TABLE `buildlist`
MODIFY `buildlist_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `cells`
--
ALTER TABLE `cells`
MODIFY `id` smallint(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `chat_channels`
--
ALTER TABLE `chat_channels`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `chat_log`
--
ALTER TABLE `chat_log`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `config`
--
ALTER TABLE `config`
MODIFY `config_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=188;
--
-- AUTO_INCREMENT for table `default_items`
--
ALTER TABLE `default_items`
MODIFY `item_id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=133;
--
-- AUTO_INCREMENT for table `default_item_sets`
--
ALTER TABLE `default_item_sets`
MODIFY `set_id` int(10) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `defense`
--
ALTER TABLE `defense`
MODIFY `def_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `deflist`
--
ALTER TABLE `deflist`
MODIFY `deflist_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `def_cat`
--
ALTER TABLE `def_cat`
MODIFY `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `def_queue`
--
ALTER TABLE `def_queue`
MODIFY `queue_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `def_requirements`
--
ALTER TABLE `def_requirements`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=78;
--
-- AUTO_INCREMENT for table `entities`
--
ALTER TABLE `entities`
MODIFY `id` int(8) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
MODIFY `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `events_exec`
--
ALTER TABLE `events_exec`
MODIFY `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fleet`
--
ALTER TABLE `fleet`
MODIFY `id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fleet_bookmarks`
--
ALTER TABLE `fleet_bookmarks`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `fleet_ships`
--
ALTER TABLE `fleet_ships`
MODIFY `fs_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ip_ban`
--
ALTER TABLE `ip_ban`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `login_failures`
--
ALTER TABLE `login_failures`
MODIFY `failure_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs_alliance`
--
ALTER TABLE `logs_alliance`
MODIFY `logs_alliance_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs_battle`
--
ALTER TABLE `logs_battle`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs_battle_queue`
--
ALTER TABLE `logs_battle_queue`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs_fleet`
--
ALTER TABLE `logs_fleet`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs_fleet_queue`
--
ALTER TABLE `logs_fleet_queue`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs_game`
--
ALTER TABLE `logs_game`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs_game_queue`
--
ALTER TABLE `logs_game_queue`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `logs_queue`
--
ALTER TABLE `logs_queue`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `market_auction`
--
ALTER TABLE `market_auction`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `market_rates`
--
ALTER TABLE `market_rates`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `market_ressource`
--
ALTER TABLE `market_ressource`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `market_ship`
--
ALTER TABLE `market_ship`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
MODIFY `message_id` mediumint(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `message_cat`
--
ALTER TABLE `message_cat`
MODIFY `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `message_ignore`
--
ALTER TABLE `message_ignore`
MODIFY `ignore_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `minimap`
--
ALTER TABLE `minimap`
MODIFY `field_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `minimap_field_types`
--
ALTER TABLE `minimap_field_types`
MODIFY `field_typ_id` int(3) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `missilelist`
--
ALTER TABLE `missilelist`
MODIFY `missilelist_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `missiles`
--
ALTER TABLE `missiles`
MODIFY `missile_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `missile_flights`
--
ALTER TABLE `missile_flights`
MODIFY `flight_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `missile_flights_obj`
--
ALTER TABLE `missile_flights_obj`
MODIFY `obj_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `missile_requirements`
--
ALTER TABLE `missile_requirements`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `notepad`
--
ALTER TABLE `notepad`
MODIFY `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `obj_transforms`
--
ALTER TABLE `obj_transforms`
MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `planet_types`
--
ALTER TABLE `planet_types`
MODIFY `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `races`
--
ALTER TABLE `races`
MODIFY `race_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `shiplist`
--
ALTER TABLE `shiplist`
MODIFY `shiplist_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ships`
--
ALTER TABLE `ships`
MODIFY `ship_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=91;
--
-- AUTO_INCREMENT for table `ship_cat`
--
ALTER TABLE `ship_cat`
MODIFY `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `ship_queue`
--
ALTER TABLE `ship_queue`
MODIFY `queue_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ship_requirements`
--
ALTER TABLE `ship_requirements`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=304;
--
-- AUTO_INCREMENT for table `sol_types`
--
ALTER TABLE `sol_types`
MODIFY `sol_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `specialists`
--
ALTER TABLE `specialists`
MODIFY `specialist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `techlist`
--
ALTER TABLE `techlist`
MODIFY `techlist_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `technologies`
--
ALTER TABLE `technologies`
MODIFY `tech_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=26;
--
-- AUTO_INCREMENT for table `tech_points`
--
ALTER TABLE `tech_points`
MODIFY `bp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10767;
--
-- AUTO_INCREMENT for table `tech_requirements`
--
ALTER TABLE `tech_requirements`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=48;
--
-- AUTO_INCREMENT for table `tech_types`
--
ALTER TABLE `tech_types`
MODIFY `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
MODIFY `id` int(6) unsigned zerofill NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ticket_cat`
--
ALTER TABLE `ticket_cat`
MODIFY `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT for table `ticket_msg`
--
ALTER TABLE `ticket_msg`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tips`
--
ALTER TABLE `tips`
MODIFY `tip_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `tutorial`
--
ALTER TABLE `tutorial`
MODIFY `tutorial_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `tutorial_texts`
--
ALTER TABLE `tutorial_texts`
MODIFY `text_id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `user_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_comments`
--
ALTER TABLE `user_comments`
MODIFY `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_log`
--
ALTER TABLE `user_log`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_multi`
--
ALTER TABLE `user_multi`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_onlinestats`
--
ALTER TABLE `user_onlinestats`
MODIFY `stats_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_points`
--
ALTER TABLE `user_points`
MODIFY `point_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_sessionlog`
--
ALTER TABLE `user_sessionlog`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_sitting`
--
ALTER TABLE `user_sitting`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_surveillance`
--
ALTER TABLE `user_surveillance`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user_warnings`
--
ALTER TABLE `user_warnings`
MODIFY `warning_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
