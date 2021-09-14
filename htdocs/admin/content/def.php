<?PHP

use EtoA\Admin\Forms\DefenseCategoriesForm;
use EtoA\Admin\Forms\DefensesForm;
use EtoA\Admin\Forms\ObjectTransformsForm;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Core\ObjectWithImage;
use EtoA\Defense\Defense;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseListSearch;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Defense\DefenseQueueSearch;
use EtoA\Defense\DefenseRepository;
use EtoA\Defense\DefenseSort;
use EtoA\Ranking\RankingService;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var DefenseDataRepository $defenseDataRepository */
$defenseDataRepository = $app[DefenseDataRepository::class];
$defenseNames = $defenseDataRepository->getDefenseNames(true);

/** @var DefenseRepository $defenseRepository */
$defenseRepository = $app[DefenseRepository::class];

/** @var DefenseQueueRepository $defenseQueueRepository */
$defenseQueueRepository = $app[DefenseQueueRepository::class];
/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var PlanetRepository $planetRepository */
$planetRepository = $app[PlanetRepository::class];

/** @var RankingService $rankingService */
$rankingService = $app[RankingService::class];

$request = Request::createFromGlobals();
//
// Battlepoints
//
if ($sub == "battlepoints") {
    echo "<h1>Punkte</h1>";
    echo "<h2>Punkte neu berechnen</h2><form action=\"?page=$page&amp;sub=$sub\" method=\"POST\">";
    if (isset($_POST['recalc']) && $_POST['recalc'] != "") {
        $numDefenses = $rankingService->calcDefensePoints();
        echo MessageBox::ok("", "Die Punkte von $numDefenses Verteidigungsanlagen wurden aktualisiert!");
    }
    echo "Nach jeder direkter &Auml;nderung an den Verteidigungsanlagen via Datenbank m&uuml;ssen die Punkte neu berechnet werden: ";
    echo "<br/><br/><input type=\"submit\" name=\"recalc\" value=\"Neu berechnen\" /></form>";
    echo "<h2>Battlepoints</h2>";
    $defenses = $defenseDataRepository->getAllDefenses();
    usort($defenses, fn (Defense $a, Defense $b) => $b->points <=> $a->points);

    if (count($defenses) > 0) {
        echo "<table class=\"tb\">";
        foreach ($defenses as $defense) {
            echo "<tr><th>" . $defense->name . "</th><td style=\"width:70%; text-align: right\"  title=\"$defense->points\">" . StringUtils::formatNumber($defense->points) . "</td></tr>";
        }
        echo "</table>";
    }
}

//
//
//
elseif ($sub == "transforms") {
    ObjectTransformsForm::render($app, $twig, $request);
}

