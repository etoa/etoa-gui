<?PHP
	/**
	* Fleet action: Collect nebula gas
	*/
	
			$cell_id=$arr['fleet_cell_to'];

			// ist das nebel feld noch vorhanden?
			$res_exist = dbquery("
				SELECT 
					cell_nebula 
				FROM 
					space_cells 
				WHERE 
					cell_id='".$cell_id."';
			");
			$arr_exist = mysql_fetch_array($res_exist);
			// wenn ja, sammle ress
			if($arr_exist['cell_nebula']=='1')
			{
                $capa=$arr['fleet_capacity_nebula'];
                $capa=round($capa);

                //80% Chance das das sammeln klappt
                $goornot=mt_rand(1,100);
                if ($goornot>20)
                {
                	//Rohstoffe berechnen und abziehen
                    $res_check=dbquery("
						SELECT 
							cell_nebula_ress 
						FROM 
							space_cells 
						WHERE 
							cell_id='".$cell_id."';
					");
                    $arr_check = mysql_fetch_array($res_check);

                    $max_ress = $arr_check['cell_nebula_ress'];

                    $nebula = mt_rand(1000,$capa);
                    $crystal=round(min($nebula,$max_ress));

                    $ress_total = $crystal;

                    dbquery("
						UPDATE 
							space_cells 
						SET 
							cell_nebula_ress=cell_nebula_ress-'".$ress_total."' 
						WHERE 
							cell_id='".$cell_id."';
					");


                    //
                    //Wenn nebula feld keine ress mehr hat -> löschen und neues erstellen
                    //
                    $res_ress_check =dbquery("
						SELECT 
							cell_nebula_ress 
						FROM 
							space_cells 
						WHERE 
							cell_id='".$cell_id."';
					");
                    $arr_ress_check = mysql_fetch_array($res_ress_check);

                    if($arr_ress_check['cell_nebula_ress']<1000)
                    {
                        // altes "löschen" //
                        dbquery("
							UPDATE 
								space_cells 
							SET 
								cell_nebula_ress='0', 
								cell_nebula='0', 
								cell_type='0' 
							WHERE 
								cell_id='".$cell_id."';
						");

                        // neues erstellen //
                        $new_ress = mt_rand($conf['nebula_ress']['p1'],$conf['nebula_ress']['p2']);

                                // hat es noch leere felder?
                        $res_search_place=dbquery("
							SELECT 
								cell_id 
							FROM 
								space_cells 
							WHERE 
								cell_type='0';
						");
                        $arr_search_place = mysql_fetch_array($res_search_place);
                        // wenn ja...
                        if (mysql_num_rows($res_search_place)>0)
                        {

							$res_rand=dbquery("
						  		SELECT 
									cell_id 
								FROM 
									space_cells 
								WHERE 
									cell_type='0';
							");
							
							$rand_num = mysql_num_rows($res_rand);
							$rand = mt_rand(0,$rand_num);
							
							//Zufälligs leeres feld im universum für neues nebulaenfeld
							for ($x=0;$x<$rand;$x++)
							{
							  $arr_rand = mysql_fetch_array($res_rand);
							}
                        // neues erstellen
						dbquery("
							UPDATE 
								space_cells 
							SET 
								cell_nebula_ress='".$new_ress."', 
								cell_nebula='1', 
								cell_type='1' 
							WHERE 
								cell_id='".$arr_rand['cell_id']."';
							");
                        }
                    }

					//Summiert Rohstoffe zu der Ladung der Flotte
                    $crystal=$crystal+$arr['fleet_res_crystal'];

                    // Flotte zurückschicken
                    fleet_return($arr,"nr","",$crystal);

					//Nachricht senden
                    $msg = "Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat [b]ein Intergalaktisches Nebelfeld [/b]\num [b]".date("d.m.Y H:i",$arr['fleet_landtime'])."[/b]\n erkundet und dabei Rohstoffe gesammelt.\n";
                    $msgres = "\n[b]ROHSTOFFE:[/b]\n\n".RES_CRYSTAL.": ".nf($crystal)."\n";
                    send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Nebelfeld erkunden",$msg.$msgres);

                    //Erbeutete Rohstoffsumme speichern
                    dbquery("
						UPDATE
							users
						SET
							user_res_from_nebula=user_res_from_nebula+'".$crystal."'
						WHERE
							user_id='".$arr['fleet_user_id']."';
					"); 

                    //Log schreiben
                    add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] at [b]ein Intergalaktisches Nebelfeld [/b] um [b]".date("d.m.Y H:i",$arr['fleet_landtime'])."[/b]\n erkundet und dabei Rohstoffe gesammelt.\n".$msgres,time());
                }

                //20% Chance das die flotte zerstört wird
                else
                {
                	//Nachricht senden
                    $msg="Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\n hatte bei ihrer Erkundung eines Intergalaktischen Nebelfeldes eine starke magnetische Störung, welche zu einem Systemausfall führte.\nZu der Flotte ist jeglicher Kontakt abgebrochen.";
                    send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Flotte verschollen",$msg);

                    //Log schreiben
                    add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] wurde bei einem Intergalaktisches Nebelfeld zerst&ouml;rt.",time());

                    // Flotte-Schiffe-Verknüpfungen löschen
                    dbquery("
						DELETE FROM 
							fleet_ships 
						WHERE 
							fs_fleet_id='".$arr['fleet_id']."';
					");

                    // Flotte aufheben
                    dbquery("
						DELETE FROM 
							fleet
						WHERE 
							fleet_id='".$arr['fleet_id']."';
					");
                }
			}

      		// nebula feld nicht mehr vorhanden
			else
			{
            	// Flotte zurückschicken
                fleet_return($arr,"nr","",$crystal);

				//Nachricht senden
                $msg="Die Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\n konnte kein Intergalaktisches Nebelfeld orten.\n";
                send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Nebelfeld verschwunden",$msg);

                //Log schreiben
                add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] konnte kein Intergalaktisches Nebelfeld orten.",time());
			}
?>