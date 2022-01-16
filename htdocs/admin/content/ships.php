<?PHP

use EtoA\Admin\Forms\ShipCategoriesForm;
use EtoA\Admin\Forms\ShipsForm;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Ranking\RankingService;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipRepository;
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
// Kategorien
//
if ($sub == "cat") {
    ShipCategoriesForm::render($app, $request);
}

//
// Daten
//
elseif ($sub == "data") {
    ShipsForm::render($app, $request);
}

/**************
 * Schiffliste *
 **************/
else {
    \EtoA\Admin\LegacyTemplateTitleHelper::$title = "Schiffliste";

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
