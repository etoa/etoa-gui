<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\DB\DatabaseMigrationService;
use EtoA\Support\DB\SchemaMigrationRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Routing\Annotation\Route;

class DatabaseMigrationController extends AbstractAdminController
{
    public function __construct(
        private DatabaseMigrationService $databaseMigrationService,
        private SchemaMigrationRepository $schemaMigrationRepository,
        private LockFactory $lockFactory,
        private LogRepository $logRepository
    ) {
    }

    #[Route("/admin/db/migration", name: "admin.db.migration")]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function overview(): Response
    {
        return $this->render('admin/database/migrations.html.twig', [
            'data' => $this->schemaMigrationRepository->getMigrations(),
            'pending' => $this->databaseMigrationService->getPendingMigrations(),
        ]);
    }

    #[Route("/admin/db/migration/migrate", name: "admin.db.migrate", methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
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
