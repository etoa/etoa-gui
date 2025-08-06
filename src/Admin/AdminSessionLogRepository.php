<?php

namespace EtoA\Admin;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\AdminSession;
use EtoA\Entity\AdminSessionLog;

/**
 * @extends ServiceEntityRepository<AdminSessionLog>
 *
 * @method AdminSessionLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdminSessionLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdminSessionLog[]    findAll()
 * @method AdminSessionLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdminSessionLogRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdminSessionLog::class);
    }

    public function addSessionLog(AdminSession $adminSession, ?int $logoutTime): void
    {
        $sessionLog = new AdminSessionLog();

        $sessionLog->setSessionId($adminSession->getId());
        $sessionLog->setUserId($adminSession->getUser()->getId());
        $sessionLog->setIpAddr($adminSession->getIpAddr());
        $sessionLog->setTimeLogin($adminSession->getTimeLogin());
        $sessionLog->setTimeAction($adminSession->getTimeAction());
        $sessionLog->setTimeLogout($logoutTime ?? time());
        $sessionLog->setUserAgent($adminSession->getUserAgent());

        $this->persist($sessionLog);
        $this->save();
    }

//    /**
//     * @return AdminSessionLog[] Returns an array of AdminSessionLog objects
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

//    public function findOneBySomeField($value): ?AdminSessionLog
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
