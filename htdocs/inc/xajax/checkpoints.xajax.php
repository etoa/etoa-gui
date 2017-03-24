<?PHP

// Main dialogs
$xajax->register(XAJAX_FUNCTION,"checkpointShow");

function checkpointShow() {

    $response = new xajaxResponse();
    ob_start();
    echo "<div>blub</div>";

    $response->assign("havenContentShips","innerHTML",ob_get_contents());
    ob_end_clean();
    return $response;

}
