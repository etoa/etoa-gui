<?php

use EtoA\Core\Configuration\ConfigurationService;
use Symfony\Component\HttpFoundation\Request;

/** @var ConfigurationService */
$config = $app['etoa.config.service'];

/** @var \EtoA\Message\MessageRepository $messageRepository */
$messageRepository = $app[\EtoA\Message\MessageRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

/** @var \EtoA\User\UserRepository $userRepository */
$userRepository = $app['etoa.user.repository'];

// DEFINITIONEN //

$msgpreview = $cu->properties->msgPreview==1 ? true : false;
$msgcreatpreview = $cu->properties->msgCreationPreview==1 ? true : false;

// BEGIN SKRIPT //

// Modus setzen
$mode = isset($_GET['mode']) && ($_GET['mode']!="") && ctype_alpha($_GET['mode']) ? $_GET['mode'] : 'inbox';

?>
<script type="text/javascript">
    function selectNewMessages()
    {
        //max = var document.getElementById("msg_cnt").value;

        if (document.getElementById("select_new_messages").innerHTML=="Nur neue Nachrichten anzeigen")
        {
            document.getElementById("select_new_messages").innerHTML="Alle Nachrichten anzeigen";

            // Geht jede einzelne Nachricht durch
            for (x=0;x<=document.getElementById("msg_cnt").value;x++)
            {
                    document.getElementById('msg_id_'+x).style.display='none';
            }

        }
        else
        {
                        document.getElementById("select_new_messages").innerHTML="Nur neue Nachrichten anzeigen";

            // Geht jede einzelne Nachricht durch
            for (x=0;x<=document.getElementById("msg_cnt").value;x++)
            {
                    document.getElementById('msg_id_'+x).style.display='';
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

//
// Neue Nachricht
//
if ($mode=="new")
{
    require('content/messages/new.php');
}

//
// Ignorierliste
//
elseif ($mode=="ignore")
{
    require('content/messages/ignore.php');
}

//
// Gelöschte Nachrichten
//
elseif ($mode=="deleted")
{
    require('content/messages/deleted.php');
}

//
// Gesendete Nachrichten
//
elseif ($mode=="sent")
{
    require('content/messages/sent.php');
}

/***********************
* Nachricht betrachten *
***********************/
else
{
    //
    // Einzelne Nachricht
    //
    if ($request->query->getInt('msg_id') > 0)
    {
        $message = $messageRepository->find($request->query->getInt('msg_id'));
        if ($message !== null && $message->userTo == $cu->id && !$message->deleted)
        {
            // Sender
            $sender = $message->userFrom > 0
                ? ($userRepository->getNick($message->userFrom) ?? '<i>Unbekannt</i>')
                : '<i>' . $messageRepository->getCategorySender($message->catId) . '</i>';

            // Title
            $subj = $message->subject !=""
                ? htmlentities($message->subject, ENT_QUOTES, 'UTF-8')
                : "<i>Kein Titel</i>";

            tableStart();
            echo "<tr><th colspan=\"2\">".$subj."</th></tr>";
            echo "<tr><th width=\"50\" valign=\"top\">Datum:</th>
            <td width=\"250\">".df($message->timestamp)."</td></tr>";
            echo "<tr><th width=\"50\" valign=\"top\">Sender:</th>
            <td width=\"250\">".userPopUp($message->userFrom, $userRepository->getNick($message->userFrom), 0)."</td></tr>";
            echo "<tr><td class=\"tbltitle\" width=\"50\" valign=\"top\">Text:<br/>";
            if ($request->query->has('src'))
            {
                echo '[<a href="?page='.$page.'&mode='.$mode.'&amp;msg_id='.$message->id.'">Nachricht</a>]';
            }
            else
            {
                echo '[<a href="?page='.$page.'&mode='.$mode.'&amp;msg_id='.$message->id.'&amp;src=1">Quelltext</a>]';
            }
            echo "</td><td width=\"250\">";
            if ($message->text != "")
            {
                if ($request->query->has('src'))
                {
                    echo '<textarea rows="30" cols="60" readonly="readonly">'.htmlentities($message->text, ENT_QUOTES, 'UTF-8').'</textarea>';
                }
                else
                {
                    echo text2html(addslashes($message->text));
                }
            }
            else
            {
                echo "<i>Kein Text</i>";
            }
            echo "</td></tr>";
            tableEnd();

            if (!$message->read)
            {
                $messageRepository->setRead($message->id);
            }

            echo "<form action=\"?page=$page&mode=new\" method=\"post\">";
            checker_init();

            echo "<input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=messages&mode=".$mode."'\"/>&nbsp;";
            echo "<input type=\"hidden\" name=\"message_id\" value=\"".$message->id."\" />";
            echo "<input type=\"hidden\" name=\"message_subject\" value=\"".$message->subject."\" />";
            echo "<input type=\"hidden\" name=\"message_sender\" value=\"".$sender."\" />";
            if ($cu->properties->msgCopy)
            {
                // Muss mit echo 'text'; erfolgen, da sonst der Text beim ersten " - Zeichen abgeschnitten wird!
                // Allerdings ist so das selbe Problem mit den ' - Zeichen!
                echo '<input type=\'hidden\' name=\'message_text\' value=\''.htmlentities($message->text, ENT_QUOTES, 'UTF-8').'\' />';
            }
            echo "<input type=\"submit\" value=\"Weiterleiten\" name=\"remit\" />&nbsp;";
            if ($message->userFrom > 0)
            {
                echo "<input type=\"hidden\" name=\"message_user_to\" value=\"".$message->userFrom."\" />";
                echo "<input type=\"submit\" value=\"Antworten\" name=\"answer\" />&nbsp;";
                echo "<input type=\"button\" value=\"Absender ignorieren\" onclick=\"document.location='?page=".$page."&amp;mode=ignore&amp;add=".$message->userFrom."'\" />&nbsp;";
            }
            echo "<input type=\"button\" value=\"Löschen\" onclick=\"document.location='?page=$page&mode=mode&del=".$message->id."';\" />&nbsp;";
            if ($message->userFrom > 0)
            {
                ticket_button('1',"Beleidigung melden",$message->userFrom);
            }
            else
            {
                ticket_button('8',"Regelverstoss melden");
            }
            echo "</form>";
        }
        else
        {
            echo "<p align=\"center\" class=\"infomsg\">Diese Nachricht existiert nicht!</p>";
            echo "<p align=\"center\"><input type=\"button\" value=\"Zurück\" onclick=\"document.location='?page=messages&mode=".$mode."'\"></p>";
        }
    }

    //
    // Übersicht
    //
    else
    {
        // Einzelne Nachricht löschen
        if ($request->request->has('submitdelete') && checker_verify())
        {
            $message = $messageRepository->find($request->request->getInt('message_id'));
            if ($message !== null && $message->userTo == $cu->id)
            {
                if ($messageRepository->setDeleted($message->id)) {
                    success_msg("Nachricht wurde gelöscht!");
                } else {
                    error_msg("Nachricht konnte nicht gelöscht werden!");
                }
            }
        }
        if ($request->query->getInt('del') > 0)
        {
            $message = $messageRepository->find($request->query->getInt('del'));
            if ($message !== null && $message->userTo == $cu->id)
            {
                if ($messageRepository->setDeleted($message->id)) {
                    success_msg("Nachricht wurde gelöscht!");
                } else {
                    error_msg("Nachricht konnte nicht gelöscht werden!");
                }
            }
        }

        // Selektiere löschen
        if ($request->request->has('submitdeleteselection')  && checker_verify())
        {
            if($mode=="archiv")
            {
                $sqladd = " AND message_archived=1";
            }
            else
            {
                $sqladd = " AND message_archived=0";
            }

            if (count($_POST['delmsg'])>0)
            {
                foreach ($_POST['delmsg'] as $id=>$val)
                {
                    dbquery("
                    UPDATE
                        messages
                    SET
                        message_deleted=1
                    WHERE
                        message_id='".intval($id)."'
                        AND message_user_to='".$cu->id."'
                        $sqladd;");
                }
                if (count($_POST['delmsg'])==1)
                {
                    success_msg("Nachricht wurde gelöscht!");
                }
                else
                {
                    success_msg("Nachrichten wurden gelöscht!");
                }
            }
        }

        // Alle Nachrichten löschen
        elseif ($request->request->has('submitdeleteall') && checker_verify())
        {
            $messageRepository->setDeletedForUser($cu->id, true, null, $mode == "archiv");
            success_msg("Alle Nachrichten wurden gelöscht!");
        }

        // Systemnachrichten löschen
        elseif ($request->request->has('submitdeletesys') && checker_verify())
        {
            $messageRepository->setDeletedForUser($cu->id, true, 0, $mode == "archiv");
            success_msg("Alle Systemnachrichten wurden gelöscht!");
        }
        elseif ($request->request->has('submitarchiving')  && checker_verify())
        {
            if (count($request->request->get('delmsg')) > 0)
            {
                if(count($_POST['delmsg'])<=($config->param1Int('msg_max_store')-$_POST['archived_msg_cnt']))
                {
                    foreach ($_POST['delmsg'] as $id=>$val)
                    {
                        dbquery("
                        UPDATE
                            messages
                        SET
                            message_archived=1
                        WHERE
                            message_id='".intval($id)."'
                            AND message_user_to='".$cu->id."'
                            ;");
                    }
                    if (count($_POST['delmsg'])==1) {
                        success_msg("Nachricht wurde archiviert!");
                    } else {
                        success_msg("Nachrichten wurden archiviert!");
                    }
                }
                else
                {
                    error_msg("Zu wenig Platz im Archiv!");
                }
            }
        }

        //Zählt gelesene Nachrichten
        $readMessagesCount = $messageRepository->countReadForUser($cu->id);

        //Zählt archivierte Nachrichten
        $archivedMessagesCount = $messageRepository->countArchivedForUser($cu->id);

        // Rechnet %-Werte für tabelle (1/2)
        $percentRead = min(ceil($readMessagesCount / $config->getInt('msg_max_store') * 100), 100);
        $percentArchived = min(ceil($archivedMessagesCount / $config->param1Int('msg_max_store') * 100), 100);

        $r_color = ($percentRead >= 90) ? 'color:red;' : '';
        $a_color = ($percentArchived >= 90) ? 'color:red;' : '';

        // Archiv-Grafik
        tableStart("Nachrichten");
        echo "<tr>
            <th style=\"text-align:center;width:50%;".$r_color."\">
                Gelesen: ".$readMessagesCount."/".$config->getInt('msg_max_store')." Nachrichten
                </th>
                <th style=\"text-align:center;width:50%;".$a_color."\">
                Archiviert: ".$archivedMessagesCount."/".$config->param1Int('msg_max_store')." Nachrichten
                </th>
            </tr>";
                    echo '<tr>
                <td style="padding:0px;height:10px;"><img src="images/poll3.jpg" style="height:10px;width:'.$percentRead.'%;" alt="poll" /></td>
                <td style="padding:0px;height:10px;"><img src="images/poll2.jpg" style="height:10px;width:'.$percentArchived.'%;" alt="poll" /></td>
            </tr>';

        // Wenn es neue Nachrichten hat, Button zum Selektieren anzeigen
        if ($messageRepository->countNewForUser($cu->id) > 0)
        {
            echo '<tr>
                <td style="text-align:center;" colspan="2">
                <a href="javascript:;" onclick="selectNewMessages();" id="select_new_messages" name="select_new_messages">Nur neue Nachrichten anzeigen</a>
                </td>
            </tr>';
        }
        tableEnd();

        echo "<form action=\"?page=$page&amp;mode=".$mode."\" method=\"post\"><div>";
        $cstr = checker_init();
        echo "<input type=\"hidden\" name=\"archived_msg_cnt\" value=\"".$archivedMessagesCount."\" />";

        // Nachrichten
        tableStart("Kategorien");
        $msgcnt=0;
        $rcnt=0;

        $categories = $messageRepository->findAllCategories();
        $categories[] = [
            'cat_id' => 0,
            'cat_name' => "Ohne Kategorie",
            'cat_desc' => "",
            'cat_sender' => "System",
        ];

        foreach ($categories as $category)
        {
            if($mode=="archiv")
            {
                $mres = dbquery("
                SELECT
                    md.subject,
                    md.text,
                    message_id,
                    message_timestamp,
                    message_user_from,
                    message_read,
                    message_massmail,
                    message_replied,
                    message_forwarded,
                    user_nick
                FROM
                    messages
        INNER JOIN
            message_data as md
            ON message_id=md.id
                LEFT JOIN
                    users
                    ON message_user_from=user_id
                WHERE
                    message_user_to='".$cu->id."'
                    AND message_cat_id='".$category['cat_id']."'
                    AND message_deleted=0
                    AND message_archived=1
                ORDER BY
                    message_timestamp DESC;");
            }
            else
            {
                $mres = dbquery("
                SELECT
                    md.subject,
                    md.text,
                    message_id,
                    message_timestamp,
                    message_user_from,
                    message_read,
                    message_massmail,
                    message_read,
                    message_replied,
                    message_forwarded,
                    user_nick
                FROM
                    messages
        INNER JOIN
            message_data as md
            ON message_id=md.id
                LEFT JOIN
                    users
                    ON message_user_from=user_id
                WHERE
                    message_user_to='".$cu->id."'
                    AND message_cat_id='".$category['cat_id']."'
                    AND message_deleted=0
                    AND message_archived=0
                ORDER BY
                    message_read ASC,
                    message_timestamp DESC;");
            }
            $ccnt=mysql_num_rows($mres);

            // Kategorie-Titel
            if ($ccnt>0)
            {
                echo "<tr>
                    <th colspan=\"4\">".text2html($category['cat_name'])." (".$ccnt." Nachrichten)</th>
                    <th style=\"text-align:center;\"><input type=\"button\" id=\"selectBtn[".$category['cat_id']."]\" value=\"X\" onclick=\"xajax_messagesSelectAllInCategory(".$category['cat_id'].",".$ccnt.",this.value)\"/></td>
                </tr>";
            }
            else
            {
                echo "<tr>
                    <th colspan=\"5\">".text2html($category['cat_name'])."</th>
                </tr>";
            }
            if ($ccnt>0)
            {
                $dcnt=0;
                while ($marr = mysql_fetch_array($mres))
                {
                    // Sender
                    $sender = $marr['message_user_from']>0 ? ($marr['user_nick']!='' ? $marr['user_nick'] : '<i>Unbekannt</i>') : '<i>'.$category['cat_sender'].'</i>';

                    // Title
                    $subj = $marr['subject']!="" ? htmlentities($marr['subject'],ENT_QUOTES,'UTF-8') : "<i>Kein Titel</i>";

                    // Read or not read
                    if ($marr['message_read']==0)
                    {
                        $im_path = "images/pm_new.gif";
                        $subj = '<strong>'.$subj.'</strong>';
                        $strong = 1;
                    }
                    else
                    {
                        $im_path = "images/pm_normal.gif";
                        $strong = 0;
                    }

                    if ($marr['message_read']==1)
                    {
                        echo "<tr id=\"msg_id_".$rcnt."\" style=\"display:;\">";
                        $rcnt++;
                    }
                    else
                    {
                        echo "<tr style=\"display:;\">";
                    }

                    echo "				<td style=\"width:2%;\">
                            <img src=\"".$im_path."\" alt=\"Mail\" id=\"msgimg".$marr['message_id']."\" />
                        </td>
                    <td style=\"width:66%;\" ";
                    if ($msgpreview)
                    {
                        // subj has already been encoded above
                        echo tm($subj,htmlentities(substr(strip_bbcode($marr['text']), 0, 500),ENT_QUOTES,'UTF-8'));
                    }
                    echo ">";
                    if ($marr['message_massmail']==1)
                    {
                        echo "<b>[Rundmail]</b> ";
                    }
                    //Wenn Speicher voll ist Nachrichten Markieren
                    if($mode!="archiv" && $readMessagesCount>=$config->getInt('msg_max_store'))
                    {
                        echo "<span style=\"color:red;\">".$subj."</span>";
                    }
                    else
                    {
                        if ($msgpreview)
                        {
                            echo "<a href=\"javascript:;\" onclick=\"toggleBox('msgtext".$marr['message_id']."');xajax_messagesSetRead(".$marr['message_id'].")\" >".$subj."</a>";
                        }
                        else
                        {
                            echo "<a href=\"?page=$page&amp;msg_id=".$marr['message_id']."&amp;mode=".$mode."\">".$subj."</a>";
                        }
                    }
                    echo "</td>";
                    echo "<td style=\"width:15%;\">".userPopUp($marr['message_user_from'],$marr['user_nick'],0,$strong)."</td>";
                    echo "<td style=\"width:15%;\">".date("d.m.Y H:i",$marr['message_timestamp'])."</td>";
                    echo "<td style=\"width:2%;text-align:center;padding:0px;vertical-align:middle;\">
                    <input id=\"delcb_".$category['cat_id']."_".$dcnt."\" type=\"checkbox\" name=\"delmsg[".$marr['message_id']."]\" value=\"1\" title=\"Nachricht zum Löschen markieren\" /></td>";
                    echo "</tr>\n";
                    if ($msgpreview)
                    {
                        echo "<tr style=\"display:none;\" id=\"msgtext".$marr['message_id']."\"><td colspan=\"5\" class=\"tbldata\">";
                        echo text2html(addslashes($marr['text']));
                        echo "<br/><br/>";
                        $msgadd = "&amp;message_text=".base64_encode($marr['message_id'])."&amp;message_sender=".base64_encode($sender);
                        if(substr($marr['subject'],0,3) == "Fw:")
                        {
                            $subject = base64_encode($marr['subject']);
                        }
                        else
                        {
                            $subject = base64_encode("Fw: ".$marr['subject']);
                        }
                        echo "<input type=\"button\" value=\"Weiterleiten\" onclick=\"document.location='?page=$page&mode=new&amp;message_subject=".$subject."".$msgadd."'\" name=\"remit\" />&nbsp;";
                        if ($marr['message_user_from']>0)
                        {
                            if(substr($marr['subject'],0,3) == "Re:")
                            {
                                $subject = base64_encode($marr['subject']);
                            }
                            else
                            {
                                $subject = base64_encode("Re: ".$marr['subject']);
                            }

                            if ($cu->properties->msgCopy)
                            {
                                echo "<input type=\"button\" value=\"Antworten\" name=\"answer\" onclick=\"document.location='?page=$page&mode=new&message_user_to=".$marr['message_user_from']."&amp;message_subject=".$subject."".$msgadd."'\" />&nbsp;";
                            }
                            else
                            {
                                echo "<input type=\"button\" value=\"Antworten\" name=\"answer\" onclick=\"document.location='?page=$page&mode=new&message_user_to=".$marr['message_user_from']."&amp;message_subject=".$subject."'\" />&nbsp;";
                            }
                            echo "<input type=\"button\" value=\"Absender ignorieren\" onclick=\"document.location='?page=".$page."&amp;mode=ignore&amp;add=".$marr['message_user_from']."'\" />&nbsp;";
                        }
                        echo "<input type=\"button\" value=\"Löschen\" onclick=\"document.location='?page=$page&mode=mode&del=".$marr['message_id']."';\" />&nbsp;";
                        if ($marr['message_user_from']>0)
                        {
                            ticket_button('1',"Beleidigung melden",$marr['message_user_from']);
                        }
                        else
                        {
                            ticket_button('8',"Regelverstoss melden");
                        }
                        echo "<br/>";
                        echo "</td></tr>";
                    }
                    $dcnt++;
                    $msgcnt++;
                }
            }
            else
            {
                echo "<tr>
                    <td colspan=\"5\"><i>Keine Nachrichten vorhanden</i></td>
                </tr>";
            }
        }
        tableEnd();
        if ($msgcnt>0)
        {
            // Übergibt alle Nachrichten-ID's an die javascript funktion
            echo "<input type=\"hidden\" id=\"msg_cnt\" value=\"".$msgcnt."\" />";

            echo "<input type=\"submit\" name=\"submitdeleteselection\" value=\"Markierte löschen\" />&nbsp;
            <input type=\"submit\" name=\"submitdeleteall\" value=\"Alle löschen\" onclick=\"return confirm('Wirklich alle Nachrichten löschen?');\" />&nbsp;
            <input type=\"submit\" name=\"submitdeletesys\" value=\"Systemnachrichten löschen\" />";
            if($mode!="archiv")
            {
                echo "&nbsp;<input type=\"submit\" name=\"submitarchiving\" value=\"Markierte archivieren\" />";
            }
        }
        echo "</div></form>";
    }
}
