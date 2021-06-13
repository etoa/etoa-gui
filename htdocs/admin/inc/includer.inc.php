<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

define('RELATIVE_ROOT','../');
define('ADMIN_MODE',true);
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../inc/bootstrap.inc.php';

if (isDebugEnabled()) {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

// Renderzeit-Start festlegen
$render_time = explode(" ",microtime());
$render_starttime=$render_time[1]+$render_time[0];

define('IMAGE_PATH',"../images/imagepacks/Discovery");
define('IMAGE_EXT',"png");

// Feste Konstanten

define('SESSION_NAME',"adminsession");
define('USER_TABLE_NAME','admin_users');

define('URL_SEARCH_STRING', "page=$page&amp;sub=$sub&amp;tmp=1");
define('URL_SEARCH_STRING2', "page=$page");
define('URL_SEARCH_STRING3', "page=$page");

define('DATE_FORMAT',$conf['admin_dateformat']['v']);

define('USER_BLOCKED_DEFAULT_TIME',3600*24*$conf['user_ban_min_length']['v']);	// Standardsperrzeit
define('USER_HMODE_DEFAULT_TIME',3600*24*$conf['user_umod_min_length']['v']);	// Standardurlaubszeit

define('ADMIN_FILESHARING_DIR',CACHE_ROOT."/admin");

$css_theme = (!isset($themePath) || !is_file("web/css/themes/".$themePath."css")) ? "default" : $themePath;

