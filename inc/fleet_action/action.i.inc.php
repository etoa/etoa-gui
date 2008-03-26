<?PHP
	/**
	* Fleet-Action: Invade
	*/
	
	//Lädt User-ID des momentanen Besitzers
    $res_check=dbquery("
		SELECT 
			planet_user_id,
			planet_user_main 
		FROM 
			planets
		WHERE 
			planet_id='".$arr['fleet_planet_to']."'
	");
	
  $arr_check=mysql_fetch_array($res_check);

  //Kontrolliert bei einer Invasion, ob der Planet nicht schon demjenigengehört gehört
  //gehört bereits dem User, dann flotte stationieren
  if($arr_check['planet_user_id']==$arr['fleet_user_id'])
  {
    //Flotte stationieren & Waren ausladen (ohne den Abzug eines Invasionsschiffes)
    $msg_ship_res=fleet_land($arr,1,0,1);

    // Flottelöschen
    fleet_delete($arr['fleet_id']);

		// Nachricht senden
    $msg = "Die Flotte hat folgendes Ziel erreicht:\n[b]Planet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Zeit:[/b] ".date("d.m.Y H:i",$arr['fleet_landtime'])."\n[b]Bericht:[/b] Die Flotte ist auf dem Planeten gelandet!";
    $msg.= $msg_ship_res[0].$msg_ship_res[1];
    send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Flotte angekommen",$msg);

    $showinfo=0;
	}
  //gehört nicht dem User, dann fight
  else
  {
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
			// Anti-Hack (exploited by Pain & co)
			// Check if an invasion ship is part of the fleet (exploited by faking the form which calls fleet_launch,
			// setting an fleet action which wasn't allowed)
			// Attention: The invasion ship could break in battle, this doesn't matter in the past, but now it will matter..
			// This issue has to be discussed, perhabs this check should be performed before the battle
       $fsres = dbquery("
				SELECT
					ship_id
				FROM
					fleet_ships
				INNER JOIN 
					ships ON fs_ship_id = ship_id
					AND fs_fleet_id='".$arr['fleet_id']."'
					AND fs_ship_faked='0'
					AND ship_invade=1;
			");	
			if (mysql_num_rows($fsres)>0)
			{			
				// Anti-Hack (exploited by Pain & co)
				// Check again if planet is no a main planet
				// Also explioted using a fake haven form, such 
				// that an invasion to an illegal target could be launched
				if ($arr_check['planet_user_main']==0)
				{
				
					$return_fleet = true;
		
					$coords_target = coords_format2($arr['fleet_planet_to']);
					$coords_from = coords_format2($arr['fleet_planet_from']);
		
					//Liest Punkte des 'Opfers' aus
		            $points_def_res=dbquery("
		            SELECT
		                user_points
		            FROM
		                ".$db_table['users']."
		            WHERE
		                user_id='".$user_to_id."';");
		            $points_def_arr=mysql_fetch_array($points_def_res);
		
					//Liest Punkte des Angreiffers aus
		            $points_att_res=dbquery("
		            SELECT
		                user_points
		            FROM
		                ".$db_table['users']."
		            WHERE
		                user_id='".$arr['fleet_user_id']."';");
		            $points_att_arr=mysql_fetch_array($points_att_res);
		
		            //Punkteverhältnis
		            $chance=INVADE_POSSIBILITY / $points_att_arr['user_points'] * $points_def_arr['user_points'];
								//echo "Invasiergrundchange: ".INVADE_POSSIBILITY.", Attp: ".$points_att_arr['user_points'].", Defp: ".$points_def_arr['user_points'];
		
		            //Prüft, ob das Verhältnis die Mindest- bzw. Maximalgrenze nicht unter- oder überschreitet
		            if($factor<1 && $chance>INVADE_MAX_POSSIBILITY)
		            	$chance = INVADE_MAX_POSSIBILITY;
		            if($factor>1 && $chance<INVADE_MIN_POSSIBILITY)
		            	$chance = INVADE_MIN_POSSIBILITY;
		
					$iposs = mt_rand(0,100);
					$iperc = intval(100*$chance);
		
					//echo " Factor $factor Chance $change iposs $iposs iperc $iperc";
		
					//Ist invasion erfolgreich? (Chance ok)
					if ($iposs<=$iperc)
					{
						// Lade Planeten des Users
						$max_planet_res = dbquery("
						SELECT 
							COUNT(planet_user_id)
						FROM 
							planets
						WHERE 
							planet_user_id='".$arr['fleet_user_id']."'
						;");
						$max_planet_arr = mysql_fetch_row($max_planet_res);
							
						//Hat der User schon die maximale Anzahl Planeten?
						if($max_planet_arr[0] < $conf['user_max_planets']['v'])
						{
		                    //Liest Planet ID und Cell ID vom HP des 'Opfers' aus
		                    $mplanet_res = dbquery("
		                    SELECT
		                        planet_id,
		                        planet_solsys_id
		                    FROM
		                        ".$db_table['planets']."
		                    WHERE
		                        planet_user_id='".$user_to_id."'
		                        AND planet_user_main='1';");
		                    $mplanet_arr = mysql_fetch_array($mplanet_res);
		
		                    // Alle Flotten des 'Opfers', zum Planeten fliegen zum Hauptplaneten schicken mit der Aktion 'Flug abgebrochen'
		                    $iflres = dbquery("
		                    SELECT
		                        fleet_landtime,
		                        fleet_launchtime,
		                        fleet_cell_to,
		                        fleet_planet_to,
		                        fleet_action,
		                        fleet_id
		                    FROM
		                        ".$db_table['fleet']."
		                    WHERE
		                        fleet_user_id='".$user_to_id."'
		                        AND fleet_planet_to='".$arr['fleet_planet_to']."' 
		                        ;");
		                    $time = time();                        
		                    if (mysql_num_rows($iflres)>0)
		                    {
			                    while ($iflarr = mysql_fetch_array($iflres))
			                    {
			                        $duration = min($time,$iflarr['fleet_landtime'])-$iflarr['fleet_launchtime'];
			                        $launchtime = $time;
			                        $landtime = $launchtime + $duration;
															$action = substr($iflarr['fleet_action'],0,1)."c";
			                        dbquery("
			                        UPDATE
			                            ".$db_table['fleet']."
			                        SET
			                            fleet_cell_from='".$iflarr['fleet_cell_to']."',
			                            fleet_cell_to='".$mplanet_arr['planet_solsys_id']."',
			                            fleet_planet_from='".$iflarr['fleet_planet_to']."',
			                            fleet_planet_to='".$mplanet_arr['planet_id']."',
			                            fleet_action='".$action."',
			                            fleet_launchtime='".$launchtime."',
			                            fleet_landtime='".$landtime."'
			                        WHERE
			                            fleet_id='".$iflarr['fleet_id']."';");
			                    }
			                    // 
			                    $text="Eure Schife, welche zum Planeten [b]".$coords_target."[/b] unterwegs waren, wurden auf euren Hauptplaneten umgeleitet!\n";
			                    send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Schiffe umgeleitet",$text);
			                  }
		
		                    // Planet übernehmen
		                    invasion_planet($arr['fleet_planet_to'],$arr['fleet_user_id']);
		
		                    //Flotte Stationieren & Waren ausladen
		                    $msg_ship_res=fleet_land($arr,1);
		
												//Gelandete Schiffe und Rohstoffe speichern
												$msg=$msg_ship_res[0].$msg_ship_res[1];
		
		                    // Nachrichten senden
		                    $text="[b]Planet:[/b] $coords_target\n[b]Besitzer:[/b] ".get_user_nick($user_to_id)."\n\nDieser Planet wurde von einer Flotte, welche vom Planeten $coords_from stammt, übernommen!\n";
		                    $text="Ein Invasionsschiff wurde bei der Übernahme aufgebraucht!\n";
		                    $text.=$msg;
		                    send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Planet erfolgreich invasiert",$text);
		                    send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Kolonie wurde invasiert",$text);
		
											Ranking::addBattlePoints($arr['fleet_user_id'],BATTLE_POINTS_SPECIAL,"Spezialaktion");
		
		
		                    $return_fleet=false;
		        }
		        //Der User hat bereits die maximale Anzahl Planeten
		        else
		        {
							$text="[b]Planet:[/b] $coords_target\n[b]Besitzer:[/b] ".get_user_nick($user_to_id)."\n\nEine Flotte vom Planeten $coords_from versuchte, das Ziel zu übernehmen. Dieser Versuch schlug aber fehl und die Flotte machte sich auf den Rückweg!";
							$text1="[b]Planet:[/b] $coords_target\n[b]Besitzer:[/b] ".get_user_nick($user_to_id)."\n\nEine Flotte vom Planeten $coords_from versuchte, das Ziel zu übernehmen. Dieser Versuch schlug aber fehl und die Flotte machte sich auf den Rückweg! Hinweis: Du hast bereits die maximale Anzahl Planeten erreicht!";
		
							send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",$text1);
							send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",$text);
		      	}
					}
					// Invasion klappte nicht
					else
					{
						$text="[b]Planet:[/b] $coords_target\n[b]Besitzer:[/b] ".get_user_nick($user_to_id)."\n\nEine Flotte vom Planeten $coords_from versuchte, das Ziel zu übernehmen. Dieser Versuch schlug aber fehl und die Flotte machte sich auf den Rückweg!";
		
						send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",$text);
						send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",$text);
		
					}
				
				}
				else
				{
					$text="[b]Planet:[/b] $coords_target\n[b]Besitzer:[/b] ".get_user_nick($user_to_id)."\n\nEine Flotte vom Planeten $coords_from versuchte, das Ziel zu übernehmen. Dies ist aber ein Hauptplanet, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
					send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",$text);
					send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",$text);
				}
			}
			else
			{
				$text="[b]Planet:[/b] $coords_target\n[b]Besitzer:[/b] ".get_user_nick($user_to_id)."\n\nEine Flotte vom Planeten $coords_from versuchte, das Ziel zu übernehmen. Leider war kein Schiff mehr in der Flotte, welches einen Planeten invasieren kann, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
				send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",$text);
				send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",$text);
			}			
		}
		if ($return_fleet || $binfo[0]==4)
		{
			fleet_return($arr,"ir");
		}
		else
		{
			fleet_delete($arr['fleet_id']);
		}

  }


?>