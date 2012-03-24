<?php
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
// $Id$
//////////////////////////////////////////////////////

/**
 * Providess session and authentication management
 * for admin area. See parent class for documentation
 *
 * @author Nicolas Perrenoud <mrcage@etoa.ch>
 */
class AdminSession extends Session
{
	const tableUser = "admin_users";
	const tableSession = "admin_user_sessions";
	const tableLog = "admin_user_sessionlog";

	protected $namePrefix = "admin";

	/**
	 * Returns the single instance of this class
	 *
	 * @return AdminSession Instance of this class
	 */
	public static function getInstance()
	{
		return parent::getInstance(__CLASS__);
	}

	function login($data)
	{
		self::cleanup();

		// TODO: Use preg_match
		if ($data['login_nick']!="" && $data['login_pw']!="" && !stristr($data['login_nick'],"'") && !stristr($data['login_pw'],"'"))
		{
			$sql = "
			SELECT
				user_id,
				user_nick,
				user_password
			FROM
				".self::tableUser."
			WHERE
				LCASE(user_nick)=LCASE(?)
			LIMIT 1;
			;";
			$ures = dbQuerySave($sql,array($data['login_nick']));
			if (mysql_num_rows($ures)>0)
			{
				$uarr = mysql_fetch_assoc($ures);
				if (validatePasswort($data['login_pw'], $uarr['user_password']))
				{
					$this->user_id = $uarr['user_id'];
					$this->user_nick = $uarr['user_nick'];
					$t = time();
					$this->time_login = $t;
					$this->time_action = $t;
					$this->registerSession();

					$this->firstView = true;
					return true;
				}
				else
				{
					$this->lastError = "Benutzer nicht vorhanden oder Passwort falsch!";
					$this->lastErrorCode = "pass";
				}
			}
			else
			{
				$this->lastError = "Benutzer nicht vorhanden oder Passwort falsch!";
				$this->lastErrorCode = "pass";
			}
		}
		else
		{
			$this->lastError = "Kein Benutzername oder Passwort eingegeben oder ungültige Zeichen verwendet!";
			$this->lastErrorCode = "name";
		}
		return false;
	}

	/**
	 * Checks if the current session is valid
	 *
	 * @return True if session is valid
	 */
	function validate()
	{
		if (isset($this->time_login))
		{
			$sql = "
			SELECT
				id
			FROM
				`".self::tableSession."`
			WHERE
				id='".session_id()."'
				AND `user_id`=".intval($this->user_id)."
				AND `ip_addr`='".$_SERVER['REMOTE_ADDR']."'
				AND `user_agent`='".$_SERVER['HTTP_USER_AGENT']."'
				AND `time_login`=".intval($this->time_login)."
			LIMIT 1
			;";
			$res = dbquery($sql);
			if (mysql_num_rows($res)>0)
			{
				$t = time();
				$cfg = Config::getInstance();
				if ($this->time_action + $cfg->admin_timeout->v > $t)
				{
					dbquery("
					UPDATE
						`".self::tableSession."`
					SET
						time_action=".$t."
					WHERE
						id='".session_id()."'
					;");
					$this->time_action = $t;
					return true;
				}
				else
				{
					$this->lastError = "Das Timeout von ".tf($cfg->admin_timeout->v)." wurde überschritten!";
					$this->lastErrorCode = "timeout";
				}
			}
			else
			{
				$this->lastError = "Session nicht mehr vorhanden!";
				$this->lastErrorCode = "nosession";
			}
		}
		else
		{
			$this->lastError = "Keine Session!";
			$this->lastErrorCode = "nologin";
		}
		self::unregisterSession();
		return false;
	}

	function registerSession()
	{
		$sql = "
		DELETE FROM
			`".self::tableSession."`
		WHERE
			user_id=".intval($this->user_id)."
			OR id='".session_id()."'
		;";
		dbquery($sql);

		$sql = "
		INSERT INTO
			`".self::tableSession."`
		(
			`id` ,
			`user_id`,
			`ip_addr`,
			`user_agent`,
			`time_login`
		)
		VALUES
		(
			'".session_id()."',
			".intval($this->user_id).",
			'".$_SERVER['REMOTE_ADDR']."',
			'".$_SERVER['HTTP_USER_AGENT']."',
			".intval($this->time_login)."
		)
		";
		$res = dbquery($sql);
	}

	function logout()
	{
		self::unregisterSession();
	}

	static function unregisterSession($sid=null,$logoutPressed=1)
	{
		if ($sid == null)
			$sid = self::getInstance()->id;

		$res = dbquery("
		SELECT
			*
		FROM
			`".self::tableSession."`
		WHERE
			id='".$sid."'
		;");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_assoc($res);
			dbquery("
			INSERT INTO
				`".self::tableLog."`
			(
				`session_id` ,
				`user_id`,
				`ip_addr`,
				`user_agent`,
				`time_login`,
				`time_action`,
				`time_logout`
			)
			VALUES
			(
				'".$arr['id']."',
				'".$arr['user_id']."',
				'".$arr['ip_addr']."',
				'".$arr['user_agent']."',
				'".$arr['time_login']."',
				'".$arr['time_action']."',
				'".($logoutPressed==1 ? time() : 0)."'
			)
			");
			dbquery("
			DELETE FROM
				`".self::tableSession."`
			WHERE
				id='".$sid."'
			;");
		}
		session_destroy();
		session_regenerate_id();
	}

	static function cleanup()
	{
		$cfg = Config::getInstance();

		$res = dbquery("
		SELECT
			id
		FROM
			`".self::tableSession."`
		WHERE
			time_action+".($cfg->admin_timeout->v)." < '".time()."'
		;");
		if (mysql_num_rows($res)>0)
		{
			while ($arr = mysql_fetch_row($res))
			{
				self::unregisterSession($arr[0],0);
			}
		}
	}

	static function cleanupLogs($threshold=0)
	{
		$cfg = Config::getInstance();
		if ($threshold>0)
			$tstamp = time() - $threshold;
		else
			$tstamp = time() - (24*3600*$cfg->sessionlog_store_days->p2);
		dbquery("
		DELETE FROM
			`".self::tableLog."`
		WHERE
			time_action < ".$tstamp.";");
		$nr = mysql_affected_rows();
		Log::add(Log::F_SYSTEM, Log::INFO, "$nr Adminsession-Logs die älter als ".date("d.m.Y, H:i",$tstamp)." sind wurden gelöscht.");
		return $nr;
	}

	static function kick($sid)
	{
		self::unregisterSession($sid);
	}
}
?>