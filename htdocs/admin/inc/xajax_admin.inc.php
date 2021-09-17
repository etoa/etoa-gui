<?PHP

require_once __DIR__ . "/../../libs/xajax/xajax_core/xajax.inc.php";

$xajax = new xajax();
$xajax->configure("javascript URI", "../libs/xajax");
$xajax->configure('debug', false);

require_once __DIR__ . "/xajax/global.xajax.php";

require_once __DIR__ . "/xajax/techtree.xajax.php";
require_once __DIR__ ."/../../inc/xajax/techtree.xajax.php";

if (isset($page) && $page == "chat") {
    require_once __DIR__ . "/xajax/chat.xajax.php";
}

if (isset($page) && $page == "alliances") {
    require_once __DIR__ . "/xajax/alliances.xajax.php";
}

if (isset($page) && ($page == "user" || $page == "sendmessage")) {
    require_once __DIR__ . "/xajax/user.xajax.php";
}

if (isset($page) && $page == "ships") {
    require_once __DIR__ . "/xajax/ships.xajax.php";
}

if (isset($page) && $page == "messages") {
    require_once __DIR__ . "/xajax/messages.xajax.php";
}

if (isset($sub) && $sub == "defaultitems") {
    require_once __DIR__ . "/xajax/defaultitems.xajax.php";
}


if (isset($page) && $page == "logs") {
    require_once __DIR__ . "/xajax/logs.xajax.php";
}

if (isset($page) && $page == "tfcalculator") {
    require_once __DIR__ . "/xajax/tfcalculator.xajax.php";
}

$xajax->processRequest();
