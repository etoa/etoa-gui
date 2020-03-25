<?php declare(strict_types=1);

namespace EtoA\Defense;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class DefenseDataRepository extends AbstractRepository
{
    private const DEFENSE_NAMES = 'defense.names';

    /** @var CacheProvider */
    private $cache;

    public function __construct(Connection $connection, CacheProvider $cache)
    {
        parent::__construct($connection);
        $this->cache = $cache;
    }

    public function getDefenseNames(): array
    {
        if (!$this->cache->contains(self::DEFENSE_NAMES)) {
            $names = $this->createQueryBuilder()
                ->select('def_id, def_name')
                ->addSelect()
                ->from('defense')
                ->execute()
                ->fetchAll(\PDO::FETCH_KEY_PAIR);

            $this->cache->save(self::DEFENSE_NAMES, $names);
        }

        return $this->cache->fetch(self::DEFENSE_NAMES);
    }
}
