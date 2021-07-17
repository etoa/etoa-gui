<?PHP

use EtoA\Building\BuildingDataRepository;
use EtoA\Technology\TechnologyDataRepository;

$twig->addGlobal("title", TITLE);

// Lade Gebäude- & Technologienamen
/** @var BuildingDataRepository $buildingRepository */
$buildingRepository = $app[BuildingDataRepository::class];
$bu = $buildingRepository->getBuildingNames(true);
/** @var TechnologyDataRepository $technologyRepository */
$technologyRepository = $app[TechnologyDataRepository::class];
$te = $technologyRepository->getTechnologyNames(true);

$res = dbquery("
    SELECT
        `" . ITEM_ID_FLD . "` as id,
        `" . ITEM_NAME_FLD . "` as name
    FROM
        `" . ITEMS_TBL . "`
    WHERE
        " . ITEM_ENABLE_FLD . "=1
    ORDER BY
        " . ITEM_ORDER_FLD . ";");
if (mysql_num_rows($res) > 0) {
    echo "<table><tr>
            <th colspan=\"" . (defined('ITEM_IMAGE_PATH') ? 2 : 1) . "\">Name</th>
            <th>Voraussetzungen</th>
        </tr>";
    while ($arr = mysql_fetch_assoc($res)) {
        $id = $arr['id'];
        echo "<tr>";
        if (defined('ITEM_IMAGE_PATH')) {
            $path = preg_replace('/<DB_TABLE_ID>/', $id, ITEM_IMAGE_PATH);
            if (is_file($path)) {
                $imsize = getimagesize($path);
                echo "<td style=\"background:#000;width:" . $imsize[0] . "px;\"><img src=\"" . $path . "\"/></td>";
            } else {
                echo "<td style=\"background:#000;width:40px;\"><img src=\"../images/blank.gif\" style=\"width:40px;height:40px;\" /></td>";
            }
        }
        echo "<td>" . $arr['name'] . "</td><td>";

        echo "<div id=\"item_container_" . $id . "\">";
        drawTechTreeForSingleItem(REQ_TBL, $id);
        echo "</div>";
        echo "<br/><select id=\"reqid_" . $id . "\">
            <option value=\"\">Anforderung wählen...</option>";
        foreach ($bu as $k => $v) {
            echo "<option value=\"b:$k\">$v</option>";
        }
        echo "<option value=\"\">----------------------</option>";
        foreach ($te as $k => $v) {
            echo "<option value=\"t:$k\">$v</option>";
        }
        echo "</select><input type=\"text\" id=\"reqlvl_" . $id . "\" size=\"2\" maxlength=\"2\" value=\"1\" />
            <input type=\"button\" onclick=\"xajax_addToTechTree('" . REQ_TBL . "'," . $id . ",document.getElementById('reqid_" . $id . "').value,document.getElementById('reqlvl_" . $id . "').value);\" value=\"Hinzufügen\" />";


        echo "</td></tr>";
    }
    tableEnd();
} else {
    echo "<i>Keine Objekte vorhanden!</i>";
}
