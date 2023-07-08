<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\AdminUserRepository;
use EtoA\Help\TicketSystem\TicketMessageRepository;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Help\TicketSystem\TicketService;
use EtoA\Help\TicketSystem\TicketSolution;
use EtoA\Help\TicketSystem\TicketStatus;
use EtoA\Security\Admin\CurrentAdmin;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketController extends AbstractController
{
    public function __construct(
        private readonly UserRepository          $userRepository,
        private readonly TicketRepository        $ticketRepository,
        private readonly TicketService           $ticketService,
        private readonly AdminUserRepository     $adminUserRepository,
        private readonly TicketMessageRepository $ticketMessageRepository
    )
    {
    }

    #[Route("/admin/tickets/", name: "admin.ticket.active")]
    public function active(): Response
    {
        return $this->render('admin/ticket/active.html.twig', [
            'assignedTickets' => $this->ticketRepository->findBy(['status' => TicketStatus::ASSIGNED]),
            'newTickets' => $this->ticketRepository->findBy(['status' => TicketStatus::NEW]),
            'ticketCategories' => $this->ticketRepository->findAllCategoriesAsMap(),
            'userNicks' => $this->userRepository->searchUserNicknames(),
            'adminNicks' => $this->adminUserRepository->searchNicknames(),
            'messageCounts' => $this->ticketMessageRepository->countsByTicket(),
        ]);
    }

    #[Route("/admin/tickets/closed", name: "admin.ticket.closed")]
    public function closed(): Response
    {
        return $this->render('admin/ticket/closed.html.twig', [
            'closedTickets' => $this->ticketRepository->findBy(['status' => TicketStatus::CLOSED]),
            'ticketCategories' => $this->ticketRepository->findAllCategoriesAsMap(),
            'userNicks' => $this->userRepository->searchUserNicknames(),
            'adminNicks' => $this->adminUserRepository->searchNicknames(),
        ]);
    }

    #[Route("/admin/tickets/new", name: "admin.ticket.new")]
    public function new(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $this->ticketService->create(
                $request->request->getInt('user_id'),
                $request->request->getInt('cat_id'),
                $request->request->get('message')
            );

            $this->addFlash('success', "Das Ticket wurde erstellt!");

            return $this->redirectToRoute('admin.ticket.active');
        }

        return $this->render('admin/ticket/new.html.twig', [
            'users' => $this->userRepository->searchUserNicknames(),
            'ticketCategories' => $this->ticketRepository->findAllCategoriesAsMap(),
        ]);
    }

    #[Route("/admin/tickets/{id}/details", name: "admin.ticket.details")]
    public function details(Request $request, int $id): Response
    {
        /** @var CurrentAdmin $adminUser */
        $adminUser = $this->getUser();
        $ticket = $this->ticketRepository->find($id);

        if ($request->isMethod('POST')) {
            if ($request->request->has('submit_new_post') && $request->request->get('message')) {
                $this->ticketService->addMessage(
                    $ticket,
                    $request->request->get('message'),
                    0,
                    $adminUser->getId(),
                    !$request->request->has('should_close')
                );
                $this->addFlash('success', "Nachricht hinzugefügt!");
            }

            if ($request->request->has('should_close')) {
                $this->ticketService->close($ticket, $request->request->get('close_solution'));
            }

            if ($request->request->has('admin_comment')) {
                $ticket->adminComment = $request->request->get('admin_comment');
                $this->ticketRepository->persist($ticket);
            }
        }

        $messages = $this->ticketService->getMessages($ticket);
        $messageAuthor = [];
        foreach ($messages as $message) {
            $messageAuthor[$message->id] = $this->ticketService->getAuthorNick($message);
        }

        return $this->render('admin/ticket/details.html.twig', [
            'ticket' => $ticket,
            'ticketCategories' => $this->ticketRepository->findAllCategoriesAsMap(),
            'userNick' => $this->userRepository->getNick($ticket->userId),
            'adminNick' => $this->adminUserRepository->getNick($ticket->adminId),
            'messages' => $messages,
            'messageAuthor' => $messageAuthor,
            'ticketSolutions' => TicketSolution::items(),
        ]);
    }

    #[Route("/admin/tickets/{id}/edit", name: "admin.ticket.edit")]
    public function edit(Request $request, int $id): Response
    {
        /** @var CurrentAdmin $adminUser */
        $adminUser = $this->getUser();
        $ticket = $this->ticketRepository->find($id);

        if ($request->isMethod('POST')) {
            if (!isset(TicketStatus::items()[$request->request->get('status')])) {
                $this->addFlash('error', 'Ungültiger Ticketstatus!');
            } elseif (!isset(TicketSolution::items()[$request->request->get('solution')])) {
                $this->addFlash('error', 'Ungültiger Ticketlösung!');
            } else {
                $ticket->status = $request->request->get('status');
                $ticket->solution = $request->request->get('solution');
                $ticket->catId = $request->request->getInt('cat_id');
                $ticket->adminId = $request->request->getInt('admin_id');
                $ticket->adminComment = $request->request->get('admin_comment');

                if ($this->ticketRepository->persist($ticket)) {
                    $this->addFlash('success', "Ticket aktualisiert!");

                    return $this->redirectToRoute('admin.ticket.details', ['id' => $ticket->id]);
                }
            }
        }

        return $this->render('admin/ticket/edit.html.twig', [
            'ticket' => $ticket,
            'ticketCategories' => $this->ticketRepository->findAllCategoriesAsMap(),
            'userNick' => $this->userRepository->getNick($ticket->userId),
            'messages' => $this->ticketService->getMessages($ticket),
            'ticketSolutions' => TicketSolution::items(),
            'ticketStatus' => TicketStatus::items(),
            'adminNicks' => $this->adminUserRepository->searchNicknames(),
        ]);
    }

    #[Route("/admin/tickets/{id}/assign", name: "admin.ticket.assign", methods: ['POST'])]
    public function assign(int $id): RedirectResponse
    {
        /** @var CurrentAdmin $adminUser */
        $adminUser = $this->getUser();

        $ticket = $this->ticketRepository->find($id);
        if ($this->ticketService->assign($ticket, $adminUser->getId())) {
            $this->addFlash('success', "Ticket aktualisiert!");
        }

        return $this->redirectToRoute('admin.ticket.details', ['id' => $id]);
    }

    #[Route("/admin/tickets/{id}/reopen", name: "admin.ticket.reopen", methods: ['POST'])]
    public function reopen(int $id): RedirectResponse
    {
        /** @var CurrentAdmin $adminUser */
        $adminUser = $this->getUser();

        $ticket = $this->ticketRepository->find($id);
        if ($this->ticketService->reopen($ticket)) {
            $this->addFlash('success', "Ticket aktualisiert!");
        }

        return $this->redirectToRoute('admin.ticket.details', ['id' => $id]);
    }
}
