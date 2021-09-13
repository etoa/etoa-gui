<?php declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Core\Database\AbstractSearch;

class FleetSearch extends AbstractSearch
{
    public static function create(): FleetSearch
    {
        return new FleetSearch();
    }

    public function isLeader(): self
    {
        $this->parts[] = 'fleet.id = fleet.leader_id';

        return $this;
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

    public function allianceId(int $allianceId): self
    {
        $this->parts[] = 'users.user_alliance_id = :allianceId';
        $this->parameters['allianceId'] = $allianceId;

        return $this;
    }

    public function status(int $status): self
    {
        $this->parts[] = 'status = :status';
        $this->parameters['status'] = $status;

        return $this;
    }

    /**
     * @param int[] $status
     */
    public function statusIn(array $status): self
    {
        $this->parts[] = 'status IN (:status)';
        $this->stringArrayParameters['statusIN'] = $status;

        return $this;
    }

    /**
     * @param string[] $actions
     */
    public function actionIn(array $actions): self
    {
        $this->parts[] = 'action IN (:actions)';
        $this->stringArrayParameters['actions'] = $actions;

        return $this;
    }

    /**
     * @param string[] $actions
     */
    public function actionNotIn(array $actions): self
    {
        $this->parts[] = 'action NOT IN (:notActions)';
        $this->stringArrayParameters['notActions'] = $actions;

        return $this;
    }

    public function filterNonLeadingAllianceAttacks(): self
    {
        $this->parts[] = '!(fleet.action = :allianceAttackAction AND fleet.leader_id != fleet.id)';
        $this->parameters['allianceAttackAction'] = FleetAction::ALLIANCE;

        return $this;
    }

    public function nextId(int $nextId): self
    {
        $this->parts[] = 'next_id = :nextId';
        $this->parameters['nextId'] = $nextId;

        return $this;
    }

    public function entityTo(int $entityTo): self
    {
        $this->parts[] = 'entity_to = :entityTo';
        $this->parameters['entityTo'] = $entityTo;

        return $this;
    }

    public function leader(int $leader): self
    {
        $this->parts[] = 'leader_id = :leader';
        $this->parameters['leader'] = $leader;

        return $this;
    }
}
