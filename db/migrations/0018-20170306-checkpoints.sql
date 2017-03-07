ALTER TABLE `users` ADD `npc` TINYINT NOT NULL DEFAULT '0';
INSERT INTO `planet_types` (`type_name`, `type_habitable`, `type_comment`, `type_consider`) VALUES
('Kontrollpunkt', '0', 'Placeholder','0');
