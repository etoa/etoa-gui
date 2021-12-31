<?PHP

use EtoA\Admin\AdminUser;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use EtoA\Message\ReportRepository;
use EtoA\Message\ReportSearch;
use EtoA\Message\ReportTypes;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\Mail\MailSenderService;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;

define("USER_MESSAGE_CAT_ID", 1);
define("SYS_MESSAGE_CAT_ID", 5);

define('MESSAGE_TYPE_IN_GAME', 0);
define('MESSAGE_TYPE_EMAIL', 1);
define('MESSAGE_TYPE_BOTH', 2);

define('RECIPIENT_TYPE_SINGLE', 0);
define('RECIPIENT_TYPE_ALL', 1);

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

/** @var MessageRepository $messageRepository */
$messageRepository = $app[MessageRepository::class];

/** @var MessageCategoryRepository $messageCategoryRepository */
$messageCategoryRepository = $app[MessageCategoryRepository::class];

/** @var ReportRepository $reportRepository */
$reportRepository = $app[ReportRepository::class];

/** @var MailSenderService $mailSenderService */
$mailSenderService = $app[MailSenderService::class];

/** @var Request */
$request = Request::createFromGlobals();

if ($sub == "sendmsg") {
    sendUserMessageForm($request, $cu, $messageRepository, $userRepository, $mailSenderService);
} elseif ($sub == "reports") {
    manageReports($request, $reportRepository, $userRepository);
}

