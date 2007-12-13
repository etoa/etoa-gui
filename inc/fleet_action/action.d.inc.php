<?PHP
	/**
	* Fleet-Action: EMP-Attack
	*/

	// Calc battle
	require_once("inc/battle.inc.php");
	$binfo = battle($arr['fleet_id'],$arr['fleet_planet_to']);

	// Send messages
	$user_to_id = get_user_id_by_planet($arr['fleet_planet_to']);
	send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Kampfbericht (".$binfo[2].")",$binfo[1]);
	send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Kampfbericht (".$binfo[3].")",$binfo[1]);

	// Add log
	add_log(1,$binfo[1],$arr['fleet_landtime']);

	// Aktion durchführen
	if ($binfo[0]==1)
	{
		$return_fleet = true;
		require('inc/fleet_loadspecial.inc.php');

		$coords_target = coords_format2($arr['fleet_planet_to']);
		$coords_from = coords_format2($arr['fleet_planet_from']);
		
		//Lädt EMP-Tech level
		$tres=dbquery("
		SELECT 
			techlist_current_level 
		FROM 
			".$db_table['techlist']." 
		WHERE 
              techlist_user_id='".$arr['fleet_user_id']."' 
              AND techlist_tech_id='17'");
          $tarr=mysql_fetch_array($tres);
		
		//10% + Bonis, dass Deaktivierung erfolgreich
		$goornot=mt_rand(0,100);
		if ($goornot<(10+(SHIP_BOMB_FACTOR*$tarr['techlist_current_level']+$special_ship_bonus_deactivade*100)))
		{
			//Generiert Zufallswert, wie viele Stunden das Gebäude deaktiviert wird (min. 1h)
			$percent = mt_rand(1,(10+$tarr['techlist_current_level']));
			$plus=$percent*3600;
			$h=floor($plus/3600);
			if ($tarr['techlist_current_level']==0)
			{
				$tarr['techlist_current_level']=1;
			}

			//Lädt Zufällig Schiffswerft, Waffenfabrik, Flottenkontrolle, Raketensilo oder Kryptocenter
			$bres=dbquery("
			SELECT
				buildlist_deactivated,
				buildlist_building_id
			FROM
				".$db_table['buildlist']."
			WHERE
				buildlist_planet_id='".$arr['fleet_planet_to']."'
				AND buildlist_current_level > 0
				AND (
				buildlist_building_id='".FLEET_CONTROL_ID."' 
				OR buildlist_building_id='".FACTORY_ID."' 
				OR buildlist_building_id='".SHIPYARD_ID."'
				OR buildlist_building_id='".BUILD_MISSILE_ID."'
				OR buildlist_building_id='".BUILD_CRYPTO_ID."'
				)
			ORDER BY
				RAND()
			LIMIT 1;");
			if (mysql_num_rows($bres)>0)
			{
				$barr=mysql_fetch_array($bres);

                //Rechnet die Deaktivierungszeit (summiert Zeit)
                  $time=max($arr['fleet_landtime'],$barr['buildlist_deactivated']);
                  $time_to_add=$time+$plus;

                  //Deaktivierzeit Updaten
                  dbquery("
                  UPDATE
                      ".$db_table['buildlist']."
                  SET
                      buildlist_deactivated='".$time_to_add."'
                  WHERE
                      buildlist_planet_id='".$arr['fleet_planet_to']."'
                      AND buildlist_building_id='".$barr['buildlist_building_id']."'");

                  //Lädt Gebäudename
                  $name_res = dbquery("
                  SELECT 
                      building_name 
                  FROM 
                      ".$db_table['buildings']." 
                  WHERE 
                      building_id='".$barr['buildlist_building_id']."';");
                  $name_arr=mysql_fetch_array($name_res);


                  //Zieht 1 Deaktivierungsbomber von der Flotte ab
                  $sres=dbquery("
                  SELECT 
                      ship_id 
                  FROM 
                      ".$db_table['ships']." 
                  WHERE 
                      ship_deactivade='1'");
                  $sarr=mysql_fetch_array($sres);
                  
                  dbquery("
                  UPDATE 
                      ".$db_table['fleet_ships']." 
                  SET 
                      fs_ship_cnt=fs_ship_cnt-1 
                  WHERE 
                      fs_fleet_id='".$arr['fleet_id']."'
                      AND fs_ship_id='".$sarr['ship_id']."';");

                  //Wenn kein Schiff mehr in der flotte ist, kein rückflug
                  $check_res=dbquery("
                  SELECT 
                      SUM(fs_ship_cnt) AS cnt 
                  FROM 
                      ".$db_table['fleet_ships']." 
                  WHERE 
                      fs_fleet_id='".$arr['fleet_id']."';");
                  $check_arr=mysql_fetch_array($check_res);
                  
                  if ($check_arr['cnt']<=0)
                  {
										$return_fleet=false;
                  }

                  //Nachricht senden
                  $text="Eine Flotte vom Planet ".$coords_from." hat auf dem Planeten ".$coords_target." das Gebäude \"".$name_arr['building_name']."\"  für ".$h."h deaktiviert.";
                  send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Deaktivierung",$text);
                  send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Deaktivierung",$text);

			}
			else
			{
				//Nachricht senden (Es wurden noch keine Gebäude gebaut, welche deaktiviert werden können)
				$text="Eine Flotte vom Planet ".$coords_from." hat erfolglos versucht auf dem Planeten ".$coords_target." ein Gebäude zu deaktivieren.\nHinweis: Der Spieler hat keine Gebäudeeinrichtungen, welche deaktiviert werden können!";
				send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Deaktivierung erfolglos",$text);
				send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Deaktivierung erfolglos",$text);
			}
		}
		else
		{
			//Nachricht senden (Deaktivierung fehlgeschlagen)
			$text="Eine Flotte vom Planet ".$coords_from." hat erfolglos versucht auf dem Planeten ".$coords_target." ein Gebäude zu deaktivieren.";
			send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Deaktivierung erfolglos",$text);
			send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Deaktivierung erfolglos",$text);
		}
	}
	
	if ($return_fleet || $binfo[0]==4)
	{
		fleet_return($arr,"dr");
	}
	else
	{
		fleet_delete($arr['fleet_id']);
	}
?>