<?PHP

use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Support\BBCodeUtils;

/** @var \EtoA\Alliance\Alliance $alliance */
/** @var \EtoA\Alliance\UserAlliancePermission $userAlliancePermission */

if ($userAlliancePermission->checkHasRights(AllianceRights::HISTORY, $page)) {
    /** @var AllianceHistoryRepository $allianceHistoryRepository */
    $allianceHistoryRepository = $app[AllianceHistoryRepository::class];

    echo "<h2>Allianzgeschichte</h2>";
    tableStart("Geschichtsdaten");
    echo "<tr><th style=\"width:120px;\">Datum / Zeit</th><th>Ereignis</th></tr>";
    $entries = $allianceHistoryRepository->findForAlliance($alliance->id);
    foreach ($entries as $entry) {
        echo "<tr><td>" . date("d.m.Y H:i", $entry->timestamp) . "</td><td>" . BBCodeUtils::toHTML($entry->text) . "</td></tr>";
    }
    tableEnd();
    echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
}
