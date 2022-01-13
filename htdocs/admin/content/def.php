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
//
//
if ($sub == "transforms") {
    ObjectTransformsForm::render($app, $request);
}

//
// Bearbeiten
//
elseif ($sub == "data") {
    DefensesForm::render($app, $request);
}

//
// Kategorien
//
elseif ($sub == "cat") {
    DefenseCategoriesForm::render($app, $request);
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
