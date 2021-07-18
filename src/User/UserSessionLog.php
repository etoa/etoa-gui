<?php declare(strict_types=1);

namespace EtoA\User;

class UserSessionLog
{
    public int $id;
    public string $sessionId;
    public int $userId;
    public string $ip;
    public string $userAgent;
    public int $timeLogin;
    public int $timeAction;
    public int $timeLogout;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->sessionId = $data['session_id'];
        $this->userId = (int) $data['user_id'];
        $this->ip = $data['ip_addr'];
        $this->userAgent = $data['user_agent'];
        $this->timeLogin = (int) $data['time_login'];
        $this->timeAction = (int) $data['time_action'];
        $this->timeLogout = (int) $data['time_logout'];
    }
}
