INSERT INTO `specialists` (`specialist_id`, `specialist_name`, `specialist_desc`, `specialist_enabled`, `specialist_points_req`, `specialist_costs_metal`, `specialist_costs_crystal`, `specialist_costs_plastic`, `specialist_costs_fuel`, `specialist_costs_food`, `specialist_days`, `specialist_prod_metal`, `specialist_prod_crystal`, `specialist_prod_plastic`, `specialist_prod_fuel`, `specialist_prod_food`, `specialist_power`, `specialist_population`, `specialist_time_tech`, `specialist_time_buildings`, `specialist_time_defense`, `specialist_time_ships`, `specialist_costs_buildings`, `specialist_costs_defense`, `specialist_costs_ships`, `specialist_costs_tech`, `specialist_fleet_speed`, `specialist_fleet_max`, `specialist_def_repair`, `specialist_spy_level`, `specialist_tarn_level`, `specialist_trade_time`, `specialist_trade_bonus`) VALUES
(10, 'Architekt', 'Der Architekt hilft mit seinem Wissen bei der Planung und Konstruktion komplexer Bauprojekte. Aufgrund seiner langjährigen Erfahrung können Bauten unter seiner Leitung schneller realisiert werden.', 1, 100000, 100000, 100000, 100000, 100000, 100000, 7, '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '0.90', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', '1.00', 0, '1.00', 0, 0, '1.00', '1.00');

UPDATE `buildings` SET `building_costs_power` = '10' WHERE `buildings`.`building_id` = 1;
UPDATE `buildings` SET `building_costs_power` = '20' WHERE `buildings`.`building_id` = 2;
UPDATE `buildings` SET `building_costs_power` = '20' WHERE `buildings`.`building_id` = 3;
UPDATE `buildings` SET `building_costs_power` = '50' WHERE `buildings`.`building_id` = 4;
UPDATE `buildings` SET `building_costs_power` = '5' WHERE `buildings`.`building_id` = 5;
UPDATE `buildings` SET `building_costs_power` = '50' WHERE `buildings`.`building_id` = 6;
UPDATE `buildings` SET `building_costs_power` = '100' WHERE `buildings`.`building_id` = 22;
UPDATE `buildings` SET `building_costs_power` = '50000' WHERE `buildings`.`building_id` = 25;
