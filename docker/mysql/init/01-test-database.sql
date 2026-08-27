-- Runs once, when the database volume is created. MYSQL_DATABASE/MYSQL_USER from
-- compose.yaml already create the etoa database and the etoa user; this adds the
-- second database the test suite uses, mirroring provision.sh.
CREATE DATABASE IF NOT EXISTS etoa_test
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON etoa_test.* TO 'etoa'@'%';
FLUSH PRIVILEGES;
