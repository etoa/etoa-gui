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
	// Letzte Änderung: 10.05.2006	Lamborghini								//
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
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Gebäude</td><td class=\"tbldata\">Liste aller Gebäudeeigenschaften</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=buildings\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Planeten</td><td class=\"tbldata\">Liste aller Planeteneigenschaften</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=planets\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Rassen</td><td class=\"tbldata\">Vorteile und Spezialeinheiten der Rassen</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=races\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Sterne</td><td class=\"tbldata\">Eigenschaften der Sterne</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=stars\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Schiffe</td><td class=\"tbldata\">Liste aller Schiffe</td><td class=\"tbldata\"><a href=\"?page=$page&site=shipyard\" width=\"60\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Technologien</td><td class=\"tbldata\">Was die technologischen Fortschritte bringen</td><td class=\"tbldata\"><a href=\"?page=$page&site=research\" width=\"60\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Verteidigung</td><td class=\"tbldata\">Liste aller Verteidigungssysteme</td><td class=\"tbldata\"><a href=\"?page=$page&site=defense\" width=\"60\">Anzeigen</a></td></tr>";
		infobox_end(1);


		infobox_start("Mechanismen",1);
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Spionage</td><td class=\"tbldata\">Wie das Spionagesystem funktioniert</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=spy\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Angrifftechniken</td><td class=\"tbldata\">Die verschiedenen Angriffsaktionen in der Übersicht</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=action\">Anzeigen</a></td></tr>";
		infobox_end(1);

		infobox_start("Weitere Infos",1);
		echo "<tr><td class=\"tbldata\"><div align=\"center\"><a href=\"http://www.etoa.ch/forum/index.php\" target=\"_Blank\"><b>Forum</b></a></div></td></tr>";
		echo "<tr><td class=\"tbldata\"><div align=\"center\"><a href=\"http://www.etoa.ch/wiki/wiki/index.php?title=Hauptseite\" target=\"_Blank\"><img src=\"images/wiki.jpg\" border=\"0\" alt=\"Wiki\"></a></div></td></tr>";
		infobox_end(1);





	}


