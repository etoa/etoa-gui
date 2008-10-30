<?PHP

	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: update_fleet.php													//
	// Topic: Fleetupdate			 									//
	// Version: 0.1																	//
	// Letzte Änderung: 21.05.2005									//
	//////////////////////////////////////////////////

	//
	// Funktionen (können eventuell noch ausgelagert werden in externe Datei)
	//

	/**
	* Waren entladen / Flotte stationieren
	*
	* @param array $arr Gesamte Flottendaten
	* @param int $planet_id Zielplanet ID
	* @param int $fleet_action Aktion -> 1 = Stationieren, 2 = Waren entladen
	* @author Lamborghini
	*/
	function fleet_land($arr,$fleet_action=0,$already_colonialized=0,$already_invaded=0)
	{
        global $conf;

		//Flotte wird stationiert und Waren werden ausgeladen
		if($fleet_action==1)
		{
            // Waren entladen
            $people=$arr['fleet_pilots']+$arr['fleet_res_people'];
            dbquery("
				UPDATE
					planets
				SET
					planet_res_metal=planet_res_metal+'".$arr['fleet_res_metal']."',
					planet_res_crystal=planet_res_crystal+'".$arr['fleet_res_crystal']."',
					planet_res_plastic=planet_res_plastic+'".$arr['fleet_res_plastic']."',
					planet_res_fuel=planet_res_fuel+'".$arr['fleet_res_fuel']."',
					planet_res_food=planet_res_food+'".$arr['fleet_res_food']."',
					planet_people=planet_people+'".$people."'
				WHERE
					planet_id='".$arr['fleet_planet_to']."';
			");

			//Rohstoffnachricht für den User
			$msgres= "\n\n[b]WAREN[/b]\n\n[b]".RES_METAL.":[/b] ".nf($arr['fleet_res_metal'])."\n[b]".RES_CRYSTAL.":[/b] ".nf($arr['fleet_res_crystal'])."\n[b]".RES_PLASTIC.":[/b] ".nf($arr['fleet_res_plastic'])."\n[b]".RES_FUEL.":[/b] ".nf($arr['fleet_res_fuel'])."\n[b]".RES_FOOD.":[/b] ".nf($arr['fleet_res_food'])."\n[b]Bewohner:[/b] ".nf($arr['fleet_res_people'])."\n";

			// Flotte stationieren
            // Laden der Schiffsdaten
            $fsres = dbquery("
				SELECT
					fs.fs_ship_cnt,
					fs.fs_ship_id,
					fs.fs_special_ship,
					fs.fs_special_ship_level,
					fs.fs_special_ship_exp,
					fs.fs_special_ship_bonus_weapon,
					fs.fs_special_ship_bonus_structure,
					fs.fs_special_ship_bonus_shield,
					fs.fs_special_ship_bonus_heal,
					fs.fs_special_ship_bonus_capacity,
					fs.fs_special_ship_bonus_speed,
					fs.fs_special_ship_bonus_pilots,
					fs.fs_special_ship_bonus_tarn,
					fs.fs_special_ship_bonus_antrax,
					fs.fs_special_ship_bonus_forsteal,
					fs.fs_special_ship_bonus_build_destroy,
					fs.fs_special_ship_bonus_antrax_food,
					fs.fs_special_ship_bonus_deactivade,
					s.ship_name,
					s.ship_colonialize,
					s.ship_invade
				FROM
					fleet_ships AS fs 
				INNER JOIN 
					ships AS s ON fs.fs_ship_id = s.ship_id
					AND fs.fs_fleet_id='".$arr['fleet_id']."'
					AND fs.fs_ship_faked='0';
			");
            if (mysql_num_rows($fsres)>0)
            {
                $msgships ='';
                while ($fsarr = mysql_fetch_array($fsres))
                {
                	$ship_cnt = $fsarr['fs_ship_cnt'];

                    // Ein Koloschiff subtrahieren, falls kolonialisieren gewählt ist (einmalig)
                    if ($fsarr['ship_colonialize']==1 && $already_colonialized==0 && $arr['fleet_action']=="ko")
                    {
                        $ship_cnt = $fsarr['fs_ship_cnt']-1;
                        $already_colonialized=1;
                    }

                    // Ein Invasionsschiff subtrahieren, falls invasieren gewählt ist (einmalig)
                    if ($fsarr['ship_invade']==1 && $already_invaded==0 && $arr['fleet_action']=="io")
                    {
                        $ship_cnt = $fsarr['fs_ship_cnt']-1;
                        $already_invaded=1;
                    }


                    //Sucht einen bestehenden Datensatz auf dem Zielplanet aus
                    //Achtung: In dem Query darf NICHT auch noch nach der User-ID gefragt werden, weil Handelsschiffe die User-ID=0 haben!
                    $slres = dbquery("
						SELECT
							shiplist_id
						FROM
							shiplist
						WHERE
							shiplist_ship_id='".$fsarr['fs_ship_id']."'
							AND shiplist_planet_id='".$arr['fleet_planet_to']."';
					");
                    $slarr = mysql_fetch_array($slres);

					//Bestehender Datensatz gefunden -> Stationiert die Schiffe mit all ihren Werten (Update)
                    if (mysql_num_rows($slres)>0)
                    {
                        dbquery("
							UPDATE
								shiplist
							SET
								shiplist_count=shiplist_count+'".$ship_cnt."',
								shiplist_special_ship='".$fsarr['fs_special_ship']."',
								shiplist_special_ship_level='".$fsarr['fs_special_ship_level']."',
								shiplist_special_ship_exp='".$fsarr['fs_special_ship_exp']."',
								shiplist_special_ship_bonus_weapon='".$fsarr['fs_special_ship_bonus_weapon']."',
								shiplist_special_ship_bonus_structure='".$fsarr['fs_special_ship_bonus_structure']."',
								shiplist_special_ship_bonus_shield='".$fsarr['fs_special_ship_bonus_shield']."',
								shiplist_special_ship_bonus_heal='".$fsarr['fs_special_ship_bonus_heal']."',
								shiplist_special_ship_bonus_capacity='".$fsarr['fs_special_ship_bonus_capacity']."',
								shiplist_special_ship_bonus_speed='".$fsarr['fs_special_ship_bonus_speed']."',
								shiplist_special_ship_bonus_pilots='".$fsarr['fs_special_ship_bonus_pilots']."',
								shiplist_special_ship_bonus_tarn='".$fsarr['fs_special_ship_bonus_tarn']."',
								shiplist_special_ship_bonus_antrax='".$fsarr['fs_special_ship_bonus_antrax']."',
								shiplist_special_ship_bonus_forsteal='".$fsarr['fs_special_ship_bonus_forsteal']."',
								shiplist_special_ship_bonus_build_destroy='".$fsarr['fs_special_ship_bonus_build_destroy']."',
								shiplist_special_ship_bonus_antrax_food='".$fsarr['fs_special_ship_bonus_antrax_food']."',
								shiplist_special_ship_bonus_deactivade='".$fsarr['fs_special_ship_bonus_deactivade']."'
							WHERE
								shiplist_id='".$slarr['shiplist_id']."';
						");
                    }
                    //Keinen bestehenden Datensatz gefunden -> Stationiert die Schiffe mit all ihren Werten (Insert)
                    else
                    {

                        //überprüft, ob die Flotte eine User ID besitzt, sonst eine generieren durch Planet ID (z.b. für Handelsschiffe)
                        if($arr['fleet_user_id']!=0)
                        {
                            $user_id = $arr['fleet_user_id'];
                        }
                        else
                        {
                            $user_id = get_user_id_by_planet($arr['fleet_planet_to']);
                        }

                        dbquery("
							INSERT INTO
							shiplist (
								shiplist_user_id,
								shiplist_ship_id,
								shiplist_planet_id,
								shiplist_count,
								shiplist_special_ship,
								shiplist_special_ship_level,
								shiplist_special_ship_exp,
								shiplist_special_ship_bonus_weapon,
								shiplist_special_ship_bonus_structure,
								shiplist_special_ship_bonus_shield,
								shiplist_special_ship_bonus_heal,
								shiplist_special_ship_bonus_capacity,
								shiplist_special_ship_bonus_speed,
								shiplist_special_ship_bonus_pilots,
								shiplist_special_ship_bonus_tarn,
								shiplist_special_ship_bonus_antrax,
								shiplist_special_ship_bonus_forsteal,
								shiplist_special_ship_bonus_build_destroy,
								shiplist_special_ship_bonus_antrax_food,
								shiplist_special_ship_bonus_deactivade 
							)
							VALUES (
								'".$user_id."',
								'".$fsarr['fs_ship_id']."',
								'".$arr['fleet_planet_to']."',
								'".$ship_cnt."',
								'".$fsarr['fs_special_ship']."',
								'".$fsarr['fs_special_ship_level']."',
								'".$fsarr['fs_special_ship_exp']."',
								'".$fsarr['fs_special_ship_bonus_weapon']."',
								'".$fsarr['fs_special_ship_bonus_structure']."',
								'".$fsarr['fs_special_ship_bonus_shield']."',
								'".$fsarr['fs_special_ship_bonus_heal']."',
								'".$fsarr['fs_special_ship_bonus_capacity']."',
								'".$fsarr['fs_special_ship_bonus_speed']."',
								'".$fsarr['fs_special_ship_bonus_pilots']."',
								'".$fsarr['fs_special_ship_bonus_tarn']."',
								'".$fsarr['fs_special_ship_bonus_antrax']."',
								'".$fsarr['fs_special_ship_bonus_forsteal']."',
								'".$fsarr['fs_special_ship_bonus_build_destroy']."',
								'".$fsarr['fs_special_ship_bonus_antrax_food']."',
								'".$fsarr['fs_special_ship_bonus_deactivade']."'
							);
						");
                    }
                    //Schreibt alle Schiffe mit deren Anzahl in ein Array (für Nachricht an den User)
                    if ($ship_cnt>0)
                    {
                    	$msgships.= "\n[b]".$fsarr['ship_name'].":[/b] ".nf($ship_cnt);
                    }
				}
				if ($msgships=='')
				{
					$msgships = "\n\n[b]SCHIFFE[/b]\n[i]Keine weiteren Schiffe in der Flotte![/i]";					
				}
				else
				{
					$msgships = "\n\n[b]SCHIFFE[/b]\n".$msgships;					
				}
				
				//Gibt die Nachrichten in einem Array zurück -> $msg[0]=Schiffe, $msg[1]=Rohstoffe
				$msg=array($msgships,$msgres);
				return $msg;
			}
		}
		//Waren werden ausgeladen
		elseif($fleet_action==2)
		{
            // Waren entladen
            $people=$arr['fleet_pilots']+$arr['fleet_res_people'];
            dbquery("
				UPDATE
					planets
				SET
					planet_res_metal=planet_res_metal+'".$arr['fleet_res_metal']."',
					planet_res_crystal=planet_res_crystal+'".$arr['fleet_res_crystal']."',
					planet_res_plastic=planet_res_plastic+'".$arr['fleet_res_plastic']."',
					planet_res_fuel=planet_res_fuel+'".$arr['fleet_res_fuel']."',
					planet_res_food=planet_res_food+'".$arr['fleet_res_food']."',
					planet_people=planet_people+'".$people."'
				WHERE
					planet_id='".$arr['fleet_planet_to']."';
			");

			$msgres= "\n\n[b]WAREN[/b]\n\n[b]".RES_METAL.":[/b] ".nf($arr['fleet_res_metal'])."\n[b]".RES_CRYSTAL.":[/b] ".nf($arr['fleet_res_crystal'])."\n[b]".RES_PLASTIC.":[/b] ".nf($arr['fleet_res_plastic'])."\n[b]".RES_FUEL.":[/b] ".nf($arr['fleet_res_fuel'])."\n[b]".RES_FOOD.":[/b] ".nf($arr['fleet_res_food'])."\n[b]Bewohner:[/b] ".nf($arr['fleet_res_people'])."\n";

			return $msgres;
		}
		//Fehler, die Flotte hat eine ungültige Aktion
		else
		{
			return "Fehler, die Flotte hat eine ungültige Aktion!<br>";
		}
	}

	/**
	* Flotte zurückschicken
	*
	* @param array $arr Gesamte Flottendaten
	* @param string $action Flottenaktion
	* @param int $fleet_res... Rohstoffe, Standart: unverändert
	* @author Lamborghini
	*/
	function fleet_return($arr,$action,$res_metal=-1,$res_crystal=-1,$res_plastic=-1,$res_fuel=-1,$res_food=-1,$res_people=-1)
	{
        global $conf;

        // Flotte zurückschicken
        $duration = $arr['fleet_landtime'] - $arr['fleet_launchtime'];
        $launchtime=$arr['fleet_landtime'];
        $landtime=$launchtime+$duration;

        $sql = "
			UPDATE
				fleet
			SET
				fleet_cell_from='".$arr['fleet_cell_to']."',
				fleet_cell_to='".$arr['fleet_cell_from']."',
				fleet_planet_from='".$arr['fleet_planet_to']."',
				fleet_planet_to='".$arr['fleet_planet_from']."',
				fleet_action='".$action."',
				fleet_launchtime='".$launchtime."',
				fleet_landtime='".$landtime."'";
				if($res_metal>-1) 
					$sql .= ", fleet_res_metal='".$res_metal."'";
				if($res_crystal>-1) 
					$sql .= ", fleet_res_crystal='".$res_crystal."'";
				if($res_plastic>-1) 
					$sql .= ", fleet_res_plastic='".$res_plastic."'";
				if($res_fuel>-1) 
					$sql .= ", fleet_res_fuel='".$res_fuel."'";
				if($res_food>-1) 
					$sql .= ", fleet_res_food='".$res_food."'";
				if($res_people>-1) 
					$sql .= ", fleet_res_people='".$res_people."'";
			$sql.="	WHERE
				fleet_id='".$arr['fleet_id']."';";
		
		dbquery($sql);
	}

	/**
	* Flotte löschen
	*
	* @param int Flotten-ID
	*/
	function fleet_delete($fid)
	{
		// Flotte-Schiffe-Verknüpfungen löschen
		dbquery("
			DELETE FROM 
				fleet_ships
			WHERE 
				fs_fleet_id='".$fid."';
		");
		// Flotte aufheben
		dbquery("
			DELETE FROM 
				fleet
			WHERE 
				fleet_id='".$fid."';
		");			
	}

/**
* Aktualisiert die gegebene Flotte
*
* @param array Flotten-Array
*/
function update_fleet($arr,$output=0)
{
	global $conf;
	$time = time();
	
	// Nachprüfen ob Landezeit wirklich kleider ist als aktuelle Zeit
	if ($arr['fleet_landtime'] < $time && $arr['fleet_updating']==0)
	{
		// Update-Flag setzen
		dbquery("
		UPDATE 
			fleet 
		SET 
			fleet_updating=1 
		WHERE 
			fleet_id='".$arr['fleet_id']."';");

		// Load action
		if (stristr($arr['fleet_action'],"c"))
		{
			require("inc/fleet_action/cancel.inc.php");
		}
		elseif (stristr($arr['fleet_action'],"r"))
		{
			require("inc/fleet_action/return.inc.php");
		}
		else
		{
			$fak = fa_key($arr['fleet_action']);
			$fa_path = "inc/fleet_action/action.".$fak.".inc.php";
			if (file_exists($fa_path))
			{
				require($fa_path);
			}
			else
			{
				require("inc/fleet_action/default.inc.php");
			}
		}
		
		/*
				    **************************************************
				    * Handelsstationieren kommt beim Zielplanet an   *
				    * (Schiffe und Rohstoffe)                        *
				    **************************************************
				elseif ($arr['fleet_action']=="mpo")
				{
				        //Flotte stationieren und Waren ausladen
				        $msg_ships_res=fleet_land($arr,1);
				
					//Sucht User-ID
					$user_to_id = get_user_id_by_planet($arr['fleet_planet_to']);
				
					//Nachricht senden
					$msg.="Eine Flotte vom Handelsministerium hat folgendes Ziel erreicht:\n[b]Planet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Zeit:[/b] ".date("d.m.Y H:i",$arr['fleet_landtime'])."\n[b]Bericht:[/b] Die gekauften Schiffe sind gelandet.\n";
					$msg.=$msg_ships_res[0];
					//Wenn das schiff auch Rohstoffe mitgebracht hat
					if($arr['fleet_res_metal']!='0' || $arr['fleet_res_crystal']!='0' || $arr['fleet_res_plastic']!='0' || $arr['fleet_res_fuel']!='0' || $arr['fleet_res_food']!='0')
					{
						//Nachricht, wie viele Rohstoffe abgeladen wurden
						$msg.="Es wurden zudem folgende Rohstoffe abgeladen:\n";
						$msg.=$msg_ships_res[1];
					}
				
					$msg.="\n\nUnser Unternehmen dankt ihnen f&uuml;r die Unterst&uuml;tzung und wir hoffen sie sind mit uns zufrieden und w&uuml;nschen ihnen auch in Zukunft viel Erfolg.\nDas Handelsministerium";
				
					send_msg($user_to_id,SHIP_MISC_MSG_CAT_ID,"Flotte vom Handelsministerium",$msg);
				
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
				    /**********************************************
				    /* Handelstransport kommt beim Zielplanet an  *
				    /* (Nur Rohstoffe)                            *
				    /**********************************************
				elseif ($arr['fleet_action']=="mto")
				{
				        //Waren ausladen
				        $msgres=fleet_land($arr,2);
				
					//Sucht User-ID
					$user_to_id = get_user_id_by_planet($arr['fleet_planet_to']);
				
					//Nachricht senden
					$msg = "Eine Flotte vom Handelsministerium hat folgendes Ziel erreicht:\n[b]Planet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Zeit:[/b] ".date("d.m.Y H:i",$arr['fleet_landtime'])."\n[b]Bericht:[/b] Folgende Waren wurden ausgeladen:\n";
					$msg.= $msgres;
					$msg.="\n\nUnser Unternehmen dankt ihnen f&uuml;r die Unterst&uuml;tzung und wir hoffen sie sind mit uns zufrieden und w&uuml;nschen ihnen auch in Zukunft viel Erfolg.\nDas Handelsministerium";
				
					send_msg($user_to_id,SHIP_MISC_MSG_CAT_ID,"Transport vom Handelsministerium",$msg);
				
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
		*/

		// Update-Flag löschen, Update-Counter erhöhen
		dbquery("
		UPDATE
			fleet
		SET
			fleet_updating=0,
			fleet_update_counter=fleet_update_counter+1
		WHERE
			fleet_id='".$arr['fleet_id']."';");

		return true;
	}

	// Wenn Voraussetzungen falsch sind, Warnung ausgeben
	else
	{
		echo "Die Flotte ".$arr['fleet_id']." konnte nicht bearbeitet werden, Landezeit falsch!\n";
		return false;
	}

  //Arrays löschen (Speicher freigeben)
  unset($arr);
}
?>
