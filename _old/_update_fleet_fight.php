<?PHP

	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: update_fleet_fight.php													//
	// Topic: Feindlicher Angriff			 									//
	// Version: 0.1																	//
	// Letzte Änderung: 21.05.2005									//
	//////////////////////////////////////////////////

	// Updates

	include_once("battle.php");

	$return_fleet=1;

	if ($arr['fleet_action']=="ao" || $arr['fleet_action']=="io" || $arr['fleet_action']=="bo" || $arr['fleet_action']=="xo" || $arr['fleet_action']=="lo" || $arr['fleet_action']=="vo" || $arr['fleet_action']=="ho" || $arr['fleet_action']=="do")
	{
		$binfo = battle($arr['fleet_id'],$arr['fleet_planet_to']);
		switch ($binfo[0])
		{
			case 1:	//angreifer hat gewonnen
				$bstat = "Gewonnen";
				$bstat2 = "Verloren";
				break;
			case 2:	//agreifer hat verloren
				$bstat = "Verloren";
				$bstat2 = "Gewonnen";
				break;
			case 3:	//beide flotten haben überlebt
				$bstat = "Unentschieden";
				$bstat2 = "Unentschieden";
				break;
			case 4: //beide flotten sind kaputt
				$bstat = "Unentschieden";
				$bstat2 = "Unentschieden";
				break;
		}

        // Lädt die Bonis der Spezialschiffe und summiert sie
        $special_bonis_res = dbquery("
        SELECT
            s.special_ship_bonus_antrax,
            s.special_ship_bonus_forsteal,
            s.special_ship_bonus_build_destroy,
            s.special_ship_bonus_antrax_food,
            s.special_ship_bonus_deactivade,

            fs.fs_ship_cnt,

            sl.shiplist_special_ship_bonus_antrax,
            sl.shiplist_special_ship_bonus_forsteal,
            sl.shiplist_special_ship_bonus_build_destroy,
            sl.shiplist_special_ship_bonus_antrax_food,
            sl.shiplist_special_ship_bonus_deactivade
        FROM 
			(
				(
					".$db_table['fleet_ships']." AS fs 
				INNER JOIN 
					".$db_table['fleet']." AS f 
				ON fs.fs_fleet_id = f.fleet_id
				) 
			INNER JOIN 
				".$db_table['ships']." AS s 
			ON fs.fs_ship_id = s.ship_id
			) 
		INNER JOIN 
			".$db_table['shiplist']." AS sl 
		ON sl.shiplist_planet_id = f.fleet_planet_from 
		AND sl.shiplist_user_id = f.fleet_user_id 
		AND s.ship_id = sl.shiplist_ship_id
		AND f.fleet_id='".$arr['fleet_id']."' 
		AND s.special_ship='1';");        
        
        $special_ship_bonus_antrax = 0;
        $special_ship_bonus_forsteal = 0;
        $special_ship_bonus_build_destroy = 0;
        $special_ship_bonus_antrax_food = 0;
        $special_ship_bonus_deactivade = 0;

        if (mysql_num_rows($special_bonis_res)>0)
        {
            while ($special_bonis_arr=mysql_fetch_array($special_bonis_res))
            {
            	$special_ship_bonus_antrax+=$special_bonis_arr['special_ship_bonus_antrax'] * $special_bonis_arr['shiplist_special_ship_bonus_antrax'];
            	$special_ship_bonus_forsteal+=$special_bonis_arr['special_ship_bonus_forsteal'] * $special_bonis_arr['shiplist_special_ship_bonus_forsteal'];
            	$special_ship_bonus_build_destroy+=$special_bonis_arr['special_ship_bonus_build_destroy'] * $special_bonis_arr['shiplist_special_ship_bonus_build_destroy'];
            	$special_ship_bonus_antrax_food+=$special_bonis_arr['special_ship_bonus_antrax_food'] * $special_bonis_arr['shiplist_special_ship_bonus_antrax_food'];
            	$special_ship_bonus_deactivade+=$special_bonis_arr['special_ship_bonus_deactivade'] * $special_bonis_arr['shiplist_special_ship_bonus_deactivade'];
            }

         }

		$user_to_id = get_user_id_by_planet($arr['fleet_planet_to']);

		send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Kampfbericht ($bstat)",$binfo[1]);
		send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Kampfbericht ($bstat2)",$binfo[1]);

		add_log(1,$binfo[1],$arr['fleet_landtime']);

		//unentschiedene kämpfe -> flotte zurückschicken
		if($binfo[0]==3 && $arr['fleet_action']=="ao") $action="ar";
		if($binfo[0]==3 && $arr['fleet_action']=="io") $action="ir";
		if($binfo[0]==3 && $arr['fleet_action']=="bo") $action="br";
		if($binfo[0]==3 && $arr['fleet_action']=="xo") $action="xr";
		if($binfo[0]==3 && $arr['fleet_action']=="lo") $action="lr";
		if($binfo[0]==3 && $arr['fleet_action']=="vo") $action="vr";
		if($binfo[0]==3 && $arr['fleet_action']=="ho") $action="hr";
		if($binfo[0]==3 && $arr['fleet_action']=="do") $action="dr";




		//
		// Invasieren
		//

		if ($binfo[0]==1 &&  $arr['fleet_action']=="io")
		{
			$coords_target = coords_format2($arr['fleet_planet_to']);
			$coords_from = coords_format2($arr['fleet_planet_from']);

			//Liest Punkte des 'Opfers' aus
            $points_def_res=dbquery("
            SELECT
                user_points
            FROM
                ".$db_table['users']."
            WHERE
                user_id='".$user_to_id."';");
            $points_def_arr=mysql_fetch_array($points_def_res);

			//Liest Punkte des Angreiffers aus
            $points_att_res=dbquery("
            SELECT
                user_points
            FROM
                ".$db_table['users']."
            WHERE
                user_id='".$arr['fleet_user_id']."';");
            $points_att_arr=mysql_fetch_array($points_att_res);

            //Punkteverhältnis
            $factor=$points_att_arr['user_points'] / $points_def_arr['user_points'];
            $chance=INVADE_POSSIBILITY/$factor;

            //Prüft, ob das Verhältnis die Mindest- bzw. Maximalgrenze nicht unter- oder überschreitet
            if($factor<1 && $chance>INVADE_MAX_POSSIBILITY)
            	$chance = INVADE_MAX_POSSIBILITY;
            if($factor>1 && $chance<INVADE_MIN_POSSIBILITY)
            	$chance = INVADE_MIN_POSSIBILITY;

			$iposs = mt_rand(0,100);
			$iperc = intval(100*$chance);

			//Ist invasion erfolgreich? (Chance ok)
			if ($iposs<=$iperc)
			{
				$max_planet_res = dbquery("
				SELECT 
					planet_user_id 
				FROM 
					".$db_table['planets']." 
				WHERE 
					planet_user_id='".$arr['fleet_user_id']."';");
					
				//Hat der User schon die maximale Anzahl Planeten?
				if(mysql_num_rows($max_planet_res) < $conf['user_max_planets']['v'])
				{
                    //Liest Planet ID und Cell ID vom HP des 'Opfers' aus
                    $mplanet_res = dbquery("
                    SELECT
                        planet_id,
                        planet_solsys_id
                    FROM
                        ".$db_table['planets']."
                    WHERE
                        planet_user_id='".$user_to_id."'
                        AND planet_user_main='1';");
                    $mplanet_arr = mysql_fetch_array($mplanet_res);

                    // Alle Flotten des 'Opfers', welche vom- oder zum Planeten Fliegen zum Hauptplaneten schicken mit der Aktion 'Flug abgebrochen'
                    $iflres = dbquery("
                    SELECT
                        *
                    FROM
                        ".$db_table['fleet']."
                    WHERE
                        fleet_user_id='".$user_to_id."'
                        AND (fleet_planet_to='".$arr['fleet_planet_to']."' OR fleet_planet_from='".$arr['fleet_planet_to']."');");
                    while ($iflarr = mysql_fetch_array($iflres))
                    {
                        $launchtime=time();
                        $landtime=$launchtime+time()-$iflarr['fleet_launchtime'];

                        dbquery("
                        UPDATE
                            ".$db_table['fleet']."
                        SET
                            fleet_cell_from='".$iflarr['fleet_cell_to']."',
                            fleet_cell_to='".$mplanet_arr['planet_solsys_id']."',
                            fleet_planet_from='".$iflarr['fleet_planet_to']."',
                            fleet_planet_to='".$mplanet_arr['planet_id']."',
                            fleet_action='foc',
                            fleet_launchtime='".$launchtime."',
                            fleet_landtime='".$landtime."'
                        WHERE
                            fleet_id='".$iflarr['fleet_id']."';");
                    }


                    // Planet übernehmen
                    invasion_planet($arr['fleet_planet_to'],$arr['fleet_user_id']);

                    //Flotte Stationieren & Waren ausladen
                    $msg_ship_res=fleet_land($arr,1);

					//Gelandete Schiffe und Rohstoffe speichern
					$msg=$msg_ship_res[0].$msg_ship_res[1];

                    // Flotte-Schiffe-Verknüpfungen löschen
                    dbquery("DELETE FROM ".$db_table['fleet_ships']." WHERE fs_fleet_id='".$arr['fleet_id']."';");

                    // Flotte aufheben
                    dbquery("DELETE FROM ".$db_table['fleet']." WHERE fleet_id='".$arr['fleet_id']."';");

                    // Nachrichten senden
                    $text="[b]Planet:[/b] $coords_target\n[b]Besitzer:[/b] ".get_user_nick($user_to_id)."\n\nDieser Planet wurde von einer Flotte, welche vom Planeten $coords_from stammt, übernommen!\n";
                    $text.=$msg;
                    send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Planet erfolgreich invasiert",$text);
                    send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Kolonie wurde invasiert",$text);

                    $return_fleet=0;
                }
                //Der User hat bereits die maximale Anzahl Planeten
                else
                {
					$text="[b]Planet:[/b] $coords_target\n[b]Besitzer:[/b] ".get_user_nick($user_to_id)."\n\nEine Flotte vom Planeten $coords_from versuchte, das Ziel zu übernehmen. Dieser Versuch schlug aber fehl und die Flotte machte sich auf den Rückweg!";
					$text1="[b]Planet:[/b] $coords_target\n[b]Besitzer:[/b] ".get_user_nick($user_to_id)."\n\nEine Flotte vom Planeten $coords_from versuchte, das Ziel zu übernehmen. Dieser Versuch schlug aber fehl und die Flotte machte sich auf den Rückweg! Hinweis: Du hast bereits die maximale Anzahl Planeten erreicht!";

					send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",$text1);
					send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",$text);
                }
			}
			else
			{
				$text="[b]Planet:[/b] $coords_target\n[b]Besitzer:[/b] ".get_user_nick($user_to_id)."\n\nEine Flotte vom Planeten $coords_from versuchte, das Ziel zu übernehmen. Dieser Versuch schlug aber fehl und die Flotte machte sich auf den Rückweg!";

				send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",$text);
				send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Invasionsversuch gescheitert",$text);

			}
			$action="ir";
		}

		//
		//Bombardiern
		//
		elseif ($binfo[0]==1 &&  $arr['fleet_action']=="bo")
		{
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
                AND techlist_tech_id='15'");
			$tarr=mysql_fetch_array($tres);

			//10% + Bonis, dass Bombardierung erfolgreich
			$goornot=mt_rand(0,100);
			if ($goornot<=(10+(SHIP_BOMB_FACTOR*$tarr['techlist_current_level']+$special_ship_bonus_build_destroy*100)))
			{
				//Wählt EIN gebäude aus, welches nicht im bau ist
				$blres=dbquery("
				SELECT 
					* 
				FROM 
					".$db_table['buildlist']." 
				WHERE 
                    buildlist_planet_id='".$arr['fleet_planet_to']."'
                    AND buildlist_current_level>'0' 
                    AND buildlist_build_type='0'
                LIMIT 1;");
                
                //Ist ein Gebäude gefunden worden...
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
                    $check_arr=mysql_fetch_array($check_res);
                    
                    if ($check_arr['cnt']<=0)
                    {
                        $return_fleet=0;
                        dbquery("DELETE FROM ".$db_table['fleet']." WHERE fleet_id='".$arr['fleet_id']."';");
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
			$action="br";

		}

		//
		//Deaktivieren
		//
		elseif ($binfo[0]==1 && $arr['fleet_action']=="do")
		{
			$coords_target = coords_format2($arr['fleet_planet_to']);
			$coords_from = coords_format2($arr['fleet_planet_from']);
			
			//Lädt EMP-Tech level
			$tres=dbquery("
			SELECT 
				techlist_current_level 
			FROM 
				".$db_table['techlist']." 
			WHERE 
                techlist_user_id='".$arr['fleet_user_id']."' 
                AND techlist_tech_id='17'");
            $tarr=mysql_fetch_array($tres);
			
			//10% + Bonis, dass Deaktivierung erfolgreich
			$goornot=mt_rand(0,100);
			if ($goornot<(10+(SHIP_BOMB_FACTOR*$tarr['techlist_current_level']+$special_ship_bonus_deactivade*100)))
			{
				//Generiert Zufallswert, wie viele Stunden das Gebäude deaktiviert wird (min. 1h)
				$percent = mt_rand(1,(10+$tarr['techlist_current_level']));
				$plus=$percent*3600;
				$h=floor($plus/3600);
				if ($tarr['techlist_current_level']==0)
				{
					$tarr['techlist_current_level']=1;
				}

				//Lädt Zufällig Schiffswerft (9) , Waffenfabrik (10) oder Flottenkontrolle (11)
				$bres=dbquery("
				SELECT
					buildlist_deactivated,
					buildlist_building_id
				FROM
					".$db_table['buildlist']."
				WHERE
					buildlist_planet_id='".$arr['fleet_planet_to']."'
					AND (buildlist_building_id='11' OR buildlist_building_id='10' OR buildlist_building_id='9')
				ORDER BY
					RAND()
				LIMIT 1;");
				if (mysql_num_rows($bres)>0)
				{
					$barr=mysql_fetch_array($bres);

	                //Rechnet die Deaktivierungszeit (summiert Zeit)
                    $time=max($arr['fleet_landtime'],$barr['buildlist_deactivated']);
                    $time_to_add=$time+$plus;

                    //Deaktivierzeit Updaten
                    dbquery("
                    UPDATE
                        ".$db_table['buildlist']."
                    SET
                        buildlist_deactivated='".$time_to_add."'
                    WHERE
                        buildlist_planet_id='".$arr['fleet_planet_to']."'
                        AND buildlist_building_id='".$barr['buildlist_building_id']."'");

                    //Lädt Gebäudename
                    $name_res = dbquery("
                    SELECT 
                        building_name 
                    FROM 
                        ".$db_table['buildings']." 
                    WHERE 
                        building_id='".$barr['buildlist_building_id']."';");
                    $name_arr=mysql_fetch_array($name_res);


                    //Zieht 1 Deaktivierungsbomber von der Flotte ab
                    $sres=dbquery("
                    SELECT 
                        ship_id 
                    FROM 
                        ".$db_table['ships']." 
                    WHERE 
                        ship_deactivade='1'");
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
                    $check_arr=mysql_fetch_array($check_res);
                    
                    if ($check_arr['cnt']<=0)
                    {
                        //Flotte löschen
                        $return_fleet=0;
                        dbquery("DELETE FROM ".$db_table['fleet']." WHERE fleet_id='".$arr['fleet_id']."';");
                    }


                    //Nachricht senden
                    $text="Eine Flotte vom Planet ".$coords_from." hat auf dem Planeten ".$coords_target." das Gebäude \"".$name_arr['building_name']."\"  für ".$h."h deaktiviert.";
                    send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Deaktivierung",$text);
                    send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Deaktivierung",$text);

				}
				else
				{
					//Nachricht senden (Es wurden noch keine Gebäude gebaut, welche deaktiviert werden können)
					$text="Eine Flotte vom Planet ".$coords_from." hat erfolglos versucht auf dem Planeten ".$coords_target." ein Gebäude zu deaktivieren.\nHinweis: Der Spieler hat keine Gebäudeeinrichtungen, welche deaktiviert werden können!";
					send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Deaktivierung erfolglos",$text);
					send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Deaktivierung erfolglos",$text);
				}
			}
			else
			{
				//Nachricht senden (Deaktivierung fehlgeschlagen)
				$text="Eine Flotte vom Planet ".$coords_from." hat erfolglos versucht auf dem Planeten ".$coords_target." ein Gebäude zu deaktivieren.";
				send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Deaktivierung erfolglos",$text);
				send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Deaktivierung erfolglos",$text);
			}
			$action="dr";
		}


		//
		//Giftgas
		//
		elseif ($binfo[0]==1 && $arr['fleet_action']=="xo")
		{
			//Lädt Gifttechnologie level
			$tres=dbquery("
			SELECT 
				techlist_current_level 
			FROM 
				".$db_table['techlist']." 
			WHERE 
        techlist_user_id='".$arr['fleet_user_id']."' 
        AND techlist_tech_id='18'");
    	$tarr=mysql_fetch_array($tres);		
		
			//40% + Boni Chance, dass Giftgasangriff erfolgreich
			$goornot=mt_rand(0,100);
			if ($goornot<=(40+$tarr['techlist_current_level']*5+$special_ship_bonus_antrax*100))
			{
				$coords_target = coords_format2($arr['fleet_planet_to']);
				$coords_from = coords_format2($arr['fleet_planet_from']);
				
				//1%-95% der Bevölkerung werden ausgelöscht
				$percent = mt_rand(1,95);
				
				//Lädt Anzahl Bewohner
				$pres=dbquery("
				SELECT 
					planet_people 
				FROM 
					".$db_table['planets']." 
				WHERE 
					planet_id='".$arr['fleet_planet_to']."';");
				$parr=mysql_fetch_array($pres);
				
				//Rechnet Bewohner (Neue Anzahl und Verlust)
				$people=round($parr['planet_people']*$percent/100);
				$rest=round($parr['planet_people']-$people);
				
				//Bewohner werden abgezogen
				dbquery("
				UPDATE 
					".$db_table['planets']." 
				SET 
					planet_people='".$people."' 
				WHERE 
					planet_id='".$arr['fleet_planet_to']."';");
					
				//Nachricht senden
				$text="Eine Flotte vom Planet ".$coords_from." hat einen Giftgasangriff auf den Planeten ".$coords_target." verübt es starben dabei ".nf($rest)." Bewohner";
				send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Giftgasangriff",$text);
				send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Giftgasangriff",$text);
			} 
			else 
			{
				//Nachricht senden (Giftgasangriff fehlgeschlagen)
				$text="Eine Flotte vom Planet ".$coords_from." hat erfolglos einen Giftgasangriff auf den Planeten ".$coords_target." verübt.";
				send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Giftgasangriff erfolglos",$text);
				send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Giftgasangriff erfolglos",$text);
			}
			$action="xr";
		}

		//
		//Antrax
		//
		elseif ($binfo[0]==1 && $arr['fleet_action']=="ho")
		{
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
				
				//Rechnet Schadensfaktor
				$fak = mt_rand(1,(20+$tarr['techlist_curren_level']*3));
				
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
			} 
			else 
			{
				//Nachricht senden (Antraxangriff fehlgeschlagen)
				$text="Eine Flotte vom Planet ".$coords_from." hat erfolglos einen Antraxangriff auf den Planeten ".$coords_target." verübt.";
				send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Antraxangriff erfolglos",$text);
				send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Antraxangriff erfolglos",$text);
			}
			
			$action="hr";
		}

		//
		//Spionageangriff
		//
		elseif ($binfo[0]==1 && $arr['fleet_action']=="lo")
		{

			$coords_target = coords_format2($arr['fleet_planet_to']);
			$coords_from = coords_format2($arr['fleet_planet_from']);

			//Lädt Spiotech level der Kontrahenten
			//Angreiffer
			$spy_res_a=dbquery("
			SELECT 
				techlist_current_level 
			FROM 
				".$db_table['techlist']." 
			WHERE 
        techlist_user_id='".$arr['fleet_user_id']."' 
        AND techlist_tech_id='7';");
    	$spy_arr_a=mysql_fetch_array($spy_res_a);		
            	
			//Verteidiger
			$spy_res_d=dbquery("
			SELECT 
				techlist_current_level 
			FROM 
				".$db_table['techlist']." 
			WHERE 
                techlist_user_id='".$user_to_id."' 
                AND techlist_tech_id='7';");
            $spy_arr_d=mysql_fetch_array($spy_res_d);

			//3% + (Spiotech Angreiffer - Spiotech Verteidiger) + Boni Chance, dass Spioangriff erfolgreich
			$goornot = mt_rand(1,100);
			$chance=3+($spy_arr_a['techlist_current_level']-$spy_arr_d['techlist_current_level']+$special_ship_bonus_forsteal*100);
			if ($goornot<=$chance && $chance>0)
			{
				//Sucht eine zufalls Tech vom gegner aus, welche einen höheren Level als die eigenen techs haben. Es werden nur tech geladen, welche man selber schon einmal geforscht hat und die tech, die man selber grad forscht wird ausgeschlossen!
                $techres = dbquery("
                SELECT
                    t.tech_name,
                    def.techlist_tech_id,
                    def.techlist_current_level,
                    att.techlist_current_level
                FROM
                    	".$db_table['technologies']." AS t
                    INNER JOIN
                    (
                        	".$db_table['techlist']." AS def
                        INNER JOIN
                        	".$db_table['techlist']." AS att
                        ON def.techlist_tech_id = att.techlist_tech_id
                        AND att.techlist_build_type!=1
                        AND def.techlist_user_id='".$user_to_id."'
                        AND att.techlist_user_id=".$arr['fleet_user_id']."
                        AND def.techlist_current_level>att.techlist_current_level
                    )
                    ON t.tech_id = def.techlist_tech_id
                ORDER BY
                	RAND()
                LIMIT 1;");
                $techarr=mysql_fetch_array($techres);

				if(mysql_num_rows($techres)>0)
				{
					//Beendet die eigene Forschung, falls ihr Ausbau über die maximal Stufe rausragen würde
					if($techarr['techlist_current_level']==$techarr['tech_last_level'])
					{
                        dbquery("
                        UPDATE
                            ".$db_table['techlist']."
                        SET
                        	techlist_current_level=".$techarr['techlist_current_level'].",
                            techlist_build_type=0,
                            techlist_build_start_time=0,
                            techlist_build_end_time=0
                        WHERE
                            techlist_user_id=".$arr['fleet_user_id']."
                            AND techlist_tech_id=".$techarr['techlist_tech_id']."");
                    }
                    else
                    {
                        dbquery("
                        UPDATE
                            ".$db_table['techlist']."
                        SET
                            techlist_current_level=".$techarr['techlist_current_level']."
                        WHERE
                            techlist_user_id=".$arr['fleet_user_id']."
                            AND techlist_tech_id=".$techarr['techlist_tech_id']."");
                    }

                    //Zieht 1 Deaktivierungsbomber von der Flotte ab
                    $sres=dbquery("
                    SELECT 
                        ship_id 
                    FROM 
                        ".$db_table['ships']." 
                    WHERE 
                    	ship_forsteal='1'");
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
                    $check_arr=mysql_fetch_array($check_res);
                    
                    if ($check_arr['cnt']<=0)
                    {
                        //Flotte löschen
                        $return_fleet=0;
                        dbquery("DELETE FROM ".$db_table['fleet']." WHERE fleet_id='".$arr['fleet_id']."';");
                    }
                    
					//Nachricht senden
					$text="Eine Flotte vom Planeten ".$coords_from." hat erfolgreich einen Spionageangriff durchgeführt und erfuhr so die Geheimnisse der Forschung ".$techarr['tech_name']." bis zum Level ".$techarr['techlist_current_level']."\n";
					send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Spionageangriff",$text);
					send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Spionageangriff",$text);
					add_log(FLEET_ACTION_LOG_CAT,$text,$arr['fleet_landtime']);                    
                    
				}
				else
				{
					//Nachricht senden (kein Abschauen möglich)
					$text="Eine Flotte vom Planeten ".$coords_from." hat erfolglos einen Spionageangriff durchgeführt.\n Das Ziel hat keine Technologie, welche eine höhere Stufe hat!";
					send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Spionageangriff erfolglos",$text);
					send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Spionageangriff erfolglos",$text);
					add_log(FLEET_ACTION_LOG_CAT,$text,$arr['fleet_landtime']);
				}
			}
			else
			{
				//Nachricht senden (Spioangriff fehlgeschlagen)
				$text="Eine Flotte vom Planeten ".$coords_from." hat erfolglos einen Spionageangriff durchgeführt.";
				send_msg($arr['fleet_user_id'],SHIP_WAR_MSG_CAT_ID,"Spionageangriff erfolglos",$text);
				send_msg($user_to_id,SHIP_WAR_MSG_CAT_ID,"Spionageangriff erfolglos",$text);
				add_log(FLEET_ACTION_LOG_CAT,$text,$arr['fleet_landtime']);
			}

			$action="lr";
		}

	}


	//
	// Flotte zurückschicken
	//
	
	if ($return_fleet==1)
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
            fleet_action='".$action."',
            fleet_launchtime='".$launchtime."',
            fleet_landtime='".$landtime."'
		WHERE
			fleet_id='".$arr['fleet_id']."';");
	}


?>
