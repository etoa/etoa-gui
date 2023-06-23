<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceApplication
{
    public int $userId;
    public string $userNick;
    public int $userPoints;
    public int $userRank;
    public int $userRegistered;
    public int $timestamp;
    public string $text;

    public function __construct(array $data)
    {
        $this->userId = (int) $data['user_id'];
        $this->userNick = $data['user_nick'];
        $this->userPoints = (int) $data['user_points'];
        $this->userRank = (int) $data['user_rank'];
        $this->userRegistered = (int) $data['user_registered'];
        $this->timestamp = (int) $data['timestamp'];
        $this->text = $data['text'];
    }
}
