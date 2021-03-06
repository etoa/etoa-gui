<?PHP

use EtoA\Admin\AdminUser;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use EtoA\Message\ReportRepository;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;

define("USER_MESSAGE_CAT_ID", 1);
define("SYS_MESSAGE_CAT_ID", 5);

define('MESSAGE_TYPE_IN_GAME', 0);
define('MESSAGE_TYPE_EMAIL', 1);
define('MESSAGE_TYPE_BOTH', 2);

define('RECIPIENT_TYPE_SINGLE', 0);
define('RECIPIENT_TYPE_ALL', 1);

/** @var UserRepository */
$userRepository = $app[UserRepository::class];

/** @var MessageRepository */
$messageRepository = $app[MessageRepository::class];

/** @var MessageCategoryRepository */
$messageCategoryRepository = $app[MessageCategoryRepository::class];

/** @var ReportRepository */
$reportRepository = $app[ReportRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

if ($sub == "sendmsg") {
    sendMessageForm($request, $cu, $messageRepository, $userRepository);
} elseif ($sub == "reports") {
    manageReports($request, $reportRepository, $userRepository);
} else {
    manageMessages($request, $messageRepository, $messageCategoryRepository, $userRepository);
}

function sendMessageForm(
    Request $request,
    AdminUser $cu,
    MessageRepository $messageRepository,
    UserRepository $userRepository
): void {
    global $page;
    global $sub;

    echo "<h1>Nachrichten</h1>";

    echo "Nachricht an einen Spieler senden:<br/><br/>";

    $subj = $request->query->get('message_subject', '');
    $text = "";
    if ($request->request->has('submit')) {
        if ($request->request->get('message_subject') != "" && $request->request->get('message_text') != "") {
            $to = [];
            if ($request->request->getInt('rcpt_type') === RECIPIENT_TYPE_ALL) {
                $to = $userRepository->getEmailAddressesWithDisplayName();
            } else {
                $userId = $request->request->getInt('message_user_to');
                $recipient = $userRepository->getUser($userId);
                $to[$userId] = $recipient->getEmailAddressWithDisplayName();
            }

            $msg_type = $request->request->getInt('msg_type');

            if (in_array($msg_type, [MESSAGE_TYPE_EMAIL, MESSAGE_TYPE_BOTH], true)) {
                $mail = new Mail($request->request->get('message_subject'), $request->request->get('message_text'));
                if ($request->request->getInt('from_id') > 0) {
                    $replyUser = $userRepository->getUser($cu->playerId);
                    $reply = $replyUser->getEmailAddressWithDisplayName();
                } else {
                    $reply = "";
                }
            }

            $mailCnt = 0;
            $msgCnt = 0;

            foreach ($to as $userId => $userEmail) {
                if (in_array($msg_type, [MESSAGE_TYPE_IN_GAME, MESSAGE_TYPE_BOTH], true)) {
                    $messageRepository->sendFromUserToUser(
                        $request->request->getInt('from_id'),
                        $userId,
                        $request->request->get('message_subject'),
                        $request->request->get('message_text')
                    );
                    $msgCnt++;
                }
                if (in_array($msg_type, [MESSAGE_TYPE_EMAIL, MESSAGE_TYPE_BOTH], true)) {
                    $mail->send($userEmail, $reply);
                    $mailCnt++;
                }
            }
            if ($msgCnt > 0) {
                success_msg("$msgCnt InGame-Nachrichten wurden versendet!");
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
    $userNicks = $userRepository->getUserNicknames();
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
        $sql = '';
        if ($request->request->getInt('user_id') > 0) {
            $sql .= " AND user_id='" . $request->request->getInt('user_id') . "' ";
        }
        if ($request->request->get('user_nick') != "") {
            $uid = $userRepository->getUserIdByNick($request->request->get('user_nick'));
            if ($uid !== null) {
                $sql .= " AND user_id='" . $uid . "' ";
            }
        }
        if ($request->request->getInt('opponent1_id') > 0) {
            $sql .= " AND opponent1_id='" . $request->request->getInt('opponent1_id') . "' ";
        }
        if ($request->request->get('opponent1_nick') != "") {
            $uid = $userRepository->getUserIdByNick($request->request->get('opponent1_nick'));
            if ($uid !== null) {
                $sql .= " AND opponent1_id='" . $uid . "' ";
            }
        }
        if ($request->request->has('subject') && $request->request->get('subject') != "") {
            if (stristr($request->request->get('qmode')['subject'], "%")) $addchars = "%";
            else $addchars = "";
            $sql .= " AND subject " . stripslashes($request->request->get('qmode')['subject']) . $request->request->get('subject') . "$addchars'";
        }

        if ($request->request->getInt('read') == 1) {
            $sql .= " AND (read=1)";
        } elseif ($request->request->getInt('read') == 0) {
            $sql .= " AND (read=0)";
        }
        if ($request->request->getInt('deleted') == 1) {
            $sql .= " AND (deleted=1)";
        } else if ($request->request->getInt('deleted') == 0) {
            $sql .= " AND (deleted=0)";
        }
        if ($request->request->get('type') != "")
            $sql .= " AND type='" . $request->request->get('type') . "' ";

        if ($request->request->get('date_from') != "") {
            if ($ts = strtotime($request->request->get('date_from'))) {
                $sql .= " AND (timestamp>" . $ts . ")";
            } else {
                echo "Ungültiges Datum";
            }
        }

        if ($request->request->get('date_to') != "") {
            if ($ts = strtotime($request->request->get('date_to'))) {
                $sql .= " AND (timestamp<" . $ts . ")";
            } else {
                echo "Ungültiges Datum";
            }
        }

        if ($request->request->getInt('entity1_id') > 0) {
            $sql .= " AND (entity1_id=" . $request->request->getInt('entity1_id') . " OR entity2_id=" . $request->request->getInt('entity1_id') . ") ";
        }
        if ($request->request->getInt('entity2_id') > 0) {
            $sql .= " AND (entity2_id=" . $request->request->getInt('entity2_id') . " OR entity1_id=" . $request->request->getInt('entity2_id') . ") ";
        }

        //data tables
        $join = '';
        if ($request->request->has('type') && $request->request->get('type') != "") {
            $join = " INNER JOIN `reports_" . $request->request->get('type') . "` AS rd ON reports.id=rd.id ";
        }

        if ($request->request->has('subtype') && $request->request->get('subtype') != "") {
            $sql .= " AND rd.subtype='" . $request->request->get('subtype') . "'";
        }

        //market
        if ($request->request->has('type') && $request->request->get('type') == 'market') {
            if ($request->request->getInt('fleet1_id') > 0) {
                $sql .= " AND (rd.fleet1_id=" . $request->request->getInt('fleet1_id') . " OR rd.fleet2_id=" . $request->request->getInt('fleet1_id') . ") ";
            }
            if ($request->request->getInt('fleet2_id') > 0) {
                $sql .= " AND (rd.fleet2_id=" . $request->request->getInt('fleet2_id') . " OR rd.fleet1_id=" . $request->request->get('fleet2_id') . ") ";
            }

            if ($request->request->getInt('ship_id') > 0) {
                $sql .= " AND rd.ship_id=" . $request->request->getInt('ship_id');
            }
            if ($request->request->get('ship_count') > 0) {
                $sql .= " AND rd.ship_count=" . $request->request->getInt('ship_count');
            }

            if ($request->request->has('sell_0') && $request->request->getInt('sell_0') == 1)
                $sql .= " AND rd.sell_0>'0'";
            if ($request->request->has('sell_1') && $request->request->getInt('sell_1') == 1)
                $sql .= " AND rd.sell_1>'0'";
            if ($request->request->has('sell_2') && $request->request->getInt('sell_2') == 1)
                $sql .= " AND rd.sell_2>'0'";
            if ($request->request->has('sell_3') && $request->request->getInt('sell_3') == 1)
                $sql .= " AND rd.sell_3>'0'";
            if ($request->request->has('sell_4') && $request->request->getInt('sell_4') == 1)
                $sql .= " AND rd.sell_4>'0'";

            if ($request->request->has('buy_0') && $request->request->getInt('buy_0') == 1)
                $sql .= " AND rd.buy_0>'0'";
            if ($request->request->has('buy_1') && $request->request->getInt('buy_1') == 1)
                $sql .= " AND rd.buy_1>'0'";
            if ($request->request->has('buy_2') && $request->request->getInt('buy_2') == 1)
                $sql .= " AND rd.buy_2>'0'";
            if ($request->request->has('buy_3') && $request->request->getInt('buy_3') == 1)
                $sql .= " AND rd.buy_3>'0'";
            if ($request->request->has('buy_4') && $request->request->getInt('buy_4') == 1)
                $sql .= " AND rd.buy_4>'0'";
        }

        //battle
        if ($request->request->has('type') && $request->request->get('type') == 'battle') {
            // TODO
            echo "TODO";
        }

        //other
        if ($request->request->has('type') && $request->request->get('type') == 'other') {
            if ($request->request->getInt('fleet1_id') > 0) {
                $sql .= " AND (rd.fleet1_id=" . $request->request->getInt('fleet1_id') . " OR rd.fleet2_id=" . $request->request->getInt('fleet1_id') . ") ";
            }

            if ($request->request->getInt('ship_id') > 0) {
                if ($request->request->getInt('ship_count') > 0) {
                    $sql .= " AND rd.ships LIKE '%" . $request->request->getInt('ship_id') . ":" . $request->request->getInt('ship_count') . ",%'";
                } else {
                    $sql .= " AND rd.ships LIKE '%" . $request->request->getInt('ship_id') . ":%'";
                }
            } elseif ($request->request->getInt("ship_count") != "") {
                $sql .= " AND rd.ships LIKE '%:" . $request->request->getInt('ship_count') . ",%'";
            }

            if ($request->request->has('res_0') && $request->request->getInt('res_0') == 1) {
                $sql .= " AND rd.res_0>'0'";
            }
            if ($request->request->has('res_1') && $request->request->getInt('res_1') == 1) {
                $sql .= " AND rd.res_1>'0'";
            }
            if ($request->request->has('res_2') && $request->request->getInt('res_2') == 1) {
                $sql .= " AND rd.res_2>'0'";
            }
            if ($request->request->has('res_3') && $request->request->getInt('res_3') == 1) {
                $sql .= " AND rd.res_3>'0'";
            }
            if ($request->request->has('res_4') && $request->request->getInt('res_4') == 1) {
                $sql .= " AND rd.res_4>'0'";
            }

            if ($request->request->get('status') != "") {
                $sql .= " AND rd.status='" . $request->request->get('status') . "'";
            }

            if ($request->request->get('action') != "") {
                $sql .= " AND rd.action='" . $request->request->get('action') . "'";
            }
        }

        //LIMIT
        if ($request->request->getInt('report_limit') != "") {
            $limit = $request->request->getInt('report_limit');
        } else {
            $limit = 1;
        }

        $reports = Report::find($sql, null, $limit, 0, true, $join);

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
            $types = Report::$types;
            foreach ($reports as $rid => $r) {
                if ($request->request->get('type') == 'battle' && $request->request->getInt("entity_ships") == 1) {
                    if ($r->entityShips == "" || $r->entityShips == 0)
                        continue;
                }

                $sql .= ($request->request->getInt('entity_ships') == 1) ? " AND rd.entity_ships != '' " : " AND rd.entity_ships='' ";

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
                echo "<td style=\"$style\">" . $types[$r->type] . "</td>";
                echo "<td style=\"$style\">" . cut_string($recipient, 11) . "</td>";
                echo "<td><div id=\"r_s_" . $rid . "\" style=\"" . $style . "cursor:pointer;\" onclick=\"$('#r_l_" . $rid . "').toggle();\">" . cut_string($r->subject, 50) . "</div><div id=\"r_l_" . $rid . "\" style=\"display:none;\"><br/>" . $r . "</div></td>";
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
        foreach (Report::$types as $k => $v)
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

        echo "<br/>Es sind " . nf($reportRepository->count()) . " Einträge in der Datenbank vorhanden.";
    }
}

function manageMessages(
    Request $request,
    MessageRepository $messageRepository,
    MessageCategoryRepository $messageCategoryRepository,
    UserRepository $userRepository
): void {
    global $page;

    echo "<h1>Nachrichten</h1>";
    //
    // Suchresultate
    //
    if ($request->request->get('user_search') != "" || $request->query->get('action') == "searchresults") {
        $params = $_SESSION['admin.messages.search'] ?? getMessageParamsFromRequest($request);
        $limit = $request->request->getInt('message_limit');

        $messages = $messageRepository->findBy($params, $limit);
        if (count($messages) > 0) {
            $_SESSION['admin.messages.search'] = $params;

            echo count($messages) . " Datensätze vorhanden<br/><br/>";
            if (count($messages) > 20) {
                echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /><br/><br/>";
            }

            echo "<b>Legende:</b> <span style=\"color:#0f0;\">Ungelesen</span>, <span style=\"color:#f90;\">Gelöscht</span>, <span style=\"font-style:italic;\">Archiviert</span><br/><br/>";

            $categories = $messageCategoryRepository->getNames();

            echo "<table class=\"tb\">";
            echo "<tr>";
            echo "<th>Sender</th>";
            echo "<th>Empfänger</th>";
            echo "<th>Betreff</th>";
            echo "<th>Datum</th>";
            echo "<th>Kategorie</th>";
            echo "<th>Aktion</th>";
            echo "</tr>";
            foreach ($messages as $message) {
                $sender = $message->userFrom > 0
                    ? $userRepository->getNick($message->userFrom)
                    : "<i>System</i>";

                $recipient = $message->userTo > 0
                    ? $userRepository->getNick($message->userTo)
                    : "<i>System</i>";

                if ($message->deleted) {
                    $style = "style=\"color:#f90\"";
                } elseif (!$message->read) {
                    $style = "style=\"color:#0f0\"";
                } elseif ($message->archived) {
                    $style = "style=\"font-style:italic;\"";
                } else {
                    $style = "";
                }
                echo "<tr>";
                echo "<td $style>" . cut_string($sender, 11) . "</a></td>";
                echo "<td $style>" . cut_string($recipient, 11) . "</a></td>";
                echo "<td $style " . mTT($message->subject, text2html(substr($message->text, 0, 500))) . ">" . cut_string($message->subject, 20) . "</a></td>";
                echo "<td $style>" . date("Y-d-m H:i", $message->timestamp) . "</a></td>";
                echo "<td $style>" . ($categories[$message->catId] ?? '-') . "</td>";
                echo "<td>" . edit_button("?page=$page&sub=edit&message_id=" . $message->id) . " ";
                echo del_button("?page=$page&sub=trash&message_id=" . $message->id) . "</td>";
                echo "</tr>";
            }
            echo "</table><br/>";
            echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" />";
        } else {
            echo "Die Suche lieferte keine Resultate!<br/><br/>";
            echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Zur&uuml;ck\" /><br/><br/>";
        }
    } elseif ($request->query->get('sub') == "edit") {
        if ($request->request->has('msg_edit')) {
            $messageRepository->setDeleted(
                $request->query->getInt('message_id'),
                $request->request->getBoolean('check')
            );
        }

        $message = $messageRepository->find($request->query->getInt('message_id'));
        $sender = $message->userFrom > 0 ? $userRepository->getNick($message->userFrom) : "<i>System</i>";
        $recipient = $message->userTo > 0 ? $userRepository->getNick($message->userTo) : "<i>System</i>";

        echo "<form action=\"?page=$page&sub=edit&message_id=" . $request->query->getInt('message_id') . "\" method=\"post\">";
        echo "<table class=\"tbl\">";
        echo "<tr><td class=\"tbltitle\" valign=\"top\">ID</td><td class=\"tbldata\">" . $message->id . "</td></tr>";
        echo "<tr><td class=\"tbltitle\" valign=\"top\">Sender</td><td class=\"tbldata\">$sender</td></tr>";
        echo "<tr><td class=\"tbltitle\" valign=\"top\">Empfänger</td><td class=\"tbldata\">$recipient</td></tr>";
        echo "<tr><td class=\"tbltitle\" valign=\"top\">Datum</td><td class=\"tbldata\">" . date("Y-m-d H:i:s", $message->timestamp) . "</td></tr>";
        echo "<tr><td class=\"tbltitle\" valign=\"top\">Betreff</td><td class=\"tbldata\">" . text2html($message->subject) . "</td></tr>";
        echo "<tr><td class=\"tbltitle\" valign=\"top\">Text</td><td class=\"tbldata\">" . text2html($message->text) . "</td></tr>";
        echo "<tr><td class=\"tbltitle\" valign=\"top\">Quelltext</td>
        <td class=\"tbldata\"><textarea rows=\"20\" cols=\"80\" readonly=\"readonly\">" . $message->text . "</textarea></td></tr>";
        echo "<tr><td class=\"tbltitle\" valign=\"top\">Gelesen?</td><td class=\"tbldata\">";
        echo $message->read ? "Ja" : "Nein";
        echo "</td></tr>";
        echo "<tr><td class=\"tbltitle\" valign=\"top\">Gelöscht?</td><td class=\"tbldata\">";
        $checked = $message->deleted ? 'checked' : '';
        echo '<input type="checkbox" name="check" ' . $checked . '>';
        echo " <input type=\"submit\" name=\"msg_edit\" value=\"Speichern\" />";
        echo "</td></tr>";
        echo "<tr><td class=\"tbltitle\" valign=\"top\">Rundmail?</td><td class=\"tbldata\">";
        echo $message->massMail ? "Ja" : "Nein";
        echo "</td></tr>";

        echo "</table><br/><input type=\"button\" onclick=\"document.location='?page=$page&amp;action=searchresults'\" value=\"Zur&uuml;ck zu den Suchergebnissen\" /> &nbsp;
        <input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" />";
        echo "</form>";
    } elseif ($request->query->get('sub') == "trash") {
        $messageRepository->setRead($request->query->getInt('message_id'));
        $messageRepository->setDeleted($request->query->getInt('message_id'));
        forward('?page=' . $page . '&action=searchresults');
    } else {
        unset($_SESSION['admin.messages.search']);

        echo "Suchmaske:<br/><br/>";
        echo "<form action=\"?page=$page\" method=\"post\">";
        echo "<table class=\"tb\">";
        echo "<tr><th style=\"width:130px;\">Sender-ID</th>
            <td><input type=\"text\" name=\"message_user_from_id\" value=\"\" size=\"4\" maxlength=\"250\" /></td>";
        echo "<tr><th>Sender-Nick</th>
            <td><input type=\"text\" name=\"message_user_from_nick\" id=\"message_user_from_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'message_user_from_nick','citybox');\" /><br><div class=\"citybox\" id=\"citybox\">&nbsp;</div></td>";
        echo "<tr><th>Empfänger-ID</th>
            <td><input type=\"text\" name=\"message_user_to_id\" value=\"\" size=\"4\" maxlength=\"250\" /></td>";
        echo "<tr><th>Empfänger-Nick</th>
            <td><input type=\"text\" name=\"message_user_to_nick\" id=\"message_user_to_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchUser(this.value,'message_user_to_nick','citybox1');\" /><br><div class=\"citybox\" id=\"citybox1\">&nbsp;</div></td>";
        echo "<tr><th>Betreff enthält</th>
            <td><input type=\"text\" name=\"message_subject\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
        echo "<tr><th>Text enthält</th>
            <td><input type=\"text\" name=\"message_text\" value=\"\" size=\"20\" maxlength=\"250\" /></td></tr>";
        echo "<tr><th style=\"width:130px;\">Flotten-ID</th>
            <td><input type=\"text\" name=\"message_fleet_id\" value=\"\" size=\"4\" maxlength=\"250\" /></td>";
        echo "<tr><th style=\"width:130px;\">Entitiy-ID</th>
            <td><input type=\"text\" name=\"message_entity_id\" value=\"\" size=\"4\" maxlength=\"250\" /></td>";
        echo "<tr><th>Gelesen</th>
            <td><input type=\"radio\" name=\"message_read\" value=\"2\" checked=\"checked\" /> Egal
                <input type=\"radio\" name=\"message_read\" value=\"0\" /> Nein
                <input type=\"radio\" name=\"message_read\" value=\"1\" /> Ja</td></tr>";
        echo "<tr><th>Rundmail</th>
            <td><input type=\"radio\" name=\"message_massmail\" value=\"2\" checked=\"checked\" /> Egal
                <input type=\"radio\" name=\"message_massmail\" value=\"0\" /> Nein
                <input type=\"radio\" name=\"message_massmail\" value=\"1\" /> Ja</td></tr>";
        echo "<tr><th>Gelöscht</th>
            <td><input type=\"radio\" name=\"message_deleted\" value=\"2\" checked=\"checked\" /> Egal
                <input type=\"radio\" name=\"message_deleted\" value=\"0\" /> Nein
                <input type=\"radio\" name=\"message_deleted\" value=\"1\" /> Ja</td></tr>";
        echo "<tr><th>Kategorie</th><td><select name=\"message_cat_id\">";
        echo "<option value=\"\">(egal)</option>";
        $categories = $messageCategoryRepository->getNames();
        foreach ($categories as $categoryId => $categoryName) {
            echo "<option value=\"" . $categoryId . "\">" . $categoryName . "</option>";
        }
        echo "</select></tr>";
        echo "<tr><th>Anzahl Datensätze</th><td class=\"tbldata\"><select name=\"message_limit\">";
        for ($x = 100; $x <= 2000; $x += 100) {
            echo "<option value=\"$x\">$x</option>";
        }
        echo "</select></td></tr>";

        echo "</table>";
        echo "<br/><input type=\"submit\" class=\"button\" name=\"user_search\" value=\"Suche starten\" /></form>";

        echo "<br/>Es sind " . nf($messageRepository->count()) . " Einträge in der Datenbank vorhanden.";
    }
}

function getMessageParamsFromRequest(Request $request): array
{
    $params = [];

    if ($request->request->getInt('message_user_from_id') > 0) {
        $params['user_from_id'] = $request->request->getInt('message_user_from_id');
    }
    if (filled($request->request->get('message_user_from_nick'))) {
        $params['user_from_nick'] = $request->request->get('message_user_from_nick');
    }
    if ($request->request->getInt('message_user_to_id') > 0) {
        $params['user_to_id'] = $request->request->getInt('message_user_to_id');
    }
    if (filled($request->request->get('message_user_to_nick'))) {
        $params['user_to_nick'] = $request->request->get('message_user_to_nick');
    }
    if (filled($request->request->get('message_subject'))) {
        $params['subject'] = $request->request->get('message_subject');
    }
    if (filled($request->request->get('message_text'))) {
        $params['text'] = $request->request->get('message_text');
    }
    if ($request->request->getInt('message_fleet_id') > 0) {
        $params['fleet_id'] = $request->request->getInt('message_fleet_id');
    }
    if ($request->request->getInt('message_entity_id') > 0) {
        $params['entity_id'] = $request->request->getInt('message_entity_id');
    }
    if ($request->request->getInt('message_cat_id') > 0) {
        $params['cat_id'] = $request->request->getInt('message_cat_id');
    }
    if ($request->request->getInt('message_read') < 2) {
        $params['read'] = $request->request->getInt('message_read') === 1;
    }
    if ($request->request->getInt('message_massmail') < 2) {
        $params['massmail'] = $request->request->getInt('message_massmail') === 1;
    }
    if ($request->request->getInt('message_deleted') < 2) {
        $params['deleted'] = $request->request->getInt('message_deleted') === 1;
    }

    return $params;
}
