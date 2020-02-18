ALTER TABLE `ships` ADD `ship_tradable` tinyint(1) unsigned NOT NULL DEFAULT '1';
UPDATE `ships` SET `ship_tradable`=0 WHERE `special_ship`=1;