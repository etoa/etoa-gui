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
	// 	Dateiname: haven.php
	// 	Topic: Raumschiffhafen
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 09.06.2006
	// 	Kommentar:
	//

	// DATEN LADEN

	$rsc = get_resources_array();

	// DEFINITIONEN //

	define(USER_MAX_PLANETS,$conf['user_max_planets']['v']);
	define(IMAGE_PATH,"images/style");
	define(IMAGE_SHIP_DIR,"ships");
	define(FLEET_CONTROL_ID,11);
	define(FLEET_NOCONTROL_NUM,1);
	define(TECH_SPEED_CAT,1);
	define(TECH_WORMHOLE,22);
	define(DEFAULT_ACTION,"fo");

	define(FLEET_FACTOR_F,$conf['flight_flight_time']['v']);
	define(FLEET_FACTOR_S,$conf['flight_start_time']['v']);
	define(FLEET_FACTOR_L,$conf['flight_land_time']['v']);


	// BEGIN SKRIPT //

	if ($planets->current)
	{
		$c = $planets->getCurrentData();

		echo "<h1>Raumschiffhafen des Planeten ".$c->name."</h1>";
		$c->resBox();

		$fleets_away = mysql_num_rows(dbquery("
		SELECT 
		fleet_id 
		FROM 
		".$db_table['fleet']." 
		WHERE 
		fleet_user_id='".$_SESSION[ROUNDID]['user']['id']."' 
		AND ((fleet_planet_from='".$c->id."' AND fleet_action LIKE '%o%' AND fleet_action NOT LIKE '%c%') 
			OR (fleet_planet_to='".$c->id."' AND (fleet_action LIKE '%r%'  OR fleet_action LIKE '%c%')));"));
		$bres = dbquery("SELECT buildlist_current_level FROM ".$db_table['buildlist']." WHERE buildlist_building_id=".FLEET_CONTROL_ID." AND buildlist_user_id='".$_SESSION[ROUNDID]['user']['id']."' AND buildlist_planet_id='".$c->id."';");
		if (mysql_num_rows($bres)>0)
		{
			$barr = mysql_fetch_array($bres);
			$fleets_start_possible = FLEET_NOCONTROL_NUM + $barr['buildlist_current_level'];
		}
		else
			$fleets_start_possible=FLEET_NOCONTROL_NUM;
		$fleets_start_possible-=$fleets_away;


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
		elseif ($dt = check_building_deactivated($_SESSION[ROUNDID]['user']['id'],$c->id,FLEET_CONTROL_ID))
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
			if ($_SESSION['lastpage']!=$page) $_SESSION['haven']=Null;
			if ($_POST['reset']!="")  $_SESSION['haven']=Null;
			if ($_SESSION['haven']['pid']!=$c->id) $_SESSION['haven']=Null;
			$_SESSION['haven']['pid']=$c->id;

			//
			// Start durchführen
			//
			if ($_POST['submit_actionselection']!="" && $_SESSION['haven']['status']=="chooseAction" &&$fleets_start_possible>0)
			{
				include ("inc/haven_launch.inc.php");
			}

			//
			// Aktion und Waren werden ausgewählt und der Start bestätigt
			//
			elseif ((($_POST['submit_planetselection']!="" && $_SESSION['haven']['status']=="choosePlanet") || ($_SESSION['haven']['status']=="chooseAction" && $_POST['back']=="") )&& $fleets_start_possible>0)
			{
				include ("inc/haven_choose_action.inc.php");
			}

			//
			// Ziel auswählen
			//
			elseif (((($_POST['submit_shipselection']!="" || $_POST['submit_shipselection_all']!="") && ($_SESSION['haven']['status']=="chooseAction" || $_SESSION['haven']['status']==Null )) || ($_SESSION['haven']['status']=="choosePlanet" && $_POST['back']=="") || ($_POST['back']!="" && $_SESSION['haven']['status']=="chooseAction")) &&$fleets_start_possible>0)
			{
				include ("inc/haven_choose_planet.inc.php");
			}

			//
			// Schiffsauswahl
			//
			else
			{
				include ("inc/haven_choose_ships.inc.php");
			}
		}
	}
?>
