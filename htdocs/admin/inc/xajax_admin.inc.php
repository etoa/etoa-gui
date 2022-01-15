<?PHP

require_once __DIR__ . "/../../libs/xajax/xajax_core/xajax.inc.php";

$xajax = new xajax();
$xajax->configure("javascript URI", "../libs/xajax");
$xajax->configure('debug', false);

require_once __DIR__ . "/xajax/global.xajax.php";

require_once __DIR__ . "/xajax/techtree.xajax.php";
require_once __DIR__ ."/../../inc/xajax/techtree.xajax.php";

if (isset($page) && ($page == "user")) {
    require_once __DIR__ . "/xajax/user.xajax.php";
}

if (isset($page) && $page == "ships") {
    require_once __DIR__ . "/xajax/ships.xajax.php";
}

if (isset($sub) && $sub == "defaultitems") {
    require_once __DIR__ . "/xajax/defaultitems.xajax.php";
}

$xajax->processRequest();
