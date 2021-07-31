<?PHP

use EtoA\Tutorial\TutorialManager;

class GetTutorialJsonResponder extends JsonResponder
{
    function getRequiredParams()
    {
        return array('id');
    }

    function getResponse($params)
    {
        $data = array();

        /** @var TutorialManager $tutorialManager */
        $tutorialManager = $this->app[TutorialManager::class];

        $currentStep = 0;
        if (isset($params['step'])) {
            $currentStep = (int) $params['step'];
        } else if (isset($_SESSION['user_id'])) {
            $currentStep = $tutorialManager->getUserProgress((int) $_SESSION['user_id'], (int) $params['id']);
        }

        $tutorialText = $tutorialManager->getText((int) $params['id'], $currentStep);
        if ($tutorialText != null) {
            $data['title'] = $tutorialText->title;
            $data['content'] = text2html($tutorialText->content);
            $data['prev'] = $tutorialText->prev;
            $data['next'] = $tutorialText->next;

            if (isset($_SESSION['user_id'])) {
                $tutorialManager->setUserProgress((int) $_SESSION['user_id'], (int) $params['id'], $tutorialText->step);
            }
        }
        return $data;
    }
}
