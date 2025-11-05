<?php

declare(strict_types=1);

namespace EtoA\Universe\Entity;

use \Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Core\Database\AbstractSort;
use EtoA\Entity\Cell;
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

        return $this->getEntityCoordinatesQueryBuilder()
            ->where('q.code IN (:codes)')
            ->andWhere('q.pos = 0')
            ->orderBy('RAND()')
            ->setParameters(['codes'=>$codes])
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findIncludeCell(Entity $entity): ?Entity
    {
        return $this->getEntityCoordinatesQueryBuilder()
            ->where('e.id = :entity')
            ->setParameters([
                'entity' => $entity,
            ])
            ->getQuery()
            ->execute();
    }

    public function findByCellAndPosition(int $cellId, int $position): ?Entity
    {
        $entity = $this->findBy(['cellId'=>$cellId,'pos'=>$position]);
        return $entity ? $entity[0]: null;
    }

    public function findByCoordinates(EntityCoordinates $coordinates): ?Entity
    {
        return $this->createQueryBuilder('q')
            ->where('c.sx = :sx')
            ->andWhere('c.sy = :sy')
            ->andWhere('c.cx = :cx')
            ->andWhere('c.cy = :cy')
            ->andWhere('q.pos = :pos')
            ->setParameters([
                'sx' => $coordinates->sx,
                'sy' => $coordinates->sy,
                'cx' => $coordinates->cx,
                'cy' => $coordinates->cy,
                'pos' => $coordinates->pos,
            ])
            ->innerJoin('App:Cell', 'c', 'WITH', 'q.cell = c.id')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Entity[]
     */
    public function searchEntities(EntitySearch $search, EntitySort $sort = null): array
    {
        return $this->getEntityCoordinatesQueryBuilder($search, $sort)
            ->getQuery()
            ->getArrayResult();
    }

    public function getAllianceMarketId(): int
    {
        return $this->findOneBy(['code'=>EntityType::ALLIANCE_MARKET])->getId();
    }

    public function add(Cell $cell, string $code, int $pos = 0): Entity
    {
        $entity = new Entity();
        $entity->setCell($cell);
        $entity->setCode($code);
        $entity->setPos($pos);

        $this->persist($entity);
        $this->save();
        return $entity;
    }

    public function updateCode(Entity $entity, string $code): void
    {
        $entity->setCode($code);
        $this->save();
    }

    /**
     * @return array<int, string>
     */
    public function getEntityCodes(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('q.id, q.code')
            ->getQuery()
            ->execute();

        return array_column($data, 'code', 'id');
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
            ->getQuery()
            ->execute();

        $entities = [];
        foreach ($data as $row) {
            $entities[$row->getId()] = $row;
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
            ->select('q')
            ->leftJoin('App:Planet', 'planets', 'WITH', 'q.id = planets.id')
            ->leftJoin('App:User', 'users', 'WITH', 'users.id = planets.user');
    }

    public function countEntityLabels(EntityLabelSearch $search = null): int
    {
        return (int) $this->getEntityCoordinatesQueryBuilder($search)
            ->select('COUNT(q)')
            ->leftJoin('App:Planet', 'planets', 'WITH', 'q.id = planets.id')
            ->leftJoin('App:User', 'users', 'WITH', 'users.id = planets.user')
            ->leftJoin('App:Star', 'stars', 'WITH', 'q.id = stars.id')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function getEntityCoordinatesQueryBuilder(EntitySearch $search = null, AbstractSort $sort = null, int $limit = null, int $offset = null): QueryBuilder
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, $sort, $limit, $offset)
            ->select(
                'q.id',
                'c.id as cid',
                'q.code',
                'q.pos',
                'c.sx',
                'c.sy',
                'c.cx',
                'c.cy'
            )
            ->innerJoin('App:Cell', 'c', 'WITH', 'q.cell = c.id');
    }
}
