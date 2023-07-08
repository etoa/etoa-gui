<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\DB\DatabaseBackupService;
use EtoA\Support\DB\DatabaseManagerRepository;
use EtoA\Support\DB\DatabaseMigrationService;
use EtoA\Support\DB\SchemaMigrationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DatabaseResetController extends AbstractAdminController
{
    public function __construct(
        private readonly LockFactory               $lockFactory,
        private readonly ConfigurationService      $config,
        private readonly DatabaseManagerRepository $databaseManager,
        private readonly LogRepository             $logRepository,
        private readonly DatabaseMigrationService  $databaseMigrationService,
        private readonly DatabaseBackupService     $databaseBackupService
    )
    {
    }

    #[Route("/admin/db/reset", name: "admin.db.reset")]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function reset(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            switch ($request->request->get('action')) {
                case 'drop':
                    $this->drop();

                    break;
                case 'truncate':
                    $this->truncate();

                    break;
            }
        }

        return $this->render('admin/database/reset-full.html.twig');
    }

    private function truncate(): void
    {
        $lock = $this->lockFactory->createLock('db');

        $persistentTables = fetchJsonConfig("persistent-tables.conf");

        try {
            // Acquire mutex
            $lock->acquire(true);

            // Do the backup
            $dir = $this->databaseBackupService->getBackupDir();
            $gzip = $this->config->getBoolean('backup_use_gzip');
            $this->databaseBackupService->backupDB($dir, $gzip);

            $tables = $this->databaseManager->getTables();
            $emptyTables = [];
            foreach ($tables as $table) {
                if (!in_array($table, $persistentTables['definitions'], true) && $table !== SchemaMigrationRepository::SCHEMA_MIGRATIONS_TABLE) {
                    $emptyTables[] = $table;
                }
            }

            if (count($emptyTables) > 0) {
                $this->databaseManager->truncateTables($emptyTables);

                $this->addFlash('info', 'Leere Tabellen: ' . implode(', ', $emptyTables));
            }

            // Restore default config
            $cr = $this->config->restoreDefaults();
            $this->config->reload();

            $this->addFlash('success', count($emptyTables) . " Tabellen geleert, $cr Einstellungen auf Standard zurückgesetzt!");
        } catch (\Exception $e) {
            $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::ERROR, "[b]Datenbank-Reset fehlgeschlagen[/b]\nFehler: " . $e->getMessage());

            $this->addFlash('error', 'Beim Ausführen des Resaet-Befehls trat ein Fehler auf: ' . $e->getMessage());
        } finally {
            $lock->release();
        }
    }

    private function drop(): void
    {
        $lock = $this->lockFactory->createLock('db');

        try {
            // Acquire mutex
            $lock->acquire(true);

            // Do the backup
            $dir = $this->databaseBackupService->getBackupDir();
            $gzip = $this->config->getBoolean('backup_use_gzip');
            $this->databaseBackupService->backupDB($dir, $gzip);

            // Drop tables
            $tableCount = $this->databaseManager->dropAllTables();

            // Load schema
            $this->databaseMigrationService->migrate();

            // Load config default
            $this->config->restoreDefaults();
            $this->config->reload();

            $this->addFlash('success', $tableCount . ' Tabellen gelöscht, Datenbankschema neu initialisiert!');
        } catch (\Exception $e) {
            $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::ERROR, "[b]Datenbank-Reset fehlgeschlagen[/b]\nFehler: " . $e->getMessage());

            $this->addFlash('error', 'Beim Ausführen des Resaet-Befehls trat ein Fehler auf: ' . $e->getMessage());
        } finally {
            $lock->release();
        }
    }
}
