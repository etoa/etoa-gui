<?PHP

	define('RELATIVE_ROOT','../');
	define('ADMIN_MODE',true);

	require_once("../inc/bootstrap.inc.php");

	// Renderzeit-Start festlegen
	$render_time = explode(" ",microtime());
	$render_starttime=$render_time[1]+$render_time[0];

	define('IMAGE_PATH',"../images/imagepacks/Discovery");
	define('IMAGE_EXT',"png");

	// Load specific admin functions
	require("inc/admin_functions.inc.php");

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

?>