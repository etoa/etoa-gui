<?php declare(strict_types=1);

namespace EtoA\Alliance\Board;

class Topic
{
    public int $id;
    public int $categoryId;
    public int $bndId;
    public int $userId;
    public string $subject;
    public int $count;
    public int $timestamp;
    public bool $top;
    public bool $closed;

    public function __construct(array $data)
    {
        $this->id = (int) $data['topic_id'];
        $this->categoryId = (int) $data['topic_cat_id'];
        $this->bndId = (int) $data['topic_bnd_id'];
        $this->userId = (int) $data['topic_user_id'];
        $this->subject = $data['topic_subject'];
        $this->count = (int) $data['topic_count'];
        $this->timestamp = (int) $data['topic_timestamp'];
        $this->top = (bool) $data['topic_top'];
        $this->closed = (bool) $data['topic_closed'];
    }
}
