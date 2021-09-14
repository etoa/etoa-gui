<?PHP

use EtoA\Admin\Forms\ShipCategoriesForm;
use EtoA\Admin\Forms\ShipsForm;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Core\ObjectWithImage;
use EtoA\Ranking\RankingService;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipQueueSearch;
use EtoA\Ship\ShipRepository;
use EtoA\Ship\ShipSort;
use EtoA\Ship\ShipXpCalculator;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var ShipDataRepository $shipDataRepository */
$shipDataRepository = $app[ShipDataRepository::class];

/** @var ShipRepository $shipRepository */
$shipRepository = $app[ShipRepository::class];

/** @var ShipQueueRepository $shipQueueRepository */
$shipQueueRepository = $app[ShipQueueRepository::class];

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

/** @var PlanetRepository $planetRepository */
$planetRepository = $app[PlanetRepository::class];

/** @var RankingService $rankingService */
$rankingService = $app[RankingService::class];

/** @var Request */
$request = Request::createFromGlobals();

//
// Battlepoints
//
if ($sub == "battlepoints") {
    $twig->addGlobal("title", "Schiff-Punkte");

    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"POST\">";
    if (isset($_POST['recalc'])) {
        $numShips = $rankingService->calcShipPoints();
        echo MessageBox::ok("", "Die Punkte von $numShips Schiffen wurden aktualisiert!");
    }
    echo "<p>Nach jeder direkter &Auml;nderung an den Schiffen via Datenbank m&uuml;ssen die Punkte neu berechnet werden!</p>
        <p><input type=\"submit\" name=\"recalc\" value=\"Neu berechnen\" /></p>
        </form>";

    $ships = $shipDataRepository->getAllShips(true, 'ship_points');
    echo "<table class=\"tb\">";
    foreach ($ships as $ship) {
        echo "<tr><th>" . $ship->name . "</th><td style=\"width:70%; text-align: right\" title=\"$ship->points\">" . StringUtils::formatNumber($ship->points) . "</td></tr>";
    }
    echo "</table>";
}


//
// XP-Rechner
//
elseif ($sub == "xpcalc") {
    $twig->addGlobal("title", "XP-Rechner");

    echo "Schiff wählen: <select onchange=\"document.location='?page=" . $page . "&sub=" . $sub . "&id='+this.options[this.selectedIndex].value\">";
    $specialShips = $shipDataRepository->getSpecialShips();
    $ship_xp = 0;
    $ship_xp_multiplier = 0;
    foreach ($specialShips as $ship) {
        echo "<option value=\"" . $ship->id . "\"";
        if ((isset($_GET['id']) && $_GET['id'] == $ship->id) || (!isset($_GET['id']) && $ship_xp === 0)) {
            echo " selected=\"selected\"";
            $ship_xp = $ship->specialNeedExp;
            $ship_xp_multiplier = $ship->specialExpFactor;
        }
        echo ">" . $ship->name . "</option>";
    }
    echo "</select><br/><br/>";

    echo "<table class=\"tb\"><tr><th>Level</th><th>Experience</th></tr>";
    for ($level = 1; $level <= 30; $level++) {
        echo "<tr><td>$level</td><td>" . StringUtils::formatNumber(ShipXpCalculator::xpByLevel($ship_xp, $ship_xp_multiplier, $level)) . "</td></tr>";
    }
    echo "</table>";
}


//
// Kategorien
//
elseif ($sub == "cat") {
    ShipCategoriesForm::render($app, $twig, $request);
}

//
// Daten
//
elseif ($sub == "data") {
    ShipsForm::render($app, $twig, $request);
}

//
// Schiffsanforderungen
//
elseif ($sub == "req") {
    //Definistion für die normalen Schiffe
    define("TITLE", "Schiffanforderungen");
    define("REQ_TBL", "ship_requirements");
    define("ITEM_IMAGE_PATH", ObjectWithImage::BASE_PATH . "/ships/ship<DB_TABLE_ID>_small.png");

    $objectNames = $shipDataRepository->getShipNames(true, ShipSort::category());
    include("inc/requirements.inc.php");
}

