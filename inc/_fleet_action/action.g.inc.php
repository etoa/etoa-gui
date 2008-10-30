<?PHP
	/**
	* Fleet-Action: Gas collect on gas planet
	*/
			$destroy=0;
			if (mt_rand(0,100)>80)	// 20 % Chance dass Schiffe überhaupt zerstört werden
      			$destroy=mt_rand(0,10);		// 0 <= X <= 10 Prozent an Schiffen werden Zerstört
			if($destroy>0)
			{
          $cnt_res=dbquery("
					SELECT
						s.ship_name,
						fs.fs_ship_id,
						fs.fs_ship_cnt
					FROM
						(
							fleet_ships AS fs 
							INNER JOIN 
							fleet AS f 
							ON fs.fs_fleet_id = f.fleet_id 
						) 
						INNER JOIN 
						ships AS s 
						ON fs.fs_ship_id = s.ship_id
						AND f.fleet_id='".$arr['fleet_id']."'
					GROUP BY
						fs.fs_ship_id;
				");
                $destroyed_ships="";
                while($cnt_arr=mysql_fetch_array($cnt_res))
                {
                	//Berechnet wie viele Schiffe von jedem Typ zerstört werden
                    $ship_destroy=floor($cnt_arr['fs_ship_cnt']*$destroy/100);
                    if($ship_destroy>0)
                    {
                    		// "Zerstörte" Schiffe aus der Flotte löschen
                        dbquery("
												UPDATE
													fleet_ships
												SET
													fs_ship_cnt=fs_ship_cnt-'".$ship_destroy."'
												WHERE
													fs_fleet_id='".$arr['fleet_id']."'
													AND fs_ship_id='".$cnt_arr['fs_ship_id']."';");
                        $destroyed_ships.="".$ship_destroy." ".$cnt_arr['ship_name']."\n";
                    }
                }
                
                if($ship_destroy>0)
                {
				    			$destroyed_ships_msg = "\n\nAufgrund starker Wasserstoffexplosionen sind einige deiner Schiffe zerst&ouml;rt worden:\n\n".$destroyed_ships."";
								}
			}
			else
			{
				$destroyed_ships_msg="";
			}
			//Laden der Tritiummenge auf dem Planeten
			$fuelRes = dbquery("SELECT
									planet_res_fuel
								FROM
									planets
								WHERE
									planet_id='".$arr['fleet_planet_to']."';");
			$fuelArr = mysql_fetch_array($fuelRes);
			

			// Anzahl gesammelter Rohstoffe berechen
    	  	$capa=$arr['fleet_capacity_nebula'];
    		$fuel = mt_rand(1000,$capa);
		
			$fuel = min($fuel, $fuelArr['planet_res_fuel']);
			
			//Tritium nach dem Saugen berechnen und speichern
			$newFuel = $fuelArr['planet_res_fuel'] - $fuel;
			dbquery("UPDATE 
						planets 
					SET 
						planet_res_fuel='".$newFuel."' 
					WHERE 
						planet_id='".$arr['fleet_planet_to']."';");

			//Smmiert erhaltenes Tritium zu der Ladung der Flotte
			$fuel_total=$fuel+$arr['fleet_res_fuel'];

      // Flotte zurückschicken
      fleet_return($arr,"gr","","","",$fuel_total);

			//Nachricht senden
			$msg = "[b]GASSAUGER-RAPPORT[/b]\n\nEine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat [b]".coords_format2($arr['fleet_planet_to'])."[/b]\num [b]".date("d.m.y, H:i:s",$arr['fleet_landtime'])."[/b]\n erreicht und Gas gesaugt\n";
			$msgres="\n[b]ROHSTOFFE:[/b]\n\n".RES_FUEL.": ".nf($fuel).$destroyed_ships_msg;
      send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Gas gesaugt",$msg.$msgres);

      //Erbeutete Rohstoffsumme speichern
      dbquery("
			UPDATE
				users
			SET
				user_res_from_nebula=user_res_from_nebula+'".$fuel."'
			WHERE
				user_id='".$arr['fleet_user_id']."';");  

      //Log schreiben
			add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] hat den Gasplaneten [b]".coords_format2($arr['fleet_planet_to'])."[/b] um [b]".date("d.m.y, H:i:s",$arr['fleet_landtime'])."[/b] erreicht und Gas gesaugt.\n".$msgres,time());


?>