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
}
