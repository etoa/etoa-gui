<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

use EtoA\Admin\AdminUserRepository;
use EtoA\Entity\Ticket;
use EtoA\Entity\TicketMessage;
use EtoA\Message\MessageCategoryId;
use EtoA\Message\MessageRepository;
use EtoA\User\UserRepository;

class TicketService
{
    private TicketRepository $ticketRepo;
    private TicketMessageRepository $messageRepo;
    private AdminUserRepository $adminUserRepo;
    private UserRepository $userRepo;
    private MessageRepository $userMessageRepo;

    const INACTIVE_TIME = 72 * 3600; // 72 hours

    public function __construct(
        TicketRepository $ticketRepo,
        TicketMessageRepository $messageRepo,
        AdminUserRepository $adminUserRepo,
        UserRepository $userRepo,
        MessageRepository $userMessageRepo
    ) {
        $this->ticketRepo = $ticketRepo;
        $this->messageRepo = $messageRepo;
        $this->adminUserRepo = $adminUserRepo;
        $this->userRepo = $userRepo;
        $this->userMessageRepo = $userMessageRepo;
    }

    public function create(int $userId, int $catId, string $message): Ticket
    {
        $ticket = new Ticket();
        $ticket->setSolution(TicketSolution::OPEN);
        $ticket->setStatus(TicketStatus::NEW);
        $ticket->setUserId($userId);
        $ticket->setCatId($catId);
        $this->ticketRepo->persist($ticket);

        $ticketMessage = new TicketMessage();
        $ticketMessage->setTicketId($ticket->getId());
        $ticketMessage->setUserId($ticket->getUserId());
        $ticketMessage->setMessage($message);
        $this->messageRepo->create($ticketMessage);

        $text = "Hallo!

Dein [page ticket id=" . $ticket->getId() . "]Ticket #" . $ticket->getIdString() . "[/page] wurde erfolgreich erstellt.
Es wird sich in Kürze ein Admin um dein Anliegen kümmern.

Dein Admin-Team";
        $this->userMessageRepo->createSystemMessage($ticket->getUserId(), MessageCategoryId::USER, "Dein Ticket " . $ticket->getIdString() . ' wurde erstellt', $text);

        return $ticket;
    }

    public function assign(Ticket $ticket, int $adminId): bool
    {
        $ticket->setAdminId($adminId);
        $ticket->setStatus(TicketStatus::ASSIGNED);
        $changed = $this->ticketRepo->persist($ticket);
        if ($changed) {
            $this->addMessage($ticket, "Das Ticket wurde dem Administrator " . $this->adminUserRepo->getNick($ticket->getAdminId()) . " zugewiesen.");
        }

        return $changed;
    }

    public function close(Ticket $ticket, string $solution): bool
    {
        if ($ticket->getStatus() == TicketStatus::ASSIGNED) {
            $ticket->setStatus(TicketStatus::CLOSED);
            $ticket->setSolution($solution);
            $changed = $this->ticketRepo->persist($ticket);
            if ($changed) {
                $this->addMessage($ticket, "Das Ticket wurde geschlossen und als " . TicketSolution::label($ticket->getSolution()) . " gekennzeichnet.");
            }

            return $changed;
        }

        return false;
    }

    public function reopen(Ticket $ticket): bool
    {
        if ($ticket->getStatus() == TicketStatus::CLOSED) {
            $ticket->setAdminId(0);
            $ticket->setStatus(TicketStatus::NEW);
            $ticket->setSolution(TicketSolution::OPEN);
            $changed = $this->ticketRepo->persist($ticket);
            if ($changed) {
                $this->addMessage($ticket, "Das Ticket wurde wieder eröffnet.");
            }

            return $changed;
        }
        if ($ticket->getStatus() == TicketStatus::ASSIGNED) {
            $ticket->setAdminId(0);
            $ticket->setStatus(TicketStatus::NEW);
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

    public function addMessage(Ticket $ticket, string $message, int $userId = 0, int $adminId = 0, bool $informUser = true): TicketMessage
    {
        $ticketMessage = new TicketMessage();
        $ticketMessage->setTicketId( $ticket->getId());
        $ticketMessage->setUserId( $userId);
        $ticketMessage->setAdminId( $adminId);
        $ticketMessage->setMessage($message);
        $this->messageRepo->create($ticketMessage);

        if ($informUser && $ticketMessage->getUserId() == 0) {
            $text = "Hallo!

Dein [page ticket id=" . $ticket->getId() . "]Ticket " . $ticket->getIdString() . "[/page] wurde aktualisiert!

[page ticket id=" . $ticket->getId() . "]Klicke HIER um die Änderungen anzusehen.[/page]

Dein Admin-Team";
            $this->userMessageRepo->createSystemMessage($ticket->getUserId(), MessageCategoryId::USER, "Dein Ticket " . $ticket->getIdString() . ' wurde aktualisiert', $text);
        }

        return $ticketMessage;
    }

    /**
     * @return array<TicketMessage>
     */
    public function getMessages(Ticket $ticket): array
    {
        return $this->messageRepo->findByTicket($ticket->getId());
    }

    public function getAuthorNick(TicketMessage $message): string
    {
        if ($message->getUserId() > 0) {
            return $this->userRepo->getNick($message->getUserId());
        }
        if ($message->getAdminId() > 0) {
            return $this->adminUserRepo->getNick($message->getAdminId()) . " (Admin)";
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
