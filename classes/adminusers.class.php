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
class AdminUsers
{
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