function sendUserMessageForm(
    Request $request,
    AdminUser $cu,
    MessageRepository $messageRepository,
    UserRepository $userRepository,
    MailSenderService $mailSenderService
): void {
    global $page;
    global $sub;

    echo "<h1>Nachrichten</h1>";

    echo "Nachricht an einen Spieler senden:<br/><br/>";

    $subj = $request->query->get('message_subject', '');
    $text = "";
    if ($request->request->has('submit')) {
        if ($request->request->get('message_subject') != "" && $request->request->get('message_text') != "") {
            $msg_type = $request->request->getInt('msg_type');

            $msgCnt = 0;
            if (in_array($msg_type, [MESSAGE_TYPE_IN_GAME, MESSAGE_TYPE_BOTH], true)) {
                if ($request->request->getInt('rcpt_type') === RECIPIENT_TYPE_ALL) {
                    $userIds = array_keys($userRepository->searchUserNicknames());
                } else {
                    $userIds = [$request->request->getInt('message_user_to')];
                }
                foreach ($userIds as $userId) {
                    $messageRepository->sendFromUserToUser(
                        $request->request->getInt('from_id'),
                        $userId,
                        $request->request->get('message_subject'),
                        $request->request->get('message_text')
                    );
                    $msgCnt++;
                }
            }
            if ($msgCnt > 0) {
                success_msg("$msgCnt InGame-Nachrichten wurden versendet!");
            }

            $mailCnt = 0;
            if (in_array($msg_type, [MESSAGE_TYPE_EMAIL, MESSAGE_TYPE_BOTH], true)) {
                if ($request->request->getInt('rcpt_type') === RECIPIENT_TYPE_ALL) {
                    $recipients = $userRepository->getEmailAddressesWithNickname();
                } else {
                    $recipient = $userRepository->getUser($request->request->getInt('message_user_to'));
                    $recipients = [$recipient->email => $recipient->nick];
                }

                if ($request->request->getInt('from_id') > 0) {
                    $replyUser = $userRepository->getUser($cu->playerId);
                    $replyTo = [$replyUser->email => $replyUser->nick];
                } else {
                    $replyTo = null;
                }

                $mailSenderService->send(
                    $request->request->get('message_subject'),
                    $request->request->get('message_text'),
                    $recipients,
                    $replyTo
                );
                $mailCnt++;
            }
            if ($mailCnt > 0) {
                success_msg("$mailCnt Mails wurden versendet!");
            }
        } else {
            echo MessageBox::error("", "Nachricht konnte nicht gesendet werden! Text oder Titel fehlt!");
        }
        $subj = $request->request->get('message_subject');
        $text = $request->request->get('message_text');
    }

    echo "<form action=\"?page=$page&sub=$sub\" method=\"POST\">";
    echo "<table width=\"300\" class=\"tb\">";
    echo "<tr>
        <th width=\"50\">Sender:</th>
        <td>";
    $playerUser = $userRepository->getUser($cu->playerId);
    if ($playerUser !== null) {
        echo "<input type=\"radio\" name=\"from_id\" id=\"from_id_1\" value=\"" . $cu->playerId . "\" checked=\"checked\" /> <label for=\"from_id_1\">" . $playerUser->nick . " (InGame-Account #" . $playerUser->id . ")</label><br/>";
        echo "<input type=\"radio\" name=\"from_id\" id=\"from_id_0\" value=\"0\" /> <label for=\"from_id_0\">System</label><br/>";
    } else {
        echo "System <input type=\"hidden\" name=\"from_id\" value=\"0\" />";
    }
    echo "</td></tr>";
    echo "<tr>
        <th>Empfänger:</th>
        <td class=\"tbldata\" width=\"250\">
        <b>An:</b>
        <input type=\"radio\" name=\"rcpt_type\" id=\"rcpt_type_1\" value=\"" . RECIPIENT_TYPE_ALL . "\"  checked=\"checked\"  onclick=\"document.getElementById('message_user_to').style.display='none';\" /> <label for=\"rcpt_type_1\">Alle Spieler</label>
        <input type=\"radio\" name=\"rcpt_type\" id=\"rcpt_type_0\" value=\"" . RECIPIENT_TYPE_SINGLE . "\"  onclick=\"document.getElementById('message_user_to').style.display='';\" /> <label for=\"rcpt_type_0\">Einzelner Empfänger</label>
        <select name=\"message_user_to\" id=\"message_user_to\" style=\"display:none\">";
    $userNicks = $userRepository->searchUserNicknames();
    foreach ($userNicks as $userId => $userNick) {
        echo "<option value=\"" . $userId . "\"";
        echo ">" . $userNick . "</option>";
    }
    echo "</select> &nbsp;

    <br/>
    <b>Typ:</b>
    <input type=\"radio\" name=\"msg_type\" value=\"" . MESSAGE_TYPE_IN_GAME . "\" id=\"msg_type_0\"  checked=\"checked\" /> <label for=\"msg_type_0\">InGame-Nachricht</label>
    <input type=\"radio\" name=\"msg_type\" value=\"" . MESSAGE_TYPE_EMAIL . "\" id=\"msg_type_1\" /> <label for=\"msg_type_1\">E-Mail</label>
    <input type=\"radio\" name=\"msg_type\" value=\"" . MESSAGE_TYPE_BOTH . "\" id=\"msg_type_2\" /> <label for=\"msg_type_2\">InGame-Nachricht &amp; E-Mail</label>
    </td></tr>";
    echo "<tr>
        <th>Betreff:</th>
        <td><input type=\"text\" name=\"message_subject\" value=\"" . $subj . "\" size=\"60\" maxlength=\"255\"></td></tr>";
    echo "<tr>
        <th>Text:</th>
        <td><textarea name=\"message_text\" rows=\"10\" cols=\"60\">" . $text . "</textarea></td></tr>";
    echo "</table>";
    echo "<p align=\"center\"><input type=\"submit\" class=\"button\" name=\"submit\" value=\"Senden\"></p>";
    echo "</form>";
}

