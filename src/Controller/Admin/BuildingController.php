<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingPointRepository;
use EtoA\Ranking\RankingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BuildingController extends AbstractAdminController
{
    public function __construct(
        private BuildingDataRepository $buildingDataRepository,
        private BuildingPointRepository $buildingPointRepository,
        private RankingService $rankingService,
    ) {
    }

    #[Route("/admin/buildings/points", name: "admin.buildings.points")]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function points(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $numBuildings = $this->rankingService->calcBuildingPoints();
            $this->addFlash('success', sprintf("Die Punkte von %s Gebäude wurden aktualisiert!", $numBuildings));
        }

        return $this->render('admin/building/points.html.twig', [
            'buildingNames' => $this->buildingDataRepository->getBuildingNames(true),
            'pointsMap' => $this->buildingPointRepository->getAllMap(),
        ]);
    }
}
