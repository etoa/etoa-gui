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
  	buildlist_planet_id='".$cp->id()."'
  	AND buildlist_building_id='".BUILD_CRYPTO_ID."'
  	AND buildlist_current_level>='1'
  	AND buildlist_user_id='".$cu->id()."'");

  // Prüfen ob Gebäude gebaut ist
  if (mysql_num_rows($werft_res)>0)
  {
  	$werft_arr=mysql_fetch_assoc($werft_res);
  	$center_level = $werft_arr['buildlist_current_level'];
  	
		// Titel
		echo "<h1>Kryptocenter (Stufe ".$center_level.") des Planeten ".$cp->name."</h1>";		
		
		// Ressourcen anzeigen
		$cp->resBox();

		if ($cp->prodPower - $cp->usePower >= 0  && $cp->prodPower>0 && $werft_arr['buildlist_prod_percent']==1)
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
						if ($cp->resFuel >= CRYPTO_FUEL_COSTS_PER_SCAN)
						{
							$target = Entity::createFactoryByCoords($sx,$sy,$cx,$cy,$pp);
							if (true)
							{
								$dist = $cp->distance($target);
								if ($dist <= CRYPTO_RANGE_PER_LEVEL*$center_level)
								{									
									$cp->changeRes(0,0,0,-CRYPTO_FUEL_COSTS_PER_SCAN,0);

									if ($target->ownerId() > 0)
									{									
										send_msg($arr['user_id'],SHIP_SPY_MSG_CAT_ID,"Funkstörung","Eure Flottenkontrolle hat soeben eine kurzzeitige Störung des Kommunikationsnetzes festgestellt.");
									}
									
									$out="[b]Flottenscan vom Planeten ".$target."\n
									[b]Zeit:[/b] ".date("d.m.Y H:i:s")."\n\n";
									$out.="[b]Eintreffende Flotten[/b]\n\n";
									$fres = dbquery("
									SELECT
										fleet_landtime,
										fleet_action,
										fleet_entity_from,
										user_nick
									FROM
										fleet
									LEFT JOIN
										users
										ON fleet_user_id=user_id
									WHERE
										fleet_entity_to=".$target->id()."
									");
									if (mysql_num_rows($fres)>0)
									{
										require("inc/fleet_action.inc.php");
										while ($farr=mysql_fetch_assoc($fres))
										{
											$source = Entity::createFactoryById($farr['fleet_entity_from']);
											
											$out.='[b]Herkunft:[/b] '.$source.'), [b]Besitzer:[/b] '.$farr['user_nick'];
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
												while ($sarr=mysql_fetch_assoc($sres))
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
									
									/*
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
										while ($farr=mysql_fetch_assoc($fres))
										{
											$out.='[b]Ziel:[/b] '.$farr['planet_name'].' ('.$farr['cell_sx'].'/'.$farr['cell_sy'].' : '.$farr['cell_cx'].'/'.$farr['cell_cy'].' : '.$farr['planet_solsys_pos'].'), [b]Besitzer:[/b] '.$farr['user_nick'];
											$out.= "\n[b]Ankunft:[/b] ".df($farr['fleet_landtime']).", [b]Aktion:[/b] ".get_fleet_action($farr['fleet_action'])."\n\n";
										}
									}								
									else
									{
										$out.='Keine wegfliegenden Flotten gefunden!';
									}*/
									
									infobox_start("Ergebnis der Analyse");
									echo text2html($out);
									infobox_end();
									
									// Add note to user's notepad if selected
									if (isset($_POST['scan_to_notes']))
									{
										$np = new Notepad($cu->id());
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
								  	buildlist_planet_id='".$cp->id()."'
								  	AND buildlist_building_id='".BUILD_CRYPTO_ID."'
								  	AND buildlist_user_id='".$cu->id()."'");
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
									echo "Das Ziel ist zu weit entfernt (".ceil($dist)." AE, momentan sind ".CRYPTO_RANGE_PER_LEVEL*$center_level." möglich, ".CRYPTO_RANGE_PER_LEVEL." pro Gebäudestufe)!<br/><br/>";
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
					if (isset($_GET['target']) && $_GET['target']>0)
					{
						$ent = Entity::createFactoryById($_GET['target']);
						$coords = $ent->coordsArray();
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
						$coords[0] = $cp->sx;
						$coords[1] = $cp->sy;
						$coords[2] = $cp->cx;
						$coords[3] = $cp->cy;
						$coords[4] = $cp->pos;
					}  		
					echo '<form action="?page='.$page.'" method="post">';		
					checker_init();
					infobox_start("Ziel für Flottenanalyse wahlen:");
	
					//
					// Bookmarks laden
					//

					$bm = new BookmarkManager($cu->id());
					
					echo 'Koordinaten eingeben: 
						<input type="text" name="sx" id="sx" value="'.$coords[0].'" size="2" maxlength="2" /> / 
						<input type="text" name="sy" id="sy" value="'.$coords[1].'" size="2" maxlength="2" /> :
						<input type="text" name="cx" id="cx" value="'.$coords[2].'" size="2" maxlength="2" /> /
						<input type="text" name="cy" id="cy" value="'.$coords[3].'" size="2" maxlength="2" /> :
						<input type="text" name="p" id="p" value="'.$coords[4].'" size="2" maxlength="2" /><br/><br/>';
					
					// Bookmarkliste anzeigen
					echo "<i>oder</i> Favorit wählen: ".$bm->drawSelector("bookmarkselect","applyBookmark();")."<br/><br/>
					<input type=\"checkbox\" name=\"scan_to_notes\" value=\"1\" checked=\"checked\" /> Zu meinem Notizblock hinzufügen";					
					echo $bm->drawSelectorJavaScript("bookmarkselect","applyBookmark");
					infobox_end();
					if ($cp->resFuel >= CRYPTO_FUEL_COSTS_PER_SCAN)
					{
						echo '<input type="submit" name="scan" value="Analyse für '.nf(CRYPTO_FUEL_COSTS_PER_SCAN).' '.RES_FUEL.' starten" />';
					}
					else
					{
						echo "Zuwenig Rohstoffe für eine Analyse vorhanden, ".nf(CRYPTO_FUEL_COSTS_PER_SCAN)." ".RES_FUEL." benötigt!";
					}
					echo '</form>';				
					
		
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
		echo "<h1>Kryptocenter des Planeten ".$cp->name."</h1>";		
		
		// Ressourcen anzeigen
		$cp->resBox();
		echo "<h2>Fehler!</h2>Das Cryptocenter wurde noch nicht gebaut!<br>";
	}
?>