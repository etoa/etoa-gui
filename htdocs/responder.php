<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
// $Id: index.php 1417 2012-03-24 13:51:47Z etoa-live $
//////////////////////////////////////////////////////

/**
* Simple request responder, returns a JSON data structure
* 
* @author MrCage mrcage@etoa.ch
* @copyright Copyright (c) 2004 EtoA Gaming, www.etoa.ch
*/

header('Content-Type: application/json');

require_once("inc/bootstrap.inc.php");

$loggedIn = false;
if ($s->validate(0)) {
  $cu = new CurrentUser($s->user_id);
  if ($cu->isValid) {
    $loggedIn = true;
  }
}

$data = array();

if ($loggedIn) {
  
  $action = isset($_GET['action']) ? $_GET['action'] : null;

  // Test if actions is valid
  if ($action != null && preg_match('/^[a-z\_]+$/', $action) && strlen($action) <= 50) {
    
    try {
      $params = $_GET;
      unset($params['action']);
      $responder = JsonResponder::createFactory($action);

      if (!$responder->validateParams($params)) {
        throw new Exception("Insufficient parameters specified! Required: ".implode(', ', $responder->getRequiredParams()));
      }

      ob_start();

      $data = $responder->getResponse($params);

      // Handle overflow data
      $overflow = ob_get_clean();
      if ($overflow != "" && $overflow != b"\xEF\xBB\xBF") {
        $data['overflow'] = $overflow;
      }
      
    } catch (Exception $e) {
      $data['error'] = $e->getMessage();
    }
    
  } else {
    $data['error'] = 'Invalid action';
  }
  
} else {
  $data['error'] = "Not logged in";
}

echo json_encode($data);
?>