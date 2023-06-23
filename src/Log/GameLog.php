<?php declare(strict_types=1);

namespace EtoA\Log;

class GameLog
{
    public int $id;
    public int $severity;
    public int $facility;
    public int $objectId;
    public int $level;
    public string $message;
    public int $status;
    public int $entityId;
    public int $userId;
    public int $allianceId;
    public string $ip;
    public int $timestamp;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->severity = (int) $data['severity'];
        $this->facility = (int) $data['facility'];
        $this->objectId = (int) $data['object_id'];
        $this->level = (int) $data['level'];
        $this->message = $data['message'];
        $this->status = (int) $data['status'];
        $this->entityId = (int) $data['entity_id'];
        $this->userId = (int) $data['user_id'];
        $this->allianceId = (int) $data['alliance_id'];
        $this->ip = $data['ip'];
        $this->timestamp = (int) $data['timestamp'];
    }
}
