<?php declare(strict_types=1);

namespace EtoA\Chat;

class ChatBan
{
    public int $userId;
    public string $userNick;
    public ?string $reason;
    public int $timestamp;

    public function __construct(array $data)
    {
        $this->userId = (int) $data['user_id'];
        $this->userNick = $data['user_nick'];
        $this->reason = $data['reason'];
        $this->timestamp = (int) $data['timestamp'];
    }
}
