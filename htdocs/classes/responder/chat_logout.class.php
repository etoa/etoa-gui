<?PHP
class ChatLogoutJsonResponder extends JsonResponder
{
  function getRequiredParams() {
    return array();
  }

  function validateSession()
  {
    global $s;
    return $s->chatValidate();
  }

  function getResponse($params) {
    $data = array();

    if(!isset($_SESSION['user_id']) || !isset($_SESSION['user_nick']))
    {
      return array();
    }

    ChatManager::logoutUser($_SESSION['user_id']);
    ChatManager::sendSystemMessage($_SESSION['user_nick'].' verlässt den Chat.');

    return $data;
  }
}
?>