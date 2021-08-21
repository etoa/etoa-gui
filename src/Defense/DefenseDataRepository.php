<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\Core\AbstractRepository;

class DefenseDataRepository extends AbstractRepository
{
    private const DEFENSE_NAMES = 'defense.names';

    /**
     * @return array<int, string>
     */
    public function getDefenseNames(bool $showAll = false, DefenseSort $orderBy = null): array
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
    public function searchDefenseNames(DefenseSearch $search = null, DefenseSort $orderBy = null, int $limit = null): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder(), $search, $orderBy ?? DefenseSort::name(), $limit)
            ->select('def_id', 'def_name')
            ->addSelect()
            ->from('defense')
            ->execute()
            ->fetchAllKeyValue();
    }

    /**
     * @return array<int, float>
     */
    public function getDefensePoints(): array
    {
        $data = $this->createQueryBuilder()
            ->select('def_id', 'def_points')
            ->from('defense')
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (float) $value, $data);
    }

    public function updateDefensePoints(int $defenseId, float $points): void
    {
        $this->createQueryBuilder()
            ->update('defense')
            ->set('def_points', ':points')
            ->where('def_id = :defenseId')
            ->setParameters([
                'defenseId' => $defenseId,
                'points' => $points,
            ])
            ->execute();
    }

    public function getDefense(int $defenseId): ?Defense
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('defense')
            ->where('def_show = 1')
            ->andWhere('def_id = :defenseId')
            ->setParameter('defenseId', $defenseId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new Defense($data) : null;
    }

    /**
     * @return Defense[]
     */
    public function getDefenseByRace(int $raceId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('defense')
            ->where('def_race_id = :raceId')
            ->andWhere('def_buildable = 1')
            ->andWhere('def_show = 1')
            ->setParameter('raceId', $raceId)
            ->orderBy('def_order')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Defense($row), $data);
    }

    /**
     * @return Defense[]
     */
    public function getDefenseByCategory(int $categoryId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('defense')
            ->where('def_cat_id = :categoryId')
            ->andWhere('def_buildable = 1')
            ->andWhere('def_show = 1')
            ->setParameter('categoryId', $categoryId)
            ->orderBy('def_order')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Defense($row), $data);
    }

    /**
     * @return array<int, Defense>
     */
    public function getAllDefenses(): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('defense')
            ->orderBy('def_order')
            ->execute()
            ->fetchAllAssociative();

        $result = [];
        foreach ($data as $row) {
            $defense = new Defense($row);
            $result[$defense->id] = $defense;
        }

        return $result;
    }

    /**
     * @return Defense[]
     */
    public function searchDefense(DefenseSearch $search, DefenseSort $sort = null, int $limit = null): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search, $sort, $limit)
            ->select('*')
            ->from('defense')
            ->execute()
            ->fetchAllAssociative();

        $results = [];
        foreach ($data as $row) {
            $defense = new Defense($row);
            $results[$defense->id] = $defense;
        }

        return $results;
    }
}
