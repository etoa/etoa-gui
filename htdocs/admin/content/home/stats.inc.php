<?PHP

use EtoA\Alliance\AllianceStatsRepository;
use EtoA\Alliance\AllianceStatsSort;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Ranking\UserTitlesService;
use EtoA\Support\RuntimeDataStore;
use EtoA\Support\StringUtils;
use EtoA\User\UserRatingRepository;
use EtoA\User\UserStatRepository;
use EtoA\User\UserStatSearch;

/** @var RuntimeDataStore $runtimeDataStore */
$runtimeDataStore = $app[RuntimeDataStore::class];

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];
/** @var AllianceStatsRepository $allianceStatsRepository */
$allianceStatsRepository = $app[AllianceStatsRepository::class];
/** @var UserRatingRepository $userRatingRepository */
$userRatingRepository = $app[UserRatingRepository::class];
/** @var UserStatRepository $userStatsRepository */
$userStatsRepository = $app[UserStatRepository::class];

echo "<h1>Rangliste</h1>";

$mode = isset($_GET['mode']) && $_GET['mode'] != "" ? $_GET['mode'] : "user";

// Menü
echo "<br/><table class=\"tbl\">";

if ($mode == "battle") {
    echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=battle\" class=\"tabEnabled\">Kampf</a></td>";
} else {
    echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=battle\" class=\"tabDefault\">Kampf</a></td>";
}

if ($mode == "trade") {
    echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=trade\" class=\"tabEnabled\">Handel</a></td>";
} else {
    echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=trade\" class=\"tabDefault\">Handel</a></td>";
}

if ($mode == "diplomacy") {
    echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=diplomacy\" class=\"tabEnabled\">Diplomatie</a></td>";
} else {
    echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=diplomacy\" class=\"tabDefault\">Diplomatie</a></td>";
}

if ($mode == "alliances") {
    echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=alliances\" class=\"tabEnabled\">Allianzen</a></td>";
} else {
    echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=alliances\" class=\"tabDefault\">Allianzen</a></td>";
}

if ($mode == "base") {
    echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=base\" class=\"tabEnabled\">Allianzbasis</a></td>";
} else {
    echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=base\" class=\"tabDefault\">Allianzbasis</a></td>";
}

if ($mode == "titles") {
    echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=titles\" class=\"tabEnabled\">Titel</a></td></tr>";
} else {
    echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=titles\" class=\"tabDefault\">Titel</a></td></tr>";
}

echo "</table><br/>";

if ($mode == "titles") {
    /** @var UserTitlesService $userTitlesService */
    $userTitlesService = $app[UserTitlesService::class];
    if (!file_exists($userTitlesService->getUserTitlesAdminCacheFilePath())) {
        echo "<b>Fehler! Die Liste wurde noch nicht erstellt! Bitte das nächste Statistikupdate abwarten.<br/><br/>";
    } else {
       include($userTitlesService->getUserTitlesAdminCacheFilePath());
    }
}

//
// Allianzen
//

elseif ($mode == "alliances") {
    echo "<table class=\"tb\">";
    echo "<tr><th colspan=\"7\" style=\"text-align:center\">Allianzrangliste</th></tr>";
    echo "<tr>";
    echo "<th>#</th>";
    echo "<th>Tag</th>";
    echo "<th>Name</th>";
    if (isset($_GET['order_field']) && $_GET['order_field'] == "upoints") {
        echo "<th><i>Punkte</i> ";
    } else {
        echo "<th>Punkte ";
    }
    echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=" . $mode . "&amp;order_field=points&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
    echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=" . $mode . "&amp;order_field=points&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
    if (isset($_GET['order_field']) && $_GET['order_field'] == "uavg") {
        echo "<th><i>User-Schnitt</i> ";
    } else {
        echo "<th>User-Schnitt ";
    }
    echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=" . $mode . "&amp;order_field=uavg&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
    echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=" . $mode . "&amp;order_field=uavg&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
    echo "</th>";
    if (isset($_GET['order_field']) && $_GET['order_field'] == "cnt") {
        echo "<th><i>User</i> ";
    } else {
        echo "<th>User ";
    }
    echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=" . $mode . "&amp;order_field=cnt&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
    echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=" . $mode . "&amp;order_field=cnt&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";

    echo "<th style=\"width:60px;\">Details</th>";

    echo "</tr>";

    if (isset($_GET['order_field']) && $_GET['order_field'] == "uavg") {
        $order = "uavg";
    } elseif (isset($_GET['order_field']) && $_GET['order_field'] == "cnt") {
        $order = "cnt";
    } else {
        $order = "points";
    }

    if (isset($_GET['order']) && $_GET['order'] == "ASC") {
        $sort = "ASC";
    } else {
        $sort = "DESC";
    }

    $allianceStats = $allianceStatsRepository->getStats(AllianceStatsSort::create()->withSort($order, $sort));
    if (count($allianceStats) > 0) {
        $count = 1;
        foreach ($allianceStats as $stats) {
            echo "<tr>";
            echo "<td align=\"right\">" . StringUtils::formatNumber($count) . "</td>";
            echo "<td>" . $stats->allianceTag . "</td>";
            echo "<td>" . $stats->allianceName . "</td>";
            echo "<td>" . StringUtils::formatNumber($stats->points) . "</td>";
            echo "<td>" . StringUtils::formatNumber($stats->userAverage) . "</td>";
            echo "<td>" . StringUtils::formatNumber($stats->count) . "</td>";
            echo "<td>" . edit_button("?page=alliances&amp;sub=edit&amp;alliance_id=" . $stats->allianceId . "") . "</td>";
            echo "</tr>";
            $count++;
        }
    } else {
        echo "<tr><td colspan=\"7\" align=\"center\"><i>Keine Allianzen in der Statistik</i></tr>";
    }
    echo "</table>";
}

