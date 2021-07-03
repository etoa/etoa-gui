<?php declare(strict_types=1);

namespace EtoA\User;

class UserSession
{
    public string $id;
    public int $userId;
    public ?string $ipAddr;
    public ?string $userAgent;
    public int $timeLogin;
    public int $timeAction;
    public int $lastSpan;
    public int $botCount;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->userId = (int) $data['user_id'];
        $this->ipAddr = $data['ip_addr'];
        $this->userAgent = $data['user_agent'];
        $this->timeLogin = (int) $data['time_login'];
        $this->timeAction = (int) $data['time_action'];
        $this->lastSpan = (int) $data['last_span'];
        $this->botCount = (int) $data['bot_count'];
    }
}
