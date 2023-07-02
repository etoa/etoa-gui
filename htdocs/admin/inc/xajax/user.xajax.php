<?PHP

use EtoA\Alliance\AllianceRankRepository;
use EtoA\Support\StringUtils;
use EtoA\User\UserPointsRepository;

$xajax->register(XAJAX_FUNCTION, "allianceRankSelector");
$xajax->register(XAJAX_FUNCTION, "userPointsTable");

function allianceRankSelector($parent, $name, $value = 0, $aid = 0)
{
    global $app;

    /** @var AllianceRankRepository $allianceRankRepository */
    $allianceRankRepository = $app[AllianceRankRepository::class];

    $or = new xajaxResponse();
    ob_start();
    if ($aid != 0) {
        $ranks = $allianceRankRepository->getRanks($aid);
        if (count($ranks) > 0) {
            echo "<select name=\"" . $name . "\"><option value=\"0\">(Kein Rang)</option>";
            foreach ($ranks as $rank) {
                echo "<option value=\"" . $rank->id . "\"";
                if ($value == $rank->id) {
                    echo " selected=\"selected\"";
                }
                echo ">" . $rank->name . "</option>";
            }
            echo "</select>";
        } else {
            echo "-";
        }
    } else {
        echo "-";
    }
    $out = ob_get_contents();
    ob_end_clean();
    $or->assign($parent, "innerHTML", $out);
    return $or;
}

function userPointsTable($uid, $target, $length = 100, $start = -1, $end = -1)
{
    global $app;

    /** @var UserPointsRepository $userPointsRepository */
    $userPointsRepository = $app[UserPointsRepository::class];

    $t = time();
    if ($start == -1) {
        $start = $t - 172800;
    }
    if ($end == -1) {
        $end = $t;
    }

    $or = new xajaxResponse();
    ob_start();
    $limitarr = array(10, 20, 30, 50, 100, 200);

    echo "<div id=\"pointGraphDetail\" style=\"text-align:center;margin-bottom:6px;\">
    <img src=\"../admin/images/stats/" . $uid . "&amp;limit=" . $length . "&amp;start=" . $start . "&amp;end=" . $end . "\" alt=\"Diagramm\" />
    <br/>";
    echo "Zeige maximal <select id=\"pointsLimit\" onchange=\"xajax_userPointsTable($uid,'$target',
    document.getElementById('pointsLimit').options[document.getElementById('pointsLimit').selectedIndex].value,
    document.getElementById('pointsTimeStart').options[document.getElementById('pointsTimeStart').selectedIndex].value,
    document.getElementById('pointsTimeEnd').options[document.getElementById('pointsTimeEnd').selectedIndex].value
    );\">";
    foreach ($limitarr as $x) {
        echo "<option value=\"$x\"";
        if ($x == $length) echo " selected=\"selected\"";
        echo ">$x</option>";
    }
    echo "</select> Datensätze von <select id=\"pointsTimeStart\" onchange=\"xajax_userPointsTable($uid,'$target',
    document.getElementById('pointsLimit').options[document.getElementById('pointsLimit').selectedIndex].value,
    document.getElementById('pointsTimeStart').options[document.getElementById('pointsTimeStart').selectedIndex].value,
    document.getElementById('pointsTimeEnd').options[document.getElementById('pointsTimeEnd').selectedIndex].value
    );\">";
    for ($x = $t - 86400; $x > $t - (14 * 86400); $x -= 86400) {
        echo "<option value=\"$x\"";
        if ($x <= $start + 300 && $x >= $start - 300) echo " selected=\"selected\"";
        echo ">" . StringUtils::formatDate($x) . "</option>";
    }
    echo "</select> bis <select id=\"pointsTimeEnd\" onchange=\"xajax_userPointsTable($uid,'$target',
    document.getElementById('pointsLimit').options[document.getElementById('pointsLimit').selectedIndex].value,
    document.getElementById('pointsTimeStart').options[document.getElementById('pointsTimeStart').selectedIndex].value,
    document.getElementById('pointsTimeEnd').options[document.getElementById('pointsTimeEnd').selectedIndex].value
    );\">";
    for ($x = $t; $x > $t - (13 * 86400); $x -= 86400) {
        echo "<option value=\"$x\"";
        if ($x <= $end + 300 && $x >= $end - 300) echo " selected=\"selected\"";
        echo ">" . StringUtils::formatDate($x) . "</option>";
    }
    echo "</select>

    <br/></div>";
    echo "<table class=\"tb\">";
    $userPoints = $userPointsRepository->getPoints($uid, $length, $start, $end);
    if (count($userPoints) > 0) {
        echo "<tr>
            <th>Datum</th>
            <th>Zeit</th>
            <th>Punkte</th>
            <th>Gebäude</th>
            <th>Forschung</th>
            <th>Flotte</th>
        </tr>";
        foreach ($userPoints as $points) {
            echo "<tr>
                <td class=\"tbldata\">" . date("d.m.Y", $points->timestamp) . "</td>
                <td class=\"tbldata\">" . date("H:i", $points->timestamp) . "</td>
                <td class=\"tbldata\">" . StringUtils::formatNumber($points->points) . "</td>
                <td class=\"tbldata\">" . StringUtils::formatNumber($points->buildingPoints) . "</td>
                <td class=\"tbldata\">" . StringUtils::formatNumber($points->techPoints) . "</td>
                <td class=\"tbldata\">" . StringUtils::formatNumber($points->shipPoints) . "</td>
            </tr>";
        }
    } else {
        echo "<tr><td class=\"tbldata\">Keine fehlgeschlagenen Logins</td></tr>";
    }
    echo "</table>";

    $out = ob_get_contents();
    ob_end_clean();
    $or->assign($target, "innerHTML", $out);
    return $or;
}

