<?php declare(strict_types=1);

namespace EtoA\BuddyList;

class PendingBuddyRequest
{
    public int $id;
    public int $userId;
    public string $userNick;
    public int $points;

    public function __construct(array $data)
    {
        $this->id = (int) $data['bl_id'];
        $this->userId = (int) $data['user_id'];
        $this->userNick = $data['user_nick'];
        $this->points = (int) $data['user_points'];
    }
}