//
// Allianzbasis
//
elseif ($mode == "base") {
    echo "<table class=\"tb\">";
    echo "<tr><th colspan=\"7\" style=\"text-align:center\">Allianzbasis</th></tr>";
    echo "<tr>";
    echo "<th style=\"width:50px;\">Rang</th>";
    echo "<th>Tag</th>";
    if (isset($sort) && $sort == "bpoints")
        echo "<th><i>Gebäude</i> ";
    else
        echo "<th>Gebäude ";
    echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','bpoints','DESC')\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
    echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','bpoints','ASC')\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a></th>";
    if (isset($sort) && $sort == "tpoints")
        echo "<th><i>Forschung</i> ";
    else
        echo "<th>Forschung ";
    echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','tpoints','DESC')\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
    echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','tpoints','ASC')\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a></th>";
    if (isset($sort) && $sort == "spoints")
        echo "<th><i>Schiffe</i> ";
    else
        echo "<th>Schiffe ";
    echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','spoints','DESC')\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
    echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','spoints','ASC')\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a></th>";
    if (isset($sort) && $sort == "epoints")
        echo "<th><i>Erfahrung</i> ";
    else
        echo "<th>Allianzbasis ";
    echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','apoints','DESC')\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
    echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','apoints','ASC')\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a></th>";
    echo "<th style=\"width:60px;\">Details</th>";
    echo "</tr>";
    $search = AllianceStatsSort::createAllianceBase();
    if (isset($sort) && $sort != "") {
        $search = $search->withSort($sort, 'DESC');
    }

    $allianceStats = $allianceStatsRepository->getStats($search);
    if (count($allianceStats) > 0) {
        $cnt = 1;
        foreach ($allianceStats as $stats) {
            echo "<tr>
                        <td>
                            " . StringUtils::formatNumber($cnt, true) . "
                        </td>";
            echo "<td >
                <div id=\"ttuser" . $stats->allianceId . "\" style=\"display:none;\">
                    <a href=\"?page=alliances&amp;sub=edit&amp;id=" . $stats->allianceId . "\">Allianzseite</a><br/>";
            echo "</div><a href=\"#\" " . cTT($stats->allianceName, "ttuser" . $stats->allianceId) . ">
                " . $stats->allianceTag . "</td>";
            echo "<td >" . StringUtils::formatNumber($stats->buildingPoints) . "</td>";
            echo "<td >" . StringUtils::formatNumber($stats->technologyPoints) . "</td>";
            echo "<td >" . StringUtils::formatNumber($stats->shipPoints) . "</td>";
            echo "<td >" . StringUtils::formatNumber($stats->alliancePoints) . "</td>";
            echo "<td>" . edit_button("?page=alliances&amp;sub=edit&amp;alliance_id=" . $stats->allianceId . "") . "</td>";
            echo "</tr>";
            $cnt++;
        }
    } else {
        echo "<tr><td colspan=\"8\" align=\"center\"><i>Keine Allianzen in der Statistik</i></tr>";
    }
    echo "</table>";
}


