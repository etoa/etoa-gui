<?PHP

use EtoA\Alliance\AllianceStatsRepository;
use EtoA\Alliance\AllianceStatsSort;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Ranking\GameStatsGenerator;
use EtoA\Ranking\RankingService;
use EtoA\User\UserRatingRepository;
use EtoA\User\UserRatingSearch;
use EtoA\User\UserRatingSort;
use EtoA\User\UserRepository;
use EtoA\User\UserStatRepository;
use EtoA\User\UserStatSearch;

$xajax->register(XAJAX_FUNCTION, 'statsShowBox');
$xajax->register(XAJAX_FUNCTION, 'statsShowTable');


function statsShowBox($mode, $sort = "", $sortOrder = "")
{
    global $page;
    global $app;

    /** @var AllianceStatsRepository $allianceStatsRepository */
    $allianceStatsRepository = $app[AllianceStatsRepository::class];
    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];

    $objResponse = new xajaxResponse();

    $_SESSION['statsmode'] = $mode;

    $out = "";

    //
    // Allianzdaten
    //
    if ($mode == "alliances") {
        ob_start();
        tableStart("Allianzen");
        echo "<tr>";
        echo "<th style=\"width:50px;\">Rang</th>";
        echo "<th>Tag</th>";
        echo "<th>Name</th>";
        if ($sort == "points")
            echo "<th><i>Punkte</i> ";
        else
            echo "<th>Punkte ";
        echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','points','DESC')\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
        echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','points','ASC')\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
        if ($sort == "uavg")
            echo "<th><i>User-Schnitt</i> ";
        else
            echo "<th>User-Schnitt ";
        echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','uavg','DESC')\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
        echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','uavg','ASC')\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
        echo "</th>";
        if ($sort == "cnt")
            echo "<th><i>User</i> ";
        else
            echo "<th>User ";
        echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','cnt','DESC')\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
        echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','cnt','ASC')\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
        echo "</tr>";
        $search = AllianceStatsSort::create();
        if ($sort != "" && $sortOrder != "") {
            $search = $search->withSort($sort, $sortOrder);
        }
        $entries = $allianceStatsRepository->getStats($search);
        if (count($entries) > 0) {
            $cnt = 1;
            foreach ($entries as $stats) {
                $addstyle = "";
                if ($stats->allianceTag == $_SESSION['alliance_tag'])
                    $addstyle = " class=\"userAllianceMemberColor\"";
                echo "<tr>";
                echo  "<td $addstyle " . tm("Punkteverlauf", "<div><img src=\"misc/alliance_stats.image.php?alliance=" . $stats->allianceId . "\" alt=\"Diagramm\" style=\"width:600px;height:400px;background:#335 url(images/loading335.gif) no-repeat 300px 200px;\" /></div>") . ">
                " . nf($cnt) . " ";
                if ($stats->currentRank == $stats->lastRank)
                    echo  "<img src=\"images/stats/stat_same.gif\" alt=\"same\" width=\"21\" height=\"9\" />";
                elseif ($stats->currentRank > $stats->lastRank)
                    echo  "<img src=\"images/stats/stat_down.gif\" alt=\"up\" width=\"9\" height=\"12\" />";
                elseif ($stats->currentRank < $stats->lastRank)
                    echo  "<img src=\"images/stats/stat_up.gif\" alt=\"down\" width=\"9\" height=\"11\" />";
                echo "<td $addstyle>" . ($stats->allianceTag) . "</td>";
                echo "<td >
                <div id=\"ttuser" . $stats->allianceId . "\" style=\"display:none;\">
                    " . popUp("Allianzseite", "page=alliance&id=" . $stats->allianceId) . "<br/>
                    " . popUp("Punkteverlauf", "page=$page&amp;mode=$mode&amp;alliancedetail=" . $stats->allianceId) . "<br/>";
                echo "</div><a $addstyle href=\"#\" " . cTT($stats->allianceName, "ttuser" . $stats->allianceId) . ">
                " . $stats->allianceName . "</td>";
                echo "<td $addstyle>" . nf($stats->points) . "</td>";
                echo "<td $addstyle >" . nf($stats->userAverage) . "</td>";
                echo "<td $addstyle>" . nf($stats->count) . "</td>";
                echo "</tr>";
                $cnt++;
            }
        } else {
            echo "<tr><td colspan=\"8\" align=\"center\"><i>Keine Allianzen in der Statistik</i></tr>";
        }
        tableEnd();
        $objResponse->assign('statsBox', 'innerHTML', ob_get_clean());
    }

    //
    // Allianzbasis
    //
    elseif ($mode == "base") {
        ob_start();
        tableStart("Allianzbasis");
        echo "<tr>";
        echo "<th style=\"width:50px;\">Rang</th>";
        echo "<th>Tag</th>";
        if ($sort == "bpoints")
            echo "<th><i>Gebäude</i> ";
        else
            echo "<th>Gebäude ";
        echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','bpoints','DESC')\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
        echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','bpoints','ASC')\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a></th>";
        if ($sort == "tpoints")
            echo "<th><i>Forschung</i> ";
        else
            echo "<th>Forschung ";
        echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','tpoints','DESC')\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
        echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','tpoints','ASC')\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a></th>";
        if ($sort == "spoints")
            echo "<th><i>Schiffe</i> ";
        else
            echo "<th>Schiffe ";
        echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','spoints','DESC')\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
        echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','spoints','ASC')\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a></th>";
        if ($sort == "epoints")
            echo "<th><i>Erfahrung</i> ";
        else
            echo "<th>Allianzbasis ";
        echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','apoints','DESC')\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
        echo "<a href=\"javascript:;\" onclick=\"xajax_statsShowBox('$mode','apoints','ASC')\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a></th>";
        echo "</tr>";

        $search = AllianceStatsSort::createAllianceBase();
        if ($sort != "" && $sortOrder != "") {
            $search = $search->withSort($sort, $sortOrder);
        }
        $entries = $allianceStatsRepository->getStats($search);
        if (count($entries) > 0) {
            $cnt = 1;
            foreach ($entries as $stats) {
                $addstyle = "";
                if ($stats->allianceTag == $_SESSION['alliance_tag'])
                    $addstyle = " class=\"userAllianceMemberColor\"";
                echo "<tr>
                        <td $addstyle>
                            " . nf($cnt) . "
                        </td>";
                echo "<td $addstyle>
                <div id=\"ttuser" . $stats->allianceId . "\" style=\"display:none;\">
                    " . popUp("Allianzseite", "page=alliance&id=" . $stats->allianceId) . "<br/>
                    " . popUp("Punkteverlauf", "page=$page&amp;mode=$mode&amp;alliancedetail=" . $stats->allianceId) . "<br/>";
                echo "</div><a $addstyle href=\"#\" " . cTT($stats->allianceName, "ttuser" . $stats->allianceId) . ">
                " . $stats->allianceTag . "</td>";
                echo "<td $addstyle>" . nf($stats->buildingPoints) . "</td>";
                echo "<td $addstyle>" . nf($stats->technologyPoints) . "</td>";
                echo "<td $addstyle>" . nf($stats->shipPoints) . "</td>";
                echo "<td $addstyle>" . nf($stats->alliancePoints) . "</td>";
                echo "</tr>";
                $cnt++;
            }
        } else {
            echo "<tr><td colspan=\"8\" align=\"center\"><i>Keine Allianzen in der Statistik</i></tr>";
        }
        tableEnd();
        $objResponse->assign('statsBox', 'innerHTML', ob_get_clean());
    }

    //
    // Gamestats
    //
    elseif ($mode == "gamestats") {
        ob_start();
        if (is_file(USERSTATS_OUTFILE)) {
            echo '<p><img src="' . USERSTATS_OUTFILE . '" alt="Userstats" /></p>';
        }

        /** @var GameStatsGenerator */
        $gameStatsGenerator = $app[GameStatsGenerator::class];
        echo $gameStatsGenerator->readCached() ?? "<p>Statistiken noch nicht vorhanden!</p>";

        $objResponse->assign('statsBox', 'innerHTML', ob_get_clean());
    }

    //
    // Pranger
    //
    elseif ($mode == "pillory") {
        $entries = $userRepository->getPillory();
        ob_start();
        tableStart("Pranger");
        echo "
        <tr>
            <th>Nick</th>
            <th>Von:</th>
            <th>Bis:</th>
            <th>Admin</th>
            <th>Grund der Sperrung</th>
        </tr>";
        if (count($entries) > 0) {
            foreach ($entries as $entry) {
                echo "<tr>
                <td>" . $entry->userNick . "</td>
                <td>" . df($entry->blockedFrom) . "</td>
                <td>" . df($entry->blockedTo) . "</td>
                <td><a href=\"mailto:" . $entry->adminEmail . "\">" . $entry->adminNick . "</a></td>
                <td>" . text2html($entry->banReason) . "</td>
                </tr>";
            }
        } else
            echo "<tr><td colspan=\"5\"><i>Keine Eintr&auml;ge vorhanden</i></tr>";
        tableEnd();
        $objResponse->assign('statsBox', 'innerHTML', ob_get_clean());
    }

    //
    // Titles
    //
    elseif ($mode == "titles") {
        /** @var RankingService $rankingService */
        $rankingService = $app[RankingService::class];

        ob_start();
        if (!file_exists($rankingService->getUserTitlesCacheFilePath())) {
            echo "<b>Fehler! Die Liste wurde noch nicht erstellt! Bitte das nächste Statistikupdate abwarten.<br/><br/>";
        } else {
            include($rankingService->getUserTitlesCacheFilePath());
        }
        $out .= ob_get_contents();
        ob_end_clean();
        $objResponse->assign('statsBox', 'innerHTML', $out);
    }

    //
    // Normal Stats
    //
    else {
        ob_start();
        iBoxStart("Statistik");
        echo "<div id=\"statsHeaderContainer\">
        <div id=\"statsSearchContainer\">
            <b>&nbsp;&nbsp;Suche:</b>
            <input type=\"text\" class=\"search\" name=\"user_nick\" autocomplete=\"off\" value=\"\" size=\"\" onclick=\"this.select()\" onkeyup=\"
            if(window.mytimeout) window.clearTimeout(window.mytimeout);
             window.mytimeout = window.setTimeout('loadingMsg(\'statsTable\',\'Suche Spieler...\');xajax_statsShowTable(\'$mode\',0,document.getElementById(\'searchString\').value);', 500);
             return true;\" id=\"searchString\"/>
            <input type=\"button\" onclick=\"loadingMsg('statsTable','Lade Statistiktabelle...');getElementById('searchString').value='';xajax_statsShowTable('$mode');\" value=\"Reset\" />
            <input type=\"button\" onclick=\"loadingMsg('statsTable','Suche Spieler...');getElementById('searchString').value='" . $_SESSION['user_nick'] . "';xajax_statsShowTable('$mode',0,'" . $_SESSION['user_nick'] . "',1);\" value=\"" . $_SESSION['user_nick'] . "\" />
        </div>";
        echo "<div id=\"statsNav1\">";
        // >> AJAX generated content here
        echo "</div>
        <br style=\"clear:both;\"/>
        </div>
        <div id=\"statsTable\">";
        echo "<div class=\"loadingMsg\">Lade Statistiktabelle...</div>";
        // >> AJAX generated content here
        echo "</div>
        <div id=\"statsNav2\">";
        // >> AJAX generated content here
        echo "</div>
        <br style=\"clear:both;\"/>";
        iBoxEnd();
        $objResponse->assign('statsBox', 'innerHTML', ob_get_clean());
        $objResponse->script("xajax_statsShowTable('$mode');");
    }

    return $objResponse;
}

