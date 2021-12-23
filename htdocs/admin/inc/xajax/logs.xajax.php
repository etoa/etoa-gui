<?PHP

$xajax->register(XAJAX_FUNCTION,"applyGameLogFilter");

function applyGameLogFilter($args,$limit=0)
{
    $objResponse = new xajaxResponse();
    require_once __DIR__ . '/../../inc/admin_functions.inc.php';
    ob_start();
    showGameLogs($args,$limit);
    $objResponse->assign("log_contents","innerHTML",ob_get_clean());

    return $objResponse;
}
