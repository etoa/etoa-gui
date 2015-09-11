<?PHP
class CloseTutorialJsonResponder extends JsonResponder 
{
  function getRequiredParams() {
    return array('id');
  }

  function getResponse($params) {
    
    $data = array();
        
	$ttm = new TutorialManager();
	if (isset($_SESSION['user_id']))
	{
		$ttm->closeTutorial($_SESSION['user_id'], $params['id']);
	}
	
    return $data;
  }
}
?>