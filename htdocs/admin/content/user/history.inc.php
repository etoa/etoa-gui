<?PHP

use EtoA\User\UserRepository;

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
echo "<h1>User-History</h1>";
$userNicks = $userRepository->getUserNicknames();
echo "<h2>Auswahl</h2>";
echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
echo "Spieler ausw&auml;hlen: <select name=\"user_id\">";
foreach ($userNicks as $userId => $userNick) {
    $hcnt = mysql_fetch_row(dbquery("SELECT COUNT(*) FROM user_history WHERE history_user_id='" . $userId . "' ORDER BY history_timestamp ASC;"));
    if ($hcnt[0] > 0)
        echo "<option value=\"$userId\">" . $userNick . " (" . $hcnt[0] . " Eintr&auml;ge)</option>";
}
echo "</select> <input type=\"submit\" name=\"submit\" value=\"Anzeigen\" />";


if ((isset($_POST['submit']) && $_POST['user_id'] > 0) || (isset($_GET['id']) && $_GET['id'] > 0)) {
    if ($_POST['user_id'] != "")
        $userid = $_POST['user_id'];
    else
        $userid = $_GET['id'];

    $user = $userRepository->getUser((int) $userid);
    echo "<h2>Geschichte der Users <a href=\"?page=user&sub=edit&user_id=" . $user->id . "\">" . $user->nick . "</a> (" . $user->name . ", " . $user->email . ")</h2>";
    $hres = dbquery("SELECT * FROM user_history WHERE history_user_id='" . $user->id . "' ORDER BY history_timestamp ASC;");
    if (mysql_num_rows($hres) > 0) {
        echo "<table>";
        echo "<tr><th class=\"tbltitle\" style=\"width:120px;\">Datum / Zeit</th><th class=\"tbltitle\">Ereignis</th></tr>";
        while ($harr = mysql_fetch_array($hres)) {
            echo "<tr><td class=\"tbldata\">" . date("d.m.Y H:i", $harr['history_timestamp']) . "</td><td class=\"tbldata\">" . text2html($harr['history_text']) . "</td></tr>";
        }
        echo "</table><br/><br/>";
    } else
        echo "<i>Keine Daten vorhanden!</i><br/><br/>";
}
