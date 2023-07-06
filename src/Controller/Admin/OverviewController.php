<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\AdminRoleManager;
use EtoA\Admin\AdminSessionRepository;
use EtoA\Admin\AdminUserRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceSearch;
use EtoA\Backend\EventHandlerManager;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Form\Type\Admin\GameOfflineType;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Ranking\GameStatsGenerator;
use EtoA\Support\DB\DatabaseManagerRepository;
use EtoA\Text\TextRepository;
use EtoA\Universe\Cell\CellRepository;
use EtoA\Universe\Entity\EntityLabel;
use EtoA\Universe\Entity\EntityLabelSearch;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use EtoA\User\UserSessionRepository;
use League\CommonMark\ConverterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class OverviewController extends AbstractAdminController
{
    public function __construct(
        private readonly ConverterInterface        $markdown,
        private readonly GameStatsGenerator        $gameStatsGenerator,
        private readonly DatabaseManagerRepository $databaseManager,
        private readonly string                    $cacheDir,
        private readonly ConfigurationService      $config,
        private readonly AdminRoleManager          $roleManager,
        private readonly CellRepository            $cellRepository,
        private readonly TicketRepository          $ticketRepository,
        private readonly TextRepository            $textRepository,
        private readonly UserRepository            $userRepository,
        private readonly AllianceRepository        $allianceRepository,
        private readonly EntityRepository          $entityRepository,
        private readonly UserSessionRepository     $userSessionRepository,
        private readonly AdminSessionRepository    $adminSessionRepository,
        private readonly AdminUserRepository       $adminUserRepository,
        private readonly EventHandlerManager       $eventHandlerManager,
    )
    {
    }

    #[Route("/admin/overview/", name: "admin.overview")]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
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

        if (isUnixOS()) {
            $eventHandlerPid = $this->eventHandlerManager->checkDaemonRunning();
            exec("cat /proc/cpuinfo | grep processor | wc -l", $out);
            $load = sys_getloadavg();
            $systemLoad = round($load[2] / intval($out[0]) * 100, 2);
        }

        return $this->render('admin/overview/overview.html.twig', [
            'welcomeMessage' => 'Hallo <b>' . $admin->getUsername() . '</b>, willkommen im Administrationsmodus! Deine Rolle(n): <b>' . $this->roleManager->getRolesStr($admin->getData()) . '.</b>',
            'hasTfa' => (bool)$admin->getData()->tfaSecret,
            'didBigBangHappen' => $this->cellRepository->count() !== 0,
            'forcePasswordChange' => $admin->getData()->forcePasswordChange,
            'numNewTickets' => $this->ticketRepository->countNew(),
            'numOpenTickets' => $this->ticketRepository->countAssigned($admin->getId()),
            'fleetBanText' => $fleetBanText,
            'fleetBanTitle' => $fleetBanTitle,
            'adminInfo' => $this->textRepository->getEnabledTextOrDefault('admininfo'),
            'systemMessage' => $this->textRepository->getEnabledTextOrDefault('system_message'),
            'dbSizeInMB' => $this->databaseManager->getDatabaseSize(),
            'usersOnline' => $this->userSessionRepository->countActiveSessions($this->config->getInt('user_timeout')),
            'usersCount' => $this->userRepository->count(),
            'usersAllowed' => $this->config->getInt('enable_register'),
            'adminsOnline' => $this->adminSessionRepository->countActiveSessions($this->config->getInt('admin_timeout')),
            'adminsCount' => $this->adminUserRepository->count(),
            'sysLoad' => $systemLoad ?? null,
            'eventHandlerPid' => $eventHandlerPid ?? null,
        ]);
    }

    #[Route("/admin/overview/changelog", name: "admin.overview.changelog")]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function changelog(): Response
    {
        $changelogFile = __DIR__ . "/../../../Changelog.md";
        $changelogPublicFile = __DIR__ . "/../../../Changelog_public.md";

        return $this->render('admin/overview/changelog.html.twig', [
            'changelog' => is_file($changelogFile) ? $this->markdown->convert(file_get_contents($changelogFile)) : null,
            'changelogPublic' => is_file($changelogPublicFile) ? $this->markdown->convert(file_get_contents($changelogPublicFile)) : null,
        ]);
    }

    #[Route("/admin/overview/gamestats", name: "admin.overview.gamestats")]
    #[IsGranted('ROLE_ADMIN_TRIAL-ADMIN')]
    public function gameStats(): Response
    {
        return $this->render('admin/overview/gamestats.html.twig', [
            'userStats' => file_exists($this->cacheDir . GameStatsGenerator::USER_STATS_FILE) ? GameStatsGenerator::USER_STATS_FILE_PUBLIC_PATH : null,
            'xmlInfo' => file_exists($this->cacheDir . GameStatsGenerator::XML_INFO_FILE) ? GameStatsGenerator::XML_INFO_FILE_PUBLIC_PATH : null,
            'gameStats' => $this->gameStatsGenerator->readCached(),
        ]);
    }

    #[Route("/admin/overview/sysinfo", name: "admin.overview.sysinfo")]
    #[IsGranted('ROLE_ADMIN_SUPER-ADMIN')]
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

    #[Route("/admin/overview/game-offline", name: "admin.overview.game-offline")]
    #[IsGranted('ROLE_ADMIN_GAME-ADMIN')]
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

    #[Route('/admin/overview/search', 'admin.overview.search')]
    public function search(Request $request): JsonResponse
    {
        $users = $this->userRepository->searchUserNicknames(UserSearch::create()->nickOrEmailOrDualLike($request->query->getAlnum('query')), 30);
        $alliances = $this->allianceRepository->getAllianceNamesWithTags(AllianceSearch::create()->nameOrTagLike($request->query->getAlnum('query')), 30);
        $entities = $this->entityRepository->searchEntityLabels(EntityLabelSearch::create()->likePlanetName($request->query->getAlnum('query')), null, 30);
        $choices = [
            $this->choiceGroup($users, 1, 'Spieler', 'admin.alliances.edit'),
            $this->choiceGroup($alliances, 2, 'Allianzen', 'admin.alliances.edit'),
            $this->choiceGroup(array_map(fn(EntityLabel $label) => $label->toString(), $entities), 3, 'Planeten', 'admin.universe.entity'),
        ];

        return new JsonResponse($choices);
    }

    /**
     * @param array<int, string> $options
     * @return array<string, mixed>
     */
    private function choiceGroup(array $options, int $id, string $label, string $route): array
    {
        if (count($options) === 0) {
            return [];
        }

        $choices = [
            'label' => $label,
            'id' => $id,
            'disabled' => false,
            'choices' => [],
        ];
        foreach ($options as $optionId => $optionValue) {
            $choices['choices'][] = [
                'label' => $optionValue,
                'value' => $optionId,
                'customProperties' => ['link' => $this->generateUrl($route, ['id' => $optionId])],
            ];
        }

        return $choices;
    }
}
