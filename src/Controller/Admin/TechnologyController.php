<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Ranking\RankingService;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyPointRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TechnologyController extends AbstractAdminController
{
    public function __construct(
        private TechnologyDataRepository $technologyDataRepository,
        private TechnologyPointRepository $technologyPointRepository,
        private RankingService $rankingService,
    ) {
    }

    #[Route("/admin/technology/points", name: "admin.technology.points")]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function points(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $numTechnologies = $this->rankingService->calcTechPoints();
            $this->addFlash('success', sprintf("Die Punkte von %s Technologien wurden aktualisiert!", $numTechnologies));
        }

        return $this->render('admin/technology/points.html.twig', [
            'technologyNames' => $this->technologyDataRepository->getTechnologyNames(true),
            'pointsMap' => $this->technologyPointRepository->getAllMap(),
        ]);
    }
}