//
// Bauliste
//
elseif ($sub == "queue") {
    echo "<h2>Bauliste</h2>";

    if ((isset($_POST['defqueue_search']) && $_POST['defqueue_search'] != "") || (isset($_GET['action']) && $_GET['action'] == "searchresults")) {
        // Suchquery generieren
        if (!isset($_SESSION['defqueue']['query'])) {
            $queueSearch = DefenseQueueSearch::create();
            if ($request->request->getInt('planet_id') > 0) {
                $queueSearch->entityId($request->request->getInt('planet_id'));
            }
            if ((bool) $request->request->get('planet_name')) {
                $queueSearch->likePlanetName($request->request->get('planet_name'));
            }
            if ($request->request->getInt('user_id') > 0) {
                $queueSearch->userId($request->request->getInt('user_id'));
            }
            if ((bool) $request->request->get('user_nick')) {
                $queueSearch->likeUserNick($request->request->get('user_nick'));
            }
            if ($request->request->getInt('def_id') > 0) {
                $queueSearch->defenseId($request->request->getInt('def_id'));
            }

            $_SESSION['defqueue']['query'] = serialize($queueSearch);
        } else {
            $queueSearch = unserialize($_SESSION['defqueue']['query'], ['allowed_classes' => [DefenseQueueSearch::class]]);
        }

        $entries = $defenseQueueRepository->adminSearchQueueItems($queueSearch);
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
            foreach ($entries as $entry) {
                if ($pid > 0 && $pid !== $entry->entityId) {
                    echo "<tr><td colspan=\"8\" style=\"height:3px;background:#000;\" class=\"tbldata\"></td></tr>";
                }
                $pid = $entry->entityId;

                $error = false;

                // Planet gehört nicht dem Besitzer
                if ($entry->userId !== $entry->planetUserId) {
                    $error = true;
                    $errorMsg = "Planet geh&ouml;rt nicht dem Schiffbesitzer! Wird auf den Heimatplaneten verschoben";
                }

                if ($error)
                    $style = " style=\"color:#f30\"";
                elseif ($entry->count === 0)
                    $style = " style=\"color:#999\"";
                else
                    $style = "";
                echo "<tr>";
                echo "<td class=\"tbldata\" $style>" . $entry->id . "</a></td>";
                echo "<td class=\"tbldata\"$style " . mTT($entry->defenseName, "<b>Verteidigungs-ID:</b> " . $entry->id) . ">" . $entry->defenseName . "</td>";
                echo "<td class=\"tbldata\"$style>" . StringUtils::formatNumber($entry->count) . "</td>";
                echo "<td class=\"tbldata\"$style " . mTT($entry->planetName, "<b>Planet-ID:</b> " . $entry->entityId . "<br/><b>Koordinaten:</b> " . $entry->entity->sx . "/" . $entry->entity->sy . " : " . $entry->entity->cx . "/" . $entry->entity->cy . " : " . $entry->entity->pos) . ">" . StringUtils::cutString($entry->planetName, 11) . "</td>";
                echo "<td class=\"tbldata\"$style " . mTT($entry->userNick, "<b>User-ID:</b> " . $entry->userId . "<br/><b>Punkte:</b> " . StringUtils::formatNumber($entry->userPoints)) . ">" . StringUtils::cutString($entry->userNick, 11) . "</td>";
                echo "<td class=\"tbldata\"$style>" . StringUtils::formatDate($entry->startTime) . "</td>";
                echo "<td class=\"tbldata\"$style>" . StringUtils::formatDate($entry->endTime) . "</td>";
                echo "<td class=\"tbldata\"$style>" . edit_button("?page=$page&sub=$sub&action=edit&id=" . $entry->id);
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
        if ($request->request->has('save')) {
            $queueItem = $defenseQueueRepository->getQueueItem((int) $_GET['id']);
            if ($queueItem !== null) {
                $queueItem->count = (int) $_POST['queue_cnt'];
                $queueItem->startTime = (new \DateTime($_POST['queue_starttime']))->getTimestamp();
                $queueItem->endTime = (new \DateTime($_POST['queue_endtime']))->getTimestamp();

                $defenseQueueRepository->saveQueueItem($queueItem);
            }
        }

        // Auftrag löschen
        elseif ($request->request->has('del')) {
            $defenseQueueRepository->deleteQueueItem((int) $_GET['id']);
            echo "Datensatz entfernt!<br/><br/>";
        }

        // Auftrag abschliessen
        elseif ($request->request->has('build_finish')) {
            $queueItem = $defenseQueueRepository->getQueueItem((int) $_GET['id']);
            if ($queueItem !== null) {
                $defenseRepository->addDefense($queueItem->defenseId, $queueItem->count, $queueItem->userId, $queueItem->entityId);
                $defenseQueueRepository->deleteQueueItem((int) $_GET['id']);
            }
            echo "Bau abgeschlossen!<br/><br/>";
        }

        $queueItem = $defenseQueueRepository->getQueueItem((int) $_GET['id']);
        if ($queueItem !== null) {
            if ($queueItem->startTime > 0)
                $bst = date($config->get('admin_dateformat'), $queueItem->startTime);
            else
                $bst = "";
            if ($queueItem->endTime > 0)
                $bet = date($config->get('admin_dateformat'), $queueItem->endTime);
            else
                $bet = "";

            $userNick = $userRepository->getNick($queueItem->userId);
            $planet = $planetRepository->find($queueItem->entityId);
            $defenseNames = $defenseDataRepository->getDefenseNames(true);

            echo "<form action=\"?page=$page&sub=$sub&action=edit&id=" . $queueItem->id . "\" method=\"post\">";
            echo "<table class=\"tbl\">";
            echo "<tr><td class=\"tbltitle\">ID</td><td class=\"tbldata\">" . $queueItem->id . "</td></tr>";
            echo "<tr><td class=\"tbltitle\">Planet</td><td class=\"tbldata\">" . $planet->name . "</td></tr>";
            echo "<tr><td class=\"tbltitle\">Spieler</td><td class=\"tbldata\">" . $userNick . "</td></tr>";
            echo "<tr><td class=\"tbltitle\">Schiff</td><td class=\"tbldata\">" . $defenseNames[$queueItem->defenseId] . "</td></tr>";
            echo "<tr><td class=\"tbltitle\">Anzahl</td><td class=\"tbldata\"><input type=\"text\" name=\"queue_cnt\" value=\"" . $queueItem->count . "\" size=\"5\" maxlength=\"20\" /></td></tr>";
            echo "<tr><td class=\"tbltitle\">Baustart</td><td class=\"tbldata\">
                <input type=\"text\" id=\"shiplist_build_start_time\" name=\"queue_starttime\" value=\"$bst\" size=\"20\" maxlength=\"30\" />
                <input type=\"button\" value=\"Jetzt\" onclick=\"document.getElementById('shiplist_build_start_time').value='" . date("Y-d-m h:i") . "'\" /></td></tr>";
            echo "<tr><td class=\"tbltitle\">Bauende</td><td class=\"tbldata\">
                <input type=\"text\" id=\"shiplist_build_end_time\" name=\"queue_endtime\" value=\"$bet\" size=\"20\" maxlength=\"30\" /></td></tr>";
            echo "<tr><td class=\"tbltitle\">Bauzeit pro Schiff</td><td class=\"tbldata\">" . StringUtils::formatTimespan($queueItem->objectTime) . "</td></tr>";
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
        unset($_SESSION['defqueue']['query']);

        // Suchmaske
        echo "Suchmaske:<br/><br/>";
        echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
        echo "<table class=\"tbl\">";
        echo "<tr><td class=\"tbltitle\">Planet ID</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td>";
        echo "<tr><td class=\"tbltitle\">Planetname</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" value=\"\" size=\"20\" maxlength=\"250\" /> ";
        echo "</td></tr>";
        echo "<tr><td class=\"tbltitle\">Spieler ID</td><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
        echo "<tr><td class=\"tbltitle\">Spieler Nick</td><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" /> ";
        echo "</td></tr>";
        echo "<tr><td class=\"tbltitle\">Verteidigung</td><td class=\"tbldata\"><select name=\"def_id\"><option value=\"\"><i>---</i></option>";
        foreach ($defenseNames as $defenseId => $defenseName) {
            echo "<option value=\"" . $defenseId . "\">" . $defenseName . "</option>";
        }
        echo "</select></td>";
        echo "</table>";
        echo "<br/><input type=\"submit\" class=\"button\" name=\"defqueue_search\" value=\"Suche starten\" /></form>";
        $tblcnt = $defenseQueueRepository->count();
        echo "<br/>Es sind " . StringUtils::formatNumber($tblcnt) . " Eintr&auml;ge in der Datenbank vorhanden.<br/>";
    }
}

//
// Bearbeiten
//
elseif ($sub == "data") {
    DefensesForm::render($app, $twig, $request);
}

//
// Kategorien
//
elseif ($sub == "cat") {
    DefenseCategoriesForm::render($app, $twig, $request);
}

//
// Voraussetzungen
//
elseif ($sub == "req") {
    define("TITLE", "Verteidigungsanforderungen");
    define("REQ_TBL", "def_requirements");
    define("ITEM_IMAGE_PATH", ObjectWithImage::BASE_PATH . "/defense/def<DB_TABLE_ID>_small.png");

    /** @var DefenseDataRepository $defenseDataRepository */
    $defenseDataRepository = $app[DefenseDataRepository::class];
    $objectNames = $defenseDataRepository->getDefenseNames(true, DefenseSort::category());
    include("inc/requirements.inc.php");
}

//
// Liste
//
else {
    echo "<h1>Verteidigungsliste</h1>";

    if (isset($_POST['deflist_search']) || (isset($_GET['action']) && $_GET['action'] == "searchresults")) {
        // Suchquery generieren
        if (!isset($_SESSION['defedit']['query']) || $_SESSION['defedit']['query'] == "") {
            $defenseSearch = DefenseListSearch::create();
            if ($request->request->getInt('planet_id') > 0)
                $defenseSearch->entityId($request->request->getInt('planet_id'));
            if ((bool) $request->request->get('planet_name')) {
                $defenseSearch->likePlanetName($request->request->get('planet_name'));
            }
            if ($request->request->getInt('user_id') > 0)
                $defenseSearch->userId($request->request->getInt('user_id'));
            if ((bool) $request->request->get('user_nick')) {
                $defenseSearch->likeUserNick($request->request->get('user_nick'));
            }
            if ($request->request->getInt('def_id') > 0) {
                $defenseSearch->defenseId($request->request->getInt('def_id'));
            }

            $_SESSION['defedit']['query'] = serialize($defenseSearch);
        } else {
            $defenseSearch = unserialize($_SESSION['defedit']['query'], ['allowed_classes' => [DefenseListSearch::class]]);
        }

        if (isset($_POST['save'])) {
            $defenseRepository->setDefenseCount((int) $_POST['deflist_id'], (int) $_POST['deflist_count']);
            success_msg("Gespeichert");
        } elseif (isset($_POST['del'])) {
            $defenseRepository->removeEntry((int) $_POST['deflist_id']);
            success_msg("Gelöscht");
        } elseif (isset($_GET['cleanup']) && $_GET['cleanup'] == 1) {
            $defenseRepository->cleanupEmpty();
            success_msg("Aufgeräumt");
        }

        $defenseListItems = $defenseRepository->adminSearchQueueItems($defenseSearch);
        $nr = count($defenseListItems);
        if ($nr > 0) {
            echo $nr . " Datens&auml;tze vorhanden<br/><br/>";
            if ($nr > 20) {
                echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&sub=$sub'\" /> ";
                echo "<input type=\"button\" value=\"Aktualisieren\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=searchresults'\" /> ";
                echo "<input type=\"button\" value=\"Clean-Up\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=searchresults&amp;cleanup=1'\" /><br/><br/>";
            }
            echo "<table class=\"tbl\">";
            echo "<tr>";
            echo "<td class=\"tbltitle\">ID</td>";
            echo "<td class=\"tbltitle\">Planet</td>";
            echo "<td class=\"tbltitle\">Spieler</td>";
            echo "<td class=\"tbltitle\">Verteidigung</td>";
            echo "<td class=\"tbltitle\">Anzahl</td>";
            echo "</tr>";
            foreach ($defenseListItems as $item) {
                if ($item->count === 0)
                    $style = " style=\"color:#999\"";
                else
                    $style = "";

                echo "<tr>";
                echo "<td class=\"tbldata\" $style>" . $item->id . "</a></td>";
                echo "<td class=\"tbldata\" $style" . mTT($item->planetName, "<b>Planet-ID:</b> " . $item->entityId . "<br/><b>Koordinaten:</b> " . $item->entity->sx . "/" . $item->entity->sy . " : " . $item->entity->cx . "/" . $item->entity->cy . " : " . $item->entity->pos) . ">" . StringUtils::cutString($item->planetName, 11) . "</a></td>";
                echo "<td class=\"tbldata\" $style" . mTT($item->userNick, "<b>User-ID:</b> " . $item->userId . "<br/><b>Punkte:</b> " . StringUtils::formatNumber($item->userPoints)) . ">" . StringUtils::cutString($item->userNick, 11) . "</a></td>";
                echo "<td class=\"tbldata\" $style" . mTT($item->defenseName, "<b>Verteidigungs-ID:</b> " . $item->defenseId) . ">" . $item->defenseName . "</a></td>";
                echo "<td class=\"tbldata\" $style>" . StringUtils::formatNumber($item->count) . "</a></td>";
                echo "<td class=\"tbldata\">" . edit_button("?page=$page&sub=$sub&action=edit&deflist_id=" . $item->id) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<br/><input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&sub=$sub'\" /> ";
            echo "<input type=\"button\" value=\"Aktualisieren\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=searchresults'\" />";
        } else {
            echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page&sub=$sub'\" />";
        }
    }

    //
    // Bearbeiten
    //
    elseif (isset($_GET['action']) && $_GET['action'] == "edit") {
        $listItem = $defenseRepository->getItem($_GET['deflist_id']);
        if ($listItem !== null) {
            $defenseNames = $defenseDataRepository->getDefenseNames(true);
            $userNick = $userRepository->getNick($listItem->userId);
            $planet = $planetRepository->find($listItem->entityId);
            echo "<form action=\"?page=$page&sub=$sub&action=searchresults\" method=\"post\">";
            echo "<input type=\"hidden\" name=\"deflist_id\" value=\"" . $listItem->id . "\" />";
            echo "<table class=\"tbl\">";
            echo "<tr><td class=\"tbltitle\">ID</td><td class=\"tbldata\">" . $listItem->id . "</td></tr>";
            echo "<tr><td class=\"tbltitle\">Planet</td><td class=\"tbldata\">" . $planet->name . "</td></tr>";
            echo "<tr><td class=\"tbltitle\">Spieler</td><td class=\"tbldata\">" . $userNick . "</td></tr>";
            echo "<tr><td class=\"tbltitle\">Verteidigung</td><td class=\"tbldata\">" . $defenseNames[$listItem->defenseId] . "</td></tr>";
            echo "<tr><td class=\"tbltitle\">Anzahl</td><td class=\"tbldata\">
                    <input type=\"text\" name=\"deflist_count\" value=\"" . $listItem->count . "\" size=\"5\" maxlength=\"20\" /></td></tr>";

            echo "</table><br/>";
            echo "<input type=\"submit\" name=\"save\" value=\"&Uuml;bernehmen\" class=\"button\" />&nbsp;";
            echo "<input type=\"submit\" name=\"del\" value=\"L&ouml;schen\" class=\"button\" onclick=\"return confirm('Wirklich löschen?');\" />&nbsp;";
            echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" /> ";
            echo "<input type=\"button\" value=\"Neue Suche\" class=\"button\" onclick=\"document.location='?page=$page&sub=$sub'\" /></form>";
        } else
            echo "Dieser Datensatz wurde gel&ouml;scht!<br/><br/><input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" />&nbsp;";
    }

    //
    // Suchformular
    //
    else {


        $_SESSION['defedit']['query'] = "";

        // Verteidigung laden
        echo "<h2>Schnellsuche</h2>";
        // Hinzufügen
        echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\" id=\"selector\">";
        tableStart();

        //Sonnensystem
        echo "<tr><th class=\"tbltitle\">Sonnensystem</th><td class=\"tbldata\">
            <select name=\"cell_sx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showDefenseOnPlanet');\">";
        echo "<option value=\"0\">Sektor X</option>";
        for ($x = 1; $x <= $config->param1Int('num_of_sectors'); $x++)
            echo "<option value=\"$x\">$x</option>";
        echo "</select>/<select name=\"cell_sy\"  onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showDefenseOnPlanet');\">";
        echo "<option value=\"0\">Sektor Y</option>";
        for ($x = 1; $x <= $config->param2Int('num_of_sectors'); $x++)
            echo "<option value=\"$x\">$x</option>";
        echo "</select> : <select name=\"cell_cx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showDefenseOnPlanet');\">";
        echo "<option value=\"0\">Zelle X</option>";
        for ($x = 1; $x <= $config->param1Int('num_of_cells'); $x++)
            echo "<option value=\"$x\">$x</option>";
        echo "</select>/<select name=\"cell_cy\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showDefenseOnPlanet');\">";
        echo "<option value=\"0\">Zelle Y</option>";
        for ($x = 1; $x <= $config->param2Int('num_of_cells'); $x++)
            echo "<option value=\"$x\">$x</option>";
        echo "</select></td></tr>";

        //User
        echo "<tr><th class=\"tbltitle\"><i>oder</i> User</th><td class=\"tbldata\">";
        echo "<input type=\"text\" name=\"userlist_nick\" id=\"userlist_nick\" value=\"\" autocomplete=\"off\" size=\"30\" maxlength=\"30\" onkeyup=\"xajax_searchUserList(this.value,'showDefenseOnPlanet');\"><br>
            <div id=\"userlist\">&nbsp;</div>";
        echo "</td></tr>";

        //Planeten
        echo "<tr><th class=\"tbltitle\">Planeten</th><td class=\"tbldata\" id=\"planetSelector\">Sonnensystem oder User w&auml;hlen...</td></tr>";

        //Def Hinzufügen
        echo "<tr><th class=\"tbltitle\">Hinzuf&uuml;gen:</th><td class=\"tbldata\">
            <input type=\"text\" name=\"deflist_count\" value=\"1\" size=\"1\" maxlength=\"3\" />
            <select name=\"def_id\">";
        foreach ($defenseNames as $defenseId => $defenseName) {
            echo "<option value=\"" . $defenseId . "\">" . $defenseName . "</option>";
        }
        echo "</select> &nbsp; <input type=\"button\" onclick=\"xajax_addDefenseToPlanet(xajax.getFormValues('selector'));\" value=\"Hinzuf&uuml;gen\" /></td></tr>";

        //Vorhandene Def
        echo "<tr><th class=\"tbltitle\">Vorhandene Verteidigung:</th><td class=\"tbldata\" id=\"shipsOnPlanet\">Planet w&auml;hlen...</td></tr>";
        tableEnd();
        echo "</form>";
        echo '<script type="text/javascript">document.forms[0].user_nick.focus();</script>';

        //Add User
        if (searchQueryArray($sa, $so)) {
            if (isset($sa['user_nick'])) {
                echo "<script type=\"text/javascript\">document.getElementById('userlist_nick').value=\"" . $sa['user_nick'][1] . "\";xajax_searchUserList('" . $sa['user_nick'][1] . "','showDefenseOnPlanet');</script>";
            }
        }

        $tblcnt = $defenseRepository->count();
        echo "Es sind " . StringUtils::formatNumber($tblcnt) . " Eintr&auml;ge in der Datenbank vorhanden.<br/><br />";


        // Suchmaske
        echo "<h2>Suchmaske</h2>";

        echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
        tableStart();
        echo "<tr><th class=\"tbltitle\">Planet ID</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
        echo "<tr><th class=\"tbltitle\">Planetname</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" value=\"\" size=\"20\" maxlength=\"250\" /> ";
        echo "</td></tr>";
        echo "<tr><th class=\"tbltitle\">Spieler ID</td><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
        echo "<tr><th class=\"tbltitle\">Spieler Nick</td><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'user_nick','citybox1');\"/> ";
        echo "<br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></tr>";
        echo "<tr><th class=\"tbltitle\">Verteidigung</td><td class=\"tbldata\"><select name=\"def_id\"><option value=\"\"><i>---</i></option>";
        foreach ($defenseNames as $defenseId => $defenseName)
            echo "<option value=\"" . $defenseId . "\">" . $defenseName . "</option>";
        echo "</select></td></tr>";
        tableEnd();
        echo "<br/><input type=\"submit\" class=\"button\" name=\"deflist_search\" value=\"Suche starten\" /></form>";
    }
}
