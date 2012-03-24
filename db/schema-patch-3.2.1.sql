DROP TABLE `admin_surveillance`;
ALTER TABLE `users` CHANGE `user_password` `user_password` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

