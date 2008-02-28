<?PHP

	/**
	* Provides static functions for 
	* calculating and displaying
	* player ranking
	*/
	
	class Ranking
	{
		/**
		* Shows player tytles
		*/
		static function getTitles($admin=0)
		{
			global $conf;
			ob_start();
			
			$img_dir = ($admin==1) ? "../images" : "images";
			
			$titles=array();
			$titles['total']="user_points";
			$titles['fleet']="user_points_ships";
			$titles['tech']="user_points_tech";
			$titles['buildings']="user_points_buildings";
			
			infobox_start("Allgemeine Titel",1);
			$cnt = 0;
			foreach ($titles as $k => $v)
			{
				$res = dbquery("
				SELECT 
					user_nick,
					".$v.",
					user_id
				FROM 
					user_stats
				WHERE 
					user_points>".USERTITLES_MIN_POINTS." 
				ORDER BY 
					".$v." DESC 
				LIMIT 1;");
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_row($res);
					$profile = ($admin==1) ? "?page=user&amp;sub=edit&amp;user_id=".$arr[2]."" : "?page=userinfo&amp;id=".$arr[2];
					echo "<tr>
						<th class=\"tbltitle\" style=\"width:100px;height:100px;\">
							<img src='".$img_dir."/medals/medal_".$k.".png' style=\"height:100px;\" />
						</th>
						<td class=\"tbldata\" style=\"font-size:16pt;vertical-align:middle;padding:2px 10px 2px 10px;width:360px;\">
							".$conf['userrank_'.$k]['v']."
						</td>
						<td class=\"tbldata\" style=\"vertical-align:middle;padding-top:0px;padding-left:15px;\">
							<span style=\"font-size:13pt;color:#ff0;\">".$arr[0]."</span><br/><br/>
							".nf($arr[1])." Punkte<br/><br/>
							[<a href=\"".$profile."\">Profil</a>]
						</td>
					</tr>";
				}
				$cnt++;
			}
			if ($cnt==0)
			{
				echo "<tr><td class=\"tbldata\">Keine Titel vorhanden (kein Spieler hat die minimale Punktzahl zum Erwerb eines Titels erreicht)!</td></tr>";
			}
			infobox_end(1);
			infobox_start("Rassenleader",1);
			$rres = dbquery("
			SELECT
				race_id,
				race_leadertitle,
				race_name
			FROM
				races
			ORDER BY
				race_name;
			");
			while ($rarr = mysql_fetch_array($rres))
			{
				$res = dbquery("
				SELECT
					user_nick,
					user_points,
					user_id				
				FROM
					users
				WHERE
					user_race_id=".$rarr['race_id']."
					AND user_show_stats=1
				ORDER BY
					user_points DESC
				LIMIT 1;
				");
				if (mysql_num_rows($res)>0)
				{
					$arr = mysql_fetch_row($res);
					$cres = dbquery("SELECT COUNT(user_race_id) FROM users WHERE user_race_id=".$rarr['race_id']."");
					$carr = mysql_fetch_row($cres);
					$profile = ($admin==1) ? "?page=user&amp;sub=edit&amp;user_id=".$arr[2]."" : "?page=userinfo&amp;id=".$arr[2];
					
					echo "<tr>
						<th class=\"tbltitle\" style=\"width:70px;height:70px;\">
							<img src='".$img_dir."/medals/medal_race.png' style=\"height:70px;\" />
						</th>
						<td class=\"tbldata\" style=\"vertical-align:middle;padding:2px 10px 2px 10px;width:360px;\">
							<div style=\"font-size:16pt;\">".$rarr['race_leadertitle']."</div>
							".$carr[0]." V&ouml;lker
						</td>	
						<td class=\"tbldata\" style=\"vertical-align:middle;padding-top:0px;padding-left:15px;\">
							<span style=\"font-size:13pt;color:#ff0;\">".$arr[0]."</span><br/><br/>
							".nf($arr[1])." Punkte &nbsp;&nbsp;&nbsp;
							[<a href=\"".$profile."\">Profil</a>]
						</td>							
					</tr>";
				}
			}
			infobox_end(1);
		
			infobox_start("Allianzgr&uuml;nder",1);
			$res = dbquery("
			SELECT
				alliance_id,
				alliance_tag,
				alliance_name,
				user_nick,
				user_id
			FROM
				alliances
			INNER JOIN
				users 
				ON user_id=alliance_founder_id
			ORDER BY
				alliance_tag;
			");
			while ($arr = mysql_fetch_array($res))
			{				
				$cres = dbquery("SELECT COUNT(user_alliance_id) FROM users WHERE user_alliance_id=".$arr['alliance_id']."");
				$carr = mysql_fetch_row($cres);					
				
				$aprofile = ($admin==1) ? "?page=alliances&amp;sub=edit&amp;alliance_id=".$arr['alliance_id'] : "?page=alliance&amp;id=".$arr['alliance_id'];
				$profile = ($admin==1) ? "?page=user&amp;sub=edit&amp;user_id=".$arr['user_id'] : "?page=userinfo&amp;id=".$arr['user_id'];

				echo "<tr>
					<td class=\"tbldata\" style=\"vertical-align:middle;padding:2px 10px 2px 10px;width:360px;\">
						<div style=\"font-size:13pt;padding-bottom:4px;\">[".$arr['alliance_tag']."] ".$arr['alliance_name']."</div><nr/>
						".$carr[0]." Mitglieder [<a href=\"".$aprofile."\">Info</a>]
					</td>	
					<td class=\"tbldata\" style=\"vertical-align:middle;padding-top:0px;padding-left:15px;\">
						<span style=\"font-size:13pt;color:#ff0;\">".$arr['user_nick']."</span>
						[<a href=\"".$profile."\">Profil</a>]
					</td>							
				</tr>";
			}
			infobox_end(1);			
			$rtn = ob_get_contents();
			ob_end_clean();
			return $rtn;		
		}
		
		
		/**
		* Writes generated titles to cache files
		*/
		static function calcTitles()
		{
			$file_u = CACHE_ROOT."/out/usertitles.gen";
			$file_a = CACHE_ROOT."/out/usertitles_a.gen";
			$titles_u = Ranking::getTitles();
			$titles_a = Ranking::getTitles(1);
			if ($d = fopen ($file_u,"w+"))
			{
				fwrite($d,$titles_u);
				fclose($d);
			}
			if ($d = fopen ($file_a,"w+"))
			{
				fwrite($d,$titles_a);
				fclose($d);
			}
			
		}
		
		/*
		/**
		* Punkteberechnung
		*/
	
		static function calc($manual=false)
		{
			global $db_table, $conf;
	
			$allpoints=0;
			$res_amount_per_point = $conf['points_update']['p1'];
	
			// Schiffe laden
			$res = dbquery("
				SELECT
					ship_battlepoints,
					ship_id
				FROM
					".$db_table['ships'].";
			");
	    	$ship=array();
			while ($arr = mysql_fetch_array($res))
			{
				$ship[$arr['ship_id']]=$arr['ship_battlepoints'];
			}
	
			// Verteidigung laden
			$res = dbquery("
				SELECT
					def_battlepoints,
					def_id
				FROM
					".$db_table['defense'].";
			");
	    	$def=array();
			while ($arr = mysql_fetch_array($res))
			{
				$def[$arr['def_id']]=$arr['def_battlepoints'];
			}
	
			// Gebäude laden
			$res = dbquery("
				SELECT
					bp_level,
					bp_building_id,
					bp_points
				FROM
					".$db_table['building_points'].";
			");
	    	$building=array();
			while ($arr = mysql_fetch_array($res))
			{
				$building[$arr['bp_building_id']][$arr['bp_level']]=$arr['bp_points'];
			}
	
			// Technologien laden
			$res = dbquery("
				SELECT
					bp_level,
					bp_tech_id,
					bp_points
				FROM
					".$db_table['tech_points'].";
			");
	    	$tech=array();
			while ($arr = mysql_fetch_array($res))
			{
				$tech[$arr['bp_tech_id']][$arr['bp_level']]=$arr['bp_points'];
			}
	
			$ures =	dbquery("
				SELECT
					user_id
				FROM
					".$db_table['users'].";
			");
			while ($uarr=mysql_fetch_array($ures))
			{
				$user_id = $uarr['user_id'];
				$points = 0;
				$points_ships = 0;
				$points_tech = 0;
				$points_building = 0;
	
				//
				// Punkte für Schiffe (aus Planeten)
				//
				$res = dbquery("
					SELECT
						shiplist_ship_id,
						shiplist_count
					FROM
						".$db_table['shiplist']."
					WHERE
						shiplist_user_id='".$user_id."';
				");
				while ($arr = mysql_fetch_array($res))
				{
					$p = round($arr['shiplist_count']*$ship[$arr['shiplist_ship_id']]);
					$points+=$p;
					$points_ships+=$p;
				}
	
				//
				// Punkte für Schiffe (in Flotten)
				//
				$res = dbquery("
					SELECT
						fs.fs_ship_id,
						fs.fs_ship_cnt
					FROM
						".$db_table['fleet']." AS f
						INNER JOIN ".$db_table['fleet_ships']." AS fs
						ON f.fleet_id = fs.fs_fleet_id
						AND f.fleet_user_id='".$user_id."'
						AND fs.fs_ship_faked='0';
				");
				while ($arr = mysql_fetch_array($res))
				{
					$p = round($arr['fs_ship_cnt']*$ship[$arr['fs_ship_id']]);
					$points+=$p;
					$points_ships+=$p;
				}
	
				//
				// Punkte für Verteidigung
				//
				$res = dbquery("
					SELECT
						deflist_count,
						deflist_def_id
					FROM
						".$db_table['deflist']."
					WHERE
						deflist_user_id='".$user_id."';
				");
				while ($arr = mysql_fetch_array($res))
				{
					$p = round($arr['deflist_count']*$def[$arr['deflist_def_id']]);
					$points+=$p;
					$points_building+=$p;
				}
	
				//
				// Punkte für Gebäude
				//
				$res = dbquery("
					SELECT
						buildlist_current_level,
						buildlist_building_id
					FROM
						".$db_table['buildlist']."
					WHERE
						buildlist_user_id='".$user_id."';
				");
				while ($arr = mysql_fetch_array($res))
				{
					$p = round($building[$arr['buildlist_building_id']][$arr['buildlist_current_level']]);
					$points+=$p;
					$points_building+=$p;
				}
	
				//
				// Punkte für Forschung
				//
				$res = dbquery("
					SELECT
						techlist_current_level,
						techlist_tech_id
					FROM
						".$db_table['techlist']."
					WHERE
						techlist_user_id='".$user_id."';
				");
				while ($arr = mysql_fetch_array($res))
				{
					$p = round($tech[$arr['techlist_tech_id']][$arr['techlist_current_level']]);
					$points+=$p;
					$points_tech+=$p;
				}
	
				// Punkte speichern
				dbquery("
					UPDATE
						".$db_table['users']."
					SET
						user_points='".$points."',
						user_points_ships='".$points_ships."',
						user_points_tech='".$points_tech."',
						user_points_buildings='".$points_building."'
					WHERE
						user_id='".$user_id."';
				");
	
				dbquery("
					INSERT INTO
					".$db_table['user_points']."
					(
						point_user_id,
						point_timestamp,
						point_points,
						point_ship_points,
						point_tech_points,
						point_building_points
					)
					VALUES
					(
						'".$user_id."',
						'".time()."',
						'".$points."',
						'".$points_ships."',
						'".$points_tech."',
						'".$points_building."'
					);
				");
				$allpoints+=$points;
			}
	
			//Array Löschen (Speicher freigeben)
			unset($ship);
			unset($def);
			unset($building);
			unset($tech);
			unset($p);
			unset($points);
			unset($points_ships);
			unset($points_tech);
			unset($points_building);
	
			// Ränge berechnen
			dbquery("
				UPDATE
					".$db_table['users']."
				SET
					user_rank_last=user_rank_current;
			");
	
			// Statistiktabelle löschen
			dbquery("
				TRUNCATE TABLE
					user_stats;
			");
	
			// Heimatplaneten laden
			$mp=array();
			$res=dbquery("
			SELECT
				cell_sx,
				cell_sy,
				planet_user_id
			FROM
				space_cells
			INNER JOIN
				planets
				ON planet_solsys_id=cell_id
				AND planet_user_main=1	
			");		
			if (mysql_num_rows($res)>0)
			{
				while($arr=mysql_fetch_array($res))
				{
					$mp[$arr['planet_user_id']]=array();
					$mp[$arr['planet_user_id']]['sx']=$arr['cell_sx'];
					$mp[$arr['planet_user_id']]['sy']=$arr['cell_sy'];
				}
			}
			mysql_free_result($res);
	
	
			$res=dbquery("
				SELECT
					user_id,
					user_rank_current,
					user_highest_rank,
					
					user_nick,
					user_points,
					user_points_ships,
					user_points_tech,
					user_points_buildings,
					user_rank_last,
					alliance_tag,
					alliance_id,
					race_name,
					user_last_online,
					user_blocked_from,
					user_blocked_to,
					user_hmode_from,
					user_hmode_to				
				FROM
					".$db_table['users']."
				LEFT JOIN
					alliances
					ON user_alliance_id=alliance_id
					AND user_alliance_application=''
				INNER JOIN
					races
					ON user_race_id=race_id			
				WHERE
					user_show_stats='1'
				ORDER BY
					user_points DESC,
					user_registered DESC,
					user_nick ASC;
			");
			if (mysql_num_rows($res)>0)
			{
				$rank=1;
				while ($arr=mysql_fetch_array($res))
				{
					if ($arr['user_highest_rank']>0)
						$hr = min($arr['user_highest_rank'],$rank);
					else
						$hr = $arr['user_rank_current'];
					dbquery("
						UPDATE
							".$db_table['users']."
						SET
							user_rank_current='".$rank."',
							user_highest_rank='".$hr."'
						WHERE
							user_id='".$arr['user_id']."';
					");
					
					$blocked=0;
					if ($arr['user_blocked_from']>0 && $arr['user_blocked_from']<time() && $arr['user_blocked_to']>time())
					{
						$blocked=1;
					}
					$hmod=0;
					if ($arr['user_hmode_from']>0 && $arr['user_hmode_from']< time())				
					{
						$hmod=1;
					}
					$inactive=0;
					if ($arr['user_last_online']>0 && $arr['user_last_online']< time()-($conf['user_inactive_days']['v']*3600*24))
					{
						$inactive=1;
					}
					
					dbquery("
					INSERT INTO
						user_stats
					(
						user_id,
						user_points,
						user_points_ships,
						user_points_tech,
						user_points_buildings,
						user_rank_current,
						user_rank_last,
						user_nick,
						alliance_tag,
						alliance_id,
						race_name,
						cell_sx,
						cell_sy,
						user_inactive,
						user_hmod,
						user_blocked
					)
					VALUES
					(
						".$arr['user_id'].",
						".$arr['user_points'].",
						".$arr['user_points_ships'].",
						".$arr['user_points_tech'].",
						".$arr['user_points_buildings'].",
						".$rank.",
						".$arr['user_rank_last'].",
						'".$arr['user_nick']."',
						'".$arr['alliance_tag']."',
						'".$arr['alliance_id']."',
						'".$arr['race_name']."',
						'".$mp[$arr['user_id']]['sx']."',
						'".$mp[$arr['user_id']]['sy']."',
						'".$inactive."',
						'".$hmod."',
						'".$blocked."'						
					);");
					
					$rank++;
				}
			}
	
			// Allianz Statistik generieren
			dbquery("
			TRUNCATE TABLE
				alliance_stats
			");
			
			$res=dbquery("SELECT 
				a.alliance_tag,
				a.alliance_name,
				a.alliance_id,
				COUNT(*) AS cnt, 
				SUM(u.user_points) AS upoints, 
				AVG(u.user_points) AS uavg 
			FROM 
				".$db_table['alliances']." as a
			INNER JOIN
				user_stats as u
			ON
				u.alliance_id=a.alliance_id
			GROUP BY 
				a.alliance_id 
			;");
			if (mysql_num_rows($res)>0)
			{
				while ($arr=mysql_fetch_array($res))
				{
					$apoints=0;
					if ($arr['upoints']>0 && $conf['points_update']['p2']>0)
					{
						$apoints = floor($arr['upoints'] / $conf['points_update']['p2']);
					}
					dbquery("
					INSERT INTO
						alliance_stats
					(
						alliance_id,
						alliance_tag,
						alliance_name,
						upoints,
						uavg,
						cnt
					) 
					VALUES 
					(
						'".$arr['alliance_id']."',
						'".$arr['alliance_tag']."',
						'".$arr['alliance_name']."',
						'".$apoints."',
						'".$arr['uavg']."',
						'".$arr['cnt']."'
					);");
				}
			}		
	
	
			// Zeit in Config speichern
			dbquery ("
				UPDATE
					".$db_table['config']."
				SET
					config_value='".time()."'
				WHERE
					config_name='statsupdate';
			");
			$num = mysql_num_rows($ures);
	
	 		// Log-Eintrag
	 		if ($manual)
	 			add_log("4","Statistiken wurden manuell vom User ".$_SESSION[SESSION_NAME]['user_nick']." aktualisiert!",time());
	 		else
	 			add_log("4","Statistiken wurden aktualisiert!",time());
	
			return array($num,$allpoints);
	
			//Arrays löschen (Speicher freigeben)
			mysql_free_result($res);
	        unset($arr);
		}		

	}

?>