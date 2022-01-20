<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\User\UserStat;
use EtoA\User\UserStatRepository;
use EtoA\User\UserStatSearch;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractAdminController
{
    public function __construct(
        private UserStatRepository $userStatsRepository,
    ) {
    }

    #[Route("/admin/stats/users", name: 'admin.stats.users')]
    public function users(Request $request): Response
    {
        $shipsStats = [];
        foreach ($this->userStatsRepository->searchStats(UserStatSearch::ships()) as $stat) {
            $shipsStats[$stat->id] = $stat;
        }
        $technologyStats = [];
        foreach ($this->userStatsRepository->searchStats(UserStatSearch::technologies()) as $stat) {
            $technologyStats[$stat->id] = $stat;
        }
        $buildingStats = [];
        foreach ($this->userStatsRepository->searchStats(UserStatSearch::buildings()) as $stat) {
            $buildingStats[$stat->id] = $stat;
        }
        $expStats = [];
        foreach ($this->userStatsRepository->searchStats(UserStatSearch::exp()) as $stat) {
            $expStats[$stat->id] = $stat;
        }
        $pointsStats = [];
        foreach ($this->userStatsRepository->searchStats(UserStatSearch::points()) as $stat) {
            $pointsStats[$stat->id] = $stat;
        }

        /** @var UserStat[][] $sorts */
        $sorts = [
            'ships' => $shipsStats,
            'tech' => $technologyStats,
            'buildings' => $buildingStats,
            'exp' => $expStats,
            'points' => $pointsStats,
        ];

        $sort = isset($sorts[$request->query->getAlnum('sort')]) ? $request->query->getAlnum('sort') : 'points';
        $order = $request->query->get('order') === 'ASC' ? 'ASC' : 'DESC';

        $userOrder = [];
        foreach ($sorts[$sort] as $entry) {
            $userOrder[$entry->id] = $entry->shift;
        }

        if ($order === 'ASC') {
            $userOrder = array_reverse($userOrder, true);
        }

        return $this->render('admin/stats/users.html.twig', [
            'userOrder' => $userOrder,
            'pointsStats' => $pointsStats,
            'shipsStats' => $shipsStats,
            'technologyStats' => $technologyStats,
            'buildingStats' => $buildingStats,
            'expStats' => $expStats,
        ]);
    }
}
