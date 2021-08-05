<?PHP

use EtoA\User\UserPointsRepository;
use EtoA\User\UserRepository;

echo "<h1>Punktespeicherung</h1>";

/** @var UserPointsRepository $userPointRepository */
$userPointRepository = $app[UserPointsRepository::class];

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
$userNicks = $userRepository->searchUserNicknames();
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
    $tblcnt = $userPointRepository->count();
    echo "Es sind " . nf($tblcnt) . " Eintr&auml;ge in der Datenbank vorhanden.<br/><br/>";
} else {
    echo "<i>Keine Benutzer vorhanden!</i>";
}

if (isset($_GET['user_id']) && $_GET['user_id'] > 0) {
    $user = $userRepository->getUser((int) $_GET['user_id']);
    if ($user !== null) {
        echo "<h2>Punktedetails f&uuml;r <a href=\"?page=$page&amp;action=edit&amp;id=" . $user->id . "\">" . $user->nick . "</a></h2>";
        echo "<b>Punkte aktuell:</b> " . nf($user->points) . ", <b>Rang aktuell:</b> " . $user->rank . "<br/><br/>";
        echo "<img src=\"../misc/stats.image.php?user=" . $user->id . "\" alt=\"Diagramm\" /><br/><br/>";
        $pointEntries = $userPointRepository->getPoints($user->id);
        if (count($pointEntries) > 0) {
            echo "<table width=\"400\" class=\"tbl\">";
            echo "<tr><th class=\"tbltitle\">Datum</th><th class=\"tbltitle\">Zeit</th><th class=\"tbltitle\">Punkte</th><th class=\"tbltitle\">Flotte</th><th class=\"tbltitle\">Forschung</th><th class=\"tbltitle\">Geb&auml;ude</th></tr>";
            foreach ($pointEntries as $entry) {
                echo "<tr><td class=\"tbldata\">" . date("d.m.Y", $entry->timestamp) . "</td><td class=\"tbldata\">" . date("H:i", $entry->timestamp) . "</td>";
                echo "<td class=\"tbldata\">" . nf($entry->points) . "</td><td class=\"tbldata\">" . nf($entry->shipPoints) . "</td><td class=\"tbldata\">" . nf($entry->techPoints) . "</td><td class=\"tbldata\">" . nf($entry->buildingPoints) . "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "<i>Keine Punktedaten vorhanden!</i>";
        }
    } else {
        echo "<i>Datensatz wurde nicht gefunden!</i>";
    }
}
