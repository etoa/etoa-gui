DELETE FROM missilelist WHERE missilelist_user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE missilelist ADD CONSTRAINT missilelist_user_id FOREIGN KEY (missilelist_user_id) REFERENCES users(user_id) ON DELETE CASCADE;

DELETE FROM techlist WHERE techlist_user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE techlist ADD CONSTRAINT techlist_user_id FOREIGN KEY (techlist_user_id) REFERENCES users(user_id) ON DELETE CASCADE;

DELETE FROM shiplist WHERE shiplist_user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE shiplist ADD CONSTRAINT shiplist_user_id FOREIGN KEY (shiplist_user_id) REFERENCES users(user_id) ON DELETE CASCADE;

DELETE FROM ship_queue WHERE queue_user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE ship_queue ADD CONSTRAINT ship_queue_user_id FOREIGN KEY (queue_user_id) REFERENCES users(user_id) ON DELETE CASCADE;

DELETE FROM deflist WHERE deflist_user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE deflist ADD CONSTRAINT deflist_user_id FOREIGN KEY (deflist_user_id) REFERENCES users(user_id) ON DELETE CASCADE;

DELETE FROM def_queue WHERE queue_user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE def_queue ADD CONSTRAINT def_queue_user_id FOREIGN KEY (queue_user_id) REFERENCES users(user_id) ON DELETE CASCADE;

DELETE FROM buildlist WHERE buildlist_user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE buildlist ADD CONSTRAINT buildlist_user_id FOREIGN KEY (buildlist_user_id) REFERENCES users(user_id) ON DELETE CASCADE;

ALTER TABLE reports ENGINE=InnoDB;
ALTER TABLE reports_other ENGINE=InnoDB;
ALTER TABLE reports_spy ENGINE=InnoDB;
ALTER TABLE reports_battle ENGINE=InnoDB;
ALTER TABLE reports_market ENGINE=InnoDB;
DELETE FROM reports WHERE user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE reports ADD CONSTRAINT user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;
ALTER TABLE reports_other ADD CONSTRAINT other_report_id FOREIGN KEY (id) REFERENCES reports(id) ON DELETE CASCADE;
ALTER TABLE reports_spy ADD CONSTRAINT spy_report_id FOREIGN KEY (id) REFERENCES reports(id) ON DELETE CASCADE;
ALTER TABLE reports_battle ADD CONSTRAINT battle_report_id FOREIGN KEY (id) REFERENCES reports(id) ON DELETE CASCADE;
ALTER TABLE reports_market ADD CONSTRAINT market_report_id FOREIGN KEY (id) REFERENCES reports(id) ON DELETE CASCADE;

DELETE FROM notepad_data WHERE id NOT IN (SELECT id FROM notepad);
ALTER TABLE notepad_data ADD CONSTRAINT notepad_data_id FOREIGN KEY (id) REFERENCES notepad(id) ON DELETE CASCADE;

DELETE FROM notepad WHERE user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE notepad ADD CONSTRAINT notepad_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;
