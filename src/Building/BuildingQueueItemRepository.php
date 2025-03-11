<?php

namespace EtoA\Building;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Entity\BuildingQueueItem;
use EtoA\Entity\Entity;

/**
 * @extends ServiceEntityRepository<BuildingQueueItem>
 *
 * @method BuildingQueueItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method BuildingQueueItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method BuildingQueueItem[]    findAll()
 * @method BuildingQueueItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BuildingQueueItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BuildingQueueItem::class);
    }

    public function removeForEntity(Entity $entity): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.entity = :entity')
            ->setParameter('entity', $entity)
            ->getQuery()
            ->execute();
    }

//    /**
//     * @return BuildingQueueItem[] Returns an array of BuildingQueueItem objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BuildingQueueItem
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
