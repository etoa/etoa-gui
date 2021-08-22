<?PHP

use EtoA\Alliance\AlliancePointsRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\RuntimeDataStore;
use EtoA\Support\StringUtils;
use EtoA\User\UserPointsRepository;
use EtoA\User\UserRepository;

/** @var RuntimeDataStore $runtimeDataStore */
$runtimeDataStore = $app[RuntimeDataStore::class];

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];
/** @var AllianceRepository $allianceRepository */
$allianceRepository = $app[AllianceRepository::class];
/** @var AlliancePointsRepository $alliancePointsRepository */
$alliancePointsRepository = $app[AlliancePointsRepository::class];

echo "<h1>Statistiken</h1>";

//
// Details anzeigen
//

if (isset($_GET['userdetail']) && intval($_GET['userdetail']) > 0) {
    $udid = intval($_GET['userdetail']);

    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];
    $user = $userRepository->getUser($udid);
    if ($user !== null) {
        tableStart("Statistiken f&uuml;r " . text2html($user->nick) . "");

        echo "<tr><td colspan=\"6\" style=\"text-align:center;\">
                <b>Punkte aktuell:</b> " . StringUtils::formatNumber($user->points) . ", <b>Rang aktuell:</b> " . $user->rank . "
            </td></tr>";
        echo "<tr><td colspan=\"6\" style=\"text-align:center;\">
                <img src=\"misc/stats.image.php?user=" . $user->id . "\" alt=\"Diagramm\" />
            </td></tr>";

        /** @var UserPointsRepository $userPointsRepository */
        $userPointsRepository = $app[UserPointsRepository::class];
        $pointEntries = $userPointsRepository->getPoints($user->id, 48);
        if (count($pointEntries) > 0) {
            echo "<tr><th>Datum</th><th>Zeit</th><th>Punkte</th><th>Flotte</th><th>Forschung</th><th>Geb&auml;ude</th></tr>";
            foreach ($pointEntries as $entry) {
                echo "<tr><td>" . date("d.m.Y", $entry->timestamp) . "</td><td>" . date("H:i", $entry->timestamp) . "</td>";
                echo "<td>" . StringUtils::formatNumber($entry->points) . "</td><td>" . StringUtils::formatNumber($entry->shipPoints) . "</td><td>" . StringUtils::formatNumber($entry->techPoints) . "</td><td>" . StringUtils::formatNumber($entry->buildingPoints) . "</td></tr>";
            }
        } else {
            echo "<tr><td colspan=\"6\"><i>Keine Punktedaten vorhanden!</td></tr>";
        }

        tableEnd();

        if (!$popup)
            echo "<input type=\"button\" value=\"Profil anzeigen\" onclick=\"document.location='?page=userinfo&id=" . $user->id . "'\" /> &nbsp; ";
    } else
        error_msg("Datensatz wurde nicht gefunden!");
} elseif (isset($_GET['alliancedetail']) && intval($_GET['alliancedetail']) > 0) {
    $adid = intval($_GET['alliancedetail']);

    $alliance = $allianceRepository->getAlliance($adid);
    if ($alliance !== null) {
        echo "<h2>Punktedetails f&uuml;r [" . text2html($alliance->tag) . "] " . text2html($alliance->name) . "</h2>";
        echo "<b>Punkte aktuell:</b> " . StringUtils::formatNumber($alliance->points) . ", <b>Rang aktuell:</b> " . $alliance->currentRank . "<br/><br/>";
        echo "<img src=\"misc/alliance_stats.image.php?alliance=" . $alliance->id . "\" alt=\"Diagramm\" /><br/><br/>";
        $pointEntries = $alliancePointsRepository->getPoints($alliance->id, 48);
        if (count($pointEntries) > 0) {
            tableStart('', '400');
            echo "<tr><th>Datum</th><th>Zeit</th><th>Punkte</th><th>User-Schnitt</th><th>User</th></tr>";
            foreach ($pointEntries as $entry) {
                echo "<tr><td>" . date("d.m.Y", $entry->timestamp) . "</td><td>" . date("H:i", $entry->timestamp) . "</td>";
                echo "<td>" . StringUtils::formatNumber($entry->points) . "</td><td>" . StringUtils::formatNumber($entry->avg) . "</td><td>" . StringUtils::formatNumber($entry->count) . "</td></tr>";
            }
            tableEnd();
            echo "<input type=\"button\" value=\"Allianzdetails anzeigen\" onclick=\"document.location='?page=alliance&info_id=" . $alliance->id . "'\" /> &nbsp; ";
        } else
            error_msg("Keine Punktedaten vorhanden!");
    } else
        error_msg("Datensatz wurde nicht gefunden!");

    $limit = 0;
    if (isset($_GET['limit'])) {
        $limit = intval($_GET['limit']);
    }
    echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page&mode=$mode&limit=" . $limit . "'\" /> &nbsp; ";
}

