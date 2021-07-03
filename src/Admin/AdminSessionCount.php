<?php declare(strict_types=1);

namespace EtoA\Admin;

class AdminSessionCount
{
    public int $userId;
    public string $userNick;
    public int $count;

    public function __construct(array $data)
    {
        $this->userId = (int) $data['user_id'];
        $this->userNick = $data['user_nick'];
        $this->count = (int) $data['cnt'];
    }
}
