<?php declare(strict_types=1);

namespace EtoA\Log;

class BattleLog
{
    public int $id;
    public string $fleetUserIds;
    public string $entityUserIds;
    public int $landTime;
    public int $entityId;
    public string $action;
    public bool $war;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->fleetUserIds = $data['user_id'];
        $this->entityUserIds = $data['entity_user_id'];
        $this->landTime = (int) $data['landtime'];
        $this->entityId = (int) $data['entity_id'];
        $this->action = $data['action'];
        $this->war = $data['war'];
    }
}
