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
	// 	Dateiname: haven_choose_ships.php
	// 	Topic: Raumschiffhafen - Schiffauswahl
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 09.06.2006
	// 	Kommentar:
	//



	// Schiff-Infos

  tableStart("Schiff-Infos");
  $res = dbquery("
  SELECT
  	ship_structure,
		ship_shield,
		ship_weapon,
		ship_heal,
		shiplist_count,
  	shiplist_special_ship_bonus_weapon, 	
  	shiplist_special_ship_bonus_structure,
  	shiplist_special_ship_bonus_shield,
  	shiplist_special_ship_bonus_heal,
 		special_ship_bonus_weapon,
 		special_ship_bonus_structure,
 		special_ship_bonus_shield,
 		special_ship_bonus_heal
  FROM
  	shiplist
  INNER JOIN
  	ships
  ON
		shiplist_ship_id=ship_id
  	AND shiplist_entity_id=".$cp->id."
  	AND shiplist_count>0;");
  $struct_tech_special=0;
  $shield_tech_special=0;
  $weaopn_tech_special=0;
  $heal_tech_special=0;
  if (mysql_num_rows($res)>0)
  {
  	$struct=0;
  	$shield=0;
  	$weapon=0;
  	$count=0;  	
  	$heal=0;
  	while ($arr=mysql_fetch_array($res))
  	{
  		$struct += $arr['ship_structure']*$arr['shiplist_count'];
  		$struct_tech_special += $arr['shiplist_special_ship_bonus_structure'] * $arr['special_ship_bonus_structure'];
  		
  		$shield += $arr['ship_shield']*$arr['shiplist_count'];
  		$shield_tech_special += $arr['shiplist_special_ship_bonus_shield'] * $arr['special_ship_bonus_shield'];
  		
  		$weapon += $arr['ship_weapon']*$arr['shiplist_count']; 
  		$weaopn_tech_special += $arr['shiplist_special_ship_bonus_weapon'] * $arr['special_ship_bonus_weapon'];
  		
  		$heal += $arr['ship_heal']*$arr['shiplist_count'];
  		$heal_tech_special += $arr['shiplist_special_ship_bonus_heal'] * $arr['special_ship_bonus_heal'];
  		
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
			techlist_user_id='".$cu->id()."'
			AND
			(
				techlist_tech_id='".STRUCTURE_TECH_ID."'
				OR techlist_tech_id='".SHIELD_TECH_ID."'
				OR techlist_tech_id='".WEAPON_TECH_ID."'
				OR techlist_tech_id='".REGENA_TECH_ID."'
			)
  		;");

			$structure_tech_name="";
			$shield_tech_name="";
			$weapon_tech_name="";
			$heal_tech_name="";
			$structure_tech_level=0;
			$shield_tech_level=0;
			$weapon_tech_level=0;
			$heal_tech_level=0;
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
      
      $struct_str = get_percent_string($structure_tech_a,1)." durch ".$structure_tech_name." ".$structure_tech_level;
      $shield_str = get_percent_string($shield_tech_a,1)." durch ".$shield_tech_name." ".$shield_tech_level;
      $weapon_str = get_percent_string($weapon_tech_a,1)." durch ".$weapon_tech_name." ".$weapon_tech_level;
      $heal_str = get_percent_string($heal_tech_a,1)." durch ".$heal_tech_name." ".$heal_tech_level;
      
      if ($struct_tech_special>0)
      {
      	$structure_tech_a += $struct_tech_special;
      	$struct_str.= ", ".get_percent_string($struct_tech_special+1,1)." durch Spezialschiffe";
      }
      if ($shield_tech_special>0)
      {
      	$shield_tech_a += $shield_tech_special;
      	$shield_str.= ", ".get_percent_string($shield_tech_special+1,1)." durch Spezialschiffe";
      }
      if ($weaopn_tech_special>0)
      {
      	$weapon_tech_a += $weaopn_tech_special;
      	$weapon_str.= ", ".get_percent_string($weaopn_tech_special+1,1)." durch Spezialschiffe";
      }
      if ($heal_tech_special>0)
      {
      	$heal_tech_a += $heal_tech_special;
      	$heal_str.= ", ".get_percent_string($heal_tech_special+1,1)." durch Spezialschiffe";
      }
      
		echo "<tr><td class=\"tbltitle\"><b>Einheit</b></td><td class=\"tbltitle\">Grundwerte</td><td class=\"tbltitle\">Aktuelle Werte</td></tr>";
  	echo "<tr>
			<td class=\"tbldata\"><b>Struktur:</b></td>
			<td class=\"tbldata\">".nf($struct)."</td>
			<td class=\"tbldata\">".nf($struct*$structure_tech_a);
			if ($structure_tech_a>1)
			{
				echo " (".$struct_str.")";
			}
			echo "</td></tr>";
  	echo "<tr><td class=\"tbldata\"><b>Schilder:</b></td>
			<td class=\"tbldata\">".nf($shield)."</td>
			<td class=\"tbldata\">".nf($shield*$shield_tech_a);
			if ($shield_tech_a>1)
			{
				echo " (".$shield_str.")";
			}
			echo "</td></tr>";
  	echo "<tr><td class=\"tbldata\"><b>Waffen:</b></td>
			<td class=\"tbldata\">".nf($weapon)."</td>
			<td class=\"tbldata\">".nf($weapon*$weapon_tech_a);
			if ($weapon_tech_a>1)
			{
				echo " (".$weapon_str.")";
			}
			echo "</td></tr>";
  	echo "<tr><td class=\"tbldata\"><b>Heilung:</b></td>
			<td class=\"tbldata\">".nf($heal)."</td>
			<td class=\"tbldata\">".nf($heal*$heal_tech_a);
			if ($heal_tech_a>1)
			{
				echo " (".$heal_str.")";
			}
			echo "</td></tr>";
  	echo "<tr><td class=\"tbldata\"><b>Anzahl Schiffe:</b></td><td class=\"tbldata\" colspan=\"2\">".nf($count)."</td></tr>";
  }
  else
  {
  	echo "<tr><td class=\"tbldata\"><i>Keine Schiffe vorhanden!</i></td></tr>";
  }
  tableEnd();



//	if ($_POST['back']=="")
//		$_SESSION['haven']=Null;
//
//	$_SESSION['haven']=Null; //<- von lambo definiert, es gab ein bug: alleschiffe auswählen -> weiter zum start -> zurück zur zielwahl -> zurück zur flotten wahl -> nur noch ein typ schiff auswählen -> weiter -> es sind wieder alle schiffe ausgewählt, jedoch nur mit der zeit von dem einen typ!

	echo "<h2>Neue Schiffsauswahl</h2>";

	// Flotten unterwegs
	$fleets_away = mysql_num_rows(dbquery("
	SELECT
		fleet_id
	FROM
		fleet
	WHERE
		fleet_user_id='".$cu->id()."'
        AND (
            (fleet_entity_from='".$cp->id."'
            AND fleet_action LIKE '%o%'
            AND fleet_action NOT LIKE '%c%')
            OR
            (fleet_entity_to='".$cp->id."'
            AND (fleet_action LIKE '%r%'
            OR fleet_action LIKE '%c%'))
        );"));

	if ($fleets_away>1)
		echo "<b>$fleets_away</b> Flotten dieses Planeten sind <a href=\"?page=fleets\">unterwegs</a>.<br/>";
	elseif ($fleets_away==1)
		echo "<b>Eine</b> Flotte dieses Planeten ist <a href=\"?page=fleets\">unterwegs</a>.<br/>";
	else
		echo "Es sind <b>keine</b> Flotten dieses Planeten unterwegs.<br/>";

	// Flotten startbar?
	//lädt flottenkontrolle level
	$bres = dbquery("
	SELECT
		buildlist_current_level
	FROM
		buildlist
	WHERE
        buildlist_building_id=".FLEET_CONTROL_ID."
        AND buildlist_user_id='".$cu->id()."'
        AND buildlist_entity_id='".$cp->id."';");
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
	if ($fleets_start_possible>1 )
		echo "<b>$fleets_start_possible</b> Flotten k&ouml;nnen von diesem Planeten starten!<br/>";
	elseif ($fleets_start_possible==1 )
		echo "<b>Eine</b> Flotte kann von diesem Planeten starten!<br/>";
	else
		echo "Es k&ouml;nnen <b>keine</b> Flotten von diesem Planeten starten!<br/>";

	// Piloten
	$pbarr= mysql_fetch_row(dbquery("
	SELECT 
		SUM(buildlist_people_working) 
	FROM 
		buildlist
	WHERE 
		buildlist_entity_id='".$cp->id."';"));
	$people_available = floor($cp->people - $pbarr[0]);

	if ($people_available>1)
		echo "<b>".$people_available."</b> Piloten k&ouml;nnen eingesetzt werden.<br/><br/>";
	elseif ($people_available==1)
		echo "<b>Ein</b> Pilot kann eingesetzt werden.<br/><br/>";
	else
		echo "Es sind <b>keine</b> Piloten verf&uuml;gbar.<br/><br/>";

	// Rassenspeed laden
	$rres=dbquery("
	SELECT 
		race_f_fleettime 
	FROM 
		races 
	WHERE 
		race_id=".$cu->raceId().";");
	$rarr=mysql_fetch_array($rres);
	if ($rarr['race_f_fleettime']!=1)
	{
		$racefactor =  2-$rarr['race_f_fleettime'];
		echo "Die Schiffe fliegen aufgrund deiner Rasseneigenschaft mit ".($racefactor*100)."% Geschwindigkeit!<br/><br/>";
	}
	else
	{
		$racefactor =  1;
	}


	// Schiffe auflisten
	$res = dbquery("
	SELECT
		*
	FROM
    shiplist AS sl
	INNER JOIN
	  ships AS s
	ON
    s.ship_id=sl.shiplist_ship_id
    AND sl.shiplist_user_id='".$cu->id()."'
    AND sl.shiplist_entity_id='".$cp->id."'
    AND sl.shiplist_count>0
	ORDER BY
		s.special_ship DESC,
		s.ship_launchable DESC,
		s.ship_name;");

	if (mysql_num_rows($res)>0)
	{
		echo "<form action=\"?page=".$page."\" method=\"post\">";

		//
		// Auflistung der Schiffe
		//
        tableStart("Verf&uuml;gbare Schiffe");
        echo "<tr><td class=\"tbltitle\" colspan=\"2\">Typ</td><td class=\"tbltitle\" valign=\"top\" width=\"110\">Speed</td><td class=\"tbltitle\" valign=\"top\" width=\"110\">Piloten</td><td class=\"tbltitle\" valign=\"top\" width=\"110\">Anzahl</td><td class=\"tbltitle\" valign=\"top\" width=\"110\">Auswahl</td></tr>\n";

        $tabulator=1;
        while ($arr = mysql_fetch_array($res))
        {

            //Geschwindigkeitsbohni der entsprechenden Antriebstechnologien laden und zusammenrechnen
            $vres=dbquery("
            SELECT
                techlist.techlist_current_level,
                technologies.tech_name,
                ship_requirements.req_req_tech_level
            FROM
                techlist,
                ship_requirements,
                technologies
            WHERE
                ship_requirements.req_ship_id=".$arr['ship_id']."
                AND technologies.tech_type_id='".TECH_SPEED_CAT."'
                AND ship_requirements.req_req_tech_id=technologies.tech_id
                AND technologies.tech_id=techlist.techlist_tech_id
                AND techlist.techlist_tech_id=ship_requirements.req_req_tech_id
                AND techlist.techlist_user_id=".$cu->id()."
            GROUP BY
                ship_requirements.req_id;");
            if ($rarr['race_f_fleettime']!=1)
                $speedtechstring="Rasse: ".((1-$rarr['race_f_fleettime'])*100)."%<br>";
            else
                $speedtechstring="";

            $timefactor=$racefactor;
            if (mysql_num_rows($vres)>0)
            {
                while ($varr=mysql_fetch_array($vres))
                {
                    if($varr['techlist_current_level']-$varr['req_req_tech_level']<=0)
                    {
                        $timefactor+=0;
                         $speedtechstring.=$varr['tech_name']." ".$varr['techlist_current_level'].": +0%<br>";
                    }
                    else
                    {
                        $timefactor+=($varr['techlist_current_level']-$varr['req_req_tech_level'])*0.1;
                        $speedtechstring.=$varr['tech_name']." ".$varr['techlist_current_level'].": +".(($varr['techlist_current_level']-$varr['req_req_tech_level'])*10)."%<br>";
                    }
                }
            }

/*
            if ($_SESSION['haven']['ships'][$arr['ship_id']]>0)
                $val = $_SESSION['haven']['ships'][$arr['ship_id']];
            else
*/
            $val=0;
            $arr['ship_speed']/=FLEET_FACTOR_F;

            if($arr['special_ship']==1)
            {
                echo "<tr><td class=\"tbldata\" style=\"width:40px;background:#000;\"><a href=\"?page=ship_upgrade&amp;id=".$arr['ship_id']."\">
                <img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id']."_small.".IMAGE_EXT."\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/></a></td>";
            }
            else
            {
                echo "<tr><td class=\"tbldata\" style=\"width:40px;background:#000;\"><a href=\"?page=help&amp;site=shipyard&amp;id=".$arr['ship_id']."\">
                <img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id']."_small.".IMAGE_EXT."\" width=\"40\" height=\"40\" alt=\"Ship\" border=\"0\"/></a></td>";
            }

            echo "<td class=\"tbldata\" ".tm($arr['ship_name'],text2html($arr['ship_shortcomment'])).">".$arr['ship_name']."</td>";
            echo "<td class=\"tbldata\" width=\"190\" ".tm("Geschwindigkeit","Grundgeschwindigkeit: ".$arr['ship_speed']." AE/h<br>$speedtechstring").">".nf($arr['ship_speed']*$timefactor)." AE/h</td>";
            echo "<td class=\"tbldata\" width=\"110\">".nf($arr['ship_pilots'])."</td>";
            echo "<td class=\"tbldata\" width=\"110\">".nf($arr['shiplist_count'])."<br/>";
            if ($people_available<$arr['shiplist_count']*$arr['ship_pilots'])
                echo "(<span title=\"Mit der momentanen Anzahl Piloten k&ouml;nnen soviel Schiffe gestartet werden\">".floor($people_available/$arr['ship_pilots']).")</span>";
            echo "</td>";
            echo "<td class=\"tbldata\" width=\"110\">";
            if ($arr['ship_launchable']==1)
            {
            	echo "<input type=\"text\" id=\"ship_count_".$arr['ship_id']."\" name=\"ship_count[".$arr['ship_id']."]\" size=\"10\" value=\"$val\"  title=\"Anzahl Schiffe eingeben, die mitfliegen sollen\" onclick=\"this.select();\" tabindex=\"".$tabulator."\" onkeyup=\"FormatNumber(this.id,this.value,".$arr['shiplist_count'].",'','');\"/>
            	<br/><a href=\"javascript:;\" onclick=\"document.getElementById('ship_count_".$arr['ship_id']."').value=".$arr['shiplist_count']."\">max</a>";
            }
            else
            {
            	echo "-";
            }
            echo "</td></tr>\n";
            $tabulator++;
        }
        tableEnd();

/*
		if (intval($_GET['planet_to'])>0)
		{
			echo "<input type=\"hidden\" name=\"planet_to\" value=\"".intval($_GET['planet_to'])."\" />";
		}
		if (intval($_GET['cell_to_id'])>0)
		{
			echo "<input type=\"hidden\" name=\"cell_to\" value=\"".intval($_GET['cell_to_id'])."\" />";
		}
*/
		if ($fleets_start_possible>0)
		{
			$btn1 = "<input type=\"submit\" name=\"submit_shipselection_all\" value=\"Alle Schiffe ausw&auml;hlen &gt;&gt;&gt;\" title=\"Klicke hier um alle Schiffe auszuw&auml;hlen\" tabindex=\"".($tabulator+2)."\">&nbsp;";
			$btn2 = "<input type=\"submit\" name=\"submit_shipselection\" value=\"Weiter zur Zielauswahl &gt;&gt;&gt;\" title=\"Wenn du die Schiffe ausgew&auml;hlt hast, klicke hier um das Ziel auszuw&auml;hlen\" tabindex=\"".($tabulator+1)."\" /> &nbsp;";
			if ($cu->getp("havenships_buttons")==1)
			{
				echo $btn2.$btn1;
			}
			else
			{
				echo $btn1.$btn2;
			}      
		}
		else
		{
			echo "<i>Es k&ouml;nnen nicht noch mehr Flotten starten! Bau zuerst deine Flottenkontrolle aus!</i>";
		}
		echo "</form>";
	}
	else
		echo "<i>Es sind keine Schiffe auf diesem Planeten vorhanden!</i>";
?>
