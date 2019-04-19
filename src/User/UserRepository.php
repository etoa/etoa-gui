<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\AbstractRepository;

class UserRepository extends AbstractRepository
{
    public function getDiscoverMask(int $userId): string
    {
        return $this->getUserProperty($userId, 'discoverymask');
    }

    public function getPoints(int $userId): int
    {
        return (int)$this->getUserProperty($userId, 'user_points');
    }

    public function getAllianceId(int $userId): int
    {
        return (int)$this->getUserProperty($userId, 'user_alliance_id');
    }

    public function getSpecialistId(int $userId): int
    {
        return (int)$this->getUserProperty($userId, 'user_specialist_id');
    }

    private function getUserProperty(int $userId, string $property): string
    {
        return $this->createQueryBuilder()
            ->select($property)
            ->from('users')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchColumn();
    }
}
