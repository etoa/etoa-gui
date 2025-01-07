<?php

namespace EtoA\Alliance;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\AlliancePollVotes;

/**
 * @extends ServiceEntityRepository<AlliancePollVotes>
 *
 * @method AlliancePollVotes|null find($id, $lockMode = null, $lockVersion = null)
 * @method AlliancePollVotes|null findOneBy(array $criteria, array $orderBy = null)
 * @method AlliancePollVotes[]    findAll()
 * @method AlliancePollVotes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlliancePollVotesRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AlliancePollVotes::class);
    }

//    /**
//     * @return AlliancePollVotes[] Returns an array of AlliancePollVotes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AlliancePollVotes
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
