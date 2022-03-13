<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

class FleetSearchRequest
{
    public ?int $entityFrom = null;
    public ?int $entityTo = null;
    public ?string $action = null;
    public ?int $status = null;
    public ?int $user = null;
}
