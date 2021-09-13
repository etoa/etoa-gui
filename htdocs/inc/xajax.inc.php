<?PHP

require_once __DIR__ . "/../libs/xajax/xajax_core/xajax.inc.php";

$xajax = new xajax();
$xajax->configure("javascript URI", __DIR__ . "/../libs/xajax");
$xajax->configure('debug', isDebugEnabled());

require_once(RELATIVE_ROOT . "inc/xajax/global.xajax.php");

if (isset($page)) {
    if ($page == "haven") {
        require_once(RELATIVE_ROOT . "inc/xajax/haven.xajax.php");
    } elseif ($page == "stats") {
        require_once(RELATIVE_ROOT . "inc/xajax/stats.xajax.php");
    } elseif ($page == "alliance") {
        require_once(RELATIVE_ROOT . "inc/xajax/alliance.xajax.php");
    } elseif ($page == "messages") {
        require_once(RELATIVE_ROOT . "inc/xajax/messages.xajax.php");
    } elseif ($page == "reports") {
        require_once(RELATIVE_ROOT . "inc/xajax/reports.xajax.php");
    } elseif ($page == "userconfig") {
        require_once(RELATIVE_ROOT . "inc/xajax/userconfig.xajax.php");
    } elseif ($page == "cell" || $page == "sector") {
        require_once(RELATIVE_ROOT . "inc/xajax/cell.xajax.php");
    } elseif ($page == "market") {
        require_once(RELATIVE_ROOT . "inc/xajax/market.xajax.php");
    } elseif ($page == "techtree" || $page == "help") {
        require_once(RELATIVE_ROOT . "inc/xajax/techtree.xajax.php");
    } elseif ($page == "bookmarks") {
        require_once(RELATIVE_ROOT . "inc/xajax/bookmarks.xajax.php");
    }
}

if (isset($index)) {
    if ($index == "register") {
        require_once(RELATIVE_ROOT . "inc/xajax/register.xajax.php");
    }
}

$xajax->processRequest();
