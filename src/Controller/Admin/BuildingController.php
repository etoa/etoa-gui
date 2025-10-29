<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Entity\BuildingListItem;
use EtoA\Entity\BuildingRequirements;
use EtoA\Form\Type\Admin\AddBuildingItemType;
use EtoA\Form\Type\Admin\BuildingSearchType;
use EtoA\Form\Type\Admin\ObjectRequirementListType;
use EtoA\Ranking\RankingService;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BuildingController extends AbstractAdminController
{
    public function __construct(
        private readonly BuildingDataRepository        $buildingDataRepository,
        private readonly RankingService                $rankingService,
        private readonly BuildingListItemRepository    $buildingRepository
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
            $addItem->setUser($addForm->get('entity')->getData()->getUser());
            $this->buildingRepository->persist($addItem);
            $this->buildingRepository->save();

            $this->addFlash('success', 'Gebäude hinzugefügt');
        }

        return $this->render('admin/building/search.html.twig', [
            'addForm' => $addForm->createView(),
            'form' => $this->createForm(BuildingSearchType::class, $request->query->all()),
            'total' => $this->buildingRepository->count([]),
        ]);
    }

    #[Route("/admin/buildings/points", name: "admin.buildings.points")]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function points(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('calc', SubmitType::class, ['label' => 'Neu berechnen'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $numBuildings = $this->rankingService->calcBuildingPoints();
            $this->addFlash('success', sprintf("Die Punkte von %s Gebäude wurden aktualisiert!", $numBuildings));
        }

        return $this->render('admin/building/points.html.twig', [
            'buildings' => $this->buildingDataRepository->getBuildingNames(true),
            'form' => $form
        ]);
    }

    #[Route('/admin/buildings/requirements', name: 'admin.buildings.requirements')]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function requirements(Request $request): Response
    {
        $buildings = $this->buildingDataRepository->getBuildings();
        $form = $this->createForm(ObjectRequirementListType::class, $buildings,['type'=>BuildingRequirements::class]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->buildingRepository->save();

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
