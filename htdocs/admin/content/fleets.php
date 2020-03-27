<?PHP

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
		if (isset($_POST['flightban_deactivate']))
		{
			dbquery("
			UPDATE 
				config 
			SET 
				config_value=0,
				config_param1='' 
			WHERE 
				config_name='flightban';");

			$conf['flightban']['v']=0;
		}


		// Flottensperre aktivieren
		if (isset($_POST['flightban_activate']) || isset($_POST['flightban_update']))
		{
			$flightban_from = parseDatePicker('flightban_time_from', $_POST);
			$flightban_to = parseDatePicker('flightban_time_to', $_POST);

			if($flightban_from < $flightban_to)
			{
				dbquery("
				UPDATE 
					config 
				SET 
					config_param1='".$flightban_from."',
					config_param2='".$flightban_to."' 
				WHERE 
					config_name='flightban_time';");

				dbquery("
				UPDATE 
					config 
				SET 
					config_value=1,
					config_param1='".mysql_real_escape_string($_POST['flightban_reason'])."' 
				WHERE 
					config_name='flightban';");

				$conf['flightban']['v']=1;
				$conf['flightban']['p1']=mysql_real_escape_string($_POST['flightban_reason']);
				$conf['flightban_time']['p1']=$flightban_from;
				$conf['flightban_time']['p2']=$flightban_to;
			}
			else
			{
				echo "<b>Fehler:</b> Das Ende muss nach dem Start erfolgen!<br><br>";
			}
		}

		// Kampfsperre deaktivieren
		if (isset($_POST['battleban_deactivate']))
		{
			dbquery("
			UPDATE 
				config 
			SET 
				config_value=0,
				config_param1='' 
			WHERE 
				config_name='battleban';");

			$conf['battleban']['v']=0;
		}

		// Kampfsperre aktivieren
		if (isset($_POST['battleban_activate']) || isset($_POST['battleban_update']))
		{
			$battleban_from = parseDatePicker('battleban_time_from', $_POST);
			$battleban_to = parseDatePicker('battleban_time_to', $_POST);

			if($battleban_from < $battleban_to)
			{

				dbquery("
				UPDATE 
					config 
				SET 
					config_value=1,
					config_param1='".mysql_real_escape_string($_POST['battleban_reason'])."'
				WHERE 
					config_name='battleban';");

				dbquery("
				UPDATE 
					config 
				SET 
					config_param1='".mysql_real_escape_string($_POST['battleban_arrival_text_fleet'])."',
					config_param2='".mysql_real_escape_string($_POST['battleban_arrival_text_missiles'])."'
				WHERE 
					config_name='battleban_arrival_text';");

				dbquery("
				UPDATE 
					config 
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
				config
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
				config
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
			$flightban_button = "<input type=\"submit\" name=\"flightban_update\" value=\"Aktualisieren\" /> <input type=\"submit\" name=\"flightban_deactivate\" value=\"Deaktivieren\" />";
		}
		// ...wenn nicht aktiv
		else
		{
			$flightban_status = "<div style=\"color:#0f0\">Die Flottensperre ist deaktiviert!</div>";
			$flightban_time_from = time();
			$flightban_time_to = time()+3600;
			$flightban_reason = "";
			$flightban_button = "<input type=\"submit\" name=\"flightban_activate\" value=\"Aktivieren\" />";
		}

		echo "<h2>Flottensperre</h2><table class=\"tbl\">";
		echo "<tr>
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
						echo showDatepicker("flightban_time_from", $flightban_time_from, true);
			echo "</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">Bis</td>
						<td class=\"tbldata\">";
						echo showDatepicker("flightban_time_to", $flightban_time_to, true);
			echo "</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">Grund</td>
						<td class=\"tbldata\">
							<textarea name=\"flightban_reason\" cols=\"50\" rows=\"3\">".$flightban_reason."</textarea>
						</td>
					</tr>
				</table>
				<p>".$flightban_button."</p><br/>";


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

			$battleban_status = "<div style=\"color:#f90\">Die Kampfsperre ist aktiviert! ".$battleban_time_status."</div>";
			$battleban_time_from = $conf['battleban_time']['p1'];
			$battleban_time_to = $conf['battleban_time']['p2'];
			$battleban_reason = $conf['battleban']['p1'];
			$battleban_button = "<input type=\"submit\" name=\"battleban_update\" value=\"Aktualisieren\" /> <input type=\"submit\" name=\"battleban_deactivate\" value=\"Deaktivieren\" />";
		}
		// ...wenn nicht aktiv
		else
		{
			$battleban_status = "<div style=\"color:#0f0\">Die Kampfsperre ist deaktiviert!</div>";
			$battleban_time_from = time();
			$battleban_time_to = time()+3600;
			$battleban_reason = "";
			$battleban_button = "<input type=\"submit\" name=\"battleban_activate\" value=\"Aktivieren\" />";
		}

		echo "<h2>Kampfsperre</h2>
		<table class=\"tbl\">";
		echo "<tr>
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
						showDatepicker("battleban_time_from", $battleban_time_from, true);
			echo "</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">Bis</td>
						<td class=\"tbldata\">";
						showDatepicker("battleban_time_to", $battleban_time_to, true);
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
				</tr>
			</table>
			<p>".$battleban_button."</p>";

		echo "</form>";
	}

	//
	// Flotten
	//
	else
	{
		$twig->addGlobal('title', "Flotten");

		//
		// Flotte bearbeiten
		//
		if (isset($_GET['fleetedit']) && $_GET['fleetedit']>0)
		{
			echo "<h2>Flotte bearbeiten</h2>";
			if (isset($_POST['submit_edit']))
			{
				$launchtime = parseDatePicker('launchtime', $_POST);
				$landtime = parseDatePicker('landtime', $_POST);
				if ($landtime<=$launchtime) $landtime=$launchtime+60;

				if ($srcEnt = Entity::createFactoryByCoords($_POST['sx_start'],$_POST['sy_start'],$_POST['cx_start'],$_POST['cy_start'],$_POST['p_start']))
				{
					$srcstr = "entity_from=".$srcEnt->id().",";
				}
				else
				{
					error_msg("Startentität nicht vorhanden");
				}
				if ($trgEnt = Entity::createFactoryByCoords($_POST['sx_end'],$_POST['sy_end'],$_POST['cx_end'],$_POST['cy_end'],$_POST['p_end']))
				{
					$trgstr = "entity_to=".$trgEnt->id().",";
				}
				else
				{
					error_msg("Zielentität nicht vorhanden");
				}

				dbquery("
				UPDATE 
					fleet
				SET 
					user_id='".intval($_POST['user_id'])."',
					launchtime='$launchtime',
					landtime='$landtime',
					".$srcstr."
					".$trgstr."
					action='".mysql_real_escape_string($_POST['action'])."',
					status='".intval($_POST['status'])."',
					pilots='".intval($_POST['pilots'])."',
					usage_fuel='".intval($_POST['usage_fuel'])."',
					usage_food='".intval($_POST['usage_food'])."',
					usage_power='".intval($_POST['usage_power'])."',
					res_metal='".intval($_POST['res_metal'])."',
					res_crystal='".intval($_POST['res_crystal'])."',
					res_plastic='".intval($_POST['res_plastic'])."',
					res_fuel='".intval($_POST['res_fuel'])."',
					res_food='".intval($_POST['res_food'])."',
					res_power='".intval($_POST['res_power'])."',
					res_people='".intval($_POST['res_people'])."',
					fetch_metal='".intval($_POST['fetch_metal'])."',
					fetch_crystal='".intval($_POST['fetch_crystal'])."',
					fetch_plastic='".intval($_POST['fetch_plastic'])."',
					fetch_fuel='".intval($_POST['fetch_fuel'])."',
					fetch_food='".intval($_POST['fetch_food'])."',
					fetch_power='".intval($_POST['fetch_power'])."',
					fetch_people='".intval($_POST['fetch_people'])."'
				WHERE 
					id='".intval($_GET['fleetedit'])."';");
				success_msg("Flottendaten geändert!");
			}

			$ures=dbquery("SELECT user_id,user_nick FROM users ORDER BY user_nick;");
			$users=array();
			while ($uarr=mysql_fetch_array($ures))
			{
				$users[$uarr['user_id']]=$uarr['user_nick'];
			}
			$res=dbquery("
			SELECT 
				*
			FROM 
				fleet
			WHERE
				id='".intval($_GET['fleetedit'])."'
			;");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_array($res);

				// Cancel flight
				if (isset($_POST['submit_cancel']) && $arr['status']==0)
				{
					$difftime = time() - $arr['launchtime'];
					$landtime = time() + $difftime ;
					dbquery("
					UPDATE 
						fleet
					SET 
						launchtime='".time()."',
						landtime='".$landtime."',
						entity_from=".$arr['entity_to'].",
						entity_to=".$arr['entity_from'].",
						status='2'
					WHERE 
						id='".intval($_GET['fleetedit'])."';");
					$res=dbquery("
					SELECT 
						*
					FROM 
						fleet
					WHERE
						id='".intval($_GET['fleetedit'])."'
					;");
					$arr=mysql_fetch_array($res);
				}

				// Return flight
				if (isset($_POST['submit_return']) && $arr['status']==0)
				{
					$difftime = time() - $arr['launchtime'];
					$landtime = time() + $difftime ;
					dbquery("
					UPDATE 
						fleet
					SET 
						launchtime='".time()."',
						landtime='".$landtime."',
						entity_from=".$arr['entity_to'].",
						entity_to=".$arr['entity_from'].",
						status='1'
					WHERE 
						id='".intval($_GET['fleetedit'])."';");
					$res=dbquery("
					SELECT 
						*
					FROM 
						fleet
					WHERE
						id='".intval($_GET['fleetedit'])."'
					;");
					$arr=mysql_fetch_array($res);
				}

				// Land fleet
				if (isset($_POST['submit_land']))
				{
					$trgEnt = Entity::createFactoryById($arr['entity_to']);

					if (($arr['user_id']==0 || $arr['user_id']==$trgEnt->ownerId()) && $trgEnt->ownerId() > 0)
					{
						$sres=dbquery("
						SELECT
							fs_id,
				      fs_ship_id,
				      fs_ship_cnt
						FROM
			       	fleet_ships
						WHERE
			      	fs_fleet_id=".intval($_GET['fleetedit'])."
			      	AND fs_ship_faked='0';");
						if (mysql_num_rows($sres)>0)
						{
							$sl = new ShipList($trgEnt->id(),$trgEnt->ownerId());
							while ($sarr=mysql_fetch_array($sres))
							{
								$sl->add($sarr['fs_ship_id'],$sarr['fs_ship_cnt']);
								dbquery("
								DELETE FROM
									fleet_ships
								WHERE
									fs_id=".$sarr['fs_id']."
								");
							}
						}

						if (in_array('OwnableEntity', class_implements($trgEnt)))
						{
							$trgEnt->changeRes($arr['res_metal'],$arr['res_crystal'],$arr['res_plastic'],$arr['res_fuel'],$arr['res_food'],$arr['res_power']);
							$trgEnt->chgPeople($arr['pilots']+$arr['res_people']);
              if ($arr['res_metal'] + $arr['res_crystal'] + $arr['res_plastic'] + $arr['res_fuel'] + $arr['res_food'] + $arr['res_power'] + $arr['pilots'] + $arr['res_people'] > 0) {
                success_msg("Ressourcen transferiert!");
              }
						}

						// TODO: Add parts of usaged stuff (power cells, fuel, food)

						dbquery("
						DELETE FROM
							fleet
						WHERE
							id=".intval($_GET['fleetedit']).";");
						success_msg("Flotte gelandet!");
						unset($arr);
					}
					else
					{
						error_msg("Kann Flotte nicht landen, Ziel ist unbewohnt oder Flottenbesitzer entspricht nicht Zielbesitzer.");
					}
				}

				if (isset($arr))
				{
					echo "<form action=\"?page=$page&amp;sub=$sub&amp;fleetedit=".$_GET['fleetedit']."\" method=\"post\" name=\"fleetform\">";
					echo "<table class=\"tbl\">";

					// Owner
					echo "<tr><th class=\"tbltitle\">Besitzer:</th><td class=\"tbldata\"><select name=\"user_id\"><option value=\"\" style=\"font-style:italic\">(niemand)</option>";
					foreach ($users as $id=>$val)
					{
						echo "<option value=\"$id\"";
						if ($id==$arr['user_id']) echo " selected=\"selected\"";
						echo ">$val</option>";
					}
					echo "</select></td></tr>";

					// Time Data
					echo "<tr><th class=\"tbltitle\">Startzeit:</th><td class=\"tbldata\">";
					showDatepicker("launchtime",$arr['launchtime'], true, true);
					echo "</td></tr>";
					echo "<tr><th class=\"tbltitle\">Landezeit:</th><td class=\"tbldata\">";
					showDatepicker("landtime",$arr['landtime'], true, true);
					echo " &nbsp; Flugdauer: ".tf($arr['landtime']-$arr['launchtime'])."</td></tr>";

					// Source and Target Data
					$srcEnt = Entity::createFactoryById($arr['entity_from']);
					$trgEnt = Entity::createFactoryById($arr['entity_to']);

					echo "<tr><td class=\"tbltitle\">Startzelle</td><td class=\"tbldata\">
					<select name=\"sx_start\" onchange=\"submitForm();\">";
					for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
					{
						echo "<option value=\"$x\"";
						if ($srcEnt->sx()==$x) echo " selected=\"selected\"";
						echo ">$x</option>";
					}
					echo "</select>/<select name=\"sy_start\" onchange=\"submitForm();\">";
					for ($x=1;$x<=$conf['num_of_sectors']['p2'];$x++)
					{
						echo "<option value=\"$x\"";
						if ($srcEnt->sy()==$x) echo " selected=\"selected\"";
						echo ">$x</option>";
					}
					echo "</select> : <select name=\"cx_start\" onchange=\"submitForm();\">";
					for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
					{
						echo "<option value=\"$x\"";
						if ($srcEnt->cx()==$x) echo " selected=\"selected\"";
						echo ">$x</option>";
					}
					echo "</select>/<select name=\"cy_start\" onchange=\"submitForm();\">";
					for ($x=1;$x<=$conf['num_of_cells']['p2'];$x++)
					{
						echo "<option value=\"$x\"";
						if ($srcEnt->cy()==$x) echo " selected=\"selected\"";
						echo ">$x</option>";
					}
					echo "</select> : <select name=\"p_start\" onchange=\"submitForm();\">";
					for ($x=0;$x<=$conf['num_planets']['p2'];$x++)
					{
						echo "<option value=\"$x\"";
						if ($srcEnt->pos()==$x) echo " selected=\"selected\"";
						echo ">$x</option>";
					}
					echo "</select> &nbsp; ".$srcEnt->name()." (".$srcEnt->entityCodeString().", ".$srcEnt->owner().")";

					echo "</td></tr>";
					echo "<tr><td class=\"tbltitle\">Endzelle</td><td class=\"tbldata\">
					<select name=\"sx_end\" onchange=\"submitForm();\">";
					for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
					{
						echo "<option value=\"$x\"";
						if ($trgEnt->sx()==$x) echo " selected=\"selected\"";
						echo ">$x</option>";
					}
					echo "</select>/<select name=\"sy_end\" onchange=\"submitForm();\">";
					for ($x=1;$x<=$conf['num_of_sectors']['p2'];$x++)
					{
						echo "<option value=\"$x\"";
						if ($trgEnt->sy()==$x) echo " selected=\"selected\"";
						echo ">$x</option>";
					}
					echo "</select> : <select name=\"cx_end\" onchange=\"submitForm();\">";
					for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
					{
						echo "<option value=\"$x\"";
						if ($trgEnt->cx()==$x) echo " selected=\"selected\"";
						echo ">$x</option>";
					}
					echo "</select>/<select name=\"cy_end\" onchange=\"submitForm();\">";
					for ($x=1;$x<=$conf['num_of_cells']['p2'];$x++)
					{
						echo "<option value=\"$x\"";
						if ($trgEnt->cy()==$x) echo " selected=\"selected\"";
						echo ">$x</option>";
					}
					echo "</select>  : <select name=\"p_end\" onchange=\"submitForm();\">";
					for ($x=0;$x<=$conf['num_planets']['p2'];$x++)
					{
						echo "<option value=\"$x\"";
						if ($trgEnt->pos()==$x) echo " selected=\"selected\"";
						echo ">$x</option>";
					}
					echo "</select> &nbsp; ".$trgEnt->name()." (".$trgEnt->entityCodeString().", ".$trgEnt->owner().")";

					// Action
					echo "</td></tr>";
					echo "<tr><td class=\"tbltitle\">Aktion:</td><td class=\"tbldata\"><select name=\"action\">";
					echo "<option value=\"\">(egal)</option>";
					$fas = FleetAction::getAll();
					foreach ($fas as $fa)
					{
						echo "<option value=\"".$fa->code()."\" style=\"color:".FleetAction::$attitudeColor[$fa->attitude()]."\"";
						if ($arr['action']==$fa->code())
							echo " selected=\"selected\"";

						echo ">".$fa->name()."</option>";
					}
					echo "</select> &nbsp; <select name=\"status\">";
					echo "<option value=\"\">(egal)</option>";
					foreach (FleetAction::$statusCode as $k => $v)
					{
						echo "<option value=\"".$k."\" ";
						if ($arr['status']==$k)
							echo " selected=\"selected\"";
						echo ">".$v."</option>";
					}
					echo "</select></td></tr>";

					// Usage
					echo "<tr><td style=\"background:#000;height:2px;\" colspan=\"2\"></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Piloten:</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"pilots\" value=\"".$arr['pilots']."\" size=\"10\" /></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Verbrauch: ".RES_FUEL.":</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"usage_fuel\" value=\"".$arr['usage_fuel']."\" size=\"10\" /></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Verbrauch: ".RES_FOOD.":</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"usage_food\" value=\"".$arr['usage_food']."\" size=\"10\" /></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Verbrauch: ".RES_POWER.":</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"usage_power\" value=\"".$arr['usage_power']."\" size=\"10\" /></td></tr>";

					// Freight
					echo "<tr><td style=\"background:#000;height:2px;\" colspan=\"2\"></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Fracht: ".RES_METAL.":</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"res_metal\" value=\"".$arr['res_metal']."\" size=\"10\" /></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Fracht: ".RES_CRYSTAL."::</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"res_crystal\" value=\"".$arr['res_crystal']."\" size=\"10\" /></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Fracht: ".RES_PLASTIC.":</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"res_plastic\" value=\"".$arr['res_plastic']."\" size=\"10\" /></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Fracht: ".RES_FUEL.":</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"res_fuel\" value=\"".$arr['res_fuel']."\" size=\"10\" /></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Fracht: ".RES_FOOD.":</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"res_food\" value=\"".$arr['res_food']."\" size=\"10\" /></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Fracht: ".RES_POWER.":</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"res_power\" value=\"".$arr['res_power']."\" size=\"10\" /></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Passagiere:</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"res_people\" value=\"".$arr['res_people']."\" size=\"10\" /></td></tr>";

					echo "<tr><td style=\"background:#000;height:2px;\" colspan=\"2\"></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Abholen: ".RES_METAL.":</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"fetch_metal\" value=\"".$arr['fetch_metal']."\" size=\"10\" /></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Abholen: ".RES_CRYSTAL."::</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"fetch_crystal\" value=\"".$arr['fetch_crystal']."\" size=\"10\" /></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Abholen: ".RES_PLASTIC.":</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"fetch_plastic\" value=\"".$arr['fetch_plastic']."\" size=\"10\" /></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Abholen: ".RES_FUEL.":</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"fetch_fuel\" value=\"".$arr['fetch_fuel']."\" size=\"10\" /></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Abholen: ".RES_FOOD.":</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"fetch_food\" value=\"".$arr['fetch_food']."\" size=\"10\" /></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Abholen: ".RES_POWER.":</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"fetch_power\" value=\"".$arr['fetch_power']."\" size=\"10\" /></td></tr>";
					echo "<tr>
						<td class=\"tbltitle\">Abholen: Passagiere:</td>
						<td class=\"tbldata\">
							<input type=\"text\" name=\"fetch_people\" value=\"".$arr['fetch_people']."\" size=\"10\" /></td></tr>";

					echo "</table><br/>
						<input type=\"submit\" value=\"&Uuml;bernehmen\" name=\"submit_edit\" /> ";
					if ($arr['status']==0)
					{
						echo "	<input type=\"submit\" value=\"Flug abbrechen\" name=\"submit_cancel\" /> 
						<input type=\"submit\" value=\"Flug zurückschicken\" name=\"submit_return\" /> 
						<input type=\"submit\" value=\"Flotte auf dem Ziel landen\" name=\"submit_land\" /> 
						";
					}
					else
					{
						echo "	<input type=\"submit\" value=\"Flotte auf dem Ziel landen\" name=\"submit_land\" /> 
						";
					}
					echo "</form><br/>";
					echo "<script>
					function submitForm()
					{
						//document.fleetform.submit();
					}
					</script>";


				// Ships
				echo "<h3>Schiffe der Flotte bearbeiten</h3>";
				if ($_POST['newship_submit']!="" && $_POST['fs_ship_cnt_new']>0 && $_POST['fs_ship_id_new']>0)
				{
					if (mysql_num_rows(dbquery("SELECT * FROM fleet_ships WHERE fs_fleet_id=".intval($_GET['fleetedit'])." AND fs_ship_id=".intval($_POST['fs_ship_id_new']).";"))<1)
						dbquery("INSERT INTO fleet_ships (fs_fleet_id,fs_ship_id,fs_ship_cnt) VALUES (".intval($_GET['fleetedit']).",".intval($_POST['fs_ship_id_new']).",".intval($_POST['fs_ship_cnt_new']).");");
					else
						dbquery("UPDATE fleet_ships SET fs_ship_cnt=fs_ship_cnt+".intval($_POST['fs_ship_cnt_new'])." WHERE fs_fleet_id=".intval($_GET['fleetedit'])." AND fs_ship_id=".intval($_POST['fs_ship_id_new']).";");
					success_msg("Schiffe hinzugefügt");
				}
				if ($_POST['editship_submit']!="")
				{
					foreach ($_POST['fs_ship_cnt'] as $ship=>$cnt)
					dbquery("UPDATE fleet_ships SET fs_ship_cnt=".intval($cnt)." WHERE fs_fleet_id=".intval($_GET['fleetedit'])." AND fs_ship_id=intval($ship);");
					success_msg("Schiffe geändert");
				}
				if (intval($_GET['shipdel'])>0)
				{
					dbquery("DELETE FROM fleet_ships WHERE fs_fleet_id=".intval($_GET['fleetedit'])." AND fs_ship_id=".intval($_GET['shipdel']).";");
					success_msg("Schiffe gelöscht");
				}

				$sres=dbquery("
				SELECT
		      ship_name,
		      ship_id,
		      fs_ship_cnt
				FROM
	       	fleet_ships
	     	INNER JOIN
	     		ships
				WHERE
	      	fs_ship_id=ship_id
	      	AND fs_fleet_id=".intval($_GET['fleetedit']));
				if (mysql_num_rows($sres)>0)
				{
					echo "<form action=\"?page=$page&amp;sub=$sub&amp;fleetedit=".intval($_GET['fleetedit'])."\" method=\"post\">";
					echo "<table class=\"tbl\">";
					echo "<tr><th class=\"tbltitle\">Typ</th><th class=\"tbltitle\">Anzahl</th><th class=\"tbltitle\">&nbsp;</th></tr>";
					while ($sarr=mysql_fetch_array($sres))
					{
						echo "<tr><td class=\"tbldata\">".$sarr['ship_name']."</td>";
						echo "<td class=\"tbldata\">
							<input type=\"text\" name=\"fs_ship_cnt[".$sarr['ship_id']."]\" value=\"".$sarr['fs_ship_cnt']."\" size=\"5\" /></td>";
						echo "<td class=\"tbldata\">
							<a href=\"?page=$page&amp;sub=$sub&amp;fleetedit=".$_GET['fleetedit']."&amp;shipdel=".$sarr['ship_id']."\" onclick=\"return confirm('Soll ".$sarr['ship_name']." wirklich aus der Flotte entfernt werden?');\">L&ouml;schen</a></td>";
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
	                    fleet_ships,
	                    ships
	                WHERE
	                    fs_ship_id=ship_id
	                    AND fs_fleet_id=".intval($_GET['fleetedit'])."
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


	                        	echo "<td class=\"tbldata\"><a href=\"?page=$page&amp;sub=$sub&amp;fleetedit=".$_GET['fleetedit']."&amp;shipdel=".$sfarr['ship_id']."\" onclick=\"return confirm('Soll ".$sfarr['ship_name']." wirklich aus der Flotte entfernt werden?');\">L&ouml;schen</a></td>";

	                        echo "</tr>";
						}

						echo "</table><br/>";
					}

					echo "<input type=\"submit\" name=\"editship_submit\" value=\"&Auml;nderungen &uuml;bernehmen\" />
					<br/><br/>";

					echo "<input type=\"text\" name=\"fs_ship_cnt_new\" value=\"1\" size=\"5\" /> Schiffe des Typs 
					<select name=\"fs_ship_id_new\">";
					$ssres=dbquery("SELECT ship_id,ship_name FROM ships ORDER BY ship_name;");
					while ($ssarr=mysql_fetch_array($ssres))
					{
						echo "<option value=\"".$ssarr['ship_id']."\">".$ssarr['ship_name']."</option>";
					}
					echo "</select> hinzuf&uuml;gen: 
					<input type=\"submit\" name=\"newship_submit\" value=\"Ausf&uuml;hren\" /></form><br/>";
				}
				else
				{
					echo MessageBox::error("", "Diese Flotte besitzt keine Schiffe!");
				}
				}
				else
				{
					echo MessageBox::error("", "Flotte nicht mehr vorhanden!");
				}

				echo "<br/><br/><input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&sub=$sub&action=searchresults'\" /> ";
				echo "<input type=\"button\" value=\"Neue zur Suche\" onclick=\"document.location='?page=$page&sub=$sub'\" />";

			}
			else
				echo MessageBox::error("", "Datensatz nicht vorhanden!");
		}

		//
		// Suchergebnisse anzeigen
		//
		elseif (isset($_POST['fleet_search']) || isset($_GET['action']) && $_GET['action']=="searchresults")
		{
			// Flotte löschen
			if (isset($_GET['fleetdel']) && intval($_GET['fleetdel'])>0)
			{
				dbquery("DELETE FROM fleet WHERE id='".intval($_GET['fleetdel'])."';");
				dbquery("DELETE FROM fleet_ships WHERE fs_fleet_id='".intval($_GET['fleetdel'])."';");
				echo MessageBox::ok("", "Die Flotte wurde gel&ouml;scht!");
			}

			// Suchquery zusammenstellen
			$sql="";
			if ($_SESSION['fleetedit']['query']=="")
			{
				if (intval($_POST['sx_start'])>0 && intval($_POST['sy_start'])>0 && intval($_POST['cx_start'])>0 && intval($_POST['cy_start'])>0 && $_POST['p_start']!="")
				{
					if ($srcEnt = Entity::createFactoryByCoords($_POST['sx_start'],$_POST['sy_start'],$_POST['cx_start'],$_POST['cy_start'],$_POST['p_start']))
					{
						$sql.=" AND entity_from=".$srcEnt->id()."";
					}
					else
					{
						error_msg("Startentität existiert nicht, Bedingung ausgelassen!");
					}
				}

				if (intval($_POST['sx_end'])>0 && intval($_POST['sy_end'])>0 && intval($_POST['cx_end'])>0 && intval($_POST['cy_end'])>0 && $_POST['p_end']!="")
				{
					if ($trgEnt = Entity::createFactoryByCoords($_POST['sx_end'],$_POST['sy_end'],$_POST['cx_end'],$_POST['cy_end'],$_POST['p_end']))
					{
						$sql.=" AND entity_to=".$trgEnt->id()."";
					}
					else
					{
						error_msg("Startentität existiert nicht, Bedingung ausgelassen!");
					}
				}

				if ($_POST['fleet_action']!="")
				{
					if ($_POST['fleet_action']=="-")
						$sql.=" AND action=''";
					else
						$sql.=" AND action='".$_POST['fleet_action']."'";
				}
				if ($_POST['entity_from_id']!="")
				{
					$sql.= " AND entity_from=".$_POST['entity_from_id']."";
				}
				if ($_POST['entity_to_id']!="")
				{
					$sql.= " AND entity_to=".$_POST['entity_to_id']."";
				}
				if ((int)$_POST['user_id'])
					$sql.=" AND user_id=".(int)$_POST['user_id'];
				if ($_POST['fleet_id']!="")
					$sql.=" AND id=".$_POST['id'];
				if ($_POST['user_nick']!="")
				{
					if (stristr($_POST['qmode']['user_nick'],"%"))
						$addchars = "%";else $addchars = "";
					$sql.= " AND user_nick ".stripslashes($_POST['qmode']['user_nick']).$_POST['user_nick']."$addchars'";
				}

				$sqlstart = "
				SELECT
          f.id,
          entity_from,
          entity_to,
          u.user_nick,
          action,
          status,
          launchtime,
          landtime
				FROM
        	fleet f
        LEFT JOIN
        	users u
        	ON f.user_id=u.user_id
        WHERE 
        	1
				";

				$sqlend = " ORDER BY ";

				switch ($_POST['fleet_order'])
				{
					case "launchtime":
						$sqlend.="launchtime DESC;";
						break;
					case "landtime":
						$sqlend.="landtime ASC;";
						break;
					case "user":
						$sqlend.="user_nick ASC;";
						break;
					case "action":
						$sqlend.="action ASC;";
						break;
					default:
						$sqlend.="landtime DESC;";
						break;
				}

				$sql = $sqlstart.$sql.$sqlend;

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

				echo "<table class=\"tbl\">";
				echo "<tr>";
				echo "<td class=\"tbltitle\">Besitzer</td>";
				echo "<td class=\"tbltitle\">Aktion</td>";
				echo "<td class=\"tbltitle\">Start</td>";
				echo "<td class=\"tbltitle\">Ziel</td>";
				echo "<td class=\"tbltitle\">Startzeit</td>";
				echo "<td class=\"tbltitle\">Landezeit</td>";
				echo "</tr>";
				while ($arr = mysql_fetch_array($res))
				{
					$stl="";
					if ($arr['landtime']< time())
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

					if ($fa = FleetAction::createFactory($arr['action']))
					{
					echo "<tr>";
					echo "<td class=\"tbldata\" $stl>".$owner."</td>";
					echo "<td class=\"tbldata\"><span style=\"color:".FleetAction::$attitudeColor[$fa->attitude()]."\">";
					echo $fa."</span><br/>";
					echo FleetAction::$statusCode[$arr['status']];
					echo "</td>";
					echo "<td class=\"tbldata\" $stl>";
					$startEntity = Entity::createFactoryById($arr['entity_from']);
					echo $startEntity."<br/>".$startEntity->entityCodeString().", ".$startEntity->owner()."</td>";
					echo "<td class=\"tbldata\" $stl>";
					$endEntity = Entity::createFactoryById($arr['entity_to']);
					echo $endEntity."<br/>".$endEntity->entityCodeString().", ".$endEntity->owner()."</td>";
					echo "<td class=\"tbldata\" $stl>".date("d.m.y",$arr['landtime'])." &nbsp; ".date("H:i:s",$arr['launchtime'])."</td>";
					echo "<td class=\"tbldata\" $stl>".date("d.m.y",$arr['landtime'])." &nbsp; ".date("H:i:s",$arr['landtime'])."</td>";
					echo "<td class=\"tbldata\">";
					echo edit_button("?page=$page&amp;sub=$sub&fleetedit=".$arr['id'])." ";
					echo del_button("?page=$page&amp;sub=$sub&fleetdel=".$arr['id']."&amp;action=searchresults","return confirm('Soll diese Flotte wirklich gel&ouml;scht werden?');");
					echo "</tr>";
					}
					else
					{
					echo "<tr>";
					echo "<td class=\"tbldata\" $stl>".$owner."</td>";
					echo "<td class=\"tbldata\"><span style=\"color:red\">";
					echo "Ungültig (".$arr['action'].")</span><br/>";
					echo "</td>";
					echo "<td class=\"tbldata\" $stl>";
					$startEntity = Entity::createFactoryById($arr['entity_from']);
					echo $startEntity."<br/>".$startEntity->entityCodeString().", ".$startEntity->owner()."</td>";
					echo "<td class=\"tbldata\" $stl>";
					$endEntity = Entity::createFactoryById($arr['entity_to']);
					echo $endEntity."<br/>".$endEntity->entityCodeString().", ".$endEntity->owner()."</td>";
					echo "<td class=\"tbldata\" $stl>".date("d.m.y",$arr['landtime'])." &nbsp; ".date("H:i:s",$arr['launchtime'])."</td>";
					echo "<td class=\"tbldata\" $stl>".date("d.m.y",$arr['landtime'])." &nbsp; ".date("H:i:s",$arr['landtime'])."</td>";
					echo "<td class=\"tbldata\">";
					echo edit_button("?page=$page&amp;sub=$sub&fleetedit=".$arr['id'])." ";
					echo del_button("?page=$page&amp;sub=$sub&fleetdel=".$arr['id']."&amp;action=searchresults","return confirm('Soll diese Flotte wirklich gel&ouml;scht werden?');");
					echo "</tr>";

					}
				}
				echo "</table>";
				echo "<br/><input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" /> ";
				echo "<input type=\"button\" value=\"Aktualisieren\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;action=searchresults'\" />";
			}
			else
			{
				$twig->addGlobal("infoMessage", "Die Suche lieferte keine Resultate!");
				echo "<p><input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" /></p>";
				$_SESSION['fleetedit']['query']=Null;
			}
		}

		//
		// Flottensuche
		//
		else
		{

			echo '<div class="tabs">
			<ul>
				<li><a href="#tabs-1">Suchmaske</a></li>
				<li><a href="#tabs-2">Flotte erstellen</a></li>
				<li><a href="#tabs-3">Schiffe senden</a></li>
			</ul>
			<div id="tabs-1">';

			// Search mask
			$_SESSION['fleetedit']['query']=Null;

			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
			echo "<table class=\"tbl\">";
			echo "<tr><td class=\"tbltitle\">Startentität-Koordinaten</td><td class=\"tbldata\"><select name=\"sx_start\">";
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
			echo "</select> : <select name=\"p_start\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=0;$x<=$conf['num_planets']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select></td></tr>";
			echo "<tr><td class=\"tbltitle\">Zielentität-Koordinaten</td><td class=\"tbldata\"><select name=\"sx_end\">";
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
			echo "</select> : <select name=\"p_end\">";
			echo "<option value=\"\">(egal)</option>";
			for ($x=0;$x<=$conf['num_planets']['p2'];$x++)
				echo "<option value=\"$x\">$x</option>";
			echo "</select>		
			</td></tr>";
			echo "<tr><td class=\"tbltitle\">Startentität-ID</td><td class=\"tbldata\"><input type=\"text\" name=\"entity_from_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Zielentität-ID</td><td class=\"tbldata\"><input type=\"text\" name=\"entity_to_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";


			echo "<tr><td class=\"tbltitle\">Flottenaktion</td><td class=\"tbldata\"><select name=\"fleet_action\">";
			echo "<option value=\"\">(egal)</option>";
			$fas = FleetAction::getAll();
			foreach ($fas as $fa)
			{
				echo "<option value=\"".$fa->code()."\">".$fa->name()."</option>";
			}
			echo "<option value=\"-\">(keine)</option>";
			echo "</select></td></tr>";
			echo "<tr><td class=\"tbltitle\">Flotten ID</td><td class=\"tbldata\"><input type=\"text\" name=\"fleet_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Besitzer ID</td><td class=\"tbldata\"><input type=\"text\" name=\"user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
			echo "<tr><td class=\"tbltitle\">Besitzer Nick</td><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('user_nick');echo "</td></tr>";

			echo "<tr>
				<td class=\"tbltitle\">Sortieren nach</td>
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
			$tblcnt = mysql_fetch_row(dbquery("SELECT count(id) FROM fleet;"));
			echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.";

			//
			// Create fleet
			//
			$ures=dbquery("SELECT user_id,user_nick FROM users ORDER BY user_nick;");
			$users=array();
			while ($uarr=mysql_fetch_array($ures))
			{
				$users[$uarr['user_id']]=$uarr['user_nick'];
			}

			echo '</div><div id="tabs-2">';

			if (isset($_POST['submit_new_fleet']))
			{
				if ($srcEnt = Entity::createFactoryByCoords($_POST['sx_start'],$_POST['sy_start'],$_POST['cx_start'],$_POST['cy_start'],$_POST['p_start']))
				{
					if ($trgEnt = Entity::createFactoryByCoords($_POST['sx_end'],$_POST['sy_end'],$_POST['cx_end'],$_POST['cy_end'],$_POST['p_end']))
					{
						$launchtime = parseDatePicker('launchtime', $_POST);
						$landtime = parseDatePicker('landtime', $_POST);

						dbquery("
						INSERT INTO 
							fleet
						(
						user_id,
						launchtime,
						landtime,
						entity_from,
						entity_to,
						action,
						status
						)
						VALUES
						( 
							'".intval($_POST['user_id'])."',
							".$launchtime.",
							".$landtime.",
							".$srcEnt->id().",
							".$trgEnt->id().",
							'".mysql_real_escape_string($_POST['action'])."',
							".intval($_POST['status'])."
						);");
						$fid = mysql_insert_id();
						dbquery("
						INSERT INTO 
							fleet_ships 
						(
							fs_fleet_id,
							fs_ship_id,
							fs_ship_cnt
						) 
						VALUES 
						(
							".$fid.",
							".intval($_POST['fs_ship_id_new']).",
							".intval($_POST['fs_ship_cnt_new'])."
						);");
						$twig->addGlobal('successMessage', "Neue Flotte erstellt! <a href=\"?page=$page&amp;sub=$sub&fleetedit=".$fid."\">Details</a>");
					}
					else
					{
						$twig->addGlobal('errorMessage', "Zielentität nicht vorhanden");
					}
				}
				else
				{
					$twig->addGlobal('errorMessage', "Startentität nicht vorhanden");
				}
			}

			echo "<form action=\"?page=$page\" method=\"post\" name=\"fleetform\">";
			echo "<table class=\"tbl\">";

			// Owner
			echo "<tr>
				<th class=\"tbltitle\">Besitzer:</th>
				<td class=\"tbldata\">
					<select name=\"user_id\"><option value=\"\" style=\"font-style:italic\">(niemand)</option>";
					foreach ($users as $id=>$val)
					{
						echo "<option value=\"$id\"";
						echo ">$val</option>";
					}
			echo "</select></td></tr>";

			// Time Data
			echo "<tr><th class=\"tbltitle\">Startzeit:</th><td class=\"tbldata\">";
			showDatepicker("launchtime",time()+10, true, true);
			echo "</td></tr>";
			echo "<tr><th class=\"tbltitle\">Landezeit:</th><td class=\"tbldata\">";
			showDatepicker("landtime",time()+90,  true, true);
			echo " </td></tr>";

			// Source and Target Data
			echo "<tr><td class=\"tbltitle\">Startzelle</td><td class=\"tbldata\">
			<select name=\"sx_start\" onchange=\"submitForm();\">";
			for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
			{
				echo "<option value=\"$x\"";
				echo ">$x</option>";
			}
			echo "</select>/<select name=\"sy_start\" onchange=\"submitForm();\">";
			for ($x=1;$x<=$conf['num_of_sectors']['p2'];$x++)
			{
				echo "<option value=\"$x\"";
				echo ">$x</option>";
			}
			echo "</select> : <select name=\"cx_start\" onchange=\"submitForm();\">";
			for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
			{
				echo "<option value=\"$x\"";
				echo ">$x</option>";
			}
			echo "</select>/<select name=\"cy_start\" onchange=\"submitForm();\">";
			for ($x=1;$x<=$conf['num_of_cells']['p2'];$x++)
			{
				echo "<option value=\"$x\"";
				echo ">$x</option>";
			}
			echo "</select> : <select name=\"p_start\" onchange=\"submitForm();\">";
			for ($x=0;$x<=$conf['num_planets']['p2'];$x++)
			{
				echo "<option value=\"$x\"";
				echo ">$x</option>";
			}
			echo "</select> ";

			echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">Endzelle</td><td class=\"tbldata\">
			<select name=\"sx_end\" onchange=\"submitForm();\">";
			for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
			{
				echo "<option value=\"$x\"";
				echo ">$x</option>";
			}
			echo "</select>/<select name=\"sy_end\" onchange=\"submitForm();\">";
			for ($x=1;$x<=$conf['num_of_sectors']['p2'];$x++)
			{
				echo "<option value=\"$x\"";
				echo ">$x</option>";
			}
			echo "</select> : <select name=\"cx_end\" onchange=\"submitForm();\">";
			for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
			{
				echo "<option value=\"$x\"";
				echo ">$x</option>";
			}
			echo "</select>/<select name=\"cy_end\" onchange=\"submitForm();\">";
			for ($x=1;$x<=$conf['num_of_cells']['p2'];$x++)
			{
				echo "<option value=\"$x\"";
				echo ">$x</option>";
			}
			echo "</select>  : <select name=\"p_end\" onchange=\"submitForm();\">";
			for ($x=0;$x<=$conf['num_planets']['p2'];$x++)
			{
				echo "<option value=\"$x\"";
				echo ">$x</option>";
			}
			echo "</select>";

			// Action
			echo "</td></tr>";
			echo "<tr><td class=\"tbltitle\">Aktion:</td><td class=\"tbldata\"><select name=\"action\">";
			$fas = FleetAction::getAll();
			foreach ($fas as $fa)
			{
				echo "<option value=\"".$fa->code()."\" style=\"color:".FleetAction::$attitudeColor[$fa->attitude()]."\"";
				echo ">".$fa->name()."</option>";
			}
			echo "</select> &nbsp; <select name=\"status\">";
			foreach (FleetAction::$statusCode as $k => $v)
			{
				echo "<option value=\"".$k."\" ";
				echo ">".$v."</option>";
			}
			echo "</select></td></tr>";
			echo "<tr>
			<td class=\"tbltitle\">Schiffe:</td>
				<td class=\"tbldata\">
					<input type=\"text\" name=\"fs_ship_cnt_new\" value=\"1\" size=\"5\" /> 
					<select name=\"fs_ship_id_new\">";
					$ssres=dbquery("SELECT ship_id,ship_name FROM ships ORDER BY ship_name;");
					while ($ssarr=mysql_fetch_array($ssres))
					{
						echo "<option value=\"".$ssarr['ship_id']."\">".$ssarr['ship_name']."</option>";
					}
				echo "</select></td></tr>";
			echo "</table><br/>
			<input type=\"submit\" value=\"Erstellen\" name=\"submit_new_fleet\" /> ";

			echo '</div><div id="tabs-3">';

			if (isset($_POST['submit_send_ships']))
			{
				if ($_POST['fs_ship_id_new'] > 0) {
					if ($srcEnt = Entity::createFactoryByCoords($_POST['sx_start'],$_POST['sy_start'],$_POST['cx_start'],$_POST['cy_start'],$_POST['p_start']))
					{
						$mpres = dbquery("
						SELECT
							id,
							planet_user_id
						FROM
							planets
						WHERE
							planet_user_main=1");
						$fi = 0;
						while ($mparr = mysql_fetch_assoc($mpres)) {

							$launchtime = parseDatePicker('launchtime', $_POST);
							$landtime = parseDatePicker('landtime', $_POST);

							dbquery("
							INSERT INTO
								fleet
							(
							user_id,
							launchtime,
							landtime,
							entity_from,
							entity_to,
							action,
							status
							)
							VALUES
							(
								'".$mparr['planet_user_id']."',
								".$launchtime.",
								".$landtime.",
								".$srcEnt->id().",
								".$mparr['id'].",
								'flight',
								1
							);");
							$fid = mysql_insert_id();
							dbquery("
							INSERT INTO
								fleet_ships
							(
								fs_fleet_id,
								fs_ship_id,
								fs_ship_cnt
							)
							VALUES
							(
								".$fid.",
								".intval($_POST['fs_ship_id_new']).",
								".intval($_POST['fs_ship_cnt_new'])."
							);");
							$fi++;
						}
						$twig->addGlobal('successMessage', "$fi Flotten erstellt!");
					}
					else
					{
						$twig->addGlobal('errorMessage', "Startentität nicht vorhanden");
					}
				}
				else
				{
					$twig->addGlobal('errorMessage', "Schiffstyp nicht ausgewählt!");
				}
			}

			echo "<form action=\"?page=$page\" method=\"post\" name=\"fleetform\">";
			echo "<table class=\"tbl\">";

			// Time Data
			echo "<tr><th clas s=\"tbltitle\">Startzeit:</th><td class=\"tbldata\">";
			showDatepicker("launchtime",time()+10, true, true);
			echo "</td></tr>";
			echo "<tr><th class=\"tbltitle\">Landezeit:</th><td class=\"tbldata\">";
			showDatepicker("landtime",time()+90,  true, true);
			echo " </td></tr>";

			// Source and Target Data
			echo "<tr><th class=\"tbltitle\">Startzelle:</th><td class=\"tbldata\">
			<select name=\"sx_start\" onchange=\"submitForm();\">";
			for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
			{
				echo "<option value=\"$x\"";
				echo ">$x</option>";
			}
			echo "</select>/<select name=\"sy_start\" onchange=\"submitForm();\">";
			for ($x=1;$x<=$conf['num_of_sectors']['p2'];$x++)
			{
				echo "<option value=\"$x\"";
				echo ">$x</option>";
			}
			echo "</select> : <select name=\"cx_start\" onchange=\"submitForm();\">";
			for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
			{
				echo "<option value=\"$x\"";
				echo ">$x</option>";
			}
			echo "</select>/<select name=\"cy_start\" onchange=\"submitForm();\">";
			for ($x=1;$x<=$conf['num_of_cells']['p2'];$x++)
			{
				echo "<option value=\"$x\"";
				echo ">$x</option>";
			}
			echo "</select> : <select name=\"p_start\" onchange=\"submitForm();\">";
			for ($x=0;$x<=$conf['num_planets']['p2'];$x++)
			{
				echo "<option value=\"$x\"";
				echo ">$x</option>";
			}
			echo "</select> ";

			echo "</td></tr>";

			echo "<tr><th>Ziel:</th><td>Hauptplanet jedes Spielers</td></tr>";

			echo "<tr>
			<td class=\"tbltitle\">Schiffe:</td>
				<td class=\"tbldata\">
					<input type=\"text\" name=\"fs_ship_cnt_new\" value=\"1\" size=\"5\" />
					<select name=\"fs_ship_id_new\">";
					echo "<option value=\"0\">Schiff wählen...</option>";
					$ssres=dbquery("SELECT ship_id,ship_name FROM ships ORDER BY ship_name;");
					while ($ssarr=mysql_fetch_array($ssres))
					{
						echo "<option value=\"".$ssarr['ship_id']."\">".$ssarr['ship_name']."</option>";
					}
				echo "</select></td></tr>";
			echo "</table><br/>
			<input type=\"submit\" value=\"Erstellen\" name=\"submit_send_ships\" /> ";

			echo '</div>
			</div>';

		}
	}
?>
