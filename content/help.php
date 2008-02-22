<?PHP

	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	File: help.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Online-Help and info tables
	*
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	



	// BEGIN SKRIPT //

	echo "<h1>Help & Info</h1>"; //Titel angepasst <h1> by Lamborghini
	if (isset($_GET['site']) && $_GET['site']!="")
	{
		$site = $_GET['site'];
		if ($site!="")
		{
			if (@file_exists("info/$site.php"))
			{
				include ("info/$site.php");
			}
		}
		echo "&nbsp;&nbsp;<input type=\"button\" value=\"Hilfe&uuml;bersicht\" onclick=\"document.location='?page=$page'\" /><br/><br/>";

	}
	else
	{
		echo "<h2>&Uuml;bersicht</h2>";
		Help::navi();
		echo "Hier findest du Informationen zu verschiedenen Objekten des Spiels:<br/><br/>";

		infobox_start("Daten",1);
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Geb&auml;ude</td><td class=\"tbldata\">Liste aller Geb&auml;ude</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=buildings\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Technologien</td><td class=\"tbldata\">Liste aller Technologien</td><td class=\"tbldata\"><a href=\"?page=$page&site=research\" width=\"60\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Schiffe</td><td class=\"tbldata\">Liste aller Schiffe</td><td class=\"tbldata\"><a href=\"?page=$page&site=shipyard\" width=\"60\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Verteidigung</td><td class=\"tbldata\">Liste aller Verteidigungsanlagen</td><td class=\"tbldata\"><a href=\"?page=$page&site=defense\" width=\"60\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Raketen</td><td class=\"tbldata\">Liste aller Raketen</td><td class=\"tbldata\"><a href=\"?page=$page&site=missiles\" width=\"60\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Planeten</td><td class=\"tbldata\">Liste aller Planeten</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=planets\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Sterne</td><td class=\"tbldata\">Liste aller Sterne</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=stars\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Rassen</td><td class=\"tbldata\">Liste aller Rassen</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=races\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Ressourcen</td><td class=\"tbldata\">Liste aller Ressourcen</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=resources\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Einstellungen</td><td class=\"tbldata\">Grundlegende Einstellungen dieser Runde</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=settings\">Anzeigen</a></td></tr>";
		//echo "<tr><td class=\"tbltitle\" width=\"25%\">Spezialisten</td><td class=\"tbldata\">Was man mit Spezialisten machen kann</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=specialists\">Anzeigen</a></td></tr>";
		infobox_end(1);


		infobox_start("Mechanismen",1);
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Raumkarte</td><td class=\"tbldata\">Wie ist das Universum aufgebaut</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=space\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Bewohner</td><td class=\"tbldata\">Wie arbeite ich mit Bewohnern und was muss ich beachten</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=population\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Statistik</td><td class=\"tbldata\">Was sind Statistiken und wie werden sie berechnet</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=stats\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Technikbaum</td><td class=\"tbldata\">Wie lese ich daraus die Voraussetzungen ab</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=techtree\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Markt</td><td class=\"tbldata\">Wie der Marktplatz funktioniert</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=market\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Rohstoffkurse</td><td class=\"tbldata\">Welche Werte die Rohstoffe akuell haben</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=rates\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Multis und Sitting</td><td class=\"tbldata\">Wie wir Mehrfachaccounts handhaben und wie Sitting funktioniert</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=multi_sitting\">Anzeigen</a></td></tr>";
		//echo "<tr><td class=\"tbltitle\" width=\"25%\">Kryptocenter</td><td class=\"tbldata\">Wie man fremde Flottenbewegungen scannt</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=crypto\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Raketen</td><td class=\"tbldata\">Wie das Raketensystem funktioniert</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=missile_system\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Schiffsaktionen</td><td class=\"tbldata\">Die verschiedenen Aktionen in der &Uuml;bersicht</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=action\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Spionage</td><td class=\"tbldata\">Wie das Spionagesystem funktioniert</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=spy_info\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Textformatierung</td><td class=\"tbldata\">Wie man Text formatieren kann (BBcode)</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=textformat\">Anzeigen</a></td></tr>";
		echo "<tr><td class=\"tbltitle\" width=\"25%\">Urlaubsmodus</td><td class=\"tbldata\">Was das ist und wie es funktioniert</td><td class=\"tbldata\" width=\"60\"><a href=\"?page=$page&site=u_mod\">Anzeigen</a></td></tr>";
		infobox_end(1);

		infobox_start("Weitere Infos",1);
		echo "<tr>
		<td class=\"tbldata\" style=\"text-align:center;width:20%\">
			<a href=\"".FORUM_PATH."\" target=\"_Blank\"><img src=\"images/users.png\" border=\"0\" alt=\"Forum\" title=\"Forum\" style=\"width:40px;height:40px;\"></a><br/>
			Forum
		</td>
		<td class=\"tbldata\" style=\"text-align:center;width:20%\">
			<a href=\"".HELPCENTER_PATH."\" target=\"_Blank\"><img src=\"images/help.png\" border=\"0\" alt=\"FAQ\" style=\"width:40px;height:40px;\" title=\"Hilfecenter und FAQ\"></a><br/>
			Häufig gestellte Fragen (FAQ)
		</td>
		<td class=\"tbldata\" style=\"text-align:center;width:20%\">
			<a href=\"".DEVCENTER_PATH."\" target=\"_Blank\"><img src=\"images/bug.png\" border=\"0\" alt=\"FAQ\" style=\"width:40px;height:40px;\" title=\"FAQ\"></a><br/>
			Fehler melden
		</td>
		<td class=\"tbldata\" style=\"text-align:center;width:20%\">
			<a href=\"?page=contact\"><img src=\"images/mail.png\" border=\"0\" alt=\"Kontakt\" style=\"width:40px;height:40px;\" title=\"Kontakt\"></a><br/>
			Admin kontaktieren
		</td>
		<td class=\"tbldata\" style=\"text-align:center;width:20%\">
			<a href=\"?page=abuse\"><img src=\"images/abuse.png\" border=\"0\" alt=\"Missbrauch\" style=\"width:40px;height:40px;\" title=\"Missbrauch melden\"></a><br/>
			Missbrauch melden
		</td>
		</td></tr>";
		infobox_end(1);





	}


?>