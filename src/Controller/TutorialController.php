<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\Core\TokenContext;
use EtoA\Entity\Tutorial;
use EtoA\Support\BBCodeUtils;
use EtoA\Tutorial\TutorialManager;
use EtoA\Tutorial\TutorialUserProgressRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TutorialController extends AbstractController
{

    public function __construct(
        private readonly TutorialUserProgressRepository $tutorialUserProgressRepository,
        private readonly TutorialManager                $tutorialManager,
    )
    {
    }


    // The placeholder must be named "id": the EntityValueResolver looks for a request
    // attribute matching either the argument name or "id", so {tutorialId} left $tutorial null.
    #[Route("/api/tutorials/{id}", name:"api.tutorial.show", methods:['GET'])]
    public function showAction(Request $request, TokenContext $context, ?Tutorial $tutorial = null): JsonResponse
    {
        $data = [];

        if($tutorial ){
            if ($request->query->has('step')) {
                $currentStep = $request->query->getInt('step');
            } else {
                $currentStep = $this->tutorialUserProgressRepository->getUserProgress($context->getCurrentUser(), $tutorial);
            }

            $tutorialText = $this->tutorialManager->getText($tutorial, $currentStep);

            if ($tutorialText) {
                $data['title'] = $tutorialText->getTitle();
                $data['content'] = BBCodeUtils::toHTML($tutorialText->getContent());
                $data['prev'] = $tutorialText->prev;
                $data['next'] = $tutorialText->next;
            }
        }

        return new JsonResponse($data);
    }


    #[Route("/api/tutorials/{id}/close", name:"api.tutorial.close",methods:["PUT"])]
    public function closeAction(TokenContext $context, ?Tutorial $tutorial = null) : JsonResponse
    {
        if ($tutorial === null) {
            return new JsonResponse(['error' => 'Tutorial nicht gefunden!'], 404);
        }

        $this->tutorialUserProgressRepository->closeTutorial($context->getCurrentUser(), $tutorial);

        return new JsonResponse();
    }

    /**
     * Lets a closed tutorial show up again, used by the "Anzeigen" button in the settings.
     */
    #[Route("/api/tutorials/{id}/reopen", name:"api.tutorial.reopen", methods:["PUT"])]
    public function reopenAction(TokenContext $context, ?Tutorial $tutorial = null): JsonResponse
    {
        if ($tutorial === null) {
            return new JsonResponse(['error' => 'Tutorial nicht gefunden!'], 404);
        }

        $this->tutorialUserProgressRepository->reopenTutorial(
            (int) $context->getCurrentUser()->getId(),
            (int) $tutorial->getId()
        );

        return new JsonResponse();
    }
}
