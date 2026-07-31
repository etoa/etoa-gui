<?php declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\Alliance;
use EtoA\Entity\Entity;
use EtoA\Entity\Fleet;
use EtoA\Entity\User;

class FleetSearch extends AbstractSearch
{
    public static function create(): FleetSearch
    {
        return new FleetSearch();
    }

    public function id(int $id): self
    {
        $this->parts[] = 'q.id = :id';
        $this->parameters['id'] = $id;

        return $this;
    }

    /**
     * @param int[] $ids
     */
    public function ids(array $ids): self
    {
        $this->parts[] = 'q.id IN(:ids)';
        $this->stringArrayParameters['ids'] = $ids;

        return $this;
    }

    public function isLeader(): self
    {
        $this->parts[] = 'q.id = q.leader';

        return $this;
    }

    public function user(User $user): self
    {
        $this->parts[] = 'q.user = :fleetUser';
        $this->parameters['fleetUser'] = $user;

        return $this;
    }

    public function notUser(User $user): self
    {
        $this->parts[] = 'q.user <> :fleetUser';
        $this->parameters['fleetUser'] = $user;

        return $this;
    }

    public function controlledByEntity(int $entityId): self
    {
        $this->parts[] = '(q.entityFrom = :controlledByEntity AND q.status = :departureState) OR (q.entityTo = :controlledByEntity AND q.status <> :departureState)';
        $this->parameters['controlledByEntity'] = $entityId;
        $this->parameters['departureState'] = FleetStatus::DEPARTURE->value;

        return $this;
    }

    public function planetUser(User $user): self
    {
        $this->parts[] = 'q.entityTo IN (:planets)';

        $ids = [];
        foreach ($user->getPlanets()->getValues() as $item) {
            $ids = $item->getEntity()->getId();
        }

        $this->parameters['planets'] = $ids;

        return $this;
    }

    public function alliance(Alliance $alliance): self
    {
        $this->parts[] = 'u.alliance = :alliance';
        $this->parameters['alliance'] = $alliance;

        return $this;
    }

    public function status(int $status): self
    {
        $this->parts[] = 'q.status = :status';
        $this->parameters['status'] = $status;

        return $this;
    }

    /**
     * @param int[] $status
     */
    public function statusIn(array $status): self
    {
        $this->parts[] = 'q.status IN (:statusIn)';
        $this->stringArrayParameters['statusIn'] = $status;

        return $this;
    }

    /**
     * @param string[] $actions
     */
    public function actionIn(array $actions): self
    {
        $this->parts[] = 'q.action IN (:actions)';
        $this->stringArrayParameters['actions'] = $actions;

        return $this;
    }

    /**
     * @param string[] $actions
     */
    public function actionNotIn(array $actions): self
    {
        $this->parts[] = 'q.action NOT IN (:notActions)';
        $this->stringArrayParameters['notActions'] = $actions;

        return $this;
    }

    public function filterNonLeadingAllianceAttacks(): self
    {
        $this->parts[] = 'NOT (q.action = :allianceAttackAction AND q.leader != q.id)';
        $this->parameters['allianceAttackAction'] = FleetAction::ALLIANCE;

        return $this;
    }

    public function nextId(int $nextId): self
    {
        $this->parts[] = 'q.nextId = :nextId';
        $this->parameters['nextId'] = $nextId;

        return $this;
    }

    public function entityFrom(int|Entity $entityFrom): self
    {
        $this->parts[] = 'q.entityFrom = :entityFrom';
        $this->parameters['entityFrom'] = $entityFrom;

        return $this;
    }

    public function entityTo(int|Entity $entityTo): self
    {
        $this->parts[] = 'q.entityTo = :entityTo';
        $this->parameters['entityTo'] = $entityTo;

        return $this;
    }

    public function leader(Fleet|int $leader): self
    {
        $this->parts[] = 'q.leader = :leader';
        $this->parameters['leader'] = $leader;

        return $this;
    }

    public function userIn(array $user): self
    {
        $this->parts[] = 'q.user in (:user)';
        $this->parameters['user'] = $user;

        return $this;
    }
}
