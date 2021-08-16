<?PHP

use EtoA\User\UserRepository;

$xajax->register(XAJAX_FUNCTION,"showBattle");

$xajax->register(XAJAX_FUNCTION,"applyLogFilter");
$xajax->register(XAJAX_FUNCTION,"applyGameLogFilter");
$xajax->register(XAJAX_FUNCTION,"applyFleetLogFilter");
$xajax->register(XAJAX_FUNCTION,"applyAttackAbuseLogFilter");
$xajax->register(XAJAX_FUNCTION,"applyDebrisLogFilter");

function applyLogFilter($args,$limit=0)
{
    $objResponse = new xajaxResponse();
    require_once("inc/admin_functions.inc.php");
    ob_start();
    showLogs($args,$limit);
    $objResponse->assign("log_contents","innerHTML",ob_get_clean());

    return $objResponse;
}

function applyGameLogFilter($args,$limit=0)
{
    $objResponse = new xajaxResponse();
    require_once("inc/admin_functions.inc.php");
    ob_start();
    showGameLogs($args,$limit);
    $objResponse->assign("log_contents","innerHTML",ob_get_clean());

    return $objResponse;
}

function applyFleetLogFilter($args,$limit=0)
{
    $objResponse = new xajaxResponse();
    require_once("inc/admin_functions.inc.php");
    ob_start();
    showFleetLogs($args,$limit);
    $objResponse->assign("log_contents","innerHTML",ob_get_clean());

    return $objResponse;
}

function applyAttackAbuseLogFilter($args,$limit=0)
{
    $objResponse = new xajaxResponse();
    require_once("inc/admin_functions.inc.php");
    ob_start();
    showAttackAbuseLogs($args,$limit);
    $objResponse->assign("log_contents","innerHTML",ob_get_clean());

    return $objResponse;
}

function applyDebrisLogFilter($args,$limit=0)
{
    $objResponse = new xajaxResponse();
    require_once("inc/admin_functions.inc.php");
    ob_start();
    showDebrisLogs($args,$limit);
    $objResponse->assign("log_contents","innerHTML",ob_get_clean());
    return $objResponse;
}

function showBattle($battle,$id)
{
    ob_start();
    $objResponse = new xajaxResponse();

    if($battle!="")
    {
        $objResponse->assign("show_battle_".$id."","innerHTML", $battle);
    }
    else
    {
        $objResponse->assign("show_battle_".$id."","innerHTML", "");
    }

    $objResponse->assign("logsinfo","innerHTML",ob_get_contents());
    ob_end_clean();

    return $objResponse;

}
