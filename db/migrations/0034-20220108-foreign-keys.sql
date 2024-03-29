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

ALTER TABLE users ENGINE=InnoDB;
DELETE FROM reports WHERE user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE reports MODIFY user_id smallint(5) unsigned NOT NULL;
ALTER TABLE reports ADD CONSTRAINT reports_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;

DELETE FROM notepad_data WHERE id NOT IN (SELECT id FROM notepad);
ALTER TABLE notepad_data ADD CONSTRAINT notepad_data_id FOREIGN KEY (id) REFERENCES notepad(id) ON DELETE CASCADE;

DELETE FROM notepad WHERE user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE notepad ADD CONSTRAINT notepad_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;

DELETE FROM user_comments WHERE comment_user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE user_comments ADD CONSTRAINT comment_user_id FOREIGN KEY (comment_user_id) REFERENCES users(user_id) ON DELETE CASCADE;

DELETE FROM tickets WHERE user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE tickets ADD CONSTRAINT tickets_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;

DELETE FROM user_log WHERE user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE user_log ADD CONSTRAINT user_log_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;

DELETE FROM user_multi WHERE user_id NOT IN (SELECT user_id FROM users);
DELETE FROM user_multi WHERE multi_id NOT IN (SELECT user_id FROM users);
ALTER TABLE user_multi ADD CONSTRAINT user_multi_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;
ALTER TABLE user_multi ADD CONSTRAINT user_multi_multi_id FOREIGN KEY (multi_id) REFERENCES users(user_id) ON DELETE CASCADE;

DELETE FROM user_properties WHERE id NOT IN (SELECT user_id FROM users);
ALTER TABLE user_properties ADD CONSTRAINT user_properties_id FOREIGN KEY (id) REFERENCES users(user_id) ON DELETE CASCADE;

DELETE FROM user_ratings WHERE id NOT IN (SELECT user_id FROM users);
ALTER TABLE user_ratings ADD CONSTRAINT user_ratings_id FOREIGN KEY (id) REFERENCES users(user_id) ON DELETE CASCADE;

DELETE FROM user_surveillance WHERE user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE user_surveillance ADD CONSTRAINT user_surveillance_user_id FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE;

DELETE FROM alliance_ranks WHERE rank_alliance_id NOT IN (SELECT alliance_id FROM alliances);
ALTER TABLE alliance_ranks ADD CONSTRAINT rank_alliance_id FOREIGN KEY (rank_alliance_id) REFERENCES alliances(alliance_id) ON DELETE CASCADE;

DELETE FROM alliance_bnd WHERE alliance_bnd_alliance_id1 NOT IN (SELECT alliance_id FROM alliances);
DELETE FROM alliance_bnd WHERE alliance_bnd_alliance_id2 NOT IN (SELECT alliance_id FROM alliances);
ALTER TABLE alliance_bnd ADD CONSTRAINT alliance_bnd_alliance_id1 FOREIGN KEY (alliance_bnd_alliance_id1) REFERENCES alliances(alliance_id) ON DELETE CASCADE;
ALTER TABLE alliance_bnd ADD CONSTRAINT alliance_bnd_alliance_id2 FOREIGN KEY (alliance_bnd_alliance_id2) REFERENCES alliances(alliance_id) ON DELETE CASCADE;
