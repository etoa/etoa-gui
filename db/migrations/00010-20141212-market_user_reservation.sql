ALTER TABLE `market_ressource` ADD `for_user` INT unsigned NOT NULL DEFAULT '0' AFTER `buyer_entity_id` ;
ALTER TABLE `market_ship` ADD `for_user` INT unsigned NOT NULL DEFAULT '0' AFTER `buyer_entity_id` ;
