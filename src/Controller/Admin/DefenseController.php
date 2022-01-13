<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Defense\Defense;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Defense\DefenseListItem;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Form\Type\Admin\AddDefenseListType;
use EtoA\Form\Type\Admin\DefenseSearchType;
use EtoA\Ranking\RankingService;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefenseController extends AbstractAdminController
{
    public function __construct(
        private RankingService $rankingService,
        private DefenseDataRepository $defenseDataRepository,
        private DefenseQueueRepository $defenseQueueRepository,
        private DefenseRepository $defenseRepository,
        private PlanetRepository $planetRepository,
    ) {
    }

    #[Route("/admin/defense/search", name: "admin.defense.search")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function search(Request $request): Response
    {
        $addItem = DefenseListItem::empty();
        $addForm = $this->createForm(AddDefenseListType::class, $addItem);
        $addForm->handleRequest($request);
        if ($addForm->isSubmitted() && $addForm->isValid()) {
            $userId = $this->planetRepository->getPlanetUserId($addItem->entityId);
            $this->defenseRepository->addDefense($addItem->defenseId, $addItem->count, $userId, $addItem->entityId);

            $this->addFlash('success', sprintf('%s Verteidigungsanlagen hinzugefügt', StringUtils::formatNumber($addItem->count)));
        }

        return $this->render('admin/defense/search.html.twig', [
            'addForm' => $addForm->createView(),
            'form' => $this->createForm(DefenseSearchType::class, $request->query->all())->createView(),
            'total' => $this->defenseRepository->count(),
        ]);
    }

    #[Route("/admin/defense/queue", name: "admin.defense.queue")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function queue(Request $request): Response
    {
        return $this->render('admin/defense/queue.html.twig', [
            'form' => $this->createForm(DefenseSearchType::class, $request->query->all())->createView(),
            'total' => $this->defenseQueueRepository->count(),
        ]);
    }

    #[Route("/admin/defense/points", name: "admin.defense.points")]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function points(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $num = $this->rankingService->calcDefensePoints();
            $this->addFlash('success', sprintf("Die Punkte von %s Verteidigungsanlagen wurden aktualisiert!", $num));
        }

        $defenses = $this->defenseDataRepository->getAllDefenses();
        usort($defenses, fn (Defense $a, Defense $b) => $b->points <=> $a->points);

        return $this->render('admin/defense/points.html.twig', [
            'defenses' => $defenses,
        ]);
    }
}
