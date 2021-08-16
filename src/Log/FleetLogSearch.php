<?php declare(strict_types=1);

namespace EtoA\Log;

use EtoA\Core\Database\AbstractSearch;

class FleetLogSearch extends AbstractSearch
{
    public static function create(): FleetLogSearch
    {
        return new FleetLogSearch();
    }

    public function action(string $action): self
    {
        $this->parts[] = 'action = :action';
        $this->parameters['action'] = $action;

        return $this;
    }

    public function fleetUserId(int $fleetUserId): self
    {
        $this->parts[] = 'user_id = :fleetUserId';
        $this->parameters['fleetUserId'] = $fleetUserId;

        return $this;
    }

    public function entityUserId(int $entityUserId): self
    {
        $this->parts[] = 'entity_user_id = :entityUserId';
        $this->parameters['entityUserId'] = $entityUserId;

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
