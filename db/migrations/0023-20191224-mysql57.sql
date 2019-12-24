ALTER TABLE admin_users
    CHANGE user_session_key user_session_key varchar(250) DEFAULT NULL,
    CHANGE user_ip user_ip varchar(20) DEFAULT NULL,
    CHANGE user_hostname user_hostname varchar(150) DEFAULT NULL;

ALTER TABLE stars CHANGE name name varchar(30) DEFAULT NULL;
ALTER TABLE planets
    CHANGE planet_name planet_name varchar(30) DEFAULT NULL,
    CHANGE planet_desc planet_desc text DEFAULT NULL;

ALTER TABLE users
    CHANGE user_password_temp user_password_temp varchar(30) DEFAULT NULL,
    CHANGE user_session_key user_session_key varchar(250) DEFAULT NULL,
    CHANGE user_ip user_ip varchar(20) DEFAULT NULL,
    CHANGE user_hostname user_hostname varchar(150) DEFAULT NULL,
    CHANGE user_ban_reason user_ban_reason text DEFAULT NULL,
    CHANGE user_profile_text user_profile_text text DEFAULT NULL,
    CHANGE user_avatar user_avatar varchar(250) DEFAULT NULL,
    CHANGE user_signature user_signature text DEFAULT NULL,
    CHANGE user_client user_client varchar(255) DEFAULT NULL,
    CHANGE user_profile_board_url user_profile_board_url char(250) DEFAULT NULL,
    CHANGE user_profile_img user_profile_img char(250) DEFAULT NULL,
    CHANGE user_observe user_observe text DEFAULT NULL,
    CHANGE discoverymask discoverymask text DEFAULT NULL,
    CHANGE dual_email dual_email varchar(50) DEFAULT NULL,
    CHANGE dual_name dual_name varchar(30) DEFAULT NULL;

ALTER TABLE user_ratings
    CHANGE battles_fought battles_fought smallint(5) unsigned DEFAULT 0,
    CHANGE battles_won battles_won smallint(5) unsigned DEFAULT 0,
    CHANGE battles_lost battles_lost smallint(5) unsigned DEFAULT 0,
    CHANGE battle_rating battle_rating smallint(5) unsigned DEFAULT 0,
    CHANGE trades_sell trades_sell smallint(5) unsigned DEFAULT 0,
    CHANGE trade_rating trade_rating smallint(5) unsigned DEFAULT 0,
    CHANGE diplomacy_rating diplomacy_rating smallint(5) unsigned DEFAULT 0;

ALTER TABLE user_properties
    CHANGE image_ext image_ext varchar(6) DEFAULT NULL,
    CHANGE image_url image_url varchar(255) DEFAULT NULL,
    CHANGE css_style css_style varchar(30) DEFAULT NULL,
    CHANGE msgsignature msgsignature text DEFAULT NULL;

ALTER TABLE alliances
    CHANGE alliance_text alliance_text text DEFAULT NULL,
    CHANGE alliance_img alliance_img varchar(255) DEFAULT NULL,
    CHANGE alliance_url alliance_url varchar(255) DEFAULT NULL,
    CHANGE alliance_visits alliance_visits int(10) unsigned DEFAULT 0,
    CHANGE alliance_visits_ext alliance_visits_ext int(10) unsigned DEFAULT 0,
    CHANGE alliance_application_template alliance_application_template text DEFAULT NULL;
