<?PHP

use EtoA\Core\Configuration\ConfigurationService;

/** @var ConfigurationService */
$config = $app['etoa.config.service'];

if (Alliance::checkActionRights('applications'))
{
    $maxMemberCount = $config->getInt("alliance_max_member_count");

    echo "<h2>Bewerbungen</h2>";
    if(isset($_POST['applicationsubmit']) && checker_verify())
    {
        if (count($_POST['application_answer'])>0)
        {
            $cnt = 0;
            /** @var \EtoA\Alliance\AllianceRepository $allianceRepository */
				$allianceRepository = $app['etoa.alliance.repository'];
				$alliance = $allianceRepository->getAlliance((int) $cu->allianceId);
            $new_member = false;

            foreach ($_POST['application_answer'] as $id=>$answer)
            {

                $nick = $_POST['application_user_nick_'.$id.''];

                // Anfrage annehmen
                if ($answer==2)
                {
                    if ($maxMemberCount != 0 && Alliance::countMembers($cu->allianceId) >= $maxMemberCount) {
                        error_msg("Maximale Anzahl an Mitgliedern erreicht!");
                        break;
                    }

                    $cnt++;
                    $new_member = true;
                    success_msg($nick." wurde angenommen.");

                    // Nachricht an den Bewerber schicken
                    /** @var \EtoA\Message\MessageRepository $messageRepository */
                    $messageRepository = $app[\EtoA\Message\MessageRepository::class];
                    $messageRepository->createSystemMessage($id, MSG_ALLYMAIL_CAT, "Bewerbung angenommen", "Deine Allianzbewerbung wurde angenommen!\n\n[b]Antwort:[/b]\n".addslashes($_POST['application_answer_text'][$id]));

                    // Log schreiben
                    /** @var \EtoA\Alliance\AllianceHistoryRepository $allianceHistoryRepository */
                    $allianceHistoryRepository = $app[\EtoA\Alliance\AllianceHistoryRepository::class];
                    $allianceHistoryRepository->addEntry((int) $cu->allianceId, "Die Bewerbung von [b]".$nick."[/b] wurde akzeptiert!");
                    Log::add(5,Log::INFO, "Der Spieler [b]".$nick."[/b] tritt der Allianz [b]" . $alliance->nameWithTag . "[/b] bei!");

                    $tu = new User($id);
                    $tu->addToUserLog("alliance","{nick} ist nun ein Mitglied der Allianz ".$alliance->name.".");

                    // Speichern
                    dbquery("
                    UPDATE
                        users
                    SET
                        user_alliance_id=".$cu->allianceId."
                    WHERE
                        user_id='".$id."';");

                    dbquery("
                    DELETE FROM
                        alliance_applications
                    WHERE
                        user_id=".$id."
                        AND alliance_id=".$cu->allianceId.";");
                }
                // Anfrage ablehnen
                elseif($answer==1)
                {
                    $cnt++;
                    success_msg($nick." wurde abgelehnt.");

                    // Nachricht an den Bewerber schicken
                    /** @var \EtoA\Message\MessageRepository $messageRepository */
                    $messageRepository = $app[\EtoA\Message\MessageRepository::class];
                    $messageRepository->createSystemMessage($id, MSG_ALLYMAIL_CAT, "Bewerbung abgelehnt", "Deine Allianzbewerbung wurde abgelehnt!\n\n[b]Antwort:[/b]\n".addslashes($_POST['application_answer_text'][$id]));

                    // Log schreiben
                    /** @var \EtoA\Alliance\AllianceHistoryRepository $allianceHistoryRepository */
                    $allianceHistoryRepository = $app[\EtoA\Alliance\AllianceHistoryRepository::class];
                    $allianceHistoryRepository->addEntry((int) $cu->allianceId, "Die Bewerbung von [b]".$nick."[/b] wurde abgelehnt!");

                    // Anfrage löschen
                    dbquery("
                    DELETE FROM
                        alliance_applications
                    WHERE
                        user_id=".$id."
                        AND alliance_id=".$cu->allianceId.";");
                }
                // Anfrage unbearbeitet lassen, jedoch Nachricht verschicken wenn etwas geschrieben ist
                else
                {
                    $text = str_replace(' ','',$_POST['application_answer_text'][$id]);
                    if($text != '')
                    {
                        // Nachricht an den Bewerber schicken
                        /** @var \EtoA\Message\MessageRepository $messageRepository */
                        $messageRepository = $app[\EtoA\Message\MessageRepository::class];
                        $messageRepository->createSystemMessage($id, MSG_ALLYMAIL_CAT, "Bewerbung: Nachricht", "Antwort auf die Bewerbung an die Allianz [b]" . $alliance->nameWithTag . "[/b]:\n".$_POST['application_answer_text'][$id]."");

                        $cnt++;
                        success_msg($nick.": Nachricht gesendet");
                    }
                }
            }

            // Wenn neue Members hinzugefügt worde sind werden ev. die Allianzrohstoffe angepasst
            if($new_member)
            {
                $cu->alliance->calcMemberCosts();
            }

            success_msg("Änderungen übernommen");
        }
    }

    $currentMemberCount = Alliance::countMembers($cu->allianceId);

    echo "<form action=\"?page=$page&action=applications\" method=\"post\" id=\"applicationsForm\">";
    checker_init();
    $res = dbquery("
    SELECT
        aa.timestamp,
        aa.text,
    u.user_id,
    u.user_nick,
    u.user_points,
    u.user_rank,
    u.user_registered
    FROM
        alliance_applications as aa
    INNER JOIN
        users as u
    ON
        aa.user_id=u.user_id
        AND aa.alliance_id=".$cu->allianceId.";");
    if (mysql_num_rows($res)>0)
    {
        tableStart("Bewerbungen prüfen");
        echo "<tr>
                        <th width=\"10%\">User</td>
                        <th width=\"35%\">Datum / Text</td>
                        <th width=\"35%\">Nachricht</td>
                        <th width=\"20%\">Aktion</td>
                    </tr>";
        while ($arr = mysql_fetch_array($res))
        {
            echo "<tr>
            <td ".tm("Info","Rang: ".$arr['user_rank']."<br>Punkte: ".nf($arr['user_points'])."<br>Registriert: ".date("d.m.Y H:i",$arr['user_registered'])."").">
                <a href=\"?page=userinfo&id=".$arr['user_id']."\">".$arr['user_nick']."</a>";

                // Übergibt Usernick dem Formular, damit beim Submit nicht nochmals eine DB Abfrage gestartet werden muss
                echo "<input type=\"hidden\" name=\"application_user_nick_".$arr['user_id']."\" value=\"".$arr['user_nick']."\" />
            </td>
            <td>".df($arr['timestamp'])."<br/><br/>".text2html($arr['text'])."</td>
            <td>
                <textarea rows=\"6\" cols=\"40\" name=\"application_answer_text[".$arr['user_id']."]\" /></textarea><br/>".helpLink('textformat', 'Hilfe zur Formatierung')."
            </td>
            <td>";
            if ($maxMemberCount == 0 || $currentMemberCount < $maxMemberCount) {
                echo "<input type=\"radio\" name=\"application_answer[".$arr['user_id']."]\" value=\"2\" onchange=\"xajax_showAllianceMemberAddCosts('".$cu->allianceId()."',xajax.getFormValues('applicationsForm'));\"/> <span ".tm("Anfrage annehmen","".$arr['user_nick']." wird in die Allianz aufgenommen.<br>Eine Nachricht wird versendet.").">Annehmen</span><br><br>";
            }
            echo "<input type=\"radio\" name=\"application_answer[".$arr['user_id']."]\" value=\"1\" onchange=\"xajax_showAllianceMemberAddCosts('".$cu->allianceId()."',xajax.getFormValues('applicationsForm'));\"/> <span ".tm("Anfrage ablehnen","".$arr['user_nick']." wird der Zutritt zu der Allianz verweigert.<br>Eine Nachricht wird versendet.").">Ablehnen</span><br><br>";
            echo "<input type=\"radio\" name=\"application_answer[".$arr['user_id']."]\" value=\"0\" checked=\"checked\" onchange=\"xajax_showAllianceMemberAddCosts('".$cu->allianceId()."',xajax.getFormValues('applicationsForm'));\"/> <span ".tm("Anfrage nicht bearbeiten","Sofern vorhanden, wird eine Nachricht an ".$arr['user_nick']." geschickt.").">Nicht bearbeiten</span>";
            echo "</td>
            </tr>";
        }
        echo "<tr id=\"memberCosts\" style=\"display: none;\"><td colspan=\"4\" id=\"memberCostsTD\"></td></tr>";
        tableEnd();
        echo "<input type=\"submit\" name=\"applicationsubmit\" value=\"&Uuml;bernehmen\" />&nbsp;&nbsp;&nbsp;";
    }
    else
    {
        error_msg("Keine Bewerbungen vorhanden!");
    }
    echo "<input type=\"button\" onclick=\"document.location='?page=alliance';\" value=\"Zur&uuml;ck\" /></form>";

    if ($maxMemberCount != 0) {
        echo "<p><b>Hinweis:</b> Eine Allianz darf maximal $maxMemberCount Mitglieder haben (aktuell $currentMemberCount)!</p>";
    }

}
