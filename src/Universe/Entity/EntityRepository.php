<?php

declare(strict_types=1);

namespace EtoA\Universe\Entity;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Core\Database\AbstractSort;
use EtoA\Entity\Entity;

class EntityRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entity::class);
    }

    public function countEntitiesOfCodeInSector(int $sx, int $sy, string $code): int
    {

        return $this->createQueryBuilder('q')
            ->select('count(distinct(q.id))')
            ->innerJoin('App:Cell', 'c', 'WITH', 'q.cellId = c.id')
            ->where('q.code = :code')
            ->andWhere('c.sx = :sx')
            ->andWhere('c.sy = :sy')
            ->setParameters([
                'sx' => $sx,
                'sy' => $sy,
                'code' => $code,
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countWithUserInSector(int $sx, int $sy): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('COUNT(DISTINCT(q.id))')
            ->innerJoin('App:Cell', 'c', 'WITH', 'q.cellId = c.id')
            ->innerJoin('App:Planet', 'p', 'WITH', 'p.id = q.id AND p.userId > 0')
            ->where('q.code = :code')
            ->andWhere('c.sx = :sx')
            ->andWhere('c.sy = :sy')
            ->setParameters([
                'sx' => $sx,
                'sy' => $sy,
                'code' => EntityType::PLANET,
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findRandomId(string $code): ?int
    {
        $id = $this->createQueryBuilder('q')
            ->select('id')
            ->from('entities')
            ->where('code = :code')
            ->orderBy('RAND()')
            ->setParameters([
                'code' => $code,
            ])
            ->fetchOne();

        return $id !== false ? (int) $id : null;
    }

    public function getEntity(int $id): ?Entity
    {
        $data = $this->getEntityCoordinatesQueryBuilder()
            ->where('e.id = :id')
            ->setParameters(['id' => $id])
            ->fetchAssociative();

        return $data !== false ? new Entity($data) : null;
    }

    /**
     * @param string[] $codes
     * @return array<Entity>
     */
    public function findRandomByCodes(array $codes, int $limit): array
    {
        if (count($codes) == 0) {
            return [];
        }

        $data = $this->getEntityCoordinatesQueryBuilder()
            ->where('code IN (' . implode(',', array_fill(0, count($codes), '?')) . ')')
            ->andWhere('pos = 0')
            ->orderBy('RAND()')
            ->setParameters(array_values($codes))
            ->setMaxResults($limit)
            ->fetchAllAssociative();

        return array_map(fn (array $arr) => new Entity($arr), $data);
    }

    public function findIncludeCell(int $id): ?Entity
    {
        $data = $this->getEntityCoordinatesQueryBuilder()
            ->where('e.id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->fetchAssociative();

        return $data !== false ? new Entity($data) : null;
    }

    public function findByCellAndPosition(int $cellId, int $position): ?Entity
    {
        $data = $this->getEntityCoordinatesQueryBuilder()
            ->where('e.cell_id = :cellId')
            ->andWhere('e.pos = :position')
            ->setParameters([
                'cellId' => $cellId,
                'position' => $position,
            ])
            ->fetchAssociative();

        return $data !== false ? new Entity($data) : null;
    }

    public function findByCoordinates(EntityCoordinates $coordinates): ?Entity
    {
        $data = $this->getEntityCoordinatesQueryBuilder()
            ->where('c.sx = :sx')
            ->andWhere('c.sy = :sy')
            ->andWhere('c.cx = :cx')
            ->andWhere('c.cy = :cy')
            ->andWhere('e.pos = :pos')
            ->setParameters([
                'sx' => $coordinates->sx,
                'sy' => $coordinates->sy,
                'cx' => $coordinates->cx,
                'cy' => $coordinates->cy,
                'pos' => $coordinates->pos,
            ])
            ->fetchAssociative();

        return $data !== false ? new Entity($data) : null;
    }

    /**
     * @return Entity[]
     */
    public function searchEntities(EntitySearch $search, EntitySort $sort = null): array
    {
        $data = $this->getEntityCoordinatesQueryBuilder($search, $sort)
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new Entity($row), $data);
    }

    public function getAllianceMarketId(): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('id')
            ->from('entities')
            ->where('code = :code')
            ->setParameter('code', EntityType::ALLIANCE_MARKET)
            ->fetchOne();
    }

    public function add(int $cellId, string $code, int $pos = 0): int
    {
        $this->createQueryBuilder('q')
            ->insert('entities')
            ->values([
                'cell_id' => ':cell_id',
                'code' => ':code',
                'pos' => ':pos',
            ])
            ->setParameters([
                'cell_id' => $cellId,
                'code' => $code,
                'pos' => $pos,
            ])
            ->executeQuery();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function updateCode(int $id, string $code): void
    {
        $this->createQueryBuilder('q')
            ->update('entities')
            ->set('code', ':code')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'code' => $code,
            ])
            ->executeQuery();
    }

    /**
     * @return array<int, string>
     */
    public function getEntityCodes(): array
    {
        return $this->createQueryBuilder('q')
            ->select('id, code')
            ->from('entities')
            ->fetchAllKeyValue();
    }

    public function getMaxEntityId(): int
    {
        return (int) $this->createQueryBuilder('q')
            ->select('MAX(id)')
            ->from('entities')
            ->fetchOne();
    }

    /**
     * @return EntityLabel[]
     */
    public function searchEntityLabels(EntitySearch $search, EntityLabelSort $sort = null, int $limit = null, int $offset = null): array
    {
        $data = $this->entityLabelQuerBuilder($search, $sort, $limit, $offset)
            ->fetchAllAssociative();

        $entities = [];
        foreach ($data as $row) {
            $entity = new EntityLabel($row);
            $entities[$entity->getId()] = $entity;
        }

        return $entities;
    }

    public function searchEntityLabel(EntitySearch $search, EntityLabelSort $sort = null): ?EntityLabel
    {
        $data = $this->entityLabelQuerBuilder($search, $sort, 1)
            ->fetchAssociative();

        return $data !== false ? new EntityLabel($data) : null;
    }

    private function entityLabelQuerBuilder(EntitySearch $search, EntityLabelSort $sort = null, int $limit = null, int $offset = null): QueryBuilder
    {
        return $this->getEntityCoordinatesQueryBuilder($search, $sort, $limit, $offset)
            ->addSelect('planets.planet_name, planets.planet_user_main, planets.planet_type_id as planet_type, planets.planet_image as planet_image')
            ->addSelect('stars.name as star_name, stars.type_id as star_type')
            ->addSelect('wormholes.persistent as wormhole_persistent, wormholes.target_id as wormhole_target')
            ->addSelect('users.user_nick, users.user_id')
            ->leftJoin('e', 'planets', 'planets', 'e.id = planets.id')
            ->leftJoin('planets', 'users', 'users', 'users.user_id = planets.planet_user_id')
            ->leftJoin('e', 'stars', 'stars', 'e.id = stars.id')
            ->leftJoin('e', 'wormholes', 'wormholes', 'e.id = wormholes.id');
    }

    public function countEntityLabels(EntityLabelSearch $search = null): int
    {
        return (int) $this->getEntityCoordinatesQueryBuilder($search)
            ->select('COUNT(*)')
            ->leftJoin('e', 'planets', 'planets', 'e.id = planets.id')
            ->leftJoin('planets', 'users', 'users', 'users.user_id = planets.planet_user_id')
            ->leftJoin('e', 'stars', 'stars', 'e.id = stars.id')
            ->fetchOne();
    }

    private function getEntityCoordinatesQueryBuilder(EntitySearch $search = null, AbstractSort $sort = null, int $limit = null, int $offset = null): QueryBuilder
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, $sort, $limit, $offset)
            ->select(
                'e.id',
                'c.id as cid',
                'code',
                'pos',
                'sx',
                'sy',
                'cx',
                'cy'
            )
            ->from('entities', 'e')
            ->innerJoin('e', 'cells', 'c', 'e.cell_id = c.id');
    }
}
