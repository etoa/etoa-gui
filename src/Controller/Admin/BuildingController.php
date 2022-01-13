<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingPointRepository;
use EtoA\Building\BuildingRequirementRepository;
use EtoA\Form\Type\Admin\ObjectRequirementListType;
use EtoA\Ranking\RankingService;
use EtoA\Requirement\ObjectRequirement;
use EtoA\Requirement\RequirementsUpdater;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function DeepCopy\deep_copy;

class BuildingController extends AbstractAdminController
{
    public function __construct(
        private BuildingDataRepository $buildingDataRepository,
        private BuildingPointRepository $buildingPointRepository,
        private RankingService $rankingService,
        private BuildingRequirementRepository $buildingRequirementRepository,
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

    #[Route('/admin/buildings/requirements', name: 'admin.buildings.requirements')]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function requirements(Request $request): Response
    {
        $collection = $this->buildingRequirementRepository->getAll();
        $buildings = $this->buildingDataRepository->getBuildings();
        $requirements = [];
        $names = [];
        foreach ($buildings as $building) {
            $names[$building->id] = $building->name;
            $requirements[$building->id] = $collection->getAll($building->id);
        }

        $requirementsCopy = deep_copy($requirements);

        $form = $this->createForm(ObjectRequirementListType::class, $requirements, ['objectIds' => array_keys($buildings), 'objectNames' => $names]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ObjectRequirement[][] $updatedRequirements */
            $updatedRequirements = $form->getData();
            (new RequirementsUpdater($this->buildingRequirementRepository))->update($requirementsCopy, $updatedRequirements);

            $this->addFlash('success', 'Voraussetzungen aktualisiert');
        }

        return $this->render('admin/requirements/requirements.html.twig', [
            'objects' => $buildings,
            'form' => $form->createView(),
            'name' => 'Gebäude',
        ]);
    }
}
