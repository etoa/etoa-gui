CREATE INDEX reports_user_type_entity1_idx ON reports (user_id, type, entity1_id);
CREATE INDEX reports_user_read_deleted_idx ON reports (user_id, `read`, `deleted`);
CREATE INDEX reports_user_archived_type_idx ON reports (user_id, archived, type);
CREATE INDEX reports_user_deleted_archived_idx ON reports (user_id, `deleted`, archived, timestamp);

ALTER TABLE reports_other ADD PRIMARY KEY(id);
ALTER TABLE reports_spy ADD PRIMARY KEY(id);
ALTER TABLE reports_battle ADD PRIMARY KEY(id);

CREATE INDEX logs_game_user_facility_timestamp_idx ON logs_game (user_id, facility);
CREATE INDEX logs_game_facility_object_idx ON logs_game (facility, object_id);

