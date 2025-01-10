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


    #[Route("/api/tutorials/{tutorialId}", name:"api.tutorial.show", methods:['GET'])]
    public function showAction(Request $request, TokenContext $context, ?Tutorial $tutorial = null): JsonResponse
    {
        $data = [];

        if($tutorial ){
            if ($request->query->has('step')) {
                $currentStep = $request->query->getInt('step');
            } else {
                $currentStep = $this->tutorialUserProgressRepository->getUserProgress($context->getCurrentUser()->getData(), $tutorial);
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


    #[Route("/api/tutorials/{tutorialId}/close", name:"api.tutorial.close",methods:["PUT"])]
    public function closeAction(TokenContext $context, ?Tutorial $tutorial = null) : JsonResponse
    {
        $this->tutorialUserProgressRepository->closeTutorial($context->getCurrentUser()->getData(), $tutorial);

        return new JsonResponse();
    }
}
