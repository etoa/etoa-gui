<?PHP

use EtoA\User\UserRepository;
use EtoA\User\UserSearch;

if (isset($_POST['search_query']) && $_POST['search_query'] != "") {
    $search = $_POST['search_query'];
    echo "<h1>Suche nach <i>" . $search . "</i></h1>";

    // Users
    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];
    $userNicks = $userRepository->searchUserNicknames(UserSearch::create()->nameOrEmailOrDualLike($search), 30);
    if (count($userNicks) > 0) {
        echo "<h2>Spieler</h2><ul>";
        foreach ($userNicks as $userId => $userNick) {
            echo "<li><a href=\"?page=user&amp;sub=edit&amp;id=" . $userId . "\">" . $userNick . "</a></li>";
        }
        echo "</ul>";
    }

    // Alliances
    $res = dbquery("
        SELECT
            alliance_id,
            alliance_name,
            alliance_tag
        FROM
            alliances
        WHERE
            alliance_name LIKE '%" . $search . "%'
            OR alliance_tag LIKE '%" . $search . "%'
        ORDER BY
            alliance_tag
        ");
    if (mysql_num_rows($res) > 0) {
        echo "<h2>Allianzen</h2><ul>";
        while ($arr = mysql_fetch_array($res)) {
            echo "<li><a href=\"?page=alliances&amp;sub=edit&amp;alliance_id=" . $arr['alliance_id'] . "\">[" . $arr['alliance_tag'] . "] " . $arr['alliance_name'] . "</a></li>";
        }
        echo "</ul>";
    }

    // Planets
    $res = dbquery("
        SELECT
            id
        FROM
            planets
        WHERE
            planet_name LIKE '%" . $search . "%'
        ORDER BY planet_name
        LIMIT 30;
        ");
    if (mysql_num_rows($res) > 0) {
        echo "<h2>Planeten</h2><ul>";
        while ($arr = mysql_fetch_array($res)) {
            $pl = Planet::getById($arr['id']);
            echo "<li><a href=\"?page=galaxy&sub=edit&id=" . $arr['id'] . "\">" . $pl . "</a></li>";
        }
        echo "</ul>";
    }
} else {
    echo "<h1>Suche</h1>";
    echo error_msg("Kein Suchbegriff eingegeben!");
}
