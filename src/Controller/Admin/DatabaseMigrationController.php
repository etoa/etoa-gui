<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\DB\DatabaseMigrationService;
use EtoA\Support\DB\SchemaMigrationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Routing\Annotation\Route;

class DatabaseMigrationController extends AbstractController
{
    private DatabaseMigrationService $databaseMigrationService;
    private SchemaMigrationRepository $schemaMigrationRepository;
    private LockFactory $lockFactory;
    private LogRepository $logRepository;

    public function __construct(DatabaseMigrationService $databaseMigrationService, SchemaMigrationRepository $schemaMigrationRepository, LockFactory $lockFactory, LogRepository $logRepository)
    {
        $this->databaseMigrationService = $databaseMigrationService;
        $this->schemaMigrationRepository = $schemaMigrationRepository;
        $this->lockFactory = $lockFactory;
        $this->logRepository = $logRepository;
    }

    /**
     * @Route("/admin/db/migration", name="admin.db.migration")
     */
    public function overview(): Response
    {
        return $this->render('admin/database/migrations.html.twig', [
            'data' => $this->schemaMigrationRepository->getMigrations(),
            'pending' => $this->databaseMigrationService->getPendingMigrations(),
        ]);
    }

    /**
     * @Route("/admin/db/migration/migrate", methods={"POST"}, name="admin.db.migrate")
     */
    public function migrate(): Response
    {
        $lock = $this->lockFactory->createLock('db');

        try {
            $lock->acquire(true);

            // Migrate schema
            $cnt = $this->databaseMigrationService->migrate();
            if ($cnt === 0) {
                $this->addFlash('success', 'Datenbankschema ist bereits aktuell!');
            } else {
                $this->addFlash('success', 'Datenbankschema wurde aktualisiert!');
            }
        } catch (\Exception $e) {
            // Write log
            $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::ERROR, "[b]Datenbank-Migration fehlgeschlagen[/b]\nFehler: ".$e->getMessage());

            // Show message
            $this->addFlash('error', 'Beim Ausführen des Migration-Befehls trat ein Fehler auf: ' . $e->getMessage());
        } finally {
            $lock->release();
        }

        return $this->redirectToRoute('admin.db.migration');
    }
}
