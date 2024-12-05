<?php declare(strict_types=1);

namespace EtoA\Core;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\QueryBuilder;
use EtoA\Core\Database\AbstractSearch;
use EtoA\Core\Database\AbstractSort;

abstract class AbstractRepository extends ServiceEntityRepository
{
    public function save(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Object $entity):void
    {
        $this->getEntityManager()->remove($entity);
    }

    public function persist(Object $entity):void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function getChangeset(Object $model): array
    {
        $uow = $this->getEntityManager()->getUnitOfWork();
        $uow->computeChangeSets();
        return $uow->getEntityChangeSet($model);
    }

    protected function applySearchSortLimit(QueryBuilder $qb, AbstractSearch $search = null, AbstractSort $sorts = null, int $limit = null, int $offset = null): QueryBuilder
    {
        if ($search !== null) {
            $qb->setParameters($search->parameters);
            foreach ($search->stringArrayParameters as $parameter => $value) {
                $qb->setParameter($parameter, $value, ArrayParameterType::STRING);
            }
            foreach ($search->parts as $query) {
                $qb->andWhere($query);
            }
        }

        if ($sorts !== null) {
            foreach ($sorts->sorts as $sort => $order) {
                $qb->addOrderBy($sort, $order);
            }
        }

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        if ($offset !== null) {
            $qb->setFirstResult($offset);
        }

        return $qb;
    }
}
