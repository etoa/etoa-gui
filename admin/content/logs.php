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

	$lres=dbquery("SELECT cat_id,cat_name,COUNT(*) as cnt FROM ".$db_table['log_cat'].",".$db_table['logs']." WHERE log_cat=cat_id GROUP BY cat_id;;");
	$log_type=array();
	while ($larr=mysql_fetch_array($lres))
	{
		$log_type[$larr['cat_id']]['name']=$larr['cat_name'];
		$log_type[$larr['cat_id']]['cnt']=$larr['cnt'];
	}

	echo "<h1>Logs</h1>";
	echo "<div id=\"logsinfo\"></div>"; //nur zu entwicklungszwecken!
	
	
	if ($sub=="dellogs")
	{
		if ($_POST['delentrys']!="")
		{
			$tstamp = time()-$_POST['log_timestamp'];
			dbquery("DELETE FROM ".$db_table['logs']." WHERE log_cat=".$_POST['log_cat']." AND log_timestamp<$tstamp;");
			echo mysql_affected_rows()." Eintr&auml;ge wurden gel&ouml;scht!<br/><br/><input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page&sub=$sub'\" />";
		}
		else
		{
			echo "Logs l&ouml;schen:<br/><br/>";
			echo "<form action=\"?page=$page&sub=$sub\" method=\"post\">";
			echo "Eintr&auml;ge in <select name=\"log_cat\">";
			echo "<option value=\"0\">(nicht zugeordnet)</option>";
			foreach ($log_type as $id=>$val)
			{
				echo "<option value=\"$id\">".$val['name']." (".$val['cnt'].")</option>";
			}
			echo "</select> l&ouml;schen die &auml;lter als <select name=\"log_timestamp\">";
			echo "<option value=\"432000\">5 Tage</option>";
			echo "<option value=\"604800\" selected=\"selected\">1 Woche</option>";
			echo "<option value=\"1209600\">2 Wochen</option>";
			echo "<option value=\"2419200\">4 Wochen</option>";
			echo "</select> sind.<br/>";
			echo "<br/><input type=\"submit\" name=\"delentrys\" value=\"Ausf&uuml;hren\" /></form>";
			$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM ".$db_table['logs'].";"));
			echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.";
		}
	}
	else
	{
		if ($_POST['alliance_search']!="" || $_GET['action']=="searchresults")
		{
			if ($_SESSION['logs']['query']=="")
			{
				$sqlstart = "SELECT * FROM ".$db_table['logs'].",".$db_table['log_cat']." WHERE log_cat=cat_id ";
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
		elseif ($_GET['sub']=="view")
		{
			$res = dbquery("SELECT * FROM ".$db_table['logs'].",".$db_table['log_cat']." WHERE log_cat=cat_id AND log_id=".$_GET['log_id'].";");
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
		elseif ($_GET['sub']=="logs_battle")
		{
			echo "Battle Log im aufbau!<br>";
		}
		elseif ($_GET['sub']=="logs_game")
		{
			$lres=dbquery("
			SELECT 
				logs_game_cat_id,
				logs_game_cat_name,
				COUNT(logs_game_id) as cnt 
			FROM 
				".$db_table['logs_game_cat']."
				INNER JOIN
				".$db_table['logs_game']." 
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
		
		elseif ($_POST['logs_submit']!="" && checker_verify())
		{
			$sql_query = stripslashes($_POST['sql_query']);
			
			
			if ($_POST['log_cat']=="logs")
			{
				echo "allgemeine logs anzeigen...";
			}
			elseif ($_POST['log_cat']=="logs_battle")
			{			
				echo "Legende:<br/>
				<span style=\"color:#0f0;font-weight:bold;\">Grüner Nick</span> = Flotte hat überlebt<br/>
				<span style=\"color:red;font-weight:bold;\">Roter Nick</span> = Flotte wurde zerstört<br><br>";
				
				$res = dbquery($sql_query);

				infobox_start("".mysql_num_rows($res)." Ergebnisse",1);
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
									<td class=\"tbldata\">".fa($arr['logs_battle_fleet_action'])."</td>
									<td class=\"tbldata\" onclick=\"xajax_showBattle('".$battle."',".$arr['logs_battle_id'].");\" ".tm("","Klicken für Anzeige des Berichtes!").">
										<a href=\"javascript:;\">Anzeigen</a>
									</td>
								</tr>
								<tr>
									<td class=\"tbldata\" id=\"show_battle_".$arr['logs_battle_id']."\" style=\"vertical-align:middle;\" colspan=\"5\" ondblclick=\"xajax_showBattle('',".$arr['logs_battle_id'].");\" ".tm("","Doppelklick zum deaktivieren des Fensters!").">
									</td>
								</tr>
								";
				}
				
				
				infobox_end(1);
			}
			elseif ($_POST['log_cat']=="logs_game")
			{
				echo "<form action=\"?page=".$page."\" method=\"post\">";
				
				$res = dbquery($sql_query);

				infobox_start("".mysql_num_rows($res)." Ergebnisse",1);
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
							".$db_table['buildings']."
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
							".$db_table['technologies']."
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
					
					$log_text = text2html($arr['logs_game_text']);
					
					echo "<tr>
									<td class=\"tbldata\">
										<b>".date("Y-m-d H:i:s",$arr['logs_game_timestamp'])."</b><br>".date("Y-m-d H:i:s",$arr['logs_game_realtime'])."
									</td>
									<td class=\"tbldata\">".$arr['logs_game_cat_name']."</td>
									<td class=\"tbldata\">".get_user_nick($arr['logs_game_user_id'])."</td>
									<td class=\"tbldata\">".$object."</td>
									<td class=\"tbldata\" onclick=\"xajax_showGameLogs('".$log_text."',".$arr['logs_game_id'].");\" ".tm("","Klicken für Anzeige des Berichtes!").">
										<a href=\"javascript:;\">Anzeigen</a>
									</td>
								</tr>
								<tr>
									<td class=\"tbldata\" id=\"show_game_logs_".$arr['logs_game_id']."\" style=\"vertical-align:middle;\" colspan=\"5\" ondblclick=\"xajax_showGameLogs('',".$arr['logs_game_id'].");\" ".tm("","Doppelklick zum deaktivieren des Fensters!").">
									</td>
								</tr>";
				}
				
				infobox_end(1);
			}
			
		}
		
		//Neue Log Seite
		elseif ($_GET['sub']=="new_logs_page")
		{
			echo "<h1>Neue Log Seite!</h1><br>";
			
			echo "Suchmaske:<br/><br/>";
			echo "<form action=\"?page=$page\" method=\"post\" id=\"log_selector\">";
			$cstr = checker_init();
			
			infobox_start("",1);
			
			//Kategorie wählen
			echo "<tr>
							<td class=\"tbltitle\">Kategorie</td><td class=\"tbldata\">
								<select name=\"log_cat\" onChange=\"xajax_logSelectorCat(xajax.getFormValues('log_selector'),1);\">
									<option value=\"0\">(nicht zugeordnet)</option>
									<option value=\"logs\">Allgemeine</option>
									<option value=\"logs_battle\">Kampfberichte</option>
									<option value=\"logs_game\">Game</option>
								</select>
							</td>
						</tr>";							
			infobox_end(1);	
			
			// Suchformular
			echo "<div id=\"catSelector\"></div>";			
			echo "</form>";		
		}		
		
		//Angriffsverletzung
		elseif ($_GET['sub']=="check_fights")
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
			
			
			
			echo "Angriffsverletzung (Test tool)<br><br><br>";
			
			$res=dbquery("
			SELECT 
				logs_battle_id,
				logs_battle_user1_id,
				logs_battle_user2_id,
				logs_battle_alliances_have_war,
				logs_battle_planet_id,
				logs_battle_fleet_landtime
			FROM 
				".$db_table['logs_battle']."
			WHERE
				logs_battle_user1_weapon>0
				AND logs_battle_fleet_landtime>".(time()-$can_attack_total_time)."
			ORDER BY
				logs_battle_user1_id ASC,
				logs_battle_planet_id ASC,
				logs_battle_fleet_landtime ASC
			LIMIT
				100;");
				
			$user_id = 0;
			$planet_id = 0;
			
			$first_time = 0;
			$first_planet_time = 0;
			$last_planet_time = 0;
			
			$attack_cnt_total = 0;					// Total Angriffe (3er Welle = 1 Angriff)
			$attack_cnt_planet_total = array();		// Den gleichen Planeten
			$attack_cnt_planet = 0;
			
			if (mysql_num_rows($res)>0)
			{			
				echo "".mysql_num_rows($res)." Datensätze!<br><br><br>";
				while ($arr=mysql_fetch_array($res))
				{
					
					// Neuer User. Alle Variabeln zurücksetzen
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
					
					// Neuer Planet. Einige Variabeln ändern (Setzt auch neue Variablen wenn letzter Angriff länger als 6h her ist)
					if($planet_id!=$arr['logs_battle_planet_id'] 
					|| ($last_planet_time < $arr['logs_battle_fleet_landtime'] - $can_attack_planets_time))
					{
						$planet_id = $arr['logs_battle_planet_id'];
						
						$attack_cnt_total += 1;
						$attack_cnt_planet = 0;
						
						if($attack_cnt_planet_total[$arr['logs_battle_planet_id']]>0)
						{
							$attack_cnt_planet_total[$arr['logs_battle_planet_id']] += 1;
						}
						else
						{
							$attack_cnt_planet_total[$arr['logs_battle_planet_id']] = 1;
						}
						
						$first_time = $arr['logs_battle_fleet_landtime'];
						$first_planet_time = $arr['logs_battle_fleet_landtime'];		
						$last_planet_time = $arr['logs_battle_fleet_landtime'];		
					}
					
					
					//
					// Überprüfungen
					//
					
					// Wellen check

					$attack_cnt_planet += 1;
					$last_planet_time = $arr['logs_battle_fleet_landtime'];
					
					// Wenn kein Krieg herrscht
					if($arr['logs_battle_alliances_have_war']==0)
					{
						// Sperre wenn mehr als 3 Angriffe und die Angriffe keine 6h auseinander liegen
						if($attack_cnt_planet > $can_attack_on_one_planet && $arr['logs_battle_alliances_have_war']==0
							AND $last_planet_time > $arr['logs_battle_fleet_landtime'] - $can_attack_planets_time)
						{
							$bann = 1;
							$bann_reason .= "<b>Sperre:</b> ".get_user_nick($arr['logs_battle_user1_id']).". Grund: Mehr als ".$can_attack_on_one_planet." Angriffe auf den selben Planeten innerhalb von ".($can_attack_planets_time/3600)." Stunden!<br>";
						}
						
						// Sperre wenn zwischen erstem und letztem angriff mehr als 15min liegen aber weniger als 6h
						if($last_planet_time - $first_planet_time > $can_attack_on_one_planet_time 
							&& $last_planet_time - $first_planet_time < $can_attack_planets_time)
						{
							$bann = 1;
							$bann_reason .= "<b>Sperre:</b> ".get_user_nick($arr['logs_battle_user1_id']).". Grund: ".(($last_planet_time- $first_planet_time)/60)." Minuten liegen zwischen dem ersten und dem letzten Angriff auf den gleichen Planeten! (Erlaubt wären ".($can_attack_on_one_planet_time/60)." Minuten)<br>";
						}
						
						// Sperre wenn gleicher Planet mehr als 2 mal angegriffen wurde
						foreach ($attack_cnt_planet_total as $id => $cnt)
						{
							if($cnt > $can_attack_on_one_planet_again)
							{
								$bann = 1;
								$bann_reason .= "<b>Sperre:</b> ".get_user_nick($arr['logs_battle_user1_id']).". Grund: einen Planeten mehr als ".$can_attack_on_one_planet_again." mal angegriffen<br>";
							}
						}
						
						// Sperre wenn mehr als 5 Planeten angegriffen (innerhalb der 24h die angezeigt werden)
						if(count($attack_cnt_planet_total) > $can_attack_planets)
						{
							$bann = 1;
							$bann_reason .= "<b>Sperre:</b> ".get_user_nick($arr['logs_battle_user1_id']).". Grund: mehr als ".$can_attack_planets." Planeten angegriffen<br>";
						}
					}
					else
					{
						// Sperre wenn mehr als 4 Angriffe und die Angriffe keine 6h auseinander liegen
						if($attack_cnt_planet > $can_attack_on_one_planet_war && $arr['logs_battle_alliances_have_war']==1
							AND $last_planet_time > $arr['logs_battle_fleet_landtime'] - $can_attack_planets_time)
						{
							$bann = 1;
							$bann_reason .= "<b>Sperre:</b> ".get_user_nick($arr['logs_battle_user1_id']).". Grund: Mehr als ".$can_attack_on_one_planet_war." Angriffe auf den selben Planeten innerhalb von ".($can_attack_planets_time/3600)." Stunden!<br>";
						}
						
						// Sperre wenn zwischen erstem und letztem angriff mehr als 15min liegen aber weniger als 6h
						if($last_planet_time - $first_planet_time > $can_attack_on_one_planet_time 
							&& $last_planet_time - $first_planet_time < $can_attack_planets_time)
						{
							$bann = 1;
							$bann_reason .= "<b>Sperre:</b> ".get_user_nick($arr['logs_battle_user1_id']).". Grund: ".(($last_planet_time- $first_planet_time)/60)." Minuten liegen zwischen dem ersten und dem letzten Angriff auf den gleichen Planeten! (Erlaubt wären ".($can_attack_on_one_planet_time/60)." Minuten)<br>";
						}
						
						// Sperre wenn gleicher Planet mehr als 2 mal angegriffen wurde
						foreach ($attack_cnt_planet_total as $id => $cnt)
						{
							if($cnt > $can_attack_on_one_planet_again_war)
							{
								$bann = 1;
								$bann_reason .= "<b>Sperre:</b> ".get_user_nick($arr['logs_battle_user1_id']).". Grund: einen Planeten mehr als ".$can_attack_on_one_planet_again." mal angegriffen<br>";
							}
						}
						
						// Sperre wenn mehr als 5 Planeten angegriffen (innerhalb der 24h die angezeigt werden)
						if(count($attack_cnt_planet_total) > $can_attack_planets)
						{
							$bann = 1;
							$bann_reason .= "<b>Sperre:</b> ".get_user_nick($arr['logs_battle_user1_id']).". Grund: mehr als ".$can_attack_planets." Planeten angegriffen<br>";
						}					
					}
					
					echo "<b>".get_user_nick($arr['logs_battle_user1_id'])." VS. ".get_user_nick($arr['logs_battle_user2_id']).": Planet: ".$arr['logs_battle_planet_id']." / Zeit: ".date("Y-m-d H:i:s",$arr['logs_battle_fleet_landtime'])."<br></b>";
					echo "attack: ".$attack_cnt_planet.", last time: ".$last_planet_time."<br><br>";
					
					if($bann==1)
					{
						echo "".$bann_reason."<br><br>";
						$bann = 0;
						$bann_reason = "";
					}
					
					
					
				}	
			}
		}	
			
		else
		{
			$_SESSION['logs']['query']=Null;
			echo "Suchmaske:<br/><br/>";
			echo "<form action=\"?page=$page\" method=\"post\">";
			echo "<table class=\"tbl\">";
			echo "<tr><td class=\"tbltitle\">Kategorie</td><td class=\"tbldata\"><select name=\"log_cat\">";
			echo "<option value=\"0\">(nicht zugeordnet)</option>";
			foreach ($log_type as $id=>$val)
			{
				echo "<option value=\"$id\">".$val['name']." (".$val['cnt'].")</option>";
			}
			echo "</select></td>";
			echo "<tr><td class=\"tbltitle\">String</td><td class=\"tbldata\"><input type=\"text\" name=\"log_text\" value=\"\" size=\"40\" maxlength=\"250\" /></td>";
			echo "<input type=\"hidden\" name=\"qmode[log_text]\" value=\"LIKE '%\"></tr>";
			echo "<tr><td class=\"tbltitle\">String 2</td><td class=\"tbldata\"><input type=\"text\" name=\"log_text2\" value=\"\" size=\"40\" maxlength=\"250\" /></td>";
			echo "<input type=\"hidden\" name=\"qmode[log_text2]\" value=\"LIKE '%\"></tr>";			
			echo "<tr><td class=\"tbltitle\">Hostname</td><td class=\"tbldata\"><input type=\"text\" name=\"log_hostname\" value=\"\" size=\"40\" maxlength=\"250\" /></td>";
			echo "<input type=\"hidden\" name=\"qmode[log_hostname]\" value=\"LIKE '%\"></tr>";			
			echo "<tr><td class=\"tbltitle\">IP</td><td class=\"tbldata\"><input type=\"text\" name=\"log_ip\" value=\"\" size=\"40\" maxlength=\"250\" /></td>";
			echo "<input type=\"hidden\" name=\"qmode[log_ip]\" value=\"LIKE '%\"></tr>";			
			echo "<tr><th class=\"tbltitle\">Anzahl Datens&auml;tze</th><td class=\"tbldata\"><select name=\"limit\">";
			for ($x=100;$x<=2000;$x+=100)
				echo "<option value=\"$x\">$x</option>";
			echo "</select></td></tr></table>";
			echo "<br/><input type=\"submit\" name=\"alliance_search\" value=\"Suche starten\" /></form>";

			$tblcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM ".$db_table['logs'].";"));
			echo "<br/>Es sind ".nf($tblcnt[0])." Eintr&auml;ge in der Datenbank vorhanden.<br><br>";
			
		}
	}
?>

