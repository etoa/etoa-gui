<?PHP

use EtoA\Chat\ChatBanRepository;
use EtoA\Chat\ChatUserRepository;

class ChatPollJsonResponder extends JsonResponder
{
  function getRequiredParams() {
    return array('minId', 'chanId');
  }

  function validateSession()
  {
    global $s;
    return $s->chatValidate();
  }

  function getResponse($params) {

    $data = array();

    // Check user is logged in
    if (isset($_SESSION['user_id']))
    {
        $userId = (int) $_SESSION['user_id'];
        /** @var ChatBanRepository $chatBanRepository */
        $chatBanRepository = $this->app[ChatBanRepository::class];
        $ban = $chatBanRepository->getUserBan($userId);

        if ($ban !== null) {
            return [
                'cmd' => 'bn',
                'msg' => StringUtils::replaceAsciiControlCharsUnicode($ban->reason),
            ];
        }

        /** @var ChatUserRepository $chatUserRepository */
        $chatUserRepository = $this->app[ChatUserRepository::class];
        $chatUser = $chatUserRepository->getChatUser($userId);

        if ($chatUser !== null) {
            if ($chatUser->kick !== null) {
                $chatUserRepository->deleteUser($userId);
                 return [
                    'cmd' => 'ki',
                    'msg' => StringUtils::replaceAsciiControlCharsUnicode($chatUser->kick)
                ];
            }

        } else {
            // User does not exist yet
            ChatManager::sendSystemMessage($_SESSION['user_nick'].' betritt den Chat.');
            $data['cmd'] = 'li';
            $data['msg'] = ChatManager::getWelcomeMessage($_SESSION['user_nick']);
      }

      // User exists, not kicked, not banned.
      ChatManager::updateUserEntry($_SESSION['user_id'], $_SESSION['user_nick']);

      // Query new messages
      $res = dbquery('
      SELECT
        id,
        nick,
        timestamp,
        text,
        color,
        user_id,
        admin
      FROM
        chat
      WHERE
        id>'.intval($params['minId']).'
        AND channel_id='.intval($params['chanId']).'
      ORDER BY
        timestamp ASC
      ');

      $lastid = intval($params['minId']);
      // check whether 'login' has been set
      if(!isset($data['cmd']))
      {
        $data['cmd'] = 'up';
      }
      $data['out'] = array();
      if (mysql_num_rows($res)>0)
      {
        // new messages available
        while ($arr=mysql_fetch_assoc($res))
        {
         $data['out'][] = array(
            'id' => $arr['id'],
            'text' => StringUtils::replaceAsciiControlChars(htmlspecialchars($arr['text'])),
            'time' => date("H:i",$arr['timestamp']),
            'color' => $arr['color'],
            'userId' => $arr['user_id'],
            'nick' => $arr['nick'],
            'admin' => $arr['admin']
          );
          $lastid = $arr['id'];
        }
      }
      $data['lastId'] = intval($lastid);
    }
    else
    {
      // 'lo' = logged out
      return array(
        'cmd' => 'lo'
      );
    }

    return $data;
  }
}
?>
