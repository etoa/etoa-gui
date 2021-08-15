<?php declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Core\Database\AbstractSearch;

class FleetSearch extends AbstractSearch
{
    public static function create(): FleetSearch
    {
        return new FleetSearch();
    }

    public function user(int $userId): self
    {
        $this->parts[] = 'user_id = :fleetUserId';
        $this->parameters['fleetUserId'] = $userId;

        return $this;
    }

    public function notUser(int $userId): self
    {
        $this->parts[] = 'user_id <> :fleetUserId';
        $this->parameters['fleetUserId'] = $userId;

        return $this;
    }

    public function controlledByEntity(int $entityId): self
    {
        $this->parts[] = '(entity_from = :controlledByEntity AND status = :departureState) OR (entity_to = :controlledByEntity AND status <> :departureState)';
        $this->parameters['controlledByEntity'] = $entityId;
        $this->parameters['departureState'] = FleetStatus::DEPARTURE;

        return $this;
    }

    public function planetUser(int $userId): self
    {
        $this->parts[] = 'planets.planet_user_id = :planetUserId';
        $this->parameters['planetUserId'] = $userId;

        return $this;
    }

    public function status(int $status): self
    {
        $this->parts[] = 'status = :status';
        $this->parameters['status'] = $status;

        return $this;
    }

    /**
     * @param string[] $actions
     */
    public function actionNotIn(array $actions): self
    {
        $this->parts[] = 'action NOT IN (:actions)';
        $this->stringArrayParameters['actions'] = $actions;

        return $this;
    }
}
