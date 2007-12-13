<?PHP
	/**
	* Fleet-Action: Bombard
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
		
		//Lädt Bombentechlevel
		$tres=dbquery("
		SELECT 
			techlist_current_level 
		FROM 
			".$db_table['techlist']." 
		WHERE 
	    techlist_user_id='".$arr['fleet_user_id']."' 
	    AND techlist_tech_id='".BOMB_TECH_ID."'");
		$tarr=mysql_fetch_array($tres);
	
		// 10% + Bonis, dass Bombardierung erfolgreich
		$goornot=mt_rand(0,100);
		if ($goornot<=(10+(SHIP_BOMB_FACTOR*$tarr['techlist_current_level']+$special_ship_bonus_build_destroy*100)))
		{
			// Wählt EIN gebäude aus, welches nicht im bau ist
			$blres=dbquery("
			SELECT 
				buildlist_id,
				buildlist_building_id,
				buildlist_current_level
			FROM 
				".$db_table['buildlist']." 
			WHERE 
        buildlist_planet_id='".$arr['fleet_planet_to']."'
        AND buildlist_current_level>'0' 
        AND buildlist_build_type='0'
      ORDER BY
      	RAND()
      LIMIT 1;");
	            
      // Ist ein Gebäude gefunden worden...
			if(mysql_num_rows($blres)>0)
			{
				$blarr=mysql_fetch_array($blres);
				
	                //Gebäude ein Stuffe zurücksetzten
	                $blarr['buildlist_current_level']-=1;
	                
	                //Lädt Gebäudenamen
	                $bres=dbquery("
	                SELECT 
	                    building_name 
	                FROM 
	                    ".$db_table['buildings']." 
	                WHERE 
	                    building_id='".$blarr['buildlist_building_id']."'");
	                $barr=mysql_fetch_array($bres);
	                
	                //Setzt Gebäude um ein Level zurück
	                dbquery("
	                UPDATE 
	                    buildlist 
	                SET 
	                    buildlist_current_level='".$blarr['buildlist_current_level']."'
	                WHERE 
	                    buildlist_id=".$blarr['buildlist_id']."");
	                    
	                //Zieht 1 Bomberschiff von der Flotte ab
	                $sres=dbquery("
	                SELECT 
	                	ship_id 
	                FROM 
	                	".$db_table['ships']." 
	                WHERE 
	                	ship_build_destroy='1'");
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
	                $check_arr=mysql_fetch_row($check_res);	                
	                if ($check_arr[0]<=0)
	                {
	                    $return_fleet=false;
	                }
	                
	                //Nachricht senden
	                $text="Eine Flotte vom Planet ".$coords_from." hat das Gebäude ".$barr['building_name']." des Planeten ".$coords_target." um ein Level auf Stufe ".$blarr['buildlist_current_level']." zurück gesetzt";
	                send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Gebäude bombardiert",$text);
	                send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Gebäude bombardiert",$text);                    
	            }
		}
		else
		{
			$text="Eine Flotte vom Planet ".$coords_from." hat erfolglos versucht ein Gebäude des Planeten ".$coords_target." um ein Level zu senken";
			send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Bombardierung gescheitert",$text);
			send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Bombardierung gescheitert",$text);
		}
	}

	if ($return_fleet || $binfo[0]==4)
	{
		fleet_return($arr,"br");
	}
	else
	{
		fleet_delete($arr['fleet_id']);
	}

?>