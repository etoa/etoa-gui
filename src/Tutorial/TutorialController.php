<?php

namespace EtoA\Tutorial;

use EtoA\Core\TokenContext;
use Symfony\Component\HttpFoundation\JsonResponse;

class TutorialController
{
    /** @var TutorialUserProgressRepository */
    private $tutorialUserProgressRepository;

    public function __construct(TutorialUserProgressRepository $tutorialUserProgressRepository)
    {
        $this->tutorialUserProgressRepository = $tutorialUserProgressRepository;
    }

    public function closeAction(TokenContext $context, $tutorialId)
    {
        $this->tutorialUserProgressRepository->closeTutorial($context->getCurrentUser()->getId(), $tutorialId);

        return new JsonResponse();
    }
}
