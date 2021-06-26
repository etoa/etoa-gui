<?PHP

use Symfony\Component\HttpFoundation\Request;

/** @var Request */
$request = Request::createFromGlobals();

//
// Techtree
//
if ($sub=="techtree")
{

    echo "<h1>Technikbaum</h1>";
    $starItem = 6;

    echo "<select onchange=\"xajax_reqInfo(this.value,'b')\">
    <option value=\"0\">Gebäude wählen...</option>";
    /** @var \EtoA\Building\BuildingDataRepository $buildingRepository */
    $buildingRepository = $app[\EtoA\Building\BuildingDataRepository::class];
    $buildingNames = $buildingRepository->getBuildingNames();
    foreach ($buildingNames as $buildingId => $buildingName) {
        echo "<option value=\"".$buildingId."\">".$buildingName."</option>";
    }
    echo "</select> ";

    echo "<select onchange=\"xajax_reqInfo(this.value,'t')\">
    <option value=\"0\">Technologie wählen...</option>";
    /** @var \EtoA\Technology\TechnologyDataRepository $technologyRepository */
    $technologyRepository = $app[\EtoA\Technology\TechnologyDataRepository::class];
    $technologyNames = $technologyRepository->getTechnologyNames();
    $teres = dbquery("SELECT tech_id,tech_name FROM technologies WHERE tech_show=1 ORDER BY tech_name;");
    foreach ($technologyNames as $technologyId => $technologyName) {
        echo "<option value=\"".$technologyId."\">".$technologyName."</option>";
    }
    echo "</select> ";

    echo "<select onchange=\"xajax_reqInfo(this.value,'s')\">
    <option value=\"0\">Schiff wählen...</option>";
    /** @var \EtoA\Ship\ShipDataRepository $shipRepository */
    $shipRepository = $app[\EtoA\Ship\ShipDataRepository::class];
    $shipNames = $shipRepository->getShipNames();
    foreach ($shipNames as $shipId => $shipName) {
        echo "<option value=\"".$shipId."\">".$shipName."</option>";
    }
    echo "</select> ";

    echo "<select onchange=\"xajax_reqInfo(this.value,'d')\">
    <option value=\"0\">Verteidigung wählen...</option>";
    /** @var \EtoA\Defense\DefenseDataRepository $defenseRepository */
    $defenseRepository = $app[\EtoA\Defense\DefenseDataRepository::class];
    $defenseNames = $defenseRepository->getDefenseNames();
    foreach ($defenseNames as $defenseId => $defenseName) {
        echo "<option value=\"".$defenseId."\">".$defenseName."</option>";
    }
    echo "</select><br/><br/>";

    echo "<div id=\"reqInfo\" style=\"width:650px;text-align:center;;margin-left:10px;padding:10px;
    background:#fff;color:#000;border:1px solid #000\">
    Bitte warten...
    </div>";
    echo '<script type="text/javascript">xajax_reqInfo('.$starItem.',"b")</script>';

    echo "<br/><br/>";
    $bures = dbquery("SELECT COUNT(*),obj_id,req_building_id FROM building_requirements WHERE req_building_id>0 GROUP BY obj_id,req_building_id;");
    while ($buarr = mysql_fetch_row($bures))
    {
        if ($buarr[0]!=1)
            echo "Gebäude-Bedingung Fehler bei Gebäude ".$buarr[1]." (".$buarr[2].")<br/>";
    }
    $bures = dbquery("SELECT COUNT(*),obj_id,req_tech_id FROM building_requirements WHERE req_tech_id>0 GROUP BY obj_id,req_tech_id;");
    while ($buarr = mysql_fetch_row($bures))
    {
        if ($buarr[0]!=1)
            echo "Tech-Bedingung Fehler bei Gebäude ".$buarr[1]." (".$buarr[2].")<br/>";
    }

    $bures = dbquery("SELECT COUNT(*),obj_id,req_building_id FROM tech_requirements WHERE req_building_id>0 GROUP BY obj_id,req_building_id;");
    while ($buarr = mysql_fetch_row($bures))
    {
        if ($buarr[0]!=1)
            echo "Gebäude-Bedingung Fehler bei Tech ".$buarr[1]." (".$buarr[2].")<br/>";
    }
    $bures = dbquery("SELECT COUNT(*),obj_id,req_tech_id FROM tech_requirements WHERE req_tech_id>0 GROUP BY obj_id,req_tech_id;");
    while ($buarr = mysql_fetch_row($bures))
    {
        if ($buarr[0]!=1)
            echo "Tech-Bedingung Fehler bei Tech ".$buarr[1]." (".$buarr[2].")<br/>";
    }

    $bures = dbquery("SELECT COUNT(*),obj_id,req_building_id FROM ship_requirements WHERE req_building_id>0 GROUP BY obj_id,req_building_id;");
    while ($buarr = mysql_fetch_row($bures))
    {
        if ($buarr[0]!=1)
            echo "Gebäude-Bedingung Fehler bei Schiff ".$buarr[1]." (".$buarr[2].")<br/>";
    }
    $bures = dbquery("SELECT COUNT(*),obj_id,req_tech_id FROM ship_requirements WHERE req_tech_id>0 GROUP BY obj_id,req_tech_id;");
    while ($buarr = mysql_fetch_row($bures))
    {
        if ($buarr[0]!=1)
            echo "Tech-Bedingung Fehler bei Schiff ".$buarr[1]." (".$buarr[2].")<br/>";
    }
}
else
{
    require("../content/help.php");
}
