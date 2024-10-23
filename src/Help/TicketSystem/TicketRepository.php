<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Ticket;

class TicketRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function countNew(): int
    {
        return (int) $this->createQueryBuilder('q')
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

        return (int) $this->createQueryBuilder('q')
            ->select("COUNT(*)")
            ->from('tickets')
            ->where("status = :status")
            ->andWhere('admin_id = :admin_id')
            ->setParameter('admin_id', $adminId)
            ->setParameter('status', TicketStatus::ASSIGNED)
            ->fetchOne();
    }

    public function persist(Ticket $ticket): bool
    {
        $ticket->setTimestamp(time());

        $this->entityManager->persist($ticket);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @return array<int>
     */
    public function findAssignedIds(): array
    {
        $data = $this->createQueryBuilder('q')
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

        $qry = $this->createQueryBuilder('q')
            ->delete('tickets')
            ->where('id IN(' . implode(',', array_fill(0, count($ticketIds), '?')) . ')');
        foreach ($ticketIds as $k => $id) {
            $qry->setParameter($k, $id);
        }

        return $qry->executeQuery()->rowCount();
    }

    public function removeForUser(int $userId) : void
    {
        $this->createQueryBuilder('q')
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
        return $this->createQueryBuilder('q')
            ->select("id", 'name')
            ->from('ticket_cat')
            ->orderBy('sort')
            ->addOrderBy('name')
            ->fetchAllKeyValue();
    }

    public function getCategoryName(int $catId): ?string
    {
        $data = $this->createQueryBuilder('q')
            ->select('name')
            ->from('ticket_cat')
            ->where('id = :id')
            ->setParameter('id', $catId)
            ->fetchOne();

        return $data !== false ? $data : null;
    }
}
