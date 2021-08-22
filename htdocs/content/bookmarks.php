<?PHP

use EtoA\Bookmark\BookmarkOrder;
use EtoA\Bookmark\BookmarkRepository;
use EtoA\Bookmark\FleetBookmarkRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\Universe\Entity\EntityCoordinates;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\BaseResources;
use EtoA\User\UserPropertiesRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var EntityRepository $entityRepository */
$entityRepository = $app[EntityRepository::class];
/** @var UserUniverseDiscoveryService $userUniverseDiscoveryService */
$userUniverseDiscoveryService = $app[UserUniverseDiscoveryService::class];
/** @var FleetBookmarkRepository $fleetBookmarkRepository */
$fleetBookmarkRepository = $app[FleetBookmarkRepository::class];
/** @var BookmarkRepository $bookmarkRepository */
$bookmarkRepository = $app[BookmarkRepository::class];
/** @var PlanetRepository $planetRepository */
$planetRepository = $app[PlanetRepository::class];

/** @var UserPropertiesRepository $userPropertiesRepository */
$userPropertiesRepository = $app[UserPropertiesRepository::class];

$properties = $userPropertiesRepository->getOrCreateProperties($cu->id);

$mode = (isset($_GET['mode']) && $_GET['mode'] != "" && ctype_alpha($_GET['mode'])) ? $_GET['mode'] : 'target';

// save current planet for use in xajax functions
$_SESSION['currentEntity'] = serialize($cp);

$user = $userRepository->getUser($cu->id);

// Header & Menu
echo "<h1>Favoriten</h1>";
show_tab_menu(
    "mode",
    array(
        "target" => "Zielfavoriten",
        "fleet" => "Flottenfavoriten",
        "new" => "Neuer Flottenfavorit"
    )
);
echo '<br/>';

// Save edited or new fleet bookmarks
// max length (in database) of action is 15 chars
if ((isset($_POST['submitEdit']) || isset($_POST['submitNew'])) && (isset($_POST['action']) && ctype_alpha($_POST['action']) && strlen($_POST['action']) <= 15)) {
    $sx = (int) $_POST['sx'];
    $cx = (int) $_POST['cx'];
    $sy = (int) $_POST['sy'];
    $cy = (int) $_POST['cy'];
    $pos = (int) $_POST['pos'];

    // Check entity
    $entity = $entityRepository->findByCoordinates(new EntityCoordinates($sx, $sy, $cx, $cy, $pos));
    if ($entity !== null) {
        //Check discovered for fleet bookmarks, bugfix by river
        $absX = (($sx - 1) * $config->param1Int('num_of_cells')) + $cx;
        $absY = (($sy - 1) * $config->param2Int('num_of_cells')) + $cy;
        if ($userUniverseDiscoveryService->discovered($user, $absX, $absY)) {
            // Create shipstring
            $addships = "";
            foreach ($_POST['ship_count'] as $sid => $count) {
                if ($addships == "")
                    $addships .= (int) $sid . ":" . nf_back($count);
                else
                    $addships .= "," . (int) $sid . ":" . nf_back($count);
            }

            $speed = max(1, min(100, (int) nf_back($_POST['value'])));

            // Create restring
            $freight = new BaseResources();
            $freight->metal = (int) nf_back_sign($_POST['res0']);
            $freight->crystal = (int) nf_back_sign($_POST['res1']);
            $freight->plastic = (int) nf_back_sign($_POST['res2']);
            $freight->fuel = (int) nf_back_sign($_POST['res3']);
            $freight->food = (int) nf_back_sign($_POST['res4']);
            $freight->people = (int) nf_back_sign($_POST['res5']);

            $fetch = new BaseResources();
            $fetch->metal = max(0, (int) nf_back($_POST['fetch0']));
            $fetch->crystal = max(0, (int) nf_back($_POST['fetch1']));
            $fetch->plastic = max(0, (int) nf_back($_POST['fetch2']));
            $fetch->fuel = max(0, (int) nf_back($_POST['fetch3']));
            $fetch->food = max(0, (int) nf_back($_POST['fetch4']));
            $fetch->people = max(0, (int) nf_back($_POST['fetch5']));

            // Save new bookmark
            if (isset($_POST['submitNew'])) {
                $fleetBookmarkRepository->add($user->id, $_POST['name'], $entity->id, $addships, $freight, $fetch, $_POST['action'], $speed);

                success_msg("Der Favorit wurde hinzugef&uuml;gt!");
            } elseif (isset($_POST['submitEdit'])) {
                // Update edidet bookmark
                $fleetBookmarkRepository->update((int) $_POST['id'], $user->id, $_POST['name'], $entity->id, $addships, $freight, $fetch, $_POST['action'], $speed);

                success_msg("Der Favorit wurde gespeichert!");
            }
        } else {
            error_msg('Ziel wurde noch nicht entdeckt.');
        }
    } else {
        error_msg("Es existiert kein Objekt an den angegebenen Koordinaten!");
    }
}

