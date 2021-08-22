<?PHP

use EtoA\Support\StringUtils;
use EtoA\User\UserSittingRepository;

echo "<h1>Sitting: Laufende Sitteraccounts</h1>";

/** @var UserSittingRepository $userSittingRepository */
$userSittingRepository = $app[UserSittingRepository::class];
$entries = $userSittingRepository->getActiveSittingEntries();
if (count($entries) > 0) {
    echo "<table class=\"tb\" width=\"100%\">";
    echo "<tr><th class=\"tbltitle\">User</th>
            <th class=\"tbltitle\">Sitter</th>
            <th class=\"tbltitle\">Von</th>
            <th class=\"tbltitle\">Bis</th>
            </tr>";
    foreach ($entries as $entry) {
        echo "<tr>";
        echo "<td>" . $entry->userNick . "</td>";
        echo "<td>" . $entry->sitterNick . "</td>";
        echo "<td>" . StringUtils::formatDate($entry->dateFrom) . "</td><td>" . StringUtils::formatDate($entry->dateTo) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<i>Keine Datensätze vorhanden!</i>";
}
