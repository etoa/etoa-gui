<?php declare(strict_types=1);

namespace EtoA\User;

class UserLog
{
    public int $id;
    public int $userId;
    public int $timestamp;
    public string $zone;
    public string $message;
    public string $host;
    public bool $public;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->userId = (int) $data['user_id'];
        $this->timestamp = (int) $data['timestamp'];
        $this->zone = $data['zone'];
        $this->message = $data['message'];
        $this->host = $data['host'];
        $this->public = (bool) $data['public'];
    }
}
