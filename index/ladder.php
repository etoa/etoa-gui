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
	// 	File: ladder.php
	// 	Created: 12.03.2009
	// 	Last edited: 12.03.2009
	// 	Last edited by: glaubinix <glaubinix@etoa.ch>
	//	
	/**
	* Displays user statistics
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2009 by EtoA Gaming, www.etoa.net
	*/	

	// BEGIN SKRIPT //
	
	echo "<h1>Statistiken</h1>";

	//
	// Details anzeigen
	//

		
		$ddm = new DropdownMenu(1);
		$ddm->add('total','Gesamtstatistik','xajax_statsShowBox(\'user\');');
		$ddm->add('detail','Detailstatistiken','');
		$ddm->add('special','Spezialstatistiken','');
		$ddm->add('alliances','Allianzen','xajax_statsShowBox(\'alliances\')');
		$ddm->add('pillory','Pranger','xajax_statsShowBox(\'pillory\')');
		$ddm->add('titles','Titel','xajax_statsShowBox(\'titles\');');

		$ddm->addChild('buildings','Gebäude','xajax_statsShowBox(\'buildings\');','detail');
		$ddm->addChild('tech','Forschung','xajax_statsShowBox(\'tech\');','detail');
		$ddm->addChild('ships','Schiffe','xajax_statsShowBox(\'ships\');','detail');
		$ddm->addChild('exp','Erfahrung','xajax_statsShowBox(\'exp\');','detail');

		$ddm->addChild('battle','Kampfpunkte','xajax_statsShowBox(\'battle\');','special');
		$ddm->addChild('trade','Handelspunkte','xajax_statsShowBox(\'trade\');','special');
		$ddm->addChild('diplomacy','Diplomatiepunkte','xajax_statsShowBox(\'diplomacy\');','special');
		
		$ddm->addChild('base','Allianzbasis','xajax_statsShowBox(\'base\');','alliances');

		echo $ddm; 
		
		

		echo "<br/>";

    echo "<div id=\"statsBox\">
    <div class=\"loadingMsg\">Lade Daten... <br/>(JavaScript muss aktiviert sein!)</div>";
		// >> AJAX generated content inserted here
		echo "</div>";
		
		if (isset($_GET['mode']))
		{
			$mode = $_GET['mode'];
		}
		elseif(isset($_SESSION['statsmode']))
		{
			$mode=$_SESSION['statsmode'];
		}				
		else
		{
			$mode="user";			
		}

		echo "<script type=\"text/javascript\">
		xajax_statsShowBox('".$mode."');
		</script><br/>";


		// Legende
		iBoxStart("Legende zur Statistik");
		echo "<b>Farben:</b> 
		<span class=\"userLockedColor\">Gesperrt</span>, 
		<span class=\"userHolidayColor\">Urlaubsmodus</span>, 
		<span class=\"userInactiveColor\">Inaktiv (".USER_INACTIVE_SHOW." Tage)</span>, 
		<br/>";
		echo "Letzte Aktualisierung: <b>".df($cfg->get('statsupdate'))." Uhr</b><br/>";
		echo "Die Aktualisierung der Punkte erfolgt ";
		$h = $conf['points_update']['v']/3600;
		if ($h>1)
			echo "alle $h Stunden!<br>";
		elseif ($h==1)
			echo " jede Stunde!<br>";
		else
		{
			$m = $cfg->get('points_update')/60;
			echo "alle $m Minuten!<br/>";
		}
		echo "Neu angemeldete Benutzer erscheinen erst nach der ersten Aktualisierung in der Liste.<br/>";
		echo "F&uuml;r ".STATS_USER_POINTS." verbaute Rohstoffe bekommt der Spieler 1 Punkt in der Statistik<br/>
		F&uuml;r ".STATS_ALLIANCE_POINTS." Spielerpunkte bekommt die Allianz 1 Punkt in der Statistik";
		iBoxEnd();
?>
