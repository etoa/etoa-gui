<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

use EtoA\Admin\AdminUserRepository;
use EtoA\Message\MessageRepository;

class TicketService
{
    private TicketRepository $ticketRepo;
    private TicketMessageRepository $messageRepo;
    private AdminUserRepository $adminUserRepo;
    private MessageRepository $userMessageRepo;

    const INACTIVE_TIME = 72 * 3600; // 72 hours

    public function __construct(
        TicketRepository $ticketRepo,
        TicketMessageRepository $messageRepo,
        AdminUserRepository $adminUserRepo,
        MessageRepository $userMessageRepo
    ) {
        $this->ticketRepo = $ticketRepo;
        $this->messageRepo = $messageRepo;
        $this->adminUserRepo = $adminUserRepo;
        $this->userMessageRepo = $userMessageRepo;
    }

    public function create(int $userId, int $catId, string $message): Ticket
    {
        $ticket = new Ticket();
        $ticket->solution = TicketSolution::OPEN;
        $ticket->status = TicketStatus::NEW;
        $ticket->userId = $userId;
        $ticket->catId = $catId;
        $this->ticketRepo->persist($ticket);

        $ticketMessage = new TicketMessage();
        $ticketMessage->ticketId = $ticket->id;
        $ticketMessage->userId = $ticket->userId;
        $ticketMessage->message = $message;
        $this->messageRepo->create($ticketMessage);

        $text = "Hallo!

Dein [page ticket id=" . $ticket->id . "]Ticket #" . $ticket->getIdString() . "[/page] wurde erfolgreich erstellt.
Es wird sich in Kürze ein Admin um dein Anliegen kümmern.

Dein Admin-Team";
        $this->userMessageRepo->createSystemMessage($ticket->userId, USER_MSG_CAT_ID, "Dein Ticket " . $ticket->getIdString() . ' wurde erstellt', $text);

        return $ticket;
    }

    public function assign(Ticket $ticket, int $adminId): bool
    {
        $ticket->adminId = $adminId;
        $ticket->status = TicketStatus::ASSIGNED;
        $changed = $this->ticketRepo->persist($ticket);
        if ($changed) {
            $this->addMessage($ticket, "Das Ticket wurde dem Administrator " . $this->adminUserRepo->getNick($ticket->adminId) . " zugewiesen.");
        }
        return $changed;
    }

    public function close(Ticket $ticket, string $solution): bool
    {
        if ($ticket->status == TicketStatus::ASSIGNED) {
            $ticket->status = TicketStatus::CLOSED;
            $ticket->solution = $solution;
            $changed = $this->ticketRepo->persist($ticket);
            if ($changed) {
                $this->addMessage($ticket, "Das Ticket wurde geschlossen und als " . TicketSolution::label($ticket->solution) . " gekennzeichnet.");
            }
            return $changed;
        }
        return false;
    }

    public function reopen(Ticket $ticket): bool
    {
        if ($ticket->status == TicketStatus::CLOSED) {
            $ticket->adminId = 0;
            $ticket->status = TicketStatus::NEW;
            $ticket->solution = TicketSolution::OPEN;
            $changed = $this->ticketRepo->persist($ticket);
            if ($changed) {
                $this->addMessage($ticket, "Das Ticket wurde wieder eröffnet.");
            }
            return $changed;
        }
        if ($ticket->status == TicketStatus::ASSIGNED) {
            $ticket->adminId = 0;
            $ticket->status = TicketStatus::NEW;
            $changed = $this->ticketRepo->persist($ticket);
            if ($changed) {
                $this->addMessage($ticket, "Der Ticketadministrator hat das Ticket wieder als Neu markiert.");
            }
            return $changed;
        }
        return false;
    }

    public function closeAssignedInactive(): int
    {
        $threshold = time() - self::INACTIVE_TIME;

        $ticketIds = $this->ticketRepo->findAssignedIds();
        $i = 0;
        foreach ($ticketIds as $id) {
            $message = $this->messageRepo->findLastMessageForTicket($id);
            if ($message != null) {
                if ($message->adminId > 0 && $message->timestamp < $threshold) {
                    $ticket = $this->ticketRepo->find($id);
                    $this->addMessage($ticket, "Das Ticket wurde automatisch geschlossen, da wir innerhalb der letzten 72 Stunden nichts mehr von dir gehört haben.");
                    $this->close($ticket, "solved");
                    $i++;
                }
            }
        }
        return $i;
    }

    public function addMessage(Ticket $ticket, string $message, int $userId = 0, int $adminId = 0, bool $informUser = true): TicketMessage
    {
        $ticketMessage = new TicketMessage();
        $ticketMessage->ticketId = $ticket->id;
        $ticketMessage->userId = $userId;
        $ticketMessage->adminId = $adminId;
        $ticketMessage->message = $message;
        $this->messageRepo->create($ticketMessage);

        if ($informUser && $ticketMessage->userId == 0) {
            $text = "Hallo!

Dein [page ticket id=" . $ticket->id . "]Ticket " . $ticket->getIdString() . "[/page] wurde aktualisiert!

[page ticket id=" . $ticket->id . "]Klicke HIER um die Änderungen anzusehen.[/page]

Dein Admin-Team";
            $this->userMessageRepo->createSystemMessage($ticket->userId, USER_MSG_CAT_ID, "Dein Ticket " . $ticket->getIdString() . ' wurde aktualisiert', $text);
        }

        return $ticketMessage;
    }

    /**
     * @return array<TicketMessage>
     */
    public function getMessages(Ticket $ticket): array
    {
        return $this->messageRepo->findByTicket($ticket->id);
    }
}
