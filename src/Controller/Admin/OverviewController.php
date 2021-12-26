<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\AdminRoleManager;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Form\Type\Admin\GameOfflineType;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Ranking\GameStatsGenerator;
use EtoA\Support\DB\DatabaseManagerRepository;
use EtoA\Text\TextRepository;
use EtoA\Universe\Cell\CellRepository;
use League\CommonMark\MarkdownConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OverviewController extends AbstractAdminController
{
    public function __construct(
        private MarkdownConverterInterface $markdown,
        private GameStatsGenerator $gameStatsGenerator,
        private DatabaseManagerRepository $databaseManager,
        private string $cacheDir,
        private ConfigurationService $config,
        private AdminRoleManager $roleManager,
        private CellRepository $cellRepository,
        private TicketRepository $ticketRepository,
        private TextRepository $textRepository
    ) {
    }

    /**
     * @Route("/admin/overview/", name="admin.overview")
     */
    public function index(): Response
    {
        $fleetBanTitle = null;
        $fleetBanText = null;
        if ($this->config->getBoolean('flightban')) {
            // Prüft, ob die Sperre schon abgelaufen ist
            if ($this->config->param1Int('flightban_time') <= time() && $this->config->param2Int('flightban_time') >= time()) {
                $flightban_time_status = "<span style=\"color:#0f0\">Aktiv</span> Es können keine Flüge gestartet werden!";
            } elseif ($this->config->param1Int('flightban_time') > time() && $this->config->param2Int('flightban_time') > time()) {
                $flightban_time_status = "Ausstehend";
            } else {
                $flightban_time_status = "<span style=\"color:#f90\">Abgelaufen</span>";
            }

            $fleetBanTitle = "Flottensperre aktiviert";
            $fleetBanText = "Die Flottensperre wurde aktiviert.<br><br><b>Status:</b> " . $flightban_time_status . "<br><b>Zeit:</b> " . date("d.m.Y H:i", $this->config->param1Int('flightban_time')) . " - " . date("d.m.Y H:i", $this->config->param2Int('flightban_time')) . "<br><b>Grund:</b> " . $this->config->param1('flightban') . "<br><br>Zum deaktivieren: <a href=\"?page=fleets&amp;sub=fleetoptions\">Flottenoptionen</a>";
        }

        // Kampfsperre aktiv
        if ($this->config->getBoolean('battleban')) {
            // Prüft, ob die Sperre schon abgelaufen ist
            if ($this->config->param1Int('battleban_time') <= time() && $this->config->param2Int('battleban_time') >= time()) {
                $battleban_time_status = "<span style=\"color:#0f0\">Aktiv</span> Es können keine Angriffe geflogen werden!";
            } elseif ($this->config->param1Int('battleban_time') > time() && $this->config->param2Int('battleban_time') > time()) {
                $battleban_time_status = "Ausstehend";
            } else {
                $battleban_time_status = "<span style=\"color:#f90\">Abgelaufen</span>";
            }

            $fleetBanTitle = "Kampfsperre aktiviert";
            $fleetBanText = "Die Kampfsperre wurde aktiviert.<br><br><b>Status:</b> " . $battleban_time_status . "<br><b>Zeit:</b> " . date("d.m.Y H:i", $this->config->param1Int('battleban_time')) . " - " . date("d.m.Y H:i", $this->config->param2Int('battleban_time')) . "<br><b>Grund:</b> " . $this->config->param1('battleban') . "<br><br>Zum deaktivieren: <a href=\"?page=fleets&amp;sub=fleetoptions\">Flottenoptionen</a>";
        }

        //
        // Schnellsuche
        //
        $_SESSION['planets']['query'] = null;
        $_SESSION['admin']['user_query'] = "";
        $_SESSION['admin']['queries']['alliances'] = "";

        $admin = $this->getUser();

        return $this->render('admin/overview/overview.html.twig', [
            'welcomeMessage' => 'Hallo <b>' . $admin->getUsername() . '</b>, willkommen im Administrationsmodus! Deine Rolle(n): <b>' . $this->roleManager->getRolesStr($admin->getData()) . '.</b>',
            'hasTfa' => (bool) $admin->getData()->tfaSecret,
            'didBigBangHappen' => $this->cellRepository->count() !== 0,
            'forcePasswordChange' => $admin->getData()->forcePasswordChange,
            'numNewTickets' => $this->ticketRepository->countNew(),
            'numOpenTickets' => $this->ticketRepository->countAssigned($admin->getId()),
            'fleetBanText' => $fleetBanText,
            'fleetBanTitle' => $fleetBanTitle,
            'adminInfo' => $this->textRepository->getEnabledTextOrDefault('admininfo'),
            'systemMessage' => $this->textRepository->getEnabledTextOrDefault('system_message'),
        ]);
    }

    /**
     * @Route("/admin/overview/changelog", name="admin.overview.changelog")
     */
    public function changelog(): Response
    {
        $changelogFile = __DIR__ . "/../../../Changelog.md";
        $changelogPublicFile = __DIR__ . "/../../../Changelog_public.md";

        return $this->render('admin/overview/changelog.html.twig', [
            'changelog' => is_file($changelogFile) ? $this->markdown->convertToHtml(file_get_contents($changelogFile)) : null,
            'changelogPublic' => is_file($changelogPublicFile) ? $this->markdown->convertToHtml(file_get_contents($changelogPublicFile)) : null,
        ]);
    }

    /**
     * @Route("/admin/overview/gamestats", name="admin.overview.gamestats")
     */
    public function gameStats(): Response
    {
        return $this->render('admin/overview/gamestats.html.twig', [
            'userStats' => file_exists($this->cacheDir . GameStatsGenerator::USER_STATS_FILE) ? GameStatsGenerator::USER_STATS_FILE_PUBLIC_PATH : null,
            'xmlInfo' => file_exists($this->cacheDir . GameStatsGenerator::XML_INFO_FILE) ? GameStatsGenerator::XML_INFO_FILE_PUBLIC_PATH : null,
            'gameStats' => $this->gameStatsGenerator->readCached(),
        ]);
    }

    /**
     * @Route("/admin/overview/sysinfo", name="admin.overview.sysinfo")
     */
    public function systemInfo(): Response
    {
        $unix = isUnixOS() ? posix_uname() : null;

        return $this->render('admin/overview/sysinfo.html.twig', [
            'phpVersion' => phpversion(),
            'dbVersion' => $this->databaseManager->getDatabasePlatform(),
            'webserverVersion' => $_SERVER['SERVER_SOFTWARE'] ?? '',
            'unixName' => isUnixOS() ? $unix['sysname'] . ' ' . $unix['release'] . ' ' . $unix['version'] : null,
        ]);
    }

    /**
     * @Route("/admin/overview/game-offline", name="admin.overview.game-offline")
     */
    public function gameOffline(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            if ($request->request->has('offline')) {
                $this->config->set('offline', 1);
            } elseif ($request->request->has('online')) {
                $this->config->set('offline', 0);
            } elseif ($request->request->has('save')) {
                $this->config->set('offline_ips_allow', $request->request->get('offline_ips_allow'));
                $this->config->set('offline_message', $request->request->get('offline_message'));
            }
        }

        $form = $this->createForm(GameOfflineType::class, [
            'offline_ips_allow' => $this->config->get('offline_ips_allow'),
            'offline_message' => $this->config->get('offline_message'),
        ], ['isOffline' => $this->config->getBoolean('offline')]);

        return $this->render('admin/overview/game-offline.html.twig', [
            'form' => $form->createView(),
            'isOffline' => $this->config->getBoolean('offline'),
        ]);
    }
}