// Delete fleet bookmark
if (isset($_GET['del']) && intval($_GET['del']) > 0) {
    $bmid = intval($_GET['del']);

    if ($fleetBookmarkRepository->remove($bmid, $user->id)) {
        success_msg("Gelöscht");
    }
}

if ($mode == "fleet") {
    // Load fleet bookmarks
    $bookmarks = $fleetBookmarkRepository->getForUser($user->id);
    if (count($bookmarks) > 0) {
        /** @var \EtoA\Ship\ShipDataRepository $shipDataRepository */
        $shipDataRepository = $app[\EtoA\Ship\ShipDataRepository::class];
        $shipNames = $shipDataRepository->getShipNames(true);

        tableStart("Gespeicherte Favoriten");
        echo "<tr>
                    <th>Name</th>
                    <th colspan=\"2\">Ziel</th>
                    <th>Aktion</th>
                    <th>Schiffe</th>
                    <th>Aktionen</th>
            </tr>";
        foreach ($bookmarks as $bookmark) {
            $ent = Entity::createFactoryById($bookmark->targetId);
            $ac = FleetAction::createFactory($bookmark->action);

            echo "<tr>
                    <td>" . BBCodeUtils::toHTML($bookmark->name) . "</td>
                    <td style=\"width:40px;background:#000\"><img src=\"" . $ent->imagePath() . "\" /></td>
                    <td>" . $ent . "<br/>(" . $ent->entityCodeString() . ")</td>
                    <td>" . $ac . "</td>
                    <td>";

            // Creating ship-print-string
            foreach ($bookmark->ships as $shipId => $count) {
                echo StringUtils::formatNumber($count) . " " . $shipNames[$shipId] . "<br />";
            }
            echo "</td>
                    <td id=\"fleet_bm_actions_" . $bookmark->id . "\" class=\"tbldata\">
                        <a href=\"javascript:;\" onclick=\"$('#fleet_bm_actions_" . $bookmark->id . "').html('Flotte wird gestartet...');xajax_launchBookmarkProbe(" . $bookmark->id . ");\">Starten</a>
                        <a href=\"?page=$page&amp;mode=new&amp;edit=" . $bookmark->id . "\">Bearbeiten</a>
                        <a href=\"?page=$page&amp;mode=$mode&amp;del=" . $bookmark->id . "\" onclick=\"return confirm('Soll dieser Favorit wirklich gel&ouml;scht werden?');\">Entfernen</a>
                    </td>
                </tr>";
        }
        tableEnd();

        // Create box for future events
        echo '<div id="fleet_info_box" style="display:none;">';
        iBoxStart("Flotten");
        echo '<div id="fleet_info"></div>';
        iBoxEnd();
        echo '</div>';
    } else {
        info_msg("Noch keine Favoriten vorhanden!");
    }
} elseif ($mode == "new") {
    // Creat array for data
    $data = [
        'id' => null,
        'name' => '',
        'res' => new BaseResources(),
        'fetch' => new BaseResources(),
        'ships' => [],
        'speed' => 100,
        'action' => 'flight',
    ];
    $new = false;

    $_SESSION['bookmarks'] = array('added');
    $_SESSION['bookmarks']['added'] = array();

    $entity = null;
    if (isset($_GET['edit']) && intval($_GET['edit']) > 0) {
        $bmid = intval($_GET['edit']);

        // Load bookmark data
        $bookmark = $fleetBookmarkRepository->get($bmid, $user->id);
        if ($bookmark !== null) {
            $entity = $entityRepository->getEntity($bookmark->targetId);
            if ($entity !== null) {
                // Fill data array
                $data['res'] = $bookmark->freight;
                $data['fetch'] = $bookmark->fetch;
                $data['ships'] = $bookmark->ships;
                $data['speed'] = $bookmark->speed;
                $data['name'] = $bookmark->name;
                $data['id'] = $bookmark->id;
                $data['action'] = $bookmark->action;
            } else {
                error_msg("Ziel wurde nicht gefunden!");
            }
        } else {
            error_msg("Flottenfavorit konnte nicht gefunden werden!");
        }
    }

    // If data array is without data create a new one
    if ($entity === null) {
        $new = true;

        $entity = $entityRepository->findByCoordinates(new EntityCoordinates(1, 1, 1, 1, 0));
    }

    echo '<form id="bookmarkForm" action="?page=' . $page . '&amp;mode=fleet" method="post">';
    checker_init();
    echo '<input type="hidden" name="id" value="' . $data['id'] . '" />';

    tableStart('Allgemeines');
    echo '<tr>
            <th>Name</th>
            <td><input type="text" name="name" id="name" value="' . $data['name'] . '" autocomplete="off" size="30" maxlength="30" /></td>
        </tr>
        <tr>
            <th>Flottenaktion</th>
            <td>
                <select name="action">';
    foreach (FleetAction::getAll(true) as $ai) {
        echo '<option value="' . $ai->code() . '" style="color:' . $ai->color() . '"';
        if ($data['action'] == $ai->code())
            echo ' selected="selected" ';
        echo '>' . $ai->name() . '</option>';
    }
    echo '		</select>
            </td>
        </tr>
        <tr>
            <td colspan="2">Wichtig: Die Flotte wird nur starten, falls die Schiffe und das Ziel die gewählte Aktion unterstützen. Es muss pro Schiffstyp mindestens ein Schiff vorhanden sein, damit die Flotte startet. Bei den Rohstoffen wird Rohstoff für Rohstoff jeweils das Maximum eingeladen.</td>
        </tr>';
    tableEnd();

    // Ship databox
    $cnt = 0;
    tableStart('Schiffe', 0, "", 'bookmarkShiplistInputTable');
    echo "<tbody></tbody>";
    tableEnd();

    echo "<script>$(function(){";
    foreach ($data['ships'] as $id => $count) {
?>
        fleetBookmarkAddShipToList(<?PHP echo "'" . $id . "', '" . $count . "'"; ?>);
<?PHP
    }
    echo "});</script>";

    // Ship addbox
    tableStart('Schiffe hinzufügen', 0, '', 'shipadder');
    echo '<tr>
            <th colspan="2">Schiffname:</th>
            <td>
                <input type="text" name="shipname" id="shipname" value="" autocomplete="off" size="30" maxlength="30" onkeyup="xajax_searchShipList(this.value);">
                <br>
                <div id="shiplist">&nbsp;</div>
            </td>
            <td ';
    if ($cnt == 0) echo 'style="display:none;"';
    echo 'id="saveShips">
                <input type="button" value="Keine weiteren Schiffe hinzufügen" onclick="toggleBox(\'shipadder\');toggleBox(\'targetBox\');xajax_bookmarkTargetInfo(xajax.getFormValues(\'bookmarkForm\'));" />
        </tr>';
    tableEnd();

    // Show target selector
    tableStart('Zielwahl', 0, 'nondisplay', 'targetBox');

    // Manuel selector
    echo '<tr id="manuelselect">
            <th width="25%">Manuelle Eingabe:</th>
            <td colspan="2" width="75%">
                <input type="text"
                    id="sx"
                    name="sx"
                    size="1"
                    maxlength="1"
                    value="' . $entity->sx . '"
                    title="Sektor X-Koordinate"
                    autocomplete="off"
                    onfocus="this.select()"
                    onclick="this.select()"
                    onkeydown="detectChangeRegister(this,\'t1\');"
                    onkeyup="if (detectChangeTest(this,\'t1\')) { showLoader(\'targetinfo\');xajax_bookmarkTargetInfo(xajax.getFormValues(\'bookmarkForm\')); }"
                    onkeypress="return nurZahlen(event)"
                />&nbsp;/&nbsp;
                <input type="text"
                    id="sy"
                    name="sy"
                    size="1"
                    maxlength="1"
                    value="' . $entity->sy . '"
                    title="Sektor Y-Koordinate"
                    autocomplete="off"
                    onfocus="this.select()"
                    onclick="this.select()"
                    onkeydown="detectChangeRegister(this,\'t2\');"
                    onkeyup="if (detectChangeTest(this,\'t2\')) { showLoader(\'targetinfo\');xajax_bookmarkTargetInfo(xajax.getFormValues(\'bookmarkForm\')); }"
                    onkeypress="return nurZahlen(event)"
                />&nbsp;&nbsp;:&nbsp;&nbsp;
                <input type="text"
                    id="cx"
                    name="cx"
                    size="2"
                    maxlength="2"
                    value="' . $entity->cx . '"
                    title="Zelle X-Koordinate"
                    autocomplete="off"
                    onfocus="this.select()"
                    onclick="this.select()"
                    onkeydown="detectChangeRegister(this,\'t3\');"
                    onkeyup="if (detectChangeTest(this,\'t3\')) { showLoader(\'targetinfo\');xajax_bookmarkTargetInfo(xajax.getFormValues(\'bookmarkForm\')); }"
                    onkeypress="return nurZahlen(event)"
            />&nbsp;/&nbsp;
            <input type="text"
                    id="cy"
                    name="cy"
                    size="2"
                    maxlength="2"
                    value="' . $entity->cy . '"
                    title="Zelle Y-Koordinate"
                    autocomplete="off"
                    onfocus="this.select()"
                    onclick="this.select()"
                    onkeydown="detectChangeRegister(this,\'t4\');"
                    onkeyup="if (detectChangeTest(this,\'t4\')) { showLoader(\'targetinfo\');xajax_bookmarkTargetInfo(xajax.getFormValues(\'bookmarkForm\')); }"
                    onkeypress="return nurZahlen(event)"
            />&nbsp;&nbsp;:&nbsp;&nbsp;
            <input type="text"
                    id="pos"
                    name="pos"
                    size="2"
                    maxlength="2"
                    value="' . $entity->pos . '"
                    title="Position des Planeten im Sonnensystem"
                    autocomplete="off"
                    onfocus="this.select()"
                    onclick="this.select()"
                    onkeydown="detectChangeRegister(this,\'t5\');"
                    onkeyup="if (detectChangeTest(this,\'t5\')) { showLoader(\'targetinfo\');xajax_bookmarkTargetInfo(xajax.getFormValues(\'bookmarkForm\')); }"
                    onkeypress="return nurZahlen(event)"
            /></td></tr>';

    // Bookmark selector
    echo '<tr id="bookmarkselect">
            <th width="25%">Zielfavoriten:</th>
            <td colspan="2" width="75%" align="left">
                <select name="bookmarks"
                        id="bookmarks"
                        onchange="xajax_bookmarkBookmark(xajax.getFormValues(\'bookmarkForm\'));"
                >\n
                    <option value="0">Wählen...</option>';

    $planets = $planetRepository->getUserPlanetsWithCoordinates($user->id);
    if (count($planets) > 0) {
        foreach ($planets as $planet) {
            echo '<option value="' . $planet->id . '">Eigener Planet: ' . $planet->toString() . '</option>\n';
        }
    }

    $bookmarks = $bookmarkRepository->findForUser($user->id);
    if (count($bookmarks) > 0) {
        echo '<option value="0">-------------------------------</option>\n';

        foreach ($bookmarks as $bookmark) {
            $ent = Entity::createFactory($bookmark->entityCode, $bookmark->entityId);
            echo '<option value="' . $ent->id() . '">' . $ent->entityCodeString() . ' - ' . $ent . ' (' . $bookmark->comment . ')</option>\n';
        }
    }
    echo '		</select>
            </td>
        </tr>
        <tr>
            <th width="25%"><b>Ziel-Informationen:</b></th>
            <td colspan="2" id="targetinfo" style="padding:2px 2px 3px 6px;background:#000;color:#fff;height:47px;">
                <img src="images/loading.gif" alt="Loading" /> Lade Daten...
            </td>
        </tr>
        <tr>
            <th>Speedfaktor:</th>
            <td width="75%">
                <div id="slider" style="margin:10px;"></div>
            </td>
            <td style="background:#000;vertical-align:middle;text-align:center;">
                <input type="text" id="value" name="value" value="' . $data['speed'] . ' %" size="4" style="border:0"/>
            </td>
        </tr>';
    tableEnd();

    tableStart('Ladung', 0, 'nondisplay', 'resbox');
    echo '<tr>
            <th>&nbsp;</th>
            <th>Fracht</th>
            <th>Abholauftrag</th>
        </tr>
        <tr>
            <th>' . RES_ICON_METAL . '' . RES_METAL . '</th>
            <td>
                <input type="text" name="res0" id="res0" value="' . $data['res']->metal . '" size="16" onkeyup="FormatSignedNumber(this.id,this.value, \'\', \'\', \'\');" />
            </td>
            <td>
                <input type="text" name="fetch0" id="fetch0" value="' . $data['fetch']->metal . '" size="16" onkeyup="FormatNumber(this.id,this.value, \'\', \'\', \'\');" />
            </td>
        </tr>
        <tr>
            <th>' . RES_ICON_CRYSTAL . '' . RES_CRYSTAL . '</th>
            <td>
                <input type="text" name="res1" id="res1" value="' . $data['res']->crystal . '" size="16" onkeyup="FormatSignedNumber(this.id,this.value, \'\', \'\', \'\');" />
            </td>
            <td>
                <input type="text" name="fetch1" id="fetch1" value="' . $data['fetch']->crystal . '" size="16" onkeyup="FormatNumber(this.id,this.value, \'\', \'\', \'\');" />
            </td>
        </tr>
        <tr>
            <th>' . RES_ICON_PLASTIC . '' . RES_PLASTIC . '</th>
            <td>
                <input type="text" name="res2" id="res2" value="' . $data['res']->plastic . '" size="16" onkeyup="FormatSignedNumber(this.id,this.value, \'\', \'\', \'\');" />
            </td>
            <td>
                <input type="text" name="fetch2" id="fetch2" value="' . $data['fetch']->plastic . '" size="16" onkeyup="FormatNumber(this.id,this.value, \'\', \'\', \'\');" />
            </td>
        </tr>
        <tr>
            <th>' . RES_ICON_FUEL . '' . RES_FUEL . '</th>
            <td>
                <input type="text" name="res3" id="res3" value="' . $data['res']->fuel . '" size="16" onkeyup="FormatSignedNumber(this.id,this.value, \'\', \'\', \'\');" />
            </td>
            <td>
                <input type="text" name="fetch3" id="fetch3" value="' . $data['fetch']->fuel . '" size="16" onkeyup="FormatNumber(this.id,this.value, \'\', \'\', \'\');" />
            </td>
        </tr>
        <tr>
            <th>' . RES_ICON_FOOD . '' . RES_FOOD . '</th>
            <td>
                <input type="text" name="res4" id="res4" value="' . $data['res']->food . '" size="16" onkeyup="FormatSignedNumber(this.id,this.value, \'\', \'\', \'\');" />
            </td>
            <td>
                <input type="text" name="fetch4" id="fetch4" value="' . $data['fetch']->food . '" size="16" onkeyup="FormatNumber(this.id,this.value, \'\', \'\', \'\');" />
            </td>
        </tr>
        <tr>
            <th>' . RES_ICON_PEOPLE . 'Passagiere</th>
            <td>
                <input type="text" name="res5" id="res5" value="' . $data['res']->people . '" size="16" onkeyup="FormatSignedNumber(this.id,this.value, \'\', \'\', \'\');" />
            </td>
            <td>
                <input type="text" name="fetch5" id="fetch5" value="' . $data['fetch']->people . '" size="16" onkeyup="FormatNumber(this.id,this.value, \'\', \'\', \'\');" />
            </td>
        </tr>';
    tableEnd();

    jsSlider("slider", $data['speed']);

    echo '<div id="submit" style="display:none;">';
    if ($new)
        echo '<input type="submit" value="Speichern" name="submitNew" id="submitNew" />';
    else
        echo '<input type="submit" value="Speichern" name="submitEdit" id="submitEdit" />';
    echo '</div>';
    echo "</form>";
} else {
    /****************************
     *  Sortiereingaben speichern *
     ****************************/
    if (count($_POST) > 0 && isset($_POST['sort_submit']) && isset($_POST['sort_value']) && StringUtils::hasAlphaDotsOrUnderlines($_POST['sort_value']) && isset($_POST['sort_way']) && StringUtils::hasAlphaDotsOrUnderlines($_POST['sort_way'])) {
        $properties->itemOrderBookmark = $_POST['sort_value'];
        $properties->itemOrderWay = $_POST['sort_way'];
        $userPropertiesRepository->storeProperties($cu->id, $properties);
    }

    // Bearbeiten
    if (isset($_GET['edit']) && intval($_GET['edit']) > 0) {
        $bmid = intval($_GET['edit']);

        echo "<form action=\"?page=$page\" method=\"post\">";
        checker_init();
        $bookmark = $bookmarkRepository->getBookmark($bmid, $user->id);
        if ($bookmark !== null) {
            $ent = Entity::createFactory($bookmark->entityCode, $bookmark->entityId);

            tableStart("Favorit bearbeiten");
            echo "<tr>
                            <th>Koordinaten</th>
                            <td>" . $ent->entityCodeString() . " - " . $ent . "</td>
                        </tr>
                        <tr>
                            <th>Kommentar</th>
                            <td>
                                <textarea name=\"bookmark_comment\" rows=\"3\" cols=\"60\">" . stripslashes($bookmark->comment) . "</textarea>
                            </td>
                        </tr>";
            tableEnd();

            echo "<input type=\"hidden\" name=\"bookmark_id\" value=\"" . $bmid . "\" />";
            echo "<input type=\"submit\" value=\"Speichern\" name=\"submit_edit_target\" /> &nbsp; ";
        } else {
            error_msg("Datensatz nicht gefunden!");
        }
        echo " <input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
        echo "</form>";
    } else {
        // Bearbeiteter Favorit speichern
        if (isset($_POST['submit_edit_target']) && isset($_POST['bookmark_comment']) && isset($_POST['bookmark_id']) && intval($_POST['bookmark_id']) > 0 && checker_verify()) {
            $bmid = intval($_POST['bookmark_id']);

            if ($bookmarkRepository->updateComment($bmid, $user->id, $_POST['bookmark_comment'])) {
                success_msg("Gespeichert");
            }
        }

        // Favorit löschen
        if (isset($_GET['del']) && intval($_GET['del']) > 0) {
            $bmid = intval($_GET['del']);

            if ($bookmarkRepository->remove($bmid, $user->id)) {
                success_msg("Gelöscht");
            }
        }

        // Neuen Favorit speichern
        if (isset($_POST['submit_target']) && $_POST['submit_target'] != "" && checker_verify()) {
            $sx = intval($_POST['sx']);
            $cx = intval($_POST['cx']);
            $sy = intval($_POST['sy']);
            $cy = intval($_POST['cy']);
            $pos = intval($_POST['pos']);

            $absX = (($sx - 1) * $config->param1Int('num_of_cells')) + $cx;
            $absY = (($sy - 1) * $config->param2Int('num_of_cells')) + $cy;
            if ($userUniverseDiscoveryService->discovered($user, $absX, $absY)) {
                $entity = $entityRepository->findByCoordinates(new EntityCoordinates($sx, $sy, $cx, $cy, $pos));
                if ($entity !== null) {
                    if (!$bookmarkRepository->hasEntityBookmark($user->id, $entity->id)) {
                        $bookmarkRepository->add($user->id, $entity->id, $_POST['bookmark_comment']);
                        success_msg("Der Favorit wurde hinzugef&uuml;gt!");
                    } else {
                        error_msg("Dieser Favorit existiert schon!");
                    }
                } else {
                    error_msg("Es existiert kein Objekt an den angegebenen Koordinaten!");
                }
            } else {
                error_msg("Das Gebiet ist noch nicht erkundet!");
            }
        }

        // Neuer Favorit speichern (id gegeben
        if (isset($_GET['add']) && intval($_GET['add']) > 0) {
            $bmid = intval($_GET['add']);

            $entity = $entityRepository->getEntity($bmid);
            if ($entity !== null) {
                if (!$bookmarkRepository->hasEntityBookmark($user->id, $entity->id)) {
                    $bookmarkRepository->add($user->id, $entity->id, '-');

                    success_msg("Der Favorit wurde hinzugef&uuml;gt!");
                } else {
                    error_msg("Dieser Favorit existiert schon!");
                }
            } else {
                error_msg("Es existiert kein Objekt an den angegebenen Koordinaten!!");
            }
        }

        // Add-Bookmakr-Box
        iBoxStart("Favorit hinzuf&uuml;gen");
        echo "<form action=\"?page=$page\" method=\"post\">";
        checker_init();
        echo "<select name=\"sx\">";
        for ($x = 1; $x <= $config->param1Int('num_of_sectors'); $x++) {
            echo "<option value=\"$x\">$x</option>";
        }
        echo "</select> / <select name=\"sy\">";
        for ($y = 1; $y <= $config->param2Int('num_of_sectors'); $y++) {
            echo "<option value=\"$y\">$y</option>";
        }
        echo "</select> : <select name=\"cx\">";
        for ($x = 1; $x <= $config->param1Int('num_of_cells'); $x++) {
            echo "<option value=\"$x\">$x</option>";
        }
        echo "</select> / <select name=\"cy\">";
        for ($y = 1; $y <= $config->param2Int('num_of_cells'); $y++) {
            echo "<option value=\"$y\">$y</option>";
        }
        echo "</select> : <select name=\"pos\">";
        for ($y = 0; $y <= $config->param2Int('num_planets'); $y++) {
            echo "<option value=\"$y\">$y</option>";
        }
        echo "</select> &nbsp; ";
        echo "<input type=\"text\" name=\"bookmark_comment\" size=\"20\" maxlen=\"200\" value=\"Kommentar\" onfocus=\"if (this.value=='Kommentar') this.value=''\" /> &nbsp;";
        echo "<input type=\"submit\" value=\"Speichern\" name=\"submit_target\" />";

        iBoxEnd();

        // List bookmarks
        $bookmarks = $bookmarkRepository->findForUser($user->id, new BookmarkOrder($properties->itemOrderBookmark , $properties->itemOrderWay));
        if (count($bookmarks) > 0) {
            tableStart("Gespeicherte Favoriten");
            /*************
             * Sortierbox *
             *************/
            //Legt Sortierwerte in einem Array fest
            echo "<tr>
                    <td colspan=\"6\" style=\"text-align:center;\">
                        <select name=\"sort_value\">";
            foreach (BookmarkOrder::ALL_ORDERS as $value => $name) {
                echo "<option value=\"" . $value . "\"";
                if ($properties->itemOrderBookmark == $value) {
                    echo " selected=\"selected\"";
                }
                echo ">" . $name . "</option>";
            }
            echo "</select>

                <select name=\"sort_way\">";

            //Aufsteigend
            echo "<option value=\"ASC\"";
            if ($properties->itemOrderWay == 'ASC') echo " selected=\"selected\"";
            echo ">Aufsteigend</option>";

            //Absteigend
            echo "<option value=\"DESC\"";
            if ($properties->itemOrderWay == 'DESC') echo " selected=\"selected\"";
            echo ">Absteigend</option>";

            echo "</select>

                        <input type=\"submit\" class=\"button\" name=\"sort_submit\" value=\"Sortieren\"/>
                    </td>
                </tr>";
            echo "<tr>
                            <th colspan=\"2\">Typ</th>
                            <th>Koordinaten</th>
                            <th>Besitzer</th>
                            <th>Kommentar</th>
                            <th>Aktionen</th>
                        </tr>";
            foreach ($bookmarks as $bookmark) {
                $ent = Entity::createFactory($bookmark->entityCode, $bookmark->entityId);

                echo "<tr>
                                    <td style=\"width:40px;background:#000\"><img src=\"" . $ent->imagePath() . "\" /></td>
                                    <td>" . $ent->entityCodeString() . "</td>
                                    <td><a href=\"?page=cell&amp;id=" . $ent->cellId() . "&amp;hl=" . $ent->id() . "\">" . $ent . "</a></td>
                                    <td>" . $ent->owner() . "</td>
                                    <td>" . BBCodeUtils::toHTML($bookmark->comment) . "</td>
                                    <td>";

                // Action icons added by river, Info link moved to coordinates (above)

                // Flotte
                if ($ent->entityCode() == 'p' || $ent->entityCode() == 'a' || $ent->entityCode() == 'w' || $ent->entityCode() == 'n' || $ent->entityCode() == 'e') {
                    echo "<a href=\"?page=haven&amp;target=" . $ent->id() . "\" title=\"Flotte hinschicken\">" . icon('fleet') . "</a> ";
                }

                if ($ent->entityCode() == 'p') {
                    // Nachrichten-Link
                    if ($ent->ownerId() > 0 && $user->id != $ent->ownerId()) {
                        echo "<a href=\"?page=messages&amp;mode=new&amp;message_user_to=" . $ent->ownerId() . "\" title=\"Nachricht senden\">" . icon("mail") . "</a> ";
                    }

                    // Ausspionieren, Raketen, Krypto
                    if ($user->id != $ent->ownerId()) {
                        // Besiedelter Planet
                        if ($ent->ownerId() > 0) {
                            echo "<a href=\"javascript:;\" onclick=\"xajax_launchSypProbe(" . $ent->id() . ");\" title=\"Ausspionieren\">" . icon("spy") . "</a>";
                            echo "<a href=\"?page=missiles&amp;target=" . $ent->id() . "\" title=\"Raketenangriff starten\">" . icon("missile") . "</a> ";
                            echo "<a href=\"?page=crypto&amp;target=" . $ent->id() . "\" title=\"Flottenbewegungen analysieren\">" . icon("crypto") . "</a> ";
                        }
                    }
                }

                // Analysieren, letzten Analysebericht als Popup anzeigen
                if (in_array("analyze", $ent->allowedFleetActions(), true)) {
                    if ($properties->showCellreports) {
                        $reports = Report::find(array("type" => "spy", "user_id" => $user->id, "entity1_id" => $ent->id()), "timestamp DESC", 1, 0, true);
                        if (count($reports) > 0) {
                            $r = array_pop($reports);
                            echo "<span " . tm($r->subject, $r . "<br style=\"clear:both\" />") . "><a href=\"javascript:;\" onclick=\"xajax_launchAnalyzeProbe(" . $ent->id() . ");\" title=\"Analysieren\">" . icon("spy") . "</a></span>";
                        } else
                            echo "<a href=\"javascript:;\" onclick=\"xajax_launchAnalyzeProbe(" . $ent->id() . ");\" title=\"Analysieren\">" . icon("spy") . "</a> ";
                    } else
                        echo "<a href=\"javascript:;\" onclick=\"xajax_launchAnalyzeProbe(" . $ent->id() . ");\" title=\"Analysieren\">" . icon("spy") . "</a> ";
                }
                echo "
                                        <a href=\"?page=entity&amp;id=" . $ent->id() . "&amp;hl=" . $ent->id() . "\">" . icon('info') . "</a>
                                        <a href=\"?page=$page&amp;edit=" . $bookmark->id . "\">" . icon('edit') . "</a>
                                        <a href=\"?page=$page&amp;del=" . $bookmark->id . "\" onclick=\"return confirm('Soll dieser Favorit wirklich gel&ouml;scht werden?');\">" . icon('delete') . "</a>
                                </td>
                        </tr>";
            }

            // Feedback-box für Ausspionieren und Analysieren von river
            echo '
                    <tr><td colspan="6"><div id="spy_info_box" style="display:none"><span id="spy_info"></span></div></td></tr>';

            tableEnd();
        } else {
            info_msg("Noch keine Bookmarks vorhanden!");
        }
    }
}
