<?PHP

use EtoA\Bookmark\FleetBookmarkRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Entity\EntityCoordinates;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;

$xajax->register(XAJAX_FUNCTION, 'launchBookmarkProbe');
$xajax->register(XAJAX_FUNCTION, 'searchShipList');
$xajax->register(XAJAX_FUNCTION, 'bookmarkTargetInfo');
$xajax->register(XAJAX_FUNCTION, 'bookmarkBookmark');

$xajax->register(XAJAX_FUNCTION, 'showFleetCategorie');

// Spy and analyze probe also available on bookmark page
include_once('cell.xajax.php');

function showFleetCategorie($cId)
{
    $objResponse = new xajaxResponse();

    ob_start();

    $fbm = unserialize($_SESSION['bookmarks']['fbm']);

    echo $fbm->printBookmarks($cId);

    $_SESSION['bookmarks']['fbm'] = serialize($fbm);

    $objResponse->assign("bookmark$cId", "innerHTML", ob_get_contents());
    ob_end_clean();

    return $objResponse;
}

function launchBookmarkProbe($bid)
{
    global $app;

    /** @var FleetBookmarkRepository $fleetBookMarkRepository */
    $fleetBookMarkRepository = $app[FleetBookmarkRepository::class];
    $cp = Entity::createFactoryById($_SESSION['cpid']);

    $objResponse = new xajaxResponse();

    ob_start();
    $launched = false;
    $bookmark = $fleetBookMarkRepository->get($bid, $cp->owner()->id);
    if ($bookmark !== null) {
        $fleet = new FleetLaunch($cp, $cp->owner());
        if ($fleet->checkHaven()) {
            $shipOutput = "";
            $probeCount = true;
            /** @var \EtoA\Ship\ShipDataRepository $shipDataRepository */
            $shipDataRepository = $app[\EtoA\Ship\ShipDataRepository::class];
            $ships = $shipDataRepository->getShipNames(true);
            foreach ($bookmark->ships as $shipId => $count) {
                $probeCount = min($probeCount, $fleet->addShip($shipId, $count));
                if ($shipOutput != "") $shipOutput .= ", ";
                $shipOutput .= $count . " " . $ships[$shipId];
            }

            if ($probeCount) {
                if ($fleet->fixShips()) {
                    if ($ent = Entity::createFactoryById($bookmark->targetId)) {
                        if ($fleet->setTarget($ent)) {
                            $fleet->setSpeedPercent($bookmark->speed);
                            if ($fleet->checkTarget()) {
                                if ($fleet->setAction($bookmark->action)) {
                                    $fleet->loadResource(0, $bookmark->freight->metal, 1);
                                    $fleet->loadResource(1, $bookmark->freight->crystal, 1);
                                    $fleet->loadResource(2, $bookmark->freight->plastic, 1);
                                    $fleet->loadResource(3, $bookmark->freight->fuel, 1);
                                    $fleet->loadResource(4, $bookmark->freight->food, 1);
                                    $fleet->loadPeople($bookmark->freight->people);

                                    $fleet->fetchResource(0, $bookmark->freight->metal);
                                    $fleet->fetchResource(1, $bookmark->freight->crystal);
                                    $fleet->fetchResource(2, $bookmark->freight->plastic);
                                    $fleet->fetchResource(3, $bookmark->freight->fuel);
                                    $fleet->fetchResource(4, $bookmark->freight->food);
                                    $fleet->fetchResource(5, $bookmark->freight->people);

                                    if ($fid = $fleet->launch()) {
                                        $flObj = new Fleet($fid);


                                        $str = "Folgende Schiffe sind unterwegs: $shipOutput. Ankunft in " . tf($flObj->remainingTime());
                                        $launched = true;
                                    } else
                                        $str = $fleet->error();
                                } else
                                    $str = $fleet->error();
                            } else
                                $str = $fleet->error();
                        } else
                            $str = $fleet->error();
                    } else {
                        $str = "Problem beim Finden des Zielobjekts!";
                    }
                } else {
                    $str = $fleet->error();
                }
            } else {
                $str = "Auf deinem Planeten befinden sich nicht genug Schiffe der ausgewählten Typen!";
            }
        } else {
            $str = $fleet->error();
        }
    } else {
        $str = "Der ausgewählte Flottenfavorit ist ungültig!";
    }
    if ($launched) {
        echo "<div style=\"color:#0f0\">" . $str . "<div>";
    } else {
        echo "<div style=\"color:#f90\">" . $str . "<div>";
    }

    $action_content = "<a href=\"javascript:;\" onclick=\"$('#fleet_bm_actions_" . $bid . "').html('Flotte wird gestartet...');xajax_launchBookmarkProbe(" . $bid . ");\"  onclick=\"\">Starten</a>
                            <a href=\"?page=bookmarks&amp;mode=new&amp;edit=" . $bid . "\">Bearbeiten</a>
                            <a href=\"?page=bookmarks&amp;mode=fleet&amp;del=" . $bid . "\" onclick=\"return confirm('Soll dieser Favorit wirklich gel&ouml;scht werden?');\">Entfernen</a>";
    $objResponse->assign("fleet_bm_actions_" . $bid, "innerHTML", $action_content);
    $objResponse->assign("fleet_info_box", "style.display", 'block');
    $objResponse->append("fleet_info", "innerHTML", ob_get_contents());
    ob_end_clean();
    return $objResponse;
}

