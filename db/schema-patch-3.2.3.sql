ALTER TABLE `admin_users`
ADD `roles` VARCHAR( 255 ) NOT NULL ,
ADD `is_contact` TINYINT UNSIGNED NOT NULL ,
DROP `user_admin_rank`;

DROP TABLE `admin_groups`;
