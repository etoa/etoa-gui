<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\AdminRoleManager;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Ranking\GameStatsGenerator;
use EtoA\Security\Admin\CurrentAdmin;
use EtoA\Support\DB\DatabaseManagerRepository;
use EtoA\Text\TextRepository;
use EtoA\Universe\Cell\CellRepository;
use League\CommonMark\MarkdownConverterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OverviewController extends AbstractController
{
    private MarkdownConverterInterface $markdown;
    private GameStatsGenerator $gameStatsGenerator;
    private DatabaseManagerRepository $databaseManager;
    private string $cacheDir;
    private ConfigurationService $config;
    private AdminRoleManager $roleManager;
    private CellRepository $cellRepository;
    private TicketRepository $ticketRepository;
    private TextRepository $textRepository;

    public function __construct(MarkdownConverterInterface $markdown, GameStatsGenerator $gameStatsGenerator, DatabaseManagerRepository $databaseManager, string $cacheDir, ConfigurationService $config, AdminRoleManager $roleManager, CellRepository $cellRepository, TicketRepository $ticketRepository, TextRepository $textRepository)
    {
        $this->markdown = $markdown;
        $this->gameStatsGenerator = $gameStatsGenerator;
        $this->databaseManager = $databaseManager;
        $this->cacheDir = $cacheDir;
        $this->config = $config;
        $this->roleManager = $roleManager;
        $this->cellRepository = $cellRepository;
        $this->ticketRepository = $ticketRepository;
        $this->textRepository = $textRepository;
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

        /** @var CurrentAdmin $admin */
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
            'webserverVersion' => $_SERVER['SERVER_SOFTWARE'],
            'unixName' => isUnixOS() ? $unix['sysname'] . ' ' . $unix['release'] . ' ' . $unix['version'] : null,
        ]);
    }
}
