<?php declare(strict_types=1);

namespace EtoA\Core;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\Database\AbstractSearch;
use EtoA\Core\Database\AbstractSort;

abstract class AbstractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $className='')
    {
        parent::__construct($registry, $className);
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
