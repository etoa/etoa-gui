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
		try
		{
			if ($class_name != "xajax")
			{
				if (defined("CLASS_ROOT"))
					$dir = CLASS_ROOT;
				else
				{
					if (stristr($_SERVER["SCRIPT_FILENAME"],"admin/index.php"))
						$dir = "../classes";
					else
						$dir = "classes";
				}
				$file = strtolower($class_name).'.class.php';
	      if (file_exists($dir.'/'.$file))
	      {
	        include_once($dir.'/'.$file);
	      }
	      elseif (file_exists($dir.'/entity/'.$file))
	      {
	        include_once($dir.'/entity/'.$file);
	      }
	      elseif (file_exists($dir.'/fleetaction/'.$file))
	      {
	        include_once($dir.'/fleetaction/'.$file);
	      }    
	      else
	      {
	      	throw new EException("Die Klasse ".$class_name." wurde nicht gefunden (".$dir."/".$file.")!");
		    }
		  }
		}
		catch (EException $e)
		{
			echo $e;
			exit;
		}
	}
	
	/**
	* Baut die Datenbankverbindung auf
	*/
	function dbconnect()
	{
		global $db_handle;
		global $query_counter;
		global $queries;
		global $dbopen;

		$queries = array();
		$query_counter=0;
		try
		{
			if (!$db_handle = @mysql_connect(DB_SERVER,DB_USER,DB_PASSWORD))
			{
				throw new DBException("Zum Datenbankserver auf <b>".DB_SERVER."</b> kann keine Verbindung hergestellt werden!");	
			}
			if (!mysql_select_db(DB_DATABASE))
			{
				throw new DBException("Auf die Datenbank <b>".DB_DATABASE."</b> auf <b>".DB_SERVER."</b> kann nicht zugegriffen werden!");				
			}
			$dbopen = true;
			dbquery("SET NAMES 'utf8';"); 
			return true;		
		}
		catch (DBException $e)
		{
			echo $e;
			exit;
		}
	}

	/**
	* Trennt die Datenbankverbindung
	*/
	function dbclose()
	{
		global $db_handle;
		global $res;
		global $query_counter; 
		global $queries;
		global $dbopen;
		if (ETOA_DEBUG==1 && false )
		{
			echo "Queries done: ".$query_counter."<br/>";
			foreach ($queries as $q)
			{
				echo "<b>".$q[0]."</b><br/>".($q[1])."<br/>";
				$res = mysql_query("EXPLAIN ".$q[0]."");
				drawDbQueryResult($res);
				echo "<br/>";
			}
		}
		if (isset($res))
		{
			@mysql_free_result($res);
		}
		@mysql_close($db_handle);
		unset($db_handle);
		$dbopen = false;
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
		global $queries;
		global $dbopen;
		if (!$dbopen)
		{
			dbconnect();			
		}
			
		$query_counter++;
		if (ETOA_DEBUG==1 && stristr($string,"SELECT"))
		{
			ob_start();
			debug_print_backtrace();
			$queries[] = array($string,ob_get_clean());
		}
		if ($result=mysql_query($string))
			return $result;
		elseif ($fehler==1)
		{
			try
			{
				throw new DBException($string);	
			}
			catch (DBException $e)
			{
				echo $e;
			}			
		}
	}

	function drawDbQueryResult($res)
	{
		if (mysql_num_rows($res)>0)
		{
			echo "<table class=\"tb\"><tr>";
			for ($x=0;$x<mysql_num_fields($res);$x++)
			{
				echo "<th>".mysql_field_name($res,$x)."</th>";
			}
			echo "</tr>";
			while ($arr=mysql_fetch_row($res))
			{
				echo "<tr>";
				foreach ($arr as $a)
				{
					echo "<td>".$a."</td>";
				}
				echo "</tr>";
			}
			echo "</table>";
		}
		else
		{
			echo "No result!<br/>";
		}
	}

	function getArrayFromTable($table,$field)
	{
		$r = array();
		$res = dbquery("
		SELECT
			`".$field."`
		FROM
			`".$table."`
		");
		if (mysql_num_rows($res)>0)
		{
			while ($arr=mysql_fetch_row($res))
			{
				$r[] = $arr[0];
			}
		}
		return $r;
	}

	/**
	* Gesamte Config-Tabelle lesen und Werte in Array speichern
	* DEPRECATED! This is only a wrapper!
	*/
	function get_all_config()
	{
		$cfg = Config::getInstance();
		return $cfg->getArray();
	}

	/**
	* Rassen-Daten in Array speichern
	*/
	function get_races_array()
	{
		$race_name = array();
		$res = dbquery("
		SELECT
			*
		FROM
			races
		ORDER BY
			race_name;");
		while ($arr = mysql_fetch_assoc($res))
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
		$names = array();

		$res = dbquery("
		SELECT
      alliance_tag,
      alliance_id,
      alliance_name,
      alliance_founder_id
		FROM
			alliances
		ORDER BY
			alliance_name;");
		while ($arr = mysql_fetch_assoc($res))
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
		$names = array();
		$res = dbquery("
			SELECT
				alliance_tag,
				alliance_id,
				alliance_name,
				alliance_founder_id
			FROM
				alliances
			WHERE
				alliance_id!='".$id."'
			ORDER BY
				alliance_name;
		");
		while ($arr = mysql_fetch_assoc($res))
		{
			$names[$arr['alliance_id']]['tag'] = $arr['alliance_tag'];
			$names[$arr['alliance_id']]['name'] = $arr['alliance_name'];
			$names[$arr['alliance_id']]['founder_id'] = $arr['alliance_founder_id'];
		}
		return $names;
	}
	
/**
	* Allianz Name in Array speichern, jedoch nur eine Allianz
	*/
	function get_alliance_names2($id)
	{
		$names = array();
		$res = dbquery("
			SELECT
				alliance_tag,
				alliance_id,
				alliance_name,
				alliance_founder_id
			FROM
				alliances
			WHERE
				alliance_id='".$id."'
			ORDER BY
				alliance_name;
		");
		while ($arr = mysql_fetch_assoc($res))
		{
			$names[$arr['alliance_id']]['tag'] = $arr['alliance_tag'];
			$names[$arr['alliance_id']]['name'] = $arr['alliance_name'];
			$names[$arr['alliance_id']]['founder_id'] = $arr['alliance_founder_id'];
		}
		return $names;
	}

	/**
	* User-Nick via User-Id auslesen
	*/
	function get_user_nick($id)
	{
		$res = dbquery("
			SELECT
				user_nick
			FROM
				users
			WHERE
				user_id='".$id."';
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_assoc($res);
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
		$res = dbquery("
		SELECT
			a.alliance_name,
			a.alliance_id,
			a.alliance_tag
		FROM
			users AS u
			INNER JOIN alliances AS a
			ON u.user_alliance_id = a.alliance_id
			AND u.user_id='".$id."';
		");
		if (mysql_num_rows($res)>0)
		{
			return mysql_fetch_assoc($res);
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
		$res = dbquery("
			SELECT
				alliance_id
			FROM
				alliances
			WHERE
				alliance_name='".$name."';
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_assoc($res);
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
		$res = dbquery("
			SELECT
				alliance_id
			FROM
				alliances
			WHERE
				alliance_tag='".$tag."';
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_assoc($res);
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
		$res = dbquery("
			SELECT
				user_id
			FROM
				users
			WHERE
				user_nick='".$nick."';
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_assoc($res);
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
	* @Todo: propabely remove
	*/
	function get_user_id_by_planet($pid)
	{
		$res = dbquery("
			SELECT
				planet_user_id
			FROM
				planets
			WHERE
				id='".$pid."';
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_assoc($res);
			return $arr['planet_user_id'];
		}
		else
		{
			return 0;
		}

	}

	/**
	* Format number
	*/
	function nf($number,$colorize=0,$ex=0)	// Number format
	{
		if ($ex==1)
		{
			if ($number>1000000000)
				$n = round($number/1000000000,3)." G";
			elseif ($number>1000000)
				$n = round($number/1000000,3)." M";
			elseif ($number>1000)
				$n = round($number/1000,3)." K";
			else
				$n = round($number,0);
			return $n;
		}
		else
			$n = number_format($number,0,",","`");
		if ($colorize==1)
		{
			if ($number>0)
				return "<span style=\"color:#0f0\">".$n."</span>";
			if ($number<0)
				return "<span style=\"color:#f00\">".$n."</span>";
		}
		return $n;
	}

	/**
	* Convert formated number back to integer
	*/
	function nf_back($number)	// Number format
	{
		$number = str_replace('`', '', $number);
		$number = abs(intval($number));
		return $number;
		
	}

	/**
	* An alternative number formatter
	*
	* @todo Merge this with nf()
	*/	
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

	/**
	* Format time in seconds to hour,minute,seconds
	*/
	function tf($ts)	// Time format
	{
		$w = floor($ts / 3600 / 24 / 7);
		$ts -= $w*3600*24*7;
		$t = floor($ts / 3600 / 24);
		$h = floor(($ts-($t*3600*24)) / 3600);
		$m = floor(($ts-($t*3600*24)-($h*3600))/60);
		$s = floor(($ts-($t*3600*24)-($h*3600)-($m*60)));

		if ($w>0)
			return $w."w ".$t."d ".$h."h ".$m."m ".$s."s";
		if ($t>0)
			return $t."d ".$h."h ".$m."m ".$s."s";
		if ($h>0)
			return $h."h ".$m."m ".$s."s";
		if ($m>0)		
			return $m."m ".$s."s";
		return $s."s";
	}

	/**
	* Corrects a web url
	*/
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

	/**
	* Überprüft ob unerlaubte Zeichen im Text sind und gibt Antwort zurück
	*
	* @todo Should be removed (better use some regex and strip-/addslashes/trim
	*/
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

	/**
	* Überprüft ob unerlaubte Zeichen im Text sind und gibt Antwort zurück
	*
	* @todo Check if this method is still usable
	*/
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

	/**
	* Sends a system message to an user
	*/
	function send_msg($user_id,$msg_type,$subject,$text)
	{
		dbquery("
			INSERT INTO
				messages
			(
				message_user_from,
				message_user_to,
				message_timestamp,
				message_cat_id
			)
			VALUES
			(
				'0',
				'".$user_id."',
				'".time()."',
				'".$msg_type."'
			);
		");
		dbquery("
			INSERT INTO
				message_data
			(
				id,
				subject,
				text
			)
			VALUES
			(
				".mysql_insert_id().",
				'".addslashes($subject)."',
				'".addslashes($text)."'
			);
		");		
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
		if ($log_timestamp==0)
		{
		 	$log_timestamp=time();
		}
		 dbquery("
		 INSERT INTO
		 logs
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
		 	'".resolveIp($_SERVER['REMOTE_ADDR'])."'
		 );");
	}

	/**
	* Adds an user log item
	*/
	function add_log_user($log_cat,$log_text,$uid1,$uid2=0,$pid=0,$sid=0,$log_timestamp=0)
	{
		if ($log_timestamp==0)
		{
		 	$log_timestamp=time();
		}
		 dbquery("
		 INSERT INTO
		 logs
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
		 	'".resolveIp($_SERVER['REMOTE_ADDR'])."',
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
		
		//Setzt auktuelle Zeit wenn keine andere angegeben wird
		if ($log_timestamp==0)
		{
		 	$log_timestamp=time();
		}
		
		//Speichert Log
		dbquery("
		INSERT INTO
		logs_game
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
			'".resolveIp($_SERVER['REMOTE_ADDR'])."',
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
		
		//Setzt auktuelle Zeit wenn keine andere angegeben wird
		if ($log_timestamp==0)
		{
		 	$log_timestamp=time();
		}
		
		//Speichert Log
		dbquery("
		INSERT INTO
		logs_game
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
			'".resolveIp($_SERVER['REMOTE_ADDR'])."',
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
		
		//Setzt auktuelle Zeit wenn keine andere angegeben wird
		if ($log_timestamp==0)
		{
		 	$log_timestamp=time();
		}
		
		//Speichert Log
		dbquery("
		INSERT INTO
		logs_game
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
			'".resolveIp($_SERVER['REMOTE_ADDR'])."',
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
		
		//Setzt auktuelle Zeit wenn keine andere angegeben wird
		if ($log_timestamp==0)
		{
		 	$log_timestamp=time();
		}
		
		//Speichert Log
		dbquery("
		INSERT INTO
		logs_game
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
			'".resolveIp($_SERVER['REMOTE_ADDR'])."',
			'".intval($user_id)."',
			'".intval($alliance_id)."',
			'".intval($planet_id)."',
			'".intval($build_type)."'
		);");
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
	*
	*@Todo: outsource, is only for >admins
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
	*@Todo: outsource, is only for >admins
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
	*@Todo: outsource, is only for >admins
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
		
	/**
	* Cuts a string by a given length
	*/
	function cut_string($string,$num)
	{
		if (strlen($string)>$num+3)
			return substr($string,0,$num)."...";
		else
			return $string;
	}

	/**
	* Checks for a valid mail address
	*/
	function checkEmail($email)
	{
	  return preg_match("/^[a-zA-Z0-9-_.]+@[a-zA-Z0-9-_.]+\.[a-zA-Z]{2,4}$/",$email);
	}
	
	/**
	* Checks vor a vaid name
	*/
	function checkValidName($name)
	{
		return eregi(REGEXP_NAME, $name);
	}

	/**
	* Checks for a valid nick
	*/
	function checkValidNick($name)
	{
		return eregi(REGEXP_NICK, $name);
	}

	/**
	* User Name in Array speichern
	*/
	function get_user_names()
	{
		$names = array();
		$res = dbquery("
			SELECT
				user_id,
				user_nick,
				user_name,
				user_email,
				user_alliance_id
			FROM
				users;
		");
		while ($arr = mysql_fetch_assoc($res))
		{
			$names[$arr['user_id']]['nick'] = $arr['user_nick'];
			$names[$arr['user_id']]['name'] = $arr['user_name'];
			$names[$arr['user_id']]['email'] = $arr['user_email'];
			$names[$arr['user_id']]['alliance_id'] = $arr['user_alliance_id'];
		}
		return $names;
	}


	function tableStart($title="",$width=0,$layout="")
	{
		if ($width>0)
		{
			$w = "width:".$width."px;";
		}		
		elseif ($width!="")
		{
			$w = "width:".$width."";
		}
		else
		{
			$w = "width:650px";
		}
		if ($layout=="double")
		{
			echo "<table style=\"".$w."\"><tr><td style=\"width:50%;vertical-align:top;\">";
		}
		else
		{
			echo "<table class=\"tb boxLayout\" style=\"".$w."\">";
		}
		
		if ($title!="")
			echo "<tr><th class=\"infoboxtitle\" colspan=\"20\">$title</th></tr>";
	}

	function tableEnd()
	{
		echo "</table>";
	}

	/**
	* Infobox-Header
	*/
	function iBoxStart($title="",$width=0)
	{
		if ($width>0)
		{
			$w = "width:".$width."px;";
		}
		elseif ($width!="")
		{
			$w = "width:".$width."";
		}
		else
		{
			$w = "width:650px";
		}
		
		echo "<div class=\"boxLayout\" style=\"".$w."\">";

		if ($title!="")
			echo "<div class=\"infoboxtitle\">$title</div>";
		echo "<div class=\"infoboxcontent\">";
	}

	/**
	* Infobox-Footer
	*/
	function iBoxEnd()
	{
		echo "</div>";
		echo "</div>";
	}

	/**
	* Planetendaten zurücksetzen
	*
	* $planet_id: MySQL-ID des Planeten
	* @todo Use class method!
	*/
	function reset_planet($planet_id)
	{
		if ($planet_id>0)
		{
			dbquery("
				UPDATE
					planets
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
					id='".$planet_id."';
			");

			dbquery("
				DELETE FROM
					shiplist
				WHERE
					shiplist_entity_id='".$planet_id."';
			");
			dbquery("
				DELETE FROM
					buildlist
				WHERE
					buildlist_entity_id='".$planet_id."';
			");
			dbquery("
				DELETE FROM
					deflist
				WHERE
					deflist_entity_id='".$planet_id."';
			");
			add_log("6","Der Planet mit der ID ".$planet_id." wurde zurückgesetzt!",time());
			return true;
		}
		else
			return false;
	}

	/*
	* Formatierte Fehlermeldung anzeigen
	*
	* $msg: Fehlermeldung
	*/
	function err_msg($msg)
	{
		error_msg($msg);
	}
	
	/**
	* Formatierte OK-Meldung anzeigen
	*
	* $msg: OK-Meldung
	*/
	function ok_msg($msg)
	{
		success_msg($msg);
	}

	/**
	* Sucess msg
	*/
	function success_msg($text,$type=0)
	{
		echo "<div class=\"successBox\">";
		switch($type)
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
       
  /**
  * Error msg
  */
	function error_msg($text,$type=0,$exit=0,$addition=0,$stacktrace=null)
	{
		// TODO: Do check on headers
		
		echo "<div class=\"errorBox\">";
		switch($type)
		{
			case 1:
				echo "";
				break;
			case 2:
				echo "<b>Warnung:</b> ";
				break;
			case 3:
				echo "<b>Problem:</b> ";
				break;
			case 4:
				echo "<b>Datenbankproblem:</b> ";
				break;
			default:
				echo "<b>Fehler:</b> ";
		}		
		echo text2html($text);
		switch($addition)
		{		
			case 1:
				echo text2html("\n\n[url http://forum.etoa.ch]Zum Forum[/url] | [email mail@etoa.ch]Mail an die Spielleitung[/email]");		
				break;
			case 2:
				echo text2html("\n\n[url http://bugs.etoa.net]Fehler melden[/url]");		
				break;				
			default:
				echo '';
		}
		if (isset($stacktrace))
		{
			echo "<div style=\"text-align:left;border-top:1px solid #000;\">
			<b>Stack-Trace:</b><br/>".nl2br($stacktrace)."<br/><a href=\"http://bugs.etoa.net\" target=\"_blank\">Fehler melden</a></div>";
		}
		echo "</div>";
		if ($exit>0) 
		{
			echo "</body></html>";
			exit;
		}
	}


	/**
	* Prozentwert generieren und zurückgeben
	*
	* $val: Einzelner Wert oder Array von Werten als Dezimalzahl; 1.0 = 0%
	* $colors: Farben anzeigen (1) oder nicht anzeigen (0)
	*/
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

	/**
	* Tabulator-Menü anzeigen
	*
	* $varname: Name des Modusfeldes
	* $data: Array mit Menüdaten
	*/
	function show_tab_menu($varname,$data)
	{
		global $page,$$varname;
		
		echo "<div class=\"tabMenu\">";
		$cnt=0;
		foreach ($data as $val => $text)
		{
			$cnt++;
			if ($$varname==$val)
				echo "<a href=\"?page=$page&amp;".$varname."=$val\" class=\"tabEnabled".($cnt==count($data)?' tabLast':'')."\">$text</a>";
			else
				echo "<a href=\"?page=$page&amp;".$varname."=$val\"".($cnt==count($data)?' class="tabLast"':'').">$text</a>";
		}
		echo "<br style=\"clear:both;\"/>";
		echo "</div>";
	}
	
	/**
	* Tab Menu with on-click
	*/
	function show_js_tab_menu($data)
	{
		echo "<div class=\"tabMenu\">";
		$cnt=0;
		foreach ($data as $val => $text)
		{
			echo "<a href=\"#\" id=\"tabMenu$x\" onclick=\"$val\" ".($cnt==count($data)?' class="tabLast"':'').">$text</a>";
		}
		echo "<br style=\"clear:both;\"/>";
		echo "</div>";		
	}
    
  /**
  * Get imagepacks
  */
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

	/**
	* Wählt die verschiedenen Designs aus und schreibt sie in ein array. by Lamborghini
	*/
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

	/**
	* Überprüft ob ein Gebäude deaktiviert ist
	*
	* $user_id: Benutzer-ID
	* $planet_id: Planet-ID
	* $building_id: Gebäude-ID
	*
	* @todo Typo in method name... bether think of creating a building class
	*/
	function check_building_deactivated($user_id,$planet_id,$building_id)
	{
		$res=dbquery("
			SELECT
				buildlist_deactivated
			FROM
				buildlist
			WHERE
				buildlist_user_id='".$user_id."'
				AND buildlist_entity_id='".$planet_id."'
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

  /**
	* Fremde, feindliche Flotten
  * Gibt Anzahl feindliche Flotten zurück unter beachtung von Tarn- und Spionagetechnik
  * Sind keine Flotten unterwegs -> return 0
  *
  * @author MrCage
  * @param int $user_id User ID
  *
  */
	function check_fleet_incomming($user_id)
	{
		$fm = new FleetManager($user_id);
		return $fm->loadAggressiv();	
	}

	/**
	* Add text to alliance history
	*/
	function add_alliance_history($alliance_id,$text)
	{
		dbquery("
			INSERT INTO
			alliance_history
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

	/**
	* User-history adder
	* @todo User history no longer uses
	*/
	function add_user_history($user_id,$text)
	{
		dbquery("
			INSERT INTO
			user_history
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

	/**
	* Check for buddys who are online
	*/
	function check_buddys_online($id)
	{
		global $conf;
		$res = dbquery("
			SELECT
				COUNT(user_id)
			FROM
				buddylist AS bl
				INNER JOIN users AS u
				ON bl.bl_buddy_id = u.user_id
				AND bl_user_id='".$id."'
				AND bl_allow=1
				AND user_acttime>".(time()-$conf['online_threshold']['v']*60).";
		");
		$arr = mysql_fetch_row($res);
		return $arr[0];		
	}

	function check_buddy_req($id)
	{
		$res=dbquery("
			SELECT
		    COUNT(bl_id) 
			FROM
		    buddylist
		  WHERE
		  	bl_buddy_id='".$id."'
		    AND bl_allow=0");
		$arr = mysql_fetch_row($res);
		return $arr[0];
	}

	/**
	* The form checker - init
	*/
	function checker_init($debug=0)
	{
		$_SESSION['checker']=md5(mt_rand(0,99999999).time());
		if (isset($_SESSION['checker_last']))
		{
			while ($_SESSION['checker_last']==$_SESSION['checker'])
			{
				$_SESSION['checker']=md5(mt_rand(0,99999999).time());
			}
		}
		$_SESSION['checker_last']=$_SESSION['checker'];
		echo "<input type=\"hidden\" name=\"checker\" value=\"".$_SESSION['checker']."\" />";
		if ($debug==1)
			echo "Checker initialized with ".$_SESSION['checker']."<br/><br/>";
		return "<input type=\"hidden\" name=\"checker\" value=\"".$_SESSION['checker']."\" />";
	}

	/**
	* The form checker - verify
	*/
	function checker_verify($debug=0,$msg=1)
	{
		global $_POST,$_GET;
		if ($debug==1)
			echo "Checker-Session is: ".$_SESSION['checker'].", Checker-POST is: ".$_POST['checker']."<br/><br/>";
		if (isset($_SESSION['checker']) && ((isset($_POST['checker']) && $_SESSION['checker']==$_POST['checker']) || ( isset($_GET['checker']) && $_SESSION['checker']==$_GET['checker'] ))&& $_SESSION['checker']!="")
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

	/**
	* The form checker - get key
	*/
	function checker_get_key()
	{
		return $_SESSION['checker'];
	}

	/**
	* The form checker - debug
	*/
	function checker_get_link_key()
	{
		return "&amp;checker=".$_SESSION['checker'];
	}

	/**
	* Displays a simple back button
	*/
	function return_btn()
	{
		global $page;
		echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Zur&uuml;ck\" />";
	}
	
	function button($label,$target)
	{
		return "<input type=\"button\" value=\"$label\" onclick=\"document.location='$target'\" />";
	}

	/**
	* A pseudi randomizer
	*/
	function pseudo_randomize($faktor,$qx,$qy,$px,$py)
	{
		$str=floor((abs(sin($qx)) + abs(sin($qy)) + abs(sin($px)) + abs(sin($py)))*10000);
		return round ($faktor*((substr($str,strlen($str)-1,1)+1)/10),0);
	}

	/**
	* Prevents negative numbers
	*/
	function zeroPlus($val)
	{
		if ($val<0)
			return 0;
		else
			return $val;
	}

	/**
	* Diese Funktion liefert 5 Optionsfelder in denen man den Tag,Monat,Jahr,Stunde,Minute auswählen kann
	*/
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

	/**
	* Servertime
	*/
	function serverTime()
	{
		echo date("H:i:s");
	}

	/**
	* Tipmessage
	*/
	function tm($title,$text)
	{
		return mTT($title,$text);
	}
	
	/**
	* Tooltip
	*/
	function tt($text)
	{
		return mTT("",$text);
	}	

	/**
	* Date format
	*/
	function df($date,$seconds=1)
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

	/**
	* Sends an email
	*
	* @todo Needs refractoring!!
	*/
    function send_mail($preview,$adress,$topic,$text,$style,$align,$force=0)
    {
        $conf = get_all_config();

        //if($style=="")
		//	$style=$conf['default_css_style']['v'];

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
        	if (!mail($adress,$topic,$email_text,$email_header))
        	{
        		error_msg("Die Mail konnte nicht versendet werden.\n\n".$email_text."");
        	}

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
	function shiplistAdd($entity,$user,$ship,$cnt)
	{
		dbquery("
				INSERT INTO
				shiplist
				(
					shiplist_user_id,
					shiplist_entity_id,
					shiplist_ship_id,
					shiplist_count
				)
				VALUES
				(
					'".$user."',
					'".$entity."',
					'".$ship."',
					'".max($cnt,0)."'
				)
				ON DUPLICATE KEY
				UPDATE
					shiplist_count = shiplist_count + VALUES(shiplist_count);
			");
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
	function deflistAdd($entity,$user,$def,$cnt)
	{
			dbquery("
				INSERT INTO
				deflist
				(
					deflist_user_id,
					deflist_entity_id,
					deflist_def_id,
					deflist_count
				)
				VALUES
				(
					'".$user."',
					'".$entity."',
					'".$def."',
					'".max($cnt,0)."'
				)
				ON DUPLICATE KEY
				UPDATE
					deflist_count = deflist_count + VALUES(deflist_count);
			");
	}
	
	/**
	* Gebäude zur gebäudeliste hinzufügen
	*
	* @param int $planet Planet-ID
	* @param int $user User-ID
	* @param int $ship Schiff-ID
	* @param int $cnt Anzahl
	* @author MrCage
	*/
	function buildlistAdd($entity,$user,$building,$level)
	{
			dbquery("
				INSERT INTO
				buildlist
				(
					buildlist_user_id,
					buildlist_entity_id,
					buildlist_building_id,
					buildlist_current_level
				)
				VALUES
				(
					'".$user."',
					'".$entity."',
					'".$building."',
					'".max($level,0)."'
				)
				ON DUPLICATE KEY
				UPDATE
					buildlist_current_level = '".max($level,0)."';
			");
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
		$res=dbquery("
			SELECT
				missilelist_id
			FROM
				missilelist
			WHERE
				missilelist_user_id='".$user."'
				AND missilelist_entity_id='".$planet."'
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
					AND missilelist_entity_id='".$planet."'
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
					missilelist_entity_id,
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
		dbquery("
			INSERT INTO
			mail_queue
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
		$res=dbquery("
			SELECT
				*
			FROM
				mail_queue
			ORDER BY
				msg_timestamp ASC
			LIMIT $cnt
		");
		$nr = mysql_num_rows($res);
		if ($nr>0)
		{
			while ($arr=mysql_fetch_assoc($res))
			{
				mail($arr['msg_to'],stripslashes($arr['msg_subject']),stripslashes($arr['msg_text']),stripslashes($arr['msg_header'])) or die("Mail problem!\n");
				
				dbquery("
					DELETE FROM
						mail_queue
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
	* Cuts words
	*/
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

	/**
	* Stopwatch start
	*/
	function timerStart()
	{
		// Renderzeit-Start festlegen
		$render_time = explode(" ",microtime());
		return $render_time[1]+$render_time[0];
	}

	/**
	* Stopwatch stop
	*/
	function timerStop($starttime)
	{
		// Renderzeit
		$render_time = explode(" ",microtime());
		$rtime = $render_time[1]+$render_time[0]-$starttime;
		return round($rtime,3);
	}

function imagecreatefromfile($path, $user_functions = false)
{
    $info = @getimagesize($path);
   
    if(!$info)
    {
        return false;
    }
   
    $functions = array(
        IMAGETYPE_GIF => 'imagecreatefromgif',
        IMAGETYPE_JPEG => 'imagecreatefromjpeg',
        IMAGETYPE_PNG => 'imagecreatefrompng',
        IMAGETYPE_WBMP => 'imagecreatefromwbmp',
        IMAGETYPE_XBM => 'imagecreatefromwxbm',
        );
   
    if($user_functions)
    {
        $functions[IMAGETYPE_BMP] = 'imagecreatefrombmp';
    }
   
    if(!$functions[$info[2]])
    {
        return false;
    }
   
    if(!function_exists($functions[$info[2]]))
    {
        return false;
    }
   
    return $functions[$info[2]]($path);
}


	/**
	* Resizes a image and save it to a given filename
	*
	*/
	function resizeImage($fileFrom, $fileTo, $newMaxWidth = 0, $newMaxHeight = 0, $type="jpeg" ) 
	{
		if ($type=='png')
		{
			$imgfrom = "ImageCreateFromPNG";
			$imgsave = "ImagePNG";
			$quality=null;
		}
		elseif ($type=='gif')
		{
			$imgfrom = "ImageCreateFromGIF";
			$imgsave = "ImageGIF";
			$quality=0;
		}
		elseif ($type=="jpeg" || $type=="jpg")
		{
			$imgfrom = "ImageCreateFromJPEG";
			$imgsave = "ImageJPEG";
			$quality=100;
		}
		else
			return false;
			
		if ($img = imagecreatefromfile($fileFrom)) 
		{
			$width = ImageSX($img);
			$height = ImageSY($img);
			$resize = FALSE;
			
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
					
					imagealphablending($imageId, false);
					imagesavealpha($imageId,true);
					$transparent = imagecolorallocatealpha($imageId, 255, 255, 255, 127);
					imagefilledrectangle($imageId, 0, 0, $newWidth, $newHeight, $transparent);
					
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
			} 
			else 
			{
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
	* @todo source this out
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
			while($arr=mysql_fetch_assoc($res))
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
	* @param float costFactor (like specialist)
	* @return array Array of calculated costs
	*
	*/	
	function calcBuildingCosts($buildingArray, $level, $fac)
	{
		$bc=array();
		$bc['metal'] = $fac * $buildingArray['building_costs_metal'] * pow($buildingArray['building_build_costs_factor'],$level);
		$bc['crystal'] = $fac * $buildingArray['building_costs_crystal'] * pow($buildingArray['building_build_costs_factor'],$level);
		$bc['plastic'] = $fac * $buildingArray['building_costs_plastic'] * pow($buildingArray['building_build_costs_factor'],$level);
		$bc['fuel'] = $fac * $buildingArray['building_costs_fuel'] * pow($buildingArray['building_build_costs_factor'],$level);
		$bc['food'] = $fac * $buildingArray['building_costs_food'] * pow($buildingArray['building_build_costs_factor'],$level);
		$bc['power'] = $fac * $buildingArray['building_costs_power'] * pow($buildingArray['building_build_costs_factor'],$level);
		return $bc;
	}
	
	/**
	* Calculates costs per level for a given technology costs array
	*
	* @param array Array of db cost values
	* @param int Level
	* @param float costFactor (like specialist)
	* @return array Array of calculated costs
	*
	*/
	function calcTechCosts($arr,$l,$fac)
	{

		// Baukostenberechnung          Baukosten = Grundkosten * (Kostenfaktor ^ Ausbaustufe)
		$bc = array();
		$bc['metal'] = $fac * $arr['tech_costs_metal'] * pow($arr['tech_build_costs_factor'],$l);
		$bc['crystal'] = $fac * $arr['tech_costs_crystal'] * pow($arr['tech_build_costs_factor'],$l);
		$bc['plastic'] = $fac * $arr['tech_costs_plastic'] * pow($arr['tech_build_costs_factor'],$l);
		$bc['fuel'] = $fac * $arr['tech_costs_fuel'] * pow($arr['tech_build_costs_factor'],$l);
		$bc['food'] = $fac * $arr['tech_costs_food'] * pow($arr['tech_build_costs_factor'],$l);
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
	function ticket_button($cat,$title="Missbrauch",$uid=0,$aid=0)
	{		
		echo "<input type=\"button\" value=\"".$title."\" onclick=\"window.open('show.php?page=ticket&ext=1&cat=".$cat."&uid=".$uid."&$aid=".$aid."','abuse','width=700,height=470,status=no,scrollbars=yes')\" />";
	}
	
	/**
	* Checks current wars / peace between alliances
	* if they're still valid
	* @todo outsource
	*/
	function warpeace_update()
	{
		$time = time();
		
		// Assign diplomacy points for pacts
		$res=dbquery("
		SELECT
			alliance_bnd_id,
			alliance_bnd_diplomat_id,
			alliance_bnd_alliance_id1,
			alliance_bnd_alliance_id2,
			alliance_bnd_points
		FROM 
			alliance_bnd
		WHERE
			alliance_bnd_date<".($time-DIPLOMACY_POINTS_MIN_PACT_DURATION)."
			AND alliance_bnd_points>0
			AND alliance_bnd_level=2
		");
		if (mysql_num_rows($res)>0)
		{
			while ($arr=mysql_fetch_assoc($res))
			{
				$user = new User($arr['alliance_bnd_diplomat_id']);
				$user->rating->addDiplomacyRating($arr['alliance_bnd_points'],"Bündnis ".$arr['alliance_bnd_alliance_id1']." mit ".$arr['alliance_bnd_alliance_id1']);
				dbquery("
				UPDATE
					alliance_bnd
				SET
					alliance_bnd_points=0
				WHERE
					alliance_bnd_id=".$arr['alliance_bnd_id']."
				");
			}
		}
		
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
			a2.alliance_founder_id as a2f,
			alliance_bnd_points,
			alliance_bnd_diplomat_id
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
			while ($arr=mysql_fetch_assoc($res))
			{
				// Add log							
				$text = "Der Krieg zwischen [b][".$arr['a1tag']."] ".$arr['a1name']."[/b] und [b][".$arr['a2tag']."] ".$arr['a2name']."[/b] ist zu Ende! Es folgt eine Friedenszeit von ".round(PEACE_DURATION/3600)." Stunden.";
				add_alliance_history($arr['a1id'],$text);
				add_alliance_history($arr['a2id'],$text);

				// Send message to leader
				send_msg($arr['a1f'],MSG_ALLYMAIL_CAT,"Krieg beendet",$text." Während dieser Friedenszeit kann kein neuer Krieg erklärt werden!");
				send_msg($arr['a2f'],MSG_ALLYMAIL_CAT,"Krieg beendet",$text." Während dieser Friedenszeit kann kein neuer Krieg erklärt werden!");
		
				// Assing diplomacy points
				$user = new User($arr['alliance_bnd_diplomat_id']);
				$user->rating->addDiplomacyRating($arr['alliance_bnd_points'],"Krieg ".$arr['a1id']." gegen ".$arr['a2id']);

				dbquery("
				UPDATE
					alliance_bnd
				SET
					alliance_bnd_level=4,
					alliance_bnd_date=".$time.",
					alliance_bnd_points=0
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
			while ($arr=mysql_fetch_assoc($res))
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
	
	
	/**
	* Simple recursive function to calculate the power of a number
	* this is faster than the original implementation of pow()
	* because it only uses integer exponents
	*/
	function intpow($base,$exponent)
	{
		if ($exponent<=0)
			return 1;
		return $base * intpow($base,$exponent-1);
	}

	
	function countDown($elem,$targettime,$elementToSetEmpty="")
	{
		?>
		<script type="text/javascript">
			if (document.getElementById('<?PHP echo $elem;?>')!=null)
			{
				cnt["<?PHP echo $elem;?>"] = 0;
				setCountdown('<?PHP echo $elem;?>',<?PHP echo time();?>,<?PHP echo $targettime;?>,'<?PHP echo $elementToSetEmpty;?>');
			}
		</script>
		<?PHP	
	}
	
	function jsProgressBar($elem,$startTime,$endTime,$length=450)
	{
		?>
		<script type="text/javascript">
			if (document.getElementById('<?PHP echo $elem;?>')!=null)
			{
				updateProgressBar('<?PHP echo $elem;?>',<?PHP echo ceil($startTime);?>,<?PHP echo ceil($endTime);?>,<?PHP echo time();?>,<?PHP echo $length; ?>);
			}
		</script>
		<?PHP	
	}	


	/**
	* The ultimate answer
	*/
	function answer_to_life_the_universe_and_everything()
	{
		return 42;
	}


	/**
	* Startet den Javascript Counter bzw. die Uhr
	*
	* @param $time int Gibt die Restzeit oder den Timestamp an
	* @param $target string Gibt die Ziel-ID an
	* @param $format int 0=Counter, 1=Uhr
	* @param $text string Ein optionaler Text kann eingebunden werden -> "Es geht noch TIME bis zum Ende"
	*/
	function startTime($time, $target, $format=0, $text="")
	{
		return "<script type=\"text/javascript\">time(".$time.", '".$target."', ".$format.", '".$text."');</script>";
	}

	/**
	* Prints an array
	* For debug purposes only
	*/
	function dump($val)
	{	
		ob_start();
		print_r($val);
		$tmp = ob_get_clean();
		echo nl2br($tmp);
	}

	function popUp($caption, $args,$width=800,$height=600)
	{
		return "<a href=\"?".$args."\" onclick=\"window.open('show.php?".$args."','popup','status=no,width=".$width.",height=".$height.",scrollbars=yes');return false;\">".$caption."</a> ";		
	}

	function ticketLink($caption,$category)
	{
		$width=700;
		$height=600;
		return "<a href=\"?page=ticket&amp;cat=".$category."\" onclick=\"window.open('show.php?page=ticket&amp;cat=".$category."','popup','status=no,width=".$width.",height=".$height.",scrollbars=yes');return false;\">".$caption."</a>";		
	}

	function helpLink($site,$caption="Hilfe",$style="")
	{
		$width=900;
		$height=600;
		return "<a href=\"?page=help&amp;site=".$site."\" style=\"$style\" onclick=\"window.open('show.php?page=help&amp;site=".$site."','popup','status=no,width=".$width.",height=".$height.",scrollbars=yes');return false;\">".$caption."</a>";		
	}

	function helpImageLink($site,$url,$alt="Item",$style="")
	{
		$width=900;
		$height=600;
		return "<a href=\"?page=help&amp;site=".$site."\" onclick=\"window.open('show.php?page=help&amp;site=".$site."','popup','status=no,width=".$width.",height=".$height.",scrollbars=yes');return false;\">
		<img src=\"".$url."\" alt=\"".$alt."\" style=\"border:none;".$style."\" />
		</a>";		
	}

	function showTechTree($type,$itemId)
	{
		echo "<div id=\"reqInfo\" style=\"width:100%;text-align:center;;
		color:#fff;border:none;margin:0px auto;\">
		Bitte warten...
		</div>";	
		echo '<script type="text/javascript">xajax_reqInfo('.$itemId.',"'.$type.'")</script>';
	}
	
	/**
	* Returns the hostname of the given ip address
	* Lookup results are cached in a static array and used
	* if this function is used multiple times with the same ip. Additionally
	* the values are stored in a memory database table for faster lookups. This records
	* expire after one day.
	*/
	function resolveIp($ip)
	{        
		if (!isset($hostcache))
			static $hostcache = array();       
			
		if (isset($hostcache[$ip]))
			return $hostcache[$ip];
		
		$t = time();
		$res = dbquery("
		SELECT
			host
		FROM
			hostname_cache
		WHERE
			addr='".$ip."'
			AND timestamp>".($t-86400)."
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_row($res);
			$host = $arr[0];
			$hostcache[$ip] = $host;
			return $host;
		}
		
		$host = @gethostbyaddr($ip);
		$hostcache[$ip] = $host;
		$res = dbquery("
		REPLACE INTO
			hostname_cache
		(
		  addr,
			host,
			timestamp
		)
		VALUES
		(
			'".$ip."',
			'".$host."',
			".$t."
		);
		");		
		return $host;
	}

	function resolveHostname($host)
	{
		if (!isset($ipcache))
			static $ipcache = array();       
			
		if (isset($ipcache[$host]))
			return $ipcache[$host];
		
		$t = time();
		$res = dbquery("
		SELECT
			addr
		FROM
			hostname_cache
		WHERE
			host='".$host."'
			AND timestamp>".($t-86400)."
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_row($res);
			$ip = $arr[0];
			$ipcache[$host] = $ip;
			return $ip;
		}
		
		$ip = @gethostbyname($host);
		$ipcache[$host] = $ip;
		$res = dbquery("
		REPLACE INTO
			hostname_cache
		(
		  addr,
			host,
			timestamp
		)
		VALUES
		(
			'".$ip."',
			'".$host."',
			".$t."
		);
		");		
		return $ip;		
		
	}
	
	
	function initTT()
	{
		echo '<div class="tooltip" id="tooltip" style="display:none;">
  	<div class="tttitle" id="tttitle"></div>
  	<div class="ttcontent" id="ttcontent"></div>
 </div> ';		
	}

	function cTT($title,$content)
	{
		return " onclick=\"showTT('".str_replace('"',"\'",$title)."','".str_replace('"',"\'",$content)."',0,event,this);return false;\" ";
	}

	function mTT($title,$content)
	{
		
		return " onmouseover=\"showTT('".str_replace('"',"\'",$title)."','".str_replace('"',"\'",$content)."',1,event,this);\" onmouseout=\"hideTT()\" ";
	}
	
	function chatSystemMessage($msg)
	{
		dbquery("INSERT INTO
			chat
		(
			timestamp,
			text
		)
		VALUES
		(
			".time().",
			'".addslashes($msg)."'
		)");	
	}	
	
	function chatUserCleanUp()
	{
		$res = dbquery("SELECT nick FROM chat_users WHERE timestamp < ".(time()-120));		
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_row($res);
			chatSystemMessage($arr[0]." verlässt den Chat (Timeout).");
		}
		dbquery("DELETE FROM chat_users WHERE timestamp < ".(time()-180));		
	}
	
	function checkDaemonRunning($pidfile)
	{
		if ($fh = @fopen($pidfile,"r"))
		{
			$pid = intval(fread($fh,50));
			fclose($fh);
			if ($pid > 0)
			{
	     	$cmd = "ps $pid";
	     	exec($cmd, $output);
	     	if(count($output) >= 2)
	     	{
	      	return $pid;   
	    	}
			}
		}
		return false;
	}
	
	function sendBackendMessage($message)
	{
		if (function_exists("msg_get_queue"))
		{
			$q = msg_get_queue(IPC_TOKEN,0600);
			return msg_send($q,1,$message,false,false);
		}
		return false;
	}

	function icon($name)
	{
		return "<img src=\"".(defined('IMAGE_DIR')? IMAGE_DIR : 'images')."/icons/".$name.".png\" alt=\"$name\" />";
	}

	// Test

	/**
	* Textfunktionen einbinden
	*/
	include('inc/text.inc.php');

?>