function manageReports(Request $request, ReportRepository $reportRepository, UserRepository $userRepository): void
{
    global $page;
    global $sub;

    echo "<h1>Berichte</h1>";
    //
    // Suchresultate
    //
    if ($request->request->has('user_search') && $request->request->get('user_search') != "" || $request->query->get('action') == "searchresults") {
        $search = ReportSearch::create();
        if ($request->request->getInt('user_id') > 0) {
            $search->userId($request->request->getInt('user_id'));
        } elseif ($request->request->get('user_nick') != "") {
            $uid = $userRepository->getUserIdByNick($request->request->get('user_nick'));
            if ($uid !== null) {
                $search->userId($uid);
            }
        }

        if ($request->request->getInt('opponent1_id') > 0) {
            $search->opponentId($request->request->getInt('opponent1_id'));
        } elseif ($request->request->get('opponent1_nick') != "") {
            $uid = $userRepository->getUserIdByNick($request->request->get('opponent1_nick'));
            if ($uid !== null) {
                $search->opponentId($uid);
            }
        }

        if ($request->request->getInt('read') == 1) {
            $search->read(true);
        } elseif ($request->request->getInt('read') == 0) {
            $search->read(false);
        }
        if ($request->request->getInt('deleted') == 1) {
            $search->deleted(true);
        } else if ($request->request->getInt('deleted') == 0) {
            $search->deleted(false);
        }

        if ($request->request->get('type') != "") {
            $search->type($request->request->get('type'));
        }

        if ($request->request->get('date_from') != "") {
            if ($ts = strtotime($request->request->get('date_from'))) {
                $search->dateFrom($ts);
            } else {
                echo "Ungültiges Datum";
            }
        }

        if ($request->request->get('date_to') != "") {
            if ($ts = strtotime($request->request->get('date_to'))) {
                $search->dateTo($ts);
            } else {
                echo "Ungültiges Datum";
            }
        }

        if ($request->request->getInt('entity1_id') > 0) {
            $search->entityId($request->request->getInt('entity1_id'), 1);
        }
        if ($request->request->getInt('entity2_id') > 0) {
            $search->entityId($request->request->getInt('entity2_id'), 2);
        }

        //LIMIT
        if ($request->request->getInt('report_limit') != "") {
            $limit = $request->request->getInt('report_limit');
        } else {
            $limit = 1;
        }

        $reports = Report::find($search, $limit);

        $cnt = count($reports);
        echo $cnt . " Datensätze vorhanden<br/><br/>";
        if ($cnt > 0) {
            if ($cnt > 20) {
                echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Neue Suche\" /><br/><br/>";
            }

            echo "<b>Legende:</b> <span style=\"color:#0f0;\">Ungelesen</span>, <span style=\"color:#f90;\">Gelöscht</span>, <span style=\"font-style:italic;\">Archiviert</span><br/><br/>";

            echo "<table class=\"tb\">";
            echo "<tr>";
            echo "<th>Datum</th>";
            echo "<th>Kategorie</th>";
            echo "<th>Empfänger</th>";
            echo "<th>Betreff</th>";
            echo "</tr>";
            foreach ($reports as $rid => $r) {
                if ($request->request->get('type') == 'battle' && $request->request->getInt("entity_ships") == 1) {
                    if ($r->entityShips == "" || $r->entityShips == 0)
                        continue;
                }

                $recipient = $r->userId > 0
                    ? $userRepository->getNick($r->userId)
                    : "<i>System</i>";

                if ($r->deleted == 1)
                    $style = "color:#f90;";
                elseif ($r->read == 0)
                    $style = "color:#0f0;";
                elseif ($r->archived == 1)
                    $style = "font-style:italic;";
                else
                    $style = "";
                echo "<tr>";
                echo "<td style=\"$style;width:110px;\">" . date("Y-d-m H:i", $r->timestamp) . "</td>";
                echo "<td style=\"$style\">" . ReportTypes::TYPES[$r->type] . "</td>";
                echo "<td style=\"$style\">" . StringUtils::cutString($recipient, 11) . "</td>";
                echo "<td><div id=\"r_s_" . $rid . "\" style=\"" . $style . "cursor:pointer;\" onclick=\"$('#r_l_" . $rid . "').toggle();\">" . StringUtils::cutString($r->subject, 50) . "</div><div id=\"r_l_" . $rid . "\" style=\"display:none;\"><br/>" . $r . "</div></td>";
                echo "</tr>";
            }
            echo "</table><br/>";
            echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Neue Suche\" />";
        } else {
            echo "Die Suche lieferte keine Resultate!<br/><br/>";
            echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Zur&uuml;ck\" /><br/><br/>";
        }
    } else {
        unset($_SESSION['admin.messages.search']);

        echo "Suchmaske:<br/><br/>";
        echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
        tableStart("", 'auto');
        echo "		<tr>
                        <th>Empfänger-ID</th>
                        <td>
                            <input type=\"text\" name=\"user_id\" value=\"\" size=\"4\" maxlength=\"250\" />
                        </td>
                    </tr>
                    <tr>
                        <th>Empfänger-Nick</th>
                        <td>
                            <input type=\"text\" name=\"user_nick\" id=\"user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'message_user_to_nick','citybox1');\" />
                            <br />
                            <div class=\"citybox\" id=\"citybox1\">&nbsp;</div>
                        </td>
                    </tr>
                    <tr>
                        <th>Gegespieler-ID</th>
                        <td>
                            <input type=\"text\" name=\"opponent1_id\" value=\"\" size=\"4\" maxlength=\"250\" />
                        </td>
                    </tr>
                    <tr>
                        <th>Gegespieler-Nick</th>
                        <td>
                            <input type=\"text\" name=\"opponent1_nick\" id=\"opponent1_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,opponent1_nick,citybox1);\" />
                            <br />
                            <div class=\"citybox\" id=\"citybox1\">&nbsp;</div>
                        </td>
                    </tr>
                    <tr>
                        <th>Kategorie</th>
                            <td>
                                <select name=\"type\" onchange=\"xajax_showDetail(this.value);\" >
                                    <option value=\"\">(egal)</option>";
        foreach (ReportTypes::TYPES as $k => $v)
            echo "					<option value=\"" . $k . "\">" . $v . "</option>";

        echo "					</select>
                            </td>
                        </tr>

                        <tr>
                            <th style=\"width:130px;\">Entitiy-ID's</th>
                            <td>
                                <input type=\"text\" name=\"entity1_id\" value=\"\" size=\"4\" maxlength=\"250\" />&nbsp;
                                <input type=\"text\" name=\"entity2_id\" value=\"\" size=\"4\" maxlength=\"250\" />
                            </td>
                        </tr>
                        <tr>
                            <th>Gelesen</th>
                            <td>
                                <input type=\"radio\" name=\"read\" value=\"2\" checked=\"checked\" /> Egal
                                <input type=\"radio\" name=\"read\" value=\"0\" /> Nein
                                <input type=\"radio\" name=\"read\" value=\"1\" /> Ja
                            </td>
                        </tr>
                        <tr>
                            <th>Gelöscht</th>
                            <td>
                                <input type=\"radio\" name=\"deleted\" value=\"2\" checked=\"checked\" /> Egal
                                <input type=\"radio\" name=\"deleted\" value=\"0\" /> Nein
                                <input type=\"radio\" name=\"deleted\" value=\"1\" /> Ja
                            </td>
                        </tr>
                        <tr>
                            <th>Schiffe auf Ziel</th>
                            <td>
                                <input type=\"radio\" name=\"entity_ships\" value=\"2\" checked=\"checked\" /> Egal
                                <input type=\"radio\" name=\"entity_ships\" value=\"0\" /> Nein
                                <input type=\"radio\" name=\"entity_ships\" value=\"1\" /> Ja
                            </td>
                        </tr>
                        <tr>
                            <th>Datum von</th>
                            <td>
                                <input type=\"text\" name=\"date_from\" id=\"date_from\" value=\"\" size=\"20\" maxlength=\"250\" />
                            </td>
                        </tr>
                        <tr>
                            <th>Datum bis</th>
                            <td>
                                <input type=\"text\" name=\"date_to\" id=\"date_to\" value=\"\" size=\"20\" maxlength=\"250\" />
                            </td>
                        </tr>
                        <tr>
                            <th>Anzahl Datensätze</th>
                            <td class=\"tbldata\">
                                <select name=\"report_limit\">";
        for ($x = 100; $x <= 2000; $x += 100)
            echo "					<option value=\"$x\">$x</option>";
        echo "					</select>
                            </td>
                        </tr>

                    </table>
                    <p><input type=\"submit\" class=\"button\" name=\"user_search\" value=\"Suche starten\" /></p>
                </form>";

        echo "<br/>Es sind " . StringUtils::formatNumber($reportRepository->count()) . " Einträge in der Datenbank vorhanden.";
    }
}
