<?PHP

$xajax = new xajax();
$xajax->configure("javascript URI", "/xajax");
$xajax->configure('debug', false);

require_once "global.xajax.php";

if (isset($page)) {
    require_once "viewport.xajax.php";
    if ($page == "haven") {
        require_once "haven.xajax.php";
    } elseif ($page == "stats") {
        require_once "stats.xajax.php";
    } elseif ($page == "alliance") {
        require_once "alliance.xajax.php";
    } elseif ($page == "messages") {
        require_once "messages.xajax.php";
    } elseif ($page == "reports") {
        require_once "reports.xajax.php";
    } elseif ($page == "userconfig") {
        require_once "userconfig.xajax.php";
    } elseif ($page == "cell" || $page == "sector") {
        require_once "cell.xajax.php";
    } elseif ($page == "market") {
        require_once "market.xajax.php";
    } elseif ($page == "techtree" || $page == "help") {
        require_once "techtree.xajax.php";
    } elseif ($page == "bookmarks") {
        require_once "bookmarks.xajax.php";
    }
}

if (isset($index)) {
    if ($index == "register") {
        require_once "register.xajax.php";
    }
}

$xajax->processRequest();

return $xajax;
