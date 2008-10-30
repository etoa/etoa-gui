<?PHP
	/**
	* Fleet-Action: Collect asteroids
	*/ 
            $cell_id=$arr['fleet_cell_to'];

            // ist das asteroiden feld noch vorhanden?
            $res_exist = dbquery("
				SELECT 
					cell_asteroid 
				FROM 
					space_cells 
				WHERE 
					cell_id='".$cell_id."';
			");
            $arr_exist = mysql_fetch_array($res_exist);
            // wenn ja, sammle ress
            if($arr_exist['cell_asteroid']=='1')
            {
                $capa=$arr['fleet_capacity_asteroid'];
                $capa=round($capa/3);

                //80% Chance das das Sammeln klappt
                $goornot=mt_rand(1,100);
                if ($goornot>20)
                {
                    // Ressourcen berechnen und abziehen
                    $res_check=dbquery("
						SELECT 
							cell_asteroid_ress 
						FROM 
							space_cells 
						WHERE 
							cell_id='".$cell_id."';
					");
                    $arr_check = mysql_fetch_array($res_check);

                    $max_ress = $arr_check['cell_asteroid_ress']/3;

                    $asteroid = mt_rand(1000,$capa);
                    $metal=round(min($asteroid,$max_ress));

                    $asteroid = mt_rand(1000,$capa);
                    $crystal=round(min($asteroid,$max_ress));

                    $asteroid = mt_rand(1000,$capa);
                    $plastic=round(min($asteroid,$max_ress));

                    $ress_total = $metal + $crystal + $plastic;
                    dbquery("
						UPDATE 
							space_cells 
						SET 
							cell_asteroid_ress=cell_asteroid_ress-'".$ress_total."' 
						WHERE 
							cell_id='".$cell_id."';
					");

                    //
                    //Wenn Asteroidenfeld keine ress mehr hat -> löschen und neues erstellen
                    //
                    $res_ress_check =dbquery("
						SELECT 
							cell_asteroid_ress 
						FROM 
							space_cells 
						WHERE 
							cell_id='".$cell_id."';
					");
                    $arr_ress_check = mysql_fetch_array($res_ress_check);

                    if($arr_ress_check['cell_asteroid_ress']<1000)
                    {
                        // altes "löschen" //
                        dbquery("
							UPDATE 
								space_cells 
							SET 
								cell_asteroid_ress='0', 
								cell_asteroid='0', 
								cell_type='0' 
							WHERE 
								cell_id='".$cell_id."';
						");

                        // neues erstellen //
                        $new_ress = mt_rand($conf['asteroid_ress']['p1'],$conf['asteroid_ress']['p2']);

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

                            //Zufälligs leeres feld im universum für neues Asteroidenfeld
                            for ($x=0;$x<$rand;$x++)
                            {
                                $arr_rand = mysql_fetch_array($res_rand);
                            }
                            // neues erstellen
                            dbquery("
								UPDATE 
									space_cells 
								SET 
									cell_asteroid_ress='".$new_ress."', 
									cell_asteroid='1', 
									cell_type='1' 
								WHERE 
									cell_id='".$arr_rand['cell_id']."';
							");

                        }
                    }

					//Summiert Rohstoffe zu der Ladung der Flotte
					$metal=$metal+$arr['fleet_res_metal'];
					$crystal=$metal+$arr['fleet_res_crystal'];
					$plastic=$metal+$arr['fleet_res_plastic'];

                    // Flotte zurückschicken
                    fleet_return($arr,"yr",$metal,$crystal,$plastic);

					//Nachricht senden
                    $msg = "Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat [b]ein Asteroidenfeld[/b]\num [b]".date("d.m.Y H:i",$arr['fleet_landtime'])."[/b]\n erreicht und Rohstoffe gesammelt.\n";
                    $msgres = "\n[b]ROHSTOFFE:[/b]\n\n".RES_METAL.": ".nf($metal)."\n".RES_CRYSTAL.": ".nf($crystal)."\n".RES_PLASTIC.": ".nf($plastic)."\n";
                    send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Asteroiden gesammelt",$msg.$msgres);

                    //Erbeutete Rohstoffsumme speichern
                    $res_sum=$metal+$crystal+$plastic;
                    dbquery("
						UPDATE
							users
						SET
							user_res_from_asteroid=user_res_from_asteroid+'".$res_sum."'
						WHERE
							user_id='".$arr['fleet_user_id']."';
					");  

                    //Log schreiben
                    add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] hat [b]ein Asteroidenfeld[/b] um [b]".date("d.m.Y H:i",$arr['fleet_landtime'])."[/b]\n erreicht und Rohstoffe gesammelt.".$msgres,time());
                }

                //20% Chance das die flotte zerstört wird
                else
                {
                	//Nachricht senden
                    $msg="Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\n wurde bei einem Asteroidenfeld abgeschossen.";
                    send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Flotte abgeschossen",$msg);

                    //Log schreiben
					add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] wurde bei einem Asteroidenfeld abgeschossen.",time());

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
      		// Asteroiden feld nicht mehr vorhanden
			else
			{
				// Flotte zurückschicken
                fleet_return($arr,"yr");

                // Nachricht senden
                $msg="Die Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\n fand kein Asteroidenfeld mehr vor.\n";
                send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Asteroidenfeld aufgelöst",$msg);

                //Log schreiben
                add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] fand kein Asteroidenfeld mehr vor.",time());
    		}


?>