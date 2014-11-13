-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 13. Nov 2014 um 18:47
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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events_exec`
--
ALTER TABLE `events_exec`
 ADD PRIMARY KEY (`event_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events_exec`
--
ALTER TABLE `events_exec`
MODIFY `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
