<?php

namespace EtoA\Tutorial;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Entity\Tutorial;

/**
 * @extends ServiceEntityRepository<Tutorial>
 *
 * @method Tutorial|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tutorial|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tutorial[]    findAll()
 * @method Tutorial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TutorialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tutorial::class);
    }

//    /**
//     * @return Tutorial[] Returns an array of Tutorial objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Tutorial
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
