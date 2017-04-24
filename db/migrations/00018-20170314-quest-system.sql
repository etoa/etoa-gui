CREATE TABLE IF NOT EXISTS quests (
  id INT NOT NULL AUTO_INCREMENT,
  user_id INT NOT NULL,
  quest_data_id INT NOT NULL,
  slot_id VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  state VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY(id),
  KEY `user_state_idx` (`user_id`,`state`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS quest_tasks (
  id       INT NOT NULL AUTO_INCREMENT,
  quest_id INT NOT NULL,
  task_id  INT NOT NULL,
  progress INT NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT `quest_fk` FOREIGN KEY (`quest_id`) REFERENCES `quests` (`id`)
)  ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS quest_log (
  id INT NOT NULL AUTO_INCREMENT,
  quest_id INT NOT NULL,
  user_id INT NOT NULL,
  quest_data_id INT NOT NULL,
  slot_id VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  previous_state VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  transition VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  date INT NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
