<?PHP

use EtoA\Chat\ChatManager;

class ChatLogoutJsonResponder extends JsonResponder
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
        $data = array();

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_nick'])) {
            return array();
        }

        /** @var ChatManager */
        $chatManager = $this->app[ChatManager::class];

        $chatManager->logoutUser((int) $_SESSION['user_id']);
        $chatManager->sendSystemMessage($_SESSION['user_nick'] . ' verlässt den Chat.');

        return $data;
    }
}
