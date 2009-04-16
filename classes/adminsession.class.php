<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of adminsession
 *
 * @author Nicolas
 */
class AdminSession extends Session
{
	protected $namePrefix = "etoaadmin";

	protected $tableUser = "admin_users";
	protected $tableSession = "admin_user_sessions";
	protected $tableLog = "admin_user_sessionlog";

	public static function getInstance()
	{
		if (empty(self::$instance))
		{
			$className = __CLASS__;
			self::$instance = new $className(func_get_args());
		}
		return self::$instance;
	}

	function login($data)
	{
		if ($data['login_nick']!="" && $data['login_pw']!="" && !stristr($data['login_nick'],"'") && !stristr($data['login_pw'],"'"))
		{
			$sql = "
			SELECT
				user_id,
				user_nick,
				user_password
			FROM
				".$this->tableUser."
			WHERE
				LCASE(user_nick)='".strtolower($data['login_nick'])."'
			LIMIT 1;
			;";

			$ures = dbquery($sql);
			if (mysql_num_rows($ures)>0)
			{
				$uarr = mysql_fetch_assoc($ures);
				if ($uarr['user_password'] == pw_salt($data['login_pw'],$uarr['user_id']))
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
				}
			}
			else
			{
				$this->lastError = "Der Benutzername ist in dieser Runde nicht registriert!";
			}
		}
		else
		{
			$this->lastError = "Kein Benutzername oder Passwort eingegeben oder ungültige Zeichen verwendet!";
		}

		return false;
	}

}
?>
