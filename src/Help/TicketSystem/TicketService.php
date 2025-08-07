<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

use EtoA\Admin\AdminUserRepository;
use EtoA\Entity\AdminUser;
use EtoA\Entity\MessageCategory;
use EtoA\Entity\Ticket;
use EtoA\Entity\TicketCategory;
use EtoA\Entity\TicketMessage;
use EtoA\Entity\User;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageCategoryRepository;
use EtoA\Message\MessageRepository;
use EtoA\User\UserRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TicketService
{
    const INACTIVE_TIME = 72 * 3600; // 72 hours

    public function __construct(
        private readonly TicketRepository $ticketRepo,
        private readonly TicketMessageRepository $messageRepo,
        private readonly AdminUserRepository $adminUserRepo,
        private readonly MessageRepository $userMessageRepo,
        private readonly UrlGeneratorInterface $router,
        private readonly MessageCategoryRepository $messageCategoryRepository
    ) {}

    public function create(?User $user = null, ?Ticket $ticket = null): Ticket
    {

        $ticket = $ticket??new Ticket();

        if($user)
            $ticket->setUser($user);
        else {
            $user = $ticket->getUser();
        }

        $this->ticketRepo->persist($ticket);

        $text = "Hallo!

        Dein [page=".$this->router->generate('game.ticket.view',['id'=>$ticket->getId()])."]Ticket #" . $ticket->getIdString() . "[/page] wurde erfolgreich erstellt.
        Es wird sich in Kürze ein Admin um dein Anliegen kümmern.
        
        Dein Admin-Team";
        $this->userMessageRepo->createSystemMessage($user, $this->messageCategoryRepository->find(MessageCategoryId::USER), "Dein Ticket " . $ticket->getIdString() . ' wurde erstellt', $text);

        return $ticket;
    }

    public function assign(Ticket $ticket, AdminUser $admin): void
    {
        $ticket->setAdmin($admin);
        $ticket->setStatus(TicketStatus::ASSIGNED->value);
        $this->ticketRepo->persist($ticket);
        $message = new TicketMessage();
        $message->setMessage("Das Ticket wurde dem Administrator " . $ticket->getAdmin()->getNick() . " zugewiesen.");

        $this->addMessage($ticket,$message);
    }

    public function close(Ticket $ticket, string $solution = ''): bool
    {
        if ($ticket->getStatus() === TicketStatus::ASSIGNED->value) {
            $ticket->setStatus(TicketStatus::CLOSED->value);
            if($solution)
                $ticket->setSolution($solution);
            $this->ticketRepo->persist($ticket);

            $message = new TicketMessage();
            $message->setMessage( "Das Ticket wurde geschlossen und als " . TicketSolution::items()[$ticket->getSolution()] . " gekennzeichnet.");
            $this->addMessage($ticket,$message);

            return true;
        }

        return false;
    }

    public function reopen(Ticket $ticket): void
    {
        $ticketMessage = new TicketMessage();

        if ($ticket->getStatus() === TicketStatus::CLOSED->value) {
            $ticket->setAdmin(null);
            $ticket->setStatus(TicketStatus::NEW->value);
            $ticket->setSolution(TicketSolution::OPEN->value);
            $ticketMessage->setMessage("Das Ticket wurde wieder eröffnet.");
            $this->addMessage($ticket, $ticketMessage);

            $this->ticketRepo->persist($ticket);
        }
        if ($ticket->getStatus() === TicketStatus::ASSIGNED->value) {
            $ticket->setAdmin(null);
            $ticket->setStatus(TicketStatus::NEW->value);
            $ticketMessage->setMessage("Der Ticketadministrator hat das Ticket wieder als Neu markiert.");
            $this->addMessage($ticket, $ticketMessage);

            $this->ticketRepo->persist($ticket);
        }
    }

    public function closeAssignedInactive(): int
    {
        $threshold = time() - self::INACTIVE_TIME;

        $ticketIds = $this->ticketRepo->findAssignedIds();
        $i = 0;
        foreach ($ticketIds as $id) {
            $message = $this->messageRepo->findLastMessageForTicket($id);
            if ($message != null) {
                if ($message->getAdminId() > 0 && $message->getTimestamp() < $threshold) {
                    $ticket = $this->ticketRepo->find($id);
                    $this->addMessage($ticket, "Das Ticket wurde automatisch geschlossen, da wir innerhalb der letzten 72 Stunden nichts mehr von dir gehört haben.");
                    $this->close($ticket, "solved");
                    $i++;
                }
            }
        }

        return $i;
    }

    public function addMessage(Ticket $ticket, TicketMessage $ticketMessage, User $user = null, AdminUser $admin = null, bool $informUser = true): TicketMessage
    {
        $ticketMessage->setUser($user);
        $ticketMessage->setAdmin($admin);
        $ticketMessage->setTimestamp(time());
        $ticket->addTicketMessage($ticketMessage);

        $this->ticketRepo->persist($ticket);

        if ($informUser && !$ticketMessage->getUser()) {
            $text = "Hallo!

Dein Ticket " . $ticket->getIdString() . " wurde aktualisiert!

[page ticket id=".$this->router->generate('game.ticket.view',['id'=>$ticket->getId()])."]Klicke HIER um die Änderungen anzusehen.[/page]

Dein Admin-Team";
            $this->userMessageRepo->createSystemMessage($ticket->getUser(), $this->messageCategoryRepository->find(MessageCategoryId::USER), "Dein Ticket " . $ticket->getIdString() . ' wurde aktualisiert', $text);
        }

        return $ticketMessage;
    }

    /**
     * @return array<TicketMessage>
     */
    public function getMessages(Ticket $ticket): array
    {
        return $this->messageRepo->findBy(['ticket'=>$ticket],['timestamp'=>'ASC']);
    }

    public function getAuthorNick(TicketMessage $message): string
    {
        if ($message->getUser()) {
            return $message->getUser()->getNick();
        }
        if ($message->getAdmin()) {
            return $message->getAdmin()->getNick() . " (Admin)";
        }

        return "System";
    }

    /**
     * @param int[] $ticketIds
     */
    public function removeByIds(array $ticketIds): int
    {
        if (count($ticketIds) === 0) {
            return 0;
        }

        $this->messageRepo->removeByTicketIds($ticketIds);

        return $this->ticketRepo->removeByIds($ticketIds);
    }
}
