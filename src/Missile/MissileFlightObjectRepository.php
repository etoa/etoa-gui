<?php

namespace EtoA\Missile;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\MissileFlightObject;

/**
 * @extends AbstractRepository<MissileFlightObject>
 *
 * @method MissileFlightObject|null find($id, $lockMode = null, $lockVersion = null)
 * @method MissileFlightObject|null findOneBy(array $criteria, array $orderBy = null)
 * @method MissileFlightObject[]    findAll()
 * @method MissileFlightObject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MissileFlightObjectRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MissileFlightObject::class);
    }

//    /**
//     * @return MissileFlightObject[] Returns an array of MissileFlightObject objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MissileFlightObject
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
