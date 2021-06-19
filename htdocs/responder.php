<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

/**
* Simple request responder, returns a JSON data structure
*
* @author MrCage mrcage@etoa.ch
* @copyright Copyright (c) 2004 EtoA Gaming, www.etoa.ch
*/

header('Content-Type: application/json');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/inc/bootstrap.inc.php';
$app = require __DIR__ . '/../src/app.php';

$data = array();

$action = isset($_GET['action']) ? $_GET['action'] : null;

// Test if actions is valid
if ($action != null && preg_match('/^[a-z\_]+$/', $action) && strlen($action) <= 50) {

  try {
    $params = $_GET;
    unset($params['action']);
    $responder = JsonResponder::createFactory($action, $app);

    if($responder->validateSession())
    {
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
    }
    else
    {
      $data['error'] = 'You are not logged in.';
    }
  } catch (Exception $e) {
    $data['error'] = $e->getMessage();
  }

} else {
  $data['error'] = 'Invalid action';
}

echo json_encode($data);
