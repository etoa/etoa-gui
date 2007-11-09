<?
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: imagepack.php													//
	// Topic: Bildpacket-Verwaltung	 								//
	// Version: 1.0																	//
	// Letzte Änderung: 01.06.2005									//
	//////////////////////////////////////////////////

	// DEFINITIONEN //
	
	define(IMAGEPACK_TABLE,$db_table['imagepack']);
	
	
	// SKRIPT //
	
	echo "<h4 align=\"center\">:: Bildpackete ::</h4>";
	
	$res = dbquery("SELECT imagepack_name,imagepack_desc,imagepack_ext,imagepack_zippath FROM `".IMAGEPACK_TABLE."` WHERE imagepack_zippath!='' ORDER BY imagepack_name;");
	if (mysql_num_rows($res)>0)
	{
		echo "<table style=\"border-collapse:collapse;width:100%;margin:10px auto;\">";
		echo "<tr><th class=\"tbltitle\">Name</th><th class=\"tbltitle\">Format</th><th class=\"tbltitle\">Beschreibung</th><th class=\"tbltitle\">Download ZIP</th></tr>";
		while ($arr=mysql_fetch_array($res))
		{
			echo "<tr><td class=\"tbldata\" style=\"vertical-align:top;\">".$arr['imagepack_name']."</td>";
			echo "<td class=\"tbldata\" style=\"vertical-align:top;\">".$arr['imagepack_ext']."</td>";
			echo "<td class=\"tbldata\" width=\"40%\" style=\"vertical-align:top;\">".text2html($arr['imagepack_desc'])."</td>";
			echo "<td class=\"tbldata\" style=\"vertical-align:top;\"><a href=\"".$arr['imagepack_zippath']."\">".$arr['imagepack_zippath']."</a></td></tr>";
		}		
		echo "</table>";
	}
	else
		echo "<i>Es sind keine Bildpackete in der Datenbank vorhanden!</i></br>";
	echo "<br/><a href=\"?page=userconfig\">Zur&uuml;ck zu den Benutzereinstellungen</a>";	
	
?>