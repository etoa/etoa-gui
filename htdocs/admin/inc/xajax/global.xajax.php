<?PHP

use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceSearch;
use EtoA\Building\BuildingDataRepository;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;

$xajax->register(XAJAX_FUNCTION, "searchUser");
$xajax->register(XAJAX_FUNCTION, "searchAlliance");
$xajax->register(XAJAX_FUNCTION, "lockUser");

$xajax->register(XAJAX_FUNCTION, "buildingPrices");
$xajax->register(XAJAX_FUNCTION, "totalBuildingPrices");


//Listet gefundene User auf
function searchUser($val, $field_id = 'user_nick', $box_id = 'citybox')
{
    global $app;

    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];

    $sOut = "";
    $sLastHit = null;

    $userNicks = $userRepository->searchUserNicknames(UserSearch::create()->nickLike($val), 20);
    $nCount = count($userNicks);
    foreach ($userNicks as $nick) {
        $sOut .= "<a href=\"#\" onclick=\"javascript:document.getElementById('" . $field_id . "').value='" . htmlentities($nick) . "';document.getElementById('" . $box_id . "').style.display = 'none';\">" . htmlentities($nick) . "</a>";
        $sLastHit = $nick;
    }

    if ($nCount > 20) {
        $sOut = "";
    }

    $objResponse = new xajaxResponse();

    if (strlen($sOut) > 0) {
        $sOut = "" . $sOut . "";
        $objResponse->script("document.getElementById('" . $box_id . "').style.display = \"block\"");
    } else {
        $objResponse->script("document.getElementById('" . $box_id . "').style.display = \"none\"");
    }

    //Wenn nur noch ein User in frage kommt, diesen Anzeigen
    if ($nCount == 1) {
        $objResponse->script("document.getElementById('" . $box_id . "').style.display = \"none\"");
        $objResponse->script("document.getElementById('" . $field_id . "').value = \"" . $sLastHit . "\"");
    }

    $objResponse->assign($box_id, "innerHTML", $sOut);

    return $objResponse;
}


//Listet gefundene Allianzen auf
function searchAlliance($val, $field_id = 'alliance_name', $box_id = 'citybox')
{
    global $app;

    /** @var AllianceRepository $allianceRepository */
    $allianceRepository = $app[AllianceRepository::class];

    $sOut = "";
    $nCount = 0;
    $sLastHit = null;

    $allianceNames = $allianceRepository->getAllianceNames(AllianceSearch::create()->nameLike($val), 20);
    foreach ($allianceNames as $allianceName) {
        $nCount++;
        $sOut .= "<a href=\"#\" onclick=\"javascript:document.getElementById('" . $field_id . "').value='" . htmlentities($allianceName) . "';document.getElementById('" . $box_id . "').style.display = 'none';\">" . htmlentities($allianceName) . "</a>";
        $sLastHit = $allianceName;
    }

    if ($nCount > 20) {
        $sOut = "";
    }

    $objResponse = new xajaxResponse();

    if (strlen($sOut) > 0) {
        $sOut = "" . $sOut . "";
        $objResponse->script("document.getElementById('" . $box_id . "').style.display = \"block\"");
    } else {
        $objResponse->script("document.getElementById('" . $box_id . "').style.display = \"none\"");
    }

    //Wenn nur noch ein User in frage kommt, diesen Anzeigen
    if ($nCount == 1) {
        $objResponse->script("document.getElementById('" . $box_id . "').style.display = \"none\"");
        $objResponse->script("document.getElementById('" . $field_id . "').value = \"" . $sLastHit . "\"");
    }

    $objResponse->assign($box_id, "innerHTML", $sOut);

    return $objResponse;
}

function lockUser($uid, $time, $reason)
{
    global $app;

    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];

    $t1 = time();
    $t2 = $t1 + $time;
    $userRepository->blockUser($uid, $t1, $t2, $reason, $_SESSION[SESSION_NAME]['user_id']);
    $objResponse = new xajaxResponse();
    $objResponse->alert("Der Benutzer wurde gesperrt!");
    return $objResponse;
}

/***********/

function buildingPrices($id, $lvl)
{
    global $app;

    /** @var BuildingDataRepository $buildingRepository */
    $buildingRepository = $app[BuildingDataRepository::class];

    $objResponse = new xajaxResponse();
    $building = $buildingRepository->getBuilding($id);
    $bc = calcBuildingCosts($building, $lvl);
    $objResponse->assign("c1_metal", "innerHTML", StringUtils::formatNumber($bc['metal']));
    $objResponse->assign("c1_crystal", "innerHTML", StringUtils::formatNumber($bc['crystal']));
    $objResponse->assign("c1_plastic", "innerHTML", StringUtils::formatNumber($bc['plastic']));
    $objResponse->assign("c1_fuel", "innerHTML", StringUtils::formatNumber($bc['fuel']));
    $objResponse->assign("c1_food", "innerHTML", StringUtils::formatNumber($bc['food']));
    $objResponse->assign("c1_power", "innerHTML", StringUtils::formatNumber($bc['power']));

    return $objResponse;
}

function totalBuildingPrices($form)
{
    global $app;

    /** @var BuildingDataRepository $buildingRepository */
    $buildingRepository = $app[BuildingDataRepository::class];

    $objResponse = new xajaxResponse();
    $bctt = array();
    foreach ($form['b_lvl'] as $id => $lvl) {
        $building = $buildingRepository->getBuilding($id);
        $bct = array();
        for ($x = 0; $x < $lvl; $x++) {
            $bc = calcBuildingCosts($building, $x);
            $bct['metal'] += $bc['metal'];
            $bct['crystal'] += $bc['crystal'];
            $bct['plastic'] += $bc['plastic'];
            $bct['fuel'] += $bc['fuel'];
            $bct['food'] += $bc['food'];
        }
        $bctt['metal'] += $bct['metal'];
        $bctt['crystal'] += $bct['crystal'];
        $bctt['plastic'] += $bct['plastic'];
        $bctt['fuel'] += $bct['fuel'];
        $bctt['food'] += $bct['food'];
        $objResponse->assign("b_metal_" . $id, "innerHTML", StringUtils::formatNumber($bct['metal']));
        $objResponse->assign("b_crystal_" . $id, "innerHTML", StringUtils::formatNumber($bct['crystal']));
        $objResponse->assign("b_plastic_" . $id, "innerHTML", StringUtils::formatNumber($bct['plastic']));
        $objResponse->assign("b_fuel_" . $id, "innerHTML", StringUtils::formatNumber($bct['fuel']));
        $objResponse->assign("b_food_" . $id, "innerHTML", StringUtils::formatNumber($bct['food']));
    }
    $objResponse->assign("t_metal", "innerHTML", StringUtils::formatNumber($bctt['metal']));
    $objResponse->assign("t_crystal", "innerHTML", StringUtils::formatNumber($bctt['crystal']));
    $objResponse->assign("t_plastic", "innerHTML", StringUtils::formatNumber($bctt['plastic']));
    $objResponse->assign("t_fuel", "innerHTML", StringUtils::formatNumber($bctt['fuel']));
    $objResponse->assign("t_food", "innerHTML", StringUtils::formatNumber($bctt['food']));


    return $objResponse;
}
