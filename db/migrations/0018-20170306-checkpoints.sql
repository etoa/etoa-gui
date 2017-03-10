ALTER TABLE `users` ADD `npc` TINYINT NOT NULL DEFAULT '0';

INSERT INTO `planet_types` (`type_name`, `type_habitable`, `type_comment`, `type_consider`) VALUES
('Kontrollpunkt', '0', 'Placeholder','0');

INSERT INTO `alliance_buildings` (`alliance_building_name`, `alliance_building_longcomment`, `alliance_building_needed_level`, `alliance_building_last_level`) VALUES
  ('Allianzhafen', 'Von hier können Allianzschiffe gestartet werden',0,'10'),
  ('Allianzfabrik', 'Dient zur Herstellung von Allianzverteidigung',0,'10');

ALTER TABLE `users` ADD `user_storage_metal` decimal(18,6) DEFAULT '0';
ALTER TABLE `users` ADD `user_storage_crystal` decimal(18,6) DEFAULT '0';
ALTER TABLE `users` ADD `user_storage_plastic` decimal(18,6) DEFAULT '0';
ALTER TABLE `users` ADD `user_storage_fuel` decimal(18,6) DEFAULT '0';
ALTER TABLE `users` ADD `user_storage_food` decimal(18,6) DEFAULT '0';

ALTER TABLE `planets` ADD `planet_bonus_metal` TINYINT DEFAULT '0';
ALTER TABLE `planets` ADD `planet_bonus_crystal` TINYINT DEFAULT '0';
ALTER TABLE `planets` ADD `planet_bonus_plastic` TINYINT DEFAULT '0';
ALTER TABLE `planets` ADD `planet_bonus_fuel` TINYINT DEFAULT '0';
ALTER TABLE `planets` ADD `planet_bonus_food` TINYINT DEFAULT '0';

INSERT INTO `def_cat` VALUES
  (4, 'Allianzverteidigung',3,'#080DC7');

ALTER TABLE `defense` ADD `def_alliance_factory_level` TINYINT DEFAULT '0';
ALTER TABLE `defense` ADD `def_alliance_costs` MEDIUMINT DEFAULT '0';
