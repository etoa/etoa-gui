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
        global $db_table;

		//Flotte wird stationiert und Waren werden ausgeladen
		if($fleet_action==1)
		{
            // Waren entladen
            $people=$arr['fleet_pilots']+$arr['fleet_res_people'];
            dbquery("
				UPDATE
					".$db_table['planets']."
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
					".$db_table['fleet_ships']." AS fs 
				INNER JOIN 
					".$db_table['ships']." AS s ON fs.fs_ship_id = s.ship_id
					AND fs.fs_fleet_id='".$arr['fleet_id']."'
					AND fs.fs_ship_faked='0';
			");
            if (mysql_num_rows($fsres)>0)
            {
                $msgships = "\n\n[b]SCHIFFE[/b]\n";
                while ($fsarr = mysql_fetch_array($fsres))
                {
                	$ship_cnt = $fsarr['fs_ship_cnt'];

                    // Ein Koloschiff subtrahieren, falls kolonialisieren gewählt ist (einmalig)
                    if ($fsarr['ship_colonialize']==1 && $already_colonialized==0 && $arr['fleet_action']=="ko")
                    {
                        $ship_cnt = $fsarr['fs_ship_cnt']-1;
                        $already_colonialized==1;
                    }

                    // Ein Invasionsschiff subtrahieren, falls invasieren gewählt ist (einmalig)
                    if ($fsarr['ship_invade']==1 && $already_invaded==0 && $arr['fleet_action']=="io")
                    {
                        $ship_cnt = $fsarr['fs_ship_cnt']-1;
                        $already_invaded==1;
                    }


                    //Sucht einen bestehenden Datensatz auf dem Zielplanet aus
                    //Achtung: In dem Query darf NICHT auch noch nach der User-ID gefragt werden, weil Handelsschiffe die User-ID=0 haben!
                    $slres = dbquery("
						SELECT
							shiplist_id
						FROM
							".$db_table['shiplist']."
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
								".$db_table['shiplist']."
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
							".$db_table['shiplist']." (
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
                    $msgships.= "\n[b]".$fsarr['ship_name'].":[/b] ".nf($ship_cnt);
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
					".$db_table['planets']."
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
	function fleet_return($arr,$action,$res_metal="",$res_crystal="",$res_plastic="",$res_fuel="",$res_food="",$res_people="")
	{
        global $conf;
        global $db_table;

		//Wenn keine Rohstoffe angegeben sind, übernahme der bereits Transportierten Rohstoffe
		if($res_metal=="") $res_metal=$arr['fleet_res_metal'];
		if($res_crystal=="") $res_crystal=$arr['fleet_res_crystal'];
		if($res_plastic=="") $res_plastic=$arr['fleet_res_plastic'];
		if($res_fuel=="") $res_fuel=$arr['fleet_res_fuel'];
		if($res_food=="") $res_food=$arr['fleet_res_food'];
		if($res_people=="") $res_people=$arr['fleet_res_people'];

        // Flotte zurückschicken
        $duration = $arr['fleet_landtime'] - $arr['fleet_launchtime'];
        $launchtime=$arr['fleet_landtime'];
        $landtime=$launchtime+$duration;
        dbquery("
			UPDATE
				".$db_table['fleet']."
			SET
				fleet_cell_from='".$arr['fleet_cell_to']."',
				fleet_cell_to='".$arr['fleet_cell_from']."',
				fleet_planet_from='".$arr['fleet_planet_to']."',
				fleet_planet_to='".$arr['fleet_planet_from']."',
				fleet_action='".$action."',
				fleet_launchtime='".$launchtime."',
				fleet_landtime='".$landtime."',
				fleet_res_metal='".$res_metal."',
				fleet_res_crystal='".$res_crystal."',
				fleet_res_plastic='".$res_plastic."',
				fleet_res_fuel='".$res_fuel."',
				fleet_res_food='".$res_food."',
				fleet_res_people='".$res_people."'
			WHERE
				fleet_id='".$arr['fleet_id']."';
		");
	}

	include_once("get_fleet_action.php");
	include_once("battle.php");


/**
* Aktualisiert die gegebene Flotte
*
*/
function update_fleet($arr,$output=0)
{
	global $conf;
	global $db_table;

	// Nachprüfen ob Landezeit wirklich kleider ist als aktuelle Zeit
	if ($arr['fleet_landtime']<time() && $arr['fleet_updating']==0)
	{
		// Update-Flag setzen
		dbquery("
			UPDATE 
				".$db_table['fleet']." 
			SET 
				fleet_updating=1 
			WHERE 
				fleet_id='".$arr['fleet_id']."';
		");


        /***********************************************************************/
        /* Flotte soll auf dem Zielplanet landen                               */
        /* Prüfen auf: Stationieren (po), Rückflug (r) oder Abbruch (c)		   */
        /***********************************************************************/
		if ($arr['fleet_action']=="po" || stristr($arr['fleet_action'],"c") || stristr($arr['fleet_action'],"r"))
		{
            //Flotte stationieren und Waren ausladen
            $msg_ships_res=fleet_land($arr,1);

            // Flotte-Schiffe-Verknüpfungen löschen
            dbquery("
				DELETE FROM 
					".$db_table['fleet_ships']." 
				WHERE 
					fs_fleet_id='".$arr['fleet_id']."';
			");

            // Flotte aufheben
            dbquery("
				DELETE FROM 
					".$db_table['fleet']." 
				WHERE 
					fleet_id='".$arr['fleet_id']."';
			");

			//Nachricht senden
            $msg = "[b]FLOTTE GELANDET[/b]\n\nEine eurer Flotten hat hat ihr Ziel erreicht!\n\n[b]Zielplanet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Startplanet:[/b] ".coords_format2($arr['fleet_planet_from'])."\n[b]Zeit:[/b] ".date("d.m.y, H:i:s",$arr['fleet_landtime'])."\n[b]Auftrag:[/b] ".get_fleet_action($arr['fleet_action']);
            $msg.= $msg_ships_res[0].$msg_ships_res[1];
            send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Flotte angekommen",$msg);

            $showinfo=0;
		}


        /*********************************************************/
        /* Kolonialisieren (ko) kommt beim Zielplaneten an       */
        /*********************************************************/
		if ($arr['fleet_action']=="ko")
		{
            // Planet auf Bewohner prüfen
            $ures = dbquery("
				SELECT 
					planet_user_id 
				FROM 
					".$db_table['planets']." 
				WHERE 
					planet_user_id>0 
					AND planet_id='".$arr['fleet_planet_to']."';
			");
            $uarr = mysql_fetch_array($ures);

            //Planet ist bereits kolonialisiert
            if (mysql_num_rows($ures)>0)
            {
                //Planet wurde bereits vom gleichen User kolonialisiert
                if($uarr['planet_user_id']==$arr['fleet_user_id'])
                {
                    //Flotte stationieren & Waren ausladen (ohne abzug eines Kolonieschiffes)
                    $msg_ship_res=fleet_land($arr,1,1);

                    // Flotte-Schiffe-Verknüpfungen löschen
                    dbquery("
						DELETE FROM 
							".$db_table['fleet_ships']." 
						WHERE 
							fs_fleet_id='".$arr['fleet_id']."';
					");

                    // Flotte aufheben
                    dbquery("
						DELETE FROM 
							".$db_table['fleet']." 
						WHERE 
							fleet_id='".$arr['fleet_id']."';
					");

					//Nachricht senden
                    $msg = "Die Flotte hat folgendes Ziel erreicht:\n[b]Planet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Zeit:[/b] ".date("d.m.Y H:i",$arr['fleet_landtime'])."\n[b]Bericht:[/b] Die Flotte ist auf dem Planeten gelandet!";
                    $msg.= $msg_ship_res[0].$msg_ship_res[1];
                    send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Flotte angekommen",$msg);

                    $showinfo=0;
                }
                //Planet gehört bereits an einem anderen User
                else
                {
                	//Nachricht senden
                    $msg = "Die Flotte kann den Planeten nicht kolonialisieren, da er bereits von einem anderen Volk kolonialisiert wurde!\n";
                    send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Landung nicht möglich",$msg);

                    // Flotte zurückschicken
                    fleet_return($arr,"koc");
                }
            }
            //Planet ist noch frei und kann kolonialisiert werden
            else
            {
                // Auf eigene Maximalanzahl prüfen
                if (mysql_num_rows(dbquery("
					SELECT 
						planet_user_id 
					FROM 
						".$db_table['planets']." 
					WHERE 
						planet_user_id='".$arr['fleet_user_id']."';
				"))>= $conf['user_max_planets']['v'])
                {
                	//Nachricht senden
                    $msg = "Die Flotte kann den Planeten nicht kolonialisieren, da die maximale Zahl an Planeten auf denen du regieren darfst, bereits erreicht worden ist!\n";
                    send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Landung nicht möglich",$msg);

                    // Flotte zurückschicken
                    fleet_return($arr,"koc");
                }
                //Kolonie erfolgreich gewonnen
                else
                {
                    //Planet zurücksetzen
                    reset_planet($arr['fleet_planet_to']);

                    // Planet übernehmen
                    dbquery("
						UPDATE
							".$db_table['planets']."
						SET
							planet_user_id='".$arr['fleet_user_id']."',
							planet_name='Unbenannt'
						WHERE
							planet_id='".$arr['fleet_planet_to']."';
					");

                    //Flotte stationieren & Waren ausladen (mit abzug eines Kolonieschiffes)
                    $msg_ship_res=fleet_land($arr,1);

                    // Flotte-Schiffe-Verknüpfungen löschen
                    dbquery("
						DELETE FROM 
							".$db_table['fleet_ships']." 
						WHERE 
							fs_fleet_id='".$arr['fleet_id']."';
					");

                    // Flotte aufheben
                    dbquery("
						DELETE FROM 
							".$db_table['fleet']." 
						WHERE 
							fleet_id='".$arr['fleet_id']."';
					");

					//Nachricht senden
                    $msg = "Die Flotte hat folgendes Ziel erreicht:\n[b]Planet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Zeit:[/b] ".date("d.m.Y H:i",$arr['fleet_landtime'])."\n[b]Bericht:[/b] Die Flotte hat eine neue Kolonie errichtet!\n";
                    $msg.= $msg_ship_res[0].$msg_ship_res[1];
                    send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Planet kolonialisiert",$msg);

                    $showinfo=0;
                }
            }
		}


        /****************************************/
        /* Flotte kommt beim Zielplaneten an    */
        /* Prüfen auf: Flug (fo), Fake (eo)     */
        /****************************************/
		elseif ($arr['fleet_action']=="fo" || $arr['fleet_action']=="eo")
		{
			if($arr['fleet_action']=="fo")
				$action="fr";
			if($arr['fleet_action']=="eo")
				$action="er";

            // Flotte zurückschicken
            fleet_return($arr,$action);
		}


        /**************************************************/
        /* Handelsstationieren kommt beim Zielplanet an   */
        /* (Schiffe und Rohstoffe)                        */
        /**************************************************/
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
					".$db_table['fleet_ships']." 
				WHERE 
					fs_fleet_id='".$arr['fleet_id']."';
			");

			// Flotte aufheben
			dbquery("
				DELETE FROM 
					".$db_table['fleet']." 
				WHERE 
					fleet_id='".$arr['fleet_id']."';
			");
		}


        /**********************************************/
        /* Handelstransport kommt beim Zielplanet an  */
        /* (Nur Rohstoffe)                            */
        /**********************************************/
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
					".$db_table['fleet_ships']." 
				WHERE 
					fs_fleet_id='".$arr['fleet_id']."';
			");

			// Flotte aufheben
			dbquery("
				DELETE FROM 
					".$db_table['fleet']." 
				WHERE 
					fleet_id='".$arr['fleet_id']."';
			");
		}


        /************************************************/
        /* Transportflotte kommt beim Zielplaneten an   */
        /*                                              */
        /************************************************/
		elseif ($arr['fleet_action']=="to")
		{
            //Waren ausladen
            $msgres=fleet_land($arr,2);

			//Sucht User-ID
			$user_to_id = get_user_id_by_planet($arr['fleet_planet_to']);

			$msg = "[B]TRANSPORT GELANDET[/B]\n\nEine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat ihr Ziel erreicht!\n\n[b]Planet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Zeit:[/b] ".date("d.m.y, H:i:s",$arr['fleet_landtime'])."\n";
			$msg.= $msgres;
			// Nachrichten senden
			send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Transport angekommen",$msg);
			//Nachricht an Empfänger senden, falls Empfänger != Sender
			if ($arr['fleet_user_id']!=$user_to_id)
				send_msg($user_to_id,SHIP_MISC_MSG_CAT_ID,"Transport angekommen",$msg);

            // Flotte zurückschicken & Waren aus dem Frachtraum löschen
            fleet_return($arr,"tr","0","0","0","0","0","0");

			// Handel loggen falls der transport an einen anderen user ging
      		if($arr['fleet_user_id']!=$user_to_id)
      		{
      			add_log("11","Der Spieler [URL=?page=user&sub=edit&user_id=".$arr['fleet_user_id']."][B]".get_user_nick($arr['fleet_user_id'])."[/B][/URL] sendet dem Spieler [URL=?page=user&sub=edit&user_id=".$user_to_id."][B]".get_user_nick($user_to_id)."[/B][/URL] folgende Rohstoffe\n\n".$msgres."",time());
      		}
		}


        /*****************************************/
        /* Recycler kommt beim Zielplaneten an   */
        /* Trümmerfeld (Titan, Silizium, PVC)    */
        /*****************************************/
		elseif ($arr['fleet_action']=="wo")
		{
			$capa=$arr['fleet_capacity'];

			//Lädt Trümmerfeld Rohstoffe
			$rparr = mysql_fetch_array(dbquery("
				SELECT 
					planet_wf_metal,
					planet_wf_crystal,
					planet_wf_plastic 
				FROM 
					".$db_table['planets']." 
				WHERE 
					planet_id='".$arr['fleet_planet_to']."';
			"));
			$raid_r[0]=$rparr['planet_wf_metal'];
			$raid_r[1]=$rparr['planet_wf_crystal'];
			$raid_r[2]=$rparr['planet_wf_plastic'];
			//Rohstoffe prozentual aufteilen, wenn die Kapazität nicht für das ganze TF reicht
			for ($rcnt=0;$rcnt<3;$rcnt++)
			{
				if ($capa<=array_sum($raid_r))
					$raid_r_to_ship[$rcnt]+=round($raid_r[$rcnt]*$capa/array_sum($raid_r));
				else
					$raid_r_to_ship[$rcnt]+=round($raid_r[$rcnt]);
			}

			// Rohstoffe vom Planeten abziehen
			dbquery("
				UPDATE
					".$db_table['planets']."
				SET
					planet_wf_metal=planet_wf_metal-'".$raid_r_to_ship[0]."',
					planet_wf_crystal=planet_wf_crystal-'".$raid_r_to_ship[1]."',
					planet_wf_plastic=planet_wf_plastic-'".$raid_r_to_ship[2]."'
				WHERE
					planet_id='".$arr['fleet_planet_to']."';
			");

			//Summiert erhaltene Rohstoffe vom TF zu des Ladung
			$metal=$arr['fleet_res_metal']+$raid_r_to_ship[0];
			$crystal=$arr['fleet_res_crystal']+$raid_r_to_ship[1];
			$plastic=$arr['fleet_res_plastic']+$raid_r_to_ship[2];

			// Flotte zurückschicken mit Ress von TF und bestehenden ress
			fleet_return($arr,"wr",$metal,$crystal,$plastic);

			// Nachricht senden
			$msg = "[b]TR&Uuml;MMERSAMMLER-RAPPORT[/b]\n\nEine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat das Tr&uuml;mmerfeld bei \n[b]".coords_format2($arr['fleet_planet_to'])."[/b]\num [b]".date("d.m.y, H:i:s",$arr['fleet_landtime'])."[/b]\n erreicht und Tr&uuml;mmer gesammelt.\n";
			$msgres = "\n[b]ROHSTOFFE:[/b]\n\n".RES_METAL.": ".nf($raid_r_to_ship[0])."\n".RES_CRYSTAL.": ".nf($raid_r_to_ship[1])."\n".RES_PLASTIC.": ".nf($raid_r_to_ship[2])."";
			send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Tr&uuml;mmer gesammelt",$msg.$msgres);


            //Erbeutete Rohstoffsumme speichern
            $res_sum=array_sum($raid_r_to_ship);
            dbquery("
				UPDATE
					".$db_table['users']."
				SET
					user_res_from_tf=user_res_from_tf+'".$res_sum."'
				WHERE
					user_id='".$arr['fleet_user_id']."';
			");  

			//Log schreiben
			add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] hat das Tr&uuml;mmerfeld bei [b]".coords_format2($arr['fleet_planet_to'])."[/b] um [b]".date("d.m.y, H:i:s",$arr['fleet_landtime'])."[/b] erreicht und Tr&uuml;mmer gesammelt.\n".$msgres,time());
		}


        /******************************************/
        /* Gassauger kommt beim Zielplaneten an   */
        /* (Tritium abbaubar)                     */
        /******************************************/
		elseif ($arr['fleet_action']=="go")
		{

			$destroy=0;
			if (mt_rand(0,100)>60)	// 40 % Chance dass Schiffe überhaupt zerstört werden
      			$destroy=mt_rand(0,20);		// 0 <= X <= 20 Prozent an Schiffen werden Zerstört
			if($destroy>0)
			{
                $cnt_res=dbquery("
					SELECT
						s.ship_name,
						fs.fs_ship_id,
						fs.fs_ship_cnt
					FROM
						(
							".$db_table['fleet_ships']." AS fs INNER JOIN ".$db_table['fleet']." AS f ON fs.fs_fleet_id = f.fleet_id 
						) 
						INNER JOIN ".$db_table['ships']." AS s ON fs.fs_ship_id = s.ship_id
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
                    	//"Zerstörte" Schiffe aus der Flotte löschen
                        dbquery("
							UPDATE
								".$db_table['fleet_ships']."
							SET
								fs_ship_cnt=fs_ship_cnt-'".$ship_destroy."'
							WHERE
								fs_fleet_id='".$arr['fleet_id']."'
								AND fs_ship_id='".$cnt_arr['fs_ship_id']."';
						");
                        $destroyed_ships.="".$ship_destroy." ".$cnt_arr['ship_name']."\n";
                    }
                }
                if($ship_destroy>0)
                {
				    $destroyed_ships_msg="\n\nAufgrund starker Wasserstoffexplosionen sind einige deiner Schiffe zerst&ouml;rt worden:\n\n".$destroyed_ships."";
				}
			}
			else
				$destroyed_ships_msg="";

			// Anzahl gesammelter Rohstoffe berechen
            $capa=$arr['fleet_capacity_nebula'];
          	$fuel = mt_rand(1000,$capa);

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
					".$db_table['users']."
				SET
					user_res_from_nebula=user_res_from_nebula+'".$fuel."'
				WHERE
					user_id='".$arr['fleet_user_id']."';
			");  

      		//Log schreiben
			add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] hat den Gasplaneten [b]".coords_format2($arr['fleet_planet_to'])."[/b] um [b]".date("d.m.y, H:i:s",$arr['fleet_landtime'])."[/b] erreicht und Gas gesaugt.\n".$msgres,time());
		}


        /**************************************************/
        /* Asteroiden sammeln kommt beim Zielplaneten an  */
        /* (Titan,Silizium,PVC abbaubar)                  */
        /**************************************************/
		elseif ($arr['fleet_action']=="yo")
		{
            $cell_id=$arr['fleet_cell_to'];

            // ist das asteroiden feld noch vorhanden?
            $res_exist = dbquery("
				SELECT 
					cell_asteroid 
				FROM 
					".$db_table['space_cells']." 
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
							".$db_table['space_cells']." 
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
							".$db_table['space_cells']." 
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
							".$db_table['space_cells']." 
						WHERE 
							cell_id='".$cell_id."';
					");
                    $arr_ress_check = mysql_fetch_array($res_ress_check);

                    if($arr_ress_check['cell_asteroid_ress']<1000)
                    {
                        // altes "löschen" //
                        dbquery("
							UPDATE 
								".$db_table['space_cells']." 
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
								".$db_table['space_cells']." 
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
									".$db_table['space_cells']." 
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
									".$db_table['space_cells']." 
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
							".$db_table['users']."
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
							".$db_table['fleet_ships']." 
						WHERE 
							fs_fleet_id='".$arr['fleet_id']."';
					");

                    // Flotte aufheben
                    dbquery("
						DELETE FROM 
							".$db_table['fleet']." 
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
		}


        /**********************************************/
        /* Nebel erkunden kommt beim Zielplaneten an  */
        /* (Silizium abbaubar!)                       */
        /**********************************************/
		elseif ($arr['fleet_action']=="no")
		{
			$cell_id=$arr['fleet_cell_to'];

			// ist das nebel feld noch vorhanden?
			$res_exist = dbquery("
				SELECT 
					cell_nebula 
				FROM 
					".$db_table['space_cells']." 
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
							".$db_table['space_cells']." 
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
							".$db_table['space_cells']." 
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
							".$db_table['space_cells']." 
						WHERE 
							cell_id='".$cell_id."';
					");
                    $arr_ress_check = mysql_fetch_array($res_ress_check);

                    if($arr_ress_check['cell_nebula_ress']<1000)
                    {
                        // altes "löschen" //
                        dbquery("
							UPDATE 
								".$db_table['space_cells']." 
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
								".$db_table['space_cells']." 
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
									".$db_table['space_cells']." 
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
								".$db_table['space_cells']." 
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
							".$db_table['users']."
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
							".$db_table['fleet_ships']." 
						WHERE 
							fs_fleet_id='".$arr['fleet_id']."';
					");

                    // Flotte aufheben
                    dbquery("
						DELETE FROM 
							".$db_table['fleet']."
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
		}


        /****************************************************/
        /* Trümmerfeld erstellen kommt beim Zielplanet an   */
        /* (Gesamte Flotte wird zerstört)                   */
        /****************************************************/
		elseif ($arr['fleet_action']=="zo")
		{
			//Verwandelt die ganze Flotte in ein TF (Grösse = 40% der Baukosten)
			$rres=dbquery("
				SELECT
					s.ship_id,
					s.ship_costs_metal,
					s.ship_costs_crystal,
					s.ship_costs_plastic,
					fs.fs_ship_cnt
				FROM
					".$db_table['fleet_ships']." AS fs 
					INNER JOIN ".$db_table['ships']." AS s ON fs.fs_ship_id = s.ship_id
					AND fs.fs_fleet_id='".$arr['fleet_id']."';
			");
			while ($rarr=mysql_fetch_array($rres))
			{
				$cnt=ceil($rarr['fs_ship_cnt']*0.4);
				$tf_metal+=$cnt*$rarr['ship_costs_metal'];
				$tf_crystal+=$cnt*$rarr['ship_costs_crystal'];
				$tf_plastic+=$cnt*$rarr['ship_costs_plastic'];

				dbquery("
					DELETE FROM 
						".$db_table['fleet_ships']." 
					WHERE 
						fs_fleet_id='".$arr['fleet_id']."' 
						AND fs_ship_id='".$rarr['ship_id']."';
				");
			}

			//Speichert enstandenes TF (Rohstoffe werden zum bestehenden TF summiert)
			dbquery("
				UPDATE
					".$db_table['planets']."
				SET
					planet_wf_metal=planet_wf_metal+'".$tf_metal."',
					planet_wf_crystal=planet_wf_crystal+'".$tf_crystal."',
					planet_wf_plastic=planet_wf_plastic+'".$tf_plastic."'
				WHERE
					planet_id='".$arr['fleet_planet_to']."';
			");

            // Flotte-Schiffe-Verknüpfungen löschen
            dbquery("
				DELETE FROM 
					".$db_table['fleet_ships']." 
				WHERE 
					fs_fleet_id='".$arr['fleet_id']."';
			");

            // Flotte aufheben
            dbquery("
				DELETE FROM 
					".$db_table['fleet']." 
				WHERE 
					fleet_id='".$arr['fleet_id']."';
			");

			//Nachricht senden
			$coords_target = coords_format2($arr['fleet_planet_to']);
			$coords_from = coords_format2($arr['fleet_planet_from']);
			$msg="Eine Flotte vom Planeten ".$coords_from." hat auf dem Planeten ".$coords_target." ein Trümmerfeld erstellt.";
			send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Trümmerfeld erstellt",$msg);

			//Log schreiben
			add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten ".$coords_from." hat auf dem Planeten ".$coords_target." ein Trümmerfeld erstellt.",time());
		}


        /*************************************************/
        /* Inavasionsflotte kommt beim Zielplaneten an   */
        /*                                               */
        /*************************************************/
        elseif ($arr['fleet_action']=="io")
        {
        	//Lädt User-ID des momentanen Besitzers
            $res_check=dbquery("
				SELECT 
					planet_user_id 
				FROM 
					".$db_table['planets']." 
				WHERE 
					planet_id='".$arr['fleet_planet_to']."';
			");
            $arr_check=mysql_fetch_array($res_check);

            //Kontrolliert bei einer Invasion, ob der Planet nicht schon demjenigengehört gehört
            //gehört bereits dem User, dann flotte stationieren
            if($arr_check['planet_user_id']==$arr['fleet_user_id'])
            {
                //Flotte stationieren & Waren ausladen (ohne den Abzug eines Invasionsschiffes)
                $msg_ship_res=fleet_land($arr,1,0,1);

                // Flotte-Schiffe-Verknüpfungen löschen
                dbquery("
					DELETE FROM 
						".$db_table['fleet_ships']." 
					WHERE 
						fs_fleet_id='".$arr['fleet_id']."';
				");

                // Flotte aufheben
                dbquery("
					DELETE FROM 
						".$db_table['fleet']." 
					WHERE 
						fleet_id='".$arr['fleet_id']."';
				");

				//Nachricht senden
                $msg = "Die Flotte hat folgendes Ziel erreicht:\n[b]Planet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Zeit:[/b] ".date("d.m.Y H:i",$arr['fleet_landtime'])."\n[b]Bericht:[/b] Die Flotte ist auf dem Planeten gelandet!";
                $msg.= $msg_ship_res[0].$msg_ship_res[1];
                send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Flotte angekommen",$msg);

                $showinfo=0;
            }
            //gehört nicht dem User, dann fight
            else
            {
                include("update_fleet_fight.php");
            }
        }


        /*********************************************************************/
        /* Spionageflotte oder Angriffsflotte kommt beim Zielplaneten an     */
        /* (benötigt battle() Funktion welche zuoberst includet wird)        */
        /* Prüfen auf: Angreiffen (ao), Spionieren (so), Bombardieren (bo),  */
        /*             Giftgas (xo), Spionageangriff (lo), Tarnangriff (vo), */
        /*             Antrax (ho), EMP (do)                                 */
        /*********************************************************************/
        elseif ($arr['fleet_action']=="ao" || $arr['fleet_action']=="so" || $arr['fleet_action']=="bo" || $arr['fleet_action']=="xo" || $arr['fleet_action']=="lo" || $arr['fleet_action']=="vo" || $arr['fleet_action']=="ho" || $arr['fleet_action']=="do")
        {
            include("update_fleet_fight.php");
        }

		// Update-Flag löschen, Update-Counter erhöhen
		dbquery("
			UPDATE
				".$db_table['fleet']."
			SET
				fleet_updating=0,
				fleet_update_counter=fleet_update_counter+1
			WHERE
				fleet_id='".$arr['fleet_id']."';
		");

		return true;
	}

	//
	// Wenn Voraussetzungen falsch sind, Warnung ausgeben
	//
	else
	{
		echo "Die Flotte ".$arr['fleet_id']." konnte nicht bearbeitet werden, Landezeit falsch!\n";
		return false;
	}

    //Arrays löschen (Speicher freigeben)
    unset($arr);
    unset($msg);
    unset($msgres);
}
?>
