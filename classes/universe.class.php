<?PHP

	/**
	* Universe class
	* Provides creatoin!
	*/
	class Universe
	{

		/**
		* Create the universe.
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

			$type=array();

			//
			// Set cell types
			//
			
			// by image
			$imgpath = "../images/galaxylayout_".($sx_num*$cx_num)."_".($sy_num*$cy_num).".png";
			if (is_file($imgpath))	
			{
				$im = imagecreatefrompng($imgpath);
				$w = imagesx($im);
				$h = imagesy($im);

				echo "Bildvorlage gefunden, verwende diese: <img src=\"".$imgpath."\" /><br/>";

				for($x=1;$x<=$w;$x++)
				{
					for($y=1;$y<=$h;$y++)
					{
						$o = imagecolorat($im,$x,$y);
						
						if ($o>0)
						{
							$type[$x][$y]='s';
						}
						else
						{
							$ct = mt_rand(1,100);
							if ($ct<= $perc_asteroids)
								$type[$x][$y]='a';
							elseif ($ct<= $perc_asteroids + $perc_nebulas)
								$type[$x][$y]='n';
							elseif ($ct<= $perc_asteroids + $perc_nebulas + $perc_wormholes)
								$type[$x][$y]='w';
							else
								$type[$x][$y]='e';
						}						
						
						/*
						if ($o>0)
						{
							$ct = mt_rand(1,100);
							if ($ct<=$perc_solsys)
								$type[$x][$y]='s';
							elseif ($ct<=$perc_solsys + $perc_asteroids)
								$type[$x][$y]='a';
							elseif ($ct<=$perc_solsys + $perc_asteroids + $perc_nebulas)
								$type[$x][$y]='n';
							elseif ($ct<=$perc_solsys + $perc_asteroids + $perc_nebulas + $perc_wormholes)
								$type[$x][$y]='w';
							else
								$type[$x][$y]='s';
						}
						else
						{
							$type[$x][$y]='e';
						}*/
					}
				}
			}
			// by randomizer with config values
			else
			{
				for($x=1;$x<=($sx_num*$cx_num);$x++)
				{
					for($y=1;$y<=($sy_num*$cy_num);$y++)
					{
						$ct = mt_rand(1,100);
						if ($ct<=$perc_solsys)
							$type[$x][$y]='s';
						elseif ($ct<=$perc_solsys + $perc_asteroids)
							$type[$x][$y]='a';
						elseif ($ct<=$perc_solsys + $perc_asteroids + $perc_nebulas)
							$type[$x][$y]='n';
						elseif ($ct<=$perc_solsys + $perc_asteroids + $perc_nebulas + $perc_wormholes)
							$type[$x][$y]='w';
						else
							$type[$x][$y]='e';
					}
				}				
			}
		
			// Save cell info
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
				id,
				sx,
				sy,
				cx,
				cy
			FROM
				cells;");
			while ($arr=mysql_fetch_row($res))
			{
				$cell_id = $arr[0];					
				$x = (($arr[1]-1)*10)+$arr[3];
				$y = (($arr[2]-1)*10)+$arr[4];
							
				// Star system
				if ($type[$x][$y]=='s')
				{
					// The Star
					$st = $sol_types[array_rand($sol_types)];
					$sql = "
						INSERT INTO
							entities
						(
							cell_id,
							code,
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
								code,
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
				elseif ($type[$x][$y]=='a')
				{
					$sql = "
						INSERT INTO
							entities
						(
							cell_id,
							code,
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
				elseif ($type[$x][$y]=='n')
				{
					$sql = "
						INSERT INTO
							entities
						(
							cell_id,
							code,
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
				elseif ($type[$x][$y]=='w')
				{
					$sql = "
						INSERT INTO
							entities
						(
							cell_id,
							code,
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
							code,
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
							id
						)
						VALUES
						(
							".$eid."
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
        	code='e'
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
			
			echo "Platziere Marktplatz...<br />";
			dbquery("
				UPDATE
					entities
				SET
					code='m'
				WHERE
					code='e'
				ORDER BY
					RAND()
				LIMIT
					1;");
			dbquery("
				DELETE FROM
					spaces
				WHERE
					id='".ysql_insert_id()."'
				LIMIT
					1;");
					
			echo "Erstelle Piraten, Aliens, Schiffe,..<br />";
			$res = dbquery("
							SELECT
								id,
								code
							FROM
								entities
							ORDER BY
								RAND();");
			while ($arr=mysql_fetch_array($res)) {
				
				dbquery("
						UPDATE
							entities
						SET
							explore_code='".$code."'
						WHERE
							id='".$arr["id"]."'
						LIMIT
							1;");
			}
				
			
			
			
					
			echo "Universum erstellt!<br> $sol_count Sonnensysteme mit $planet_count Planeten, $asteroids_count Asteroidenfelder, $nebula_count Nebel und $wormhole_count Wurmlöcher!";
	
		}	
		
		/**
		* Resets the universe and all user data
		* The Anti-Big-Bang
		*/
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
			$tbl[]="alliance_points";

			$tbl[]="users";
			$tbl[]="user_history";
			$tbl[]="user_multi";
			$tbl[]="user_log";
			$tbl[]="user_points";
			$tbl[]="user_requests";
			$tbl[]="user_sitting";
			$tbl[]="user_sitting_date";
			$tbl[]="user_stats";
			$tbl[]="user_ratings";
			$tbl[]="user_onlinestats";
			$tbl[]="user_comments";
			$tbl[]="user_warnings";

			
			$tbl[]="buddylist";
			$tbl[]="messages";
			$tbl[]="message_data";
			$tbl[]="message_ignore";
			$tbl[]="notepad";
			$tbl[]="notepad_data";
			$tbl[]="bookmarks";

			$tbl[]="logs";
			$tbl[]="login_failures";
			$tbl[]="admin_user_log";
			$tbl[]="logs_game";
			$tbl[]="logs_battle";
			$tbl[]="tickets";
			$tbl[]="chat";
			$tbl[]="chat_users";
			
	
			foreach ($tbl as $t)
			{
				dbquery("TRUNCATE $t;");
				echo "Leere Tabelle <b>$t</b><br/>";
			}
			return true;
		}
		
		/**
		* Erweitert Universum
		*
		* @param string $sx_num_new,$sy_num_new
		* @author Lamborghini
		* @todo This method has to be rewritten due to the database changes with the new entity table
		*/
		function expansion_universe($sx_num_new,$sy_num_new)
		{
			global $conf,$db_table;
	
			$sx_num=$conf['num_of_sectors']['p1'];
			$sy_num=$conf['num_of_sectors']['p2'];
			$cx_num=$conf['num_of_cells']['p1'];
			$cy_num=$conf['num_of_cells']['p2'];
			$planet_fields_min=$conf['planet_fields']['p1'];
			$planet_fields_max=$conf['planet_fields']['p2'];
			$planet_temp_min=$conf['planet_temp']['p1'];
			$planet_temp_max=$conf['planet_temp']['p2'];
			$planet_temp_diff=$conf['planet_temp']['v'];
			$planet_temp_totaldiff=abs($planet_temp_min)+abs($planet_temp_max);
			$perc_solsys=$conf['space_percent_solsys']['v'];
			$perc_asteroids=$conf['space_percent_asteroids']['v'];
			$perc_nebulas=$conf['space_percent_nebulas']['v'];
			$perc_wormholes=$conf['space_percent_wormholes']['v'];
			$num_planets_min=$conf['num_planets']['p1'];
			$num_planets_max=$conf['num_planets']['p2'];
			$num_sol_types=mysql_num_rows(dbquery("SELECT * FROM ".$db_table['sol_types'].";"));
			$sol_types = get_sol_types_array();
			$num_planet_types=mysql_num_rows(dbquery("SELECT * FROM ".$db_table['planet_types'].";"));
			$planet_types = get_planet_types_array();
			$num_planet_images = $conf['num_planet_images']['v'];
			$planet_count = 0;
			$sol_count = 0;
			$nebula_count = 0;
			$asteroids_count = 0;
			$wormhole_count = 0;
	
			echo "Erweitere Universum von $sx_num x $sy_num Sektoren auf $sx_num_new x $sy_num_new Sektoren. Es werden ".$cx_num*$cy_num." Zellen pro Sektor und ".($sx_num_new*$sy_num_new-$sx_num*$sy_num)*$cx_num*$cy_num." Zellen total hinzugefügt<br>";
	
			for ($sx=1;$sx<=$sx_num_new;$sx++)
			{
				for ($sy=1;$sy<=$sy_num_new;$sy++)
				{
					//überprüft ob dieser sektor schon vorhanden ist
	                $res = dbquery("
						SELECT
							cell_id
						FROM
							".$db_table['space_cells']."
						WHERE
							cell_sx='".$sx."'
							AND cell_sy='".$sy."';
					");
	                if (mysql_num_rows($res)==0)
	                {
	                    for ($cx=1;$cx<=$cx_num;$cx++)
	                    {
	                        for ($cy=1;$cy<=$cy_num;$cy++)
	                        {
	                            $ct = mt_rand(1,100);
	                            //Sonnensystem
	                            if ($ct<=$perc_solsys)
	                            {
	                                $st = mt_rand(1,$num_sol_types);
	                                $np = mt_rand($num_planets_min,$num_planets_max);
	                                $sql = "
										INSERT INTO
										".$db_table['space_cells']."
										(
											cell_sx,
											cell_sy,
											cell_cx,
											cell_cy,
											cell_type,
											cell_solsys_num_planets,
											cell_solsys_solsys_sol_type
										)
										VALUES
										(
											'".$sx."',
											'".$sy."',
											'".$cx."',
											'".$cy."',
											'1',
											'".$np."',
											'".$st."'
										);
									";
	                                dbquery($sql);  // Zelle speichern
	                                $solsys_id = mysql_insert_id();
	                                for ($cnp=1;$cnp<=$np;$cnp++)
	                                {
	                                    $pt = mt_rand(1,$num_planet_types);
	                                    $img_nr = $pt."_".mt_rand(1,$num_planet_images);
	                                    $fields = mt_rand($planet_fields_min,$planet_fields_max);
	                                    $tblock =  round($planet_temp_totaldiff / $np);
	                                    $temp = mt_rand($planet_temp_max-($tblock*$cnp),($planet_temp_max-($tblock*$cnp)+$tblock));
	                                    $tmin = $temp - $planet_temp_diff;
	                                    $tmax = $temp + $planet_temp_diff;
	                                    $sql = "
											INSERT INTO
											".$db_table['planets']."
											(
												planet_solsys_id,
												planet_solsys_pos,
												planet_type_id,
												planet_fields,
												planet_image,
												planet_temp_from,
												planet_temp_to
											)
											VALUES
											(
												'".$solsys_id."',
												'".$cnp."',
												'".$pt."',
												'".$fields."',
												'".$img_nr."',
												'".$tmin."',
												'".$tmax."'
											)
										";
	                                    dbquery($sql);  // Planet speichern
	                                    $planet_count++;
	                                }
	                                $sol_count++;
	                            }
	                            //Asteroidenfeld
	                            elseif ($ct<=$perc_solsys + $perc_asteroids)
	                            {
	                                $asteroid_ress = mt_rand($conf['asteroid_ress']['p1'],$conf['asteroid_ress']['p2']);
	                                $sql = "
										INSERT INTO
										".$db_table['space_cells']."
										(
											cell_sx,
											cell_sy,
											cell_cx,
											cell_cy,
											cell_type,
											cell_asteroid,
											cell_asteroid_ress
										)
										VALUES
										(
											'".$sx."',
											'".$sy."',
											'".$cx."',
											'".$cy."',
											'1',
											'1',
											'".$asteroid_ress."'
										);
									";
	                                dbquery($sql);  // Zelle speichern
	                                $asteroids_count++;
	                            }
	                            //Intergalaktischer Nebel
	                            elseif ($ct<=$perc_solsys + $perc_asteroids + $perc_nebulas)
	                            {
	                                $nebula_ress = mt_rand($conf['nebula_ress']['p1'],$conf['nebula_ress']['p2']);
	
	                                $sql = "
										INSERT INTO
										".$db_table['space_cells']."
										(
											cell_sx,
											cell_sy,
											cell_cx,
											cell_cy,
											cell_type,
											cell_nebula,
											cell_nebula_ress
										)
										VALUES
										(
											'".$sx."',
											'".$sy."',
											'".$cx."',
											'".$cy."',
											'1',
											'1',
											'".$nebula_ress."'
										);
									";
	                                dbquery($sql);  // Zelle speichern
	                                $nebula_count++;
	                            }
	                            //Wurmlöcher
	                            elseif ($ct<=$perc_solsys + $perc_asteroids + $perc_nebulas + $perc_wormholes)
	                            {
	                                // echo "$sx/$sy : $cx/$cy &nbsp;-&nbsp; Wurmloch<br>";
	                                $sql = "
										INSERT INTO
										".$db_table['space_cells']."
										(
											cell_sx,
											cell_sy,
											cell_cx,
											cell_cy,
											cell_type,
											cell_wormhole_id
										)
										VALUES
										(
											'".$sx."',
											'".$sy."',
											'".$cx."',
											'".$cy."',
											'1',
											'1'
										);
									";
	                                dbquery($sql);  // Zelle speichern
	                                $wormhole_count++;
	                            }
	                            //Leere Zellen
	                            else
	                            {
	                                $sql = "
										INSERT INTO
										".$db_table['space_cells']."
										(
											cell_sx,
											cell_sy,
											cell_cx,
											cell_cy,
											cell_type
										)
										VALUES
										(
											'".$sx."',
											'".$sy."',
											'".$cx."',
											'".$cy."',
											'0'
										);
									";
	                                dbquery($sql);  // Zelle speichern
	                            }
	                        }
	                    }
					}
				}
			}
	
			$wh = array();
			$wh_new = array();
			$res = dbquery("
	        SELECT
	            *
	        FROM
	            ".$db_table['space_cells']."
	        WHERE
	            cell_wormhole_id!='0';
			");
			if (fmod(mysql_num_rows($res),2)!=0) //wenn Zahl ungerade ist
			{
				echo "<br>Eins ist zuviel!<br>";
				dbquery("
	            UPDATE
	            	".$db_table['space_cells']."
	            SET
	            	cell_wormhole_id='0'
	            WHERE
	            	cell_wormhole_id!='0'
	            LIMIT 1;
				");
				echo mysql_error();
				$res = dbquery("
	            SELECT
	            	*
	            FROM
	            	".$db_table['space_cells']."
	            WHERE
	            	cell_wormhole_id!='0';
				");
			}
			while ($arr=mysql_fetch_assoc($res))
			{
				array_push($wh,$arr['cell_id']);
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
	            	".$db_table['space_cells']."
	            SET
	            	cell_wormhole_id='".$k."'
	            WHERE
	            	cell_id='".$v."';
				");
				echo mysql_error();
				dbquery("
	            UPDATE
	            	".$db_table['space_cells']."
	            SET
	            	cell_wormhole_id='".$v."'
	            WHERE
	            	cell_id='".$k."';
				");
							echo mysql_error();
			}
	
			//Neue Sektorenanzahl in der Config speichern
			dbquery("
			UPDATE
				".$db_table['config']."
			SET
	            config_param1='".$sx_num_new."',
	            config_param2='".$sy_num_new."'
			WHERE
				config_name='num_of_sectors';");
	
			echo "Universum erweitert:<br>$sol_count Sonnensysteme mit $planet_count Planeten, $asteroids_count Asteroidenfelder, $nebula_count Nebel und $wormhole_count Wurmlöcher!";
	
		}


	
	}


?>
