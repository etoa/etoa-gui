<?PHP

use EtoA\Alliance\AllianceApplicationRepository;
use EtoA\Alliance\AllianceManagementService;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\InvalidAllianceParametersException;
use EtoA\Core\Configuration\ConfigurationService;
use Symfony\Component\HttpFoundation\Request;

/** @var ConfigurationService */
$config = $app['etoa.config.service'];

/** @var AllianceManagementService */
$allianceManagementService = $app[AllianceManagementService::class];

/** @var AllianceRepository */
$allianceRepository = $app[AllianceRepository::class];

/** @var AllianceApplicationRepository */
$allianceApplicationRepository = $app[AllianceApplicationRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

if ($config->getBoolean("alliance_allow")) {
    if ($cu->allianceId == 0)
    {
        //
        // Infotext bei aktiver Bewerbung
        //
        $application = $allianceApplicationRepository->findOneForUser($cu->id);
        if ($application !== null)
        {
            // Bewerbung zurückziehen
            if ($request->query->get('action') == "cancelapplication")
            {
                $allianceManagementService->withdrawApplication($application->allianceId, $cu->id);
                echo "Deine Bewerbung wurde gelöscht!<br/><br/>
                    <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"OK\" />";
            }
            // Bewerbungsstatus anzeigen
            else
            {
                echo "<h2>Bewerbungsstatus</h2>";
                $alliance = $allianceRepository->getAlliance($application->allianceId);
                if ($alliance !== null) {
                    success_msg("Du hast dich am ".df($application->timestamp)." bei der Allianz " . $alliance->toString() . " beworben und musst nun darauf warten, dass deine Bewerbung akzeptiert wird!");
                    echo "<input type=\"button\" onclick=\"document.location='?page=$page&action=cancelapplication';\" value=\"Bewerbung zurückziehen\" />";
                }
                else
                {
                    error_msg("Du hast dich am ".df($application->timestamp)." bei einer Allianz beworben, diese Allianz existiert aber leider nicht mehr. Deine Bewerbung wurde deshalb gelöscht!");
                    echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=join';\" value=\"Bei einer anderen Allianz bewerben\" />";
                }
            }
        }

        //
        // Allianzgründung
        //
        elseif ($request->query->get('action') == "create")
        {
            echo "<h2>Gründung einer Allianz</h2>";

            $finish = false;
            // Allianzgründung speichern
            if ($request->request->get('createsubmit', '') != '' && checker_verify())
            {
                try {
                    $alliance = $allianceManagementService->create(
                        $request->request->get('alliance_tag'),
                        $request->request->get('alliance_name'),
                        $cu->id
                    );
                    success_msg("Allianz [b]".$alliance->toString()."[/b] gegründet!");
                    echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Weiter\" />";
                    $finish = true;
                } catch (InvalidAllianceParametersException $ex) {
                    error_msg($ex->getMessage());
                }
            }
            if (!$finish)
            {
                echo "<form action=\"?page=$page&amp;action=create\" method=\"post\">";
                checker_init();
                tableStart("Allianz-Daten");
                echo "<tr><th>Tag / Name:</th>
                <td>
                [<input type=\"text\" name=\"alliance_tag\" size=\"6\" maxlength=\"6\" value=\"".StringUtils::encodeDBStringToPlaintext($request->request->get('alliance_tag', ''))."\" />]
                &nbsp; <input type=\"text\" name=\"alliance_name\" size=\"25\" maxlength=\"25\" value=\"".StringUtils::encodeDBStringToPlaintext($request->request->get('alliance_name', ''))."\" /></td></tr>
                <tr><td colspan=\"2\">Alle weiteren Daten könnten nach der Erstellung im Allianzmenü geändert werden.</td></tr>";
                tableEnd();
                echo "<input type=\"submit\" name=\"createsubmit\" value=\"Speichern\" /> &nbsp;
                <input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Übersicht\" /></form>";
            }
        }

        //
        // Bewerbung bei einer Allianz
        //
        elseif ($request->query->get('action') == "join")
        {
            // Bewerbungstext schreiben
            if ($request->query->getInt('alliance_id') > 0)
            {
                $alliance = $allianceRepository->getAlliance($request->query->getInt('alliance_id'));
                if ($alliance !== null) {
                    echo "<h2>Bewerbung bei der Allianz " . $alliance->toString() . "</h2>";
                    if ($alliance->acceptApplications) {
                        echo "<form action=\"?page=$page&amp;action=join\" method=\"post\">";
                        checker_init();
                        tableStart("Bewerbungstext");
                        echo "<tr><th>Nachricht:</th><td><textarea rows=\"15\" cols=\"80\" name=\"user_alliance_application\">".$alliance->applicationTemplate."</textarea><br/>".helpLink('textformat', 'Hilfe zur Formatierung')."</td>";
                        tableEnd();
                        echo "<input type=\"hidden\" name=\"user_alliance_id\" value=\"".$alliance->id."\" />";
                        echo "<input type=\"submit\" name=\"submitapplication\" value=\"Senden\" />&nbsp;<input type=\"button\" onclick=\"document.location='?page=alliance&action=join'\" value=\"Zurück\" /></form>";
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
            elseif ($request->request->has('submitapplication') && checker_verify())
            {
                echo "<h2>Bewerbung abschicken</h2>";

                $allianceId = $request->request->getInt('user_alliance_id');
                if ($request->request->get('user_alliance_application', '') != '')
                {
                    $allianceManagementService->submitApplication(
                        $allianceId,
                        $cu->id,
                        $request->request->get('user_alliance_application')
                    );

                    $alliance = $allianceRepository->getAlliance($allianceId);
                    success_msg("Deine Bewerbung bei der Allianz " . $alliance->toString() . " wurde gespeichert! Die Allianzleitung wurde informiert und wird deine Bewerbung ansehen.");
                    echo "<input value=\"Übersicht\" type=\"button\" onclick=\"document.location='?page=$page'\" />";
                }
                else
                {
                    error_msg("Du musst einen Bewerbungstext eingeben!");
                    echo "<input value=\"Zurück\" type=\"button\" onclick=\"document.location='?page=$page&action=join&alliance_id=".$allianceId."'\" />";
                }
            }
            // Allianzauswahl anzeigen
            else
            {
                echo "<h2>Allianz w&auml;hlen</h2>
                Nicht alle Allianzen akzeptieren jederzeit eine Bewerbung. <br/>
                Im Folgenden findest du eine Liste der Allianzen die momentan Bewerbungen akzeptieren:<br/><br/>";
                $alliances = $allianceRepository->findOpen();
                if (count($alliances) > 0)
                {
                    tableStart("","400"," align=\"center\"");
                    echo "<tr>
                            <th>Tag</th>
                            <th>Name</th>
                            <th>Mitglieder</th>
                            <th style=\"width:100px;\">Aktionen</th>
                        </tr>";
                    foreach ($alliances as $alliance)
                    {
                        echo "<tr><td>".$alliance->tag."</td>
                        <td>".$alliance->name."</td>
                        <td>".$allianceRepository->countUsers($alliance->id)."</td>
                        <td><a href=\"?page=alliance&amp;info_id=".$alliance->id."\">Info</a>";
                        echo "&nbsp;<a href=\"?page=$page&action=join&alliance_id=".$alliance->id."\">Bewerben</a>";
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
                    error_msg("Es gibt im Moment keine Allianzen denen man beitreten könnte!");
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
                Angriffen oder r&auml;chen dich wenn du angegriffen worden bist. Trete einer Allianz bei oder gründe selber eine Allianz.<br/><br/>";
            echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=join';\" value=\"Einer Allianz beitreten\" />&nbsp;&nbsp;&nbsp;";
            echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=create';\" value=\"Eine Allianz gründen\" />";
        }

    }
}
else {
    echo "Allianzen sind zur Zeit deaktiviert.";
}
