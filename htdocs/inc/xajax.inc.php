<?PHP

require_once __DIR__ . "/../libs/xajax/xajax_core/xajax.inc.php";

$xajax = new xajax();
$xajax->configure("javascript URI", "../libs/xajax");
$xajax->configure('debug', false);

require_once __DIR__  . "/xajax/global.xajax.php";

if (isset($page)) {
    if ($page == "haven") {
        require_once __DIR__ . "/xajax/haven.xajax.php";
    } elseif ($page == "stats") {
        require_once __DIR__ . "/xajax/stats.xajax.php";
    } elseif ($page == "alliance") {
        require_once __DIR__ . "/xajax/alliance.xajax.php";
    } elseif ($page == "messages") {
        require_once __DIR__ . "/xajax/messages.xajax.php";
    } elseif ($page == "reports") {
        require_once __DIR__ . "/xajax/reports.xajax.php";
    } elseif ($page == "userconfig") {
        require_once __DIR__ . "/xajax/userconfig.xajax.php";
    } elseif ($page == "cell" || $page == "sector") {
        require_once __DIR__ . "/xajax/cell.xajax.php";
    } elseif ($page == "market") {
        require_once __DIR__ . "/xajax/market.xajax.php";
    } elseif ($page == "techtree" || $page == "help") {
        require_once __DIR__ . "/xajax/techtree.xajax.php";
    } elseif ($page == "bookmarks") {
        require_once __DIR__ . "/xajax/bookmarks.xajax.php";
    }
}

if (isset($index)) {
    if ($index == "register") {
        require_once __DIR__ . "/xajax/register.xajax.php";
    }
}

$xajax->processRequest();
