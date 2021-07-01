<?php

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Message\MessageCategory;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

/** @var MessageRepository */
$messageRepository = $app[MessageRepository::class];

/** @var MessageCategoryRepository */
$messageCategoryRepository = $app[MessageCategoryRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

/** @var UserRepository */
$userRepository = $app[UserRepository::class];

$mode = $request->query->get('mode', '') != '' && ctype_alpha($request->query->get('mode'))
    ? $request->query->get('mode')
    : 'inbox';

?>
<script type="text/javascript">
    function selectNewMessages() {
        if (document.getElementById("select_new_messages").innerHTML == "Nur neue Nachrichten anzeigen") {
            document.getElementById("select_new_messages").innerHTML = "Alle Nachrichten anzeigen";

            // Geht jede einzelne Nachricht durch
            for (x = 0; x <= document.getElementById("msg_cnt").value; x++) {
                document.getElementById('msg_id_' + x).style.display = 'none';
            }
        } else {
            document.getElementById("select_new_messages").innerHTML = "Nur neue Nachrichten anzeigen";

            // Geht jede einzelne Nachricht durch
            for (x = 0; x <= document.getElementById("msg_cnt").value; x++) {
                document.getElementById('msg_id_' + x).style.display = '';
            }
        }
    }
</script>
<?php

echo '<h1>Nachrichten</h1>';
echo '<br style="clear:both;" />';

// Menü

show_tab_menu("mode", [
    "inbox" => "Posteingang",
    "new" => "Erstellen",
    "archiv" => "Archiv",
    "sent" => "Gesendet",
    "deleted" => "Papierkorb",
    "ignore" => "Ignorierliste"
]);
echo "<br/>";

if ($mode == "new") {
    require('content/messages/new.php');
} elseif ($mode == "ignore") {
    require('content/messages/ignore.php');
} elseif ($mode == "deleted") {
    require('content/messages/deleted.php');
} elseif ($mode == "sent") {
    require('content/messages/sent.php');
} elseif ($request->query->getInt('msg_id') > 0) {
    viewSingleMessage($request, $messageRepository, $messageCategoryRepository, $userRepository, $cu);
} else {
    listMessagesOverview($request, $messageRepository, $messageCategoryRepository, $userRepository, $cu, $config);
}

function viewSingleMessage(
    Request $request,
    MessageRepository $messageRepository,
    MessageCategoryRepository $messageCategoryRepository,
    UserRepository $userRepository,
    CurrentUser $cu
): void {
    global $page;
    global $mode;

    $messages = $messageRepository->findBy([
        'id' => $request->query->getInt('msg_id'),
        'user_to_id' => $cu->id,
        'deleted' => false
    ]);
    if (count($messages) > 0) {
        $message = $messages[0];

        // Sender
        $sender = $message->userFrom > 0
            ? ($userRepository->getNick($message->userFrom) ?? '<i>Unbekannt</i>')
            : '<i>' . $messageCategoryRepository->getSender($message->catId) . '</i>';

        // Title
        $subj = filled($message->subject)
            ? htmlentities($message->subject, ENT_QUOTES, 'UTF-8')
            : "<i>Kein Titel</i>";

        tableStart();
        echo "<tr><th colspan=\"2\">" . $subj . "</th></tr>";
        echo "<tr><th width=\"50\" valign=\"top\">Datum:</th>
        <td width=\"250\">" . df($message->timestamp) . "</td></tr>";
        echo "<tr><th width=\"50\" valign=\"top\">Sender:</th>
        <td width=\"250\">" . userPopUp($message->userFrom, $userRepository->getNick($message->userFrom), 0) . "</td></tr>";
        echo "<tr><td class=\"tbltitle\" width=\"50\" valign=\"top\">Text:<br/>";
        echo $request->query->has('src')
            ? '[<a href="?page=' . $page . '&mode=' . $mode . '&amp;msg_id=' . $message->id . '">Nachricht</a>]'
            : '[<a href="?page=' . $page . '&mode=' . $mode . '&amp;msg_id=' . $message->id . '&amp;src=1">Quelltext</a>]';
        echo "</td><td width=\"250\">";
        if (filled($message->text)) {
            echo $request->query->has('src')
                ? '<textarea rows="30" cols="60" readonly="readonly">' . htmlentities($message->text, ENT_QUOTES, 'UTF-8') . '</textarea>'
                : text2html(addslashes($message->text));
        } else {
            echo "<i>Kein Text</i>";
        }
        echo "</td></tr>";
        tableEnd();

        if (!$message->read) {
            $messageRepository->setRead($message->id);
        }

        echo "<form action=\"?page=$page&mode=new\" method=\"post\">";
        checker_init();

        echo "<input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=messages&mode=" . $mode . "'\"/>&nbsp;";
        echo "<input type=\"hidden\" name=\"message_id\" value=\"" . $message->id . "\" />";
        echo "<input type=\"hidden\" name=\"message_subject\" value=\"" . $message->subject . "\" />";
        echo "<input type=\"hidden\" name=\"message_sender\" value=\"" . $sender . "\" />";
        if ($cu->properties->msgCopy) {
            // Muss mit echo 'text'; erfolgen, da sonst der Text beim ersten " - Zeichen abgeschnitten wird!
            // Allerdings ist so das selbe Problem mit den ' - Zeichen!
            echo '<input type=\'hidden\' name=\'message_text\' value=\'' . htmlentities($message->text, ENT_QUOTES, 'UTF-8') . '\' />';
        }
        echo "<input type=\"submit\" value=\"Weiterleiten\" name=\"remit\" />&nbsp;";
        if ($message->userFrom > 0) {
            echo "<input type=\"hidden\" name=\"message_user_to\" value=\"" . $message->userFrom . "\" />";
            echo "<input type=\"submit\" value=\"Antworten\" name=\"answer\" />&nbsp;";
            echo "<input type=\"button\" value=\"Absender ignorieren\" onclick=\"document.location='?page=" . $page . "&amp;mode=ignore&amp;add=" . $message->userFrom . "'\" />&nbsp;";
        }
        echo "<input type=\"button\" value=\"Löschen\" onclick=\"document.location='?page=$page&mode=mode&del=" . $message->id . "';\" />&nbsp;";
        if ($message->userFrom > 0) {
            ticket_button('1', "Beleidigung melden", $message->userFrom);
        } else {
            ticket_button('8', "Regelverstoss melden");
        }
        echo "</form>";
    } else {
        echo "<p align=\"center\" class=\"infomsg\">Diese Nachricht existiert nicht!</p>";
        echo "<p align=\"center\"><input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=messages&mode=" . $mode . "'\"></p>";
    }
}

function listMessagesOverview(
    Request $request,
    MessageRepository $messageRepository,
    MessageCategoryRepository $messageCategoryRepository,
    UserRepository $userRepository,
    CurrentUser $cu,
    ConfigurationService $config
): void {
    global $page;
    global $mode;

    // Einzelne Nachricht löschen
    if ($request->request->has('submitdelete') && checker_verify()) {
        if ($messageRepository->setDeleted($request->request->getInt('message_id'), true, $cu->id)) {
            success_msg("Nachricht wurde gelöscht!");
        } else {
            error_msg("Nachricht konnte nicht gelöscht werden!");
        }
    }
    if ($request->query->getInt('del') > 0) {
        if ($messageRepository->setDeleted($request->query->getInt('del'), true, $cu->id)) {
            success_msg("Nachricht wurde gelöscht!");
        } else {
            error_msg("Nachricht konnte nicht gelöscht werden!");
        }
    }

    // Selektiere löschen
    if ($request->request->has('submitdeleteselection')  && checker_verify()) {
        if (count($request->request->get('delmsg')) > 0) {
            foreach (array_keys($request->request->get('delmsg')) as $id) {
                $messageRepository->setDeleted((int) $id, true, $cu->id, $mode == "archiv");
            }
            success_msg("Nachricht(en) wurden gelöscht!");
        }
    }

    // Alle Nachrichten löschen
    elseif ($request->request->has('submitdeleteall') && checker_verify()) {
        $messageRepository->setDeletedForUser($cu->id, true, null, $mode == "archiv");
        success_msg("Alle Nachrichten wurden gelöscht!");
    }

    // Systemnachrichten löschen
    elseif ($request->request->has('submitdeletesys') && checker_verify()) {
        $messageRepository->setDeletedForUser($cu->id, true, 0, $mode == "archiv");
        success_msg("Alle Systemnachrichten wurden gelöscht!");
    } elseif ($request->request->has('submitarchiving')  && checker_verify()) {
        if (count($request->request->get('delmsg')) > 0) {
            $archiveSpace = $config->param1Int('msg_max_store') - $request->request->getInt('archived_msg_cnt');
            if (count($request->request->get('delmsg')) <= $archiveSpace) {
                foreach (array_keys($request->request->get('delmsg')) as $id) {
                    $messageRepository->setArchived((int) $id, true, $cu->id);
                }
                success_msg("Nachricht(en) wurden archiviert!");
            } else {
                error_msg("Zu wenig Platz im Archiv!");
            }
        }
    }

    $readMessagesCount = $messageRepository->countReadForUser($cu->id);
    $archivedMessagesCount = $messageRepository->countArchivedForUser($cu->id);

    // Rechnet %-Werte für tabelle (1/2)
    $percentRead = min(ceil($readMessagesCount / $config->getInt('msg_max_store') * 100), 100);
    $percentArchived = min(ceil($archivedMessagesCount / $config->param1Int('msg_max_store') * 100), 100);

    $r_color = ($percentRead >= 90) ? 'color:red;' : '';
    $a_color = ($percentArchived >= 90) ? 'color:red;' : '';

    // Archiv-Grafik
    tableStart("Nachrichten");
    echo "<tr>
        <th style=\"text-align:center;width:50%;" . $r_color . "\">
            Gelesen: " . $readMessagesCount . "/" . $config->getInt('msg_max_store') . " Nachrichten
            </th>
            <th style=\"text-align:center;width:50%;" . $a_color . "\">
            Archiviert: " . $archivedMessagesCount . "/" . $config->param1Int('msg_max_store') . " Nachrichten
            </th>
        </tr>";
    echo '<tr>
            <td style="padding:0px;height:10px;"><img src="images/poll3.jpg" style="height:10px;width:' . $percentRead . '%;" alt="poll" /></td>
            <td style="padding:0px;height:10px;"><img src="images/poll2.jpg" style="height:10px;width:' . $percentArchived . '%;" alt="poll" /></td>
        </tr>';

    // Wenn es neue Nachrichten hat, Button zum Selektieren anzeigen
    if ($messageRepository->countNewForUser($cu->id) > 0) {
        echo '<tr>
            <td style="text-align:center;" colspan="2">
            <a href="javascript:;" onclick="selectNewMessages();" id="select_new_messages" name="select_new_messages">Nur neue Nachrichten anzeigen</a>
            </td>
        </tr>';
    }
    tableEnd();

    $previewMessages = $cu->properties->msgPreview == 1;

    echo "<form action=\"?page=$page&amp;mode=" . $mode . "\" method=\"post\"><div>";
    checker_init();
    echo "<input type=\"hidden\" name=\"archived_msg_cnt\" value=\"" . $archivedMessagesCount . "\" />";

    // Nachrichten
    tableStart("Kategorien");
    $messageCount = 0;
    $messagesReadCount = 0;

    $categories = $messageCategoryRepository->findAll();

    $otherCategory = new MessageCategory();
    $otherCategory->id = 0;
    $otherCategory->name = 'Ohne Kategorie';
    $otherCategory->description = '';
    $otherCategory->sender = 'System';
    $categories[] = $otherCategory;

    foreach ($categories as $category) {
        $messages = $messageRepository->findBy([
            'user_to_id' => $cu->id,
            'cat_id' => $category->id,
            'deleted' => false,
            'archived' => $mode == "archiv",
        ]);

        if (count($messages) > 0) {
            echo "<tr>
                <th colspan=\"4\">" . text2html($category->name) . " (" . count($messages) . " Nachrichten)</th>
                <th style=\"text-align:center;\"><input type=\"button\" id=\"selectBtn[" . $category->id. "]\" value=\"X\" onclick=\"xajax_messagesSelectAllInCategory(" . $category->id . "," . count($messages) . ",this.value)\"/></td>
            </tr>";
        } else {
            echo "<tr>
                <th colspan=\"5\">" . text2html($category->name) . "</th>
            </tr>";
        }

        if (count($messages) > 0) {
            $dcnt = 0;
            foreach ($messages as $message) {
                // Sender
                $sender = $message->userFrom > 0
                    ? ($userRepository->getNick($message->userFrom) ?? '<i>Unbekannt</i>')
                    : '<i>' . $category->sender . '</i>';

                // Title
                $subj = filled($message->subject)
                    ? htmlentities($message->subject, ENT_QUOTES, 'UTF-8')
                    : "<i>Kein Titel</i>";

                // Read or not read
                if (!$message->read) {
                    $im_path = "images/pm_new.gif";
                    $subj = '<strong>' . $subj . '</strong>';
                    $strong = 1;
                } else {
                    $im_path = "images/pm_normal.gif";
                    $strong = 0;
                }

                if ($message->read) {
                    echo "<tr id=\"msg_id_" . $messagesReadCount . "\" style=\"display:;\">";
                    $messagesReadCount++;
                } else {
                    echo "<tr style=\"display:;\">";
                }

                echo "<td style=\"width:2%;\">
                        <img src=\"" . $im_path . "\" alt=\"Mail\" id=\"msgimg" . $message->id . "\" />
                    </td>
                <td style=\"width:66%;\" ";
                if ($previewMessages) {
                    // subj has already been encoded above
                    echo tm($subj, htmlentities(substr(strip_bbcode($message->text), 0, 500), ENT_QUOTES, 'UTF-8'));
                }
                echo ">";
                if ($message->massMail) {
                    echo "<b>[Rundmail]</b> ";
                }
                // Wenn Speicher voll ist Nachrichten Markieren
                if ($mode != "archiv" && $readMessagesCount >= $config->getInt('msg_max_store')) {
                    echo "<span style=\"color:red;\">" . $subj . "</span>";
                } else {
                    if ($previewMessages) {
                        echo "<a href=\"javascript:;\" onclick=\"toggleBox('msgtext" . $message->id . "');xajax_messagesSetRead(" . $message->id . ")\" >" . $subj . "</a>";
                    } else {
                        echo "<a href=\"?page=$page&amp;msg_id=" . $message->id . "&amp;mode=" . $mode . "\">" . $subj . "</a>";
                    }
                }
                echo "</td>";
                echo "<td style=\"width:15%;\">" . userPopUp($message->userFrom, $userRepository->getNick($message->userFrom), 0, $strong) . "</td>";
                echo "<td style=\"width:15%;\">" . date("d.m.Y H:i", $message->timestamp) . "</td>";
                echo "<td style=\"width:2%;text-align:center;padding:0px;vertical-align:middle;\">
                <input id=\"delcb_" . $category->id . "_" . $dcnt . "\" type=\"checkbox\" name=\"delmsg[" . $message->id . "]\" value=\"1\" title=\"Nachricht zum Löschen markieren\" /></td>";
                echo "</tr>\n";
                if ($previewMessages) {
                    echo "<tr style=\"display:none;\" id=\"msgtext" . $message->id . "\"><td colspan=\"5\" class=\"tbldata\">";
                    echo text2html(addslashes($message->text));
                    echo "<br/><br/>";
                    $msgadd = "&amp;message_text=" . base64_encode((string) $message->id) . "&amp;message_sender=" . base64_encode($sender);
                    if (substr($message->subject, 0, 3) == "Fw:") {
                        $subject = base64_encode($message->subject);
                    } else {
                        $subject = base64_encode("Fw: " . $message->subject);
                    }
                    echo "<input type=\"button\" value=\"Weiterleiten\" onclick=\"document.location='?page=$page&mode=new&amp;message_subject=" . $subject . "" . $msgadd . "'\" name=\"remit\" />&nbsp;";
                    if ($message->userFrom > 0) {
                        if (substr($message->subject, 0, 3) == "Re:") {
                            $subject = base64_encode($message->subject);
                        } else {
                            $subject = base64_encode("Re: " . $message->subject);
                        }

                        if ($cu->properties->msgCopy) {
                            echo "<input type=\"button\" value=\"Antworten\" name=\"answer\" onclick=\"document.location='?page=$page&mode=new&message_user_to=" . $message->userFrom . "&amp;message_subject=" . $subject . "" . $msgadd . "'\" />&nbsp;";
                        } else {
                            echo "<input type=\"button\" value=\"Antworten\" name=\"answer\" onclick=\"document.location='?page=$page&mode=new&message_user_to=" . $message->userFrom . "&amp;message_subject=" . $subject . "'\" />&nbsp;";
                        }
                        echo "<input type=\"button\" value=\"Absender ignorieren\" onclick=\"document.location='?page=" . $page . "&amp;mode=ignore&amp;add=" . $message->userFrom . "'\" />&nbsp;";
                    }
                    echo "<input type=\"button\" value=\"Löschen\" onclick=\"document.location='?page=$page&mode=mode&del=" . $message->id . "';\" />&nbsp;";
                    if ($message->userFrom > 0) {
                        ticket_button('1', "Beleidigung melden", $message->userFrom);
                    } else {
                        ticket_button('8', "Regelverstoss melden");
                    }
                    echo "<br/>";
                    echo "</td></tr>";
                }
                $dcnt++;
                $messageCount++;
            }
        } else {
            echo "<tr>
                <td colspan=\"5\"><i>Keine Nachrichten vorhanden</i></td>
            </tr>";
        }
    }
    tableEnd();

    if ($messageCount > 0) {
        // Übergibt alle Nachrichten-ID's an die javascript funktion
        echo "<input type=\"hidden\" id=\"msg_cnt\" value=\"" . $messageCount . "\" />";

        echo "<input type=\"submit\" name=\"submitdeleteselection\" value=\"Markierte löschen\" />&nbsp;
        <input type=\"submit\" name=\"submitdeleteall\" value=\"Alle löschen\" onclick=\"return confirm('Wirklich alle Nachrichten löschen?');\" />&nbsp;
        <input type=\"submit\" name=\"submitdeletesys\" value=\"Systemnachrichten löschen\" />";
        if ($mode != "archiv") {
            echo "&nbsp;<input type=\"submit\" name=\"submitarchiving\" value=\"Markierte archivieren\" />";
        }
    }
    echo "</div></form>";
}
