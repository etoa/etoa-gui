<?php declare(strict_types=1);

namespace EtoA\Log;

class DebrisLog
{
    public int $id;
    public int $adminId;
    public int $userId;
    public int $timestamp;
    public int $metal;
    public int $crystal;
    public int $plastic;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->adminId = (int) $data['admin_id'];
        $this->userId = (int) $data['user_id'];
        $this->timestamp = (int) $data['time'];
        $this->metal = (int) $data['metal'];
        $this->crystal = (int) $data['crystal'];
        $this->plastic = (int) $data['plastic'];
    }
}
