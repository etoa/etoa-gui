<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\User;

class UserSessionLogRequest
{
    public ?User $user = null;
    public ?string $ip = null;
    public ?string $client = null;
}
