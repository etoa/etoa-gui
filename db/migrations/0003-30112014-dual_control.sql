ALTER TABLE `users` ADD `dual_email` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `users` ADD `dual_name` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
INSERT INTO `ticket_cat` (`name`, `sort`) VALUES ('Änderung meines Dualspielers', '8');
