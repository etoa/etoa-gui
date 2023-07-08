<?php declare(strict_types=1);

namespace EtoA\EventSubscriber;

use EtoA\Admin\AdminNotesRepository;
use EtoA\Admin\AdminRoleManager;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Security\Admin\CurrentAdmin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

class AdminTwigSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly Environment           $twig,
        private readonly AdminNotesRepository  $notesRepository,
        private readonly TicketRepository      $ticketRepository,
        private readonly AdminRoleManager      $adminRoleManager,
        private readonly UrlGeneratorInterface $router,
        private readonly string                $projectDir,
    )
    {
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

        $this->twig->addGlobal('navMenu', $this->createNavMenu($adminUser, $request));
        $this->twig->addGlobal('userRoles', $adminUser->getData()->roles);
        $this->twig->addGlobal('searchQuery', $request->request->get('search_query'));
        $this->twig->addGlobal('page', $request->query->get('page', 'overview'));
        $this->twig->addGlobal('sub', $request->query->get('sub'));
        $this->twig->addGlobal('numTickets', $this->ticketRepository->countAssigned($adminUser->getId()) + $this->ticketRepository->countNew());
        $this->twig->addGlobal('numNotes', $this->notesRepository->countForAdmin($adminUser->getId()));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    private function createNavMenu(CurrentAdmin $adminUser, Request $request): array
    {
        $navTree = require($this->projectDir . '/config/admin_menu.php');
        $userRoles = $adminUser->getData()->roles;
        $currentRouteName = $request->attributes->get('_route');
        $page = $request->query->get('page', 'overview');

        foreach ($navTree as $key => &$item) {
            $processedItem = $this->handleNavItem($item, $userRoles, $currentRouteName, $page);
            if ($processedItem == null) {
                unset($navTree[$key]);
                continue;
            }
            $navTree[$key] = $processedItem;

            $activeChild = false;
            if (count($item['children'] ?? []) > 0) {
                foreach ($item['children'] as $childKey => &$childItem) {
                    $processedItem = $this->handleNavItem($childItem, $userRoles, $currentRouteName, $page, $item['page'] ?? null);
                    if ($processedItem == null) {
                        unset($navTree[$key]['children'][$childKey]);
                        continue;
                    }
                    $activeChild |= $processedItem['active'];
                    $navTree[$key]['children'][$childKey] = $processedItem;
                }
                $navTree[$key]['active'] |= $activeChild;
            }
            if (!$navTree[$key]['active']) {
                $navTree[$key]['children'] = [];
            }
        }

        return $navTree;
    }

    private function handleNavItem(array $item, array $userRoles, ?string $currentRouteName, ?string $currentPage, ?string $parentPage = null)
    {
        // Remove nav tree element if roles don't match i.e. user is not authorized for this page
        if (!$this->adminRoleManager->checkAllowedRoles($userRoles, $item['roles'])) {
            return null;
        }

        // Mark item as active if it matches route name
        if (isset($item['route'])) {
            $item['href'] = $this->router->generate($item['route']);
            $item['active'] = $currentRouteName == $item['route'];
        } else {
            $item['href'] = '/admin/?page=' . ($item['page'] ?? $parentPage) . (isset($item['sub']) ? '&sub=' . $item['sub'] : '');
            $item['active'] = $currentPage == ($item['page'] ?? $parentPage);
        }

        return $item;
    }
}
