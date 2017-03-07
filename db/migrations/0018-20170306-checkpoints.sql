ALTER TABLE `users` ADD `npc` TINYINT NOT NULL DEFAULT '0';

INSERT INTO `planet_types` (`type_name`, `type_habitable`, `type_comment`, `type_consider`) VALUES
('Kontrollpunkt', '0', 'Placeholder','0');

INSERT INTO `alliance_buildings` (`alliance_building_name`, `alliance_building_longcomment`, `alliance_needed_level`, `alliance_building_last_level`) VALUES
  ('Allianzhafen', 'Von hier können Allianzschiffe gestartet werden',0,'10');
