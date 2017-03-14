CREATE TABLE quests (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  quest_data_id INT NOT NULL,
  slot_id VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  state VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE INDEX user_state_idx ON quests (user_id, state);

CREATE TABLE quest_tasks (
  id       INT NOT NULL AUTO_INCREMENT,
  quest_id INT NOT NULL,
  task_id  INT NOT NULL,
  progress INT NOT NULL,
  PRIMARY KEY (id)
)  ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE INDEX quest_idx ON quest_tasks (quest_id);

ALTER TABLE quest_tasks ADD CONSTRAINT quest_fk FOREIGN KEY (quest_id) REFERENCES quests (id);
