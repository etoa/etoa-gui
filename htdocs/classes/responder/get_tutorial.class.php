<?PHP
class GetTutorialJsonResponder extends JsonResponder
{
  function getRequiredParams() {
    return array('id');
  }

  function getResponse($params) {

    $data = array();

	$ttm = new TutorialManager();

	$currentStep = 0;
	if (isset($params['step'])) {
		$currentStep = $params['step'];
	} else if (isset($_SESSION['user_id'])) {
		$currentStep = $ttm->getUserProgess($_SESSION['user_id'], $params['id']);
	}

	$tutorialText = $ttm->getText($params['id'], $currentStep);
	if ($tutorialText != null) {
		$data['title'] = $tutorialText->title;
		$data['content'] = text2html($tutorialText->content);
		$data['prev'] = $tutorialText->prev;
		$data['next'] = $tutorialText->next;

	    if (isset($_SESSION['user_id']))
		{
			$ttm->setUserProgess($_SESSION['user_id'], $params['id'], $tutorialText->step);
		}
	}
    return $data;
  }
}
?>