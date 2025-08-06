<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\AdminUser;
use EtoA\Entity\Ticket;
use EtoA\Entity\User;

class TicketRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function countNew(): int
    {
        return $this->count(['status'=>TicketStatus::NEW]);
    }

    public function countAssigned(AdminUser $admin): int
    {
        return $this->count(['status'=>TicketStatus::ASSIGNED,'admin'=>$admin]);
    }

    public function persist(Object $entity): void
    {
        $entity->setTimestamp(time());

        parent::persist($entity);
        $this->save();
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

    public function removeForUser(User $user) : void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
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
