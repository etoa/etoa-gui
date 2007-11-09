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

/* Veraltet:
		$arr['fleet_action']=="po" || $arr['fleet_action']=="poc"
		|| $arr['fleet_action']=="tr" || $arr['fleet_action']=="toc"
		|| $arr['fleet_action']=="ar" || $arr['fleet_action']=="aoc"
		|| $arr['fleet_action']=="sr" || $arr['fleet_action']=="soc"
		|| $arr['fleet_action']=="hr" || $arr['fleet_action']=="hoc"
		|| $arr['fleet_action']=="ko" || $arr['fleet_action']=="koc"
		|| $arr['fleet_action']=="ir"  || $arr['fleet_action']=="ioc"
		|| $arr['fleet_action']=="wr" || $arr['fleet_action']=="woc"
		|| $arr['fleet_action']=="fr"   || $arr['fleet_action']=="foc"
		|| $arr['fleet_action']=="gr" || $arr['fleet_action']=="goc"
		|| $arr['fleet_action']=="br" || $arr['fleet_action']=="boc"
		|| $arr['fleet_action']=="lr" || $arr['fleet_action']=="loc"
		|| $arr['fleet_action']=="xr" || $arr['fleet_action']=="xoc"
		|| $arr['fleet_action']=="vr" || $arr['fleet_action']=="voc"
		|| $arr['fleet_action']=="dr" || $arr['fleet_action']=="doc"
		|| $arr['fleet_action']=="zr" || $arr['fleet_action']=="zoc"
		|| $arr['fleet_action']=="yr" || $arr['fleet_action']=="yoc"
		|| $arr['fleet_action']=="er" || $arr['fleet_action']=="eoc"
		|| $arr['fleet_action']=="nr"  || $arr['fleet_action']=="noc")
*/

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
		dbquery("UPDATE ".$db_table['fleet']." SET fleet_updating=1 WHERE fleet_id=".$arr['fleet_id'].";");

        /************************************************************************************/
        /* Flotte soll auf dem Zielplanet landen                                            */
        /* Prüfen auf: Kolonialisieren (ko), Stationieren (po), Rückflug r oder Abbruch c   */
        /************************************************************************************/
		if ($arr['fleet_action']=="po" || $arr['fleet_action']=="ko" || stristr($arr['fleet_action'],"c") || stristr($arr['fleet_action'],"r"))
		{
			$fleet_land = true;

			//
			// Prüfen auf Kolonialisieren
			//
			if ($arr['fleet_action']=="ko" )
			{
				// Planet auf Bewohner prüfen
				if (mysql_num_rows(dbquery("SELECT planet_user_id FROM ".$db_table['planets']." WHERE planet_user_id>0 AND planet_id=".$arr['fleet_planet_to'].";"))>0)
				{
					$fleet_land=false;
					$not_land_reason="Die Flotte kann den Planeten nicht kolonialisieren, da er bereits von einem anderen Volk kolonialisiert wurde!";
				}
				else
				{
					// Auf eigene Maximalanzahl prüfen
					if (mysql_num_rows(dbquery("SELECT planet_user_id FROM ".$db_table['planets']." WHERE planet_user_id='".$arr['fleet_user_id']."';"))>= $conf['user_max_planets']['v'])
					{
						$fleet_land=false;
						$not_land_reason="Die Flotte kann den Planeten nicht kolonialisieren, da die maximale Zahl an Planeten auf denen du regieren darfst, bereits erreicht worden ist!";
					}
					else
					{
						//Planet zurücksetzen
						reset_planet($arr['fleet_planet_to']);
						// Planet übernehmen
						dbquery("UPDATE ".$db_table['planets']." SET
						planet_user_id='".$arr['fleet_user_id']."',
						planet_name='Unbenannt'
						WHERE
						planet_id=".$arr['fleet_planet_to'].";");
					}
				}
			}

			//
			// Flotte landen
			//
			if ($fleet_land)
			{
			    $already_colonialized=0;
			    $total_pilots=0;


			    // Schiffe in Zielplanet-Schiffsliste eintragen
			    $fsres = dbquery("
			    SELECT
			        fs.fs_ship_cnt,
			        fs.fs_ship_id,
			        s.ship_colonialize,
			        s.ship_pilots,
			        s.ship_name,
			        s.special_ship
			    FROM
			        ".$db_table['fleet_ships']." AS fs,
			        ".$db_table['ships']." AS s
			    WHERE
			        fs.fs_ship_id=s.ship_id
			        AND fs.fs_fleet_id='".$arr['fleet_id']."'
			        AND fs.fs_ship_faked='0';");
				if (mysql_num_rows($fsres)>0)
				{
					$msgships = "\n[b]SCHIFFE[/b]\n";
                    while ($fsarr = mysql_fetch_array($fsres))
                    {
                        // Ein Koloschiff subtrahieren, falls kolonialisieren gewählt ist
                        if ($fsarr['ship_colonialize']==1 && $already_colonialized==0 && $arr['fleet_action']=="ko")
                        {
                            $ship_cnt = $fsarr['fs_ship_cnt']-1;
                            $already_colonialized==1;
                        }
                        else
                            $ship_cnt = $fsarr['fs_ship_cnt'];

                        $slres = dbquery("
                        SELECT
                            shiplist_id
                        FROM
                            ".$db_table['shiplist']."
                        WHERE
                            shiplist_ship_id='".$fsarr['fs_ship_id']."'
                            AND shiplist_planet_id='".$arr['fleet_planet_to']."'
                            AND shiplist_user_id='".$arr['fleet_user_id']."';");
                        $slarr = mysql_fetch_array($slres);


                        if($arr['fleet_action']=="po" || $arr['fleet_action']=="ko")
                        {
                            $special_slres = dbquery("
                            SELECT
                                shiplist_id,
                                shiplist_special_ship_level,
                                shiplist_special_ship_exp,
                                shiplist_special_ship_bonus_weapon,
                                shiplist_special_ship_bonus_structure,
                                shiplist_special_ship_bonus_shield,
                                shiplist_special_ship_bonus_heal,
                                shiplist_special_ship_bonus_capacity,
                                shiplist_special_ship_bonus_speed,
                                shiplist_special_ship_bonus_pilots,
                                shiplist_special_ship_bonus_tarn
                            FROM
                                ".$db_table['shiplist']."
                            WHERE
                                shiplist_ship_id='".$fsarr['fs_ship_id']."'
                                AND shiplist_planet_id='".$arr['fleet_planet_from']."'
                                AND shiplist_user_id='".$arr['fleet_user_id']."';");
                            $special_slarr = mysql_fetch_array($special_slres);

                            if (mysql_num_rows($slres)>0)
                            {
                                dbquery("
                                UPDATE
                                    ".$db_table['shiplist']."
                                SET
                                    shiplist_count=shiplist_count+".$ship_cnt.",
                                    shiplist_special_ship=".$fsarr['special_ship'].",
                                    shiplist_special_ship_level=".$special_slarr['shiplist_special_ship_level'].",
                                    shiplist_special_ship_exp=".$special_slarr['shiplist_special_ship_exp'].",
                                    shiplist_special_ship_bonus_weapon=".$special_slarr['shiplist_special_ship_bonus_weapon'].",
                                    shiplist_special_ship_bonus_structure=".$special_slarr['shiplist_special_ship_bonus_structure'].",
                                    shiplist_special_ship_bonus_shield=".$special_slarr['shiplist_special_ship_bonus_shield'].",
                                    shiplist_special_ship_bonus_heal=".$special_slarr['shiplist_special_ship_bonus_heal'].",
                                    shiplist_special_ship_bonus_capacity=".$special_slarr['shiplist_special_ship_bonus_capacity'].",
                                    shiplist_special_ship_bonus_speed=".$special_slarr['shiplist_special_ship_bonus_speed'].",
                                    shiplist_special_ship_bonus_pilots=".$special_slarr['shiplist_special_ship_bonus_pilots'].",
                                    shiplist_special_ship_bonus_tarn=".$special_slarr['shiplist_special_ship_bonus_tarn']."
                                WHERE
                                    shiplist_id=".$slarr['shiplist_id'].";");


                            }
                            else
                            {
                                dbquery("
                                INSERT INTO
                                ".$db_table['shiplist']."
                                    (shiplist_user_id,
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
                                    shiplist_special_ship_bonus_tarn)
                                VALUES
                                    ('".$arr['fleet_user_id']."',
                                    '".$fsarr['fs_ship_id']."',
                                    '".$arr['fleet_planet_to']."',
                                    '".$ship_cnt."',
                                    '".$fsarr['special_ship']."',
                                    '".$special_slarr['shiplist_special_ship_level']."',
                                    '".$special_slarr['shiplist_special_ship_exp']."',
                                    '".$special_slarr['shiplist_special_ship_bonus_weapon']."',
                                    '".$special_slarr['shiplist_special_ship_bonus_structure']."',
                                    '".$special_slarr['shiplist_special_ship_bonus_shield']."',
                                    '".$special_slarr['shiplist_special_ship_bonus_heal']."',
                                    '".$special_slarr['shiplist_special_ship_bonus_capacity']."',
                                    '".$special_slarr['shiplist_special_ship_bonus_speed']."',
                                    '".$special_slarr['shiplist_special_ship_bonus_pilots']."',
                                    '".$special_slarr['shiplist_special_ship_bonus_tarn']."');");


                            }
                            $msgships.= "\n[b]".$fsarr['ship_name'].":[/b] ".nf($ship_cnt);
                      	}
                      	else
                      	{
                            shiplistAdd($arr['fleet_planet_to'],$arr['fleet_user_id'],$fsarr['fs_ship_id'],$ship_cnt);
                            $msgships.= "\n[b]".$fsarr['ship_name'].":[/b] ".nf($ship_cnt);
                      	}
			    	}
				}

			  $people=$arr['fleet_pilots']+$arr['fleet_res_people'];

			  // Waren entladen
			  dbquery("
			  UPDATE
			  	".$db_table['planets']."
			  SET
			      planet_res_metal=planet_res_metal+".$arr['fleet_res_metal'].",
			      planet_res_crystal=planet_res_crystal+".$arr['fleet_res_crystal'].",
			      planet_res_plastic=planet_res_plastic+".$arr['fleet_res_plastic'].",
			      planet_res_fuel=planet_res_fuel+".$arr['fleet_res_fuel'].",
			      planet_res_food=planet_res_food+".$arr['fleet_res_food'].",
			      planet_people=planet_people+".$people."
			  WHERE
			  	planet_id=".$arr['fleet_planet_to'].";");
			  $msgres= "\n\n[b]WAREN[/b]\n\n[b]".RES_METAL.":[/b] ".nf($arr['fleet_res_metal'])."\n[b]".RES_CRYSTAL.":[/b] ".nf($arr['fleet_res_crystal'])."\n[b]".RES_PLASTIC.":[/b] ".nf($arr['fleet_res_plastic'])."\n[b]".RES_FUEL.":[/b] ".nf($arr['fleet_res_fuel'])."\n[b]".RES_FOOD.":[/b] ".nf($arr['fleet_res_food'])."\n[b]Bewohner:[/b] ".nf($arr['fleet_res_people'])."\n";


			  // Flotte-Schiffe-Verknüpfungen löschen
			  dbquery("DELETE FROM ".$db_table['fleet_ships']." WHERE fs_fleet_id='".$arr['fleet_id']."';");

			  // Flotte aufheben
			  dbquery("DELETE FROM ".$db_table['fleet']." WHERE fleet_id='".$arr['fleet_id']."';");

			  // Nachricht senden
			  if ($arr['fleet_action']=="ko")
			  {
			  	send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Planet kolonialisiert","Die Flotte hat folgendes Ziel erreicht:\n[b]Planet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Zeit:[/b] ".date("d.m.Y H:i",$arr['fleet_landtime'])."\n[b]Bericht:[/b] Die Flotte hat eine neue Kolonie errichtet!");
			  }
			  else
			  {
			  	$msg = "[b]FLOTTE GELANDET[/b]\n\nEine eurer Flotten hat hat ihr Ziel erreicht!\n\n[b]Zielplanet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Startplanet:[/b] ".coords_format2($arr['fleet_planet_from'])."\n[b]Zeit:[/b] ".date("d.m.y, H:i:s",$arr['fleet_landtime'])."\n[b]Auftrag:[/b] ".get_fleet_action($arr['fleet_action']);
			  	$msg.= $msgres.$msgships;
			  	send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Flotte angekommen",$msg);
			  }
			  $showinfo=0;
			}
			else
			{
			    $msg = "$not_land_reason\n";
			    send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Landung nicht möglich",$msg);

			    // Flotte zurückschicken
			    $duration = $arr['fleet_landtime'] - $arr['fleet_launchtime'];
			    $launchtime=$arr['fleet_landtime'];
			    $landtime=$launchtime+$duration;
			    dbquery("UPDATE ".$db_table['fleet']." SET fleet_cell_from='".$arr['fleet_cell_to']."',fleet_cell_to='".$arr['fleet_cell_from']."',fleet_planet_from='".$arr['fleet_planet_to']."',fleet_planet_to='".$arr['fleet_planet_from']."',fleet_action='koc',fleet_launchtime=$launchtime,fleet_landtime=$landtime WHERE fleet_id='".$arr['fleet_id']."';");
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
			$duration = $arr['fleet_landtime'] - $arr['fleet_launchtime'];
			$launchtime = $arr['fleet_landtime'];

			$landtime=$launchtime+$duration;
			dbquery("
			UPDATE
				".$db_table['fleet']."
			SET
                fleet_cell_from='".$arr['fleet_cell_to']."',
                fleet_cell_to='".$arr['fleet_cell_from']."',
                fleet_planet_from='".$arr['fleet_planet_to']."',
                fleet_planet_to='".$arr['fleet_planet_from']."',
                fleet_action='$action',
                fleet_launchtime=$launchtime,
                fleet_landtime=$landtime
			WHERE
				fleet_id='".$arr['fleet_id']."';");
		}


        /**************************************************/
        /* Handelsstationieren kommt beim Zielplanet an   */
        /* (Schiffe und ress)                             */
        /**************************************************/
		elseif ($arr['fleet_action']=="mpo")
		{
			$user_to_id = get_user_id_by_planet($arr['fleet_planet_to']);
			$fsres = dbquery("SELECT fs_ship_id,fs_ship_cnt FROM ".$db_table['fleet_ships']." WHERE fs_fleet_id='".$arr['fleet_id']."';");
			$fsarr = mysql_fetch_array($fsres);
			$slres = dbquery("
			SELECT 
				shiplist_id,
				shiplist_count 
			FROM 
				".$db_table['shiplist']." 
			WHERE 
				shiplist_planet_id='".$arr['fleet_planet_to']."'
				AND shiplist_user_id='$user_to_id'
				AND shiplist_ship_id='".$fsarr['fs_ship_id']."';");
			if (mysql_num_rows($slres)!=0)
			{
				$slarr = mysql_fetch_array($slres);
				$count=$fsarr['fs_ship_cnt']+$slarr['shiplist_count'];
				dbquery("UPDATE ".$db_table['shiplist']." SET shiplist_count='".$count."' WHERE shiplist_id='".$slarr['shiplist_id']."' AND shiplist_planet_id='".$arr['fleet_planet_to']."' AND shiplist_user_id='".$user_to_id."'");
			}
			else
			{
				dbquery("INSERT INTO ".$db_table['shiplist']." (shiplist_user_id,shiplist_ship_id,shiplist_planet_id,shiplist_count) VALUES('".$user_to_id."','".$fsarr['fs_ship_id']."','".$arr['fleet_planet_to']."','".$fsarr['fs_ship_cnt']."');");
			}

			// Waren entladen
			dbquery("
			UPDATE 
				".$db_table['planets']." 
			SET 
                planet_res_metal=planet_res_metal+".$arr['fleet_res_metal'].",
                planet_res_crystal=planet_res_crystal+".$arr['fleet_res_crystal'].",
                planet_res_plastic=planet_res_plastic+".$arr['fleet_res_plastic'].",
                planet_res_fuel=planet_res_fuel+".$arr['fleet_res_fuel'].",
                planet_res_food=planet_res_food+".$arr['fleet_res_food']."  
			WHERE 
				planet_id=".$arr['fleet_planet_to'].";");

			//Nachricht senden
			$msg.="Eine Flotte vom Handelsministerium hat folgendes Ziel erreicht:\n[b]Planet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Zeit:[/b] ".date("d.m.Y H:i",$arr['fleet_landtime'])."\n[b]Bericht:[/b] Die gekauften Schiffe sind gelandet.\n\n";

			//wenn das schiff auch ress mitgebracht hat
			if($arr['fleet_res_metal']!='0' || $arr['fleet_res_crystal']!='0' || $arr['fleet_res_plastic']!='0' || $arr['fleet_res_fuel']!='0' || $arr['fleet_res_food']!='0')
			{

				$msgres= "\n[b]".RES_METAL.":[/b] ".nf($arr['fleet_res_metal'])."\n[b]".RES_CRYSTAL.":[/b] ".nf($arr['fleet_res_crystal'])."\n[b]".RES_PLASTIC.":[/b] ".nf($arr['fleet_res_plastic'])."\n[b]".RES_FUEL.":[/b] ".nf($arr['fleet_res_fuel'])."\n[b]".RES_FOOD.":[/b] ".nf($arr['fleet_res_food'])."\n\n";

				$msg.="Es wurden zudem folgende Rohstoffe abgeladen:\n";
				$msg.="$msgres";
			}

			$msg.="Unser Unternehmen dankt ihnen f&uuml;r die Unterst&uuml;tzung und wir hoffen sie sind mit uns zufrieden und w&uuml;nschen ihnen auch in Zukunft viel Erfolg.\nDas Handelsministerium";

			send_msg($user_to_id,SHIP_MISC_MSG_CAT_ID,"Flotte vom Handelsministerium",$msg);

			// Flotte-Schiffe-Verknüpfungen löschen
			dbquery("DELETE FROM ".$db_table['fleet_ships']." WHERE fs_fleet_id='".$arr['fleet_id']."';");

			// Flotte aufheben
			dbquery("DELETE FROM ".$db_table['fleet']." WHERE fleet_id='".$arr['fleet_id']."';");
		}


        /**********************************************/
        /* Handelstransport kommt beim Zielplanet an  */
        /* (nur ress)                                 */
        /**********************************************/
		elseif ($arr['fleet_action']=="mto")
		{
			// Waren entladen
			dbquery("UPDATE ".$db_table['planets']." SET planet_res_metal=planet_res_metal+".$arr['fleet_res_metal'].",planet_res_crystal=planet_res_crystal+".$arr['fleet_res_crystal'].",planet_res_plastic=planet_res_plastic+".$arr['fleet_res_plastic'].",planet_res_fuel=planet_res_fuel+".$arr['fleet_res_fuel'].",planet_res_food=planet_res_food+".$arr['fleet_res_food']."  WHERE planet_id=".$arr['fleet_planet_to'].";");


			$msgres= "\n[b]".RES_METAL.":[/b] ".nf($arr['fleet_res_metal'])."\n[b]".RES_CRYSTAL.":[/b] ".nf($arr['fleet_res_crystal'])."\n[b]".RES_PLASTIC.":[/b] ".nf($arr['fleet_res_plastic'])."\n[b]".RES_FUEL.":[/b] ".nf($arr['fleet_res_fuel'])."\n[b]".RES_FOOD.":[/b] ".nf($arr['fleet_res_food'])."\n\n";

			$msg = "Eine Flotte vom Handelsministerium hat folgendes Ziel erreicht:\n[b]Planet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Zeit:[/b] ".date("d.m.Y H:i",$arr['fleet_landtime'])."\n[b]Bericht:[/b] Folgende Waren wurden ausgeladen:\n\n";
			$msg.= $msgres;
			$msg.="Unser Unternehmen dankt ihnen f&uuml;r die Unterst&uuml;tzung und wir hoffen sie sind mit uns zufrieden und w&uuml;nschen ihnen auch in Zukunft viel Erfolg.\nDas Handelsministerium";
			$user_to_id = get_user_id_by_planet($arr['fleet_planet_to']);

			send_msg($user_to_id,SHIP_MISC_MSG_CAT_ID,"Transport vom Handelsministerium",$msg);

			// Flotte löschen und Schiffe löschen
			dbquery("DELETE FROM ".$db_table['fleet']." WHERE fleet_id='".$arr['fleet_id']."';");
			dbquery("DELETE FROM ".$db_table['fleet_ships']." WHERE fs_fleet_id='".$arr['fleet_id']."';");
		}


        /************************************************/
        /* Transportflotte kommt beim Zielplaneten an   */
        /*                                              */
        /************************************************/
		elseif ($arr['fleet_action']=="to")
		{
			// Waren entladen
			dbquery("UPDATE ".$db_table['planets']." SET
				planet_res_metal=planet_res_metal+".$arr['fleet_res_metal'].",
				planet_res_crystal=planet_res_crystal+".$arr['fleet_res_crystal'].",
				planet_res_plastic=planet_res_plastic+".$arr['fleet_res_plastic'].",
				planet_res_fuel=planet_res_fuel+".$arr['fleet_res_fuel'].",
				planet_res_food=planet_res_food+".$arr['fleet_res_food'].",
				planet_people=planet_people+".$arr['fleet_res_people']."
			WHERE planet_id=".$arr['fleet_planet_to'].";");
			$msgres = "\n[b]".RES_METAL.":[/b] ".nf($arr['fleet_res_metal'])."\n[b]".RES_CRYSTAL.":[/b] ".nf($arr['fleet_res_crystal'])."\n[b]".RES_PLASTIC.":[/b] ".nf($arr['fleet_res_plastic'])."\n[b]".RES_FUEL.":[/b] ".nf($arr['fleet_res_fuel'])."\n[b]".RES_FOOD.":[/b] ".nf($arr['fleet_res_food'])."\n[b]Bewohner:[/b] ".nf($arr['fleet_res_people']);
			$msg = "[B]TRANSPORT GELANDET[/B]\n\nEine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat ihr Ziel erreicht!\n\n[b]Planet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Zeit:[/b] ".date("d.m.y, H:i:s",$arr['fleet_landtime'])."\n\n[B]WAREN[/B]\n";
			$msg.= $msgres;
			$user_to_id = get_user_id_by_planet($arr['fleet_planet_to']);
			// Nachrichten senden
			send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Transport angekommen",$msg);
			if ($arr['fleet_user_id']!=$user_to_id)
				send_msg($user_to_id,SHIP_MISC_MSG_CAT_ID,"Transport angekommen",$msg);

			// Flotte zurückschicken & Waren aus dem Frachtraum löschen
			$duration = $arr['fleet_landtime'] - $arr['fleet_launchtime'];
			$launchtime = $arr['fleet_landtime'];
			$landtime=$launchtime+$duration;
			dbquery("UPDATE ".$db_table['fleet']." SET fleet_cell_from='".$arr['fleet_cell_to']."',fleet_cell_to='".$arr['fleet_cell_from']."',fleet_planet_from='".$arr['fleet_planet_to']."',fleet_planet_to='".$arr['fleet_planet_from']."',fleet_action='tr',fleet_launchtime=$launchtime,fleet_landtime=$landtime,fleet_res_metal=0,fleet_res_crystal=0,fleet_res_plastic=0,fleet_res_fuel=0,fleet_res_food=0,fleet_res_people=0 WHERE fleet_id='".$arr['fleet_id']."';");

			// Handel loggen falls der transport an einen anderen user ging
      		if($arr['fleet_user_id']!=$user_to_id)
      		{
      			add_log("11","Der Spieler [URL=?page=user&sub=edit&user_id=".$arr['fleet_user_id']."][B]".get_user_nick($arr['fleet_user_id'])."[/B][/URL] sendet dem Spieler [URL=?page=user&sub=edit&user_id=".$user_to_id."][B]".get_user_nick($user_to_id)."[/B][/URL] folgende Rohstoffe\n\n".$msgres."",time());
      		}
		}


        /*****************************************/
        /* Recycler kommt beim Zielplaneten an   */
        /*                                       */
        /*****************************************/
		elseif ($arr['fleet_action']=="wo")
		{

			$capa=0;
			$rfrar=mysql_fetch_row(dbquery("
			SELECT
				SUM(s.ship_capacity*fs.fs_ship_cnt) AS capa 
			FROM 
                ".$db_table['fleet_ships']." AS fs,
                ".$db_table['ships']." AS s,
                ".$db_table['fleet']." AS f 
			WHERE 
                fs.fs_ship_id=s.ship_id 
                AND fs.fs_fleet_id=f.fleet_id 
                AND f.fleet_id=".$arr['fleet_id']." 
			GROUP BY 
				f.fleet_id;"));
			$capa=$rfrar[0];

			$rparr = mysql_fetch_array(dbquery("SELECT planet_wf_metal,planet_wf_crystal,planet_wf_plastic FROM ".$db_table['planets']." WHERE planet_id=".$arr['fleet_planet_to'].";"));
			$raid_r[0]=$rparr['planet_wf_metal'];
			$raid_r[1]=$rparr['planet_wf_crystal'];
			$raid_r[2]=$rparr['planet_wf_plastic'];
			for ($rcnt=0;$rcnt<3;$rcnt++)
			{
				if ($capa<=array_sum($raid_r))
					$raid_r_to_ship[$rcnt]+=round($raid_r[$rcnt]*$capa/array_sum($raid_r));
				else
					$raid_r_to_ship[$rcnt]+=round($raid_r[$rcnt]);
			}

			// Rohstoffe vom Planeten abziehen
			dbquery("UPDATE ".$db_table['planets']." SET planet_wf_metal=planet_wf_metal-".$raid_r_to_ship[0].",planet_wf_crystal=planet_wf_crystal-".$raid_r_to_ship[1].",planet_wf_plastic=planet_wf_plastic-".$raid_r_to_ship[2]." WHERE planet_id=".$arr['fleet_planet_to'].";");

			// Flotte zurückschicken
			$duration = $arr['fleet_landtime'] - $arr['fleet_launchtime'];
			$launchtime=$arr['fleet_landtime'];
			$landtime=$launchtime+$duration;
			dbquery("UPDATE ".$db_table['fleet']." SET
				fleet_cell_from='".$arr['fleet_cell_to']."',
				fleet_cell_to='".$arr['fleet_cell_from']."',
				fleet_planet_from='".$arr['fleet_planet_to']."',
				fleet_planet_to='".$arr['fleet_planet_from']."',
				fleet_action='wr',
				fleet_launchtime=$launchtime,
				fleet_landtime=$landtime,
				fleet_res_metal=".$raid_r_to_ship[0].",
				fleet_res_crystal=".$raid_r_to_ship[1].",
				fleet_res_plastic=".$raid_r_to_ship[2]."
			WHERE fleet_id='".$arr['fleet_id']."';");

			// Nachricht und Log speichern
			$msg = "[b]TR&Uuml;MMERSAMMLER-RAPPORT[/b]\n\nEine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat das Tr&uuml;mmerfeld bei \n[b]".coords_format2($arr['fleet_planet_to'])."[/b]\num [b]".date("d.m.y, H:i:s",$arr['fleet_landtime'])."[/b]\n erreicht und Tr&uuml;mmer gesammelt.\n";
			$msgres = "\n[b]ROHSTOFFE:[/b]\n\n".RES_METAL.": ".nf($raid_r_to_ship[0])."\n".RES_CRYSTAL.": ".nf($raid_r_to_ship[1])."\n".RES_PLASTIC.": ".nf($raid_r_to_ship[2])."";
			send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Tr&uuml;mmer gesammelt",$msg.$msgres);
			add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] hat das Tr&uuml;mmerfeld bei [b]".coords_format2($arr['fleet_planet_to'])."[/b] um [b]".date("d.m.y, H:i:s",$arr['fleet_landtime'])."[/b] erreicht und Tr&uuml;mmer gesammelt.\n".$msgres,time());
		}


        /******************************************/
        /* Gassauger kommt beim Zielplaneten an   */
        /*                                        */
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
                    ".$db_table['fleet_ships']." AS fs,
                    ".$db_table['ships']." AS s,
                    ".$db_table['fleet']." AS f
                WHERE
                    f.fleet_id=".$arr['fleet_id']."
                    AND fs.fs_fleet_id=f.fleet_id
                    AND fs.fs_ship_id=s.ship_id
                GROUP BY
                    fs.fs_ship_id;");
                $destroyed_ships="";
                while($cnt_arr=mysql_fetch_array($cnt_res))
                {
                	//Berechnet wie viele Schiffe von jedem Typ zerstört werden
                    $ship_destroy=floor($cnt_arr['fs_ship_cnt']*$destroy/100);
                    if($ship_destroy>0)
                    {
                        dbquery("
                        UPDATE
                            ".$db_table['fleet_ships']."
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
				    $destroyed_ships_msg="\n\nAufgrund starker Wasserstoffexplosionen sind einige deiner Schiffe zerst&ouml;rt worden:\n\n".$destroyed_ships."";
				}
			}
			else
				$destroyed_ships_msg="";

			// Anzahl gesammelter Rohstoffe berechen
            $capa=0;
            $rfrar=mysql_fetch_row(dbquery("
          	SELECT
            	SUM(s.ship_capacity*fs.fs_ship_cnt) AS capa
          	FROM
              	".$db_table['fleet_ships']." AS fs,
              	".$db_table['ships']." AS s,
              	".$db_table['fleet']." AS f
          	WHERE
              	fs.fs_ship_id=s.ship_id
              	AND s.ship_nebula=1
              	AND fs.fs_fleet_id=f.fleet_id
              	AND f.fleet_id=".$arr['fleet_id']."
          	GROUP BY
              	f.fleet_id;"));
          	$capa=$rfrar[0];
          	$fuel = mt_rand(1000,$capa);

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
              	fleet_action='gr',
              	fleet_launchtime=$launchtime,
              	fleet_landtime=$landtime,
              	fleet_res_fuel=$fuel
          	WHERE
              	fleet_id='".$arr['fleet_id']."';");

			$msg = "[b]GASSAUGER-RAPPORT[/b]\n\nEine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat [b]".coords_format2($arr['fleet_planet_to'])."[/b]\num [b]".date("d.m.y, H:i:s",$arr['fleet_landtime'])."[/b]\n erreicht und Gas gesaugt\n";
			$msgres="\n[b]ROHSTOFFE:[/b]\n\n".RES_FUEL.": ".nf($fuel).$destroyed_ships_msg;
      send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Gas gesaugt",$msg.$msgres);
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
            $res_exist = dbquery("SELECT cell_asteroid FROM ".$db_table['space_cells']." WHERE cell_id='$cell_id'");
            $arr_exist = mysql_fetch_array($res_exist);
            // wenn ja, sammle ress
            if($arr_exist['cell_asteroid']=='1')
            {
                $capa=0;
                $rfrar=mysql_fetch_row(dbquery("SELECT SUM(s.ship_capacity*fs.fs_ship_cnt) AS capa FROM ".$db_table['fleet_ships']." AS fs,".$db_table['ships']." AS s,".$db_table['fleet']." AS f WHERE fs.fs_ship_id=s.ship_id AND s.ship_asteroid='1' AND fs.fs_fleet_id=f.fleet_id AND f.fleet_id=".$arr['fleet_id']." GROUP BY f.fleet_id;"));
                $capa=$rfrar[0];
                $capa=round($capa/3);

                //80% Chance das das sammel klappt
                $goornot=mt_rand(1,100);
                if ($goornot>20)
                {
                    // Ressourcen berechnen und abziehen
                    $res_check=dbquery("SELECT cell_asteroid_ress FROM ".$db_table['space_cells']." WHERE cell_id='$cell_id'");
                    $arr_check = mysql_fetch_array($res_check);

                    $max_ress = $arr_check['cell_asteroid_ress']/3;

                    $asteroid = mt_rand(1000,$capa);
                    $metal=round(min($asteroid,$max_ress));

                    $asteroid = mt_rand(1000,$capa);
                    $crystal=round(min($asteroid,$max_ress));

                    $asteroid = mt_rand(1000,$capa);
                    $plastic=round(min($asteroid,$max_ress));

                    $ress_total = $metal + $crystal + $plastic;
                    dbquery("UPDATE ".$db_table['space_cells']." SET cell_asteroid_ress=cell_asteroid_ress-'$ress_total' WHERE cell_id='$cell_id'");

                    //
                    //Wenn Asteroidenfeld keine ress mehr hat -> löschen und neues erstellen
                    //
                    $res_ress_check =dbquery("SELECT cell_asteroid_ress FROM ".$db_table['space_cells']." WHERE cell_id='$cell_id'");
                    $arr_ress_check = mysql_fetch_array($res_ress_check);

                    if($arr_ress_check['cell_asteroid_ress']<1000)
                    {
                        // altes "löschen" //
                        dbquery("UPDATE ".$db_table['space_cells']." SET cell_asteroid_ress='0', cell_asteroid='0', cell_type='0' WHERE cell_id='$cell_id'");

                        // neues erstellen //
                        $new_ress = mt_rand($conf['asteroid_ress']['p1'],$conf['asteroid_ress']['p2']);

                            // hat es noch leere felder?
                        $res_search_place=dbquery("SELECT cell_id FROM ".$db_table['space_cells']." WHERE cell_type='0' ");
                        $arr_search_place = mysql_fetch_array($res_search_place);
                        // wenn ja...
                        if (mysql_num_rows($res_search_place)>0)
                        {

                            $res_rand=dbquery("SELECT cell_id FROM ".$db_table['space_cells']." WHERE cell_type='0' ");

                            $rand_num = mysql_num_rows($res_rand);
                            $rand = mt_rand(0,$rand_num);

                            //Zufälligs leeres feld im universum für neues Asteroidenfeld
                            for ($x=0;$x<$rand;$x++)
                            {
                                $arr_rand = mysql_fetch_array($res_rand);
                            }
                            // neues erstellen
                            dbquery("UPDATE ".$db_table['space_cells']." SET cell_asteroid_ress='$new_ress', cell_asteroid='1', cell_type='1' WHERE cell_id='".$arr_rand['cell_id']."'");

                        }
                    }

                    // Flotte zurückschicken
                    $duration = $arr['fleet_landtime'] - $arr['fleet_launchtime'];
                    $launchtime=$arr['fleet_landtime'];
                    $landtime=$launchtime+$duration;
                    dbquery("UPDATE ".$db_table['fleet']." SET fleet_cell_from='".$arr['fleet_cell_to']."',fleet_cell_to='".$arr['fleet_cell_from']."',fleet_planet_from='".$arr['fleet_planet_to']."',fleet_planet_to='".$arr['fleet_planet_from']."',fleet_action='yr',fleet_launchtime=$launchtime,fleet_landtime=$landtime,fleet_res_metal='$metal',fleet_res_crystal='$crystal',fleet_res_plastic='$plastic' WHERE fleet_id='".$arr['fleet_id']."';");

                    $msg = "Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat [b]ein Asteroidenfeld[/b]\num [b]".date("d.m.Y H:i",$arr['fleet_landtime'])."[/b]\n erreicht und Rohstoffe gesammelt.\n";
                    $msgres = "\n[b]ROHSTOFFE:[/b]\n\n".RES_METAL.": ".nf($metal)."\n".RES_CRYSTAL.": ".nf($crystal)."\n".RES_PLASTIC.": ".nf($plastic)."\n";
                    send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Asteroiden gesammelt",$msg.$msgres);
                    add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] hat [b]ein Asteroidenfeld[/b] um [b]".date("d.m.Y H:i",$arr['fleet_landtime'])."[/b]\n erreicht und Rohstoffe gesammelt.".$msgres,time());
                }

                //20% Chance das die flotte zerstört wird
                else
                {
                $msg="Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\n wurde bei einem Asteroidenfeld abgeschossen.";
                send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Flotte abgeschossen",$msg);
                dbquery("DELETE FROM ".$db_table['fleet_ships']." WHERE fs_fleet_id='".$arr['fleet_id']."'");
                dbquery("DELETE FROM ".$db_table['fleet']." WHERE fleet_id='".$arr['fleet_id']."'");
                    add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] wurde bei einem Asteroidenfeld abgeschossen.",time());
                }
            }
      		// Asteroiden feld nicht mehr vorhanden
			else
			{
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
                    fleet_action='yr',
                    fleet_launchtime=$launchtime,
                    fleet_landtime=$landtime
                WHERE
                	fleet_id='".$arr['fleet_id']."';");

                // Nachricht & Log speichern
                $msg="Die Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\n fand kein Asteroidenfeld mehr vor.\n";
                send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Asteroidenfeld aufgelöst",$msg);
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
			$res_exist = dbquery("SELECT cell_nebula FROM ".$db_table['space_cells']." WHERE cell_id='$cell_id'");
			$arr_exist = mysql_fetch_array($res_exist);
			// wenn ja, sammle ress
			if($arr_exist['cell_nebula']=='1')
			{
			  $capa=0;
		    $rfrar=mysql_fetch_row(dbquery("SELECT SUM(s.ship_capacity*fs.fs_ship_cnt) AS capa FROM ".$db_table['fleet_ships']." AS fs,".$db_table['ships']." AS s,".$db_table['fleet']." AS f WHERE fs.fs_ship_id=s.ship_id AND s.ship_nebula='1' AND fs.fs_fleet_id=f.fleet_id AND f.fleet_id=".$arr['fleet_id']." GROUP BY f.fleet_id;"));
   			$capa=$rfrar[0];
   			$capa=round($capa);

   			//80% Chance das das sammeln klappt
   			$goornot=mt_rand(1,100);
   			if ($goornot>20)
   			{
   			  $res_check=dbquery("SELECT cell_nebula_ress FROM ".$db_table['space_cells']." WHERE cell_id='$cell_id'");
   			  $arr_check = mysql_fetch_array($res_check);

   			  $max_ress = $arr_check['cell_nebula_ress'];

   			  //$nebula = mt_rand(1000,$capa);
   			  //$metal=round(min($nebula,$max_ress));

   			  $nebula = mt_rand(1000,$capa);
   			  $crystal=round(min($nebula,$max_ress));

   			  //$nebula = mt_rand(1000,$capa);
   			  //$plastic=round(min($nebula,$max_ress));

   			  $ress_total = $crystal;

   			  dbquery("UPDATE ".$db_table['space_cells']." SET cell_nebula_ress=cell_nebula_ress-'$ress_total' WHERE cell_id='$cell_id'");


   				//
   			  //Wenn nebula feld keine ress mehr hat -> löschen und neues erstellen
   			  //
   			  $res_ress_check =dbquery("SELECT cell_nebula_ress FROM ".$db_table['space_cells']." WHERE cell_id='$cell_id'");
   			  $arr_ress_check = mysql_fetch_array($res_ress_check);

   			  if($arr_ress_check['cell_nebula_ress']<1000)
   			  {
   			      // altes "löschen" //
   			      dbquery("UPDATE ".$db_table['space_cells']." SET cell_nebula_ress='0', cell_nebula='0', cell_type='0' WHERE cell_id='$cell_id'");

   			      // neues erstellen //
   			      $new_ress = mt_rand($conf['nebula_ress']['p1'],$conf['nebula_ress']['p2']);

							// hat es noch leere felder?
   			      $res_search_place=dbquery("SELECT cell_id FROM ".$db_table['space_cells']." WHERE cell_type='0' ");
   			      $arr_search_place = mysql_fetch_array($res_search_place);
   			      // wenn ja...
   			      if (mysql_num_rows($res_search_place)>0)
   			      {

   			          $res_rand=dbquery("SELECT cell_id FROM ".$db_table['space_cells']." WHERE cell_type='0' ");

   			          $rand_num = mysql_num_rows($res_rand);
   			          $rand = mt_rand(0,$rand_num);

   			          //Zufälligs leeres feld im universum für neues nebulaenfeld
   			          for ($x=0;$x<$rand;$x++)
   			          {
   			              $arr_rand = mysql_fetch_array($res_rand);
   			          }
   			      	// neues erstellen
   			          dbquery("UPDATE ".$db_table['space_cells']." SET cell_nebula_ress='$new_ress', cell_nebula='1', cell_type='1' WHERE cell_id='".$arr_rand['cell_id']."'");
   			      }
   			  }

   			  // Flotte zurückschicken
   			  $duration = $arr['fleet_landtime'] - $arr['fleet_launchtime'];
   			  $launchtime=$arr['fleet_landtime'];
   			  $landtime=$launchtime+$duration;
   			  dbquery("UPDATE ".$db_table['fleet']." SET fleet_cell_from='".$arr['fleet_cell_to']."',fleet_cell_to='".$arr['fleet_cell_from']."',fleet_planet_from='".$arr['fleet_planet_to']."',fleet_planet_to='".$arr['fleet_planet_from']."',fleet_action='nr',fleet_launchtime=$launchtime,fleet_landtime=$landtime,fleet_res_crystal='$crystal' WHERE fleet_id='".$arr['fleet_id']."';");

   			  $msg = "Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\nhat [b]ein Intergalaktisches Nebelfeld [/b]\num [b]".date("d.m.Y H:i",$arr['fleet_landtime'])."[/b]\n erkundet und dabei Rohstoffe gesammelt.\n";
   			  $msgres = "\n[b]ROHSTOFFE:[/b]\n\n".RES_CRYSTAL.": ".nf($crystal)."\n";
   			  send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Nebelfeld erkunden",$msg.$msgres);
					add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] at [b]ein Intergalaktisches Nebelfeld [/b] um [b]".date("d.m.Y H:i",$arr['fleet_landtime'])."[/b]\n erkundet und dabei Rohstoffe gesammelt.\n".$msgres,time());
   			}

   			//20% Chance das die flotte zerstört wird
   			else
   			{
 			    $msg="Eine Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\n hatte bei ihrer Erkundung eines Intergalaktischen Nebelfeldes eine starke magnetische Störung, welche zu einem Systemausfall führte.\nZu der Flotte ist jeglicher Kontakt abgebrochen.";
 			    send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Flotte verschollen",$msg);
 			    dbquery("DELETE FROM ".$db_table['fleet_ships']." WHERE fs_fleet_id='".$arr['fleet_id']."'");
 			    dbquery("DELETE FROM ".$db_table['fleet']." WHERE fleet_id='".$arr['fleet_id']."'");
					add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] wurde bei einem Intergalaktisches Nebelfeld zerst&ouml;rt.",time());
   			}
			}

      // nebula feld nicht mehr vorhanden
			else
			{
     		// Flotte zurückschicken
     		$duration = $arr['fleet_landtime'] - $arr['fleet_launchtime'];
     		$launchtime=$arr['fleet_landtime'];
     		$landtime=$launchtime+$duration;
     		dbquery("UPDATE ".$db_table['fleet']." SET
     			fleet_cell_from='".$arr['fleet_cell_to']."',
     			fleet_cell_to='".$arr['fleet_cell_from']."',
     			fleet_planet_from='".$arr['fleet_planet_to']."',
     			fleet_planet_to='".$arr['fleet_planet_from']."',
     			fleet_action='nr',
     			fleet_launchtime=$launchtime,
     			fleet_landtime=$landtime
     		WHERE fleet_id='".$arr['fleet_id']."';");
     		$msg="Die Flotte vom Planeten \n[b]".coords_format2($arr['fleet_planet_from'])."[/b]\n konnte kein Intergalaktisches Nebelfeld orten.\n";
     		send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Nebelfeld verschwunden",$msg);
				add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten [b]".coords_format2($arr['fleet_planet_from'])."[/b] konnte kein Intergalaktisches Nebelfeld orten.",time());
			}
		}


        /******************************************/
        /* Tf erstellen kommt beim Zielplanet an  */
        /*                                        */
        /******************************************/
		elseif ($arr['fleet_action']=="zo")
		{
			$rres=dbquery("
			SELECT 
                ships.ship_id,
                ships.ship_costs_metal,
                ships.ship_costs_crystal,
                ships.ship_costs_plastic,
                fleet_ships.fs_ship_cnt 
			FROM 
                ".$db_table['ships'].",
                ".$db_table['fleet_ships']." 
			WHERE 
				fleet_ships.fs_fleet_id=".$arr['fleet_id']." 
				AND ships.ship_id=fleet_ships.fs_ship_id");
			while ($rrow=mysql_fetch_array($rres)) {
				$cnt=ceil($rrow['fs_ship_cnt']*0.4);
				$tf_metal+=$cnt*$rrow['ship_costs_metal'];
				$tf_crystal+=$cnt*$rrow['ship_costs_crystal'];
				$tf_plastic+=$cnt*$rrow['ship_costs_plastic'];
				dbquery("DELETE FROM ".$db_table['fleet_ships']." WHERE fs_fleet_id=".$arr['fleet_id']." AND fs_ship_id=".$rrow['ship_id']."");
			}
			dbquery("UPDATE ".$db_table['planets']." SET planet_wf_metal=planet_wf_metal+".$tf_metal.",planet_wf_crystal=planet_wf_crystal+".$tf_crystal.",planet_wf_plastic=planet_wf_plastic+".$tf_plastic." WHERE planet_id=".$arr['fleet_planet_to']."");
			dbquery("DELETE FROM ".$db_table['fleet']." WHERE fleet_id=".$arr['fleet_id']."");
			$coords_target = coords_format2($arr['fleet_planet_to']);
			$coords_from = coords_format2($arr['fleet_planet_from']);
			$msg="Eine Flotte vom Planeten ".$coords_from." hat auf dem Planeten ".$coords_target." ein Trümmerfeld erstellt.";
			send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Trümmerfeld erstellt",$msg);
			add_log(13,"Eine Flotte des Spielers [B]".get_user_nick($arr['fleet_user_id'])."[/B] vom Planeten ".$coords_from." hat auf dem Planeten ".$coords_target." ein Trümmerfeld erstellt.",time());
		}


        /*************************************************/
        /* Inavasionsflotte kommt beim Zielplaneten an   */
        /*                                               */
        /*************************************************/
        elseif ($arr['fleet_action']=="io")
        {
            $res_check=dbquery("SELECT planet_user_id FROM ".$db_table['planets']." WHERE planet_id=".$arr['fleet_planet_to']."");
            $arr_check=mysql_fetch_array($res_check);

            // Kontrolliert bei einer Invasion, ob der Planet nicht schon demjenigengehört gehört

            //gehört bereits dem user, dann flotte stationieren
            if($arr_check['planet_user_id']==$arr['fleet_user_id'])
            {
                // Schiffe in Zielplanet-Schiffsliste eintragen
                $fsres = dbquery("
                SELECT
                    fs.fs_ship_cnt,
                    fs.fs_ship_id,
                    s.ship_colonialize,
                    s.ship_pilots,
                    s.special_ship
                FROM
                    ".$db_table['fleet_ships']." AS fs,
                    ".$db_table['ships']." AS s
                WHERE
                    fs.fs_ship_id=s.ship_id
                    AND fs.fs_fleet_id='".$arr['fleet_id']."';");

                $total_pilots=0;
                while ($fsarr = mysql_fetch_array($fsres))
                {
                    $slres = dbquery("
                    SELECT
                        shiplist_id
                    FROM
                        ".$db_table['shiplist']."
                    WHERE
                        shiplist_ship_id='".$fsarr['fs_ship_id']."'
                        AND shiplist_planet_id='".$arr['fleet_planet_to']."'
                        AND shiplist_user_id='".$arr['fleet_user_id']."';");
                    $slarr = mysql_fetch_array($slres);

                    $ship_cnt = $fsarr['fs_ship_cnt'];

                    if($arr['fleet_action']=="po" || $arr['fleet_action']=="ko")
                    {
                        $special_slres = dbquery("
                        SELECT
                            shiplist_id,
                            shiplist_special_ship_level,
                            shiplist_special_ship_exp,
                            shiplist_special_ship_bonus_weapon,
                            shiplist_special_ship_bonus_structure,
                            shiplist_special_ship_bonus_shield,
                            shiplist_special_ship_bonus_heal,
                            shiplist_special_ship_bonus_capacity,
                            shiplist_special_ship_bonus_speed,
                            shiplist_special_ship_bonus_pilots,
                            shiplist_special_ship_bonus_tarn
                        FROM
                            ".$db_table['shiplist']."
                        WHERE
                            shiplist_ship_id='".$fsarr['fs_ship_id']."'
                            AND shiplist_planet_id='".$arr['fleet_planet_from']."'
                            AND shiplist_user_id='".$arr['fleet_user_id']."';");
                        $special_slarr = mysql_fetch_array($special_slres);

                        if (mysql_num_rows($slres)>0)
                        {
                            dbquery("
                            UPDATE
                                ".$db_table['shiplist']."
                            SET
                                shiplist_count=shiplist_count+".$ship_cnt.",
                                shiplist_special_ship=".$fsarr['special_ship'].",
                                shiplist_special_ship_level=".$special_slarr['shiplist_special_ship_level'].",
                                shiplist_special_ship_exp=".$special_slarr['shiplist_special_ship_exp'].",
                                shiplist_special_ship_bonus_weapon=".$special_slarr['shiplist_special_ship_bonus_weapon'].",
                                shiplist_special_ship_bonus_structure=".$special_slarr['shiplist_special_ship_bonus_structure'].",
                                shiplist_special_ship_bonus_shield=".$special_slarr['shiplist_special_ship_bonus_shield'].",
                                shiplist_special_ship_bonus_heal=".$special_slarr['shiplist_special_ship_bonus_heal'].",
                                shiplist_special_ship_bonus_capacity=".$special_slarr['shiplist_special_ship_bonus_capacity'].",
                                shiplist_special_ship_bonus_speed=".$special_slarr['shiplist_special_ship_bonus_speed'].",
                                shiplist_special_ship_bonus_pilots=".$special_slarr['shiplist_special_ship_bonus_pilots'].",
                                shiplist_special_ship_bonus_tarn=".$special_slarr['shiplist_special_ship_bonus_tarn']."
                            WHERE
                                shiplist_id=".$slarr['shiplist_id'].";");


                        }
                        else
                        {
                            dbquery("
                            INSERT INTO
                            ".$db_table['shiplist']."
                                (shiplist_user_id,
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
                                shiplist_special_ship_bonus_tarn)
                            VALUES
                                ('".$arr['fleet_user_id']."',
                                '".$fsarr['fs_ship_id']."',
                                '".$arr['fleet_planet_to']."',
                                '".$ship_cnt."',
                                '".$fsarr['special_ship']."',
                                '".$special_slarr['shiplist_special_ship_level']."',
                                '".$special_slarr['shiplist_special_ship_exp']."',
                                '".$special_slarr['shiplist_special_ship_bonus_weapon']."',
                                '".$special_slarr['shiplist_special_ship_bonus_structure']."',
                                '".$special_slarr['shiplist_special_ship_bonus_shield']."',
                                '".$special_slarr['shiplist_special_ship_bonus_heal']."',
                                '".$special_slarr['shiplist_special_ship_bonus_capacity']."',
                                '".$special_slarr['shiplist_special_ship_bonus_speed']."',
                                '".$special_slarr['shiplist_special_ship_bonus_pilots']."',
                                '".$special_slarr['shiplist_special_ship_bonus_tarn']."');");


                        }
                    }
                    else
                    {
                        if (mysql_num_rows($slres)>0)
                        {
                            dbquery("
                            UPDATE
                                ".$db_table['shiplist']."
                            SET
                                shiplist_count=shiplist_count+".$ship_cnt.",
                                shiplist_special_ship=".$fsarr['special_ship']."
                            WHERE
                                shiplist_id=".$slarr['shiplist_id'].";");

                        }
                        else
                        {
                            dbquery("
                            INSERT INTO
                            ".$db_table['shiplist']."
                                (shiplist_user_id,
                                shiplist_ship_id,
                                shiplist_planet_id,
                                shiplist_count)
                            VALUES
                                ('".$arr['fleet_user_id']."',
                                '".$fsarr['fs_ship_id']."',
                                '".$arr['fleet_planet_to']."',
                                '".$ship_cnt."');");

                        }
                    }
                }

                $people=$arr['fleet_pilots']+$arr['fleet_res_people'];
                dbquery("UPDATE ".$db_table['planets']." SET planet_people=planet_people+".$people." WHERE planet_id=".$arr['fleet_planet_to'].";");

                // Waren entladen
                dbquery("UPDATE ".$db_table['planets']." SET planet_res_metal=planet_res_metal+".$arr['fleet_res_metal'].",planet_res_crystal=planet_res_crystal+".$arr['fleet_res_crystal'].",planet_res_plastic=planet_res_plastic+".$arr['fleet_res_plastic'].",planet_res_fuel=planet_res_fuel+".$arr['fleet_res_fuel'].",planet_res_food=planet_res_food+".$arr['fleet_res_food']."  WHERE planet_id=".$arr['fleet_planet_to'].";");

                $msgres= "\n\n[b]WAREN[/b]\n\n[b]".RES_METAL.":[/b] ".nf($arr['fleet_res_metal'])."\n[b]".RES_CRYSTAL.":[/b] ".nf($arr['fleet_res_crystal'])."\n[b]".RES_PLASTIC.":[/b] ".nf($arr['fleet_res_plastic'])."\n[b]".RES_FUEL.":[/b] ".nf($arr['fleet_res_fuel'])."\n[b]".RES_FOOD.":[/b] ".nf($arr['fleet_res_food'])."\n[b]Bewohner:[/b] ".nf($arr['fleet_res_people'])."\n";


                // Flotte-Schiffe-Verknüpfungen löschen
                dbquery("DELETE FROM ".$db_table['fleet_ships']." WHERE fs_fleet_id='".$arr['fleet_id']."';");

                // Flotte aufheben
                dbquery("DELETE FROM ".$db_table['fleet']." WHERE fleet_id='".$arr['fleet_id']."';");


                $msg = "Die Flotte hat folgendes Ziel erreicht:\n[b]Planet:[/b] ".coords_format2($arr['fleet_planet_to'])."\n[b]Zeit:[/b] ".date("d.m.Y H:i",$arr['fleet_landtime'])."\n[b]Bericht:[/b] Die Flotte ist auf dem Planeten gelandet!";
                $msg.= $msgres;
                send_msg($arr['fleet_user_id'],SHIP_MISC_MSG_CAT_ID,"Flotte angekommen",$msg);

                $showinfo=0;
            }
            //gehört nicht dem user, dann fight
            else
            {
                include("update_fleet_fight.php");
            }
        }

        /********************************************************************/
        /* Spionageflotte oder Angriffsflotte kommt beim Zielplaneten an    */
        /* (benötigt battle() Funktion welche zuoberst includet wird)       */
        /********************************************************************/
        elseif ($arr['fleet_action']=="ao" || $arr['fleet_action']=="so" || $arr['fleet_action']=="bo" || $arr['fleet_action']=="xo" || $arr['fleet_action']=="lo" || $arr['fleet_action']=="vo" || $arr['fleet_action']=="ho" || $arr['fleet_action']=="do")
        {
            include("update_fleet_fight.php");
        }

		// Update-Flag löschen, Update-Counter erhöhen
		dbquery("UPDATE ".$db_table['fleet']." SET fleet_updating=0,fleet_update_counter=fleet_update_counter+1 WHERE fleet_id=".$arr['fleet_id'].";");
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
}
?>
