DELETE FROM def_requirements WHERE id IN (
    SELECT * FROM (
        (SELECT max(id) FROM def_requirements WHERE req_tech_id IS NOT NULL GROUP BY obj_id, req_tech_id HAVING COUNT(*) > 1)
    ) as t
);
DELETE FROM def_requirements WHERE id IN (
  SELECT * FROM (
                  (SELECT max(id) FROM def_requirements WHERE req_building_id IS NOT NULL GROUP BY obj_id, req_building_id HAVING COUNT(*) > 1)
                ) as t
);
ALTER TABLE def_requirements DROP KEY obj_id;
ALTER TABLE def_requirements ADD CONSTRAINT `obj_building` UNIQUE (`obj_id`, `req_building_id`);
ALTER TABLE def_requirements ADD CONSTRAINT `obj_tech` UNIQUE (`obj_id`, `req_tech_id`);

DELETE FROM building_requirements WHERE id IN (
  SELECT * FROM (
                  (SELECT max(id) FROM building_requirements WHERE req_tech_id IS NOT NULL GROUP BY obj_id, req_tech_id HAVING COUNT(*) > 1)
                ) as t
);
DELETE FROM building_requirements WHERE id IN (
  SELECT * FROM (
                  (SELECT max(id) FROM building_requirements WHERE req_building_id IS NOT NULL GROUP BY obj_id, req_building_id HAVING COUNT(*) > 1)
                ) as t
);
ALTER TABLE building_requirements DROP KEY obj_id;
ALTER TABLE building_requirements ADD CONSTRAINT `obj_building` UNIQUE (`obj_id`, `req_building_id`);
ALTER TABLE building_requirements ADD CONSTRAINT `obj_tech` UNIQUE (`obj_id`, `req_tech_id`);

DELETE FROM ship_requirements WHERE id IN (
  SELECT * FROM (
                  (SELECT max(id) FROM ship_requirements WHERE req_tech_id IS NOT NULL GROUP BY obj_id, req_tech_id HAVING COUNT(*) > 1)
                ) as t
);
DELETE FROM ship_requirements WHERE id IN (
  SELECT * FROM (
                  (SELECT max(id) FROM ship_requirements WHERE req_building_id IS NOT NULL GROUP BY obj_id, req_building_id HAVING COUNT(*) > 1)
                ) as t
);
ALTER TABLE ship_requirements DROP KEY obj_id;
ALTER TABLE ship_requirements ADD CONSTRAINT `obj_building` UNIQUE (`obj_id`, `req_building_id`);
ALTER TABLE ship_requirements ADD CONSTRAINT `obj_tech` UNIQUE (`obj_id`, `req_tech_id`);

DELETE FROM tech_requirements WHERE id IN (
  SELECT * FROM (
                  (SELECT max(id) FROM tech_requirements WHERE req_tech_id IS NOT NULL GROUP BY obj_id, req_tech_id HAVING COUNT(*) > 1)
                ) as t
);
DELETE FROM tech_requirements WHERE id IN (
  SELECT * FROM (
                  (SELECT max(id) FROM tech_requirements WHERE req_building_id IS NOT NULL GROUP BY obj_id, req_building_id HAVING COUNT(*) > 1)
                ) as t
);
ALTER TABLE tech_requirements DROP KEY obj_id;
ALTER TABLE tech_requirements ADD CONSTRAINT `obj_building` UNIQUE (`obj_id`, `req_building_id`);
ALTER TABLE tech_requirements ADD CONSTRAINT `obj_tech` UNIQUE (`obj_id`, `req_tech_id`);

DELETE FROM missile_requirements WHERE id IN (
  SELECT * FROM (
                  (SELECT max(id) FROM missile_requirements WHERE req_tech_id IS NOT NULL GROUP BY obj_id, req_tech_id HAVING COUNT(*) > 1)
                ) as t
);
DELETE FROM missile_requirements WHERE id IN (
  SELECT * FROM (
                  (SELECT max(id) FROM missile_requirements WHERE req_building_id IS NOT NULL GROUP BY obj_id, req_building_id HAVING COUNT(*) > 1)
                ) as t
);
ALTER TABLE missile_requirements DROP KEY obj_id;
ALTER TABLE missile_requirements ADD CONSTRAINT `obj_building` UNIQUE (`obj_id`, `req_building_id`);
ALTER TABLE missile_requirements ADD CONSTRAINT `obj_tech` UNIQUE (`obj_id`, `req_tech_id`);
