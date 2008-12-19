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
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/

	echo "<h1>Hilfe</h1>"; 
	
	if (isset($_GET['site']) && $_GET['site']!="")
	{
		$site = $_GET['site'];
		if ($site!="")
		{
			if (IS_ADMIN_MODE && file_exists("../info/$site.php"))
			{
				include ("../info/$site.php");
			}
			elseif (file_exists("info/$site.php"))
			{
				include ("info/$site.php");
			}
			else
			{
				err_msg("Hilfedatei nicht gefunden!");
			}
		}
		echo "&nbsp;&nbsp;<input type=\"button\" value=\"Hilfe&uuml;bersicht\" onclick=\"document.location='?page=$page'\" /><br/><br/>";

	}
	else
	{

		echo "<h2>&Uuml;bersicht</h2>";
		
		HelpUtil::breadCrumbs();
		
		echo "Hier findest du Informationen zu verschiedenen Objekten des Spiels:<br/><br/>";

		if (!IS_ADMIN_MODE)
		{
			tableStart("Tools");
			echo "<tr>
			<td style=\"text-align:center;width:20%\">
				<a href=\"?page=ticket\"><img src=\"images/abuse.png\" border=\"0\" alt=\"Missbrauch\" style=\"width:40px;height:40px;\" title=\"Ticket erstellen\"></a><br/>
				Ticket an einen Admin schreiben
			</td>
			<td style=\"text-align:center;width:20%\">
				<a href=\"?page=contact\"><img src=\"images/mail.png\" border=\"0\" alt=\"Kontakt\" style=\"width:40px;height:40px;\" title=\"Kontakt\"></a><br/>
				Admin per E-Mail kontaktieren
			</td>
			<td style=\"text-align:center;width:20%\">
				<a href=\"javascript:;\" onclick=\"".HELPCENTER_ONCLICK."\"><img src=\"images/help.png\" border=\"0\" alt=\"FAQ\" style=\"width:40px;height:40px;\" title=\"Hilfecenter und FAQ\"></a><br/>
				Häufig gestellte Fragen (FAQ)
			</td>
			<td style=\"text-align:center;width:20%\">
				<a href=\"".FORUM_PATH."\" target=\"_Blank\"><img src=\"images/users.png\" border=\"0\" alt=\"Forum\" title=\"Forum\" style=\"width:40px;height:40px;\"></a><br/>
				Forum
			</td>
			<td style=\"text-align:center;width:20%\">
				<a href=\"".DEVCENTER_PATH."\" target=\"_Blank\"><img src=\"images/bug.png\" border=\"0\" alt=\"FAQ\" style=\"width:40px;height:40px;\" title=\"FAQ\"></a><br/>
				Fehler melden
			</td>
			</td></tr>";
			tableEnd();
		}
				
		$helpNav = array();
    $helpNav["Datenbank"]["Einstellungen"] = array('settings','Grundlegende Einstellungen dieser Runde');
    $helpNav["Datenbank"]["Gebäude"] = array('buildings','Liste aller Geb&auml;ude');
    $helpNav["Datenbank"]["Planeten"] = array('planets','Liste aller Planeten');
    $helpNav["Datenbank"]["Raketen"] = array('missiles','Liste aller Raketen');
    $helpNav["Datenbank"]["Rassen"] = array('races','Liste aller Rassen');
    $helpNav["Datenbank"]["Ressourcen"] = array('resources','Liste aller Ressourcen');
    $helpNav["Datenbank"]["Rohstoffkurse"] = array('rates','Welche Werte die Rohstoffe akuell haben');
    $helpNav["Datenbank"]["Schiffe"] = array('shipyard','Liste aller Schiffe');
    $helpNav["Datenbank"]["Schiffsaktionen"] = array('action','Die verschiedenen Aktionen in der &Uuml;bersicht');
    $helpNav["Datenbank"]["Spezialisten"] = array('specialists','Was man mit Spezialisten machen kann');
    $helpNav["Datenbank"]["Sterne"] = array('stars','Liste aller Sterne');
    $helpNav["Datenbank"]["Technologien"] = array('research','Liste aller Technologien');
    $helpNav["Datenbank"]["Verteidigung"] = array('defense','Liste aller Verteidigungsanlagen');
    $helpNav["Spielmechanismen"]["Bewohner"] = array('population','Wie arbeite ich mit Bewohnern und was muss ich beachten?');
    $helpNav["Spielmechanismen"]["Kryptocenter"] = array('crypto','Wie man fremde Flottenbewegungen scannt?');
    $helpNav["Spielmechanismen"]["Markt"] = array('market','Wie der Marktplatz funktioniert?');
    $helpNav["Spielmechanismen"]["Multis und Sitting"] = array('multi_sitting','Wie wir Mehrfachaccounts handhaben und wie Sitting funktioniert?');
    $helpNav["Spielmechanismen"]["Raketen"] = array('missile_system','Wie das Raketensystem funktioniert?');
    $helpNav["Spielmechanismen"]["Raumkarte"] = array('space','Wie ist das Universum aufgebaut?');
    $helpNav["Spielmechanismen"]["Spezialpunkte"] = array('specialpoints','Wie man Spezialpunkte und Titel erwerben kann?');
    $helpNav["Spielmechanismen"]["Spionage"] = array('spy_info','Wie das Spionagesystem funktioniert?');
    $helpNav["Spielmechanismen"]["Statistik"] = array('stats','Was sind Statistiken und wie werden sie berechnet?');
    $helpNav["Spielmechanismen"]["Technikbaum"] = array('techtree','Wie lese ich daraus die Voraussetzungen ab?');
    $helpNav["Spielmechanismen"]["Textformatierung"] = array('textformat','Wie man Text formatieren kann (BBcode)?');
    $helpNav["Spielmechanismen"]["Urlaubsmodus"] = array('u_mod','Was das ist und wie es funktioniert?');
    $helpNav["Spielmechanismen"]["Wärme- und Kältebonus"] = array('tempbonus','Welche Auswirkungen hat die Planetentemperatur?');
  
		foreach ($helpNav as $cat => $data)
		{
			tableStart($cat);
			foreach ($data as $title=>$item)
			{
				echo "<tr><td width=\"35%\"><b><a href=\"?page=$page&site=".$item[0]."\">".$title."</b></td><td>".$item[1]."</td></tr>";
			}
			tableEnd();
		}    


	}
?>