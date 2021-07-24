<?php declare(strict_types=1);

namespace EtoA\Market;

use EtoA\Universe\Resources\BaseResources;

class MarketAuction
{
    public int $id;
    public int $userId;
    public int $entityId;
    public int $dateStart;
    public int $dateEnd;
    public int $deleted;
    public int $sell0;
    public int $sell1;
    public int $sell2;
    public int $sell3;
    public int $sell4;
    public int $shipId;
    public int $shipCount;
    public string $text;
    public int $currency0;
    public int $currency1;
    public int $currency2;
    public int $currency3;
    public int $currency4;
    public int $currentBuyerId;
    public int $currentBuyerEntityId;
    public int $currentBuyerDate;
    public int $buy0;
    public int $buy1;
    public int $buy2;
    public int $buy3;
    public int $buy4;
    public int $bidCount;
    public bool $buyable;
    public int $sent;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->userId = (int) $data['user_id'];
        $this->entityId = (int) $data['entity_id'];
        $this->dateStart = (int) $data['date_start'];
        $this->dateEnd = (int) $data['date_end'];
        $this->deleted = (int) $data['date_delete'];
        $this->sell0 = (int) $data['sell_0'];
        $this->sell1 = (int) $data['sell_1'];
        $this->sell2 = (int) $data['sell_2'];
        $this->sell3 = (int) $data['sell_3'];
        $this->sell4 = (int) $data['sell_4'];
        $this->shipId = (int) $data['ship_id'];
        $this->shipCount = (int) $data['ship_count'];
        $this->text = $data['text'];
        $this->currency0 = (int) $data['currency_0'];
        $this->currency1 = (int) $data['currency_1'];
        $this->currency2 = (int) $data['currency_2'];
        $this->currency3 = (int) $data['currency_3'];
        $this->currency4 = (int) $data['currency_4'];
        $this->currentBuyerId = (int) $data['current_buyer_id'];
        $this->currentBuyerEntityId = (int) $data['current_buyer_entity_id'];
        $this->currentBuyerDate = (int) $data['current_buyer_date'];
        $this->buy0 = (int) $data['buy_0'];
        $this->buy1 = (int) $data['buy_1'];
        $this->buy2 = (int) $data['buy_2'];
        $this->buy3 = (int) $data['buy_3'];
        $this->buy4 = (int) $data['buy_4'];
        $this->bidCount = (int) $data['bidcount'];
        $this->buyable = (bool) $data['buyable'];
        $this->sent = (int) $data['sent'];
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

    public function getCurrencyResources(): BaseResources
    {
        $resources = new BaseResources();
        $resources->metal = $this->currency0;
        $resources->crystal = $this->currency1;
        $resources->plastic = $this->currency2;
        $resources->fuel = $this->currency3;
        $resources->food = $this->currency4;

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
