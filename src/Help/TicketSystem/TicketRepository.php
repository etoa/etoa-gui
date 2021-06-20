<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

use Doctrine\DBAL\Connection;
use EtoA\Admin\AdminUserRepository;
use EtoA\Core\AbstractRepository;
use EtoA\Message\MessageRepository;

class TicketRepository extends AbstractRepository
{
    private AdminUserRepository $adminUserRepo;
    private TicketMessageRepository $messageRepo;
    private MessageRepository $userMessageRepo;

    const INACTIVE_TIME = 72 * 3600; // 72 hours

    public function __construct(
        Connection $connection,
        AdminUserRepository $adminUserRepo,
        TicketMessageRepository $messageRepo,
        MessageRepository $userMessageRepo
    ) {
        parent::__construct($connection);
        $this->adminUserRepo = $adminUserRepo;
        $this->messageRepo = $messageRepo;
        $this->userMessageRepo = $userMessageRepo;
    }

    public function countNew(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('tickets')
            ->where("status = 'new'")
            ->execute()
            ->fetchOne();
    }

    public function countAssigned(int $adminId): int
    {
        if ($adminId == 0) {
            return 0;
        }
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('tickets')
            ->where("status = 'assigned'")
            ->andWhere('admin_id = :admin_id')
            ->setParameter('admin_id', $adminId)
            ->execute()
            ->fetchOne();
    }

    public function find(int $id): ?Ticket
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('tickets')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetchAssociative();
        return $data ? Ticket::createFromArray($data) : null;
    }

    /**
     * @return array<Ticket>
     */
    public function findBy(array $args = []): array
    {
        $qry = $this->createQueryBuilder()
            ->select("*")
            ->from('tickets')
            ->orderBy('status')
            ->addOrderBy('timestamp', 'DESC');
        foreach ($args as $key => $val) {
            $qry->andWhere($key . ' = :' . $key)
                ->setParameter($key, $val);
        }
        $data = $qry->execute()
            ->fetchAllAssociative();

        return collect($data)
            ->map(fn ($arr) => Ticket::createFromArray($arr))
            ->toArray();
    }

    public function persist(Ticket $ticket): bool
    {
        $ticket->timestamp = time();

        if ($ticket->id > 0) {
            $affected = $this->createQueryBuilder()
                ->update('tickets')
                ->set('status', ':status')
                ->set('solution', ':solution')
                ->set('cat_id', ':cat_id')
                ->set('admin_id', ':admin_id')
                ->set('admin_comment', ':admin_comment')
                ->set('timestamp', ':timestamp')
                ->where('id = :ticket_id')
                ->setParameters([
                    'ticket_id' => $ticket->id,
                    'status' => $ticket->status,
                    'solution' => $ticket->solution,
                    'cat_id' => $ticket->catId,
                    'admin_id' => $ticket->adminId,
                    'admin_comment' => $ticket->adminComment,
                    'timestamp' => $ticket->timestamp,
                ])
                ->execute();
            return (int) $affected > 0;
        }
        $this->createQueryBuilder()
            ->insert('tickets')
            ->values([
                'status' => ':status',
                'solution' => ':solution',
                'user_id' => ':user_id',
                'cat_id' => ':cat_id',
                'admin_id' => ':admin_id',
                'admin_comment' => ':admin_comment',
                'timestamp' => ':timestamp',
            ])
            ->setParameters([
                'status' => $ticket->status,
                'solution' => $ticket->solution,
                'user_id' => $ticket->userId,
                'cat_id' => $ticket->catId,
                'admin_id' => $ticket->adminId ?? 0,
                'admin_comment' => $ticket->adminComment,
                'timestamp' => $ticket->timestamp,
            ])
            ->execute();
        $ticket->id = (int) $this->getConnection()->lastInsertId();
        return true;
    }

    public function create(int $userId, int $catId, string $message): Ticket
    {
        $ticket = new Ticket();
        $ticket->solution = 'open';
        $ticket->status = 'new';
        $ticket->userId = $userId;
        $ticket->catId = $catId;
        $this->persist($ticket);

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
        $ticket->status = "assigned";
        $changed = $this->persist($ticket);
        if ($changed) {
            $this->addMessage($ticket, "Das Ticket wurde dem Administrator " . $this->adminUserRepo->getNick($ticket->adminId) . " zugewiesen.");
        }
        return $changed;
    }

    public function close(Ticket $ticket, $solution): bool
    {
        if ($ticket->status == "assigned") {
            $ticket->status = "closed";
            $ticket->solution = $solution;
            $changed = $this->persist($ticket);
            if ($changed) {
                $this->addMessage($ticket, "Das Ticket wurde geschlossen und als " . TicketSolution::label($ticket->solution) . " gekennzeichnet.");
            }
            return $changed;
        }
        return false;
    }

    public function reopen(Ticket $ticket): bool
    {
        if ($ticket->status == "closed") {
            $ticket->adminId = 0;
            $ticket->status = "new";
            $ticket->solution = "open";
            $changed = $this->persist($ticket);
            if ($changed) {
                $this->addMessage($ticket, "Das Ticket wurde wieder eröffnet.");
            }
            return $changed;
        }
        if ($ticket->status == "assigned") {
            $ticket->adminId = 0;
            $ticket->status = "new";
            $changed = $this->persist($ticket);
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

        $ticketIds = collect($this->createQueryBuilder()
            ->select("id")
            ->from('tickets')
            ->where("status = 'assigned'")
            ->execute()
            ->fetchAllAssociative())
            ->map(fn ($ticket) => (int) $ticket['id']);
        $i = 0;
        foreach ($ticketIds as $id) {
            $message = $this->messageRepo->findLastMessageForTicket($id);
            if ($message != null) {
                if ($message->adminId > 0 && $message->timestamp < $threshold) {
                    $ticket = $this->find($id);
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

    /**
     * @return array<int>
     */
    public function findOrphanedIds(): array
    {
        return $this->createQueryBuilder()
            ->select('id')
            ->from('tickets', 't')
            ->where('!(t.user_id IN(SELECT u.user_id FROM users u))')
            ->execute()
            ->fetchFirstColumn();
    }

    public function removeByIds(...$ticketIds): int
    {
        if (count($ticketIds) == 0) {
            return 0;
        }

        $this->messageRepo->removeByTicketIds(...$ticketIds);

        $qry = $this->createQueryBuilder()
            ->delete('tickets')
            ->where('id IN(' . implode(',', array_fill(0, count($ticketIds), '?')) . ')');
        foreach ($ticketIds as $k => $id) {
            $qry->setParameter($k, $id);
        }
        $affected = (int) $qry->execute();
        return $affected;
    }

    public function findAllCategoriesAsMap()
    {
        return $this->createQueryBuilder()
            ->select("id", 'name')
            ->from('ticket_cat')
            ->orderBy('sort')
            ->addOrderBy('name')
            ->execute()
            ->fetchAllKeyValue();
    }

    public function getCategoryName(int $catId): ?string
    {
        $data = $this->createQueryBuilder()
            ->select('name')
            ->from('ticket_cat')
            ->where('id = :id')
            ->setParameter('id', $catId)
            ->execute()
            ->fetchOne();
        return $data !== false ? $data : null;
    }
}
