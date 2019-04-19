<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\AbstractRepository;

class UserRepository extends AbstractRepository
{
    public function getDiscoverMask($userId)
    {
        return $this->getUserProperty($userId, 'discoverymask');
    }

    public function getPoints($userId)
    {
        return (int)$this->getUserProperty($userId, 'user_points');
    }

    public function getAllianceId($userId)
    {
        return (int)$this->getUserProperty($userId, 'user_alliance_id');
    }

    public function getSpecialistId($userid)
    {
        return (int)$this->getUserProperty($userid, 'user_specialist_id');
    }

    private function getUserProperty($userId, $property)
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
