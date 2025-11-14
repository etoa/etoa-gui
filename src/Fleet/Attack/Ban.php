<?php declare(strict_types=1);

namespace EtoA\Fleet\Attack;

use EtoA\Entity\Planet;
use EtoA\Entity\User;

class Ban
{
    public function __construct(
        public string $action,
        public int $timestamp,
        public User $fleetUser,
        public User $entityUser,
        public Planet $entity,
        public string $banReason
    ) {
    }
}
