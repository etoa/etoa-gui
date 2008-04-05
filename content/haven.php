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
	// 	File: haven.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Sends ships on their flights
	*
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	// BEGIN SKRIPT //

	if ($cp)
	{

		echo "<h1>Raumschiffhafen des Planeten ".$cp->name."</h1>";
		$cp->resBox();

		$fleets_away = mysql_num_rows(dbquery("
		SELECT 
			fleet_id 
		FROM 
			fleet 
		WHERE 
			fleet_user_id='".$cu->id()."' 
		AND ((fleet_entity_from='".$cp->id."' 
			AND fleet_action LIKE '%o%' 
			AND fleet_action NOT LIKE '%c%') 
			OR (fleet_entity_to='".$cp->id."' 
			AND (fleet_action LIKE '%r%'  
			OR fleet_action LIKE '%c%')));"));
		$bres = dbquery("
		SELECT 
			buildlist_current_level 
		FROM 
			buildlist 
		WHERE 
			buildlist_building_id=".FLEET_CONTROL_ID." 
			AND buildlist_user_id='".$cu->id()."' 
			AND buildlist_planet_id='".$cp->id."';");
		if (mysql_num_rows($bres)>0)
		{
			$barr = mysql_fetch_array($bres);
			$fleets_start_possible = FLEET_NOCONTROL_NUM + $barr['buildlist_current_level'];
		}
		else
		{
			$fleets_start_possible=FLEET_NOCONTROL_NUM;
		}
		$fleets_start_possible-=$fleets_away;


		//
		// Kampfsperre prüfen
		//
		if ($conf['battleban']['v']!=0 && $conf['battleban_time']['p1']<=time() && $conf['battleban_time']['p2']>time())
		{
			infobox_start("Kampfsperre");
			echo "Es ist momentan nicht m&ouml;glich andere Spieler anzugreifen. Grund: ".text2html($conf['battleban']['p1'])."<br>Die Sperre dauert vom ".date("d.m.Y",$conf['battleban_time']['p1'])." um ".date("H:i",$conf['battleban_time']['p1'])." Uhr bis am ".date("d.m.Y",$conf['battleban_time']['p2'])." um ".date("H:i",$conf['battleban_time']['p2'])." Uhr!";
			infobox_end();
		}

		//
		// Flottensperre prüfen
		//
		if ($conf['flightban']['v']!=0 && $conf['flightban_time']['p1']<=time() && $conf['flightban_time']['p2']>time())
		{
			infobox_start("Flottensperre");
			echo "Es ist momentan nicht m&ouml;glich Fl&uuml;ge zu starten. Grund: ".text2html($conf['flightban']['p1'])."<br>Die Sperre dauert vom ".date("d.m.Y",$conf['flightban_time']['p1'])." um ".date("H:i",$conf['flightban_time']['p1'])." Uhr bis am ".date("d.m.Y",$conf['flightban_time']['p2'])." um ".date("H:i",$conf['flightban_time']['p2'])." Uhr!";
			infobox_end();
		}
		
		//
		// Prüfen ob dieses Gebäude deaktiviert wurde
		//
		elseif ($dt = check_building_deactivated($cu->id(),$cp->id,FLEET_CONTROL_ID))
 		{
			infobox_start("Geb&auml;ude nicht bereit");
			echo "Dieser Raumschiffhafen ist bis ".date("d.m.Y H:i",$dt)." deaktiviert.";
			infobox_end();
		}
		//
		// Hafen-Optionen
		//
		else
		{
			
			// Set possible launch count to default 
			$fleets_start_possible = FLEET_NOCONTROL_NUM;
			
			// Add level of fleet control station to launch count
			$bres = dbquery("
			SELECT 
				buildlist_current_level 
			FROM
				buildlist
			WHERE 
				buildlist_building_id=".FLEET_CONTROL_ID." 
				AND buildlist_user_id='".$cu->id()."' 
				AND buildlist_planet_id='".$cp->id()."'
			;");
			$fleetControlLevel=0;
			if (mysql_num_rows($bres)>0)
			{
				$barr = mysql_fetch_row($bres);
				$fleetControlLevel = $barr[0];
				$fleets_start_possible += FLEET_NOCONTROL_NUM + $fleetControlLevel;
			}
	
			// Subtract current fleet count from launch count
			$fleets_away = mysql_num_rows(dbquery("
			SELECT 
				fleet_id 
			FROM 
				fleet
			WHERE 
				fleet_user_id='".$cu->id()."' 
				AND 
				(
					(
						fleet_entity_from='".$cp->id()."' 
						AND fleet_action LIKE '%o%' AND fleet_action NOT LIKE '%c%'
					) 
					OR 
					(
						fleet_entity_to='".$cp->id()."' 
						AND 
						(
							fleet_action LIKE '%r%' OR fleet_action LIKE '%c%'
						)
					)
				);"));
			$fleets_start_possible -= $fleets_away;
	
			// Piloten
			$pbarr= mysql_fetch_row(dbquery("SELECT SUM(buildlist_people_working) FROM ".$db_table['buildlist']." WHERE buildlist_planet_id='".$cp->id()."';"));
			$people_available = floor($cp->people - $pbarr[0]);			

			// Rassenspeed laden
			$rres=dbquery("SELECT race_f_fleettime FROM races WHERE race_id=".$cu->race_id.";");
			$rarr=mysql_fetch_array($rres);
			if ($rarr['race_f_fleettime']!=1)
			{
				$racefactor =  2-$rarr['race_f_fleettime'];
			}
			else
			{
				$racefactor =  1;
			}		
			
			$xajax->register(XAJAX_CALLABLE_OBJECT,$cp);				
			
			// Set vars for xajax
			$_SESSION['haven'] = Null;
			$_SESSION['haven']['user_id'] = $cu->id();
			$_SESSION['haven']['planet_id'] = $cp->id(); 
			$_SESSION['haven']['planet_cx'] = $cp->cx; 
			$_SESSION['haven']['planet_cy'] = $cp->cy; 
			$_SESSION['haven']['planet_sx'] = $cp->sx; 
			$_SESSION['haven']['planet_sy'] = $cp->sy; 
			$_SESSION['haven']['planet_p'] = $cp->pos; 
			$_SESSION['haven']['people_available'] = $people_available;
			$_SESSION['haven']['fleets_start_possible'] = $fleets_start_possible;

			if (isset ($_GET['target']) && intval($_GET['target'])>0)
			{
				$_SESSION['haven']['target']=$_GET['target'];
			}

			
    	infobox_start("Hafen-Infos",1);
    		
			// Flotten unterwegs
    	echo "<tr><th class=\"tbltitle\">Aktive Flotten:</th><td class=\"tbldata\">";
			if ($fleets_away>1)
				echo "<b>$fleets_away</b> Flotten dieses Planeten sind <a href=\"?page=fleets\">unterwegs</a>.";
			elseif ($fleets_away==1)
				echo "<b>Eine</b> Flotte dieses Planeten ist <a href=\"?page=fleets\">unterwegs</a>.";
			else
				echo "Es sind <b>keine</b> Flotten dieses Planeten unterwegs.";
			echo "</td></tr>";
		
			// Flotten startbar?
    	echo "<tr><th class=\"tbltitle\">Flottenstart:</th><td class=\"tbldata\">";
 			if ($fleets_start_possible>1 )
				echo "<b>$fleets_start_possible</b> Flotten k&ouml;nnen von diesem Planeten starten!";
			elseif ($fleets_start_possible==1 )
				echo "<b>Eine</b> Flotte kann von diesem Planeten starten!";
			else
				echo "Es k&ouml;nnen <b>keine</b> Flotten von diesem Planeten starten!";
			echo " (Flottenkontrolle: Stufe ".$fleetControlLevel.")</td></tr>";
		
			// Piloten		
    	echo "<tr><th class=\"tbltitle\">Piloten:</th><td class=\"tbldata\">";
			if ($people_available>1)
				echo "<b>".$people_available."</b> Piloten k&ouml;nnen eingesetzt werden.";
			elseif ($people_available==1)
				echo "<b>Ein</b> Pilot kann eingesetzt werden.";
			else
				echo "Es sind <b>keine</b> Piloten verf&uuml;gbar.";
			echo "</td></tr>";
					
			// Rasse		
  		if ($racefactor!=1)
  		{
  			echo "<tr><th class=\"tbltitle\">Piloten:</th><td class=\"tbldata\">";
				echo "Die Schiffe fliegen aufgrund deiner Rasseneigenschaft mit ".get_percent_string($racefactor,1)." Geschwindigkeit!";
				echo "</td></tr>";
			}
			infobox_end(1);
			
			echo "<div id=\"havenContent\">
			<div id=\"havenContentShips\" style=\"\">
			<div style=\"padding:20px\"><img src=\"images/loading.gif\" alt=\"Loading\" /> Lade Daten...</div>
			</div>
			<div id=\"havenContentTarget\" style=\"display:none;\"></div>
			<div id=\"havenContentAction\" style=\"display:none;\"></div>
			
			
			</div>";
			echo "<script type=\"text/javascript\">xajax_havenShowShips();</script>";	
	
			
			/*
			//if ($_SESSION['lastpage']!=$page) $_SESSION['haven']=Null;
			//if ($_POST['reset']!="")  $_SESSION['haven']=Null;
			//if ($_SESSION['haven']['pid']!=$cp->id) $_SESSION['haven']=Null;
			$_SESSION['haven_pid']=$cp->id();
			$_SESSION['haven_uid']=$cu->id();
			

			echo "<div id=\"havenContentShips\"></div>";
			echo "<div id=\"havenContentTarget\"></div>";
			echo "<script type=\"text/javascript\">xajax_havenShowShips();</script>";

/*
			//
			// Start durchführen
			//
			if ($_POST['submit_actionselection']!="" && $_SESSION['haven']['status']=="chooseAction" &&$fleets_start_possible>0)
			{
				require ("haven/launch.inc.php");
			}

			//
			// Aktion und Waren werden ausgewählt und der Start bestätigt
			//
			elseif ((($_POST['submit_planetselection']!="" && $_SESSION['haven']['status']=="choosePlanet") || ($_SESSION['haven']['status']=="chooseAction" && $_POST['back']=="") )&& $fleets_start_possible>0)
			{
				require ("haven/choose_action.inc.php");
			}

			//
			// Ziel auswählen
			//
			elseif (((($_POST['submit_shipselection']!="" || $_POST['submit_shipselection_all']!="") && ($_SESSION['haven']['status']=="chooseAction" || $_SESSION['haven']['status']==Null )) || ($_SESSION['haven']['status']=="choosePlanet" && $_POST['back']=="") || ($_POST['back']!="" && $_SESSION['haven']['status']=="chooseAction")) &&$fleets_start_possible>0)
			{
				require ("haven/choose_planet.inc.php");
			}

			//
			// Schiffsauswahl
			//
			else
			{    */
			//	require ("haven/choose_ships.inc.php");
				
			//}
		}
	}
?>
