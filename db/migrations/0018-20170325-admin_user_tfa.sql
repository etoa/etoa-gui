ALTER TABLE `admin_users` ADD `tfa_secret` VARCHAR(255) NOT NULL AFTER `user_password`;
