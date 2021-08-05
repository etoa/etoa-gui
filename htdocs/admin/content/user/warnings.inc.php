<?PHP

use EtoA\Admin\AdminUserRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserWarningRepository;

echo "<h1>Verwarnungen</h1>";

/** @var UserWarningRepository $userWarningRepository */
$userWarningRepository = $app[UserWarningRepository::class];
/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
$userNicks = $userRepository->searchUserNicknames();

if (isset($_GET['edit'])) {
    $warning = $userWarningRepository->getWarning((int) $_GET['edit']);
    echo "<h2>Verwarnung bearbeiten</h2>";
    if ($warning !== null) {
        echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">
            <input type=\"hidden\" name=\"warning_id\" value=\"" . $_GET['edit'] . "\" />
                <table class=\"tb\">
                    <tr>
                        <th>User:</th>
                        <td>" . $userNicks[$warning->userId] . "</td>
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
            if ($adminUserId === $warning->adminId)
                echo " selected=\"selected\"";
            echo ">" . $adminUserNick . "</option>";
        }
        echo "</select>
                        </td>
                    </tr>
                    <tr>
                        <th>Verwarnungstext:</th>
                        <td><textarea name=\"warning_text\" rows=\"10\" cols=\"70\">" . $warning->text . "</textarea></td>
                    </tr>
                </table><br/>
                <input type=\"submit\" name=\"edit\" value=\"Speichern\" />
            </form><br/>";
    }
} else {

    if (isset($_POST['add'])) {
        $userWarningRepository->addEntry((int) $_POST['warning_user_id'], $_POST['warning_text'], $cu->id);

        /** @var \EtoA\Message\MessageRepository $messageRepository */
        $messageRepository = $app[\EtoA\Message\MessageRepository::class];
        $messageRepository->createSystemMessage((int) $_POST['warning_user_id'], 7, "Verwarnung", "Du hast vom Administrator " . $cu->nick . " eine Verwarnung erhalten!\n\n" . $_POST['warning_text']);

        success_msg("Verwarnung gespeichert!");
    }

    if (isset($_POST['edit'])) {
        $userWarningRepository->updateEntry((int) $_POST['warning_id'], $_POST['warning_text'], (int) $_POST['warning_admin_id']);
        success_msg("Verwarnung gespeichert!");
    }


    if (isset($_GET['del'])) {
        $userWarningRepository->deleteEntry((int) $_GET['del']);
        success_msg("Verwarnung gelöscht!");
    }


    echo "<h2>Neue Verwarnung</h2>";
    echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">
            <table class=\"tb\">
                <tr>
                    <th>User:</th>
                    <td>
                        <select name=\"warning_user_id\">";

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
    $warningCounts = $userWarningRepository->getWarningCountsByUser();
    if (count($warningCounts) > 0) {
        foreach ($warningCounts as $warningCount) {
            echo "<div style=\"padding:5px;border-bottom:1px solid #fff\">
                <b>" . $warningCount['nick'] . "</b> &nbsp;
                [<a href=\"#\" onclick=\"toggleBox('w" . $warningCount['userId'] . "')\">" . nf($warningCount['count']) . " Verwarnungen</a>] &nbsp;
                [<a href=\"?page=user&amp;sub=edit&amp;id=" . $warningCount['userId'] . "\">Daten</a>] &nbsp;
                <table id=\"w" . $warningCount['userId'] . "\" style=\"margin-top:10px;" . ((isset($_GET['user']) && $_GET['user'] == $warningCount['userId']) ? "" : "display:none;") . "\" class=\"tb\">
                    <tr>
                        <th style=\"\">Text</th>
                        <th style=\"width:130px;\">Datum</th>
                        <th style=\"width:100px;\">Verwarnt von</th>
                        <th style=\"width:150px;\">Optionen</th>
                    </tr>";
            $userWarnings = $userWarningRepository->getUserWarnings($warningCount['userId']);
            foreach ($userWarnings as $warning) {
                echo "<tr>
                                <td>" . stripslashes(nl2br($warning->text)) . "</td>
                                <td>" . df($warning->date) . "</td>
                                <td><b>" . $warning->adminNick . "</b></td>
                                <td>
                                    <a href=\"?page=$page&amp;sub=$sub&amp;edit=" . $warning->id . "\">Bearbeiten</a>
                                    <a href=\"?page=$page&amp;sub=$sub&amp;del=" . $warning->id . "\" onclick=\"return confirm('Verwarnung löschen?')\">Löschen</a>
                                </td>
                            </tr>";
            }

            echo "</table></div>";
        }
    } else {
        echo "<i>Keine Verwarnungen vorhanden!</i>";
    }
}
