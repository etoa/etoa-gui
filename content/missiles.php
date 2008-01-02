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

	// Info-Link
	define("HELP_URL","?page=help&site=missiles");

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
					
					// Rechnet %-Werte für Tabelle
					$store_width = ceil($cnt/$max_space*100);
					
					infobox_start("Silobelegung",1);
					echo '<tr>
									<td class="tbldata" style="padding:0px;height:10px;"><img src="images/poll3.jpg" style="height:10px;width:'.$store_width.'%;" alt="poll" />
								</tr>
								<tr>
									<td class="tbldata" style="text-align:center;">
										'.nf($cnt).' von '.nf($max_space).', '.round($cnt/$max_space*100,0).'% ('.MISSILE_SILO_MISSILES_PER_LEVEL.' pro Gebäudestufe)
								</tr>';
					infobox_end(1);
					
					infobox_start("Raketen verwalten",1);
					
					$cnt2 = 0;
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
							//Errechnet wie viele Raketen von diesem Typ maximal gekauft werden können mit den aktuellen Rohstoffen
							
							// Silokapazität
							$store = $max_space - $cnt;
														
							//Titan
							if($arr['missile_costs_metal']>0)
							{
								$build_cnt_metal=floor($c->res->metal/$arr['missile_costs_metal']);
							}
							else
							{
								$build_cnt_metal=99999999999;
							}

							//Silizium
							if($arr['missile_costs_crystal']>0)
							{
								$build_cnt_crystal=floor($c->res->crystal/$arr['missile_costs_crystal']);
							}
							else
							{
								$build_cnt_crystal=99999999999;
							}
					
							//PVC
							if($arr['missile_costs_plastic']>0)
							{
								$build_cnt_plastic=floor($c->res->plastic/$arr['missile_costs_plastic']);
							}
							else
							{
								$build_cnt_plastic=99999999999;
							}
							
							//Tritium
							if($arr['missile_costs_fuel']>0)
							{
								$build_cnt_fuel=floor($c->res->fuel/$arr['missile_costs_fuel']);
							}
							else
							{
								$build_cnt_fuel=99999999999;
							}

							//Nahrung
							if($arr['missile_costs_food']>0)
							{
								$build_cnt_food=floor($c->res->food/$arr['missile_costs_food']);
							}
							else
							{
								$build_cnt_food=99999999999;
							}

							//Effetiv max. kaufbare Raketen in Betrachtung der Rohstoffe und der Silokapazität
							$missile_max_build=min($build_cnt_metal,$build_cnt_crystal,$build_cnt_plastic,$build_cnt_fuel,$build_cnt_food,$store);
							
							// Grösste Zahl die eingegeben werden kann (Da man auch verschrotten kann)
							$missile_max_number = max($missile_max_build,$missilelist[$mid]);

							//Tippbox Nachricht generieren
							//X Anlagen baubar
							if($missile_max_build>0)
							{
								$tm_cnt="Es k&ouml;nnen maximal ".nf($missile_max_build)." Raketen gekauft werden.";
							}
							//Zu wenig Felder.
							elseif($store==0)
							{
								$tm_cnt="Das Silo ist zu klein für weitere Raketen!";
							}
							//Zuwenig Rohstoffe. Wartezeit errechnen
							elseif($missile_max_build==0 && $store!=0)
							{
								//Wartezeit Titan
  			    		if ($c->prod->metal>0)
  			    		{
  			    			$bwait['metal']=ceil(($arr['missile_costs_metal']-$c->res->metal)/$c->prod->metal*3600);
  			    		}
  			    		else
  			    		{
  			    			$bwait['metal']=0;
  			    		}
  			    		
  			    		//Wartezeit Silizium
  			    		if ($c->prod->crystal>0)
  			    		{
  			    			$bwait['crystal']=ceil(($arr['missile_costs_crystal']-$c->res->crystal)/$c->prod->crystal*3600);
  			    		}
  			    		else
  			    		{ 
  			    			$bwait['crystal']=0;
  			    		}
  			    		
  			    		//Wartezeit PVC
  			    		if ($c->prod->plastic>0)
  			    		{
  			    			$bwait['plastic']=ceil(($arr['missile_costs_plastic']-$c->res->plastic)/$c->prod->plastic*3600);
  			    		}
  			    		else
  			    		{ 
  			    			$bwait['plastic']=0;
  			    		}
  			    		
  			    		//Wartezeit Tritium
  			    		if ($c->prod->fuel>0)
  			    		{
  			    			$bwait['fuel']=ceil(($arr['missile_costs_fuel']-$c->res->fuel)/$c->prod->fuel*3600);
  			    		}
  			    		else
  			    		{ 
  			    			$bwait['fuel']=0;
  			    		}
  			    		
  			    		//Wartezeit Nahrung
  			    		if ($c->prod->food>0)
  			    		{
  			    			$bwait['food']=ceil(($arr['missile_costs_food']-$c->res->food)/$c->prod->food*3600);
  			    		}
  			    		else
  			    		{ 
  			    			$bwait['food']=0;
  			    		}
  			    		
  			    		//Maximale Wartezeit ermitteln
  			    		$bwmax=max($bwait['metal'],$bwait['crystal'],$bwait['plastic'],$bwait['fuel'],$bwait['food']);
  			    		
  			    		$tm_cnt="Rohstoffe verf&uuml;gbar in ".tf($bwmax)."";
							}
							else
							{
								$tm_cnt="";
							}

							//Stellt Rohstoff Rot dar, wenn es von diesem zu wenig auf dem Planeten hat
							//Titan
							if($arr['missile_costs_metal']>$c->res->metal)
							{
								$ress_style_metal="style=\"color:red;\"";
							}
							else
							{
								$ress_style_metal="";
							}
							
							//Silizium
							if($arr['missile_costs_crystal']>$c->res->crystal)
							{
								$ress_style_crystal="style=\"color:red;\"";
							}
							else
							{
								$ress_style_crystal="";
							}
							
							//PVC
							if($arr['missile_costs_plastic']>$c->res->plastic)
							{
								$ress_style_plastic="style=\"color:red;\"";
							}
							else
							{
								$ress_style_plastic="";
							}
							
							//Tritium
							if($arr['missile_costs_fuel']>$c->res->fuel)
							{
								$ress_style_fuel="style=\"color:red;\"";
							}
							else
							{
								$ress_style_fuel="";
							}
							
							//Nahrung
							if($arr['missile_costs_food']>$c->res->food)
							{
								$ress_style_food="style=\"color:red;\"";
							}
							else
							{
								$ress_style_food="";
							}

							
							// Volle Ansicht
			      	if($s['user']['item_show']=='full')
			      	{	
			      		if ($cnt2>0)
  			      	{
  			      			echo "<tr>
  			      							<td colspan=\"5\" style=\"height:5px;\"></td>
  			      					</tr>";
  			      	}
  			      					      		
			      	  $d_img = IMAGE_PATH.'/missiles/missile'.$mid.'_middle.'.IMAGE_EXT;
			      	  echo "<tr>
			      	  				<td class=\"tbltitle\" colspan=\"5\">".$arr['missile_name']."</td>
			      	  			</tr>
			      	  			<tr>
			      	  				<td class=\"tbldata\" width=\"120\" height=\"120\" rowspan=\"5\">";
			    			      	  //Bild mit Link zur Hilfe darstellen
													echo "<a href=\"".HELP_URL."&amp;id=".$mid."\" title=\"Info zu dieser Rakete anzeigen\">
		    			      	  	<img src=\"".$d_img."\" width=\"120\" height=\"120\" border=\"0\" /></a>";
			      	  	echo "</td>
			      	  				<td class=\"tbldata\" colspan=\"4\" valign=\"top\">".$arr['missile_sdesc']."</td>
			      	  			</tr>
			      	  			<tr>
		    			      		<th class=\"tbltitle\">Geschwindigkeit:</th>
		    			      	  <td class=\"tbldata\">";
		    			      	  if($arr['missile_speed']>0)
		    			      	  {
		    			      	  	echo "".nf($arr['missile_speed'])."";
		    			      	  }
		    			      	  else
		    			      	  {
		    			      	  	echo "-";
		    			      	  }
		    			    echo "</td>
		    			      	  <th class=\"tbltitle\" rowspan=\"2\">Vorhanden:</th>
		    			      	  <td class=\"tbldata\" rowspan=\"2\">".nf($missilelist[$mid])."</td>
		    			      	</tr>
		    			      	<tr>
		    			      		<th class=\"tbltitle\">Reichweite:</th>
		    			      	  <td class=\"tbldata\">";
		    			      	  if($arr['missile_range']>0)
		    			      	  {
		    			      	  	echo "".nf($arr['missile_range'])." AE";
		    			      	  }
		    			      	  else
		    			      	  {
		    			      	  	echo "-";
		    			      	  }
		    			    echo "</td>
		    			      	</tr>
		    			      	<tr>
		    			      		<th class=\"tbltitle\">";
		    			      	
			    			      	if ($arr['missile_def']>0)
												{
													echo "Sprengköpfe";
												}
												elseif ($arr['missile_damage']>0)
												{
													echo "Schaden";
												}
												elseif($arr['missile_deactivate']>0)	
												{
													echo "Schaden";
												}
												
		    			    echo "</th>
			      	  				<td class=\"tbldata\">"; 
			      	  				
			      	  				if ($arr['missile_def']>0)
												{
													echo nf($arr['missile_def']);
												}
												elseif ($arr['missile_damage']>0)
												{
													echo nf($arr['missile_damage']);
												}
												else
												{
													echo "0";
												}
												
			      	  	echo "</td>
			      	  				<th class=\"tbltitle\" rowspan=\"2\">Kaufen:</th>
    			      	      <td class=\"tbldata\" rowspan=\"2\">
			      							<input type=\"text\" value=\"0\" id=\"missile_count_".$mid."\" name=\"missile_count[".$mid."]\" size=\"5\" maxlength=\"9\" ".tm("",$tm_cnt)." tabindex=\"".$tabulator."\" onkeyup=\"FormatNumber(this.id,this.value, ".$missile_max_number.", '', '');\"/><br><a href=\"javascript:;\" onclick=\"document.getElementById('missile_count_".$mid."').value=".$missile_max_build.";\">max</a>
    			      	      </td>";
	      	      echo "<tr>
	      	      				<th class=\"tbltitle\">EMP:</th>
	      	      				<td class=\"tbldata\">";
	      	      				if($arr['missile_deactivate']>0)	
												{
													echo tf($arr['missile_deactivate']);
												}
												else
												{
													echo "Nein";
												}
									echo "</td>
											</tr>";
		    			      	
			      	  echo "</tr>";
			      	  echo "<tr>
		    			      	  <td class=\"tbltitle\" height=\"20\" width=\"110\">".RES_METAL.":</td>
		    			      	  <td class=\"tbltitle\" height=\"20\" width=\"97\">".RES_CRYSTAL.":</td>
		    			      	  <td class=\"tbltitle\" height=\"20\" width=\"98\">".RES_PLASTIC.":</td>
		    			      	  <td class=\"tbltitle\" height=\"20\" width=\"97\">".RES_FUEL.":</td>
		    			      	  <td class=\"tbltitle\" height=\"20\" width=\"98\">".RES_FOOD."</td></tr>";
			      	  echo "<tr>
			      	  				<td class=\"tbldata\" height=\"20\" width=\"110\" ".$ress_style_metal.">
			      	  					".nf($arr['missile_costs_metal'])."
			      	  				</td>
			      	  				<td class=\"tbldata\" height=\"20\" width=\"25%\" ".$ress_style_crystal.">
			      	  					".nf($arr['missile_costs_crystal'])."
			      	  				</td>
			      	  				<td class=\"tbldata\" height=\"20\" width=\"25%\" ".$ress_style_plastic.">
			      	  					".nf($arr['missile_costs_plastic'])."
			      	  				</td>
			      	  				<td class=\"tbldata\" height=\"20\" width=\"25%\" ".$ress_style_fuel.">
			      	  					".nf($arr['missile_costs_fuel'])."
			      	  				</td>
			      	  				<td class=\"tbldata\" height=\"20\" width=\"25%\" ".$ress_style_food.">
			      	  					".nf($arr['missile_costs_food'])."
			      	  				</td>
			      	  			</tr>";
			      	}
			      	
			      	//Einfache Ansicht der Schiffsliste
			      	else
			      	{
		      			$d_img = IMAGE_PATH.'/missiles/missile'.$mid.'_middle.'.IMAGE_EXT;
		      			echo "<tr>
		      							<td class=\"tbldata\">";
			      							//Bild mit Link zur Hilfe darstellen
		  			      				echo "<a href=\"".HELP_URL."&amp;id=".$mid."\"><img src=\"".$d_img."\" width=\"40\" height=\"40\" border=\"0\" /></a></td>";
			      			echo "<td class=\"tbltitle\" width=\"40%\">
			      							".$arr['missile_name']."<br/>
			      							<span style=\"font-weight:500;font-size:8pt;\">
			      							<b>Vorhanden:</b> ".nf($missilelist[$mid])."</span></td>
			      						<td class=\"tbldata\" width=\"10%\" ".$ress_style_metal.">".nf($arr['missile_costs_metal'])."</td>
			      						<td class=\"tbldata\" width=\"10%\" ".$ress_style_crystal.">".nf($arr['missile_costs_crystal'])."</td>
			      						<td class=\"tbldata\" width=\"10%\" ".$ress_style_plastic.">".nf($arr['missile_costs_plastic'])."</td>
			      						<td class=\"tbldata\" width=\"10%\" ".$ress_style_fuel.">".nf($arr['missile_costs_fuel'])."</td>
			      						<td class=\"tbldata\" width=\"10%\" ".$ress_style_food.">".nf($arr['missile_costs_food'])."</td>
			      						<td class=\"tbldata\">
			      							<input type=\"text\" value=\"0\" id=\"missile_count_".$mid."\" name=\"missile_count[".$mid."]\" size=\"5\" maxlength=\"9\" ".tm("",$tm_cnt)." tabindex=\"".$tabulator."\" onkeyup=\"FormatNumber(this.id,this.value, ".$missile_max_number.", '', '');\"/><br><a href=\"javascript:;\" onclick=\"document.getElementById('missile_count_".$mid."').value=".$missile_max_build.";\">max</a>
			      						</td>
			      					</tr>";
			      	}
			      
			      	$tabulator++;
			      	$cnt2++;
							
						}							
					}
					infobox_end(1);
					echo '<br/><input type="submit" name="buy" value="Ausgewählte Anzahl kaufen" /> &nbsp; ';
					echo '<input type="submit" name="scrap" value="Ausgewählte Anzahl verschrotten" onclick="return confirm(\'Sollen die gewählten Raketen wirklich verschrottet werden? Es werden keine Ressourcen zurückerstattet!\')" /></form><br/><br><br>';
					
					if ($cnt > 0)
					{
						echo '<h2>Raketen starten</h2>';
						
						// Kampfsperre prüfen
						if ($conf['battleban']['v']!=0 && $conf['battleban_time']['p1']<=time() && $conf['battleban_time']['p2']>time())
						{
							infobox_start("Kampfsperre");
							echo "Es ist momentan nicht m&ouml;glich andere Spieler anzugreifen. Grund: ".text2html($conf['battleban']['p1'])."<br>Die Sperre dauert vom ".date("d.m.Y",$conf['battleban_time']['p1'])." um ".date("H:i",$conf['battleban_time']['p1'])." Uhr bis am ".date("d.m.Y",$conf['battleban_time']['p2'])." um ".date("H:i",$conf['battleban_time']['p2'])." Uhr!";
							infobox_end();
						}
						else
						{
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
