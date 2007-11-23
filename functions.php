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
	// 	File: functions.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//
	/**
	* Main function file
	*
	* @package etoa_gameserver
	* @author MrCage mrcage@etoa.ch
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/

	/**
	* Loads missing classes
	*
	* @class_name Name of missing class
	*/
	function __autoload($class_name) 
	{
		if ($class_name != "xajax")
		{
	    if (!@include_once('classes/'.strtolower($class_name).'.class.php'))
	    {
	    	die('Class '.$class_name.' not found!');
	    }
	  }
	}

	/**
	* Baut die Datenbankverbindung auf
	*/
	function dbconnect()
	{
		global $db_handle;
		global $nohtml;
		global $query_counter;
		$query_counter=0;
		if (!$db_handle = mysql_connect(DB_SERVER,DB_USER,DB_PASSWORD))
		{
			if (!$nohtml)
			{
				echo "</head><body>";
				print_fs_error_msg("Zum Datenbankserver auf <b>".DB_SERVER."</b> kann keine Verbindung hergestellt werden! 
				Bitte schaue später nochmals vorbei.<br/><br/>
				<a href=\"http://forum.etoa.ch\">Zum Forum</a> | 
				<a href=\"mailto:mail@etoa.ch\">Mail an die Spielleitung</a>","MySQL-Verbindungsproblem");
			}
			else
			{
				echo "Zum Datenbankserver auf <b>".DB_SERVER."</b> kann keine Verbindung hergestellt 
				werden!";
			}
		}
		if (!mysql_select_db(DB_DATABASE))
		{
			
			if (!$nohtml)
			{
				echo "</head><body>";
				print_fs_error_msg("Auf die Datenbank <b>".DB_DATABASE."</b> auf <b>".DB_SERVER."</b> kann 
				nicht zugegriffen werden! Bitte schaue später nochmals vorbei.<br/><br/>
				<a href=\"http://forum.etoa.ch\">Zum Forum</a> | 
				<a href=\"mailto:mail@etoa.ch\">Mail an die Spielleitung</a>","MySQL-Verbindungsproblem");
			}
			else
			{
				echo "Auf die Datenbank <b>".DB_SERVER."</b> auf <b>".DB_SERVER."</b> kann nicht zugegriffen werden!";
			}
		}
		dbquery("SET NAMES 'utf8';"); 
	}

	/**
	* Trennt die Datenbankverbindung
	*/
	function dbclose()
	{
		global $db_handle;
		global $res;
		global $query_counter;
		//echo "Queries done: ".$query_counter;
		if (isset($res))
		{
			@mysql_free_result($res);
		}
		@mysql_close($db_handle);
	}

	/**
	* Führt eine Datenbankabfrage aus
	*
	* @param string $string SQL-Abfrage
	* #param int $fehler Erzwing Fehleranzeige, Standard: 1
	*/
	function dbquery($string, $fehler=1)
	{
		global $db_handle;
		global $nohtml;
		global $query_counter;
		$query_counter++;
		if ($result=mysql_query($string))
			return $result;
		elseif ($fehler==1)
		{
			if ($nohtml)
				echo "Bei Ihrer Datenbankabfrage trat ein Fehler auf!\n\n[b]MySQL-Meldung:[/b] [i]".mysql_error()."[/i]\n\n[b]Originalabfrage:[/b] ".$string;
			else
				errBox("Datenbankfehler","Bei Ihrer Datenbankabfrage trat ein Fehler auf!\n\n[b]MySQL-Meldung:[/b] [i]".mysql_error()."[/i]\n\n[b]Originalabfrage:[/b] ".$string);
		}
	}

	/**
	* Gesamte Config-Tabelle lesen und Werte in Array speichern
	*/
	function get_all_config()
	{
		global $db_table;
		$conf = array();
		$res = dbquery("
		SELECT
	    config_name,
	    config_value,
	    config_param1,
	    config_param2
		FROM
			config;");
		while ($arr = mysql_fetch_array($res))
		{
			$conf[$arr['config_name']]['v'] = $arr['config_value'];
			$conf[$arr['config_name']]['p1'] = $arr['config_param1'];
			$conf[$arr['config_name']]['p2'] = $arr['config_param2'];
		}
		return $conf;
	}

	/**
	* Sonnentyp-Name in Array speichern
	*/
	function get_sol_types_array()
	{
		global $db_table;
		$sol_types = array();
		$res = dbquery("
		SELECT
	    type_id,
	    type_name
		FROM
			".$db_table['sol_types'].";");
		while ($arr = mysql_fetch_array($res))
		{
			$sol_types[$arr['type_id']] = $arr['type_name'];
		}
		return $sol_types;
	}

	/**
	* Planetentyp-Name in Array speichern
	*/
	function get_planet_types_array()
	{
		global $db_table;
		$planet_types = array();
		$res = dbquery("
		SELECT
	    type_id,
	    type_name
		FROM
			".$db_table['planet_types'].";");
		while ($arr = mysql_fetch_array($res))
		{
			$planet_types[$arr['type_id']] = $arr['type_name'];
		}
		return $planet_types;
	}

	/**
	* Planetentyp-Daten in Array speichern
	*/
	function get_planet_types_data()
	{
		global $db_table;
		$planet_types = array();
		$res = dbquery("
		SELECT
			*
		FROM
			".$db_table['planet_types'].";");
		while ($arr = mysql_fetch_array($res))
		{
			$planet_types[$arr['type_id']] = $arr;
		}
		return $planet_types;
	}

	/**
	* Sonnentyp-Daten in Array speichern
	*/
	function get_sol_types_data()
	{
		global $db_table;
		$planet_types = array();
		$res = dbquery("
		SELECT
			*
		FROM
			".$db_table['sol_types'].";");
		while ($arr = mysql_fetch_array($res))
		{
			$sol_types[$arr['type_id']] = $arr;
		}
		return $sol_types;
	}

	/**
	* Rassen-Daten in Array speichern
	*/
	function get_races_array()
	{
		global $db_table;
		$race_name = array();
		$res = dbquery("
		SELECT
			*
		FROM
			".$db_table['races']."
		ORDER BY
			race_name;");
		while ($arr = mysql_fetch_array($res))
		{
			$race_name[$arr['race_id']] = $arr;
		}
		return $race_name;
	}

	/**
	* Allianz Name in Array speichern
	*/
	function get_alliance_names()
	{
		global $db_table;
		$names = array();

		$res = dbquery("
		SELECT
      alliance_tag,
      alliance_id,
      alliance_name,
      alliance_founder_id
		FROM
			".$db_table['alliances']."
		ORDER BY
			alliance_name;");
		while ($arr = mysql_fetch_array($res))
		{
			$names[$arr['alliance_id']]['tag'] = $arr['alliance_tag'];
			$names[$arr['alliance_id']]['name'] = $arr['alliance_name'];
			$names[$arr['alliance_id']]['founder_id'] = $arr['alliance_founder_id'];
		}
		return $names;
	}

	/**
	* Allianz Name in Array speichern, aber ohne eigene Allianz
	*/
	function get_alliance_names1($id)
	{
		global $db_table;
		$names = array();
		$res = dbquery("
			SELECT
				alliance_tag,
				alliance_id,
				alliance_name,
				alliance_founder_id
			FROM
				".$db_table['alliances']."
			WHERE
				alliance_id!='".$id."'
			ORDER BY
				alliance_name;
		");
		echo $_SESSION[ROUNDID]['user']['alliance_id'];
		while ($arr = mysql_fetch_array($res))
		{
			$names[$arr['alliance_id']]['tag'] = $arr['alliance_tag'];
			$names[$arr['alliance_id']]['name'] = $arr['alliance_name'];
			$names[$arr['alliance_id']]['founder_id'] = $arr['alliance_founder_id'];
		}
		return $names;
	}

	/**
	* Ressourcen-Namen in Array speichern
	*/
	function get_resources_array()
	{
		global $db_table;
		$resource_name = array();
		$res = dbquery("
		SELECT
            resource_type,
            resource_name
		FROM
			".$db_table['resources'].";");
		while ($arr = mysql_fetch_array($res))
		{
			$resource_name[$arr['resource_type']] = $arr['resource_name'];
		}
		return $resource_name;
	}

	/**
	* Ressourcen-Daten in Array speichern
	*/
	function get_resources_data()
	{
		global $db_table;
		$resource_name = array();
		$res = dbquery("
		SELECT
			*
		FROM
			".$db_table['resources'].";");
		while ($arr = mysql_fetch_array($res))
		{
			$resources[$arr['resource_type']] = $arr;
		}
		return $resources;
	}

	/**
	* User-Nick via User-Id auslesen
	*/
	function get_user_nick($id)
	{
		global $db_table;
		$res = dbquery("
			SELECT
				user_nick
			FROM
				".$db_table['users']."
			WHERE
				user_id='".$id."';
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			return $arr['user_nick'];
		}
		else
		{
			return "<i>Unbekannter Benutzer</i>";
		}
	}

	/**
	* Allianz-Daten via User-Id auslesen
	*
	* @param int $id User ID
	*/
	function get_user_alliance($id)
	{
		global $db_table;
		$res = dbquery("
		SELECT
			a.alliance_name,
			a.alliance_id,
			a.alliance_tag
		FROM
			".$db_table['users']." AS u
			INNER JOIN ".$db_table['alliances']." AS a
			ON u.user_alliance_id = a.alliance_id
			AND u.user_id='".$id."';
		");
		if (mysql_num_rows($res)>0)
		{
			return mysql_fetch_array($res);
		}
		else
		{
			return "";
		}
	}

	/**
	* Returns the alliance id of a given alliance name
	*
	* @param int $name Alliance Name
	*/
	function get_alliance_id_by_name($name)
	{
		global $db_table;
		$res = dbquery("
			SELECT
				alliance_id
			FROM
				".$db_table['alliances']."
			WHERE
				alliance_name='".$name."';
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			return $arr['alliance_id'];
		}
		else
		{
			return 0;
		}
	}


	/**
	* Returns the alliance id of a given alliance tag
	*
	* @param int $tag Alliance tag
	*/
	function get_alliance_id($tag)
	{
		global $db_table;
		$res = dbquery("
			SELECT
				alliance_id
			FROM
				".$db_table['alliances']."
			WHERE
				alliance_tag='".$tag."';
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			return $arr['alliance_id'];
		}
		else
		{
			return 0;
		}
	}

	/**
	* User-Id via Nick auslesen
	*
	* @param char $nick User-nick
	*/
	function get_user_id($nick)
	{
		global $db_table;
		$res = dbquery("
			SELECT
				user_id
			FROM
				".$db_table['users']."
			WHERE
				user_nick='".$nick."';
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			return $arr['user_id'];
		}
		else
		{
			return 0;
		}
	}

	/**
	* User-Id via Planeten-Id auslesen
	*
	* @param int $pid Planet-ID
	*/
	function get_user_id_by_planet($pid)
	{
		global $db_table;
		$res = dbquery("
			SELECT
				planet_user_id
			FROM
				".$db_table['planets']."
			WHERE
				planet_id='".$pid."';
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			return $arr['planet_user_id'];
		}
		else
		{
			return 0;
		}

	}

	function get_user_nick_by_planet($pid)
	{
		global $db_table;
		$res = dbquery("
			SELECT
				u.user_nick
			FROM
				".$db_table['users']." AS u
				INNER JOIN ".$db_table['planets']." AS p
				ON u.user_id = p.planet_user_id
				AND p.planet_id='".$pid."';
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			return $arr['user_nick'];
		}
		else
		{
			return 0;
		}

	}


	//
	//Universum erstellen
	//

	function create_universe()
	{
		global $conf,$db_table;

		echo "Universum existiert noch nicht!<br>";
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

		echo "Erstelle ".$sx_num*$sy_num." Sektoren à ".$cx_num*$cy_num." Zellen, d.h. ".$sx_num*$sy_num*$cx_num*$cy_num." Zellen total<br>";

		for ($sx=1;$sx<=$sx_num;$sx++)
		{
			for ($sy=1;$sy<=$sy_num;$sy++)
			{
				for ($cx=1;$cx<=$cx_num;$cx++)
				{
					for ($cy=1;$cy<=$cy_num;$cy++)
					{
						$ct = mt_rand(1,100);
						if ($ct<=$perc_solsys)
						{
							$st = mt_rand(1,$num_sol_types);
							$np = mt_rand($num_planets_min,$num_planets_max);
							$sql = "
								INSERT INTO ".$db_table['space_cells']." (
									cell_sx,
									cell_sy,
									cell_cx,
									cell_cy,
									cell_type,
									cell_solsys_num_planets,
									cell_solsys_solsys_sol_type)
								VALUES (
									'".$sx."',
									'".$sy."',
									'".$cx."',
									'".$cy."',
									'1',
									'".$np."',
									'".$st."');
							";
							dbquery($sql);	// Zelle speichern
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
									)";
								dbquery($sql);	// Planet speichern
								$planet_count++;
							}
							$sol_count++;
						}
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
							dbquery($sql);	// Zelle speichern
							$asteroids_count++;
						}
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
							dbquery($sql);	// Zelle speichern
							$nebula_count++;
						}
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
							dbquery($sql);	// Zelle speichern
							$wormhole_count++;
						}
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
							dbquery($sql);	// Zelle speichern
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
		while ($arr=mysql_fetch_array($res))
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

		echo "Universum erstellt!<br> $sol_count Sonnensysteme mit $planet_count Planeten, $asteroids_count Asteroidenfelder, $nebula_count Nebel und $wormhole_count Wurmlöcher!";

	}



	/**
	* Erweitert Universum
	*
	* @param string $sx_num_new,$sy_num_new
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
		while ($arr=mysql_fetch_array($res))
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

	//
	// Seitenanzeige überprüfen
	//

	function checkPage($id,$comp,$page,$op)
	{
		// Diese Funktion überprüft, ob eine gegebene Variable (id) gleich einem Vergleichswert (comp) ist, wenn ja wird die gegebene Seite (page) aufgerufen
		if ($op==1)
		{
			if ($id==$comp)
			{
				die ("<script>document.location='?page=$page'</script>");
			}
		}
		elseif ($op==0)
		{
			if ($id!=$comp)
			{
				die ("<script>document.location='?page=$page'</script>");
			}
		}
	}

	//
	// Zahlen formatieren
	//

	function nf($number,$colorize=0)	// Number format
	{
		if ($colorize==1)
		{
			if ($number>0)
				return "<span style=\"color:#0f0\">".number_format($number,0,",","`")."</span>";
			if ($number<0)
				return "<span style=\"color:#f00\">".number_format($number,0,",","`")."</span>";
		}
		return number_format($number,0,",","`");
	}


	//
	// Zahlen deformatieren
	//

	function nf_back($number)	// Number format
	{
		$number = str_replace('`', '', $number);
		$number = abs(intval($number));
		return $number;
		
	}


	function nf2($number,$colorize=0)	// Number format
	{
		if ($colorize==1)
		{
			if ($number>0)
				return "<span style=\"color:#0f0\">".number_format($number,0,",",".")."</span>";
			if ($number<0)
				return "<span style=\"color:#f00\">".number_format($number,0,",",".")."</span>";
		}
		return number_format($number,0,",",".");
	}
	
	

	//
	// Zeit formatieren
	//

	function tf($ts)	// Time format
	{
		$t = floor($ts / 3600 / 24);
		$h = floor(($ts-($t*3600*24)) / 3600);
		$m = floor(($ts-($t*3600*24)-($h*3600))/60);
		$s = floor(($ts-($t*3600*24)-($h*3600)-($m*60)));

		if ($t>0)
			return $t."d ".$h."h ".$m."m ".$s."s";
		return $h."h ".$m."m ".$s."s";
	}

	//
	// Ressourcen eines Planeten berechnen
	//

	function calculate_ress_on_planet($planet_id)
	{
		global $db_table,$conf;

		$rp = get_planet_types_data();
		$rs = get_sol_types_data();
		$race = get_races_array();

		$arr = mysql_fetch_array(dbquery("
			SELECT
				p.planet_type_id,
				p.planet_people,
				p.planet_res_metal,
				p.planet_res_crystal,
				p.planet_res_plastic,
				p.planet_res_fuel,
				p.planet_res_food,
				p.planet_last_updated,
				p.planet_prod_metal,
				p.planet_prod_plastic,
				p.planet_prod_crystal,
				p.planet_prod_fuel,
				p.planet_prod_food,
				p.planet_store_metal,
				p.planet_store_crystal,
				p.planet_store_plastic,
				p.planet_store_fuel,
				p.planet_store_food,
				p.planet_people_place,
				u.user_race_id
			FROM
				".$db_table['planets']." AS p
				INNER JOIN ".$db_table['users']." AS u
				ON p.planet_user_id = u.user_id
				AND p.planet_id='".$planet_id."';")
		);

		if ($arr['planet_last_updated']==0)
		{
			$arr['planet_last_updated']=time();
		}
		$p_metal=$arr['planet_res_metal'];
		$p_crystal=$arr['planet_res_crystal'];
		$p_plastic=$arr['planet_res_plastic'];
		$p_fuel=$arr['planet_res_fuel'];
		$p_food=$arr['planet_res_food'];
		$people=$arr['planet_people'];

		if ($people<1)$people=1;

		if ($p_metal<0) $p_metal=0;
		if ($p_crystal<0) $p_crystal=0;
		if ($p_plastic<0) $p_plastic=0;
		if ($p_fuel<0) $p_fuel=0;
		if ($p_food<0) $p_food=0;

		$t = time()-$arr['planet_last_updated'];

		if ($arr['planet_store_metal']>$p_metal+($arr['planet_prod_metal']/3600*$t))
			$p_metal+=$arr['planet_prod_metal']/3600*$t;
		elseif ($arr['planet_store_metal']>$p_metal)
			$p_metal=$arr['planet_store_metal'];

		if ($arr['planet_store_crystal']>$p_crystal+($arr['planet_prod_crystal']/3600*$t))
			$p_crystal+=$arr['planet_prod_crystal']/3600*$t;
		elseif ($arr['planet_store_crystal']>$p_crystal)
			$p_crystal=$arr['planet_store_crystal'];

		if ($arr['planet_store_plastic']>$p_plastic+($arr['planet_prod_plastic']/3600*$t))
			$p_plastic+=$arr['planet_prod_plastic']/3600*$t;
		elseif ($arr['planet_store_plastic']>$p_plastic)
			$p_plastic=$arr['planet_store_plastic'];

		if ($arr['planet_store_fuel']>$p_fuel+($arr['planet_prod_fuel']/3600*$t))
			$p_fuel+=$arr['planet_prod_fuel']/3600*$t;
		elseif ($arr['planet_store_fuel']>$p_fuel)
			$p_fuel=$arr['planet_store_fuel'];

		if ($arr['planet_store_food']>$p_food+($arr['planet_prod_food']/3600*$t))
			$p_food+=$arr['planet_prod_food']/3600*$t;
		elseif ($arr['planet_store_food']>$p_food)
			$p_food=$arr['planet_store_food'];

		if ($arr['planet_store_food']>$p_food+($arr['planet_prod_food']/3600*$t))
			$p_food+=$arr['planet_prod_food']/3600*$t;
		elseif ($arr['planet_store_food']>$p_food)
			$p_food=$arr['planet_store_food'];

		$people_births = $people * ($conf['people_multiply']['v']-1);

		$people_births = $people * $conf['people_multiply']['v'] - $people;
		$people_births= $people_births * ($rp[$arr['planet_type_id']]['type_f_population']) * ($race[$arr['user_race_id']]['race_f_population']) * ($rs[$arr['planet_type_id']]['type_f_population']);
		if($people_births<=1)
			$people_births=1;

		if ($arr['planet_people_place']>$people+($people_births/3600*$t))
			$people+=$people_births/3600*$t;
		elseif ($arr['planet_people_place']>$people)
			$people=$arr['planet_people_place'];

		dbquery("
		UPDATE
			".$db_table['planets']."
        SET
            planet_res_metal='".$p_metal."',
            planet_res_crystal='".$p_crystal."',
            planet_res_plastic='".$p_plastic."',
            planet_res_fuel='".$p_fuel."',
            planet_res_food='".$p_food."',
            planet_people='".$people."',
            planet_last_updated=''".time()."''
        WHERE
            planet_id='".$planet_id."';
		");
	}

	function reset_universe()
	{
		global $db_table;

		$tbl[1]="alliances";
		$tbl[2]="alliance_bnd";
		$tbl[3]="alliance_history";
		$tbl[4]="alliance_news";
		$tbl[5]="alliance_ranks";
		$tbl[6]="buddylist";
		$tbl[7]="buildlist";
		$tbl[8]="deflist";
		$tbl[9]="fleet";
		$tbl[10]="fleet_ships";
		$tbl[11]="logs";
		$tbl[12]="market_auction";
		$tbl[13]="market_ressource";
		$tbl[14]="market_ship";
		$tbl[15]="messages";
		$tbl[16]="notepad";
		$tbl[17]="planets";
		$tbl[18]="shiplist";
		$tbl[19]="space_cells";
		$tbl[20]="target_bookmarks";
		$tbl[21]="techlist";
		$tbl[22]="users";
		$tbl[23]="user_history";
		$tbl[24]="user_multi";
		$tbl[25]="user_log";
		$tbl[26]="user_points";
		$tbl[27]="user_requests";
		$tbl[28]="user_sitting";
		$tbl[29]="user_sitting_date";
		$tbl[30]="target_bookmarks";
		$tbl[31]="buddylist";
		
		$tbl[32]="message_ignore";
		$tbl[33]="allianceboard_catranks";
		$tbl[34]="allianceboard_topics";
		$tbl[35]="def_queue";
		$tbl[36]="ship_queue";
		$tbl[37]="alliance_poll_votes";
		$tbl[38]="allianceboard_cat";
		$tbl[39]="login_failures";
		$tbl[40]="allianceboard_posts";
		$tbl[41]="missilelist";
		$tbl[42]="alliance_rankrights";
		$tbl[43]="admin_user_log";
		$tbl[44]="alliance_stats";
		$tbl[45]="user_stats";
		$tbl[46]="user_onlinestats";
		$tbl[47]="logs_game";
		
		$tbl[47]="alliance_shoutbox";
		$tbl[47]="alliance_polls";
		$tbl[47]="logs_battle";

		foreach ($tbl as $t)
		{
			dbquery("TRUNCATE $t;");
			echo "Leere Tabelle <b>$t</b><br/>";
		}
		return true;
	}


	//
	// Ressourcen eines Planeten auslesen
	//

	function get_ress_on_planet($planet_id)
	{
		global $db_table;
		$res =dbquery("
        SELECT
            planet_res_metal,
            planet_res_crystal,
            planet_res_plastic,
            planet_res_fuel,
            planet_res_food
        FROM
            ".$db_table['planets']."
        WHERE
            planet_id='".$planet_id."';");
		$arr = mysql_fetch_array($res);

		$p['metal']=$arr['planet_res_metal'];
		$p['crystal']=$arr['planet_res_crystal'];
		$p['plastic']=$arr['planet_res_plastic'];
		$p['fuel']=$arr['planet_res_fuel'];
		$p['food']=$arr['planet_res_food'];

		return $p;
	}

	//
	// Treibstoff vom Ressourcen-Konto abbuchen
	//

	function subst_fuel($planet_id,$fuel_amount)
	{
		global $db_table;
		dbquery("
			UPDATE
				".$db_table['planets']."
			SET
				planet_res_fuel=planet_res_fuel-".$fuel_amount."
			WHERE
				planet_id='".$planet_id."';
		");
	}

	function subst_food($planet_id,$food_amount)
	{
		global $db_table;
		dbquery("
			UPDATE
				".$db_table['planets']."
			SET
				planet_res_food=planet_res_food-".$food_amount."
			WHERE
				planet_id='".$planet_id."';
		");
	}


	//
	// Koordinaten formatieren
	//

	function coords_format1($cell_id,$planet_id)
	{
		global $db_table;
		$res = dbquery("
			SELECT
				c.cell_sx,
				c.cell_sy,
				c.cell_cx,
				c.cell_cy,
				p.planet_solsys_pos,
				p.planet_name
			FROM
				".$db_table['planets']." AS p
				INNER JOIN ".$db_table['space_cells']." AS c
				ON p.planet_solsys_id = c.cell_id
				AND p.planet_id='".$planet_id."';
		");
		$arr = mysql_fetch_array($res);

		$coords = $arr['cell_sx']."/".$arr['cell_sy']." : ".$arr['cell_cx']."/".$arr['cell_cy']." : ".$arr['planet_solsys_pos'];
		if ($arr['planet_name']!="")
			return $arr['planet_name']." ($coords)";
		else
			return $coords;
	}

	//
	// Koordinaten formatieren
	//

	function coords_format2($planet_id,$link=0,$action_color=0)
	{
		global $db_table;
		$res = dbquery("
			SELECT
				c.cell_id,
				c.cell_sx,
				c.cell_sy,
				c.cell_cx,
				c.cell_cy,
				p.planet_solsys_pos,
				p.planet_name
			FROM
				".$db_table['planets']." AS p
				INNER JOIN ".$db_table['space_cells']." AS c
				ON p.planet_solsys_id = c.cell_id
				AND p.planet_id='".$planet_id."';
		");
		$arr = mysql_fetch_array($res);
		$coords = $arr['cell_sx']."/".$arr['cell_sy']." : ".$arr['cell_cx']."/".$arr['cell_cy']." : ".$arr['planet_solsys_pos'];
		if ($action_color==1) $col="#0f0";
		if ($action_color==2) $col="#f00";

		if ($arr['planet_name']!="")
		{
			if ($link==1)
				return "<a style=\"color:$col;\" href=\"?page=solsys&id=".$arr['cell_id']."\">".$arr['planet_name']." ($coords)</a>";
			else
				return $arr['planet_name']." ($coords)";
		}
		else
		{
			if ($link==1)
				return "<a style=\"color:$col;\" href=\"?page=solsys&id=".$arr['cell_id']."\">$coords</a>";
			else
				return $coords;
		}
	}


	//
	// Koordinaten formatieren
	//

	function coords_format3($planet_id,$link=0,$action_color=0)
	{
		global $db_table;
		$res = dbquery("
			SELECT
				c.cell_id,
				c.cell_sx,
				c.cell_sy,
				c.cell_cx,
				c.cell_cy,
				p.planet_solsys_pos,
				p.planet_name
			FROM
				".$db_table['planets']." AS p
				INNER JOIN ".$db_table['space_cells']." AS c
				ON p.planet_solsys_id = c.cell_id
				AND p.planet_id='".$planet_id."';
		");
		$arr = mysql_fetch_array($res);
		$coords = $arr['cell_sx']."/".$arr['cell_sy']." : ".$arr['cell_cx']."/".$arr['cell_cy']." : ".$arr['planet_solsys_pos'];
		if ($action_color==1) $col="#0f0";
		if ($action_color==2) $col="#f00";

			return $coords;
	}

	//
	// Koordinaten formatieren
	//

	function coords_format4($cell_id)
	{
		global $db_table;
		$res = dbquery("
			SELECT
				cell_sx,
				cell_sy,
				cell_cx,
				cell_cy,
				cell_asteroid,
				cell_nebula
			FROM
				".$db_table['space_cells']."
			WHERE
				cell_id='".$cell_id."';
		");
		$arr = mysql_fetch_array($res);
		$coords = $arr['cell_sx']."/".$arr['cell_sy']." : ".$arr['cell_cx']."/".$arr['cell_cy'];
		if ($arr['cell_asteroid']==1)
			return "<a href=\"?page=space&cell_id=$cell_id\">Asteoridenfeld ($coords)</a>";
		elseif ($arr['cell_nebula']==1)
			return "<a href=\"?page=space&cell_id=$cell_id\">Nebelfeld ($coords)</a>";
		else
			return $coords;
	}


	function format_link($string)
	{
		$string = eregi_replace("([ \n])http://([^ ,\n]*)", "\\1[url]http://\\2[/url]", $string);
		$string = eregi_replace("([ \n])ftp://([^ ,\n]*)", "\\1[url]ftp://\\2[/url]", $string);
		$string = eregi_replace("([ \n])www\\.([^ ,\n]*)", "\\1[url]http://www.\\2[/url]", $string);
		$string = eregi_replace("^http://([^ ,\n]*)", "[url]http://\\1[/url]", $string);
		$string = eregi_replace("^ftp://([^ ,\n]*)", "[url]ftp://\\1[/url]", $string);
		$string = eregi_replace("^www\\.([^ ,\n]*)", "[url]http://www.\\1[/url]", $string);
	 	$string = eregi_replace('\[url\]www.([^\[]*)\[/url\]', '<a href="http://www.\1">\1</a>', $string);
		$string = eregi_replace('\[url\]([^\[]*)\[/url\]', '<a href="\1">\1</a>', $string);
		$string = eregi_replace('\[mailurl\]([^\[]*)\[/mailurl\]', '<a href="\1">Link</a>', $string);
		return $string;
	}

	//
	// Überprüft ob unerlaubte Zeichen im Text sind und gibt Antwort zurück
	//
	function check_illegal_signs($string)
	{
			if (
				!stristr($string,"'")
                && !stristr($string,"<")
                && !stristr($string,">")
                && !stristr($string,"?")
                && !stristr($string,"\"")
                && !stristr($string,"$")
                && !stristr($string,"!")
                && !stristr($string,"=")
                && !stristr($string,";")
                && !stristr($string,"&")
            )
           	{
           		return "";
           	}
           	else
           	{
           		return "&lt; &gt; ' \" ? ! $ = ; &amp;";
           	}
	}

	//
	// Überprüft ob unerlaubte Zeichen im Text sind und gibt Antwort zurück
	//
	function remove_illegal_signs($string)
	{
		$string = str_replace("'","",$string);
		$string = str_replace("<","",$string);
		$string = str_replace(">","",$string);
		$string = str_replace("?","",$string);
		$string = str_replace("\\","",$string);
		$string = str_replace("$","",$string);
		$string = str_replace("!","",$string);
		$string = str_replace("=","",$string);
		$string = str_replace(";","",$string);
		$string = str_replace("&","",$string);
		return $string;
	}



	function send_msg($user_id,$msg_type,$subject,$text)
	{
		global $db_table;
		dbquery("
			INSERT INTO
			".$db_table['messages']."
			(
				message_user_from,
				message_user_to,
				message_timestamp,
				message_cat_id,
				message_subject,
				message_text
			)
			VALUES
			(
				'0',
				'".$user_id."',
				'".time()."',
				'".$msg_type."',
				'".addslashes($subject)."',
				'".addslashes($text)."'
			);
		");
	}

	function send_msg_hd($user_id,$msg_type,$subject,$text,$user_from)
	{
		global $db_table;
		dbquery("
			INSERT INTO
			".$db_table['messages']."
			(
				message_user_from,
				message_user_to,
				message_timestamp,
				message_cat_id,
				message_subject,
				message_text
			)
			VALUES
			(
				'".$user_from."',
				'".$user_id."',
				'".time()."',
				'".$msg_type."',
				'".addslashes($subject)."',
				'".addslashes($text)."'
			);
		");
	}

    function endpage()
    {
        echo "</body></html>";
        exit();
    }



	function get_sector_of_main_planet ($user_id,$link=0)
	{
		global $db_table;
		$res = dbquery("
		SELECT
            c.cell_sx,
            c.cell_sy
		FROM
            ".$db_table['space_cells']." AS c
            INNER JOIN
            ".$db_table['planets']." AS p
            ON p.planet_solsys_id=c.cell_id
 			AND p.planet_user_id='".$user_id ."'
			AND p.planet_user_main='1';");
		$arr = mysql_fetch_array($res);
		$coords = $arr['cell_sx']."/".$arr['cell_sy'];
		if ($coords=="/")
			return "";
		elseif ($link==1)
			return "<a href=\"?page=space&sx=".$arr['cell_sx']."&sy=".$arr['cell_sy']."\">$coords</a>";
		else
			return $coords;
	}

	/**
	* Speichert Daten in die Log-Tabelle
	*
	* @param int $log_cat Log Kategorie
	* @param string $log_text Log text
	* @param int $log_timestamp Zeit
	* @author MrCage
	*/
	function add_log($log_cat,$log_text,$log_timestamp=0)
	{
		global $db_table;
		if ($log_timestamp==0)
		{
		 	$log_timestamp=time();
		}
		 dbquery("
		 INSERT INTO
		 ".$db_table['logs']."
		 (
			 log_cat,
			 log_timestamp,
			 log_realtime,
			 log_text,
			 log_ip,
			 log_hostname
		 )
		 VALUES
		 (
		 	'".$log_cat."',
		 	'".$log_timestamp."',
		 	'".time()."',
		 	'".addslashes($log_text)."',
		 	'".$_SERVER['REMOTE_ADDR']."',
		 	'".@gethostbyaddr($_SERVER['REMOTE_ADDR'])."'
		 );");
	}

	function add_log_user($log_cat,$log_text,$uid1,$uid2=0,$pid=0,$sid=0,$log_timestamp=0)
	{
		global $db_table;
		if ($log_timestamp==0)
		{
		 	$log_timestamp=time();
		}
		 dbquery("
		 INSERT INTO
		 ".$db_table['logs']."
		 (
			 log_cat,
			 log_timestamp,
			 log_realtime,
			 log_text,
			 log_ip,
			 log_hostname,
			 log_user1_id,
			 log_user2_id,
			 log_planet_id,
			 log_ship_id
		 )
		 VALUES
		 (
		 	'".$log_cat."',
		 	'".$log_timestamp."',
		 	'".time()."',
		 	'".addslashes($log_text)."',
		 	'".$_SERVER['REMOTE_ADDR']."',
		 	'".@gethostbyaddr($_SERVER['REMOTE_ADDR'])."',
		 	'".intval($uid1)."',
		 	'".intval($uid2)."',
		 	'".intval($pid)."',
		 	'".intval($sid)."'
		 );");
	}

	/**
	* Speichert Gebäudedaten in die Game_Log-Tabelle
	*
	* @param string $log_text Log text
	* @param int $user_id - User ID
	* @param int $alliance_id - Allianz ID
	* @param int $planet_id - Planet ID
	* @param int $building_id - Gebäude ID
	* @param int $build_type - Bau Typ (Ausbau, Abriss...)
	* @param int $log_timestamp - Log Zeit
	* @author Lamborghini
	*/

	function add_log_game_building($log_text,$user_id,$alliance_id,$planet_id,$building_id,$build_type=0,$log_timestamp=0)
	{
		global $db_table;
		
		//Setzt auktuelle Zeit wenn keine andere angegeben wird
		if ($log_timestamp==0)
		{
		 	$log_timestamp=time();
		}
		
		//Speichert Log
		dbquery("
		INSERT INTO
		".$db_table['logs_game']."
		(
			logs_game_cat,
			logs_game_timestamp,
			logs_game_realtime,
			logs_game_text,
			logs_game_ip,
			logs_game_hostname,
			logs_game_user_id,
			logs_game_alliance_id,
			logs_game_planet_id,
			logs_game_building_id,
			logs_game_build_type
		)
		VALUES
		(
			'1',
			'".$log_timestamp."',
			'".time()."',
			'".addslashes($log_text)."',
			'".$_SERVER['REMOTE_ADDR']."',
			'".@gethostbyaddr($_SERVER['REMOTE_ADDR'])."',
			'".intval($user_id)."',
			'".intval($alliance_id)."',
			'".intval($planet_id)."',
			'".intval($building_id)."',
			'".intval($build_type)."'
		);");
	}


	/**
	* Speichert Forschungsdaten in die Game_Log-Tabelle
	*
	* @param string $log_text Log text
	* @param int $user_id - User ID
	* @param int $alliance_id - Allianz ID
	* @param int $planet_id - Planet ID
	* @param int $tech_id - Gebäude ID
	* @param int $build_type - Bau Typ (Ausbau, Abriss...)
	* @param int $log_timestamp - Log Zeit
	* @author Lamborghini
	*/

	function add_log_game_research($log_text,$user_id,$alliance_id,$planet_id,$tech_id,$build_type=0,$log_timestamp=0)
	{
		global $db_table;
		
		//Setzt auktuelle Zeit wenn keine andere angegeben wird
		if ($log_timestamp==0)
		{
		 	$log_timestamp=time();
		}
		
		//Speichert Log
		dbquery("
		INSERT INTO
		".$db_table['logs_game']."
		(
			logs_game_cat,
			logs_game_timestamp,
			logs_game_realtime,
			logs_game_text,
			logs_game_ip,
			logs_game_hostname,
			logs_game_user_id,
			logs_game_alliance_id,
			logs_game_planet_id,
			logs_game_tech_id,
			logs_game_build_type
		)
		VALUES
		(
			'2',
			'".$log_timestamp."',
			'".time()."',
			'".addslashes($log_text)."',
			'".$_SERVER['REMOTE_ADDR']."',
			'".@gethostbyaddr($_SERVER['REMOTE_ADDR'])."',
			'".intval($user_id)."',
			'".intval($alliance_id)."',
			'".intval($planet_id)."',
			'".intval($tech_id)."',
			'".intval($build_type)."'
		);");
	}


	/**
	* Speichert Schiffsdaten in die Game_Log-Tabelle
	*
	* @param string $log_text Log text
	* @param int $user_id - User ID
	* @param int $alliance_id - Allianz ID
	* @param int $planet_id - Planet ID
	* @param int $ship_id - Schiff ID
	* @param int $build_type - Bau Typ (Ausbau, Abbruch)
	* @param int $log_timestamp - Log Zeit
	* @author Lamborghini
	*/

	function add_log_game_ship($log_text,$user_id,$alliance_id,$planet_id,$build_type=0,$log_timestamp=0)
	{
		global $db_table;
		
		//Setzt auktuelle Zeit wenn keine andere angegeben wird
		if ($log_timestamp==0)
		{
		 	$log_timestamp=time();
		}
		
		//Speichert Log
		dbquery("
		INSERT INTO
		".$db_table['logs_game']."
		(
			logs_game_cat,
			logs_game_timestamp,
			logs_game_realtime,
			logs_game_text,
			logs_game_ip,
			logs_game_hostname,
			logs_game_user_id,
			logs_game_alliance_id,
			logs_game_planet_id,
			logs_game_build_type
		)
		VALUES
		(
			'3',
			'".$log_timestamp."',
			'".time()."',
			'".addslashes($log_text)."',
			'".$_SERVER['REMOTE_ADDR']."',
			'".@gethostbyaddr($_SERVER['REMOTE_ADDR'])."',
			'".intval($user_id)."',
			'".intval($alliance_id)."',
			'".intval($planet_id)."',
			'".intval($build_type)."'
		);");
	}



	/**
	* Speichert Defdaten in die Game_Log-Tabelle
	*
	* @param string $log_text Log text
	* @param int $user_id - User ID
	* @param int $alliance_id - Allianz ID
	* @param int $planet_id - Planet ID
	* @param int $def_id - Def ID
	* @param int $build_type - Bau Typ (Ausbau, Abbruch)
	* @param int $log_timestamp - Log Zeit
	* @author Lamborghini
	*/

	function add_log_game_def($log_text,$user_id,$alliance_id,$planet_id,$build_type=0,$log_timestamp=0)
	{
		global $db_table;
		
		//Setzt auktuelle Zeit wenn keine andere angegeben wird
		if ($log_timestamp==0)
		{
		 	$log_timestamp=time();
		}
		
		//Speichert Log
		dbquery("
		INSERT INTO
		".$db_table['logs_game']."
		(
			logs_game_cat,
			logs_game_timestamp,
			logs_game_realtime,
			logs_game_text,
			logs_game_ip,
			logs_game_hostname,
			logs_game_user_id,
			logs_game_alliance_id,
			logs_game_planet_id,
			logs_game_build_type
		)
		VALUES
		(
			'4',
			'".$log_timestamp."',
			'".time()."',
			'".addslashes($log_text)."',
			'".$_SERVER['REMOTE_ADDR']."',
			'".@gethostbyaddr($_SERVER['REMOTE_ADDR'])."',
			'".intval($user_id)."',
			'".intval($alliance_id)."',
			'".intval($planet_id)."',
			'".intval($build_type)."'
		);");
	}



	function get_spy_tech($user_id)
	{
		global $db_table;
		global $conf;

		$res = dbquery("
		SELECT
			techlist_current_level
		FROM
			".$db_table['techlist']."
		WHERE
			techlist_user_id=$user_id
			AND techlist_tech_id='".SPY_TECH_ID."';");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			return 	$arr['techlist_current_level'];
		}
		else
			return 0;
	}

	/**
	* Tabellen optimieren
	*/
	function optimize_tables($manual=false)
	{
		$res = dbquery("SHOW TABLES;");
		$n = mysql_num_rows($res);
		$cnt=0;
		$tbls = '';
		while ($arr=mysql_fetch_row($res))
		{
			$tbls.=$arr[0];
			$cnt++;
			if ($cnt<$n)
			{
				$tbls.=',';
			}
		}
		$ores = dbquery("OPTIMIZE TABLE ".$tbls.";");
		if ($manual)
		{
			add_log("4",$n." Tabellen wurden manuell optimiert!",time());
			return $ores;
		}
		else
		{
			add_log("4",$n." Tabellen wurden optimiert!",time());
			return $n;
		}
	}

	/**
	* Tabellen reparieren
	*/
	function repair_tables($manual=false)
	{
		$res = dbquery("SHOW TABLES;");
		$n = mysql_num_rows($res);
		$cnt=0;
		$tbls = '';
		while ($arr=mysql_fetch_row($res))
		{
			$tbls.=$arr[0];
			$cnt++;
			if ($cnt<$n)
			{
				$tbls.=',';
			}
		}
		$ores = dbquery("REPAIR TABLE ".$tbls.";");
		if ($manual)
		{
			add_log("4",$n." Tabellen wurden manuell repariert!",time());
			return $ores;
		}
		else
		{
			add_log("4",$n." Tabellen wurden repariert!",time());
			return $n;
		}	
	}

	/**
	* Tabellen prüfen
	*/
	function check_tables()
	{
		$res = dbquery("SHOW TABLES;");
		$n = mysql_num_rows($res);
		$cnt=0;
		$tbls = '';
		while ($arr=mysql_fetch_row($res))
		{
			$tbls.=$arr[0];
			$cnt++;
			if ($cnt<$n)
			{
				$tbls.=',';
			}
		}
		$ores = dbquery("CHECK TABLE ".$tbls.";");
		return $ores;
	}
	
	/**
	* Tabellen analysieren
	*/
	function analyze_tables()
	{
		$res = dbquery("SHOW TABLES;");
		$n = mysql_num_rows($res);
		$cnt=0;
		$tbls = '';
		while ($arr=mysql_fetch_row($res))
		{
			$tbls.=$arr[0];
			$cnt++;
			if ($cnt<$n)
			{
				$tbls.=',';
			}
		}
		$ores = dbquery("ANALYZE TABLE ".$tbls.";");
		return $ores;
	}
		
		


	//
	// Punkteberechnung
	//

	function pointsupdate($manual=false)
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

	function cut_string($string,$num)
	{
		if (strlen($string)>$num+3)
			return substr($string,0,$num)."...";
		else
			return $string;
	}


	function archk($rank,$urank)
	{
		if ($rank>$urank)
		{
			echo "<b>Sperre:</b> Du hast keinen Zugriff auf diese Seite!<br/>";
			return false;
		}
		else
			return true;
	}

	function checkEmail($email)
	{
	  return preg_match("/^[a-zA-Z0-9-_.]+@[a-zA-Z0-9-_.]+\.[a-zA-Z]{2,4}$/",$email);
	}
	
	function checkValidName($name)
	{
		return eregi(REGEXP_NAME, $name);
	}

	function checkValidNick($name)
	{
		return eregi(REGEXP_NICK, $name);
	}


	function encode_textfield ($string)
	{
		str_replace("\"","&quot;",$string);
		//str_replace("&","&amp;",$string);
		str_replace("<","&lt;",$string);
		str_replace(">","&gt;",$string);
		str_replace("´","&acute;",$string);
		str_replace("‘","&lsquo;",$string);
		str_replace("’","&rsquo;",$string);
		str_replace("'","&#39;",$string);

		return $string;
	}

	function decode_textfield ($string)
	{
		str_replace("&quot;","\"",$string);
		//str_replace("&amp;","&",$string);
		str_replace("&lt;","<",$string);
		str_replace("&gt;",">",$string);
		str_replace("&acute;","´",$string);
		str_replace("&lsquo;","‘",$string);
		str_replace("&rsquo;","’",$string);
		str_replace("&#39;","'",$string);

		return $string;
	}

	function print_fs_error_msg($string,$title="Fehler!")
	{
		echo "<table style=\"width:80%;margin:10px auto;border:1px solid #fff;border-collapse:collapse\">";
		echo "<tr><th class=\"tbltitle\">$title</th></tr>";
		echo "<tr><td class=\"tbldata\">$string</td></tr>";
		echo "</table>";
		echo "</body></html>";
		exit;
	}

	//
	// User Name in Array speichern
	//

	function get_user_names()
	{
		global $db_table;
		$names = array();
		$res = dbquery("
			SELECT
				user_id,
				user_nick,
				user_name,
				user_email,
				user_alliance_id
			FROM
				".$db_table['users']."
			ORDER BY
				user_nick;
		");
		while ($arr = mysql_fetch_array($res))
		{
			$names[$arr['user_id']]['nick'] = $arr['user_nick'];
			$names[$arr['user_id']]['name'] = $arr['user_name'];
			$names[$arr['user_id']]['email'] = $arr['user_email'];
			$names[$arr['user_id']]['alliance_id'] = $arr['user_alliance_id'];
		}
		return $names;
	}

	//
	// Die ultimative Antwort
	//

	function answer_to_life_the_universe_and_everything()
	{
		return 42;
	}

	//
	// Inaktive User löschen
	//

	function remove_inactive($manual=false)
	{
		global $conf,$db_table;

		$register_time = time()-(24*3600*$conf['user_inactive_days']['p2']);		// Zeit nach der ein User gelöscht wird wenn er noch 0 Punkte hat
		$online_time = time()-(24*3600*$conf['user_inactive_days']['p1']);	// Zeit nach der ein User normalerweise gelöscht wird

		$res =	dbquery("
			SELECT
				user_id
			FROM
				users
			WHERE
				user_show_stats='1'
				AND (user_registered<'".$register_time."' AND user_points='0')
				OR (user_last_online<'".$online_time."' AND user_last_online>0 AND user_hmode_from='0');
		");
		if (mysql_num_rows($res)>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
				delete_user($arr['user_id']);
			}
		}
		if ($manual)
			add_log("4",mysql_num_rows($res)." inaktive User die seit ".date("d.m.Y H:i",$online_time)." nicht mehr online waren oder seit ".date("d.m.Y H:i",$register_time)." keine Punkte haben wurden manuell gelöscht!",time());
		else
			add_log("4",mysql_num_rows($res)." inaktive User die seit ".date("d.m.Y H:i",$online_time)." nicht mehr online waren oder seit ".date("d.m.Y H:i",$register_time)." keine Punkte haben wurden gelöscht!",time());
			
		// Nachricht an lange inaktive
		$res =	dbquery("
			SELECT
				user_id,
				user_nick,
				user_email,
				user_last_online
			FROM
				users
			WHERE
				user_show_stats='1'
				AND user_last_online<'".USER_INACTIVE_TIME_LONG."' 
				AND user_last_online>'".(USER_INACTIVE_TIME_LONG-3600*24)."' 
				AND user_hmode_from='0';
		");
		if (mysql_num_rows($res)>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
			$text ="Hallo ".$arr['user_nick']."
			
Du hast dich seit mehr als ".USER_INACTIVE_LONG." Tage nicht mehr bei EtoA: Escape to Andromeda ( http://www.etoa.ch ) eingeloggt und
dein Account wurde deshalb als inaktiv markiert. Solltest du dich innerhalb von ".USER_INACTIVE_SHOW." Tage
nicht mehr einloggen wird der Account gelöscht.

Mit freundlichen Grüßen,
die Spielleitung";
			send_mail('',$arr['user_email'],'Inaktivität bei Escape to Andromeda',$text,'','');			
				
			}
		}			
			
			
			
			
		return mysql_num_rows($res);
	}

	/**
	* Delete user marked as delete
	*/
	function remove_deleted_users($manual=false)
	{
		global $db_table;

		$res =	dbquery("
			SELECT
				user_id
			FROM
				".$db_table['users']."
			WHERE
				user_deleted>0 && user_deleted<".time()."
		");
		if (mysql_num_rows($res)>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
				delete_user($arr['user_id']);
			}
		}
		if ($manual)
			add_log("4",mysql_num_rows($res)." als gelöscht markierte User wurden manuell gelöscht!",time());
		else
			add_log("4",mysql_num_rows($res)." als gelöscht markierte User wurden gelöscht!",time());
		return mysql_num_rows($res);
	}


	//
	// Alte Logs löschen
	//

	function remove_logs()
	{
		global $conf,$db_table;
		$tstamp=time()-(24*3600*$conf['log_threshold_days']['v']);
		dbquery("
			DELETE FROM
				".$db_table['logs']."
			WHERE
				log_timestamp<'".$tstamp."';
		");
		add_log("4","Logs die älter als ".date("d.m.Y H:i",$tstamp)." sind wurden gelöscht!",time());
	}

	//
	// Alte Nachrichten löschen
	//

	function remove_messages()
	{
		global $conf,$db_table;
		$tstamp=time()-(24*3600*$conf['messages_threshold_days']['v']);
		dbquery("
			DELETE FROM
				".$db_table['messages']."
			WHERE
				message_archived='0'
				AND message_timestamp<'".$tstamp."';
		");
		add_log("4","Unarchivierte Nachrichten die älter als ".date("d.m.Y H:i",$tstamp)." sind wurden gelöscht!",time());
	}

	// Benutzer löschen

	function delete_user($user_id,$self=false,$from="")
	{
		global $db_table;
  	$conf = get_all_config();
   	define(FLEET_ACTION_RESS,$conf['market_ship_action_ress']['v']); // Ressourcen
   	define(FLEET_ACTION_SHIP,$conf['market_ship_action_ship']['v']); // Schiffe

		$res=dbquery("
			SELECT
				user_nick,
				user_alliance_id,
				user_name,
				user_email,
				user_points,
				user_id
			FROM
				".$db_table['users']."
			WHERE
				user_id='".$user_id."';
		");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			if ($xmlfile = writeUserToXml($arr['user_id']))
			{

			//
			//Flotten und deren Schiffe löschen
			//
			$fres=dbquery("
				SELECT
					fleet_id
				FROM
					".$db_table['fleet']."
				WHERE
					fleet_user_id='".$user_id."';
			");
			if (mysql_num_rows($fres)>0)
			{
				while ($farr=mysql_fetch_array($fres))
				{
					// Flotten-Schiffe löschen
					dbquery("
						DELETE FROM
							".$db_table['fleet_ships']."
						WHERE
							fs_fleet_id='".$farr['fleet_id']."';
					");
				}
			}
			// Flotten löschen
			dbquery("
				DELETE FROM
					".$db_table['fleet']."
				WHERE
					fleet_user_id=".$user_id.";
			");


			//
			//Planeten Reseten und Handelschiffe die auf dem Weg zu einem Planeten sind löschen
			//
			$pres=dbquery("
				SELECT
					planet_id,
					planet_name,
					planet_res_metal,
					planet_res_crystal,
					planet_res_plastic,
					planet_res_fuel,
					planet_res_food
				FROM
					".$db_table['planets']."
				WHERE
					planet_user_id='".$user_id."';
			");
			if (mysql_num_rows($pres)>0)
			{
				while ($parr=mysql_fetch_array($pres))
				{
					//löscht alle markt-handelschiffe die auf dem weg zu dem user sind
                    $fres2=dbquery("
						SELECT
							fleet_id
						FROM
							".$db_table['fleet']."
						WHERE
							fleet_planet_to='".$parr['planet_id']."'
							AND (fleet_action='".FLEET_ACTION_RESS."' OR fleet_action='".FLEET_ACTION_SHIP."');
					");
                    if (mysql_num_rows($fres2)>0)
                    {
                        while ($farr2=mysql_fetch_array($fres2))
                        {
                        	// Flotten-Schiffe löschen
                            dbquery("
								DELETE FROM
									".$db_table['fleet_ships']."
								WHERE
									fs_fleet_id='".$farr2['fleet_id']."';
							");
                        }
                    }
					//Setzt Planet zurück
					reset_planet($parr['planet_id']);
				}
			}


			//
			// Allianz löschen (falls alleine) oder einen Nachfolger bestimmen
			//
			if ($arr['user_alliance_id']>0)
			{
				$ares=dbquery("
					SELECT
						u.user_id,
						a.alliance_founder_id
					FROM
						".$db_table['users']." AS u
						INNER JOIN
						".$db_table['alliances']." AS a
						ON u.user_alliance_id = a.alliance_id
						AND a.alliance_id=".$arr['user_alliance_id']."
						AND u.user_id!='".$user_id."'
					GROUP BY
						u.user_id
					ORDER BY
						u.user_points DESC;
				");
				if (mysql_num_rows($ares)>0)
				{
					$aarr=mysql_fetch_array($ares);

					 // Wenn der User der Gründer der Allianz ist wird der User mit den höchsten Punkten zum neuen Allianzgründer
					if ($user_id==$aarr['alliance_founder_id'])
					{
						dbquery("
							UPDATE
								".$db_table['alliances']."
							SET
								alliance_founder_id='".$aarr['user_id']."'
							WHERE
								alliance_id='".$arr['user_alliance_id']."';
						");
					}
				}
				else
				{
					// Wenn der User das einzige/letzte Mitglied der Allianz ist wird sie aufgelöst
					delete_alliance($arr['user_alliance_id']);
				}
			}



			//
			//Rest löschen
			//

			//Baulisten löschen
			dbquery("DELETE FROM ".$db_table['shiplist']." WHERE shiplist_user_id='".$user_id."';");		// Schiffe löschen
			dbquery("DELETE FROM ".$db_table['deflist']." WHERE deflist_user_id='".$user_id."';");			// Verteidigung löschen
			dbquery("DELETE FROM ".$db_table['techlist']." WHERE techlist_user_id='".$user_id."';");		// Forschung löschen
			dbquery("DELETE FROM ".$db_table['buildlist']." WHERE buildlist_user_id='".$user_id."';");		// Gebäude löschen

			//Buddyliste löschen
			dbquery("DELETE FROM ".$db_table['buddylist']." WHERE bl_user_id='".$user_id."' OR bl_buddy_id='".$user_id."';");

			//Markt Angebote löschen
			dbquery("DELETE FROM ".$db_table['market_ressource']." WHERE user_id='".$user_id."' AND ressource_buyable='1';"); 	// Rohstoff Angebot
			dbquery("DELETE FROM ".$db_table['market_ship']." WHERE user_id='".$user_id."' AND ship_buyable='1';"); 				// Schiff Angebot
			dbquery("DELETE FROM ".$db_table['market_auction']." WHERE auction_user_id='".$user_id."' AND auction_buyable='1';"); // Auktionen

			// Nachrichten löschen
			//dbquery("DELETE FROM ".$db_table['messages']." WHERE message_user_to='".$user_id."' OR message_user_from='".$user_id."';");

			//Notitzen löschen
			dbquery("DELETE FROM ".$db_table['notepad']." WHERE note_user_id='".$user_id."';");

			//Gespeicherte Koordinaten löschen
			dbquery("DELETE FROM ".$db_table['target_bookmarks']." WHERE bookmark_user_id='".$user_id."';");

			//'user' Info löschen
			//dbquery("DELETE FROM ".$db_table['user_log']." WHERE log_user_id='".$user_id."';"); 			//Log löschen
			dbquery("DELETE FROM ".$db_table['user_multi']." WHERE user_multi_user_id='".$user_id."' OR user_multi_multi_user_id='".$user_id."';"); //Multiliste löschen
			dbquery("DELETE FROM ".$db_table['user_points']." WHERE point_user_id='".$user_id."';"); 					//Punkte löschen
			dbquery("DELETE FROM ".$db_table['user_requests']." WHERE request_user_id='".$user_id."';"); 				//Nickänderungsanträge löschen
			dbquery("DELETE FROM ".$db_table['user_sitting']." WHERE user_sitting_user_id='".$user_id."';"); 			//Sitting löschen
			dbquery("DELETE FROM ".$db_table['user_sitting_date']." WHERE user_sitting_date_user_id='".$user_id."';"); //Sitting Daten löschen

			//
			//Benutzer löschen
			//
			dbquery("DELETE FROM ".$db_table['users']." WHERE user_id='".$user_id."';");




			//Log schreiben
			if($self)
				add_log("3","Der Benutzer ".$arr['user_nick']." hat sich selbst gelöscht!\nDie Daten des Benutzers wurden nach ".$xmlfile." exportiert.",time());
			elseif(!$self && $from!="")
				add_log("3","Der Benutzer ".$arr['user_nick']." wurde von ".$from." gelöscht!\nDie Daten des Benutzers wurden nach ".$xmlfile." exportiert.",time());
			else
				add_log("3","Der Benutzer ".$arr['user_nick']." wurde gelöscht!\nDie Daten des Benutzers wurden nach ".$xmlfile." exportiert.",time());

			$text ="Hallo ".$arr['user_nick']."
			
dein Accouont bei EtoA: Escape to Andromeda ( http://www.etoa.ch ) wurde auf Grund von Inaktivität 
oder auf eigenem Wunsch nun gelöscht.

Mit freundlichen Grüßen,
die Spielleitung";
			send_mail('',$arr['user_email'],'Accountlöschung bei Escape to Andromeda',$text,'','');
			
			//Arrays löschen (Speicher freigeben)
			mysql_free_result($bres);
			unset($barr);
			mysql_free_result($tres);
			unset($tarr);
			mysql_free_result($sres);
			unset($sarr);
			mysql_free_result($dres);
			unset($darr);
			mysql_free_result($pres);
			unset($parr);
			unset($lstr);

			return true;

			}
			else
			{
				echo "Konnte UserXML für ".$user_id." nicht exportieren, User nicht gelöscht!";
			}
		}
	}

	function delete_alliance($alliance_id,$self=false)
	{
		global $db_table;
		$res=dbquery("
			SELECT
				alliance_name
			FROM
				".$db_table['alliances']."
			WHERE
				alliance_id='".$alliance_id."';
		");
		$arr=mysql_fetch_array($res);

		//Daten löschen
		dbquery("
		DELETE FROM
			".$db_table['alliances']."
		WHERE
			alliance_id='".$alliance_id."';");

		dbquery("
			DELETE FROM
				".$db_table['alliance_bnd']."
			WHERE
				alliance_bnd_alliance_id1='".$alliance_id."'
				OR alliance_bnd_alliance_id2='".$alliance_id."';
		");
		dbquery("DELETE FROM ".$db_table['alliance_ranks']." WHERE rank_alliance_id='".$alliance_id."';");
		dbquery("DELETE FROM ".$db_table['alliance_history']." WHERE history_alliance_id='".$alliance_id."';");
		dbquery("DELETE FROM allianceboard_cat WHERE cat_alliance_id='".$alliance_id."';");
		dbquery("DELETE FROM ".$db_table['alliance_polls']." WHERE poll_alliance_id='".$alliance_id."';");
		dbquery("DELETE FROM ".$db_table['alliance_poll_votes']." WHERE vote_alliance_id='".$alliance_id."';");
		dbquery("
			UPDATE
				".$db_table['users']."
			SET
				user_alliance_id='0',
				user_alliance_application=''
			WHERE
				user_alliance_id='".$alliance_id."';
		");

		//Log schreiben
		if($self)
			add_log("5","Die Allianz [b]".$arr['alliance_name']."[/b] wurde manuell aufgelöst!",time());
		else
			add_log("5","Die Allianz [b]".$arr['alliance_name']."[/b] wurde gelöscht!",time());
		return true;
	}

	//
	// Abgelaufene Sperren löschen
	//

	function remove_old_banns()
	{
		global $db_table;
		dbquery("
			UPDATE
				".$db_table['users']."
			SET
				user_blocked_from='0',
				user_blocked_to='0',
				user_ban_reason='',
				user_ban_admin_id='0'
			WHERE
				user_blocked_to<'".time()."';
		");
	}


	function infobox_start($title,$table=0,$stretch=1,$width=0)
	{
		if ($width>0)
		{
			$w = " style=\"width:".$width."px\"";
		}
		else
		{
			$w = "";
		}
		
		if ($table==1)
		{
			if ($stretch==1)
				echo "<table class=\"tbl\" $w>";
			else
				echo "<table class=\"tblc\" $w>";
			if ($title!="")
				echo "<tr><td class=\"infoboxtitle\" colspan=\"20\">$title</td></tr>";
		}
		else
		{
			if ($title!="")
				echo "<div class=\"infoboxtitle\" $w>$title</div>";
			echo "<div class=\"infoboxcontent\" $w>";
		}
	}

	function infobox_end($table=0,$dontbreak=0)
	{
		if ($table==1)
		{
			echo "</table>";

			if($dontbreak==0)
			{
				echo "<br/><br/>";
			}
		}
		else
		{
			echo "</div>";

			if($dontbreak==0)
			{
				echo "<br/><br/>";
			}
		}
	}

	//
	// Planetendaten zurücksetzen
	//
	// $planet_id: MySQL-ID des Planeten
	//

	function reset_planet($planet_id)
	{
		global $db_table;
		if ($planet_id>0)
		{
			dbquery("
				UPDATE
					".$db_table['planets']."
				SET
					planet_user_id=0,
					planet_name='',
					planet_user_main=0,
					planet_fields_used=0,
					planet_fields_extra=0,
					planet_res_metal=0,
					planet_res_crystal=0,
					planet_res_fuel=0,
					planet_res_plastic=0,
					planet_res_food=0,
					planet_use_power=0,
					planet_last_updated=0,
					planet_prod_metal=0,
					planet_prod_crystal=0,
					planet_prod_plastic=0,
					planet_prod_fuel=0,
					planet_prod_food=0,
					planet_prod_power=0,
					planet_store_metal=0,
					planet_store_crystal=0,
					planet_store_plastic=0,
					planet_store_fuel=0,
					planet_store_food=0,
					planet_people=1,
					planet_people_place=0,
					planet_desc=''
				WHERE
					planet_id='".$planet_id."';
			");

			dbquery("
				DELETE FROM
					".$db_table['shiplist']."
				WHERE
					shiplist_planet_id='".$planet_id."';
			");
			dbquery("
				DELETE FROM
					".$db_table['buildlist']."
				WHERE
					buildlist_planet_id='".$planet_id."';
			");
			dbquery("
				DELETE FROM
					".$db_table['deflist']."
				WHERE
					deflist_planet_id='".$planet_id."';
			");
			add_log("6","Der Planet mit der ID ".$planet_id." wurde zurückgesetzt!",time());
			return true;
		}
		else
			return false;
	}

	//
	// Formatierte Fehlermeldung anzeigen
	//
	// $msg: Fehlermeldung
	//

	function err_msg($msg)
	{
		success_msg($msg);
		//echo "<div class=\"errMsg\"><b>Fehler:</b> ".text2html($msg)."</div><br/>";
	}

	//
	// Formatierte OK-Meldung anzeigen
	//
	// $msg: OK-Meldung
	//

	function ok_msg($msg)
	{
		error_msg($msg);
		//echo "<div class=\"okMsg\">".text2html($msg)."</div><br/>";
	}

	function success_msg($text,$type=0)
	{
		//infobox_start("Erfolgsmeldung");
		//echo '<div style="height:50px;padding:20px 0px 0px 60px;margin-bottom:-20px;;text-align:left;background: url(\'images/ok_middle.png\') no-repeat;">';
    //echo text2html($text).'</div>';
		//infobox_end();
		echo "<div class=\"successBox\">";
		switch($title)
		{
			case 1:
				echo "";
				break;
			case 2:
				echo "<b>Hurra:</b> ";
				break;
			default:
				echo "<b>Erfolg:</b> ";
		}		
		echo text2html($text)."</div>";		
	}

	function error_msg($text,$type=0)
	{
		//infobox_start("Fehlermeldung");
		//echo '<div style="height:50px;padding:20px 0px 0px 60px;margin-bottom:-20px;;text-align:left;background: url(\'images/err_middle.png\') no-repeat;">';
    //echo text2html($text).'</div>';
		//infobox_end();
		echo "<div class=\"errorBox\">";
		switch($title)
		{
			case 3:
				echo "";
				break;
			case 2:
				echo "<b>Warnung:</b> ";
				break;
			case 3:
				echo "<b>Problem:</b> ";
				break;
			default:
				echo "<b>Fehler:</b> ";
		}		
		echo text2html($text)."</div>";
	}


	//
	// Prozentwert generieren und zurückgeben
	//
	// $val: Einzelner Wert oder Array von Werten als Dezimalzahl; 1.0 = 0%
	// $colors: Farben anzeigen (1) oder nicht anzeigen (0)
	//

	function get_percent_string($val,$colors=0,$inverse=0)
	{
		$string=0;
		if (is_array($val))
		{
			foreach ($val as $v)
			{
				$string+=($v*100)-100;
			}
		}
		else
			$string = ($val*100)-100;

		$string=round($string,2);

		if ($string>0)
		{
			if ($colors!=0)
			{
				if ($inverse==1)
					$string="<span style=\"color:#f00\">+".$string."%</span>";
				else
					$string="<span style=\"color:#0f0\">+".$string."%</span>";
			}
			else
				$string=$string."%";
		}
		elseif ($string<0)
		{
			if ($colors!=0)
			{
				if ($inverse==1)
					$string="<span style=\"color:#0f0\">".$string."%</span>";
				else
					$string="<span style=\"color:#f00\">".$string."%</span>";
			}
			else
				$string=$string."%";
		}
		else
		{
			$string="0%";
		}
		return $string;
	}

	//
	// Tabulator-Menü anzeigen
	//
	// $varname: Name des Modusfeldes
	// $data: Array mit Menüdaten
	//

	function show_tab_menu($varname,$data)
	{
		global $page,$$varname;
		$width = 100/count($data);
		echo "<table class=\"tbl\"><tr>";
		foreach ($data as $val => $text)
		{
			if ($$varname==$val)
				echo "<td class=\"statsTab\" style=\"width:".$width."%;vertical-align:middle;\"><a href=\"?page=$page&amp;".$varname."=$val\" class=\"tabEnabled\">$text</a></td>";
			else
				echo "<td class=\"statsTab\" style=\"width:".$width."%;vertical-align:middle;\"><a href=\"?page=$page&amp;".$varname."=$val\" class=\"tabDefault\">$text</a></td>";
		}
		echo "</tr></table>";
	}

	function show_js_tab_menu($data)
	{
		$width = 100/count($data);
		echo "<table class=\"tbl\"><tr>";
		$x=0;
		foreach ($data as $val => $text)
		{
			//if ($$varname==$val)
			//	echo "<td class=\"statsTab\" width=\"$width%\"><a href=\"javascript:;\" onclick=\"$val\" class=\"tabEnabled\">$text</a></td>";
			//else
				echo "<td class=\"statsTab\" width=\"$width%\"><a href=\"javascript:;\" id=\"tabMenu$x\" onclick=\"$val\" class=\"tabDefault\">$text</a></td>";
			$x++;
		}
		echo "</tr></table>";
	}

	function get_imagepacks($path="")
	{
		$pack=array();
		global $conf;
		if ($d=opendir($path.IMAGEPACK_DIRECTORY))
		{
			while ($f=readdir($d))
			{
				$dir = IMAGEPACK_DIRECTORY."/".$f;
				if (is_dir($path.$dir) && $f!=".." && $f!=".")
				{
					$file = $path.$dir."/imagepack.xml";
					
					if (is_file($file))
					{
						$xml = new XMLReader();
						$xml->open($file);
				    while ($xml->read()) 
				    {
			        switch ($xml->name) 
			        {
			        	case "name":
			            $xml->read();
			            $pack[$dir]['name']= $xml->value;
			            $xml->read();
			            break;
			        	case "changed":
			            $xml->read();
			            $pack[$dir]['changed']= $xml->value;
			            $xml->read();
			            break;
			       	 	case "extensions":
			            $xml->read();
			            $pack[$dir]['extensions']= explode(",",$xml->value);
			            $xml->read();
			            break;
			       	 	case "author":
			            $xml->read();
			            $pack[$dir]['author']= $xml->value;
			            $xml->read();
			            break;
			       	 	case "email":
			            $xml->read();
			            $pack[$dir]['email']= $xml->value;
			            $xml->read();
			            break;		
			       	 	case "files":
			            $xml->read();
			            $pack[$dir]['files']= explode(",",$xml->value);
			            $xml->read();
			            break;			            		            
			        }
				    }
						$xml->close();
					}
				}
			}
		}
		return $pack;
	}

	//Wählt die verschiedenen Designs aus und schreibt sie in ein array. by Lamborghini
	function get_designs($path="")
	{
		$designs=array();
		if ($d=opendir($path.DESIGN_DIRECTORY))
		{
			while ($f=readdir($d))
			{
				$dir = DESIGN_DIRECTORY."/".$f;
				if (is_dir($path.$dir) && $f!=".." && $f!=".")
				{
					$file = $path.$dir."/design.xml";
					if (is_file($file))
					{
						$xml = new XMLReader();
						$xml->open($file);
				    while ($xml->read()) 
				    {
				        switch ($xml->name) 
				        {
				        	case "name":
				            $xml->read();
				            $designs[$f]['name']= $xml->value;
				            $xml->read();
				            break;
				        	case "changed":
				            $xml->read();
				            $designs[$f]['changed']= $xml->value;
				            $xml->read();
				            break;
				       	 	case "version":
				            $xml->read();
				            $designs[$f]['version']= $xml->value;
				            $xml->read();
				            break;
				       	 	case "author":
				            $xml->read();
				            $designs[$f]['author']= $xml->value;
				            $xml->read();
				            break;
				       	 	case "email":
				            $xml->read();
				            $designs[$f]['email']= $xml->value;
				            $xml->read();
				            break;	
				       	 	case "description":
				            $xml->read();
				            $designs[$f]['description']= $xml->value;
				            $xml->read();
				            break;						            			            
				        }
				    }
						$xml->close();
					}
				}
			}
		}
		return $designs;
	}	

	//
	// Überprüft ob ein Gebäude deaktiviert ist
	//
	// $user_id: Benutzer-ID
	// $planet_id: Planet-ID
	// $building_id: Gebäude-ID
	//

	function check_building_deactivated($user_id,$planet_id,$building_id)
	{
		global $db_table;
		$res=dbquery("
			SELECT
				buildlist_deactivated
			FROM
				".$db_table['buildlist']."
			WHERE
				buildlist_user_id='".$user_id."'
				AND buildlist_planet_id='".$planet_id."'
				AND buildlist_building_id='".$building_id."'
				AND buildlist_deactivated>'".time()."';
		");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_row($res);
			return $arr[0];
		}
		else
			return false;
	}

	function check_new_messages($user_id)
	{
		global $db_table;
		$mres = dbquery("
			SELECT
				message_id
			FROM
				".$db_table['messages']."
			WHERE
				message_deleted='0'
				AND message_user_to='".$user_id."'
				AND message_read='0';
		");
		$count=mysql_num_rows($mres);
		return $count;
	}



	//Fremde, nicht feindliche Flotten
	function check_fleet_incomming_friendly($user_id)
	{
		global $db_table;

        $fres = dbquery("
			SELECT
				f.fleet_id
			FROM
				".$db_table['fleet']." AS f
				INNER JOIN
				".$db_table['planets']." AS p
				ON f.fleet_planet_to = p.planet_id
				AND f.fleet_user_id!='".$user_id."'
				AND p.planet_user_id='".$user_id."'
				AND f.fleet_action!='wo'
				AND f.fleet_action!='vo'
				AND f.fleet_action!='zo'
				AND f.fleet_action!='ao'
				AND f.fleet_action!='io'
				AND f.fleet_action!='so'
				AND f.fleet_action!='bo'
				AND f.fleet_action!='xo'
				AND f.fleet_action!='vo'
				AND f.fleet_action!='lo'
				AND f.fleet_action!='do'
				AND f.fleet_action!='ho'
				AND f.fleet_action!='eo'
			ORDER BY
				f.fleet_landtime ASC;
		");
		$count=mysql_num_rows($fres);

        return $count;
	}

	//Fremde, feindliche Flotten
    /**
    * Gibt Anzahl feindliche Flotten zurück unter beachtung von Tarn- und Spionagetechnik
    * Sind keine Flotten unterwegs -> return 0
    *
    * @author MrCage
    * @param int $user_id User ID
    */
	function check_fleet_incomming($user_id)
	{
		global $db_table;
		$num=0;
		$ffres=dbquery("
			SELECT
				f.fleet_id,
				f.fleet_user_id,
				f.fleet_planet_from,
				f.fleet_landtime,
				f.fleet_launchtime,
				f.fleet_action
			FROM
				".$db_table['fleet']." AS f
				INNER JOIN
				".$db_table['planets']." AS p
				ON f.fleet_planet_to = p.planet_id
				AND f.fleet_user_id!='".$user_id."'
				AND p.planet_user_id='".$user_id."'
				AND (f.fleet_action='ao'
				OR f.fleet_action='io'
				OR f.fleet_action='so'
				OR f.fleet_action='bo'
				OR f.fleet_action='xo'
				OR f.fleet_action='lo'
				OR f.fleet_action='ho'
				OR f.fleet_action='do'
				OR f.fleet_action='eo'
				OR f.fleet_action='vo');
		");


		$spiores=dbquery("
			SELECT
				techlist_current_level
			FROM
				".$db_table['techlist']."
			WHERE
				techlist_user_id='".$user_id."'
				AND techlist_tech_id='7'
		"); //Spiotech
		$spiorow=mysql_fetch_array($spiores);

		if(mysql_num_rows($ffres)>0)
		{
            while ($farr=mysql_fetch_array($ffres))
            {
                $show_tarn=0;
                //if($farr['fleet_action']=='vo')
                //{
                    //Zählt alle nicht tarnfähigen Schiffe aus
                    $tarn_ship_res=dbquery("
						SELECT
							s.ship_id
						FROM
							".$db_table['fleet_ships']." AS fs
							INNER JOIN ".$db_table['ships']." AS s
							ON fs.fs_ship_id = s.ship_id
							AND fs.fs_fleet_id='".$farr['fleet_id']."'
							AND s.ship_tarned!='1';
					");

                    if(mysql_num_rows($tarn_ship_res)>0)
                    {
                        $show_tarn=1;
                    }
                //}

                //Liest Tarntechnik vom Angreiffer aus
                $tarnres=dbquery("
					SELECT
						techlist_current_level
					FROM
						".$db_table['techlist']."
					WHERE
						techlist_user_id='".$farr['fleet_user_id']."'
						AND techlist_tech_id='11'
				"); //Tarntech
                $tarnrow=mysql_fetch_array($tarnres);


                $special_ship_bonus_tarn = 0;

                //Liest Tarnbonus von den Spezialschiffen aus
                $special_boni_res=dbquery("
					SELECT
						s.special_ship_bonus_tarn,
						sl.shiplist_special_ship_bonus_tarn
					FROM
						".$db_table['ships']." AS s
						INNER JOIN
						(
							".$db_table['shiplist']." AS sl
							INNER JOIN
							".$db_table['fleet_ships']." AS fs
							ON sl.shiplist_ship_id = fs.fs_ship_id
						)
						ON s.ship_id = fs.fs_ship_id
						AND fs.fs_fleet_id='".$farr['fleet_id']."'
						AND sl.shiplist_user_id='".$farr['fleet_user_id']."'
						AND sl.shiplist_planet_id='".$farr['fleet_planet_from']."'
						AND s.special_ship='1';
				");
                if(mysql_num_rows($special_boni_res)>0)
                {
                    while ($special_boni_arr=mysql_fetch_array($special_boni_res))
                    {
                        $special_ship_bonus_tarn+=$special_boni_arr['special_ship_bonus_tarn'] * $special_boni_arr['shiplist_special_ship_bonus_tarn'];
                    }
                }


                if ($tarnrow['techlist_current_level']-$spiorow['techlist_current_level']<0)
                {
                    $diff_time_factor=0;
                }
                elseif ($tarnrow['techlist_current_level']-$spiorow['techlist_current_level']>9)
                {
                    $diff_time_factor=9;
                }
                else
                {
                    $diff_time_factor=$tarnrow['techlist_current_level']-$spiorow['techlist_current_level'];
                }

                $tarned = 0.1*$diff_time_factor+$special_ship_bonus_tarn;
                //Flotte kann maximum zu 90% des Fluges getarnt werden, auch mit Spezialschiffsboni
                if($tarned>0.9)
                    $tarned=0.9;

                if (time() - $farr['fleet_landtime'] - ($farr['fleet_launchtime'] - $farr['fleet_landtime']) * (1-$tarned)>0 && $spiorow['techlist_current_level']>=SPY_TECH_SHOW_ATTITUDE && $show_tarn==1)
                {
                    $fres = dbquery("
						SELECT
							f.fleet_id
						FROM
							".$db_table['fleet']." AS f
							INNER JOIN
							".$db_table['planets']." AS p
							ON f.fleet_planet_to = p.planet_id
							AND f.fleet_id='".$farr['fleet_id']."'
							AND f.fleet_user_id!='".$user_id."'
							AND p.planet_user_id='".$user_id."'
							AND (f.fleet_action='ao'
								OR f.fleet_action='io'
								OR f.fleet_action='so'
								OR f.fleet_action='bo'
								OR f.fleet_action='xo'
								OR f.fleet_action='lo'
								OR f.fleet_action='ho'
								OR f.fleet_action='do'
								OR f.fleet_action='eo'
								OR f.fleet_action='vo');
					");

                    if (mysql_num_rows($fres)>0)
                    {
                        $num++;
                    }
                }
            }
        }
		return $num;
	}

	function add_alliance_history($alliance_id,$text)
	{
		global $db_table;
		dbquery("
			INSERT INTO
			".$db_table['alliance_history']."
			(
				history_alliance_id,
				history_text,
				history_timestamp
			)
			VALUES
			(
				'".$alliance_id."',
				'".addslashes($text)."',
				'".time()."'
			);");
	}

	function add_user_history($user_id,$text)
	{
		global $db_table;
		dbquery("
			INSERT INTO
			".$db_table['user_history']."
			(
				history_user_id,
				history_text,
				history_timestamp
			)
			VALUES
			(
				'".$user_id."',
				'".addslashes($text)."',
				'".time()."'
			);");
	}

	function check_buddys_online($id)
	{
		global $db_table,$conf;
		return mysql_num_rows(dbquery("
			SELECT
				user_id
			FROM
				".$db_table['buddylist']." AS bl
				INNER JOIN ".$db_table['users']." AS u
				ON bl.bl_buddy_id = u.user_id
				AND bl_user_id='".$id."'
				AND bl_allow=1
				AND user_acttime>".(time()-$conf['online_threshold']['v']*60).";
		"));
	}

	function checker_init($debug=0)
	{
		$_SESSION['checker']=md5(mt_rand(0,99999999).time());
		while ($_SESSION['checker_last']==$_SESSION['checker'])
		{
			$_SESSION['checker']=md5(mt_rand(0,99999999).time());
		}
		$_SESSION['checker_last']=$_SESSION['checker'];
		echo "<input type=\"hidden\" name=\"checker\" value=\"".$_SESSION['checker']."\" />";
		if ($debug==1)
			echo "Checker initialized with ".$_SESSION['checker']."<br/><br/>";
		return "<input type=\"hidden\" name=\"checker\" value=\"".$_SESSION['checker']."\" />";
	}

	function checker_verify($debug=0,$msg=1)
	{
		global $_POST,$_GET;
		if ($debug==1)
			echo "Checker-Session is: ".$_SESSION['checker'].", Checker-POST is: ".$_POST['checker']."<br/><br/>";
		if (($_SESSION['checker']==$_POST['checker'] || $_SESSION['checker']==$_GET['checker'] )&& $_SESSION['checker']!="")
		{
			$_SESSION['checker']=Null;
			return true;
		}
		else
		{
			if ($msg==1)
			{
				error_msg("Seite kann nicht mehrfach aufgerufen werden!");
			}
			else
			{
				echo "<b>Fehler:</b> Seite kann nicht mehrfach aufgerufen werden!<br/><br/>";
			}
			return false;
		}
	}

	function checker_get_key()
	{
		return $_SESSION['checker'];
	}

	function checker_get_link_key()
	{
		return "&amp;checker=".$_SESSION['checker'];
	}



	//
	// Entfernt Tick's die per JavaScript hinzugefügt wurden
	//
	function deltick($text)
	{
		if (is_array($text))
		{
			$tmp=array();
			foreach ($text as $t)
			{
				array_push($tmp,str_replace(".","",$t));
			}
			$text= $tmp;
		}
		else
			$text= str_replace(".","",$text);
		return $text;
	}

	//
	// Schaltet die Tick-Funktion für ein Feld ein
	//
	function numberfield($defval=0)
	{
		if ($_SESSION[ROUNDID]['user']['tick'])
		{
			return  " onfocus=\"removeDefVal(this,0".$defval.")\" onblur=\"setDefVal(this,0".$defval.")\" onkeypress=\"return nurZahlen(event)\" onkeyup=\"setTick(this);\"";
		}
	}


	function return_btn()
	{
		global $page;
		echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Zur&uuml;ck\" />";
	}


	function pseudo_randomize($faktor,$qx,$qy,$px,$py)
	{
		$str=floor((abs(sin($qx)) + abs(sin($qy)) + abs(sin($px)) + abs(sin($py)))*10000);
		return round ($faktor*((substr($str,strlen($str)-1,1)+1)/10),0);
	}


	function zeroPlus($val)
	{
		if ($val<0)
			return 0;
		else
			return $val;
	}

	// Diese Funktion liefert 5 Optionsfelder in denen man den Tag,Monat,Jahr,Stunde,Minute auswählen kann
	function show_timebox($element_name,$def_val,$seconds=0)
	{
			// Liefert Tag 1-31
			echo "<select name=\"".$element_name."_d\" id=\"".$element_name."_d\">";
			for ($x=1;$x<32;$x++)
			{
				echo "<option value=\"$x\"";
				if (date("d",$def_val)==$x) echo " selected=\"selected\"";
				echo ">";
				if ($x<10) echo 0;
				echo "$x</option>";
			}
			echo "</select>.";

			// Liefert Monat 1-12
			echo "<select name=\"".$element_name."_m\" id=\"".$element_name."_m\">";
			for ($x=1;$x<13;$x++)
			{
				echo "<option value=\"$x\"";
				if (date("m",$def_val)==$x) echo " selected=\"selected\"";
				echo ">";
				if ($x<10) echo 0;
				echo "$x</option>";
			}
			echo "</select>.";

			// Liefert Jahr +-1 vom jetzigen Jahr
			echo "<select name=\"".$element_name."_y\" id=\"".$element_name."_y\">";
			for ($x=date("Y")-1;$x<date("Y")+2;$x++)
			{
				echo "<option value=\"$x\"";
				if (date("Y",$def_val)==$x) echo " selected=\"selected\"";
				echo ">$x</option>";
			}
			echo "</select> &nbsp;&nbsp;";

			// Liefert Stunden von 00-24
			echo "<select name=\"".$element_name."_h\" id=\"".$element_name."_h\">";
			for ($x=0;$x<25;$x++)
			{
				echo "<option value=\"$x\"";
				if (date("H",$def_val)==$x) echo " selected=\"selected\"";
				echo ">";
				if ($x<10) echo 0;
				echo "$x</option>";
			}
			echo "</select>:";

			// Liefert Minuten 1-60
			echo "<select name=\"".$element_name."_i\" id=\"".$element_name."_i\">";
			for ($x=0;$x<60;$x++)
			{
				echo "<option value=\"$x\"";
				if (date("i",$def_val)==$x) echo " selected=\"selected\"";
				echo ">";
				if ($x<10) echo 0;
				echo "$x</option>";
			}
			echo "</select>";
			if ($seconds==1)
				echo ":";

			// Liefert Sekunden 1-60
			if ($seconds==1)
			{
				echo "<select name=\"".$element_name."_s\" id=\"".$element_name."_s\">";
				for ($x=0;$x<60;$x++)
				{
					echo "<option value=\"$x\"";
					if (date("s",$def_val)==$x) echo " selected=\"selected\"";
					echo ">";
					if ($x<10) echo 0;
					echo "$x</option>";
				}
				echo "</select>";
			}
			
	}

	function serverTime()
	{
		echo date("H:i:s");
	}

	// Tipmessage
	function tm($title,$text,$mouse=0)
	{
		$text = cut_word($text,150,1);
		$text = str_replace('"',"\'",$text);
		if($mouse==0)
		{
			return "onmouseover=\"stm(['".$title."','".$text."'],stl)\" onmouseout=\"htm()\"";
		}
		else
		{
			return "onclick=\"stm(['".$title."','".$text."'],stl)\" onmouseout=\"htm()\"";
		}
	}

	// Date format
	function df($date,$seconds=0)
	{
		if ($seconds==1)
		{
			if (date("dmY") == date("dmY",$date))
				$string = "Heute, ".date("H:i:s",$date);
			else
				$string = date("d.m.Y, H:i:s",$date);
		}
		else
		{
			if (date("dmY") == date("dmY",$date))
				$string = "Heute, ".date("H:i",$date);
			else
				$string = date("d.m.Y, H:i",$date);
		}
		return $string;
	}


	//Markt: Abgelaufene Auktionen löschen
	function market_auction_update()
	{
		global $db_table;
		global $conf;


    $res=dbquery("
		SELECT
			*
		FROM
			".$db_table['market_auction']."
		WHERE
			auction_end<'".time()."'
			OR auction_delete_date!='0';");
    if(mysql_num_rows($res) > 0)
    {
    	$sell_metal_total = 0;
			$sell_crystal_total = 0;
			$sell_plastic_total = 0;
			$sell_fuel_total = 0;
			$sell_food_total = 0;
			
      while($arr=mysql_fetch_array($res))
      {
          //Markt Level vom Verkäufer laden
          $mres = dbquery("
					SELECT
						buildlist_current_level
					FROM
						".$db_table['buildlist']."
					WHERE
						buildlist_planet_id='".$arr['auction_planet_id']."'
						AND buildlist_building_id='".MARKTPLATZ_ID."'
						AND buildlist_current_level>'0'
						AND buildlist_user_id='".$arr['auction_user_id']."';");
          $marr = mysql_fetch_array($mres);
          
          // Definiert den Rückgabefaktor
          $return_factor = 1 - (1/($marr['buildlist_current_level']+1));

          $partner_user_nick = get_user_nick($arr['auction_user_id']);
          $buyer_user_nick = get_user_nick($arr['auction_current_buyer_id']);
          $delete_date = time() + (AUCTION_DELAY_TIME * 3600);

          //überprüfen ob geboten wurde, wenn nicht, Waren dem Verkäufer zurückgeben
          if($arr['auction_current_buyer_id']=='0')
          {
            // Ress dem besitzer zurückgeben (mit dem faktor)
            dbquery("
						UPDATE
							".$db_table['planets']."
						SET
							planet_res_metal=planet_res_metal+".($arr['auction_sell_metal']*$return_factor).",
							planet_res_crystal=planet_res_crystal+".($arr['auction_sell_crystal']*$return_factor).",
							planet_res_plastic=planet_res_plastic+".($arr['auction_sell_plastic']*$return_factor).",
							planet_res_fuel=planet_res_fuel+".($arr['auction_sell_fuel']*$return_factor).",
							planet_res_food=planet_res_food+".($arr['auction_sell_food']*$return_factor)."
						WHERE
							planet_id='".$arr['auction_planet_id']."'
							AND planet_user_id='".$arr['auction_user_id']."';");


            // Nachricht senden
            $msg = "Folgende Auktion ist erfolglos abgelaufen und wurde gelöscht.\n\n"; 
            
            $msg .= "Start: ".date("d.m.Y H:i",$arr['auction_start'])."\n";
            $msg .= "Ende: ".date("d.m.Y H:i",$arr['auction_end'])."\n\n";
            
            $msg .= "[b]Waren:[/b]\n";
            $msg .= "".RES_METAL.": ".nf($arr['auction_sell_metal'])."\n";
            $msg .= "".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n";
            $msg .= "".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n";
            $msg .= "".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n";
            $msg .= "".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\n";
            
            $msg .= "Du erhälst ".(round($return_factor,2)*100)."% deiner Rohstoffe wieder zurück (abgerundet)!\n\n";
            
            $msg .= "Das Handelsministerium";
            send_msg($arr['auction_user_id'],SHIP_MISC_MSG_CAT_ID,"Auktion beendet",$msg);

            //Auktion löschen
            dbquery("
						DELETE FROM
							".$db_table['market_auction']."
						WHERE
							auction_market_id='".$arr['auction_market_id']."';");

          }
          //Jemand hat geboten: Waren zum Versenden freigeben und Nachricht schreiben
          elseif($arr['auction_current_buyer_id']!='0' && $arr['auction_buyable']=='1')
          {
            // Nachricht an Verkäufer
            $msg = "Die Auktion vom ".date("d.m.Y H:i",$arr['auction_start']).", welche am ".date("d.m.Y H:i",$arr['auction_end'])." endete, ist erfolgteich abgelaufen und wird nach ".AUCTION_DELAY_TIME." Stunden gelöscht. Die Waren werden nach wenigen Minuten versendet.\n\nDer Spieler ".$buyer_user_nick." hat von dir folgende Rohstoffe ersteigert:\n\n";
            
            $msg .= "".RES_METAL.": ".nf($arr['auction_sell_metal'])."\n";
            $msg .= "".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n";
            $msg .= "".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n";
            $msg .= "".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n";
            $msg .= "".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\n";
            
            $msg .= "Dies macht dich um folgende Rohstoffe reicher:\n";   
            $msg .= "".RES_METAL.": ".nf($arr['auction_buy_metal'])."\n";   
            $msg .= "".RES_CRYSTAL.": ".nf($arr['auction_buy_crystal'])."\n";
            $msg .= "".RES_PLASTIC.": ".nf($arr['auction_buy_plastic'])."\n";
            $msg .= "".RES_FUEL.": ".nf($arr['auction_buy_fuel'])."\n";   
            $msg .= "".RES_FOOD.": ".nf($arr['auction_buy_food'])."\n\n";   
            
            $msg .= "Das Handelsministerium";
            send_msg($arr['auction_user_id'],SHIP_MISC_MSG_CAT_ID,"Auktion beendet",$msg);

            // Nachricht an Käufer
            $msg = "Du warst der höchstbietende in der Auktion vom Spieler ".$partner_user_nick.", welche am ".date("d.m.Y H:i",$arr['auction_end'])." zu Ende ging.\n
            Du hast folgende Rohstoffe ersteigert:\n\n";
            
            $msg .= "".RES_METAL.": ".nf($arr['auction_sell_metal'])."\n";
            $msg .= "".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n";
            $msg .= "".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n";
            $msg .= "".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n";  
            $msg .= "".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\n";
            
            $msg .= "Dies hat dich folgende Rohstoffe gekostet:\n\n"; 
            
            $msg .= "".RES_METAL.": ".nf($arr['auction_buy_metal'])."\n"; 
            $msg .= "".RES_CRYSTAL.": ".nf($arr['auction_buy_crystal'])."\n";
            $msg .= "".RES_PLASTIC.": ".nf($arr['auction_buy_plastic'])."\n";
            $msg .= "".RES_FUEL.": ".nf($arr['auction_buy_fuel'])."\n";   
            $msg .= "".RES_FOOD.": ".nf($arr['auction_buy_food'])."\n\n"; 
            
            $msg .= "Die Auktion wird nach ".AUCTION_DELAY_TIME." Stunden gelöscht und die Waren in wenigen Minuten versendet.\n\n";
            
            $msg .= "Das Handelsministerium";
            send_msg($arr['auction_current_buyer_id'],SHIP_MISC_MSG_CAT_ID,"Auktion beendet",$msg);

            

            //Log schreiben, falls dieser Handel regelwidrig ist
            $multi_res1=dbquery("
						SELECT
							user_multi_multi_user_id
						FROM
							".$db_table['user_multi']."
						WHERE
							user_multi_user_id='".$arr['auction_user_id']."'
							AND user_multi_multi_user_id='".$arr['auction_current_buyer_id']."';");

            $multi_res2=dbquery("
						SELECT
							user_multi_multi_user_id
						FROM
							".$db_table['user_multi']."
						WHERE
							user_multi_user_id='".$arr['auction_current_buyer_id']."'
							AND user_multi_multi_user_id='".$arr['auction_user_id']."';");

            if(mysql_num_rows($multi_res1)!=0 && mysql_num_rows($multi_res2)!=0)
            {
              add_log(10,"[URL=?page=user&sub=edit&user_id=".$arr['auction_current_buyer_id']."][B]".$buyer_user_nick."[/B][/URL] hat an einer Auktion von [URL=?page=user&sub=edit&user_id=".$arr['auction_user_id']."][B]".$partner_user_nick."[/B][/URL] gewonnen:\n\nSchiffe:\n".nf($arr['auction_ship_count'])." ".$arr['auction_ship_name']."\n\nRohstoffe:\n".RES_METAL.": ".nf($arr['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:\n".RES_METAL.": ".nf($arr['auction_buy_metal'])."\n".RES_CRYSTAL.": ".nf($arr['auction_buy_crystal'])."\n".RES_PLASTIC.": ".nf($arr['auction_buy_plastic'])."\n".RES_FUEL.": ".nf($arr['auction_buy_fuel'])."\n".RES_FOOD.": ".nf($arr['auction_buy_food'])."",time());
            }


            // Log schreiben
            add_log(7,"Auktion erfolgreich abgelaufen.\nDer Spieler ".$buyer_user_nick." hat vom Spieler ".$partner_user_nick." folgende Waren ersteigert:\n\nRohstoffe:\n".RES_METAL.": ".nf($arr['auction_sell_metal'])."\n".RES_CRYSTAL.": ".nf($arr['auction_sell_crystal'])."\n".RES_PLASTIC.": ".nf($arr['auction_sell_plastic'])."\n".RES_FUEL.": ".nf($arr['auction_sell_fuel'])."\n".RES_FOOD.": ".nf($arr['auction_sell_food'])."\n\nDies hat ihn folgende Rohstoffe gekostet:\n".RES_METAL.": ".nf($arr['auction_buy_metal'])."\n".RES_CRYSTAL.": ".nf($arr['auction_buy_crystal'])."\n".RES_PLASTIC.": ".nf($arr['auction_buy_plastic'])."\n".RES_FUEL.": ".nf($arr['auction_buy_fuel'])."\n".RES_FOOD.": ".nf($arr['auction_buy_food'])."\n\nDie Auktion und wird nach ".AUCTION_DELAY_TIME." Stunden gelöscht.",time());

            //Auktion noch eine zeit lang anzeigen, aber unkäuflich machen
            dbquery("
						UPDATE
							".$db_table['market_auction']."
						SET
							auction_buyable='0',
							auction_delete_date='".$delete_date."',
							auction_sent='0'
						WHERE
							auction_market_id='".$arr['auction_market_id']."';");
							
						// Verkauftse Roshtoffe summieren für Config
						$sell_metal_total += $arr['auction_sell_metal'];
						$sell_crystal_total += $arr['auction_sell_crystal'];
						$sell_plastic_total += $arr['auction_sell_plastic'];
						$sell_fuel_total += $arr['auction_sell_fuel'];
						$sell_food_total += $arr['auction_sell_food'];

          }
          // Waren sind gesendet, jetzt nur noch nachricht schreiben und löschendatum festlegen
          elseif($arr['auction_delete_date']==0 && $arr['auction_sent']==1)
          {
              // Nachricht senden
              $msg = "Die Auktion vom ".date("d.m.Y H:i",$arr['auction_start']).", welche am ".date("d.m.Y H:i",$arr['auction_end'])." endete, ist erfolgreich abgelaufen und wird nach ".AUCTION_DELAY_TIME." Stunden gelöscht.\n\n";
              
              $msg .= "Das Handelsministerium";
              send_msg($arr['auction_user_id'],SHIP_MISC_MSG_CAT_ID,"Auktion abgelaufen",$msg);

              //Auktion noch eine zeit lang anzeigen, aber unkäuflich machen
              dbquery("
							UPDATE
								".$db_table['market_auction']."
							SET
								auction_buyable='0',
								auction_delete_date='".$delete_date."'
							WHERE
								auction_market_id='".$arr['auction_market_id']."';
						");
          }

          //Auktionen löschen, welche bereits abgelaufen sind und die Anzeigedauer auch hinter sich haben
          dbquery("
					DELETE FROM
						".$db_table['market_auction']."
					WHERE
						auction_market_id='".$arr['auction_market_id']."'
						AND auction_delete_date<='".time()."'
						AND auction_sent='1';");
      }
      
      // Verkaufte Rohstoffe in Config-DB speichern für Kurs berechnung
			// Titan
			dbquery("
			UPDATE
				".$db_table['config']."
			SET
				config_param1=config_param1+".$sell_metal_total."
			WHERE
				config_name='market_metal_factor'");		
				
			// Silizium
			dbquery("
			UPDATE
				".$db_table['config']."
			SET
				config_param1=config_param1+".$sell_crystal_total."
			WHERE
				config_name='market_crystal_factor'");	
				
			// PVC
			dbquery("
			UPDATE
				".$db_table['config']."
			SET
				config_param1=config_param1+".$sell_plastic_total."
			WHERE
				config_name='market_plastic_factor'");		
				
			// Tritium
			dbquery("
			UPDATE
				".$db_table['config']."
			SET
				config_param1=config_param1+".$sell_fuel_total."
			WHERE
				config_name='market_fuel_factor'");	
				
			// Food
			dbquery("
			UPDATE
				".$db_table['config']."
			SET
				config_param1=config_param1+".$sell_food_total."
			WHERE
				config_name='market_food_factor'");
    }
	}


	//
	// Markt Update (Verschicken von allen gekauften/ersteigerten Waren) und berechnen der Roshtoffkurse
	//
	function market_update()
	{
		global $db_table;
		$conf = get_all_config();

		//Auktionen Updaten (beenden)
		market_auction_update();

		// Ermittelt die Geschwindigkeit des Handelsschiffes
		$res=dbquery("
		SELECT
			ship_speed,
			ship_time2start,
			ship_time2land
		FROM
			".$db_table['ships']."
		WHERE
			ship_id='".MARKET_SHIP_ID."';");		
    if (mysql_num_rows($res) > 0)
    {
    	$arr=mysql_fetch_array($res);
    	$ship_speed = $arr['ship_speed'];
    	$ship_starttime = $arr['ship_time2start'];
    	$ship_landtime = $arr['ship_time2land'];
    }
    else
    {
    	$speed = 1;
    }

		//
		// Rohstoffe
		//
		$res=dbquery("
		SELECT
			*
		FROM
			".$db_table['market_ressource']."
		WHERE
			ressource_buyable='0';");    			
    if (mysql_num_rows($res) > 0)
    {
    	while($arr=mysql_fetch_array($res))
    	{
        //Flotte zum Verkäufer schicken
        $launchtime = time(); // Startzeit
        $duration = calcDistanceByPlanetId($arr['planet_id'],$arr['ressource_buyer_planet_id']) / $ship_speed * 3600 + $ship_starttime + $ship_landtime; // Dauer
        $landtime = $launchtime + $duration; // Landezeit

        dbquery("
				INSERT INTO ".$db_table['fleet']."
				(
					fleet_user_id,
					fleet_cell_from,
					fleet_cell_to,
					fleet_planet_from,
					fleet_planet_to,
					fleet_launchtime,
					fleet_landtime,
					fleet_action,
					fleet_res_metal,
					fleet_res_crystal,
					fleet_res_plastic,
					fleet_res_fuel,
					fleet_res_food
				)
				VALUES
				(
					'0',
					'0',
					'".$arr['cell_id']."',
					'0',
					'".$arr['planet_id']."',
					'".$launchtime."',
					'".$landtime."',
					'".FLEET_ACTION_RESS."',
					'".($arr['buy_metal'])."',
					'".($arr['buy_crystal'])."',
					'".($arr['buy_plastic'])."',
					'".($arr['buy_fuel'])."',
					'".($arr['buy_food'])."'
				);");
			
        dbquery("
				INSERT INTO
				".$db_table['fleet_ships']."
				(
					fs_fleet_id,
					fs_ship_id,
					fs_ship_cnt
				)
				VALUES
				(
					'".mysql_insert_id()."',
					'".MARKET_SHIP_ID."',
					'1'
				);");

        //Flotte zum Käufer schicken
        dbquery("
				INSERT INTO
				".$db_table['fleet']."
				(
					fleet_user_id,
					fleet_cell_from,
					fleet_cell_to,
					fleet_planet_from,
					fleet_planet_to,
					fleet_launchtime,
					fleet_landtime,
					fleet_action,
					fleet_res_metal,
					fleet_res_crystal,
					fleet_res_plastic,
					fleet_res_fuel,
					fleet_res_food
				)
				VALUES
				(
					'0',
					'0',
					'".$arr['ressource_buyer_cell_id']."',
					'0',
					'".$arr['ressource_buyer_planet_id']."',
					'".$launchtime."',
					'".$landtime."',
					'".FLEET_ACTION_RESS."',
					'".$arr['sell_metal']."',
					'".$arr['sell_crystal']."',
					'".$arr['sell_plastic']."',
					'".$arr['sell_fuel']."',
					'".$arr['sell_food']."'
				);");

        dbquery("
				INSERT INTO
				".$db_table['fleet_ships']."
				(
					fs_fleet_id,
					fs_ship_id,
					fs_ship_cnt
				)
				VALUES
				(
					'".mysql_insert_id()."',
					'".MARKET_SHIP_ID."',
					'1'
				);");

        //Angebot löschen
        dbquery("
				DELETE FROM
					".$db_table['market_ressource']."
				WHERE
					ressource_market_id='".$arr['ressource_market_id']."';");
    	}
  	}

		//
  	// Schiffe
  	//
  	$res=dbquery("
		SELECT
			*
		FROM
			".$db_table['market_ship']."
		WHERE
			ship_buyable='0';");
		if (mysql_num_rows($res)!=0)
		{
    	while($arr=mysql_fetch_array($res))
    	{
				//Flotte zum Verkäufer schicken
        $launchtime = time(); // Startzeit
        $duration = calcDistanceByPlanetId($arr['planet_id'],$arr['ship_buyer_planet_id']) / $ship_speed * 3600 + $ship_starttime + $ship_landtime; // Dauer
        $landtime = $launchtime + $duration; // Landezeit

				dbquery("
					INSERT INTO
					".$db_table['fleet']."
					(
						fleet_user_id,
						fleet_cell_from,
						fleet_cell_to,
						fleet_planet_from,
						fleet_planet_to,
						fleet_launchtime,
						fleet_landtime,
						fleet_action,
						fleet_res_metal,
						fleet_res_crystal,
						fleet_res_plastic,
						fleet_res_fuel,
						fleet_res_food
					)
					VALUES
					(
						'0',
						'0',
						'".$arr['cell_id']."',
						'0',
						'".$arr['planet_id']."',
						'".$launchtime."',
						'".$landtime."',
						'".FLEET_ACTION_RESS."',
						'".$arr['ship_costs_metal']."',
						'".$arr['ship_costs_crystal']."',
						'".$arr['ship_costs_plastic']."',
						'".$arr['ship_costs_fuel']."',
						'".$arr['ship_costs_food']."'
					);");

				dbquery("
				INSERT INTO
				".$db_table['fleet_ships']."
				(
					fs_fleet_id,
					fs_ship_id,
					fs_ship_cnt
				)
				VALUES
				(
					'".mysql_insert_id()."',
					'".MARKET_SHIP_ID."',
					'1'
				);");

				//Flotte zum Käufer schicken
				dbquery("
				INSERT INTO
				".$db_table['fleet']."
				(
					fleet_user_id,
					fleet_cell_from,
					fleet_cell_to,
					fleet_planet_from,
					fleet_planet_to,
					fleet_launchtime,
					fleet_landtime,
					fleet_action,
					fleet_res_metal,
					fleet_res_crystal,
					fleet_res_plastic,
					fleet_res_fuel,
					fleet_res_food
				)
				VALUES
				(
					'0',
					'0',
					'".$arr['ship_buyer_cell_id']."',
					'0',
					'".$arr['ship_buyer_planet_id']."',
					'".$launchtime."',
					'".$landtime."',
					'".FLEET_ACTION_SHIP."',
					'0',
					'0',
					'0',
					'0',
					'0'
				);");

				dbquery("
				INSERT INTO
				".$db_table['fleet_ships']."
				(
					fs_fleet_id,
					fs_ship_id,
					fs_ship_cnt
				)
				VALUES
				(
					'".mysql_insert_id()."',
					'".$arr['ship_id']."',
					'".$arr['ship_count']."'
				);");

				//Angebot löschen
				dbquery("
				DELETE FROM
					".$db_table['market_ship']."
				WHERE
					ship_market_id='".$arr['ship_market_id']."';");
    	}
		}

		//
    //Auktionen
    //
    $res=dbquery("
		SELECT
			*
		FROM
			".$db_table['market_auction']."
		WHERE
			auction_buyable='0'
			AND auction_sent='0'
			AND auction_delete_date>'".time()."';");
    if (mysql_num_rows($res)!=0)
    {
      while($arr=mysql_fetch_array($res))
      {
        //Flotte zum verkäufer der auktion schicken
        $launchtime = time(); // Startzeit
        $duration = calcDistanceByPlanetId($arr['auction_planet_id'],$arr['auction_current_buyer_planet_id']) / $ship_speed * 3600 + $ship_starttime + $ship_landtime; // Dauer
        $landtime = $launchtime + $duration; // Landezeit

        dbquery("
				INSERT INTO ".$db_table['fleet']."
				(
					fleet_user_id,
					fleet_cell_from,
					fleet_cell_to,
					fleet_planet_from,
					fleet_planet_to,
					fleet_launchtime,
					fleet_landtime,
					fleet_action,
					fleet_res_metal,
					fleet_res_crystal,
					fleet_res_plastic,
					fleet_res_fuel,
					fleet_res_food
				)
				VALUES
				(
					'0',
					'0',
					'".$arr['auction_cell_id']."',
					'0',
					'".$arr['auction_planet_id']."',
					'".$launchtime."',
					'".$landtime."',
					'".FLEET_ACTION_RESS."',
					'".$arr['auction_buy_metal']."',
					'".$arr['auction_buy_crystal']."',
					'".$arr['auction_buy_plastic']."',
					'".$arr['auction_buy_fuel']."',
					'".$arr['auction_buy_food']."'
				);");

        dbquery("
				INSERT INTO
				".$db_table['fleet_ships']."
				(
					fs_fleet_id,
					fs_ship_id,
					fs_ship_cnt
				)
				VALUES
				(
					'".mysql_insert_id()."',
					'".MARKET_SHIP_ID."',
					'1'
				);");

        

        //Flotte zum hochstbietenden schicken (Käufer)
        dbquery("
				INSERT INTO ".$db_table['fleet']."
				(
					fleet_user_id,
					fleet_cell_from,
					fleet_cell_to,
					fleet_planet_from,
					fleet_planet_to,
					fleet_launchtime,
					fleet_landtime,
					fleet_action,
					fleet_res_metal,
					fleet_res_crystal,
					fleet_res_plastic,
					fleet_res_fuel,
					fleet_res_food
				)
				VALUES
				(
					'0',
					'0',
					'".$arr['auction_current_buyer_cell_id']."',
					'0',
					'".$arr['auction_current_buyer_planet_id']."',
					'".$launchtime."',
					'".$landtime."',
					'".FLEET_ACTION_RESS."',
					'".$arr['auction_sell_metal']."',
					'".$arr['auction_sell_crystal']."',
					'".$arr['auction_sell_plastic']."',
					'".$arr['auction_sell_fuel']."',
					'".$arr['auction_sell_food']."'
				);");


        // Schickt gekaufte Rohstoffe mit Handelsschiff
        dbquery("
				INSERT INTO
				".$db_table['fleet_ships']."
				(
					fs_fleet_id,
					fs_ship_id,
					fs_ship_cnt
				)
				VALUES
				(
					'".mysql_insert_id()."',
					'".MARKET_SHIP_ID."',
					'1'
				);");
        

        //Waren als "gesendet" markieren
        dbquery("
				UPDATE
					".$db_table['market_auction']."
				SET
					auction_sent='1'
				WHERE
					auction_market_id='".$arr['auction_market_id']."';");

      }
    }


		//
		// Rohstoffkurs Berechnung & Update (Der Schiffshandel beeinflusst die Kurse nicht!)
		//

		// Summiert alle bisher verkauften Rohstoffe
		$sell_ress_total = $conf['market_metal_factor']['p1'] + $conf['market_metal_factor']['p2']
											+ $conf['market_crystal_factor']['p1'] + $conf['market_crystal_factor']['p2']
											+ $conf['market_plastic_factor']['p1'] + $conf['market_plastic_factor']['p2']
											+ $conf['market_fuel_factor']['p1'] + $conf['market_fuel_factor']['p2']
											+ $conf['market_food_factor']['p1'] + $conf['market_food_factor']['p2'];
											
		// Berechnet die neuen Kurse -> Kurs = Total Verkaufte Rohstoffe / Total verkaufter Rohstoffe je Typ
		$metal_tax = round($sell_ress_total / ($conf['market_metal_factor']['p1'] + $conf['market_metal_factor']['p2']),2);
		$crystal_tax = round($sell_ress_total / ($conf['market_crystal_factor']['p1'] + $conf['market_crystal_factor']['p2']),2);
		$plastic_tax = round($sell_ress_total / ($conf['market_plastic_factor']['p1'] + $conf['market_plastic_factor']['p2']),2);
		$fuel_tax = round($sell_ress_total / ($conf['market_fuel_factor']['p1'] + $conf['market_fuel_factor']['p2']),2);
		$food_tax = round($sell_ress_total / ($conf['market_food_factor']['p1'] + $conf['market_food_factor']['p2']),2);

		// Update der Kurse
		// Titan
		dbquery("
		UPDATE
			".$db_table['config']."
		SET
			config_value='".$metal_tax."'
		WHERE
			config_name='market_metal_factor'");		
			
		// Silizium
		dbquery("
		UPDATE
			".$db_table['config']."
		SET
			config_value='".$crystal_tax."'
		WHERE
			config_name='market_crystal_factor'");	
			
		// PVC
		dbquery("
		UPDATE
			".$db_table['config']."
		SET
			config_value='".$plastic_tax."'
		WHERE
			config_name='market_plastic_factor'");		
			
		// Tritium
		dbquery("
		UPDATE
			".$db_table['config']."
		SET
			config_value='".$fuel_tax."'
		WHERE
			config_name='market_fuel_factor'");	
			
		// Food
		dbquery("
		UPDATE
			".$db_table['config']."
		SET
			config_value='".$food_tax."'
		WHERE
			config_name='market_food_factor'");		

    //Arrays löschen (Speicher freigeben)
		mysql_free_result($res);
		unset($arr);
	}

    function send_mail($preview,$adress,$topic,$text,$style,$align,$force=0)
    {
        global $db_table;
        $conf = get_all_config();

        if($style=="")
			$style=$conf['default_css_style']['v'];

		if($align=="")
			$align="center";

		$adress = strtolower($adress); //wandelt email adresse in kleinbuchstaben um
		//$text = nl2br($text);
        $email_header = "From: Escape to Andromeda<etoa@orion.etoa.net>\n";
        $email_header .= "Reply-To: mail@etoa.net\n";
        $email_header .= "X-Mailer: PHP/" . phpversion(). "\n";
        $email_header .= "X-Sender-IP: ".$_SERVER['REMOTE_ADDR']."\n";
				$email_header .= "Content-Type: text/plain; Charset=utf-8\r\n";         
        
        //$email_header .= "Content-type: text/html\n";
        //$email_header .= "Content-Style-Type: text/css\n";

		$round_url="http://round1.etoa.net/";
        $logo_path="".$round_url."images/Etoa-Gaming-Logo.gif";
        $logo_width="120";
        $logo_height="120";

/*
        $email_text = "
        <html>
        	<head>
				<link rel=\"stylesheet\" type=\"text/css\" href=\"".$round_url."".$style."/style.css\" />
				<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\" />
        	</head>
        	<body>
        	<center>
        		<div align=\"center\" style=\"width:600px\">
        			<img src=\"".$round_url."images/game_logo.gif\">
        			<hr size=2 width=\"100%\">
        			<br>";

        $email_text .= "<div class=\"infoboxtitle\">$topic</div>
        				<div class=\"infoboxcontent\"><div align=\"".$align."\">".$text."</div></div><br>";

		$email_text .= "<hr size=2 width=\"100%\">
					<br><br>
					<table border=\"0\" style=\"font-size: 10pt;\">
						<tr>
							<td><img src=\"".$logo_path."\" width=\"$logo_width\" height=\"$logo_height\"></td>
							<td style=\"text-align:center\"><a href=\"http://www.etoa.ch\">Escape to Andromeda - Das Sci-Fi Browsergame</a><br>Powered and Copyright &copy; by EtoA-Gaming 2006<br><br>Kontakt: <a href=\"".$round_url."show.php?index=contact\">Team</a> / <a href=\"http://etoa.dysign.ch/forum/index.php\">Forum</a> / <a href=\"mailto:mail@etoa.ch\">mail@etoa.ch</a></td>
							<td><img src=\"".$logo_path."\" width=\"$logo_width\" height=\"$logo_height\"></td>
						</tr>
					</table>
				</div>
			</center>
			</body>

        </html>
        ";
*/
		$email_text = $text."		
		
Escape to Andromeda - Das Sci-Fi Browsergame - www.etoa.ch
Powered and Copyright (C) by EtoA-Gaming 2007
Kontakt: mail@etoa.ch
Forum: http://www.etoa.ch/forum";
		
		if($preview=="" || $preview==0)
		{
			// queue disabled
        	mail($adress,$topic,$email_text,$email_header) or die("Die Mail konnte nicht versendet werden.");

        	//Arrays löschen (Speicher freigeben)
        	unset($email_text);
        	unset($email_header);
        }
        else
        {
        	$email_text .= "<center><br><br><a href=\"javascript:window.close();\">Vorschau Schliessen</a></center>";

			$text = ereg_replace("(\r\n|\n|\r)", "", $email_text);
            echo "
            <script type=\"text/javascript\">
              Preview = window.open(\"about:blank\",\"test\");
              Preview.focus();
              Preview.document.write('$text');

            </script>";

        }

    }


	/**
	* Verknüpft das Wurmloch einer Zelle mit einem neuen Wurmloch
	*
	* @param int $w1 Ursprüngliche Startzelle
	* @param int $w2 Neue Zielzelle
	* @tables space_cells
	* @author MrCage
	*/
	function change_wormhole($w1,$w2)
	{
		global $db_table;

		$res=dbquery("
			SELECT
				cell_wormhole_id
			FROM
				".$db_table['space_cells']."
			WHERE
				cell_id='".$w1."';
		");
		$arr=mysql_fetch_array($res);
		// Prüfen ob Wurmloch sich geändert hat und ob nicht die aktuelle Zelle gewählt wurde
		if ($arr['cell_wormhole_id']!=$w2 && $w1!=$w2)
		{
			// Abfragen mit wem das neue Ziel verknüpft ist
			$wres=dbquery("
				SELECT
					cell_wormhole_id
				FROM
					".$db_table['space_cells']."
				WHERE
					cell_id='".$w2."';
			");
			$warr=mysql_fetch_array($wres);
			dbquery("
				UPDATE
					".$db_table['space_cells']."
				SET
					cell_wormhole_id='".$arr['cell_wormhole_id']."',
					cell_wormhole_changed='".time()."'
				WHERE
					cell_id='".$warr['cell_wormhole_id']."';
			");
			dbquery("
				UPDATE
					".$db_table['space_cells']."
				SET
					cell_wormhole_id='".$warr['cell_wormhole_id']."',
					cell_wormhole_changed='".time()."'
				WHERE
					cell_id='".$arr['cell_wormhole_id']."';
			");
			dbquery("
				UPDATE
					".$db_table['space_cells']."
				SET
					cell_wormhole_id='".$w2."',
					cell_wormhole_changed='".time()."'
				WHERE cell_id='".$w1."';
			");
			dbquery("
				UPDATE
					".$db_table['space_cells']."
				SET
					cell_wormhole_id='".$w1."',
					cell_wormhole_changed='".time()."'
				WHERE
					cell_id='".$w2."';
			");
		}
	}

	/**
	* Vertauscht zufällig mehrere Wurmlöcher miteinander
	*
	* @param int $cnt Anzahl zu ändernder Wurmlöcher
	* @athor MrCage
	*/
	function whupdate()
	{
		global $db_table;

		// Löschen
		$deleted = array();
		$delcnt=0;
		$res=dbquery("
			SELECT
				cell_id,
				cell_wormhole_id
			FROM
				".$db_table['space_cells']."
			WHERE
				cell_wormhole_id>'0'
				AND cell_wormhole_changed<".(time()-WH_UPDATE_AFFECT_TIME)."
			ORDER BY
				RAND()
			LIMIT ".WH_UPDATE_AFFECT_CNT.";
		");
		if (mysql_num_rows($res)>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
				if (!in_array($arr['cell_id'],$deleted) && !in_array($arr['cell_wormhole_id'],$deleted))
				{
					dbquery("
						UPDATE
							".$db_table['space_cells']."
						SET
							cell_wormhole_id='0',
							cell_wormhole_changed='0'
						WHERE
							cell_id='".$arr['cell_id']."';
					");
					dbquery("
						UPDATE
							".$db_table['space_cells']."
						SET
							cell_wormhole_id='0',
							cell_wormhole_changed='0'
						WHERE cell_id='".$arr['cell_wormhole_id']."';
					");
					array_push($deleted,$arr['cell_id']);
					array_push($deleted,$arr['cell_wormhole_id']);
					$delcnt++;
				}
			}
		}

		// Neue erstellen
		$res=dbquery("
			SELECT
				cell_id
			FROM
				".$db_table['space_cells']."
			WHERE
				cell_wormhole_id='0'
				AND cell_nebula='0'
				AND cell_asteroid='0'
				AND cell_solsys_solsys_sol_type='0'
				AND cell_solsys_num_planets='0'
			ORDER BY
				RAND()
			LIMIT ".($delcnt*2).";
		");
		while ($arr1=mysql_fetch_row($res))
		{
			$arr2=mysql_fetch_row($res);
			dbquery("
				UPDATE
					".$db_table['space_cells']."
				SET
					cell_wormhole_id='".$arr2[0]."',
					cell_wormhole_changed='".time()."'
				WHERE
					cell_id='".$arr1[0]."' ;
			");
			dbquery("
				UPDATE
					".$db_table['space_cells']."
				SET
					cell_wormhole_id='".$arr1[0]."',
					cell_wormhole_changed='".time()."'
				WHERE
					cell_id='".$arr2[0]."' ;
			");
		}
	}

	/**
	* Fehlermeldungs-Box anzeigen
	*
	* @param string $title Titel
	* @param string $text Text
	* @param int $return Bei 1 String zurückgeben statt ausgeben
	* @athor MrCage
	*/
	function errBox($title,$text,$return=0)
	{
		$title_str="<br/><div style=\"font-family:arial,helvetica;padding:5px;margin:0px auto; width:600px;background:#225;font-weight:bold;border:1px solid black;\">".text2html($title)."</div>";
		$text_str="<div style=\"font-family:arial,helvetica;padding:5px;margin:0px auto; width:600px;;background:#223;border:1px solid black;\">".text2html($text)."</div><br/>";
		if ($return==1)
		{
			return $title_str.$text_str;
		}
		else
		{
			echo $title_str.$text_str;
		}
	}

	/**
	* Schiffe zur Schiffsliste hinzufügen
	*
	* @param int $planet Planet-ID
	* @param int $user User-ID
	* @param int $ship Schiff-ID
	* @param int $cnt Anzahl
	* @author MrCage
	*/
	function shiplistAdd($planet,$user,$ship,$cnt)
	{
		global $db_table;
		$res=dbquery("
			SELECT
				shiplist_id
			FROM
				".$db_table['shiplist']."
			WHERE
				shiplist_user_id='".$user."'
				AND shiplist_planet_id='".$planet."'
				AND shiplist_ship_id='".$ship."';
		");
		if (mysql_num_rows($res)>0)
		{
			dbquery("
				UPDATE
					".$db_table['shiplist']."
				SET
					shiplist_count=shiplist_count+".max($cnt,0)."
				WHERE
					shiplist_user_id='".$user."'
					AND shiplist_planet_id='".$planet."'
					AND shiplist_ship_id='".$ship."';
			");
		}
		else
		{
			dbquery("
				INSERT INTO
				".$db_table['shiplist']."
				(
					shiplist_user_id,
					shiplist_planet_id,
					shiplist_ship_id,
					shiplist_count
				)
				VALUES
				(
					'".$user."',
					'".$planet."',
					'".$ship."',
					'".max($cnt,0)."'
				);
			");
		}
	}
	
	
	/**
	* Verteidigungsanlagen zur Anlagenliste hinzufügen
	*
	* @param int $planet Planet-ID
	* @param int $user User-ID
	* @param int $def Schiff-ID
	* @param int $cnt Anzahl
	* @author MrCage
	*/
	function deflistAdd($planet,$user,$def,$cnt)
	{
		global $db_table;
		$res=dbquery("
			SELECT
				deflist_id
			FROM
				".$db_table['deflist']."
			WHERE
				deflist_user_id='".$user."'
				AND deflist_planet_id='".$planet."'
				AND deflist_def_id='".$def."';
		");
		if (mysql_num_rows($res)>0)
		{
			dbquery("
				UPDATE
					".$db_table['deflist']."
				SET
					deflist_count=deflist_count+".max($cnt,0)."
				WHERE
					deflist_user_id='".$user."'
					AND deflist_planet_id='".$planet."'
					AND deflist_def_id='".$def."';
			");
		}
		else
		{
			dbquery("
				INSERT INTO
				".$db_table['deflist']."
				(
					deflist_user_id,
					deflist_planet_id,
					deflist_def_id,
					deflist_count
				)
				VALUES
				(
					'".$user."',
					'".$planet."',
					'".$def."',
					'".max($cnt,0)."'
				);
			");
		}
	}
	
	/**
	* Raketen zur Raketenliste hinzufügen
	*
	* @param int $planet Planet-ID
	* @param int $user User-ID
	* @param int $ship Schiff-ID
	* @param int $cnt Anzahl
	* @author MrCage
	*/
	function missilelistAdd($planet,$user,$ship,$cnt)
	{
		global $db_table;
		$res=dbquery("
			SELECT
				missilelist_id
			FROM
				missilelist
			WHERE
				missilelist_user_id='".$user."'
				AND missilelist_planet_id='".$planet."'
				AND missilelist_missile_id='".$ship."';
		");
		if (mysql_num_rows($res)>0)
		{
			dbquery("
				UPDATE
					missilelist
				SET
					missilelist_count=missilelist_count+".max($cnt,0)."
				WHERE
					missilelist_user_id='".$user."'
					AND missilelist_planet_id='".$planet."'
					AND missilelist_missile_id='".$ship."';
			");
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
				)
				VALUES
				(
					'".$user."',
					'".$planet."',
					'".$ship."',
					'".max($cnt,0)."'
				);
			");
		}
	}	
	
	
	/**
	* Fügt eine Mail Nachricht der Mail-Warteschlange hinzu
	*
	* @param string $msg_to Empfänger
	* @param string $msg_subject Titel
	* @param string $msg_text Text
	* @param string $msg_header Header
	* @author MrCage
	*/
	function mail_queue($msg_to,$msg_subject,$msg_text,$msg_header)
	{
		global $db_table;
		dbquery("
			INSERT INTO
			".$db_table['mail_queue']."
			(
				msg_to,
				msg_subject,
				msg_text,
				msg_header,
				msg_timestamp
			)
			VALUES
			(
				'".$msg_to."',
				'".addslashes($msg_subject)."',
				'".addslashes($msg_text)."',
				'".addslashes($msg_header)."',
				'".time()."'
			);
		");
		return true;
	}

	/**
	* Sendet $cnt Anzahl Nachrichten, die sich in der Mail-Warteschlange befinden
	*
	* @param int $cnt Anzahl Nachrichten, Standard: 1
	* @author MrCage
	*/
	function mail_queue_send($cnt=1)
	{
		global $db_table;
		$res=dbquery("
			SELECT
				*
			FROM
				".$db_table['mail_queue']."
			ORDER BY
				msg_timestamp ASC
			LIMIT $cnt
		");
		$nr = mysql_num_rows($res);
		if ($nr>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
				mail($arr['msg_to'],stripslashes($arr['msg_subject']),stripslashes($arr['msg_text']),stripslashes($arr['msg_header'])) or die("Mail problem!\n");
				
				dbquery("
					DELETE FROM
						".$db_table['mail_queue']."
					WHERE
						msg_id='".$arr['msg_id']."';
				");
			}
		}
		return $nr;
	}

	/**
	* Zeigt ein Avatarbild an
	*/
	function show_avatar($avatar=BOARD_DEFAULT_IMAGE)
	{
		if ($avatar=="") $avatar=BOARD_DEFAULT_IMAGE;
		//echo "<div style=\"background:url('images/frame.gif') no-repeat;padding:8px;\">";
		echo "<div style=\"padding:8px;\">";
		echo "<img id=\"avatar\" src=\"".BOARD_AVATAR_DIR."/".$avatar."\" alt=\"avatar\" style=\"width:64px;height:64px;\"/></div>";
	}

	/**
	* Ressourcen-Update
	*/
	function updateAllEconomy()
	{
		global $conf,$db_table;

		// User-Planeten aktualisieren
		$res=dbquery("
			SELECT
				planet_id
			FROM
				".$db_table['planets']."
			WHERE
				planet_user_id>'0'
		");
		$cnt=mysql_num_rows($res);
		if ($cnt>0)
		{
			while ($arr=mysql_fetch_row($res))
			{
				$p = new Planet($arr[0]);
				$p->updateEconomy();
			}
		}
		return $cnt;

		//Arrays löschen (Speicher freigeben)
		mysql_free_result($res);
		unset($arr);
		unset($p);
	}

	/**
	* Objekt-Update
	*/
	function updateAllObjects()
	{
		global $conf,$db_table;

		// User-Planeten aktualisieren
		$res=dbquery("
			SELECT
				planet_id
			FROM
				".$db_table['planets']."
			WHERE
				planet_user_id>'0'
		");
		$cnt=mysql_num_rows($res);
		if ($cnt>0)
		{
			while ($arr=mysql_fetch_row($res))
			{
				$p = new Planet($arr[0]);
				$p->update();
			}
		}
		return $cnt;

		//Arrays löschen (Speicher freigeben)
		mysql_free_result($res);
		unset($arr);
		unset($p);
	}

	/**
	* Gasplaneten-Update
	* @author MrCage
	*/
	function updateGasPlanets()
	{
		global $conf,$db_table;

		// Gasplanet-Update
		$res=dbquery("
			SELECT
				planet_id,
				planet_res_fuel,
				planet_fields,
				planet_last_updated
			FROM
				".$db_table['planets']."
			WHERE
				planet_type_id='".$conf['gasplanet']['v']."';
		");
		$cnt=mysql_num_rows($res);
		if ($cnt>0)
		{
			$time = time();
			while ($arr=mysql_fetch_array($res))
			{
				if ($arr['planet_last_updated']==0) $arr['planet_last_updated']=$time;
				$fuel = min((max($time-$arr['planet_last_updated'],0)*$conf['gasplanet']['p1']/3600)+$arr['planet_res_fuel'],$conf['gasplanet']['p2']*$arr['planet_fields']);

				dbquery("
					UPDATE
					".$db_table['planets']."
					SET
						planet_res_fuel='".$fuel."',
						planet_last_updated='".$time."'
					WHERE
						planet_id='".$arr['planet_id']."';
				");
			}
		}
		return $cnt;
	}

	/**
	* Flotten-Update
	*
	* @author MrCage
	*/
	function updateAllFleet()
	{
		global $conf;
		global $db_table;

		$sql = "
			SELECT
				*
			FROM
				".$db_table['fleet']."
			WHERE
				fleet_updating='0'
				AND fleet_landtime<'".time()."'
			ORDER BY
				fleet_landtime ASC;
		";
		$res=dbquery($sql);
		$nr = mysql_num_rows($res);
		$ids=array();
		if ($nr>0)
		{
			require_once(GAME_ROOT_DIR."/inc/fleet_action.inc.php");
			require_once(GAME_ROOT_DIR."/inc/fleet_update.inc.php");
			while ($arr=mysql_fetch_array($res))
			{
				update_fleet($arr,0);
				array_push($ids,$arr['fleet_id']);
			}
		}
		return array($nr,$ids);

		//Arrays löschen (Speicher freigeben)
		mysql_free_result($res);
		unset($arr);
		unset($ids);

	}

	/**
	* Übrnimmt einen Planeten (Invasion)
	*
	* @param int $planet_id Planet ID
	* @param int $new_user_id User ID des 'Übernehmers'
	* @athor Lamborghini
	*/
	function invasion_planet($planet_id,$new_user_id)
	{
		global $db_table;

        // Planet übernehmen
        dbquery("
			UPDATE
				".$db_table['planets']."
			SET
				planet_user_id='".$new_user_id."',
				planet_name='Unbenannt'
			WHERE
				planet_id='".$planet_id."';
		");


        // Gebäude übernehmen
        dbquery("
			UPDATE
				".$db_table['buildlist']."
			SET
				buildlist_user_id='".$new_user_id."'
			WHERE
				buildlist_planet_id='".$planet_id."';
		");


    // Bestehende Schiffs-Einträge löschen
    dbquery("
			DELETE FROM
				".$db_table['shiplist']."
			WHERE
				shiplist_planet_id='".$planet_id."';
		");
    dbquery("
			DELETE FROM
				ship_queue
			WHERE
				queue_planet_id='".$planet_id."';
		");		
		


    // Bestehende Verteidigungs-Einträge löschen
    dbquery("
			DELETE FROM
				".$db_table['deflist']."
			WHERE
				deflist_planet_id='".$planet_id."';
		");
    dbquery("
			DELETE FROM
				def_queue
			WHERE
				queue_planet_id='".$planet_id."';
		");

	}


   function cut_word($txt, $where, $br=0) {
       if (empty($txt)) return false;
       for ($c = 0, $a = 0, $g = 0; $c<strlen($txt); $c++) {
           $d[$c+$g]=$txt[$c];
           if ($txt[$c]!=" ") $a++;
           else if ($txt[$c]==" ") $a = 0;
           if ($a==$where) {
           $g++;
           if ($br==0)
           	$d[$c+$g]="\n";
           else
           	$d[$c+$g]="<br/>";

           $a = 0;
           }
       }
       return implode("", $d);
   }


	function timerStart()
	{
		// Renderzeit-Start festlegen
		$render_time = explode(" ",microtime());
		return $render_time[1]+$render_time[0];
	}

	function timerStop($starttime)
	{
		// Renderzeit
		$render_time = explode(" ",microtime());
		$rtime = $render_time[1]+$render_time[0]-$starttime;
		return round($rtime,3);
	}

  function encryptCaptchaString($string, $key)
  {
	   $result = '';
	   for($i=0; $i<strlen($string); $i++)
	   {
	      $char = substr($string, $i, 1);
	      $keychar = substr($key, ($i % strlen($key))-1, 1);
	      $char = chr(ord($char)+ord($keychar));
	      $result.=$char;
	   }
   	$result = base64_encode($result);
   	$result  = str_replace("=", "", $result);
   	return $result;
  }


	/**
	* Resizes a jpeg image and save it to a given filename
	*
	* No return value
	*/
	function resizeImage($fileFrom, $fileTo, $newMaxWidth = 0, $newMaxHeight = 0, $type="jpeg" ) 
	{
		if ($type=='png')
		{
			$imgfrom = "ImageCreateFromPNG";
			$imgsave = "ImagePNG";
		}
		elseif ($type=='gif')
		{
			$imgfrom = "ImageCreateFromGIF";
			$imgsave = "ImageGIF";
			$quality=0;
		}
		else
		{
			$imgfrom = "ImageCreateFromJPEG";
			$imgsave = "ImageJPEG";
			$quality=100;
		}
		if ($img = $imgfrom($fileFrom)) 
		{
			$width = ImageSX($img);
			$height = ImageSY($img);
			$resize = FALSE;
			$handle;
			if ($width > $newMaxWidth) {
				$newWidth = $newMaxWidth;
				$newHeight = intval($height * ($newWidth / $width));
				if ($newHeight > $newMaxHeight) {
					$newHeight = $newMaxHeight;
					$newWidth = intval($width * ($newHeight / $height));
				}
				$resize = TRUE;
			} else if($height > $newMaxHeight) {
				$newHeight = $newMaxHeight;
				$newWidth = intval($width * ($newHeight / $height));
				$resize = TRUE;
			}
			
			if ($resize) 
			{
				// resize using appropriate function
				if (GD_VERSION == 2) 
				{
					$imageId =  ImageCreateTrueColor ( $newWidth , $newHeight );
					ImageCopyResampled($imageId, $img, 0,0,0,0, $newWidth, $newHeight, $width, $height);
				}
				else 
				{
					$imageId = ImageCreate($newWidth , $newHeight);
					ImageCopyResized($imageId, $img, 0,0,0,0, $newWidth, $newHeight, $width, $height);
				}
				$handle = $imageId;
				// free original image
				ImageDestroy($img);
			} else {
				$handle = $img;
			}	
   		$imgsave($handle, $fileTo, $quality);
			ImageDestroy($handle);
			return true;
		}
		return false;
	}

	/**
	* Checks and handles missile actions
	*/
	function check_missiles()
	{
		$res = dbquery("
		SELECT
			flight_id
		FROM
			missile_flights
		WHERE
			flight_landtime < ".time()."
		ORDER BY
			flight_landtime ASC
		;");
		if (mysql_num_rows($res)>0)
		{
			include("inc/missiles.inc.php");
			while($arr=mysql_fetch_array($res))
			{
				missile_battle($arr['flight_id']);				
			}
		}		
	}

	/**
	* Calculates costs per level for a given building costs array
	*
	* @param array Array of db cost values
	* @param int Level
	* @return array Array of calculated costs
	*
	*/	
	function calcBuildingCosts($buildingArray, $level)
	{
		$bc=array();
		$bc['metal'] = $buildingArray['building_costs_metal'] * pow($buildingArray['building_build_costs_factor'],$level);
		$bc['crystal'] = $buildingArray['building_costs_crystal'] * pow($buildingArray['building_build_costs_factor'],$level);
		$bc['plastic'] = $buildingArray['building_costs_plastic'] * pow($buildingArray['building_build_costs_factor'],$level);
		$bc['fuel'] = $buildingArray['building_costs_fuel'] * pow($buildingArray['building_build_costs_factor'],$level);
		$bc['food'] = $buildingArray['building_costs_food'] * pow($buildingArray['building_build_costs_factor'],$level);
		$bc['power'] = $buildingArray['building_costs_power'] * pow($buildingArray['building_build_costs_factor'],$level);
		return $bc;
	}
	
	/**
	* Calculates costs per level for a given technology costs array
	*
	* @param array Array of db cost values
	* @param int Level
	* @return array Array of calculated costs
	*
	*/
	function calcTechCosts($arr,$l)
	{

		// Baukostenberechnung          Baukosten = Grundkosten * (Kostenfaktor ^ Ausbaustufe)
		$bc = array();
		$bc['metal'] = $arr['tech_costs_metal'] * pow($arr['tech_build_costs_factor'],$l);
		$bc['crystal'] = $arr['tech_costs_crystal'] * pow($arr['tech_build_costs_factor'],$l);
		$bc['plastic'] = $arr['tech_costs_plastic'] * pow($arr['tech_build_costs_factor'],$l);
		$bc['fuel'] = $arr['tech_costs_fuel'] * pow($arr['tech_build_costs_factor'],$l);
		$bc['food'] = $arr['tech_costs_food'] * pow($arr['tech_build_costs_factor'],$l);
		return $bc;
	}

	/**
	* Formates a given number of bytes to a humand readable string of Bytes, Kilobytes, 
	* Megabytes, Gigabytes or Terabytes and rounds it to three digits
	* 
	* @param int Number of bytes
	* @return string Well-formated byte number
	* @author Nicolas Perrenoud
	*/
	function byte_format($s)
	{
		if ($s>=1099511627776)
		{
			return round($s/1099511627776,3)." TB";
		}
		if ($s>=1073741824)
		{
			return round($s/1073741824,3)." GB";
		}
		elseif($s>=1048576)
		{
			return round($s/1048576,3)." MB";
		}
		elseif($s>=1024)
		{
			return round($s/1024,3)." KB";
		}
		else
		{
			return round($s)." B";
		}
	}	
	
	function calcDistance($sx1,$sy1,$cx1,$cy1,$pp1,$sx2,$sy2,$cx2,$cy2,$pp2)
	{
		global $conf;
		// Calc time and distance
		$nx=$conf['num_of_cells']['p1'];		// Anzahl Zellen Y
		$ny=$conf['num_of_cells']['p2'];		// Anzahl Zellen X
		$ae=$conf['cell_length']['v'];			// Länge vom Solsys in AE
		$np=$conf['num_planets']['p2'];			// Max. Planeten im Solsys
		$dx = abs(((($sx2-1) * $nx) + $cx2) - ((($sx1-1) * $nx) + $cx1));
		$dy = abs(((($sy2-1) * $nx) + $cy2) - ((($sy1-1) * $nx) + $cy1));
		$sd = sqrt(pow($dx,2)+pow($dy,2));			// Distanze zwischen den beiden Zellen
		$sae = $sd * $ae;											// Distance in AE units
		if ($sx1==$sx2 && $sy1==$sy2 && $cx1==$cx2 && $cy1=$cy2)
			$ps = abs($pp2-$pp1)*$ae/4/$np;				// Planetendistanz wenn sie im selben Solsys sind
		else
			$ps = ($ae/2) - (($pp2)*$ae/4/$np);	// Planetendistanz wenn sie nicht im selben Solsys sind
		$ssae = $sae + $ps;
		return $ssae;	
	}		

	function calcDistanceByPlanetId($pid1,$pid2)
	{
		$c1 = getCoordsByPlanetId($pid1);
		$c2 = getCoordsByPlanetId($pid2);
		return calcDistance($c1['sx'],$c1['sy'],$c1['cx'],$c1['cy'],$c1['pp'],$c2['sx'],$c2['sy'],$c2['cx'],$c2['cy'],$c2['pp']);
	}

	
	function getCoordsByPlanetId($id)
	{		
		$coords = array();
		$res = dbquery("
		SELECT
			cell_sx,
			cell_sy,
			cell_cx,
			cell_cy,
			cell_id,
			planet_id,
			planet_solsys_pos,
			planet_user_id
		FROM
			planets
		INNER JOIN
			space_cells
			ON planet_solsys_id=cell_id
			AND planet_id='".$id."'				
		");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			$coords['id'] = $arr['cell_id'];
			$coords['sx'] = $arr['cell_sx'];
			$coords['sy'] = $arr['cell_sy'];
			$coords['cx'] = $arr['cell_cx'];
			$coords['cy'] = $arr['cell_cy'];
			$coords['pp'] = $arr['planet_solsys_pos'];			
			$coords['user_id'] = $arr['planet_user_id'];
			$coords['planet_id'] = $id;
			return $coords;
		}		
		return false;		
	}
	
	/**
	* Generates a password using the password string, a user based seed, and a system wide seed
	*
	* @param string Password from user
	* @param string User's salt (e.g. registration date or id)
	*
	*/
	function pw_salt($pw,$seed=0)
	{
		return md5($pw.$seed.PASSWORD_SALT).md5(PASSWORD_SALT.$seed.$pw);
	}
	
	/**
	* Displays a button which opens an abuse report dialog when clicked
	*
	* @param string Preselected category
	* @param string Title of the button
	* @param int Concerning user id
	* @param int Concerning alliance id
	*/
	function abuse_button($cat,$title="Missbrauch",$uid=0,$aid=0)
	{		
		echo "<input type=\"button\" value=\"".$title."\" onclick=\"window.open('show.php?page=abuse&ext=1&cat=".$cat."&uid=".$uid."&$aid=".$aid."','abuse','width=700,height=470,status=no')\" />";
	}
	
	/**
	* Checks current wars / peace between alliances
	* if they're still valid
	*/
	function warpeace_update()
	{
		$time = time();
		
		// Wars
		$res = dbquery("
		SELECT
			alliance_bnd_id,
			a1.alliance_id as a1id,
			a2.alliance_id as a2id,
			a1.alliance_name as a1name,
			a2.alliance_name as a2name,
			a1.alliance_tag as a1tag,
			a2.alliance_tag as a2tag,
			a1.alliance_founder_id as a1f,
			a2.alliance_founder_id as a2f
		FROM 
			alliance_bnd
		INNER JOIN
			alliances as a1
			ON a1.alliance_id=alliance_bnd_alliance_id1
		INNER JOIN
			alliances as a2
			ON a2.alliance_id=alliance_bnd_alliance_id2		
		WHERE
			alliance_bnd_date<".($time-WAR_DURATION)."
			AND alliance_bnd_level=3
		");
		$nr = mysql_num_rows($res);
		if ($nr>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
				// Add log							
				$text = "Der Krieg zwischen [b][".$arr['a1tag']."] ".$arr['a1name']."[/b] und [b][".$arr['a2tag']."] ".$arr['a2name']."[/b] ist zu Ende! Es folgt eine Friedenszeit von ".round(PEACE_DURATION/3600)." Stunden.";
				add_alliance_history($arr['a1id'],$text);
				add_alliance_history($arr['a2id'],$text);

				// Send message to leader
				send_msg($arr['a1f'],MSG_ALLYMAIL_CAT,"Krieg beendet",$text." Während dieser Friedenszeit kann kein neuer Krieg erklärt werden!");
				send_msg($arr['a2f'],MSG_ALLYMAIL_CAT,"Krieg beendet",$text." Während dieser Friedenszeit kann kein neuer Krieg erklärt werden!");
		
				dbquery("
				UPDATE
					alliance_bnd
				SET
					alliance_bnd_level=4,
					alliance_bnd_date=".$time."
				WHERE
					alliance_bnd_id=".$arr['alliance_bnd_id']."
				");			
			}				
		}
		
		// Peaces
		$res=dbquery("
		SELECT
			alliance_bnd_id,
			a1.alliance_id as a1id,
			a2.alliance_id as a2id,
			a1.alliance_name as a1name,
			a2.alliance_name as a2name,
			a1.alliance_tag as a1tag,
			a2.alliance_tag as a2tag,
			a1.alliance_founder_id as a1f,
			a2.alliance_founder_id as a2f
		FROM 
			alliance_bnd
		INNER JOIN
			alliances as a1
			ON a1.alliance_id=alliance_bnd_alliance_id1
		INNER JOIN
			alliances as a2
			ON a2.alliance_id=alliance_bnd_alliance_id2		
		WHERE
			alliance_bnd_date<".($time-PEACE_DURATION)."
			AND alliance_bnd_level=4
		");
		$nr = mysql_num_rows($res);
		if ($nr>0)
		{
			while ($arr=mysql_fetch_array($res))
			{
				// Add log							
				$text = "Der Friedensvertrag zwischen [b][".$arr['a1tag']."] ".$arr['a1name']."[/b] und [b][".$arr['a2tag']."] ".$arr['a2name']."[/b] ist abgelaufen. Ihr könnt einander nun wieder Krieg erklären.";
				add_alliance_history($arr['a1id'],$text);
				add_alliance_history($arr['a2id'],$text);

				// Send message to leader
				send_msg($arr['a1f'],MSG_ALLYMAIL_CAT,"Friedensvertrag abgelaufen!",$text);
				send_msg($arr['a2f'],MSG_ALLYMAIL_CAT,"Friedensvertrag abgelaufen!",$text);
		
				dbquery("
				DELETE FROM
					alliance_bnd
				WHERE
					alliance_bnd_id=".$arr['alliance_bnd_id']."
				");			
			}		
				
		}		
		
		return $nr;
	}
	
	
	function writeUserToXml($userId,$path="")
	{
		$filename = $userId."_".date("Y-m-d_H-i").".xml";
		$file = $path."cache/user_xml/".$filename;
		if ($xml =  userToXml($userId))
		{
			if ($d=fopen($file,"w+"))
			{
				fwrite($d,$xml);
				fclose($d);
				return $filename;
			}
			echo "Konnte Datei $file nicht zum XML Export öffnen!";			
			return false;
		}
		echo "XML Export fehlgeschlagen. User $userId nicht gefunden!";			
		return false;
	}
	
	function userToXml($userId)
	{
		$res = dbquery("
		SELECT
			users.*,
			alliance_tag,
			alliance_name,
			race_name
		FROM
			users
		LEFT JOIN
			alliances ON user_alliance_id=alliance_id
		INNER JOIN
			races ON user_race_id=race_id
		WHERE user_id=".$userId."
		;");
		if (mysql_num_rows($res)>0)
		{		
			$arr=mysql_fetch_array($res);
			
$xml = "<userbackup>
	<export date=\"".date("d.m.Y, H:i")."\" timestamp=\"".time()."\" />
	<account>
		<id>".$arr['user_id']."</id>	
		<nick>".$arr['user_nick']."</nick>
		<name>".$arr['user_name']."</name>
		<email>".$arr['user_email']."</email>
		<points>".$arr['user_points']."</points>
		<rank>".$arr['user_rank_current']."</rank>
		<online>".date("d.m.Y, H:i",$arr['user_last_online'])."</online>		
		<ip>".$arr['user_ip']."</ip>		
		<host>".$arr['user_hostname']."</host>		
		<alliance id=\"".$arr['user_alliance_id']."\" tag=\"".$arr['alliance_tag']."\">".$arr['alliance_name']."</alliance>
		<race id=\"".$arr['user_race_id']."\">".$arr['race_name']."</race>
	</account>
	<planets>";
			$pres=dbquery("
				SELECT
					planet_id,
					planet_name,
					planet_res_metal,
					planet_res_crystal,
					planet_res_plastic,
					planet_res_fuel,
					planet_res_food,
					planet_people,
					planet_type_id,
					type_name,
					planet_user_main
				FROM
					planets
				INNER JOIN
					planet_types ON type_id=planet_type_id
					AND	planet_user_id='".$userId."';
			");
			if (mysql_num_rows($pres)>0)
			{
				while ($parr=mysql_fetch_array($pres))
				{
					if ($parr['planet_user_main']==1)
					{
						$mainPlanet = $parr['planet_id'];
					}
					$xml.= "
		<planet id=\"".$parr['planet_id']."\" name=\"".$parr['planet_name']."\" main=\"".$parr['planet_user_main']."\">
			<type id=\"".$parr['planet_type_id']."\">".$parr['type_name']."</type>					
			<metal>".intval($parr['planet_res_metal'])."</metal>
			<crystal>".intval($parr['planet_res_crystal'])."</crystal>
			<plastic>".intval($parr['planet_res_plastic'])."</plastic>
			<fuel>".intval($parr['planet_res_fuel'])."</fuel>
			<food>".intval($parr['planet_res_food'])."</food>
			<people>".intval($parr['planet_people'])."</people>
		</planet>";
				}
			}
			$xml.="
	</planets>
	<buildings>";	
			//Gebäude
			$bres = dbquery("
				SELECT
					building_name,
					buildlist_current_level,
					buildlist_planet_id,
					building_id
				FROM
					buildings
				INNER JOIN
					buildlist
					ON building_id = buildlist_building_id
					AND buildlist_user_id='".$userId."'
				ORDER BY
					buildlist_planet_id;
			");
			if (mysql_num_rows($bres)>0)
			{
				while ($barr=mysql_fetch_array($bres))
				{
					$xml.="
		<building planet=\"".$barr['buildlist_planet_id']."\" id=\"".$barr['building_id']."\" level=\"".$barr['buildlist_current_level']."\">".$barr['building_name']."</building>";
				}
			}
			$xml.="
	</buildings>
	<technologies>";	
			//Technologien
			$tres = dbquery("
				SELECT
					tech_name,
					techlist_current_level,
					tech_id
				FROM
					techlist
				INNER JOIN
					technologies
					ON techlist_tech_id = tech_id
					AND techlist_user_id='".$userId."';
			");
			if (mysql_num_rows($tres)>0)
			{
				while ($tarr=mysql_fetch_array($tres))
				{
					$xml.="
		<technology id=\"".$tarr['tech_id']."\" level=\"".$tarr['techlist_current_level']."\">".$tarr['tech_name']."</technology>";
				}
			}

			$xml.="
	</technologies>
	<ships>";	
			//Schiffe
			$sres = dbquery("
				SELECT
					ship_name,
					ship_id,
					shiplist_count,
					shiplist_planet_id
				FROM
					shiplist
				INNER JOIN
					ships
					ON shiplist_ship_id = ship_id
					AND shiplist_user_id='".$userId."'
					AND shiplist_count>0
				ORDER BY
					shiplist_planet_id;
			");
			if (mysql_num_rows($sres)>0)
			{
				while ($sarr=mysql_fetch_array($sres))
				{
					$xml.="
		<ship planet=\"".$sarr['shiplist_planet_id']."\" id=\"".$sarr['ship_id']."\" count=\"".$sarr['shiplist_count']."\">".$sarr['ship_name']."</ship>";
				}
			}
			//Flotten und deren Schiffe
			$fres=dbquery("
				SELECT
					fleet_id
				FROM
					fleet
				WHERE
					fleet_user_id='".$userId."';
			");
			if (mysql_num_rows($fres)>0)
			{
				while ($farr=mysql_fetch_array($fres))
				{
					$sres = dbquery("
						SELECT
							s.ship_name,
							s.ship_id,
							fs.fs_ship_cnt
						FROM
							fleet_ships AS fs
							INNER JOIN
							ships AS s
							ON fs.fs_ship_id = s.ship_id
							AND fs.fs_ship_faked=0
							AND fs.fs_fleet_id='".$farr['fleet_id']."';
					");
					if (mysql_num_rows($sres)>0)
					{
						while ($sarr=mysql_fetch_array($sres))
						{
					$xml.="
		<ship planet=\"".$mainPlanet."\" id=\"".$sarr['ship_id']."\" count=\"".$sarr['fs_ship_cnt']."\">".$sarr['ship_name']."</ship>";
						}
					}
				}
			}			
			$xml.="
	</ships>
	<defenses>";	
			//Verteidigung
			$dres = dbquery("
				SELECT
					d.def_name,
					d.def_id,
					dl.deflist_count,
					dl.deflist_planet_id
				FROM
					defense AS d
				INNER JOIN
					deflist AS dl
				ON d.def_id = dl.deflist_def_id
					AND dl.deflist_user_id='".$userId."'
					AND deflist_count>0
				ORDER BY
					dl.deflist_planet_id;
			");
			if (mysql_num_rows($dres)>0)
			{
				while ($darr=mysql_fetch_array($dres))
				{
					$xml.="
		<defense planet=\"".$darr['deflist_planet_id']."\" id=\"".$darr['def_id']."\" count=\"".$darr['deflist_count']."\">".$darr['def_name']."</defense>";
				}
			}
			$xml.="
	</defenses>
";	
			$xml.="</userbackup>";
			return $xml;
		}
		return false;		
	}

	
	
	/**
	* Textfunktionen einbinden
	*/
	include('inc/text.inc.php');

?>