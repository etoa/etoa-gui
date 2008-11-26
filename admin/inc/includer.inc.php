<?PHP
	// Seitenwahl zuweisen
	$page = isset($_GET['page']) ? $_GET['page'] : 'home';
	$sub = isset($_GET['sub']) ? $_GET['sub'] : '';

	define(USE_HTML,true);


	// Renderzeit-Start festlegen
	$render_time = explode(" ",microtime());
	$render_starttime=$render_time[1]+$render_time[0];

	define(IMAGE_PATH,"../images/imagepacks/Discovery");
	define(IMAGE_EXT,"png");

	// Session-Cookie setzen
	ini_set('arg_separator.output',  '&amp;');
	session_start();

	// Funktionen und Config einlesen
	if (!@include_once("../conf.inc.php")) die("conf.inc.php does not exist, please read INSTALL for how to create this file or <a href=\"..\">click here</a> for the setup wizard!!");
	require("../functions.php");
	
	require("inc/admin_functions.inc.php");

	// Mit der DB verbinden
	dbconnect();
	
	// Admin defs
	
	define('CACHE_ROOT','../cache');
	define('CLASS_ROOT','../classes');
	define('DATA_DIR',"../data");
	
	// Config-Werte laden
	$cfg = Config::getInstance();
	$conf = $cfg->getArray();
	include("../def.inc.php");

	// Navigation laden
	require_once('nav.php');

	// Feste Konstanten
	define('IS_ADMIN_MODE',true);

	define('SESSION_NAME',"adminsession");
	define('USER_TABLE_NAME','admin_users');

	define('URL_SEARCH_STRING', "page=$page&amp;sub=$sub&amp;tmp=1");
	define('URL_SEARCH_STRING2', "page=$page");
	define('URL_SEARCH_STRING3', "page=$page");

	define('DATE_FORMAT',$conf['admin_dateformat']['v']);
	define('TIMEOUT',$conf['admin_timeout']['v']);

	define('HTPASSWD_COMMAND',$conf['htaccess']['v']);
	define('HTPASSWD_FILE',$conf['htaccess']['p2']);
	define('HTPASSWD_USER',$conf['admin_htaccess']['p1']);

	// User-Farben
	define('USER_COLOR_DEFAULT',$conf['color_default']['v']);
	define('USER_COLOR_BANNED',$conf['color_banned']['v']);
	define('USER_COLOR_INACTIVE',$conf['color_inactive']['v']);
	define('USER_COLOR_HOLIDAY',$conf['color_umod']['v']);
	define('USER_COLOR_FRIEND',$conf['color_friend']['v']);
	define('USER_COLOR_ENEMY',$conf['color_enemy']['v']);
	define('USER_COLOR_DELETED','#09f');

	define('USER_BLOCKED_DEFAULT_TIME',3600*24*$conf['user_ban_min_length']['v']);	// Standardsperrzeit
	define('USER_HMODE_DEFAULT_TIME',3600*24*$conf['user_umod_min_length']['v']);	// Standardurlaubszeit

	define('ADMIN_FILESHARING_DIR',CACHE_ROOT."/admin");

	// XAJAX
	include("inc/xajax_admin.inc.php");

	// Zufallsgenerator initialisieren
	mt_srand(time());
	
	// Check Login
	require("inc/admin_login.inc.php");	
	
	// Define s as the current session variable
	$s = $_SESSION[SESSION_NAME];
	

?>