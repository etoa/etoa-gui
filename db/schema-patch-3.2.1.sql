--
-- DROP TABLE `admin_surveillance`;
--
ALTER TABLE `users` CHANGE `user_password` `user_password` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

--
-- New config value for new shippoints algorithm by river ---
--
INSERT INTO config (config_name, config_value, config_param1, config_param2) VALUES('alliance_shippoints_base','1.4','','');