<?PHP

/** @var Request */

use Symfony\Component\HttpFoundation\Request;

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
    $bures = dbquery("SELECT building_id,building_name FROM buildings WHERE building_show=1 ORDER BY building_name;");
    while ($buarr = mysql_fetch_array($bures))
    {
        echo "<option value=\"".$buarr['building_id']."\">".$buarr['building_name']."</option>";
    }
    echo "</select> ";


    echo "<select onchange=\"xajax_reqInfo(this.value,'t')\">
    <option value=\"0\">Technologie wählen...</option>";
    $teres = dbquery("SELECT tech_id,tech_name FROM technologies WHERE tech_show=1 ORDER BY tech_name;");
    while ($tearr = mysql_fetch_array($teres))
    {
        echo "<option value=\"".$tearr['tech_id']."\">".$tearr['tech_name']."</option>";
    }
    echo "</select> ";

    echo "<select onchange=\"xajax_reqInfo(this.value,'s')\">
    <option value=\"0\">Schiff wählen...</option>";
    $teres = dbquery("SELECT ship_id,ship_name FROM ships WHERE ship_show=1 AND special_ship=0 ORDER BY ship_name;");
    while ($tearr = mysql_fetch_array($teres))
    {
        echo "<option value=\"".$tearr['ship_id']."\">".$tearr['ship_name']."</option>";
    }
    echo "</select> ";

    echo "<select onchange=\"xajax_reqInfo(this.value,'d')\">
    <option value=\"0\">Verteidigung wählen...</option>";
    $teres = dbquery("SELECT def_id,def_name FROM defense WHERE def_show=1 ORDER BY def_name;");
    while ($tearr = mysql_fetch_array($teres))
    {
        echo "<option value=\"".$tearr['def_id']."\">".$tearr['def_name']."</option>";
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
