<?php

declare(strict_types=1);

namespace EtoA\Ranking;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Race\RaceDataRepository;
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

    public function __construct(
        ConfigurationService $config,
        RaceDataRepository $raceRepository,
        UserStatRepository $userStatRepository,
        UserRepository $userRepository,
        UserRatingRepository $userRatingRepository
    ) {
        $this->config = $config;
        $this->raceRepository = $raceRepository;
        $this->userStatRepository = $userStatRepository;
        $this->userRepository = $userRepository;
        $this->userRatingRepository = $userRatingRepository;
    }

    public function getTitles(bool $admin = false): string
    {
        ob_start();

        $img_dir = ($admin == 1)
            ? "../images"
            : "images";

        tableStart("Allgemeine Titel");
        $cnt = 0;

        $titles = [
            [
                'search' => UserStatSearch::points(),
                'medal_image' => $img_dir . '/medals/medal_total.png',
                'rank_title' => $this->config->get('userrank_total'),
            ],
            [
                'search' => UserStatSearch::ships(),
                'medal_image' => $img_dir . '/medals/medal_fleet.png',
                'rank_title' => $this->config->get('userrank_fleet'),
            ],
            [
                'search' => UserStatSearch::technologies(),
                'medal_image' => $img_dir . '/medals/medal_tech.png',
                'rank_title' => $this->config->get('userrank_tech'),
            ],
            [
                'search' => UserStatSearch::buildings(),
                'medal_image' => $img_dir . '/medals/medal_buildings.png',
                'rank_title' => $this->config->get('userrank_buildings'),
            ],
            [
                'search' => UserStatSearch::exp(),
                'medal_image' => $img_dir . '/medals/medal_exp.png',
                'rank_title' => $this->config->get('userrank_exp'),
            ],
        ];
        foreach ($titles as $title) {
            $stats = $this->userStatRepository->searchStats($title['search'], null, 1);
            if (count($stats) > 0) {
                $stat = $stats[0];
                if ($stat->points > 0) {
                    $profile = ($admin == 1)
                        ? "?page=user&amp;sub=edit&amp;user_id=" . $stat->id . ""
                        : "?page=userinfo&amp;id=" . $stat->id;
                    echo "<tr>
                        <th class=\"tbltitle\" style=\"width:100px;height:100px;\">
                            <img src='" . $title['medal_image'] . "' alt=\"medal\" style=\"height:100px;\" />
                        </th>
                        <td class=\"tbldata\" style=\"font-size:16pt;vertical-align:middle;padding:2px 10px 2px 10px;width:400px;\">
                            " . $title['rank_title'] . "
                        </td>
                        <td class=\"tbldata\" style=\"vertical-align:middle;padding-top:0px;padding-left:15px;\">
                            <span style=\"font-size:13pt;color:#ff0;\">" . $stat->nick . "</span><br/><br/>
                            " . nf($stat->points) . " Punkte<br/><br/>";
                    echo "[<a href=\"" . $profile . "\">Profil</a>]";
                    echo "</td>
                    </tr>";
                    $cnt++;
                }
            }
        }

        $titles2 = [
            [
                'results' => $this->userRatingRepository->getBattleRating(
                    UserRatingSearch::create()->ghost(false),
                    UserRatingSort::rank('DESC'),
                    1
                ),
                'medal_image' => $img_dir . '/medals/medal_battle.png',
                'rank_title' => $this->config->get('userrank_battle'),
            ],
            [
                'results' => $this->userRatingRepository->getTradeRating(
                    UserRatingSearch::create()->ghost(false),
                    UserRatingSort::rank('DESC'),
                    1
                ),
                'medal_image' => $img_dir . '/medals/medal_trade.png',
                'rank_title' => $this->config->get('userrank_trade'),
            ],
            [
                'results' => $this->userRatingRepository->getDiplomacyRating(
                    UserRatingSearch::create()->ghost(false),
                    UserRatingSort::rank('DESC'),
                    1
                ),
                'medal_image' => $img_dir . '/medals/medal_diplomacy.png',
                'rank_title' => $this->config->get('userrank_diplomacy'),
            ],
        ];
        foreach ($titles2 as $title) {
            if (count($title['results']) > 0) {
                $rating = $title['results'][0];
                if ($rating->rating > 0) {
                    $profile = ($admin == 1)
                        ? "?page=user&amp;sub=edit&amp;user_id=" . $rating->userId . ""
                        : "?page=userinfo&amp;id=" . $rating->userId;
                    echo "<tr>
                        <th class=\"tbltitle\" style=\"width:100px;height:100px;\">
                            <img src='" . $title['medal_image'] . "' style=\"height:100px;\" />
                        </th>
                        <td class=\"tbldata\" style=\"font-size:16pt;vertical-align:middle;padding:2px 10px 2px 10px;width:400px;\">
                            " . $title['rank_title'] . "
                        </td>
                        <td class=\"tbldata\" style=\"vertical-align:middle;padding-top:0px;padding-left:15px;\">
                            <span style=\"font-size:13pt;color:#ff0;\">" . $rating->userNick . "</span><br/><br/>
                            " . nf($rating->rating) . " Punkte<br/><br/>";
                    echo "[<a href=\"" . $profile . "\">Profil</a>]";
                    echo "</td>
                    </tr>";
                    $cnt++;
                }
            }
        }

        if ($cnt == 0) {
            echo "<tr><td class=\"tbldata\">Keine Titel vorhanden (kein Spieler hat die minimale Punktzahl zum Erwerb eines Titels erreicht)!</td></tr>";
        }

        tableEnd();
        tableStart("Rassenleader");
        $races = $this->raceRepository->getActiveRaces();
        foreach ($races as $race) {
            $users = $this->userRepository->searchUsers(
                UserSearch::create()
                    ->raceId($race->id)
                    ->notGhost()
                    ->hasPoints(),
                UserSort::points('desc'),
                1
            );
            if (count($users) > 0) {
                $user = array_values($users)[0];
                $profile = ($admin == 1)
                    ? "?page=user&amp;sub=edit&amp;user_id=" . $user->id . ""
                    : "?page=userinfo&amp;id=" . $user->id;
                echo "<tr>
                        <th class=\"tbltitle\" style=\"width:70px;height:70px;\">
                            <img src='" . $img_dir . "/medals/medal_race.png' style=\"height:70px;\" />
                        </th>
                        <td class=\"tbldata\" style=\"vertical-align:middle;padding:2px 10px 2px 10px;width:360px;\">
                            <div style=\"font-size:16pt;\">" . $race->leaderTitle . "</div>
                            " . $this->raceRepository->getNumberOfUsersWithRace($race->id) . " V&ouml;lker
                        </td>
                        <td class=\"tbldata\" style=\"vertical-align:middle;padding-top:0px;padding-left:15px;\">
                            <span style=\"font-size:13pt;color:#ff0;\">" . $user->nick . "</span><br/><br/>
                            " . nf($user->points) . " Punkte &nbsp;&nbsp;&nbsp;";
                echo "[<a href=\"" . $profile . "\">Profil</a>]";
                echo "</td>
                    </tr>";
            }
        }
        tableEnd();

        $rtn = ob_get_contents();
        ob_end_clean();

        return $rtn;
    }

    /**
     * Writes generated titles to cache files
     */
    public function calcTitles(): void
    {
        $dir = CACHE_ROOT . "/out";
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        file_put_contents($this->getUserTitlesCacheFilePath(), $this->getTitles());
        file_put_contents($this->getUserTitlesAdminCacheFilePath(), $this->getTitles(true));
    }

    public function getUserTitlesCacheFilePath(): string
    {
        return CACHE_ROOT . "/out/usertitles.html";
    }

    public function getUserTitlesAdminCacheFilePath(): string
    {
        return CACHE_ROOT . "/out/usertitles_a.html";
    }
}
