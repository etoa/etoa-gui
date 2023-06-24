<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\AdminSessionManager;
use EtoA\Alliance\AlliancePointsRepository;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseRepository;
use EtoA\Log\LogCleanup;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Message\MessageRepository;
use EtoA\Message\MessageService;
use EtoA\Message\ReportCleanup;
use EtoA\Message\ReportRepository;
use EtoA\Missile\MissileRepository;
use EtoA\Ranking\PointsService;
use EtoA\Ship\ShipRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\User\UserPointsRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserService;
use EtoA\User\UserSessionManager;
use EtoA\User\UserSessionRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DatabaseCleanupController extends AbstractAdminController
{
    public function __construct(
        private MessageRepository $messageRepository,
        private ReportRepository $reportRepository,
        private LogRepository $logRepository,
        private UserPointsRepository $userPointsRepository,
        private AlliancePointsRepository $alliancePointsRepository,
        private UserService $userService,
        private UserRepository $userRepository,
        private ShipRepository $shipRepository,
        private DefenseRepository $defenseRepository,
        private BuildingRepository $buildingRepository,
        private TechnologyRepository $technologyRepository,
        private MissileRepository $missileRepository,
        private ConfigurationService $config,
        private UserSessionRepository $userSessionRepository,
        private PointsService $pointsService,
        private MessageService $messageService,
        private UserSessionManager $userSessionManager,
        private AdminSessionManager $adminSessionManager,
        private LogCleanup $logCleanup,
        private ReportCleanup $reportCleanup,
    ) {
    }

    #[Route('/admin/db/cleanup', name: 'admin.db.cleanup')]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
    public function cleanup(Request $request): Response
    {
        $messageDays = $this->daysBuilder([1, 7, 14, 21, 28], $this->config->getInt('messages_threshold_days'));
        $messageDeletedDays = $this->daysBuilder([7, 14, 21, 28, 42], $this->config->param1Int('messages_threshold_days'));
        $reportsDays = $this->daysBuilder([1, 7, 14, 21, 28], $this->config->getInt('reports_threshold_days'));
        $reportsDeletedDays = $this->daysBuilder([7, 14, 21, 28, 42], $this->config->param1Int('reports_threshold_days'));
        $logDays = $this->daysBuilder([7, 14, 21, 28], $this->config->getInt('log_threshold_days'));

        $message = '';
        if ($request->isMethod('POST')) {
            $message = "Clean-Up wird durchgeführt...<br/>";
            $all = $request->request->has('submit_cleanup_all');

            // Log cleanup
            if ($request->request->has('cl_log') || $all) {
                $nr = $this->logCleanup->cleanup($request->request->getInt('log_day') * 24 * 3600);
                $message .= $nr . " Logs wurden gelöscht!<br/>";
            }

            // Session-Log cleanup
            if ($request->request->has('cl_sesslog') || $all) {
                $sessionThreshold = $request->request->getInt('sess_log_day') * 24 * 3600;
                $nr = $this->userSessionManager->cleanupLogs($sessionThreshold);
                $nr += $this->adminSessionManager->cleanupLogs($sessionThreshold);
                $message .= $nr . " Session-Logs wurden gelöscht!<br/>";
            }

            /* Message cleanup */
            if ($request->request->has('cl_msg') || $all) {
                if ($request->request->getBoolean('only_deleted')) {
                    $nr = $this->messageService->removeOld($request->request->getInt('message_deleted_day') * 24 * 3600, true);
                } else {
                    $nr = $this->messageService->removeOld($request->request->getInt('message_day') * 24 * 3600);
                }
                $message .= $nr . " Nachrichten wurden gelöscht!<br/>";
            }

            /* Reports cleanup */
            if ($request->request->has('cl_report') || $all) {
                if ($request->request->getBoolean('only_deleted_reports')) {
                    $nr = $this->reportCleanup->cleanup($request->request->getInt('report_deleted_day') * 24 * 3600, true);
                } else {
                    $nr = $this->reportCleanup->cleanup($request->request->getInt('report_day') * 24 * 3600);
                }

                $message .= $nr . " Berichte wurden gelöscht!<br/>";
            }

            // User-Point-History
            if ($request->request->has('cl_points') || $all) {
                $userThreshold = $request->request->getInt('del_user_points') * 24 * 3600;
                $nr = $this->pointsService->cleanupUserPoints($userThreshold);
                $message .= $nr . " Benutzerpunkte-Logs und ";
                $nr = $this->pointsService->cleanupAlliancePoints($userThreshold);
                $message .= $nr . " Allianzpunkte-Logs wurden gelöscht!<br/>";
            }

            // Inactive and delete jobs
            if ($request->request->has('cl_inactive') || $all) {
                $message .= $this->userService->removeInactive() . " inaktive User wurden gelöscht!<br/>";
                $this->userService->informLongInactive();
                $message .= $this->userService->removeDeleted(true) . " gelöschte User wurden endgültig gelöscht!<br/>";
            }

            /* object lists */
            if ($request->request->has('cl_objlist') || $all) {
                $nr = $this->shipRepository->cleanUp();
                $message .= $nr . " leere Schiffdaten wurden gelöscht!<br/>";
                $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "$nr leere Schiffsdatensätze wurden manuell gelöscht!");

                $nr = $this->defenseRepository->cleanUp();
                $message .= $nr . " leere Verteidigungsdaten wurden gelöscht!<br/>";
                $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "$nr leere Verteidigungsdatensätze wurden manuell gelöscht!");

                $message .= $this->missileRepository->deleteEmpty() . " leere Raketendatendaten wurden gelöscht!<br/>";
                $message .= $this->buildingRepository->deleteEmpty() . " leere Gebäudedaten wurden gelöscht!<br/>";
                $message .= $this->technologyRepository->deleteEmpty() . " leere Forschungsdaten wurden gelöscht!<br/>";
            }

            $message .= "Clean-Up fertig!<br/><br/>";
        }

        return $this->render('admin/database/cleanup.html.twig', [
            'messageNotArchivedCount' => $this->messageRepository->countNotArchived(),
            'messageDeletedCount' => $this->messageRepository->countDeleted(),
            'reportNotArchivedCount' => $this->reportRepository->countNotArchived(),
            'reportDeletedCount' => $this->reportRepository->countDeleted(),
            'logCount' => $this->logRepository->count(),
            'sessionCount' => $this->userSessionRepository->count(),
            'userPointsCount' => $this->userPointsRepository->count(),
            'alliancePointsCount' => $this->alliancePointsRepository->count(),
            'userInactiveCount' => $this->userService->getNumInactive(),
            'userDeletedCount' => count($this->userRepository->findDeleted()),
            'shipCount' => $this->shipRepository->count(),
            'shipEmptyCount' => $this->shipRepository->countEmpty(),
            'defenseCount' => $this->defenseRepository->count(),
            'defenseEmptyCount' => $this->defenseRepository->countEmpty(),
            'buildingCount' => $this->buildingRepository->numBuildingListEntries(),
            'buildingEmptyCount' => $this->buildingRepository->countEmpty(),
            'technologyCount' => $this->technologyRepository->count(),
            'technologyEmptyCount' => $this->technologyRepository->countEmpty(),
            'missileCount' => $this->missileRepository->count(),
            'missileEmptyCount' => $this->missileRepository->countEmpty(),
            'messageDays' => $messageDays,
            'messageDeletedDays' => $messageDeletedDays,
            'reportsDays' => $reportsDays,
            'reportsDeletedDays' => $reportsDeletedDays,
            'logDays' => $logDays,
            'userInactiveConfig1' => $this->config->param1Int('user_inactive_days'),
            'userInactiveConfig2' => $this->config->param2Int('user_inactive_days'),
            'message' => $message,
        ]);
    }

    /**
     * @param list<int> $options
     * @return array<int, bool>
     */
    private function daysBuilder(array $options, int $configValue): array
    {
        $days = [];
        foreach ($options as $day) {
            $days[$day] = false;
        }

        $days[$configValue] = true;
        ksort($days);

        return $days;
    }
}
