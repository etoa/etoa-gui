<?PHP

	define('RELATIVE_ROOT','../');
	define('ADMIN_MODE',true);

	require_once("../global.inc.php");

	// Seitenwahl zuweisen
	$page = isset($_GET['page']) ? $_GET['page'] : 'home';
	$sub = isset($_GET['sub']) ? $_GET['sub'] : '';

	// Renderzeit-Start festlegen
	$render_time = explode(" ",microtime());
	$render_starttime=$render_time[1]+$render_time[0];

	define('IMAGE_PATH',"../images/imagepacks/Discovery");
	define('IMAGE_EXT',"png");

	// Session-Cookie setzen
	ini_set('arg_separator.output',  '&amp;');
	session_start();

	// Funktionen und Config einlesen
	if (!@include_once("../conf.inc.php")) die("conf.inc.php does not exist, please read INSTALL for how to create this file or <a href=\"..\">click here</a> for the setup wizard!!");
	require("../functions.php");
	
	require("inc/admin_functions.inc.php");

	// Mit der DB verbinden
	dbconnect();
	

	// Config-Werte laden
	$cfg = Config::getInstance();
	$conf = $cfg->getArray();
	include("../def.inc.php");

	// Navigation laden
	require_once('nav.php');

	// Feste Konstanten

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

	define('USER_BLOCKED_DEFAULT_TIME',3600*24*$conf['user_ban_min_length']['v']);	// Standardsperrzeit
	define('USER_HMODE_DEFAULT_TIME',3600*24*$conf['user_umod_min_length']['v']);	// Standardurlaubszeit

	define('ADMIN_FILESHARING_DIR',CACHE_ROOT."/admin");

	// XAJAX
	include("inc/xajax_admin.inc.php");

	// Check Login
	require("inc/admin_login.inc.php");	
	
	// Define s as the current session variable
	$s = $_SESSION[SESSION_NAME];
	

?>