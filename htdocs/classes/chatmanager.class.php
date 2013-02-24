<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
// $Id$
//////////////////////////////////////////////////////

class ChatManager {

  /**
  * Inserts a system message into the chat table
  */
	static function sendSystemMessage($msg)
	{
		dbQuerySave("
      INSERT INTO
        chat
      (
        timestamp,
        text
      )
      VALUES
      (
        UNIX_TIMESTAMP(),?
      );",
      array($msg)
    );	
	}	
	
  /**
  * Remove a user from the chat user list by 
  * insterting a kick reason into the chat user table
  */
	static function kickUser($uid, $msg = '') 
	{
		if($msg == '')
		{
			$msg = 'Kicked by Admin';
		}
		$res = dbQuerySave('
      UPDATE
        chat_users
      SET
        kick="'.mysql_real_escape_string($msg).'"
      WHERE
        user_id=?', 
      array($uid)
    );
		if (mysql_affected_rows()>0)
		{
			return true;
		}
		return false;
	}
  
  /**
  * Inserts or updates a user in the chat user table
  */
  static function updateUserEntry($id, $nick) {
    dbQuerySave('
      REPLACE INTO
        chat_users
      (
        timestamp,
        user_id,
        nick
      )
      VALUES
      (
        UNIX_TIMESTAMP(),?,?
      )',
      array($id, $nick)
    );  
  }
  
  /**
  * Performs an ordinary logout of an user
  */
  static function logoutUser($userId) {
    dbQuerySave('
      DELETE FROM
        chat_users
      WHERE
        user_id=?;', 
      array($userId)
    );
  }

  /**
  * Gets the configured welcome message
  */
  static function getWelcomeMessage($nick) {
    return str_replace(
      array('%nick%'),
      array($nick),
      Config::getInstance()->chat_welcome_message->v
    );
  }
  
  /**
  * Returns true if the specified user is online in the chat
  */
  static function isUserOnline($userId) {
    $res = dbQuerySave('
      SELECT
        COUNT(user_id)
      FROM
        chat_users
      WHERE
        user_id =?',
      array($userId)
    );
    return mysql_num_rows($res) > 0;
  }
  
  /**
  * Gets a list of users currently being online in the chat
  */
  static function getUserOnlineList() {
    $data = array();
    $res = dbquery('
    SELECT
      nick,
      user_id
    FROM
      chat_users
    ORDER BY
      nick;');
    $nr = mysql_num_rows($res);
    if ($nr > 0)
    {
      while ($arr=mysql_fetch_assoc($res))
      {
        $data[] = array(
          'id' => $arr['user_id'],
          'nick' => $arr['nick']
        );
      }
    }
    return $data;
  }
  
	/**
  * Cleans users from the chat user table if timeout exceeded
  */
	static function cleanUpUsers()
	{
		$res = dbquery('
      SELECT user_id,nick 
      FROM chat_users 
      WHERE timestamp < UNIX_TIMESTAMP() - '.intval(Config::getInstance()->chat_user_timeout->v).';'
    );
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_assoc($res);
			self::sendSystemMessage($arr['nick'].' verlässt den Chat (Timeout).');
			dbquery('DELETE FROM chat_users WHERE user_id = '.$arr['user_id'].';');		
		}
	}
  
  /**
  * Removes old messages from the chat table
  * Keeps only the last X messages
  */
  static function cleanUpMessages() 
  {
		$res = dbquery("
      SELECT id 
      FROM chat 
      ORDER BY id DESC 
      LIMIT ".intval(Config::getInstance()->chat_recent_messages->v).",1;"
    );
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_row($res);
			dbquery("DELETE FROM chat WHERE id < ".$arr[0]);		
		}
  }
  
}
?>