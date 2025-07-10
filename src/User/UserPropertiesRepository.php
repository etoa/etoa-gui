<?php

declare(strict_types=1);

namespace EtoA\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\UserProperties;

class UserPropertiesRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, UserProperties::class);
    }

    public function addBlank(int $id): void
    {
        $this->createQueryBuilder('q')
            ->delete('user_properties')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->executeQuery();

        $this->createQueryBuilder('q')
            ->insert('user_properties')
            ->values([
                'id' => ':id',
            ])
            ->setParameters([
                'id' => $id,
            ])
            ->executeQuery();
    }

    public function getOrCreateProperties(int $userId): UserProperties
    {
        $data = $this->getProperties($userId);

        if ($data === null) {
            $this->addBlank($userId);
            $data = $this->getProperties($userId);
        }

        return $data;
    }

    public function getProperties(int $userId): ?UserProperties
    {
        return $this->find($userId);
    }

    public function storeProperties(UserProperties $properties): void
    {
        $this->entityManager->persist($properties);
        $this->entityManager->flush();

    }

    /**
     * @return array<int, array>
     */
    public function getDesignStats(int $limit): array
    {
        return $this->createQueryBuilder('q')
            ->select( 'COUNT(q.id) as cnt')
            ->addSelect('q.cssStyle')
            ->groupBy('q.cssStyle')
            ->orderBy('cnt','DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    public function removeForUser(int $userId) : void
    {
        $this->createQueryBuilder('q')
            ->delete('user_properties')
            ->where('id = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery();
    }
}
