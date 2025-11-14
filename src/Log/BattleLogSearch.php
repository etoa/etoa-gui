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
        $this->parts[] = 'q.fleetUserIds LIKE :fleetUserId';
        $this->parameters['fleetUserId'] = '%,' . $fleetUserId . ',%';

        return $this;
    }

    public function entityUserId(int $entityUserId): self
    {
        $this->parts[] = 'q.entityUserIds LIKE :entityUserId';
        $this->parameters['entityUserId'] = '%,' . $entityUserId . ',%';

        return $this;
    }

    public function action(string $action): self
    {
        $this->parts[] = 'q.action = :action';
        $this->parameters['action'] = $action;

        return $this;
    }

    public function attackingBetween(int $start, int $end): self
    {
        $this->parts[] = 'q.fleetWeapon > 0 AND q.landtime <= :attackStart AND q.landtime > :attackEnd';
        $this->parameters['attackStart'] = $start;
        $this->parameters['attackEnd'] = $end;

        return $this;
    }

    public function severity(int $severity): self
    {
        $this->parts[] = 'q.severity >= :severity';
        $this->parameters['severity'] = $severity;

        return $this;
    }

    public function facility(int $facility): self
    {
        $this->parts[] = 'q.facility = :facility';
        $this->parameters['facility'] = $facility;

        return $this;
    }
}
