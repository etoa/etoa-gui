<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Entity\Ship;
use EtoA\Entity\ShipListItem;
use EtoA\Form\Type\Admin\AddShipListType;
use EtoA\Form\Type\Admin\ObjectRequirementListType;
use EtoA\Form\Type\Admin\ShipSearchType;
use EtoA\Form\Type\Admin\ShipXpCalculatorType;
use EtoA\Ranking\RankingService;
use EtoA\Requirement\ObjectRequirement;
use EtoA\Requirement\RequirementsUpdater;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Ship\ShipRequirementRepository;
use EtoA\Ship\ShipSearch;
use EtoA\Ship\ShipXpCalculator;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function DeepCopy\deep_copy;

class ShipController extends AbstractAdminController
{
    public function __construct(
        private readonly ShipDataRepository        $shipDataRepository,
        private readonly ShipRequirementRepository $shipRequirementRepository,
        private readonly RankingService            $rankingService,
        private readonly ShipQueueRepository       $shipQueueRepository,
        private readonly PlanetRepository          $planetRepository,
        private readonly ShipRepository            $shipRepository,
    )
    {
    }

    #[Route("/admin/ships/search", name: "admin.ships.search")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function search(Request $request): Response
    {
        $addItem = ShipListItem::empty();
        $addForm = $this->createForm(AddShipListType::class, $addItem);
        $addForm->handleRequest($request);
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $userId = $this->planetRepository->getPlanetUserId($addItem->entityId);
            $this->shipRepository->addShip($addItem->shipId, $addItem->count, $userId, $addItem->entityId);

            $this->addFlash('success', sprintf('%s Schiffe hinzugefügt', StringUtils::formatNumber($addItem->count)));
        }

        return $this->render('admin/ships/search.html.twig', [
            'addForm' => $addForm->createView(),
            'form' => $this->createForm(ShipSearchType::class, $request->query->all()),
            'total' => $this->shipRepository->count(),
        ]);
    }

    #[Route("/admin/ships/queue", name: "admin.ships.queue")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function queue(Request $request): Response
    {
        return $this->render('admin/ships/queue.html.twig', [
            'form' => $this->createForm(ShipSearchType::class, $request->query->all()),
            'total' => $this->shipQueueRepository->count(),
        ]);
    }

    #[Route("/admin/ships/xp-calculator", name: "admin.ships.xp-calculator")]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function xpCalculator(Request $request): Response
    {
        $shipSearch = ShipSearch::create()->special(true);
        $form = $this->createForm(ShipXpCalculatorType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && isset($form->getData()['ship'])) {
            $shipSearch->id($form->getData()['ship']);
        }

        $ship = $this->shipDataRepository->searchShip($shipSearch);
        $levels = [];
        for ($level = 1; $level <= 30; $level++) {
            $levels[$level] = ShipXpCalculator::xpByLevel($ship->specialNeedExp, $ship->specialExpFactor, $level);
        }

        return $this->render('admin/ships/xp-calculator.html.twig', [
            'levels' => $levels,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/admin/ships/points", name: "admin.ships.points")]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function points(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $num = $this->rankingService->calcShipPoints();
            $this->addFlash('success', sprintf("Die Punkte von %s Schiffen wurden aktualisiert!", $num));
        }

        $ships = $this->shipDataRepository->getAllShips(true);
        usort($ships, fn(Ship $a, Ship $b) => $b->points <=> $a->points);

        return $this->render('admin/ships/points.html.twig', [
            'ships' => $ships,
        ]);
    }

    #[Route('/admin/ships/requirements', name: 'admin.ships.requirements')]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function requirements(Request $request): Response
    {
        $collection = $this->shipRequirementRepository->getAll();
        $ships = $this->shipDataRepository->getAllShips();
        $requirements = [];
        $names = [];
        foreach ($ships as $ship) {
            $names[$ship->id] = $ship->name;
            $requirements[$ship->id] = $collection->getAll($ship->id);
        }

        $requirementsCopy = deep_copy($requirements);

        $form = $this->createForm(ObjectRequirementListType::class, $requirements, ['objectIds' => array_keys($ships), 'objectNames' => $names]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ObjectRequirement[][] $updatedRequirements */
            $updatedRequirements = $form->getData();
            (new RequirementsUpdater($this->shipRequirementRepository))->update($requirementsCopy, $updatedRequirements);

            $this->addFlash('success', 'Voraussetzungen aktualisiert');
        }

        return $this->render('admin/requirements/requirements.html.twig', [
            'objects' => $ships,
            'form' => $form->createView(),
            'name' => 'Schiffe',
        ]);
    }
}
