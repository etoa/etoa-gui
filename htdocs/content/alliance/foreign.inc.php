<?PHP

use EtoA\Core\Configuration\ConfigurationService;

/** @var ConfigurationService */
$config = $app['etoa.config.service'];

if ($config->getBoolean("alliance_allow")) {
		if ($cu->allianceId == 0)
		{
		    /** @var \EtoA\Alliance\AllianceRepository $allianceRepository */
		    $allianceRepository = $app['etoa.alliance.repository'];

			// Check application
        $application_alliance=0;
        $application_timestamp = 0;
        $res = dbquery("
        SELECT
            alliance_id,
            timestamp
        FROM
            alliance_applications
        WHERE
            user_id=".$cu->id."
        ;");
        if (mysql_num_rows($res))
        {
            $arr=mysql_fetch_row($res);
            $application_alliance=$arr[0];
            $application_timestamp=$arr[1];
        }

			//
			// Infotext bei aktiver Bewerbung
			//
			if ($application_alliance>0)
			{
				// Bewerbung zurückziehen
				if (isset($_GET['action']) && $_GET['action']=="cancelapplication")
				{
				    $alliance = $allianceRepository->getAlliance((int) $application_alliance);
        send_msg($alliance->founderId,MSG_ALLYMAIL_CAT,"Bewerbung zurückgezogen","Der Spieler ".$cu->nick." hat die Bewerbung bei deiner Allianz zurückgezogen!");
        add_alliance_history($application_alliance,"Der Spieler [b]".$cu->nick."[/b] zieht seine Bewerbung zurück.");
        dbquery("
        DELETE FROM
            alliance_applications
        WHERE
            user_id=".$cu->id."
            AND alliance_id=".$application_alliance.";");
        echo "Deine Bewerbung wurde gel&ouml;scht!<br/><br/>
        <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"OK\" />";
            }
            // Bewerbungsstatus anzeigen
            else
            {
                echo "<h2>Bewerbungsstatus</h2>";
                $appres = dbquery("
                SELECT
            alliance_tag,
            alliance_name
                FROM
            alliances
                WHERE
                    alliance_id=".$application_alliance.";");
                if (mysql_num_rows($appres)>0)
                {
                    $apparr = mysql_fetch_array($appres);
            success_msg("Du hast dich am ".df($application_timestamp)." bei der Allianz [".$apparr['alliance_tag']."] ".$apparr['alliance_name']." beworben
            und musst nun darauf warten, dass deine Bewerbung akzeptiert wird!");
            echo "<input type=\"button\" onclick=\"document.location='?page=$page&action=cancelapplication';\" value=\"Bewerbung zurückziehen\" />";
                }
                else
                {
            error_msg("Du hast dich am ".df($application_timestamp)." bei einer Allianz beworben, diese Allianz existiert aber leider nicht mehr.
            Deine Bewerbung wurde deshalb gelöscht!");
            echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=join';\" value=\"Bei einer anderen Allianz bewerben\" />";
                }
            }
        }

        //
        // Allianzgründung
        //
        elseif (isset($_GET['action']) && $_GET['action']=="create")
        {
            echo "<h2>Gr&uuml;ndung einer Allianz</h2>";

            $defTag = "";
            $defName = "";
            $finish = false;
            // Allianzgründung speichern
            if (isset($_POST['createsubmit']) && $_POST['createsubmit']!="" && checker_verify())
            {
                $rtnMsg = "";
                if (Alliance::create(array(
                    "tag" => $_POST['alliance_tag'],
                    "name" => $_POST['alliance_name'],
                    "founder" => $cu
                    ),$rtnMsg))
                {
                    success_msg("Allianz [b]".$rtnMsg."[/b] gegründet!");
                    echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Weiter\" />";
                    $finish = true;

                    $app['dispatcher']->dispatch(new \EtoA\Alliance\Event\AllianceCreate(), \EtoA\Alliance\Event\AllianceCreate::CREATE_SUCCESS);
                }
                else
                {
                    $defTag = $_POST['alliance_tag'];
                    $defName = $_POST['alliance_name'];
                    error_msg($rtnMsg);
                }
            }
            if (!$finish)
            {
                echo "<form action=\"?page=$page&amp;action=create\" method=\"post\">";
                checker_init();
                tableStart("Allianz-Daten");
                echo "<tr><th>Tag / Name:</th>
                <td>
                [<input type=\"text\" name=\"alliance_tag\" size=\"6\" maxlength=\"6\" value=\"".StringUtils::encodeDBStringToPlaintext($defTag)."\" />]
                &nbsp; <input type=\"text\" name=\"alliance_name\" size=\"25\" maxlength=\"25\" value=\"".StringUtils::encodeDBStringToPlaintext($defName)."\" /></td></tr>
                <tr><td colspan=\"2\">Alle weiteren Daten könnten nach der Erstellung im Allianzmenü geändert werden.</td></tr>";
                tableEnd();
                echo "<input type=\"submit\" name=\"createsubmit\" value=\"Speichern\" /> &nbsp;
                <input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Übersicht\" /></form>";
            }
        }

        //
        // Bewerbung bei einer Allianz
        //
        elseif (isset($_GET['action']) && $_GET['action']=="join")
        {
            // Bewerbungstext schreiben
            if (isset($_GET['alliance_id']) && intval($_GET['alliance_id'])>0)
            {
                $res=dbquery("
                SELECT
                    alliance_id,
                    alliance_tag,
                    alliance_name,
                    alliance_application_template,
                    alliance_accept_applications
                FROM
                    alliances
                WHERE
                    alliance_id='".intval($_GET['alliance_id'])."'");
                if (mysql_num_rows($res)>0)
                {
                    $arr=mysql_fetch_array($res);
                    echo "<h2>Bewerbung bei der Allianz [".$arr['alliance_tag']."] ".$arr['alliance_name']."</h2>";
                    if($arr['alliance_accept_applications']==1)
                    {
                        echo "<form action=\"?page=$page&amp;action=join\" method=\"post\">";
                        checker_init();
                        tableStart("Bewerbungstext");
                        echo "<tr><th>Nachricht:</th><td><textarea rows=\"15\" cols=\"80\" name=\"user_alliance_application\">".$arr['alliance_application_template']."</textarea><br/>".helpLink('textformat', 'Hilfe zur Formatierung')."</td>";
                        tableEnd();
                        echo "<input type=\"hidden\" name=\"user_alliance_id\" value=\"".intval($arr['alliance_id'])."\" />";
                        echo "<input type=\"submit\" name=\"submitapplication\" value=\"Senden\" />&nbsp;<input type=\"button\" onclick=\"document.location='?page=alliance&action=join'\" value=\"Zur&uuml;ck\" /></form>";
                    }
                    else
                    {
                        error_msg("Die Allianz nimmt keine Bewerbungen an!");
                    }
                }
                else
                {
                    error_msg("Allianzdatensatz nicht gefunden!");
                }
            }
            // Bewerbungstext senden
            elseif (isset($_POST['submitapplication']) && checker_verify())
            {
                echo "<h2>Bewerbung abschicken</h2>";

                $aid = (int) $_POST['user_alliance_id'];
                if ($_POST['user_alliance_application']!='')
                {
                    $alliance = $allianceRepository->getAlliance($aid);
                    send_msg($alliance->founderId,MSG_ALLYMAIL_CAT,"Bewerbung","Der Spieler ".$cu->nick." hat sich bei deiner Allianz beworben. Gehe auf die [page=alliance&action=applications]Allianzseite[/page] für Details!");
                    add_alliance_history($aid,"Der Spieler [b]".$cu->nick."[/b] bewirbt sich sich bei der Allianz.");
                    dbquery("
                    INSERT INTO
                        alliance_applications
                    (
                        user_id,
                        alliance_id,
                        text,
                        timestamp
                    )
                    VALUES
                    (
                        ".$cu->id.",
                        ".$aid.",
                        '".mysql_real_escape_string($_POST['user_alliance_application'])."',
                        ".time()."
                    );
                    ");

                    success_msg("Deine Bewerbung bei der Allianz " . $alliance->nameWithTag . " wurde gespeichert! Die Allianzleitung wurde informiert und wird deine Bewerbung ansehen.");
                    echo "<input value=\"&Uuml;bersicht\" type=\"button\" onclick=\"document.location='?page=$page'\" />";
                }
                else
                {
                    error_msg("Du musst einen Bewerbungstext eingeben!");
                    echo "<input value=\"Zur&uuml;ck\" type=\"button\" onclick=\"document.location='?page=$page&action=join&alliance_id=".$aid."'\" />";
                }
            }
            // Allianzauswahl anzeigen
            else
            {
                echo "<h2>Allianz w&auml;hlen</h2>
                Nicht alle Allianzen akzeptieren jederzeit eine Bewerbung. <br/>
                Im Folgenden findest du eine Liste der Allianzen die momentan Bewerbungen akzeptieren:<br/><br/>";
                $res=dbquery("
                SELECT
                    alliances.alliance_id,
                    alliances.alliance_tag,
                    alliances.alliance_name,
                    alliances.alliance_accept_applications,
                    COUNT(users.user_id) as member_count
                FROM
                    alliances
                LEFT JOIN
                    users
                    ON users.user_alliance_id=alliances.alliance_id
                WHERE
                    alliances.alliance_accept_applications=1
                GROUP BY
                    alliances.alliance_id
                ORDER BY
                    alliances.alliance_name,
                    alliances.alliance_tag;");
                if (mysql_num_rows($res)>0)
                {
                    tableStart("","400"," align=\"center\"");
//					echo "<table width=\"300\" align=\"center\" class=\"tbl\">";
                    echo "<tr>
                                    <th>Tag</th>
                                    <th>Name</th>
                                    <th>Mitglieder</th>
                                    <th style=\"width:100px;\">Aktionen</th>
                            </tr>";
                    while ($arr=mysql_fetch_array($res))
                    {
                        echo "<tr><td>".$arr['alliance_tag']."</td>
                        <td>".$arr['alliance_name']."</td>
                        <td>".$arr['member_count']."</td>
                        <td><a href=\"?page=alliance&amp;info_id=".$arr['alliance_id']."\">Info</a>";
                        echo "&nbsp;<a href=\"?page=$page&action=join&alliance_id=".$arr['alliance_id']."\">Bewerben</a>";
                        echo "</td></tr>";
                    }
                    tableEnd();

                    $maxMemberCount = $config->getInt("alliance_max_member_count");
                    if ($maxMemberCount != 0) {
                        echo "<p><b>Hinweis:</b> Eine Allianz darf maximal $maxMemberCount Mitglieder haben!</p>";
                    }

                    echo "<a href=\"?page=$page&amp;action=create\">Gründe</a> eine eigene Allianz.</a>";
                }
                else
                {
                    error_msg("Es gibt im Moment keine Allianzen denen man beitreten k&ouml;nnte!");
                    echo "<a href=\"?page=$page&amp;action=create\">Gründe</a> eine eigene Allianz.</a>";
                }
            }
        }

        //
        // Infotext wenn in keiner Allianz
        //
        else
        {
            echo "Es kann von Vorteil sein, wenn man sich nicht alleine gegen den Rest des Universums durchsetzen muss. Dazu gibt es das Allianz-System,
                mit dem du dich mit anderen Spielern als Team zusammentun kannst. Viele Allianzen pflegen eine regelm&auml;ssige Kommunikation, bieten Schutz vor
                Angriffen oder r&auml;chen dich wenn du angegriffen worden bist. Trete einer Allianz bei oder gr&uuml;nde selber eine Allianz.<br/><br/>";
            echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=join';\" value=\"Einer Allianz beitreten\" />&nbsp;&nbsp;&nbsp;";
            echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=create';\" value=\"Eine Allianz gr&uuml;nden\" />";
        }

    }
}
else
    echo "Allianzen sind zur Zeit deaktiviert.";
