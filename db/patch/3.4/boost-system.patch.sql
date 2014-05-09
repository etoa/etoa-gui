ALTER TABLE `users` 
ADD COLUMN `boost_bonus_production` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `discoverymask_last_updated`,
ADD COLUMN `boost_bonus_building` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0.00' AFTER `boost_bonus_production`;
