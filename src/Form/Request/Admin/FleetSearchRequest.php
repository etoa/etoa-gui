<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\Entity;
use EtoA\Entity\User;

class FleetSearchRequest
{
    public ?Entity $entityFrom = null;
    public ?Entity $entityTo = null;
    public ?string $action = null;
    public ?int $status = null;
    public ?User $user = null;
}