//
// Bauliste
//
elseif ($sub == "queue") {
    $twig->addGlobal("title", "Schiff-Bauliste");

    if (isset($_POST['shipqueue_search']) || isset($_GET['action']) && $_GET['action'] == "searchresults") {
        // Suchquery generieren
        if (!isset($_SESSION['shipqueue']['query'])) {
            $queueSearch = ShipQueueSearch::create();
            if ($_POST['planet_id'] != "") {
                $queueSearch->entityId($_POST['planet_id']);
            }
            if ($_POST['planet_name'] != "") {
                $queueSearch->likePlanetName($_POST['planet_name']);
            }
            if ($_POST['user_id'] != "") {
                $queueSearch->userId($_POST['user_id']);
            }
            if ($_POST['user_nick'] != "") {
                $queueSearch->likeUserNick($_POST['user_nick']);
            }
            if ($_POST['ship_id'] != "") {
                $queueSearch->shipId($_POST['ship_id']);
            }
            $_SESSION['shipqueue']['query'] = serialize($queueSearch);
        } else {
            $queueSearch = unserialize($_SESSION['shipqueue']['query'], ['allowed_classes' => [ShipQueueSearch::class]]);
        }

        $entries = $shipQueueRepository->adminSearchQueueItems($queueSearch);
        $nr = count($entries);
        if ($nr > 0) {
            echo "$nr Datens&auml;tze vorhanden<br/><br/>";
            if ($nr > 20) {
                echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" /> ";
                echo "<input type=\"button\" value=\"Aktualisieren\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=searchresults'\" /><br/><br/>";
            }

            echo "<table class=\"tbl\">";
            echo "<tr>";
            echo "<td class=\"tbltitle\">ID</td>";
            echo "<td class=\"tbltitle\">Schiff</td>";
            echo "<td class=\"tbltitle\">Anzahl</td>";
            echo "<td class=\"tbltitle\">Planet</td>";
            echo "<td class=\"tbltitle\">Spieler</td>";
            echo "<td class=\"tbltitle\">Start</td>";
            echo "<td class=\"tbltitle\">Ende</td>";
            echo "<td></td>";
            echo "</tr>";
            $check = array();
            $pid = 0;
            foreach ($entries as $shipQueueItem) {
                if ($pid > 0 && $pid !== $shipQueueItem->entityId) {
                    echo "<tr><td colspan=\"8\" style=\"height:3px;background:#000;\" class=\"tbldata\"></td></tr>";
                }
                $pid = $shipQueueItem->entityId;

                $error = false;

                // Planet gehört nicht dem Besitzer
                if ($shipQueueItem->userId !== $shipQueueItem->planetUserId) {
                    $error = true;
                    $errorMsg = "Planet geh&ouml;rt nicht dem Schiffbesitzer! Wird auf den Heimatplaneten verschoben";
                }

                if ($error)
                    $style = " style=\"color:#f30\"";
                elseif ($shipQueueItem->count === 0)
                    $style = " style=\"color:#999\"";
                else
                    $style = "";
                echo "<tr>";
                echo "<td class=\"tbldata\" $style>" . $shipQueueItem->id . "</a></td>";
                echo "<td class=\"tbldata\"$style " . mTT($shipQueueItem->shipName, "<b>Schiff-ID:</b> " . $shipQueueItem->shipId) . ">" . $shipQueueItem->shipName . "</td>";
                echo "<td class=\"tbldata\"$style>" . StringUtils::formatNumber($shipQueueItem->count) . "</td>";
                echo "<td class=\"tbldata\"$style " . mTT($shipQueueItem->planetName, "<b>Planet-ID:</b> " . $shipQueueItem->entityId . "<br/><b>Koordinaten:</b> " . $shipQueueItem->entity->sx . "/" . $shipQueueItem->entity->sy . " : " . $shipQueueItem->entity->cx . "/" . $shipQueueItem->entity->cy . " : " . $shipQueueItem->entity->pos) . ">" . StringUtils::cutString($shipQueueItem->planetName, 11) . "</td>";
                echo "<td class=\"tbldata\"$style " . mTT($shipQueueItem->userNick, "<b>User-ID:</b> " . $shipQueueItem->userId . "<br/><b>Punkte:</b> " . StringUtils::formatNumber($shipQueueItem->userPoints)) . ">" . StringUtils::cutString($shipQueueItem->userNick, 11) . "</td>";
                echo "<td class=\"tbldata\"$style>" . StringUtils::formatDate($shipQueueItem->startTime) . "</td>";
                echo "<td class=\"tbldata\"$style>" . StringUtils::formatDate($shipQueueItem->endTime) . "</td>";
                echo "<td class=\"tbldata\"$style>" . edit_button("?page=$page&sub=$sub&action=edit&id=" . $shipQueueItem->id);
                echo "</td>";
                echo "</tr>";
            }
            $check = NULL;
            echo "</table>";
            echo "<br/><input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" /> ";
            echo "<input type=\"button\" value=\"Aktualisieren\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=searchresults'\" />";
        } else {
            echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" />";
        }
    }

    //
    // Auftrag bearbeiten
    //
    elseif (isset($_GET['action']) && $_GET['action'] == "edit" && $_GET['id'] > 0) {
        // Änderungen speichern
        if (isset($_POST['save'])) {
            $queueItem = $shipQueueRepository->getQueueItem((int) $_GET['id']);
            if ($queueItem !== null) {
                $queueItem->count = (int) $_POST['queue_cnt'];
                $queueItem->startTime = (new \DateTime($_POST['queue_starttime']))->getTimestamp();
                $queueItem->endTime = (new \DateTime($_POST['queue_endtime']))->getTimestamp();
                $shipQueueRepository->saveQueueItem($queueItem);
            }
        }

        // Auftrag löschen
        elseif (isset($_POST['del'])) {
            $shipQueueRepository->deleteQueueItem((int) $_GET['id']);
            echo "Datensatz entfernt!<br/><br/>";
        }

        // Auftrag abschliessen
        elseif (isset($_POST['build_finish'])) {
            $queueItem = $shipQueueRepository->getQueueItem((int) $_GET['id']);
            if ($queueItem !== null) {
                $shipRepository->addShip($queueItem->shipId, $queueItem->count, $queueItem->userId, $queueItem->endTime);
                $shipQueueRepository->deleteQueueItem($queueItem->id);
            }
            echo "Bau abgeschlossen!<br/><br/>";
        }

        $queue = $shipQueueRepository->getQueueItem($_GET['id']);
        if ($queue !== null) {
            if ($queue->startTime > 0)
                $bst = date($config->get('admin_dateformat'), $queue->startTime);
            else
                $bst = "";
            if ($queue->endTime > 0)
                $bet = date($config->get('admin_dateformat'), $queue->endTime);
            else
                $bet = "";

            $userNick = $userRepository->getNick($queue->userId);
            $shipNames = $shipDataRepository->getShipNames(true);
            $planet = $planetRepository->find($queue->entityId);
            echo "<form action=\"?page=$page&sub=$sub&action=edit&id=" . $queue->id . "\" method=\"post\">";
            echo "<table class=\"tbl\">";
            echo "<tr><td class=\"tbltitle\">ID</td><td class=\"tbldata\">" . $queue->id . "</td></tr>";
            echo "<tr><td class=\"tbltitle\">Planet</td><td class=\"tbldata\">" . $planet->name . "</td></tr>";
            echo "<tr><td class=\"tbltitle\">Spieler</td><td class=\"tbldata\">" . $userNick . "</td></tr>";
            echo "<tr><td class=\"tbltitle\">Schiff</td><td class=\"tbldata\">" . $shipNames[$queue->shipId] . "</td></tr>";
            echo "<tr><td class=\"tbltitle\">Anzahl</td><td class=\"tbldata\"><input type=\"text\" name=\"queue_cnt\" value=\"" . $queue->count . "\" size=\"5\" maxlength=\"20\" /></td></tr>";
            echo "<tr><td class=\"tbltitle\">Baustart</td><td class=\"tbldata\">
                <input type=\"text\" id=\"shiplist_build_start_time\" name=\"queue_starttime\" value=\"$bst\" size=\"20\" maxlength=\"30\" />
                <input type=\"button\" value=\"Jetzt\" onclick=\"document.getElementById('shiplist_build_start_time').value='" . date("Y-d-m h:i") . "'\" /></td></tr>";
            echo "<tr><td class=\"tbltitle\">Bauende</td><td class=\"tbldata\">
                <input type=\"text\" id=\"shiplist_build_end_time\" name=\"queue_endtime\" value=\"$bet\" size=\"20\" maxlength=\"30\" /></td></tr>";
            echo "<tr><td class=\"tbltitle\">Bauzeit pro Schiff</td><td class=\"tbldata\">" . StringUtils::formatTimespan($queue->objectTime) . "</td></tr>";
            echo "</table><br/>";
            echo "<input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />&nbsp;";
            echo "<input type=\"submit\" name=\"build_finish\" value=\"Bau fertigstellen\" />&nbsp;";
            echo "<input type=\"submit\" name=\"del\" value=\"L&ouml;schen\" class=\"button\" onclick=\"return confirm('Schiffe wirklich l&ouml;schen?')\" />&nbsp;";
            echo "<hr/>";
            echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" />&nbsp;";
            echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&sub=$sub';\" />";
            echo "</form>";
        } else {
            echo "Dieser Datensatz existiert nicht mehr!<br/><br/>";
            echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" />&nbsp;";
        }
    }


    //
    // Suchmaske Schiffaufträge
    //
    else {
        unset($_SESSION['shipqueue']['query']);

        // Schiffe laden
        /** @var ShipDataRepository $shipRepository */
        $shipRepository = $app[ShipDataRepository::class];
        $shipNames = $shipRepository->getShipNames(true);

        // Suchmaske
        $twig->addGlobal("subtitle", "Suchmaske");
        echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
        echo "<table class=\"tbl\">";
        echo "<tr><td class=\"tbltitle\">Planet ID</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td>";
        echo "<tr><td class=\"tbltitle\">Planetname</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" value=\"\" size=\"20\" maxlength=\"250\" /> ";
        echo "</td></tr>";
        echo "<tr><td class=\"tbltitle\">Spieler ID</td><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
        echo "<tr><td class=\"tbltitle\">Spieler Nick</td><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" /> ";
        echo "</td></tr>";
        echo "<tr><td class=\"tbltitle\">Schiff</td><td class=\"tbldata\"><select name=\"ship_id\"><option value=\"\"><i>---</i></option>";
        foreach ($shipNames as $shipId => $shipName) {
            echo "<option value=\"" . $shipId . "\">" . $shipName . "</option>";
        }
        echo "</select></td>";
        echo "</table>";
        echo "<p><input type=\"submit\" class=\"button\" name=\"shipqueue_search\" value=\"Suche starten\" /></p></form>";
        $tblcnt = $shipQueueRepository->count();
        echo "<p>Es sind " . StringUtils::formatNumber($tblcnt) . " Eintr&auml;ge in der Datenbank vorhanden.</p>";
    }
}

