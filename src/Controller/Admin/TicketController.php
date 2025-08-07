<?php declare(strict_types=1);

namespace EtoA\Controller\Admin;

use EtoA\Admin\AdminUserRepository;
use EtoA\Entity\Ticket;
use EtoA\Entity\TicketMessage;
use EtoA\Form\Type\Core\TicketTextType;
use EtoA\Help\TicketSystem\TicketCategoryRepository;
use EtoA\Help\TicketSystem\TicketMessageRepository;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Help\TicketSystem\TicketService;
use EtoA\Help\TicketSystem\TicketSolution;
use EtoA\Help\TicketSystem\TicketStatus;
use EtoA\Security\Admin\CurrentAdmin;
use EtoA\User\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
        private readonly TicketCategoryRepository $ticketCategoryRepository
    )
    {
    }

    #[Route("/admin/tickets/", name: "admin.ticket.active")]
    public function active(): Response
    {
        return $this->render('admin/ticket/active.html.twig', [
            'assignedTickets' => $this->ticketRepository->findBy(['status' => TicketStatus::ASSIGNED]),
            'newTickets' => $this->ticketRepository->findBy(['status' => TicketStatus::NEW]),
        ]);
    }

    #[Route("/admin/tickets/closed", name: "admin.ticket.closed")]
    public function closed(): Response
    {
        return $this->render('admin/ticket/closed.html.twig', [
            'closedTickets' => $this->ticketRepository->findBy(['status' => TicketStatus::CLOSED->value]),
        ]);
    }

    #[Route("/admin/tickets/new", name: "admin.ticket.new")]
    public function new(Request $request): Response
    {
        $categories = $this->ticketCategoryRepository->findBy([],['sort'=>'DESC']);
        $ticket = new Ticket();
        $ticketMessage = new TicketMessage();
        $ticketMessage->setTimestamp(time());
        $ticketMessage->setAdmin($this->getUser()->getData());
        $ticket->addTicketMessage($ticketMessage);

        $form = $this->createFormBuilder($ticket)
            ->add('cat', ChoiceType::class, [
                'label' => false,
                'choices' => $categories,
                'choice_value' => 'id',
                'choice_label' => 'name'
            ])
            ->add('ticketMessages',CollectionType::class, [
                'entry_type' => TicketTextType::class,
                'entry_options' => [
                    'label' =>false
                ]
            ])
            ->add('user', ChoiceType::class, [
                'label' => false,
                'choices' => $this->userRepository->searchUserNicknames(),
                'choice_value' => 'id',
                'choice_label' => 'nick'
            ])
            ->add('submit', SubmitType::class, ['label' => 'Speichern'])
            ->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ticketService->create(
                ticket: $ticket
            );

            $this->addFlash('success', "Das Ticket wurde erstellt!");

            return $this->redirectToRoute('admin.ticket.active');
        }

        return $this->render('admin/ticket/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route("/admin/tickets/{id}/details", name: "admin.ticket.details")]
    public function details(Request $request, Ticket $ticket): Response
    {
        /** @var CurrentAdmin $adminUser */
        $adminUser = $this->getUser();
        $ticketMessage =  new TicketMessage();

        $form = $this->createFormBuilder($ticket)
            ->add('adminComment', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'rows'=>"4",
                    'cols'=>"60"
                ],
                'required' => false
            ])
            ->add('solution', ChoiceType::class, [
                'label' => false,
                'choices' => array_flip(TicketSolution::items()),
            ])
            ->add('submitAdminComment', SubmitType::class, [
                'label' => 'Speichern'
            ])
            ->add('shouldClose', CheckboxType::class, [
                'label'    => false,
                'required' => false,
                'mapped' => false
            ])
            ->add('submitNewPost', SubmitType::class, [
                'label' => 'Senden'
            ])
            ->add('ticketMessages', TicketTextType::class, [
                'label' => false,
                'attr' => [
                    'rows'=>"8",
                    'cols'=>"60"
                ],
                'data' => $ticketMessage,
                'required' => false,
                'mapped' => false,
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('submitNewPost')->isClicked() && $ticketMessage->getMessage()) {
                $this->ticketService->addMessage(
                    $ticket,
                    $ticketMessage,
                    null,
                    $adminUser->getData(),
                    !$form->get('shouldClose')->getData()
                );
                $this->addFlash('success', "Nachricht hinzugefügt!");
            }

            if ($form->get('shouldClose')->getData()) {
                $this->ticketService->close($ticket);
            }

            $this->ticketRepository->persist($ticket);
        }

        return $this->render('admin/ticket/details.html.twig', [
            'ticket' => $ticket,
            'ticketSolutions' => TicketSolution::items(),
            'form' => $form
        ]);
    }

    #[Route("/admin/tickets/{id}/edit", name: "admin.ticket.edit")]
    public function edit(Request $request, Ticket $ticket): Response
    {
        $categories = $this->ticketCategoryRepository->findBy([],['sort'=>'DESC']);

        $form = $this->createFormBuilder($ticket)
            ->add('cat', ChoiceType::class, [
                'label' => false,
                'choices' => $categories,
                'choice_value' => 'id',
                'choice_label' => 'name'
            ])
            ->add('admin', ChoiceType::class, [
                'label' => false,
                'choices' => $this->adminUserRepository->searchNicknames(),
                'choice_value' => 'id',
                'choice_label' => 'nick',
                'placeholder' => '(Niemand)',
            ])
            ->add('status', ChoiceType::class, [
                'label' => false,
                'choices' => array_flip(TicketStatus::items())
            ])
            ->add('solution', ChoiceType::class, [
                'label' => false,
                'choices' => array_flip(TicketSolution::items())
            ])
            ->add('adminComment', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'rows'=>"4",
                    'cols'=>"60"
                ],
                'required' => false
            ])
            ->add('submit', SubmitType::class, ['label' => 'Änderungen übernehmen'])
            ->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!isset(TicketStatus::items()[$ticket->getStatus()])) {
                $this->addFlash('error', 'Ungültiger Ticketstatus!');
            } elseif (!isset(TicketSolution::items()[$ticket->getSolution()])) {
                $this->addFlash('error', 'Ungültiger Ticketlösung!');
            } else {
                $this->ticketRepository->persist($ticket);
                $this->addFlash('success', "Ticket aktualisiert!");
                return $this->redirectToRoute('admin.ticket.details', ['id' => $ticket->getId()]);
            }
        }

        return $this->render('admin/ticket/edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route("/admin/tickets/{id}/assign", name: "admin.ticket.assign", methods: ['POST'])]
    public function assign(Ticket $ticket): RedirectResponse
    {
        /** @var CurrentAdmin $adminUser */
        $adminUser = $this->getUser();

        $this->ticketService->assign($ticket, $adminUser->getData());
        $this->addFlash('success', "Ticket aktualisiert!");

        return $this->redirectToRoute('admin.ticket.details', ['id' => $ticket->getId()]);
    }

    #[Route("/admin/tickets/{id}/reopen", name: "admin.ticket.reopen", methods: ['POST'])]
    public function reopen(int $id): RedirectResponse
    {
        $ticket = $this->ticketRepository->find($id);
        $this->ticketService->reopen($ticket);
        $this->addFlash('success', "Ticket aktualisiert!");

        return $this->redirectToRoute('admin.ticket.details', ['id' => $id]);
    }
}
