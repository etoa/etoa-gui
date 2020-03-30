ALTER TABLE logs_battle_queue
    MODIFY entity_win_exp INT(10) NOT NULL DEFAULT -1,
    MODIFY fleet_win_exp INT(10) NOT NULL DEFAULT -1;
    
ALTER TABLE logs
    MODIFY ip VARCHAR(39) DEFAULT NULL;
