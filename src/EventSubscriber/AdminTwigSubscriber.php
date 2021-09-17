<?php declare(strict_types=1);

namespace EtoA\EventSubscriber;

use EtoA\Admin\AdminNotesRepository;
use EtoA\Admin\AdminSessionRepository;
use EtoA\Admin\AdminUserRepository;
use EtoA\Backend\EventHandlerManager;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Security\Admin\CurrentAdmin;
use EtoA\Support\DB\DatabaseManagerRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSessionRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

class AdminTwigSubscriber implements EventSubscriberInterface
{
    private TokenStorageInterface $tokenStorage;
    private Environment $twig;
    private AdminNotesRepository $notesRepository;
    private TicketRepository $ticketRepository;
    private EventHandlerManager $eventHandlerManager;
    private ConfigurationService $config;
    private UserSessionRepository $userSessionRepository;
    private UserRepository $userRepository;
    private AdminSessionRepository $adminSessionRepository;
    private AdminUserRepository $adminUserRepository;
    private DatabaseManagerRepository $databaseManager;

    public function __construct(TokenStorageInterface $tokenStorage, Environment $twig, AdminNotesRepository $notesRepository, TicketRepository $ticketRepository, EventHandlerManager $eventHandlerManager, ConfigurationService $config, UserSessionRepository $userSessionRepository, UserRepository $userRepository, AdminSessionRepository $adminSessionRepository, AdminUserRepository $adminUserRepository, DatabaseManagerRepository $databaseManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->twig = $twig;
        $this->notesRepository = $notesRepository;
        $this->ticketRepository = $ticketRepository;
        $this->eventHandlerManager = $eventHandlerManager;
        $this->config = $config;
        $this->userSessionRepository = $userSessionRepository;
        $this->userRepository = $userRepository;
        $this->adminSessionRepository = $adminSessionRepository;
        $this->adminUserRepository = $adminUserRepository;
        $this->databaseManager = $databaseManager;
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $token = $this->tokenStorage->getToken();
        if ($token === null || !$token->getUser() instanceof CurrentAdmin) {
            return;
        }

        /** @var CurrentAdmin $adminUser */
        $adminUser = $token->getUser();
        $request = $event->getRequest();

        $this->twig->addGlobal('navMenu', fetchJsonConfig("admin-menu.conf"));
        $this->twig->addGlobal('userRoles', $adminUser->getData()->roles);

        $this->twig->addGlobal('searchQuery', $request->request->get('search_query'));
        $this->twig->addGlobal('page', $request->query->get('page', 'overview'));
        $this->twig->addGlobal('sub', $request->query->get('sub'));
        $this->twig->addGlobal('numTickets', $this->ticketRepository->countAssigned($adminUser->getId()) + $this->ticketRepository->countNew());
        $this->twig->addGlobal('numNotes', $this->notesRepository->countForAdmin($adminUser->getId()));

        if (isUnixOS()) {
            $eventHandlerPid = $this->eventHandlerManager->checkDaemonRunning();
            exec("cat /proc/cpuinfo | grep processor | wc -l", $out);
            $load = sys_getloadavg();
            $systemLoad = round($load[2] / intval($out[0]) * 100, 2);

            $this->twig->addGlobal('sysLoad', $systemLoad);
            $this->twig->addGlobal('eventHandlerPid', $eventHandlerPid);
        }

        $this->twig->addGlobal('usersOnline', $this->userSessionRepository->countActiveSessions($this->config->getInt('user_timeout')));
        $this->twig->addGlobal('usersCount', $this->userRepository->count());
        $this->twig->addGlobal('usersAllowed', $this->config->getInt('enable_register'));
        $this->twig->addGlobal('adminsOnline', $this->adminSessionRepository->countActiveSessions($this->config->getInt('admin_timeout')));
        $this->twig->addGlobal('adminsCount', $this->adminUserRepository->count());
        $this->twig->addGlobal('dbSizeInMB', $this->databaseManager->getDatabaseSize());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
