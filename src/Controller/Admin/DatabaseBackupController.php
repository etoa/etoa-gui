<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\DB\DatabaseBackupService;
use EtoA\Support\StringUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Routing\Annotation\Route;

class DatabaseBackupController extends AbstractAdminController
{
    public function __construct(
        private LockFactory $lockFactory,
        private DatabaseBackupService $databaseBackupService,
        private ConfigurationService $config,
        private LogRepository $logRepository
    ) {
    }

    #[Route("/admin/db/backups", name: "admin.db.backups")]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function backups(): Response
    {
        $dir = $this->databaseBackupService->getBackupDir();
        $backupDir = null;
        $backups = [];
        if ($dir !== null) {
            $backupDir = realpath($dir);

            $backupFiles = $this->databaseBackupService->getBackupImages($dir, false);
            foreach ($backupFiles as $f) {
                $backups[] = [
                    'filename' => $f,
                    'date' => substr($f, strpos($f, '-') + 1, 16),
                    'createdAt' => StringUtils::formatDate(filectime($dir . '/' . $f)),
                    'size' => StringUtils::formatBytes(filesize($dir . '/' . $f)),
                    'downloadLink' => createDownloadLink($dir . '/' . $f),
                ];
            }
        }

        return $this->render('admin/database/backups.html.twig', [
            'backupDir' => $backupDir,
            'backups' => $backups,
        ]);
    }

    #[Route("/admin/db/backups/settings", name: "admin.db.backup.settings", methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function backupSettings(Request $request): RedirectResponse
    {
        $this->config->set("backup_dir", $request->request->get('backup_dir'));
        $this->config->set("backup_retention_time", $request->request->get('backup_retention_time'));
        $this->config->set("backup_use_gzip", $request->request->get('backup_use_gzip'));

        $this->addFlash('success', 'Einstellungen gespeichert');

        return $this->redirectToRoute('admin.db.backups');
    }

    #[Route("/admin/db/backup", name: "admin.db.backup", methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function create(): RedirectResponse
    {
        $lock = $this->lockFactory->createLock('db');

        try {
            $lock->acquire(true);

            // Do the backup
            $dir = $this->databaseBackupService->getBackupDir();
            $gzip = $this->config->getBoolean('backup_use_gzip');
            $log = $this->databaseBackupService->backupDB($dir, $gzip);

            // Write log
            $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "[b]Datenbank-Backup[/b]\n" . $log);

            // Show message
            $this->addFlash('success', $log);
        } catch (\Exception $e) {
            // Write log
            $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::ERROR, "[b]Datenbank-Backup[/b]\nFehler: " . $e->getMessage());

            // Show message
            $this->addFlash('error', 'Beim Ausführen des Backup-Befehls trat ein Fehler auf: ' . $e->getMessage());
        } finally {
            $lock->release();
        }

        return $this->redirectToRoute('admin.db.backups');
    }

    #[Route("/admin/db/restore/{restorePoint}", name: "admin.db.restore", methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function restore(string $restorePoint): RedirectResponse
    {
        $dir = $this->databaseBackupService->getBackupDir();
        $gzip = $this->config->getBoolean('backup_use_gzip');

        $lock = $this->lockFactory->createLock('db');

        try {
            $lock->acquire(true);

            // Backup current database
            $log = 'Anlegen einer Sicherungskopie: ';
            $log .= $this->databaseBackupService->backupDB($dir, $gzip);

            // Restore database
            $log .= "\nWiederherstellen der Datenbank: ";
            $log .= $this->databaseBackupService->restoreDB($dir, $restorePoint);

            // Write log
            $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "[b]Datenbank-Restore[/b]\n" . $log);

            // Show message
            $this->addFlash('success', 'Das Backup ' . $restorePoint . ' wurde wiederhergestellt und es wurde eine Sicherungskopie der vorherigen Daten angelegt!');
        } catch (\Exception $e) {
            // Write log
            $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::ERROR, "[b]Datenbank-Restore[/b]\nDie Datenbank konnte nicht vom Backup [b]" . $restorePoint . "[/b] aus dem Verzeichnis [b]" . $dir . "[/b] wiederhergestellt werden: " . $e->getMessage());

            // Show message
            $this->addFlash('error', 'Beim Ausf&uuml;hren des Restore-Befehls trat ein Fehler auf! ' . $e->getMessage());
        } finally {
            $lock->release();
        }

        return $this->redirectToRoute('admin.db.backups');
    }
}
