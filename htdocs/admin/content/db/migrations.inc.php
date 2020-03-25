<?PHP

$successMessage = null;
$errorMessage = null;
if (isset($_POST['migrate'])) {
    try {
        $mtx = new Mutex();
        $mtx->acquire();

        // Migrate schema
        $cnt = DBManager::getInstance()->migrate();
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
        Log::add(Log::F_SYSTEM, Log::ERROR, "[b]Datenbank-Migration fehlgeschlagen[/b]\nFehler: ".$e->getMessage());

        // Show message
        $errorMessage = 'Beim Ausführen des Migration-Befehls trat ein Fehler auf: ' . $e->getMessage();
    }
}

$data = DBManager::getInstance()->getArrayFromTable(DBManager::SCHEMA_MIGRATIONS_TABLE,["version", "date"],"version");
$pending = DBManager::getInstance()->getPendingMigrations();

echo $twig->render('admin/database/migrations.html.twig', [
    'data' => $data,
    'pending' => $pending,
    'successMessage' => $successMessage,
    'errorMessage' => $errorMessage,
]);
exit();
