<?PHP

use EtoA\Building\BuildingDataRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Technology\TechnologyDataRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var Request */
$request = Request::createFromGlobals();

//
// Techtree
//
if ($sub == "techtree") {

    echo "<h1>Technikbaum</h1>";
    $starItem = 6;

    echo "<select onchange=\"xajax_reqInfo(this.value,'b')\">
    <option value=\"0\">Gebäude wählen...</option>";
    /** @var BuildingDataRepository $buildingRepository */
    $buildingRepository = $app[BuildingDataRepository::class];
    $buildingNames = $buildingRepository->getBuildingNames();
    foreach ($buildingNames as $buildingId => $buildingName) {
        echo "<option value=\"" . $buildingId . "\">" . $buildingName . "</option>";
    }
    echo "</select> ";

    echo "<select onchange=\"xajax_reqInfo(this.value,'t')\">
    <option value=\"0\">Technologie wählen...</option>";
    /** @var TechnologyDataRepository $technologyRepository */
    $technologyRepository = $app[TechnologyDataRepository::class];
    $technologyNames = $technologyRepository->getTechnologyNames();
    foreach ($technologyNames as $technologyId => $technologyName) {
        echo "<option value=\"" . $technologyId . "\">" . $technologyName . "</option>";
    }
    echo "</select> ";

    echo "<select onchange=\"xajax_reqInfo(this.value,'s')\">
    <option value=\"0\">Schiff wählen...</option>";
    /** @var ShipDataRepository $shipRepository */
    $shipRepository = $app[ShipDataRepository::class];
    $shipNames = $shipRepository->getShipNames();
    foreach ($shipNames as $shipId => $shipName) {
        echo "<option value=\"" . $shipId . "\">" . $shipName . "</option>";
    }
    echo "</select> ";

    echo "<select onchange=\"xajax_reqInfo(this.value,'d')\">
    <option value=\"0\">Verteidigung wählen...</option>";
    /** @var DefenseDataRepository $defenseRepository */
    $defenseRepository = $app[DefenseDataRepository::class];
    $defenseNames = $defenseRepository->getDefenseNames();
    foreach ($defenseNames as $defenseId => $defenseName) {
        echo "<option value=\"" . $defenseId . "\">" . $defenseName . "</option>";
    }
    echo "</select><br/><br/>";

    echo "<div id=\"reqInfo\" style=\"width:650px;text-align:center;;margin-left:10px;padding:10px;
    background:#fff;color:#000;border:1px solid #000\">
    Bitte warten...
    </div>";
    echo '<script type="text/javascript">xajax_reqInfo(' . $starItem . ',"b")</script>';
} else {
    require __DIR__ . "/../../content/help.php";
}
