<?php

declare(strict_types=1);

namespace EtoA\Fleet;

class FleetSearchParameters
{
    public ?int $id = null;
    public ?int $userId = null;
    public ?string $userNick = null;
    public ?int $entityFrom = null;
    public ?int $entityTo = null;
    public ?string $action = null;
}
