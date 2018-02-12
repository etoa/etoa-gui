ALTER TABLE buildings ENGINE=InnoDB;
ALTER TABLE defense ENGINE=InnoDB;
ALTER TABLE technologies ENGINE=InnoDB;
ALTER TABLE ships ENGINE=InnoDB;
ALTER TABLE missiles ENGINE=InnoDB;

ALTER TABLE building_requirements ENGINE=InnoDB;
ALTER TABLE def_requirements ENGINE=InnoDB;
ALTER TABLE missile_requirements ENGINE=InnoDB;
ALTER TABLE ship_requirements ENGINE=InnoDB;
ALTER TABLE tech_requirements ENGINE=InnoDB;

ALTER TABLE building_types ENGINE=InnoDB;
ALTER TABLE def_cat ENGINE=InnoDB;
ALTER TABLE ship_cat ENGINE=InnoDB;
ALTER TABLE tech_types ENGINE=InnoDB;

ALTER TABLE ticket_cat ENGINE=InnoDB;


ALTER TABLE building_requirements CHANGE obj_id obj_id INT UNSIGNED NOT NULL, CHANGE req_building_id req_building_id INT UNSIGNED DEFAULT NULL, CHANGE req_tech_id req_tech_id INT UNSIGNED DEFAULT NULL, CHANGE req_level req_level SMALLINT DEFAULT 1 NOT NULL;

UPDATE building_requirements SET req_building_id = null WHERE req_building_id = 0;
UPDATE building_requirements SET req_tech_id = null WHERE req_tech_id = 0;

ALTER TABLE building_requirements ADD CONSTRAINT FK_EB479F2566093344 FOREIGN KEY (obj_id) REFERENCES buildings (building_id);
ALTER TABLE building_requirements ADD CONSTRAINT FK_EB479F257E57261C FOREIGN KEY (req_building_id) REFERENCES buildings (building_id);
ALTER TABLE building_requirements ADD CONSTRAINT FK_EB479F2568C70794 FOREIGN KEY (req_tech_id) REFERENCES technologies (tech_id);
CREATE INDEX IDX_EB479F2566093344 ON building_requirements (obj_id);
CREATE INDEX IDX_EB479F257E57261C ON building_requirements (req_building_id);
CREATE INDEX IDX_EB479F2568C70794 ON building_requirements (req_tech_id);


ALTER TABLE def_requirements CHANGE obj_id obj_id INT UNSIGNED NOT NULL, CHANGE req_building_id req_building_id INT UNSIGNED DEFAULT NULL, CHANGE req_tech_id req_tech_id INT UNSIGNED DEFAULT NULL, CHANGE req_level req_level SMALLINT DEFAULT 1 NOT NULL;

UPDATE def_requirements SET req_building_id = null WHERE req_building_id = 0;
UPDATE def_requirements SET req_tech_id = null WHERE req_tech_id = 0;

ALTER TABLE def_requirements ADD CONSTRAINT FK_21FC302366093344 FOREIGN KEY (obj_id) REFERENCES defense (def_id);
ALTER TABLE def_requirements ADD CONSTRAINT FK_21FC30237E57261C FOREIGN KEY (req_building_id) REFERENCES buildings (building_id);
ALTER TABLE def_requirements ADD CONSTRAINT FK_21FC302368C70794 FOREIGN KEY (req_tech_id) REFERENCES technologies (tech_id);
CREATE INDEX IDX_21FC302366093344 ON def_requirements (obj_id);
CREATE INDEX IDX_21FC30237E57261C ON def_requirements (req_building_id);
CREATE INDEX IDX_21FC302368C70794 ON def_requirements (req_tech_id);


ALTER TABLE missile_requirements CHANGE obj_id obj_id INT UNSIGNED NOT NULL, CHANGE req_building_id req_building_id INT UNSIGNED DEFAULT NULL, CHANGE req_tech_id req_tech_id INT UNSIGNED DEFAULT NULL, CHANGE req_level req_level SMALLINT UNSIGNED DEFAULT 1 NOT NULL;

UPDATE missile_requirements SET req_building_id = null WHERE req_building_id = 0;
UPDATE missile_requirements SET req_tech_id = null WHERE req_tech_id = 0;

