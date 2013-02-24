<?PHP
class ChatPushJsonResponder extends JsonResponder 
{
  function getRequiredParams() {
    return array('ctext');
  }

  function getResponse($params) {

    $data = array();
  
    if (isset($_SESSION['user_id']))
    {
      $admin = 0;
      $res = dbquery('
      SELECT
        user_chatadmin,admin
      FROM
        users
      WHERE
        user_id='.$_SESSION['user_id'].';');
      if (mysql_num_rows($res)>0) // Should always be true, otherwise the user does not exist
      {
        // chatadmins = 2, admins = 1, noadmin-entwickler = 3,
        // leiter team community = 4, admin-entwickler = 5
        $arr = mysql_fetch_assoc($res);
        if($arr['admin'] == 1)
        {
          if($arr['user_chatadmin'] == 3)
          {
            $admin = 5; // Entwickler mit Adminrechten
          }
          else
          {
            $admin = 1; // Admin
          }
        }
        elseif ($arr['user_chatadmin'] == 1)
          $admin = 2; // Chatadmin
        elseif ($arr['user_chatadmin'] == 2)
          $admin = 4; // Leiter Team Community
        elseif($arr['admin'] == 2)
          $admin = 3; // Entwickler ohne Adminrechte
      }
      else
      {
        return array('cmd' => 'nu'); // no user
      }

      $ct = $params['ctext'];
      
      // Detect command
      $m = array();
      $words = StringUtils::splitBySpaces($ct);
      $commandMatch = array();
      

      // Handle command
      if (count($words) > 0 && preg_match('#^/([a-z]+)$#i', array_shift($words), $commandMatch)) 
      {
        $command = strtolower($commandMatch[1]);
        
        // Kick user
        if ($command == "kick" && $admin > 0 && $admin != 3)
        {
          if (isset($words[0])) 
          {
            $uid = User::findIdByNick($words[0]);
            if ($uid>0)
            {
              $msg = (count($words) > 1) ? implode(' ', array_slice($words, 1)) : '';
              if (ChatManager::kickUser($uid, $msg))
              {
                 ChatManager::sendSystemMessage($words[0].' wurde gekickt!'.($msg != '' ? ' Grund: '.$msg : ''));
              }
              else
              {
                return array(
                  'cmd' => 'aa',
                  'msg' => 'User is not online in chat!'
                );
              }
            }
            else
            {
              return array(
                'cmd' => 'aa',
                'msg' => 'A user with this nick does not exist!'
              );
            }
          }
          else
          {
            return array(
              'cmd' => 'aa',
              'msg' => 'No user specified!'
            );
          }          
        }
        
        // Ban user
        elseif ($command == "ban" && $admin > 0 && $admin != 3)
        {
          if (isset($words[0])) 
          {
            $uid = User::findIdByNick($words[0]);
            if ($uid>0)
            {
              $text = (count($words) > 1) ? implode(' ', array_slice($words, 1)) : '';
              dbquery('
              INSERT INTO
                chat_banns
                (user_id,reason,timestamp)
              VALUES ('.$uid.',"'.mysql_real_escape_string($text).'",'.time().')
              ON DUPLICATE KEY UPDATE
              timestamp='.time().',reason="'.mysql_real_escape_string($text).'"');
              ChatManager::kickUser($uid, $text);
              ChatManager::sendSystemMessage($words[0].' wurde gebannt! Grund: '.$text);
            }
            else
            {
              return array(
                'cmd' => 'aa',
                'msg' => 'A user with this nick does not exist!'
              );              
            }
          }
          else
          {
            return array(
              'cmd' => 'aa',
              'msg' => 'No user specified!'
            );            
          }          
        }
    
        elseif ($command == "unban" && $admin > 0 && $admin != 3)
        {
          if (isset($words[0])) 
          {
            $uid = User::findIdByNick($words[0]);
            if ($uid>0)
            {
              dbquery('DELETE FROM
                chat_banns
              WHERE
                user_id='.$uid.';');
              if (mysql_affected_rows()>0)
              {
                return array(
                  'cmd' => 'aa',
                  'msg' => 'Unbanned '.$words[0].'!'
                );            
              }
              else
              {
                return array(
                  'cmd' => 'aa',
                  'msg' => 'A user with that nick is not banned!'
                );
              }
            }
            else
            {
              return array(
                'cmd' => 'aa',
                'msg' => 'A user with this nick does not exist!'
              );            
            }
          }
          else
          {
            return array(
              'cmd' => 'aa',
              'msg' => 'No user specified!'
            );            
          }
        }
    
        elseif ($command == "banlist" && $admin > 0 && $admin != 3)
        {
          $res = dbquery('SELECT
            user_id,reason,timestamp
          FROM
            chat_banns
          ;');
          if (mysql_num_rows($res)>0)
          {
            $list = array();
            while ($arr=mysql_fetch_assoc($res))
            {
              $tu = new User($arr['user_id']);
              if ($tu->isValid)
              {
                $list[] = array(
                  'nick' => $tu->nick,
                  'reason' => $arr['reason'],
                  'date' => df($arr['timestamp'])
                );
              }
            }
            return array(
              'cmd' => 'bl',
              'list' => $list
            ); 
          }
          else
          {
            return array(
              'cmd' => 'aa',
              'msg' => 'Bannliste leer!'
            );             
          }
        }        
        
        // Unknown command
        else
        {
          return array(
            'cmd' => 'aa',
            'msg' => 'Unknown command \''.$command.'\'!'
          );            
        }        
      }
     
      // Handle normal message
      else
      {
        $hash = md5($ct);
        // Woo Hoo, Md5 hashtable
        if ($ct!='' && (!isset($_SESSION['lastchatmsg']) || $_SESSION['lastchatmsg']!= $hash) )
        {
          dbquery("INSERT INTO
            chat
          (
            timestamp,
            nick,
            text,
            color,
            user_id,
            admin
          )
          VALUES
          (
            ".time().",
            '".$_SESSION['user_nick']."',
            '".mysql_real_escape_string(($ct))."',
            '".(isset($_SESSION['ccolor'])?('#'.$_SESSION['ccolor']):'')."',
            '".$_SESSION['user_id']."',
            '".$admin."'
          );");
          dbquery("INSERT INTO
            chat_log
          (
            timestamp,
            nick,
            text,
            color,
            user_id,
            admin
          )
          VALUES
          (
            ".time().",
            '".$_SESSION['user_nick']."',
            '".mysql_real_escape_string(($ct))."',
            '".(isset($_SESSION['ccolor'])?('#'.$_SESSION['ccolor']):'')."',
            '".$_SESSION['user_id']."',
            '".$admin."'
          );");			
          $_SESSION['lastchatmsg']=$hash;
        }
        else
        {
          // zweimal gleiche Nachricht nacheinander
          return array('cmd' => 'de'); 
        }
      }
    }
    else
    {
      // !isset $s[userid] => not logged in
      return array('cmd' => 'nl'); 
    }
  
    return $data;
  }
}  
?>