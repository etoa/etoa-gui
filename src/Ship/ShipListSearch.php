<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\Core\Database\AbstractSearch;

class ShipListSearch extends AbstractSearch
{
    public static function create(): ShipListSearch
    {
        return new ShipListSearch();
    }

    public function userId(int $userId): self
    {
        $this->parts[] = 'shiplist_user_id = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function entityId(int $entityId): self
    {
        $this->parts[] = 'shiplist_entity_id = :entityId';
        $this->parameters['entityId'] = $entityId;

        return $this;
    }


    public function shipId(int $shipId): self
    {
        $this->parts[] = 'shiplist_ship_id = :shipId';
        $this->parameters['shipId'] = $shipId;

        return $this;
    }

    public function hasShips(): self
    {
        $this->parts[] = "shiplist_count > 0 OR shiplist_bunkered > 0";

        return $this;
    }
}
