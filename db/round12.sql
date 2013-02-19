-- MySQL dump 10.13  Distrib 5.1.66, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: etoa_round12
-- ------------------------------------------------------
-- Server version	5.1.66-0+squeeze1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accesslog`
--

DROP TABLE IF EXISTS `accesslog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accesslog` (
  `target` varchar(255) NOT NULL,
  `sub` varchar(255) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `sid` varchar(32) NOT NULL,
  `domain` varchar(255) NOT NULL,
  KEY `target` (`target`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accesslog`
--

LOCK TABLES `accesslog` WRITE;
/*!40000 ALTER TABLE `accesslog` DISABLE KEYS */;
/*!40000 ALTER TABLE `accesslog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_notes`
--

DROP TABLE IF EXISTS `admin_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_notes` (
  `notes_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned NOT NULL,
  `titel` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `date` int(10) unsigned NOT NULL,
  PRIMARY KEY (`notes_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_notes`
--

LOCK TABLES `admin_notes` WRITE;
/*!40000 ALTER TABLE `admin_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_surveillance`
--

DROP TABLE IF EXISTS `admin_surveillance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_surveillance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `page` varchar(50) NOT NULL,
  `request` text NOT NULL,
  `post` text NOT NULL,
  `session` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_surveillance`
--

LOCK TABLES `admin_surveillance` WRITE;
/*!40000 ALTER TABLE `admin_surveillance` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_surveillance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_user_log`
--

DROP TABLE IF EXISTS `admin_user_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_user_log` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_user_log`
--

LOCK TABLES `admin_user_log` WRITE;
/*!40000 ALTER TABLE `admin_user_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_user_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_user_sessionlog`
--

DROP TABLE IF EXISTS `admin_user_sessionlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_user_sessionlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` char(40) NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_addr` varchar(40) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `time_login` int(10) unsigned NOT NULL DEFAULT '0',
  `time_action` int(10) unsigned NOT NULL DEFAULT '0',
  `time_logout` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_user_sessionlog`
--

LOCK TABLES `admin_user_sessionlog` WRITE;
/*!40000 ALTER TABLE `admin_user_sessionlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_user_sessionlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_user_sessions`
--

DROP TABLE IF EXISTS `admin_user_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_user_sessions` (
  `id` char(40) NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_addr` varchar(40) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `time_login` int(11) unsigned NOT NULL DEFAULT '0',
  `time_action` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_user_sessions`
--

LOCK TABLES `admin_user_sessions` WRITE;
/*!40000 ALTER TABLE `admin_user_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_user_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_users` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alliance_applications`
--

DROP TABLE IF EXISTS `alliance_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliance_applications` (
  `user_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `alliance_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `text` text NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `user_id` (`user_id`),
  KEY `alliance_id` (`alliance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliance_applications`
--

LOCK TABLES `alliance_applications` WRITE;
/*!40000 ALTER TABLE `alliance_applications` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_applications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alliance_bnd`
--

DROP TABLE IF EXISTS `alliance_bnd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliance_bnd` (
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
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliance_bnd`
--

LOCK TABLES `alliance_bnd` WRITE;
/*!40000 ALTER TABLE `alliance_bnd` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_bnd` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alliance_buildings`
--

DROP TABLE IF EXISTS `alliance_buildings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliance_buildings` (
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
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliance_buildings`
--

LOCK TABLES `alliance_buildings` WRITE;
/*!40000 ALTER TABLE `alliance_buildings` DISABLE KEYS */;
INSERT INTO `alliance_buildings` VALUES (1,'Zentrale','','Die Zentrale ist das Hauptgebäude der Allianzbasis. Baut dieses aus um weitere Objekte zu erhalten.',100000,100000,70000,35000,50000,3600,'2.00',4,1,0,0),(2,'Handelszentrum','','Das Handelszentrum ermöglicht den risikofreien Handel unter den Allianzmitgliedern. Dieser erlaubt es die Angebote auf einem abgeschotteten Markt anzubieten, auf welchen nur Allianzmitglieder zutritt haben.',300000,250000,350000,35000,0,18000,'2.00',10,1,1,1),(3,'Schiffswerft','','Die Allianzschiffswerft produziert einzelne Schiffsteile, mit welchen ein ganzes Schiff hergestellt werden kann. Je weiter die Werft ausgebaut ist, desto schneller können die Teile hergestellt werden und desto mehr Baupläne für Schiffstypen werden konstruiert.',145000,102000,117000,80000,0,15000,'2.50',99,1,4,1),(4,'Flottenkontrolle','','Flottenkontrolletext hierrein',100000,75000,50000,25000,0,15000,'2.01',99,1,1,1),(5,'Forschungslabor','','Bau dir was',60000,90000,45000,35000,0,15000,'2.00',99,1,1,1),(6,'Kryptocenter','','',250000,2250000,250000,3250000,0,20000,'3.00',10,1,1,2);
/*!40000 ALTER TABLE `alliance_buildings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alliance_buildlist`
--

DROP TABLE IF EXISTS `alliance_buildlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliance_buildlist` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliance_buildlist`
--

LOCK TABLES `alliance_buildlist` WRITE;
/*!40000 ALTER TABLE `alliance_buildlist` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_buildlist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alliance_history`
--

DROP TABLE IF EXISTS `alliance_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliance_history` (
  `history_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `history_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `history_text` text NOT NULL,
  `history_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`history_id`),
  KEY `latest` (`history_alliance_id`,`history_timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliance_history`
--

LOCK TABLES `alliance_history` WRITE;
/*!40000 ALTER TABLE `alliance_history` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alliance_news`
--

DROP TABLE IF EXISTS `alliance_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliance_news` (
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
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliance_news`
--

LOCK TABLES `alliance_news` WRITE;
/*!40000 ALTER TABLE `alliance_news` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alliance_points`
--

DROP TABLE IF EXISTS `alliance_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliance_points` (
  `point_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `point_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `point_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `point_points` bigint(12) unsigned NOT NULL DEFAULT '0',
  `point_avg` bigint(12) unsigned NOT NULL DEFAULT '0',
  `point_cnt` bigint(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`point_id`),
  KEY `point_user_id` (`point_alliance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Speichert den Punkteverlauf der Allianz';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliance_points`
--

LOCK TABLES `alliance_points` WRITE;
/*!40000 ALTER TABLE `alliance_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alliance_poll_votes`
--

DROP TABLE IF EXISTS `alliance_poll_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliance_poll_votes` (
  `vote_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `vote_poll_id` int(10) unsigned NOT NULL DEFAULT '0',
  `vote_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `vote_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `vote_number` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`vote_id`),
  KEY `vote_poll_id` (`vote_poll_id`),
  KEY `vote_user_id` (`vote_user_id`),
  KEY `vote_alliance_id` (`vote_alliance_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliance_poll_votes`
--

LOCK TABLES `alliance_poll_votes` WRITE;
/*!40000 ALTER TABLE `alliance_poll_votes` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_poll_votes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alliance_polls`
--

DROP TABLE IF EXISTS `alliance_polls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliance_polls` (
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliance_polls`
--

LOCK TABLES `alliance_polls` WRITE;
/*!40000 ALTER TABLE `alliance_polls` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_polls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alliance_rankrights`
--

DROP TABLE IF EXISTS `alliance_rankrights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliance_rankrights` (
  `rr_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rr_rank_id` int(10) unsigned NOT NULL DEFAULT '0',
  `rr_right_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rr_id`),
  KEY `rr_rank_id` (`rr_rank_id`),
  KEY `rr_right_id` (`rr_right_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliance_rankrights`
--

LOCK TABLES `alliance_rankrights` WRITE;
/*!40000 ALTER TABLE `alliance_rankrights` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_rankrights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alliance_ranks`
--

DROP TABLE IF EXISTS `alliance_ranks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliance_ranks` (
  `rank_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rank_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `rank_name` varchar(30) NOT NULL,
  `rank_level` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `rank_points` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rank_id`),
  KEY `rank_alliance_id` (`rank_alliance_id`,`rank_level`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliance_ranks`
--

LOCK TABLES `alliance_ranks` WRITE;
/*!40000 ALTER TABLE `alliance_ranks` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_ranks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alliance_rights`
--

DROP TABLE IF EXISTS `alliance_rights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliance_rights` (
  `right_id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `right_key` varchar(30) NOT NULL,
  `right_desc` text NOT NULL,
  PRIMARY KEY (`right_id`),
  KEY `right_key` (`right_key`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliance_rights`
--

LOCK TABLES `alliance_rights` WRITE;
/*!40000 ALTER TABLE `alliance_rights` DISABLE KEYS */;
INSERT INTO `alliance_rights` VALUES (1,'editdata','Allianzdaten (Name, Tag, Beschreibung, Bild, Link) ändern'),(2,'viewmembers','Mitglieder anschauen'),(3,'applicationtemplate','Bewerbungsvorlage bearbeiten'),(4,'history','Allianzgeschichte betrachten'),(5,'massmail','Allianzinternes Rundmail versenden'),(6,'ranks','Allianzränge bearbeiten'),(7,'alliancenews','Allianznews (Rathaus) verfassen'),(8,'relations','Allianzbeziehungen (Bündnisse / Kriege) verwalten'),(10,'allianceboard','Forum verwalten'),(11,'editmembers','Mitglieder verwalten'),(12,'applications','Bewerbungen bearbeiten'),(13,'polls','Umfrage erstellen'),(14,'fleetminister','Allianzflotten bearbeiten'),(15,'wings','Wings hinzufügen und entfernen'),(16,'buildminister','Allianzbasis ausbauen (Gebäude, Technologien)'),(17,'cryptominister','Kryptocenter benutzen');
/*!40000 ALTER TABLE `alliance_rights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alliance_spends`
--

DROP TABLE IF EXISTS `alliance_spends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliance_spends` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliance_spends`
--

LOCK TABLES `alliance_spends` WRITE;
/*!40000 ALTER TABLE `alliance_spends` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_spends` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alliance_stats`
--

DROP TABLE IF EXISTS `alliance_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliance_stats` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliance_stats`
--

LOCK TABLES `alliance_stats` WRITE;
/*!40000 ALTER TABLE `alliance_stats` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alliance_techlist`
--

DROP TABLE IF EXISTS `alliance_techlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliance_techlist` (
  `alliance_techlist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alliance_techlist_alliance_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_tech_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_current_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_build_start_time` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_build_end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `alliance_techlist_member_for` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`alliance_techlist_id`),
  KEY `alliance_techlist_alliance_id` (`alliance_techlist_alliance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliance_techlist`
--

LOCK TABLES `alliance_techlist` WRITE;
/*!40000 ALTER TABLE `alliance_techlist` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliance_techlist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alliance_technologies`
--

DROP TABLE IF EXISTS `alliance_technologies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliance_technologies` (
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
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliance_technologies`
--

LOCK TABLES `alliance_technologies` WRITE;
/*!40000 ALTER TABLE `alliance_technologies` DISABLE KEYS */;
INSERT INTO `alliance_technologies` VALUES (4,'Tarntechnik','In Zeiten einer neuen Ära mit grösseren Flottenverbänden bestehend aus mehreren Teilflotten, reichte die gewöhnliche Tarntechnik nicht mehr aus. So setzten sich Spieler zusammen und teilten ihr Wissen und ihre Ressourcen, um auch diese Hürde zu überwinden.\r\nJe höher diese Technologie erforscht ist, desto länger bleiben Allianzverbände für den Gegner unentdeckt.','In Zeiten einer neuen Ära mit grösseren Flottenverbänden bestehend aus mehreren Teilflotten, reichte die gewöhnliche Tarntechnik nicht mehr aus. So setzten sich Spieler zusammen und teilten ihr Wissen und ihre Ressourcen, um auch diese Hürde zu überwinden.\r\nJe höher diese Technologie erforscht ist, desto länger bleiben Allianzverbände für den Gegner unentdeckt.',75000,25000,50000,50000,50000,900,'1.60',50,1,0,0),(5,'Waffentechnik','','',0,0,0,0,0,0,'1.00',50,1,5,2),(6,'Schutzschilder','','',0,0,0,0,0,0,'1.00',50,1,5,2),(7,'Panzerung','','',0,0,0,0,0,0,'1.00',50,1,5,2),(8,'Spionagetechnik','','',0,0,0,0,0,0,'1.00',0,1,5,1),(9,'Antriebstechnologie','','',0,0,0,0,0,0,'1.00',0,1,5,11);
/*!40000 ALTER TABLE `alliance_technologies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `allianceboard_cat`
--

DROP TABLE IF EXISTS `allianceboard_cat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `allianceboard_cat` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `allianceboard_cat`
--

LOCK TABLES `allianceboard_cat` WRITE;
/*!40000 ALTER TABLE `allianceboard_cat` DISABLE KEYS */;
/*!40000 ALTER TABLE `allianceboard_cat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `allianceboard_catranks`
--

DROP TABLE IF EXISTS `allianceboard_catranks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `allianceboard_catranks` (
  `cr_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cr_rank_id` int(10) unsigned NOT NULL,
  `cr_cat_id` int(10) unsigned NOT NULL,
  `cr_bnd_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cr_id`),
  KEY `cr_rank_id` (`cr_rank_id`),
  KEY `cr_cat_id` (`cr_cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `allianceboard_catranks`
--

LOCK TABLES `allianceboard_catranks` WRITE;
/*!40000 ALTER TABLE `allianceboard_catranks` DISABLE KEYS */;
/*!40000 ALTER TABLE `allianceboard_catranks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `allianceboard_posts`
--

DROP TABLE IF EXISTS `allianceboard_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `allianceboard_posts` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `allianceboard_posts`
--

LOCK TABLES `allianceboard_posts` WRITE;
/*!40000 ALTER TABLE `allianceboard_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `allianceboard_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `allianceboard_topics`
--

DROP TABLE IF EXISTS `allianceboard_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `allianceboard_topics` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `allianceboard_topics`
--

LOCK TABLES `allianceboard_topics` WRITE;
/*!40000 ALTER TABLE `allianceboard_topics` DISABLE KEYS */;
/*!40000 ALTER TABLE `allianceboard_topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alliances`
--

DROP TABLE IF EXISTS `alliances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alliances` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Allianz-Daten';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alliances`
--

LOCK TABLES `alliances` WRITE;
/*!40000 ALTER TABLE `alliances` DISABLE KEYS */;
/*!40000 ALTER TABLE `alliances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asteroids`
--

DROP TABLE IF EXISTS `asteroids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asteroids` (
  `id` int(10) unsigned NOT NULL,
  `res_metal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_crystal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_plastic` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_fuel` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_food` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_power` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asteroids`
--

LOCK TABLES `asteroids` WRITE;
/*!40000 ALTER TABLE `asteroids` DISABLE KEYS */;
/*!40000 ALTER TABLE `asteroids` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attack_ban`
--

DROP TABLE IF EXISTS `attack_ban`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attack_ban` (
  `attack_ban_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `attack_ban_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `attack_ban_reason` text NOT NULL,
  `attack_ban_time` int(10) unsigned NOT NULL DEFAULT '0',
  `attack_ban_deleted` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`attack_ban_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attack_ban`
--

LOCK TABLES `attack_ban` WRITE;
/*!40000 ALTER TABLE `attack_ban` DISABLE KEYS */;
/*!40000 ALTER TABLE `attack_ban` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookmarks`
--

DROP TABLE IF EXISTS `bookmarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookmarks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bookmark_user_id` (`user_id`),
  KEY `absindex` (`user_id`,`entity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookmarks`
--

LOCK TABLES `bookmarks` WRITE;
/*!40000 ALTER TABLE `bookmarks` DISABLE KEYS */;
/*!40000 ALTER TABLE `bookmarks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `buddylist`
--

DROP TABLE IF EXISTS `buddylist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `buddylist` (
  `bl_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `bl_buddy_id` int(10) unsigned NOT NULL DEFAULT '0',
  `bl_allow` int(10) unsigned NOT NULL DEFAULT '0',
  `bl_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bl_comment` text NOT NULL,
  `bl_comment_buddy` text NOT NULL,
  PRIMARY KEY (`bl_id`),
  KEY `bl_buddy_id` (`bl_buddy_id`),
  KEY `bl_user_id` (`bl_user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=238 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buddylist`
--

LOCK TABLES `buddylist` WRITE;
/*!40000 ALTER TABLE `buddylist` DISABLE KEYS */;
/*!40000 ALTER TABLE `buddylist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `building_points`
--

DROP TABLE IF EXISTS `building_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `building_points` (
  `bp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bp_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `bp_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bp_points` decimal(20,3) unsigned NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`bp_id`),
  KEY `bp_building_id` (`bp_building_id`),
  KEY `bp_level` (`bp_level`),
  KEY `bp_points` (`bp_points`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `building_points`
--

LOCK TABLES `building_points` WRITE;
/*!40000 ALTER TABLE `building_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `building_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `building_queue`
--

DROP TABLE IF EXISTS `building_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `building_queue` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `building_queue`
--

LOCK TABLES `building_queue` WRITE;
/*!40000 ALTER TABLE `building_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `building_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `building_requirements`
--

DROP TABLE IF EXISTS `building_requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `building_requirements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `obj_id` (`obj_id`,`req_building_id`,`req_tech_id`)
) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `building_requirements`
--

LOCK TABLES `building_requirements` WRITE;
/*!40000 ALTER TABLE `building_requirements` DISABLE KEYS */;
INSERT INTO `building_requirements` VALUES (1,1,6,0,1),(2,2,6,0,1),(5,5,6,0,1),(6,7,6,0,1),(11,12,6,0,1),(12,3,1,0,2),(13,3,12,0,3),(14,4,12,0,4),(15,4,2,0,3),(16,8,12,0,5),(17,10,8,0,2),(18,9,10,0,2),(19,9,8,0,4),(20,11,9,0,1),(21,13,0,3,3),(22,14,0,3,8),(23,15,0,3,5),(24,16,1,0,3),(25,17,2,0,3),(26,18,3,0,4),(27,19,5,0,3),(28,20,4,0,4),(29,13,6,0,1),(30,14,6,0,1),(31,15,6,0,1),(32,4,1,0,1),(33,21,8,0,7),(35,22,0,3,10),(36,22,10,10,8),(37,23,1,0,10),(38,23,2,0,9),(39,23,14,0,6),(40,23,0,3,8),(41,23,0,16,3),(50,25,0,24,1),(51,25,11,0,12),(52,25,10,0,10),(53,24,11,0,11),(54,24,14,0,5),(55,24,0,7,13),(56,24,0,25,7),(57,26,16,0,1),(58,26,17,0,1),(59,26,18,0,1),(60,26,19,0,1),(61,26,20,0,1),(62,27,11,0,5),(63,27,0,3,5),(64,27,0,25,3),(65,27,0,11,5);
/*!40000 ALTER TABLE `building_requirements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `building_types`
--

DROP TABLE IF EXISTS `building_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `building_types` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL,
  `type_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type_color` char(7) NOT NULL DEFAULT '#ffffff',
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `building_types`
--

LOCK TABLES `building_types` WRITE;
/*!40000 ALTER TABLE `building_types` DISABLE KEYS */;
INSERT INTO `building_types` VALUES (1,'Allgemeine Gebäude',1,'#ffffff'),(2,'Rohstoffgebäude',2,'#ffffff'),(3,'Kraftwerke',3,'#ffffff'),(4,'Speicher',4,'#ffffff');
/*!40000 ALTER TABLE `building_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `buildings`
--

DROP TABLE IF EXISTS `buildings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `buildings` (
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
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buildings`
--

LOCK TABLES `buildings` WRITE;
/*!40000 ALTER TABLE `buildings` DISABLE KEYS */;
INSERT INTO `buildings` VALUES (1,'Titanmine',2,'Produziert Titan.','Produziert Titan.',100,45,0,0,0,0,'1.90','0.20',10,0,0,65,0,0,0,0,0,'1.50',0,0,0,0,0,'0.00',0,50,2,1,0,0,0,0,0,0),(2,'Siliziummine',2,'Produziert Silizium.','Produziert Silizium.',150,50,0,0,0,0,'1.90','0.20',20,0,0,0,50,0,0,0,0,'1.50',0,0,0,0,0,'0.00',0,50,2,1,1,0,0,0,0,0),(3,'Chemiefabrik',2,'Produziert PVC.','Produziert PVC.',100,80,0,0,0,0,'1.90','0.20',20,0,0,0,0,40,0,0,0,'1.50',0,0,0,0,0,'0.00',0,50,3,1,3,0,0,0,0,0),(4,'Tritiumsynthesizer',2,'Produziert Tritium.','Produziert Tritium.',160,110,0,50,0,0,'2.00','0.20',50,0,0,0,0,0,28,0,0,'1.50',0,0,0,0,0,'0.00',0,50,3,1,4,0,0,0,0,0),(5,'Gewächshaus',2,'Produziert Nahrung.','Produziert Nahrung.',80,100,0,0,0,0,'1.90','0.20',5,0,0,0,0,0,0,40,0,'1.50',0,0,0,0,0,'0.00',0,50,2,1,5,0,0,0,0,0),(6,'Planetenbasis',1,'Das Grundgebäude jedes Planeten bietet Platz für Bewohner, Lagerräume und produziert Rohstoffe.','Die Planetenbasis ist die Schaltzentrale aller Aktivitäten auf deinem Planeten. Du musst zuerst eine Planetenbasis bauen, danach kannst du alle weiteren Gebäude errichten. Die Planetenbasis liefert ein Grundeinkommen an Rohstoffen und eine minimale Energieversorgung durch ein integriertes Erdwärmekraftwerk. Es ist jedoch sinnvoll, Minen und Fabriken zu bauen, um die Rohstoffproduktion zu steigern.',500,250,0,300,0,0,'2.00','0.00',50,0,0,50,20,10,5,15,200,'1.00',100000,100000,100000,100000,100000,'1.00',300,1,5,1,0,0,1,0,0,0),(7,'Wohnmodul',1,'Mit einem Wohnmodul wird die Kapazität für Bewohner erhöht.','Mit steigendem Wachstum eines Planeten werden immer mehr Gebäude errichtet und ausgebaut, wofür mehr Arbeiter benötigt werden.\r\nEin Ausbau des Wohnmoduls ist deshalb wichtig, welches die Kapazität der Bewohner erhöht und so potenzielle Arbeiter freigibt.',50,30,0,150,0,0,'2.00','0.40',0,0,0,0,0,0,0,0,0,'0.00',0,0,0,0,0,'1.80',300,50,1,1,1,0,0,0,0,0),(8,'Forschungslabor',1,'Im Labor werden neue Techniken entwickelt. Höhere Stufen senken die Forschungszeit.','Damit Schiffe und Spezialgebäude errichten werden können, braucht es ein Forschungslabor, in dem die Wissenschaftler neue Technologien entwickeln. Je höher das Forschungslabor ausgebaut ist, desto mehr Technologien können entwickelt werden. Erforschte Technologien gelten automatisch auf allen Planeten deines Reiches.\r\nAusserdem senkt das Forschungslabor die Forschungszeit, jedoch erst ab einer bestimmten Stufe!\r\nUm zur Elite auf dem Gebiet der Technologien zu gehören, ist ein guter Ausbau des Forschungslabors unverzichtbar. ',500,700,210,350,0,0,'2.00','0.40',0,0,0,0,0,0,0,0,0,'0.00',0,0,0,0,0,'0.00',0,50,4,1,2,0,1,0,0,0),(9,'Schiffswerft',1,'In der Werft werden alle Raumschiffe gebaut.Höhere Stufen senken die Bauzeit.','In der Schiffswerft werden Schiffe gebaut, die im Krieg oder für den Handel mit anderen Völkern eingesetzt werden können. Je höher die Werft, desto mehr Schiffe können gebaut werden.\r\nAusserdem senkt die Schiffswerft die Bauzeit der Schiffe, jedoch erst ab einer bestimmten Stufe!',900,680,510,780,0,0,'1.80','0.40',0,0,0,0,0,0,0,0,0,'0.00',0,0,0,0,0,'0.00',0,50,6,1,3,0,1,0,0,0),(10,'Waffenfabrik',1,'In der Waffenfabrik werden Verteidigungsanlagen gebaut. Höhere Stufen senken die Bauzeit.','Die Waffenfabrik bietet jedem Volk die Möglichkeit, Verteidigungsanlagen gegen feindliche Angriffe zu errichten.\r\nVerteidigungsanlagen funktionieren, wenn sie mal gebaut sind, selbstständig und eröffnen das Feuer gegen angreifende Flotten. \r\nAusserdem senkt der Ausbau der Waffenfabrik die Bauzeit der Verteidigungsanlagen, jedoch erst ab einer bestimmten Stufe!',750,480,320,500,0,0,'1.80','0.40',0,0,0,0,0,0,0,0,0,'0.00',0,0,0,0,0,'0.00',0,50,5,1,4,0,1,0,0,0),(11,'Flottenkontrolle',1,'Koordiniert deine Flotten. Je weiter die Flottenkontrolle ausgebaut ist, desto mehr Flotten können starten.','Die Flottenkontrolle ist ein Gebäude voller Überwachungscomputer, Leitsystemen, Empfänger- sowie Sendeanlagen. Mit Hilfe der Flottenkontrolle werden Flotten gesteuert. Sie ist ebenfalls Voraussetzung für den Bau von Schiffen. Je weiter die Flottenkontrolle ausgebaut ist, desto mehr Flotten können vom Planeten gestartet werden.',1100,750,0,500,0,0,'1.80','0.40',0,0,0,0,0,0,0,0,0,'0.00',0,0,0,0,0,'0.00',0,50,5,1,5,0,0,0,0,0),(12,'Windkraftwerk',3,'Nicht sehr effizientes und relativ teures Kraftwerk, welches Energie mit Hilfe des Windes gewinnt.','Windenergieanlagen wandeln mit Hilfe des Rotors die Windenergie in eine Drehbewegung um. Mit Hilfe von Generatoren wird diese Drehbewegung in eine elektrische Energie umgewandelt, welche dann in das Stromnetz des Planeten eingespeist wird.\r\nWindenergie ist eine alternative Energie, jedoch noch nicht sehr effizient. Der Bau ist relativ teuer und die Produktion nur mittelmässig.',250,50,5,80,0,0,'1.90','0.20',0,0,0,0,0,0,0,0,80,'1.65',0,0,0,0,0,'0.00',0,50,1,1,0,0,0,0,0,0),(13,'Solarkraftwerk',3,'Solarkraftwerke gewinnen Energie durch Sonnenlicht. ','In einer Solarstromanlage findet die Umwandlung von Sonnenenergie in elektrische Energie statt. Eine Solarstromanlage besteht aus mehreren Komponenten. Der Generator empfängt und wandelt die Lichtenergie in elektrische Energie um. Als Empfänger dient die Solarzelle. Hierbei kommen Spiegel oder Linsensysteme zum Einsatz, die die Strahlung auf die Zellen umleiten und konzentrieren.\r\nEiner der wichtigsten Bestandteile einer Solarzelle ist das Metal Silizium. Dieses hat die Eigenschaft, unter Bestrahlung von Licht eine elektrische Spannung erzeugen zu können.\r\nDiese Methode für die Energieerzeugung ist noch sehr jung und unerforscht. Wegen den grossen Mengen an Silizium die es benötigt, wird das Solarkraftwerk oft als unrentabel bezeichnet, jedoch kann sich die Energiegewinnung daraus sehen lassen.',150,250,0,160,0,0,'1.90','0.20',0,0,0,0,0,0,0,0,100,'1.70',0,0,0,0,0,'0.00',0,50,1,1,2,0,0,0,0,0),(14,'Fusionskraftwerk',3,'Durch die Fusion von Tritium und Deuterium werden im Fusionskraftwerk riesige Energiemengen gewonnen. ','Als Kernfusion wird der Prozess des Verschmelzens zweier Atomkerne zu einem schwereren Kern bezeichnet. Besonders viel Energie wird frei, wenn Deuterium und Tritium miteinander verschmelzen. Hier beträgt der Massendefekt fast 4 Promille. Die fehlende Masse wird aufgrund der Äquivalenz von Masse und Energie aus Einsteins Gleichung E=mc^2 als kinetische Energie auf die Reaktionsprodukte übertragen. Da c^2 eine sehr grosse Zahl ist, setzt schon die Fusion kleiner Mengen von Deuterium und Tritium gewaltige Energiemengen frei.\r\nDie Effizienz dieses Kraftwerkes wird pro Stufe immer wie grösser! Die Energie, welche das Kraftwerk in den ersten Stufen freisetzt, wird oft als normal angesehen, jedoch stellt sich schon sehr früh heraus, dass beim weiteren Ausbau des Fusionskraftwerkes die Effizient beachtlich gesteigert wird!',3000,4900,8300,1500,0,0,'1.90','0.20',0,0,0,0,0,0,0,0,1500,'1.95',0,0,0,0,0,'0.00',0,50,2,1,5,0,0,0,0,0),(15,'Gezeitenkraftwerk',3,'Dieses Kraftwerk gewinnt Energie durch den Hubunterschied der Gezeiten.','Ein Gezeitenkraftwerk ist ein Kraftwerk zur Produktion von elektrischem Strom, das durch die Tide angetrieben wird. Sie sind eine Sonderform der Wasserkraftwerke.\r\nGezeitenkraftwerke werden an Meeresbuchten und in Ästuaren errichtet, die einen besonders hohen Tidenhub haben. Dazu wird die entsprechende Bucht durch einen Deich abgedämmt. Dadurch kann das Wasser der Tidenströme durch die Turbinen strömen, die aufgrund der Gezeitenströme, welche viermal am Tag die Richtung wechseln, auf Zweirichtungsbetrieb eingestellt sind.',2100,1000,500,2000,0,0,'1.85','0.20',0,0,0,0,0,0,0,0,750,'1.75',0,0,0,0,0,'0.00',0,50,3,1,3,0,0,0,0,0),(16,'Titanspeicher',4,'Lagert Titan.','Lagert Titan. Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.',4000,100,0,100,0,0,'2.00','0.20',0,0,0,0,0,0,0,0,0,'0.00',100000,0,0,0,0,'1.80',0,50,1,1,0,0,0,0,0,0),(17,'Siliziumspeicher',4,'Lagert Silizium.','Lagert Silizium. Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.',100,3500,0,100,0,0,'2.00','0.20',0,0,0,0,0,0,0,0,0,'0.00',0,100000,0,0,0,'1.80',0,50,1,1,1,0,0,0,0,0),(18,'Lagerhalle',4,'Lagert Plastik.','Lagert Plastik. Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.',50,50,0,3750,0,0,'2.00','0.20',0,0,0,0,0,0,0,0,0,'0.00',0,0,100000,0,0,'1.80',0,50,1,1,2,0,0,0,0,0),(19,'Nahrungssilo',4,'Lagert Nahrung.','Lagert Nahrung.Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.',1000,1000,0,1000,0,0,'2.00','0.20',0,0,0,0,0,0,0,0,0,'0.00',0,0,0,0,100000,'1.80',0,50,1,1,4,0,0,0,0,0),(20,'Tritiumsilo',4,'Lagert Tritium.','Lagert Tritium. Wenn die Lagerkapazität des Speichers überschritten ist, können keine weiteren Rohstoffe produziert bzw. gefördert werden. Baue in diesem Fall den Speicher aus.',500,500,3000,0,0,0,'2.00','0.20',0,0,0,0,0,0,0,0,0,'0.00',0,0,0,100000,0,'1.80',0,50,1,1,3,0,0,0,0,0),(21,'Marktplatz',1,'Auf dem Marktplatz können Schiffe und Rohstoffe gehandelt und ersteigert werden.','Der Marktplatz bildet das Zentrum aller Händler in Andromeda.\r\nHand mit Schiffen, Rohstoffen, \r\nJe höher der Marktplatz ausgebaut ist, desto mehr Waren können gleichzeitig angeboten werden.\r\nAusserdem werden mehr Waren zurück erstattet, wenn ein Angebot zurückgezogen wird.\r\nDer Markt kann aber nicht beliebig weit ausgebaut werden, sondern ist durch ein Maximallevel beschränkt.',30000,25000,3500,35000,0,0,'1.50','1.50',0,0,0,0,0,0,0,0,0,'0.00',0,0,0,0,0,'0.00',0,10,4,1,6,0,0,0,0,0),(22,'Orbitalplattform',1,'Die Orbitalplattform erhöht den Platz auf einem Planeten und bietet Lagerräume für Ressourcen.','Die Orbitalplattform erhöht die Anzahl verfügbarer Felder auf einem Planeten. Dies wird besonders wichtig, wenn ein Planet nicht allzu viele Felder besitzt, oder viele Verteidigungsanlagen errichtet wurden. Ebenfalls befinden sich auf der Plattform zusätzliche Lagerräume für diverse Ressourcen.\r\nPro Ausbaustufe erhöht sich die Anzahl der Felder, ebenso die Grösse der Lagerräume.',30000,60000,50000,55000,0,0,'1.90','0.00',100,0,0,0,0,0,0,0,0,'1.80',10000,15000,20000,0,0,'2.00',0,50,0,1,7,60,0,0,0,0),(23,'Multimine',2,'Dieses riesige Mine fördert Titan und Silizium zu Tage und kann auch eine gewisse Menge an Rohstoffen speichern. Allerdings verbraucht sie enorm viel Energie!','Dieses riesige Mine fördert Titan und Silizium zu Tage und kann auch eine gewisse Menge an Rohstoffen speichern. Allerdings verbraucht sie enorm viel Energie! Da sie so enorm gross ist, braucht sie viele Felder und kann nur bis zu Stufe 15 gebaut werden.',5100,7200,160,1100,0,0,'2.00','0.00',100,0,0,100,70,0,0,0,0,'1.80',50000,50000,0,0,0,'1.50',0,15,8,0,20,0,0,0,0,0),(24,'Kryptocenter',1,'Das Kryptocenter analysiert Kommunikationskanäle um Infos über fremde Flottenbewegungen zu erhalten. ','Das Kryptocenter analysiert Kommunikationskanäle zwischen Flotten und Bodenstationen, um Aufschluss über fremde Flottenbewegungen zu erhalten. Mit Hilfe eines riesigen unterirdischen Rechenzentrums werden die gewonnenen Daten analysiert, entschlüsselt und ausgewertet, deshalb braucht diese Anlage enorm viel Energie zum Bau und zum  Betrieb. Je höher der Level dieser Anlage, desto grösser ist auch die Reichweite des Scanners.',50000,450000,650000,50000,0,1000000,'1.50','0.10',0,1000000,0,0,0,0,0,0,0,'1.50',0,0,0,0,0,'0.00',0,10,5,0,11,0,0,0,0,0),(25,'Raketensilo',1,'Im Raketensilo werden Raketen gebaut unt gestartet, um gegnerische Verteidigungsanlagen zu beschädigen.','Im Raketensilo werden Raketen gelagert und gestartet, mit denen man gegnerische Verteidigungsanlagen beschädigen oder ausser Gefecht setzen kann, sowie Raketen um gegnerische Raketen abzufangen. Je grösser das Silo ist, desto mehr Raketen können darin gelagert werden.',100000,50000,70000,20000,0,20000,'1.40','0.00',50000,0,300,0,0,0,0,0,0,'1.10',0,0,0,0,0,'0.00',0,20,2,1,10,0,0,0,0,0),(26,'Rohstoffbunker',1,'In diesem Bunker kann im Falle eines Angriffs ein Teil der Rohstoffe versteckt werden.','In diesem Bunker kann im Falle eines Angriffs ein Teil der Rohstoffe versteckt werden, so dass sie nicht geklaut werden können. Das Verstecken geschieht automatisch. Auf Stufe 1 können 5000 Resourcen versteckt werden, pro Stufe verdoppelt sich diese Anzahl.',5000,1000,0,2000,0,0,'2.00','0.50',0,0,0,0,0,0,0,0,0,'0.00',0,0,0,0,0,'2.00',0,10,0,1,8,0,0,5000,0,0),(27,'Flottenbunker',1,'','',20000,10000,0,5000,0,0,'2.00','0.50',0,0,0,0,0,0,0,0,0,'0.00',0,0,0,0,0,'2.00',0,10,0,1,9,0,0,0,5,2500);
/*!40000 ALTER TABLE `buildings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `buildlist`
--

DROP TABLE IF EXISTS `buildlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `buildlist` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buildlist`
--

LOCK TABLES `buildlist` WRITE;
/*!40000 ALTER TABLE `buildlist` DISABLE KEYS */;
/*!40000 ALTER TABLE `buildlist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cells`
--

DROP TABLE IF EXISTS `cells`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cells` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cells`
--

LOCK TABLES `cells` WRITE;
/*!40000 ALTER TABLE `cells` DISABLE KEYS */;
/*!40000 ALTER TABLE `cells` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat`
--

DROP TABLE IF EXISTS `chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat` (
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
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat`
--

LOCK TABLES `chat` WRITE;
/*!40000 ALTER TABLE `chat` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_banns`
--

DROP TABLE IF EXISTS `chat_banns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_banns` (
  `user_id` varchar(50) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_banns`
--

LOCK TABLES `chat_banns` WRITE;
/*!40000 ALTER TABLE `chat_banns` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_banns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_log`
--

DROP TABLE IF EXISTS `chat_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_log` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_log`
--

LOCK TABLES `chat_log` WRITE;
/*!40000 ALTER TABLE `chat_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat_users`
--

DROP TABLE IF EXISTS `chat_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_users` (
  `nick` varchar(30) NOT NULL,
  `user_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `kick` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat_users`
--

LOCK TABLES `chat_users` WRITE;
/*!40000 ALTER TABLE `chat_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
  `config_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `config_name` varchar(50) NOT NULL,
  `config_value` text NOT NULL,
  `config_param1` text NOT NULL,
  `config_param2` text NOT NULL,
  PRIMARY KEY (`config_id`),
  UNIQUE KEY `config_name_2` (`config_name`)
) ENGINE=MyISAM AUTO_INCREMENT=194 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` VALUES (1,'roundname','Runde 12','',''),(2,'roundurl','http://round12.live.etoa.net','',''),(3,'loginurl','http://etoa.ch','',''),(4,'user_timeout','2400','',''),(5,'mail_sender','no-reply@etoa.ch','',''),(6,'mail_reply','mail@etoa.ch','',''),(7,'enable_register','0','1361386800','1000'),(8,'enable_login','1','1361559600',''),(9,'round_end','0','1205485200',''),(10,'points_update','3600','1000','100'),(11,'statsupdate','1361286002','0',''),(12,'stats_num_rows','50','',''),(13,'url_rules','http://etoa.ch/rules','',''),(14,'url_bugs','http://etoa.ch/forum/index.php?page=Thread&threadID=7927','',''),(15,'url_teamspeak','http://etoa.ch/404.html','',''),(16,'messages_threshold_days','28','14',''),(17,'reports_threshold_days','42','42',''),(18,'referers','http://round12.live.etoa.net\r\nhttp://dev.etoa.net\r\nhttp://etoa.ch\r\nhttp://www.etoa.ch','',''),(19,'under_construction','','',''),(20,'system_message','','',''),(21,'offline','0','',''),(22,'offline_message','','',''),(23,'offline_ips_allow','','',''),(24,'register_key','','',''),(25,'bot_max_count','5','',''),(26,'global_time','12','',''),(27,'shipdefbuild_cancel_time','15','',''),(28,'build_time_boni_forschungslabor','5','10','0.2'),(29,'build_time_boni_schiffswerft','5','10',''),(30,'build_time_boni_waffenfabrik','5','10',''),(31,'ship_build_time','0.8','',''),(32,'def_build_time','0.8','',''),(33,'build_build_time','1','',''),(34,'flight_flight_time','1','',''),(35,'flight_start_time','1','',''),(36,'flight_land_time','1','',''),(37,'res_build_time','1','',''),(38,'num_of_sectors','','2','2'),(39,'num_of_cells','','10','10'),(40,'num_planets','','5','20'),(41,'space_percent_solsys','50','',''),(42,'space_percent_asteroids','12','',''),(43,'space_percent_nebulas','12','',''),(44,'space_percent_wormholes','12','',''),(45,'num_planet_images','5','',''),(46,'planet_fields','','600','2500'),(47,'planet_temp','20','-155','166'),(48,'field_squarekm','11694','',''),(49,'cell_length','300','',''),(50,'map_init_sector','','1','1'),(51,'user_planet_name','Startplanet','',''),(52,'user_min_fields','1200','',''),(53,'user_max_planets','15','',''),(54,'asteroid_ress','','10000','1000000'),(55,'nebula_ress','','100000','3000000'),(56,'wh_update','172800','1',''),(57,'gasplanet','7','3600','500'),(58,'solsys_percent_planet','85','',''),(59,'solsys_percent_asteroids','5','',''),(60,'user_attack_min_points','5000','',''),(61,'user_attack_percentage','0.2','',''),(62,'invade_possibility','0.5','1','0.1'),(63,'invade_ship_destroy','0.3','',''),(64,'def_restore_percent','0.4','',''),(65,'def_wf_percent','0.4','',''),(66,'ship_wf_percent','0.5','',''),(67,'deactivate_fleet','0','',''),(68,'ship_bomb_factor','5','10',''),(69,'battle_rounds','5','',''),(70,'gasattack_action','25','95',''),(71,'elorating','1600','15',''),(72,'battle_rebuildable','0','0.75','1'),(73,'rebuildable_costs','0.25','',''),(74,'invade_active_users','0','',''),(75,'alliance_fleets_max_players','1','3',''),(76,'res_update','300','',''),(77,'def_store_capacity','200000','',''),(78,'user_start_metal','4000','',''),(79,'user_start_crystal','3000','',''),(80,'user_start_plastic','2500','',''),(81,'user_start_fuel','200','',''),(82,'user_start_food','500','',''),(83,'user_start_people','200','250',''),(84,'people_food_require','12','',''),(85,'people_multiply','1.1','',''),(86,'people_work_done','3','',''),(87,'specialistconfig','0.3','10','100000'),(88,'market_enabled','1','',''),(89,'market_response_time','14','',''),(90,'market_ship_action_ress','market','',''),(91,'market_ship_action_ship','market','',''),(92,'market_ship_flight_time','','15','180'),(93,'market_auction_delay_time','24','',''),(94,'market_rate_0','1','',''),(95,'market_rate_1','1','',''),(96,'market_rate_2','1','',''),(97,'market_rate_3','1','',''),(98,'market_rate_4','1','',''),(99,'default_image_path','images/imagepacks/Discovery','',''),(100,'default_css_style','Graphite','',''),(101,'imagepack_zip_format','zip','',''),(102,'imagepack_predirectory','','',''),(103,'imagesize','220','120','40'),(104,'num_nebula_images','9','',''),(105,'num_asteroid_images','5','',''),(106,'num_space_images','10','',''),(107,'num_wormhole_images','1','',''),(108,'wordbanlist','','',''),(109,'msg_flood_control','10','',''),(110,'msg_ban_hours','0','',''),(111,'mailqueue','50','',''),(112,'msg_max_store','200','20',''),(113,'password_minlength','6','30',''),(114,'hmode_days','2','42','1'),(115,'user_inactive_days','7','21','8'),(116,'user_ban_min_length','1','',''),(117,'user_umod_min_length','2','',''),(118,'user_sitting_days','12','2',''),(119,'online_threshold','5','',''),(120,'nick_length','','3','15'),(121,'main_planet_changetime','7','',''),(122,'name_length','30','',''),(123,'user_delete_days','5','',''),(124,'profileimagecheck_done','1209569797','',''),(125,'admininfo','','',''),(126,'admin_timeout','1200','',''),(127,'htaccess','htpasswd2','.htaccess','cache/security/.htpasswd'),(128,'admin_htaccess','Administration-','etoa',''),(129,'admin_dateformat','Y-m-d H:i:s','',''),(130,'flightban','0','',''),(131,'battleban','0','',''),(132,'battleban_time','','1234165500','1234174500'),(133,'flightban_time','','1199293080','1199552280'),(134,'battleban_arrival_text','','Die ankommenden Schiffe sind auf dem Planeten gelandet. Nach einer kurzen Kaffeepause der Piloten kehrten sie wieder um und machten sich auf den Rückflug.','Auf dem Weg zu ihrem Ziel flogen deine Raketen in ein intergalaktisches Warpfeld. Sie wurden deaktiviert und in ihr Lager gebeamt.'),(135,'asteroid_action','30','20','0'),(136,'gascollect_action','20','10','1000'),(137,'nebula_action','30','50','1000'),(138,'antrax_action','30','90',''),(139,'spyattack_action','3','1','10'),(140,'userrank_total','Imperator von Andromeda','',''),(141,'userrank_buildings','Grossbaumeister von Andromeda','',''),(142,'userrank_tech','Hochtechnokrat von Andromeda','',''),(143,'userrank_fleet','Flottenadmiral von Andromeda','',''),(144,'userrank_battle','Generalfeldmarschall von Andromeda','',''),(145,'userrank_trade','Handelsfürst von Andromeda','',''),(146,'userrank_diplomacy','Botschafter von Andromeda','',''),(147,'userrank_exp','Kriegsheld von Andromeda','',''),(148,'alliance_membercosts_factor','0.9','',''),(149,'alliance_shippoints_per_hour','5','',''),(150,'alliance_shipcosts_factor','1.02','',''),(151,'alliance_tech_bonus','10','',''),(152,'alliance_war_time','48','48',''),(153,'alliance_shippoints_base','1.4','',''),(154,'allow_wings','0','',''),(155,'townhall_ban','86400','Nichtbeachtung der Rathaus-Regeln',''),(156,'discoverymask','0.4','10','5'),(157,'discover_percent_pirates','10','a,e,n,s,w','7'),(158,'discover_percent_aliens','5','a,e,n,s,w','1'),(159,'discover_percent_resources','35','a,e,n,s,w',''),(160,'discover_percent_ships','25','a,e,n,s,w',''),(161,'discover_percent_total_lost','1','a,e,n,s,w',''),(162,'discover_percent_fast_flight','8','a,e,n,s,w','80'),(163,'discover_percent_slow_fight','5','a,e,n,s,w','1.80'),(164,'discover_percent_sheet','3','a,e,n,s,w',''),(165,'discover_pirates','1','5','1.5'),(166,'discover_aliens','5','10','1.75'),(167,'discover_resources','5000','50','5'),(168,'discover_fleet','5','1','15'),(169,'random_event_hits','0','',''),(170,'random_event_misses','0','',''),(171,'log_threshold_days','28','',''),(172,'sessionlog_store_days','','30','60'),(173,'daemon_exe','/usr/local/bin/etoad','',''),(174,'daemon_logfile','/var/log/etoad/roundx.log','',''),(175,'daemon_pidfile','/var/run/etoad/roundx.pid','',''),(176,'daemon_ipckey','1','',''),(177,'backend_status','0','',''),(178,'backend_offline_message','Der EtoA-Updatedienst ist momentan ausser Betrieb. Die Entwickler sind informiert. Es ist nicht notwendig ein Ticket zu eröffnen. Unterdessen werden weder Gebäude, Technologien, Schiffe, Verteidigungen, Planeten noch Flotten aktualisiert.','',''),(179,'backend_offline_mail','river@etoa.ch;mrcage@etoa.ch','',''),(180,'update_enabled','1','',''),(181,'backup_dir','/home/etoa/backup/','',''),(182,'backup_retention_time','14','',''),(183,'backup_use_gzip','0','',''),(184,'backup_time_interval','6','',''),(185,'backup_time_hour','1','',''),(186,'backup_time_minute','47','',''),(187,'debug','0','',''),(188,'accesslog','0','',''),(189,'crypto_enable','1','',''),(190,'cryptocenter','86400','7200','21600'),(191,'info','[color=yellow][b]Dies ist ein Testserver und die Spielwiese der Entwickler![/b] \r\nEs gibt weder Admin-Support noch werden irgendwelche Sachen erstattet. Rechnet damit dass eure Accs unter Umständen ohne Vorankündigung gelöscht oder angegriffen werden können, falls wir etwas testen müssen.[/color]\r\n\r\n[color=limegreen]Alle Infos zur Entwicklung: [url]https://dev.etoa.net[/url]\r\n\r\n[color orange][b]Fehler melden:[/b]\r\nDas es scheinbar noch das eine oder andere Missverständnis gibt: Rechtschreibefehler und Derartiges bitte unter [url]http://www.etoa.ch/forum/thread.php?postid=67971[/url] melden und Bugs unter [url]https://dev.etoa.net/trac/newticket[/url] und bei Milestone bitte Beta V3 auswählen und wenn möglich kurz schauen, ob der entsprechende Bug schon gemeldet wurde.\r\n\r\nViele Dank ihr macht somit uns, wie auch euch das Ganze einiges einfacher.[/color]\r\n\r\n[color blue][b]Flottenupdates:[/b]\r\nBezüglich des Flottenscripts ist es momentan noch möglich, dass Aktionen nicht korrekt ausgeführt werden oder dass das Script aufgrund einer Aktion blockiert wird. Vollen Support diesbezüglich wird es ab Donnerstag Abend geben.\r\n\r\nBesten Dank für euer Verständnis\r\n[/color]\r\n','',''),(192,'contact_message','Die folgenden Angaben betreffen nur die aktuelle Spielrunde. Bitte bei Problemen im Zusammenhang mit dem Spielablauf (Namenswechsel, Cheater/Buguser melden etc) die Game-Admins kontaktieren. Bei schweren Bugs oder Fragen bitte zuerst im Forum nachschauen, dann erst die Entwickler kontaktieren. Bitte nur ganz wichtige E-Mails an die Projektleitung senden, belanglose Mails und Spam werden stillschweigend ignoriert!','',''),(193,'welcome_message','Seid gegrüsst, Imperator!\r\n\r\nIch beglückwünsche Euch zum Antritt Eurer Regentschaft. Die Zukunft Eurer Rasse liegt nun in Euren Händen. Eure Heimatwelt hat sich soweit entwickelt dass ihre Bewohner sich danach sehnen die Galaxie um sie herum zu erkunden und fremde Welten zu besiedeln.\r\n\r\nLinks seht ihr die Navigation, mit der ihr Euer Reich verwalten könnt. Baut zuerst einige Gebäude um Rohstoffe zu fördern. Danach solltet ihr Forschungslabors und Werften errichten, damit ihr Raumschiffe bauen könnt um die Weiten von Andromeda zu erkunden. Bedenkt dass einige Gebäude Energie benötigen, vernachlässigt also den Bau von geeigneten Kraftwerken nicht.\r\n\r\nAnsonsten schaut Euch einfach um, zweifellos werdet Ihr Euch rasch zurechtfinden.\r\n\r\nWeitere Hilfen und Tipps findet ihr hier:\r\n\r\nHilfe: [url ?page=help]Umfangreiche InGame-Hilfe[/url]\r\nKontakt: [url ?page=contact]Game-Admin kontaktieren[/url]\r\nForum: [url http://www.etoa.ch/forum]Offizielles Forum[/url]\r\nFAQ: [url http://www.etoa.ch/faq]Häufig gestellte Fragen und Antworten dazu[/url]\r\nWiki: [url http://etoa.ch/help/?page=article]How-To\'s und hilfreiche Artikel[/url]\r\n\r\nIch wünsche Euch nun viel Erfolg in der Galaxie von Andromeda. Mögen Euer Imperium gross und Eure Schlachten erfolgreich sein!\r\n\r\nAnmerkung: Eine Kopie dieser Nachricht wird in Eurer Nachrichten-Box hinterlegt. Mit dem Schliessen dieser Nachricht leuchtet das Briefsymbol am oberen Bildschirmrand grün. Klickt darauf, um die Nachrichtenbox anzuzeigen.','','');
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `def_cat`
--

DROP TABLE IF EXISTS `def_cat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `def_cat` (
  `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `cat_order` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `cat_color` char(7) COLLATE utf8_unicode_ci NOT NULL DEFAULT '#ffffff',
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `def_cat`
--

LOCK TABLES `def_cat` WRITE;
/*!40000 ALTER TABLE `def_cat` DISABLE KEYS */;
INSERT INTO `def_cat` VALUES (1,'Schilder',1,'#0080FF'),(2,'Geschütze',0,'#00ff00'),(3,'Spezialanlagen',2,'#B048F8');
/*!40000 ALTER TABLE `def_cat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `def_queue`
--

DROP TABLE IF EXISTS `def_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `def_queue` (
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
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `def_queue`
--

LOCK TABLES `def_queue` WRITE;
/*!40000 ALTER TABLE `def_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `def_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `def_requirements`
--

DROP TABLE IF EXISTS `def_requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `def_requirements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `obj_id` (`obj_id`,`req_building_id`,`req_tech_id`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `def_requirements`
--

LOCK TABLES `def_requirements` WRITE;
/*!40000 ALTER TABLE `def_requirements` DISABLE KEYS */;
INSERT INTO `def_requirements` VALUES (35,12,0,16,8),(2,2,10,0,3),(3,3,10,0,6),(4,3,0,3,5),(5,4,0,3,7),(6,4,10,0,8),(7,4,8,0,5),(8,5,10,0,3),(11,5,0,3,4),(10,5,8,0,2),(12,6,10,0,6),(13,6,0,3,6),(14,6,8,0,5),(15,7,10,0,10),(16,7,0,8,11),(17,7,0,3,10),(18,8,10,0,10),(19,8,0,3,8),(20,8,10,10,12),(21,8,0,9,6),(22,8,22,0,3),(23,3,0,8,5),(24,4,8,8,7),(25,9,10,0,4),(26,9,0,8,3),(27,9,0,4,2),(28,11,10,0,8),(29,11,0,25,3),(30,11,0,16,4),(31,11,0,19,3),(32,12,10,0,9),(33,12,8,0,7),(34,12,0,3,10),(36,10,0,25,5),(37,10,0,11,8),(38,10,0,3,10),(39,10,13,0,5),(41,14,10,0,6),(42,14,0,3,5),(43,14,0,8,5),(44,14,0,12,9),(72,15,0,12,7),(71,15,10,0,3),(69,16,10,0,1),(75,17,0,16,4),(68,1,10,0,1),(73,17,10,0,8),(70,16,0,12,5),(74,17,0,25,3),(76,17,0,19,3),(77,17,0,12,11);
/*!40000 ALTER TABLE `def_requirements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `default_item_sets`
--

DROP TABLE IF EXISTS `default_item_sets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `default_item_sets` (
  `set_id` int(10) NOT NULL AUTO_INCREMENT,
  `set_name` varchar(50) NOT NULL,
  `set_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`set_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `default_item_sets`
--

LOCK TABLES `default_item_sets` WRITE;
/*!40000 ALTER TABLE `default_item_sets` DISABLE KEYS */;
INSERT INTO `default_item_sets` VALUES (5,'Standard',1),(7,'All Objects',0);
/*!40000 ALTER TABLE `default_item_sets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `default_items`
--

DROP TABLE IF EXISTS `default_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `default_items` (
  `item_id` int(10) NOT NULL AUTO_INCREMENT,
  `item_set_id` int(20) NOT NULL DEFAULT '0',
  `item_cat` char(1) NOT NULL,
  `item_object_id` int(10) NOT NULL,
  `item_count` int(10) NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `item_set_id` (`item_set_id`)
) ENGINE=MyISAM AUTO_INCREMENT=133 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `default_items`
--

LOCK TABLES `default_items` WRITE;
/*!40000 ALTER TABLE `default_items` DISABLE KEYS */;
INSERT INTO `default_items` VALUES (132,7,'s',71,10),(131,7,'d',1,10),(130,7,'d',11,1),(129,7,'d',10,1),(128,7,'s',69,20),(127,7,'s',24,20),(126,7,'s',31,20),(125,7,'s',68,20),(124,7,'s',46,20),(123,7,'s',20,10),(122,7,'s',13,1),(121,7,'s',36,20),(120,7,'s',42,20),(119,7,'s',27,20),(118,7,'s',8,20),(117,7,'s',4,20),(116,7,'s',60,1),(115,7,'s',9,10),(69,7,'b',6,1),(70,7,'b',7,15),(71,7,'b',8,20),(72,7,'b',21,10),(73,7,'b',9,20),(74,7,'b',10,20),(75,7,'b',11,20),(76,7,'b',22,1),(77,7,'b',24,1),(78,7,'b',25,10),(79,7,'b',1,25),(80,7,'b',2,25),(81,7,'b',3,25),(82,7,'b',4,25),(83,7,'b',5,25),(84,7,'b',12,12),(85,7,'b',13,12),(86,7,'b',15,6),(87,7,'b',14,20),(88,7,'b',16,25),(89,7,'b',17,25),(90,7,'b',18,25),(91,7,'b',20,25),(92,7,'b',19,25),(93,7,'t',4,20),(94,7,'t',5,20),(95,7,'t',14,20),(96,7,'t',6,15),(97,7,'t',21,15),(98,7,'t',20,15),(99,7,'t',8,20),(100,7,'t',9,20),(101,7,'t',10,20),(102,7,'t',11,20),(103,7,'t',15,10),(104,7,'t',17,10),(105,7,'t',18,10),(106,7,'t',19,10),(107,7,'t',22,1),(108,7,'t',23,1),(109,7,'t',24,1),(110,7,'t',3,20),(111,7,'t',7,20),(112,7,'t',16,10),(113,7,'t',25,10),(114,7,'t',12,10);
/*!40000 ALTER TABLE `default_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `defense`
--

DROP TABLE IF EXISTS `defense`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `defense` (
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
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `defense`
--

LOCK TABLES `defense` WRITE;
/*!40000 ALTER TABLE `defense` DISABLE KEYS */;
INSERT INTO `defense` VALUES (1,'SPICA Flakkanone','Einfache und billige Abwehrwaffe.','Einfache und billige Abwehrwaffe.\r\nSie wird auf Gebäuden befestigt und braucht daher keine Felder. Sie ist aber nicht sehr effektiv. Darum ist es besser, sie nur am Anfang und auch dann nur in grossen Mengen zu bauen.',800,475,0,425,0,0,1,0,0,0,1,1,0,300,150,250,0,0,0,2,1000000,'1.700'),(2,'POLARIS Raketengeschütz','Die Raketen dieses Geschützes verfolgen ihr Ziel mittels Lasersteuerung.','Um den gegnerischen Schiffen mit Raketen beizukommen, wurde dieses Raketengeschütz entwickelt. Es schiesst kleinere Raketen ab, welche dann das Ziel bis zur Zerstörung verfolgen. Es ist jedoch nicht sehr stark und dient vor allem zu Beginn als gute und billige Verteidigungswaffe.',1000,700,300,500,0,0,3,0,0,0,1,1,2,450,325,350,0,0,0,2,1000000,'2.500'),(3,'ZIBAL Laserturm','Dieses Geschütz richtet einen gebündelten und starken Energiestrahl auf ihr Ziel.','Dieses Geschütz richtet einen gebündelten und starken Energiestrahl auf ihr Ziel. Es ist eine weiterentwickelte Verteidigungsanlage, welche es auch mit grösseren Schiffen aufnehmen kann.',3900,3100,2100,1500,0,0,8,0,0,0,1,1,3,1500,2000,1800,0,0,0,2,100000,'10.600'),(4,'OMEGA Geschütz','Diese mächtige Abwehrwaffe beschützt deinen Planeten auch vor grösseren Angriffen.','Diese mächtige Abwehrwaffe beschützt deinen Planeten auch vor grösseren Angriffen. Da es aber eine starke Waffe ist, können maximal 1\\\'000 Stück gebaut werden.',750000,525000,165000,325000,0,0,15,0,0,1,1,1,4,300000,350000,275000,0,0,0,2,1000,'1765.000'),(5,'VEGA Hochenergieschild','Dieser kleine Hochenergieschild schützt deine Verteidigungsanlagen und Schiffe vor feindlichem Beschuss.','Dieser kleine Hochenergieschild schützt deine Verteidigungsanlagen und Schiffe vor feindlichem Beschuss. Es ist jedoch nicht sehr gut und kann nur wenig Beschuss abhalten.',3000,1200,1800,600,0,0,0,0,0,1,1,1,0,1200,3500,0,0,0,0,1,1,'6.600'),(6,'CASTOR Hochenergieschild','Dieser grosse Hochenergieschild schützt deine Verteidigungsanlagen und Schiffe vor feindlichem Beschuss.','Dieser grosse Hochenergieschild schützt deine Verteidigungsanlagen und Schiffe vor feindlichem Beschuss.',95000,40000,45000,25000,0,0,0,0,0,2,1,1,1,52500,105000,0,0,0,0,1,1,'205.000'),(7,'NEKKAR Plasmawerfer','Die stärkste Verteidigung in ganz Andromeda.','Die stärkste Verteidigung in ganz Andromeda. Dieser Plasmawerfer kann es sogar mit einem Andromeda Kampfstern aufnehmen! Dabei schiesst er hochenergetische Teilchen auf das Ziel.\r\nBedingt durch seine Grösse und Stärke ist die maximale Anzahl pro Planet auf 15 limitiert.',25000000,20000000,11500000,12000000,0,0,0,0,0,2,1,1,5,14000000,9500000,14500000,0,0,0,2,15,'68500.000'),(8,'SIGMA Hochenergieschild','Dies ist der grösste Schild in ganz Andromeda.','Dies ist der grösste Schild in ganz Andromeda. Dieser Schild nutzt hochenergetische Teilchen, um die Angriffe der Gegner abzufangen. Beim Bau dieses Schildes wird gleich noch ein Kraftwerk nur für diesen Schild gebaut, damit die Energieversorgung gesichert ist. Deshalb ist er so unglaublich teuer.',250000000,20000000,25000000,5000000,0,0,0,0,0,100,1,1,3,25000000,225000000,0,0,0,0,1,1,'300000.000'),(9,'KAPPA Minen','Diese Minen schweben im Orbit und können gegnerische Schiffe zerstören.','Diese Minen schweben im Orbit und können gegnerische Schiffe zerstören. Sie sind mit Tritium gefüllt und explodieren bei einer Kollision mit feindlichen Schiffen. Da ein kleiner Korridor für eigene Schiffe und Handelsschiffe frei bleiben muss, kann maximal eine Million dieser Minen gebaut werden.',25,10,18,5,0,0,0,0,0,0,1,1,1,1,1,20,0,0,0,3,1000000,'0.058'),(11,'PHOENIX Reparaturplattform','Diese Anlage repariert bei einem Kampf jede Runde eine gewisse Anzahl an Strukturpunkten, kann jedoch selbst auch zerstört werden.','Diese Anlage repariert bei einem Kampf jede Runde eine gewisse Anzahl an Strukturpunkten, kann jedoch selbst auch zerstört werden.\r\nDie grundlegende Idee, welche zur Entwicklung dieser Reparaturplattform führte, fanden die Serrakin in den Mutterschiffen der Cardassianer.',6500,3500,3000,1900,0,0,0,0,0,1,1,1,10,3750,2500,2500,1000,0,10,3,1000000,'14.900'),(12,'SAGITTARIUS Plasmaschild','Dieser spezielle Schild wurde schon oft zu kopieren versucht, doch bisher gelang es keiner anderen Rasse als den Serrakin, ihn so effizient herzustellen.','Dieser spezielle Schild wurde schon oft zu kopieren versucht, doch bisher gelang es keiner anderen Rasse als den Serrakin, ihn so effizient herzustellen.',1350000,1000000,1050000,625000,0,0,0,0,0,20,1,1,2,1400000,2100000,0,0,0,10,1,1,'4025.000'),(10,'MAGNETRON Störsender','Diese defensive Anlage kann zufällige Signale in den Raum abgeben und so das Auffinden und Entschlüsseln der eigenen Flottenkommunikation durch gegnerische Spione erschweren.','Durch die Verfügbarkeit von grossen Rechenzentren ist in letzter Zeit die Bedrohung durch kryptographische Angriffe auf die eigenen Flottenfunkverbindungen stark angestiegen. Viele Generäle fühlten sich nicht mehr sicher, da ihre Feinde anscheinend plötzlich sehr genau wussten, wann und wo ihre Flotten landen würden. Dies führte zur Erfindung des MAGNETRON Störsenders. Die riesigen Sendeanlagen erzeugen zufällige Funksignale, die sie in den Raum abgeben. Eine gegnerische Analyse der Funksignale eines Planeten findet so viel zu viele Signale und hat Mühe, die richtigen herauszufiltern. ',20000,50000,10000,15000,0,0,0,0,0,5,1,1,10,15000,1200,0,0,1,0,3,10,'95.000'),(14,'ZIBAL Laserturm M','Dieses Geschütz richtet einen gebündelten und starken Energiestrahl auf ihr Ziel. Mobile Version.','Dieses Geschütz richtet einen gebündelten und starken Energiestrahl auf ihr Ziel. Es ist eine weiterentwickelte Verteidigungsanlage, welche es auch mit grösseren Schiffen aufnehmen kann. Kann auf andere Planeten transportiert werden.',3900,3100,2100,1500,0,0,8,0,0,0,1,1,3,1500,2000,1800,0,0,10,2,100000,'10.600'),(15,'POLARIS Raketengeschütz M','Die Raketen dieses Geschützes verfolgen ihr Ziel mittels Lasersteuerung. Mobile Version.','Um den gegnerischen Schiffen mit Raketen beizukommen, wurde dieses Raketengeschütz entwickelt. Es schiesst kleinere Raketen ab, welche dann das Ziel bis zur Zerstörung verfolgen. Es ist jedoch nicht sehr stark und dient vor allem zu Beginn als gute und billige Verteidigungswaffe. Kann auf andere Planeten transportiert werden.',1000,700,300,500,0,0,3,0,0,0,1,1,2,450,325,350,0,0,10,2,1000000,'2.500'),(16,'SPICA Flakkanone M','Einfache und billige Abwehrwaffe. Mobile Version.','Einfache und billige Abwehrwaffe.\r\nSie wird auf Gebäuden befestigt und braucht daher keine Felder. Sie ist aber nicht sehr effektiv. Darum ist es besser, sie nur am Anfang und auch dann nur in grossen Mengen zu bauen. Kann auf andere Planeten transportiert werden.',800,475,0,425,0,0,1,0,0,0,1,1,0,300,150,250,0,0,10,2,1000000,'1.700'),(17,'PHOENIX Reparaturplattform M','Diese Anlage repariert bei einem Kampf jede Runde eine gewisse Anzahl an Strukturpunkten, kann jedoch selbst auch zerstört werden. Mobile Version.','Diese Anlage repariert bei einem Kampf jede Runde eine gewisse Anzahl an Strukturpunkten, kann jedoch selbst auch zerstört werden.\r\nDie grundlegende Idee, welche zur Entwicklung dieser Reparaturplattform führte, fanden die Serrakin in den Mutterschiffen der Cardassianer. Kann auf andere Planeten transportiert werden.',6500,3500,3000,1900,0,0,0,0,0,1,1,1,10,3750,2500,2500,1000,0,10,3,1000000,'14.900');
/*!40000 ALTER TABLE `defense` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deflist`
--

DROP TABLE IF EXISTS `deflist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deflist` (
  `deflist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `deflist_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `deflist_def_id` int(10) unsigned NOT NULL DEFAULT '0',
  `deflist_entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `deflist_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`deflist_id`),
  UNIQUE KEY `deflist_all` (`deflist_user_id`,`deflist_entity_id`,`deflist_def_id`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deflist`
--

LOCK TABLES `deflist` WRITE;
/*!40000 ALTER TABLE `deflist` DISABLE KEYS */;
/*!40000 ALTER TABLE `deflist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entities`
--

DROP TABLE IF EXISTS `entities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `entities` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `cell_id` int(6) unsigned NOT NULL,
  `code` char(1) DEFAULT NULL,
  `pos` int(2) unsigned NOT NULL DEFAULT '0',
  `lastvisited` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cell_id` (`cell_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 COMMENT='Entities in Space, acts as fleet targets';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entities`
--

LOCK TABLES `entities` WRITE;
/*!40000 ALTER TABLE `entities` DISABLE KEYS */;
/*!40000 ALTER TABLE `entities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,100,'Bruchlandung Marauder','Ein Flotte mit {reward:p:ship} der Handelsföderation ist auf deinem Planeten {planet} abgestürzt, es würde dich {costs:p:metal}, {costs:p:crystal} und {costs:p:plastic} kosten das Schiff zu bergen. Möchtest du es bergen?',1,'Deine Bergungsmannschaft konnte {reward:p:shipcnt} {reward:p:ship} bergen!','Leider hast du deiner Bergungsmannschaft keine Ressourcen gegeben um {reward:p:shipcnt} {reward:p:ship} zu bergen!','0.0100',0,0,0,0,0,0,0,0,0,0,0,0,29,1,10,0,0,0,0,0,0,0,'0.0100',200,400,150,300,150,300,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.9999',0,0,0,0,0,0,0,0,0),(2,100,'Intergalaktischer Sturm','Ein intergalaktischer Sturm, ist über deinen Planeten {planet} gefegt, und hat dabei {reward:p:metal} und {reward:p:crystal} auf deinem Planeten hinterlassen. ',0,'','','0.0100',1,200,1,200,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.9999',0,0,0,0,0,0,0,0,0),(3,100,'Vulkanausbruch','Ein Vulkan ist auf deinem Planeten {planet} ausgebrochen, und hat {reward:p:metal} aus dem Erdinneren hervor gebracht. Das Abbauen kostet dich {costs:p:fuel} und {costs:p:food}. Sollen das Titan abgebaut werden?',1,'Es wurde {reward:p:metal} abgebaut!','','0.0100',200,400,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,20,200,30,150,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0),(4,100,'Zusammenstoss von Gasplaneten','Nach dem Zusammenstoss von zwei Gasplaneten sind {reward:p:fuel} auf deinen Planeten {planet} gefallen.',0,'','','0.0100',0,0,0,0,0,0,10,80,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0),(5,100,'Supernova','Bei einer Supernova sind {reward:p:crystal} ins Weltall geschleudert worden und nun auf deinem Planeten {planet} angekommen.',0,'','','0.0100',0,0,20,70,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0),(6,100,'Unabhängige Bürger','Eine Gruppe von {reward:p:people} unabhängigen Bürgern ist auf deinem Planeten gelandet, möchtes du ihnen eine Unterkunft anbieten?',1,'{reward:p:people} Bürger schliessen sich deiner Zivilisation an!','Die unabhängigen Bürger sind empört dass du ihnen keine Unterkunft gewährt hast und stürmen deine {costs:n:building}, dabei geht ein Teil kaputt und die Stufe des Silos verringert sich um {costs:n:buildinglevel}.','0.0100',0,0,0,0,0,0,0,0,0,0,10,100,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,19,1,0,0,'0.0100',0,0,0,0,0,0,0,0,0),(7,100,'Defekter Antrieb','Ein Imperialisches Schlachtschiff hat einen defekten Antrieb, möchtest du der Besatzung {costs:p:crystal} geben, damit sie ihren Antrieb wieder benutzen können?',0,'Die Besatzung des Schlachtschiffes ist auf ihren Planeten zurück geflogen und hat sich dann entschieden, dir ein Geschenk zu machen: {reward:p:shipcnt} {reward:p:ship}','Die Piloten des fremden Schiffes versucht, das Silizium zu stehlen, dabei kommt es zu Aueinandersetzungen mit deiner Armee. Du verlierst {costs:n:people} Bürger und {costs:n:crystal}','0.0100',0,0,0,0,0,0,0,0,0,0,0,0,2,1,11,0,0,0,0,0,0,0,'0.0100',0,0,350,841,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,50,0,0,0,0,0,0,0,50,0,0,0,0,0,0,0,0,0,0,'0.0100',0,0,0,0,0,0,0,0,0);
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events_exec`
--

DROP TABLE IF EXISTS `events_exec`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events_exec` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events_exec`
--

LOCK TABLES `events_exec` WRITE;
/*!40000 ALTER TABLE `events_exec` DISABLE KEYS */;
/*!40000 ALTER TABLE `events_exec` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fleet`
--

DROP TABLE IF EXISTS `fleet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fleet` (
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
) ENGINE=MyISAM AUTO_INCREMENT=610 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fleet`
--

LOCK TABLES `fleet` WRITE;
/*!40000 ALTER TABLE `fleet` DISABLE KEYS */;
/*!40000 ALTER TABLE `fleet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fleet_bookmarks`
--

DROP TABLE IF EXISTS `fleet_bookmarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fleet_bookmarks` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fleet_bookmarks`
--

LOCK TABLES `fleet_bookmarks` WRITE;
/*!40000 ALTER TABLE `fleet_bookmarks` DISABLE KEYS */;
/*!40000 ALTER TABLE `fleet_bookmarks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fleet_ships`
--

DROP TABLE IF EXISTS `fleet_ships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fleet_ships` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1044 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fleet_ships`
--

LOCK TABLES `fleet_ships` WRITE;
/*!40000 ALTER TABLE `fleet_ships` DISABLE KEYS */;
INSERT INTO `fleet_ships` VALUES (73,67,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(58,57,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(57,0,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(56,55,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(55,0,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(54,53,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(44,44,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(43,43,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(53,0,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(49,49,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(48,48,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(62,56,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(63,57,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(64,58,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(65,59,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(66,60,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(67,61,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(69,63,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(71,65,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(75,69,16,2,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(76,70,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(77,71,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(78,72,16,23,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(79,73,16,33,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(80,74,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(81,75,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(82,76,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(83,77,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(85,79,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(87,81,16,58,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(93,87,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(89,83,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(92,86,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(91,85,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(94,88,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(95,89,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(96,90,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(97,91,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(98,92,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(99,93,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(100,94,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(101,95,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(103,97,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(111,105,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(113,107,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(114,108,16,100,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(115,109,16,506,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(231,216,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(232,217,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(315,291,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(312,288,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(316,292,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(310,286,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(317,293,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(311,287,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(313,289,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(318,294,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(319,295,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(324,300,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(323,299,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(322,298,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(321,297,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(320,296,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(325,301,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(326,302,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(327,303,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0),(328,304,16,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
/*!40000 ALTER TABLE `fleet_ships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hostname_cache`
--

DROP TABLE IF EXISTS `hostname_cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hostname_cache` (
  `addr` char(39) NOT NULL,
  `host` varchar(100) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`addr`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hostname_cache`
--

LOCK TABLES `hostname_cache` WRITE;
/*!40000 ALTER TABLE `hostname_cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `hostname_cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ip_ban`
--

DROP TABLE IF EXISTS `ip_ban`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip_ban` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `msg` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ip_ban`
--

LOCK TABLES `ip_ban` WRITE;
/*!40000 ALTER TABLE `ip_ban` DISABLE KEYS */;
/*!40000 ALTER TABLE `ip_ban` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_failures`
--

DROP TABLE IF EXISTS `login_failures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_failures` (
  `failure_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `failure_time` int(10) unsigned NOT NULL DEFAULT '0',
  `failure_ip` varchar(15) NOT NULL,
  `failure_host` varchar(50) NOT NULL,
  `failure_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `failure_pw` char(50) NOT NULL,
  `failure_client` varchar(255) NOT NULL,
  PRIMARY KEY (`failure_id`),
  KEY `failure_user_id` (`failure_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_failures`
--

LOCK TABLES `login_failures` WRITE;
/*!40000 ALTER TABLE `login_failures` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_failures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_alliance`
--

DROP TABLE IF EXISTS `logs_alliance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs_alliance` (
  `logs_alliance_id` int(10) NOT NULL AUTO_INCREMENT,
  `logs_alliance_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `logs_alliance_text` text NOT NULL,
  `logs_alliance_alliance_id` int(10) unsigned NOT NULL DEFAULT '0',
  `logs_alliance_alliance_tag` varchar(10) NOT NULL DEFAULT '0',
  `logs_alliance_alliance_name` varchar(30) NOT NULL,
  `logs_alliance_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`logs_alliance_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_alliance`
--

LOCK TABLES `logs_alliance` WRITE;
/*!40000 ALTER TABLE `logs_alliance` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_alliance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_battle`
--

DROP TABLE IF EXISTS `logs_battle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs_battle` (
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
  `win_metal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `win_crystal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `win_pvc` bigint(20) unsigned NOT NULL DEFAULT '0',
  `win_tritium` bigint(20) unsigned NOT NULL DEFAULT '0',
  `win_food` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tf_metal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tf_crystal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tf_pvc` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `logs_battle_fleet_landtime` (`landtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_battle`
--

LOCK TABLES `logs_battle` WRITE;
/*!40000 ALTER TABLE `logs_battle` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_battle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_battle_queue`
--

DROP TABLE IF EXISTS `logs_battle_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs_battle_queue` (
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
  `win_metal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `win_crystal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `win_pvc` bigint(20) unsigned NOT NULL DEFAULT '0',
  `win_tritium` bigint(20) unsigned NOT NULL DEFAULT '0',
  `win_food` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tf_metal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tf_crystal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `tf_pvc` bigint(20) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_battle_queue`
--

LOCK TABLES `logs_battle_queue` WRITE;
/*!40000 ALTER TABLE `logs_battle_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_battle_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_fleet`
--

DROP TABLE IF EXISTS `logs_fleet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs_fleet` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_fleet`
--

LOCK TABLES `logs_fleet` WRITE;
/*!40000 ALTER TABLE `logs_fleet` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_fleet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_fleet_queue`
--

DROP TABLE IF EXISTS `logs_fleet_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs_fleet_queue` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_fleet_queue`
--

LOCK TABLES `logs_fleet_queue` WRITE;
/*!40000 ALTER TABLE `logs_fleet_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_fleet_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_game`
--

DROP TABLE IF EXISTS `logs_game`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs_game` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_game`
--

LOCK TABLES `logs_game` WRITE;
/*!40000 ALTER TABLE `logs_game` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_game` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_game_queue`
--

DROP TABLE IF EXISTS `logs_game_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs_game_queue` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_game_queue`
--

LOCK TABLES `logs_game_queue` WRITE;
/*!40000 ALTER TABLE `logs_game_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_game_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_queue`
--

DROP TABLE IF EXISTS `logs_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facility` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `severity` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `ip` varchar(39) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=98 DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_queue`
--

LOCK TABLES `logs_queue` WRITE;
/*!40000 ALTER TABLE `logs_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `market_auction`
--

DROP TABLE IF EXISTS `market_auction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `market_auction` (
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
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `market_auction`
--

LOCK TABLES `market_auction` WRITE;
/*!40000 ALTER TABLE `market_auction` DISABLE KEYS */;
/*!40000 ALTER TABLE `market_auction` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `market_rates`
--

DROP TABLE IF EXISTS `market_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `market_rates` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `market_rates`
--

LOCK TABLES `market_rates` WRITE;
/*!40000 ALTER TABLE `market_rates` DISABLE KEYS */;
/*!40000 ALTER TABLE `market_rates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `market_ressource`
--

DROP TABLE IF EXISTS `market_ressource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `market_ressource` (
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
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `market_ressource`
--

LOCK TABLES `market_ressource` WRITE;
/*!40000 ALTER TABLE `market_ressource` DISABLE KEYS */;
/*!40000 ALTER TABLE `market_ressource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `market_ship`
--

DROP TABLE IF EXISTS `market_ship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `market_ship` (
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
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `market_ship`
--

LOCK TABLES `market_ship` WRITE;
/*!40000 ALTER TABLE `market_ship` DISABLE KEYS */;
/*!40000 ALTER TABLE `market_ship` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_cat`
--

DROP TABLE IF EXISTS `message_cat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_cat` (
  `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(50) NOT NULL,
  `cat_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cat_desc` text NOT NULL,
  `cat_sender` varchar(50) NOT NULL,
  PRIMARY KEY (`cat_id`),
  KEY `cat_order` (`cat_order`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_cat`
--

LOCK TABLES `message_cat` WRITE;
/*!40000 ALTER TABLE `message_cat` DISABLE KEYS */;
INSERT INTO `message_cat` VALUES (1,'Persönliche Nachrichten',0,'',''),(2,'Spionageberichte',1,'','Flottenkontrolle'),(3,'Kriegsberichte',2,'','Flottenkontrolle'),(4,'Überwachungsberichte',3,'','Raumüberwachung'),(5,'Sonstige Nachrichten',5,'','System'),(6,'Allianz',4,'','Allianzverwaltung'),(7,'Account',5,'','EtoA Administration');
/*!40000 ALTER TABLE `message_cat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_data`
--

DROP TABLE IF EXISTS `message_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_data` (
  `id` mediumint(8) unsigned NOT NULL,
  `subject` varchar(200) NOT NULL,
  `text` text NOT NULL,
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `fleet_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  CONSTRAINT `message_data_ibfk_1` FOREIGN KEY (`id`) REFERENCES `messages` (`message_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_data`
--

LOCK TABLES `message_data` WRITE;
/*!40000 ALTER TABLE `message_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_ignore`
--

DROP TABLE IF EXISTS `message_ignore`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_ignore` (
  `ignore_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ignore_owner_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ignore_target_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ignore_id`),
  KEY `ignore_owner_id` (`ignore_owner_id`),
  KEY `ignore_target_id` (`ignore_target_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_ignore`
--

LOCK TABLES `message_ignore` WRITE;
/*!40000 ALTER TABLE `message_ignore` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_ignore` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `minimap`
--

DROP TABLE IF EXISTS `minimap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `minimap` (
  `field_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `field_x` int(3) unsigned NOT NULL DEFAULT '0',
  `field_y` int(3) unsigned NOT NULL DEFAULT '0',
  `field_typ_id` int(3) unsigned NOT NULL DEFAULT '0',
  `field_event_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `minimap`
--

LOCK TABLES `minimap` WRITE;
/*!40000 ALTER TABLE `minimap` DISABLE KEYS */;
/*!40000 ALTER TABLE `minimap` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `minimap_field_types`
--

DROP TABLE IF EXISTS `minimap_field_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `minimap_field_types` (
  `field_typ_id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `field_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `field_image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `field_blocked` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`field_typ_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `minimap_field_types`
--

LOCK TABLES `minimap_field_types` WRITE;
/*!40000 ALTER TABLE `minimap_field_types` DISABLE KEYS */;
INSERT INTO `minimap_field_types` VALUES (1,'Gras','gras01.jpg',0),(2,'Felsen','rock01.jpg',1);
/*!40000 ALTER TABLE `minimap_field_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `missile_flights`
--

DROP TABLE IF EXISTS `missile_flights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `missile_flights` (
  `flight_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `flight_entity_from` int(10) unsigned NOT NULL DEFAULT '0',
  `flight_entity_to` int(10) unsigned NOT NULL DEFAULT '0',
  `flight_starttime` int(10) unsigned NOT NULL DEFAULT '0',
  `flight_landtime` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`flight_id`),
  KEY `flight_planet_from` (`flight_entity_from`),
  KEY `flight_planet_to` (`flight_entity_to`),
  KEY `flight_user_id` (`flight_starttime`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `missile_flights`
--

LOCK TABLES `missile_flights` WRITE;
/*!40000 ALTER TABLE `missile_flights` DISABLE KEYS */;
/*!40000 ALTER TABLE `missile_flights` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `missile_flights_obj`
--

DROP TABLE IF EXISTS `missile_flights_obj`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `missile_flights_obj` (
  `obj_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `obj_flight_id` int(10) unsigned NOT NULL DEFAULT '0',
  `obj_missile_id` int(10) unsigned NOT NULL DEFAULT '0',
  `obj_cnt` int(15) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`obj_id`),
  KEY `obj_flight_id` (`obj_flight_id`),
  KEY `obj_missile_id` (`obj_missile_id`),
  KEY `obj_cnt` (`obj_cnt`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `missile_flights_obj`
--

LOCK TABLES `missile_flights_obj` WRITE;
/*!40000 ALTER TABLE `missile_flights_obj` DISABLE KEYS */;
/*!40000 ALTER TABLE `missile_flights_obj` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `missile_requirements`
--

DROP TABLE IF EXISTS `missile_requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `missile_requirements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `obj_id` (`obj_id`,`req_building_id`,`req_tech_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `missile_requirements`
--

LOCK TABLES `missile_requirements` WRITE;
/*!40000 ALTER TABLE `missile_requirements` DISABLE KEYS */;
INSERT INTO `missile_requirements` VALUES (1,2,25,0,1),(2,2,0,25,1),(4,1,25,0,3),(5,1,0,24,3),(6,3,25,0,4),(7,3,0,24,5),(8,4,25,0,5),(9,4,0,24,6),(10,4,0,25,5),(11,1,0,25,2),(12,3,0,25,4),(13,3,0,17,8),(14,1,0,8,8);
/*!40000 ALTER TABLE `missile_requirements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `missilelist`
--

DROP TABLE IF EXISTS `missilelist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `missilelist` (
  `missilelist_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `missilelist_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `missilelist_entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `missilelist_missile_id` int(10) unsigned NOT NULL DEFAULT '0',
  `missilelist_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`missilelist_id`),
  KEY `missilelist_missile_id` (`missilelist_missile_id`),
  KEY `missilelist_user_id` (`missilelist_user_id`,`missilelist_entity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `missilelist`
--

LOCK TABLES `missilelist` WRITE;
/*!40000 ALTER TABLE `missilelist` DISABLE KEYS */;
/*!40000 ALTER TABLE `missilelist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `missiles`
--

DROP TABLE IF EXISTS `missiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `missiles` (
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `missiles`
--

LOCK TABLES `missiles` WRITE;
/*!40000 ALTER TABLE `missiles` DISABLE KEYS */;
INSERT INTO `missiles` VALUES (1,'PHOBOS Rakete','Zerstört gegnerische Verteidigung.','Diese Rakete kann auf Verteidigungsanlagen eines feindlichen Planeten abgefeuert werden und verursacht an diesen einen gewissen Schaden, so dass einige Anlagen unter Umständen zerstört werden. Diese Raketen haben eine begrenzte Reichweite, treffen ihr Ziel aber immer.',18000,6000,5000,15000,0,25000,100000,3000,0,0,1,1),(2,'GEMINI Abfangrakete','Abfangraketen schiessen selbstständig gegnerische Raketen ab, die diesen Planeten anfliegen.','Bei einem Raketenangriff können diese Raketen jeweils eine fremde Rakete zerstören. Sie lösen sich selbständig aus und bieten so einen guten Schutz gegen anfliegende Raketen. Gegen feindliche Flotten können sie jedoch nichts ausrichten. Ausserdem ist die Rakete nach dem Abfangen verbraucht und muss jeweils wieder neu gekauft werden.',9000,18000,6000,4000,2000,0,0,0,0,1,0,1),(3,'VEGA EMP-Rakete','Kann ein gegnerisches Gebäude temporär deaktivieren.','Diese Rakete kann angreifen um ein gegnerisches Gebäude temporär ausser Kraft zu setzen. Sie richtet an der Verteidigung aber keinen Schaden an und kann ein Gebäude auch nicht vollständig zerstören! Die Rakete wird beim EMP-Angriff verbraucht und hat auch nur eine begrenzte Reichweite.',18000,6000,5000,15000,0,0,90000,3000,300,0,1,1),(4,'VIRGO Abfangrakete','Verbesserte Abfangrakete; schiesst selbstständig zwei gegnerische Raketen ab.','Bei einem Raketenangriff können diese Raketen jeweils zwei fremde Rakete zerstören. Sie lösen sich  selbständig aus und bieten so einen guten Schutz. Gegen feindliche Flotten können sie jedoch nichts ausrichten. Ausserdem ist die Rakete nach dem Abfangen verbraucht und muss jeweils wieder neu gekauft werden.',15000,23000,9000,4000,2000,0,0,0,0,2,0,1);
/*!40000 ALTER TABLE `missiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `multifire`
--

DROP TABLE IF EXISTS `multifire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `multifire` (
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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `multifire`
--

LOCK TABLES `multifire` WRITE;
/*!40000 ALTER TABLE `multifire` DISABLE KEYS */;
INSERT INTO `multifire` VALUES (1,6,0,2,30,0),(2,6,0,3,50,0);
/*!40000 ALTER TABLE `multifire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nebulas`
--

DROP TABLE IF EXISTS `nebulas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nebulas` (
  `id` int(10) unsigned NOT NULL,
  `res_metal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_crystal` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_plastic` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_fuel` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_food` bigint(20) unsigned NOT NULL DEFAULT '0',
  `res_power` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nebulas`
--

LOCK TABLES `nebulas` WRITE;
/*!40000 ALTER TABLE `nebulas` DISABLE KEYS */;
/*!40000 ALTER TABLE `nebulas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ng_ships`
--

DROP TABLE IF EXISTS `ng_ships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ng_ships` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ng_ships`
--

LOCK TABLES `ng_ships` WRITE;
/*!40000 ALTER TABLE `ng_ships` DISABLE KEYS */;
/*!40000 ALTER TABLE `ng_ships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notepad`
--

DROP TABLE IF EXISTS `notepad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notepad` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `list` (`user_id`,`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notepad`
--

LOCK TABLES `notepad` WRITE;
/*!40000 ALTER TABLE `notepad` DISABLE KEYS */;
/*!40000 ALTER TABLE `notepad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notepad_data`
--

DROP TABLE IF EXISTS `notepad_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notepad_data` (
  `id` mediumint(8) unsigned NOT NULL,
  `subject` varchar(100) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notepad_data`
--

LOCK TABLES `notepad_data` WRITE;
/*!40000 ALTER TABLE `notepad_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `notepad_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `obj_transforms`
--

DROP TABLE IF EXISTS `obj_transforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `obj_transforms` (
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
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `obj_transforms`
--

LOCK TABLES `obj_transforms` WRITE;
/*!40000 ALTER TABLE `obj_transforms` DISABLE KEYS */;
INSERT INTO `obj_transforms` VALUES (1,14,79,0,0,0,0,0,0,'0.0','1.0',1),(2,15,81,0,0,0,0,0,0,'0.0','1.0',1),(3,16,80,0,0,0,0,0,0,'0.0','1.0',1),(4,17,82,0,0,0,0,0,0,'0.0','1.0',1);
/*!40000 ALTER TABLE `obj_transforms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `planet_types`
--

DROP TABLE IF EXISTS `planet_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `planet_types` (
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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planet_types`
--

LOCK TABLES `planet_types` WRITE;
/*!40000 ALTER TABLE `planet_types` DISABLE KEYS */;
INSERT INTO `planet_types` VALUES (1,'Erdähnlicher Planet',1,'Dieser Planet hat eine sehr ausgeglichene Umwelt und ähnelt unseren ehemaligen Erde am meisten. Da der Mensch ein Gewohnheitstier ist, sind erdähnliche Planeten ideal für das Heranwachsen einer Zivilisation geeignet, da die notwendigen Voraussetzungen für alle Bereiche gegeben sind.','1.15','1.00','1.20','1.00','1.50','1.10','1.20','1.00','1.00',0,1),(2,'Wasserplanet',1,'Die Oberfläche dieses Planeten besteht zum grössten Teil aus Ozeanen. Die wenigen Landteile sind nicht wirklich geeignet für grossflächigen Abbau von Mineralen, dafür kann aus dem Wasser Tritium gewonnen werden. Ebenfalls ist durch das viele vorhandene Wasser die Hauptgrundlage für Nahrungsabbau gelegt, ausserdem ist der Planet bestens geeignet, mit Hilfe von Wasserkraftwerken grosse Mengen an Energie zu erzeugen.','0.90','0.90','1.00','1.15','1.15','1.60','0.80','1.00','0.85',0,1),(3,'Wüstenplanet',1,'Wüste, Sand, Trockenheit und ein unwirtliches Klima zeichnet diesen Planetentyp aus. Der allgegenwärtige Sand hat aber auch etwas positives, denn aus ihm können grosse Mengen von wertvollem Silizium gewonnen werden.','1.00','1.70','1.00','0.90','0.80','0.90','0.85','0.85','1.10',0,1),(4,'Eisplanet',1,'Auf diesem unwirtlichen Planeten lockt einzig der Abbau von Tritium, welches sich aus den Eisschichen herausextrahieren lässt.\r\nVor kurzem haben Forscher eine neue chemische Methode entwickelt, aus Eismassen Silizium zu gewinnen. Diese neuartige Abbaumöglichkeit macht die Eisplaneten für Silizium-Anwender interessanter.','1.00','1.20','1.00','1.60','0.90','1.00','0.50','1.00','1.00',0,1),(5,'Dschungelplanet',1,'Riesige Wälder wachsen auf diesem Planeten, dessen Klima sehr gut für das Wachstum der Umwelt ist. Daher kann viel Nahrung für die Bevölkerung geerntet werden, welche sich auf einem Dschungelplaneten auch sonst sehr wohl fühlt.','1.00','1.00','1.20','1.15','1.40','1.00','1.50','1.10','1.00',0,1),(6,'Gebirgsplanet',1,'Den Namen hat dieser Planetentyp durch seine felsige Oberfläche erhalten. Ein Abbau von Erzen bietet sich optimalerweise an, hingegen ist der Abbau von Nahrung und die Herstellung von PVC mit Aufwand verbunden, da die Umgebung deren Anforderungen nicht gerecht wird.','1.30','1.00','0.90','1.00','0.90','1.10','0.90','1.10','0.90',0,1),(7,'Gasplanet',0,'Dieser Planet ist unbewohnbar, da er keine feste Oberfläche hat, sondern aus lauter gasartigen Nebeln besteht. Seine Gase lassen sich jedoch mit Hilfe von Gassaugern zu Tritium umwandeln.','0.50','0.60','0.40','3.00','0.30','1.20','0.30','1.20','1.20',1,1);
/*!40000 ALTER TABLE `planet_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `planets`
--

DROP TABLE IF EXISTS `planets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `planets` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `planets`
--

LOCK TABLES `planets` WRITE;
/*!40000 ALTER TABLE `planets` DISABLE KEYS */;
/*!40000 ALTER TABLE `planets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `races`
--

DROP TABLE IF EXISTS `races`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `races` (
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
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `races`
--

LOCK TABLES `races` WRITE;
/*!40000 ALTER TABLE `races` DISABLE KEYS */;
INSERT INTO `races` VALUES (1,'Terraner','Die Terraner sind eine eher jüngere Rasse, deren Vorfahren ursprünglich vom Planeten Erde kamen. Die Menschen sind besonders gut in Forschung, der Herstellung von Plastik und dem Anbau von Nahrung. Ihre Schwächen liegen im Abbau von Erzen und Kristallen. Da sie ihre ganzen Ressourcen in die Forschung steckten, sind ihre Schiffe relativ langsam.','','terranischer','terranisches','terranische','Präsident der Terraner','0.85','1.00','0.95','0.90','0.90','1.30','1.00','1.30','1.00','1.00',1),(2,'Andorianer','Die Andorianer sind zugleich humanoid und insektoid. Sie haben graublaue Haut und weisses Haar. Auf ihrem Kopf haben sie zwei Fühler, die ihnen zur feinfühligen sinnlichen Wahrnehmung dienen. Ihre Stärke ist die Produktion künstlicher Stoffe wie Plastik. Ihre Schwäche ist der schlechte Umgang mit Energie.','','','','','Schwarmführer der Andorianer','1.00','1.00','1.00','1.00','1.10','1.60','1.00','1.00','0.90','1.00',1),(3,'Rigelianer','Die Rigelianer stammen aus dem Rigel-System. Ihre Stärke liegt im Abbau von Kristallen, die für Steuereinheiten in Gebäuden und Schiffen verwendet werden. Da sie lange nur auf den Handel mit Silizium gesetzt haben, sind ihre Kenntnisse beim Abbau anderer Stoffe eher schlecht.','','','','','Kaiser der Rigelianer','1.00','1.00','1.00','0.80','1.80','0.90','0.90','0.90','1.00','1.10',1),(4,'Orioner','Die Orioner sind eine humanoide Rasse aus der Nähe des Orions. Die Gesellschaft der Orioner besteht hauptsächlich aus Schmugglern und Piraten. Ihre Schiffe sind bekannt für ihre Schnelligkeit.','','','','','Kapitän der Orioner','1.00','1.00','2.00','1.10','1.10','0.80','0.90','1.00','1.00','1.10',1),(5,'Minbari','Die Minbari sind eine humanoide Rasse. Dadurch, dass sie den Rohstoff Erdöl nie gekannt haben, sind sie seit Ewigkeiten auf den Abbau von Tritium spezialisiert. Durch ihre enormen Treibstoffreserven und ihre grossen Anwendungskenntnisse von Tritium haben sie relativ schnelle Raumschiffe.','Eine Rasse mit schnellen Schiffen und grossem Wissen über Tritium.','minbarischer','','minbarische','Vorsteher des Minbarikonzils','1.00','1.00','1.20','0.90','0.90','1.10','1.80','1.00','1.00','1.00',1),(8,'Centauri','Die Centauri haben die besten Wissenschaftler des Universums, darum können sie auch schneller Technologien erforschen. Allerdings verbrauchen sie für ihre Labore sehr viel Strom.','','','','','Professor der Centauri','0.60','1.00','1.00','0.90','0.90','1.10','0.90','1.00','0.85','1.00',1),(6,'Ferengi','Die Ferengi sind eine humanoide Rasse. Sie sind etwas kleinwüchsiger als Menschen und  haben grosse Ohren. Die Stärke der Ferengi liegt beim Abbau von Metall.','','','','','Grosser Nagus der Ferengi','1.00','1.00','1.20','1.60','0.90','1.00','0.90','1.00','1.00','1.00',1),(7,'Vorgonen','Die Vorgonen sind eine Rasse, die vor allem gut bauen kann. Sie können ihre Gebäude viel schneller fertig stellen als alle Anderen. Dafür lassen ihre Schiffe und ihre Produktionsrate zu wünschen übrig.','','','','','Architekt der Vorgonen','1.00','0.60','0.95','0.90','0.80','0.80','1.10','0.70','1.20','0.90',1),(9,'Cardassianer','Seit einer grossen Hungersnot haben sich die Cardassianer auf die Nahrungsherstellung spezialisiert, haben aber den Abbau von Erzen vernachlässigt.\r\nIhre andere Stärke liegt in der Fähigkeit der Mutterschiffe zur Regeneration von ganzen Flottenverbänden.','','','','','Zentralrat der Cardassianer','1.00','1.00','1.00','0.80','0.90','1.20','1.00','1.60','1.10','1.10',1),(10,'Serrakin','Die Serrakin sind eine sehr friedliche Rasse, welche sich nicht gerne in grosse Auseinandersetzungen einmischt. Sie weiss sich aber bei Angriffen sehr gut zu wehren, da die Verteidigungstechnologie ihr Spezialgebiet ist.','','serrakinischer','','serrakinische','Beschützer der Serrakin','1.00','1.00','0.90','1.15','1.15','1.10','0.90','1.10','1.10','0.80',1);
/*!40000 ALTER TABLE `races` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_battle`
--

DROP TABLE IF EXISTS `reports_battle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_battle` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_battle`
--

LOCK TABLES `reports_battle` WRITE;
/*!40000 ALTER TABLE `reports_battle` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports_battle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_market`
--

DROP TABLE IF EXISTS `reports_market`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_market` (
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
  `timestamp2` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_market`
--

LOCK TABLES `reports_market` WRITE;
/*!40000 ALTER TABLE `reports_market` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports_market` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_other`
--

DROP TABLE IF EXISTS `reports_other`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_other` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_other`
--

LOCK TABLES `reports_other` WRITE;
/*!40000 ALTER TABLE `reports_other` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports_other` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports_spy`
--

DROP TABLE IF EXISTS `reports_spy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports_spy` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports_spy`
--

LOCK TABLES `reports_spy` WRITE;
/*!40000 ALTER TABLE `reports_spy` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports_spy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ship_cat`
--

DROP TABLE IF EXISTS `ship_cat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ship_cat` (
  `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(50) NOT NULL,
  `cat_order` int(2) unsigned NOT NULL DEFAULT '0',
  `cat_color` char(7) NOT NULL,
  PRIMARY KEY (`cat_id`),
  KEY `cat_name` (`cat_name`),
  KEY `cat_order` (`cat_order`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ship_cat`
--

LOCK TABLES `ship_cat` WRITE;
/*!40000 ALTER TABLE `ship_cat` DISABLE KEYS */;
INSERT INTO `ship_cat` VALUES (1,'Kriegsschiff',2,'#0080FF'),(2,'Ziviles Schiff',1,'#00FF00'),(3,'Episches Schiff',4,'#B048F8'),(4,'Rassenspezifisches Schiff',3,'#f00'),(5,'NPC-Schiff',6,'#F07902'),(6,'Allianzschiff',5,'#ffffff');
/*!40000 ALTER TABLE `ship_cat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ship_queue`
--

DROP TABLE IF EXISTS `ship_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ship_queue` (
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
) ENGINE=MyISAM AUTO_INCREMENT=88 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ship_queue`
--

LOCK TABLES `ship_queue` WRITE;
/*!40000 ALTER TABLE `ship_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `ship_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ship_requirements`
--

DROP TABLE IF EXISTS `ship_requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ship_requirements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `obj_id` (`obj_id`,`req_building_id`,`req_tech_id`)
) ENGINE=MyISAM AUTO_INCREMENT=304 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ship_requirements`
--

LOCK TABLES `ship_requirements` WRITE;
/*!40000 ALTER TABLE `ship_requirements` DISABLE KEYS */;
INSERT INTO `ship_requirements` VALUES (1,1,9,0,2),(2,1,11,0,1),(3,2,9,0,2),(4,3,9,0,1),(5,4,9,0,5),(6,4,11,0,2),(7,6,9,0,7),(8,6,11,0,7),(9,7,9,0,8),(10,7,11,0,5),(11,8,9,0,10),(12,8,11,0,8),(13,8,8,0,7),(14,1,0,4,4),(15,2,0,4,1),(16,3,0,7,1),(17,3,0,4,1),(18,4,0,5,3),(19,7,0,5,5),(20,6,0,6,4),(22,9,0,4,2),(23,10,9,0,5),(24,10,0,5,5),(25,11,0,6,11),(26,11,9,0,9),(27,11,11,0,10),(28,10,0,10,5),(32,12,9,0,8),(33,12,0,3,6),(34,12,0,10,5),(35,13,9,0,12),(36,13,11,0,10),(38,13,0,6,13),(42,14,9,0,4),(43,14,11,0,4),(44,14,0,14,3),(47,15,9,0,4),(46,15,0,5,7),(49,15,0,8,12),(50,15,0,3,9),(51,8,0,5,8),(52,12,0,4,7),(53,42,9,0,10),(54,42,0,16,7),(55,42,0,11,10),(194,43,9,0,3),(57,43,0,20,5),(58,42,0,6,6),(60,43,0,16,2),(61,44,9,0,8),(62,44,0,16,4),(64,40,9,0,5),(65,40,0,16,4),(66,40,0,11,10),(67,40,0,5,6),(68,41,9,0,3),(69,41,0,16,2),(70,41,0,20,5),(71,45,9,0,7),(72,45,0,20,7),(73,45,0,16,6),(74,36,9,0,7),(75,36,0,16,6),(76,36,0,19,4),(77,36,0,20,6),(78,37,9,0,3),(79,37,0,16,2),(80,37,0,20,5),(81,38,9,0,5),(82,38,0,16,4),(83,38,0,6,5),(84,33,9,0,3),(85,33,0,16,2),(86,33,0,20,5),(87,34,9,0,5),(88,34,0,16,4),(89,34,0,5,10),(90,34,0,8,13),(91,32,9,0,7),(92,32,0,16,6),(93,32,0,20,6),(94,32,0,17,3),(95,30,9,0,5),(96,30,0,16,6),(97,30,0,10,12),(98,30,0,5,6),(99,29,9,0,3),(100,29,0,16,2),(101,29,0,20,5),(102,31,9,0,5),(103,31,0,16,4),(104,31,0,10,8),(105,31,0,20,6),(106,28,9,0,5),(107,28,0,16,4),(108,28,0,20,6),(109,28,11,0,6),(110,27,9,0,3),(111,27,0,16,2),(112,27,0,20,5),(113,26,9,0,7),(114,26,0,16,6),(115,26,0,6,6),(116,26,0,18,4),(117,23,9,0,7),(118,23,0,16,6),(119,23,0,6,5),(120,23,0,7,15),(121,25,9,0,3),(122,25,11,0,7),(123,25,0,20,5),(124,25,0,16,2),(125,24,9,0,5),(126,24,0,16,2),(127,24,0,5,7),(128,24,0,10,8),(129,20,9,0,7),(130,20,0,16,6),(131,20,0,6,6),(132,20,0,18,4),(133,21,9,0,3),(134,21,0,16,2),(135,21,0,20,5),(136,22,9,0,7),(137,22,0,6,7),(138,22,0,10,7),(139,22,0,16,6),(140,19,9,0,3),(141,19,0,16,2),(142,19,0,20,5),(143,18,9,0,5),(144,18,0,16,4),(145,18,0,10,7),(146,18,0,5,7),(147,17,9,0,7),(148,17,0,16,6),(149,17,0,6,6),(150,17,0,15,3),(151,46,9,0,6),(152,46,0,12,3),(153,46,0,4,6),(157,47,9,0,11),(158,47,11,0,7),(160,47,0,21,9),(162,47,0,3,6),(163,50,9,0,6),(164,50,0,14,5),(165,50,0,12,6),(166,50,0,3,5),(167,9,11,0,1),(169,51,9,0,1),(170,51,0,4,3),(171,51,0,14,9),(172,51,0,7,8),(173,51,0,11,5),(174,52,9,0,6),(175,52,0,6,8),(176,52,0,3,4),(178,22,11,0,5),(179,44,0,21,6),(180,37,0,21,3),(181,23,0,21,5),(182,53,11,0,8),(184,53,0,14,10),(185,53,0,21,8),(186,53,9,0,10),(187,53,0,9,4),(188,13,0,10,7),(189,13,0,9,6),(190,13,0,8,7),(191,6,0,9,6),(193,2,0,5,1),(195,57,9,0,5),(196,57,0,4,3),(197,58,8,0,4),(199,60,0,5,5),(201,60,0,7,7),(202,60,9,0,9),(203,61,0,19,5),(204,60,0,4,4),(205,61,0,6,7),(206,61,9,0,9),(207,61,0,16,5),(208,61,0,20,4),(209,62,0,21,6),(210,62,0,6,8),(211,62,0,16,5),(212,62,9,0,9),(213,62,0,7,15),(214,63,9,0,9),(215,63,0,5,10),(216,63,0,16,5),(217,63,0,6,9),(218,63,0,10,10),(220,57,11,0,4),(221,59,0,11,14),(222,59,9,0,9),(223,59,0,6,8),(224,59,0,16,8),(225,64,0,20,8),(226,64,0,6,7),(227,64,9,0,9),(228,64,0,16,5),(229,65,0,17,4),(230,65,0,6,7),(231,65,0,20,6),(232,65,0,16,5),(233,65,9,0,9),(234,66,0,15,4),(235,66,0,6,8),(236,66,0,20,5),(237,66,0,16,5),(238,66,9,0,9),(239,67,0,18,5),(240,67,0,6,8),(241,67,0,20,6),(242,67,9,0,9),(243,67,0,16,5),(244,68,0,3,2),(245,68,0,5,2),(246,69,9,0,8),(247,69,11,0,6),(248,69,0,5,5),(249,69,0,9,4),(250,69,0,25,2),(251,70,0,20,5),(252,70,9,0,3),(253,70,0,16,2),(278,72,0,6,8),(275,71,9,0,1),(277,72,0,19,6),(271,72,0,10,10),(279,72,0,16,5),(268,72,9,0,9),(274,71,0,4,1),(280,68,9,0,1),(281,9,9,0,1),(282,79,0,5,4),(286,82,0,5,5),(287,81,0,5,2),(288,80,0,5,1),(289,83,24,0,3),(290,83,0,11,15),(291,83,0,20,7),(292,83,0,25,3),(303,2,0,15,1);
/*!40000 ALTER TABLE `ship_requirements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shiplist`
--

DROP TABLE IF EXISTS `shiplist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shiplist` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shiplist`
--

LOCK TABLES `shiplist` WRITE;
/*!40000 ALTER TABLE `shiplist` DISABLE KEYS */;
/*!40000 ALTER TABLE `shiplist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ships`
--

DROP TABLE IF EXISTS `ships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ships` (
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
) ENGINE=MyISAM AUTO_INCREMENT=91 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ships`
--

LOCK TABLES `ships` WRITE;
/*!40000 ALTER TABLE `ships` DISABLE KEYS */;
INSERT INTO `ships` VALUES (1,'UNUKALHAI Transportschiff',1,'Dies ist ein grosses Transportschiff, dessen riesige Lagerräume alle Arten von Waren aufnehmen können. ','Nachdem die Algol Transportschiffe sich mit einem ungeahnten Erfolg im ganzen Universum verbreitet hatten, wurde das Unukalhai Transportschiff konzipiert, welches eine grössere Lagerkapzität aufweist. Da man die Konvois mit Antares schützte, war auch für die Unukalhais keine grössere Bewaffnung nötig; man konzentrierte sich ausserdem vor allem auch auf die grössere Sicherheit für die Navigationssysteme, weil diese bei den Algols viel wegen kosmischer Strahlung ausgefallen sind.',6000,1400,0,2100,0,0,0,45,70,10,0,65000,0,1,2850,600,300,1,1,0,'transport,position,fetch,attack,flight,support,alliance','0.50',0,400,100,30,0,1,0,2,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','9.500',0,0),(2,'ANTARES Jäger',1,'Kleines Kampfschiff, ideal für die Begleitung kleinerer Konvois. Auch geeignet für Raubzüge und Übergriffe auf schwach befestigte Planeten.','Der Antares Jäger wurde als erster kampftauglicher Jäger hergestellt, um die Rohstoffkonvois vor Piraten zu schützen. Sie eignen sich zu Beginn als Begleitschutz, aber ihre Technologie ist nicht sehr weit entwickelt, deshalb sind die Herstellungskosten im Vergleich mit ihrer Leistung relativ hoch. Die Antares wurden nicht für grössere Angriffe auf befestigte Planeten konzipiert, auch deshalb werden sie von den wenigsten Armeen in grösseren Mengen genutzt.',750,575,50,420,0,0,0,5,4,1,0,500,0,1,380,15,13,1,1,0,'transport,position,attack,flight,support,alliance','0.50',0,330,60,170,0,1,0,1,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','1.795',0,0),(3,'ZAVIJAH Spionagesonde',1,'Diese Sonde erkundet fremde Planeten und sendet die Daten an dein Kontrollzentrum zurück.','Nachdem die Raumpiraten wegen den schnell konstruierten planetaren Verteidigungsanlagen nicht mehr jedes System gefahrlos ausrauben konnten, erfanden sie dieses kleine, nützliche Schiff. Es kann wegen seiner Grösse praktisch unbemerkt in Frage kommende Planeten ausspionieren und detaillierte Informationen über die stationierte Flotte liefern. Dank seiner Geschwindigkeit wird es dabei äusserst selten abgeschossen. Um diese Geschwindigkeit erreichen zu können, müssen sie sehr leicht gebaut sein und können keine Bewaffnung tragen. Ausserdem haben sie einen sehr kleinen Laderaum und können deshalb nur über kürzere Distanzen verwendet werden.',100,300,0,80,0,0,0,1,1,0,0,150,0,0,25000,2,1,1,1,9,'position,spy,flight,support','0.50',0,10,1,0,0,1,0,2,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.480',0,0),(4,'TAURUS Besiedlungsschiff',1,'Das TAURUS Besiedlungsschiff ist ein Schiff, mit dem andere Planeten besiedelt werden können. Es kann Rohstoffe und Passagiere aufnehmen, ist aber auch dementsprechend langsam.','Sobald auf dem Heimatplaneten die grundlegende Infrastruktur aufgebaut war, waren die Herrscher mit nur einem Planeten nicht mehr zufrieden. Also baute man die Taurus Besiedlungsschiffe, die andere Planeten für das eigene Imperium annektieren können. Da sie die ganze Lebenserhaltung für die Kolonialisten in einer lebensfeindlichen Umwelt gewährleisten müssen, gestaltet sich ihre Herstellung als langwierig und teuer, und das Schiff kann wegen seiner Masse nur langsam bewegt werden.',8000,10500,1200,5000,0,0,0,13,15,5,0,10000,0,5,750,600,660,1,1,8,'transport,position,attack,colonize,flight,support,alliance','0.50',0,1000,500,100,0,1,0,2,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','24.700',0,0),(6,'HADAR Schlachtschiff',1,'Das HADAR-Schlachtschiff ist ein gut gepanzertes und stark bewaffnetes Kriegsschiff. Mit ihm können auch grössere Verteidigungsstellungen ausgeschaltet, oder die eigenen Planeten vor Angriffen geschützt werden.','Nachdem jede noch so kleine Nation eine Verteidigung errichtet hatte, welche mit Antares ohne tragbare Verluste nicht geknackt werden konnte, entschlossen sich die grösseren Nationen, ein neues Kampfschiff zu konstruieren. Man nahm den Rumpf eines Besiedlungsschiffes, baute Waffen und eine Panzerung ein, und das Hadar Schlachtschiff war geboren.',50000,31500,19500,12500,0,0,0,45,90,80,0,8500,0,4,3200,1260,220,1,1,3,'transport,position,attack,flight,support,alliance','0.50',0,28200,7100,13000,0,1,0,1,1,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','113.500',0,0),(7,'POLLUX Bomber',1,'Dieses Raumschiff ist sehr effektiv gegen gegnerische Verteidigungsanlagen.','Trotz allen Erfolgen, die die Hadar Schlachtschiffe bei der Zerstörung gegnerischer Flotten und Verteidigung erzielten, war man damit noch nicht zufrieden. Deshalb konstruierte man ein neues, bis an die Zähne bewaffnetes Schiff, den Pollux Bomber. Nachdem man das Schiff mit Waffen beladen hatte, erwies es sich, dass deshalb die Angriffsgeschwindigkeit eingeschränkt wurde. Wegen diesem Nachteil konnte der Bomber sich in grossen Flotten nicht etablieren, er ist aber trotzdem in allem eine nicht zu unterschätzende Waffe, welche grosse Zerstörung anrichten kann.',9700,21000,11500,8500,0,0,0,55,80,70,0,2000,0,2,1200,600,120,1,1,4,'transport,position,attack,flight,support,alliance','0.50',0,2600,500,18000,0,1,0,1,1,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','50.700',0,0),(8,'SIRIUS Invasionsschiff',1,'Mit Hilfe dieses Raumschiffes können Planeten von anderen Spielern übernommen werden.','Es gab einmal ein florierendes Wirtschaftsimperium und die Infrastruktur ihrer Kolonien wurde von den anderen Völkern beneidet. Einer dieser bösen Nachbaren hatte die Idee, dass er so einen Planeten wirklich gut gebrauchen könnte. So wurde unter strengster Geheimhaltung dieses Invasionsschiff gebaut, welches die Planeten anderer Spieler übernehmen kann. Das Schiff hat aber nicht die grössten Erfolgschancen und es kann keine Hauptplaneten übernehmen. Trotzdem stellt dieses Schiff eine Bedrohung dar, deshalb sollte man seine Planeten nie unbewacht lassen.',80000,35000,55000,40500,0,0,0,15,800,500,0,20000,0,20,600,800,500,1,1,9,'transport,position,attack,invade,flight,support,alliance','0.50',0,2000,3000,180,0,1,0,1,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','210.500',0,0),(9,'ALGOL Transportschiff',1,'Dies ist ein kleines Transportschiff, dessen Lagerräume alle Arten von Waren aufnehmen können. ','Das Algol Transportschiff war das erste wirkliche Raumschiff, welches in Serienproduktion ging. Man wollte damit vor allem Rohstoffe zu anderen Planeten transportieren, damit man die natürlichen Ressourcen der verschiedenen Planeten besser ausnutzen kann. Deshalb hat man bei der Ausrüstung auf eine Bewaffnung weitestgehend verzichtet. Obwohl Algols mittlerweile veraltet sind, hat man dieses beliebte Schiff immer wieder mit neuen Motoren modifiziert, deshalb sieht man auch heute noch viele Transporter ähnlichen Typs.',700,180,0,500,0,0,0,25,9,4,0,15000,0,1,800,500,250,1,1,7,'transport,fetch,position,attack,flight,support,alliance','0.50',0,300,500,10,0,1,0,2,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','1.380',0,0),(10,'REGULUS Trümmersammler',1,'Mit diesem Schiff können die Trümmer der nach einer Schlacht zerstörten Schiffe eingesammelt und wiederverwendet werden.','Nachdem die Piraten durch die Entwicklung der mächtigen Kampfschiffe nicht mehr die unbewaffneten Transportkonvois überfallen konnten, entwickelten sie dieses Schiff, um mit ihm nach den grösseren Schlachten zwischen den kriegslustigen Imperien aufzutauchen, und ihren Lebensunterhalt aus den Überresten der zerstörten Schiffe zu gewinnen. Der Wert dieser Trümmersammler wurde schon bald erkannt, und ab dann führte niemand mehr Krieg, ohne sich nicht die Überreste der Schiffe zurück zu holen, um daraus neue Schiffe zu bauen.',3000,2000,1000,8000,0,0,5,33,20,20,0,15000,0,2,600,600,200,1,1,10,'transport,collectdebris,position,attack,flight,support,alliance','0.50',0,800,1200,20,0,1,0,2,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','14.000',0,0),(11,'RIGEL Dreadnought',1,'Dieses Schiff ist eine riesige fliegende Festung. ','Aus der Erfahrungen, die man mit den Hadar und den Pollux gewonnen hatte, wurde ein neues Superschiff kreiert, der Rigel Dreadnought. Optimierungen in der Herstellung und bei den Antrieben verliehen dem Schiff eine aussergewöhnliche Kampfkraft, Effizienz und Geschwindigkeit zu erstaunlich niedrigen Preisen. Zusätzlich erhöhte man die Transportkapazität, so dass die Rigel eigenständig praktisch aus dem Nichts heraus Raubzüge unternehmen können, ohne sich mit langsamen Transportern zu belasten. ',3350000,2975000,1750000,750000,0,0,0,280,2350,3400,0,600000,0,560,4800,620,400,1,1,5,'transport,position,attack,flight,support,alliance','0.50',0,1000000,1350000,1750000,0,1,0,1,1,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','8825.000',0,0),(12,'ELNATH Gassauger',1,'Dieses Schiff kann Wasserstoff aus der Atmosphäre von Gasplaneten einsaugen und daraus Tritium gewinnen.','Nachdem die Flotten immer grösser wurden, hatte man nicht mehr genug Tritium auf den Planeten zur Verfügung, um sie zu bewegen. Deshalb kam man auf die Idee, Wasserstoff von den unbewohnbaren Gasplaneten abzusaugen und es in Tritium umzuwandeln. Genau dafür wurde dieses Schiff konstruiert. Es wurde schnell klar, dass dieses Saugen äusserst rentabel ist und deshalb wurde der Gassauger soweit verbessert, dass heute eine grössere Flotte ohne ihn praktisch undenkbar ist.',20000,7500,22200,15000,0,0,0,55,160,130,0,9000,0,3,600,4300,860,1,1,12,'transport,position,collectcrystal,collectfuel,flight,support','0.50',0,650,800,0,0,1,0,2,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','64.700',0,0),(13,'ANDROMEDA Kampfstern',1,'Dieses Schiff ist das mächtigste Schiff der Galaxien.','Ein verrückter Wissenschaftler war von der Idee besessen, ein Kampfschiff zu bauen, welches so gross wie ein ganzer Trabant wäre. Er wurde so lange ausgelacht, bis er einen anderen Verrückten traf, der zufällig nebenberuflich Imperator war und der ihn unterstützte. Danach wurde Wissenschaftler allgemein als Genius bekannt, welcher die ultimative Waffe erschaffen hatte: den Andromeda Kampfstern. Seine Waffen und Schilder sind bis heute noch unübertroffen!\r\nDer einzige Nachteil dieses monströsen Kampfschiffes ist, dass es wegen seiner Masse lange Start- und Landezeiten hat, und eine zahlreiche Besatzung benötigt wird.',20000000,10000000,12000000,12000000,0,0,0,800,8000,4000,0,6000000,0,990,10000,3501,2450,1,1,7,'transport,position,attack,flight,support,alliance','0.50',0,8500000,9000000,9500000,0,1,0,1,1,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','54000.000',0,0),(14,'STARLIGHT Jäger',1,'Weiterentwicklung des ANTARES Jäger.','Parallel zu den Antares Jägern wurde der STARLIGHT Jäger entwickelt, welcher besser gepanzert war und auch die bessere Bewaffnung aufwies. Er nutzte auch einen neuartigen Antrieb, welcher aber noch nicht ganz serienreif war, da er andauernd ausfiel, und selten wie geplant lief. Nach einigen Untersuchungen fand man heraus, dass dies daran lag, dass beim Bau des Motors billiges Material verwendet wurde. Das stellte den viel gelobten Jäger in ein anderes Licht, aber andererseits erwies er sich in Raumschlachten als zuverlässiger Mitstreiter.',4900,3400,2400,2100,0,0,0,2,5,6,0,800,0,1,975,22,20,1,1,1,'transport,position,attack,flight,support,alliance','0.50',0,2100,1100,1900,0,1,0,1,1,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','12.800',0,0),(15,'ONEFIGHT Kampfdrohne',1,'Die Kampfdrohne ist sehr nützlich, um zuerst die gegnerische Flotte zu zerstören und danach mit Transportern die Rohstoffe abzusahnen. Wie der Name schon sagt, sind diese Drohnen Einweg-Sonden; sie werden bei einem Angriff immer verbraucht.','Es gab zwei Nachbarn, die lange Zeit friedlich miteinander lebten, aus dem einfachen Grund, dass die Flotten beider Kontrahenten etwa gleich gross war; und niemand den anderen ohne Verluste hätte angreifen können. Das änderte sich, als der erste die Kampfdrohnen entwickelte, ein billiges Schiff, welches aber eine äusserst grosse Kampfkraft aufweist, aber sobald es von einer Waffe getroffen wird, explodiert. Als die Flotte des einen zerstört war, hatte man der Invasion nichts mehr entgegenzusetzen, und jetzt leben sie als eine Rasse wieder friedlich miteinander.',200,700,300,300,0,0,0,1,15,2,0,300,0,0,17000,20,30,1,1,2,'position,attack,flight,support,alliance','0.50',0,0,0,650,0,1,0,1,1,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','1.500',0,0),(16,'Handelsschiff',1,'Ein Schiff der neutralen Handelsgilde.','Ein Schiff der neutralen Handelsgilde. Es wird benutzt um Einkäufe im Markt zu den Käufern zu transportieren.',0,0,0,0,0,0,0,0,0,0,0,100000,0,0,10000,60,60,0,0,0,'market','0.50',0,0,0,0,0,0,0,5,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.000',0,0),(17,'TERRANIA Zerstörer',1,'Kann ein Gebäude bombadieren.','Den Terranern war die Infrastruktur ihrer Feinde ein Dorn im Auge, also entwickelten sie diesen Zerstörer, um den Gegner durch die Zerstörung seiner Infrastruktur zur Kapitulation zu zwingen. Der Terrania Zerstörer ist ein gutes Schiff, obwohl der Angriff nicht immer erfolgreich ist, da sich herausstellte, dass das Zielen vom Orbit aus nicht die leichteste Übung ist. Dafür kann ein erfolgreicher Bombenabwurf enormen Schaden hervorrufen.',85000,40000,50000,40000,0,0,0,100,651,525,0,50000,0,25,3000,1950,1919,1,1,0,'transport,position,attack,bombard,flight,support,alliance','0.50',0,20000,19000,60000,1,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','215.000',0,0),(18,'PROMETHEUS Recycler',1,'Grosser Recycler.','Dieser Recycler wurde nach Prometheus dem Titanen, welcher gegen Zeus rebellierte, und den Menschen das Feuer brachte, benannt, da mit den Rohstoffen, welche die Terraner mit seiner Hilfe gewinnen, deren Flotten gebaut werden. Früher brachte Prometheus ihnen mit dem Feuer die Möglichkeit, eine Kultur zu entwickeln. Heute bringen viele Tausend Prometheus den Menschen mit ihren Rohstoffen die Grundlage, ihre Kultur weiterzuentwickeln.',10000,8000,4000,25000,0,0,0,42,50,80,0,90000,0,3,1500,360,182,1,1,0,'transport,collectdebris,position,attack,flight,support,alliance','0.50',0,800,1000,5,1,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','47.000',0,0),(19,'GAIA Transporter',1,'Bewohnertransporter der Terraner.','Als die Erde wegen Überbevölkerung einen vollständigen Kollaps erlitt, musste sie schleunigst evakuiert werden, und dafür wurde dieser Transporter entwickelt. Die Bewohner wurden zu Zehntausenden bei normalerweise untragbaren Bedingungen in diese Kolosse gesteckt und verfrachtet. Nach dieser Katastrophe etablierte dieser Transporter sich zu einem beliebten Fährschiff, mit welchem die Leute zu den Vergnügungsplaneten flogen, um sich vom täglichen Arbeitsstress zu erholen.',3500,1000,750,1250,0,0,0,55,100,80,0,3000,10000,1,900,720,360,1,1,0,'transport,fetch,position,attack,flight,support,alliance','0.50',0,750,300,50,1,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','6.500',0,0),(20,'ANDREIA Bomber',1,'Dieser Bomber ermöglicht Giftgasangriffe.','Die Andorianer sind sehr invasionsfreudig, aber sie wollten die invasierten Planeten nicht der ursprünglichen Bevölkerung überlassen, da sie sich selber genug schnell vermehren können. Deshalb fliegen sie vorher mit den Andreia Bombern über die Planeten, welche dann die Bevölkerung mit Giftgas auslöschen. Brutal, aber effizient.',85000,40000,50000,40000,0,0,0,93,525,650,0,15000,0,25,1000,1200,1320,1,1,0,'transport,position,attack,gasattack,flight,support,alliance','0.50',0,25000,9000,10000,2,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','215.000',0,0),(21,'ATLAS Transporter',1,'Grosser Transporter','Auch die Andorianer entwickelten einen grösseren Transporter, da sie nicht wollten, dass andere Rassen mit ihren Transportern ihre Ressourcen herumschippern konnten und sie von diesen abhängig wären. Die Atlas entwickelten sich zu viel genutzten Transportern im Andorianischen Imperium. Sie erwiesen sich als viel nützlicher, als es sich die Regierungsmitglieder jemals erhofft hätten.',30000,6000,1000,12500,0,0,0,55,100,10,0,325000,0,1,3100,720,300,1,1,0,'transport,fetch,position,attack,flight,support,alliance','0.50',0,300,500,30,2,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','49.500',0,0),(22,'ZELOS Kreuzer',1,'Weiterentwicklung des HADAR Schlachtschiffes.','Nachdem sich der Bau von Hadar Schlachtschiffen durchgesetzt hatte, wollten die Andorianer dieses noch übertreffen. So wurde der Zelos Kreuzer entwickelt.\r\nDieses Schiff hat ungeheuer starke Schilde und ist sehr gut für die Verteidigung Planeten geeignet.',121000,44000,50000,45400,0,0,0,45,160,100,0,16000,0,10,5500,350,320,1,1,2,'transport,position,attack,flight,support,alliance','0.50',0,15000,56500,50000,2,1,0,4,1,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','260.400',0,0),(23,'CENTAURUS Spioschiff',1,'Kann von einem anderen Spieler eine Technologie klauen.','Die Centauri waren äusserst stolz darauf, dass sie die höchsten Technologien aller Völker besassen. Entsprechend gross war der Neid, als sie von einem andern Volk in einer von ihnen vernachlässigten Technologie übertrumpft wurden. Also erfanden sie dieses Spionageschiff, mit dessen Hilfe sie den anderen Völkern etwaige höher entwickelte Technologien klauen können.',85000,40000,50000,40000,0,0,0,7,125,250,0,7500,0,45,600,2905,2359,1,1,0,'transport,position,attack,spy,spyattack,flight,support,alliance','0.50',0,3250,2250,500,8,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','215.000',0,0),(24,'PEGASUS Gassauger',1,'Grosser Gassauger der Centauri.','Um ihre teuren Forschungen zu betreiben, mussten die Centauri einen neuen Gassauger entwerfen, welcher eine grössere Kapazität hat, da die normalen Sauger die Bedürfnisse ihrer Forschungslabore nicht stillen konnten. Der Pegasus hat  eine wesentlich grössere Saugkapazität als herkömmliche Sauger, und durch seine hoch entwickelten Saugarme hat er die grössere Effizienz. Dies ist die Antwort der Centauri auf Tritiumknappheit.',60000,28000,25000,60000,0,0,0,38,5,8,0,40000,0,3,1500,950,1450,1,1,0,'transport,position,attack,collectcrystal,collectfuel,flight,support,alliance','0.50',0,800,1000,5,8,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','173.000',0,0),(25,'EUROPA Fighter',1,'Mittelgrosses, für seine Verhältnisse jedoch sehr starkes Kriegsschiff der Centauri.','Die Centauri suchten ihren Vorteil in der Überlegenheit der Technologien, aber als die Rigel die Herrschaft über die Schlachtfelder übernahmen, entwickelten sie ihren eigenen Prototypen, den Europa Fighter. Heutzutage eines der stärksten Raumschiffe der mittleren Kampfklasse. Die Europas sind bei weitem nicht so stark wie Rigel, jedoch haben sie eine sehr kurze Startzeit, was sie sehr gefährlich macht.',20000,11000,18000,8000,0,0,0,85,35,25,0,22000,0,5,6900,280,870,1,1,0,'transport,position,attack,flight,support,alliance','0.50',0,6250,12500,7500,8,1,0,4,1,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','57.000',0,0),(26,'VORGONIA Bomber',1,'Kann einen Antraxangriff ausführen','Die Vorgonen verachten alle Rassen, die sich von ihrer eigenen unterscheiden, und entschieden sich deshalb dafür, dieses Übel mit allen Mitteln auszumerzen. Dafür bauten sie die Antraxbomber, um mit dem Kampfstoff Antrax die systematische Vernichtung feindlicher Völker zu beginnen. Zusätzlich wird die Nahrung auf dem Planeten vergiftet und dadurch unbrauchbar. Da viele Völker danach von Hungersnöten heimgesucht wurden, haben viele Bewohner Angst vor diesen Bombern und sind deshalb gegen Kriege mit Vorgonen.',85000,40000,50000,40000,0,0,0,80,625,550,0,13500,0,42,1000,1358,1989,1,1,0,'transport,position,attack,antrax,flight,support,alliance','0.50',0,28000,8000,8000,7,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','215.000',0,0),(27,'PAN Transporter',1,'Bewohnertransporter der Vorgonen.','Nachdem die Vorgonen die feindliche Bevölkerung mit ihren Antraxbombern eliminiert hatten, mussten sie ihre eigene Bevölkerung auf diese Planeten transportieren. Dazu entwickelten sie die Pan Transporter.',3250,1000,750,1250,0,0,0,55,100,50,0,2000,6750,1,1000,720,360,1,1,0,'transport,fetch,position,attack,flight,support,alliance','0.50',0,300,500,30,7,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','6.250',0,0),(28,'IKAROS Jäger',1,'Schwebt in der Atmosphäre und hat somit keine Start- und Landezeit.','Die Vorgonen raubten alle ihre direkten Nachbarn mit Jägern aus, und bald einmal gab es erste Piloten, die gar nie mehr richtig auf dem Heimatplaneten landeten, sondern im Dauereinsatz waren. Dank ihren unerwarteten Raubzügen konnten sie viele Rohstoffe erbeuten. Diese Elitepiloten waren aber bald nicht mehr zufrieden mit den normalen Schiffen, also entwickelten sie ihre Jäger weiter, bis die Ikaros entstanden, die im Orbit des Planeten stationiert sind, so dass sie sofort und ohne Treibstoffverbrauch starten und landen können.',4000,2000,1000,2000,0,0,0,30,0,0,0,20000,0,2,2400,0,0,1,1,0,'transport,position,attack,flight,support,alliance','0.50',0,350,2750,1250,7,1,0,4,1,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','9.000',0,0),(29,'MARAUDER Transporter',1,'Grosser Transporter','Auch die Ferengi sahen sich genötigt, grosse Transporter zu entwickeln, wenn auch nicht aus denselben Gründen wie die anderen Rassen. Die Ferengi hatten wegen ihrer Titanproduktion alle ihre Lager längstens überfüllt und keinen Platz mehr, um grössere zu bauen. Also erschufen sie mit den Marauder Transportern eine Art fliegendes Lager, damit sie ihr Titan im Weltraum zwischenlagern konnten, wo es genug Platz dafür hat.',33000,6000,1000,4000,0,0,0,60,100,50,0,325000,0,1,3700,720,333,1,1,0,'transport,fetch,position,attack,flight,support,alliance','0.50',0,300,500,30,6,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','44.000',0,0),(30,'BELL Zerstörer',1,'Dieses Schiff hat einen riesigen Schutzschildgenerator an Bord.','Die Ferengi waren an einem Punkt angelagt, wo sie ihr Titan nicht mehr verbrauchen konnten. Es musste etwas erfunden werden, das mit möglichst viel Titan und wenig Zusatzstoffen gebaut werden konnte. Aus diesem Bedürfnis entstand der Bell Zerstörer.\r\nDieses Schiff hat einen riesigen Schutzschildgenerator an Bord, welcher einen Schutzschild erzeugt, der kaum überwunden werden kann. Aufgrund der Masse des Schiffes, dessen Antrieben und dem eingebauten Generator gehört der Bell Zerstörer nicht zu den schnellsten Schiffen der Galaxien, jedoch zu den Stärksten im Kampf.\r\nEin Nachteil des Bell Zerstörers ist sein immenser Tritiumverbrauch, der aus dem grossen Gewicht und der tiefen Fluggeschwindigkeit resultiert.',60000,5000,21250,5000,0,0,0,100,150,80,0,40000,0,30,4125,1230,890,1,1,0,'transport,position,attack,flight,support,alliance','0.50',0,6000,35000,1500,6,1,0,4,1,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','91.250',0,0),(31,'RUTICULUS Sammler',1,'Allessammler der Ferengi.','Den Ferengi war es zu umständlich, Trümmersammler, Asteroidensammler und Gassauger zu haben, also konzipierten sie kurzerhand ein Schiff, welches alles kann. Der Ruticulus Sammler ist deshalb ein äusserst praktisches Schiff, da jeder sie nach seinem Wunsch und entsprechend der jeweiligen Situation anwenden kann.',20000,10000,15000,30000,0,0,0,40,20,20,0,15000,0,1,600,640,1800,1,1,0,'transport,collectdebris,position,attack,collectmetal,collectcrystal,collectfuel,flight,support,alliance','0.50',0,800,1000,50,6,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','75.000',0,0),(32,'RIGELIA Bomber',1,'Kann ein Gebäude für eine bestimmte Zeit mittels EMP-Technologie ausser Kraft setzten.','Der Rigelia Bomber kann mit seinen EMP-Angriffen die feindlichen Gebäude für eine kurze Zeit ausser Kraft setzen, was in einem Krieg schwerwiegende Folgen haben kann. Obwohl der Bomber sehr teuer ist, und von seiner Kampfkraft her gesehen kaum genutzt werden sollte, sind viele Generäle der Meinung, dass seine Bomben genug effektiv sind, so dass man diese Möglichkeit in einem Krieg immer einsetzen sollte.',85000,40000,50000,40000,0,0,0,55,325,250,0,15000,0,41,1000,1520,2001,1,1,0,'transport,position,attack,emp,flight,support,alliance','0.50',0,25000,6500,12500,3,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','215.000',0,0),(33,'EOS Transporter',1,'Grosser Transporter','Als die Cardassianer und die Minbari die grossen Transporter entwickelt hatten, konnten die Rigelianer dem nicht nachstehen und fertigten sofort ihre eigene Version eines grossen Transporters an. Vom Prinzip her ist es genau dasselbe Schiff wie der Saiph Transporter der Minbari. Die Rigelianer passten einfach das Design und die Steuergeräte ihren Bedürfnisse an.',25000,7000,1000,3000,0,0,0,55,100,50,0,325000,0,1,3800,252,435,1,1,0,'transport,fetch,position,attack,flight,support,alliance','0.50',0,300,500,30,3,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','36.000',0,0),(34,'HELIOS Drohne',1,'Weiterentwicklung der Onefight Kampfdrohne. Rassenschiff der Rigelianer.','Die Rigelianer waren von den Onefight Kampfdrohnen begeistert. Sie steckten deshalb ihre ganzen Forschungsmittel in deren Weiterentwicklung. So entstand die Helios Drohne: Diese Drohne ist noch effizienter als die Onefight und kann in genügend grosser Anzahl den Gegner empfindlich treffen. Ausserdem können die Helios im Gegensatz zu den Onefights einen Kampf auch überleben.\r\nDie Helios sind überall wegen ihrer Kampfkraft gefürchtet, und da sie auf dem Standardantrieb der Drohnen aufbauen, haben sie auch eine hohe Geschwindigkeit, weshalb man sich nie vor einem Angriff sicher fühlen kann.',2500,6200,2000,2300,0,0,0,1,5,5,0,500,0,0,15000,40,60,1,1,0,'position,attack,flight,support,alliance','0.50',0,1,0,6000,3,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','13.000',0,0),(36,'CARDASSIA Mutterschiff',1,'Heilt während dem Kampf eine gewisse Anzahl Schild- und Strukturpunkte.','Nachdem die Cardassianer mit ihren Nilams die ganze Galaxie in Angst und Schrecken versetzt hatten, schlossen sich alle anderen Rassen zu einem Bund zusammen, um die Cardassianer zu vernichten. Trotzdem hatten sie nicht mit dem neuen Geniestreich der Cardassianer gerechnet: Den Mutterschiffen. Mit diesem hoch entwickelten Raumschiff können die Cardassianer ihre Flotte während dem Kampf reparieren, um so Verluste auszugleichen. Nur dank der Hilfe dieses Schiffes konnten die Cardassianer den immerwährenden Angriffen standhalten.',70000,27500,27500,35000,0,0,0,60,70,10,0,1500,0,5,1800,726,333,1,1,0,'transport,position,attack,flight,support,alliance','0.50',10000,7000,3000,125,9,1,0,4,1,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','160.000',0,0),(37,'DEMETER Transporter',1,'Grosser Transporter','Die Cardassianer waren allgemein wegen ihren vielen Rohstoffen beneidet, vor allem wegen ihrer Nahrung, die sie wie keine anderen herstellen können. Um sich vor Übergriffen zu schützen und um ihre Gegner im Unklaren über ihre wahren Rohstoffmengen zu lassen, entwickelten sie diese Transporter, welche mit den Rohstoffen irgendwo in der Ewigkeit des Alls herumfliegen, damit sie nicht gefunden werden. Die Cardassianer sind die einzigen, deren Organisation solch perfekte Nachschublinien zustande bringt.',23000,8300,1200,1500,0,0,0,10,100,10,0,350000,0,1,3500,585,395,1,1,0,'transport,fetch,position,attack,flight,support,alliance','0.50',0,300,500,30,9,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','34.000',0,0),(38,'NILAM Fighter',1,'Ein starkes Kampfschiff aus der Mittelschweren Klasse, entwickelt von den Cardassianern.','Den Cardassianern waren Starlights von Anfang an zu langsam und Drohnen zu schwach. So erfanden sie die Nilam, welche sie zu gefürchteten Jägern entwickelten, da sie spezielle Antriebe haben, die ausserordentlich schnell sind. Die Cardassianer benutzen die Jäger vor allem, um ihre Militärdiktatur aufrechtzuerhalten. Sie wollen schnell reagieren und überall bereitstehen können. Dafür eignen sich die Nilams am besten. Sie kommen aus dem Nichts und verschwinden sofort wieder, nachdem sie die Schlacht gewonnen haben.',7150,4000,2000,3000,0,0,0,40,100,100,0,5000,0,4,6250,456,325,1,1,0,'transport,position,attack,flight,support,alliance','0.50',0,2900,2000,2500,9,1,0,4,1,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','16.150',0,0),(40,'SERA Kreuzer',1,'Dieses Schiff kann dem Angegriffen eine Flotte vortäuschen, die gar nicht vorhanden ist.','Den räuberischen Orionern gefiel es nicht, dass sich die Feinde auf ihre Angriffe vorbereiten konnten. Die Lösung fanden sie in der Konstruktion vom Sera Kreuzer. Dieser hat die Fähigkeit, Hologramme anderer Schiffe zu erstellen und dem Gegner damit eine grosse angreifende Flotte vortäuschen. Ziel ist es, schwächere Flotten zu vertreiben, so dass die Rohstoffe ungeschützt rumliegen.',15500,10500,6000,5500,0,0,0,30,65,45,0,6000,0,2,8500,640,156,1,1,0,'transport,position,attack,fakeattack,flight,support,alliance','0.50',0,500,5500,1500,4,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','37.500',0,0),(41,'HYPOS Drohne',1,'Kann ein Trümmerfeld beim Gegner erstellen, ohne dass dieser etwas merkt.','Die Orioner wollten nicht mehr angreifen müssen, um ein Trümmerfeld zu erstellen. Deshalb schicken sie diese Drohnen vor grossen Schlachten los, um beim Gegner ein klitzekleines Trümmerfeld zu erstellen. Damit konnte den Navigationscomputern der Sammler ein gültiges Ziel zugewiesen werden. Zu diesem Zweck muss sich die Drohne beim Gegner in die Luft sprengen, was selten für mehr als eine Sternschnuppe wahrgenommen wird.',500,300,50,200,0,0,0,6,10,10,0,2000,0,0,17000,1,1,1,1,0,'position,attack,createdebris,flight,support,alliance','0.50',0,10,0,1,4,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','1.050',0,0),(42,'MINBARI Jäger',1,'Wenn er alleine in oder in einem Flottenverband aus lauter Minbari Jägern fliegt, ist er für die gegnerische Flottenkontrolle nicht sichtbar.','Die Minbari sahen es gar nicht gerne, als man ihre Flotten schon im Anflug entdeckte und eine entsprechende Verteidigung bereitstellte. Deshalb liessen sie die besten Köpfe der Galaxie zusammenkommen, um dieses Schiff zu entwickeln, welches durch seine perfekte Tarnung erst im allerletzten Moment entdeckt werden kann. Und dann ist es bereits zu spät...',20500,13500,13500,10000,0,0,0,20,120,130,0,15000,0,10,6500,1189,125,1,1,0,'transport,position,attack,stealthattack,flight,support,alliance','0.50',0,13000,4500,5000,5,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','57.500',0,0),(43,'SAIPH Transporter',1,'Grosser Transporter der Minbari.','Die Minbari entwickelten diese grossen Transporter, um ihren steigenden Rohstofftransport-Bedürfnissen nachzukommen. Die Rohstoffmengen stiegen immer weiter an, und irgendwann war auch die Kapazität der Unukalhai ausgeschöpft. Nun musste eine neue Lösung gefunden werden, und die Ingenieure der Minbari entwickelten diesen grossen Transporter.',26000,6000,3000,9000,0,0,0,35,100,50,0,350000,0,1,5200,721,326,1,1,0,'transport,fetch,position,attack,flight,support,alliance','0.50',0,300,500,30,5,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','44.000',0,0),(44,'WEZEA Fighter',1,'Kampfschiff der Minbari, welches auch Gassaugen kann.','Die Minbari liebten es seit eh und je, über die Gasplaneten zu fliegen, da sie von den unbeschreiblich schönen Polarlichtern fasziniert sind, welche man dort beobachten kann. Deshalb gingen sie so weit, sogar ihre Jäger so zu konstruieren, dass sie zu Gasplaneten fliegen und auch Gas saugen können.\r\nEs ist ihnen sogar gelungen, den neuartigen Solarantrieb zu integrieren. Damit verbraucht der WEZEA Fighter nur Tritium für den Start und die Landung.',14000,7000,9000,8000,0,0,0,0,700,400,0,12500,0,3,5100,1750,540,1,1,0,'transport,position,attack,collectfuel,flight,support,alliance','0.50',0,7250,3500,5800,5,1,0,4,1,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','38.000',0,0),(45,'ORION Fighter',1,'Kampfschiff der Mittelschweren Klasse, entworfen von den Orionern. Kann bis zu 75% der Rohstoffe von einem fremden Planeten mitnehmen. ','Den Orionern war die Menge, welche sie normalerweise mit ihren Schiffen von gegnerischen Planeten erbeuten konnten, viel zu wenig. Der Orion Fighter ist ihre Antwort auf dieses Problem. Ein starkes Raumschiff, welches so konzipiert ist, dass es 50% mehr Rohstoffe als Beute mitnehmen kann als alle anderen Schiffe. Zusätzlich hat der Orion schlagkräftige Waffen, was den Fighter zum optimalen Schiff für Piraterie macht.',35000,10500,12500,5500,0,0,0,65,100,90,0,17500,0,4,8000,451,352,1,1,0,'transport,position,attack,flight,support,alliance','0.75',0,7500,7000,14000,4,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','63.500',0,0),(46,'FORNAX Asteroidensammler',1,'Kann Asteroidenfelder anfliegen und dort Rohstoffe sammeln.','Da die Gassauger grossen Erfolg hatten, dachte man, dass man das auch mit Asteroidenfeldern versuchen könne, so dass man auch die anderen Rohstoffe aus dem Weltraum gewinnen konnte. Leider war die praktische Umsetzung schwieriger, da eine sichere  Navigation innerhalb der Asteroidenfelder sich als praktisch unmöglich erwies. Deshalb ist dieses Konzept fehlgeschlagen, da die Sammler schneller von Asteroiden getroffen werden, als dass sie genug Rohstoffe holen können, um ihre Herstellungskosten zurückzugewinnen.',15000,5000,25000,9000,0,0,0,65,100,120,0,45000,0,8,600,4350,1050,1,1,11,'transport,position,attack,collectmetal,flight,support,alliance','0.50',0,250,1000,50,0,1,0,2,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','54.000',0,0),(47,'TITAN Transporter',1,'Dies ist ein relativ billiger und sehr schneller, grosser Transporter.','Dies ist ein relativ billiger und sehr schneller, grosser Transporter, allerdings zeigt sich der Preis in seiner Qualität. Er ist sehr schwach. Dieser Transporter setzt auf den Solarantrieb, wodurch er durch ein Sonnensegel unglaublich schnell ohne Treibstoffverbrauch fliegen kann.',35000,7000,5000,10000,0,0,0,0,1000,600,0,150000,0,45,4100,550,460,1,1,5,'transport,fetch,position,attack,flight,support,alliance','0.50',0,50,20,1,0,1,0,2,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','57.000',0,0),(50,'ASTERIO Sammler',1,'Das effizienteste Asteroidensammlerschiff in ganz Andromeda.','Auch wenn die Fornax Asteroidensammler mehr oder weniger erfolglos waren, hiess das noch lange nicht, dass das Konzept unbrauchbar war. Es wurde weiterentwickelt und so entstand der Asterio Sammler, welcher zwar eine kleinere Ladefläche als der Fornax aufweist, dafür aber wesentlich schneller unterwegs\r\nist. Bis jetzt ist es das effizienteste Asteroidensammelschiff in ganz Andromeda.\r\nDieses Schiff ist auch dazu geeignet, um Trümmerfelder anzusteuern und diese zu recyclen.',3200,1200,2500,2000,0,0,0,4,1,1,0,11000,0,1,4500,3230,560,1,1,3,'transport,collectdebris,position,attack,collectmetal,flight,support,alliance','0.50',0,50,2,0,0,1,0,2,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','8.900',0,0),(51,'HAARP Spionagesonde',1,'Diese Sonde ist die Weiterentwicklung der ZAVIJAH Spionagesonde.','Diese Sonde ist die Weiterentwicklung der ZAVIJAH Spionagesonde. Sie ist enorm schnell und gut geeignet zum Ausräumen verteidigungsloser Planeten sowie zum Ausspionieren weit entfernter Galaxien. ',1000,1000,1000,500,0,0,0,1,1,1,0,800,0,0,60000,5,4,1,1,4,'position,attack,spy,flight,support,alliance','0.50',0,0,1,0,0,1,0,2,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','3.500',0,0),(52,'AURORA Sonde',1,'Diese Sonde wurde entwickelt, da viele schnelle Schiffe nicht genug Treibstoff für lange Strecken mitnehmen konnten.','Diese Sonde wurde entwickelt, da viele schnelle Schiffe nicht genug Treibstoff für lange Strecken mitnehmen konnten. Deshalb hat diese sehr schwache und teure Sonde einen riesigen Laderaum, in dem sie den Treibstoff für die mitfliegenden Schiffe bereit halten kann. \r\n\r\nSie wird bei einem Kampf sehr schnell zerstört, da sie praktisch nur aus dünnbeschichteten Tanks & den notwendigen Antrieb besteht.',20000,18000,10000,9000,0,0,0,25,10,5,0,35000,0,0,15000,20,15,1,1,2,'transport,position,attack,flight,support,alliance','0.50',0,1,1,1,0,1,0,2,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','57.000',0,0),(53,'IMPERIALER Kreuzer',1,'Dies ist eines der grössten Schiffe in Andromeda.','Dies ist eines der grössten Schiffe in Andromeda. Es ist enorm stark gepanzert, hat allerdings einen relativ schwachen Schild. Seine Waffen sind aber nicht zu verachten. Es ist das grösste Schiff mit einem Sonnensegel zur Antriebsunterstützung.',750000,600000,415000,365000,0,0,0,45,790,560,0,230000,0,35,5800,860,420,1,1,6,'transport,position,attack,flight,support,alliance','0.50',0,505000,85000,335000,0,1,0,1,1,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','2130.000',0,0),(54,'Alien-Jäger',1,'Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.','Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.',99999999,99999999,99999999,99999999,99999999,0,0,1,0,0,0,1000,0,1,5000,0,0,0,0,0,'flight','0.50',0,500,700,50,0,1,0,5,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','499999.995',0,0),(55,'Alien-Kampschiff',1,'Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.','Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.',99999999,99999999,99999999,99999999,99999999,0,0,1,0,0,0,5000,0,1,5000,0,0,0,0,0,'flight','0.50',0,5000,7000,500,0,1,0,5,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','499999.995',0,0),(56,'Alien-Mutterschiff',1,'Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.','Niemand weis genaueres über diese Alien-Raumschiffe, nur dass sie extrem gefährlich sind.',99999999,99999999,99999999,99999999,99999999,0,0,1,0,0,0,10000,0,1,5000,0,0,0,0,0,'flight','0.50',0,50000,70000,5000,0,1,0,5,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','499999.995',0,0),(57,'ANDROMEDA Mysticum',1,'Ein einmaliges Schiff mit speziellen Fähigkeiten.','Ein einmaliges Schiff mit speziellen Fähigkeiten.',58000,67000,43600,37500,0,0,0,75,400,400,0,50000,0,10,5000,950,1350,1,1,0,'position,flight','0.50',0,50000,50000,0,0,1,0,3,0,1,1,0,350,'2.00','0.03','0.03','0.03','0.00','0.03','0.00','0.07','0.00','0.00','0.00','0.00','0.00','0.00','206.100',0,0),(59,'MINBARI Mysticum',1,'Das Spezialschiff für die Minbari.','Das Spezialschiff für die Minbari.',700000,550000,390000,480000,120000,0,0,63,850,360,0,65000,0,33,4100,1300,710,1,1,0,'position,flight','0.50',0,70000,70000,0,5,1,0,3,0,1,1,0,180,'1.70','0.01','0.01','0.02','0.00','0.00','0.00','0.05','0.05','0.00','0.00','0.00','0.00','0.00','2240.000',0,0),(60,'ANDORIA Mysticum',1,'Das Spezialschiff für die Andorianer.','Das Spezialschiff für die Andorianer.',670000,500000,350000,480000,0,0,0,60,600,520,0,68000,0,30,5300,600,1020,1,1,0,'position,flight','0.50',0,110000,86000,0,2,1,0,3,0,1,1,0,180,'1.70','0.02','0.02','0.02','0.00','0.00','0.10','0.00','0.00','0.14','0.00','0.00','0.00','0.00','2000.000',0,0),(61,'CARDASSIA Mysticum',1,'Das Spezialschiff für die Cardassianer.','Das Spezialschiff für die Cardassianer.',750000,530000,320000,450000,250000,0,0,75,450,300,0,65000,0,55,5300,840,1150,1,1,0,'position,flight','0.50',0,1500000,50000,0,9,1,0,3,0,1,1,0,180,'1.70','0.01','0.01','0.01','0.07','0.00','0.00','0.05','0.00','0.00','0.00','0.00','0.00','0.00','2300.000',0,0),(62,'CENTAURI Mysticum',1,'Das Spezialschiff für die Centauri.','Das Spezialschiff für die Centauri.',850000,630000,360000,450000,60000,0,0,65,300,360,0,120000,0,45,3500,1080,780,1,1,0,'position,flight','0.50',0,65000,25000,0,8,1,0,3,0,1,1,0,180,'1.70','0.02','0.01','0.01','0.00','0.00','0.00','0.05','0.00','0.00','0.03','0.00','0.00','0.00','2350.000',0,0),(63,'FERENGI Mysticum',1,'Das Spezialschiff für die Ferengi.','Das Spezialschiff für die Ferengi.',930000,400000,360000,520000,0,0,0,78,600,450,0,120000,0,50,5000,870,980,1,1,0,'position,flight','0.50',0,200000,0,0,6,1,0,3,0,1,1,0,180,'1.70','0.01','0.03','0.03','0.00','0.05','0.00','0.05','0.00','0.00','0.00','0.00','0.00','0.00','2210.000',0,0),(64,'ORION Mysticum',1,'Das Spezialschiff für den Orioner.','Das Spezialschiff für den Orioner.',500000,450000,680000,460000,50000,0,0,80,500,400,0,175000,0,60,6000,850,1320,1,1,0,'position,flight','0.50',0,80000,110000,0,4,1,0,3,0,1,1,0,180,'1.70','0.02','0.01','0.01','0.00','0.00','0.20','0.05','0.00','0.00','0.00','0.00','0.00','0.00','2140.000',0,0),(65,'RIGELIA Mysticum',1,'Das Spezialschiff für die Rigelianer.','Das Spezialschiff für die Rigelianer.',450000,760000,390000,330000,100000,0,0,65,720,250,0,95000,0,52,4500,750,1120,1,1,0,'position,flight','0.50',0,75000,120000,0,3,1,0,3,0,1,1,0,180,'1.70','0.02','0.01','0.01','0.00','0.00','0.00','0.05','0.00','0.00','0.00','0.00','0.00','0.12','2030.000',0,0),(66,'TERRANIA Mysticum',1,'Das Spezialschiff für die Terraner.','Das Spezialschiff für die Terraner.',650000,420000,350000,530000,100000,0,0,75,650,760,0,120000,0,56,3800,1050,860,1,1,0,'position,flight','0.50',0,115000,85000,0,1,1,0,3,0,1,1,0,180,'1.70','0.02','0.02','0.01','0.00','0.00','0.00','0.05','0.00','0.00','0.00','0.10','0.00','0.00','2050.000',0,0),(67,'VORGONIA Mysticum',1,'Das Spezialschiff für die Voronen.','Das Spezialschiff für die Voronen.',550000,550000,550000,550000,200000,0,0,80,850,490,0,130000,0,60,5200,1230,750,1,1,0,'position,flight','0.50',0,100000,100000,0,7,1,0,3,0,1,1,0,180,'1.70','0.01','0.02','0.02','0.00','0.00','0.00','0.05','0.00','0.00','0.00','0.00','0.10','0.00','2400.000',0,0),(68,'ENERGIJA Solarsatellit',1,'Ein Satellit, der im Orbit schwebt und durch Solarpanels Energie gewinnt, welche dann auf dem Planeten verwendet werden kann.','Da einige (neu entwickelte) Gebäude enorme Energiemengen verschlingen, wurde der Solarsatellit entwickelt. Diese Sonde wird im Orbit stationiert und erzeugt Energie mit Hilfe der Sonne. Die Energieausbeute pro Solarsatellit ist jedoch abhängig von der jeweiligen Planetentemperatur und der jeweiligen Entfernung zur Sonne.\r\nDie Sonden werden bei einem feindlichen Angriff abgeschossen.',300,1500,100,100,0,0,0,0,0,0,300,0,0,0,0,0,0,1,1,13,'flight','0.50',0,100,50,0,0,0,0,2,0,0,0,0,0,'0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','2.000',0,0),(69,'TEREBELLUM Analysator',1,'Diese kleine Sonde wurde dafür geschaffen, um Staub- und Gasvorkommen im All zu analysieren und festzustellen, ob sich deren Abbau lohnt.','Diese kleine Sonde wurde dafür geschaffen, um Staub- und Gasvorkommen im All zu analysieren und festzustellen, ob sich deren Abbau lohnt.',2000,4500,3000,3000,0,0,0,2,50,2,0,500,0,0,70000,10,1,1,1,1,'position,analyze,flight,support','0.50',0,100,200,0,0,1,0,2,0,0,0,0,0,'0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','12.500',0,0),(70,'LORIAL Transportschiff',1,'Dieser Transporter der Serrakin kann extrem viel transportieren und verbraucht wenig Sprit, ist dafür aber auch ziemlich langsam.','Dieser Transporter der Serrakin kann extrem viel transportieren und verbraucht wenig Sprit, ist dafür aber auch ziemlich langsam.',17000,11000,15000,7000,0,0,0,10,50,10,0,475000,0,1,800,600,500,1,1,0,'transport,fetch,position,attack,flight,support,alliance','0.50',0,200,500,50,10,1,0,4,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','50.000',0,0),(71,'AURIGA Explorer',1,'Dient zur Erkundung der unbekannten Weiten der Galaxie.','Dient zur Erkundung der unbekannten Weiten der Galaxie.',1000,800,0,0,0,0,0,1,5,0,0,100,0,0,1500,10,0,1,1,6,'position,explore,flight,support','0.50',0,50,20,0,0,1,0,2,0,0,0,0,0,'0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','1.800',0,0),(72,'SERRAKIN Mysticum',1,'Das Spezialschiff für die Serrakin','Das Spezialschiff für die Serrakin',800000,580000,350000,450000,150000,0,0,60,850,500,0,85000,0,45,5300,800,1000,1,1,0,'position','0.50',0,85000,95000,0,10,1,0,3,0,1,1,0,180,'1.70','0.00','0.03','0.00','0.05','0.05','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','2330.000',0,0),(73,'SUPRANALIS Jäger',1,'Weiterentwicklung des ANTARES Jäger.','Parallel zu den Antares Jägern wurde der STARLIGHT Jäger entwickelt, welcher besser gepanzert war und auch die bessere Bewaffnung aufwies. Er nutzte auch einen neuartigen Antrieb, welcher aber noch nicht ganz serienreif war, da er andauernd ausfiel, und selten wie geplant lief. Nach einigen Untersuchungen fand man heraus, dass dies daran lag, dass beim Bau des Motors billiges Material verwendet wurde. Das stellte den viel gelobten Jäger in ein anderes Licht, aber andererseits erwies er sich in Raumschlachten als zuverlässiger Mitstreiter.',24500,17000,10500,12000,0,0,0,2,5,6,0,800,0,1,9750,22,20,0,0,2,'transport,position,attack,flight,support,alliance','0.50',0,21000,11000,19000,0,1,0,6,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','64.000',1,3000),(74,'SUPRANALIS Bomber',1,'Dieses Raumschiff ist sehr effektiv gegen gegnerische Verteidigungsanlagen.','Trotz allen Erfolgen, die die Hadar Schlachtschiffe bei der Zerstörung gegnerischer Flotten und Verteidigung erzielten, war man damit noch nicht zufrieden. Deshalb konstruierte man ein neues, bis an die Zähne bewaffnetes Schiff, den Pollux Bomber. Nachdem man das Schiff mit Waffen beladen hatte, erwies es sich, dass deshalb die Angriffsgeschwindigkeit eingeschränkt wurde. Wegen diesem Nachteil konnte der Bomber sich in grossen Flotten nicht etablieren, er ist aber trotzdem in allem eine nicht zu unterschätzende Waffe, welche grosse Zerstörung anrichten kann.',48500,105000,42500,57500,0,0,0,550,800,700,0,2000,0,2,2400,300,60,0,0,5,'transport,position,attack,flight,support,alliance','0.50',0,26000,5000,180000,0,1,0,6,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','253.500',3,10000),(76,'SUPRANALIS Dreadnought',1,'Dieses Schiff ist eine riesige fliegende Festung. ','Aus der Erfahrungen, die man mit den Hadar und den Pollux gewonnen hatte, wurde ein neues Superschiff kreiert, der Rigel Dreadnought. Optimierungen in der Herstellung und bei den Antrieben verliehen dem Schiff eine aussergewöhnliche Kampfkraft, Effizienz und Geschwindigkeit zu erstaunlich niedrigen Preisen. Zusätzlich erhöhte man die Transportkapazität, so dass die Rigel eigenständig praktisch aus dem Nichts heraus Raubzüge unternehmen können, ohne sich mit langsamen Transportern zu belasten. ',16750000,14875000,3750000,8750000,0,0,0,2800,23500,34000,0,600000,0,560,9600,310,200,0,0,9,'transport,position,attack,flight,support,alliance','0.50',0,10000000,13500000,17500000,0,1,0,6,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','44125.000',5,17000),(77,'SUPRANALIS Kampfstern',1,'Dieses Schiff ist das mächtigste Schiff der Galaxien.','Ein verrückter Wissenschaftler war von der Idee besessen, ein Kampfschiff zu bauen, welches so gross wie ein ganzer Trabant wäre. Er wurde so lange ausgelacht, bis er einen anderen Verrückten traf, der zufällig nebenberuflich Imperator war und der ihn unterstützte. Danach wurde dieser Wissenschaftler allgemein als Genius bekannt, welcher die ultimative Waffe erschaffen hatte: den Andromeda Kampfstern. Seine Waffen und Schilder sind bis heute noch unübertroffen!\r\nDer einzige Nachteil dieses monströsen Kampfschiffes ist nur, dass es wegen seiner Masse lange Start- und Landezeiten hat, und eine zahlreiche Besatzung benötigt wird.',100000000,50000000,60000000,60000000,0,0,0,8000,80000,40000,0,6000000,0,990,20000,1750,1250,0,0,13,'transport,position,attack,flight,support,alliance','0.50',0,85000000,90000000,95000000,0,1,0,6,0,0,0,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','270000.000',7,27000),(78,'SUPRANALIS Ultra',1,'Dieses Schiff ist das mächtigste Schiff der Galaxien (nun aber wirklich ^^)','Der Andromeda Kampfstern galt lange als DAS Kampfschiff schlechthin und nicht wenige behaupten, dass es nicht möglich sei, seine Grösse und Stärke zu übetreffen, doch genau dieses Ziel hatten diverse Imperatoren einer mächtigen Allianz Namens \\\"Supranalis Ultra\\\".\r\nNach vielen Jahren, unzähligen Arbeitsstunden und diversen Todesopfern war der Prototyp dieses Superschiffs fertig.\r\nEtwas noch nie Dagewesenes wurde erschaffen um die Kontrolle eines ganzen Universums an sich zu reissen...',1000000000,500000000,600000000,600000000,0,0,0,10000,100000,100000,0,50000000,0,100000,20000,5000,3000,0,0,14,'transport,position,attack,flight,support,alliance','0.50',0,10000000,10000000,10000000,0,1,0,6,0,0,1,0,0,'1.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','2700000.000',10,150000),(79,'SCORPIUS ZIBAL Trägerschiff',1,'Transportiert mobile Verteidigungsanlagen.','Transportiert mobile Verteidigungsanlagen.',3900,3100,2100,1500,0,0,0,20,10,10,0,1000,0,0,10000,60,60,1,0,14,'position','0.50',0,1000,50,0,10,1,0,2,0,0,0,0,0,'0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','10.600',0,0),(80,'SCORPIUS SPICA Trägerschiff',1,'Transportiert mobile Verteidigungsanlagen.','Transportiert mobile Verteidigungsanlagen.',800,475,0,425,0,0,0,20,10,10,0,1000,0,0,10000,60,60,1,0,14,'position','0.50',0,1000,50,0,10,1,0,2,0,0,0,0,0,'0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','1.700',0,0),(81,'SCORPIUS POLARIS Trägerschiff',1,'Transportiert mobile Verteidigungsanlagen.','Transportiert mobile Verteidigungsanlagen.',1000,700,300,500,0,0,0,20,10,10,0,1000,0,0,10000,60,60,1,0,14,'position','0.50',0,1000,50,0,10,1,0,2,0,0,0,0,0,'0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','2.500',0,0),(82,'SCORPIUS PHOENIX Trägerschiff',1,'Transportiert mobile Verteidigungsanlagen.','Transportiert mobile Verteidigungsanlagen.',6500,3500,3000,1900,0,0,0,20,10,10,0,1000,0,0,10000,60,60,1,0,14,'position','0.50',0,1000,50,0,10,1,0,2,0,0,0,0,0,'0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','0.00','14.900',0,0);
/*!40000 ALTER TABLE `ships` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sol_types`
--

DROP TABLE IF EXISTS `sol_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sol_types` (
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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sol_types`
--

LOCK TABLES `sol_types` WRITE;
/*!40000 ALTER TABLE `sol_types` DISABLE KEYS */;
INSERT INTO `sol_types` VALUES (1,'Gelber Stern','1.30','1.30','1.10','1.00','0.90','1.10','1.30','1.10','Die gelben Sterne gehören zu der Kategorie \"mittelgrosse Sterne\". Das Alter solcher Gelben Sterne kann extrem variieren; sie können zwischen einigen Jahrtausenden bis hin zu Jahrmillionen alt sein.<br>Generell gilt jedoch, dass auf Gelben Sternen gemässigte und gute Lebensbedingungen herrschen. Ausserdem ist die Geodiversität relativ gross, was den Abbau von Metallen genauso fördert wie die Entwicklung von Chemikalien. Dank dem mineralhaltigen Boden ist sogar ein gewisser Kristallabbau möglich.<br>Einzig der Nahrung scheint der mineralienhaltige Boden nicht ganz so gut zu bekommen...','1.10',1),(2,'Blauer Stern','1.00','1.30','1.00','1.00','1.00','0.90','0.80','1.00','Diese Art von Sternen erscheint dem Beobachter meist blau; das liegt daran, dass im Innern des Sterns eine gewaltige Hitze herrscht, vergleichbar mit der blauen Färbung einer Flamme beim Schweissen.<br>Durch die gigantischen Hitzewellen sind die Lebensbedingungen im Umfeld Blauer Sterne für die verschiedenen Völker nicht optimal. Einige jedoch haben sich inzwischen dem heissen Klima anpassen können und nutzen genau dieses zur Verschmelzung von Kristallinem Material, um qualitativ hochstehende Kristallite herzustellen.<br>Bisher wollte es jedoch noch keinem Volk so richtig gelingen, aus dem heissen Klima einen weiteren Nutzen in Sachen Industrie zu ziehen. Im Gegenteil, meist ist die Stromproduktion und das Wachstum der Bevölkerung tiefer als in anderen Sternsystemen.','1.10',1),(3,'Roter Stern','0.90','1.20','1.00','1.20','1.10','0.80','1.30','1.00','Rote Sterne sind eher klein und schon recht alt. Dadurch ist ihre Energieaustrahlung nicht mehr ganz so gross, was wiederum eine gute Klimabedingung für die meisten Völker ist. Deshalb verwundert es nicht, dass man in vielen Roten Sternen alle möglichen Völker antrifft, welche dort seit ewigen Zeiten eine neue Heimat gefunden haben.<br>Ebenfalls positiv wirkt sich die gemässigte Energieabgabe der Roten Sterne auf verschiedenste Produktionen aus, was dann wiederum den dort wohnhaften Völkern zugute kommt.','1.00',1),(4,'Weisser Stern','0.90','1.10','1.00','1.60','1.00','1.60','0.95','1.00','Weisse Sterne sind stark energiehaltige Sterne, deren Energieausstösse für das extrem helle Licht verantwortlich sind.<br>Dadurch lässt sich in der Nähe von Weissen Sternen mit relativ wenig Aufwand Tritium und Strom herstellen. Ebenfalls positiv wirkt sich die Energiestrahlung auf die Kristallisation aus, jedoch nicht auf die Menschen. Jene ertragen die gewaltigen Energiemengen nicht zu lange, weshalb der Bevölkerungswachstum in Weissen Sternen meist kleiner als in anderen Sternen ist.','1.00',1),(5,'Violetter Stern','1.00','0.90','1.00','0.90','1.00','1.00','1.05','0.90','Violette Sterne sind sehr junge Sterne, die sich meistens innerhalb von Gaswolken befinden. Die für den Betrachter violette Färbung der Sonne entsteht durch die vielen verschiedenen Nebel, welche das Sonnenlicht jeweils verschieden brechen.<br>Weil die Sterne noch ziemlich jung sind, ist noch nicht viel über sie bekannt; die Beobachtungen der verschiedenen Völker haben erst begonnen.','0.90',1),(6,'Schwarzer Stern','0.90','1.00','1.20','1.10','1.00','0.80','0.60','0.85','Praktisch keiner weiss etwas über schwarze Sterne, da sie erst vor kurzem durch eine neuartige Objektivtechnologie sichtbar gemacht werden konnten.<br>Erst einzelne überragende Forscher haben angefangen, sich an diese Mysterien im All heranzuwagen.<br>Ungenannte Quellen munkeln jedoch, dass die schwarze Färbung durch aktive schwarze Löcher auftritt, was die Völker natürlich davor abschreckt, mehr über die Schwarzen Sterne rauszufinden.','1.00',1),(7,'Grüner Stern','1.40','1.10','1.00','1.00','1.20','0.90','0.90','1.10','Grüne Sterne wirken auf den ersten Blick giftig - und so ganz unrecht ist das auch nicht. Durch Gase aus dem Inneren der Sterne werden immer wieder Epidemien ausgelöst, die Teile der Bewohner von Grünen Sternen dahinraffen.<br>Entgegen den unwirtlichen Lebensbedingungen wirken sich die Gase und die Geostruktur positiv auf die Steingefüge der Sterne aus.<br>Es verwundert daher nicht, dass in Grünen Sternen oftmals Raffinerien, Erzwerke und Metallverarbeitungsanlagen anzutreffen sind.','1.00',1);
/*!40000 ALTER TABLE `sol_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `space`
--

DROP TABLE IF EXISTS `space`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `space` (
  `id` int(10) unsigned NOT NULL,
  `lastvisited` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `space`
--

LOCK TABLES `space` WRITE;
/*!40000 ALTER TABLE `space` DISABLE KEYS */;
/*!40000 ALTER TABLE `space` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `specialists`
--

DROP TABLE IF EXISTS `specialists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specialists` (
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
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `specialists`
--

LOCK TABLES `specialists` WRITE;
/*!40000 ALTER TABLE `specialists` DISABLE KEYS */;
INSERT INTO `specialists` VALUES (1,'Admiral','Der Flottenadmiral ist ein kriegserfahrener Veteran und meisterhafter Stratege. Auch im heissesten Gefecht behält er im Gefechtsleitstand den Überblick und hält Kontakt mit den ihm unterstellten Admirälen. Ein weiser Herrscher kann sich auf seine Unterstützung im Kampf absolut verlassen und somit mehr Raumflotten gleichzeitig und schneller ins Gefecht führen.',1,100000,100000,100000,100000,100000,100000,7,'1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.10',3,'1.00',0,0,'1.00','1.00'),(2,'Ingenieur','Der Ingenieur ist ein Spezialist für besonders durchdachte und stabile Verteidigungssysteme. Durch seine Mithilfe können Verteidigungsanlagen schneller und günstiger produziert werden. Nach einem Kampf kann er auch schwer beschädigte Anlagen wieder reparieren.',1,100000,100000,100000,100000,100000,100000,7,'1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','0.80','1.00','1.00','0.90','1.00','1.00','1.00',0,'1.40',0,0,'1.00','1.00'),(3,'Geologe','Der Geologe ist ein anerkannter Experte in Astromineralogie und -kristallographie. Mithilfe seines Teams aus Metallurgen und Chemieingenieuren unterstützt er interplanetarische Regierungen bei der Erschließung neuer Rohstoffquellen und der Optimierung ihrer Raffination.',1,100000,100000,100000,100000,100000,100000,7,'1.10','1.10','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00',0,'1.00',0,0,'1.00','1.00'),(4,'Professor','Die Gilde der Technokraten sind geniale Wissenschaftler, und man findet sie immer dort, wo die Grenzen des technisch Machbaren gesprengt werden. Durch seine reine Anwesenheit inspiriert er die Forscher des Imperiums.',1,100000,100000,100000,100000,100000,100000,7,'1.00','1.00','1.00','1.00','1.00','1.00','1.00','0.80','1.00','1.00','1.00','1.00','1.00','1.00','0.90','1.00',0,'1.00',0,0,'1.00','1.00'),(5,'Biologe','Der Biologe steigert durch seine gentechnischen Experimente den Ertrag deiner Gewächshäuser und sorgt für ein rascheres Bevölkerungswachstum.',1,100000,100000,100000,100000,100000,100000,7,'1.00','1.00','1.00','1.00','1.30','1.00','1.30','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00',0,'1.00',0,0,'1.00','1.00'),(6,'Spion','Der Spion ist ein Meister der Tarnung und Informationsbeschaffung. Durch seine Tricks ist es möglich, mehr Informationen über den Gegner herauszufinden und die eigenen Schiffe besser zu tarnen.',1,100000,100000,100000,100000,100000,100000,7,'1.00','1.00','1.00','1.00','1.00','1.00','1.00','0.90','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00',0,'1.00',3,2,'1.00','1.00'),(7,'Meisterhändler','Durch das Verhandlungsgeschick des Meisterhändlers fallen im Markt keine zusätzlichen Kosten an, er hat weniger Handelsbeschränkungen und seine Handelsschiffe fliegen schneller als alle anderen.',1,100000,100000,100000,100000,100000,100000,7,'1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00',0,'1.00',0,0,'6.00','0.00'),(8,'Chemiker','Der Chemiker kennt sich mit der Herstellung synthetischer und radioaktiver Rohstoffe aus. Durch sein Wissen im Bereich chemischer Herstellungsverfahren kann die Produktion von PVC und Tritium merklich gesteigert werden.',1,100000,100000,100000,100000,100000,100000,7,'1.00','1.00','1.15','1.10','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00',0,'1.00',0,0,'1.00','1.00'),(0,'Nulldummy','Nicht löschen',0,0,0,0,0,0,0,14,'1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00','1.00',0,'1.00',0,0,'1.00','1.00');
/*!40000 ALTER TABLE `specialists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stars`
--

DROP TABLE IF EXISTS `stars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stars` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(30) NOT NULL,
  `type_id` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stars`
--

LOCK TABLES `stars` WRITE;
/*!40000 ALTER TABLE `stars` DISABLE KEYS */;
/*!40000 ALTER TABLE `stars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tech_points`
--

DROP TABLE IF EXISTS `tech_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tech_points` (
  `bp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `bp_tech_id` int(10) unsigned NOT NULL,
  `bp_level` tinyint(3) unsigned NOT NULL,
  `bp_points` decimal(20,3) unsigned NOT NULL,
  PRIMARY KEY (`bp_id`),
  KEY `bp_tech_id` (`bp_tech_id`),
  KEY `bp_level` (`bp_level`),
  KEY `bp_points` (`bp_points`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tech_points`
--

LOCK TABLES `tech_points` WRITE;
/*!40000 ALTER TABLE `tech_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `tech_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tech_requirements`
--

DROP TABLE IF EXISTS `tech_requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tech_requirements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `obj_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_building_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_tech_id` int(10) unsigned NOT NULL DEFAULT '0',
  `req_level` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `obj_id` (`obj_id`,`req_building_id`,`req_tech_id`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tech_requirements`
--

LOCK TABLES `tech_requirements` WRITE;
/*!40000 ALTER TABLE `tech_requirements` DISABLE KEYS */;
INSERT INTO `tech_requirements` VALUES (1,3,8,0,3),(2,4,8,0,4),(3,4,9,0,2),(4,5,8,0,5),(5,5,9,0,4),(6,6,8,0,8),(7,6,11,0,6),(8,6,9,0,6),(9,7,8,0,4),(10,8,8,0,3),(11,9,8,0,4),(12,10,8,0,4),(17,12,8,0,7),(18,12,0,3,5),(19,13,11,0,4),(20,13,0,5,2),(21,14,0,4,6),(22,14,8,0,6),(23,11,8,0,5),(24,11,0,7,6),(25,15,8,0,8),(26,17,8,0,8),(27,20,9,0,5),(28,20,8,0,4),(29,18,8,0,8),(30,19,8,0,8),(31,16,8,0,5),(32,21,8,0,6),(33,21,9,0,5),(34,21,0,3,6),(35,22,8,0,10),(36,22,0,6,9),(37,22,0,3,10),(38,22,0,10,11),(39,23,8,0,12),(40,23,7,0,15),(41,24,8,0,10),(42,24,0,3,9),(44,24,0,4,10),(45,24,0,14,10),(46,25,0,3,5),(47,25,13,0,6);
/*!40000 ALTER TABLE `tech_requirements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tech_types`
--

DROP TABLE IF EXISTS `tech_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tech_types` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL,
  `type_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `type_color` char(7) NOT NULL DEFAULT '#ffffff',
  PRIMARY KEY (`type_id`),
  KEY `type_name` (`type_name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tech_types`
--

LOCK TABLES `tech_types` WRITE;
/*!40000 ALTER TABLE `tech_types` DISABLE KEYS */;
INSERT INTO `tech_types` VALUES (1,'Antriebstechniken',1,'#ffffff'),(2,'Kriegstechnologien',2,'#ffffff'),(4,'Allgemeine Technologien',0,'#ffffff'),(3,'Hi - Technologien',3,'#ffffff');
/*!40000 ALTER TABLE `tech_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `techlist`
--

DROP TABLE IF EXISTS `techlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `techlist` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `techlist`
--

LOCK TABLES `techlist` WRITE;
/*!40000 ALTER TABLE `techlist` DISABLE KEYS */;
/*!40000 ALTER TABLE `techlist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `technologies`
--

DROP TABLE IF EXISTS `technologies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `technologies` (
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
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `technologies`
--

LOCK TABLES `technologies` WRITE;
/*!40000 ALTER TABLE `technologies` DISABLE KEYS */;
INSERT INTO `technologies` VALUES (7,'Spionagetechnik',4,'Je höher die Spionagetechnik ist, desto mehr können Spionagesonden über gegnerische Planeten herausfinden.','Spionage ist die Auskundschaftung und Erlangung von fremden, wohlgehüteten Geheimnissen oder Wissen von fremden Planeten. Die erlangten Informationen werden dann in den eigenen wirtschaftlichen, politischen oder militärischen Machtbereich eingeführt, ohne dass eine eigenständige Erforschung erfolgen müsste. Annähernd sämtliche Imperien bedienen sich der Spionage oder \\\"nachrichtendienstlicher Mittel\\\", um andere Völker (unabhängig der feindseligen oder freundlichen Einstellung zum eigenen Volk) auszuspionieren.\r\nEine weitere nützliche Eigenschaft der Spionagetechnik ist das Enttarnen von feindlichen Angriffen, welche mit höherer Spionagetechnik schneller vonstatten geht.',750,370,150,520,0,0,'1.50',50,1,1,1),(8,'Waffentechnik',2,'Jede Ausbaustufe erhöht die Stärke der Waffen bei Raumschiffen und Verteidigungsanlagen.','Durch die Erforschung der Waffentechnik können neue und stärkere Waffen für Raumschiffe und Verteidigungsanlagen gebaut werden.\r\nPro Ausbaustufe erhöht sich die Angriffskraft deiner Schiffe und Verteidigungsanlagen um 10%.',250,800,550,200,0,0,'1.80',50,1,1,1),(4,'Wasserstoffantrieb',1,'Einfacher Antrieb für Raumschiffe','Ein Wasserstoffantrieb nutzt Wasserstoff als Treibstoff. Dieser wird durch Elektrolyse von Wasser, Reformation von Methanol oder durch Dampfreformation von Erdgas gewonnen.',500,300,250,50,0,0,'1.50',50,1,0,1),(5,'Ionenantrieb',1,'Hoch entwickelter Antrieb für Spezialschiffe. Er ist weniger schnell als der Wasserstoffantrieb, dafür kostensparend.','Ein Ionenantrieb ist ein Antrieb für Raumfahrzeuge, bei dem die Abstossung von einem Ionenstrahl zur Fortbewegung genutzt wird. Es werden auch je nach Energiequelle die Begriffe \\\"solar-elektrischer Antrieb\\\" bzw. \\\"Solar Electric Propulsion\\\" (SEP) und \\\"nuklear-elektrischer Antrieb\\\" bzw. \\\"Nuclear Electric Propulsion\\\" (NEP) verwendet.\r\nDer Ionenstrahl besteht aus einem elektrisch geladenen Gas (z.B. Xenon). Erzeugt wird der Ionenstrahl durch ionisierte Gasteilchen, die in einem elektrischen Feld oder mittels einer Kombination eines elektrischen Feldes und eines Magnetfeldes unter Ausnutzung des Hall-Effektes beschleunigt und dann in Form eines Strahls ausgestossen werden. Die Energie zur Erzeugung der Felder wird üblicherweise mit Hilfe von Solarzellen gewonnen. Als Treibstoff des Ionenantriebs dient sowohl das Gas als auch die zusätzlich benötigte elektrische Energie.\r\nDer Vorteil des Ionenantriebs gegenüber dem chemischen Antrieb liegt darin, dass er weniger Treibstoff verbraucht, weil die Geschwindigkeit der austretenden Teilchen wesentlich grösser ist.',1000,1500,800,300,0,0,'1.50',50,1,1,1),(6,'Hyperraumantrieb',1,'Sehr schneller Antrieb für grosse Schiffe, der den Hyperraum als Transportmedium benutzt.','Der Hyperraumantrieb schafft eine technisch hervorgerufene Abkürzung zwischen weit entfernten Punkten in der Raumzeit. Die Idee ist dabei folgende: Um den Weg vom Nordpol zum Südpol abzukürzen, reise man quer durch die Erde, anstatt entlang der Oberfläche. Der Weg durch die Erde (in die dritte Dimension) ist kürzer als der Weg auf der (zweidimensionalen) Erdoberfläche. Genauso könnte man sich vorstellen, dass unsere Raumzeit auch in einen höherdimensionalen Hyperraum eingebettet ist (wie die Erdoberfläche in den Raum), und man daher durch den Hyperraum abkürzen könnte. Auch hier würde man (im Hyperraum) nicht schneller als Lichtgeschwindigkeit fliegen müssen, um schneller als das Licht im Normalraum am Ziel anzukommen.\r\nDiese Antriebstechnologie wird heute für fast jedes grosse und träge Schiff eingesetzt.',4000,6000,1500,5500,0,0,'1.80',50,1,3,1),(3,'Energietechnik',4,'Diese Technologie dient zur Erforschung neuer Arten der Energiegewinnung.','Durch die Unterstützung der Energietechnik können neue Arten der Energiegewinnung erforscht werden.',300,250,30,50,0,0,'1.50',50,1,0,1),(9,'Panzerung',2,'Jede Ausbaustufe erhöht die Stärke der Panzerung bei Raumschiffen und Verteidigungsanlagen.','Jedes Schiff und jede Verteidigungsanlage besitzen eine Panzerung zum Schutz vor feindlichen Angriffen. Pro Ausbaustufe erhöht diese Technologie die Panzerung, auch genannt Struktur, um 10%.',1000,150,320,270,0,0,'1.80',50,1,2,1),(10,'Schutzschilder',2,'Jede Ausbaustufe erhöht die Stärke der Schutzschilder bei Raumschiffen und Verteidigungsanlagen.','Ein Schutzschild schützt deine Raumschiffe und Verteidigungsanlagen vor feindlichem Beschuss.\r\nPro Ausbaustufe erhöht sich die Effizienz von den Schutzschildern um 10%.',290,330,250,950,0,0,'1.80',50,1,3,1),(11,'Tarntechnik',2,'Durch eine hohe Tarntechnik können deine Flotten eine gewisse Zeit vor dem Gegner verborgen bleiben.','Die Kriegsära hat begonnen; die Völker erforschen Technologien, mit welchen sie dem Gegner in einem allfälligen Kampf überlegen sind. Die Tarntechnik ist eigentlich schon eine uralte Waffe, welche den Überraschungseffekt ausnutzt, um so eine bessere Ausgangsposition zu haben; doch erst jetzt ist es wirklich möglich, seine Schiffe von der gegnerischen Flottenkontrolle zu verstecken.\r\nJe höher diese Technologie erforscht ist, desto länger bleiben die Schiffe für den Gegner unentdeckt.',1500,750,250,800,0,0,'1.60',50,1,4,1),(12,'Recyclingtechnologie',4,'Ermöglicht eine effiziente Wiederverwertung von alten Verteidigungsanlagen und Schiffen.','Lange Zeit hatte man eine Technik gesucht, welche verbaute Rohstoffe wieder verwerten kann. Nach jahrelanger Forschung wurde ein Verfahren entwickelt, das Schiffe und Verteidigungsanlagen recyceln kann. Jedoch ist diese Technologie in der Anfangsphase noch sehr ineffizient.\r\nDies kann aber mit der Weiterentwicklung ein wenig eingedämpft werden. Man weiss jedoch, dass die Materialien nie zu 100% recycelt werden können.',12000,20000,2000,8000,0,0,'1.90',50,1,2,1),(13,'Rettungskapseln',2,'Je höher die Rettungskapseln entwickelt sind, desto mehr Piloten können sich retten, wenn ihr Schiff bei einem Kampf zerstört wird. ','Je höher die Rettungskapseln entwickelt sind, desto mehr Piloten können sich retten, wenn ihr Schiff bei einem Kampf zerstört wird.\r\nEinige Schiffe können nur gebaut werden, wenn gute Rettungskapseln an Bord sind.\r\nUm Grosse Schiffe zu bauen, muss man die Rettungskapseln entwickelt haben.',12000,2000,3000,8000,2000,0,'1.90',50,0,5,1),(14,'Kraftstoffantrieb',1,'Verbesserter Wasserstoffantrieb, der mit einer Mischung aus Tritium und Asteroidenteilchen arbeitet. ','Verbesserter Wasserstoffantrieb, der mit einer Mischung aus Tritium und Asteroidenteilchen arbeitet. Dieser Antrieb ermöglicht es grösseren Schiffen, sich schneller fortzubewegen.',25500,7752,19347,10474,0,0,'1.30',50,1,2,1),(15,'Bombentechnik',3,'Mit Hilfe dieser Technik wird die Effektivität von Bombenangriffen gesteigert.','Längst hat man rausgefunden, dass das alleinige Zerstören von gegnerischen Flotten nicht mehr unbedingt den gewünschten Effekt hat.\r\nForscher haben aus diesem Grund eine neuartige Waffe entwickelt, mit der es möglich ist, fremde Gebäude zu bombardieren und so den Gegner wieder ins industrielle Mittelalter zu befördern.\r\nDiese Methode der Kriegsführung ist aber noch sehr jung, und deshalb ist die Chance auf eine erfolgreiche Bombardierung noch nicht allzu hoch.\r\nDurch die Erforschung der Bombentechnik wird diese aber deutlich gesteigert.',13000,26000,8000,13000,0,0,'1.75',50,1,0,1),(16,'Rassentechnik',4,'Mit der Rassentechnologie kann jede Rasse ihre rassenspezifischen Objekte bauen.','Mit der Rassentechnologie kann jede Rasse ihre rassenspezifischen Objekte bauen. Je höher sie erforscht ist, desto bessere und stärkere Rassenobjekte können gebaut werden.',1000,1000,1000,1000,1000,0,'1.50',50,1,3,1),(17,'EMP-Technik',3,'EMP-Bomben löst einen Elektromagnetischen Impuls aus, welcher elektrische Einrichtungen ausser Betrieb setzen kann.','Je länger je mehr schützen die Völker ihre Schiffe, indem sie sie ständig auf Erkundungsflüge schicken und so für den Gegner unerreichbar machen.\r\nEin Forschungsteam der Rigelianer hat es sich zur Aufgabe gemacht, diese Strategie zu vernichten.\r\nNach langen Forschungen haben sie ein Schiff entwickelt, mit dem es möglich ist, ganze Einrichtungen unbrauchbar zu machen.\r\nEin elektromagnetischer Impuls setzt alle elektronischen Geräte ausser Gefecht. Mit Hilfe dieser brillianten Waffe kann man nun dem Gegner beispielsweise die Flottenkontrolle lahm legen und den Schiffen den Abflug vom Planeten verweigern.\r\nJedoch ist auch diese Technologie noch nicht ganz ausgereift; so muss man sich beispielsweise mit einer kurzfristigen Deaktivierung zufrieden geben. Durch die Weiterentwicklung der EMP Technologie erhöht sich jedoch die Effizienz des Angriffes.',15000,15000,10000,15000,0,0,'1.70',50,1,1,1),(18,'Gifttechnik',3,'Diese Technologie wird für B- und C- Waffen gebraucht.','Die Gifttechnologie ist eine Massenvernichtungswaffe für Bewohner. Durch Zerstörung der Nervenbahnen und allmähliches Verringern der Wahrnehmungsfähigkeit lässt das Gift die Bewohner erkranken und kurze Zeit später an den Folgen sterben. Eine grausame, aber sehr effektive Waffe.\r\nDie Weiterentwicklung ermöglicht einen noch präziseren Einsatz der Gifte.',10000,10000,5000,20000,0,0,'1.50',50,1,2,1),(19,'Regenatechnik',3,'Neuartige Materialien ermöglichen gewissen Schiffen, sich während dem Kampf teilweise zu reparieren.','Das Heilen von Schiffen war schon immer sehr schwierig und wird sich wohl erst in Zukunft bei einer neuen Generation von Schiffen durchsetzen.\r\nBisher ist es nur einer einzigen Rasse gelungen, ein Schiff herzustellen, welches die eigene Flotte im Kampf heilen kann.\r\nEiner anderen Rasse ist es inzwischen gelungen, diesselbe Technologie für ihre Verteidigungsanlagen anzuwenden.\r\nDurch die Erhöhung der Technologie kann deren Effizienz gesteigert werden.',30000,17500,12500,17500,0,0,'1.90',50,1,3,1),(20,'Warpantrieb',1,'Die Warpgondeln eines Raumschiffes erzeugen ein Feld, welches den Raum krümmt und so das Schiff extrem beschleunigt.','Jede Rasse hat nach einer gewissen Zeit angefangen, ihre eigenen Schiffe zu bauen. Eine uns unbekannte Rasse hat den Warpantrieb entwickelt. Die uns bekannten Rassen konnten ihn jedoch nur bedingt anwenden. So sind ihre Schiffe nicht ganz so schnell wie sie eigentlich sein könnten. Die Warpgondeln eines Raumschiffes erzeugen ein Feld, welches den Raum krümmt und so das Schiff extrem beschleunigt.',6000,4500,2000,5500,0,0,'1.70',50,1,5,1),(21,'Solarantrieb',1,'Hinter dem unspektakulären Namen steckt eine sehr sparsame und interessante Technik. ','Hinter dem unspektakulären Namen steckt eine sehr sparsame und interessante Technik. Schiffe mit einem Solarantrieb können während dem Flug ihr Triebwerk ausschalten und ein riesiges Sonnensegel ausfahren, wodurch sie vom Sonnenwind mit unglaublicher Geschwindigkeit durchs All getragen werden.\r\nDie Erforschung ist nicht sehr billig, jedoch birgt es einen unschlagbaren Vorteil. Die Schiffe verbrauchen viel weniger Treibstoff für den Flug. Es soll sogar Schiffe geben, die allein mit den Solarzellen die benötigte Energie zum Flug aufbringen und so ohne Tritiumverbrauch fliegen können.',2100,1300,1100,300,0,0,'1.80',50,1,4,1),(22,'Wurmlochforschung',3,'Ermöglicht einer Flotte das Reisen durch Wurmlöcher. Dadurch wird die Flugzeit einer Flotte enorm verkürzt.','Wurmlöcher sind topologische Konstrukte, die weit voneinander entfernt liegende Bereiche des Universums durch eine \\\'Abkürzung\\\' verbinden. Ein Ende eines Wurmlochs erscheint dem Beobachter als Kugel, die ihm die Umgebung des anderen Endes zeigt. Obwohl ein durch ein Wurmloch Reisender nie die Lichtgeschwindigkeit überschreiten würde, hätte in Bezug auf die betreffenden Start- und Zielbereiche eine Reise mit Überlichtgeschwindigkeit stattgefunden. Durch die Erforschung der Wurmlöcher gelang es Wissenschaftlern, Technologien für das Reisen durch Wurmlöcher zu entwickeln und somit die Flugzeit enorm zu verkürzen. Ob die zwei Wurmlochenden eines Lochs immer miteinander verknüpft bleiben oder ob  die Verknüpfungen von Zeit zu Zeit ändern, ist Gegenstand aktueller Untersuchungen.\r\nBisher ist es den Forschern jedoch noch nicht gelungen, ein solches Wurmloch länger als ein paar Tage offen zu halten.',100000,120000,175000,290000,250000,0,'1.60',1,1,5,1),(23,'Gentechnik',3,'Durch die Manipulierung der Gene ist es möglich, die Leistung der Arbeiter zu steigern und so die Bauzeit zu verringern.','Den Forschern ist ein absoluter Durchbruch im Bereich Genforschung gelungen. Bisher waren alle genmanipulierten Arbeiterversuche fehlgeschlagen und die meisten Versuchsobjekte überlebten dieses Experiment nicht. Doch nun gelang mit Hilfe von Hochpräzisionsmaschinen eine genetische Veränderung, sodass die Arbeiter zu höheren Leistungen fähig sind.\r\nDies hat zur Folge, dass die Bauzeit von jeglichen Produkten nochmals gesenkt werden kann.\r\nDiese revolutionäre Errungenschaft hat aber ihren Preis, denn der Eingriff ist extrem zeit- und kostenaufwändig. Viele Wissenschaftler sind sich aber dennoch einig, dass es sich allemal lohnt, diese Technologie zu verbessern und zu perfektionieren.',100000000,60000000,38000000,40000000,20000000,0,'1.40',8,1,6,0),(24,'Raketentechnik',3,'Das Wissen um diese Technologie in Verbindung mit dem Raketensilo ermöglichen es, Raketen zu konstruieren.','Damit Raketen eingesetzt werden können, muss zuerst die Raketetechnik erforscht sein. Je höher die Raketentechnik erforscht ist, desto bessere und effektivere Raketen können gebaut werden.',30000,60000,400000,20000,0,0,'1.20',10,1,6,1),(25,'Computertechnik',4,'Mit Computern können Forscher komplexe Gleichungssysteme lösen, um genauere Flugbahnen zu berechnen.','Mit Hilfe der Computerwissenschaft können Forscher komplexe Gleichungssysteme lösen, um damit zum Beispiel genaue Flugbahnen zu berechnen. Dies kann zu einem Vorteil in der gegnerischen Flottenüberwachung führen oder eine bessere Steuerbarkeit von Raketen ermöglichen.',500,5000,0,3000,0,0,'1.30',15,1,4,1);
/*!40000 ALTER TABLE `technologies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_cat`
--

DROP TABLE IF EXISTS `ticket_cat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_cat` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `sort` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_cat`
--

LOCK TABLES `ticket_cat` WRITE;
/*!40000 ALTER TABLE `ticket_cat` DISABLE KEYS */;
INSERT INTO `ticket_cat` VALUES (1,'Beleidigung in Nachricht',0),(2,'Rathaus-Missbrauch',1),(3,'Missachtung der Angriffsregeln',2),(4,'Pushing-Verdach',3),(5,'Cheat-Verdach',4),(6,'Bugusing-Verdacht',5),(7,'Anstössiges Bild',6),(8,'Sonstiger Regelverstoss',7),(9,'Änderung meiner fixen E-Mail-Adresse',9),(10,'Änderung meines Namens (Accountübergabe)',10),(11,'Probleme mit einer Flotte (Ungültige Koordinaten, hängenbleibende Flotte)',11),(12,'Problem mit der Allianz (Ränge, Forum, Bündnisse, Auslösung etc)',12),(15,'Probleme mit den Account-Einstellungen (Design, Urlaubsmodus etc)',13),(14,'Anderes Problem',20),(16,'Verdacht auf Accounthacking',14),(17,'Probleme mit meinem Passwort',15);
/*!40000 ALTER TABLE `ticket_cat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_msg`
--

DROP TABLE IF EXISTS `ticket_msg`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_msg` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  `timestamp` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_msg`
--

LOCK TABLES `ticket_msg` WRITE;
/*!40000 ALTER TABLE `ticket_msg` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_msg` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tickets` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tips`
--

DROP TABLE IF EXISTS `tips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tips` (
  `tip_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tip_text` text COLLATE utf8_unicode_ci NOT NULL,
  `tip_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`tip_id`),
  KEY `tip_active` (`tip_active`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tips`
--

LOCK TABLES `tips` WRITE;
/*!40000 ALTER TABLE `tips` DISABLE KEYS */;
INSERT INTO `tips` VALUES (1,'Gib niemals dein Passwort an andere Leute, auch nicht an Moderatoren und Admins. Logge dich nur über www.etoa.ch ein und niemals über eine andere Seite. Akzeptiere keine Dateien von fremden Spielern und sorge dafür, dass dein Passwort sicher ist und niemand Zugriff auf deinen Account bekommt.',1),(2,'Gründet Allianzen oder schliesst euch einer bestehen Allianz an, um gemeinsam gegen Feinde zu kämpfen und spezielle Allianzgebäude und -schiffe bauen zu können.',1);
/*!40000 ALTER TABLE `tips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_comments`
--

DROP TABLE IF EXISTS `user_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_comments` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `comment_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `comment_admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `comment_text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Admin comments on users';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_comments`
--

LOCK TABLES `user_comments` WRITE;
/*!40000 ALTER TABLE `user_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_log`
--

DROP TABLE IF EXISTS `user_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_log` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_log`
--

LOCK TABLES `user_log` WRITE;
/*!40000 ALTER TABLE `user_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_minimap`
--

DROP TABLE IF EXISTS `user_minimap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_minimap` (
  `minimap_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `minimap_sx` int(3) unsigned NOT NULL DEFAULT '1',
  `minimap_sy` int(3) unsigned NOT NULL DEFAULT '1',
  `minimap_cx` int(3) unsigned NOT NULL DEFAULT '1',
  `minimap_cy` int(3) unsigned NOT NULL DEFAULT '1',
  `minimap_user_fly_points` int(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`minimap_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_minimap`
--

LOCK TABLES `user_minimap` WRITE;
/*!40000 ALTER TABLE `user_minimap` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_minimap` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_multi`
--

DROP TABLE IF EXISTS `user_multi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_multi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `multi_id` int(10) unsigned NOT NULL DEFAULT '0',
  `connection` varchar(50) NOT NULL DEFAULT '0',
  `activ` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_multi_user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_multi`
--

LOCK TABLES `user_multi` WRITE;
/*!40000 ALTER TABLE `user_multi` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_multi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_onlinestats`
--

DROP TABLE IF EXISTS `user_onlinestats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_onlinestats` (
  `stats_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `stats_timestamp` int(10) unsigned NOT NULL,
  `stats_count` int(5) unsigned NOT NULL,
  `stats_regcount` int(5) unsigned NOT NULL,
  PRIMARY KEY (`stats_id`),
  KEY `stats_count` (`stats_count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_onlinestats`
--

LOCK TABLES `user_onlinestats` WRITE;
/*!40000 ALTER TABLE `user_onlinestats` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_onlinestats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_points`
--

DROP TABLE IF EXISTS `user_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_points` (
  `point_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `point_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `point_timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `point_points` bigint(12) unsigned NOT NULL DEFAULT '0',
  `point_ship_points` bigint(12) unsigned NOT NULL DEFAULT '0',
  `point_tech_points` bigint(12) unsigned NOT NULL DEFAULT '0',
  `point_building_points` bigint(12) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`point_id`),
  KEY `point_user_id` (`point_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Speichert den Punkteverlauf der Spieler';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_points`
--

LOCK TABLES `user_points` WRITE;
/*!40000 ALTER TABLE `user_points` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_points` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_properties`
--

DROP TABLE IF EXISTS `user_properties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_properties` (
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
  `keybinds_enable` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_properties`
--

LOCK TABLES `user_properties` WRITE;
/*!40000 ALTER TABLE `user_properties` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_properties` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_ratings`
--

DROP TABLE IF EXISTS `user_ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_ratings` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_ratings`
--

LOCK TABLES `user_ratings` WRITE;
/*!40000 ALTER TABLE `user_ratings` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_sessionlog`
--

DROP TABLE IF EXISTS `user_sessionlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_sessionlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` char(40) NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_addr` varchar(40) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `time_login` int(10) unsigned NOT NULL DEFAULT '0',
  `time_action` int(10) unsigned NOT NULL DEFAULT '0',
  `time_logout` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_sessionlog`
--

LOCK TABLES `user_sessionlog` WRITE;
/*!40000 ALTER TABLE `user_sessionlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_sessionlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_sessions`
--

DROP TABLE IF EXISTS `user_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_sessions` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_sessions`
--

LOCK TABLES `user_sessions` WRITE;
/*!40000 ALTER TABLE `user_sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_sitting`
--

DROP TABLE IF EXISTS `user_sitting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_sitting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sitter_id` int(10) unsigned NOT NULL DEFAULT '0',
  `password` varchar(255) NOT NULL DEFAULT '0',
  `date_from` int(10) unsigned NOT NULL DEFAULT '0',
  `date_to` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_sitting_sitter_user_id` (`sitter_id`),
  KEY `user_sitting_user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_sitting`
--

LOCK TABLES `user_sitting` WRITE;
/*!40000 ALTER TABLE `user_sitting` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_sitting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_stats`
--

DROP TABLE IF EXISTS `user_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_stats` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_stats`
--

LOCK TABLES `user_stats` WRITE;
/*!40000 ALTER TABLE `user_stats` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_surveillance`
--

DROP TABLE IF EXISTS `user_surveillance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_surveillance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `page` varchar(50) NOT NULL,
  `request` text NOT NULL,
  `post` text NOT NULL,
  `session` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_surveillance`
--

LOCK TABLES `user_surveillance` WRITE;
/*!40000 ALTER TABLE `user_surveillance` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_surveillance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_warnings`
--

DROP TABLE IF EXISTS `user_warnings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_warnings` (
  `warning_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `warning_user_id` int(10) unsigned NOT NULL,
  `warning_date` int(10) unsigned NOT NULL,
  `warning_text` text COLLATE utf8_unicode_ci NOT NULL,
  `warning_admin_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`warning_id`),
  KEY `warning_user_id` (`warning_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_warnings`
--

LOCK TABLES `user_warnings` WRITE;
/*!40000 ALTER TABLE `user_warnings` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_warnings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `user_nick` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `user_password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_password_temp` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `user_logouttime` int(10) unsigned NOT NULL DEFAULT '0',
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wormholes`
--

DROP TABLE IF EXISTS `wormholes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wormholes` (
  `id` int(10) unsigned NOT NULL,
  `target_id` int(10) unsigned NOT NULL DEFAULT '0',
  `changed` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `target_id` (`target_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wormholes`
--

LOCK TABLES `wormholes` WRITE;
/*!40000 ALTER TABLE `wormholes` DISABLE KEYS */;
/*!40000 ALTER TABLE `wormholes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-02-19 12:06:34
