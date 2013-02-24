<?PHP
class ChatUserlistJsonResponder extends JsonResponder 
{
  function getRequiredParams() {
    return array();
  }

  function getResponse($params) {
    
    $data = array();

    if (ChatManager::isUserOnline($_SESSION['user_id'])) {
      return ChatManager::getUserOnlineList();
    }
    return array();

  }
}  
?>