//
// Tabellen anzeigen
//

else {
    $alliance = $allianceRepository->getAlliance($cu->allianceId());
    $_SESSION['alliance_tag'] = $alliance !== null ? $alliance->tag : null;

    $ddm = new DropdownMenu(1);
    $ddm->add('total', 'Gesamtstatistik', 'xajax_statsShowBox(\'user\');');

    $ddm->add('buildings', 'Gebäude', 'xajax_statsShowBox(\'buildings\');');
    $ddm->add('tech', 'Forschung', 'xajax_statsShowBox(\'tech\');');
    $ddm->add('ships', 'Schiffe', 'xajax_statsShowBox(\'ships\');');
    $ddm->add('exp', 'Erfahrung', 'xajax_statsShowBox(\'exp\');');

    $ddm->add('battle', 'Kampf', 'xajax_statsShowBox(\'battle\');');
    $ddm->add('trade', 'Handel', 'xajax_statsShowBox(\'trade\');');
    $ddm->add('diplomacy', 'Diplomatie', 'xajax_statsShowBox(\'diplomacy\');');
    echo $ddm;

    $ddm = new DropdownMenu(1);
    $ddm->add('alliances', 'Allianzen', 'xajax_statsShowBox(\'alliances\');');
    $ddm->add('base', 'Allianzbasis', 'xajax_statsShowBox(\'base\');');
    $ddm->add('titles', 'Titel', 'xajax_statsShowBox(\'titles\');');

    $ddm->add('pillory', 'Pranger', 'xajax_statsShowBox(\'pillory\');');
    $ddm->add('gamestats', 'Spielstatistik', 'xajax_statsShowBox(\'gamestats\');');

    echo $ddm;



    echo "<br/>";

    echo "<div id=\"statsBox\">
    <div class=\"loadingMsg\">Lade Daten... <br/>(JavaScript muss aktiviert sein!)</div>";
    // >> AJAX generated content inserted here
    echo "</div>";

    if (isset($_GET['mode']) && ctype_alpha($_GET['mode'])) {
        $mode = $_GET['mode'];
    } elseif (isset($_SESSION['statsmode'])) {
        $mode = $_SESSION['statsmode'];
    } else {
        $mode = "user";
    }

    echo "<script type=\"text/javascript\">
        xajax_statsShowBox('" . $mode . "');
        </script><br/>";


    // Legende
    iBoxStart("Legende zur Statistik");
    echo "<b>Farben:</b>
        <span class=\"userSelfColor\">Eigener Account</span>,
        <span class=\"userLockedColor\">Gesperrt</span>,
        <span class=\"userHolidayColor\">Urlaubsmodus</span>,
        <span class=\"userInactiveColor\">Inaktiv (" . $config->getInt('user_inactive_days') . " Tage)</span>,
        <span class=\"userAllianceMemberColor\">Allianz(-mitglied)</span>
        <br/>";
    $statsUpdate = $runtimeDataStore->get('statsupdate');
    if ($statsUpdate != null) {
        echo "Letzte Aktualisierung: <b>" . df($statsUpdate) . " Uhr</b><br/>";
    }
    echo "Die Aktualisierung der Punkte erfolgt ";
    $h = $config->getInt('points_update') / 3600;
    if ($h > 1)
        echo "alle $h Stunden!<br>";
    elseif ($h == 1)
        echo " jede Stunde!<br>";
    else {
        $m = $config->getInt('points_update') / 60;
        echo "alle $m Minuten!<br/>";
    }
    echo "Neu angemeldete Benutzer erscheinen erst nach der ersten Aktualisierung in der Liste.<br/>";
    echo "F&uuml;r " . $config->param1Int('points_update') . " verbaute Rohstoffe bekommt der Spieler 1 Punkt in der Statistik<br/>
        F&uuml;r " . $config->param2Int('points_update') . " Spielerpunkte bekommt die Allianz 1 Punkt in der Statistik";
    iBoxEnd();
}
