<?php declare(strict_types=1);

namespace EtoA\Chat;

class ChatLog
{
    public int $id;
    public int $timestamp;
    public string $nick;
    public string $text;
    public string $color;
    public int $userId;
    public int $admin;
    public bool $private;
    public string $channel;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->timestamp = (int) $data['timestamp'];
        $this->nick = $data['nick'];
        $this->text = $data['text'];
        $this->color = $data['color'];
        $this->userId = (int) $data['user_id'];
        $this->admin = (int) $data['admin'];
        $this->private = (bool) $data['private'];
        $this->channel = $data['channel'];
    }
}
