<?PHP

use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\StringUtils;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var AllianceRepository $allianceRepository */
$allianceRepository = $app[AllianceRepository::class];

/** @var Alliance $ally */

if ($config->getBoolean('allow_wings') && Alliance::checkActionRights(AllianceRights::WINGS)) {
    echo "<h2>Wings verwalten</h2>";

    if (isset($_GET['remove']) && intval($_GET['remove']) > 0) {
        if ($ally->removeWing(intval($_GET['remove'])))
            success_msg("Wing entfernt!");
        else
            error_msg("Wing konnte nicht entfernt werden!");
    }

    if (isset($_GET['cancelreq']) && intval($_GET['cancelreq']) > 0) {
        if ($ally->cancelWingRequest(intval($_GET['cancelreq'])))
            success_msg("Anfrage zurückgezogen!");
        else
            error_msg("Anfrage konnte nicht zurückgezogen werden!");
    }

    if (isset($_POST['add_wing_id']) && intval($_POST['add_wing_id']) > 0) {
        if ($ally->addWingRequest(intval($_POST['add_wing_id'])))
            success_msg("Winganfrage hinzugefügt. Der Gründer der angefragten Allianz wurde informiert!");
        else
            error_msg("Es ist bereits eine Anfrage vorhanden oder die Allianz ist schon ein Wing einer anderen Allianz!");
    }

    if (isset($_POST['grant_req']) && $ally->motherRequestId > 0) {
        if ($ally->grantWingRequest())
            success_msg("Winganfrage bestätigt!");
        else
            error_msg("Es ist ein Problem aufgetreten!");
    }

    if (isset($_POST['revoke_req']) && $ally->motherRequestId > 0) {
        if ($ally->revokeWingRequest())
            success_msg("Winganfrage zurückgewiesen!");
        else
            error_msg("Es ist ein Problem aufgetreten!");
    }

    if ($ally->motherRequestId > 0) {
        echo "<form action=\"?page=$page&amp;action=wings\" method=\"post\">";
        iBoxStart("Wing-Anfrage");
        echo "Die Allianz " . $ally->motherRequest . " will diese Allianz als Wing hinzufügen.<br/><br/>";
        echo "<input type=\"submit\" name=\"grant_req\" value=\"Bestätigen\" /> ";
        echo "<input type=\"submit\" name=\"revoke_req\" value=\"Zurückweisen\" /> ";
        iBoxEnd();
        echo "</form>";
    }

    if ($ally->motherId > 0) {
        echo "<form action=\"?page=$page&amp;action=wings\" method=\"post\">";
        iBoxStart("Wing");
        echo "Diese Allianz ist ein Wing von " . $ally->mother . ".<br/><br/>";
        iBoxEnd();
        echo "</form>";
    }

    if (count($ally->wings) > 0) {
        tableStart("Wings");
        echo "<tr>
            <th>Name</th>
            <th>Punkte</th>
            <th>Mitglieder</th>
            <th>Punkteschnitt</th>
            <th>Aktionen</th>
        </tr>";
        foreach ($ally->wings as $wid => $wdata) {
            echo "<tr>
            <td>" . $wdata . "</td>
            <td>" . StringUtils::formatNumber($wdata->points) . "</td>
            <td>" . $wdata->memberCount . "</td>
            <td>" . StringUtils::formatNumber($wdata->avgPoints) . "</td>
            <td>
                <a href=\"?page=alliance&amp;id=" . $wid . "\">Allianzseite</a> &nbsp;
                <a href=\"?page=alliance&amp;action=wings&amp;remove=" . $wid . "\" onclick=\"return confirm('Wingzuordnung wirklich aufheben?')\">Entfernen</a>
            </td>
            </tr>";
        }
        echo "</td></tr>";
        tableEnd();
    }

    if (count($ally->wingRequests) > 0) {
        tableStart("Wing-Anfragen");
        echo "<tr>
            <th>Name</th>
            <th>Punkte</th>
            <th>Mitglieder</th>
            <th>Punkteschnitt</th>
            <th>Aktionen</th>
        </tr>";
        foreach ($ally->wingRequests as $wid => $wdata) {
            echo "<tr>
            <td>" . $wdata . "</td>
            <td>" . StringUtils::formatNumber($wdata->points) . "</td>
            <td>" . $wdata->memberCount . "</td>
            <td>" . StringUtils::formatNumber($wdata->avgPoints) . "</td>
            <td>
                <a href=\"?page=alliance&amp;id=" . $wid . "\">Allianzseite</a> &nbsp;
                <a href=\"?page=alliance&amp;action=wings&amp;cancelreq=" . $wid . "\" onclick=\"return confirm('Anftage wirklich zurückziehen?')\">Zurückziehen</a>
            </td>
            </tr>";
        }
        echo "</td></tr>";
        tableEnd();
    }

    echo "<form action=\"?page=$page&amp;action=wings\" method=\"post\">";
    iBoxStart("Allianz als Wing hinzufügen");
    echo "Allianz wählen: <select name=\"add_wing_id\">";
    foreach ($allianceRepository->getAllianceNamesWithTags() as $allianceId => $allianceNameWithTag) {
        if ($allianceId != $ally->id && !isset($ally->wings[$allianceId]))
            echo "<option value=\"$allianceId\">$allianceNameWithTag</option>";
    }
    echo "</select> &nbsp;
    <input type=\"submit\" name=\"add_wing\" value=\"Hinzufügen\" /> ";
    iBoxEnd();
    echo "</form>
    <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
}
