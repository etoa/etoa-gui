<?PHP
	/**
	* Fleet action: Explore
	*/
	
			$cell_id=$arr['fleet_cell_to'];
			$planet_id=$arr['fleet_planet_to'];

			// ist das nebel/asteroid feld noch vorhanden?
			$res_nop = dbquery("
				SELECT 
					cell_nebula,
					cell_asteroid 
				FROM 
					".$db_table['space_cells']." 
				WHERE 
					cell_id='".$cell_id."';
			");
			
			// ist es ein Gas-Planet?
			$res_okp = dbquery("
				SELECT 
					planet_type_id
				FROM 
					".$db_table['planets']." 
				WHERE 
					planet_id='".$planet_id."';
			");
			
			//Nebel OK
			If ($res_nop['cell_nebula']==1)
			{
				$res = dbquery("
					SELECT
						cell_nebula_ress
					FROM
						".$db_table['space_cells']."
					WHERE
						cell_id='".$cell_id."';
				");
				
				//Nachricht senden
        $msg = "Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat [b]ein Nebelfeld [/b]\num [b]".date("d.m.Y H:i",$arr['fleet_landtime'])."[/b]\n spioniert.\n";
        $msgres = "\n[b]ROHSTOFFE:[/b]\n\n".RES_CRYSTAL.": ".nf($res)."\n";
        send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Nebelfeld spionieren",$msg.$msgres);

        //Log schreiben
        add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] hat [b]ein Nebelfeld [/b] um [b]".date("d.m.Y H:i",$arr['fleet_landtime'])."[/b]\n spioniert.\n".);
        
			}
			Else
			{
				
				//Asteroid OK
				If ($res_nop['cell_asteroid']==1)
				{
					$res = dbquery("
						SELECT
							cell_asteroid_ress
						FROM
							".$db_table['space_cells']."
						WHERE
							cell_id='".$cell_id."';
					");
				
					//Nachricht senden
        	$msg = "Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat [b]ein Asteroidenfeld [/b]\num [b]".date("d.m.Y H:i",$arr['fleet_landtime'])."[/b]\n spioniert.\n";
        	$msgres = "\n[b]ROHSTOFFE:[/b]\n\n".RES_METAL.": ".nf($res)."\n";
        	send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Asteroidenfeld spionieren",$msg.$msgres);

        	//Log schreiben
        	add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] hat [b]ein Gas-Planet [/b] um [b]".date("d.m.Y H:i",$arr['fleet_landtime'])."[/b]\n spioniert.\n".);
        
					
					}
					Else
					{
								
						//Gas-Planet OK
						If ($res_okp['planet_type_id']==7)
						{
							$res = dbquery("
								SELECT 
									planet_res_fuel
								FROM 
									".$db_table['planets']." 
								WHERE 
									planet_id='".$planet_id."';
							");

							//Nachricht senden
        			$msg = "Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat [b]ein Gas-Planet [/b]\num [b]".date("d.m.Y H:i",$arr['fleet_landtime'])."[/b]\n spioniert.\n";
        			$msgres = "\n[b]ROHSTOFFE:[/b]\n\n".RES_FUEL.": ".nf($res)."\n";
        			send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Gas-Planet spionieren",$msg.$msgres);

        			//Log schreiben
        			add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] hat [b]ein Gas-Planet [/b] um [b]".date("d.m.Y H:i",$arr['fleet_landtime'])."[/b]\n spioniert.\n".);
        
						}
						Else
						{
							//Nachricht senden
        			$msg = "Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nkonnte [b]kein Gas-Planet [/b]spionieren.\n";
        			send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Gas-Planet spionieren",$msg);

        			//Log schreiben
        			add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] konnte [b]kein Gas-Planet [/b]spionieren.\n".);
						}
						
					//Nachricht senden
        	$msg = "Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nkonnte [b]kein Asteroidfeld [/b]spionieren.\n";
        	send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Gas-Planet spionieren",$msg);

        	//Log schreiben
        	add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] konnte [b]kein Asteroidfeld [/b]spionieren.\n".);
					}
					
			//Nachricht senden
      $msg = "Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nkonnte [b]kein Nebelfeld [/b]spionieren.\n";
      send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Gas-Planet spionieren",$msg);

     	//Log schreiben
     	add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] konnte [b]kein Nebelfeld [/b]spionieren.\n".);
			}
							
			$action="jo";
	
			// Flotte zurückschicken
  		fleet_return($arr,$action);
?>
					
				
				
				
			
				
			