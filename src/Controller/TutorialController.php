<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\Core\TokenContext;
use EtoA\Tutorial\TutorialUserProgressRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TutorialController extends AbstractController
{
    private TutorialUserProgressRepository $tutorialUserProgressRepository;

    public function __construct(TutorialUserProgressRepository $tutorialUserProgressRepository)
    {
        $this->tutorialUserProgressRepository = $tutorialUserProgressRepository;
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
