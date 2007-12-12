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

	// DATEN LADEN

	$rsc = get_resources_array();


	// BEGIN SKRIPT //

	if ($planets->current)
	{
		$c = $planets->getCurrentData();

		echo "<h1>Raumschiffhafen des Planeten ".$c->name."</h1>";
		$c->resBox();





	// Schiff-Infos
	ob_start();

  infobox_start("Schiff-Infos",1);
  $res = dbquery("
  SELECT
  	ship_structure,
		ship_shield,
		ship_weapon,
		ship_heal,
		shiplist_count
  FROM
  	shiplist
  INNER JOIN
  	ships
  ON
		shiplist_ship_id=ship_id
  	AND shiplist_planet_id=".$c->id.";");
  if (mysql_num_rows($res)>0)
  {
  	$struct=0;$shield=0;$weapon=0;$count=0;  	
  	while ($arr=mysql_fetch_array($res))
  	{
  		$struct += $arr['ship_structure']*$arr['shiplist_count'];
  		$shield += $arr['ship_shield']*$arr['shiplist_count'];
  		$weapon += $arr['ship_weapon']*$arr['shiplist_count'];
  		$heal += $arr['ship_heal']*$arr['shiplist_count'];
  		$count += $arr['shiplist_count'];
  	}

		// Forschung laden und bonus dazu rechnen 
    // Liest Level der Waffen-,Schild-,Panzerungs-,Regena Tech aus Datenbank (att)
		$weapon_tech_a=1;
		$structure_tech_a=1;
    $shield_tech_a=1;
    $heal_tech_a=1;

    $techres_a = dbquery("
		SELECT
			techlist_tech_id,
			techlist_current_level,
			tech_name
		FROM
			techlist
		INNER JOIN
			technologies
		ON 
			techlist_tech_id=tech_id
		AND
			techlist_user_id='".$s['user']['id']."'
			AND
			(
				techlist_tech_id='".STRUCTURE_TECH_ID."'
				OR techlist_tech_id='".SHIELD_TECH_ID."'
				OR techlist_tech_id='".WEAPON_TECH_ID."'
				OR techlist_tech_id='".REGENA_TECH_ID."'
			)
  		;");

      while ($techarr_a = mysql_fetch_array($techres_a))
      {
          if ($techarr_a['techlist_tech_id']==SHIELD_TECH_ID)
					{
              $shield_tech_a+=($techarr_a['techlist_current_level']/10);
							$shield_tech_name = $techarr_a["tech_name"];
							$shield_tech_level = $techarr_a["techlist_current_level"];
					}
          if ($techarr_a['techlist_tech_id']==STRUCTURE_TECH_ID)
					{
              $structure_tech_a+=($techarr_a['techlist_current_level']/10);
							$structure_tech_name = $techarr_a["tech_name"];
							$structure_tech_level = $techarr_a["techlist_current_level"];
					}
          if ($techarr_a['techlist_tech_id']==WEAPON_TECH_ID)
					{
              $weapon_tech_a+=($techarr_a['techlist_current_level']/10);
							$weapon_tech_name = $techarr_a["tech_name"];
							$weapon_tech_level = $techarr_a["techlist_current_level"];
					}
          if ($techarr_a['techlist_tech_id']==REGENA_TECH_ID)
					{
              $heal_tech_a+=($techarr_a['techlist_current_level']/10);
							$heal_tech_name = $techarr_a["tech_name"];
							$heal_tech_level = $techarr_a["techlist_current_level"];
					}
      }

		echo "<tr><td class=\"tbltitle\"><b>Einheit</b></td><td class=\"tbltitle\">Grundwerte</td><td class=\"tbltitle\">Aktuelle Werte</td></tr>";
  	echo "<tr>
			<td class=\"tbldata\"><b>Struktur:</b></td>
			<td class=\"tbldata\">".nf($struct)."</td>
			<td class=\"tbldata\">".nf($struct*$structure_tech_a);
			if ($structure_tech_a>1)
			{
				echo " (".get_percent_string($structure_tech_a,1)." durch ".$structure_tech_name." ".$structure_tech_level.")";
			}
			echo "</td></tr>";
  	echo "<tr><td class=\"tbldata\"><b>Schilder:</b></td>
			<td class=\"tbldata\">".nf($shield)."</td>
			<td class=\"tbldata\">".nf($shield*$shield_tech_a);
			if ($shield_tech_a>1)
			{
				echo " (".get_percent_string($shield_tech_a,1)." durch ".$shield_tech_name." ".$shield_tech_level.")";
			}
			echo "</td></tr>";
  	echo "<tr><td class=\"tbldata\"><b>Waffen:</b></td>
			<td class=\"tbldata\">".nf($weapon)."</td>
			<td class=\"tbldata\">".nf($weapon*$weapon_tech_a);
			if ($weapon_tech_a>1)
			{
				echo " (".get_percent_string($weapon_tech_a,1)." durch ".$weapon_tech_name." ".$weapon_tech_level.")";
			}
			echo "</td></tr>";
  	echo "<tr><td class=\"tbldata\"><b>Heilung:</b></td>
			<td class=\"tbldata\">".nf($heal)."</td>
			<td class=\"tbldata\">".nf($heal*$heal_tech_a);
			if ($heal_tech_a>1)
			{
				echo " (".get_percent_string($heal_tech_a,1)." durch ".$heal_tech_name." ".$heal_tech_level.")";
			}
			echo "</td></tr>";
  	echo "<tr><td class=\"tbldata\"><b>Anzahl Schiffe:</b></td><td class=\"tbldata\" colspan=\"2\">".nf($count)."</td></tr>";
  }
  else
  {
  	echo "<tr><td class=\"tbldata\"><i>Keine Schiffe vorhanden!</i></td></tr>";
  }
  infobox_end(1);
  echo ob_get_contents();
  ob_end_clean();









		$fleets_away = mysql_num_rows(dbquery("
		SELECT 
		fleet_id 
		FROM 
		".$db_table['fleet']." 
		WHERE 
		fleet_user_id='".$s['user']['id']."' 
		AND ((fleet_planet_from='".$c->id."' AND fleet_action LIKE '%o%' AND fleet_action NOT LIKE '%c%') 
			OR (fleet_planet_to='".$c->id."' AND (fleet_action LIKE '%r%'  OR fleet_action LIKE '%c%')));"));
		$bres = dbquery("SELECT buildlist_current_level FROM ".$db_table['buildlist']." WHERE buildlist_building_id=".FLEET_CONTROL_ID." AND buildlist_user_id='".$s['user']['id']."' AND buildlist_planet_id='".$c->id."';");
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
			if ($_SESSION['lastpage']!=$page) $_SESSION['haven']=Null;
			if ($_POST['reset']!="")  $_SESSION['haven']=Null;
			if ($_SESSION['haven']['pid']!=$c->id) $_SESSION['haven']=Null;
			$_SESSION['haven']['pid']=$c->id;

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
			{
				require ("haven/choose_ships.inc.php");
			}
		}
	}
?>
