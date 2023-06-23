<?php declare(strict_types=1);

namespace EtoA\Fleet\Attack;

class Ban
{
    public function __construct(
        public string $action,
        public int $timestamp,
        public int $fleetUserId,
        public int $entityUserId,
        public int $entityId,
        public string $banReason
    ) {
    }
}