//
// Special Points
//
elseif ($mode == "diplomacy" || $mode == "battle" || $mode == "trade") {
    // Punktetabelle
    if ($mode == "diplomacy") {
        $ratings = $userRatingRepository->getDiplomacyRating();
        echo "<table class=\"tb\">";
        echo "<tr><th colspan=\"9\" style=\"text-align:center\">Diplomatiewertung</th></tr>";
        $cnt = 1;
        if (count($ratings) > 0) {
            echo "<tr>
                        <th style=\"width:50px;\">#</th>
                        <th style=\"\">Nick</th>
                        <th style=\"\">Rasse</th>
                        <th style=\"\">Allianz</th>
                        <th style=\"\">Bewertung</th>
                        <th style=\"width:60px;\">Details</th>
                    </tr>";
            foreach ($ratings as $rating) {
                if ($rating->rating > 0) {
                    echo "<tr>";
                    echo "<td>" . $cnt . "</td>";
                    echo "<td>" . $rating->userNick . "</td>";
                    echo "<td>" . $rating->raceName . "</td>";
                    echo "<td >" . $rating->allianceTag . "</td>";
                    echo "<td>" . StringUtils::formatNumber($rating->rating) . "</td>";
                    echo "<td>
                            " . edit_button("?page=user&amp;sub=edit&amp;id=" . $rating->userId . "") . "
                            </td>";
                    echo "</tr>";
                    $cnt++;
                }
            }
        }
        if ($cnt == 1) {
            echo "<tr><td align=\"center\" colspan=\"8\"><i>Es wurde keine User gefunden!</i></tr>";
        }
        echo "</table><br/>";
    } elseif ($mode == "battle") {
        $ratings = $userRatingRepository->getBattleRating();
        echo "<table class=\"tb\">";
        echo "<tr><th colspan=\"9\" style=\"text-align:center\">Kampfwertung</th></tr>";
        $cnt = 1;
        if (count($ratings) > 0) {
            echo "<tr>
                        <th style=\"width:50px;\">#</th>
                        <th style=\"\">Nick</th>
                        <th style=\"\">Rasse</th>
                        <th style=\"\">Allianz</th>
                        <th style=\"\">Kämpfe Gewonnen</th>
                        <th style=\"\">Kämpfe Verloren</th>
                        <th style=\"\">Kämpfe Total</th>
                        <th style=\"\">Bewertung</th>
                        <th style=\"width:60px;\">Details</th>
                    </tr>";
            foreach ($ratings as $rating) {
                if ($rating->rating > 0) {
                    echo "<tr>";
                    echo "<td>" . $cnt . "</td>";
                    echo "<td>" . $rating->userNick . "</td>";
                    echo "<td>" . $rating->raceName . "</td>";
                    echo "<td >" . $rating->allianceTag . "</td>";
                    echo "<td>" . StringUtils::formatNumber($rating->battlesWon) . "</td>";
                    echo "<td>" . StringUtils::formatNumber($rating->battlesLost) . "</td>";
                    echo "<td>" . StringUtils::formatNumber($rating->battlesFought) . "</td>";
                    echo "<td>" . StringUtils::formatNumber($rating->rating) . "</td>";
                    echo "<td>
                            " . edit_button("?page=user&amp;sub=edit&amp;id=" . $rating->userId . "") . "
                            </td>";
                    echo "</tr>";
                    $cnt++;
                }
            }
        }
        if ($cnt == 1) {
            echo "<tr><td align=\"center\"  colspan=\"9\"><i>Es wurde keine User gefunden!</i></tr>";
        }
        echo "</table><br/>";
    } elseif ($mode == "trade") {
        $ratings = $userRatingRepository->getTradeRating();
        echo "<table class=\"tb\">";
        echo "<tr><th colspan=\"9\" style=\"text-align:center\">Handelswertung</th></tr>";
        $cnt = 1;
        if (count($ratings) > 0) {
            echo "<tr>
                        <th style=\"width:50px;\">#</th>
                        <th style=\"\">Nick</th>
                        <th style=\"\">Rasse</th>
                        <th style=\"\">Allianz</th>
                        <th style=\"\">Einkäufe</th>
                        <th style=\"\">Verkäufe</th>
                        <th style=\"\">Bewertung</th>
                        <th style=\"width:60px;\">Details</th>
                    </tr>";
            foreach ($ratings as $rating) {
                if ($rating->rating > 0) {
                    echo "<tr>";
                    echo "<td>" . $cnt . "</td>";
                    echo "<td>" . $rating->userNick . "</td>";
                    echo "<td>" . $rating->raceName . "</td>";
                    echo "<td >" . $rating->allianceTag . "</td>";
                    echo "<td>" . StringUtils::formatNumber($rating->tradesBuy) . "</td>";
                    echo "<td>" . StringUtils::formatNumber($rating->tradesSell) . "</td>";
                    echo "<td>" . StringUtils::formatNumber($rating->rating) . "</td>";
                    echo "<td>
                            " . edit_button("?page=user&amp;sub=edit&amp;id=" . $rating->userId . "") . "
                            </td>";
                    echo "</tr>";
                    $cnt++;
                }
            }
        }
        if ($cnt == 1) {
            echo "<tr><td align=\"center\" colspan=\"8\"><i>Es wurde keine User gefunden!</i></tr>";
        }
        echo "</table><br/>";
    }
}

//
// Legende
//
echo "<div style=\"text-align:center;padding:10px;\">Die Aktualisierung der Punkte erfolgt ";
$h = $config->getInt('points_update') / 3600;
if ($h > 1)
    echo "alle $h Stunden!<br/>";
elseif ($h == 1)
    echo " jede Stunde!<br/>";
else {
    $m = $config->getInt('points_update') / 60;
    echo "alle $m Minuten!<br/>";
}
$statsUpdate = $runtimeDataStore->get('statsupdate');
if ($statsUpdate !== null) {
    echo "Letzte Aktualisierung: <b>" . StringUtils::formatDate((int) $statsUpdate) . " Uhr</b><br/>";
}
echo "<b>Legende:</b>
    <span class=\"userLockedColor\">Gesperrt</span>,
    <span class=\"userHolidayColor\">Urlaubsmodus</span>,
    <span class=\"userInactiveColor\">Inaktiv (" . $config->getInt('user_inactive_days') . " Tage)</span>,
    </div>";
