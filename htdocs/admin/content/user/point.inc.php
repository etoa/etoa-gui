<?PHP

use EtoA\User\UserRepository;

echo "<h1>Punktespeicherung</h1>";

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
$userNicks = $userRepository->getUserNicknames();
if (count($userNicks) > 0) {
    echo "<b>Punkteentwicklung anzeigen f&uuml;r:</b> <select onchange=\"document.location='?page=$page&sub=$sub&user_id='+this.options[this.selectedIndex].value\">";
    echo "<option value=\"0\" style=\"font-style:italic;\">(Benutzer w&auml;hlen...)</option>";
    foreach ($userNicks as $userId => $userNick) {
        echo "<option value=\"" . $userId . "\"";
        if (isset($_GET['user_id']) && $_GET['user_id'] == $userId) {
            echo ' selected="selected"';
        }
        echo ">" . $userNick . "</option>";
    }
    echo "</select><br/><br/>";
    $tblcnt = mysql_fetch_row(dbquery("
            SELECT
                COUNT(point_id)
            FROM
                user_points
            ;"));
    echo "Es sind " . nf($tblcnt[0]) . " Eintr&auml;ge in der Datenbank vorhanden.<br/><br/>";
} else {
    echo "<i>Keine Benutzer vorhanden!</i>";
}

if (isset($_GET['user_id']) && $_GET['user_id'] > 0) {
    $user = $userRepository->getUser((int) $_GET['user_id']);
    if ($user !== null) {
        echo "<h2>Punktedetails f&uuml;r <a href=\"?page=$page&amp;action=edit&amp;id=" . $user->id . "\">" . $user->nick . "</a></h2>";
        echo "<b>Punkte aktuell:</b> " . nf($user->points) . ", <b>Rang aktuell:</b> " . $user->rank . "<br/><br/>";
        echo "<img src=\"../misc/stats.image.php?user=" . $user->id . "\" alt=\"Diagramm\" /><br/><br/>";
        $pres = dbquery("SELECT * FROM user_points WHERE point_user_id='" . $user->id . "' ORDER BY point_timestamp DESC;");
        if (mysql_num_rows($pres) > 0) {
            $points = [];
            $fleet = [];
            $tech = [];
            $buildings = [];
            while ($parr = mysql_fetch_array($pres)) {
                $points[$parr['point_timestamp']] = $parr['point_points'];
                $fleet[$parr['point_timestamp']] = $parr['point_ship_points'];
                $tech[$parr['point_timestamp']] = $parr['point_tech_points'];
                $buildings[$parr['point_timestamp']] = $parr['point_building_points'];
            }
            echo "<table width=\"400\" class=\"tbl\">";
            echo "<tr><th class=\"tbltitle\">Datum</th><th class=\"tbltitle\">Zeit</th><th class=\"tbltitle\">Punkte</th><th class=\"tbltitle\">Flotte</th><th class=\"tbltitle\">Forschung</th><th class=\"tbltitle\">Geb&auml;ude</th></tr>";
            foreach ($points as $time => $val) {
                echo "<tr><td class=\"tbldata\">" . date("d.m.Y", $time) . "</td><td class=\"tbldata\">" . date("H:i", $time) . "</td>";
                echo "<td class=\"tbldata\">" . nf($val) . "</td><td class=\"tbldata\">" . nf($fleet[$time]) . "</td><td class=\"tbldata\">" . nf($tech[$time]) . "</td><td class=\"tbldata\">" . nf($buildings[$time]) . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<i>Keine Punktedaten vorhanden!</i>";
        }
    } else {
        echo "<i>Datensatz wurde nicht gefunden!</i>";
    }
}
