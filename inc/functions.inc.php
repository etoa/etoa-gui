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
	function dbconnect($throwError = 1)
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
				if ($throwError==1)
					throw new DBException("Zum Datenbankserver auf <b>".DB_SERVER."</b> kann keine Verbindung hergestellt werden!");
				else
					return false;
			}
			if (!mysql_select_db(DB_DATABASE))
			{
				if ($throwError==1)
					throw new DBException("Auf die Datenbank <b>".DB_DATABASE."</b> auf <b>".DB_SERVER."</b> kann nicht zugegriffen werden!");
				else
					return false;
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
		if (defined('ETOA_DEBUG') && ETOA_DEBUG==1 && stristr($string,"SELECT"))
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

	/**
	* Executes an sql query savely and protects agains SQL injections
	*
	* @param string $query SQL-Query
	* @param array $params Array of arguments
	*/
	function dbQuerySave($query,$params=array()) 
	{
	    if (is_array($params) && count($params)>0) 
	    {
	        foreach ($params as &$v) 
	        { 
	        	$v = dbEscapeStr($v); 
	        }    
	        # Escaping parameters
	        # str_replace - replacing ? -> %s. %s is ugly in raw sql query
	        # vsprintf - replacing all %s to parameters
	        $sql = vsprintf( str_replace("?","'%s'",$query), $params );   
	    } 
	    else 
	    {
	        $sql = $query;    # If no params...
	    }
	    if ($res = mysql_query($sql))
	    	return $res;
			try
			{
				throw new DBException($string);	
			}
			catch (DBException $e)
			{
				echo $e;
			}
			return false;
	} 

	/**
	 * Prepares a user string for sql queries and
	 * escapes all malicious characters, e.g. '
	 * 
	 * @param string $string
	 * @return string
	 */
	function dbEscapeStr($string)
	{
		$string = trim($string);
		if(get_magic_quotes_gpc())
			$string = stripslashes($string);
		return mysql_real_escape_string($string);
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

	function dbexplain($sql)
	{
		echo "Explaining: $sql";
		$res = mysql_query("EXPLAIN ".$sql."");
		drawDbQueryResult($res);
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
	function nf_back($number,$colorize=0)
	{
		$number = str_replace('`', '', $number);
		$number = str_replace('%', '', $number);
		if ($colorize==1)
		{
			if ($number>0)
				return "<span style=\"color:#0f0\">".number_format($number,0,",",".")."</span>";
			if ($number<0)
				return "<span style=\"color:#f00\">".number_format($number,0,",",".")."</span>";
		}
		$number = abs(intval($number));
		return $number;
		
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

		$str = "";
		if ($w>0)
			$str.= $w."w ";
		if ($t>0)
			$str.=  $t."d ";
		if ($h>0)
			$str.=  $h."h ";
		if ($m>0)		
			$str.=  $m."m ";
		if ($s>0)
			$str.=  $s."s ";

		return $str;
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
		if ($user_id>0)
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
			return true;
		}
		return false;
	}

	/**
	* Speichert Daten in die Log-Tabelle
	*
	 * TDOD: deprecated, please replace
	* @param int $log_cat Log Kategorie
	* @param string $log_text Log text
	* @param int $log_timestamp Zeit
	* @author MrCage
	*/
	function add_log($log_cat,$log_text)
	{
		Log::add($log_cat,Log::INFO,$log_text);
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


	function tableStart($title="",$width=0,$layout="", $id="")
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
			global $cu;
			if (isset($cu->properties) && $cu->properties->cssStyle=="Graphite")
				$w = "width:650px";			
			else
				$w = "width:98%";
		}
		if ($id!="")
		{
			$id = "id=\"".$id."\"";
		}
		if ($layout=="double")
		{
			echo "<table ".$id." style=\"".$w."\"><tr><td style=\"width:50%;vertical-align:top;\">";
		}
		elseif ($layout=="nondisplay")
		{
			echo "<table ".$id." class=\"tb boxLayout\" style=\"display:none;".$w."\">";
		}
		else
		{
			echo "<table ".$id." class=\"tb boxLayout\" style=\"".$w."\">";
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
			global $cu;
			if (isset($cu->properties) && $cu->properties->cssStyle=="Graphite")
				$w = "width:650px";			
			else
				$w = "width:98%";
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
				INNER JOIN user_sessions AS u
				ON bl.bl_buddy_id = u.user_id
				AND bl_user_id='".$id."'
				AND bl_allow=1;
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
	function calcBuildingCosts($buildingArray, $level, $fac=1)
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
	
	function calcAllianceBuildingCosts($buildingArray, $level, $fac=1)
	{
		$bc=array();
		$bc['metal'] = $fac * $buildingArray['alliance_building_costs_metal'] * pow($buildingArray['alliance_building_costs_factor'],$level);
		$bc['crystal'] = $fac * $buildingArray['alliance_building_costs_crystal'] * pow($buildingArray['alliance_building_costs_factor'],$level);
		$bc['plastic'] = $fac * $buildingArray['alliance_building_costs_plastic'] * pow($buildingArray['alliance_building_costs_factor'],$level);
		$bc['fuel'] = $fac * $buildingArray['alliance_building_costs_fuel'] * pow($buildingArray['alliance_building_costs_factor'],$level);
		$bc['food'] = $fac * $buildingArray['alliance_building_costs_food'] * pow($buildingArray['alliance_building_costs_factor'],$level);
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
	function calcTechCosts($arr,$l,$fac=1)
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
		$salt = Config::getInstance()->password_salt->v;
		return md5($pw.$seed.$salt).md5($salt.$seed.$pw);
	}
	
	function pw_salt2($pw,$seed=0)
	{
		$gseed = Config::getInstance()->password_salt->v;	// Take the general seed
		$seedlen = strlen($gseed); // Measure it's length
		$pwlen = strlen($pw);	// Measure the password length		
		$mlen = max($pwlen,$seedlen); // Get the maximum lenght
		$saltedHash = "";	// Create an empty hash
		$pw = (string)$pw;
		$last = 0;
		for ($i=0; $i < $mlen; $i++)
		{
			// Now salt the password with the general and the individual seed ...
			$val = (ord($gseed[$i%$seedlen]) + ord($pw[$i % $pwlen]) + $seed + $last)%255;
			$saltedHash .= chr($val);
			$last = $val;
		}
		// ... and return it's SHA1 hash
		return sha1($saltedHash);
	}	
	
	function passwordStrength($password, $username = null)
	{
	    if (!empty($username))
	    {
	        $password = str_replace($username, '', $password);
	    }
	
	    $strength = 0;
	    $password_length = strlen($password);
	
	    if ($password_length < 4)
	    {
	        return $strength;
	    }
	
	    else
	    {
	        $strength = $password_length * 4;
	    }
	
	    for ($i = 2; $i <= 4; $i++)
	    {
	        $temp = str_split($password, $i);
	
	        $strength -= (ceil($password_length / $i) - count(array_unique($temp)));
	    }
	
	    preg_match_all('/[0-9]/', $password, $numbers);
	
	    if (!empty($numbers))
	    {
	        $numbers = count($numbers[0]);
	
	        if ($numbers >= 3)
	        {
	            $strength += 5;
	        }
	    }
	
	    else
	    {
	        $numbers = 0;
	    }
	
	    preg_match_all('/[|!@#$%&*\/=?,;.:\-_+~^¨\\\]/', $password, $symbols);
	
	    if (!empty($symbols))
	    {
	        $symbols = count($symbols[0]);
	
	        if ($symbols >= 2)
	        {
	            $strength += 5;
	        }
	    }
	
	    else
	    {
	        $symbols = 0;
	    }
	
	    preg_match_all('/[a-z]/', $password, $lowercase_characters);
	    preg_match_all('/[A-Z]/', $password, $uppercase_characters);
	
	    if (!empty($lowercase_characters))
	    {
	        $lowercase_characters = count($lowercase_characters[0]);
	    }
	
	    else
	    {
	        $lowercase_characters = 0;
	    }
	
	    if (!empty($uppercase_characters))
	    {
	        $uppercase_characters = count($uppercase_characters[0]);
	    }
	
	    else
	    {
	        $uppercase_characters = 0;
	    }
	
	    if (($lowercase_characters > 0) && ($uppercase_characters > 0))
	    {
	        $strength += 10;
	    }
	
	    $characters = $lowercase_characters + $uppercase_characters;
	
	    if (($numbers > 0) && ($symbols > 0))
	    {
	        $strength += 15;
	    }
	
	    if (($numbers > 0) && ($characters > 0))
	    {
	        $strength += 15;
	    }
	
	    if (($symbols > 0) && ($characters > 0))
	    {
	        $strength += 15;
	    }
	
	    if (($numbers == 0) && ($symbols == 0))
	    {
	        $strength -= 10;
	    }
	
	    if (($symbols == 0) && ($characters == 0))
	    {
	        $strength -= 10;
	    }
	
	    if ($strength < 0)
	    {
	        $strength = 0;
	    }
	
	    if ($strength > 100)
	    {
	        $strength = 100;
	    }
	    return $strength;
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
	
	function jsSlider($elem,$value=100, $target='"#value"')	
	{
		?>
		<script type="text/javascript">
			$(function() {
				$("#slider").slider({
					value: <?PHP echo $value ?>,
					min: 1,
					max: 100,
					step: 1,
					slide: function(event, ui) {
						$(<?PHP echo $target; ?>).val(ui.value + ' %');
					}
				});
				$(<?PHP echo $target; ?>).val($("#slider" + " %").slider("value"));
			});
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
	function dump($val,$return=0)
	{	
		ob_start();
		print_r($val);
		$tmp = ob_get_clean();
		if ($return==1)
			return $tmp;
		echo "<pre>".($tmp)."</pre>";
	}

	function popUp($caption, $args,$width=800,$height=600)
	{
		return "<a href=\"?".$args."\" onclick=\"window.open('show.php?".$args."','popup','status=no,width=".$width.",height=".$height.",scrollbars=yes');return false;\">".$caption."</a> ";		
	}
	
	function userPopUp($userId, $userNick, $msg=1, $strong=0)
	{
		$userNick = $userId>0 ? ($userNick!='' ? $userNick : '<i>Unbekannt</i>') : '<i>System</i>';
		
		$out = "";
		if ($userId>0 && $userNick!='')
		{
			$out .= "<div id=\"ttuser".$userId."\" style=\"display:none;\">
			".popUp("Profil anzeigen","page=userinfo&id=".$userId)."<br/>
			".popUp("Punkteverlauf","page=stats&mode=user&userdetail=".$userId)."<br/>";
			if ($userId!=$_SESSION['user_id'])
			{
				if ($msg==1)
					$out.=  "<a href=\"?page=messages&mode=new&message_user_to=".$userId."\">Nachricht senden</a><br/>";
				$out .= "<a href=\"?page=buddylist&add_id=".$userId."\">Als Freund hinzufügen</a>";
			}
			$out .= "</div>";
			if ($strong) $out .= '<strong>';
			$out .= '<a href="#" '.cTT($userNick,"ttuser".$userId).'>'.$userNick.'</a>';
			if ($strong) $out .= '</strong>';
		}
		else
		{
			$out .= $userNick;
		}
		return $out;
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
	
	
	function initTT()
	{
		echo '<div class="tooltip" id="tooltip" style="display:none;" onmouseup="hideTT();">
  	<div class="tttitle" id="tttitle"></div>
  	<div class="ttcontent" id="ttcontent"></div>
 		</div> ';		
	}

	function cTT($title,$content)
	{
		return " onclick=\"showTT('".str_replace('"',"\'",$title)."','".str_replace('"',"\'",$content)."',0,event,this);return false;\"  ";
	}

	function mTT($title,$content)
	{
		return " onmouseover=\"showTT('".str_replace('"',"\'",$title)."','".str_replace('"',"\'",$content)."',1,event,this);\" onmouseout=\"hideTT();\" ";
	}

	function tt($content)
	{
		return " onmouseover=\"showTT('','".str_replace('"',"\'",$content)."',1,event,this);\" onmouseout=\"hideTT();\" ";
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
			$dname = dirname(realpath("config/db.config.php"));
			$ipckey = ftok($dname,IPC_ID);
			$q = msg_get_queue($ipckey,0666);
			add_log(4,"Sende IPC Message mit Key $ipckey vom Token $dname und Projekt-Id ".IPC_ID.". Die Queue hat die ID ".$q);
			return msg_send($q,1,$message,false,false);
		}
		return false;
	}

	function icon($name)
	{
		return "<img src=\"".(defined('IMAGE_DIR')? IMAGE_DIR : 'images')."/icons/".$name.".png\" alt=\"$name\" />";
	}

	function htmlSelect($name,&$data,$default=null)
	{
		echo '<select name="'.$name.'">';
		foreach ($data as $k=>$v)
		{
			echo '<option value="'.$k.'" '.($default==$k ? ' selected="selected"': '').'>'.$v.'</option>';
		}
		echo "</select>";
	}

	// TODO: Implement this
	function encrypt($str,$salt)
	{
		return $str;
	}

	// TODO: Implement this
	function decrypt($str,$salt)
	{
		return $str;
	}

	
	function forward($url,$msgTitle=null,$msgText=null)
	{
		header("Location: ".$url);
		echo "<h1>".$msgTitle."</h1><p>".$msgText."</p><p>Falls die Weiterleitung nicht klappt, <a href=\"".$url."l\">hier</a> klicken...</p>";
		exit;
	}

	function showTitle($title)
	{
		echo "<br/><a href=\"?\"><img src=\"images/game_logo.gif\" alt=\"EtoA Logo\" /></a>";
		echo "<h1>$title - ".Config::getInstance()->roundname->v."</h1>";
	}

	function defineImagePaths()
	{
		global $cu;
		$cfg = Config::getInstance();
		
		if (!defined('IMAGE_PATH'))
		{
			if (!isset($cu))
				$cu = new CurrentUser($_SESSION['user_id']);


			if ($cu->properties->cssStyle !='')
			{
				define('CSS_STYLE',DESIGN_DIRECTORY."/".$cu->properties->cssStyle);
			}
			else
			{
				define('CSS_STYLE',DESIGN_DIRECTORY."/".$cfg->value('default_css_style'));
			}
			define('GAME_WIDTH',$cu->properties->gameWidth);

			// Image paths
			if ($cu->properties->imageUrl != '' && $cu->properties->imageExt != '')
			{
				define('IMAGE_PATH',$cu->properties->imageUrl);
				define('IMAGE_EXT',$cu->properties->imageExt);
			}
			else
			{
				define("IMAGE_PATH",$cfg->default_image_path->v);
				define("IMAGE_EXT","png");
			}
		}
	}

	/**
	* Textfunktionen einbinden
	*/
	include_once(RELATIVE_ROOT.'inc/text.inc.php');

?>
