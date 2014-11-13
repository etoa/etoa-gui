-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 13. Nov 2014 um 18:39
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
-- Tabellenstruktur für Tabelle `attack_ban`
--

CREATE TABLE IF NOT EXISTS `attack_ban` (
`attack_ban_id` int(10) unsigned NOT NULL,
  `attack_ban_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `attack_ban_reason` text NOT NULL,
  `attack_ban_time` int(10) unsigned NOT NULL DEFAULT '0',
  `attack_ban_deleted` tinyint(3) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attack_ban`
--
ALTER TABLE `attack_ban`
 ADD PRIMARY KEY (`attack_ban_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attack_ban`
--
ALTER TABLE `attack_ban`
MODIFY `attack_ban_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
