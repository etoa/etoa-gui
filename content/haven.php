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

	if ($planets->current)
	{
		$c = $planets->getCurrentData();

		echo "<h1>Raumschiffhafen des Planeten ".$c->name."</h1>";
		$c->resBox();
		
		//
		// Kampfsperre prüfen
		//
		if ($conf['battleban']['v']!=0 && $conf['battleban_time']['p1']<time() && $conf['battleban_time']['p2']>time())
		{
			infobox_start("Kampfsperre");
			echo "Es ist momentan nicht m&ouml;glich andere Spieler anzugreifen. Grund: ".text2html($conf['battleban']['p1'])."<br>Die Sperre dauert vom ".date("d.m.Y",$conf['battleban_time']['p1'])." um ".date("H:i",$conf['battleban_time']['p1'])." Uhr bis am ".date("d.m.Y",$conf['battleban_time']['p2'])." um ".date("H:i",$conf['battleban_time']['p2'])." Uhr!";
			infobox_end();
		}

		//
		// Flottensperre prüfen
		//
		if ($conf['flightban']['v']!=0 && $conf['flightban_time']['p1']<time() && $conf['flightban_time']['p2']>time())
		{
			infobox_start("Flottensperre");
			echo "Es ist momentan nicht m&ouml;glich Fl&uuml;ge zu starten. Grund: ".text2html($conf['flightban']['p1'])."<br>Die Sperre dauert vom ".date("d.m.Y",$conf['flightban_time']['p1'])." um ".date("H:i",$conf['flightban_time']['p1'])." Uhr bis am ".date("d.m.Y",$conf['flightban_time']['p2'])." um ".date("H:i",$conf['flightban_time']['p2'])." Uhr!";
			infobox_end();
		}
		//
		// Flottensperre (alt) prüfen
		//
		elseif ($conf['deactivate_fleet']['v']==1 && $conf['deactivate_fleet']['p1']>time())
		{
			infobox_start("Flottensperre");
			echo "Der Raumschiffhafen wurde bis ".date("d.m.Y H:i",$conf['deactivate_fleet']['p1'])." deaktiviert!";
			infobox_end();
		}
		//
		// Prüfen ob dieses Gebäude deaktiviert wurde
		//
		elseif ($dt = check_building_deactivated($s['user']['id'],$c->id,FLEET_CONTROL_ID))
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
				AND buildlist_user_id='".$s['user']['id']."' 
				AND buildlist_planet_id='".$c->id."'
			;");
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
				fleet_user_id='".$s['user']['id']."' 
				AND 
				(
					(
						fleet_planet_from='".$c->id."' 
						AND fleet_action LIKE '%o%' AND fleet_action NOT LIKE '%c%'
					) 
					OR 
					(
						fleet_planet_to='".$c->id."' 
						AND 
						(
							fleet_action LIKE '%r%' OR fleet_action LIKE '%c%'
						)
					)
				);"));
			$fleets_start_possible -= $fleets_away;
	
			// Piloten
			$parr= mysql_fetch_row(dbquery("SELECT planet_people FROM ".$db_table['planets']." WHERE planet_id='".$c->id."';"));
			$pbarr= mysql_fetch_row(dbquery("SELECT SUM(buildlist_people_working) FROM ".$db_table['buildlist']." WHERE buildlist_planet_id='".$c->id."';"));
			$people_available = floor($parr[0]-$pbarr[0]);			

			// Rassenspeed laden
			$rres=dbquery("SELECT race_f_fleettime FROM ".$db_table['races']." WHERE race_id=".$s['user']['race_id'].";");
			$rarr=mysql_fetch_array($rres);
			if ($rarr['race_f_fleettime']!=1)
			{
				$racefactor =  2-$rarr['race_f_fleettime'];
			}
			else
			{
				$racefactor =  1;
			}		
			
				$xajax->register(XAJAX_CALLABLE_OBJECT,$c);				
			
			// Set vars for xajax
			$_SESSION['haven'] = Null;
			$_SESSION['haven']['user_id'] = $s['user']['id'];
			$_SESSION['haven']['planet_id'] = $c->id; 
			$_SESSION['haven']['planet_cx'] = $c->cx; 
			$_SESSION['haven']['planet_cy'] = $c->cy; 
			$_SESSION['haven']['planet_sx'] = $c->sx; 
			$_SESSION['haven']['planet_sy'] = $c->sy; 
			$_SESSION['haven']['planet_p'] = $c->solsys_pos; 
			$_SESSION['haven']['people_available'] = $people_available;
			$_SESSION['haven']['fleets_start_possible'] = $fleets_start_possible;
			if (isset ($_GET['planet_to']) && intval($_GET['planet_to'])>0)
			{
				$_SESSION['haven']['target_planet'] = intval($_GET['planet_to']);
			}
			if (isset ($_GET['cell_to_id']) && intval($_GET['cell_to_id'])>0)
			{
				$_SESSION['haven']['target_cell'] = intval($_GET['cell_to_id']);
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
			// Check if lastpage was also haven, if reset var ist set or if current planet is not former haven planet
			if ($_SESSION['lastpage']!=$page || isset($_GET['reset']) || (isset($_SESSION['haven']['pid']) && $_SESSION['haven']['pid']!=$c->id)) 
			{
				echo "Reseting haven session...<br/>";
				// Reset haven session
				$_SESSION['haven']=Null;
			}
			// Set curent planet id
			$_SESSION['haven']['pid']=$c->id;
			
			// Set action
			$action = (isset($_GET['action']) && $_GET['action']!="") ? $_GET['action'] : "ships";

			//
			// Start durchführen
			//
			// isset($_POST['submit_actionselection'])!="" && $_SESSION['haven']['status']=="chooseAction" &&$fleets_start_possible>0
			if ($action=="launch")
			{
				require ("haven/launch.inc.php");
			}

			//
			// Aktion und Waren werden ausgewählt und der Start bestätigt
			//
			// ((isset($_POST['submit_planetselection']) && isset($_SESSION['haven']['status']) && $_SESSION['haven']['status']=="choosePlanet") || (isset($_SESSION['haven']['status']) && $_SESSION['haven']['status']=="chooseAction" && $_POST['back']=="") )&& $fleets_start_possible>0
			elseif ($action=="action")
			{
				require ("haven/choose_action.inc.php");
			}

			//
			// Ziel auswählen
			//
			// (((isset($_POST['submit_shipselection'])!="" || isset($_POST['submit_shipselection_all'])) && (isset($_SESSION['haven']['status']) && ($_SESSION['haven']['status']=="chooseAction" || $_SESSION['haven']['status']==Null) )) || (isset($_SESSION['haven']['status']) && $_SESSION['haven']['status']=="choosePlanet" && $_POST['back']=="") || ($_POST['back']!="" && isset($_SESSION['haven']['status']) &&  $_SESSION['haven']['status']=="chooseAction")) &&$fleets_start_possible>0
			elseif ($action=="target")
			{
				require ("haven/choose_planet.inc.php");
			}

			//
			// Schiffsauswahl
			//
			else
			{
				require ("haven/choose_ships.inc.php");
			} */
		}
	}
?>
