<?PHP

	/**
	* Universe class
	* Provides creatoin!
	*/
	class Universe
	{

		/**
		* Create the universe
		* And there was light!
		*/	
		static function create()
		{
			$cfg = Config::getInstance();
			
			echo "Lade Schöpfungs-Einstellungen...!<br>";
			$sx_num = $cfg->param1('num_of_sectors');
			$sy_num = $cfg->param2('num_of_sectors');
			$cx_num = $cfg->param1('num_of_cells');
			$cy_num = $cfg->param2('num_of_cells');
			$planet_fields_min = $cfg->param1('planet_fields');
			$planet_fields_max = $cfg->param2('planet_fields');
			$planet_temp_min = $cfg->param1('planet_temp');
			$planet_temp_max = $cfg->param2('planet_temp');
			$planet_temp_diff = $cfg->value('planet_temp');
			$planet_temp_totaldiff = abs($planet_temp_min) + abs($planet_temp_max);
			$perc_solsys = $cfg->value('space_percent_solsys');
			$perc_asteroids = $cfg->value('space_percent_asteroids');
			$perc_nebulas = $cfg->value('space_percent_nebulas');
			$perc_wormholes = $cfg->value('space_percent_wormholes');
			$num_planets_min = $cfg->param1('num_planets');
			$num_planets_max = $cfg->param2('num_planets');
			$num_planet_images = $cfg->value('num_planet_images');
			
			$sol_types = array();
			$res = dbquery("
			SELECT
		    type_id
			FROM
				sol_types;");
			while ($arr = mysql_fetch_array($res))
			{
				$sol_types[] = $arr['type_id'];
			}
			
			$planet_types = array();
			$res = dbquery("
			SELECT
		    type_id
			FROM
				planet_types;");
			while ($arr = mysql_fetch_array($res))
			{
				$planet_types[] = $arr['type_id'];
			}

			$planet_count = 0;
			$sol_count = 0;
			$nebula_count = 0;
			$asteroids_count = 0;
			$wormhole_count = 0;

			echo "Erstelle Universum mit ".$sx_num*$sy_num." Sektoren à ".$cx_num*$cy_num." Zellen, d.h. ".$sx_num*$sy_num*$cx_num*$cy_num." Zellen total<br>";
	
			$sql = "";	
			for ($sx=1;$sx<=$sx_num;$sx++)
			{
				for ($sy=1;$sy<=$sy_num;$sy++)
				{
					for ($cx=1;$cx<=$cx_num;$cx++)
					{
						for ($cy=1;$cy<=$cy_num;$cy++)
						{
							// Save cell
							if ($sql=="")
							{
								$sql .="(
									'".$sx."',
									'".$sy."',
									'".$cx."',
									'".$cy."'
								)";
								}
							else
							{
								$sql .=",(
									'".$sx."',
									'".$sy."',
									'".$cx."',
									'".$cy."'
								)";
							}
						}
					}
				}
			}
			echo "Zellen geneiert, speichere sie...<br/>";
			dbquery("
				INSERT INTO 
					cells 
				(
					sx,
					sy,
					cx,
					cy
				)
				VALUES ".$sql);
		
			echo "Zellen gespeichert, fülle Objekte rein...<br/>";
			$res = dbquery("
			SELECT
				id
			FROM
				cells;");
			while ($arr=mysql_Fetch_row($res))
			{
				$cell_id = $arr[0];					
							
				// Assign entities
				$ct = mt_rand(1,100);
			
				// Star system
				if ($ct<=$perc_solsys)
				{
					// The Star
					$st = $sol_types[array_rand($sol_types)];
					$sql = "
						INSERT INTO
							entities
						(
							cell_id,
							type,
							pos
						)
						VALUES
						(
							".$cell_id.",
							's',
							0
						);
					";
					dbquery($sql);
					$eid = mysql_insert_id();

					$sql = "
						INSERT INTO
							stars
						(
							id,
							type_id
						)
						VALUES
						(
							".$eid.",
							".$st."
						);
					";
					dbquery($sql);

					// The planets
					$np = mt_rand($num_planets_min,$num_planets_max);
					for ($cnp=1;$cnp<=$np;$cnp++)
					{
						$sql = "
							INSERT INTO
								entities
							(
								cell_id,
								type,
								pos
							)
							VALUES
							(
								".$cell_id.",
								'p',
								".$cnp."
							);
						";
						dbquery($sql);
						$eid = mysql_insert_id();

						$pt = $planet_types[array_rand($planet_types)];
						$img_nr = $pt."_".mt_rand(1,$num_planet_images);
						$fields = mt_rand($planet_fields_min,$planet_fields_max);
						$tblock =  round($planet_temp_totaldiff / $np);
						$temp = mt_rand($planet_temp_max-($tblock*$cnp),($planet_temp_max-($tblock*$cnp)+$tblock));
						$tmin = $temp - $planet_temp_diff;
						$tmax = $temp + $planet_temp_diff;
						$sql = "
							INSERT INTO
								planets
							(
								id,
								planet_type_id,
								planet_fields,
								planet_image,
								planet_temp_from,
								planet_temp_to
							)
							VALUES
							(
								'".$eid."',
								'".$pt."',
								'".$fields."',
								'".$img_nr."',
								'".$tmin."',
								'".$tmax."'
							)";
						dbquery($sql);	// Planet speichern
						$planet_count++;
					}
					$sol_count++;
				}
				
				// Asteroid Fields
				elseif ($ct<=$perc_solsys + $perc_asteroids)
				{
					$sql = "
						INSERT INTO
							entities
						(
							cell_id,
							type,
							pos
						)
						VALUES
						(
							".$cell_id.",
							'a',
							0
						);
					";
					dbquery($sql);
					$eid = mysql_insert_id();

					$asteroid_ress = mt_rand($cfg->param1('asteroid_ress'),$conf['asteroid_ress']['p2']);
					$sql = "
						INSERT INTO
							asteroids
						(
							id,
							resources
						)
						VALUES
						(
							".$eid.",
							".$asteroid_ress."
						);
					";
					dbquery($sql);				
					
					$asteroids_count++;
				}
				
				// Nebulas
				elseif ($ct<=$perc_solsys + $perc_asteroids + $perc_nebulas)
				{
					$sql = "
						INSERT INTO
							entities
						(
							cell_id,
							type,
							pos
						)
						VALUES
						(
							".$cell_id.",
							'n',
							0
						);
					";
					dbquery($sql);
					$eid = mysql_insert_id();

					$nebula_ress = mt_rand($cfg->param1('nebula_ress'),$cfg->param2('nebula_ress'));
					$sql = "
						INSERT INTO
							nebulas
						(
							id,
							resources
						)
						VALUES
						(
							".$eid.",
							".$nebula_ress."
						);
					";
					dbquery($sql);				
					
					$nebula_count++;
				}
				
				// Wormholes
				elseif ($ct<=$perc_solsys + $perc_asteroids + $perc_nebulas + $perc_wormholes)
				{
					$sql = "
						INSERT INTO
							entities
						(
							cell_id,
							type,
							pos
						)
						VALUES
						(
							".$cell_id.",
							'w',
							0
						);
					";
					dbquery($sql);
					$eid = mysql_insert_id();								

					$sql = "
						INSERT INTO
							wormholes
						(
							id,
							changed
						)
						VALUES
						(
							".$eid.",
							".time()."
						);
					";
					dbquery($sql);			
					$wormhole_count++;
				}
							
				// Empty space
				else
				{
					$sql = "
						INSERT INTO
							entities
						(
							cell_id,
							type,
							pos
						)
						VALUES
						(
							".$cell_id.",
							'e',
							0
						);
					";
					dbquery($sql);
					$eid = mysql_insert_id();

					$sql = "
						INSERT INTO
							space
						(
							id,
							lastvisited
						)
						VALUES
						(
							".$eid.",
							0
						);
					";
					dbquery($sql);	
				}
			}
			echo "Universum erstellt, prüfe Wurmlöcher...<br/>";
	
			//
			// Wormhole-Linking
			//
			$wh = array();
			$wh_new = array();
			$res = dbquery("
      SELECT
				id,
				target_id
	   	FROM
	    	wormholes
			");
			// Delete one wormhole if total count is odd
			// Replace it with empty space
			if (fmod(mysql_num_rows($res),2)!=0) 
			{
				echo "<br>Ein Wurmloch ist zuviel, lösche es!<br>";
				$arr=mysql_fetch_array($res);
				dbquery("
        UPDATE
        	entities
        SET
        	type='e'
        WHERE
        	id='".$arr['id']."'
				");
				dbquery("
        DELETE FROM
        	wormholes
        WHERE
        	id='".$arr['id']."'
				");
				dbquery("
					INSERT INTO
						space
					(
						id,
						lastvisited
					)
					VALUES
					(
						".$arr['id'].",
						0
					);
				");
	
				$res = dbquery("
				SELECT
					id,
					target_id
				FROM
					wormholes
				");
			}
			while ($arr=mysql_fetch_array($res))
			{
				array_push($wh,$arr['id']);
			}
			shuffle($wh);
			while (sizeof($wh)>0)
			{
				$wh_new[array_shift($wh)]=array_pop($wh);
			}
			$wormhole_count = mysql_num_rows($res);
			foreach ($wh_new as $k=>$v)
			{
				dbquery("
	            UPDATE
	            	wormholes
	            SET
	            	target_id='".$k."'
	            WHERE
	            	id='".$v."';
				");
				echo mysql_error();
				dbquery("
	            UPDATE
	            	wormholes
	            SET
	            	target_id='".$v."'
	            WHERE
	            	id='".$k."';
				");
				echo mysql_error();
			} 
	
			echo "Universum erstellt!<br> $sol_count Sonnensysteme mit $planet_count Planeten, $asteroids_count Asteroidenfelder, $nebula_count Nebel und $wormhole_count Wurmlöcher!";
	
		}	
		
		
		static function reset()
		{
			$tbl[]="cells";
			$tbl[]="entities";
			$tbl[]="stars";
			$tbl[]="planets";
			$tbl[]="asteroids";
			$tbl[]="nebulas";
			$tbl[]="wormholes";
			$tbl[]="space";
			
			$tbl[]="buildlist";
			$tbl[]="deflist";
			$tbl[]="def_queue";
			$tbl[]="fleet";
			$tbl[]="fleet_ships";
			$tbl[]="market_auction";
			$tbl[]="market_ship";
			$tbl[]="market_ressource";
			$tbl[]="missilelist";
			$tbl[]="missile_flights";
			$tbl[]="missile_flights_obj";
			$tbl[]="shiplist";
			$tbl[]="ship_queue";
			$tbl[]="techlist";

			$tbl[]="alliances";
			$tbl[]="alliance_bnd";
			$tbl[]="alliance_history";
			$tbl[]="alliance_news";
			$tbl[]="alliance_ranks";
			$tbl[]="alliance_poll_votes";
			$tbl[]="alliance_rankrights";
			$tbl[]="allianceboard_cat";
			$tbl[]="allianceboard_posts";
			$tbl[]="allianceboard_catranks";
			$tbl[]="allianceboard_topics";
			$tbl[]="alliance_stats";
			$tbl[]="alliance_shoutbox";
			$tbl[]="alliance_polls";

			$tbl[]="users";
			$tbl[]="user_history";
			$tbl[]="user_multi";
			$tbl[]="user_log";
			$tbl[]="user_points";
			$tbl[]="user_requests";
			$tbl[]="user_sitting";
			$tbl[]="user_sitting_date";
			$tbl[]="user_stats";
			$tbl[]="user_onlinestats";

			
			$tbl[]="buddylist";
			$tbl[]="messages";
			$tbl[]="message_ignore";
			$tbl[]="notepad";
			$tbl[]="target_bookmarks";

			$tbl[]="logs";
			$tbl[]="login_failures";
			$tbl[]="admin_user_log";
			$tbl[]="logs_game";
			$tbl[]="logs_battle";
	
			foreach ($tbl as $t)
			{
				dbquery("TRUNCATE $t;");
				echo "Leere Tabelle <b>$t</b><br/>";
			}
			return true;
		}
		
	
	}


?>