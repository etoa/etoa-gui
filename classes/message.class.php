<?PHP

/**
* Class for handling messages
*
* @author Nicolas Perrenoud <mrcage@etoa.ch>
*/

class Message
{

	/**
	* Check for new messages
	*/
	static function checkNew($user_id)
	{
		$mres = dbquery("
			SELECT
				COUNT(message_id)
			FROM
				messages
			WHERE
				message_deleted='0'
				AND message_user_to='".$user_id."'
				AND message_read='0';
		");
		$count=mysql_fetch_row($mres);
		return $count[0];
	}

	/**
	* Sends a message from an user to another user
	*/
	static function sendFromUserToUser($fuid,$tuid,$subject,$text,$cat=0)
	{
		if ($cat==0) 
			$cat = USER_MSG_CAT_ID;
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
   		'".$fuid."',
   		'".$tuid."',
   		".time().",
   		".$cat."
   	);");
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
	* Delete message with given id
	*/
	static function delete($id)
	{
		dbquery("
			DELETE FROM 
				messages 
			WHERE 
				message_id=".$id.";");
		dbquery("
			DELETE FROM 
				message_data 
			WHERE 
				id=".$id.";");
	}

	/**
	* Alte Nachrichten löschen
	*/
	static function removeOld($threshold=0,$onlyDeleted=0)
	{
		$cfg = Config::getInstance();

		$nr = 0;
		if ($onlyDeleted==0)
		{
			// Normal old messages
			if ($threshold>0)
				$tstamp = time() - $threshold;
			else
				$tstamp=time()-(24*3600*$cfg->value('messages_threshold_days'));
			echo df($tstamp)." ".$threshold."<br/>";
			$res = dbquery("
				SELECT
					message_id
				 FROM
					messages
				WHERE
					message_archived=0
					AND message_timestamp<'".$tstamp."';		
			");
			if (mysql_num_rows($res)>0)
			{
				while ($arr=mysql_fetch_row($res))
				{
					dbquery("
						DELETE FROM
							message_data
						WHERE
							id=".$arr[0].";
					");				
				}			
			}
			dbquery("
				DELETE FROM
					messages
				WHERE
					message_archived=0
					AND message_timestamp<'".$tstamp."';
			");		
			$nr = mysql_affected_rows();
			add_log("4","Unarchivierte Nachrichten die älter als ".date("d.m.Y H:i",$tstamp)." sind wurden gelöscht!",time());
		}
		
		// Deleted
		if ($threshold>0)
			$tstamp = time() - $threshold;
		else
			$tstamp=time()-(24*3600*$cfg->p1('messages_threshold_days'));
		$res = dbquery("
			SELECT
				message_id
			 FROM
				messages
			WHERE
				message_deleted='1'
				AND message_timestamp<'".$tstamp."';		
		");
		if (mysql_num_rows($res)>0)
		{
			while ($arr=mysql_fetch_row($res))
			{
				dbquery("
					DELETE FROM
						message_data
					WHERE
						id=".$arr[0].";
				");				
			}			
		}
		$res = dbquery("
			DELETE FROM
				messages
			WHERE
				message_deleted='1'
				AND message_timestamp<'".$tstamp."';
		");		
		add_log("4","Unarchivierte Nachrichten die älter als ".date("d.m.Y H:i",$tstamp)." sind wurden gelöscht!",time());		
		$nr += mysql_affected_rows();
		return $nr;
	}
	
	

}

?>