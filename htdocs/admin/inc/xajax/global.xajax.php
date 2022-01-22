<?PHP

use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceSearch;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;

$xajax->register(XAJAX_FUNCTION, "searchUser");
$xajax->register(XAJAX_FUNCTION, "searchAlliance");
$xajax->register(XAJAX_FUNCTION, "lockUser");


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

