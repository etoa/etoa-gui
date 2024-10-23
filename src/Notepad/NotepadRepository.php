<?php

namespace EtoA\Notepad;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Entity\Notepad;

/**
 * @extends ServiceEntityRepository<Notepad>
 *
 * @method Notepad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notepad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notepad[]    findAll()
 * @method Notepad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotepadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notepad::class);
    }

//    /**
//     * @return Notepad[] Returns an array of Notepad objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Notepad
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
