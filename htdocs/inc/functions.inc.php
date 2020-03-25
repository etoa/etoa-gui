<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

	/**
	* Main function file
	*
	* @author MrCage mrcage@etoa.ch
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/

	/**
	* Returns a string containing the game name, version and round
	*/
	function getGameIdentifier()	{
		return APP_NAME.' '.getAppVersion().' '.Config::getInstance()->roundname->v;
	}

	function getAppVersion() {
		require_once __DIR__ . '/../version.php';
		return APP_VERSION;
	}

	/**
	* Baut die Datenbankverbindung auf
	*/
	function dbconnect($throwError = 1) {
		return DBManager::getInstance()->connect($throwError);
	}

	/**
	* Trennt die Datenbankverbindung
	*/
	function dbclose() {
		return DBManager::getInstance()->close();
	}

	/**
	* Führt eine Datenbankabfrage aus
	*
	* @param string $string SQL-Abfrage
	* #param int $fehler Erzwing Fehleranzeige, Standard: 1
	*/
	function dbquery($string, $fehler=1) {
		return DBManager::getInstance()->query($string, $fehler);
	}

	/**
	* Executes an sql query savely and protects agains SQL injections
	*
	* @param string $query SQL-Query
	* @param array $params Array of arguments
	*/
	function dbQuerySave($query, $params=array()) {
	    return DBManager::getInstance()->safeQuery($query, $params);
	}

	function startTransaction() {
		dbquery("START TRANSACTION;");
	}

	function commitTransaction() {
		dbquery("COMMIT;");
	}

	function rollbackTransaction() {
		dbquery("ROLLBACK;");
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
				user_nick='".mysql_real_escape_string($nick)."';
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
        $number = intval($number);
		if ($colorize==1)
		{
			if ($number>0)
				return "<span style=\"color:#0f0\">".number_format($number,0,",",".")."</span>";
			if ($number<0)
				return "<span style=\"color:#f00\">".number_format($number,0,",",".")."</span>";
		}
		$number = abs($number);
		return $number;

	}

	/**
	* Convert formated number back to integer (positive & negative number)
	*/
	function nf_back_sign($number,$colorize=0)
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
		// $number = abs(intval($number));
		$number = intval($number);
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
		$string = preg_replace("#([ \n])(http|https|ftp)://([^ ,\n]*)#i", "\\1[url]\\2://\\3[/url]", $string);
		$string = preg_replace("#([ \n])www\\.([^ ,\n]*)#i", "\\1[url]https://www.\\2[/url]", $string);
		$string = preg_replace("#^(http|https|ftp)://([^ ,\n]*)#i", "[url]\\1://\\2[/url]", $string);
		$string = preg_replace("#^www\\.([^ ,\n]*)#i", "[url]https://www.\\1[/url]", $string);
	 	$string = preg_replace('#\[url\]www.([^\[]*)\[/url\]#i', '<a href="https://www.\1">\1</a>', $string);
		$string = preg_replace('#\[url\]([^\[]*)\[/url\]#i', '<a href="\1">\1</a>', $string);
		$string = preg_replace('#\[mailurl\]([^\[]*)\[/mailurl\]#i', '<a href="\1">Link</a>', $string);
		return $string;
	}

	/*
	 * Format the data in a row by comparing two user
	 *
	 * @param <User> $uer userobject you need to compare to the $cu
	 *
	 * option 2
	 * @param <Planet> $object
	 *
	 * @return $class
	 */
	function getFormatingColorByUser(&$user)
	{
		// if the $cu is not active as in xajax functions create one
		if ( !isset($cu) )
		{
			// if there is no session active as on external pages return nothing;
			if ( !$_SESSION['user_id']) return;
			$cu = new User($_SESSION['user_id']);
		}

		$admins = getAdmins();
		// admin
		if ( in_array($user->id, $admins) )
		{
			$class = "adminColor";
		}
		// war
		elseif ($user->allianceId > 0 && $cu->allianceId > 0 && $cu->alliance->checkWar($user->allianceId))
		{
			$class = "enemyColor";
		}
		// pact
		elseif ($user->allianceId > 0 && $cu->allianceId > 0 && $cu->alliance->checkBnd($user->allianceId))
		{
			$class = "friendColor";
		}
		// bannend/locked
		elseif ($user->locked)
		{
			$class = "userLockedColor";
		}
		// on holiday
		elseif ($user->holiday)
		{
			$class = "userHolidayColor";
		}
		// long time Inactive
		elseif ($user->lastOnline < time() - USER_INACTIVE_LONG * 86400)
		{
			$class = "userLongInactiveColor";
		}
		// inactive
		elseif ($user->lastOnline < time() - USER_INACTIVE_SHOW * 86400)
		{
			$class = "userInactiveColor";
		}
		// alliance member
		elseif($cu->allianceId() && $cu->allianceId() == $user->allianceId())
		{
			$class = "userAllianceMemberColor";
		}
		else
		{
			$class = "";
		}

		return $class;
	}

	/*
	 * Format the data in a row by comparing two user
	 *
	 * @param <Entity> $ent Entityobject you will compare the entity owner with the $cu
	 *
	 * @return $class
	 */
	function getFormatingColorByEntity(&$ent)
	{
		if ( !isset($cu) )
		{
			if ( !$_SESSION['user_id']) return;
			$cu = new User($_SESSION['user_id']);
		}

		// admin
		if ( in_array($ent->ownerId(), $admins) )
		{
		  $class = "adminColor";
		  $tm_info = "Admin/Entwickler";
		}
		// war
		elseif ($ent->owner->allianceId>0 && $cu->allianceId>0 && $cu->alliance->checkWar($ent->owner->allianceId))
		{
		  $class = "enemyColor";
		  $tm_info = "Krieg";
		}
		// pact
		elseif ($ent->owner->allianceId>0 && $cu->allianceId>0 && $cu->alliance->checkBnd($ent->owner->allianceId))
		{
		  $class = "friendColor";
		  $tm_info = "B&uuml;ndnis";
		}
		// banned/locked
		elseif ($ent->ownerLocked())
		{
		  $class = "userLockedColor";
		  $tm_info = "Gesperrt";
		}
		// on holiday
		elseif ($ent->ownerHoliday())
		{
		  $class = "userHolidayColor";
		  $tm_info = "Urlaubsmodus";
		}
		// long time inactive
		elseif ($ent->owner->lastOnline < time() - USER_INACTIVE_LONG * 86400)
		{
		  $class = "userLongInactiveColor";
		  $tm_info = "Inaktiv";
		}
		// inactive
		elseif ($ent->owner->lastOnline < time() - USER_INACTIVE_SHOW * 86400)
		{
		  $class = "userInactiveColor";
		  $tm_info = "Inaktiv";
		}
		// own planet
		elseif($cu->id == $ent->ownerId())
		{
		  $class .= "userSelfColor";
		  $tm_info = "";
		}
		// own planet
		elseif($cu->allianceId() == $ent->owner->allianceId() && $cu->allianceId())
		{
		  $class = "userAllianceMemberColor";
		  $tm_info = "Allianzmitglied";
		}
		// noob
		elseif ( ($cu->points * USER_ATTACK_PERCENTAGE > $ent->ownerPoints()
				|| $cu->points / USER_ATTACK_PERCENTAGE < $ent->ownerPoints() )
				&& $ent->ownerId() != $cu->id)
		{
		  $class = "noobColor";
		  $tm_info = "Anf&auml;ngerschutz";
		}
		// Alien/NPC
	    elseif ($ent->owner->isNPC()>0)
	    {
		    $class .= "alien";
		    $tm_info = "Alien";
		}
		else
		{
		  $class = "";
		  $tm_info="";
		}
	}

	function getAdmins()
	{
		global $admins;
		if ( !isset($admins))
		{
			$ares = dbquery("SELECT player_id FROM admin_users WHERE player_id<>0;");
			$admins = array();
			while ($arow = mysql_fetch_row($ares))
			{
				array_push($admins,$arow[0]);
			}
		}
		return $admins;
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
           		return "&lt; &gt; &apos; &quot; ? ! $ = ; &amp;";
           	}
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
				'".intval($user_id)."',
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
				'".mysql_real_escape_string($subject)."',
				'".mysql_real_escape_string($text)."'
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
	function add_log($log_cat, $log_text)
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
	  return preg_match('/^[a-zA-Z0-9-_.]+@[a-zA-Z0-9-_.]+\.[a-zA-Z]{2,4}$/',$email);
	}

	/**
	* Checks vor a vaid name
	*/
	function checkValidName($name)
	{
		return preg_match(REGEXP_NAME, $name);
	}

	/**
	* Checks for a valid nick
	*/
	function checkValidNick($name)
	{
		return preg_match(REGEXP_NICK, $name);
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
				$w = "width:100%";
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
			echo "<table ".$id." class=\"tb\" style=\"display:none;".$w."\">";
		}
		else
		{
			echo "<table ".$id." class=\"tb\" style=\"".$w."\">";
		}

		if ($title!="")
		{
			echo "<caption>".$title."</caption>";
		}
	}

	function tableEnd()
	{
		echo "</table>";
	}

	/**
	* Infobox-Header
	*/
	function iBoxStart($title="", $class="")
	{
		echo '<div class="boxLayout '.$class.'">';
		if ($title!="") {
			echo '<div class="infoboxtitle"><span>'.$title.'</span></div>';
		}
		echo '<div class="infoboxcontent">';
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
			add_log("6","Der Planet mit der ID ".$planet_id." wurde zurückgesetzt!");
			return true;
		}
		else
			return false;
	}

	/**
	* Formatierte Erfolgsmeldung anzeigen
	*
	* $msg: OK-Meldung
	*/
	function success_msg($text)
	{
		iBoxStart("Erfolg", "success");
		echo text2html($text);
		iBoxEnd();
	}

	/**
	* Formatierte Info-Meldung anzeigen
	*
	* $text: Info-Meldung
	*/
	function info_msg($text)
	{
		iBoxStart("Information", "information");
		echo text2html($text);
		iBoxEnd();
	}

	/*
	* Formatierte Fehlermeldung anzeigen
	*
	* $msg: Fehlermeldung
	*/
	function error_msg($text,$type=0,$exit=0,$addition=0,$stacktrace=null)
	{
		switch($type)
		{
			case 1:
				$title = '';
				break;
			case 2:
				$title = 'Warnung';
				break;
			case 3:
				$title = 'Problem';
				break;
			case 4:
				$title = 'Datenbankproblem';
				break;
			default:
				$title = 'Fehler';
		}

		iBoxStart($title, "error");
		echo text2html($text);

		// Addition
		switch($addition)
		{
			case 1:
				echo text2html("\n\n[url ".FORUM_URL."]Zum Forum[/url] | [email mail@etoa.ch]Mail an die Spielleitung[/email]");
				break;
			case 2:
				echo text2html("\n\n[url ".DEVCENTER_PATH."]Fehler melden[/url]");
				break;
			default:
				echo '';
		}

		// Stacktrace
		if (isset($stacktrace))
		{
			echo "<div style=\"text-align:left;border-top:1px solid #000;\">
			<b>Stack-Trace:</b><br/>".nl2br($stacktrace)."<br/><a href=\"".DEVCENTER_PATH."\" target=\"_blank\">Fehler melden</a></div>";
		}
		iBoxEnd();
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
	function get_imagepacks()
	{
		$packs = array();
		global $conf;
		if ($d=opendir(IMAGEPACK_DIRECTORY))
		{
			while ($f=readdir($d))
			{
				$dir = IMAGEPACK_DIRECTORY."/".$f;
				if (is_dir($dir) && $f!=".." && $f!=".")
				{
					$file = $dir."/".IMAGEPACK_CONFIG_FILE_NAME;

					if (is_file($file))
					{
						$pack['dir'] = $dir;
						$pack['path'] = substr($dir, strlen(RELATIVE_ROOT));
						$xml = new XMLReader();
						$xml->open($file);
						while ($xml->read())
						{
							switch ($xml->name)
							{
								case "name":
									$xml->read();
									$pack['name']= $xml->value;
									$xml->read();
									break;
								case "description":
									$xml->read();
									$pack['description']= $xml->value;
									$xml->read();
									break;
								case "version":
									$xml->read();
									$pack['version']= $xml->value;
									$xml->read();
									break;
								case "changed":
									$xml->read();
									$pack['changed']= $xml->value;
									$xml->read();
									break;
								case "extensions":
									$xml->read();
									$pack['extensions']= explode(",",$xml->value);
									$xml->read();
									break;
								case "author":
									$xml->read();
									$pack['author']= $xml->value;
									$xml->read();
									break;
								case "email":
									$xml->read();
									$pack['email']= $xml->value;
									$xml->read();
									break;
								case "files":
									$xml->read();
									$pack['files']= explode(",",$xml->value);
									$xml->read();
									break;
							}
						}
						$xml->close();
						$packs[basename($dir)] = $pack;
					}
				}
			}
		}
		return $packs;
	}

	/**
	* Wählt die verschiedenen Designs aus und schreibt sie in ein array. by Lamborghini
	*/
	function get_designs()
	{
		$rootDir = RELATIVE_ROOT.DESIGN_DIRECTORY;
		$designs = array();
		foreach(array('official', 'custom') as $rd)
		{
			$baseDir = $rootDir.'/'.$rd;
			if ($d = opendir($baseDir))
			{
				while ($f = readdir($d))
				{
					$dir = $baseDir."/".$f;
					if (is_dir($dir) && !preg_match('/^\./', $f))
					{
						$file = $dir."/".DESIGN_CONFIG_FILE_NAME;
						$design = parseDesignInfoFile($file);
						if ($design != null)
						{
							$design['dir'] = $dir;
							$design['custom'] = ($rd == 'custom');
							$designs[$f] = $design;
						}
					}
				}
			}
		}
		return $designs;
	}

	/**
	* Parses a design info file
	*/
	function parseDesignInfoFile($file)
	{
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
						$design['name']= $xml->value;
						$xml->read();
						break;
					case "changed":
						$xml->read();
						$design['changed']= $xml->value;
						$xml->read();
						break;
					case "version":
						$xml->read();
						$design['version']= $xml->value;
						$xml->read();
						break;
					case "author":
						$xml->read();
						$design['author']= $xml->value;
						$xml->read();
						break;
					case "email":
						$xml->read();
						$design['email']= $xml->value;
						$xml->read();
						break;
					case "description":
						$xml->read();
						$design['description']= $xml->value;
						$xml->read();
						break;
					case "restricted":
						$xml->read();
						$design['restricted'] = $xml->value == "true";
						$xml->read();
						break;
				}
			}
			$xml->close();
			return $design;
		}
		return null;
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
	function checker_verify($debug=0,$msg=1,$throw=false)
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
		    if ($throw) {
		        throw new \RuntimeException('Seite kann nicht mehrfach aufgerufen werden!');
            }
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
		global $page, $index;
		if ($index!="")
			echo "<input type=\"button\" onclick=\"document.location='?index=$index'\" value=\"Zur&uuml;ck\" />";
		else
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
		global $cp;
		global $cu;
		$bc=array();
		$bc['metal'] = $fac * $buildingArray['building_costs_metal'] * pow($buildingArray['building_build_costs_factor'],$level);
		$bc['crystal'] = $fac * $buildingArray['building_costs_crystal'] * pow($buildingArray['building_build_costs_factor'],$level);
		$bc['plastic'] = $fac * $buildingArray['building_costs_plastic'] * pow($buildingArray['building_build_costs_factor'],$level);
		$bc['fuel'] = $fac * $buildingArray['building_costs_fuel'] * pow($buildingArray['building_build_costs_factor'],$level);
		$bc['food'] = $fac * $buildingArray['building_costs_food'] * pow($buildingArray['building_build_costs_factor'],$level);
		$bc['power'] = $fac * $buildingArray['building_costs_power'] * pow($buildingArray['building_build_costs_factor'],$level);

        $typeBuildTime = 1.0;
        $starBuildTime = 1.0;

		if (isset($cp->typeBuildtime))
			$typeBuildTime = $cp->typeBuildtime;
		if (isset($cp->starBuildtime))
			$starBuildTime = $cp->starBuildtime;

		$bonus = $cu->race->buildTime + $typeBuildTime + $starBuildTime + $cu->specialist->buildTime - 3;
		$bc['time'] = ($bc['metal']+$bc['crystal']+$bc['plastic']+$bc['fuel']+$bc['food']) / GLOBAL_TIME * BUILD_BUILD_TIME;
		$bc['time'] *= $bonus;
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
	 * Generates a password using the password string, and possibly a user selected seed
	 *
	 * @param string Password from user
	 * @param string User's salt (must be random)
	 * @param string Returns a salted password concatenated with the salt itself to be saved in a user database
	 */
	function saltPasswort($pw, $salt=null)
	{
		if ($salt == null)
		{
			$salt = generateSalt();
		}
		return sha1($salt.$pw).$salt;
	}

	/**
	 * Returns the salt which is part of a salted password string
	 *
	 * @param string Salted password
	 */
	function getSaltFromPassword($passwordAndSalt)
	{
		$len = strlen(sha1(""));
		return substr($passwordAndSalt, $len, $len);
	}

	/**
	 * Validates if a given input matches the salted password
	 *
	 * @param string $input Clear-Text password input
	 * @param string $passwordAndSalt Salted password from a user database
	 */
	function validatePasswort($input, $passwordAndSalt)
	{
		return saltPasswort($input, getSaltFromPassword($passwordAndSalt)) == $passwordAndSalt;
	}

	/**
	 * Generates a new random salt value
	 */
	function generateSalt()
	{
		return sha1(uniqid(mt_rand(), true));
	}

	/**
	 * Generates a new random password of length 8
	 */
	function generatePasswort()
	{
		return substr(sha1(mt_rand()), 0, 8);
	}

	/**
	 * Assesses the security of a password and returns a
	 * password strength rating
	 */
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
	* Generates a random string
	*
	* @param int Length of the string
	*/
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
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

	function jsProgressBar($elem,$startTime,$endTime)
	{
		?>
		<script>
			$(function() {
				updateProgressBar('#<?PHP echo $elem;?>', <?PHP echo floor($startTime);?>, <?PHP echo ceil($endTime);?>, <?PHP echo time();?>);
			});
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
				$(<?PHP echo $target; ?>).val($("#slider").slider("value") + " %");
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
		return "<a href=\"?".$args."\" onclick=\"window.open('popup.php?".$args."','popup','status=no,width=".$width.",height=".$height.",scrollbars=yes');return false;\">".$caption."</a> ";
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
		return "<a href=\"?page=ticket&amp;cat=".$category."\" onclick=\"window.open('popup.php?page=ticket&amp;cat=".$category."','popup','status=no,width=".$width.",height=".$height.",scrollbars=yes');return false;\">".$caption."</a>";
	}

	function helpLink($site,$caption="Hilfe",$style="")
	{
		$width=900;
		$height=600;
		return "<a href=\"?page=help&amp;site=".$site."\" style=\"$style\" onclick=\"window.open('popup.php?page=help&amp;site=".$site."','popup','status=no,width=".$width.",height=".$height.",scrollbars=yes');return false;\">".$caption."</a>";
	}

	function helpImageLink($site,$url,$alt="Item",$style="")
	{
		$width=900;
		$height=600;
		return "<a href=\"?page=help&amp;site=".$site."\" onclick=\"window.open('popup.php?page=help&amp;site=".$site."','popup','status=no,width=".$width.",height=".$height.",scrollbars=yes');return false;\">
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


	function getInitTT()
	{
		return '<div class="tooltip" id="tooltip" style="display:none;" onmouseup="hideTT();">
  	<div class="tttitle" id="tttitle"></div>
  	<div class="ttcontent" id="ttcontent"></div>
 		</div> ';
	}

	function cTT($title,$content)
	{
		return " onclick=\"showTT('".StringUtils::encodeDBStringToJS($title)."','".StringUtils::encodeDBStringToJS($content)."',0,event,this);return false;\"  ";
	}

	function mTT($title,$content)
	{
		return " onmouseover=\"showTT('".StringUtils::encodeDBStringToJS($title)."','".StringUtils::replaceBR(StringUtils::encodeDBStringToJS($content))."',1,event,this);\" onmouseout=\"hideTT();\" ";
	}

	function tt($content)
	{
		return " onmouseover=\"showTT('','".StringUtils::encodeDBStringToJS($content)."',1,event,this);\" onmouseout=\"hideTT();\" ";
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
			if (!isset($cu) && isset($_SESSION['user_id']))
				$cu = new CurrentUser($_SESSION['user_id']);

			$design = DESIGN_DIRECTORY."/official/".$cfg->value('default_css_style');
			if (isset($cu) && $cu->properties->cssStyle !='')
			{
				if (is_dir(DESIGN_DIRECTORY."/custom/".$cu->properties->cssStyle))
				{
					$design = DESIGN_DIRECTORY."/custom/".$cu->properties->cssStyle;
				}
				else if (is_dir(DESIGN_DIRECTORY."/official/".$cu->properties->cssStyle))
				{
					$design = DESIGN_DIRECTORY."/official/".$cu->properties->cssStyle;
				}
			}
			define('CSS_STYLE', $design);

			// Image paths
			if (isset($cu) && $cu->properties->imageUrl != '' && $cu->properties->imageExt != '')
			{
				define('IMAGE_PATH',$cu->properties->imageUrl);
				define('IMAGE_EXT',$cu->properties->imageExt);
			}
			else
			{
				define("IMAGE_PATH", (ADMIN_MODE ? '../' : '') . $cfg->default_image_path->v);
				define("IMAGE_EXT","png");
			}
		}
	}

	function logAccess($target,$domain="",$sub="")
	{
		global $cfg;
		if ($cfg->accesslog->v == 1)
		{
			if (!isset($_SESSION['accesslog_sid']))
				$_SESSION['accesslog_sid'] = uniqid(mt_rand(), true);
			dbquery("
			INSERT INTO 
			accesslog 
			(target,timestamp,sid,sub,domain) 
			VALUES ('$target',UNIX_TIMESTAMP(),'". $_SESSION['accesslog_sid']."','$sub','$domain');");
		}
	}

	/**
	* Checks wether a given config file exists
	*/
	function configFileExists($file)	{
		return file_exists(getConfigFilePath($file));
	}

	function getConfigFilePath($file)	{
		return __DIR__ . '/../config/'.$file;
	}

	function writeConfigFile($file, $contents)	{
		file_put_contents(RELATIVE_ROOT."config/".$file, $contents);
	}

	/**
	* Fetches the contents of a JSON config file and returns it as an associative array
	*/
	function fetchJsonConfig($file)	{
		$path = getConfigFilePath($file);
		if (!file_exists($path))	{
			throw new EException("Config file $file not found!");
		}
		$data = json_decode(file_get_contents($path), true);
		if (json_last_error() != JSON_ERROR_NONE)	{
			throw new EException("Failed to parse config file $file (JSON error ".json_last_error().")!");
		}
		return $data;
	}

	function getLoginUrl($args=array()) {
		$url = Config::getInstance()->loginurl->v;
		if (empty($url)) {
			$url = "show.php?index=login";
			if (sizeof($args) > 0 && isset($args['page'])) {
				unset($args['page']);
			}
		}
		if (sizeof($args) > 0) {
			foreach ($args as $k => $v) {
				if (!stristr($url, '?')) {
					$url.="?";
				} else {
					$url.="&";
				}
				$url.= $k."=".$v;
			}
		}
		return $url;
	}

	function createZipFromDirectory($dir, $zipFile) {

		$zip = new ZipArchive();
		if ($zip->open($zipFile, ZIPARCHIVE::CREATE) !== TRUE) {
			throw new Exception("Cannot open ZIP file ".$zipFile);
		}

		// create recursive directory iterator
		$files = new RecursiveIteratorIterator (new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::LEAVES_ONLY);

		// let's iterate
		foreach ($files as $name => $file) {
			$new_filename = substr($name, strlen(dirname($dir)) + 1);
			if (is_file($file))
			{
				$zip->addFile($file, $new_filename);
			}
		}

		// close the zip file
		if (!$zip->close()) {
			throw new Exception("There was a problem writing the ZIP archive ".$zipFile);
		}
	}

	/**
	* Recursively remove a directory and its contents
	*/
	function rrmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") {
						rrmdir($dir."/".$object);
					} else {
						unlink($dir."/".$object);
					}
				}
			}
			reset($objects);
			rmdir($dir);
		}
	 }

	/**
	* Returns true if the debug mode is enabled
	* by checking the existence of the file config/debug
	*/
	function isDebugEnabled() {
		return file_exists(RELATIVE_ROOT.'config/debug');
	}

	/**
	* Returns true if script is run on command line
	*/
	function isCLI() {
		return php_sapi_name() === 'cli';
	}

	/**
	* Returns true if the specified unix command exists
	*/
	function unix_command_exists($cmd) {
		if (UNIX) {
			$returnVal = shell_exec("which $cmd 2>/dev/null");
			return (empty($returnVal) ? false : true);
		}
		return false;
	}

    function getAbsPath($path) {
        return (substr($path, 0, 1) != "/" ? realpath(RELATIVE_ROOT).'/' : '').$path;
    }

	/**
	* Textfunktionen einbinden
	*/
	include_once __DIR__ . '/text.inc.php';
