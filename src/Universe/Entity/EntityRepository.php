<?php

declare(strict_types=1);

namespace EtoA\Universe\Entity;

use Doctrine\DBAL\Query\QueryBuilder;
use EtoA\Core\AbstractRepository;
use EtoA\Core\Database\AbstractSort;

class EntityRepository extends AbstractRepository
{
    public function countEntitiesOfCodeInSector(int $sx, int $sy, string $code): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(e.id)')
            ->from('entities', 'e')
            ->innerJoin('e', 'cells', 'c', 'e.cell_id = c.id')
            ->where('code = :code')
            ->andWhere('sx = :sx')
            ->andWhere('sy = :sy')
            ->setParameters([
                'sx' => $sx,
                'sy' => $sy,
                'code' => $code,
            ])
            ->execute()
            ->fetchOne();
    }

    public function findRandomId(string $code): ?int
    {
        $id = $this->createQueryBuilder()
            ->select('id')
            ->from('entities')
            ->where('code = :code')
            ->orderBy('RAND()')
            ->setParameters([
                'code' => $code,
            ])
            ->execute()
            ->fetchOne();

        return $id !== false ? (int) $id : null;
    }

    public function getEntity(int $id): ?Entity
    {
        $data = $this->getEntityCoordinatesQueryBuilder()
            ->where('e.id = :id')
            ->setParameters(['id' => $id])
            ->execute()
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
            ->execute()
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
            ->execute()
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
            ->execute()
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
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new Entity($data) : null;
    }

    /**
     * @return Entity[]
     */
    public function searchEntities(EntitySearch $search, EntitySort $sort = null): array
    {
        $data = $this->getEntityCoordinatesQueryBuilder($search, $sort)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new Entity($row), $data);
    }

    public function getAllianceMarketId(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('id')
            ->from('entities')
            ->where('code = :code')
            ->setParameter('code', EntityType::ALLIANCE_MARKET)
            ->execute()
            ->fetchOne();
    }

    public function add(int $cellId, string $code, int $pos = 0): int
    {
        $this->createQueryBuilder()
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
            ->execute();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function updateCode(int $id, string $code): void
    {
        $this->createQueryBuilder()
            ->update('entities')
            ->set('code', ':code')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'code' => $code,
            ])
            ->execute();
    }

    /**
     * @return array<int, string>
     */
    public function getEntityCodes(): array
    {
        return $this->createQueryBuilder()
            ->select('id, code')
            ->from('entities')
            ->execute()
            ->fetchAllKeyValue();
    }

    public function getMaxEntityId(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('MAX(id)')
            ->from('entities')
            ->execute()
            ->fetchOne();
    }

    /**
     * @return EntityLabel[]
     */
    public function searchEntityLabels(EntitySearch $search, EntityLabelSort $sort = null, int $limit = null): array
    {
        $data = $this->entityLabelQuerBuilder($search, $sort, $limit)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new EntityLabel($row), $data);
    }

    public function searchEntityLabel(EntitySearch $search, EntityLabelSort $sort = null): ?EntityLabel
    {
        $data = $this->entityLabelQuerBuilder($search, $sort, 1)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new EntityLabel($data) : null;
    }

    private function entityLabelQuerBuilder(EntitySearch $search, EntityLabelSort $sort = null, int $limit = null): QueryBuilder
    {
        return $this->getEntityCoordinatesQueryBuilder($search, $sort, $limit)
            ->addSelect('planets.planet_name, planets.planet_user_main, planets.planet_type_id as planet_type')
            ->addSelect('stars.name as star_name, stars.type_id as star_type')
            ->addSelect('users.user_nick, users.user_id')
            ->leftJoin('e', 'planets', 'planets', 'e.id = planets.id')
            ->leftJoin('planets', 'users', 'users', 'users.user_id = planets.planet_user_id')
            ->leftJoin('e', 'stars', 'stars', 'e.id = stars.id');
    }

    public function countEntityLabels(EntityLabelSearch $search = null): int
    {
        return (int) $this->getEntityCoordinatesQueryBuilder($search)
            ->select('COUNT(*)')
            ->leftJoin('e', 'planets', 'planets', 'e.id = planets.id')
            ->leftJoin('planets', 'users', 'users', 'users.user_id = planets.planet_user_id')
            ->leftJoin('e', 'stars', 'stars', 'e.id = stars.id')
            ->execute()
            ->fetchOne();
    }

    private function getEntityCoordinatesQueryBuilder(EntitySearch $search = null, AbstractSort $sort = null, int $limit = null): QueryBuilder
    {
        return $this->applySearchSortLimit($this->createQueryBuilder(), $search, $sort, $limit)
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
