<?PHP

	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	Dateiname: haven_launch.php
	// 	Topic: Raumschiffhafen - Start
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 09.06.2006
	// 	Kommentar:
	//
	$_SESSION['haven']['status']=Null;
	echo "<h2>Flotte starten</h2>";

	// Formatiert Zahlen
	$_POST['fleet_res_metal'] = nf_back($_POST['fleet_res_metal']);
	$_POST['fleet_res_crystal'] = nf_back($_POST['fleet_res_crystal']);
	$_POST['fleet_res_plastic'] = nf_back($_POST['fleet_res_plastic']);
	$_POST['fleet_res_fuel'] = nf_back($_POST['fleet_res_fuel']);
	$_POST['fleet_res_food'] = nf_back($_POST['fleet_res_food']);
	$_POST['fleet_res_people'] = nf_back($_POST['fleet_res_people']);


	if ($_SESSION['haven']['wormhole']>0 && count($_SESSION['haven']['target'])>0)
	{
		$wormhole=true;
		$tvar="target2";
		$fvar="fleet2";
	}
	else
	{
		$wormhole=false;
		$tvar="target";
		$fvar="fleet";
	}

	// Ziel wählen
	if ($_SESSION['haven'][$tvar]['p']>0) // Planet
	{
		$pres = dbquery("
		SELECT
			*
		FROM
            ".$db_table['planets']." AS p,
            ".$db_table['space_cells']." AS c,
            ".$db_table['planet_types']." AS t
		WHERE
            t.type_id=p.planet_type_id
            AND p.planet_solsys_id=c.cell_id
            AND c.cell_sx='".$_SESSION['haven'][$tvar]['sx']."'
            AND c.cell_sy='".$_SESSION['haven'][$tvar]['sy']."'
            AND c.cell_cx='".$_SESSION['haven'][$tvar]['cx']."'
            AND c.cell_cy='".$_SESSION['haven'][$tvar]['cy']."'
            AND p.planet_solsys_pos='".$_SESSION['haven'][$tvar]['p']."';");
	}
	else // Asteroidenfeld, Nebel, Wurmloch
	{
		$pres=dbquery("
		SELECT
			*
		FROM
			".$db_table['space_cells']."
		WHERE
            cell_sx='".$_SESSION['haven'][$tvar]['sx']."'
            AND cell_sy='".$_SESSION['haven'][$tvar]['sy']."'
            AND cell_cx='".$_SESSION['haven'][$tvar]['cx']."'
            AND cell_cy='".$_SESSION['haven'][$tvar]['cy']."'
            AND (cell_asteroid=1 OR cell_nebula=1 OR cell_wormhole_id>0)");
	}

	// Zeiten definieren
	$launchtime = time();
	$landtime = $launchtime + $_SESSION['haven']['fleettotal']['flight_duration'];
	if ($launchtime < $landtime)
	{
		// Ziel prüfen
		if (mysql_num_rows($pres)>0 && $_POST['fleet_action']!="")
		{
			$parr = mysql_fetch_array($pres);
			
			//Bewohner überprüfen
			$people_total_arr=mysql_fetch_array(dbquery("SELECT planet_people FROM ".$db_table['planets']." WHERE id=".$c->id." AND planet_user_id=".$s['user']['id'].""));
			$people_working_arr=mysql_fetch_row(dbquery("SELECT SUM(buildlist_people_working) FROM ".$db_table['buildlist']." WHERE buildlist_entity_id='".$c->id."';"));
			$people_free = $people_total_arr['planet_people']-$people_working_arr[0]-$_SESSION['haven']['fleet']['total_pilots'];
			if ($people_free>=0)
			{
				// Treibstoff  Planetenkonto abziehen und prüfen
				if ($c->res->fuel>=$_SESSION['haven']['fleettotal']['flight_costs'])
				{
					// Nahrung vom Planetenkonto abziehen und prüfen
					if ($c->res->food>=$_SESSION['haven']['fleettotal']['flight_food'])
					{
	
						// Überprüfung der Schiffsanzahl
						$scnt=0;
						foreach ($_SESSION['haven']['ships'] as $id=>$cnt)
						{
							$res = dbquery("SELECT shiplist_count FROM ".$db_table['shiplist']." WHERE shiplist_ship_id=$id AND shiplist_user_id=".$s['user']['id']." AND shiplist_entity_id='".$c->id."';");
							$arr = mysql_fetch_array($res);
							$_SESSION['haven']['ships'][$id] = min(abs($_SESSION['haven']['ships'][$id]),$arr['shiplist_count']);
							$scnt+=$_SESSION['haven']['ships'][$id];
						}
			  		if ($scnt>0)
			  		{
			  				// Treibstoff & Nahrung abziehen
			  				$c->changeRes(0,0,0,-$_SESSION['haven']['fleettotal']['flight_costs'],-$_SESSION['haven']['fleettotal']['flight_food']);
	
							// Piloten abziehen
							dbquery("UPDATE ".$db_table['planets']." SET planet_people=planet_people-".$_SESSION['haven']['fleet']['total_pilots']." WHERE id='".$c->id."';");
	
							//Rechnet die Kapazität von Gassaugern und Asteroidensammler
	                        $capacity_nebula=0;
	                        $capacity_asteroid=0;
	                        foreach ($_SESSION['haven']['ships'] as $id=>$cnt)
	                        {
	                            $res_capa_nebula=dbquery("
	                            SELECT
	                                ships.ship_capacity
	                            FROM
	                                ".$db_table['shiplist'].",
	                                ".$db_table['ships']."
	                            WHERE
	                                shiplist.shiplist_ship_id=ships.ship_id
	                                AND shiplist_ship_id=$id
	                                AND shiplist.shiplist_user_id=".$s['user']['id']."                              
	                                AND ships.ship_nebula='1';");
	                            $arr_capa_nebula = mysql_fetch_array($res_capa_nebula);
	                            if (mysql_num_rows($res_capa_nebula)>0)
	                            {
	                            	$capacity_nebula+=$arr_capa_nebula['ship_capacity'] * $_SESSION['haven']['ships'][$id];
	                            }
	                            
	                            $res_capa_asteroid=dbquery("
	                            SELECT
	                                ships.ship_capacity
	                            FROM
	                                ".$db_table['shiplist'].",
	                                ".$db_table['ships']."
	                            WHERE
	                                shiplist.shiplist_ship_id=ships.ship_id
	                                AND shiplist_ship_id=$id
	                                AND shiplist.shiplist_user_id=".$s['user']['id']."                              
	                                AND ships.ship_asteroid='1';");
	                            $arr_capa_asteroid = mysql_fetch_array($res_capa_asteroid);
	                            if (mysql_num_rows($res_capa_asteroid)>0)
	                            {
	                            	$capacity_asteroid+=$arr_capa_asteroid['ship_capacity'] * $_SESSION['haven']['ships'][$id];
	                            }
	                        }
	
							// Warenmengen in Abhängikeit vom Laderaum und den vorhandenen Waren berechnen
							$capa = $_SESSION['haven']['fleet']['res_capacity'];
							// Falls abholen, nicht mit Planetenwaren vergleichen
							if ($_POST['fleet_action']=='fo')
							{
								$res_r[0]=$_POST['fleet_res_metal'];
								$res_r[1]=$_POST['fleet_res_crystal'];
								$res_r[2]=$_POST['fleet_res_plastic'];
								$res_r[3]=$_POST['fleet_res_fuel'];
								$res_r[4]=$_POST['fleet_res_food'];
								$fleet_people = $_POST['fleet_res_people'];
							}
							else
							{
								$res_r[0]=min($_POST['fleet_res_metal'],$c->res->metal);
								$res_r[1]=min($_POST['fleet_res_crystal'],$c->res->crystal);
								$res_r[2]=min($_POST['fleet_res_plastic'],$c->res->plastic);
								$res_r[3]=min($_POST['fleet_res_fuel'],$c->res->fuel);
								$res_r[4]=min($_POST['fleet_res_food'],$c->res->food);
								$fleet_people = min($_POST['fleet_res_people'],$_SESSION['haven']['fleet']['people_capacity'],$people_free);
							}
	
							$fleet_people_capacity = max(0,$_SESSION['haven']['fleet']['people_capacity']);
	
							for ($rcnt=0;$rcnt<5;$rcnt++)
							{
								if(array_sum($res_r)<=0)
									$r[$rcnt]+=0;
								elseif($capa<=array_sum($res_r))
									$r[$rcnt]+=floor($res_r[$rcnt]*$capa/array_sum($res_r));
								else
									$r[$rcnt]+=floor($res_r[$rcnt]);
							}
							if ($r[0]=="") $r[0]=0;
							if ($r[1]=="") $r[1]=0;
							if ($r[2]=="") $r[2]=0;
							if ($r[3]=="") $r[3]=0;
							if ($r[4]=="") $r[4]=0;
							if ($fleet_people=="") $fleet_people=0;
							$rest_capa=$capa-array_sum($r);
							$capacity_nebula=floor($capacity_nebula*$rest_capa/$_SESSION['haven']['fleet']['total_capacity']);
							$capacity_asteroid=floor($capacity_asteroid*$rest_capa/$_SESSION['haven']['fleet']['total_capacity']);
	
	
			  			// Gefakte Schiffe bei Fakeangriff auswählen
							if ($_POST['fleet_action']=="eo")
							{
								foreach ($_SESSION['haven']['ships'] as $id=>$cnt)
								{
									if (mysql_num_rows(dbquery("SELECT ship_id FROM ".$db_table['ships']." WHERE ship_id='$id' AND ship_fake=1"))!=0)
									{
										//erstellt eine zufalls fleet aus den schiffen die der user bereits gebaut hat
										$typ_count = mt_rand(1,10);
										$fres=dbquery("
										SELECT
											SUM(shiplist.shiplist_count) as cnt,
											ships.ship_id
										FROM
											".$db_table['shiplist'].",
											".$db_table['ships']."
										WHERE
											shiplist.shiplist_ship_id=ships.ship_id
											AND shiplist.shiplist_user_id=".$s['user']['id']."
											AND ships.ship_fakeable=1
										GROUP BY
											ships.ship_id
										ORDER BY
											RAND()
										LIMIT $typ_count;");
										if(mysql_num_rows($fres)>0)
										{
	                                        while ($farr=mysql_fetch_array($fres))
	                                        {
	                                            $count=$farr['cnt']*($cnt/20);
	                                            $fakeships[$farr['ship_id']]=mt_rand(1,$count);
	
	                                            if($fakeships[$farr['ship_id']]<1)
	                                                $fakeships[$farr['ship_id']]=1;
	                                        }
	                                    }
	                                    //sind keine schiffe vorhanden, so werden die fake schiffe angezeigt
	                                    else
	                                    {
	                                    	$fakeships[$id]=$cnt;
	                                    }
									}
								}
	
			  					$is_fake=1;
							}
							else
								$is_fake=0;
	
	
							/*
							Lamborghini:
							DEFAULT ACTION?
							Die flotte soll nicht starten wenn keine aktion angegeben ist!
							
							// Flottendatensatz speichern
							if ($_POST['fleet_action']!="")
								$fleet_action=$_POST['fleet_action'];
							else
								$fleet_action=DEFAULT_ACTION;
							*/
								
							$fleet_action=$_POST['fleet_action'];
							
							$sql = "
							INSERT INTO ".$db_table['fleet']." (
	                            fleet_user_id,
	                            fleet_cell_from,
	                            fleet_cell_to,
	                            fleet_planet_from,
	                            fleet_planet_to,
	                            fleet_launchtime,
	                            fleet_landtime,";
	                            if ($wormhole)
	                                $sql.="fleet_whtime,";
	                            $sql.="fleet_action,
	                            fleet_pilots,
	                            fleet_res_metal,
	                            fleet_res_crystal,
	                            fleet_res_plastic,
	                            fleet_res_fuel,
	                            fleet_res_food,
	                            fleet_res_people,
	                            fleet_capacity_people,
	                            fleet_fake,
	                            fleet_capacity,
	                            fleet_capacity_nebula,
	                            fleet_capacity_asteroid
							) 
							VALUES 
							(
	                            '".$s['user']['id']."'
	                            ,'".$c->solsys_id."',
	                            '".$parr['cell_id']."',
	                            '".$c->id."',
	                            '".$parr['id']."',
	                            '".$launchtime."',
	                            '".$landtime."',";
	                            if ($wormhole)
	                                $sql.=($launchtime+$_SESSION['haven']['fleet']['flight_duration']).",";
	                            $sql.="'".$fleet_action."',
	                            '".$_SESSION['haven']['fleet']['total_pilots']."',
	                            '".$r[0]."',
	                            '".$r[1]."',
	                            '".$r[2]."',
	                            '".$r[3]."',
	                            '".$r[4]."',
	                            '".$fleet_people."',
	                            '".$fleet_people_capacity."',
	                            '".$is_fake."',
	                            '".$rest_capa."',
	                            '".$capacity_nebula."',
	                            '".$capacity_asteroid."'
	                        );";
	
	
							dbquery($sql);
							$fleet_id = mysql_insert_id();
	
							// Flotte-Schiffe Verknüpfungen speichern & Schiffe werden vom Planeten subtrahiert
							if ($is_fake==1)
							{
								foreach ($fakeships as $id=>$cnt)
								{
									dbquery("
									INSERT INTO
									".$db_table['fleet_ships']."
	                                    (fs_fleet_id,
	                                    fs_ship_id,
	                                    fs_ship_cnt,
	                                    fs_ship_faked)
									VALUES
	                                    ('$fleet_id',
	                                    '$id',
	                                    '$cnt',
	                                    '1');");
								}
							}
							foreach ($_SESSION['haven']['ships'] as $id=>$cnt)
							{
								
	                            $special_ship_res=dbquery("
	                            SELECT
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
	                            FROM
	                                ".$db_table['shiplist']."
	                            WHERE
	                            	shiplist_entity_id='".$c->id."'
	                                AND shiplist_ship_id='".$id."'
	                                AND shiplist_user_id='".$s['user']['id']."'                              
	                                AND shiplist_special_ship='1';");
	                            $special_ship_arr = mysql_fetch_array($special_ship_res);
	                            if (mysql_num_rows($special_ship_res)>0)	
	                            {	
	                                dbquery("
	                                INSERT INTO 
	                                ".$db_table['fleet_ships']." 
	                                    (fs_fleet_id,
	                                    fs_ship_id,
	                                    fs_ship_cnt,
	                                    fs_special_ship,
	                                    fs_special_ship_level,
	                                    fs_special_ship_exp,
	                                    fs_special_ship_bonus_weapon,
	                                    fs_special_ship_bonus_structure,
	                                    fs_special_ship_bonus_shield,
	                                    fs_special_ship_bonus_heal,
	                                    fs_special_ship_bonus_capacity,
	                                    fs_special_ship_bonus_speed,
	                                    fs_special_ship_bonus_pilots,
	                                    fs_special_ship_bonus_tarn,
	                                    fs_special_ship_bonus_antrax,
	                                    fs_special_ship_bonus_forsteal,
	                                    fs_special_ship_bonus_build_destroy,
	                                    fs_special_ship_bonus_antrax_food,
	                                    fs_special_ship_bonus_deactivade) 
	                                VALUES 
	                                    ('".$fleet_id."',
	                                    '".$id."',
	                                    '".$cnt."',
	                                    '".$special_ship_arr['shiplist_special_ship']."',
	                                    '".$special_ship_arr['shiplist_special_ship_level']."',
	                                    '".$special_ship_arr['shiplist_special_ship_exp']."',
	                                    '".$special_ship_arr['shiplist_special_ship_bonus_weapon']."',
	                                    '".$special_ship_arr['shiplist_special_ship_bonus_structure']."',
	                                    '".$special_ship_arr['shiplist_special_ship_bonus_shield']."',
	                                    '".$special_ship_arr['shiplist_special_ship_bonus_heal']."',
	                                    '".$special_ship_arr['shiplist_special_ship_bonus_capacity']."',
	                                    '".$special_ship_arr['shiplist_special_ship_bonus_speed']."',
	                                    '".$special_ship_arr['shiplist_special_ship_bonus_pilots']."',
	                                    '".$special_ship_arr['shiplist_special_ship_bonus_tarn']."',
	                                    '".$special_ship_arr['shiplist_special_ship_bonus_antrax']."',
	                                    '".$special_ship_arr['shiplist_special_ship_bonus_forsteal']."',
	                                    '".$special_ship_arr['shiplist_special_ship_bonus_build_destroy']."',
	                                    '".$special_ship_arr['shiplist_special_ship_bonus_antrax_food']."',
	                                    '".$special_ship_arr['shiplist_special_ship_bonus_deactivade']."');");
	                            }
	                            else
	                            {
	                                dbquery("
	                                INSERT INTO 
	                                ".$db_table['fleet_ships']." 
	                                    (fs_fleet_id,
	                                    fs_ship_id,
	                                    fs_ship_cnt) 
	                                VALUES 
	                                    ('".$fleet_id."',
	                                    '".$id."',
	                                    '".$cnt."');");                            
	                            }
	                                
								dbquery("UPDATE ".$db_table['shiplist']." SET shiplist_count=shiplist_count-$cnt WHERE shiplist_ship_id=$id AND shiplist_user_id=".$s['user']['id']." AND shiplist_entity_id='".$c->id."';");
							}
	
							// Rohstoffe vom Planetenkonto abziehen (falls nicht abholen gewählt ist)
							if ($_POST['fleet_action']!='fo')
							{
								dbquery("
								UPDATE 
									".$db_table['planets']." 
								SET
		                            planet_res_metal=planet_res_metal-".$r[0].",
		                            planet_res_crystal=planet_res_crystal-".$r[1].",
		                            planet_res_plastic=planet_res_plastic-".$r[2].",
		                            planet_res_fuel=planet_res_fuel-".$r[3].",
		                            planet_res_food=planet_res_food-".$r[4].",
		                            planet_people=planet_people-".$fleet_people."
								WHERE 
									id='".$c->id."';");
							}
	
							echo "Die Schiffe starteten erfolgreich und sind nun unterwegs!<br/><br/>";
							tableStart("Flotteninfo");
							echo "<tr><td class=\"tbltitle\" width=\"40%\">Startzeit:</td><td class=\"tbldata\" width=\"60%\" align=\"left\">".date("d.m.Y H:i:s",$launchtime)."</td></tr>";
							echo "<tr><td class=\"tbltitle\" width=\"40%\">Ende des Fluges:</td><td class=\"tbldata\" width=\"60%\" align=\"left\">".date("d.m.Y H:i:s",$landtime)."</td></tr>";
							echo "<tr><td class=\"tbltitle\" width=\"40%\">Dauer:</td><td class=\"tbldata\" width=\"60%\" align=\"left\">".tf($_SESSION['haven']['fleettotal']['flight_duration'])."</td></tr>";
							tableEnd();
							echo "<input type=\"button\" name=\"new\" onclick=\"document.location='?page=$page'\" value=\"Eine neue Flotte losschicken\" title=\"Eine neue Flotte losschicken\" />&nbsp;";
							echo "<input type=\"button\" name=\"new\" onClick=\"document.location='?page=fleetinfo&amp;id=$fleet_id'\" value=\"Diese Flotte beobachten\" title=\"Flotte beobachten\" />";
							$_SESSION['haven']=Null;
						}
						else
						{
							$_SESSION['haven']=Null;
							echo "<b>Fehler:</b> Die gew&auml;hlten Schiffe sind nicht mehr vorhanden!<br/><br/>";
							echo "<input type=\"button\" name=\"back\" onClick=\"document.location='?page=$page';\" value=\"Neue Flottenauswahl\" />";
						}
					}
					else
					{
						$_SESSION['haven']=Null;
						echo "<b>Fehler:</b> Es ist nicht mehr genug Nahrung vorhanden!<br/><br/>";
						echo "<input type=\"button\" name=\"back\" onClick=\"document.location='?page=$page';\" value=\"Neue Flottenauswahl\" />";
					}
				}
				else
				{
					echo "<b>Fehler:</b> Es ist nicht mehr genug Treibstoff vorhanden (".$_SESSION['haven']['fleettotal']['flight_costs']." ben&ouml;tigt)!<br/><br/>";
					$_SESSION['haven']=Null;
					echo "<input type=\"button\" name=\"back\" onClick=\"document.location='?page=$page';\" value=\"Neue Flottenauswahl\" />";
				}
			}
			else
			{
				$_SESSION['haven']=Null;
				echo "<b>Fehler:</b> Es sind sind nicht mehr genug Piloten vorhanden!<br/><br/>";
				echo "<input type=\"button\" name=\"back\" onClick=\"document.location='?page=$page';\" value=\"Neue Flottenauswahl\" />";
			}
		}
		else
		{
			$_SESSION['haven']=Null;
			echo "<b>Fehler:</b> Ziel existiert nicht oder es wurde keine gültige Aktion gewählt!<br/><br/>";
			echo "<input type=\"button\" name=\"back\" onClick=\"document.location='?page=$page';\" value=\"Neue Flottenauswahl\" />";
		}
	}
	else
	{
		$_SESSION['haven']=Null;
		echo "<b>Fehler:</b> Die Flugzeit kann nicht negativ sein!<br/><br/>";
		echo "<input type=\"button\" name=\"back\" onClick=\"document.location='?page=$page';\" value=\"Neue Flottenauswahl\" />";
	}
?>
