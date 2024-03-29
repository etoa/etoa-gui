<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

use EtoA\Core\AbstractRepository;

class TicketMessageRepository extends AbstractRepository
{
    public function count(int $ticketId): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('ticket_msg')
            ->where('ticket_id = :ticket_id')
            ->setParameter('ticket_id', $ticketId)
            ->fetchOne();
    }

    /**
     * @return array<int, int>
     */
    public function countsByTicket(): array
    {
        return $this->createQueryBuilder()
            ->select("ticket_id, COUNT(*)")
            ->from('ticket_msg')
            ->groupBy('ticket_id')
            ->fetchAllKeyValue();
    }

    public function find(int $id): ?TicketMessage
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('ticket_msg')
            ->where('id = :id')
            ->setParameter('id', $id)
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
            ->fetchAllAssociative();

        return array_map(fn (array $arr) => TicketMessage::createFromArray($arr), $data);
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
            ->executeQuery();

        $message->id = (int) $this->getConnection()->lastInsertId();
    }

    /**
     * @param int[] $ticketIds
     */
    public function removeByTicketIds(array $ticketIds): void
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
        $qry->executeQuery();
    }
}
