<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Ranking\UserTitlesService;
use EtoA\User\UserRating;
use EtoA\User\UserRatingRepository;
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
        private UserTitlesService $userTitlesService,
        private UserRatingRepository $userRatingRepository,
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

    #[Route("/admin/stats/battles", name: 'admin.stats.battles')]
    public function battles(): Response
    {
        $ratings = $this->userRatingRepository->getBattleRating();

        return $this->render('admin/stats/battles.html.twig', [
            'ratings' => array_filter($ratings, fn (UserRating $rating) => $rating->rating > 0),
        ]);
    }

    #[Route("/admin/stats/trade", name: 'admin.stats.trade')]
    public function trade(): Response
    {
        $ratings = $this->userRatingRepository->getTradeRating();

        return $this->render('admin/stats/trade.html.twig', [
            'ratings' => array_filter($ratings, fn (UserRating $rating) => $rating->rating > 0),
        ]);
    }

    #[Route("/admin/stats/diplomacy", name: 'admin.stats.diplomacy')]
    public function diplomacy(): Response
    {
        $ratings = $this->userRatingRepository->getDiplomacyRating();

        return $this->render('admin/stats/diplomacy.html.twig', [
            'ratings' => array_filter($ratings, fn (UserRating $rating) => $rating->rating > 0),
        ]);
    }

    #[Route("/admin/stats/titles", name: 'admin.stats.titles')]
    public function titles(): Response
    {
        $stats = null;
        if (file_exists($this->userTitlesService->getUserTitlesAdminCacheFilePath())) {
            $stats = file_get_contents($this->userTitlesService->getUserTitlesAdminCacheFilePath());
        }

        return $this->render('admin/stats/titles.html.twig', [
            'stats' => $stats,
        ]);
    }
}
