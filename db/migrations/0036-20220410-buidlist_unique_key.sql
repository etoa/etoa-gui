ALTER TABLE buildlist ENGINE=InnoDB;
DELETE FROM buildlist WHERE buildlist_building_id NOT IN (
    SELECT building_id FROM buildings
);

ALTER TABLE buildlist ADD CONSTRAINT fk_building_id FOREIGN KEY (buildlist_building_id) REFERENCES buildings(building_id);
