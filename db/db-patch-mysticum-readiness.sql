ALTER TABLE `ships` ADD `special_ship_bonus_readiness` DECIMAL( 4, 2 ) NOT NULL AFTER `special_ship_bonus_deactivade`;
ALTER TABLE `shiplist` ADD `shiplist_special_ship_bonus_readiness` TINYINT( 2 ) UNSIGNED NOT NULL DEFAULT '0';
