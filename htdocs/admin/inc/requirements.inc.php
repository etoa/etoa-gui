<?PHP

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingSort;
use EtoA\Requirement\RequirementRepositoryProvider;
use EtoA\Technology\TechnologyDataRepository;

$twig->addGlobal("title", TITLE);

// Lade Gebäude- & Technologienamen
/** @var BuildingDataRepository $buildingRepository */
$buildingRepository = $app[BuildingDataRepository::class];
$bu = $buildingRepository->getBuildingNames(true);
/** @var TechnologyDataRepository $technologyRepository */
$technologyRepository = $app[TechnologyDataRepository::class];
$te = $technologyRepository->getTechnologyNames(true);
/** @var RequirementRepositoryProvider $requirementProvider */
$requirementProvider = $app[RequirementRepositoryProvider::class];
$requirements = $requirementProvider->getRepository(REQ_TBL)->getAll();
if (isset($objectNames) && count($objectNames) > 0) {
    echo "<table><tr>
            <th colspan=\"" . (defined('ITEM_IMAGE_PATH') ? 2 : 1) . "\">Name</th>
            <th>Voraussetzungen</th>
        </tr>";
    foreach ($objectNames as $objectId => $objectName) {
        echo "<tr>";
        if (defined('ITEM_IMAGE_PATH')) {
            $path = preg_replace('/<DB_TABLE_ID>/', $objectId, ITEM_IMAGE_PATH);
            if (is_file($path)) {
                $imsize = getimagesize($path);
                echo "<td style=\"background:#000;width:" . $imsize[0] . "px;\"><img src=\"" . $path . "\"/></td>";
            } else {
                echo "<td style=\"background:#000;width:40px;\"><img src=\"../images/blank.gif\" style=\"width:40px;height:40px;\" /></td>";
            }
        }
        echo "<td>" . $objectName . "</td><td>";

        echo "<div id=\"item_container_" . $objectId . "\">";
        drawTechTreeForSingleItem(REQ_TBL, $requirements, $objectId, $te, $bu);
        echo "</div>";
        echo "<br/><select id=\"reqid_" . $objectId . "\">
            <option value=\"\">Anforderung wählen...</option>";
        foreach ($bu as $k => $v) {
            echo "<option value=\"b:$k\">$v</option>";
        }
        echo "<option value=\"\">----------------------</option>";
        foreach ($te as $k => $v) {
            echo "<option value=\"t:$k\">$v</option>";
        }
        echo "</select><input type=\"text\" id=\"reqlvl_" . $objectId . "\" size=\"2\" maxlength=\"2\" value=\"1\" />
            <input type=\"button\" onclick=\"xajax_addToTechTree('" . REQ_TBL . "'," . $objectId . ",document.getElementById('reqid_" . $objectId . "').value,document.getElementById('reqlvl_" . $objectId . "').value);\" value=\"Hinzufügen\" />";


        echo "</td></tr>";
    }
    tableEnd();
} else {
    echo "<i>Keine Objekte vorhanden!</i>";
}
