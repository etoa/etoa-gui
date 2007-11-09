<?PHP

	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: help.php															//
	// Topic: Help & Info				 								//
	// Version: 0.1																	//
	// Letzte &Auml;nderung: 10.05.2006	Lamborghini								//
	//////////////////////////////////////////////////

	// BEGIN SKRIPT //

	$rsc = get_resources_array();

	echo "<h1>Help & Info</h1>"; //Titel angepasst <h1> by Lamborghini
	if ($_GET['site']!="")
	{
		$site = $_GET['site'];
		if ($site!="")
		{
			if (@file_exists("info/$site.php"))
			{
				include ("info/$site.php");
			}
		}
		echo "<input type=\"button\" value=\"Hilfe&uuml;bersicht\" onclick=\"document.location='?page=$page'\" />";

	}
	else
	{
		echo "Hier findest du Informationen zu verschiedenen Objekten des Spiels:<br/><br/>";
		
		infobox_start("Daten",1);
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Geb&auml;ude</td><td class=\"tbldata\">Liste aller Geb&auml;udeeigenschaften</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=buildings\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Planeten</td><td class=\"tbldata\">Liste aller Planeteneigenschaften</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=planets\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Rassen</td><td class=\"tbldata\">Vorteile und Spezialeinheiten der Rassen</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=races\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Sterne</td><td class=\"tbldata\">Eigenschaften der Sterne</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=stars\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Schiffe</td><td class=\"tbldata\">Liste aller Schiffe</td><td class=\"tbldata\"><a href=\"?page=$page&site=shipyard\" width=\"60\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Technologien</td><td class=\"tbldata\">Was die technologischen Fortschritte bringen</td><td class=\"tbldata\"><a href=\"?page=$page&site=research\" width=\"60\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Verteidigung</td><td class=\"tbldata\">Liste aller Verteidigungssysteme</td><td class=\"tbldata\"><a href=\"?page=$page&site=defense\" width=\"60\">Anzeigen</a></td></tr>";
		infobox_end(1);

		
		infobox_start("Mechanismen",1);
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Spionage</td><td class=\"tbldata\">Wie das Spionagesystem funktioniert</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=spy\">Anzeigen</a></td></tr>";
		infobox_end(1);
		
		/*infobox_start("Weitere Quellen",1)
		echo "<a href=\"http:\/\/www.etoa.ch\/forum\/index.php\">Forum</a><br\><br\>";
		echo "<a href=\"http:\/\/www.etoa.ch\/wiki\/wiki\/index.php?title=Hauptseite\"><img src=\"..\/images\/wiki.jpg\"></a><br\><br\>";
		infobox_end(1);
		*/
		

		

	}

?>
