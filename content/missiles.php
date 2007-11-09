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
	// 	File: missiles.php
	// 	Created: 01.12.2004
	// 	Last edited: 15.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Builds and launches missiles
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
  	buildlist_prod_percent
  FROM
  	".$db_table['buildlist']."
  WHERE
  	buildlist_planet_id='".$c->id."'
  	AND buildlist_building_id='".BUILD_MISSILE_ID."'
  	AND buildlist_current_level>='1'
  	AND buildlist_user_id='".$s['user']['id']."'");

  // Prüfen ob Gebäude gebaut ist
  if (mysql_num_rows($werft_res)>0)
  {
  	$werft_arr=mysql_fetch_array($werft_res);
  	$silo_level = $werft_arr['buildlist_current_level'];
		$max_space = $silo_level*MISSILE_SILO_MISSILES_PER_LEVEL;
		$max_flights = $silo_level*MISSILE_SILO_FLIGHTS_PER_LEVEL;
  	
		// Titel
		echo "<h1>Raketensilo (Stufe ".$silo_level.") des Planeten ".$c->name."</h1>";		
		
		// Ressourcen anzeigen
		$c->resBox();

		if ($c->prod->power - $c->use->power >= 0 && $c->prod->power>0 && $werft_arr['buildlist_prod_percent']==1)
		{
			if ($werft_arr['buildlist_deactivated'] < time())
			{
				
				// Requirements
				$rres = dbquery("
				SELECT 
					* 
				FROM 
					missile_requirements
				;");
				while ($rarr = mysql_fetch_array($rres))
				{
					if ($rarr['req_req_building_id']>0) 
					{
						$b_req[$rarr['req_missile_id']]['b'][$rarr['req_req_building_id']]=$rarr['req_req_building_level'];
					}
					
					if ($rarr['req_req_tech_id']>0) 
					{
						$b_req[$rarr['req_missile_id']]['t'][$rarr['req_req_tech_id']]=$rarr['req_req_tech_level'];
					}
				}
				// Gebäudeliste laden
				$sql ="
				SELECT 
					buildlist_current_level,
					buildlist_building_id
				FROM 
				"	.$db_table['buildlist']." 
				WHERE 
					buildlist_user_id='".$s['user']['id']."' 
					AND buildlist_planet_id='".$c->id."';";
				
				$blres = dbquery($sql);
				$builing_something=false;
				while ($blarr = mysql_fetch_array($blres))
				{
					$buildlist[$blarr['buildlist_building_id']]=$blarr['buildlist_current_level'];
				}		
				// Technologieliste laden
				$tres = dbquery("
				SELECT 
					techlist_tech_id,
					techlist_current_level 
				FROM 
					".$db_table['techlist']." 
				WHERE 
					techlist_user_id='".$s['user']['id']."'
				;");
				while ($tarr = mysql_fetch_array($tres))
				{
					$techlist[$tarr['techlist_tech_id']]=$tarr['techlist_current_level'];
				}				
						
			
				// Self destruct flight
				if (isset($_GET['selfdestruct']) && $_GET['selfdestruct']>0)
				{
					dbquery("
					DELETE FROM
						missile_flights
					WHERE
						flight_planet_from=".$c->id."
						AND flight_id=".intval($_GET['selfdestruct'])."
					;");
					if (mysql_affected_rows()>0)
					{
						dbquery("
						DELETE FROM
							missile_flights_obj
						WHERE
							obj_flight_id=".intval($_GET['selfdestruct'])."
						;");
						echo "Die Raketen haben sich selbst zerstört!<br/><br/>";
					}			
				}
		
				
				// Load missiles
				$missiles = array();
				$res = dbquery("
				SELECT
					*
				FROM
					missiles
				WHERE 
					missile_show=1
				ORDER BY
					missile_name
				;");
				$mc = mysql_num_rows($res);
				if ($mc > 0)
				{
					while ($arr=mysql_fetch_array($res))
					{
						$missiles[$arr['missile_id']]=$arr;
					}
				}
		
				// Load list
				$missilelist = array();
				$res = dbquery("
				SELECT
					missilelist_missile_id as mlid,
					missilelist_count as cnt
				FROM
					missilelist
				WHERE 
					missilelist_user_id=".$s['user']['id']."
					AND missilelist_planet_id=".$c->id."
				;");
				$cnt = 0;
				if (mysql_num_rows($res)>0)
				{
					while ($arr=mysql_fetch_array($res))
					{
						$missilelist[$arr['mlid']]=$arr['cnt'];
						$cnt += $arr['cnt'];
					}
				}
				
				// Launch missiles
				if (isset($_POST['launch']) && checker_verify() && $cnt > 0)
				{
					// Load missiles
					$launch = array();
					$lcnt = 0;
					foreach ($_POST['count'] as $k => $v)
					{				
						if ($v > 0)
						{
							if (isset($missilelist[$k]))
							{							
								$t = min($missilelist[$k],$v);
								if ($t > 0)
								{
									$launch[$k] = $t;
								}
							}
						}						
					}
					
					if (count($launch) > 0)
					{
						// Save flight
						dbquery("
						INSERT INTO
							missile_flights
						(
							flight_planet_from,
							flight_planet_to,
							flight_starttime,
							flight_landtime
						) VALUES (
							'".$c->id."',
							'".$_POST['targetplanet']."',
							UNIX_TIMESTAMP(),
							UNIX_TIMESTAMP()+".$_POST['timeforflight']."
						);");
						$fid = mysql_insert_id();
						foreach ($launch as $k => $v)
						{
							// Save flying missiles
							dbquery("
							INSERT INTO
								missile_flights_obj
							(
								obj_flight_id,
								obj_missile_id,
								obj_cnt
							) VALUES (
								".$fid.",
								".$k.",
								".$v."
							);");
							// Update list
							dbquery("
							UPDATE
								missilelist
							SET
								missilelist_count=missilelist_count-".$v."
							WHERE
								missilelist_user_id=".$s['user']['id']."
								AND missilelist_planet_id=".$c->id."							
								AND missilelist_missile_id=".$k."								
							;");				
							$missilelist[$k]-=$v;		
							$lcnt+=$v;	
						}						
						$cnt-=$lcnt;
						echo 'Gestartet!<br/><br/>';
					}
					else
					{
						echo 'Raketen konnten nicht gestartet werden, keine Raketen gewählt!<br/><br/>';
					}
				}		
				
				
				// Load flights
				$flights = array();
				$fcnt=0;
				$res = dbquery("
				SELECT
					flight_landtime,
					flight_id,
					planet_name,
					planet_id
				FROM
					missile_flights
				INNER JOIN
				(
					planets
				)
					ON flight_planet_to=planet_id
					AND flight_planet_from=".$c->id."
				;"); 
				if (mysql_num_rows($res)>0)
				{
					while ($arr=mysql_fetch_array($res))
					{
						$fcnt++;
						$flights[$arr['flight_id']]['landtime']=$arr['flight_landtime'];
						$flights[$arr['flight_id']]['planet_name']=$arr['planet_name'];
						$flights[$arr['flight_id']]['planet_id']=$arr['planet_id'];
						$flights[$arr['flight_id']]['obj']=array();
						$ores = dbquery("
						SELECT
							obj_cnt,
							missile_id,
							missile_name
						FROM
							missile_flights_obj
						INNER JOIN
							missiles
							ON missile_id=obj_missile_id
							AND obj_flight_id=".$arr['flight_id']."
						;");
						if (mysql_num_rows($ores)>0)
						{
							while ($oarr=mysql_fetch_array($ores))
							{
								$flights[$arr['flight_id']]['obj'][$oarr['missile_id']]['count']=$oarr['obj_cnt'];
								$flights[$arr['flight_id']]['obj'][$oarr['missile_id']]['name']=$oarr['missile_name'];
							}
						}
					}
				}
		
		
		
				// Kaufen
				if (isset($_POST['buy']) && checker_verify())
				{
					if (count($_POST['missile_count'])>0)
					{
						$buy=0;
						$valid=false;
						$buymissiles=array();
						foreach($_POST['missile_count'] as $k => $v)
						{
							$v = intval($v);
							if ($v > 0)
							{
								$valid=true;
								if ($v+$cnt <= $max_space)
								{
									$bc = $v;
								}
								else
								{
									$bc = $max_space-$cnt;
								}
								$bc = max($bc,0);
								if ($bc>0)
								{
									$buymissiles[$k]=$bc;
								}
								$cnt += $bc;
							}
						}
						
						if ($valid)
						{
							$bc = 0;
							foreach ($buymissiles as $k => $v)
							{
								$bc+=$v;
								
								$mcosts[0]=$missiles[$k]['missile_costs_metal']*$v;
								$mcosts[1]=$missiles[$k]['missile_costs_crystal']*$v;
								$mcosts[2]=$missiles[$k]['missile_costs_plastic']*$v;
								$mcosts[3]=$missiles[$k]['missile_costs_fuel']*$v;
								$mcosts[4]=$missiles[$k]['missile_costs_food']*$v;
								
								if ($c->res->metal >= $mcosts[0] &&
								$c->res->crystal >= $mcosts[1] &&
								$c->res->plastic >= $mcosts[2] &&
								$c->res->fuel >= $mcosts[3] &&
								$c->res->food >= $mcosts[4])
								{
									if (isset($missilelist[$k]))
									{
										dbquery("
										UPDATE
											missilelist
										SET
											missilelist_count=missilelist_count+".$v."
										WHERE
											missilelist_user_id=".$s['user']['id']."
											AND missilelist_planet_id=".$c->id."							
											AND missilelist_missile_id=".$k."
										");
										$missilelist[$k]+=$v;
									}
									else
									{
										dbquery("
										INSERT INTO
											missilelist
										(
											missilelist_user_id,
											missilelist_planet_id,
											missilelist_missile_id,
											missilelist_count
										) VALUES (
											".$s['user']['id'].",
											".$c->id.",
											".$k.",
											".$v."
										);");
										$missilelist[$k]=$v;
									}		
									$c->changeRes(-$mcosts[0],-$mcosts[1],-$mcosts[2],-$mcosts[3],-$mcosts[4]);	
									echo $v." ".$missiles[$k]['missile_name']." wurden gekauft!<br/><br/>";				
								}
								else
								{
									echo 'Konnte '.$missiles[$k]['missile_name'].' nicht kaufen, zu wenig Ressourcen!';
								}
							}
							if ($bc==0)
							{
								echo "Es konten keine Raketen gekauft werden, zuwenig Platz!<br/><br/>";				
							}					
						}
						else
						{
							echo "Keine oder ungültige Anzahl gewählt!<br/><br/>";				
						}
					}
					else
					{
						echo "Keine Raketen gewählt!<br/><br/>";				
					}			
				}
				
				// Remove
				if (isset($_POST['scrap']) && checker_verify())
				{
					if (count($_POST['missile_count'])>0)
					{			
						$buy=0;
						$valid=false;
						foreach($_POST['missile_count'] as $k => $v)
						{
							$v = intval($v);
							if ($v > 0)
							{
								$valid=true;
								$bc = min($v,$missilelist[$k]);
								dbquery("
								UPDATE
									missilelist
								SET
									missilelist_count=missilelist_count-".$bc."
								WHERE
									missilelist_user_id=".$s['user']['id']."
									AND missilelist_planet_id=".$c->id."							
									AND missilelist_missile_id=".$k."
								");
								$missilelist[$k]-=$bc;				
								$cnt-=$bc;		
								echo "$bc ".$missiles[$k]['missile_name']." wurden verschrottet!<br/><br/>";				
							}			
						}
						if (!$valid)	
						{
							echo "Keine oder ungültige Anzahl gewählt!<br/><br/>";				
						}
					}
					else
					{
						echo "Keine Raketen gewählt!<br/><br/>";				
					}				
				}
				
		
		  	$cstr = checker_init();
				// Flüge anzeigen
				if ($fcnt>0)
				{
					$time = time();
					echo '<h2>Abgefeuerte Raketen</h2>';
					echo '<table class="tb"><tr><th>Ziel</th><th>Flugdauer</th><th>Ankunfszeit</th><th>Raketen</th><th>Optionen</th></tr>';
					foreach ($flights as $flid => $fl)
					{
						$countdown = ($fl['landtime']-$time>=0) ? tf($fl['landtime']-$time) : 'Im Ziel';
						echo '<tr><td>'.$fl['planet_name'].'</td>
						<td>'.$countdown.'</td>
						<td>'.df($fl['landtime']).'</td>
						<td>';
						foreach ($fl['obj'] as $flo)
						{
							echo nf($flo['count']).' '.$flo['name'].'<br/>';
						}
						echo '</td>
						<td><a href="?page='.$page.'&amp;selfdestruct='.$flid.'" onclick="return confirm(\'Sollen die gewählten Raketen wirklich selbstzerstört werden?\')">Selbstzerstörung</a></td></tr>';
					}
					echo '</table><br/>';
				}
		
		
				// Raketen anzeigen
				if ($mc > 0)
				{					
					echo '<h2>Raketen verwalten</h2>';
					
				
					if ($max_space > 0)
					{
						$bar1_red = min(ceil($cnt / $max_space * 200),200);
					}
					else
					{
						$bar1_red = 0;
					}
					
					echo '<form action="?page='.$page.'" method="post">';
					echo $cstr;
					echo '<table class="tb">
					<tr><th colspan="2">Silobelegung:</th>
					<td style="padding:0px;vertical-align:middle;background:#000 url(\'images/poll2.jpg\') repeat-x 0px 4px;text-align:left;">
					<img src="images/poll4.jpg" alt="bar" style="width:'.$bar1_red.'px;height:10px;margin:0px;" />
					</td>
					<th colspan="7">
					'.nf($cnt).' von '.nf($max_space).', '.round($cnt/$max_space*100,0).'% ('.MISSILE_SILO_MISSILES_PER_LEVEL.' pro Gebäudestufe)
					</th>
					<tr><th colspan="2">Typ</th>
					<th style="width:200px;">Beschreibung</th>
					<th>'.RES_ICON_METAL.'</th>
					<th>'.RES_ICON_CRYSTAL.'</th>
					<th>'.RES_ICON_PLASTIC.'</th>
					<th>'.RES_ICON_FUEL.'</th>
					<th>'.RES_ICON_FOOD.'</th>
					<th>Vorhanden</th>
					<th>Auswahl</th>
					</tr>';
					foreach ($missiles as $mid => $arr)
					{
						
						// Check requirements for this building
						$requirements_passed = true;
						if (count($b_req[$mid]['b'])>0)
						{
							foreach ($b_req[$mid]['b'] as $b => $l)
							{
								if ($buildlist[$b] < $l)
								{
									$requirements_passed = false;
								}
							}
						}								
						if (count($b_req[$mid]['t'])>0)
						{
							foreach ($b_req[$mid]['t'] as $id => $l)
							{
								if ($techlist[$id] < $l)
								{
									$requirements_passed = false;
								}
							}
						}
						
						if ($requirements_passed)
						{					
							echo '<tr><td>
							<a href="?page=help&amp;site=missiles&amp;id='.$arr['missile_id'].'">
							<img alt="'.$arr['missile_name'].'" style="width:40px;height:40px;" src="'.IMAGE_PATH.'/missiles/missile'.$mid.'_small.'.IMAGE_EXT.'" />
							</a></td>
							<td>'.$arr['missile_name'];
							if ($arr['missile_def']>0)
							{
								echo '<br/><b>Sprengköpfe:</b> '.$arr['missile_def'];
							}					
							if ($arr['missile_damage']>0)
							{
								echo '<br/><b>Schaden:</b> '.$arr['missile_damage'];
							}					
							if ($arr['missile_deactivate']>0)
							{
								echo '<br/><b>EMP:</b> '.tf($arr['missile_deactivate']);
							}	
							if ($arr['missile_range']>0)
							{
								echo '<br/><b>Reichweite:</b> '.$arr['missile_range'].' AE';
							}
							echo '</td>
							<td>'.$arr['missile_sdesc'].'<br/>';
							if ($arr['missile_speed']>0 && $arr['missile_range']>0)
							{
								echo '<b>Geschwindigkeit:</b> '.$arr['missile_speed'].' AE/h';
							}
							echo '</td><td>'.nf($arr['missile_costs_metal']).'</td>
							<td>'.nf($arr['missile_costs_crystal']).'</td>
							<td>'.nf($arr['missile_costs_plastic']).'</td>
							<td>'.nf($arr['missile_costs_fuel']).'</td>
							<td>'.nf($arr['missile_costs_food']).'</td>
							<td>';
							if (isset($missilelist[$mid]))
							{
								echo nf($missilelist[$mid]);
							}
							else
							{
								echo 0;
							}
							echo '</td>
							<td><input type="text" name="missile_count['.$mid.']" value="0" size="3" maxlength="3" /></td></tr>';
						}
					}
					echo '</table><br/><input type="submit" name="buy" value="Ausgewählte Anzahl kaufen" /> &nbsp; ';
					echo '<input type="submit" name="scrap" value="Ausgewählte Anzahl verschrotten" onclick="return confirm(\'Sollen die gewählten Raketen wirklich verschrottet werden? Es werden keine Ressourcen zurückerstattet!\')" /></form><br/>';
					
					if ($cnt > 0)
					{
						echo '<h2>Raketen starten</h2>';
						if ($fcnt < $max_flights)
						{
							
							// Bookmarks laden
							$bookmarks=array();
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
							
							if ($_GET['c']!='' && $_GET['h']!='' && md5(base64_decode($_GET['c'])) == $_GET['h'])
							{
								$coords = explode(":",base64_decode($_GET['c']));
							}
							else
							{
								$coords[0] = $c->sx;
								$coords[1] = $c->sy;
								$coords[2] = $c->cx;
								$coords[3] = $c->cy;
								$coords[4] = $c->solsys_pos;
							}              
			                       
							$keyup_command = 'xajax_getFlightTargetInfo(xajax.getFormValues(\'targetForm\'),'.$c->sx.','.$c->sy.','.$c->cx.','.$c->cy.','.$c->solsys_pos.')';
							echo '<form action="?page='.$page.'" method="post" id="targetForm">';
							echo $cstr;
							echo '<table class="tb" style="width:700px;">
							<tr><th style="width:260px;">Raketen wählen</th><th colspan="2" style="width:440px;">Ziel wählen</th></tr>
							<tr><td rowspan="6">';
							$lblcnt=0;
							foreach ($missilelist as $k => $v)
							{
								if ($v > 0 && $missiles[$k]['missile_launchable']==1)
								{
									echo '<input type="hidden" value="'.$missiles[$k]['missile_speed'].'" name="speed['.$k.']" />';
									echo '<input type="hidden" value="'.$missiles[$k]['missile_range'].'" name="range['.$k.']" />';
									echo '<input type="text" value="0" name="count['.$k.']" size="3" maxlength="'.strlen($v).'"  onkeyup="'.$keyup_command.'" autocomplete="off" />
									'.$missiles[$k]['missile_name'].' ('.$v.' vorhanden)<br/>';
									$lblcnt++;
								}
							}
							if ($lblcnt==0)
							{
								echo 'Momentan befinden sich keine startbaren Raketen in deinem Silo!';
							}
							echo '</td><th>Koordinaten:</th>
							<td>
								<input type="text"  onkeyup="'.$keyup_command.'" name="sx" id="sx" value="'.$coords[0].'" size="2" autocomplete="off" maxlength="2" /> / 
								<input type="text"  onkeyup="'.$keyup_command.'" name="sy" id="sy" value="'.$coords[1].'" size="2" autocomplete="off" maxlength="2" /> :
								<input type="text"  onkeyup="'.$keyup_command.'" name="cx" id="cx" value="'.$coords[2].'" size="2" autocomplete="off" maxlength="2" /> /
								<input type="text"  onkeyup="'.$keyup_command.'" name="cy" id="cy" value="'.$coords[3].'" size="2" autocomplete="off" maxlength="2" /> :
								<input type="text"  onkeyup="'.$keyup_command.'" name="p" id="p" value="'.$coords[4].'" size="2" autocomplete="off" maxlength="2" />
							</td></tr>';
							
							// Bookmarkliste anzeigen
							echo "<tr><th>Favorit wählen:</th><td><select id=\"bookmarkselect\" onchange=\"applyBookmark();\">";
							if (count($bookmarks)>0)
							{
								$a=1;
								echo "<option value=\"\">W&auml;hlen...</option>";
								foreach ($bookmarks as $i=> $b)
								{
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
							echo "</select></td></tr>";								
							
							echo '<tr><th>Zielinfo:</th><td id="targetinfo">
							Wähle bitte ein Ziel...
							</td></tr>				
							<tr><th>Entfernung:</th><td id="distance">
							-
							</td></tr>				
							<tr><th>Geschwindigkeit:</th><td id="speed">
							-
							</td></tr>				
							<tr><th>Zeit:</th><td id="time">
							-
							</td></tr>				
							</table><br/><input style="color:#f00" type="submit" name="launch" id="launchbutton" value="Starten" disabled="disabled" />';
							echo '<input type="hidden" name="timeforflight" value="0" id="timeforflight" />
							<input type="hidden" name="targetcell" value="0" id="targetcell" />
							<input type="hidden" name="targetplanet" value="0" id="targetplanet" /></form>';
							echo '<script type="text/javascript">'.$keyup_command.'</script>';
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
								".$keyup_command."
							}
							</script>";								
							
						}
						else
						{
							echo "Baue zuerst dein Raketensilo aus um mehr Raketen zu starten (".MISSILE_SILO_FLIGHTS_PER_LEVEL." Angriff pro Stufe)!";
						}
					}
				}
				else
				{
					echo 'Keine Raketen verfügbar!';
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
		echo "<h1>Raketensilo des Planeten ".$c->name."</h1>";		
		
		// Ressourcen anzeigen
		$c->resBox();
		echo "<h2>Fehler!</h2>Das Raketensilo wurde noch nicht gebaut!<br>";
	}

?>
