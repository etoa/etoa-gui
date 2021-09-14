<?PHP

use EtoA\Admin\Forms\MissilesForm;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Core\ObjectWithImage;
use EtoA\Missile\MissileDataRepository;
use EtoA\Missile\MissileRepository;
use EtoA\Support\StringUtils;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var MissileDataRepository $missileDataRepository */
$missileDataRepository = $app[MissileDataRepository::class];

/** @var MissileRepository $missileRepository */
$missileRepository = $app[MissileRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

if ($sub == "data") {
    editMissileData($app, $twig, $request);
} elseif ($sub == "req") {
    missileRequirements($twig);
} else {
    missileOverview($config, $missileDataRepository, $missileRepository);
}

function editMissileData(Container $app, Environment $twig, Request $request): void
{
    MissilesForm::render($app, $twig, $request);
}

function missileRequirements(Environment $twig): void
{
    global $app;

    define("TITLE", "Raketemanforderungen");
    define("REQ_TBL", "missile_requirements");
    define("ITEM_IMAGE_PATH", ObjectWithImage::BASE_PATH . "/missiles/missile<DB_TABLE_ID>_small.png");

    /** @var MissileDataRepository $missileDataRepository */
    $missileDataRepository = $app[MissileDataRepository::class];
    $objectNames = $missileDataRepository->getMissileNames(true);
    include("inc/requirements.inc.php");
}

function missileOverview(ConfigurationService $config, MissileDataRepository $missileDataRepository, MissileRepository $missileRepository): void
{
    global $page;
    global $sub;

    echo "<h1>Listen bearbeiten</h1>";

    // Hinzufügen
    echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\" id=\"selector\">";
    tableStart();

    //Sonnensystem
    echo "<tr><th class=\"tbltitle\">Sonnensystem</th><td class=\"tbldata\">
    <select name=\"cell_sx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showMissilesOnPlanet',1);\">";
    echo "<option value=\"0\">Sektor X</option>";
    for ($x = 1; $x <= $config->param1Int('num_of_sectors'); $x++)
        echo "<option value=\"$x\">$x</option>";
    echo "</select>/<select name=\"cell_sy\"  onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showMissilesOnPlanet',1);\">";
    echo "<option value=\"0\">Sektor Y</option>";
    for ($x = 1; $x <= $config->param2Int('num_of_sectors'); $x++)
        echo "<option value=\"$x\">$x</option>";
    echo "</select> : <select name=\"cell_cx\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showMissilesOnPlanet',1);\">";
    echo "<option value=\"0\">Zelle X</option>";
    for ($x = 1; $x <= $config->param1Int('num_of_cells'); $x++)
        echo "<option value=\"$x\">$x</option>";
    echo "</select>/<select name=\"cell_cy\" onChange=\"xajax_planetSelectorByCell(xajax.getFormValues('selector'),'showMissilesOnPlanet',1);\">";
    echo "<option value=\"0\">Zelle Y</option>";
    for ($x = 1; $x <= $config->param2Int('num_of_cells'); $x++)
        echo "<option value=\"$x\">$x</option>";
    echo "</select></td></tr>";

    //User
    echo "<tr><th class=\"tbltitle\"><i>oder</i> User</th><td class=\"tbldata\">";
    echo "<input type=\"text\" name=\"userlist_nick\" id=\"userlist_nick\" value=\"\" autocomplete=\"off\" size=\"30\" maxlength=\"30\" onkeyup=\"xajax_searchUserList(this.value,'showMissilesOnPlanet');\"><br>
    <div id=\"userlist\">&nbsp;</div>";
    echo "</td></tr>";

    //Planeten
    echo "<tr><th class=\"tbltitle\">Planeten</th><td class=\"tbldata\" id=\"planetSelector\">Sonnensystem oder User wählen...</td></tr>";

    //Schiffe Hinzufügen
    echo "<tr><th class=\"tbltitle\">Hinzuf&uuml;gen:</th><td class=\"tbldata\">
    <input type=\"text\" name=\"shiplist_count\" value=\"1\" size=\"1\" maxlength=\"3\" />
    <select name=\"ship_id\">";
    $missileNames = $missileDataRepository->getMissileNames(true);
    foreach ($missileNames as $missileId => $missileName) {
        echo "<option value=\"" . $missileId . "\">" . $missileName . "</option>";
    }
    echo "</select> &nbsp; <input type=\"button\" onclick=\"xajax_addMissileToPlanet(xajax.getFormValues('selector'));\" value=\"Hinzuf&uuml;gen\" /></td></tr>";

    //Vorhandene Schiffe
    echo "<tr><th class=\"tbltitle\">Vorhandene Raketen:</th><td class=\"tbldata\" id=\"shipsOnPlanet\">Planet wählen...</td></tr>";
    tableEnd();
    echo "</form>";
    echo '<script type="text/javascript">document.forms[0].user_nick.focus();</script>';

    //Add User
    if (searchQueryArray($sa, $so)) {
        if (isset($sa['user_nick'])) {
            echo "<script type=\"text/javascript\">document.getElementById('userlist_nick').value=\"" . $sa['user_nick'][1] . "\";xajax_searchUserList('" . $sa['user_nick'][1] . "','showMissilesOnPlanet');</script>";
        }
    }

    echo "Es sind " . StringUtils::formatNumber($missileRepository->count()) . " Einträge in der Datenbank vorhanden.<br/>";
}
