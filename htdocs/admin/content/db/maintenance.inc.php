<?PHP

use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\DB\DatabaseManagerRepository;

/** @var DatabaseManagerRepository */
$databaseManager = $app[DatabaseManagerRepository::class];
/** @var LogRepository $logRepository */
$logRepository = $app[LogRepository::class];

$action = $_GET['action'] ?? null;
$subTitle = null;
$successMessage = null;

// Datenbanktabellen optimieren
if ($action === 'optimize') {
    $subTitle = 'Optimierungsbericht';
    $result = $databaseManager->optimizeTables();
    $logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, count($result) . " Tabellen wurden manuell optimiert!");
}
// Datenbanktabellen analysieren
elseif ($action === 'analyze') {
    $subTitle = 'Analysebericht';
    $successMessage = 'Tabellen deren Analysestatus bereits aktuell ist werden nicht angezeigt!';
    $result = $databaseManager->analyzeTables();
}
// Datenbanktabellen prüfen
elseif ($action === 'check') {
    $subTitle = 'Überprüfungsbericht';
    $successMessage = 'Es werden nur Tabellen mit einem Status != OK angezeigt!';
    $result = $databaseManager->checkTables();
}
// Datenbanktabellen reparieren
elseif ($action === 'repair') {
    $subTitle = 'Reparaturbericht';
    $result = $databaseManager->repairTables();
    $logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, count($result) . " Tabellen wurden manuell repariert!");
} else {
    throw new \InvalidArgumentException('Invalid action: ' . $action);
}

// Fields
$fields = count($result) > 0 ? array_keys($result[0]) : [];

// Records
$rows = [];
foreach ($result as $arr) {
    // When checking, filter all rows with OK status
    if ($action === 'check' && isset($arr['Msg_text']) && $arr['Msg_text'] === 'OK') {
        continue;
    }

    // Filter all rows which are already up do date
    if ($action === 'analyze' && isset($arr['Msg_text']) && $arr['Msg_text'] === 'Table is already up to date') {
        continue;
    }

    $rows[] = $arr;
}

echo $twig->render('admin/database/maintenance.html.twig', [
    'rows' => $rows,
    'fields' => $fields,
    'subTitle' => $subTitle,
    'successMessage' => $successMessage,
]);
exit();
