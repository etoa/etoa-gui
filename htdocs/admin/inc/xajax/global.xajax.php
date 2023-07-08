<?PHP

use EtoA\User\UserRepository;
use EtoA\User\UserSearch;

$xajax->register(XAJAX_FUNCTION, "searchUser");

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
