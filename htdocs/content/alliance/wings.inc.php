<?PHP

use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Alliance\AllianceSearch;
use EtoA\Alliance\AllianceWingService;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\StringUtils;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var AllianceRepository $allianceRepository */
$allianceRepository = $app[AllianceRepository::class];
/** @var AllianceWingService $allianceWingService */
$allianceWingService = $app[AllianceWingService::class];

/** @var \EtoA\Alliance\Alliance $alliance */
/** @var \EtoA\Alliance\UserAlliancePermission $userAlliancePermission */

if ($config->getBoolean('allow_wings') && $userAlliancePermission->checkHasRights(AllianceRights::WINGS, $page)) {
    echo "<h2>Wings verwalten</h2>";

    if (isset($_GET['remove']) && intval($_GET['remove']) > 0) {
        $wing = $allianceRepository->getAlliance(intval($_GET['remove']));
        if ($wing !== null && $allianceWingService->removeWing($alliance, $wing))
            success_msg("Wing entfernt!");
        else
            error_msg("Wing konnte nicht entfernt werden!");
    }

    if (isset($_GET['cancelreq']) && intval($_GET['cancelreq']) > 0) {
        $wingRequestAlliance = $allianceRepository->getAlliance(intval($_GET['cancelreq']));
        if ($wingRequestAlliance !== null && $allianceWingService->cancelWingRequest($alliance, $wingRequestAlliance))
            success_msg("Anfrage zurückgezogen!");
        else
            error_msg("Anfrage konnte nicht zurückgezogen werden!");
    }

    if (isset($_POST['add_wing_id']) && intval($_POST['add_wing_id']) > 0) {
        $wingRequestAlliance = $allianceRepository->getAlliance(intval($_POST['add_wing_id']));
        if ($wingRequestAlliance !== null && $allianceWingService->addWingRequest($alliance, $wingRequestAlliance))
            success_msg("Winganfrage hinzugefügt. Der Gründer der angefragten Allianz wurde informiert!");
        else
            error_msg("Es ist bereits eine Anfrage vorhanden oder die Allianz ist schon ein Wing einer anderen Allianz!");
    }

    if (isset($_POST['grant_req']) && $alliance->motherRequest > 0) {
        $motherRequestAlliance = $allianceRepository->getAlliance($alliance->motherRequest);
        if ($motherRequestAlliance !== null && $allianceWingService->acceptWingRequest($motherRequestAlliance, $alliance))
            success_msg("Winganfrage bestätigt!");
        else
            error_msg("Es ist ein Problem aufgetreten!");
    }

    if (isset($_POST['revoke_req']) && $alliance->motherRequest > 0) {
        $motherRequestAlliance = $allianceRepository->getAlliance($alliance->motherRequest);
        if ($motherRequestAlliance !== null && $allianceWingService->declineWingRequest($motherRequestAlliance, $alliance))
            success_msg("Winganfrage zurückgewiesen!");
        else
            error_msg("Es ist ein Problem aufgetreten!");
    }

    if ($alliance->motherRequest > 0) {
        $motherRequestAlliance = $allianceRepository->getAlliance($alliance->motherRequest);
        echo "<form action=\"?page=$page&amp;action=wings\" method=\"post\">";
        iBoxStart("Wing-Anfrage");
        echo "Die Allianz " . $motherRequestAlliance->nameWithTag . " will diese Allianz als Wing hinzufügen.<br/><br/>";
        echo "<input type=\"submit\" name=\"grant_req\" value=\"Bestätigen\" /> ";
        echo "<input type=\"submit\" name=\"revoke_req\" value=\"Zurückweisen\" /> ";
        iBoxEnd();
        echo "</form>";
    }

    if ($alliance->motherId > 0) {
        $motherAlliance = $allianceRepository->getAlliance($alliance->motherId);
        echo "<form action=\"?page=$page&amp;action=wings\" method=\"post\">";
        iBoxStart("Wing");
        echo "Diese Allianz ist ein Wing von " . $motherAlliance->nameWithTag . ".<br/><br/>";
        iBoxEnd();
        echo "</form>";
    }

    $wingAlliances = $allianceRepository->searchAlliances(AllianceSearch::create()->motherId($alliance->id));
    if (count($wingAlliances) > 0) {
        tableStart("Wings");
        echo "<tr>
            <th>Name</th>
            <th>Punkte</th>
            <th>Mitglieder</th>
            <th>Punkteschnitt</th>
            <th>Aktionen</th>
        </tr>";
        foreach ($wingAlliances as $wingAlliance) {
            echo "<tr>
            <td>" . $wingAlliance->nameWithTag . "</td>
            <td>" . StringUtils::formatNumber($wingAlliance->points) . "</td>
            <td>" . $wingAlliance->memberCount . "</td>
            <td>" . StringUtils::formatNumber($wingAlliance->averagePoints) . "</td>
            <td>
                <a href=\"?page=alliance&amp;id=" . $wingAlliance->id . "\">Allianzseite</a> &nbsp;
                <a href=\"?page=alliance&amp;action=wings&amp;remove=" . $wingAlliance->id . "\" onclick=\"return confirm('Wingzuordnung wirklich aufheben?')\">Entfernen</a>
            </td>
            </tr>";
        }
        echo "</td></tr>";
        tableEnd();
    }

    $wingRequestAlliances = $allianceRepository->searchAlliances(AllianceSearch::create()->motherRequestAllianceId($alliance->id));
    if (count($wingRequestAlliances) > 0) {
        tableStart("Wing-Anfragen");
        echo "<tr>
            <th>Name</th>
            <th>Punkte</th>
            <th>Mitglieder</th>
            <th>Punkteschnitt</th>
            <th>Aktionen</th>
        </tr>";
        foreach ($wingRequestAlliances as $wingRequestAlliance) {
            echo "<tr>
            <td>" . $wingRequestAlliance->nameWithTag . "</td>
            <td>" . StringUtils::formatNumber($wingRequestAlliance->points) . "</td>
            <td>" . $wingRequestAlliance->memberCount . "</td>
            <td>" . StringUtils::formatNumber($wingRequestAlliance->averagePoints) . "</td>
            <td>
                <a href=\"?page=alliance&amp;id=" . $wingRequestAlliance->id . "\">Allianzseite</a> &nbsp;
                <a href=\"?page=alliance&amp;action=wings&amp;cancelreq=" . $wingRequestAlliance->id . "\" onclick=\"return confirm('Anftage wirklich zurückziehen?')\">Zurückziehen</a>
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
        if ($allianceId !== $alliance->id && !isset($wingAlliances[$allianceId]))
            echo "<option value=\"$allianceId\">$allianceNameWithTag</option>";
    }
    echo "</select> &nbsp;
    <input type=\"submit\" name=\"add_wing\" value=\"Hinzufügen\" /> ";
    iBoxEnd();
    echo "</form>
    <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
}
