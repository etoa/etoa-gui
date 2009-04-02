<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of adminusers
 *
 * @author Nicolas
 */
class AdminUser
{
	private $id,$nick;
	private $valid = false;

	function __construct($id)
	{
		$res = dbquery("
		SELECT
			*
		FROM
			admin_users
		WHERE
			user_id=".$id."
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_assoc($res);
			$this->id = $id;
			$this->nick = $arr['user_nick'];
			$this->valid = true;
		}
	}

	function isValid()
	{
		return $this->valid;
	}

	function __get($field)
	{
		if ($field == "id")
			return $this->id;
		if ($field == "nick")
			return $this->nick;
	}

	//put your code here
	static function getArray()
	{
		$res = dbquery("SELECT user_id,user_nick FROM admin_users;");
		$rtn = array();
		while ($arr=mysql_fetch_row($res))
		{
			$rtn[$arr[0]] = $arr[1];
		}
		return $rtn;
	}

}
?>
