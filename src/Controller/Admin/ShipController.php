<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Entity\ShipListItem;
use EtoA\Entity\ShipRequirements;
use EtoA\Form\Type\Admin\AddShipListType;
use EtoA\Form\Type\Admin\ObjectRequirementListType;
use EtoA\Form\Type\Admin\ShipSearchType;
use EtoA\Form\Type\Admin\ShipXpCalculatorType;
use EtoA\Ranking\RankingService;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipSearch;
use EtoA\Ship\ShipXpCalculator;
use EtoA\Support\StringUtils;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ShipController extends AbstractAdminController
{
    public function __construct(
        private readonly ShipDataRepository        $shipDataRepository,
        private readonly RankingService            $rankingService,
        private readonly ShipQueueRepository       $shipQueueRepository,
        private readonly ShipListRepository        $shipListRepository,
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
            $this->shipListRepository->addShip($addItem->getShip(), $addItem->getCount(), $addForm->get('entity')->getData()->getUser(), $addItem->getEntity());

            $this->addFlash('success', sprintf('%s Schiffe hinzugefügt', StringUtils::formatNumber($addItem->getCount())));
        }

        return $this->render('admin/ships/search.html.twig', [
            'addForm' => $addForm->createView(),
            'form' => $this->createForm(ShipSearchType::class, $request->query->all()),
            'total' => $this->shipListRepository->count([]),
        ]);
    }

    #[Route("/admin/ships/queue", name: "admin.ships.queue")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function queue(Request $request): Response
    {
        return $this->render('admin/ships/queue.html.twig', [
            'form' => $this->createForm(ShipSearchType::class, $request->query->all()),
            'total' => $this->shipQueueRepository->count([]),
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
            $levels[$level] = ShipXpCalculator::xpByLevel($ship->getSpecialNeedExp(), $ship->getSpecialExpFactor(), $level);
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
        $form = $this->createFormBuilder()
            ->add('calc', SubmitType::class, ['label' => 'Neu berechnen'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $num = $this->rankingService->calcShipPoints();
            $this->addFlash('success', sprintf("Die Punkte von %s Schiffen wurden aktualisiert!", $num));
        }

        return $this->render('admin/ships/points.html.twig', [
            'ships' => $this->shipDataRepository->getAllShips(true),
            'form' => $form
        ]);
    }

    #[Route('/admin/ships/requirements', name: 'admin.ships.requirements')]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function requirements(Request $request): Response
    {
        $ships = $this->shipDataRepository->getAllShips();
        $form = $this->createForm(ObjectRequirementListType::class, $ships, ['type'=>ShipRequirements::class]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->shipDataRepository->save();

            $this->addFlash('success', 'Voraussetzungen aktualisiert');
        }

        return $this->render('admin/requirements/requirements.html.twig', [
            'objects' => $ships,
            'form' => $form->createView(),
            'name' => 'Schiffe',
        ]);
    }
}
