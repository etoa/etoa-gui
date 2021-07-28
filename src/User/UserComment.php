<?php declare(strict_types=1);

namespace EtoA\User;

class UserComment
{
    public int $id;
    public string $text;
    public int $timestamp;
    public ?string $adminNick;

    public function __construct(array $data)
    {
        $this->id = (int) $data['comment_id'];
        $this->text = $data['comment_text'];
        $this->timestamp = (int) $data['comment_timestamp'];
        $this->adminNick = $data['user_nick'];
    }
}
