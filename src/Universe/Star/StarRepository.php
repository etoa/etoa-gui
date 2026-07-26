<?php

declare(strict_types=1);

namespace EtoA\Universe\Star;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Cell;
use EtoA\Entity\Entity;
use EtoA\Entity\SolarType;
use EtoA\Entity\Star;

class StarRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Star::class);
    }

    /**
     * @return int[]
     */
    public function getAllIds(): array
    {
        return $this->createQueryBuilder('q')
            ->select("IDENTITY(q.entity)")
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function findStarForCell(int|Cell $cellId): ?Star
    {
        return $this->createQueryBuilder('q')
            ->innerJoin('App:Entity', 'e', 'WITH', 'e.id = IDENTITY(q.entity)')
            ->where('e.cell = :cellId')
            ->andWhere('e.pos = 0')
            ->setParameters([
                'cellId' => $cellId,
            ])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function add(Entity $entity, SolarType $type, bool $flush = true): void
    {
        $star = new Star();
        $star->setSolarType($type);
        $entity->setStar($star);

        if ($flush) {
            $this->save();
        }
    }

    public function update(int $id, ?string $name, int $typeId = null): bool
    {
        $qb = $this->createQueryBuilder('q')
            ->update('stars')
            ->set('name', ':name')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'name' => stripBBCode((string) $name),
            ]);

        if ($typeId !== null) {
            $qb
                ->set('type_id', ':type_id')
                ->setParameter('type_id', $typeId);
        }

        return (bool) $qb
            ->executeQuery()
            ->rowCount();
    }

    /**
     * @return array<int, array{name: string, cnt: string}>
     */
    public function getNumberOfNamedSystemsByType(): array
    {
        return $this->createQueryBuilder('q')
            ->select('COUNT(IDENTITY(q.entity)) as cnt')
            ->addSelect('t.name as name')
            ->innerJoin('App:SolarType', 't', 'WITH', 'q.solarType = t.id')
            ->where('q.name = :name')
            ->setParameters([
                'name' => '',
            ])
            ->groupBy('t.id')
            ->orderBy('cnt')
            ->getQuery()
            ->execute();
    }
}
