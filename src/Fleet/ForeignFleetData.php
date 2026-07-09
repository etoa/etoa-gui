<?php declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Entity\Fleet;

class ForeignFleetData
{
    public function __construct(
        public readonly Fleet $fleet,
        public readonly string $attitudeColor,
        public readonly string $attitudeString,
        public readonly string $statusCode,
        public readonly string $shipAction,
        public readonly ?int $shipsCount = null,
        public readonly ?array $ships = null,
        public readonly bool $showShipNumbers = false
    ) {}
}
