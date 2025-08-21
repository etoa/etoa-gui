<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\User;

class UserObserveRequest
{
    public ?User $user = null;
    public ?string $reason = null;
}
