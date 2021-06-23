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
    public function getDefenseNames(): array
    {
        if (!$this->cache->contains(self::DEFENSE_NAMES)) {
            $names = $this->createQueryBuilder()
                ->select('def_id, def_name')
                ->addSelect()
                ->from('defense')
                ->execute()
                ->fetchAllKeyValue();

            $this->cache->save(self::DEFENSE_NAMES, $names);
        }

        return $this->cache->fetch(self::DEFENSE_NAMES);
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

        return array_map(fn($row) => new Defense($row), $data);
    }
}
