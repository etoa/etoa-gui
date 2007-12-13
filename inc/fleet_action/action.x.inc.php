<?PHP
	/**
	* Fleet-Action: Gas-Attack
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


			//Lädt Gifttechnologie level
			$tres=dbquery("
			SELECT 
				techlist_current_level 
			FROM 
				".$db_table['techlist']." 
			WHERE 
        techlist_user_id='".$arr['fleet_user_id']."' 
        AND techlist_tech_id='18'");
    	$tarr=mysql_fetch_array($tres);		
		
			//40% + Boni Chance, dass Giftgasangriff erfolgreich
			$goornot=mt_rand(0,100);
			if ($goornot<=(40+$tarr['techlist_current_level']*5+$special_ship_bonus_antrax*100))
			{
				$coords_target = coords_format2($arr['fleet_planet_to']);
				$coords_from = coords_format2($arr['fleet_planet_from']);
				
				//Rechnet Prozent der Bevölkerung, die ausgelöscht werden (Max. 95%)
				$percent = mt_rand(1,min((25+$tarr['techlist_curren_level']*3),95));
				
				//Lädt Anzahl Bewohner
				$pres=dbquery("
				SELECT 
					planet_people 
				FROM 
					".$db_table['planets']." 
				WHERE 
					planet_id='".$arr['fleet_planet_to']."';");
				$parr=mysql_fetch_array($pres);
				
				//Rechnet Bewohner (Neue Anzahl und Verlust)
				$people=round($parr['planet_people']*$percent/100);
				$rest=round($parr['planet_people']-$people);
				
				//Bewohner werden abgezogen
				dbquery("
				UPDATE 
					".$db_table['planets']." 
				SET 
					planet_people='".$people."' 
				WHERE 
					planet_id='".$arr['fleet_planet_to']."';");
					
				//Nachricht senden
				$text="Eine Flotte vom Planet ".$coords_from." hat einen Giftgasangriff auf den Planeten ".$coords_target." verübt es starben dabei ".nf($rest)." Bewohner";
				send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Giftgasangriff",$text);
				send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Giftgasangriff",$text);
			} 
			else 
			{
				//Nachricht senden (Giftgasangriff fehlgeschlagen)
				$text="Eine Flotte vom Planet ".$coords_from." hat erfolglos einen Giftgasangriff auf den Planeten ".$coords_target." verübt.";
				send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Giftgasangriff erfolglos",$text);
				send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Giftgasangriff erfolglos",$text);
			}
	}

	if ($return_fleet || $binfo[0]==4)
	{
		fleet_return($arr,"xr");
	}
	else
	{
		fleet_delete($arr['fleet_id']);
	}
?>