<?php
/**
 * Providess session and authentication management
 * for user login areas
 *
 * @author Nicolas Perrenoud <mrcage@etoa.ch>
 */
class Session implements ISingleton
{
	//
	// Singleton code
	//
	private static $instance;
	final public static function getInstance()
	{
		if (empty(self::$instance))
		{
			$cname = __CLASS__;
			self::$instance = new $cname(func_get_args());
		}
		return self::$instance;
	}
	final function __clone() {}

	//
	// Class variables and constants
	//
	const namePrefix = "etoa";
	private $lastError;
	private $firstView=false;

	/**
	 * The constructor defines the session hash function to be used
	 * and names and initiates the session
	 */
	protected function __construct()
	{
		ini_set('session.hash_function', 1); // Use SHA1 hash
		session_name(self::namePrefix.ROUNDID); // Set session name based on round name
		session_start();	// Start the session
	}

	function __get($field)
	{
		if ($field=="lastError")
			return ($this->lastError!=null) ? $this->lastError : "";
		if ($field=="id")
			return session_id();
		if ($field=="firstView")
			return $this->firstView;
		if (isset($_SESSION[$field]))
			return $_SESSION[$field];
		return null;
	}

	function __isset($field)
	{
		return isset($_SESSION[$field]);
	}

	function __unset($field)
	{
		unset($_SESSION[$field]);
	}
	
	function __set($field,$value)
	{
		if ($field=="lastError" || $field=="id")
		{
			error_msg("Private Variable!");
			return false;
		}
		else
		{
			$_SESSION[$field] = $value;
		}
		return true;
	}

	function login($data)
	{
		if ($data['login_nick']!="" && $data['login_pw']!="" && !stristr($data['login_nick'],"'") && !stristr($data['login_pw'],"'"))
		{
			$ures = dbquery("
			SELECT
				user_id,
				user_registered,
				user_password,
				user_password_temp
			FROM
				users
			WHERE
				LCASE(user_nick)='".strtolower($data['login_nick'])."'
			LIMIT 1;
			;");
			if (mysql_num_rows($ures)>0)
			{
				$uarr = mysql_fetch_assoc($ures);
				if ($uarr['user_password'] == pw_salt($data['login_pw'],$uarr['user_registered'])
					|| ($uarr['user_password_temp']!="" && $uarr['user_password_temp']==$data['login_pw']))
				{
					$this->user_id = $uarr['user_id'];
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
					forward(LOGINSERVER_URL."?page=err&err=pass","Passwort-Fehler",$this->lastError);
				}
			}
			else
			{
				$this->lastError = "Der Benutzername ist in dieser Runde nicht registriert!";
				forward(LOGINSERVER_URL."?page=err&err=pass","Benutzer nicht vorhanden",$this->lastError);
			}
		}
		else
		{
			$this->lastError = "Kein Benutzername oder Passwort eingegeben oder ungültige Zeichen verwendet!";
			forward(LOGINSERVER_URL."?page=err&err=name","Nickname oder Password fehlerhaft",$this->lastError);
		}
		
		return false;
	}

	function validate()
	{
		$res = dbquery("
		SELECT
			id
		FROM
			`user_sessions`
		WHERE
			id='".session_id()."'
			AND `user_id`=".intval($this->user_id)."
			AND `ip_addr`='".$_SERVER['REMOTE_ADDR']."'
			AND `user_agent`='".$_SERVER['HTTP_USER_AGENT']."'
			AND `time_login`=".intval($this->time_login)."
		LIMIT 1
		;");
		if (mysql_num_rows($res)>0)
		{
			$t = time();
			$cfg = Config::getInstance();
			if ($this->time_action + $cfg->user_timeout->v > $t)
			{
				dbquery("
				UPDATE
					`user_sessions`
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
				$this->lastError = "Das Timeout von ".tf($cfg->user_timeout->v)." wurde überschritten!";
			}
		}
		else
		{
			$this->lastError = "Session nicht mehr vorhanden!";
		}
		self::unregisterSession(session_id());

		forward(LOGINSERVER_URL."?page=err&err=nosession","Ungültige Session",$this->lastError);

		return false;
	}

	function logout()
	{
		self::unregisterSession(session_id());
		forward(LOGINSERVER_URL.'?page=logout',"Lgout");
	}

	function registerSession()
	{
		dbquery("
		DELETE FROM
			`user_sessions`
		WHERE
			user_id=".intval($this->user_id)."
			OR id='".session_id()."'
		;");
		dbquery("
		INSERT INTO
			`user_sessions`
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
		");
	}

	static function unregisterSession($sid,$logoutPressed=1)
	{
		$res = dbquery("
		SELECT
			*
		FROM
			`user_sessions`
		WHERE
			id='".$sid."'
		;");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_assoc($res);
			dbquery("
			INSERT INTO
				`user_sessionlog`
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
				`user_sessions`
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
		$this->time_action +
		$res = dbquery("
		SELECT
			id
		FROM
			`user_sessions`
		WHERE
			time_action+".($cfg->user_timeout->v)." < '".time()."'
		;");
		if (mysql_num_rows($res)>0)
		{
			while ($arr = mysql_fetch_row($res))
			{
				self::unregisterSession($arr[0],0);
			}
		}
	}
}
?>
