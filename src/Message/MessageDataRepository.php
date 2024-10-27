<?php

namespace EtoA\Message;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Entity\MessageData;

/**
 * @extends ServiceEntityRepository<MessageData>
 *
 * @method MessageData|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageData|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageData[]    findAll()
 * @method MessageData[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageDataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageData::class);
    }

//    /**
//     * @return MessageData[] Returns an array of MessageData objects
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

//    public function findOneBySomeField($value): ?MessageData
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
