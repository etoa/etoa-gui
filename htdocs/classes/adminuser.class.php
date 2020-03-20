<?php
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

class AdminUser {

	const tableName = "admin_users";

	private $id = null;
	private $valid = false;
	private $passwordString;
	private $forcePasswordChange = false;

	public $nick = "";
	public $name = "";
	public $email = "";
	public $tfaSecret = "";
	public $playerId = 0;
	public $boardUrl = "";
	public $userTheme = "";
	public $ticketEmail = false;
	public $locked = false;
	public $isContact = true;
	public $roles = array();

    public function __construct($id=null) {
		if ($id != null) {
			$res = DBManager::getInstance()->safeQuery("
			SELECT
				*
			FROM
				".self::tableName."
			WHERE
				user_id=?;", array($id));
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_assoc($res);
				$this->id = $id;
				$this->passwordString = $arr['user_password'];
				$this->valid = true;

				$this->nick = $arr['user_nick'];
				$this->forcePasswordChange = ($arr['user_force_pwchange'] > 0);

				$this->name = $arr['user_name'];
				$this->email = $arr['user_email'];
				$this->tfaSecret = $arr['tfa_secret'];
				$this->playerId = $arr['player_id'];
				$this->boardUrl = $arr['user_board_url'];
				$this->userTheme = $arr['user_theme'];
				$this->ticketEmail = ($arr['ticketmail'] > 0);
				$this->locked = ($arr['user_locked'] > 0);
				$this->roles = explode(",", $arr['roles']);
				$this->isContact = ($arr['is_contact'] > 0);
			}
		}
	}

	function isValid() {
		return $this->valid;
	}

	function __toString() {
		return "[ADMIN]".$this->nick;
	}

	function __get($field) {
		if ($field == "id")
			return $this->id;
		if ($field == "nick")
			return $this->nick;
		if ($field == "forcePasswordChange")
			return $this->forcePasswordChange;
	}

	function checkEqualPassword($newPassword) {
		return validatePasswort($newPassword, $this->passwordString);
	}

	function setPassword($password, $forceChange=false)	{
		$pws = saltPasswort($password);
		DBManager::getInstance()->safeQuery("
		UPDATE 
			".self::tableName."
		SET
			user_password=?,
			user_force_pwchange=?					
		WHERE
			user_id=?;", array(
			$pws,
			($forceChange ? 1 : 0),
			$this->id
		));
		$this->passwordString = $pws;
		$this->forcePasswordChange = false;
		return true;
	}

	/**
	* Saves the current record or create a new one
	* if the id is null
	*/
	function save()	{
		if ($this->id != null)
		{
			DBManager::getInstance()->safeQuery("
			UPDATE 
				".self::tableName."
			SET 
				user_nick=?,
				user_name=?,
				user_email=?,
				tfa_secret=?,
				user_board_url=?,
				user_theme=?,
				ticketmail=?,
				player_id=?,
				user_locked=?,
				is_contact=?,
				roles=?
			WHERE 
				user_id='".$this->id."';",
				array(
					$this->nick,
					$this->name,
					$this->email,
					$this->tfaSecret,
					$this->boardUrl,
					$this->userTheme,
					($this->ticketEmail ? 1 : 0),
					$this->playerId,
					($this->locked ? 1 : 0),
					($this->isContact ? 1: 0),
					implode(',', $this->roles)
				));
		} else {
			DBManager::getInstance()->safeQuery("
			INSERT INTO 
				".self::tableName."
			(
				user_nick,
				user_name,
				user_email,
				tfa_secret,
				user_board_url,
				user_theme,
				ticketmail,
				player_id,
				user_locked,
				is_contact,
				roles,
				user_password
			) VALUES (
				?,?,?,?,?,?,?,?,?,?,?,?
			);",
			array(
				$this->nick,
				$this->name,
				$this->email,
				$this->tfaSecret,
				$this->boardUrl,
				$this->userTheme,
				($this->ticketEmail ? 1 : 0),
				$this->playerId,
				($this->locked ? 1 : 0),
				($this->isContact ? 1: 0),
				implode(',', $this->roles),
				saltPasswort(generatePasswort())
			)); // Add a random password
			$this->id = mysql_insert_id();
			$this->valid = true;
		}
	}

	/**
	* Get formatted string of admin's roles
	*/
	function getRolesStr() {
		$rm = new AdminRoleManager();
		return $rm->getRolesStr($this->roles);
	}

	/**
	* Deletes current record
	*/
	function delete()	{
		if ($this->id != null)
		{
			DBManager::getInstance()->safeQuery("
			DELETE FROM 
				".self::tableName."
			WHERE user_id=?;", array($this->id));
		}
		return false;
	}

	function hasRole($roles) {
		$rm = new AdminRoleManager();
		return ($rm->checkAllowed($roles, $this->roles));
	}

	/**
	* Finds a user by it's nickname
	*/
	static function findByNick($nick) {
		$ures = DBManager::getInstance()->safeQuery("
		SELECT
			user_id
		FROM
			".self::tableName."
		WHERE
			LCASE(user_nick)=LCASE(?)
		LIMIT 1;", array($nick));
		if (mysql_num_rows($ures)>0)
		{
			$uarr = mysql_fetch_row($ures);
			return new AdminUser($uarr[0]);
		}
		return null;
	}

	/**
	* Get an array of all user id's and nicknames
	*/
	static function getArray() {
		$res = DBManager::getInstance()->query("
		SELECT 
			user_id,
			user_nick 
		FROM 
			".self::tableName.";");
		$rtn = array();
		while ($arr=mysql_fetch_row($res))
		{
			$rtn[$arr[0]] = $arr[1];
		}
		return $rtn;
	}

	/**
	* Get an array of all users
	*/
	static function getAll() {
		$res = DBManager::getInstance()->query("
		SELECT 
			user_id
		FROM 
			".self::tableName."
		ORDER BY
			user_nick;");
		$rtn = array();
		while ($arr = mysql_fetch_row($res))
		{
			$rtn[$arr[0]] = new AdminUser($arr[0]);
		}
		return $rtn;
	}

	/**
	* Count all users
	*/
	static function countAll() {
		$res = DBManager::getInstance()->query("
		SELECT
			COUNT(user_id) 
		FROM 
			".self::tableName.";");
		$arr = mysql_fetch_row($res);
		return (int) $arr[0];
	}
}
?>
