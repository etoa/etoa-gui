<?php

declare(strict_types=1);

namespace EtoA\Bookmark;

class Bookmark
{
    public int $id;
    public int $userId;
    public int $entityId;
    public string $comment;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->userId = (int) $data['user_id'];
        $this->entityId = (int) $data['entity_id'];
        $this->comment = $data['comment'];
    }
}
