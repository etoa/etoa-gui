<?PHP

use EtoA\Admin\AdminUserRepository;
use EtoA\User\UserRepository;

echo "<h1>Verwarnungen</h1>";

if (isset($_GET['edit'])) {
    $res = dbquery("
        SELECT
            user_nick,
            user_points,
            user_id,
            warning_text,
            warning_admin_id
        FROM
            users
        INNER JOIN
            user_warnings
        ON
            warning_user_id=user_id
            AND warning_id=" . intval($_GET['edit']) . "
        ;");
    echo "<h2>Verwarnung bearbeiten</h2>";
    if (mysql_num_rows($res) > 0) {
        $arr = mysql_fetch_assoc($res);
        echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">
            <input type=\"hidden\" name=\"warning_id\" value=\"" . $_GET['edit'] . "\" />
                <table class=\"tb\">
                    <tr>
                        <th>User:</th>
                        <td>" . $arr['user_nick'] . "</td>
                    </tr>
                    <tr>
                        <th>Admin:</th>
                        <td>
                            <select name=\"warning_admin_id\">";
        /** @var AdminUserRepository $adminUserRepository */
        $adminUserRepository = $app[AdminUserRepository::class];
        $adminUserNicks = $adminUserRepository->findAllAsList();
        foreach ($adminUserNicks as $adminUserId => $adminUserNick) {
            echo "<option value=\"" . $adminUserId . "\"";
            if ($adminUserId == $arr['warning_admin_id'])
                echo " selected=\"selected\"";
            echo ">" . $adminUserNick . "</option>";
        }
        echo "</select>
                        </td>
                    </tr>
                    <tr>
                        <th>Verwarnungstext:</th>
                        <td><textarea name=\"warning_text\" rows=\"10\" cols=\"70\">" . $arr['warning_text'] . "</textarea></td>
                    </tr>
                </table><br/>
                <input type=\"submit\" name=\"edit\" value=\"Speichern\" />
            </form><br/>";
    }
} else {

    if (isset($_POST['add'])) {
        dbquery("
            INSERT INTO
                user_warnings
            (
                warning_user_id,
                warning_date,
                warning_text,
                warning_admin_id
            )
            VALUES
            (
                " . $_POST['warning_user_id'] . ",
                UNIX_TIMESTAMP(),
                '" . addslashes($_POST['warning_text']) . "',
                " . $cu->id . "
            );");

        /** @var \EtoA\Message\MessageRepository $messageRepository */
        $messageRepository = $app[\EtoA\Message\MessageRepository::class];
        $messageRepository->createSystemMessage((int) $_POST['warning_user_id'], 7, "Verwarnung", "Du hast vom Administrator " . $cu->nick . " eine Verwarnung erhalten!\n\n" . $_POST['warning_text']);

        success_msg("Verwarnung gespeichert!");
    }

    if (isset($_POST['edit'])) {
        dbquery("
            UPDATE
                user_warnings
            SET
                warning_text='" . addslashes($_POST['warning_text']) . "',
                warning_admin_id=" . $_POST['warning_admin_id'] . "
            WHERE
                warning_id=" . $_POST['warning_id'] . "
            ;");
        success_msg("Verwarnung gespeichert!");
    }


    if (isset($_GET['del'])) {
        dbquery("
            DELETE FROM
                user_warnings
            WHERE
                warning_id=" . intval($_GET['del']) . "
            ");
        success_msg("Verwarnung gelöscht!");
    }


    echo "<h2>Neue Verwarnung</h2>";
    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">
            <table class=\"tb\">
                <tr>
                    <th>User:</th>
                    <td>
                        <select name=\"warning_user_id\">";

    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];
    $userNicks = $userRepository->getUserNicknames();
    foreach ($userNicks as $userId => $userNick) {
        echo "<option value=\"" . $userId . "\">" . $userNick . "</option>";
    }
    echo "</select>
                    </td>
                </tr>
                <tr>
                    <th>Verwarnungstext</th>
                    <td><textarea name=\"warning_text\" rows=\"5\" cols=\"70\"></textarea></td>
                </tr>
            </table><br/><input type=\"submit\" name=\"add\" value=\"Neue Verwarnung erteilen\" />
        </form>";

    echo "<h2>Bestehende Verwarnungen</h2>";
    $res = dbquery("
        SELECT
            user_nick,
            user_points,
            user_id,
            COUNT(*) as cnt
        FROM
            users
        INNER JOIN
            user_warnings
        ON
            warning_user_id=user_id
        GROUP BY
            user_id
        ORDER BY user_nick
        ;");
    if (mysql_num_rows($res) > 0) {
        while ($arr = mysql_fetch_array($res)) {
            echo "<div style=\"padding:5px;border-bottom:1px solid #fff\">
                <b>" . $arr['user_nick'] . "</b> &nbsp;
                [<a href=\"#\" onclick=\"toggleBox('w" . $arr['user_id'] . "')\">" . nf($arr['cnt']) . " Verwarnungen</a>] &nbsp;
                [<a href=\"?page=user&amp;sub=edit&amp;id=" . $arr['user_id'] . "\">Daten</a>] &nbsp;
                <table id=\"w" . $arr['user_id'] . "\" style=\"margin-top:10px;" . ((isset($_GET['user']) && $_GET['user'] == $arr['user_id']) ? "" : "display:none;") . "\" class=\"tb\">
                    <tr>
                        <th style=\"\">Text</th>
                        <th style=\"width:130px;\">Datum</th>
                        <th style=\"width:100px;\">Verwarnt von</th>
                        <th style=\"width:150px;\">Optionen</th>
                    </tr>";
            $ures = dbquery("
                        SELECT
                            warning_text,
                            warning_date,
                            user_nick,
                            warning_id
                        FROM
                            user_warnings
                        LEFT JOIN
                            admin_users
                        ON
                            user_id=warning_admin_id
                        WHERE
                            warning_user_id=" . $arr['user_id'] . "
                        ORDER BY
                            warning_date DESC
                        ");
            while ($uarr = mysql_fetch_array($ures)) {
                echo "<tr>
                                <td>" . stripslashes(nl2br($uarr['warning_text'])) . "</td>
                                <td>" . df($uarr['warning_date']) . "</td>
                                <td><b>" . $uarr['user_nick'] . "</b></td>
                                <td>
                                    <a href=\"?page=$page&amp;sub=$sub&amp;edit=" . $uarr['warning_id'] . "\">Bearbeiten</a>
                                    <a href=\"?page=$page&amp;sub=$sub&amp;del=" . $uarr['warning_id'] . "\" onclick=\"return confirm('Verwarnung löschen?')\">Löschen</a>
                                </td>
                            </tr>";
            }

            echo "</table></div>";
        }
    } else {
        echo "<i>Keine Verwarnungen vorhanden!</i>";
    }
}