function statsShowTable($mode, $limit = 0, $userstring = "", $absolute = 0, $orderBy = '')
{
    global $page;
    $objResponse = new xajaxResponse();

    // TODO
    global $app;

    /** @var ConfigurationService */
    $config = $app[ConfigurationService::class];
    /** @var UserRatingRepository $userRatingRepository */
    $userRatingRepository = $app[UserRatingRepository::class];
    /** @var UserStatRepository $userStatRepository */
    $userStatRepository = $app[UserStatRepository::class];

    $numRows = $config->getInt('stats_num_rows');

    // Datensatznavigation
    $counter = 0;
    if ($mode == "diplomacy" || $mode == "battle" || $mode == "trade") {
        $num = $userRatingRepository->count();
        $counter = ($limit > 0) ? $limit : 0;
    } else {
        $num = $userStatRepository->count();
    }

    $offset = 0;
    if ($limit > 0) {
        $nextlimit = $limit + $numRows;
        $prevlimit = $limit - $numRows;
        $offset = $limit;
    } else {
        $nextlimit = $numRows;
        $prevlimit = -1;
    }
    $lastlimit = (ceil($num / $numRows) * $numRows) - $numRows;

    // Punktetabelle
    if ($num > 0) {
        $ratingSort = new UserRatingSort();
        if ($orderBy == 'nickUp') {
            $ratingSort = UserRatingSort::nick('DESC');
        } elseif ($orderBy == 'nickDown') {
            $ratingSort = UserRatingSort::nick('ASC');
        } elseif ($orderBy == 'rankUp') {
            $ratingSort = UserRatingSort::rank('DESC');
        } elseif ($orderBy == 'rankDown') {
            $ratingSort = UserRatingSort::rank('ASC');
        } elseif ($orderBy == 'allyUp') {
            $ratingSort = UserRatingSort::allianceTag('DESC');
        } elseif ($orderBy == 'allyDown') {
            $ratingSort = UserRatingSort::allianceTag('ASC');
        }

        $ratingSearch = UserRatingSearch::create()->ghost(false);
        if ($userstring !== '') {
            $offset = 0;
            $ratingSearch->nick($userstring);
        }

        $titleContainer = $contentContainer = [];
        if ($mode === "diplomacy") {
            $entries = $userRatingRepository->getDiplomacyRating($ratingSearch, $ratingSort, $numRows, $offset);
            $title = "Diplomatiewertung";
            $titleContainer = array("#", "Nick", "Rasse", "Allianz", "Bewertung");
            $contentContainer = array("counter", "user_nick", "raceName", "allianceTag", "rating");
        } elseif ($mode == "trade") {
            $entries = $userRatingRepository->getTradeRating($ratingSearch, $ratingSort, $numRows, $offset);
            $title = "Handelswertung";
            $titleContainer = array("#", "Nick", "Rasse", "Allianz", "Einkäufe", "Verkäufe", "Bewertung");
            $contentContainer = array("counter", "user_nick", "raceName", "allianceTag", "tradesBuy", "tradesSell", "rating");
        } elseif ($mode == "battle") {
            $entries = $userRatingRepository->getBattleRating($ratingSearch, $ratingSort, $numRows, $offset);
            $title = "Kampfwertung";
            $titleContainer = array("#", "Nick", "Rasse", "Allianz", "Kämpfe Gewonnen", "Kämpfe Verloren", "Kämpfe Total", "Bewertung", "Elo Rating");
            $contentContainer = array("counter", "user_nick", "raceName", "allianceTag", "battlesWon", "battlesLost", "battlesFought", "rating", "eloRating");
        } else {
            if ($mode == "ships") {
                $title = "Schiffspunkte";
                $search = UserStatSearch::ships();
            }  elseif ($mode == "tech") {
                $search = UserStatSearch::technologies();
                $title = "Technologiepunkte";
            } elseif ($mode == "buildings") {
                $search = UserStatSearch::buildings();
                $title = "Gebäudepunkte";
            } elseif ($mode == "exp") {
                $search = UserStatSearch::exp();
                $title = "Erfahrungspunkte";
            } else {
                $search = UserStatSearch::points();
                $title = "Gesamtpunkte";
            }

            if ($userstring !== '') {
                $search->nick($userstring);
            }

            $entries = $userStatRepository->searchStats($search, $ratingSort, $numRows, $offset);
        }

        $nr = count($entries);
        if ($nr > 0) {
            // Navigationsfeld
            ob_start();
            if ($userstring == '') {
                if ($prevlimit > -1 && $numRows * 2 < $num)
                    echo "<input type=\"button\" value=\"&lt;&lt;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',0,'',0,'$orderBy')\">";
                if ($prevlimit > -1)
                    echo "<input type=\"button\" value=\"&lt;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',$prevlimit,'',0,'$orderBy')\">";
                if ($nextlimit < $num)
                    echo "<input type=\"button\" value=\"&gt;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',$nextlimit,'',0,'$orderBy')\">";
                if ($nextlimit < $num && $numRows * 2 < $num)
                    echo "<input type=\"button\" value=\"&gt;&gt;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',$lastlimit,'',0,'$orderBy')\">";
                echo "<select onchange=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',this.options[this.selectedIndex].value,'',0,'$orderBy')\">";
                for ($x = 1; $x <= $num; $x += $numRows) {
                    $dif = $x + $numRows - 1;
                    if ($dif > $num) $dif = $num;
                    $oval = $x - 1;
                    echo "<option value=\"$oval\"";
                    if ($limit == $oval)
                        echo " selected=\"selected\"";
                    echo ">$x - $dif</option>";
                }
                echo "</select>";
            }
            $out = ob_get_clean();
            $objResponse->assign('statsNav1', 'innerHTML', $out);
            $objResponse->assign('statsNav2', 'innerHTML', $out);

            // Tabelle
            $out = "<table class=\"tb\" style=\"width:100%\">";
            $colspan = ($mode == "diplomacy" || $mode == "battle" || $mode == "trade") ? count($titleContainer) : 7;
            $out .= "<tr><th colspan=\"$colspan\" style=\"text-align:center;\">" . $title . "</th></tr>";
            $out .= "<tr>";
            if ($mode == "diplomacy" || $mode == "battle" || $mode == "trade") {
                foreach ($titleContainer as $tit) {
                    if ($tit == "#") $out .= "<th style=\"width:25px;\">";
                    else $out .= "<th>";
                    $out .= $tit;
                    $out .= "</th>";
                }
            } else {
                $out .= "<th style=\"width:50px;\">#
                    <a href=\"javascript:;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',0,'',0,'rankDown')\"><img src=\"images/s_asc.png\"/></a>
                    <a href=\"javascript:;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',0,'',0,'rankUp')\"><img src=\"images/s_desc.png\"/></a></th>
                </th>
                <th>Nick
                    <a href=\"javascript:;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',0,'',0,'nickDown')\"><img src=\"images/s_asc.png\"/></a>
                    <a href=\"javascript:;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',0,'',0,'nickUp')\"><img src=\"images/s_desc.png\"/></a></th>
                <th>Rasse</th>
                <th>Sektor</th>
                <th>Allianz
                    <a href=\"javascript:;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',0,'',0,'allyDown')\"><img src=\"images/s_asc.png\"/></a>
                    <a href=\"javascript:;\" onclick=\"loadingMsgPrepend('statsTable','Lade...');xajax_statsShowTable('$mode',0,'',0,'allyUp')\"><img src=\"images/s_desc.png\"/></a></th>
                </th>
                <th>Punkte</th>";
            }
            $out .= "</tr>";
            foreach ($entries as $entry) {
                if ($entry instanceof \EtoA\User\UserRating) {
                    if ($entry->userId == $_SESSION['user_id']) {
                        $addstyle = " class=\"userSelfColor\"";
                    } elseif ($entry->allianceTag != '' && $entry->allianceTag == $_SESSION['alliance_tag']) {
                        $addstyle = " class=\"userAllianceMemberColor\"";
                    } else {
                        $addstyle = "";
                    }

                    $out .= "<tr>";
                    foreach ($contentContainer as $content) {
                        if ($content == "counter") $out .= "<td $addstyle>" . ++$counter . "</td>";
                        elseif ($content === "user_nick") {
                            $out .= "<td $addstyle >
                            <div id=\"ttuser" . $entry->userId . "\" style=\"display:none;\">
                            " . popUp("Profil anzeigen", "page=userinfo&id=" . $entry->userId) . "<br/>
                            " . popUp("Punkteverlauf", "page=$page&amp;mode=$mode&amp;userdetail=" . $entry->userId) . "<br/>";
                            if ($entry->userId != $_SESSION['user_id']) {
                                $out .=  "<a href=\"?page=messages&mode=new&message_user_to=" . $entry->userId . "\">Nachricht senden</a><br/>";
                                $out .=  "<a href=\"?page=buddylist&add_id=" . $entry->userId . "\">Als Freund hinzufügen</a>";
                            }
                            $out .= "</div>
                            <a $addstyle href=\"#\" " . cTT($entry->userNick, "ttuser" . $entry->userId) . ">" . $entry->userNick . "</a></td>";
                        } else {
                            $out .= "<td $addstyle>" . $entry->{$content} . "</td>";
                        }
                    }
                } else {
                    if ($entry->id == $_SESSION['user_id']) {
                        $addstyle = " class=\"userSelfColor\"";
                    } elseif ($entry->blocked) {
                        $addstyle = " class=\"userLockedColor\"";
                    } elseif ($entry->hmod) {
                        $addstyle = " class=\"userHolidayColor\"";
                    } elseif ($entry->inactive) {
                        $addstyle = " class=\"userInactiveColor\"";
                    } elseif ($entry->allianceTag != '' && $entry->allianceTag == $_SESSION['alliance_tag']) {
                        $addstyle = " class=\"userAllianceMemberColor\"";
                    } else {
                        $addstyle = "";
                    }
                    $out .= "<tr>";

                    $out .= "<td $addstyle  align=\"right\" ";
                    if ($mode == "user")
                        $out .= tm("Punkteverlauf", "<div><img src=\"misc/stats.image.php?user=" . $entry->id . "\" alt=\"Diagramm\" style=\"width:600px;height:400px;background:#335 url(images/loading335.gif) no-repeat 300px 200px;\" /></div>");
                    $out .= ">" . nf($entry->rank) . " ";
                    if ($entry->shift === 2)
                        $out .= "<img src=\"images/stats/stat_down.gif\" alt=\"down\" width=\"9\" height=\"12\" />";
                    elseif ($entry->shift === 1)
                        $out .= "<img src=\"images/stats/stat_up.gif\" alt=\"up\" width=\"9\" height=\"11\" />";
                    else
                        $out .= "<img src=\"images/stats/stat_same.gif\" alt=\"same\" width=\"21\" height=\"9\" />";
                    $out .= "</td>";
                    $out .= "<td $addstyle >
                    <div id=\"ttuser" . $entry->id . "\" style=\"display:none;\">
                    " . popUp("Profil anzeigen", "page=userinfo&id=" . $entry->id) . "<br/>
                    " . popUp("Punkteverlauf", "page=$page&amp;mode=$mode&amp;userdetail=" . $entry->id) . "<br/>";
                    if ($entry->id != $_SESSION['user_id']) {
                        $out .=  "<a href=\"?page=messages&mode=new&message_user_to=" . $entry->id . "\">Nachricht senden</a><br/>";
                        $out .=  "<a href=\"?page=buddylist&add_id=" . $entry->id . "\">Als Freund hinzufügen</a>";
                    }
                    $out .= "</div>
                    <a $addstyle href=\"#\" " . cTT($entry->nick, "ttuser" . $entry->id) . ">" . $entry->nick . "</a></td>";
                    $out .= "<td $addstyle >" . $entry->raceName . "</td>";
                    $out .= "<td $addstyle ><a $addstyle href=\"?page=sector&sector=" . $entry->sx . "," . $entry->sy . "\">" . $entry->sx . "/" . $entry->sy . "</a></td>";
                    $out .= "<td $addstyle >" . $entry->allianceTag . "</td>";
                    $out .= "<td $addstyle >" . nf($entry->points) . "</td>";
                    $out .= "</tr>";
                }
            }
            $out .= "</table>";
        } else {
            $out = "<div><i>Es wurden keine Spieler gefunden!</i></div>";
            $objResponse->assign('statsNav1', 'innerHTML', '');
            $objResponse->assign('statsNav2', 'innerHTML', '');
        }
    } else {
        $out = "<div><i>Momentan sind keine Statistiken vorhanden, sie werden
                zur nächsten vollen Stunde erstellt!
        </i></div>";
    }
    $objResponse->assign('statsTable', 'innerHTML', $out);

    return $objResponse;
}
