<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Building\BuildingPointRepository;
use EtoA\Building\BuildingRequirementRepository;
use EtoA\Entity\BuildingListItem;
use EtoA\Form\Type\Admin\AddBuildingItemType;
use EtoA\Form\Type\Admin\BuildingSearchType;
use EtoA\Form\Type\Admin\ObjectRequirementListType;
use EtoA\Ranking\RankingService;
use EtoA\Requirement\ObjectRequirement;
use EtoA\Requirement\RequirementsUpdater;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function DeepCopy\deep_copy;

class BuildingController extends AbstractAdminController
{
    public function __construct(
        private readonly BuildingDataRepository        $buildingDataRepository,
        private readonly BuildingPointRepository       $buildingPointRepository,
        private readonly RankingService                $rankingService,
        private readonly BuildingRequirementRepository $buildingRequirementRepository,
        private readonly BuildingListItemRepository    $buildingRepository,
        private readonly PlanetRepository              $planetRepository,
    )
    {
    }

    #[Route('/admin/buildings/search', name: 'admin.buildings.search')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function search(Request $request): Response
    {
        $addItem = new BuildingListItem();
        $addForm = $this->createForm(AddBuildingItemType::class, $addItem);
        $addForm->handleRequest($request);
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $this->buildingRepository->addBuilding($addItem);

            $this->addFlash('success', 'Gebäude hinzugefügt');
        }

        return $this->render('admin/building/search.html.twig', [
            'addForm' => $addForm->createView(),
            'form' => $this->createForm(BuildingSearchType::class, $request->query->all()),
            'total' => $this->buildingRepository->count(),
        ]);
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
            $names[$building->getId()] = $building->getName();
            $requirements[$building->getId()] = $collection->getAll($building->getId());
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

    #[Route('/admin/buildings/cost-calculator', name: 'admin.buildings.cost-calculator')]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function costCalculator(): Response
    {
        return $this->render('admin/building/cost-calculator.html.twig');
    }
}
