<?php

namespace EtoA\Alliance;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Entity\AllianceRankRight;

/**
 * @extends ServiceEntityRepository<AllianceRankRight>
 *
 * @method AllianceRankRight|null find($id, $lockMode = null, $lockVersion = null)
 * @method AllianceRankRight|null findOneBy(array $criteria, array $orderBy = null)
 * @method AllianceRankRight[]    findAll()
 * @method AllianceRankRight[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AllianceRankRightRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceRankRight::class);
    }

//    /**
//     * @return RankRight[] Returns an array of RankRight objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RankRight
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