/**************
 * Schiffliste *
 **************/
else {
    $twig->addGlobal("title", "Schiffliste");

    // Schiffe laden
    $shipNames = $shipDataRepository->getShipNames(true);
    $tblcnt = $shipRepository->count();

    // Hinzufügen
    echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\" id=\"selector\" name=\"selector\">";
    echo "<table>";

    //Sonnensystem

    echo "<tr><th class=\"tbltitle\">Sonnensystem</th><td class=\"tbldata\">
            <select name=\"cell_sx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showShipsOnPlanet',1);\">";
    echo "<option value=\"0\">Sektor X</option>";
    for ($x = 1; $x <= $config->param1Int('num_of_sectors'); $x++)
        echo "<option value=\"$x\">$x</option>";
    echo "</select>/<select name=\"cell_sy\"  onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showShipsOnPlanet',1);\">";
    echo "<option value=\"0\">Sektor Y</option>";
    for ($x = 1; $x <= $config->param2Int('num_of_sectors'); $x++)
        echo "<option value=\"$x\">$x</option>";
    echo "</select> : <select name=\"cell_cx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showShipsOnPlanet',1);\">";
    echo "<option value=\"0\">Zelle X</option>";
    for ($x = 1; $x <= $config->param1Int('num_of_cells'); $x++)
        echo "<option value=\"$x\">$x</option>";
    echo "</select>/<select name=\"cell_cy\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showShipsOnPlanet',1);\">";
    echo "<option value=\"0\">Zelle Y</option>";
    for ($x = 1; $x <= $config->param2Int('num_of_cells'); $x++)
        echo "<option value=\"$x\">$x</option>";
    echo "</select></td></tr>";


    //User
    echo "<tr><th class=\"tbltitle\">User:</th><td class=\"tbldata\">";
    echo "<input type=\"text\" name=\"userlist_nick\" id=\"userlist_nick\" value=\"\" autocomplete=\"off\" size=\"30\" maxlength=\"30\" onkeyup=\"xajax_searchUserList(this.value,'showShipsOnPlanet');\"><br>
            <div id=\"userlist\">&nbsp;</div>";
    echo "</td></tr>";

    //Planeten
    echo "<tr><th class=\"tbltitle\">Kolonien:</th><td class=\"tbldata\" id=\"planetSelector\">Sonnensystem oder User w&auml;hlen...</td></tr>";

    //Schiffe Hinzufügen
    echo "<tr name=\"addObject\" id=\"addObject\" style=\"display:none;\"><th class=\"tbltitle\">Hinzuf&uuml;gen:</th><td class=\"tbldata\">
            <input type=\"text\" name=\"shiplist_count\" value=\"1\" size=\"7\" maxlength=\"10\" />
            <select name=\"ship_id\">";
    foreach ($shipNames as $shipId => $shipName) {
        echo "<option value=\"" . $shipId . "\">" . $shipName . "</option>";
    }
    echo "</select> &nbsp;
            <input type=\"button\" onclick=\"showLoaderPrepend('shipsOnPlanet');xajax_addShipToPlanet(xajax.getFormValues('selector'));\" value=\"Hinzuf&uuml;gen\" /></td></tr>";

    //Vorhandene Schiffe
    tableEnd();
    echo "<br/>";

    echo "<div id=\"shipsOnPlanet\" style=\"width:700px\"></div>";

    echo "</form>";



    //Focus
    echo "<script type=\"text/javascript\">document.getElementById('userlist_nick').focus();</script>";

    //Add User
    if (searchQueryArray($sa, $so)) {
        if (isset($sa['user_nick'])) {
            echo "<script type=\"text/javascript\">document.getElementById('userlist_nick').value=\"" . $sa['user_nick'][1] . "\";xajax_searchUserList('" . $sa['user_nick'][1] . "','showShipsOnPlanet');</script>";
        }
    }

    echo "<br/>Es sind <b>" . StringUtils::formatNumber($tblcnt) . "</b> Eintr&auml;ge in der Datenbank vorhanden.";
}
