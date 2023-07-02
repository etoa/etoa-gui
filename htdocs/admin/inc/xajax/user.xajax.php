<?PHP

use EtoA\Admin\AdminUserRepository;
use EtoA\Alliance\AllianceRankRepository;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\User\UserCommentRepository;
use EtoA\User\UserLogRepository;
use EtoA\User\UserPointsRepository;
use EtoA\User\UserService;

$xajax->register(XAJAX_FUNCTION, "showTimeBox");
$xajax->register(XAJAX_FUNCTION, "allianceRankSelector");
$xajax->register(XAJAX_FUNCTION, "userPointsTable");
$xajax->register(XAJAX_FUNCTION, "addUserComment");
$xajax->register(XAJAX_FUNCTION, "delUserComment");
$xajax->register(XAJAX_FUNCTION, "userLogs");
$xajax->register(XAJAX_FUNCTION, "addUserLog");
$xajax->register(XAJAX_FUNCTION, "userTickets");
$xajax->register(XAJAX_FUNCTION, "userComments");

function showTimeBox($parent, $name, $value, $show = 1)
{
    $or = new xajaxResponse();
    ob_start();
    if ($show > 0) {
        echo '<input type="datetime-local" value="' . date("Y-m-d\TH:i:s", intval($value)) . '" step="1" name="' . $name . '">';
    } else {
        echo "-";
    }
    $out = ob_get_contents();
    ob_end_clean();
    $or->assign($parent, "innerHTML", $out);
    return $or;
}

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

function userTickets($uid, $target)
{
    global $app;

    /** @var AdminUserRepository $adminUserRepo */
    $adminUserRepo = $app[AdminUserRepository::class];

    /** @var TicketRepository $ticketRepo */
    $ticketRepo = $app[TicketRepository::class];

    $or = new xajaxResponse();
    ob_start();
    echo "<table class=\"tb\">";

    $tickets = $ticketRepo->findBy(["user_id" => $uid]);
    if (count($tickets) > 0) {
        echo "<tr>
            <th>ID</th>
            <th>Datum</th>
            <th>Kategorie</th>
            <th>Status</th>
            <th>Admin</th>
        </tr>";
        foreach ($tickets as $ticket) {
            $adminNick = $adminUserRepo->getNick($ticket->adminId);
            echo "<tr>
                <td><a href=\"?page=tickets&id=" . $ticket->id . "\">" . $ticket->getIdString() . "</a></td>
                <td class=\"tbldata\">" . StringUtils::formatDate($ticket->timestamp) . "</td>
                <td class=\"tbldata\">" . $ticketRepo->getCategoryName($ticket->catId) . "</td>
                <td class=\"tbldata\">" . $ticket->getStatusName() . "</td>
                <td class=\"tbldata\">" . $adminNick . "</td>
            </tr>";
        }
    } else {
        echo "<tr><td class=\"tbldata\">Keine Tickets</td></tr>";
    }
    echo "</table>";

    $out = ob_get_contents();
    ob_end_clean();
    $or->assign($target, "innerHTML", $out);
    return $or;
}

function userComments($uid, $target)
{
    global $app;

    /** @var UserCommentRepository $userCommentRepository */
    $userCommentRepository = $app[UserCommentRepository::class];

    $or = new xajaxResponse();
    ob_start();
    echo "<h2>Neuer Kommentar:</h2><textarea rows=\"4\" cols=\"70\" id=\"new_comment_text\"></textarea><br/><br/>";
    echo "<input type=\"button\" onclick=\"xajax_addUserComment('$uid','$target',document.getElementById('new_comment_text').value);\" value=\"Speichern\" />";
    echo "<h2>Gespeicherte Kommentare</h2><table class=\"tb\">";

    $comments = $userCommentRepository->getComments($uid);
    if (count($comments) > 0) {
        echo "<tr>
            <th>Text</th>
            <th>Verfasst</th>
            <th>Aktionen</th>
        </tr>";
        foreach ($comments as $comment) {
            echo "<tr>
                <td class=\"tbldata\" >" . BBCodeUtils::toHTML($comment->text) . "</td>
                <td class=\"tbldata\" style=\"width:200px;\">" . StringUtils::formatDate($comment->timestamp) . " von " . $comment->adminNick . "</td>
                <td class=\"tbldata\" style=\"width:50px;\"><a href=\"javascript:;\" onclick=\"if (confirm('Wirklich löschen?')) {xajax_delUserComment('" . $uid . "','" . $target . "'," . $comment->id . ")}\">Löschen</a></td>
            </tr>";
        }
    } else {
        echo "<tr><td class=\"tbldata\">Keine Kommentare</td></tr>";
    }
    echo "</table></div>";

    $out = ob_get_contents();
    ob_end_clean();
    $or->assign($target, "innerHTML", $out);
    return $or;
}

function addUserComment($uid, $target, $text)
{
    global $app;

    /** @var UserCommentRepository $userCommentRepository */
    $userCommentRepository = $app[UserCommentRepository::class];

    $or = new xajaxResponse();
    if ($text != "") {
        $or->script("showLoader('$target');");
        $userCommentRepository->addComment($uid, $_SESSION['user_id'], $text);
        $or->script("xajax_userComments('$uid','$target')");
    } else {
        $or->alert("Fehler! Kein Text!");
    }
    return $or;
}

function delUserComment($uid, $target, $id)
{
    global $app;

    /** @var UserCommentRepository $userCommentRepository */
    $userCommentRepository = $app[UserCommentRepository::class];

    $or = new xajaxResponse();
    if ($id > 0) {
        $or->script("showLoader('$target');");
        $userCommentRepository->deleteComment($id);
        $or->script("xajax_userComments('$uid','$target')");
    } else {
        $or->alert("Fehler! Falsche ID!");
    }
    return $or;
}


function userLogs($uid, $target)
{
    global $app;

    /** @var UserLogRepository $userLogRepository */
    $userLogRepository = $app[UserLogRepository::class];

    $or = new xajaxResponse();
    ob_start();
    tableStart("", '100%');
    echo "<tr><th>Nachricht</th><th>Datum</th><th>IP</th></tr>";
    $logs = $userLogRepository->getUserLogs($uid, 100);
    foreach ($logs as $log) {
        echo "<tr><td>" . BBCodeUtils::toHTML($log->message) . "</td>
                        <td>" . StringUtils::formatDate($log->timestamp) . "</td>
                        <td><a href=\"?page=user&amp;sub=ipsearch&amp;ip=" . $log->host . "\">" . $log->host . "</a></td></tr>";
    }
    tableEnd();

    echo "<h2>Neuer Log:</h2><textarea rows=\"4\" cols=\"70\" id=\"new_log\"></textarea><br/><br/>";

    echo "<input type=\"button\" onclick=\"xajax_addUserLog('$uid','$target',document.getElementById('new_log').value);\" value=\"Speichern\" />";


    $out = ob_get_contents();
    ob_end_clean();
    $or->assign($target, "innerHTML", $out);
    return $or;
}

function addUserLog($uid, $target, $text)
{
    // TODO
    global $app;

    /** @var UserService $userService */
    $userService = $app[UserService::class];

    $or = new xajaxResponse();
    if ($text != "") {
        $or->script("showLoader('$target');");
        $userService->addToUserLog($uid, "settings", $text, true);
        $or->script("xajax_userLogs('$uid','$target')");
    } else {
        $or->alert("Fehler! Kein Text!");
    }
    return $or;
}
