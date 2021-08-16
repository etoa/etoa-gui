<?PHP

use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\DB\SchemaMigrationRepository;
use EtoA\Support\DB\DatabaseMigrationService;

/** @var SchemaMigrationRepository $schemaMigrationRepository */
$schemaMigrationRepository = $app[SchemaMigrationRepository::class];

/** @var DatabaseMigrationService $databaseMigrationService */
$databaseMigrationService = $app[DatabaseMigrationService::class];
/** @var LogRepository $logRepository */
$logRepository = $app[LogRepository::class];

$successMessage = null;
$errorMessage = null;
if (isset($_POST['migrate'])) {
    $mtx = new Mutex();

    try {
        $mtx->acquire();

        // Migrate schema
        $cnt = $databaseMigrationService->migrate();
        if ($cnt == 0) {
            $successMessage = 'Datenbankschema ist bereits aktuell!';
        } else {
            $successMessage = 'Datenbankschema wurde aktualisiert!';
        }

        $mtx->release();
    } catch (Exception $e) {
        // Release mutex
        $mtx->release();

        // Write log
        $logRepository->add(LogFacility::SYSTEM, LogSeverity::ERROR, "[b]Datenbank-Migration fehlgeschlagen[/b]\nFehler: ".$e->getMessage());

        // Show message
        $errorMessage = 'Beim Ausführen des Migration-Befehls trat ein Fehler auf: ' . $e->getMessage();
    }
}

echo $twig->render('admin/database/migrations.html.twig', [
    'data' => $schemaMigrationRepository->getMigrations(),
    'pending' => $databaseMigrationService->getPendingMigrations(),
    'successMessage' => $successMessage,
    'errorMessage' => $errorMessage,
]);
exit();
