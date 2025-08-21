<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\Planet;
use EtoA\Entity\Ship;
use EtoA\Entity\User;

class ShipListSearch extends AbstractSearch
{
    public static function create(): ShipListSearch
    {
        return new ShipListSearch();
    }

    public function userId(int|User $userId): self
    {
        $this->parts[] = 'q.user = :userId';
        $this->parameters['userId'] = $userId;

        return $this;
    }

    public function entityId(Planet|int $entityId): self
    {
        $this->parts[] = 'q.entity = :entityId';
        $this->parameters['entityId'] = $entityId;

        return $this;
    }


    public function shipId(int|Ship $shipId): self
    {
        $this->parts[] = 'q.ship = :shipId';
        $this->parameters['shipId'] = $shipId;

        return $this;
    }

    public function hasShips(): self
    {
        $this->parts[] = "q.count > 0 OR q.bunkered > 0";

        return $this;
    }
}
