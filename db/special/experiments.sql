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
