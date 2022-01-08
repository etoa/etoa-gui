DELETE FROM missilelist WHERE missilelist_user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE missilelist ADD CONSTRAINT missilelist_user_id FOREIGN KEY (missilelist_user_id) REFERENCES users(user_id) ON DELETE CASCADE;

DELETE FROM techlist WHERE techlist_user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE techlist ADD CONSTRAINT techlist_user_id FOREIGN KEY (techlist_user_id) REFERENCES users(user_id) ON DELETE CASCADE;

DELETE FROM shiplist WHERE shiplist_user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE shiplist ADD CONSTRAINT shiplist_user_id FOREIGN KEY (shiplist_user_id) REFERENCES users(user_id) ON DELETE CASCADE;

DELETE FROM ship_queue WHERE queue_user_id NOT IN (SELECT user_id FROM users);
ALTER TABLE ship_queue ADD CONSTRAINT queue_user_id FOREIGN KEY (queue_user_id) REFERENCES users(user_id) ON DELETE CASCADE;