ALTER TABLE missile_requirements ADD CONSTRAINT FK_F991BD9A66093344 FOREIGN KEY (obj_id) REFERENCES missiles (missile_id);
ALTER TABLE missile_requirements ADD CONSTRAINT FK_F991BD9A7E57261C FOREIGN KEY (req_building_id) REFERENCES buildings (building_id);
ALTER TABLE missile_requirements ADD CONSTRAINT FK_F991BD9A68C70794 FOREIGN KEY (req_tech_id) REFERENCES technologies (tech_id);
CREATE INDEX IDX_F991BD9A66093344 ON missile_requirements (obj_id);
CREATE INDEX IDX_F991BD9A7E57261C ON missile_requirements (req_building_id);
CREATE INDEX IDX_F991BD9A68C70794 ON missile_requirements (req_tech_id);


ALTER TABLE ship_requirements CHANGE obj_id obj_id INT UNSIGNED NOT NULL, CHANGE req_building_id req_building_id INT UNSIGNED DEFAULT NULL, CHANGE req_tech_id req_tech_id INT UNSIGNED DEFAULT NULL, CHANGE req_level req_level SMALLINT DEFAULT 1 NOT NULL;

UPDATE ship_requirements SET req_building_id = null WHERE req_building_id = 0;
UPDATE ship_requirements SET req_tech_id = null WHERE req_tech_id = 0;
DELETE FROM ship_requirements WHERE obj_id NOT IN (SELECT ship_id FROM ships);

ALTER TABLE ship_requirements ADD CONSTRAINT FK_4F2112CA66093344 FOREIGN KEY (obj_id) REFERENCES ships (ship_id);
ALTER TABLE ship_requirements ADD CONSTRAINT FK_4F2112CA7E57261C FOREIGN KEY (req_building_id) REFERENCES buildings (building_id);
ALTER TABLE ship_requirements ADD CONSTRAINT FK_4F2112CA68C70794 FOREIGN KEY (req_tech_id) REFERENCES technologies (tech_id);
CREATE INDEX IDX_4F2112CA66093344 ON ship_requirements (obj_id);
CREATE INDEX IDX_4F2112CA7E57261C ON ship_requirements (req_building_id);
CREATE INDEX IDX_4F2112CA68C70794 ON ship_requirements (req_tech_id);


ALTER TABLE tech_requirements CHANGE obj_id obj_id INT UNSIGNED NOT NULL, CHANGE req_building_id req_building_id INT UNSIGNED DEFAULT NULL, CHANGE req_tech_id req_tech_id INT UNSIGNED DEFAULT NULL, CHANGE req_level req_level SMALLINT DEFAULT 1 NOT NULL;

UPDATE tech_requirements SET req_building_id = null WHERE req_building_id = 0;
UPDATE tech_requirements SET req_tech_id = null WHERE req_tech_id = 0;

ALTER TABLE tech_requirements ADD CONSTRAINT FK_541D739466093344 FOREIGN KEY (obj_id) REFERENCES technologies (tech_id);
ALTER TABLE tech_requirements ADD CONSTRAINT FK_541D73947E57261C FOREIGN KEY (req_building_id) REFERENCES buildings (building_id);
ALTER TABLE tech_requirements ADD CONSTRAINT FK_541D739468C70794 FOREIGN KEY (req_tech_id) REFERENCES technologies (tech_id);
CREATE INDEX IDX_541D739466093344 ON tech_requirements (obj_id);
CREATE INDEX IDX_541D73947E57261C ON tech_requirements (req_building_id);
CREATE INDEX IDX_541D739468C70794 ON tech_requirements (req_tech_id);


ALTER TABLE def_cat CHANGE cat_order cat_order SMALLINT UNSIGNED DEFAULT 0 NOT NULL;
ALTER TABLE building_types CHANGE type_order type_order SMALLINT UNSIGNED DEFAULT 0 NOT NULL;
ALTER TABLE ship_cat CHANGE cat_order cat_order SMALLINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE cat_color cat_color CHAR(7) DEFAULT '#ffffff' NOT NULL;
ALTER TABLE tech_types CHANGE type_order type_order SMALLINT UNSIGNED DEFAULT 0 NOT NULL;


ALTER TABLE ticket_cat CHANGE sort sort SMALLINT UNSIGNED DEFAULT 0 NOT NULL;
