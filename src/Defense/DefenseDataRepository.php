<?php declare(strict_types=1);

namespace EtoA\Defense;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class DefenseDataRepository extends AbstractRepository
{
    private const DEFENSE_NAMES = 'defense.names';

    private CacheProvider $cache;

    public function __construct(Connection $connection, CacheProvider $cache)
    {
        parent::__construct($connection);
        $this->cache = $cache;
    }

    /**
     * @return array<int, string>
     */
    public function getDefenseNames(bool $showAll = false): array
    {
        $qb = $this->createQueryBuilder()
            ->select('def_id, def_name')
            ->addSelect()
            ->from('defense');

        if (!$showAll) {
            $qb
                ->where('def_show = 1');
        }

        return $qb
            ->execute()
            ->fetchAllKeyValue();
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
}
