<?php
class AdminUser
{
	const tableUser = "admin_users";
	private $id = null;
	private $valid = false;
	private $passwordString;
	private $forcePasswordChange = false;
	
	public $nick = "";
	public $name = "";
	public $email = "";
	public $playerId = 0;
	public $boardUrl = "";
	public $userTheme = "";
	public $ticketEmail = false;
	
	function __construct($id=null)
	{
		if ($id != null) {
			$res = dbquery("
			SELECT
				*
			FROM
				".self::tableUser." u
			INNER JOIN
				admin_groups g
				ON u.user_admin_rank=g.group_id
				AND u.user_id=".$id."
			");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_assoc($res);
				$this->id = $id;
				$this->passwordString = $arr['user_password'];
				$this->valid = true;
				
				$this->adminRank = $arr['user_admin_rank'];
				
				$this->nick = $arr['user_nick'];
				$this->level = $arr['group_level'];
				$this->groupName = $arr['group_name'];
				$this->forcePasswordChange = ($arr['user_force_pwchange'] > 0);

				$this->name = $arr['user_name'];
				$this->email = $arr['user_email'];
				$this->playerId = $arr['player_id'];
				$this->boardUrl = $arr['user_board_url'];
				$this->userTheme = $arr['user_theme'];
				$this->ticketEmail = ($arr['ticketmail'] > 0);
			}
		}
	}

	function isValid()
	{
		return $this->valid;
	}

	function __toString()
	{
		return "[ADMIN]".$this->nick;
	}

	function __get($field)
	{
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
		dbquery("
		UPDATE 
			".self::tableUser."
		SET
			user_password='".$pws."',
			user_force_pwchange=".($forceChange ? 1 : 0)."					
		WHERE
			user_id=".$this->id.";");
		$this->passwordString = $pws;
		$this->forcePasswordChange = false;
		return true;
	}

	function save()	{
		if ($this->id != null) 
		{
			dbquery("
			UPDATE 
				".self::tableUser."
			SET 
				user_admin_rank='".$this->adminRank."',
				user_nick='".$this->nick."',
				user_name='".$this->name."',
				user_email='".$this->email."',
				user_board_url='".$this->boardUrl."',
				user_theme='".$this->userTheme."',
				ticketmail=".($this->ticketEmail ? 1 : 0).",
				player_id=".$this->playerId."
			WHERE 
				user_id='".$this->id."';");
		} 
		else
		{
			dbquery("
			INSERT INTO 
				".self::tableUser."
			(
				user_admin_rank,
				user_nick,
				user_name,
				user_email,
				user_board_url,
				user_theme,
				ticketmail,
				player_id,
				user_password
			) VALUES (
				'".$this->adminRank."',
				'".$this->nick."',
				'".$this->name."',
				'".$this->email."',
				'".$this->boardUrl."',
				'".$this->userTheme."',
				".($this->ticketEmail ? 1 : 0).",
				".$this->playerId.",
				'".saltPasswort(generatePasswort())."'
			);"); // Add a random password
			$this->id = mysql_insert_id();
		}
	}

	//
	// Statics
	//
	
	static function findByNick($nick)
	{
		$sql = "
		SELECT
			user_id
		FROM
			".self::tableUser."
		WHERE
			LCASE(user_nick)=LCASE(?)
		LIMIT 1;
		;";
		$ures = dbQuerySave($sql, array($nick));
		if (mysql_num_rows($ures)>0)
		{
			$uarr = mysql_fetch_row($ures);
			return new AdminUser($uarr[0]);
		}
		return null;
	}
	
	static function getArray()
	{
		$res = dbquery("
		SELECT 
			user_id,
			user_nick 
		FROM 
			".self::tableUser.";");
		$rtn = array();
		while ($arr=mysql_fetch_row($res))
		{
			$rtn[$arr[0]] = $arr[1];
		}
		return $rtn;
	}
	
	static function countAll() 
	{
		$res = dbquery("
		SELECT
			COUNT(user_id) 
		FROM 
			".self::tableUser.";");
		$arr = mysql_fetch_row($res);
		return $arr[0];
	}

}
?>