//Listet gefundene Schiffe auf
function searchShipList($val)
{
    $targetId = 'shiplist';
    $inputId = 'shipname';

    $sOut = "";
    $nCount = 0;
    $sLastHit = null;

    $res = dbquery("SELECT
            ship_name
        FROM
            ships
        WHERE
            (ship_show=1
                || ship_buildable=1)
            AND ship_name LIKE '" . $val . "%'
        LIMIT 20;");
    if (mysql_num_rows($res) > 0) {
        while ($arr = mysql_fetch_row($res)) {
            $nCount++;
            $sOut .= "<a href=\"#\" onclick=\"javascript:document.getElementById('$inputId').value='" . htmlspecialchars($arr[0]) . "';fleetBookmarkAddShipToList('" . $arr[0] . "');document.getElementById('$targetId').style.display = 'none';\">" . htmlspecialchars($arr[0]) . "</a>";
            $sLastHit = $arr[0];
        }
    }

    if ($nCount > 20) {
        $sOut = "";
    }

    $objResponse = new xajaxResponse();

    if (strlen($sOut) > 0) {
        $sOut = "" . $sOut . "";
        $objResponse->script("document.getElementById('$targetId').style.display = \"block\"");
    } else {
        $objResponse->script("document.getElementById('$targetId').style.display = \"none\"");
    }

    //Wenn nur noch ein User in frage kommt, diesen Anzeigen
    if ($nCount == 1) {
        $objResponse->script("document.getElementById('$targetId').style.display = \"none\"");
        $objResponse->script("document.getElementById('$inputId').value = \"" . $sLastHit . "\"");
        $objResponse->script("document.getElementById('$inputId').value=\"\"");
        $objResponse->script("fleetBookmarkAddShipToList('$sLastHit')");
    }

    $objResponse->assign("$targetId", "innerHTML", $sOut);
    return $objResponse;
}

function bookmarkTargetInfo($form)
{
    $response = new xajaxResponse();
    ob_start();

    // TODO
    global $app;

    /** @var ConfigurationService */
    $config = $app[ConfigurationService::class];
    /** @var EntityRepository $entityRepository */
    $entityRepository = $app[EntityRepository::class];
    /** @var UserUniverseDiscoveryService */
    $userUniverseDiscoveryService = $app[UserUniverseDiscoveryService::class];

    /** @var UserRepository */
    $userRepository = $app[UserRepository::class];

    $pos = (int)$form['pos'];
    $sx = (int)$form['sx'];
    $sy = (int)$form['sy'];
    $cx = (int)$form['cx'];
    $cy = (int)$form['cy'];
    if ($sx > 0 && $sy > 0 && $cx > 0 && $cy > 0 && $pos >= 0) {
        $absX = (($sx - 1) * $config->param1Int('num_of_cells')) + $cx;
        $absY = (($sy - 1) * $config->param2Int('num_of_cells')) + $cy;

        $user = $userRepository->getUser(intval($_SESSION['user_id']));
        $code = $userUniverseDiscoveryService->discovered($user, $absX, $absY) == 0 ? 'u' : '';

        $entity = $entityRepository->findByCoordinates(new EntityCoordinates($sx, $sy, $cx, $cy, $pos));
        if ($entity !== null && !($code == 'u' && isset($form['man_p']))) {
            if ($code == '')
                $ent = Entity::createFactory($entity->code, $entity->id);
            else
                $ent = Entity::createFactory($code, $entity->id);

            echo "<img src=\"" . $ent->imagePath() . "\" style=\"float:left;\" >";

            echo "<br/>&nbsp;&nbsp; " . $ent . " (" . $ent->entityCodeString() . ", Besitzer: " . $ent->owner() . ")";
            $response->assign('targetinfo', 'style.background', "#000");
            $response->assign('submit', "style.display", "");
            $response->assign('resbox', "style.display", "");
        } else {
            echo "<div style=\"color:#f00\">Ziel nicht vorhanden!</div>";
            $response->assign('submit', "style.display", "none");
            $response->assign('resbox', "style.display", "none");
        }

        $response->assign('targetinfo', 'innerHTML', ob_get_contents());

        ob_end_clean();
    }
    return $response;
}

function bookmarkBookmark($form)
{
    $response = new xajaxResponse();

    if ($form["bookmarks"]) {
        $ent = Entity::createFactoryById($form["bookmarks"]);
        $sx = $ent->sx();
        $sy = $ent->sy();
        $cx = $ent->cx();
        $cy = $ent->cy();
        $pos = $ent->pos();

        $response->assign('sx', 'value', $sx);
        $response->assign('sy', 'value', $sy);
        $response->assign('cx', 'value', $cx);
        $response->assign('cy', 'value', $cy);
        $response->assign('pos', 'value', $pos);

        ob_start();

        echo "<img src=\"" . $ent->imagePath() . "\" style=\"float:left;\" >";

        echo "<br/>&nbsp;&nbsp; " . $ent . " (" . $ent->entityCodeString() . ", Besitzer: " . $ent->owner() . ")";
        $response->assign('targetinfo', 'style.background', "#000");

        $response->assign('targetinfo', 'innerHTML', ob_get_contents());
        $response->assign('submit', "style.display", "");
        $response->assign('resbox', "style.display", "");

        ob_end_clean();
    }
    return $response;
}
