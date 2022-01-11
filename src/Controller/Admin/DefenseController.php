<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Defense\Defense;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Ranking\RankingService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefenseController extends AbstractAdminController
{
    public function __construct(
        private RankingService $rankingService,
        private DefenseDataRepository $defenseDataRepository
    ) {
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
