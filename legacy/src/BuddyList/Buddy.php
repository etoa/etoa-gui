<?php declare(strict_types=1);

namespace EtoA\BuddyList;

class Buddy
{
    public int $id;
    public int $userId;
    public string $userNick;
    public int $points;
    public bool $allowed;
    public int $planetId;
    public ?string $comment;
    public int $lastActionLogTimestamp;
    public bool $isOnline;

    public function __construct(array $data)
    {
        $this->id = (int) $data['bl_id'];
        $this->userId = (int) $data['user_id'];
        $this->userNick = $data['user_nick'];
        $this->points = (int) $data['user_points'];
        $this->allowed = (bool) $data['bl_allow'];
        $this->planetId = (int) $data['planetId'];
        $this->comment = $data['bl_comment'];
        $this->lastActionLogTimestamp = (int) $data['last_action'];
        $this->isOnline = (bool) $data['isOnline'];
    }
}
