DROP TABLE message_data;
DROP TABLE messages;

--
-- Datenbank: `etoa_test`
--

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
) ENGINE=INNODB  DEFAULT CHARSET=utf8 DELAY_KEY_WRITE=1 AUTO_INCREMENT=1 ;

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
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id`) REFERENCES messages(`message_id`) ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8;
