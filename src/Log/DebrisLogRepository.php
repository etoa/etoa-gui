<?php declare(strict_types=1);

namespace EtoA\Log;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\AdminUser;
use EtoA\Entity\DebrisLog;
use EtoA\Entity\User;
use EtoA\Universe\Resources\BaseResources;

class DebrisLogRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DebrisLog::class);
    }

    /**
     * @return DebrisLog[]
     */
    public function searchLogs(DebrisLogSearch $search, int $limit = null, int $offset = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit, $offset)
            ->orderBy('q.time', 'DESC')
            ->getQuery()
            ->execute();
    }

    public function add(AdminUser $admin, User $user, BaseResources $resources): DebrisLog
    {
        $entry = (new DebrisLog())
            ->setAdmin($admin)
            ->setUser($user)
            ->setTime(time())
            ->setMetal($resources->metal)
            ->setCrystal($resources->crystal)
            ->setPlastic($resources->plastic);

        $this->persist($entry);
        $this->save();

        return $entry;
    }

    public function countBySearch(DebrisLogSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('COUNT(q)')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
