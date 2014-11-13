--
-- Tabellenstruktur für Tabelle `multifire`
--

CREATE TABLE IF NOT EXISTS `multifire` (
`id` int(10) unsigned NOT NULL,
  `source_ship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `source_def_id` int(10) unsigned NOT NULL DEFAULT '0',
  `target_ship_id` int(10) unsigned NOT NULL DEFAULT '0',
  `value` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `target_def_id` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `multifire`
--

INSERT INTO `multifire` (`id`, `source_ship_id`, `source_def_id`, `target_ship_id`, `value`, `target_def_id`) VALUES
(1, 6, 0, 2, 30, 0),
(2, 6, 0, 3, 50, 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ng_ships`
--

CREATE TABLE IF NOT EXISTS `ng_ships` (
`id` int(10) unsigned NOT NULL,
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
  `actions` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Indexes for table `multifire`
--
ALTER TABLE `multifire`
 ADD PRIMARY KEY (`id`), ADD KEY `source_ship_id_2` (`source_ship_id`,`target_def_id`), ADD KEY `source_ship_id` (`source_ship_id`,`target_ship_id`), ADD KEY `source_def_id` (`source_def_id`,`target_ship_id`);

--
-- Indexes for table `ng_ships`
--
ALTER TABLE `ng_ships`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `multifire`
--
ALTER TABLE `multifire`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `ng_ships`
--
ALTER TABLE `ng_ships`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
