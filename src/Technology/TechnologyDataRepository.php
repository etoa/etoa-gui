<?php declare(strict_types=1);

namespace EtoA\Technology;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Technology;
use EtoA\Entity\TechnologyType;

class TechnologyDataRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Technology::class);
    }

    /**
     * @return array<int, Technology>
     */
    public function getTechnologyNames(bool $showAll = false, TechnologySort $orderBy = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->innerJoin('App:TechnologyType', 'tt', 'WITH', 'q.type = tt.id');

        if (!$showAll) {
            $qb->where('q.show = 1');
        }

        $orderBy = $orderBy ?? TechnologySort::type();
        foreach ($orderBy->sorts as $sort) {
            $qb->addOrderBy($sort);
        }

        return $qb->getQuery()
            ->execute();
    }

    public function getTechnologyName(int $technologyId): string
    {
        return (string) $this->createQueryBuilder('q')
            ->select('t.tech_name')
            ->from('technologies', 't')
            ->where('t.tech_id = :techId')
            ->setParameter('techId', $technologyId)
            ->fetchOne();
    }

    /**
     * @return Technology[]
     */
    public function getTechnologies(): array
    {
        return $this->findBy(['show'=>1],['order'=>'DESC','name'=>'DESC']);
    }

    public function getTechnology(int $techId): ?Technology
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('technologies')
            ->where('tech_show = 1')
            ->andWhere('tech_id = :techId')
            ->setParameter('techId', $techId)
            ->fetchAssociative();

        return $data !== false ? new Technology($data) : null;
    }

    /**
     * @return Technology[]
     */
    public function getTechnologiesByType(int|TechnologyType $typeId): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.show = 1')
            ->andWhere('q.type = :typeId')
            ->setParameter('typeId', $typeId)
            ->getQuery()
            ->execute();
    }
}
