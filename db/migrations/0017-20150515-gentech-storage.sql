UPDATE `buildlist` AS target INNER JOIN (SELECT SUM(`buildlist_gen_people_working`) as workers, `buildlist_user_id` FROM buildlist GROUP BY `buildlist_user_id`) AS source ON target.`buildlist_user_id`=source.`buildlist_user_id` AND target.`buildlist_building_id`=7 SET target.`buildlist_people_working`=source.workers;
ALTER TABLE `buildlist` DROP `buildlist_gen_people_working`;
UPDATE buildings SET building_workplace=1 WHERE building_id = 7;
