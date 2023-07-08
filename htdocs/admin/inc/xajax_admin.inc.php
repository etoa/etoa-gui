<?PHP

require_once __DIR__ . "/../../libs/xajax/xajax_core/xajax.inc.php";

$xajax = new xajax();
$xajax->configure("javascript URI", "../libs/xajax");
$xajax->configure('debug', false);

require_once __DIR__ . "/xajax/global.xajax.php";

require_once __DIR__ . "/../../inc/xajax/techtree.xajax.php";

$xajax->processRequest();
