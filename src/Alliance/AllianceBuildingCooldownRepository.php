<?php

namespace EtoA\Alliance;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\AllianceBuildingCooldown;
use EtoA\Entity\Building;
use EtoA\Entity\User;

/**
 * @extends ServiceEntityRepository<AllianceBuildingCooldown>
 *
 * @method AllianceBuildingCooldown|null find($id, $lockMode = null, $lockVersion = null)
 * @method AllianceBuildingCooldown|null findOneBy(array $criteria, array $orderBy = null)
 * @method AllianceBuildingCooldown[]    findAll()
 * @method AllianceBuildingCooldown[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AllianceBuildingCooldownRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceBuildingCooldown::class);
    }

//    /**
//     * @return AllianceBuildingCooldown[] Returns an array of AllianceBuildingCooldown objects
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

//    public function findOneBySomeField($value): ?AllianceBuildingCooldown
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function getUserCooldown(User $user, int|Building $building): ?int
    {
        return $this->findOneBy(['user'=>$user,'allianceBuilding'=>$building])?->getCooldownEnd();
    }

    public function setUserCooldown(int $userId, int $buildingId, int $cooldownEnd): void
    {
        $this->getConnection()->executeStatement(
            "REPLACE INTO
                alliance_building_cooldown
            (
                cooldown_user_id,
                cooldown_alliance_building_id,
                cooldown_end
            ) VALUES (
                :userId,
                :buildingId,
                :cooldownEnd
            );",
            [
                'userId' => $userId,
                'buildingId' => $buildingId,
                'cooldownEnd' => $cooldownEnd,
            ]
        );
    }
}
