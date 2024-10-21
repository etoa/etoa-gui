<?php declare(strict_types=1);

namespace EtoA\Technology;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Technology;

class TechnologyDataRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Technology::class);
    }

    /**
     * @return array<int, string>
     */
    public function getTechnologyNames(bool $showAll = false, TechnologySort $orderBy = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select('t.tech_id', 't.tech_name')
            ->from('technologies', 't')
            ->innerJoin('t', 'tech_types', 'tt', 't.tech_type_id = tt.type_id');

        if (!$showAll) {
            $qb->where('t.tech_show = 1');
        }

        $orderBy = $orderBy ?? TechnologySort::type();
        foreach ($orderBy->sorts as $sort) {
            $qb->addOrderBy($sort);
        }

        return $qb
            ->fetchAllKeyValue();
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

        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->where('tech_show = 1')
            ->orderBy('tech_order')
            ->addOrderBy('tech_name')
            ->getQuery()
            ->execute();

        $technologies = [];
        foreach ($data as $row) {
            $technologies[$row->getId()] = $row;
        }

        return $technologies;
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
    public function getTechnologiesByType(int $typeId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('technologies')
            ->where('tech_show = 1')
            ->andWhere('tech_type_id = :typeId')
            ->setParameter('typeId', $typeId)
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new Technology($row), $data);
    }
}
