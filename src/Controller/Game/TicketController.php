<?php

namespace EtoA\Controller\Game;

use EtoA\Entity\Ticket;
use EtoA\Entity\TicketCategory;
use EtoA\Entity\TicketMessage;
use EtoA\Form\Type\Core\TicketTextType;
use EtoA\Help\TicketSystem\TicketCategoryRepository;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Help\TicketSystem\TicketService;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class TicketController extends AbstractGameController
{
    public function __construct(
        private readonly TicketCategoryRepository $ticketCategoryRepository,
        private readonly TicketRepository $ticketRepository,
        private readonly TicketService $ticketService
    )
    {
    }

    #[Route('/game/ticket/list/{id}', name: 'game.ticket.list.category')]
    public function categoryList(Request $request, ?TicketCategory $ticketCategory = null): Response {
        return $this->list($request, $ticketCategory);
    }

    #[Route('/game/ticket/list', name: 'game.ticket.list')]
    public function list(Request $request, ?TicketCategory $ticketCategory = null): Response {

        $categories = $this->ticketCategoryRepository->findBy([],['sort'=>'DESC']);
        $ticket = new Ticket();
        $ticketMessage = new TicketMessage();
        $ticketMessage->setTimestamp(time());
        $ticketMessage->setUser($this->getUser()->getData());
        $ticket->addTicketMessage($ticketMessage);

        $form = $this->createFormBuilder($ticket)
            ->add('cat', ChoiceType::class, [
                'label' => false,
                'choices' => $categories,
                'choice_value' => 'id',
                'choice_label' => 'name',
                'data' => $ticketCategory
            ])
            ->add('ticketMessages',CollectionType::class, [
                'entry_type' => TicketTextType::class,
                'entry_options' => [
                    'label' =>false
                ]
            ])
            ->add('submit', SubmitType::class, ['label' => 'Einsenden'])
            ->getForm()->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->ticketService->create($this->getUser()->getData(), $ticket);
            $msg['success'] = '<br/>Vielen Dank, dein Text wurde gespeichert.<br/>Ein Game-Administrator wird sich dem Problem annehmen.<br/><br/>';
        }

        return $this->render('game/ticket/list.html.twig', [
            'form' => $form,
            'tickets' => $this->ticketRepository->findBy(['user'=>$this->getUser()->getData()]),
            'msg' => $msg??null
        ]);
    }

    #[Route('/game/ticket/view/{id}', name: 'game.ticket.view')]
    public function view(Request $request, ?Ticket $ticket = null): Response {
        if($ticket) {
            $ticketMessage = new TicketMessage();
            $ticketMessage->setUser($this->getUser()->getData());

            $form = $this->createFormBuilder($ticketMessage)
                ->add('message',TextareaType::class, [
                    'label' =>false,
                    'attr' => [
                        'rows'=>8,
                        'cols'=>60,
                    ],
                    'constraints' => new NotBlank(
                        ['message' => 'Du musst eine Nachricht eingeben!']
                    ),
                ])
                ->add('reopen', SubmitType::class, ['label' => 'Ticket wiedereröffnen'])
                ->add('send', SubmitType::class, ['label' => 'Senden'])
                ->getForm()->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if($form->get('reopen')->isClicked()) {
                    $this->ticketService->reopen($ticket);
                }
                else {
                    $this->ticketService->addMessage($ticket, $ticketMessage, $this->getUser()->getData());
                    $msg['success'] = 'Nachricht hinzugefügt!';
                }
            }

            return $this->render('game/ticket/view.html.twig', [
                'form' => $form,
                'ticket' => $ticket,
                'msg' => $msg??null
            ]);
        }

        return $this->render('game/error.html.twig',[
            'msg' => 'Ticket nicht vorhanden!',
            'path' => $this->generateUrl('game.ticket.list'),
            'headline' => 'Ticketsystem'
        ]);
    }
}