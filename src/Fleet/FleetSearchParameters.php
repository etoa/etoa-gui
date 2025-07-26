<?php

declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Entity\User;

class FleetSearchParameters
{
    public ?int $id = null;
    public ?User $user = null;
    public ?string $userNick = null;
    public ?int $entityFrom = null;
    public ?int $entityTo = null;
    public ?string $action = null;

    public static function create(): FleetSearchParameters
    {
        return new FleetSearchParameters();
    }

    public function id(int $id): FleetSearchParameters
    {
        $this->id = $id;

        return $this;
    }

    public function user(User $user): FleetSearchParameters
    {
        $this->user = $user;

        return $this;
    }

    public function userNick(string $userNick): FleetSearchParameters
    {
        $this->userNick = $userNick;

        return $this;
    }

    public function entityFrom(int $entityFrom): FleetSearchParameters
    {
        $this->entityFrom = $entityFrom;

        return $this;
    }

    public function entityTo(int $entityTo): FleetSearchParameters
    {
        $this->entityTo = $entityTo;

        return $this;
    }

    public function action(string $action): FleetSearchParameters
    {
        $this->action = $action;

        return $this;
    }
}
