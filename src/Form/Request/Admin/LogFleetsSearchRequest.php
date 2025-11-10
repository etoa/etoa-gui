<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\User;

class LogFleetsSearchRequest
{
    public ?int $facility = null;
    public ?int $severity = null;
    public ?string $action = null;
    public ?int $status = null;
    public ?User $fleetUser = null;
    public ?User $entityUser = null;
}
