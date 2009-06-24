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
	// 	Dateiname: logs.php
	// 	Topic: Verwaltung der Log-Einträge
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar:
	//

	$lres=dbquery("SELECT cat_id,cat_name,COUNT(*) as cnt FROM log_cat,logs WHERE log_cat=cat_id GROUP BY cat_id;;");
	$log_type=array();
	while ($larr=mysql_fetch_array($lres))
	{
		$log_type[$larr['cat_id']]['name']=$larr['cat_name'];
		$log_type[$larr['cat_id']]['cnt']=$larr['cnt'];
	}

	echo "<h1>Logs</h1>";

	echo "<div id=\"logsinfo\"></div>"; //nur zu entwicklungszwecken!
	
	//
	// ??
	// 
	if (isset($_POST['alliance_search']) && $_POST['alliance_search']!="" || isset($_GET['action']) && $_GET['action']=="searchresults")
	{
		if ($_SESSION['logs']['query']=="")
		{
			$sqlstart = "SELECT * FROM logs,log_cat WHERE log_cat=cat_id ";
			$sqlend = " ORDER BY log_realtime DESC, log_timestamp DESC";
			if ($_POST['limit']>0)
				$sqlend.=" LIMIT ".$_POST['limit'].";";
			if ($_POST['log_text']!="")
			{
				if (stristr($_POST['qmode']['log_text'],"%")) $addchars = "%";else $addchars = "";
				$sql.= " AND log_text ".stripslashes($_POST['qmode']['log_text']).$_POST['log_text']."$addchars'";
			}						
			if ($_POST['log_text2']!="")
			{
				if (stristr($_POST['qmode']['log_text2'],"%")) $addchars = "%";else $addchars = "";
				$sql.= " AND log_text ".stripslashes($_POST['qmode']['log_text2']).$_POST['log_text2']."$addchars'";
			}							
			if ($_POST['log_hostname']!="")
			{
				if (stristr($_POST['qmode']['log_hostname'],"%")) $addchars = "%";else $addchars = "";
				$sql.= " AND log_hostname ".stripslashes($_POST['qmode']['log_hostname']).$_POST['log_hostname']."$addchars'";
			}							
			if ($_POST['log_ip']!="")
			{
				if (stristr($_POST['qmode']['log_ip'],"%")) $addchars = "%";else $addchars = "";
				$sql.= " AND log_ip ".stripslashes($_POST['qmode']['log_ip']).$_POST['log_ip']."$addchars'";
			}							
			if ($_POST['log_cat']>0)
			{
				$sql.=" AND log_cat=".$_POST['log_cat'];
			}
			$sql=$sqlstart.$sql.$sqlend;
			$_SESSION['logs']['query']=$sql;
		}
		else
			$sql=$_SESSION['logs']['query'];

		$res = dbquery($sql);
		if (mysql_num_rows($res)>0)
		{
			echo mysql_num_rows($res)." Datens&auml;tze vorhanden<br/><br/>";
			if (mysql_num_rows($res)>20)
				echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page'\" /><br/><br/>";

			echo "<table class=\"tbl\">";
			echo "<tr>";
			echo "<td class=\"tbltitle\" valign=\"top\">Zeit</td>";
			echo "<td class=\"tbltitle\" valign=\"top\">Kategorie</td>";
			echo "<td class=\"tbltitle\" valign=\"top\">Text</td>";
			echo "<td class=\"tbltitle\" valign=\"top\">Computer</td>";
			echo "<td>&nbsp;</td>";
			echo "</tr>";
			while ($arr = mysql_fetch_array($res))
			{
				echo "<tr>";
				echo "<td class=\"tbldata\" valign=\"top\"><b>".date("d.m.Y H:i:s",$arr['log_timestamp'])."</b><br/>";
				if ($arr['log_realtime']>0)
					echo date("Y-m-d H:i:s",$arr['log_realtime']);
				else
					echo "-";
				echo "</td>";
				echo "<td class=\"tbldata\">".$arr['cat_name']."</td>";
				echo "<td class=\"tbldata\" valign=\"top\">".text2html(cut_string($arr['log_text'],300))."</td>";
				echo "<td class=\"tbldata\" valign=\"top\">".$arr['log_ip']." ".$arr['log_hostname']."</td>";
				echo "<td class=\"tbldata\" valign=\"top\"><a href=\"?page=$page&amp;sub=view&amp;log_id=".$arr['log_id']."\">detail</a></td>";
				echo "</tr>";
			}
			echo "</table>";
			echo "<br/><input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page'\" />";
		}
		else
		{
			echo "Die Suche lieferte keine Resultate!<br/><br/>";
			echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Zur&uuml;ck\" /><br/><br/>";
		}
	}
	
	//
	// ??
	// 
	elseif (isset($_GET['sub']) && $_GET['sub']=="view")
	{
		$res = dbquery("SELECT * FROM logs,log_cat WHERE log_cat=cat_id AND log_id=".$_GET['log_id'].";");
		$arr = mysql_fetch_array($res);
		echo "<table class=\"tbl\">";
		echo "<tr><td class=\"tbltitle\" valign=\"top\">Zeit</td><td class=\"tbldata\">".date("Y-m-d H:i:s",$arr['log_timestamp'])."</td></tr>";
		echo "<tr><td class=\"tbltitle\" valign=\"top\">Realzeit</td><td class=\"tbldata\">";
		if ($arr['log_realtime']>0)
			echo date("Y-m-d H:i:s",$arr['log_realtime']);
		else
			echo "-";
		echo "</td></tr>";
		echo "<tr><td class=\"tbltitle\" valign=\"top\">Kategorie</td><td class=\"tbldata\">".$arr['cat_name']."</td></tr>";
		echo "<tr><td class=\"tbltitle\" valign=\"top\">Text</td><td class=\"tbldata\">".text2html($arr['log_text'])."</td></tr>";
		echo "<tr><td class=\"tbltitle\" valign=\"top\">Hostname</td><td class=\"tbldata\">".$arr['log_hostname']."</td></tr>";
		echo "<tr><td class=\"tbltitle\" valign=\"top\">IP</td><td class=\"tbldata\">".$arr['log_ip']."</td></tr>";
		echo "</table>";
		echo "<br/><input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page&action=searchresults'\" /> ";
		echo "<input type=\"button\" value=\"Neue Suche\" onclick=\"document.location='?page=$page'\" />";
	}

	//
	// Battle log
	//
	elseif (isset($_GET['sub']) && $_GET['sub']=="logs_battle")
	{
		echo "Battle Log im aufbau!<br>";
	}

	//
	// Game log
	//
	elseif (isset($_GET['sub']) && $_GET['sub']=="logs_game")
	{
		$lres=dbquery("
		SELECT 
			logs_game_cat_id,
			logs_game_cat_name,
			COUNT(logs_game_id) as cnt 
		FROM 
			logs_game_cat
			INNER JOIN
			logs_game 
			ON logs_game_cat=logs_game_cat_id	
		GROUP BY 
			logs_game_cat_id;");
		$logs_game_type=array();
		while ($larr=mysql_fetch_array($lres))
		{
			$logs_game_type[$larr['logs_game_cat_id']]['name']=$larr['logs_game_cat_name'];
			$logs_game_type[$larr['logs_game_cat_id']]['cnt']=$larr['cnt'];
		}			
		
		
		$_SESSION['logs']['query']=Null;
		echo "Suchmaske:<br/><br/>";
		echo "<form action=\"?page=$page\" method=\"post\">";
		echo "<table class=\"tbl\">";
		
		echo "<tr><td class=\"tbltitle\">Kategorie</td><td class=\"tbldata\"><select name=\"logs_game_cat\">";
		echo "<option value=\"0\">(nicht zugeordnet)</option>";
		foreach ($logs_game_type as $id=>$val)
		{
			echo "<option value=\"$id\">".$val['name']." (".$val['cnt'].")</option>";
		}
		echo "</select></td></tr>";			
		
		echo "<tr><td class=\"tbltitle\">Planetenname</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_name\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('planet_name');echo "</td></tr>";
		echo "<tr><td class=\"tbltitle\">Planeten-ID</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
		echo "<tr><td class=\"tbltitle\">Koordinaten</td><td class=\"tbldata\"><select name=\"cell_sx\">";
		echo "<option value=\"\">(egal)</option>";
		for ($x=1;$x<=$conf['num_of_sectors']['p1'];$x++)
			echo "<option value=\"$x\">$x</option>";
		echo "</select>/<select name=\"cell_sy\">";
		echo "<option value=\"\">(egal)</option>";
		for ($x=1;$x<=$conf['num_of_sectors']['p2'];$x++)
			echo "<option value=\"$x\">$x</option>";
		echo "</select> : <select name=\"cell_cx\">";
		echo "<option value=\"\">(egal)</option>";
		for ($x=1;$x<=$conf['num_of_cells']['p1'];$x++)
			echo "<option value=\"$x\">$x</option>";
		echo "</select>/<select name=\"cell_cy\">";
		echo "<option value=\"\">(egal)</option>";
		for ($x=1;$x<=$conf['num_of_cells']['p2'];$x++)
			echo "<option value=\"$x\">$x</option>";
		echo "</select> : <select name=\"planet_solsys_pos\">";
		echo "<option value=\"\">(egal)</option>";
		for ($x=1;$x<=$conf['num_planets']['p2'];$x++)
			echo "<option value=\"$x\">$x</option>";
		echo "</select></td></tr>";
		echo "<tr><td class=\"tbltitle\">Besitzer-ID</td><td class=\"tbldata\"><input type=\"text\" name=\"planet_user_id\" value=\"\" size=\"20\" maxlength=\"250\" /></td>";
		echo "<tr><td class=\"tbltitle\">Besitzer</td><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('user_nick');echo "</td></tr>";
		echo "<tr><td class=\"tbltitle\">Allianz-Tag</td><td class=\"tbldata\"><input type=\"text\" name=\"alliance_tag\" value=\"\" size=\"20\" maxlength=\"250\" /> ";fieldqueryselbox('alliance_tag');echo "</td></tr>";
		echo "</table></form>";
	}	
	
	//
	// ??
	//
	elseif (isset($_POST['logs_submit']) && $_POST['logs_submit']!="" && checker_verify())
	{
		$sql_query = stripslashes($_POST['sql_query']);
		
		
		if ($_POST['log_cat']=="logs")
		{
			echo "allgemeine logs anzeigen...";
		}
		elseif ($_POST['log_cat']=="logs_fleet")
		{
			$res = dbquery($sql_query);

			tableStart("".mysql_num_rows($res)." Ergebnisse");
			echo "<tr>
					<td class=\"tbltitle\" >Besitzer</td>
					<td class=\"tbltitle\" >Aktion</td>
					<td class=\"tbltitle\" >Start</td>
					<td class=\"tbltitle\" >Ziel</td>
					<td class=\"tbltitle\" >Startzeit</td>
					<td class=\"tbltitle\" >Landezeit</td>
					<td class=\"tbltitle\" >Bericht</td>
				</tr>";		
			while($arr=mysql_fetch_array($res))
			{	
				$user_nick = get_user_nick($arr["logs_fleet_fleet_user_id"]);
				if ($user_nick=="")
				{
					$owner = "<span style=\"color:#99f\">System</span>";
				}
				else
				{
					$owner = $user_nick;
				}

				if ($fa = FleetAction::createFactory($arr['logs_fleet_action']))
				{
					echo "<tr>";
					echo "<td class=\"tbldata\">".$owner."</td>";
					echo "<td class=\"tbldata\"><span style=\"color:".FleetAction::$attitudeColor[$fa->attitude()]."\">";
					echo $fa."</span><br/>";
					echo FleetAction::$statusCode[$arr['logs_fleet_status']];
					echo "</td>";
					echo "<td class=\"tbldata\" >";
						$startEntity = Entity::createFactoryById($arr['logs_fleet_entity_from']);
					echo $startEntity."<br/>".$startEntity->entityCodeString().", ".$startEntity->owner()."</td>";
					echo "<td class=\"tbldata\">";
						$endEntity = Entity::createFactoryById($arr['logs_fleet_entity_to']);
					echo $endEntity."<br/>".$endEntity->entityCodeString().", ".$endEntity->owner()."</td>";
					echo "<td class=\"tbldata\" >".date("d.m.y",$arr['logs_fleet_landtime'])." &nbsp; ".date("H:i:s",$arr['logs_fleet_landtime'])."</td>";
					echo "<td class=\"tbldata\" >".date("d.m.y",$arr['logs_fleet_landtime'])." &nbsp; ".date("H:i:s",$arr['logs_fleet_landtime'])."</td>";
				}
				else
				{
					echo "<tr>";
					echo "<td class=\"tbldata\" >".$owner."</td>";
					echo "<td class=\"tbldata\"><span style=\"color:red\">";
					echo "Ungültig (".$arr['logs_fleet_action'].")</span><br/>";
					echo "</td>";
					echo "<td class=\"tbldata\" >";
						$startEntity = Entity::createFactoryById($arr['logs_fleet_entity_from']);
					echo $startEntity."<br/>".$startEntity->entityCodeString().", ".$startEntity->owner()."</td>";
					echo "<td class=\"tbldata\" >";
						$endEntity = Entity::createFactoryById($arr['logs_fleet_entity_to']);
					echo $endEntity."<br/>".$endEntity->entityCodeString().", ".$endEntity->owner()."</td>";
					echo "<td class=\"tbldata\" >".date("d.m.y",$arr['logs_fleet_landtime'])." &nbsp; ".date("H:i:s",$arr['logs_fleet_launchtime'])."</td>";
					echo "<td class=\"tbldata\" >".date("d.m.y",$arr['logs_fleet_landtime'])." &nbsp; ".date("H:i:s",$arr['logs_fleet_landtime'])."</td>";
				}
				
				$log_text = "hamer";
				echo "<td class=\"tbldata\" onclick=\"xajax_showFleetLogs('".$log_text."',".$arr['logs_fleet_id'].");\" ".mTT("","Klicken für Anzeige des Berichtes!").">
									<a href=\"javascript:;\">Anzeigen</a>
								</td>
							</tr>
							<tr>
								<td class=\"tbldata\" id=\"show_fleet_logs_".$arr['logs_fleet_id']."\" style=\"vertical-align:middle;\" colspan=\"7\" ondblclick=\"xajax_showFleetLogs('".$log_text."',".$arr['logs_fleet_id'].");\" ".mTT("","Doppelklick zum deaktivieren des Fensters!").">
								</td>
							</tr>";
			}
			tableEnd();
		}
		elseif ($_POST['log_cat']=="logs_battle")
		{			
			echo "Legende:<br/>
			<span style=\"color:#0f0;font-weight:bold;\">Grüner Nick</span> = Flotte hat überlebt<br/>
			<span style=\"color:red;font-weight:bold;\">Roter Nick</span> = Flotte wurde zerstört<br><br>";
			
			$res = dbquery($sql_query);

			tableStart("".mysql_num_rows($res)." Ergebnisse");
			echo "<tr>
							<td class=\"tbltitle\" style=\"width:26%\">Zeit</td>
							<td class=\"tbltitle\" style=\"width:18%\">Krieg?</td>
							<td class=\"tbltitle\" style=\"width:18%\">Zählt als Angriff?</td>
							<td class=\"tbltitle\" style=\"width:18%\">Aktion</td>
							<td class=\"tbltitle\" style=\"width:20%\">Bericht</td>
						</tr>";		
			while($arr=mysql_fetch_array($res))
			{			
				$alliance_tag_a = "";
				$alliance_tag_d = "";
				
				if($arr['logs_battle_user1_alliance_id']>0)
				{
					$alliance_tag_a = " [".$arr['logs_battle_user1_alliance_tag']."]";
				}
				
				if($arr['logs_battle_user2_alliance_id']>0)
				{
					$alliance_tag_d = " [".$arr['logs_battle_user2_alliance_tag']."]";
				}
					
				// Erstellt KB-Header (Kontrahenten mit Winner/Looser)
				switch ($arr['logs_battle_result'])
				{
					case 1:	//angreifer hat gewonnen
						$header_user_a = "<span style=\"color:#0f0;\">".get_user_nick($arr['logs_battle_user1_id'])."</span>".$alliance_tag_a."";
						$header_user_d = "<span style=\"color:red;\">".get_user_nick($arr['logs_battle_user2_id'])."</span>".$alliance_tag_d."";
						break;
					case 2:	//agreifer hat verloren
						$header_user_a = "<span style=\"color:red;\">".get_user_nick($arr['logs_battle_user1_id'])."</span>".$alliance_tag_a."";
						$header_user_d = "<span style=\"color:#0f0;\">".get_user_nick($arr['logs_battle_user2_id'])."</span>".$alliance_tag_d."";
						break;
					case 3:	//beide flotten haben überlebt
						$header_user_a = "<span style=\"color:#0f0;\">".get_user_nick($arr['logs_battle_user1_id'])."</span>".$alliance_tag_a."";
						$header_user_d = "<span style=\"color:#0f0;\">".get_user_nick($arr['logs_battle_user2_id'])."</span>".$alliance_tag_d."";
						break;
					case 4: //beide flotten sind kaputt
						$header_user_a = "<span style=\"color:red;\">".get_user_nick($arr['logs_battle_user1_id'])."</span>".$alliance_tag_a."";
						$header_user_d = "<span style=\"color:red;\">".get_user_nick($arr['logs_battle_user2_id'])."</span>".$alliance_tag_d."";
						break;
				}	
			
				// Krieg?
				if($arr['logs_battle_alliances_have_war']==1)	
				{
					$war = "<div style=\"color:red;font-weight:bold;\">Ja</div>";
				}
				else
				{
					$war = "Nein";
				}
									
				// Zählt der Angriff als Angriff? (Waffen>0)
				if($arr['logs_battle_user1_weapon']>0)
				{
					$attack = "Ja";
				}
				else
				{
					$attack = "<div style=\"color:red;font-weight:bold;\">Nein</div>";
				}
				
				$battle = text2html($arr['logs_battle_fight']);

				echo "<tr>
								<td class=\"tbltitle\" style=\"vertical-align:middle\" colspan=\"5\">
								".$header_user_a." VS. ".$header_user_d."
								</td>
							</tr>
							<tr>
								<td class=\"tbldata\">
									<b>".date("Y-m-d H:i:s",$arr['logs_battle_fleet_landtime'])."</b><br>".date("Y-m-d H:i:s",$arr['logs_battle_time'])."
								</td>
								<td class=\"tbldata\">".$war."</td>
								<td class=\"tbldata\">".$attack."</td>
								<td class=\"tbldata\">".$arr['logs_battle_fleet_action']."</td>
								<td class=\"tbldata\" onclick=\"xajax_showBattle('".$battle."',".$arr['logs_battle_id'].");\" ".mTT("","Klicken für Anzeige des Berichtes!").">
									<a href=\"javascript:;\">Anzeigen</a>
								</td>
							</tr>
							<tr>
								<td class=\"tbldata\" id=\"show_battle_".$arr['logs_battle_id']."\" style=\"vertical-align:middle;\" colspan=\"5\" ondblclick=\"xajax_showBattle('',".$arr['logs_battle_id'].");\" ".mTT("","Doppelklick zum deaktivieren des Fensters!").">
								</td>
							</tr>
							";
			}
			
			
			tableEnd();
		}
		elseif ($_POST['log_cat']=="logs_game")
		{
			echo "<form action=\"?page=".$page."\" method=\"post\">";
			
			$res = dbquery($sql_query);

			tableStart("".mysql_num_rows($res)." Ergebnisse");
			echo "<tr>
							<td class=\"tbltitle\" style=\"width:26%\">Zeit</td>
							<td class=\"tbltitle\" style=\"width:18%\">Kategorie</td>
							<td class=\"tbltitle\" style=\"width:18%\">User</td>
							<td class=\"tbltitle\" style=\"width:18%\">Objekt</td>
							<td class=\"tbltitle\" style=\"width:20%\">Bericht</td>
						</tr>";		
			while($arr=mysql_fetch_array($res))
			{	
				//Objekt laden
				if($arr['logs_game_building_id']!=0)
				{
					$bres=dbquery("
					SELECT 
						building_name
					FROM
						buildings
					WHERE
						building_id='".$arr['logs_game_building_id']."';");
						
					if(mysql_num_rows($bres)>0)
					{
						$barr=mysql_fetch_array($bres);
						$object = $barr['building_name'];
						
					}
					else
					{
						$object = "Gebäude?";
					}
				}
				elseif($arr['logs_game_tech_id']!=0)
				{
					$tres=dbquery("
					SELECT 
						tech_name
					FROM
						technologies
					WHERE
						tech_id='".$arr['logs_game_tech_id']."';");
						
					if(mysql_num_rows($tres)>0)
					{
						$tarr=mysql_fetch_array($tres);
						$object = $tarr['tech_name'];
						
					}
					else
					{
						$object = "Forschung?";
					}						
				}
				else
				{
					$object = "";
				}
				
				$log_text = text2html(encode_logtext($arr['logs_game_text']));
				
				echo "<tr>
								<td class=\"tbldata\">
									<b>".date("Y-m-d H:i:s",$arr['logs_game_timestamp'])."</b><br>".date("Y-m-d H:i:s",$arr['logs_game_realtime'])."
								</td>
								<td class=\"tbldata\">".$arr['logs_game_cat_name']."</td>
								<td class=\"tbldata\">".get_user_nick($arr['logs_game_user_id'])."</td>
								<td class=\"tbldata\">".$object."</td>
								<td class=\"tbldata\" onclick=\"xajax_showGameLogs('".$log_text."',".$arr['logs_game_id'].");\" ".mTT("","Klicken für Anzeige des Berichtes!").">
									<a href=\"javascript:;\">Anzeigen</a>
								</td>
							</tr>
							<tr>
								<td class=\"tbldata\" id=\"show_game_logs_".$arr['logs_game_id']."\" style=\"vertical-align:middle;\" colspan=\"5\" ondblclick=\"xajax_showGameLogs('',".$arr['logs_game_id'].");\" ".mTT("","Doppelklick zum deaktivieren des Fensters!").">
								</td>
							</tr>";
			}
			
			tableEnd();
		}
		
	}
	
	//
	// Neue Log Seite
	// 
	elseif (isset($_GET['sub']) && $_GET['sub']=="new_logs_page")
	{
		echo "<h1>Neue Log Seite!</h1><br>";
		
		echo "Suchmaske:<br/><br/>";
		echo "<form action=\"?page=$page\" method=\"post\" id=\"log_selector\">";
		$cstr = checker_init();
		
		tableStart();
		
		//Kategorie wählen
		echo "<tr>
						<td class=\"tbltitle\">Kategorie</td><td class=\"tbldata\">
							<select name=\"log_cat\" onChange=\"xajax_logSelectorCat(xajax.getFormValues('log_selector'),1);\">
								<option value=\"0\">(nicht zugeordnet)</option>
								<option value=\"logs\">Allgemeine</option>
								<option value=\"logs_fleet\">Flotten</option>
								<option value=\"logs_battle\">Kampfberichte</option>
								<option value=\"logs_game\">Game</option>
							</select>
						</td>
					</tr>";							
		tableEnd();	
		
		// Suchformular
		echo "<div id=\"catSelector\"></div>";			
		echo "</form>";		
	}		
	
	//
	//Angriffsverletzung
	//
	elseif (isset($_GET['sub']) && $_GET['sub']=="check_fights")
	{
		$can_attack_on_one_planet = 3; 					// Max. 3er Wellen...
		$can_attack_on_one_planet_war = 4;			// Max. 4er Wellen im Krieg...
		$can_attack_on_one_planet_time = 15*60;	// ...innerhalb 15mins
		
		$can_attack_on_one_planet_again = 2; 		// Max. 2 mal den gleichen Planeten angreiffen
		$can_attack_on_one_planet_again_war = 4;// Max. 4 mal den gleichen Planeten angreiffen im Krieg
		
		$can_attack_planets = 5;								// Max. Anzahl Planeten die angegriffen werden können...
		$can_attack_planets_war = 10;						// Max. Anzahl Planeten die angegriffen werden können im Krieg...
		$can_attack_planets_time = 6*3600;			// ...innerhalb 6h
		
		$can_attack_total_time = 24*3600;				// alle Regeln gelten innerhalb von 24h
		
		$first_ban_time = 12*3600;							// Sperrzeit beim ersten Vergehen: 12h
		$add_ban_time = 12*3600;								// Sperrzeit bei jedem weiteren Vergehen: 12h (wird immer dazu addiert)
		
		
		
		echo "Angriffsverletzung (Test tool)<br><br><br>";
		
		// Alle Kampfberichte, die laut Regeln als Angriff zählen (Waffen > 0), aus den letzten 24h Stunden werden ausgelesen. KBs, die schon einmal zu einer Sperre geführt haben, werden nicht noch ein 2. mal verarbeitet.
		
		// Die Sortierung (ORDER BY) ist entscheidend für die Funtkionalität des Scripts. Sie wird wie folgt sortiert.
		// 1. User (Opfer)
		// 2. Angegriffener Planet
		// 3. Angriffszeit			
		$res=dbquery("
		SELECT 
			logs_battle_id,
			logs_battle_user1_id,
			logs_battle_user2_id,
			logs_battle_alliances_have_war,
			logs_battle_planet_id,
			logs_battle_fleet_landtime
		FROM 
			logs_battle
		WHERE
			logs_battle_fleet_landtime>".(time()-$can_attack_total_time)."
			AND logs_battle_bann_mark='0'
			AND logs_battle_user1_weapon>0
		ORDER BY
			logs_battle_user1_id ASC,
			logs_battle_planet_id ASC,
			logs_battle_fleet_landtime ASC;");
			
		$user_id = 0;							// Das Opfer
		$planet_id = 0;						// Der angegriffene Planet
		
		$first_time = 0;					// Aller erster Angriffszeitpunkt
		$first_planet_time = 0;   // Erster Angriffszeitpunkt vom Planeten X
		$last_planet_time = 0;		// Letzter Angriffszeitpunkz vom Planet X (in der letzten Schleife gleichbedeutend mit absolut letzter Angriff (pendant zu $first_time)
		
		$attack_cnt_total = 0;								// Zählt das Total an Angriffen (3er Welle = 1 Angriff)
		$attack_cnt_planet_total = array();		// Notiert die Angegriffenen Planeten
		$attack_cnt_planet = 0;								// Zählt die Anzahl Angriffe auf einen Planet
		
		if (mysql_num_rows($res)>0)
		{			
			// Alle gefundenen und sortierten Datensätze werden in einer Schleife ausgegeben und verarbeitet
			echo "".mysql_num_rows($res)." Datensätze!<br><br><br>";
			
			$user_ban = array();
			
			while ($arr=mysql_fetch_array($res))
			{
				
				// Neuer User
				// Alle Variabeln zurücksetzen
				if($user_id!=$arr['logs_battle_user2_id'])
				{
					$user_id = $arr['logs_battle_user2_id'];
					$planet_id = 0;
					
					$first_time = $arr['logs_battle_fleet_landtime'];
					$first_planet_time = 0;
					$last_planet_time = 0;
					
					$attack_cnt_total = 0;
					$attack_cnt_planet_total = array();
					$attack_cnt_planet = 0;
					
					$bann = 0;
					$bann_reason = "";
				}
				
				// Neuer Planet
				// Einige Variabeln ändern (Setzt auch neue Variablen wenn letzter Angriff länger als 6h her ist)
				if($planet_id!=$arr['logs_battle_planet_id'] 
				|| ($last_planet_time <= $arr['logs_battle_fleet_landtime'] - $can_attack_planets_time))
				{
					$planet_id = $arr['logs_battle_planet_id'];
					
					$attack_cnt_total += 1;
					$attack_cnt_planet = 0;
					
					// Wenn der jetzige Planet schon einmal angegriffen wurde, wird der Counter um eins erhöht, und sonst wird der Planet neu gespeichert
					if($attack_cnt_planet_total[$arr['logs_battle_planet_id']]>0)
					{
						$attack_cnt_planet_total[$arr['logs_battle_planet_id']] += 1;
					}
					else
					{
						$attack_cnt_planet_total[$arr['logs_battle_planet_id']] = 1;
					}
					
					// $first_time = $arr['logs_battle_fleet_landtime'];
					// da schon bei neuem user gesetzt
					$first_planet_time = $arr['logs_battle_fleet_landtime'];		
					$last_planet_time = $arr['logs_battle_fleet_landtime'];		
				
				}
				
				
				//
				// Überprüfungen
				//
				

				$attack_cnt_planet += 1; // Erhöht Counter, wie oft der momentane Planet angegriffen wurde
				//$last_planet_time = $arr['logs_battle_fleet_landtime'];
				
				// Wenn kein Krieg herrscht
				if($arr['logs_battle_alliances_have_war']==0)
				{
					// Sperre wenn mehr als 3 Angriffe auf einen Planeten und die Angriffe keine 6h auseinander liegen
					if($attack_cnt_planet > $can_attack_on_one_planet 
						&& $arr['logs_battle_alliances_have_war']==0
						&& $last_planet_time >= $arr['logs_battle_fleet_landtime'] - $can_attack_planets_time)
					{
						$bann = 1;
						$bann_reason .= "Sperre: Mehr als ".$can_attack_on_one_planet." Angriffe auf den selben Planeten innerhalb von ".($can_attack_planets_time/3600)." Stunden!\n";
					}
					
					// Sperre wenn zwischen erstem und letztem Angriff mehr als 15min liegen aber weniger als 6h
					if($arr['logs_battle_fleet_landtime'] - $first_planet_time > $can_attack_on_one_planet_time 
						&& $arr['logs_battle_fleet_landtime'] - $first_planet_time < $can_attack_planets_time)
					{
						$bann = 1;
						$bann_reason .= "Sperre: ".(($arr['logs_battle_fleet_landtime']-$first_planet_time)/60)." Minuten liegen zwischen dem ersten und dem letzten Angriff auf den gleichen Planeten! (Erlaubt wären ".round(($can_attack_on_one_planet_time/60),2)." Minuten oder ".($can_attack_planets_time/3600)." Stunden abstand)\n";
					}
					
					// Sperre wenn gleicher Planet mehr als 2 mal angegriffen wurde
					foreach ($attack_cnt_planet_total as $id => $cnt)
					{
						if($cnt > $can_attack_on_one_planet_again)
						{
							$bann = 1;
							$bann_reason .= "Sperre: einen Planeten mehr als ".$can_attack_on_one_planet_again." mal angegriffen. Anzahl Angriffe: ".$cnt."\n";
						}
					}
					
					// Sperre wenn mehr als 5 Planeten angegriffen (innerhalb der 24h die angezeigt werden)
					if(count($attack_cnt_planet_total) > $can_attack_planets)
					{
						$bann = 1;
						$bann_reason .= "Sperre: mehr als ".$can_attack_planets." Planeten angegriffen innerhalb von ".($can_attack_total_time/3600)." stunden\n";
					}
				}
				
				// Wenn Krieg herrscht
				else
				{
					// Sperre wenn mehr als 4 Angriffe und die Angriffe keine 6h auseinander liegen
					if($attack_cnt_planet > $can_attack_on_one_planet_war
						&& $arr['logs_battle_alliances_have_war']==1
						&& $last_planet_time >= $arr['logs_battle_fleet_landtime'] - $can_attack_planets_time)
					{
						$bann = 1;
						$bann_reason .= "Sperre: Mehr als ".$can_attack_on_one_planet_war." Angriffe auf den selben Planeten innerhalb von ".($can_attack_planets_time/3600)." Stunden!\n";
					}
					
					// Sperre wenn zwischen erstem und letztem Angriff mehr als 15min liegen aber weniger als 6h
					if($arr['logs_battle_fleet_landtime'] - $first_planet_time > $can_attack_on_one_planet_time 
						&& $arr['logs_battle_fleet_landtime'] - $first_planet_time < $can_attack_planets_time)
					{
						$bann = 1;
						$bann_reason .= "Sperre: ".(($arr['logs_battle_fleet_landtime']-$first_planet_time)/60)." Minuten liegen zwischen dem ersten und dem letzten Angriff auf den gleichen Planeten! (Erlaubt wären ".round(($can_attack_on_one_planet_time/60),2)." Minuten oder ".($can_attack_planets_time/3600)." Stunden abstand)\n";
					}
					
					// Sperre wenn gleicher Planet mehr als 4 mal angegriffen wurde
					foreach ($attack_cnt_planet_total as $id => $cnt)
					{
						if($cnt > $can_attack_on_one_planet_again_war)
						{
							$bann = 1;
							$bann_reason .= "Sperre: einen Planeten mehr als ".$can_attack_on_one_planet_again." mal angegriffen. Anzahl Angriffe: ".$cnt."\n";
						}
					}
					
					// Sperre wenn mehr als 10 Planeten angegriffen (innerhalb der 24h die angezeigt werden)
					if(count($attack_cnt_planet_total) > $can_attack_planets_war)
					{
						$bann = 1;
						$bann_reason .= "Sperre: mehr als ".$can_attack_planets." Planeten angegriffen. Anzahl angegriffener Planeten: ".count($attack_cnt_planet_total)."\n";
					}					
				}
				
				// Setzt den eben abgearbeiteten Planeten, als letztes Angriffsziel für den nächsten Durchgang
				$last_planet_time = $arr['logs_battle_fleet_landtime'];
				
				
				echo "<b>".get_user_nick($arr['logs_battle_user1_id'])." VS. ".get_user_nick($arr['logs_battle_user2_id']).": Planet: ".$arr['logs_battle_planet_id']." / Zeit: ".date("Y-m-d H:i:s",$arr['logs_battle_fleet_landtime'])."<br></b>";
				echo "attack: ".$attack_cnt_planet.", krieg: ".$arr['logs_battle_alliances_have_war'].", last time: ".$last_planet_time."<br><br>";
				
				// Es liegt eine Angriffsverletzung vor
				if($bann==1)
				{
					// Verstoss wird in Array geschrieben und nach der Schleife wird der entsprechende User gesperrt
					$user_ban[$arr['logs_battle_user1_id']] = $bann_reason;
					
					echo "".$bann_reason."<br><br>";
					$bann = 0;
					$bann_reason = "";
				}
			
			}
			
			echo "<br><br>sperren:<br><br>";
			// User werden gesperrt
			foreach($user_ban as $id => $reason)
			{				
				echo "user: ".$id."<br>grund:<br> ".$reason."<br><br>";
				
				// Läd die Anzahl bisheriger Sperren wegen Angriffsverletzung und die Sperrzeit wenn vorhanden
				$res = dbquery("
				SELECT 
					user_blocked_from,
					user_blocked_to,
					user_attack_bans,
					user_ban_reason
				FROM 
					users
				WHERE
					user_id='".$id."';");
				if(mysql_num_rows($res)>0)
				{
					$arr = mysql_fetch_array($res);
					
					$reason .= "\nDies ist der ".($arr['user_attack_bans']+1).". Verstoss";
					
					// Rechnet die Sperrzeit
					$ban_time = $first_ban_time + $arr['user_attack_bans'] * $add_ban_time;
					$ban_from = time();
					//$ban_to = time() + $ban_time;
					$ban_to = time() + 120;
					
					
					// Speichert die Sperre
					dbquery("
					UPDATE
						users
					SET
						user_blocked_from='".$ban_from."',
						user_blocked_to='".$ban_to."',
						user_attack_bans=user_attack_bans+1,
						user_ban_reason='".addslashes($reason)."'							
					WHERE
						user_id=".$id.";");
						
					
					// Logt die Sperre
					add_user_history($id,"[b]Accountsperrung[/b] von [b]".date("d.m.Y H:i",$ban_from)."[/b] bis [b]".date("d.m.Y H:i",$ban_to)."[/b]\n[b]Grund:[/b] ".addslashes($reason)."\n[b]Verantwortlich: [/b]System");
					
					
					// Markiert die Kampfberichte, dass keine Doppelsperren für das gleiche Vergehen vehängt werden
					dbquery("
					UPDATE
						logs_battle
					SET
						logs_battle_bann_mark='1'							
					WHERE
						logs_battle_user1_id=".$id.";");
						
					// Schickt eine IGM an den "gesperrten". nur zum test
					$msg = "Na du :)\n\nLaut System warst du ein ganz ungezogenes Kind in naher Vergangenheit!\nFolgende Sache wird dir zu Lasten gelegt:\n\n[i]".addslashes($reason)."[/i]\n\nRein theoretisch würdest du jetzt für [b]".($ban_time/3600)." Stunden[/b] gesperrt werden, aber da deine liebe Fee ein gutes Wort für dich eingelegt hat, kommst du noch ohne grosse Folgen davon!\n\nDas System wünscht noch eine angenehme Zeit. Bis zur nächsten Unartigkeit ;)";
					
					send_msg($id,5,"Angriffsverletzung",$msg);
				}
			}	
		}
	}	

	//
	// New simple AJAX based general log viewer
	//		
	else
	{
		echo "<h2>Allgemeines Log</h2>";
		iBoxStart("Filter",600);
		echo "<form><label for=\"logcat\">Kategorie:</label>
		<select id=\"logcat\" onchange=\"xajax_applyLogFilter(document.getElementById('logcat').value,document.getElementById('searchtext').value);\">
		<option value=\"0\">(Alle)</option>";
		$lres=dbquery("SELECT cat_id,cat_name,COUNT(*) as cnt FROM log_cat INNER JOIN logs ON log_cat=cat_id GROUP BY cat_id;;");
		while ($larr=mysql_fetch_assoc($lres))
		{
			echo "<option value=\"".$larr['cat_id']."\">".$larr['cat_name']." (".$larr['cnt'].")</option>";
		}
		echo "</select> &nbsp; ";
		echo " <label for=\"searchtext\">Suchtext:</label> <input type=\"text\" id=\"searchtext\" value=\"\" /> &nbsp;
		<input type=\"submit\" value=\"Anwenden\" onclick=\"xajax_applyLogFilter(document.getElementById('logcat').value,document.getElementById('searchtext').value);document.getElementById('searchtext').select();return false;\" /> &nbsp;
		<input type=\"button\" value=\"Reset\" onclick=\"xajax_applyLogFilter(0,'');document.getElementById('logcat').value=0;document.getElementById('searchtext').value='';document.getElementById('searchtext').focus();\" />
	</form>";
		iBoxEnd();

		echo "<div id=\"log_contents\">";
		showLogs();
		echo "</div>";

		$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM logs;"));
		echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.<br><br>";
		
	}
			
	

?>

