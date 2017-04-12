<?php

namespace EtoA\User;

use EtoA\Core\AbstractRepository;

class UserRepository extends AbstractRepository
{
    public function getDiscoverMask($userId)
    {
        return $this->createQueryBuilder()
            ->select('discoverymask')
            ->from('users')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchColumn();
    }

    public function getPoints($userId)
    {
        return (int)$this->createQueryBuilder()
            ->select('user_points')
            ->from('users')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchColumn();
    }

    public function getAllianceId($userId)
    {
        return (int)$this->createQueryBuilder()
            ->select('user_alliance_id')
            ->from('users')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchColumn();
    }
}
