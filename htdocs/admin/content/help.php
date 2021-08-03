<?PHP

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingRequirementRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseRequirementRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipRequirementRepository;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyRequirementRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var TechnologyRequirementRepository $technologyRequirementRepository */
$technologyRequirementRepository = $app[TechnologyRequirementRepository::class];
/** @var ShipRequirementRepository $shipRequirementRepository */
$shipRequirementRepository = $app[ShipRequirementRepository::class];
/** @var BuildingRequirementRepository $buildingRequirementRepository */
$buildingRequirementRepository = $app[BuildingRequirementRepository::class];
/** @var DefenseRequirementRepository $defenseRequirementRepository */
$defenseRequirementRepository = $app[DefenseRequirementRepository::class];

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

    echo "<br/><br/>";
    $duplicates = $buildingRequirementRepository->getDuplicateBuildingRequirements();
    foreach ($duplicates as $buildingId => $requiredObjId) {
        echo "Gebäude-Bedingung Fehler bei Gebäude " . $buildingNames[$buildingId] . " (" . $requiredObjId . ")<br/>";
    }

    $duplicates = $buildingRequirementRepository->getDuplicateTechRequirements();
    foreach ($duplicates as $buildingId => $requiredObjId) {
        echo "Tech-Bedingung Fehler bei Gebäude " . $buildingNames[$buildingId] . " (" . $requiredObjId . ")<br/>";
    }

    $duplicates = $technologyRequirementRepository->getDuplicateBuildingRequirements();
    foreach ($duplicates as $techId => $requiredObjId) {
        echo "Gebäude-Bedingung Fehler bei Tech " . $technologyNames[$techId] . " (" . $requiredObjId . ")<br/>";
    }

    $duplicates = $technologyRequirementRepository->getDuplicateTechRequirements();
    foreach ($duplicates as $techId => $requiredObjId) {
        echo "Tech-Bedingung Fehler bei Tech " . $technologyNames[$techId] . " (" . $requiredObjId . ")<br/>";
    }

    $duplicates = $shipRequirementRepository->getDuplicateBuildingRequirements();
    foreach ($duplicates as $shipId => $requiredObjId) {
        echo "Gebäude-Bedingung Fehler bei Schiff " . $shipNames[$shipId] . " (" . $requiredObjId . ")<br/>";
    }

    $duplicates = $shipRequirementRepository->getDuplicateTechRequirements();
    foreach ($duplicates as $shipId => $requiredObjId) {
        echo "Tech-Bedingung Fehler bei Schiff " . $shipNames[$shipId] . " (" . $requiredObjId . ")<br/>";
    }

    $duplicates = $defenseRequirementRepository->getDuplicateBuildingRequirements();
    foreach ($duplicates as $defenseId => $requiredObjId) {
        echo "Gebäude-Bedingung Fehler bei Verteidigung " . $defenseNames[$defenseId] . " (" . $requiredObjId . ")<br/>";
    }

    $duplicates = $defenseRequirementRepository->getDuplicateTechRequirements();
    foreach ($duplicates as $defenseId => $requiredObjId) {
        echo "Tech-Bedingung Fehler bei Verteidigung " . $defenseNames[$defenseId] . " (" . $requiredObjId . ")<br/>";
    }
} else {
    require("../content/help.php");
}
