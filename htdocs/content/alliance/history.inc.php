<?PHP

use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceRights;

/** @var mixed[] $arr alliance data */

if (Alliance::checkActionRights(AllianceRights::HISTORY)) {
    /** @var AllianceHistoryRepository $allianceHistoryRepository */
    $allianceHistoryRepository = $app[AllianceHistoryRepository::class];

    echo "<h2>Allianzgeschichte</h2>";
    tableStart("Geschichtsdaten");
    echo "<tr><th style=\"width:120px;\">Datum / Zeit</th><th>Ereignis</th></tr>";
    $entries = $allianceHistoryRepository->findForAlliance((int) $arr['alliance_id']);
    foreach ($entries as $entry) {
        echo "<tr><td>" . date("d.m.Y H:i", $entry->timestamp) . "</td><td>" . text2html($entry->text) . "</td></tr>";
    }
    tableEnd();
    echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
}
