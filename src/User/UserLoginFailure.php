<?php declare(strict_types=1);

namespace EtoA\User;

class UserLoginFailure
{
    public int $id;
    public int $time;
    public string $ip;
    public ?string $host;
    public int $userId;
    public ?string $userNick;
    public string $client;

    public function __construct(array $data)
    {
        $this->id = (int) $data['failure_id'];
        $this->time = (int) $data['failure_time'];
        $this->ip = $data['failure_ip'];
        $this->host = $data['failure_host'];
        $this->userId = (int) $data['failure_user_id'];
        $this->userNick = $data['user_nick'];
        $this->client = $data['failure_client'];
    }
}
