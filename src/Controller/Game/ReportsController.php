<?php

namespace EtoA\Controller\Game;

use EtoA\Message\ReportRepository;
use EtoA\Pagination\ArrayPaginator;
use EtoA\Pagination\SimplePagination;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportsController extends AbstractGameController
{
    public function __construct(
        private readonly ReportRepository $reportRepository
    )
    {
    }

    #[Route('/game/reports/all', name: 'game.reports.all')]
    public function all(Request $request): Response {
        $reports = $this->reportRepository->findBy(['deleted'=>false,'archived'=>false, 'user'=>$this->getUser()->getData()]);

        $currentPage = $request->query->getInt('page', 1);

        $paginator = new ArrayPaginator($reports, $currentPage, 20);
        $pagination = new SimplePagination($paginator);

        $form = $this->createFormBuilder()
            ->getForm()
            ->handleRequest($request);

        return $this->render('game/reports/reports_overview.html.twig', [
            'paginator' => $paginator,
            'pagination' => $pagination,
            'title' => 'Neueste Berichte',
            'form' => $form
        ]);
    }

    #[Route('/game/reports/battle', name: 'game.reports.battle')]
    public function battle(): Response {

    }

    #[Route('/game/reports/spy', name: 'game.reports.spy')]
    public function spy(): Response {

    }

    #[Route('/game/reports/explore', name: 'game.reports.explore')]
    public function explore(): Response {

    }

    #[Route('/game/reports/market', name: 'game.reports.market')]
    public function market(): Response {

    }

    #[Route('/game/reports/crypto', name: 'game.reports.crypto')]
    public function crypto(): Response {

    }

    #[Route('/game/reports/other', name: 'game.reports.other')]
    public function other(): Response {

    }

    #[Route('/game/reports/archive', name: 'game.reports.archive')]
    public function archive(): Response {

    }
}