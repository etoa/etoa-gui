<?PHP

	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: wormhole.php													//
	// Topic: Wurmloch-Modul			 									//
	// Version: 0.1																	//
	// Letzte Änderung: 01.10.2004									//
	//////////////////////////////////////////////////


// Dieses Modul ist momentan noch nicht im Spiel integriert! //

$res = dbquery("SELECT * FROM ".$db_table['space_cells']." WHERE cell_wormhole_id='".intval($_GET['id'])."' OR cell_id='".intval($_GET['id'])."';");
$arr1 = mysql_fetch_array($res);
$arr2 = mysql_fetch_array($res);
$coords1= $arr1['cell_sx']."/".$arr1['cell_sy']." : ".$arr1['cell_cx']."/".$arr1['cell_cy'];
$coords2= $arr2['cell_sx']."/".$arr2['cell_sy']." : ".$arr2['cell_cx']."/".$arr2['cell_cy'];
$sx1=$arr1['cell_sx'];
$sx2=$arr2['cell_sx'];
$sy1=$arr1['cell_sy'];
$sy2=$arr2['cell_sy'];


echo "<p>Dieses Wurmloch stellt eine Verbindung zwischen <a href=\"?page=space&sx=$sx1&sy=$sy1\">$coords1</a> und <a href=\"?page=space&sx=$sx2&sy=$sy2\">$coords2</a> her!</p>";

?>