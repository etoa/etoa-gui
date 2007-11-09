<?PHP

// Database
define('DB_SERVER','localhost');
define('DB_USER','sessiontest');
define('DB_PASSWORD','sessiontest');
define('DB_DATABASE','session_test');

// Tables
define('TBL_USERS','users');
define('TBL_USER_SESSIONS','user_sessions');
define('TBL_USER_FAILED_LOGINS','user_failed_logins');
define('TBL_USER_SESSION_LOG','user_session_log');


define('USER_TIMEOUT', 3);
define('LOGIN_PATH', 'login.php');
define('CLASS_DIR','../classes');
define('REGEXP_NAME','^([a-zA-Z0-9äöüÄÖÜß_. #-])*$');



?>