<?PHP
	/**
	* Fleet action: Explore
	*/
	
		$cell_id=$arr['fleet_cell_to'];
		$planet_id=$arr['fleet_planet_to'];
		
		
		//Precheck action==possible?
		$fsres = dbquery("
			SELECT
				ship_id
			FROM
				fleet_ships
			INNER JOIN 
				ships ON fs_ship_id = ship_id
				AND fs_fleet_id='".$arr['fleet_id']."'
				AND fs_ship_faked='0'
				AND ship_explore=1;");
					
		if (mysql_num_rows($fsres)>0)
		{
			
			if ($planet_id==0)
			{

				//Load celldata
				$cellres = dbquery("
					SELECT 
						* 
					FROM 
						".$db_table['space_cells']." 
					WHERE 
						cell_id='".$cell_id."';
				");
				
				$cellrow = mysql_fetch_array($cellres);
				
				//nebula?
				if ($cellrow['cell_nebula']==1)
				{
				
					//Nachricht senden
					$msg = "Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat [b]ein ".coords_format4($arr['fleet_cell_to'],0)." [/b]\num [b]".date("d.m.Y H:i",$arr['fleet_landtime'])."[/b]\n spioniert.\n";
					$msgres = "\n[b]ROHSTOFFE:[/b]\n\n".RES_CRYSTAL.": ".nf($cellrow['cell_nebula_ress'])."\n";
					send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Nebelfeld spionieren",$msg.$msgres);
	
				}
				//asteroid
				elseif ($cellrow['cell_asteroid']==1)		
				{
				
					//Nachricht senden
					$msg = "Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat [b]ein ".coords_format4($arr['fleet_cell_to'],0)." [/b]\num [b]".date("d.m.Y H:i",$arr['fleet_landtime'])."[/b]\n spioniert.\n";
					$msgres = "\nROHSTOFFE: ".nf($cellrow['cell_asteroid_ress'])."\n";
					send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Asteroidenfeld spionieren",$msg.$msgres);
	
				}
				//Field doesnt' exitst anymore
				else
				{
				
					//Nachricht senden
					$msg = "Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nkonnte [b]kein Feld [/b]spionieren, da das Feld nicht mehr existiert.\n";
					send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Asteroiden-/Nebelfeld spionieren",$msg);
		
				}
			}
			else
			{
			
				//Load Data
				$planetres = dbquery("
					SELECT 
						*
					FROM 
						".$db_table['planets']." 
					WHERE 
						planet_id='".$planet_id."';");
						
				$cellrow = mysql_fetch_array($planetres);
								
				//Gas-Planet?
				If ($cellrow['planet_type_id']==7)
				{
	
					//Nachricht senden
        			$msg = "Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat [b]den Gas-Planet (".coords_format2($arr['fleet_planet_to']).")[/b]\num [b]".date("d.m.Y H:i",$arr['fleet_landtime'])."[/b]\n spioniert.\n";
        			$msgres = "\n[b]ROHSTOFFE:[/b]\n\n".RES_FUEL.": ".nf($cellrow['planet_res_fuel'])."\n";
        			send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Gas-Planet spionieren",$msg.$msgres);

        
				}
				else
				{
					//Nachricht senden
        			$msg = "Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nkonnte [b]kein Gas-Planet [/b]spionieren.\n";
        			send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Gas-Planet spionieren",$msg);

				}
			}
						
	
		}
		else
		{
			$text="[b]Planet:[/b] $coords_target\n[b]Besitzer:[/b] ".get_user_nick($user_to_id)."\n\nEine Flotte vom Planeten $coords_from versuchte, das Ziel zu erkunden. Leider war kein Schiff mehr in der Flotte, welches erkunden kann, deshalb schlug der Versuch fehl und die Flotte machte sich auf den Rückweg!";
				send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Erkundungsversuch gescheitert",$text);							
		}
	
		$action="jr";
	
			// Flotte zurückschicken
  		fleet_return($arr,$action);
?>
					
				
				
				
			
				
			