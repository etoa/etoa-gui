<?php declare(strict_types=1);

namespace EtoA\User;

class UserOnlineStats
{
    public int $id;
    public int $timestamp;
    public int $sessionCount;
    public int $userCount;

    public function __construct(array $data)
    {
        $this->id = (int) $data['stats_id'];
        $this->timestamp = (int) $data['stats_timestamp'];
        $this->sessionCount = (int) $data['stats_count'];
        $this->userCount = (int) $data['stats_regcount'];
    }
}
