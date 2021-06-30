<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\AbstractRepository;

class UserLogRepository extends AbstractRepository
{
    public function add(User $user, string $zone, string $message, bool $public = true): void
    {
        $search = ["{user}", "{nick}"];
        $replace = [$user->nick, $user->nick];
        $message = str_replace($search, $replace, $message);

        $this->createQueryBuilder()
            ->insert('user_log')
            ->values([
                'user_id' => ':user_id',
                'timestamp' => ':timestamp',
                'zone' => ':zone',
                'message' => ':message',
                'host' => ':host',
                'public' => ':public',
            ])
            ->setParameters([
                'user_id' => $user->id,
                'timestamp' => time(),
                'zone' => $zone,
                'message' => $message,
                'host' => isset($_SERVER['REMOTE_ADDR']) ? gethostbyname($_SERVER['REMOTE_ADDR']) : '',
                'public' => $public,
            ])
            ->execute();
    }
}
