<?php declare(strict_types=1);

namespace EtoA\Market;

use EtoA\Universe\Resources\BaseResources;

class MarketShip
{
    public int $id;
    public int $userId;
    public int $entityId;
    public int $shipId;
    public int $count;
    public int $costs0;
    public int $costs1;
    public int $costs2;
    public int $costs3;
    public int $costs4;
    public int $buyerId;
    public int $buyerEntityId;
    public int $forUserId;
    public int $forAllianceId;
    public bool $buyable;
    public string $text;
    public int $date;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->userId = (int) $data['user_id'];
        $this->entityId = (int) $data['entity_id'];
        $this->shipId = (int) $data['ship_id'];
        $this->count = (int) $data['count'];
        $this->costs0 = (int) $data['costs_0'];
        $this->costs1 = (int) $data['costs_1'];
        $this->costs2 = (int) $data['costs_2'];
        $this->costs3 = (int) $data['costs_3'];
        $this->costs4 = (int) $data['costs_4'];
        $this->buyerId = (int) $data['buyer_id'];
        $this->buyerEntityId = (int) $data['buyer_entity_id'];
        $this->forUserId = (int) $data['for_user'];
        $this->forAllianceId = (int) $data['for_alliance'];
        $this->buyable = (bool) $data['buyable'];
        $this->text = $data['text'];
        $this->date = (int) $data['datum'];
    }

    public function getCosts(): BaseResources
    {
        $resources = new BaseResources();
        $resources->metal = $this->costs0;
        $resources->crystal = $this->costs1;
        $resources->plastic = $this->costs2;
        $resources->fuel = $this->costs3;
        $resources->food = $this->costs4;

        return $resources;
    }
}
