<?PHP
	/**
	* Fleet-Action: Antrax-Attack
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
        AND techlist_tech_id='18';");
    	$tarr=mysql_fetch_array($tres);			
		
			//40% + Boni Chance, dass Antrax erflogreich
			$goornot=mt_rand(0,100);
			if ($goornor<=(40+$tarr['techlist_current_level']*5+$special_ship_bonus_antrax_food*100))
			{
				$coords_target = coords_format2($arr['fleet_planet_to']);
				$coords_from = coords_format2($arr['fleet_planet_from']);
				
				//Rechnet Schadensfaktor (Max. 90%)
				$fak = mt_rand(1,min((10+$tarr['techlist_curren_level']*3),90));
				
				//Lädt Nahrung und Bewohner
				$pres=dbquery("
				SELECT 
          planet_res_food,
          planet_people 
				FROM 
					".$db_table['planets']." 
				WHERE 
					planet_id='".$arr['fleet_planet_to']."';");
				$parr=mysql_fetch_array($pres);
				
				//Rechnet Bewohner und Nahrungsverluste
				$people=round($parr['planet_people']*$fak/100);
				$people_rest=$parr['planet_people']-$people;
				$food=round($parr['planet_res_food']*$fak/100);
				$food_rest=$parr['planet_res_food']-$food;
				
				//Zieht Nahrung und Bewohner vom Planeten ab
				dbquery("
				UPDATE 
					".$db_table['planets']." 
				SET 
                    planet_res_food='".$food_rest."',
                    planet_people='".$people_rest."'
				WHERE 
	                planet_id='".$arr['fleet_planet_to']."';");
				
				//Nachricht senden
				$text="Eine Flotte vom Planet ".$coords_from." hat einen Antraxangriff auf den Planeten ".$coords_target." verübt es starben dabei ".nf($people)." Bewohner und ".nf($food)." t Nahrung wurden dabei verbrannt.";
				send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Antraxangriff",$text);
				send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Antraxangriff",$text);
									Ranking::addBattlePoints($arr['fleet_user_id'],BATTLE_POINTS_SPECIAL,"Spezialaktion");
				
			} 
			else 
			{
				//Nachricht senden (Antraxangriff fehlgeschlagen)
				$text="Eine Flotte vom Planet ".$coords_from." hat erfolglos einen Antraxangriff auf den Planeten ".$coords_target." verübt.";
				send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Antraxangriff erfolglos",$text);
				send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Antraxangriff erfolglos",$text);
			}
	}
	
	if ($return_fleet || $binfo[0]==4)
	{
		fleet_return($arr,"hr");
	}
	else
	{
		fleet_delete($arr['fleet_id']);
	}

?>