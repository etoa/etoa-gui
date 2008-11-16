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
			$cfg = Config::getInstance();
			ob_start();
			
			$img_dir = ($admin==1) ? "../images" : "images";
			
			$titles=array(
				"total"=>"",
				"battle"=>"_ships",
				"tech"=>"_tech",
				"buildings"=>"_buildings",
				"exp"=>"_exp");

			$titles2 = array('battle','trade','diplomacy');

			
			tableStart("Allgemeine Titel");
			$cnt = 0;
			foreach ($titles as $k=> $v)
			{
				$res = dbquery("
				SELECT 
					nick,
					points".$v.",
					id
				FROM 
					user_stats
				WHERE
					rank".$v.">0
					AND points".$v.">0
				ORDER BY 
					rank".$v." ASC 
				LIMIT 1;");
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_row($res);
					$profile = ($admin==1) ? "?page=user&amp;sub=edit&amp;user_id=".$arr[2]."" : "?page=userinfo&amp;id=".$arr[2];
					echo "<tr>
						<th class=\"tbltitle\" style=\"width:100px;height:100px;\">
							<img src='".$img_dir."/medals/medal_".$k.".png' alt=\"medal\" style=\"height:100px;\" />
						</th>
						<td class=\"tbldata\" style=\"font-size:16pt;vertical-align:middle;padding:2px 10px 2px 10px;width:400px;\">
							".$cfg->value('userrank_'.$k)."
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
			foreach ($titles2 as $v)
			{
				$res = dbquery("
				SELECT 
					user_nick,
					".$v."_rating,
					user_id
				FROM 
					users
				INNER JOIN	
					user_ratings
				ON user_id=id
				AND
					".$v."_rating>0
				ORDER BY 
					".$v."_rating DESC 
				LIMIT 1;");
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_row($res);
					$profile = ($admin==1) ? "?page=user&amp;sub=edit&amp;user_id=".$arr[2]."" : "?page=userinfo&amp;id=".$arr[2];
					echo "<tr>
						<th class=\"tbltitle\" style=\"width:100px;height:100px;\">
							<img src='".$img_dir."/medals/medal_".$v.".png' style=\"height:100px;\" />
						</th>
						<td class=\"tbldata\" style=\"font-size:16pt;vertical-align:middle;padding:2px 10px 2px 10px;width:400px;\">
							".$cfg->value('userrank_'.$v)."
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
			tableEnd();
			tableStart("Rassenleader");
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
			while ($rarr = mysql_fetch_assoc($rres))
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
					AND user_ghost=0
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
			tableEnd();
		
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
		
		
		/**
		* Punkteberechnung
		*/
		static function calc($manual=false)
		{
			$cfg = Config::getInstance();
			
			$time = time();
			$inactivetime = 86400 * USER_INACTIVE_SHOW;
			$allpoints=0;
			$res_amount_per_point = $cfg->param1('points_update');
	
			// Schiffe laden
			$res = dbquery("
				SELECT
					ship_id,
					ship_points					
				FROM
					ships;
			");
	    $ship=array();
			while ($arr = mysql_fetch_row($res))
			{
				$ship[$arr[0]]=$arr[1];
			}
	
			// Verteidigung laden
			$res = dbquery("
				SELECT
					def_id,
					def_points					
				FROM
					defense
			;");
	    $def=array();
			while ($arr = mysql_fetch_row($res))
			{
				$def[$arr[0]]=$arr[1];
			}
	
			// Gebäude laden
			$res = dbquery("
				SELECT
					bp_building_id,
					bp_level,					
					bp_points
				FROM
					building_points;
			");
	    $building=array();
			while ($arr = mysql_fetch_row($res))
			{
				$building[$arr[0]][$arr[1]]=$arr[2];
			}
	
			// Technologien laden
			$res = dbquery("
				SELECT
					bp_tech_id,
					bp_level,
					bp_points
				FROM
					tech_points;
			");
	    $tech=array();
			while ($arr = mysql_fetch_row($res))
			{
				$tech[$arr[0]][$arr[1]]=$arr[2];
			}
	


			// Rassen laden
			$rres = dbquery("
			SELECT
				race_id,
				race_name
			FROM
				races
			");
			$race=array();
			while($rarr=mysql_fetch_assoc($rres))
			{
				$race[$rarr['race_id']]=$rarr['race_name'];
			}
			
			// Allianzen laden
			$rres = dbquery("
			SELECT
				alliance_id,
				alliance_tag
			FROM
				alliances
			");
			$alliance=array();
			while($rarr=mysql_fetch_assoc($rres))
			{
				$alliance[$rarr['alliance_id']]=$rarr['alliance_tag'];
			}			
	
			// Load 'old' ranks
			$res = dbquery("
				SELECT
					id,
					rank,
					rank_ships,
					rank_tech,
					rank_buildings,
					rank_exp
				FROM
					user_stats;
			");
			$oldranks = array();		
			if (mysql_num_rows($res)>0)
			{
				while ($arr=mysql_fetch_row($res))
				{
					$oldranks[$arr[0]][0]=$arr[1];
					$oldranks[$arr[0]][1]=$arr[2];
					$oldranks[$arr[0]][2]=$arr[3];
					$oldranks[$arr[0]][3]=$arr[4];
					$oldranks[$arr[0]][4]=$arr[5];
				}
			}
			
			// Statistiktabelle leeren
			dbquery("
				TRUNCATE TABLE
					user_stats;
			");		
	
			// User-ID's laden
			$ures =	dbquery("
				SELECT
					user_id,
					user_nick,
					user_race_id,
					user_alliance_id,
					user_rank_highest,
					user_blocked_to,
					user_hmode_from,
					user_acttime
				FROM
					users;
			");
			$user_stats_query = "";
			$user_points_query = "";
			$user_rank_highest=array();
			while ($uarr=mysql_fetch_assoc($ures))
			{
				$user_id = $uarr['user_id'];
				$user_rank_highest[$user_id] = $uarr['user_rank_highest']>0 ? $uarr['user_rank_highest'] : 9999;
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
						shiplist
					WHERE
						shiplist_user_id='".$user_id."';
				");
				while ($arr = mysql_fetch_assoc($res))
				{
					$p = round($arr['shiplist_count']*$ship[$arr['shiplist_ship_id']]);
					$points+=$p;
					$points_ships+=$p;
				}
	
				//
				// Punkte für Schiffe (in Flotten)
				// TODO: Check this query (EXPLAIN)
				$res = dbquery("
					SELECT
						fs.fs_ship_id,
						fs.fs_ship_cnt
					FROM
						fleet AS f
					INNER JOIN 
						fleet_ships AS fs
						ON f.id = fs.fs_fleet_id
						AND fs.fs_ship_faked='0'
						AND f.user_id='".$user_id."'
						;
				");
				while ($arr = mysql_fetch_assoc($res))
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
						deflist
					WHERE
						deflist_user_id='".$user_id."';
				");
				while ($arr = mysql_fetch_assoc($res))
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
						buildlist
					WHERE
						buildlist_user_id='".$user_id."';
				");
				while ($arr = mysql_fetch_assoc($res))
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
						techlist
					WHERE
						techlist_user_id='".$user_id."';
				");
				while ($arr = mysql_fetch_assoc($res))
				{
					$p = round($tech[$arr['techlist_tech_id']][$arr['techlist_current_level']]);
					$points+=$p;
					$points_tech+=$p;
				}
				
				//
				// Punkte für XP
				//
				$res = dbquery("
					SELECT
						SUM(shiplist_special_ship_exp)
					FROM
						shiplist
					WHERE
						shiplist_user_id='".$user_id."'
						AND shiplist_count=1;
				");
				$arr = mysql_fetch_row($res);
				$points_exp = max(0,$arr[0]);

				// Save part of insert query
				$user_stats_query .= ",(
						".$user_id.",
						".$points.",
						".$points_ships.",
						".$points_tech.",
						".$points_building.",
						".$points_exp.",
						'".$uarr['user_nick']."',
						'".($uarr['user_alliance_id']>0 ? $alliance[$uarr['user_alliance_id']] : '')."',
						'".$uarr['user_alliance_id']."',
						'".($uarr['user_race_id']>0 ? $race[$uarr['user_race_id']] : '')."',
						2,
						2,
						".($uarr['user_blocked_to'] > $time ? 1 : 0).",
						".($uarr['user_acttime'] < $time-$inactivetime ? 1 : 0).",
						".($uarr['user_hmode_from'] > 0 ? 1 : 0)."
					)";
				$user_points_query.=",(
						'".$user_id."',
						'".time()."',
						'".$points."',
						'".$points_ships."',
						'".$points_tech."',
						'".$points_building."'
					)";				
				
				$allpoints+=$points;
			}
			unset($user_id);
	
			// Save points in memory cached table
			dbquery("
				INSERT INTO
					user_stats
				(
					id,
					points,
					points_ships,
					points_tech,
					points_buildings,
					points_exp,
					nick,
					alliance_tag,
					alliance_id,
					race_name,
					sx,
					sy,
					blocked,
					inactive,
					hmod
				)
				VALUES
					".substr($user_stats_query,1)."
				;
			");		
			
			// Save points to user points table
			dbquery("
				INSERT INTO
				user_points
				(
					point_user_id,
					point_timestamp,
					point_points,
					point_ship_points,
					point_tech_points,
					point_building_points
				)
				VALUES
					".substr($user_points_query,1)."
			");
	
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
			unset($user_stats_query);
			unset($user_points_query);
		
			// Ranking (Total Points)
			$res = dbquery("
			SELECT
				id,
				points
			FROM
				user_stats
			ORDER BY
				points DESC;			
			");
			$cnt=1;
			if (mysql_num_rows($res)>0)
			{
				while($arr=mysql_fetch_row($res))
				{
					$rs = 0;
					if (isset($oldranks[$arr[0]]))
					{
						if ($cnt < $oldranks[$arr[0]][0])
							$rs = 1;
						elseif ($cnt > $oldranks[$arr[0]][0])
							$rs = 2;
					}
					dbquery("
					UPDATE
						user_stats
					SET
						rank=".$cnt.",
						rankshift=".$rs."
					WHERE
						id=".$arr[0].";");
					dbquery("
					UPDATE
						users
					SET	
						user_rank=".$cnt.",
						user_points=".$arr[1].",
						user_rank_highest=".min($cnt,$user_rank_highest[$arr[0]])."
					WHERE
						user_id=".$arr[0]."
					");
						
					$cnt++;
				}				
			}
			unset($user_rank_highest);
			
			// Ranking (Ships)
			$res = dbquery("
			SELECT
				id
			FROM
				user_stats
			ORDER BY
				points_ships DESC;			
			");
			$cnt=1;
			if (mysql_num_rows($res)>0)
			{
				while($arr=mysql_fetch_row($res))
				{
					$rs = 0;
					if (isset($oldranks[$arr[0]]))
					{
						if ($cnt < $oldranks[$arr[0]][1])
							$rs = 1;
						elseif ($cnt > $oldranks[$arr[0]][1])
							$rs = 2;
					}
					dbquery("
					UPDATE
						user_stats
					SET
						rank_ships=".$cnt.",
						rankshift_ships=".$rs."
					WHERE
						id=".$arr[0].";");
					$cnt++;
				}				
			}							

			// Ranking (Tech)
			$res = dbquery("
			SELECT
				id
			FROM
				user_stats
			ORDER BY
				points_tech DESC;			
			");
			$cnt=1;
			if (mysql_num_rows($res)>0)
			{
				while($arr=mysql_fetch_row($res))
				{
					$rs = 0;
					if (isset($oldranks[$arr[0]]))
					{
						if ($cnt < $oldranks[$arr[0]][2])
							$rs = 1;
						elseif ($cnt > $oldranks[$arr[0]][2])
							$rs = 2;
					}
					dbquery("
					UPDATE
						user_stats
					SET
						rank_tech=".$cnt.",
						rankshift_tech=".$rs."
					WHERE
						id=".$arr[0].";");
					$cnt++;
				}				
			}				

			// Ranking (Buildings)
			$res = dbquery("
			SELECT
				id
			FROM
				user_stats
			ORDER BY
				points_buildings DESC;			
			");
			$cnt=1;
			if (mysql_num_rows($res)>0)
			{
				while($arr=mysql_fetch_row($res))
				{
					$rs = 0;
					if (isset($oldranks[$arr[0]]))
					{
						if ($cnt < $oldranks[$arr[0]][3])
							$rs = 1;
						elseif ($cnt > $oldranks[$arr[0]][3])
							$rs = 2;
					}
					dbquery("
					UPDATE
						user_stats
					SET
						rank_buildings=".$cnt.",
						rankshift_buildings=".$rs."
					WHERE
						id=".$arr[0].";");
					$cnt++;
				}				
			}										
	
			// Ranking (Exp)
			$res = dbquery("
			SELECT
				id
			FROM
				user_stats
			ORDER BY
				points_exp DESC;			
			");
			$cnt=1;
			if (mysql_num_rows($res)>0)
			{
				while($arr=mysql_fetch_row($res))
				{
					$rs = 0;
					if (isset($oldranks[$arr[0]]))
					{
						if ($cnt < $oldranks[$arr[0]][4])
							$rs = 1;
						elseif ($cnt > $oldranks[$arr[0]][4])
							$rs = 2;
					}
					dbquery("
					UPDATE
						user_stats
					SET
						rank_exp=".$cnt.",
						rankshift_exp=".$rs."
					WHERE
						id=".$arr[0].";");
					$cnt++;
				}				
			}				
			unset($oldranks);
			
			
			
			// Allianz Statistik generieren
			dbquery("
			TRUNCATE TABLE
				alliance_stats
			");
			
			$res=dbquery("SELECT 
				a.alliance_tag,
				a.alliance_name,
				a.alliance_id,
				a.alliance_rank_current,
				COUNT(*) AS cnt, 
				SUM(u.points) AS upoints, 
				AVG(u.points) AS uavg 
			FROM 
				alliances as a
			INNER JOIN
				user_stats as u
			ON
				u.alliance_id=a.alliance_id
			GROUP BY 
				a.alliance_id
			ORDER BY
				SUM(u.points) DESC
			;");
			if (mysql_num_rows($res)>0)
			{
				$rank=1;
				while ($arr=mysql_fetch_assoc($res))
				{
					$apoints=0;
					if ($arr['upoints']>0 && $cfg->param2('points_update')>0)
					{
						$apoints = floor($arr['upoints'] / $cfg->param2('points_update')
);
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
						cnt,
						alliance_rank_current,
						alliance_rank_last
					) 
					VALUES 
					(
						'".$arr['alliance_id']."',
						'".$arr['alliance_tag']."',
						'".$arr['alliance_name']."',
						'".$apoints."',
						'".$arr['uavg']."',
						'".$arr['cnt']."',
						'".$rank."',
						'".$arr['alliance_rank_current']."'
					);");
					
					dbquery("
					UPDATE 
						alliances
					SET
						alliance_points='".$apoints."',
						alliance_rank_current='".$rank."',
						alliance_rank_last='".$arr['alliance_rank_current']."'
					WHERE
						alliance_id='".$arr['alliance_id']."'
					;");
					
					dbquery("
					INSERT INTO
						alliance_points
					(
						point_alliance_id,
						point_timestamp,
						point_points,
						point_avg,
						point_cnt
					)
					VALUES
					(
						'".$arr['alliance_id']."',
						'".time()."',
						'".$apoints."',
						'".$arr['uavg']."',
						'".$arr['cnt']."'
					);");

				}
			}		
			
	
	
			// Zeit in Config speichern
			$cfg->set('statsupdate',time());
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

		/**
		* Add battle points
		*/
		static function addBattlePoints($userId,$points,$reason="")
		{
			if ($points!=0)
			{
				dbquery("
				UPDATE
					users
				SET
					user_points_battle=user_points_battle+".$points."
				WHERE
					user_id=".$userId.";");
				add_log(17,"Der Spieler ".$userId." erhält ".$points." Kampfpunkte. Grund: ".$reason);
			}
		}
		
		static function addTradePoints($userId,$points,$reason="")
		{
			dbquery("
			UPDATE
				users
			SET
				user_points_trade=user_points_trade+".$points."
			WHERE
				user_id=".$userId.";");			
			add_log(17,"Der Spieler ".$userId." erhält ".$points." Handelspunkte. Grund: ".$reason);
		}
		
		/**
		* Add diplomacy points
		*/
		static function addDiplomacyPoints($userId,$points,$reason="")
		{
			dbquery("
			UPDATE
				users
			SET
				user_points_diplomacy=user_points_diplomacy+".$points."
			WHERE
				user_id=".$userId.";");			
			add_log(17,"Der Spieler ".$userId." erhält ".$points." Diplomatiepunke. Grund: ".$reason);
		}				

	}

?>