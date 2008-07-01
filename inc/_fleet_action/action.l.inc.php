<?PHP
	/**
	* Fleet-Action: Spy-Attack (Steal technology)
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

			//Lädt Spiotech level der Kontrahenten
			//Angreiffer
			$spy_res_a=dbquery("
			SELECT 
				techlist_current_level 
			FROM 
				".$db_table['techlist']." 
			WHERE 
        techlist_user_id='".$arr['fleet_user_id']."' 
        AND techlist_tech_id='7';");
    	$spy_arr_a=mysql_fetch_array($spy_res_a);		
            	
			//Verteidiger
			$spy_res_d=dbquery("
			SELECT 
				techlist_current_level 
			FROM 
				".$db_table['techlist']." 
			WHERE 
          techlist_user_id='".$user_to_id."' 
          AND techlist_tech_id='7';");
      $spy_arr_d=mysql_fetch_array($spy_res_d);

			//3% + (Spiotech Angreiffer - Spiotech Verteidiger) + Boni Chance, dass Spioangriff erfolgreich
			$goornot = mt_rand(1,100);
			$chance=3+($spy_arr_a['techlist_current_level']-$spy_arr_d['techlist_current_level']+$special_ship_bonus_forsteal*100);
			if ($goornot<=$chance && $chance>0)
			{
				//Sucht eine zufalls Tech vom gegner aus, welche einen höheren Level als die eigenen techs haben. Es werden nur tech geladen, welche man selber schon einmal geforscht hat und die tech, die man selber grad forscht wird ausgeschlossen!
				// PATCH:  AND att.techlist_current_level>0 behebt einen Bug dass angeforschte und abgebrochene techs auch genommen werden
				// PATCH2: AND t.tech_stealable = '1' macht es möglich, das manche Forschungen gar nie abgeschaut werden können (z.B Gentech)
        $techres = dbquery("
        SELECT
            t.tech_name,
            t.tech_last_level,
            def.techlist_tech_id,
            def.techlist_current_level AS def_techlist_current_level,
            att.techlist_current_level AS att_techlist_current_level
        FROM
          ".$db_table['technologies']." AS t
            INNER JOIN
            (
                	".$db_table['techlist']." AS def
                INNER JOIN
                	".$db_table['techlist']." AS att
                ON def.techlist_tech_id = att.techlist_tech_id
                AND att.techlist_build_type!=1
                AND def.techlist_user_id='".$user_to_id."'
                AND att.techlist_user_id=".$arr['fleet_user_id']."
                AND def.techlist_current_level>att.techlist_current_level
                AND att.techlist_current_level>0
            )
            ON t.tech_id = def.techlist_tech_id
            AND t.tech_stealable = '1'
        ORDER BY
        	RAND()
        LIMIT 1;");
        $techarr=mysql_fetch_array($techres);

				if(mysql_num_rows($techres)>0)
				{
					//Beendet die eigene Forschung, falls ihr Ausbau über die maximal Stufe rausragen würde
					if($techarr['def_techlist_current_level']==$techarr['tech_last_level'])
					{
	          dbquery("
	          UPDATE
	              ".$db_table['techlist']."
	          SET
	          	techlist_current_level='".$techarr['def_techlist_current_level']."',
              techlist_build_type='0',
              techlist_build_start_time='0',
              techlist_build_end_time='0'
	          WHERE
              techlist_user_id=".$arr['fleet_user_id']."
              AND techlist_tech_id=".$techarr['techlist_tech_id']."");
          }
          else
          {
              dbquery("
              UPDATE
                  ".$db_table['techlist']."
              SET
                  techlist_current_level=".$techarr['def_techlist_current_level']."
              WHERE
                  techlist_user_id=".$arr['fleet_user_id']."
                  AND techlist_tech_id=".$techarr['techlist_tech_id']."");
          }

          //Zieht 1 Tech-Klau Schiff von der Flotte ab
          $sres=dbquery("
          SELECT 
             ship_id 
          FROM 
              ".$db_table['ships']." 
          WHERE 
          	ship_forsteal='1'");
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
              //Flotte löschen
              $return_fleet=false;
          }
                    
					//Nachricht senden
					$text="Eine Flotte vom Planeten ".$coords_from." hat erfolgreich einen Spionageangriff durchgeführt und erfuhr so die Geheimnisse der Forschung ".$techarr['tech_name']." bis zum Level ".$techarr['def_techlist_current_level']."\n";
					send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Spionageangriff",$text);
					send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Spionageangriff",$text);
					add_log(FLEET_ACTION_LOG_CAT,$text,$arr['fleet_landtime']);                    
                    
									Ranking::addBattlePoints($arr['fleet_user_id'],BATTLE_POINTS_SPECIAL,"Spezialaktion");
                    
				}
				else
				{
					//Nachricht senden (kein Abschauen möglich)
					$text="Eine Flotte vom Planeten ".$coords_from." hat erfolglos einen Spionageangriff durchgeführt.\n Das Ziel hat keine Technologie, welche eine höhere Stufe hat!";
					send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Spionageangriff erfolglos",$text);
					send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Spionageangriff erfolglos",$text);
					add_log(FLEET_ACTION_LOG_CAT,$text,$arr['fleet_landtime']);
				}
			}
			else
			{
				//Nachricht senden (Spioangriff fehlgeschlagen)
				$text="Eine Flotte vom Planeten ".$coords_from." hat erfolglos einen Spionageangriff durchgeführt.";
				send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Spionageangriff erfolglos",$text);
				send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Spionageangriff erfolglos",$text);
				add_log(FLEET_ACTION_LOG_CAT,$text,$arr['fleet_landtime']);
			}

	
	}

	if ($return_fleet || $binfo[0]==4)
	{
		fleet_return($arr,"lr");
	}
	else
	{
		fleet_delete($arr['fleet_id']);
	}
	
?>