<?php declare(strict_types=1);

namespace EtoA\Log;

use EtoA\Core\Database\AbstractSearch;

class BattleLogSearch extends AbstractSearch
{
    public static function create(): BattleLogSearch
    {
        return new BattleLogSearch();
    }

    public function fleetUserId(int $fleetUserId): self
    {
        $this->parts[] = 'user_id LIKE :fleetUserId';
        $this->parameters['fleetUserId'] = '%,' . $fleetUserId . ',%';

        return $this;
    }

    public function entityUserId(int $entityUserId): self
    {
        $this->parts[] = 'entity_user_id LIKE :entityUserId';
        $this->parameters['entityUserId'] = '%,' . $entityUserId . ',%';

        return $this;
    }

    public function action(string $action): self
    {
        $this->parts[] = 'action = :action';
        $this->parameters['action'] = $action;

        return $this;
    }

    public function attackingBetween(int $start, int $end): self
    {
        $this->parts[] = 'fleet_weapon > 0 AND landtime <= :attackStart AND landtime > :attackEnd';
        $this->parameters['attackStart'] = $start;
        $this->parameters['attackEnd'] = $end;

        return $this;
    }

    public function severity(int $severity): self
    {
        $this->parts[] = 'severity >= :severity';
        $this->parameters['severity'] = $severity;

        return $this;
    }

    public function facility(int $facility): self
    {
        $this->parts[] = 'facility = :facility';
        $this->parameters['facility'] = $facility;

        return $this;
    }
}
