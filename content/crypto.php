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
	// 	File: crypto.php
	// 	Created: 14.12.2007
	// 	Last edited: 15.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Manages the cryptocenter
	*
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	// BEGIN SKRIPT //

	echo "<form action=\"?page=$page\" method=\"post\">";
	
	// Gebäude Level und Arbeiter laden
  $werft_res = dbquery("
  SELECT
  	buildlist_current_level,
  	buildlist_people_working,
  	buildlist_deactivated,
  	buildlist_prod_percent,
  	buildlist_cooldown
  FROM
  	buildlist
  WHERE
  	buildlist_planet_id='".$c->id."'
  	AND buildlist_building_id='".BUILD_CRYPTO_ID."'
  	AND buildlist_current_level>='1'
  	AND buildlist_user_id='".$s['user']['id']."'");

  // Prüfen ob Gebäude gebaut ist
  if (mysql_num_rows($werft_res)>0)
  {
  	$werft_arr=mysql_fetch_array($werft_res);
  	$center_level = $werft_arr['buildlist_current_level'];
  	
		// Titel
		echo "<h1>Kryptocenter (Stufe ".$center_level.") des Planeten ".$c->name."</h1>";		
		
		// Ressourcen anzeigen
		$c->resBox();

		if ($c->prod->power - $c->use->power >= 0  && $c->prod->power>0 && $werft_arr['buildlist_prod_percent']==1)
		{
			if ($werft_arr['buildlist_deactivated'] < time())
			{
				// Calculate cooldown
				$cooldown = max($cfg->param2("cryptocenter"),$cfg->value("cryptocenter") - ($cfg->param1("cryptocenter")*($center_level-1)));
				if ($werft_arr['buildlist_cooldown']>time())
				{
					$status_text = "Bereit in ".tf($werft_arr['buildlist_cooldown']-time());
					$cd_enabled=true;
				}
				else
				{
					$status_text = "Bereit";
					$cd_enabled=false;
				}

				if (isset($_POST['scan']) && checker_verify()  && !$cd_enabled)
				{
					$sx = intval($_POST['sx']);
					$sy = intval($_POST['sy']);
					$cx = intval($_POST['cx']);
					$cy = intval($_POST['cy']);
					$pp = intval($_POST['p']);
					if ($sx>0 && $sy>0 && $cx>0 && $cy>0 && $pp>0)
					{
						if ($c->res->fuel >= CRYPTO_FUEL_COSTS_PER_SCAN)
						{
							$res = dbquery("
							SELECT
								planet_id,
								cell_id,
								planet_name,
								user_id
							FROM
								space_cells
							INNER JOIN
							(
								planets
								LEFT JOIN
									users 
									ON user_id=planet_user_id
							)
								ON planet_solsys_id=cell_id
								AND cell_sx=".$sx."
								AND cell_sy=".$sy."
								AND cell_cx=".$cx."
								AND cell_cy=".$cy."
								AND planet_solsys_pos=".$pp."
							
							;");
							if (mysql_num_rows($res)>0)
							{
								$sx1 = $c->sx;
								$sy1 = $c->sy;
								$cx1 = $c->cx;
								$cy1 = $c->cy;
								$p1 = $c->solsys_pos;
								
								$nx=$conf['num_of_cells']['p1'];		// Anzahl Zellen Y
								$ny=$conf['num_of_cells']['p2'];		// Anzahl Zellen X
								$ae=$conf['cell_length']['v'];			// Länge vom Solsys in AE
								$np=$conf['num_planets']['p2'];			// Max. Planeten im Solsys
								$dx = abs(((($sx-1) * $nx) + $cx) - ((($sx1-1) * $nx) + $cx1));
								$dy = abs(((($sy-1) * $nx) + $cy) - ((($sy1-1) * $nx) + $cy1));
								$sd = sqrt(pow($dx,2)+pow($dy,2));			// Distanze zwischen den beiden Zellen
								$sae = $sd * $ae;											// Distance in AE units
								if ($sx1==$sx && $sy1==$sy && $cx1==$cx && $cy1=$cy)
									$ps = abs($pp-$p1)*$ae/4/$np;				// Planetendistanz wenn sie im selben Solsys sind
								else
									$ps = ($ae/2) - (($pp)*$ae/4/$np);	// Planetendistanz wenn sie nicht im selben Solsys sind
								$ssae = $sae + $ps;
												
								if ($ssae <= CRYPTO_RANGE_PER_LEVEL*$center_level)
								{									
									$c->changeRes(0,0,0,-CRYPTO_FUEL_COSTS_PER_SCAN,0);
									include("inc/fleet_action.inc.php");
									$arr=mysql_fetch_array($res);          

									if ($arr['user_id']>0)
									{									
										send_msg($arr['user_id'],SHIP_SPY_MSG_CAT_ID,"Funkstörung","Eure Flottenkontrolle hat soeben eine kurzzeitige Störung des Kommunikationsnetzes festgestellt.");
									}
									
									$target = ($arr['planet_name']!='' ? $arr['planet_name'] : 'Unbenannt')."[/b] (".$sx."/".$sy." : ".$cx."/".$cy." : ".$pp.")";
									$out.="[b]Flottenscan vom Planeten ".$target."\n
									[b]Zeit:[/b] ".date("d.m.Y H:i:s")."\n\n";
									$out.="[b]Eintreffende Flotten[/b]\n\n";
									$fres = dbquery("
									SELECT
										fleet_landtime,
										fleet_action,
										planet_name,
										planet_solsys_pos,
										fleet_id,
										cell_sx,
										cell_sy,
										cell_cx,
										cell_cy,
										cell_id,
										user_nick									
									FROM
										fleet
									INNER JOIN
									(
										planets 
										LEFT JOIN
											users
											ON planet_user_id=user_id
									)
										ON fleet_planet_from=planet_id
										AND fleet_planet_to=".$arr['planet_id']."
									INNER JOIN
										space_cells
										ON cell_id=fleet_cell_from
									");
									if (mysql_num_rows($fres)>0)
									{
										require("inc/fleet_action.inc.php");
										while ($farr=mysql_fetch_array($fres))
										{
											$out.='[b]Herkunft:[/b] '.$farr['planet_name'].' ('.$farr['cell_sx'].'/'.$farr['cell_sy'].' : '.$farr['cell_cx'].'/'.$farr['cell_cy'].' : '.$farr['planet_solsys_pos'].'), [b]Besitzer:[/b] '.$farr['user_nick'];
											$out.= "\n[b]Ankunft:[/b] ".df($farr['fleet_landtime']).", [b]Aktion:[/b] ".fa($farr['fleet_action'])."\n";
											$sres = dbquery("
											SELECT
												ship_name,
												fs_ship_cnt
											FROM
												fleet_ships
											INNER JOIN
												ships
												ON ship_id=fs_ship_id
												AND fs_fleet_id=".$farr['fleet_id']."
											;");
											if (mysql_num_rows($sres)>0)
											{
												while ($sarr=mysql_fetch_array($sres))
												{
													$out.=$sarr['fs_ship_cnt']." ".$sarr['ship_name']."\n";
												}
											}	
											$out.="\n";									
										}
									}								
									else
									{
										$out.="Keine eintreffenden Flotten gefunden!\n\n";
									}
									
									$out.="[b]Wegfliegende Flotten[/b]\n\n";
									$fres = dbquery("
									SELECT
										fleet_landtime,
										fleet_action,
										planet_name,
										planet_solsys_pos,
										cell_sx,
										cell_sy,
										cell_cx,
										cell_cy,
										cell_id,
										user_nick									
									FROM
										fleet
									INNER JOIN
									(
										planets 
										LEFT JOIN
											users
											ON planet_user_id=user_id
									)
										ON fleet_planet_to=planet_id
										AND fleet_planet_from=".$arr['planet_id']."
									INNER JOIN
										space_cells
										ON cell_id=fleet_cell_to
									");
									if (mysql_num_rows($fres)>0)
									{
										while ($farr=mysql_fetch_array($fres))
										{
											$out.='[b]Ziel:[/b] '.$farr['planet_name'].' ('.$farr['cell_sx'].'/'.$farr['cell_sy'].' : '.$farr['cell_cx'].'/'.$farr['cell_cy'].' : '.$farr['planet_solsys_pos'].'), [b]Besitzer:[/b] '.$farr['user_nick'];
											$out.= "\n[b]Ankunft:[/b] ".df($farr['fleet_landtime']).", [b]Aktion:[/b] ".get_fleet_action($farr['fleet_action'])."\n\n";
										}
									}								
									else
									{
										$out.='Keine wegfliegenden Flotten gefunden!';
									}
									
									infobox_start("Ergebnis der Analyse");
									echo text2html($out);
									infobox_end();
									
									// Add note to user's notepad if selected
									if (isset($_POST['scan_to_notes']))
									{
										$np = new Notepad($s['user']['id']);
										$np->add(new Note("Flottenscan: ".$target,$out));
									}
									
									// Set cooldown
									$cd = time()+$cooldown;
									dbquery("
									UPDATE
										buildlist
									SET
										buildlist_cooldown=".$cd."
									WHERE
								  	buildlist_planet_id='".$c->id."'
								  	AND buildlist_building_id='".BUILD_CRYPTO_ID."'
								  	AND buildlist_user_id='".$s['user']['id']."'");
								  $werft_arr['buildlist_cooldown'] = $cd;
									if ($werft_arr['buildlist_cooldown']>time())
									{
										$status_text = "Bereit in ".tf($werft_arr['buildlist_cooldown']-time());
										$cd_enabled=true;
									}
									else
									{
										$status_text = "Bereit";
										$cd_enabled=false;
									}								  
								}
								else
								{
									echo "Das Ziel ist zu weit entfernt (".ceil($ssae)." AE, momentan sind ".CRYPTO_RANGE_PER_LEVEL*$center_level." möglich, ".CRYPTO_RANGE_PER_LEVEL." pro Gebäudestufe)!<br/><br/>";
								}									
							}
							else
							{
								echo "Am gewählten Ziel existiert kein Planet!<br/><br/>";
							}						
						}
						else
						{
							echo "Zuwenig ".RES_FUEL.", ".nf(CRYPTO_FUEL_COSTS_PER_SCAN)." benötigt!<br/><br/>";
						}						
					}
					else
					{
						echo "Ungültige Koordinaten!<br/><br/>";
					}
				}			
				
				infobox_start("Kryptocenter-Infos",1);
				echo "<tr><th class=\"tbltitle\">Aktuelle Reichweite:</th><td class=\"tbldata\">".(CRYPTO_RANGE_PER_LEVEL*$center_level)." AE (+".CRYPTO_RANGE_PER_LEVEL." pro Stufe)</td></tr>";
				echo "<tr><th class=\"tbltitle\">Kosten pro Scan:</th><td class=\"tbldata\">".nf(CRYPTO_FUEL_COSTS_PER_SCAN)." ".RES_FUEL."</td></tr>";
				echo "<tr><th class=\"tbltitle\">Abklingzeit:</th><td class=\"tbldata\">".tf($cooldown)." (-".tf($cfg->param1("cryptocenter"))." pro Stufe, minimal ".tf($cfg->param2("cryptocenter")).")</td></tr>";
				echo "<tr><th class=\"tbltitle\">Status:</th><td class=\"tbldata\">".$status_text."</td></tr>";
				infobox_end(1);
				
				if (!$cd_enabled)
				{				
					if ($_GET['c']!='' && $_GET['h']!='' && md5(base64_decode($_GET['c'])) == $_GET['h'])
					{
						$coords = explode(":",base64_decode($_GET['c']));
					}
					elseif(isset($_POST['scan']))
					{
						$coords[0] = $sx;
						$coords[1] = $sy;
						$coords[2] = $cx;
						$coords[3] = $cy;
						$coords[4] = $pp;
					}				
					else
					{
						$coords[0] = $c->sx;
						$coords[1] = $c->sy;
						$coords[2] = $c->cx;
						$coords[3] = $c->cy;
						$coords[4] = $c->solsys_pos;
					}  		
					echo '<form action="?page='.$page.'" method="post">';		
					checker_init();
					infobox_start("Ziel für Flottenanalyse wahlen:");
	
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
						".$db_table['planets']."
					INNER JOIN
						".$db_table['space_cells']."
					ON
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
						".$db_table['target_bookmarks']."
					INNER JOIN
	       		".$db_table['planets']."
	        ON 
	        	target_bookmarks.bookmark_user_id=".$s['user']['id']."
	         	AND target_bookmarks.bookmark_planet_id=planets.planet_id
					INNER JOIN       		
						".$db_table['space_cells']."
					ON 
						target_bookmarks.bookmark_cell_id=space_cells.cell_id
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
	
					echo 'Koordinaten eingeben: 
						<input type="text" name="sx" id="sx" value="'.$coords[0].'" size="2" maxlength="2" /> / 
						<input type="text" name="sy" id="sy" value="'.$coords[1].'" size="2" maxlength="2" /> :
						<input type="text" name="cx" id="cx" value="'.$coords[2].'" size="2" maxlength="2" /> /
						<input type="text" name="cy" id="cy" value="'.$coords[3].'" size="2" maxlength="2" /> :
						<input type="text" name="p" id="p" value="'.$coords[4].'" size="2" maxlength="2" /><br/><br/>';
					
					// Bookmarkliste anzeigen
					echo "<i>oder</i> Favorit wählen: <select id=\"bookmarkselect\" onchange=\"applyBookmark();\">";
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
							echo $b['cell_sx']."/".$b['cell_sy']." : ".$b['cell_cx']."/".$b['cell_cy']." : ".$b['planet_solsys_pos']." ".$b['planet_name'];
							if ($b['bookmark_comment']!="") echo " (".stripslashes($b['bookmark_comment']).")";
							echo "</option>";
						}
					}
					else
						echo "<option value=\"\">(Nichts vorhaden)</option>";
					echo "</select><br/><br/>
					<input type=\"checkbox\" name=\"scan_to_notes\" value=\"1\" checked=\"checked\" /> Zu meinem Notizblock hinzufügen";					
						
					infobox_end();
					if ($c->res->fuel >= CRYPTO_FUEL_COSTS_PER_SCAN)
					{
						echo '<input type="submit" name="scan" value="Analyse für '.nf(CRYPTO_FUEL_COSTS_PER_SCAN).' '.RES_FUEL.' starten" />';
					}
					else
					{
						echo "Zuwenig Rohstoffe für eine Analyse vorhanden, ".nf(CRYPTO_FUEL_COSTS_PER_SCAN)." ".RES_FUEL." benötigt!";
					}
					echo '</form>';				
					
					echo "<script type=\"text/javascript\">
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
									echo "document.getElementById('sx').value='".$b['cell_sx']."';\n";
									echo "document.getElementById('sy').value='".$b['cell_sy']."';\n";
									echo "document.getElementById('cx').value='".$b['cell_cx']."';\n";
									echo "document.getElementById('cy').value='".$b['cell_cy']."';\n";
									echo "document.getElementById('p').value='".$b['planet_solsys_pos']."';\n";
									echo "break;\n";
								}
								echo "
							}
	
						}
					}
					</script>";			
				}
				else
				{
					echo "<b>Diese Funktion wurde vor kurzem benutzt! Du musst bis ".df($werft_arr['buildlist_cooldown'])." warten, um die Funktion wieder zu benutzen!</b>";
				}				
			}
			else
			{
				echo "Dieses Gebäude ist noch bis ".df($werft_arr['buildlist_deactivated'])." deaktiviert!";
			}	
		}
		else
		{
			echo "Zu wenig Energie verfügbar! Gebäude ist deaktiviert!";
		}
	}
	else
	{
		// Titel
		echo "<h1>Kryptocenter des Planeten ".$c->name."</h1>";		
		
		// Ressourcen anzeigen
		$c->resBox();
		echo "<h2>Fehler!</h2>Das Cryptocenter wurde noch nicht gebaut!<br>";
	}
?>