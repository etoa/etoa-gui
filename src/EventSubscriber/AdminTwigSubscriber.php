<?php declare(strict_types=1);

namespace EtoA\EventSubscriber;

use EtoA\Admin\AdminNotesRepository;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Security\Admin\CurrentAdmin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

class AdminTwigSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private readonly TokenStorageInterface $tokenStorage,
        private readonly Environment           $twig,
        private readonly AdminNotesRepository  $notesRepository,
        private readonly TicketRepository      $ticketRepository,
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

        $this->twig->addGlobal('navMenu', require($this->projectDir . '/config/admin_menu.php'));
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
}
