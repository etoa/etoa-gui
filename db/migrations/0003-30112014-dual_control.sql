ALTER TABLE `users` ADD `dual_email` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `users` ADD `dual_name` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
INSERT INTO `etoa_test`.`ticket_cat` (`id`, `name`, `sort`) VALUES (NULL, '', '0'), (NULL, 'Änderung meines Dualspielers', '8');
