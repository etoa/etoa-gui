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
	// 	Dateiname: haven_choose_planet.php
	// 	Topic: Raumschiffhafen - Zielauswahl
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 09.06.2006
	// 	Kommentar:
	//


	if ($_SESSION['haven']['wormhole']>0 && count($_SESSION['haven']['target'])>0)
		$wormhole=true;
	else
		$wormhole=false;


	$_SESSION['haven']['status']="choosePlanet";

	if ($wormhole)
		echo "<h2>Ziel nach dem Wurmlochsprung w&auml;hlen</h2>";
	elseif ($_POST['planet_to']>0 || $_GET['planet_to']>0)
		echo "<h2>Ziel best&auml;tigen</h2>";
	else
		echo "<h2>Neues Ziel w&auml;hlen</h2>";

	if ( $_POST['submit_shipselection_all']!="" || (@array_sum($_POST['ship_count'])>0 && $_POST['submit_shipselection']!="") || count($_SESSION['haven']['ships'])>0)
	{
			// SQL-Query generieren
			if ($_POST['submit_shipselection_all']!="") // Falls alle Schiffe gewählt werden sollen
			{
				$sres = dbquery("
				SELECT
					*
				FROM
					".$db_table['shiplist']."
				INNER JOIN
					".$db_table['ships']."
				ON 
					ships.ship_id=shiplist.shiplist_ship_id
          AND shiplist.shiplist_user_id=".$s['user']['id']."
          AND shiplist.shiplist_planet_id=".$c->id."
          AND shiplist.shiplist_count>0
          AND ship_launchable=1
				ORDER BY
					ships.ship_name,
					ships.special_ship DESC;");

				//bonis der spezialschiffe laden
				$ssres = dbquery("
				SELECT
					ships.special_ship_bonus_speed,
					ships.special_ship_bonus_capacity,
					ships.special_ship_bonus_pilots,
					shiplist.shiplist_special_ship_bonus_speed,
					shiplist.shiplist_special_ship_bonus_capacity,
					shiplist.shiplist_special_ship_bonus_pilots
				FROM
          ".$db_table['ships']." AS ships
          INNER JOIN
          ".$db_table['shiplist']." AS shiplist
          ON ships.ship_id=shiplist.shiplist_ship_id
          AND shiplist.shiplist_user_id='".$s['user']['id']."'
          AND shiplist.shiplist_planet_id='".$c->id."'
          AND shiplist.shiplist_count>'0'
          AND shiplist.shiplist_special_ship='1';");
			}
			else
			{
				// Ausgewählte Schiffe in ein Array Speichern (normal)
				$ship_count=array();
				if (count($_POST['ship_count'])>0)
				{
					foreach ($_POST['ship_count'] as $id=>$num)	// Falls Schiffe per Formular ausgewählt worden sind
					{
						if ($num>0)
							$ship_count[$id]=nf_back($num);
					}
				}
				else //if (count($_SESSION['haven']['ships'])>0)	// Falls Schiffe im Cookie gespeichert sind
				{
					foreach ($_SESSION['haven']['ships'] as $id=>$num)
					{
						if ($num>0)
							$ship_count[$id]=$num;
					}
				}


				$sql="
				SELECT
					*
				FROM
          ".$db_table['ships'].",
          ".$db_table['shiplist']."
				WHERE
          ships.ship_id=shiplist.shiplist_ship_id
          AND shiplist.shiplist_user_id=".$s['user']['id']."
          AND shiplist.shiplist_planet_id=".$c->id."
          AND (";
          $x=1;
          foreach ($ship_count as $id=>$num)
          {
              if ($num>0)
              {
                  $sql.="ships.ship_id=$id";
                  if ($x<count($ship_count))
                      $sql.=" OR ";
                  $x++;
              }
          }
          $sql.=")
				ORDER BY
					ships.ship_name,
					ships.special_ship DESC;";

				$sres = dbquery($sql);

				//bonis der spezialschiffe laden
				$ssql="
				SELECT
					ships.special_ship_bonus_speed,
					ships.special_ship_bonus_capacity,
					ships.special_ship_bonus_pilots,
					shiplist.shiplist_special_ship_bonus_speed,
					shiplist.shiplist_special_ship_bonus_capacity,
					shiplist.shiplist_special_ship_bonus_pilots
				FROM
          ".$db_table['ships']." AS ships,
          ".$db_table['shiplist']." AS shiplist
				WHERE
          ships.ship_id=shiplist.shiplist_ship_id
          AND shiplist.shiplist_user_id=".$s['user']['id']."
          AND shiplist.shiplist_planet_id=".$c->id."
          AND (";
          $x=1;
          foreach ($ship_count as $id=>$num)
          {
              if ($num>0)
              {
                  $ssql.="ships.ship_id=$id";
                  if ($x<count($ship_count))
                      $ssql.=" OR ";
                  $x++;
              }
          }
          $ssql.=")";

				$ssres = dbquery($ssql);
			}

			// Schiff-Daten verarbeiten
			$speed_percent=$_SESSION['haven']['fleet']['speed_percent'];

			if (!$wormhole)
			{
				$_SESSION['haven']['fleet']=Null;
			}

			// Bonis der Spezialschiffe berechnen und summieren
            $special_ship_bonus_speed = 1;
            $special_ship_bonus_capacity = 1;
            $special_ship_bonus_pilots = 1;

			while ($ssarr = mysql_fetch_array($ssres))
			{
                $special_ship_bonus_speed+=$ssarr['special_ship_bonus_speed'] * $ssarr['shiplist_special_ship_bonus_speed'];
                $special_ship_bonus_capacity+=$ssarr['special_ship_bonus_capacity'] * $ssarr['shiplist_special_ship_bonus_capacity'];
                $special_ship_bonus_pilots-=$ssarr['special_ship_bonus_pilots'] * $ssarr['shiplist_special_ship_bonus_pilots'];
			}


			while ($sarr = mysql_fetch_array($sres))
			{
				if ($_POST['submit_shipselection_all']!="" && $sarr['shiplist_count']>0)
					$_SESSION['haven']['ships'][$sarr['ship_id']] = $sarr['shiplist_count'];
				elseif (min($ship_count[$sarr['ship_id']],$sarr['shiplist_count'])>0)
					$_SESSION['haven']['ships'][$sarr['ship_id']] = min($ship_count[$sarr['ship_id']],$sarr['shiplist_count']);
				else
					continue;


				$_SESSION['haven']['ship_names'][$sarr['ship_id']] = $sarr['ship_name'];


				// Rassenspeedbonus laden
				$rres=dbquery("
				SELECT
					race_f_fleettime
				FROM
					".$db_table['races']."
				WHERE
					race_id=".$s['user']['race_id'].";");
				$rarr=mysql_fetch_array($rres);


				//Geschiwindigkeitbonus wird berechnet
				$speedfactor=2-$rarr['race_f_fleettime'];

				$vres=dbquery("
				Select
                    techlist.techlist_current_level,
                    ship_requirements.req_req_tech_level,
                    technologies.tech_name
				FROM
                    ".$db_table['techlist'].",
                    ".$db_table['ship_requirements'].",
                    ".$db_table['technologies']."
				WHERE
                    ship_requirements.req_ship_id=".$sarr['ship_id']."
                    AND technologies.tech_type_id='".TECH_SPEED_CAT."'
                    AND ship_requirements.req_req_tech_id=tech_id
                    AND technologies.tech_id=techlist.techlist_tech_id
                    AND techlist.techlist_tech_id=ship_requirements.req_req_tech_id
                    AND techlist.techlist_user_id=".$s['user']['id']."
				GROUP BY
					ship_requirements.req_id;");
				if (mysql_num_rows($vres)>0)
				{
					while ($varr=mysql_fetch_array($vres))
					{
						$speedfactor+=($varr['techlist_current_level']-$varr['req_req_tech_level'])*0.1;
					}
				}
				$sarr['ship_speed']*=$speedfactor;
				$sarr['ship_speed']/=FLEET_FACTOR_F;

				// Aus allen ausgewählten Schiffstypen den langsamsten Typ wählen; alle Schiffe müssen ihre Geschwindigkeit (AE/h) an dieses Schiff anpassen
				if (!isset($min_speed) || $sarr['ship_speed']<$min_speed)
				{
					$min_speed=$sarr['ship_speed']*$special_ship_bonus_speed;
					$_SESSION['haven']['fleet']['min_speed'] = $min_speed;
				}


				// Gesamte Kosten pro 100 AE ausrechnen
				$_SESSION['haven']['fleet']['costs_per_ae'] += $min_speed * $sarr['ship_fuel_use'] / $sarr['ship_speed'] * $_SESSION['haven']['ships'][$sarr['ship_id']];

				// Lande + Startkosten ausrechnen
				if (!$wormhole)
				{
					$_SESSION['haven']['fleet']['costs_launch_land'] += 2 * ($sarr['ship_fuel_use_launch'] + $sarr['ship_fuel_use_landing']) * $_SESSION['haven']['ships'][$sarr['ship_id']];
				}

				// Zeitaufwand für Start berechnen
				if ($_SESSION['haven']['fleet']['time_to_start']=="" || $sarr['ship_time2start']>$_SESSION['haven']['fleet']['time_to_start'])
				{
					if (!$wormhole)
					{
						$_SESSION['haven']['fleet']['time_to_start'] = $sarr['ship_time2start'];
					}
				}

				// Zeitaufwand für Landung berechnen
				if ($_SESSION['haven']['fleet']['time_to_land']=="" || $sarr['ship_time2land']>$_SESSION['haven']['fleet']['time_to_land'])
				{
					if (!$wormhole)
						$_SESSION['haven']['fleet']['time_to_land'] = $sarr['ship_time2land'];
				}

				// Kapazität berechnen
				if(!$wormhole)
					$_SESSION['haven']['fleet']['total_capacity'] += $sarr['ship_capacity'] * $_SESSION['haven']['ships'][$sarr['ship_id']];

				//Kapazität der Leute berechnen
				if(!$wormhole)
					$_SESSION['haven']['fleet']['people_capacity'] += $sarr['ship_people_capacity'] * $_SESSION['haven']['ships'][$sarr['ship_id']];

				// Piloten
				if (!$wormhole)
					$_SESSION['haven']['fleet']['total_pilots'] += $sarr['ship_pilots'] * $_SESSION['haven']['ships'][$sarr['ship_id']];

				// Invasion möglich?
				if ($sarr['ship_invade']==1) $_SESSION['haven']['fleet']['can_invade'] = true;

				// Kolonialisieren möglich?
				if ($sarr['ship_colonialize']==1) $_SESSION['haven']['fleet']['can_colonialize'] = true;

				// Recyceln möglich
				if ($sarr['ship_recycle']==true) $_SESSION['haven']['fleet']['can_recycle'] = true;

				// Gassaugen möglich
				if ($sarr['ship_nebula']==1) $_SESSION['haven']['fleet']['can_collect_gas'] = true;

				//Asteroiden möglich
				if ($sarr['ship_asteroid']==1) $_SESSION['haven']['fleet']['can_asteroid'] = true;

				//Bombadieren möglich?
				if ($sarr['ship_build_destroy']==1) $_SESSION['haven']['fleet']['can_bomb']=true;

				//Giftgas möglich?
				if ($sarr['ship_antrax']==1) $_SESSION['haven']['fleet']['can_antrax'] = true;

				//Forschungsklau?
				if ($sarr['ship_forsteal']==1) $_SESSION['haven']['fleet']['can_steal'] = true;

				//Fakeangriff
				if ($sarr['ship_fake']==1) $_SESSION['haven']['fleet']['can_fake'] = true;

				//Antrax
				if ($sarr['ship_antrax_food']==1) $_SESSION['haven']['fleet']['can_antrax_food'] = true;

				//Haven deaktivieren
				if($sarr['ship_deactivade']==1) $_SESSION['haven']['fleet']['can_deactivade']=true;

				//TF erstellen
				if($sarr['ship_tf']==1) $_SESSION['haven']['fleet']['can_tf']=true;

				//Tarnangriff
				if ($sarr['ship_tarned']==1) $_SESSION['haven']['fleet']['can_tarn'] = true;
			}


		$_SESSION['haven']['fleet']['time_to_start']/=FLEET_FACTOR_S;
		$_SESSION['haven']['fleet']['time_to_land']/=FLEET_FACTOR_L;


		//Bonis von Spezialschiffe dazuzählen
		$_SESSION['haven']['fleet']['total_capacity'] = $_SESSION['haven']['fleet']['total_capacity'] * $special_ship_bonus_capacity;
		$_SESSION['haven']['fleet']['total_pilots'] = $_SESSION['haven']['fleet']['total_pilots'] * $special_ship_bonus_pilots;


		// Piloten berechnen
		$parr= mysql_fetch_row(dbquery("SELECT planet_people FROM ".$db_table['planets']." WHERE planet_id='".$c->id."';"));
		$pbarr1= mysql_fetch_row(dbquery("SELECT SUM(buildlist_people_working) FROM ".$db_table['buildlist']." WHERE buildlist_planet_id='".$c->id."';"));
		$people_available=floor($parr[0]-$pbarr1[0]);


		//
		// Planetenauswahl, falls genügend Piloten
		//
		if ($_SESSION['haven']['fleet']['total_pilots']<=$people_available)
		{

			/// Prüfen ob Schiffe vorhanden (durch Bug oder Cheat kann evtl die Liste gelöscht sein, darum dieser Check
			if (count($_SESSION['haven']['ships'])>0)
			{

				echo "<form action=\"?page=$page\" method=\"post\">\n";

				//
				// Bookmarks laden
				//
				$bookmarks=array();

				// Eigene Planeten
				$pres = dbquery("
				SELECT
                    space_cells.cell_sx,
                    space_cells.cell_sy,
                    space_cells.cell_cx,
                    space_cells.cell_cy,
                    planets.planet_solsys_pos,
                    planets.planet_name
				FROM
                    ".$db_table['space_cells'].",
                    ".$db_table['planets']."
				WHERE
                    space_cells.cell_id=planets.planet_solsys_id
                    AND planets.planet_user_id=".$s['user']['id']."
				ORDER BY
					planets.planet_user_main DESC,
					planets.planet_name ASC,
                    space_cells.cell_id ASC;");
				if (mysql_num_rows($pres)>0)
				{
					while($parr=mysql_fetch_array($pres))
					{
						array_push(
						$bookmarks,
						array(
						"cell_sx"=> $parr['cell_sx'],
						"cell_sy"=> $parr['cell_sy'],
						"cell_cx"=> $parr['cell_cx'],
						"cell_cy"=> $parr['cell_cy'],
						"planet_solsys_pos"=> $parr['planet_solsys_pos'],
						"planet_name"=> $parr['planet_name'],
						"automatic"=>1)
						);
					}
				}
				// Gespeicherte Bookmarks
				$pres = dbquery("
				SELECT
                    space_cells.cell_sx,
                    space_cells.cell_sy,
                    space_cells.cell_cx,
                    space_cells.cell_cy,
                    planets.planet_solsys_pos,
                    planets.planet_name,
                    target_bookmarks.bookmark_comment
				FROM
                    ".$db_table['space_cells'].",
                    ".$db_table['planets'].",
                    ".$db_table['target_bookmarks']."
				WHERE
                    target_bookmarks.bookmark_user_id=".$s['user']['id']."
                    AND target_bookmarks.bookmark_planet_id=planets.planet_id
                    AND target_bookmarks.bookmark_cell_id=space_cells.cell_id
                    AND planets.planet_solsys_id=space_cells.cell_id
				GROUP BY
                    target_bookmarks.bookmark_id
				ORDER BY
                    target_bookmarks.bookmark_comment,
                    target_bookmarks.bookmark_cell_id,
                    target_bookmarks.bookmark_planet_id;");
				if (mysql_num_rows($pres)>0)
				{
					while($parr=mysql_fetch_array($pres))
					{
						array_push(
						$bookmarks,
						array(
						"cell_sx"=> $parr['cell_sx'],
						"cell_sy"=> $parr['cell_sy'],
						"cell_cx"=> $parr['cell_cx'],
						"cell_cy"=> $parr['cell_cy'],
						"planet_solsys_pos"=> $parr['planet_solsys_pos'],
						"planet_name"=> $parr['planet_name'],
						"automatic"=>0,
						"bookmark_comment"=> $parr['bookmark_comment'])
						);
					}
				}

				$pres = dbquery("
                SELECT
                	space_cells.cell_sx,
                    space_cells.cell_sy,
                    space_cells.cell_cx,
                    space_cells.cell_cy,
                    space_cells.cell_nebula,
                    space_cells.cell_asteroid,
                    space_cells.cell_wormhole_id,
                    target_bookmarks.bookmark_comment
				FROM
                    ".$db_table['space_cells'].",
                    ".$db_table['target_bookmarks']."
				WHERE
                    target_bookmarks.bookmark_user_id=".$s['user']['id']."
                    AND target_bookmarks.bookmark_planet_id=0
                    AND target_bookmarks.bookmark_cell_id=space_cells.cell_id
				GROUP BY
					target_bookmarks.bookmark_id
				ORDER BY
					target_bookmarks.bookmark_comment ASC;");
				if (mysql_num_rows($pres)>0)
				{
					while($parr=mysql_fetch_array($pres))
					{
						if (!$wormhole || ($wormhole && $parr['cell_wormhole_id']==0))
						{
							array_push(
							$bookmarks,
							array(
							"cell_sx"=> $parr['cell_sx'],
							"cell_sy"=> $parr['cell_sy'],
							"cell_cx"=> $parr['cell_cx'],
							"cell_cy"=> $parr['cell_cy'],
							"planet_solsys_pos"=> 0,
							"automatic"=>0,
							"bookmark_comment"=> $parr['bookmark_comment'],
							"nebula"=> $parr['cell_nebula'],
							"asteroid"=> $parr['cell_asteroid'],
							"wormhole"=> $parr['cell_wormhole_id'])
							);
						}
					}
				}


				if ($wormhole)
				{
					$parr = mysql_fetch_array(dbquery("
					SELECT
                        cell_sx,
                        cell_sy,
                        cell_cx,
                        cell_cy
					FROM
						".$db_table['space_cells']."
					WHERE
						cell_id=".$_SESSION['haven']['wormhole'].";"));
					$sx1=$parr['cell_sx'];
					$sy1=$parr['cell_sy'];
					$cx1=$parr['cell_cx'];
					$cy1=$parr['cell_cy'];
        	$p1=0;
				}
				else
				{
					$sx1=$c->sx;
					$sy1=$c->sy;
					$cx1=$c->cx;
					$cy1=$c->cy;
        	$p1=$c->solsys_pos;
        }

				// Entfernung berechnen (JavaScript)
				echo "<script type=\"text/javascript\">
				function upd_values()
				{
					f = document.forms[0];
					if (f.fleet_sx.value!='' && f.fleet_sy.value!='' && f.fleet_cx.value!='' && f.fleet_cy.value!='' && f.fleet_p.value!='')
						calc_distance();
				}
				function calc_distance()
				{
					f = document.forms[0];
					sx2=parseInt(f.fleet_sx.value);
					sy2=parseInt(f.fleet_sy.value);
					cx2=parseInt(f.fleet_cx.value);
					cy2=parseInt(f.fleet_cy.value);
					p2=parseInt(f.fleet_p.value);
					sx1=".$sx1.";
					sy1=".$sy1.";
					cx1=".$cx1.";
					cy1=".$cy1.";
					p1=".$p1.";	// Position des aktuellen Planeten im Sonnensystem
					nx=".$conf['num_of_cells']['p1'].";		// Anzahl Zellen Y
					ny=".$conf['num_of_cells']['p2'].";		// Anzahl Zellen X
					ae=".$conf['cell_length']['v'].";			// Länge vom Solsys in AE
					np=".$conf['num_planets']['p2'].";		// Max. Planeten im Solsys
					cae=parseInt(".ceil($_SESSION['haven']['fleet']['costs_per_ae']).");				// Totale Kosten pro 100 AE
					";
					if ($wormhole)
						echo "cll=0;	// Basiskosten für Start und Landung
						";
					else
						echo "cll=parseInt(".$_SESSION['haven']['fleet']['costs_launch_land'].");	// Basiskosten für Start und Landung
						";
					echo "fspeed=parseInt(".$_SESSION['haven']['fleet']['min_speed'].");				// Geschwindigkeit der Flotte
					";
					if ($wormhole)
						echo "s_time=0;		// Startzeit
						l_time=0;		// Landezeit
						";
					else
						echo "s_time=parseInt(".$_SESSION['haven']['fleet']['time_to_start'].");		// Startzeit
						l_time=parseInt(".$_SESSION['haven']['fleet']['time_to_land'].");		// Landezeit
						";

					echo "capacity=parseInt(".$_SESSION['haven']['fleet']['total_capacity'].");// Kapazität

				fspeed = Math.ceil(fspeed * (document.getElementById('duration_percent').options[ document.getElementById('duration_percent').selectedIndex].value));
					cae = Math.ceil(cae * (document.getElementById('duration_percent').options[ document.getElementById('duration_percent').selectedIndex].value));

					dx = Math.abs((((sx2-1) * nx) + cx2) - (((sx1-1) * nx) + cx1));
					dy = Math.abs((((sy2-1) * nx) + cy2) - (((sy1-1) * nx) + cy1));
					s = Math.sqrt(Math.pow(dx,2)+Math.pow(dy,2));		// Distanze zwischen den beiden Zellen
					sae = s * ae;																		// Distance in AE units
					if (sx1==sx2 && sy1==sy2 && cx1==cx2 && cy1==cy2)
						ps = Math.abs(p2-p1)*ae/4/np;									// Planetendistanz wenn sie im selben Solsys sind
					else
						ps = (ae/2) - ((p2)*ae/4/np);									// Planetendistanz wenn sie nicht im selben Solsys sind
					ssae = sae + ps;


					c = Math.ceil((ssae * cae / 100) + cll);
				timeforflight = ssae / fspeed;


				timetotal =  (timeforflight*3600) + s_time + l_time;
					t = Math.floor(timetotal / 3600 / 24);
					h = Math.floor(timetotal / 3600);
					m = Math.floor((timetotal-(h*3600))/60);
					s = Math.floor((timetotal-(h*3600)-(m*60)));
					timeshow = h+'h '+m+'m '+s+'s';

		  	lltime = s_time+l_time;
					t = Math.floor(lltime / 3600 / 24);
					h = Math.floor(lltime / 3600);
					m = Math.floor((lltime-(h*3600))/60);
					s = Math.floor((lltime-(h*3600)-(m*60)));
					lltimeshow = h+'h '+m+'m '+s+'s';

					document.getElementById('speed').firstChild.nodeValue=fspeed.toFixed(0) +  ' AE / h';
					document.getElementById('distance').firstChild.nodeValue=ssae.toFixed(0) +  ' AE';
					document.getElementById('costs').firstChild.nodeValue=c.toFixed(0) +  ' ".$rsc['fuel']."';
					document.getElementById('duration').firstChild.nodeValue=timeshow;

					if (cx2>nx || cx2<1)
					{
						document.getElementById('comment').firstChild.nodeValue='X-Koordinate ist ausserhalb des bekannten Raums!';
						f.fleet_cx.style.color='red';
					}
					else if (cy2>ny || cy2<1)
					{
						document.getElementById('comment').firstChild.nodeValue='Y-Koordinate ist ausserhalb des bekannten Raums!';
						f.fleet_cy.style.color='red';
					}
					else if (p2>np || p2<0)
					{
						document.getElementById('comment').firstChild.nodeValue='Ung&uuml;ltige Planetennummer!';
						f.fleet_p.style.color='red';
					}
					else if (c>capacity)
					{
						capaless = c-capacity;
						document.getElementById('comment').firstChild.nodeValue='Zuwenig Laderaum f&uuml;r soviel Treibstoff (' + capaless+ ' zuviel)!';
						document.getElementById('costs').style.color='red';
					}
					else
					{
						document.getElementById('comment').firstChild.nodeValue='-';
						document.getElementById('costs').style.color='';
						f.fleet_cx.style.color='';
						f.fleet_cy.style.color='';
						f.fleet_p.style.color='';
					}
				/*else
				{
						document.getElementById('distance').firstChild.nodeValue='-';
						document.getElementById('duration').firstChild.nodeValue='-';
						document.getElementById('speed').firstChild.nodeValue='-';
						document.getElementById('costs').firstChild.nodeValue='-';
						document.getElementById('comment').firstChild.nodeValue='-';
						document.getElementById('costs').style.color='';
						f.fleet_cx.style.color='';
						f.fleet_cy.style.color='';
						f.fleet_p.style.color='';
				}    */
				}

				function applyBookmark()
				{
					select_id=document.getElementById('bookmarkselect').selectedIndex;
					select_val=document.getElementById('bookmarkselect').options[select_id].value;
					a=1;
					if (select_val!='')
					{
						switch(select_val)
						{
							";
							foreach ($bookmarks as $i=> $b)
							{
								echo "case \"$i\":\n";
								echo "document.getElementById('fleet_sx').value='".$b['cell_sx']."';\n";
								echo "document.getElementById('fleet_sy').value='".$b['cell_sy']."';\n";
								echo "document.getElementById('fleet_cx').value='".$b['cell_cx']."';\n";
								echo "document.getElementById('fleet_cy').value='".$b['cell_cy']."';\n";
								echo "document.getElementById('fleet_p').value='".$b['planet_solsys_pos']."';\n";
								echo "break;\n";
							}

							echo "
						}

					}
					upd_values();
				}
				</script>";

				if (intval($_POST['planet_to'])>0 || intval($_GET['planet_to'])>0)
				{
					if ($_POST['planet_to']!="") $pt = intval($_POST['planet_to']); else $pt = intval($_GET['planet_to']);
					$parr = mysql_fetch_array(dbquery("
					SELECT
                        s.cell_sx,
                        s.cell_sy,
                        s.cell_cx,
                        s.cell_cy,
                        p.planet_solsys_pos
					FROM
						".$db_table['space_cells']." AS s,
						".$db_table['planets']." AS p
					WHERE
						p.planet_solsys_id=s.cell_id
						AND p.planet_id='$pt';"));
					$csx = $parr['cell_sx'];
					$csy = $parr['cell_sy'];
					$ccx = $parr['cell_cx'];
					$ccy = $parr['cell_cy'];
					$psp = $parr['planet_solsys_pos'];
				}
				elseif (intval($_POST['cell_to'])!=0)
				{
					if (intval($_POST['cell_to'])!=0) $cell_id=intval($_POST['cell_to']);
					$parr = mysql_fetch_array(dbquery("
					SELECT
                        cell_sx,
                        cell_sy,
                        cell_cx,
                        cell_cy
					FROM
						".$db_table['space_cells']."
					WHERE
						cell_id=".$cell_id.";"));
					$csx = $parr['cell_sx'];
					$csy = $parr['cell_sy'];
					$ccx = $parr['cell_cx'];
					$ccy = $parr['cell_cy'];
					$psp = 0;
				}
				elseif (count($_SESSION['haven']['target'])>0)
				{
					$csx = $_SESSION['haven']['target']['sx'];
					$csy = $_SESSION['haven']['target']['sy'];
					$ccx = $_SESSION['haven']['target']['cx'];
					$ccy = $_SESSION['haven']['target']['cy'];
					$psp = $_SESSION['haven']['target']['p'];
				}
				else
				{
					$csx = $c->sx;
					$csy = $c->sy;
					$ccx = $c->cx;
					$ccy = $c->cy;
					$psp = $c->solsys_pos;
				}


				infobox_start("Koordinatenwahl",1);
				if ($wormhole)
				{
					echo "<tr><td class=\"tbltitle\" width=\"25%\">Startplanet:</td><td class=\"tbldata\" width=\"75%\">".$c->getString()."</td></tr>";
					echo "<tr><td class=\"tbltitle\" width=\"25%\">Wurmloch-Eintrittspunkt:</td><td class=\"tbldata\" width=\"75%\">".$_SESSION['haven']['target']['sx']."/".$_SESSION['haven']['target']['sy']." : ".$_SESSION['haven']['target']['cx']."/".$_SESSION['haven']['target']['cy']."</td></tr>";
					echo "<tr><td class=\"tbltitle\" width=\"25%\">Entfernung:</td><td class=\"tbldata\" width=\"75%\">".nf($_SESSION['haven']['fleet']['flight_distance'])." AE</td></tr>";
					echo "<tr><td class=\"tbltitle\" width=\"25%\">Dauer:</td><td class=\"tbldata\" width=\"75%\">".tf($_SESSION['haven']['fleet']['flight_duration'])."</td></tr>";
					echo "<tr><td class=\"tbltitle\" width=\"25%\">Treibstoff:</td><td class=\"tbldata\" width=\"75%\">".nf($_SESSION['haven']['fleet']['flight_costs'])." ".RES_FUEL."</td></tr>";
					echo "<tr><td class=\"tbltitle\" width=\"25%\">Piloten:</td><td class=\"tbldata\" width=\"75%\">".nf($_SESSION['haven']['fleet']['total_pilots'])."</td></tr>";
					echo "<tr><td class=\"tbldata\" colspan=\"2\" style=\"width:2px;\"></td></tr>";
					echo "<tr><td class=\"tbltitle\" width=\"25%\">Wurmloch-Austrittspunkt:</td><td class=\"tbldata\" width=\"75%\">$sx1/$sy1 : $cx1/$cy1</td></tr>";
				}
				else
					echo "<tr><td class=\"tbltitle\" width=\"25%\">Startplanet:</td><td class=\"tbldata\" width=\"75%\">".$c->getString()."</td></tr>";

				// Bookmarkliste anzeigen
				echo "<tr><td class=\"tbltitle\">Zielfavorit:</td><td class=\"tbldata\"><select id=\"bookmarkselect\" onchange=\"applyBookmark();\">";
				if (count($bookmarks)>0)
				{
					$a=1;
					echo "<option value=\"\">W&auml;hlen...</option>";
					foreach ($bookmarks as $i=> $b)
					{
						if ($b['automatic']==0 && $a==1)
						{
							$a=0;
							echo "<option value=\"\">-----------------------------</option>";
						}
						echo "<option value=\"$i\"";
						if ($csx==$b['cell_sx'] && $csy==$b['cell_sy'] && $ccx==$b['cell_cx'] && $ccy==$b['cell_cy'] && $psp==$b['planet_solsys_pos']) echo " selected=\"selected\"";
						echo ">";
						if ($b['automatic']==1) echo "Eigener Planet: ";
						if ($b['planet_solsys_pos']>0)
							echo $b['cell_sx']."/".$b['cell_sy']." : ".$b['cell_cx']."/".$b['cell_cy']." : ".$b['planet_solsys_pos']." ".$b['planet_name'];
						else
							echo $b['cell_sx']."/".$b['cell_sy']." : ".$b['cell_cx']."/".$b['cell_cy']." ";
						if ($b['nebula']==1) echo "Intergalaktischer Nebel";
						if ($b['asteroid']==1) echo "Asteroidenfeld";
						if ($b['wormhole']>0) echo "Wurmloch";
						if ($b['bookmark_comment']!="") echo " (".stripslashes($b['bookmark_comment']).")";
						echo "</option>";
					}
				}
				else
					echo "<option value=\"\">(Nichts vorhaden)</option>";
				echo "</select></td></tr>";
				// Manuelle Auswahl
				echo "<tr><td class=\"tbltitle\" width=\"25%\">Manuelle Zielwahl:</td><td class=\"tbldata\" width=\"75%\">";
				echo "<input type=\"text\" id=\"fleet_sx\" name=\"fleet_sx\" size=\"2\" maxlength=\"2\" value=\"$csx\" title=\"Sektor X-Koordinate\" onKeyUp=\"upd_values();\" onKeyPress=\"return nurZahlen(event)\"/>&nbsp;/&nbsp;";
				echo "<input type=\"text\" id=\"fleet_sy\" name=\"fleet_sy\" size=\"2\" maxlength=\"2\" value=\"$csy\" title=\"Sektor Y-Koordinate\" onKeyUp=\"upd_values();\" onKeyPress=\"return nurZahlen(event)\"/>&nbsp;&nbsp;:&nbsp;&nbsp;";
				echo "<input type=\"text\" id=\"fleet_cx\" name=\"fleet_cx\" size=\"2\" maxlength=\"2\" value=\"$ccx\" title=\"Zelle X-Koordinate\" onKeyUp=\"upd_values();\" onKeyPress=\"return nurZahlen(event)\"/>&nbsp;/&nbsp;";
				echo "<input type=\"text\" id=\"fleet_cy\" name=\"fleet_cy\" size=\"2\" maxlength=\"2\" value=\"$ccy\" title=\"Zelle Y-Koordinate\" onKeyUp=\"upd_values();\" onKeyPress=\"return nurZahlen(event)\"/>&nbsp;&nbsp;:&nbsp;&nbsp;";
				echo "<input type=\"text\" id=\"fleet_p\" name=\"fleet_p\" size=\"2\" maxlength=\"2\" value=\"$psp\" title=\"Position des Planeten im Sonnensystem\" onKeyUp=\"upd_values();\" onKeyPress=\"return nurZahlen(event)\"/></td></tr>";
				// Speedfaktor
				echo "<tr><td class=\"tbltitle\" width=\"25%\">Speedfaktor:</td><td class=\"tbldata\" width=\"75%\" align=\"left\"><select name=\"speed_percent\" id=\"duration_percent\" onchange=\"upd_values();\">\n";
				for ($x=1;$x>0.1;$x-=0.1)
				{
					$perc = $x*100;
					echo "<option value=\"$x\"";
					if ($speed_percent*100==$perc) echo " selected=\"selected\"";
					echo ">$perc</option>\n";
				}
				echo "</select> %</td></tr>";
				// Daten anzeigen
				echo "<tr><td class=\"tbltitle\" width=\"25%\">Kosten/100 AE:</td><td class=\"tbldata\">".nf($_SESSION['haven']['fleet']['costs_per_ae'])." t</td></tr>";
				echo "<tr><td class=\"tbltitle\" width=\"25%\">Geschwindigkeit:</td><td class=\"tbldata\" width=\"75%\" align=\"left\" id=\"speed\">-</td></tr>";
				echo "<tr><td class=\"tbltitle\" width=\"25%\">Entfernung:</td><td class=\"tbldata\" width=\"75%\" align=\"left\" id=\"distance\">-</td></tr>";
				echo "<tr><td class=\"tbltitle\" width=\"25%\">Dauer:</td><td class=\"tbldata\" width=\"75%\" align=\"left\" id=\"duration\">-</td></tr>";
				echo "<tr><td class=\"tbltitle\" width=\"25%\">Treibstoff:</td><td class=\"tbldata\" width=\"75%\" align=\"left\" id=\"costs\">-</td></tr>";
				echo "<tr><td class=\"tbltitle\" width=\"25%\">Piloten:</td><td class=\"tbldata\" width=\"75%\">".nf($_SESSION['haven']['fleet']['total_pilots'])."</td></tr>";
				echo "<tr><td class=\"tbltitle\" width=\"25%\">Bemerkungen:</td><td class=\"tbldata\" width=\"75%\" align=\"left\" id=\"comment\">-</td></tr>";
				infobox_end(1);

				echo "Schnellere Schiffe nehmen im Flottenverband automatisch die Geschwindigkeit des langsamsten Schiffes an, sie brauchen daf&uuml;r aber auch entsprechend weniger Treibstoff!<br/>";

				echo "Flugzeit wird inkl. Start- und Landezeit von ".tf($_SESSION['haven']['fleet']['time_to_start'] + $_SESSION['haven']['fleet']['time_to_land'])." berechnet.<br/> Flugkosten werden inkl. Start- und Landeverbrauch von ".nf($_SESSION['haven']['fleet']['costs_launch_land'])." ".$rsc['fuel']." berechnet.<br/><br/>";
				echo "<input type=\"submit\" name=\"reset\" value=\"Vorgang abbrechen\" />&nbsp;";
				echo "<input type=\"submit\" name=\"back\" value=\"&lt;&lt;&lt; Zur&uuml;ck zur Flottenauswahl\" title=\"Zur&uuml;ck zur Flottenauswahl\">&nbsp;";
				echo "<input type=\"submit\" name=\"submit_planetselection\" value=\"Weiter zum Start &gt;&gt;&gt;\" title=\"Wenn du das Ziel eigegeben hast, klicke hier um den Start zu best&auml;tigen\" />&nbsp;";
				echo "</form>";
				echo "<script>upd_values();</script><br/><br/>";

				//
				// Schiffe anzeigen
				//
				infobox_start("Ausgewählte Schiffe",1);
				foreach ($_SESSION['haven']['ship_names'] as $id=> $name)
				{
					echo "<tr><td class=\"tbldata\">$name</td><td class=\"tbldata\">".$_SESSION['haven']['ships'][$id]."</td></tr>";
				}
				infobox_end(1);

			}
			else
			{
				$_SESSION['haven']=Null;
				echo "<b>Fehler:</b> Es sind keine Schiffe ausgew&auml;hlt!<br/><br/>";
				echo "<input type=\"button\" name=\"back\" onClick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck zur Flottenauswahl\" title=\"Zur&uuml;ck zur Flottenauswahl\">";

			}
		}
		else
		{
			echo "<b>Fehler:</b> Es sind zuwenig Piloten vorhanden ($people_available vorhanden, ".$_SESSION['haven']['fleet']['total_pilots']." werden gebraucht!)!<br/><br/>";
			echo "<input type=\"button\" name=\"back\" onClick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck zur Flottenauswahl\" title=\"Zur&uuml;ck zur Flottenauswahl\">";
			$_SESSION['haven']=Null;
		}
	}
	else
	{
		$_SESSION['haven']=Null;
		echo "<b>Fehler:</b> Du hast keine Schiffe ausgew&auml;hlt!<br/><br/>";
		echo "<input type=\"button\" name=\"back\" onClick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck zur Flottenauswahl\" title=\"Zur&uuml;ck zur Flottenauswahl\">";
	}
?>
