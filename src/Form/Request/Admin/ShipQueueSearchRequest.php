<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

class ShipQueueSearchRequest
{
    public ?int $userId = null;
    public ?int $shipId = null;
    public ?int $entityId = null;
}
