<?php

declare(strict_types=1);

namespace EtoA\Ranking;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Race\RaceDataRepository;
use EtoA\Support\StringUtils;
use EtoA\User\UserRatingRepository;
use EtoA\User\UserRatingSearch;
use EtoA\User\UserRatingSort;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use EtoA\User\UserSort;
use EtoA\User\UserStatRepository;
use EtoA\User\UserStatSearch;

class UserTitlesService
{
    private ConfigurationService $config;
    private RaceDataRepository $raceRepository;
    private UserStatRepository $userStatRepository;
    private UserRepository $userRepository;
    private UserRatingRepository $userRatingRepository;
    private string $cacheDir;

    public function __construct(
        ConfigurationService $config,
        RaceDataRepository $raceRepository,
        UserStatRepository $userStatRepository,
        UserRepository $userRepository,
        UserRatingRepository $userRatingRepository,
        string $cacheDir
    ) {
        $this->config = $config;
        $this->raceRepository = $raceRepository;
        $this->userStatRepository = $userStatRepository;
        $this->userRepository = $userRepository;
        $this->userRatingRepository = $userRatingRepository;
        $this->cacheDir = $cacheDir;
    }

    public function getTitles(bool $admin = false): array
    {
        $img_dir = ($admin == 1)
            ? "../images"
            : "/build/images";

        $titles = [
            [
                'result' => $this->userStatRepository->searchStats(UserStatSearch::points(),null,1),
                'medal_image' => $img_dir . '/medals/medal_total.png',
                'rank_title' => $this->config->get('userrank_total'),
            ],
            [
                'result' => $this->userStatRepository->searchStats(UserStatSearch::ships(),null,1),
                'medal_image' => $img_dir . '/medals/medal_fleet.png',
                'rank_title' => $this->config->get('userrank_fleet'),
            ],
            [
                'result' => $this->userStatRepository->searchStats(UserStatSearch::technologies(),null,1),
                'medal_image' => $img_dir . '/medals/medal_tech.png',
                'rank_title' => $this->config->get('userrank_tech'),
            ],
            [
                'result' => $this->userStatRepository->searchStats(UserStatSearch::buildings(),null,1),
                'medal_image' => $img_dir . '/medals/medal_buildings.png',
                'rank_title' => $this->config->get('userrank_buildings'),
            ],
            [
                'result' => $this->userStatRepository->searchStats(UserStatSearch::exp(),null,1),
                'medal_image' => $img_dir . '/medals/medal_exp.png',
                'rank_title' => $this->config->get('userrank_exp'),
            ],
        ];

        $titles2 = [
            [
                'result' => $this->userRatingRepository->getBattleRating(
                    UserRatingSearch::create()->ghost(false),
                    UserRatingSort::rank('DESC'),
                    1
                ),
                'medal_image' => $img_dir . '/medals/medal_battle.png',
                'rank_title' => $this->config->get('userrank_battle'),
            ],
            [
                'result' => $this->userRatingRepository->getTradeRating(
                    UserRatingSearch::create()->ghost(false),
                    UserRatingSort::rank('DESC'),
                    1
                ),
                'medal_image' => $img_dir . '/medals/medal_trade.png',
                'rank_title' => $this->config->get('userrank_trade'),
            ],
            [
                'result' => $this->userRatingRepository->getDiplomacyRating(
                    UserRatingSearch::create()->ghost(false),
                    UserRatingSort::rank('DESC'),
                    1
                ),
                'medal_image' => $img_dir . '/medals/medal_diplomacy.png',
                'rank_title' => $this->config->get('userrank_diplomacy'),
            ],
        ];

        $races = $this->raceRepository->getActiveRaces();

        $titles3 = [];

        foreach ($races as $race) {
            $users = $this->userRepository->searchUsers(
                UserSearch::create()
                    ->raceId($race->getId())
                    ->notGhost()
                    ->hasPoints(),
                UserSort::points('desc'),
                1
            );

            $titles3[] = $users;
        }

        return ['titles'=>$titles,'titles2'=>$titles2,'titles3'=>$titles3];
    }

    /**
     * Writes generated titles to cache files
     */
    public function calcTitles(): void
    {
        $dir = $this->cacheDir . "/out";
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        file_put_contents($this->getUserTitlesCacheFilePath(), $this->getTitles());
        file_put_contents($this->getUserTitlesAdminCacheFilePath(), $this->getTitles(true));
    }

    public function getUserTitlesCacheFilePath(): string
    {
        return $this->cacheDir . "/out/usertitles.html";
    }

    public function getUserTitlesAdminCacheFilePath(): string
    {
        return $this->cacheDir . "/out/usertitles_a.html";
    }
}
