<?PHP

use EtoA\Chat\ChatBanRepository;
use EtoA\Chat\ChatManager;
use EtoA\Chat\ChatRepository;
use EtoA\Chat\ChatUserRepository;
use EtoA\Support\StringUtils;

class ChatPollJsonResponder extends JsonResponder
{
    function getRequiredParams()
    {
        return array('minId', 'chanId');
    }

    function validateSession()
    {
        global $s;
        return $s->chatValidate();
    }

    function getResponse($params)
    {

        $data = array();

        // Check user is logged in
        if (isset($_SESSION['user_id'])) {
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

            /** @var ChatManager */
            $chatManager = $this->app[ChatManager::class];

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
                $chatManager->sendSystemMessage($_SESSION['user_nick'] . ' betritt den Chat.');
                $data['cmd'] = 'li';
                $data['msg'] = $chatManager->getWelcomeMessage($_SESSION['user_nick']);
            }

            // User exists, not kicked, not banned.
            $chatManager->updateUserEntry((int) $_SESSION['user_id'], $_SESSION['user_nick']);

            // Query new messages
            /** @var ChatRepository $chatRepository */
            $chatRepository = $this->app[ChatRepository::class];
            $messages = $chatRepository->getMessagesAfter((int) $params['minId'], (int) $params['chanId']);

            $lastid = intval($params['minId']);
            // check whether 'login' has been set
            if (!isset($data['cmd'])) {
                $data['cmd'] = 'up';
            }
            $data['out'] = array();
            foreach ($messages as $message) {
                $data['out'][] = array(
                    'id' => $message->id,
                    'text' => StringUtils::replaceAsciiControlChars(htmlspecialchars($message->text)),
                    'time' => date("H:i", $message->timestamp),
                    'color' => $message->color,
                    'userId' => $message->userId,
                    'nick' => $message->nick,
                    'admin' => $message->admin
                );
                $lastid = $message->id;
            }
            $data['lastId'] = $lastid;
        } else {
            // 'lo' = logged out
            return array(
                'cmd' => 'lo'
            );
        }

        return $data;
    }
}
