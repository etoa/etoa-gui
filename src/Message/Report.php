<?php declare(strict_types=1);

namespace EtoA\Message;

class Report
{
    public int $id;
    public int $timestamp;
    public string $type;
    public bool $read;
    public bool $deleted;
    public bool $archived;
    public int $userId;
    public int $allianceId;
    public ?string $content;
    public int $entity1Id;
    public int $entity2Id;
    public int $opponentId;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->timestamp = (int) $data['timestamp'];
        $this->type = $data['type'];
        $this->read = (bool) $data['read'];
        $this->deleted = (bool) $data['deleted'];
        $this->archived = (bool) $data['archived'];
        $this->userId = (int) $data['user_id'];
        $this->allianceId = (int) $data['alliance_id'];
        $this->content = $data['content'];
        $this->entity1Id = (int) $data['entity1_id'];
        $this->entity2Id = (int) $data['entity2_id'];
        $this->opponentId = (int) $data['opponent1_id'];
    }
}
