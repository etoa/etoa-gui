<?PHP

use EtoA\Chat\ChatManager;

class ChatUserlistJsonResponder extends JsonResponder
{
    function getRequiredParams()
    {
        return array();
    }

    function validateSession()
    {
        global $s;
        return $s->chatValidate();
    }

    function getResponse($params)
    {
        /** @var ChatManager */
        $chatManager = $this->app[ChatManager::class];

        if ($chatManager->isUserOnline((int) $_SESSION['user_id'])) {
            return $chatManager->getUserOnlineList();
        }
        return array();
    }
}
