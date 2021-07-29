<?php declare(strict_types=1);

namespace EtoA\Alliance\Board;

class Post
{
    public int $id;
    public int $userId;
    public string $userNick;
    public int $topicId;
    public string $text;
    public int $timestamp;
    public ?int $changed;

    public function __construct(array $data)
    {
        $this->id = (int) $data['post_id'];
        $this->userId = (int) $data['post_user_id'];
        $this->userNick = $data['post_user_nick'];
        $this->topicId = (int) $data['post_topic_id'];
        $this->text = $data['post_text'];
        $this->timestamp = (int) $data['post_timestamp'];
        $this->changed = $data['post_changed'] ? (int) $data['post_changed'] : null;
    }
}
