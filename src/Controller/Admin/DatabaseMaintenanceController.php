<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\DB\DatabaseManagerRepository;
use EtoA\Support\StringUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DatabaseMaintenanceController extends AbstractAdminController
{
    public function __construct(
        private readonly DatabaseManagerRepository $databaseManager,
        private readonly LogRepository             $logRepository
    )
    {
    }

    #[Route("/admin/db/", name: "admin.db")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function overview(Request $request): Response
    {
        $st = $this->databaseManager->getGlobalStatus();

        $uts = $st['uptime'];
        $utm = round($uts / 60);
        $uth = round($uts / 3600);

        $sort = $request->query->get('sort', 'size');
        $tr = [];
        $ts = [];
        $tn = [];
        $engines = [];
        $rows = $datal = 0;
        foreach ($this->databaseManager->getTableStatus() as $arr) {
            $rows += (int)$arr['Rows'];
            $datal += (int)$arr['Data_length'] + (int)$arr['Index_length'];
            $tr[$arr['Name']] = $arr['Rows'];
            $ts[$arr['Name']] = (int)$arr['Data_length'] + (int)$arr['Index_length'];
            $tn[$arr['Name']] = $arr['Name'];
            $engines[$arr['Name']] = $arr['Engine'];
        }

        $dbStats = [];
        if ($sort === 'rows') {
            arsort($tr);
            foreach ($tr as $k => $v) {
                $dbStats[] = [
                    'name' => $tn[$k],
                    'size' => StringUtils::formatBytes($ts[$k]),
                    'entries' => StringUtils::formatNumber((int)$tr[$k]),
                    'engine' => $engines[$k],
                ];
            }
        } elseif ($sort === 'name') {
            asort($tn);
            foreach ($tn as $k => $v) {
                $dbStats[] = [
                    'name' => $tn[$k],
                    'size' => StringUtils::formatBytes($ts[$k]),
                    'entries' => StringUtils::formatNumber((int)$tr[$k]),
                    'engine' => $engines[$k],
                ];
            }
        } elseif ($sort === 'engine') {
            asort($engines);
            foreach ($engines as $k => $v) {
                $dbStats[] = [
                    'name' => $tn[$k],
                    'size' => StringUtils::formatBytes($ts[$k]),
                    'entries' => StringUtils::formatNumber((int)$tr[$k]),
                    'engine' => $engines[$k],
                ];
            }
        } else {
            arsort($ts);
            foreach ($ts as $k => $v) {
                $dbStats[] = [
                    'name' => $tn[$k],
                    'size' => StringUtils::formatBytes($ts[$k]),
                    'entries' => StringUtils::formatNumber((int)$tr[$k]),
                    'engine' => $engines[$k],
                ];
            }
        }

        return $this->render('admin/database/database.html.twig', [
            'dbStats' => $dbStats,
            'dbName' => $this->databaseManager->getDatabaseName(),
            'dbRows' => StringUtils::formatNumber($rows),
            'dbSize' => StringUtils::formatBytes($datal),
            'serverUptime' => StringUtils::formatTimespan($uts),
            'serverStarted' => StringUtils::formatDate(time() - $uts),
            'bytesReceived' => StringUtils::formatBytes($st['bytes_received']),
            'bytesReceivedHour' => StringUtils::formatBytes($uth > 0 ? (int)((int)$st['bytes_received'] / $uth) : 0),
            'bytesSent' => StringUtils::formatBytes($st['bytes_sent']),
            'bytesSentHour' => StringUtils::formatBytes($uth > 0 ? (int)((int)$st['bytes_sent'] / $uth) : 0),
            'bytesTotal' => StringUtils::formatBytes($st['bytes_received'] + $st['bytes_sent']),
            'bytesTotalHour' => StringUtils::formatBytes($uth > 0 ? (int)(($st['bytes_received'] + $st['bytes_sent']) / $uth) : 0),
            'maxUsedConnections' => StringUtils::formatNumber($st['max_used_connections']),
            'abortedConnections' => StringUtils::formatNumber($st['aborted_connects']),
            'abortedConnectsHour' => StringUtils::formatNumber($uth > 0 ? (int)((int)$st['aborted_connects'] / $uth) : 0),
            'abortedClients' => StringUtils::formatNumber($st['aborted_clients']),
            'abortedClientsHour' => StringUtils::formatNumber($uth > 0 ? (int)(((int)$st['aborted_clients']) / $uth) : 0),
            'connections' => StringUtils::formatNumber($st['connections']),
            'connectionsHour' => StringUtils::formatNumber($uth > 0 ? (int)((int)$st['connections'] / $uth) : 0),
            'questions' => StringUtils::formatNumber($st['questions']),
            'avgQuestionsDay' => StringUtils::formatNumber($uth > 0 ? (int)((int)$st['questions'] / $uth * 24) : 0),
            'avgQuestionsHour' => StringUtils::formatNumber($uth > 0 ? (int)((int)$st['questions'] / $uth) : 0),
            'avgQuestionsMinute' => StringUtils::formatNumber($utm > 0 ? (int)((int)$st['questions'] / $utm) : 0),
            'avgQuestionsSecond' => StringUtils::formatNumber($uts > 0 ? (int)((int)$st['questions'] / (int)$uts) : 0),
            'slowQueries' => StringUtils::formatNumber($st['slow_queries']),
            'createdTmpDiskTables' => StringUtils::formatNumber($st['created_tmp_disk_tables']),
            'openTables' => StringUtils::formatNumber($st['open_tables']),
            'openedTables' => StringUtils::formatNumber($st['opened_tables']),
        ]);
    }

    #[Route("/admin/db/optimize", name: "admin.db.optimize")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function optimize(): Response
    {
        $result = $this->databaseManager->optimizeTables();

        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, count($result) . " Tabellen wurden manuell optimiert!");

        return $this->render('admin/database/maintenance.html.twig', [
            'rows' => $result,
            'fields' => count($result) > 0 ? array_keys($result[0]) : [],
            'subTitle' => 'Optimierungsbericht',
        ]);
    }

    #[Route("/admin/db/analyze", name: "admin.db.analyze")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function analyze(): Response
    {
        $result = $this->databaseManager->analyzeTables();

        $this->addFlash('success', 'Tabellen deren Analysestatus bereits aktuell ist werden nicht angezeigt!');

        $result = array_values(array_filter($result, fn(array $row) => $row['Msg_text'] !== 'Table is already up to date'));

        return $this->render('admin/database/maintenance.html.twig', [
            'rows' => $result,
            'fields' => count($result) > 0 ? array_keys($result[0]) : [],
            'subTitle' => 'Analysebericht',
        ]);
    }

    #[Route("/admin/db/check", name: "admin.db.check")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function check(): Response
    {
        $result = $this->databaseManager->checkTables();

        $this->addFlash('success', 'Es werden nur Tabellen mit einem Status != OK angezeigt!');

        $result = array_values(array_filter($result, fn(array $row) => $row['Msg_text'] !== 'OK'));

        return $this->render('admin/database/maintenance.html.twig', [
            'rows' => $result,
            'fields' => count($result) > 0 ? array_keys($result[0]) : [],
            'subTitle' => 'Überprüfungsbericht',
        ]);
    }

    #[Route("/admin/db/repair", name: "admin.db.repair")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
    public function repair(): Response
    {
        $result = $this->databaseManager->repairTables();
        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, count($result) . " Tabellen wurden manuell repariert!");

        return $this->render('admin/database/maintenance.html.twig', [
            'rows' => $result,
            'fields' => count($result) > 0 ? array_keys($result[0]) : [],
            'subTitle' => 'Reparaturbericht',
        ]);
    }
}
