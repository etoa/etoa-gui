<?PHP
	require_once("conf.inc.php");
	require_once("functions.php");

	dbconnect();

	$conf = get_all_config();
	include("def.inc.php");
	
	if ($_GET['a']=="buildingdesc" && $_GET['id']>0)
	{
		$res = dbquery("
		SELECT
			building_name,
			building_longcomment
		FROM	
			buildings
		WHERE	
			building_id=".$_GET['id']."		
		");
		$arr = mysql_fetch_row($res);
		echo "<b>".$arr[0]."</b><br/>".text2html($arr[1])."";		
	}
	elseif ($_GET['a']=="buildingcat" && $_GET['id']>0)
    {
        if ($_GET['id']==BUILDING_STORE_CAT)
        {
            echo "<b>Lagerkapazit&auml;t</b><br>";
            echo "Du kannst auf einem Planeten nicht unentlich viele Rohstoffe lagern. Jeder Planet hat eine Lagerkapazit&auml;t von ".intval($conf['def_store_capacity']['v']).". Um die Lagerkapazit&auml;t zu erh&ouml;hen, kannst du eine Planetenbasis und danach verschiedene Speicher, Lagerhallen und Silos bauen, welche die Kapazit&auml;t erh&ouml;hen. Wenn eine Zahl in der Rohstoffanzeige rot gef&auml;rbt ist, bedeutet das, dass dieser Rohstoff die Lagerkapazit&auml;t &uuml;berschreitet. Baue in diesem Fall den Speicher aus. Eine &uuml;berschrittene Lagerkapazit&auml;t bedeutet, dass nichts mehr produziert wird, jedoch werden Rohstoffe, die z.B. mit einer Flotte ankommen, trotzdem auf dem Planeten gespeichert.<br>";
        }
        elseif($_GET['id']==BUILDING_POWER_CAT)
        {
            echo "<b>Energie</b><br>";
            echo "Wo es eine Produkion hat, braucht es auch Energie. Diese Energie, welche von verschiedenen Anlagen gebraucht wird, spenden uns verschiedene Kraftwerkstypen. Je h&ouml;her diese Ausgebaut sind, desto mehr Leistung erbringen sie und versorgen so die wachsende Wirtschaft.<br>
            Hat es zu wenig Energie, wird die Produktion prozentual gedrosselt, was verheerende Auswirkungen haben kann!";
        }
        elseif($_GET['id']==BUILDING_GENERAL_CAT)
        {
            echo "<b>Allgemeine Geb&auml;ude</b><br/>";
            echo "Diese Geb&auml;ude werden ben&ouml;tigt um deinen Planeten auszubauen und die Produktion und Forschung zu erm&ouml;glichen.";
        }
        elseif($_GET['id']==BUILDING_RES_CAT)
        {
            echo "<b>Rohstoffgeb&auml;ude</b><br/>";
            echo "Diese Geb&auml;ude liefern Rohstoffe, welche du f&uuml;r den Aufbau deiner Zivilisation brauchst.";
        }
        else
        {
            echo "<i>Zu dieser Kategorie sind keine Informationen vorhanden!</i>";
        }
    }
	else
	{
		echo "Keine Informationen verfügbar!";
	}
?>
