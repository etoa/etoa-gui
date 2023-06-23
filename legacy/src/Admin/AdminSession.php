<?php declare(strict_types=1);

namespace EtoA\Admin;

class AdminSession
{
    public string $id;
    public int $userId;
    public string $userNick;
    public ?string $ipAddr;
    public ?string $userAgent;
    public int $timeLogin;
    public int $timeAction;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->userId = (int) $data['user_id'];
        $this->userNick = $data['user_nick'];
        $this->ipAddr = $data['ip_addr'];
        $this->userAgent = $data['user_agent'];
        $this->timeLogin = (int) $data['time_login'];
        $this->timeAction = (int) $data['time_action'];
    }
}
