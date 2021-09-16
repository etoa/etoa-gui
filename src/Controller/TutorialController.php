<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\Core\TokenContext;
use EtoA\Support\BBCodeUtils;
use EtoA\Tutorial\TutorialManager;
use EtoA\Tutorial\TutorialUserProgressRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TutorialController extends AbstractController
{
    private TutorialUserProgressRepository $tutorialUserProgressRepository;
    private TutorialManager $tutorialManager;

    public function __construct(TutorialUserProgressRepository $tutorialUserProgressRepository, TutorialManager $tutorialManager)
    {
        $this->tutorialUserProgressRepository = $tutorialUserProgressRepository;
        $this->tutorialManager = $tutorialManager;
    }

    /**
     * @Route("/api/tutorials/{tutorialId}", methods={"GET"}, name="api.tutorial.show")
     */
    public function showAction(Request $request, TokenContext $context, int $tutorialId): JsonResponse
    {
        if ($request->query->get('step')) {
            $currentStep = $request->query->getInt('step');
        } else {
            $currentStep = $this->tutorialManager->getUserProgress($context->getCurrentUser()->getId(), $tutorialId);
        }

        $tutorialText = $this->tutorialManager->getText($tutorialId, $currentStep);

        $data = [];
        if ($tutorialText !== null) {
            $data['title'] = $tutorialText->title;
            $data['content'] = BBCodeUtils::toHTML($tutorialText->content);
            $data['prev'] = $tutorialText->prev;
            $data['next'] = $tutorialText->next;

            $this->tutorialManager->setUserProgress($context->getCurrentUser()->getId(), $tutorialId, $tutorialText->step);
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/api/tutorials/{tutorialId}/close", methods={"PUT"}, name="api.tutorial.close")
     */
    public function closeAction(TokenContext $context, int $tutorialId): JsonResponse
    {
        $this->tutorialUserProgressRepository->closeTutorial($context->getCurrentUser()->getId(), $tutorialId);

        return new JsonResponse();
    }
}
