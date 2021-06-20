<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

use Doctrine\DBAL\Connection;
use EtoA\Admin\AdminUserRepository;
use EtoA\Core\AbstractRepository;
use EtoA\User\UserRepository;

class TicketMessageRepository extends AbstractRepository
{
    private AdminUserRepository $adminUserRepo;
    private UserRepository $userRepo;

    public function __construct(
        Connection $connection,
        AdminUserRepository $adminUserRepo,
        UserRepository $userRepo
    ) {
        parent::__construct($connection);
        $this->adminUserRepo = $adminUserRepo;
        $this->userRepo = $userRepo;
    }

    public function count(int $ticketId): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('ticket_msg')
            ->where('ticket_id = :ticket_id')
            ->setParameter('ticket_id', $ticketId)
            ->execute()
            ->fetchOne();
    }

    public function find(int $id): ?TicketMessage
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('ticket_msg')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetchAssociative();
        return $data ? TicketMessage::createFromArray($data) : null;
    }

    /**
     * @return array<TicketMessage>
     */
    public function findByTicket(int $ticketId): array
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('ticket_msg')
            ->where('ticket_id = :ticket_id')
            ->orderBy('timestamp', 'ASC')
            ->setParameter('ticket_id', $ticketId)
            ->execute()
            ->fetchAllAssociative();
        return collect($data)
            ->map(fn ($arr) => TicketMessage::createFromArray($arr))
            ->toArray();
    }

    public function findLastMessageForTicket(int $ticketId): ?TicketMessage
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('ticket_msg')
            ->where('ticket_id = :ticket_id')
            ->orderBy('timestamp', 'DESC')
            ->addOrderBy('id', 'DESC')
            ->setParameter('ticket_id', $ticketId)
            ->execute()
            ->fetchAssociative();
        return $data ? TicketMessage::createFromArray($data) : null;
    }

    public function create(TicketMessage $message): void
    {
        if (!isset($message->timestamp)) {
            $message->timestamp = time();
        }

        $this->createQueryBuilder()
            ->insert('ticket_msg')
            ->values([
                'ticket_id' => ':ticket_id',
                'user_id' => ':user_id',
                'admin_id' => ':admin_id',
                'timestamp' => ':timestamp',
                'message' => ':message',
            ])
            ->setParameters([
                'ticket_id' => $message->ticketId,
                'user_id' => $message->userId ?? 0,
                'admin_id' => $message->adminId ?? 0,
                'timestamp' => $message->timestamp,
                'message' => $message->message,
            ])
            ->execute();

        $message->id = (int) $this->getConnection()->lastInsertId();
    }

    public function getAuthorNick(TicketMessage $message): string
    {
        if ($message->userId > 0) {
            return $this->userRepo->getNick($message->userId);
        }
        if ($message->adminId > 0) {
            return $this->adminUserRepo->getNick($message->adminId) . " (Admin)";
        }
        return "System";
    }

    public function removeByTicketIds(...$ticketIds): void
    {
        if (count($ticketIds) == 0) {
            return;
        }

        $qry = $this->createQueryBuilder()
            ->delete('ticket_msg')
            ->where('ticket_id IN('.implode(',', array_fill(0, count($ticketIds), '?')).')');
        foreach ($ticketIds as $k => $id) {
            $qry->setParameter($k, $id);
        }
        $qry->execute();
    }
}
