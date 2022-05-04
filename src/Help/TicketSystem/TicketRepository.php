<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

use EtoA\Core\AbstractRepository;

class TicketRepository extends AbstractRepository
{
    public function countNew(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(*)")
            ->from('tickets')
            ->where("status = :status")
            ->setParameter('status', TicketStatus::NEW)
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
            ->where("status = :status")
            ->andWhere('admin_id = :admin_id')
            ->setParameter('admin_id', $adminId)
            ->setParameter('status', TicketStatus::ASSIGNED)
            ->fetchOne();
    }

    public function find(int $id): ?Ticket
    {
        $data = $this->createQueryBuilder()
            ->select("*")
            ->from('tickets')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        return $data ? Ticket::createFromArray($data) : null;
    }

    /**
     * @param array<string, int|string> $args
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
        $data = $qry
            ->fetchAllAssociative();

        return array_map(fn (array $arr) => Ticket::createFromArray($arr), $data);
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
                ->executeQuery()
                ->rowCount();

            return $affected > 0;
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
            ->executeQuery();
        $ticket->id = (int) $this->getConnection()->lastInsertId();

        return true;
    }

    /**
     * @return array<int>
     */
    public function findAssignedIds(): array
    {
        $data = $this->createQueryBuilder()
            ->select("id")
            ->from('tickets')
            ->where("status = :status")
            ->setParameter('status', TicketStatus::ASSIGNED)
            ->fetchFirstColumn();

        return array_map(fn ($val) => (int) $val, $data);
    }

    /**
     * @param int[] $ticketIds
     */
    public function removeByIds(array $ticketIds): int
    {
        if (count($ticketIds) == 0) {
            return 0;
        }

        $qry = $this->createQueryBuilder()
            ->delete('tickets')
            ->where('id IN(' . implode(',', array_fill(0, count($ticketIds), '?')) . ')');
        foreach ($ticketIds as $k => $id) {
            $qry->setParameter($k, $id);
        }

        return $qry->executeQuery()->rowCount();
    }

    public function removeForUser(int $userId) : void
    {
        $this->createQueryBuilder()
            ->delete('tickets')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery();
    }

    /**
     * @return array<int, string>
     */
    public function findAllCategoriesAsMap(): array
    {
        return $this->createQueryBuilder()
            ->select("id", 'name')
            ->from('ticket_cat')
            ->orderBy('sort')
            ->addOrderBy('name')
            ->fetchAllKeyValue();
    }

    public function getCategoryName(int $catId): ?string
    {
        $data = $this->createQueryBuilder()
            ->select('name')
            ->from('ticket_cat')
            ->where('id = :id')
            ->setParameter('id', $catId)
            ->fetchOne();

        return $data !== false ? $data : null;
    }
}
