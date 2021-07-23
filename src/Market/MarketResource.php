<?php declare(strict_types=1);

namespace EtoA\Market;

use EtoA\Universe\Resources\BaseResources;

class MarketResource
{
    public int $id;
    public int $userId;
    public int $entityId;
    public int $sell0;
    public int $sell1;
    public int $sell2;
    public int $sell3;
    public int $sell4;
    public int $buy0;
    public int $buy1;
    public int $buy2;
    public int $buy3;
    public int $buy4;
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
        $this->sell0 = (int) $data['sell_0'];
        $this->sell1 = (int) $data['sell_1'];
        $this->sell2 = (int) $data['sell_2'];
        $this->sell3 = (int) $data['sell_3'];
        $this->sell4 = (int) $data['sell_4'];
        $this->buy0 = (int) $data['buy_0'];
        $this->buy1 = (int) $data['buy_1'];
        $this->buy2 = (int) $data['buy_2'];
        $this->buy3 = (int) $data['buy_3'];
        $this->buy4 = (int) $data['buy_4'];
        $this->buyerId = (int) $data['buyer_id'];
        $this->buyerEntityId = (int) $data['buyer_entity_id'];
        $this->forUserId = (int) $data['for_user'];
        $this->forAllianceId = (int) $data['for_alliance'];
        $this->buyable = (bool) $data['buyable'];
        $this->text = $data['text'];
        $this->date = (int) $data['datum'];
    }

    public function getSellResources(): BaseResources
    {
        $resources = new BaseResources();
        $resources->metal = $this->sell0;
        $resources->crystal = $this->sell1;
        $resources->plastic = $this->sell2;
        $resources->fuel = $this->sell3;
        $resources->food = $this->sell4;

        return $resources;
    }

    public function getBuyResources(): BaseResources
    {
        $resources = new BaseResources();
        $resources->metal = $this->buy0;
        $resources->crystal = $this->buy1;
        $resources->plastic = $this->buy2;
        $resources->fuel = $this->buy3;
        $resources->food = $this->buy4;

        return $resources;
    }
}
