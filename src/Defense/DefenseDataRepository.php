<?php declare(strict_types=1);

namespace EtoA\Defense;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Defense;
use EtoA\Entity\DefenseCategory;
use EtoA\Entity\Race;

class DefenseDataRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Defense::class);
    }

    /**
     * @return array<int, Defense>
     */
    public function getDefenseNames(bool $showAll = false, ?DefenseSort $orderBy = null): array
    {
        $search = null;
        if (!$showAll) {
            $search = DefenseSearch::create()->show();
        }

        return $this->searchDefenseNames($search, $orderBy);
    }


    /**
     * @return array<int, string>
     */
    public function searchDefenseNames(?DefenseSearch $search = null, ?DefenseSort $orderBy = null, ?int $limit = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, $orderBy ?? DefenseSort::name(), $limit)
            ->getQuery()
            ->execute();
    }

    /**
     * @return array<int, float>
     */
    public function getDefensePoints(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('q.id', 'q.points')
            ->getQuery()
            ->execute();

        return array_column($data, 'points', 'id');
    }

    public function updateDefensePoints(int $defenseId, float $points): void
    {
        $this->createQueryBuilder('q')
            ->update('defense')
            ->set('def_points', ':points')
            ->where('def_id = :defenseId')
            ->setParameters([
                'defenseId' => $defenseId,
                'points' => $points,
            ])
            ->executeQuery();
    }

    public function getDefense(int $defenseId): ?Defense
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('defense')
            ->where('def_show = 1')
            ->andWhere('def_id = :defenseId')
            ->setParameter('defenseId', $defenseId)
            ->fetchAssociative();

        return $data !== false ? new Defense($data) : null;
    }

    /**
     * @return Defense[]
     */
    public function getDefenseByRace(int|Race $raceId): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.race = :raceId')
            ->andWhere('q.buildable = 1')
            ->andWhere('q.show = 1')
            ->setParameter('raceId', $raceId)
            ->orderBy('q.order')
            ->getQuery()
            ->execute();
    }

    /**
     * @return Defense[]
     */
    public function getDefenseByCategory(int|DefenseCategory $categoryId, $sortBy = 'order', $order = 'ASC'): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.cat = :categoryId')
            ->andWhere('q.buildable = 1')
            ->andWhere('q.show = 1')
            ->setParameter('categoryId', $categoryId)
            ->orderBy("q.$sortBy",$order)
            ->getQuery()
            ->execute();
    }

    /**
     * @return array<int, Defense>
     */
    public function getAllDefenses(): array
    {
        return $this->findBy(array(), array('order'=>'DESC'));
    }

    /**
     * @return Defense[]
     */
    public function searchDefense(DefenseSearch $search, ?DefenseSort $sort = null, ?int $limit = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, $sort, $limit)
            ->select()
            ->getQuery()
            ->execute();
    }
}
