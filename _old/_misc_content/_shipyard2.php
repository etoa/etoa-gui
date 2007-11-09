<?PHP

	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: shipyard.php													//
	// Topic: Schiffswerft-Modul	 									//
	// Version: 0.1																	//
	// Letzte Änderung: 10.05.2006 Lamborghini								//
	//////////////////////////////////////////////////

	// DEFINITIONEN //

	define(TBL_SPACING,$conf['general_table_offset']['v']);
	define(TBL_PADDING,$conf['general_table_offset']['p1']);
	define(MIN_BUILD_TIME,20);
	define(SHIPYARD_ID,9);
	define("GEN_TECH_ID",23);				// ID der Gentechnologie

	//Definition für "Info" Link
	define(ITEMS_TBL,"ships");
	define(REQ_TBL,"ship_requirements");
	define(REQ_ITEM_FLD,"req_ship_id");
	define(ITEM_ID_FLD,"ship_id");
	define(ITEM_NAME_FLD,"ship_name");
	define(RACE_TO_ADD," AND (ship_race_id=0 OR ship_race_id=".$_SESSION[ROUNDID]['user']['race_id'].")");
	define(ITEM_SHOW_FLD,"ship_show");
	define(ITEM_ORDER_FLD,"ship_order");
	define(NO_ITEMS_MSG,"In dieser Kategorie gibt es keine Schiffe!");
	define(HELP_URL,"?page=help&site=shipyard");


	// BEGIN SKRIPT //

	echo "<h1>Raumschiffswerft des Planeten ".$c->name."</h1>";

	//
	// Prüfen ob dieses Gebäude deaktiviert wurde
	//
	if ($dt = check_building_deactivated($_SESSION[ROUNDID]['user']['id'],$c->id,SHIPYARD_ID))
 	{
		infobox_start("Gebäude nicht bereit");
		echo "Dieser Raumschiffhafen ist bis ".date("d.m.Y H:i",$dt)." deaktiviert.";
		infobox_end();
	}
	else
	{
		echo "<form action=\"?page=$page\" method=\"post\">";

		//Gentech level laden
		$tlres = dbquery("
		SELECT
			techlist_current_level
		FROM
			".$db_table['techlist']."
		WHERE
            techlist_user_id='".$_SESSION[ROUNDID]['user']['id']."'
            AND techlist_tech_id='".GEN_TECH_ID."';");
		if(mysql_num_rows($tlres)>0)
		{
			$tlarr = mysql_fetch_array($tlres);
			define("GEN_TECH_LEVEL",$tlarr['techlist_current_level']);
    }
    else
  		define("GEN_TECH_LEVEL",0);


			//
			// Schiffe in Auftrag geben
			//

			if($_POST['submit']!="" && checker_verify())
			{

				//Schiffsdaten in array speichern
				$slres = dbquery("
				SELECT
					*
				FROM
					".$db_table['shiplist']."
				WHERE
					shiplist_planet_id='".$c->id."'
					AND shiplist_user_id='".$_SESSION[ROUNDID]['user']['id']."';");
				while ($slarr = mysql_fetch_array($slres))
				{
					$shiplist[$slarr['shiplist_ship_id']]=$slarr;
				}


				//Zeit der Schiffe
				$tres = dbquery("
				SELECT
					shiplist_build_end_time
				FROM
					".$db_table['shiplist']."
				WHERE
                    shiplist_user_id=".$_SESSION[ROUNDID]['user']['id']."
                    AND shiplist_planet_id='".$c->id."'
                    AND shiplist_build_end_time!=0
				ORDER BY
					shiplist_build_end_time DESC
				LIMIT 1;");

				if (mysql_num_rows($tres)>0)
				{
					$tarr = mysql_fetch_array($tres);
					$start_time = $tarr['shiplist_build_end_time'];
				}
				else
					$start_time = time();


				$bc = array();
				$cancel_allowed=0;

				//
				//Bauaufträge speichern
				//
				foreach ($_POST['build_count'] as $ship_id=>$build_cnt)
				{
					$build_cnt=abs(intval($build_cnt));


					if ($build_cnt>0)
					{

						if ($end_time>0) $start_time=$end_time;
						$sres = dbquery("SELECT * FROM ".$db_table['ships']." WHERE ship_id=$ship_id;");
						$sarr = mysql_fetch_array($sres);

						//Anzahl überprüfen, ob diese die maximalzahl übersteigt, gegebenenfalls ändern
                        if($build_cnt>$sarr['ship_max_count'] && $sarr['ship_max_count']!=0)
                            $build_cnt=$sarr['ship_max_count'];

						if ($sarr['ship_costs_metal']>0) $bf['metal']=$c->res->metal/$sarr['ship_costs_metal']; else $bc['metal']=0;
						if ($sarr['ship_costs_crystal']>0) $bf['crystal']=$c->res->crystal/$sarr['ship_costs_crystal']; else $bc['crystal']=0;
						if ($sarr['ship_costs_plastic']>0) $bf['plastic']=$c->res->plastic/$sarr['ship_costs_plastic']; else $bc['plastic']=0;
						if ($sarr['ship_costs_fuel']>0) $bf['fuel']=$c->res->fuel/$sarr['ship_costs_fuel']; else $bc['fuel']=0;
						if ($_POST['additional_food_costs']>0) $bf['food']=$c->res->food/($_POST['additional_food_costs']); else $bc['food']=0;


                        // Schiffswerft level laden
                        $werft_res = dbquery("
                        SELECT
                        	buildlist_current_level
                        FROM
                        	".$db_table['buildlist']."
                        WHERE
                        	buildlist_building_id='9'
                        	AND buildlist_planet_id=".$c->id."
                        	AND buildlist_user_id='".$_SESSION[ROUNDID]['user']['id']."'");
                        $werft_arr = mysql_fetch_array($werft_res);

                        //level zählen welches der schiffswerft über dem angegeben level ist und faktor berechnen
                        $need_bonus_level = $werft_arr['buildlist_current_level'] - $conf['build_time_boni_schiffswerft']['p1'];
                        if($need_bonus_level <= 0)
                        {
                            $time_boni_factor=1;
                        }
                        else
                        {
                            $time_boni_factor=1-($need_bonus_level*($conf['build_time_boni_schiffswerft']['v']/100));
                        }

						if ($build_cnt>floor(min($bf)))
						{
							$build_cnt=floor(min($bf));
						}
						if ($build_cnt>0)
						{
							$cancel_allowed=1;
							//Errechne Kosten pro auftrag schiffe
							$bc['metal']=$sarr['ship_costs_metal']*$build_cnt;
							$bc['crystal']=$sarr['ship_costs_crystal']*$build_cnt;
							$bc['plastic']=$sarr['ship_costs_plastic']*$build_cnt;
							$bc['fuel']=$sarr['ship_costs_fuel']*$build_cnt;
							$bc['food']=($_POST['additional_food_costs'])*$build_cnt;

							$anzahl=$build_cnt;

							$fres = dbquery("SELECT buildlist_people_working FROM ".$db_table['buildlist'].",".$db_table['buildings']." WHERE buildlist_building_id=building_id AND buildlist_planet_id=".$c->id." AND building_id=".SHIP_BUILDING_ID.";");
							if (mysql_num_rows($fres)>0)
							{
								$farr=mysql_fetch_array($fres);
								if ($farr['buildlist_people_working']>0)
								{
										$people_working = $farr['buildlist_people_working'];
								}
							}
							else
								$people_working=0;

						$btime = ($sarr['ship_costs_metal'] + $sarr['ship_costs_crystal'] + $sarr['ship_costs_plastic'] + $sarr['ship_costs_fuel'] + $sarr['ship_costs_food'] - $tonns_worked) / 12 * GLOBAL_TIME * SHIP_BUILD_TIME * $time_boni_factor;

						//Rechnet zeit wenn arbeiter eingeteilt sind
						if (mysql_num_rows($fres)>0)
						{
							$farr=mysql_fetch_array($fres);
							$btime_min=$btime*(0.1-(GEN_TECH_LEVEL/100));
							if ($btime_min<MIN_BUILD_TIME) $btime_min=MIN_BUILD_TIME;
							$btime=$btime-$people_working*3;
							if ($btime<$btime_min) $btime=$btime_min;
						}
						$obj_time=$btime;
						$duration=$anzahl*$obj_time;


							$end_time = $start_time+$duration;
							if ($shiplist[$ship_id]!=Null)
							{
								if ($shiplist[$ship_id]['shiplist_build_count']>0)
								{
									$res = dbquery("
									SELECT
										sl1.shiplist_id
									FROM
										".$db_table['shiplist']." AS sl1,
										".$db_table['shiplist']." AS sl2
									WHERE
										sl1.shiplist_planet_id='".$c->id."'
										AND sl2.shiplist_id=".$shiplist[$ship_id]['shiplist_id']."
										AND sl1.shiplist_build_start_time>=sl2.shiplist_build_end_time;");
									if (mysql_num_rows($res)>0)
									{
										while($arr = mysql_fetch_array($res))
										{
                    	//fügt allen anderen aufträgen die nach diesem autrag kommen, die momentane dauer dieses auftrages an
                    	//Wenn man dies nicht tut überschneiden sich die zeiten wenn man so baut:
                    	//1. Ship 1 in auftrag geben
                    	//2. Ship 2 in auftrag geben
                    	//3. Ship 1 noch einmal in auftrag geben
                    	dbquery("
                    	UPDATE
                    	    ".$db_table['shiplist']."
                    	SET
                    	    shiplist_build_start_time=shiplist_build_start_time+$duration,
                    	    shiplist_build_end_time=shiplist_build_end_time+$duration
                    	WHERE
                    	    shiplist_id=".$arr['shiplist_id'].";");
										}
									}

									dbquery("
									UPDATE
										".$db_table['shiplist']."
									SET
										shiplist_build_count=shiplist_build_count+$build_cnt,
										shiplist_build_end_time=shiplist_build_end_time+$duration,
										shiplist_build_object_time=$obj_time
									WHERE
										shiplist_id=".$shiplist[$ship_id]['shiplist_id'].";");

									$shiplist_id = $shiplist[$ship_id]['shiplist_id'];
								}
								else
								{
									dbquery("
									UPDATE
										".$db_table['shiplist']."
									SET
										shiplist_build_count=$build_cnt,
										shiplist_build_start_time=$start_time,
										shiplist_build_end_time=$end_time,
										shiplist_build_object_time=$obj_time
									WHERE
										shiplist_id=".$shiplist[$ship_id]['shiplist_id'].";");

									$shiplist_id = $shiplist[$ship_id]['shiplist_id'];
								}
							}
							else
							{
								if($sarr['special_ship']==1)
								{
                                   dbquery("
                                    INSERT INTO
                                    ".$db_table['shiplist']."
                                        (shiplist_user_id,
                                        shiplist_ship_id,
                                        shiplist_planet_id,
                                        shiplist_build_count,
                                        shiplist_build_start_time,
                                        shiplist_build_end_time,
                                        shiplist_build_object_time,
                                        shiplist_special_ship)
                                    VALUES
                                        ('".$_SESSION[ROUNDID]['user']['id']."',
                                        '$ship_id',
                                        '".$c->id."',
                                        '$build_cnt',
                                        '$start_time',
                                        '$end_time',
                                        '$obj_time',
                                        '1');");
                                    $shiplist_id = mysql_insert_id();
								}
								else
								{
                                    dbquery("
                                    INSERT INTO
                                    ".$db_table['shiplist']."
                                        (shiplist_user_id,
                                        shiplist_ship_id,
                                        shiplist_planet_id,
                                        shiplist_build_count,
                                        shiplist_build_start_time,
                                        shiplist_build_end_time,
                                        shiplist_build_object_time)
                                    VALUES
                                        ('".$_SESSION[ROUNDID]['user']['id']."',
                                        '$ship_id',
                                        '".$c->id."',
                                        '$build_cnt',
                                        '$start_time',
                                        '$end_time',
                                        '$obj_time');");
                                    $shiplist_id = mysql_insert_id();
                                }
							}

							//Rohstoffe vom Planeten abziehen
							dbquery("
							UPDATE
								".$db_table['planets']."
							SET
                                planet_res_metal=planet_res_metal-".$bc['metal'].",
                                planet_res_crystal=planet_res_crystal-".$bc['crystal'].",
                                planet_res_plastic=planet_res_plastic-".$bc['plastic'].",
                                planet_res_fuel=planet_res_fuel-".$bc['fuel'].",
                                planet_res_food=planet_res_food-".$bc['food']."
							WHERE
								planet_id='".$c->id."';");

							echo "<input type=\"hidden\" name=\"id[]\" value=\"$shiplist_id\">";
							echo "<input type=\"hidden\" name=\"obj_cnt[]\" value=\"$build_cnt\">";
							echo "<input type=\"hidden\" name=\"obj_time[]\" value=\"$obj_time\">";
							echo "<input type=\"hidden\" name=\"res0[]\" value=\"".$bc['metal']."\">";
							echo "<input type=\"hidden\" name=\"res1[]\" value=\"".$bc['crystal']."\">";
							echo "<input type=\"hidden\" name=\"res2[]\" value=\"".$bc['plastic']."\">";
							echo "<input type=\"hidden\" name=\"res3[]\" value=\"".$bc['fuel']."\">";
							echo "<input type=\"hidden\" name=\"res4[]\" value=\"".$bc['food']."\">";
						}
					}
				}

				echo "<input type=\"hidden\" name=\"time\" value=\"".time()."\">";
			}

			if($_POST['cancel']!="" && checker_verify())
			{
				if (time()-$_POST['time']<SHIPDEFBUILD_CANCEL_TIME)
				{
					//Schiffe abbrechen (normal)
					foreach ($_POST['id'] as $arrid=>$shiplist_id)
					{
						$build_time = $_POST['obj_cnt'][$arrid]*$_POST['obj_time'][$arrid];

                        $res = dbquery("
                        SELECT
                            sl1.shiplist_id
                        FROM
                            ".$db_table['shiplist']." AS sl1,
                            ".$db_table['shiplist']." AS sl2
                        WHERE
                            sl1.shiplist_planet_id='".$c->id."'
                            AND sl2.shiplist_id='".$shiplist_id."'
                            AND sl1.shiplist_build_start_time>=sl2.shiplist_build_end_time;");
                        if (mysql_num_rows($res)>0)
                        {
                            while($arr = mysql_fetch_array($res))
                            {
                                //entzieht allen anderen auträgen die nach diesem abgebrochenen auftrag kommen die zeit wieder ab, welche sie duch den bau dazubekommen haben
                                //Wenn man dies nicht tut so entstehen leerlauf zeiten
                                //1. Ship 1 in auftrag geben
                                //2. Ship 2 in auftrag geben
                                //3. Ship 1 noch einmal in auftrag (bei Ship 2 wird die zeit erweitert)
                                //4. Ship 1 von schritt 3 abbrechen -> leerlaufzeit von Ship 2
                                dbquery("
                                UPDATE
                                    ".$db_table['shiplist']."
                                SET
                                    shiplist_build_start_time=shiplist_build_start_time-$build_time,
                                    shiplist_build_end_time=shiplist_build_end_time-$build_time
                                WHERE
                                    shiplist_id=".$arr['shiplist_id'].";");
                            }

                        }

						dbquery("
						UPDATE
							".$db_table['shiplist']."
						SET
                            shiplist_build_count=shiplist_build_count-".$_POST['obj_cnt'][$arrid].",
                            shiplist_build_end_time=shiplist_build_end_time-$build_time
						WHERE
							shiplist_id=$shiplist_id;");

						dbquery("
						UPDATE
							".$db_table['planets']."
						SET
                            planet_res_metal=planet_res_metal+".$_POST['res0'][$arrid].",
                            planet_res_crystal=planet_res_crystal+".$_POST['res1'][$arrid].",
                            planet_res_plastic=planet_res_plastic+".$_POST['res2'][$arrid].",
                            planet_res_fuel=planet_res_fuel+".$_POST['res3'][$arrid].",
                            planet_res_food=planet_res_food+".$_POST['res4'][$arrid]."
						WHERE
							planet_id='".$c->id."';");
					}
					
					update_shiplist($c->id,$_SESSION[ROUNDID]['user']['id']);
				}
			}


		$c->resBox();
		checker_init();

		if ($cancel_allowed==1)
		{
			echo "<p align=\"center\">Bauauftrag gestartet!<br/><br/><input type=\"submit\" class=\"button\" name=\"cancel\" value=\"Bauauftrag abbrechen (innerhalb ".SHIPDEFBUILD_CANCEL_TIME." s drücken)\" /></form></p>";
		}


		//
		// Liste der Bauaufträge anzeigen
		//

		$slres = dbquery("
		SELECT
            s.ship_name,
            sl.shiplist_build_count,
            sl.shiplist_build_start_time,
            sl.shiplist_build_end_time,
            sl.shiplist_build_object_time
		FROM
            ".$db_table['shiplist']." AS sl,
            ".$db_table['ships']." AS s
		WHERE
            sl.shiplist_ship_id=s.ship_id
            AND sl.shiplist_planet_id='".$c->id."'
            AND sl.shiplist_user_id='".$_SESSION[ROUNDID]['user']['id']."'
            AND sl.shiplist_build_start_time>0
            AND sl.shiplist_build_end_time>0
		ORDER BY
			shiplist_build_start_time ASC;");
		if (mysql_num_rows($slres)>0)
		{
			echo "<table width=\"300\" cellspacing=\"".TBL_SPACING."\" cellpadding=\"".TBL_PADDING."\" align=\"center\" class=\"tbl\">";
			echo "<tr><td class=\"tbltitle\" width=\"180\">Bauaufträge</td><td class=\"tbltitle\" width=\"40\">Anzahl</td><td class=\"tbltitle\" width=\"80\">Zeit</td></tr>";
			$dur_total = 0;
			$cnt = 0;
			$time = time();
			while ($slarr = mysql_fetch_array($slres))
			{
				if ($cnt==0)
				{

					$obj_t_remaining = ((($slarr['shiplist_build_end_time']-$time) / $slarr['shiplist_build_object_time'])-floor(($slarr['shiplist_build_end_time']-$time) / $slarr['shiplist_build_object_time']))*$slarr['shiplist_build_object_time'];
					if ($obj_t_remaining==0) $obj_t_remaining = $slarr['shiplist_build_object_time'];
					echo "<tr><td class=\"tbldata\">".$slarr['ship_name']."</td><td class=\"tbldata\" id=\"objcount\">".$slarr['shiplist_build_count']."</td><td class=\"tbldata\" id=\"objtime\">-</td></tr>";
					$obj_time = $slarr['shiplist_build_object_time'];
					$dur_total+=$slarr['shiplist_build_end_time']-$time;
				}
				else
				{
					echo "<tr><td class=\"tbldata\">".$slarr['ship_name']."</td><td class=\"tbldata\">".$slarr['shiplist_build_count']."</td><td class=\"tbldata\">&nbsp;</td></tr>";
					$dur_total+=$slarr['shiplist_build_end_time']-$slarr['shiplist_build_start_time'];
				}
				$cnt++;
			}
			echo "<tr><td class=\"tbldata\" colspan=\"3\" height=\"5\"><img src=\"images/blank.gif\"/></td></tr>";
			echo "<tr><td class=\"tbldata\" colspan=\"2\">Total verbleibende Zeit</td><td class=\"tbldata\" id=\"shiptime\">-</td></tr>";
			echo "</table><br>";
			?>

			<script type="text/javascript">

				function setCountdown()
				{
					var ts;
					ts1 = <?PHP if($dur_total) echo $dur_total+time(); else echo 0;?> - (<?PHP echo time();?> + cnt);
					if (objcnt<1)
						ts2 = <?PHP echo $obj_t_remaining+time();?> - (<?PHP echo time();?> + cnt);
					else
					{
						ts2 = (objcnt*<?PHP echo $obj_time;?>)+ <?PHP echo +$obj_t_remaining+time(); ?> - (<?PHP echo time();?> + cnt);
					}


					if (ts1>=0)
					{
						t = Math.floor(ts1 / 3600 / 24);
						h = Math.floor(ts1 / 3600);
						m = Math.floor((ts1-(h*3600))/60);
						s = Math.floor((ts1-(h*3600)-(m*60)));
						nv1 = h+"h "+m+"m "+s+"s";
					}
					else
					{
						nv1 = "-";
						//document.location='?page=<?PHP echo $page;?>';
					}

					if (ts2>=0)
					{
						t = Math.floor(ts2 / 3600 / 24);
						h = Math.floor(ts2 / 3600);
						m = Math.floor((ts2-(h*3600))/60);
						s = Math.floor((ts2-(h*3600)-(m*60)));
						nv2 = h+"h "+m+"m "+s+"s";
					}
					else if (ts1>0)
					{
						oc = parseInt(document.getElementById('objcount').firstChild.nodeValue);
						if (oc>1)
						{
							ts2 = 1;
							objcnt = objcnt + 1;
							document.getElementById('objcount').firstChild.nodeValue=oc-1;
						}
						else
						{
							ts2 = 0;
							nv2 = "-";
							document.getElementById('objcount').firstChild.nodeValue=0;

						}
					}
					else
					{
						nv2 = "-";
						document.getElementById('objcount').firstChild.nodeValue=0;
					}

					document.getElementById('shiptime').firstChild.nodeValue=nv1;
					document.getElementById('objtime').firstChild.nodeValue=nv2;
					cnt = cnt + 1;
					setTimeout("setCountdown()",1000);
				}




				if (document.getElementById('shiptime')!=null)
				{
					objcnt = 0;
					cnt = 0;
					setCountdown();
				}
			</script>
			<?PHP



		}











		//
		// Schiffe auflisten
		//


		// Schiffsdaten aus der Schiffsliste laden
		$slres = dbquery("
		SELECT
			*
		FROM
			".$db_table['shiplist']."
		WHERE
            shiplist_planet_id='".$c->id."'
            AND shiplist_user_id='".$_SESSION[ROUNDID]['user']['id']."';");
		while ($slarr = mysql_fetch_array($slres))
		{
			$shiplist[$slarr['shiplist_ship_id']]=$slarr;
		}

		// Vorausetzungen laden
		$res = dbquery("SELECT * FROM ".$db_table['ship_requirements'].";");
		while ($arr = mysql_fetch_array($res))
		{
			if ($arr['req_req_building_id']>0) $req[$arr['req_ship_id']]['b'][$arr['req_req_building_id']]=$arr['req_req_building_level'];
			if ($arr['req_req_tech_id']>0) $req[$arr['req_ship_id']]['t'][$arr['req_req_tech_id']]=$arr['req_req_tech_level'];
		}


		//Technologien laden
		$res = dbquery("SELECT * FROM ".$db_table['techlist']." WHERE techlist_user_id='".$_SESSION[ROUNDID]['user']['id']."';");
		while ($arr = mysql_fetch_array($res))
		{
			$techlist[$arr['techlist_tech_id']]=$arr['techlist_current_level'];
		}

		//Gebäude laden
		$res = dbquery("SELECT * FROM ".$db_table['buildlist']." WHERE buildlist_planet_id='".$c->id."' AND buildlist_user_id='".$_SESSION[ROUNDID]['user']['id']."';");
		while ($arr = mysql_fetch_array($res))
		{
			$buildlist[$arr['buildlist_building_id']]=$arr['buildlist_current_level'];
		}


		//
		// Auflistung der Schiffe
		//
		$sres = dbquery("
		SELECT
            ship_id,
            ship_name,
            ship_shortcomment,
            ship_costs_metal,
            ship_costs_crystal,
            ship_costs_plastic,
            ship_costs_fuel,
            ship_costs_food,
            ship_show,
            ship_buildable,
            ship_structure,
            ship_shield,
            ship_weapon,
            ship_race_id,
            ship_max_count,
            special_ship
        FROM
        	".$db_table['ships']."
        WHERE
        	ship_buildable=1
        	AND ship_show=1
        	AND (ship_race_id=0 OR ship_race_id=".$_SESSION[ROUNDID]['user']['race_id'].")
        ORDER BY
        	special_ship DESC,
        	ship_name;");
		$cnt = 0;
		if (mysql_num_rows($sres)>0)
		{

			$fres = dbquery("SELECT buildlist_people_working FROM ".$db_table['buildlist'].",".$db_table['buildings']." WHERE buildlist_building_id=building_id AND buildlist_planet_id=".$c->id." AND building_id=".SHIP_BUILDING_ID.";");
			if (mysql_num_rows($fres)>0)
			{
				$farr=mysql_fetch_array($fres);
				if ($farr['buildlist_people_working']>0)
				{
						$people_working = $farr['buildlist_people_working'];
				}
			} else {
				$people_working=0;
			}
			$tabulator=1;
			$abstand=0;
			while ($sarr = mysql_fetch_array($sres))
			{


                $build_ship = 1;

                if (count($req[$sarr['ship_id']]['b'])>0)
                {
                    foreach ($req[$sarr['ship_id']]['b'] as $id=>$level)
                    {
                        if ($buildlist[$id]<$level) $build_ship = 0;
                    }
                }
                if (count($req[$sarr['ship_id']]['t'])>0)
                {
                    foreach ($req[$sarr['ship_id']]['t'] as $id=>$level)
                    {
                        if ($techlist[$id]<$level) $build_ship = 0;
                    }
                }


				if ($build_ship==1)
				{

					// Schiffswerft level laden
					$werft_res = dbquery("SELECT buildlist_current_level FROM ".$db_table['buildlist']." WHERE buildlist_building_id='9' AND buildlist_planet_id=".$c->id." AND buildlist_user_id='".$_SESSION[ROUNDID]['user']['id']."'");
					$werft_arr = mysql_fetch_array($werft_res);

					//level zählen welches der schiffswerft über dem angegeben level ist und faktor berechnen
					$need_bonus_level = $werft_arr['buildlist_current_level'] - $conf['build_time_boni_schiffswerft']['p1'];
					if($need_bonus_level <= 0){
						$time_boni_factor=1;
					}else{
						$time_boni_factor=1-($need_bonus_level*($conf['build_time_boni_schiffswerft']['v']/100));
					}


                    //zählt die anzahl schiffe dieses typs im ganzen account...
                    $ship_count=0;
                    //...auf den planeten
                    $check_res = dbquery("
                    SELECT
                        shiplist_count
                    FROM
                        ".$db_table['shiplist']."
                    WHERE
                        shiplist_ship_id='".$sarr['ship_id']."'
                        AND shiplist_user_id='".$_SESSION[ROUNDID]['user']['id']."';");
                    if (mysql_num_rows($check_res)>0)
                    {
                        while ($check_arr = mysql_fetch_array($check_res))
                        {
                            $ship_count+=$check_arr['shiplist_count'];
                        }

                    }
                    //...in der luft
                    $check_res = dbquery("
                    SELECT
                        fs.fs_ship_cnt
                    FROM
                        ".$db_table['fleet']." AS f,
                        ".$db_table['fleet_ships']." AS fs
                    WHERE
                        fs.fs_ship_id='".$sarr['ship_id']."'
                        AND f.fleet_user_id='".$_SESSION[ROUNDID]['user']['id']."';");
                    if (mysql_num_rows($check_res)>0)
                    {
                        while ($check_arr = mysql_fetch_array($check_res))
                        {
                            $ship_count+=$check_arr['fs_ship_cnt'];
                        }

                    }


					$btime = ($sarr['ship_costs_metal']+$sarr['ship_costs_crystal']+$sarr['ship_costs_plastic']+$sarr['ship_costs_fuel']+$sarr['ship_costs_food']) / 12 * GLOBAL_TIME * SHIP_BUILD_TIME * $time_boni_factor;
					if (mysql_num_rows($fres)>0)
					{
						$farr=mysql_fetch_array($fres);
						$btime_min=$btime*(0.1-(GEN_TECH_LEVEL/100));
						if ($btime_min<MIN_BUILD_TIME) $btime_min=MIN_BUILD_TIME;
						$btime=$btime-$people_working*3;
						if ($btime<$btime_min) $btime=$btime_min;
					}

					$user_res = dbquery("SELECT user_item_show FROM ".$db_table['users']." WHERE user_id='".$_SESSION[ROUNDID]['user']['id']."';");
					$user_arr = mysql_fetch_array($user_res);
					$food_costs = $people_working*12 + $sarr['ship_costs_food'];

					//Ansicht für Spezialschiffe
					if($sarr['special_ship']==1)
					{

						//spezialschiffe können nur auf dem hauptplaneten gebaut werden
						if($c->isMain)
						{
							$abstand_ok=1;

                            //Volle Ansicht der Schiffsliste (spezial)
                            if($user_arr['user_item_show']=='full')
                            {
                                infobox_start("",1);
                                echo "<input type=\"hidden\" name=\"additional_food_costs\" value=\"$food_costs\" />";
                                $s_img = IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$sarr['ship_id']."_middle.".IMAGE_EXT;
                                echo "<tr><td class=\"tbltitle\" colspan=\"5\" height=\"20\">".$sarr['ship_name']."</td></tr>";
                                echo "<tr><td class=\"tbldata\" width=\"120\" height=\"120\" rowspan=\"2\"><img src=\"$s_img\" width=\"120\" height=\"120\" border=\"0\" /></td><td class=\"tbldata\" colspan=\"4\" valign=\"top\">".nl2br(stripslashes($sarr['ship_shortcomment']))."<br><br><b>Vorhanden: </b>".nf($shiplist[$sarr['ship_id']]['shiplist_count']).", <b>Gebaut werden: </b>".nf($shiplist[$sarr['ship_id']]['shiplist_build_count'])."</td></tr>";
                                echo "<tr><td class=\"tbldata\" height=\"30\" colspan=\"2\" width=\"50%\"><b>Bauzeit</b>: ".tf($btime)."</td><td class=\"tbldata\" height=\"30\" width=\"50%\" colspan=\"2\">";

                                if ($ship_count>=$sarr['ship_max_count'] && $sarr['ship_max_count']!=0)
                                {
                                   echo "<i>Maximalanzahl erreicht</i>";
                                }
                                else
                                {
                                    echo "In Aufrag geben: <input type=\"text\" value=\"0\" onKeyPress=\"return nurZahlen(event)\" name=\"build_count[".$sarr['ship_id']."]\" size=\"2\" maxlength=\"5\" tabindex=\"".$tabulator."\"></> Stück";
                                }

                                echo "</td>
                                </tr>";
                                echo "<tr><td class=\"tbltitle\" height=\"20\" width=\"110\">".RES_METAL.":</td><td class=\"tbltitle\" height=\"20\" width=\"97\">".RES_CRYSTAL.":</td><td class=\"tbltitle\" height=\"20\" width=\"98\">".RES_PLASTIC.":</td><td class=\"tbltitle\" height=\"20\" width=\"97\">".RES_FUEL.":</td><td class=\"tbltitle\" height=\"20\" width=\"98\">".RES_FOOD."</td></tr>";
                                echo "<tr><td class=\"tbldata\" height=\"20\" width=\"110\">".nf($sarr['ship_costs_metal'])."</td><td class=\"tbldata\" height=\"20\" width=\"97\">".nf($sarr['ship_costs_crystal'])."</td><td class=\"tbldata\" height=\"20\" width=\"98\">".nf($sarr['ship_costs_plastic'])."</td><td class=\"tbldata\" height=\"20\" width=\"97\">".nf($sarr['ship_costs_fuel'])."</td><td class=\"tbldata\" height=\"20\" width=\"98\">".nf($food_costs)."</td></tr>";
                                infobox_end(1);

                            }else{
                            //Einfache Ansicht der Schiffsliste (spezial)

                                infobox_start("",1);
                                echo "<input type=\"hidden\" name=\"additional_food_costs\" value=\"$food_costs\" />";
                                $s_img = IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$sarr['ship_id']."_small.".IMAGE_EXT;

                                echo "<tr><td class=\"tbldata\"><img src=\"$s_img\" width=\"40\" height=\"40\" border=\"0\" /></td>";
                                echo "<td class=\"tbltitle\" width=\"25%\">".$sarr['ship_name']."</td>";
                                echo "<td class=\"tbldata\" width=\"15%\">".tf($btime)."</td>";
                                echo "<td class=\"tbldata\" width=\"12%\">".nf($sarr['ship_costs_metal'])."</td>";
                                echo "<td class=\"tbldata\" width=\"12%\">".nf($sarr['ship_costs_crystal'])."</td>";
                                echo "<td class=\"tbldata\" width=\"12%\">".nf($sarr['ship_costs_plastic'])."</td>";
                                echo "<td class=\"tbldata\" width=\"12%\">".nf($sarr['ship_costs_fuel'])."</td>";
                                echo "<td class=\"tbldata\" width=\"12%\">".nf($food_costs)."</td>";

                                if ($ship_count>=$sarr['ship_max_count'] && $sarr['ship_max_count']!=0)
                                {
                                    echo "<td class=\"tbldata\"><input type=\"text\" value=\"Max.\" size=\"2\" maxlength=\"5\" readonly tabindex=\"".$tabulator."\"/></td></tr>";
                                }
                                else
                                {
                                    echo "<td class=\"tbldata\"><input type=\"text\" value=\"0\" onKeyPress=\"return nurZahlen(event)\" name=\"build_count[".$sarr['ship_id']."]\" size=\"2\" maxlength=\"5\" tabindex=\"".$tabulator."\" /></td></tr>";
                                }

                                infobox_end(1);

                            }
						}
					}
					//ansicht der normalen schiffe
					else
					{
                        //Volle Ansicht der Schiffsliste (normal)
                        if($user_arr['user_item_show']=='full')
                        {
                        	$abstand++;
                        	if($abstand==1 && $abstand_ok==1)
                            	echo "<br><br><br>";

                            infobox_start("",1);
                            echo "<input type=\"hidden\" name=\"additional_food_costs\" value=\"$food_costs\" />";
                            $s_img = IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$sarr['ship_id']."_middle.".IMAGE_EXT;
                            echo "<tr><td class=\"tbltitle\" colspan=\"5\" height=\"20\">".$sarr['ship_name']."</td></tr>";
                            echo "<tr><td class=\"tbldata\" width=\"120\" height=\"120\" rowspan=\"2\"><a href=\"".HELP_URL."&amp;id=".$sarr[ITEM_ID_FLD]."\" title=\"Info zu diesem Schiff anzeigen\"><img src=\"$s_img\" width=\"120\" height=\"120\" border=\"0\" /></a></td><td class=\"tbldata\" colspan=\"4\" valign=\"top\">".nl2br(stripslashes($sarr['ship_shortcomment']))."<br><br><b>Vorhanden: </b>".nf($shiplist[$sarr['ship_id']]['shiplist_count']).", <b>Gebaut werden: </b>".nf($shiplist[$sarr['ship_id']]['shiplist_build_count'])."</td></tr>";
                            echo "<tr><td class=\"tbldata\" height=\"30\" colspan=\"2\" width=\"50%\"><b>Bauzeit</b>: ".tf($btime)."</td><td class=\"tbldata\" height=\"30\" width=\"50%\" colspan=\"2\">";

                            if ($ship_count>=$sarr['ship_max_count'] && $sarr['ship_max_count']!=0)
                            {
                               echo "<i>Maximalanzahl erreicht</i>";
                            }
                            else
                            {
                                echo "In Aufrag geben: <input type=\"text\" value=\"0\" onKeyPress=\"return nurZahlen(event)\" name=\"build_count[".$sarr['ship_id']."]\" size=\"2\" maxlength=\"5\" tabindex=\"".$tabulator."\"></> Stück";
                            }

                            echo "</td>
                            </tr>";
                            echo "<tr><td class=\"tbltitle\" height=\"20\" width=\"110\">".RES_METAL.":</td><td class=\"tbltitle\" height=\"20\" width=\"97\">".RES_CRYSTAL.":</td><td class=\"tbltitle\" height=\"20\" width=\"98\">".RES_PLASTIC.":</td><td class=\"tbltitle\" height=\"20\" width=\"97\">".RES_FUEL.":</td><td class=\"tbltitle\" height=\"20\" width=\"98\">".RES_FOOD."</td></tr>";
                            echo "<tr><td class=\"tbldata\" height=\"20\" width=\"110\">".nf($sarr['ship_costs_metal'])."</td><td class=\"tbldata\" height=\"20\" width=\"97\">".nf($sarr['ship_costs_crystal'])."</td><td class=\"tbldata\" height=\"20\" width=\"98\">".nf($sarr['ship_costs_plastic'])."</td><td class=\"tbldata\" height=\"20\" width=\"97\">".nf($sarr['ship_costs_fuel'])."</td><td class=\"tbldata\" height=\"20\" width=\"98\">".nf($food_costs)."</td></tr>";
                            infobox_end(1);

                        }else{
                        //Einfache Ansicht der Schiffsliste (normal)
                        	$abstand++;
                        	if($abstand==1 && $abstand_ok==1)
                            	echo "<br><br><br>";

                            infobox_start("",1);
                            echo "<input type=\"hidden\" name=\"additional_food_costs\" value=\"$food_costs\" />";
                            $s_img = IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$sarr['ship_id']."_small.".IMAGE_EXT;

                            echo "<tr><td class=\"tbldata\"><a href=\"".HELP_URL."&amp;id=".$sarr[ITEM_ID_FLD]."\"><img src=\"$s_img\" width=\"40\" height=\"40\" border=\"0\" /></a></td>";
                            echo "<td class=\"tbltitle\" width=\"25%\">".$sarr['ship_name']."</td>";
                            echo "<td class=\"tbldata\" width=\"15%\">".tf($btime)."</td>";
                            echo "<td class=\"tbldata\" width=\"12%\">".nf($sarr['ship_costs_metal'])."</td>";
                            echo "<td class=\"tbldata\" width=\"12%\">".nf($sarr['ship_costs_crystal'])."</td>";
                            echo "<td class=\"tbldata\" width=\"12%\">".nf($sarr['ship_costs_plastic'])."</td>";
                            echo "<td class=\"tbldata\" width=\"12%\">".nf($sarr['ship_costs_fuel'])."</td>";
                            echo "<td class=\"tbldata\" width=\"12%\">".nf($food_costs)."</td>";

                            if ($ship_count>=$sarr['ship_max_count'] && $sarr['ship_max_count']!=0)
                            {
                                echo "<td class=\"tbldata\"><input type=\"text\" value=\"Max.\" size=\"2\" maxlength=\"5\" readonly tabindex=\"".$tabulator."\"/></td></tr>";
                            }
                            else
                            {
                                echo "<td class=\"tbldata\"><input type=\"text\" value=\"0\" onKeyPress=\"return nurZahlen(event)\" name=\"build_count[".$sarr['ship_id']."]\" size=\"2\" maxlength=\"5\" tabindex=\"".$tabulator."\" /></td></tr>";
                            }

                            infobox_end(1);

                        }
					}

					$cnt++;
				}
			$tabulator++;
			}

			if ($cnt>0)
			{
				echo "<table width=\"100%\" cellspacing=\"".TBL_SPACING."\" cellpadding=\"".TBL_PADDING."\" align=\"center\">\n";
				echo "<tr><td colspan=\"5\" height=\"30\" align=\"center\"><input type=\"submit\" class=\"button\" name=\"submit\" value=\"Bauaufträge übernehmen\"/></td></tr>\n";
			}
			else
			{
				echo "<table width=\"100%\" cellspacing=\"".TBL_SPACING."\" cellpadding=\"".TBL_PADDING."\" align=\"center\">\n";
				echo "<tr><td colspan=\"5\" height=\"30\" align=\"center\">Es können noch keine Schiffe gebaut werden! <br>Baue zuerst die benötigten Gebäude und erforsche die erforderlichen Technologien!</td></tr>\n";
			}

		}
		else
		{
			echo "<table width=\"100%\" cellspacing=\"".TBL_SPACING."\" cellpadding=\"".TBL_PADDING."\" align=\"center\">\n";
			echo "<tr><td align=\"center\" colspan=\"3\" class=\"infomsg\">Es gibt noch keine Schiffe!</td></tr>";
		}
		echo "</table></form>";
	}

?>
