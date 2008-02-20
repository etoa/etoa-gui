<?PHP


	// Aktions Liste vom 07.06.2006

    $fleet_actions['po']="Stationieren";
    $fleet_actions['poc']="Stationieren (Abgebrochen)";
    $fleet_actions['mto']="Transport";
    $fleet_actions['mpo']="Stationieren";
    $fleet_actions['to']="Transport (Hinflug)";
    $fleet_actions['tr']="Transport (R&uuml;ckflug)";
    $fleet_actions['toc']="Transport (Abgebrochen)";
    $fleet_actions['so']="Spionieren (Hinflug)";
    $fleet_actions['sr']="Spionieren (R&uuml;ckflug)";
    $fleet_actions['soc']="Spionieren (Abgebrochen)";
    $fleet_actions['ao']="Angreifen (Hinflug)";
    $fleet_actions['ar']="Angreifen (R&uuml;ckflug)";
    $fleet_actions['aoc']="Angreifen (Abgebrochen)";
    $fleet_actions['mo']="Marktlieferung";
    $fleet_actions['ko']="Kolonie errichten";
    $fleet_actions['koc']="Kolonie errichten (Abgebrochen)";
    $fleet_actions['io']="Invasieren";
    $fleet_actions['ir']="Invasieren (R&uuml;ckflug)";
    $fleet_actions['ioc']="Invasieren (Abgebrochen)";
    $fleet_actions['bo']="Bombadieren";
    $fleet_actions['br']="Bombadieren (R&uuml;ckflug)";
    $fleet_actions['boc']="Bombadieren (Abgebrochen)";
    $fleet_actions['do']="EMP";
    $fleet_actions['dr']="EMP (R&uuml;ckflug)";
    $fleet_actions['doc']="EMP (Abgebrochen)";
    $fleet_actions['xo']="Giftgas";
    $fleet_actions['xr']= "Giftgas (R&uuml;ckflug)";
    $fleet_actions['xoc']="Giftgas (Abgebrochen)";
    $fleet_actions['ho']="Antrax";
    $fleet_actions['hr']="Antrax (R&uuml;ckflug)";
    $fleet_actions['hoc']="Antrax (Abgebrochen)";
    $fleet_actions['vo']="Tarnangriff";
    $fleet_actions['vr']="Tarnangriff (R&uuml;ckflug)";
    $fleet_actions['voc']="Tarnangriff (Abgebrochen)";
    $fleet_actions['eo']="Angriff (fake)";
    $fleet_actions['er']="Angriff (fake)(R&uuml;ckflug)";
    $fleet_actions['eoc']="Angriff (fake)(Abgebrochen)";
    $fleet_actions['lo']="Spionageangriff";
    $fleet_actions['lr']="Spionageangriff (R&uuml;ckflug)";
    $fleet_actions['loc']="Spionageangriff (Abgebrochen)";
    $fleet_actions['zo']="Tr&uuml;mmerfeld erstellen";
    $fleet_actions['zr']="Tr&uuml;mmerfeld erstellen (R&uuml;ckflug)";
    $fleet_actions['zoc']="Tr&uuml;mmerfeld erstellen (Abgebrochen)";
    $fleet_actions['wo']="Tr&uuml;mmer sammeln";
    $fleet_actions['wr']="Tr&uuml;mmer sammeln (R&uuml;ckflug)";
    $fleet_actions['woc']="Tr&uuml;mmer sammeln (Abgebrochen)";
    $fleet_actions['go']="Gas saugen";
    $fleet_actions['gr']="Gas saugen (R&uuml;ckflug)";
    $fleet_actions['goc']="Gas saugen (Abgebrochen)";
    $fleet_actions['yo']="Asteroiden sammeln";
    $fleet_actions['yr']="Asteroiden sammeln (R&uuml;ckflug)";
    $fleet_actions['yoc']="Asteroiden sammeln (Abgebrochen)";
    $fleet_actions['no']="Nebel erkunden";
    $fleet_actions['nr']="Nebel erkunden (R&uuml;ckflug)";
    $fleet_actions['noc']="Nebel erkunden (Abgebrochen)";
    $fleet_actions['fo']="Flug";
    $fleet_actions['fr']="R&uuml;ckflug";
    $fleet_actions['foc']="Flug (Abgebrochen)";


	//
	// Flottenoptionen
	//
	if ($sub=="fleetoptions")
	{
		echo "<h1>Flottenoptionen</h1>";


		//
		// Updates
		//
		
		// Flottensperre deaktivieren
		if ($_POST['flightban_deactivate']!="")
		{
			dbquery("
			UPDATE 
				".$db_table['config']." 
			SET 
				config_value=0,
				config_param1='' 
			WHERE 
				config_name='flightban';");
				
			$conf['flightban']['v']=0;
		}
		
		// Flottensperre aktivieren
		if ($_POST['flightban_activate']!="")
		{
			$flightban_from = mktime($_POST['flightban_time_from_h'],$_POST['flightban_time_from_i'],0,$_POST['flightban_time_from_m'],$_POST['flightban_time_from_d'],$_POST['flightban_time_from_y']);
			$flightban_to = mktime($_POST['flightban_time_to_h'],$_POST['flightban_time_to_i'],0,$_POST['flightban_time_to_m'],$_POST['flightban_time_to_d'],$_POST['flightban_time_to_y']);
			
			if($flightban_from < $flightban_to)
			{
				dbquery("
				UPDATE 
					".$db_table['config']." 
				SET 
					config_param1='".$flightban_from."',
					config_param2='".$flightban_to."' 
				WHERE 
					config_name='flightban_time';");
					
				dbquery("
				UPDATE 
					".$db_table['config']." 
				SET 
					config_value=1,
					config_param1='".addslashes($_POST['flightban_reason'])."' 
				WHERE 
					config_name='flightban';");
					
				$conf['flightban']['v']=1;
				$conf['flightban']['p1']=addslashes($_POST['flightban_reason']);
				$conf['flightban_time']['p1']=$flightban_from;
				$conf['flightban_time']['p2']=$flightban_to;
			}
			else
			{
				echo "<b>Fehler:</b> Das Ende muss nach dem Start erfolgen!<br><br>";
			}
		}		

		// Kampfsperre deaktivieren
		if ($_POST['battleban_deactivate']!="")
		{
			dbquery("
			UPDATE 
				".$db_table['config']." 
			SET 
				config_value=0,
				config_param1='' 
			WHERE 
				config_name='battleban';");
				
			$conf['battleban']['v']=0;
		}
		
		// Kampfsperre aktivieren
		if ($_POST['battleban_activate']!="")
		{
			$battleban_from = mktime($_POST['battleban_time_from_h'],$_POST['battleban_time_from_i'],0,$_POST['battleban_time_from_m'],$_POST['battleban_time_from_d'],$_POST['battleban_time_from_y']);
			$battleban_to = mktime($_POST['battleban_time_to_h'],$_POST['battleban_time_to_i'],0,$_POST['battleban_time_to_m'],$_POST['battleban_time_to_d'],$_POST['battleban_time_to_y']);
			
			if($battleban_from < $battleban_to)
			{			
				
				dbquery("
				UPDATE 
					".$db_table['config']." 
				SET 
					config_value=1,
					config_param1='".addslashes($_POST['battleban_reason'])."'
				WHERE 
					config_name='battleban';");
					
				dbquery("
				UPDATE 
					".$db_table['config']." 
				SET 
					config_param1='".addslashes($_POST['battleban_arrival_text_fleet'])."',
					config_param2='".addslashes($_POST['battleban_arrival_text_missiles'])."'
				WHERE 
					config_name='battleban_arrival_text';");
					
				dbquery("
				UPDATE 
					".$db_table['config']." 
				SET 
					config_param1='".$battleban_from."',
					config_param2='".$battleban_to."' 
				WHERE 
					config_name='battleban_time';");
					
				$conf['battleban']['v']=1;
				$conf['battleban']['p1']=addslashes($_POST['battleban_reason']);
				$conf['battleban_arrival_text']['p1']=addslashes($_POST['battleban_arrival_text_fleet']);
				$conf['battleban_arrival_text']['p2']=addslashes($_POST['battleban_arrival_text_missiles']);
				$conf['battleban_time']['p1']=$battleban_from;
				$conf['battleban_time']['p2']=$battleban_to;

			}
			else
			{
				echo "<b>Fehler:</b> Das Ende muss nach dem Start erfolgen!<br><br>";
			}
		}




		//
		// Flottensperre
		//
		/*
		echo "<h2>Flottensperre</h2>";
		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		
		if ($_POST['flightban_deactivate']!="")
		{
			dbquery("
			UPDATE 
				".$db_table['config']." 
			SET 
				config_value=0,
				config_param1='' 
			WHERE 
				config_name='flightban';");
				
			$conf['flightban']['v']=0;
		}
		
		if ($_POST['flightban_activate']!="")
		{
			dbquery("
			UPDATE 
				".$db_table['config']." 
			SET 
				config_value=1,
				config_param1='".addslashes($_POST['ban_reason'])."' 
			WHERE 
				config_name='flightban';");
				
			$conf['flightban']['v']=1;
			$conf['flightban']['p1']=addslashes($_POST['ban_reason']);
		}
		
		if ($conf['flightban']['v']==1)
		{
			echo "<div style=\"color:#f90\">Die Flottensperre ist aktiviert! Es k&ouml;nnen keine Fl&uuml;ge gestartet werden!</div><br/>Grund: ".text2html($conf['flightban']['p1'])."<br/><br/><input type=\"submit\" name=\"flightban_deactivate\" value=\"Deaktivieren\" />";
		}
		else
		{
			echo "<div style=\"color:#0f0\">Die Flottensperre ist deaktiviert!</div><br/>";
			echo "Aktivierungsgrund:<br/><textarea name=\"ban_reason\" cols=\"50\" rows=\"3\"></textarea><br/><br/>";
			echo "Von: ";
			echo show_timebox("battleban_time_from",time());
			echo "<br>Bis: ";
			echo show_timebox("battleban_time_to",time());
			echo "<br><br><input type=\"submit\" name=\"flightban_activate\" value=\"Aktivieren\" />";
		}
		
		echo "</form><br/><br/>";
		*/
		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		
		//
		// Flottensperre
		//
		
		// Setzt Variabeln wenn Flottensperre aktiv...
		if ($conf['flightban']['v']==1)
		{
			// Prüft, ob die Sperre zum jetzigen Zeitpunkt gilt
			if($conf['flightban_time']['p1']<=time() && $conf['flightban_time']['p2']>=time())
			{
				$flightban_time_status = "Sie wirkt zum jetzigen Zeitpunkt!";
			}
			elseif($conf['flightban_time']['p1']>time() && $conf['flightban_time']['p2']>time())
			{
				$flightban_time_status = "Sie wirkt erst ab: ".date("d.m.Y H:i",$conf['flightban_time']['p1'])."!";
			}
			else
			{
				$flightban_time_status = "Sie ist nun aber abgelaufen!";
			}
			
			$flightban_status = "<div style=\"color:#f90\">Die Flottensperre ist aktiviert! ".$flightban_time_status."</div>";
			$flightban_time_from = $conf['flightban_time']['p1'];
			$flightban_time_to = $conf['flightban_time']['p2'];
			$flightban_reason = $conf['flightban']['p1'];
			$flightban_button = "<input type=\"submit\" name=\"flightban_deactivate\" value=\"Deaktivieren\" />";
		}
		// ...wenn nicht aktiv
		else
		{
			$flightban_status = "<div style=\"color:#0f0\">Die Flottensperre ist deaktiviert!</div>";
			$flightban_time_from = time();
			$flightban_time_to = time();
			$flightban_reason = "";
			$flightban_button = "<input type=\"submit\" name=\"flightban_activate\" value=\"Aktivieren\" />";
		}
		
		echo "<table class=\"tbl\">";
		echo "<tr>
						<td class=\"tbltitle\" colspan=\"2\"><div style=\"text-align:center;\">Flottensperre</div></td>
					</tr>
					<tr>
						<td class=\"tbltitle\" width=\"15%\">Info</td>
						<td class=\"tbldata\" width=\"85%\">Es k&ouml;nnen keine Fl&uuml;ge gestartet werden</td>
					</tr>
					<tr>
						<td class=\"tbltitle\" width=\"15%\">Status</td>
						<td class=\"tbldata\" width=\"85%\">".$flightban_status."</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">Von</td>
						<td class=\"tbldata\">";
						echo show_timebox("flightban_time_from",$flightban_time_from);
			echo "</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">Bis</td>
						<td class=\"tbldata\">";
						echo show_timebox("flightban_time_to",$flightban_time_to);
			echo "</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">Grund</td>
						<td class=\"tbldata\">
							<textarea name=\"flightban_reason\" cols=\"50\" rows=\"3\">".$flightban_reason."</textarea>
						</td>
					</tr>
					<tr>
						<td class=\"tbldata\" colspan=\"2\"><div style=\"text-align:center;\">".$flightban_button."</div></td>
					</tr>";
		echo "</table><br><br>";



		//
		// Kampfsperre
		//
		
		// Setzt Variabeln wenn Kampfsperre aktiv...
		if ($conf['battleban']['v']==1)
		{
			// Prüft, ob die Sperre zum jetzigen Zeitpunkt gilt
			if($conf['battleban_time']['p1']<=time() && $conf['battleban_time']['p2']>=time())
			{
				$battleban_time_status = "Sie wirkt zum jetzigen Zeitpunkt!";
			}
			elseif($conf['battleban_time']['p1']>time() && $conf['battleban_time']['p2']>time())
			{
				$battleban_time_status = "Sie wirkt erst ab: ".date("d.m.Y H:i",$conf['battleban_time']['p1'])."!";
			}
			else
			{
				$battleban_time_status = "Sie ist nun aber abgelaufen!";
			}
			
			$battleban_status = "<div style=\"color:#f90\">Die Flottensperre ist aktiviert! ".$battleban_time_status."</div>";
			$battleban_time_from = $conf['battleban_time']['p1'];
			$battleban_time_to = $conf['battleban_time']['p2'];
			$battleban_reason = $conf['battleban']['p1'];
			$battleban_button = "<input type=\"submit\" name=\"battleban_deactivate\" value=\"Deaktivieren\" />";
		}
		// ...wenn nicht aktiv
		else
		{
			$battleban_status = "<div style=\"color:#0f0\">Die Flottensperre ist deaktiviert!</div>";
			$battleban_time_from = time();
			$battleban_time_to = time();
			$battleban_reason = "";
			$battleban_button = "<input type=\"submit\" name=\"battleban_activate\" value=\"Aktivieren\" />";
		}
		
		echo "<table class=\"tbl\">";
		echo "<tr>
						<td class=\"tbltitle\" colspan=\"2\"><div style=\"text-align:center;\">Kampfsperre</div></td>
					</tr>
					<tr>
						<td class=\"tbltitle\" width=\"15%\">Info</td>
						<td class=\"tbldata\" width=\"85%\">Es k&ouml;nnen keine Angriffe geflogen werden</td>
					</tr>
					<tr>
						<td class=\"tbltitle\" width=\"15%\">Status</td>
						<td class=\"tbldata\" width=\"85%\">".$battleban_status."</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">Von</td>
						<td class=\"tbldata\">";
						echo show_timebox("battleban_time_from",$battleban_time_from);
			echo "</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">Bis</td>
						<td class=\"tbldata\">";
						echo show_timebox("battleban_time_to",$battleban_time_to);
			echo "</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">Grund</td>
						<td class=\"tbldata\">
							<textarea name=\"battleban_reason\" cols=\"50\" rows=\"3\">".$battleban_reason."</textarea>
						</td>
					</tr>
					<tr>
						<td class=\"tbltitle\" colspan=\"2\"><div style=\"text-align:center;\">Ankunftstext während Sperre</div></td>
					</tr>
					<tr>
						<td class=\"tbltitle\">Flotten</td>
						<td class=\"tbldata\">
							<textarea name=\"battleban_arrival_text_fleet\" cols=\"50\" rows=\"3\">".$conf['battleban_arrival_text']['p1']."</textarea>
						</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">Raketen</td>
						<td class=\"tbldata\">
							<textarea name=\"battleban_arrival_text_missiles\" cols=\"50\" rows=\"3\">".$conf['battleban_arrival_text']['p2']."</textarea>
						</td>
					</tr>
					<tr>
						<td class=\"tbldata\" colspan=\"2\"><div style=\"text-align:center;\">".$battleban_button."</div></td>
					</tr>";
		echo "</table>";


		/*
		//
		// Kampfsperre
		//
		echo "<h2>Kampfsperre</h2><form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		if ($_POST['battleban_deactivate']!="")
		{
			dbquery("UPDATE ".$db_table['config']." SET config_value=0,config_param1='' WHERE config_name='battleban';");
			$conf['battleban']['v']=0;
		}
		if ($_POST['battleban_activate']!="")
		{
			dbquery("UPDATE ".$db_table['config']." SET config_value=1,config_param1='".addslashes($_POST['ban_reason'])."' WHERE config_name='battleban';");
			$conf['battleban']['v']=1;
			$conf['battleban']['p1']=addslashes($_POST['ban_reason']);
		}
		if ($conf['battleban']['v']==1)
			echo "<div style=\"color:#f90\">Die Kampfsperre ist aktiviert! Es k&ouml;nnen keine Angriffe geflogen werden!</div><br/>Grund: ".text2html($conf['battleban']['p1'])."<br/><br/><input type=\"submit\" name=\"battleban_deactivate\" value=\"Deaktivieren\" />";
		else
			echo "<div style=\"color:#0f0\">Die Kampfsperre ist deaktiviert!</div><br/>Aktivierungsgrund:<br/><textarea name=\"ban_reason\" cols=\"50\" rows=\"3\"></textarea><br/><br/><input type=\"submit\" name=\"battleban_activate\" value=\"Aktivieren\" />";
		echo "</form><br/>";
		*/

		//
		// Schiffrückruf
		//
		echo "<h2>Flottenr&uuml;ckruf</h2><form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		if ($_POST['flightrecall']!="" && $_POST['recall']!="")
		{
			switch ($_POST['recall'])
			{
				// Schiffe instantan zurückrufen
				case "instant":
					$fres=dbquery("SELECT * FROM ".$db_table['fleet'].";");
					if (mysql_num_rows($fres))
					{
						while ($farr=mysql_fetch_array($fres))
						{
							if (!stristr($farr['fleet_action'],"c") && !stristr($farr['fleet_action'],"r"))
								$planet=$farr['fleet_planet_from'];
							else
								$planet=$farr['fleet_planet_to'];
							dbquery("DELETE FROM ".$db_table['fleet']." WHERE fleet_id=".$farr['fleet_id'].";");
							dbquery("
							UPDATE
								".$db_table['planets']."
							SET
                                planet_res_metal=planet_res_metal+".max($farr['fleet_res_metal'],0).",
                                planet_res_crystal=planet_res_crystal+".max($farr['fleet_res_crystal'],0).",
                                planet_res_plastic=planet_res_plastic+".max($farr['fleet_res_plastic'],0).",
                                planet_res_fuel=planet_res_fuel+".max($farr['fleet_res_fuel'],0).",
                                planet_res_food=planet_res_food+".max($farr['fleet_res_food'],0).",
                                planet_people=planet_people+".(max($farr['fleet_res_people'],0)+max($farr['fleet_pilots'],0))."
							WHERE
								planet_id='$planet';");
							$sres=dbquery("SELECT * FROM ".$db_table['fleet_ships']." WHERE fs_fleet_id=".$farr['fleet_id'].";");
							if (mysql_num_rows($sres)>0)
							{
								while ($sarr=mysql_fetch_array($sres))
								{
									dbquery("DELETE FROM ".$db_table['fleet_ships']." WHERE fs_fleet_id=".$farr['fleet_id']." AND fs_ship_id=".$sarr['fs_ship_id'].";");
									shiplistAdd($planet,$farr['fleet_user_id'],$sarr['fs_ship_id'],$sarr['fs_ship_cnt']);
								}
							}
						}
						echo "Es wurden ".mysql_num_rows($fres)." Flotten sofort zur&uuml;ckgerufen!<br/><br/>";
					}
					else
						echo "Es konnten keine Flotten zur&uuml;ckgerufen werden da keine unterwegs sind!<br/><br/>";
					break;
				// Schiffe per Rückflug zuückrufen
				case "return":
					$res = dbquery("SELECT * FROM ".$db_table['fleet'].";");
					if (mysql_num_rows($res)>0)
					{
						$cnt=0;
						while ($arr=mysql_fetch_array($res))
						{
							if (!stristr($arr['fleet_action'],"r") && !stristr($arr['fleet_action'],"c"))
							{
								$stime = time();
								$etime = (2*time())-$arr['fleet_launchtime'];
								$action = strtr($arr['fleet_action'],"o","r");
								dbquery("
								UPDATE
									".$db_table['fleet']."
								SET
                                    fleet_launchtime='$stime',
                                    fleet_landtime='$etime',
                                    fleet_action='$action',
                                    fleet_planet_from='".$arr['fleet_planet_to']."',
                                    fleet_planet_to='".$arr['fleet_planet_from']."',
                                    fleet_cell_from='".$arr['fleet_cell_to']."',
                                    fleet_cell_to='".$arr['fleet_cell_from']."'
								WHERE
									fleet_id='".$arr['fleet_id']."';");
								$cnt++;
							}
						}
						cms_ok_msg("Es wurden ".$cnt." Flotten per R&uuml;ckflug zur&uuml;ckgeschickt!");
					}
					else
						echo "Es konnten keine Flotten zur&uuml;ckgerufen werden da keine unterwegs sind!<br/><br/>";
					break;
				// Schiffe per Abbruch zuückrufen
				case "cancel":
					$res = dbquery("SELECT * FROM ".$db_table['fleet'].";");
					if (mysql_num_rows($res)>0)
					{
						$cnt=0;
						while ($arr=mysql_fetch_array($res))
						{
							if (!stristr($arr['fleet_action'],"r") && !stristr($arr['fleet_action'],"c"))
							{
								$stime = time();
								$etime = (2*time())-$arr['fleet_launchtime'];
								$action = $arr['fleet_action']."c";
								dbquery("
								UPDATE
									".$db_table['fleet']."
								SET
                                    fleet_launchtime='$stime',
                                    fleet_landtime='$etime',
                                    fleet_action='$action',
                                    fleet_planet_from='".$arr['fleet_planet_to']."',
                                    fleet_planet_to='".$arr['fleet_planet_from']."',
                                    fleet_cell_from='".$arr['fleet_cell_to']."',
                                    fleet_cell_to='".$arr['fleet_cell_from']."'
								WHERE
									fleet_id='".$arr['fleet_id']."';");
								$cnt++;
							}
						}
						cms_ok_msg("Es wurden ".$cnt." Flotten per Flugabbruch zur&uuml;ckgeschickt!");
					}
					else
						echo "Es konnten keine Flotten zur&uuml;ckgerufen werden da keine unterwegs sind!<br/><br/>";
					break;
			}
		}
		echo "<input type=\"radio\" name=\"recall\" value=\"instant\"> Alle Flotten instantan zur&uuml;ckrufen<br/>";
		echo "<input type=\"radio\" name=\"recall\" value=\"return\"> Alle Flotten zur&uuml;ckrufen (per R&uuml;ckflug)<br/>";
		echo "<input type=\"radio\" name=\"recall\" value=\"cancel\"> Alle Flotten zur&uuml;ckrufen (per Flugabbruch)<br/>";
		echo "<br/><input type=\"submit\" name=\"flightrecall\" value=\"Durchf&uuml;hren\" /></form>";


		echo "</form>";
	}
	
	//
	// Flotten
	//
	else
	{
		echo "<h2>Flotten</h2>";

		//
		// Flotte bearbeiten
		//
		if ($_GET['fleetedit']>0)
		{
			echo "<h3>Flotte bearbeiten</h3>";
			if (count($_POST)>0)
			{
				$launchtime=mktime($_POST['fleet_launchtime_h'],$_POST['fleet_launchtime_i'],$_POST['fleet_launchtime_s'],$_POST['fleet_launchtime_m'],$_POST['fleet_launchtime_d'],$_POST['fleet_launchtime_y']);
				$landtime=mktime($_POST['fleet_landtime_h'],$_POST['fleet_landtime_i'],$_POST['fleet_landtime_s'],$_POST['fleet_landtime_m'],$_POST['fleet_landtime_d'],$_POST['fleet_landtime_y']);
				if ($landtime<=$launchtime) $landtime=$launchtime+60;
				$scres=dbquery("SELECT cell_id FROM ".$db_table['space_cells']." WHERE cell_sx='".$_POST['sx_start']."' AND cell_sy='".$_POST['sy_start']."' AND cell_cx='".$_POST['cx_start']."' AND cell_cy='".$_POST['cy_start']."';");
				$scarr=mysql_fetch_array($scres);
				$ecres=dbquery("SELECT cell_id FROM ".$db_table['space_cells']." WHERE cell_sx='".$_POST['sx_end']."' AND cell_sy='".$_POST['sy_end']."' AND cell_cx='".$_POST['cx_end']."' AND cell_cy='".$_POST['cy_end']."';");
				$ecarr=mysql_fetch_array($ecres);
				if ($scarr[0]=="")
				{
					$scarr[0]=$_POST['fleet_cell_from_old'];
					cms_err_msg("Diese Startzelle kann nicht gew&auml;hlt werden da es leerer Raum ist!");
				}
				if ($ecarr[0]=="")
				{
					$ecarr[0]=$_POST['fleet_cell_to_old'];
					cms_err_msg("Diese Zielzelle kann nicht gew&auml;hlt werden da es leerer Raum ist!");
				}
				if ($_POST['fleet_cell_from_old']!=$scarr[0]) $planet_from=0; else $planet_from=$_POST['fleet_planet_from'];
				if ($_POST['fleet_cell_to_old']!=$ecarr[0]) $planet_to=0; else $planet_to=$_POST['fleet_planet_to'];
				dbquery("UPDATE 
				".$db_table['fleet']." 
				SET 
					fleet_cell_from='".$scarr[0]."',
					fleet_cell_to='".$ecarr[0]."',
					fleet_planet_from='".$planet_from."',
					fleet_planet_to='".$planet_to."',
					fleet_user_id='".$_POST['fleet_user_id']."',
					fleet_launchtime='$launchtime',
					fleet_landtime='$landtime',
					fleet_action='".$_POST['fleet_action']."',
					fleet_res_metal='".intval($_POST['fleet_res_metal'])."',
					fleet_res_crystal='".intval($_POST['fleet_res_crystal'])."',
					fleet_res_plastic='".intval($_POST['fleet_res_plastic'])."',
					fleet_res_fuel='".intval($_POST['fleet_res_fuel'])."',
					fleet_res_food='".intval($_POST['fleet_res_food'])."',
					fleet_res_people='".intval($_POST['fleet_res_people'])."',
					fleet_updating='".intval($_POST['fleet_updating'])."'
					WHERE fleet_id='".$_GET['fleetedit']."';");

			}

			$ures=dbquery("SELECT user_id,user_nick FROM ".$db_table['users']." ORDER BY user_nick;");
			$users=array();
			while ($uarr=mysql_fetch_array($ures))
			{
				$users[$uarr['user_id']]=$uarr['user_nick'];
			}
			$res=dbquery("SELECT 
			fleet_cell_from,
			fleet_cell_to,
			fleet_res_metal,
			fleet_res_crystal,
			fleet_res_plastic,
			fleet_res_fuel,
			fleet_res_food,
			fleet_res_people,
			fleet_updating,
			user_id,fleet_id,
			fleet_planet_from,fleet_planet_to,s.cell_nebula as snebula,s.cell_asteroid as sasteroid,s.cell_wormhole_id as swormhole,e.cell_nebula as enebula,e.cell_asteroid as easteroid,e.cell_wormhole_id as ewormhole,user_nick,fleet_action,fleet_launchtime,fleet_landtime,s.cell_cx as scx,s.cell_cy as scy,s.cell_sx as ssx,s.cell_sy as ssy,e.cell_cx as ecx,e.cell_cy as ecy,e.cell_sx as esx,e.cell_sy as esy FROM ".$db_table['fleet'].",".$db_table['users'].",".$db_table['space_cells']." AS s,".$db_table['space_cells']." AS e WHERE fleet_id='".$_GET['fleetedit']."' AND fleet_user_id=user_id AND fleet_cell_from=s.cell_id AND fleet_cell_to=e.cell_id;");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_array($res);
				echo "<form action=\"?page=$page&amp;sub=$sub&amp;fleetedit=".$_GET['fleetedit']."\" method=\"post\" name=\"fleetform\">";
				echo "<input type=\"hidden\" name=\"fleet_cell_from_old\" value=\"".$arr['fleet_cell_from']."\" />";
				echo "<input type=\"hidden\" name=\"fleet_cell_to_old\" value=\"".$arr['fleet_cell_to']."\" />";
				echo "<table class=\"tbl\">";
				echo "<tr><th class=\"tbltitle\">Besitzer:</th><td class=\"tbldata\"><select name=\"fleet_user_id\"><option value=\"\" style=\"font-style:italic\">(niemand)</option>";
				foreach ($users as $id=>$val)
				{
					echo "<option value=\"$id\"";
					if ($id==$arr['user_id']) echo " selected=\"selected\"";
					echo ">$val</option>";
				}
				echo "</select></td></tr>";
				echo "<tr><th class=\"tbltitle\">Startzeit:</th><td class=\"tbldata\">";
				show_timebox("fleet_launchtime",$arr['fleet_launchtime'],1);
				echo "</td></tr>";
				echo "<tr><th class=\"tbltitle\">Landezeit:</th><td class=\"tbldata\">";
				show_timebox("fleet_landtime",$arr['fleet_landtime'],1);
				echo "</td></tr>";
				echo "<tr><td class=\"tbltitle\">Startzelle</td><td class=\"tbldata\"><select name=\"sx_start\" onchange=\"submitForm();\">";
				for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
				{
					echo "<option value=\"$x\"";
					if ($arr['ssx']==$x) echo " selected=\"selected\"";
					echo ">$x</option>";
				}
				echo "</select>/<select name=\"sy_start\" onchange=\"submitForm();\">";
				for ($x=1;$x<=$conf['num_of_sectors']['p2'];$x++)
				{
					echo "<option value=\"$x\"";
					if ($arr['ssy']==$x) echo " selected=\"selected\"";
					echo ">$x</option>";
				}
				echo "</select> : <select name=\"cx_start\" onchange=\"submitForm();\">";
				for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
				{
					echo "<option value=\"$x\"";
					if ($arr['scx']==$x) echo " selected=\"selected\"";
					echo ">$x</option>";
				}
				echo "</select>/<select name=\"cy_start\" onchange=\"submitForm();\">";
				for ($x=1;$x<=$conf['num_of_cells']['p2'];$x++)
				{
					echo "<option value=\"$x\"";
				if ($arr['scy']==$x) echo " selected=\"selected\"";
					echo ">$x</option>";
				}
				echo "</select> ";
				if ($arr['sasteroid']>0)
					echo "Asteroidenfeld";
				elseif ($arr['snebula']>0)
					echo "Nebel";
				elseif ($arr['swormhole']>0)
					echo "Wurmloch";
				else
				{
					$pres=dbquery("SELECT planet_solsys_pos,planet_name,planet_id FROM ".$db_table['planets']." WHERE planet_solsys_id='".$arr['fleet_cell_from']."' ORDER BY planet_solsys_pos ASC;");
					echo "Planet <select name=\"fleet_planet_from\" onchange=\"submitForm();\">";
					echo "<option value=\"\">(Bitte w&auml;hlen)</option>";
					while ($parr=mysql_fetch_array($pres))
					{
						echo "<option value=\"".$parr['planet_id']."\"";
						if ($parr['planet_id']==$arr['fleet_planet_from'])echo " selected=\"selected\"";
						echo ">".$parr['planet_solsys_pos']." - ".$parr['planet_name']."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
				echo "<tr><td class=\"tbltitle\">Endzelle</td><td class=\"tbldata\"><select name=\"sx_end\" onchange=\"submitForm();\">";
				for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
				{
					echo "<option value=\"$x\"";
					if ($arr['esx']==$x) echo " selected=\"selected\"";
					echo ">$x</option>";
				}
				echo "</select>/<select name=\"sy_end\" onchange=\"submitForm();\">";
				for ($x=1;$x<=$conf['num_of_sectors']['p2'];$x++)
				{
					echo "<option value=\"$x\"";
					if ($arr['esy']==$x) echo " selected=\"selected\"";
					echo ">$x</option>";
				}
				echo "</select> : <select name=\"cx_end\" onchange=\"submitForm();\">";
				for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
				{
					echo "<option value=\"$x\"";
					if ($arr['ecx']==$x) echo " selected=\"selected\"";
					echo ">$x</option>";
				}
				echo "</select>/<select name=\"cy_end\" onchange=\"submitForm();\">";
				for ($x=1;$x<=$conf['num_of_cells']['p2'];$x++)
				{
					echo "<option value=\"$x\"";
					if ($arr['ecy']==$x) echo " selected=\"selected\"";
					echo ">$x</option>";
				}
				echo "</select> ";
				if ($arr['easteroid']>0)
					echo "Asteroidenfeld";
				elseif ($arr['enebula']>0)
					echo "Nebel";
				elseif ($arr['ewormhole']>0)
					echo "Wurmloch";
				else
				{
					$pres=dbquery("SELECT planet_solsys_pos,planet_name,planet_id FROM ".$db_table['planets']." WHERE planet_solsys_id='".$arr['fleet_cell_to']."' ORDER BY planet_solsys_pos ASC;");
					echo "Planet <select name=\"fleet_planet_to\" onchange=\"submitForm();\">";
					echo "<option value=\"\">(Bitte w&auml;hlen)</option>";
					while ($parr=mysql_fetch_array($pres))
					{
						echo "<option value=\"".$parr['planet_id']."\"";
						if ($parr['planet_id']==$arr['fleet_planet_to'])echo " selected=\"selected\"";
						echo ">".$parr['planet_solsys_pos']." - ".$parr['planet_name']."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
				echo "<tr><td class=\"tbltitle\">Aktion:</td><td class=\"tbldata\"><select name=\"fleet_action\">";
				echo "<option value=\"\">(egal)</option>";
				foreach ($fleet_actions as $key=>$val)
				{
					echo "<option value=\"$key\"";
					if ($arr['fleet_action']==$key) echo " selected=\"selected\"";
					echo ">$val</option>";
				}
				echo "</select></td></tr>";
				echo "<tr><td class=\"tbltitle\">Titan:</td><td class=\"tbldata\"><input type=\"text\" name=\"fleet_res_metal\" value=\"".$arr['fleet_res_metal']."\" size=\"10\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\">Silizium::</td><td class=\"tbldata\"><input type=\"text\" name=\"fleet_res_crystal\" value=\"".$arr['fleet_res_crystal']."\" size=\"10\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\">PVC:</td><td class=\"tbldata\"><input type=\"text\" name=\"fleet_res_plastic\" value=\"".$arr['fleet_res_plastic']."\" size=\"10\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\">Tritium:</td><td class=\"tbldata\"><input type=\"text\" name=\"fleet_res_fuel\" value=\"".$arr['fleet_res_fuel']."\" size=\"10\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\">Nahrung:</td><td class=\"tbldata\"><input type=\"text\" name=\"fleet_res_food\" value=\"".$arr['fleet_res_food']."\" size=\"10\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\">Mannschaft:</td><td class=\"tbldata\"><input type=\"text\" name=\"fleet_res_people\" value=\"".$arr['fleet_res_people']."\" size=\"10\" /></td></tr>";
				echo "<tr><td class=\"tbltitle\">Update läuft:</td><td class=\"tbldata\"><input type=\"text\" name=\"fleet_updating\" value=\"".$arr['fleet_updating']."\" size=\"1\" /></td></tr>";
				echo "</table><br/><input type=\"submit\" value=\"&Uuml;bernehmen\" name=\"submit_edit\" /> ";
				echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" /> ";
				echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&sub=$sub'\" />";
				echo "</form>";
				echo "<script>
				function submitForm()
				{
					document.fleetform.submit();
				}
				</script>";
			}
			else
				cms_err_msg("Datensatz nicht vorhanden!");
		}

		//
		// Schiffe der Flotte bearbeiten
		//
		elseif ($_GET['fleetships']>0)
		{
			echo "<h3>Schiffe der Flotte bearbeiten</h3>";
			if ($_POST['newship_submit']!="" && $_POST['fs_ship_cnt_new']>0 && $_POST['fs_ship_id_new']>0)
			{
				if (mysql_num_rows(dbquery("SELECT * FROM ".$db_table['fleet_ships']." WHERE fs_fleet_id=".$_GET['fleetships']." AND fs_ship_id=".$_POST['fs_ship_id_new'].";"))<1)
					dbquery("INSERT INTO ".$db_table['fleet_ships']." (fs_fleet_id,fs_ship_id,fs_ship_cnt) VALUES (".$_GET['fleetships'].",".$_POST['fs_ship_id_new'].",".$_POST['fs_ship_cnt_new'].");");
				else
					dbquery("UPDATE ".$db_table['fleet_ships']." SET fs_ship_cnt=fs_ship_cnt+".$_POST['fs_ship_cnt_new']." WHERE fs_fleet_id=".$_GET['fleetships']." AND fs_ship_id=".$_POST['fs_ship_id_new'].";");
			}
			if ($_POST['editship_submit']!="")
			{
				foreach ($_POST['fs_ship_cnt'] as $ship=>$cnt)
				dbquery("UPDATE ".$db_table['fleet_ships']." SET fs_ship_cnt=$cnt WHERE fs_fleet_id=".$_GET['fleetships']." AND fs_ship_id=$ship;");
			}
			if ($_GET['shipdel']>0)
			{
				dbquery("DELETE FROM ".$db_table['fleet_ships']." WHERE fs_fleet_id=".$_GET['fleetships']." AND fs_ship_id=".$_GET['shipdel'].";");
			}

			$sres=dbquery("
			SELECT
                ship_name,
                ship_id,
                fs_ship_cnt
			FROM
                ".$db_table['fleet_ships'].",
                ".$db_table['ships']."
			WHERE
                fs_ship_id=ship_id
                AND fs_fleet_id=".$_GET['fleetships']."
                AND fs_ship_faked='0';");
			if (mysql_num_rows($sres)>0)
			{
				echo "<form action=\"?page=$page&amp;sub=$sub&amp;fleetships=".$_GET['fleetships']."\" method=\"post\">";
				echo "<table class=\"tbl\">";
				echo "<tr><th class=\"tbltitle\">Typ</th><th class=\"tbltitle\">Anzahl</th><th class=\"tbltitle\">&nbsp;</th></tr>";
				while ($sarr=mysql_fetch_array($sres))
				{
					echo "<tr><td class=\"tbldata\">".$sarr['ship_name']."</td>";
					echo "<td class=\"tbldata\"><input type=\"text\" name=\"fs_ship_cnt[".$sarr['ship_id']."]\" value=\"".$sarr['fs_ship_cnt']."\" size=\"5\" /></td>";
					echo "<td class=\"tbldata\"><a href=\"?page=$page&amp;sub=$sub&amp;fleetships=".$_GET['fleetships']."&amp;shipdel=".$sarr['ship_id']."\" onclick=\"return confirm('Soll ".$sarr['ship_name']." wirklich aus der Flotte entfernt werden?');\">L&ouml;schen</a></td>";
					echo "</tr>";
				}

				echo "</table><br/>";

				//Zeigt alle gefakten schiffe in der flotte
                $sfres=dbquery("
                SELECT
                    ship_name,
                    ship_id,
                    fs_ship_cnt
                FROM
                    ".$db_table['fleet_ships'].",
                    ".$db_table['ships']."
                WHERE
                    fs_ship_id=ship_id
                    AND fs_fleet_id=".$_GET['fleetships']."
                    AND fs_ship_faked='1';");
                if (mysql_num_rows($sfres)>0)
                {
                    echo "<table class=\"tbl\">";
                    echo "<tr><th class=\"tbltitle\" colspan=\"3\">Gefakte Schiffe</th></tr>";
                    echo "<tr><th class=\"tbltitle\">Typ</th><th class=\"tbltitle\">Anzahl</th><th class=\"tbltitle\">&nbsp;</th></tr>";
                    while ($sfarr=mysql_fetch_array($sfres))
                    {
                        echo "<tr><td class=\"tbldata\">".$sfarr['ship_name']."</td>";
                        echo "<td class=\"tbldata\"><input type=\"text\" name=\"fs_ship_cnt[".$sfarr['ship_id']."]\" value=\"".$sfarr['fs_ship_cnt']."\" size=\"5\" /></td>";


                        	echo "<td class=\"tbldata\"><a href=\"?page=$page&amp;sub=$sub&amp;fleetships=".$_GET['fleetships']."&amp;shipdel=".$sfarr['ship_id']."\" onclick=\"return confirm('Soll ".$sfarr['ship_name']." wirklich aus der Flotte entfernt werden?');\">L&ouml;schen</a></td>";

                        echo "</tr>";
					}

					echo "</table><br/>";
				}

				echo "<input type=\"submit\" name=\"editship_submit\" value=\"&Auml;nderungen &uuml;bernehmen\" /></form><br/>";
				echo "<form action=\"?page=$page&amp;sub=$sub&amp;fleetships=".$_GET['fleetships']."\" method=\"post\"><input type=\"text\" name=\"fs_ship_cnt_new\" value=\"1\" size=\"5\" /> Schiffe des Typs <select name=\"fs_ship_id_new\">";
				$ssres=dbquery("SELECT ship_id,ship_name FROM ".$db_table['ships']." ORDER BY ship_name;");
				while ($ssarr=mysql_fetch_array($ssres))
				{
					echo "<option value=\"".$ssarr['ship_id']."\">".$ssarr['ship_name']."</option>";
				}
				echo "</select> hinzuf&uuml;gen: <input type=\"submit\" name=\"newship_submit\" value=\"Asuf&uuml;hren\" /></form><br/>";
				echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" /> ";
				echo "<input type=\"button\" value=\"Neue zur Suche\" onclick=\"document.location='?page=$page&sub=$sub'\" />";
			}
			else
				cms_err_msg("Diese Flotte besitzt keine Schiffe!");
		}

		//
		// Suchergebnisse anzeigen
		//
		elseif ($_POST['fleet_search']!="" || $_GET['action']=="searchresults")
		{
			// Flotte löschen
			if ($_GET['fleetdel']>0)
			{
				dbquery("DELETE FROM ".$db_table['fleet']." WHERE fleet_id='".$_GET['fleetdel']."';");
				dbquery("DELETE FROM ".$db_table['fleet_ships']." WHERE fs_fleet_id='".$_GET['fleetdel']."';");
				cms_ok_msg("Die Flotte wurde gel&ouml;scht!");
			}

			// Flotte zurückschicken
			if ($_GET['fleetreturn']>0)
			{
				$res = dbquery("SELECT * FROM ".$db_table['fleet']." WHERE fleet_id='".$_GET['fleetreturn']."';");
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
					if (!stristr($arr['fleet_action'],"r") && !stristr($arr['fleet_action'],"c"))
					{
						$stime = time();
						$etime = (2*time())-$arr['fleet_launchtime'];
						$action = $arr['fleet_action']."c";

						dbquery("UPDATE ".$db_table['fleet']." SET
						fleet_launchtime='$stime',
						fleet_landtime='$etime',
						fleet_action='$action',
						fleet_planet_from='".$arr['fleet_planet_to']."',
						fleet_planet_to='".$arr['fleet_planet_from']."',
						fleet_cell_from='".$arr['fleet_cell_to']."',
						fleet_cell_to='".$arr['fleet_cell_from']."'
						WHERE fleet_id='".$_GET['fleetreturn']."';");
						cms_ok_msg("Die Flotte wurde zur&uuml;ckgeschickt!");
					}
					else
						cms_err_msg("Die Flotte kann nicht zur&uuml;ckgeschickt werden!");
				}
				else
					cms_err_msg("Die Flotte <b>".$_GET['fleetreturn']."</b> wurde nicht gefunden!");
			}

			// Suchquery zusammenstellen
			if ($_SESSION['fleetedit']['query']=="")
			{
				if ($_POST['sx_start']>0)
					$sql.= " AND s.cell_sx=".$_POST['sx_start'];
				if ($_POST['sy_start']>0)
					$sql.= " AND s.cell_sy=".$_POST['sy_start'];
				if ($_POST['cx_start']>0)
					$sql.= " AND s.cell_cx=".$_POST['cx_start'];
				if ($_POST['cy_start']>0)
					$sql.= " AND s.cell_cy=".$_POST['cy_start'];
				if ($_POST['sx_end']>0)
					$sql.= " AND e.cell_sx=".$_POST['sx_end'];
				if ($_POST['sy_end']>0)
					$sql.= " AND e.cell_sy=".$_POST['sy_end'];
				if ($_POST['cx_end']>0)
					$sql.= " AND e.cell_cx=".$_POST['cx_end'];
				if ($_POST['cy_end']>0)
					$sql.= " AND e.cell_cy=".$_POST['cy_end'];
				if ($_POST['typ_start']!="")
				{
					switch ($_POST['typ_start'])
					{
						case "solsys":
							$typ=" s.cell_solsys_num_planets>0 AND s.cell_solsys_solsys_sol_type>0";
							break;
						case "asteroid":
							$typ=" s.cell_asteroid>0";
							break;
						case "nebula":
							$typ=" s.cell_nebula>0";
							break;
						case "wormhole":
							$typ=" s.cell_wormhole_id>0";
							break;
					}
					$sql.= " AND ".$typ;
				}
				if ($_POST['typ_end']!="")
				{
					switch ($_POST['typ_end'])
					{
						case "solsys":
							$typ=" e.cell_solsys_num_planets>0 AND e.cell_solsys_solsys_sol_type>0";
							break;
						case "asteroid":
							$typ=" e.cell_asteroid>0";
							break;
						case "nebula":
							$typ=" e.cell_nebula>0";
							break;
						case "wormhole":
							$typ=" e.cell_wormhole_id>0";
							break;
					}
					$sql.= " AND ".$typ;
				}
				if ($_POST['fleet_action']!="")
				{
					if ($_POST['fleet_action']=="-")
						$sql.=" AND fleet_action=''";
					else
						$sql.=" AND fleet_action='".$_POST['fleet_action']."'";
				}
				if ($_POST['planet_name_start']!="")
				{
					if (stristr($_POST['qmode']['planet_name_start'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND ps.planet_name ".stripslashes($_POST['qmode']['planet_name_start']).$_POST['planet_name_start']."$addchars'";
				}
				if ($_POST['planet_name_end']!="")
				{
					if (stristr($_POST['qmode']['planet_name_end'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND es.planet_name ".stripslashes($_POST['qmode']['planet_name_end']).$_POST['planet_name_end']."$addchars'";
				}
				if ($_POST['user_id']!="")
					$sql.=" AND user_id=".$_POST['user_id'];
				if ($_POST['fleet_id']!="")
					$sql.=" AND fleet_id=".$_POST['fleet_id'];
				if ($_POST['user_nick']!="")
				{
					if (stristr($_POST['qmode']['user_nick'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND user_nick ".stripslashes($_POST['qmode']['user_nick']).$_POST['user_nick']."$addchars'";
				}

				$sqlstart = "
				SELECT
                    fleet_id,
                    fleet_planet_from,
                    fleet_planet_to,
                    s.cell_nebula as snebula,
                    s.cell_asteroid as sasteroid,
                    s.cell_wormhole_id as swormhole,
                    e.cell_nebula as enebula,
                    e.cell_asteroid as easteroid,
                    e.cell_wormhole_id as ewormhole,
                    user_nick,
                    ps.planet_id as start_planet_id,
                    ps.planet_name as start_planet_name,
                    es.planet_id as end_planet_id,
                    es.planet_name as end_planet_name,
                    fleet_action,
                    fleet_launchtime,
                    fleet_landtime,
                    fleet_updating,
                    s.cell_cx as scx,
                    s.cell_cy as scy,
                    s.cell_sx as ssx,
                    s.cell_sy as ssy,
                    e.cell_cx as ecx,
                    e.cell_cy as ecy,
                    e.cell_sx as esx,
                    e.cell_sy as esy
				FROM
        	fleet
        LEFT JOIN
        	users 
        	ON fleet_user_id=user_id
        LEFT JOIN
        	space_cells AS s
        	ON fleet_cell_from=s.cell_id
        LEFT JOIN
          space_cells AS e
          ON fleet_cell_to=e.cell_id
        LEFT JOIN 
        	planets AS ps 
					ON fleet_planet_from=ps.planet_id
				LEFT JOIN 
					planets AS es
					ON fleet_planet_to=es.planet_id
				";
				
				$sqlend.= " ORDER BY ";
				
				switch ($_POST['fleet_order'])
				{
					case "launchtime":
						$sqlend.="fleet_launchtime DESC;";
						break;
					case "landtime":
						$sqlend.="fleet_landtime ASC;";
						break;
					case "user":
						$sqlend.="user_nick ASC;";
						break;
					case "action":
						$sqlend.="fleet_action ASC;";
						break;
					default:
						$sqlend.="fleet_landtime DESC;";
						break;
				}
				
				$sql = $sqlstart.$sql.$sqlend;
				
				//echo nl2br($sql);
				
				$_SESSION['fleetedit']['query']=$sql;
			}
			else
				$sql = $_SESSION['fleetedit']['query'];

			$res = dbquery($sql);
			$nr = mysql_num_rows($res);
			if (($sql!="" || $_SESSION['fleetedit']['query']!="" ) && $nr > 0)
			{
				echo $nr." Datens&auml;tze vorhanden<br/><br/>";
				if ($nr > 20)
					echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" /><br/><br/>";

				if ($arr['fleet_updating']==1)
					$style=" style=\"color:orange;\"";
				else
					$style="";
				echo "<table class=\"tbl\">";
				echo "<tr>";
				echo "<td class=\"tbltitle\" $style>Besitzer</td>";
				echo "<td class=\"tbltitle\" $style>Aktion</td>";
				echo "<td class=\"tbltitle\" $style>Start</td>";
				echo "<td class=\"tbltitle\" $style>Ziel</td>";
				echo "<td class=\"tbltitle\" $style>Startzeit</td>";
				echo "<td class=\"tbltitle\" $style>Landezeit</td>";
				echo "</tr>";
				while ($arr = mysql_fetch_array($res))
				{
					if ($arr['snebula']>0)
					{
						$startcell = "<span style=\"color:#99f;\">Nebel (".$arr['ssx']."/".$arr['ssx']." : ".$arr['scx']."/".$arr['scy'].")</span>";
					}
					elseif ($arr['sasteroid']>0)
					{
						$startcell  = "<span style=\"color:#99f;\">Asteroiden (".$arr['ssx']."/".$arr['ssx']." : ".$arr['scx']."/".$arr['scy'].")</span>";
					}
					elseif ($arr['swormhole']>0)
					{
						$startcell = "<span style=\"color:#99f;\">Wurmloch (".$arr['ssx']."/".$arr['ssx']." : ".$arr['scx']."/".$arr['scy'].")</span>";
					}
					elseif ($arr['fleet_planet_from']>0)
					{
						if ($arr['start_planet_id']>0)
						{
							if ($arr['start_planet_name']=="") 
							{
								$arr['start_planet_name']="Unbenannter Planet";
							}
							$startcell="<span ".tm("Koordinaten",$arr['ssx']."/".$arr['ssx']." : ".$arr['scx']."/".$arr['scy']." : ".$parr['planet_solsys_pos']).">".$arr['start_planet_name']."</span>";
						}
						else
						{
							$startcell=$arr['ssx']."/".$arr['ssx']." : ".$arr['scx']."/".$arr['scy']." (<span style=\"color:red;\">Unbekannter Planet</span>)";
						}
					}
					else
					{
						if ($arr['ssx'] > 0 && $arr['ssx'] > 0 && $arr['scx'] > 0 && $arr['scy'] > 0)
						{
							$startcell=$arr['ssx']."/".$arr['ssx']." : ".$arr['scx']."/".$arr['scy']." (<span style=\"color:red;\">Unbekannt</span>)";
						}
						else
						{
							$startcell = "<span style=\"color:#99f;\">Unendliche Weiten</span>";
						}
					}

					if ($arr['enebula']>0)
					{
						$endcell = "<span style=\"color:#99f;\">Nebel (".$arr['esx']."/".$arr['esx']." : ".$arr['ecx']."/".$arr['ecy'].")</span>";
					}
					elseif ($arr['easteroid']>0)
					{
						$endcell  = "<span style=\"color:#99f;\">Asteroiden (".$arr['esx']."/".$arr['esx']." : ".$arr['ecx']."/".$arr['ecy'].")</span>";
					}
					elseif ($arr['ewormhole']>0)
					{
						$endcell = "<span style=\"color:#99f;\">Wurmloch (".$arr['esx']."/".$arr['esx']." : ".$arr['ecx']."/".$arr['ecy'].")</span>";
					}
					elseif ($arr['fleet_planet_to']>0)
					{
						if ($arr['end_planet_id']>0)
						{
							if ($arr['end_planet_name']=="") 
							{
								$arr['end_planet_name']="Unbenannter Planet";
							}
							$endcell="<span ".tm("Koordinaten",$arr['esx']."/".$arr['esx']." : ".$arr['ecx']."/".$arr['ecy']." : ".$parr['planet_solsys_pos']).">".$arr['end_planet_name']."</span>";
						}
						else
						{
							$endcell=$arr['esx']."/".$arr['esx']." : ".$arr['ecx']."/".$arr['ecy']." (<span style=\"color:red;\">Unbekannter Planet</span>)";
						}
					}
					else
					{
						if ($arr['esx'] > 0 && $arr['esx'] > 0 && $arr['ecx'] > 0 && $arr['ecy'] > 0)
						{
							$endcell=$arr['esx']."/".$arr['esx']." : ".$arr['ecx']."/".$arr['ecy']." (<span style=\"color:red;\">Unbekannt</span>)";
						}
						else
						{
							$endcell = "<span style=\"color:#99f;\">Unendliche Weiten</span>";
						}
					}


					$stl="";
					if ($arr['fleet_updating']==1)
					{
						$stl="style=\"color:red;\"";
					}
					if ($arr['fleet_landtime']< time())
					{
						$stl="style=\"color:orange;\"";
					}
					
					if ($arr['user_nick']=="")
					{
						$owner = "<span style=\"color:#99f\">System</span>";
					}
					else
					{
						$owner = $arr['user_nick'];
					}
					

					echo "<tr>";
					echo "<td class=\"tbldata\" $stl>".$owner."</td>";
					echo "<td class=\"tbldata\" $stl>";
					if ($fleet_actions[$arr['fleet_action']]!="")
						echo $fleet_actions[$arr['fleet_action']];
					else
						echo "<i>".$arr['fleet_action']."</i>";
					echo "</td>";
					echo "<td class=\"tbldata\" $stl>".$startcell."</td>";
					echo "<td class=\"tbldata\" $stl>".$endcell."</td>";
					echo "<td class=\"tbldata\" $stl>".date("d.m.y",$arr['fleet_landtime'])." &nbsp; ".date("H:i:s",$arr['fleet_launchtime'])."</td>";
					echo "<td class=\"tbldata\" $stl>".date("d.m.y",$arr['fleet_landtime'])." &nbsp; ".date("H:i:s",$arr['fleet_landtime'])."</td>";
					echo "<td class=\"tbldata\"><a href=\"?page=$page&amp;sub=$sub&fleetships=".$arr['fleet_id']."\">Schiffe</a> ";
					if (!stristr($arr['fleet_action'],"r") && !stristr($arr['fleet_action'],"c"))
						echo "<a href=\"?page=$page&amp;sub=$sub&fleetreturn=".$arr['fleet_id']."&amp;action=searchresults\" onclick=\"return confirm('Soll diese Flotte wirklich zur&uuml;ckgeschickt werden?');\">Return</a> ";
					echo edit_button("?page=$page&amp;sub=$sub&fleetedit=".$arr['fleet_id'])." ";
					echo del_button("?page=$page&amp;sub=$sub&fleetdel=".$arr['fleet_id']."&amp;action=searchresults","return confirm('Soll diese Flotte wirklich gel&ouml;scht werden?');");
					echo "</tr>";
				}
				echo "</table>";
				echo "<br/><input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" /> ";
				echo "<input type=\"button\" value=\"Aktualisieren\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=searchresults'\" />";
			}
			else
			{
				echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" />";
				$_SESSION['fleetedit']['query']=Null;
			}
		}

		//
		// Flottensuche
		//
		else
		{
			$_SESSION['fleetedit']['query']=Null;
			echo "Suchmaske:<br/><br/>";
			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			echo "<table class=\"tbl\">";
			echo "<tr><td class=\"tbltitle\">Startzelle</td><td class=\"tbldata\"><select name=\"sx_start\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>/<select name=\"sy_start\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select> : <select name=\"cx_start\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>/<select name=\"cy_start\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select></td></tr>";
			echo "<tr><td class=\"tbltitle\">Endzelle</td><td class=\"tbldata\"><select name=\"sx_end\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>/<select name=\"sy_end\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=1;$x<=$conf['num_of_sectors']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select> : <select name=\"cx_end\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>/<select name=\"cy_end\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=1;$x<=$conf['num_of_cells']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select></td></tr>";
			echo "<tr><td class=\"tbltitle\">Startzellentyp</td><td class=\"tbldata\"><select name=\"typ_start\">";
			echo "<option value=\"\">(egal)</option>";
			echo "<option value=\"solsys\">Sonnensystem / Planet</option>";
			echo "<option value=\"asteroid\">Asteroidenfeld</option>";
			echo "<option value=\"nebula\">Nebel</option>";
			echo "<option value=\"wormhole\">Wurmloch</option>";
			echo "</select></td></tr>";
			echo "<tr><td class=\"tbltitle\">Zielzellentyp</td><td class=\"tbldata\"><select name=\"typ_end\">";
			echo "<option value=\"\">(egal)</option>";
			echo "<option value=\"solsys\">Sonnensystem / Planet</option>";
			echo "<option value=\"asteroid\">Asteroidenfeld</option>";
			echo "<option value=\"nebula\">Nebel</option>";
			echo "<option value=\"wormhole\">Wurmloch</option>";
			echo "</select></td></tr>";
			echo "<tr><td class=\"tbltitle\">Flottenaktion</td><td class=\"tbldata\"><select name=\"fleet_action\">";
			echo "<option value=\"\">(egal)</option>";
			echo "<option value=\"-\">(nichts)</option>";
			foreach ($fleet_actions as $key=>$val)
			{
				echo "<option value=\"$key\">$val</option>";
			}
			echo "</select></td></tr>";
			echo "<tr><td class=\"tbltitle\">Startplanetname</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_name_start\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('planet_name_start');echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">Zielplanetname</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_name_end\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('planet_name_end');echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">Flotten ID</td><td class=\"tbldata\"><input type=\"text\" name=\"fleet_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Besitzer ID</td><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Besitzer Nick</td><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('user_nick');echo "</td></tr>";
			
			echo "<tr>
				<td class=\"tbltitle\">Besitzer Nick</td>
				<td class=\"tbldata\">
					<select name=\"fleet_order\">
						<option value=\"landtime\">Landezeit</option>
						<option value=\"starttime\">Startzeit</option>
						<option value=\"user\">Besitzer</option>
						<option value=\"action\">Aktion</option>
					</select>
				</td>
			</tr>";
			
			echo "</table>";
			echo "<br/><input type=\"submit\" class=\"button\" name=\"fleet_search\" value=\"Suche starten\" /></form>";
			$tblcnt = mysql_fetch_row(dbquery("SELECT count(fleet_id) FROM ".$db_table['fleet'].";"));
			echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.";
		}
	}
?>	
