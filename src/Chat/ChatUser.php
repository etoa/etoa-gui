<?php declare(strict_types=1);

namespace EtoA\Chat;

class ChatUser
{
    public int $id;
    public string $nick;
    public int $timestamp;
    public ?string $kick;

    public function __construct(array $data)
    {
        $this->id = (int) $data['user_id'];
        $this->nick = $data['nick'];
        $this->timestamp = (int) $data['timestamp'];
        $this->kick = $data['kick'];
    }
}
