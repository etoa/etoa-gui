<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\User;

class UserLoginFailureRequest
{
    public ?User $user = null;
    public ?string $ip = null;
    public ?string $host = null;
    public ?string $client = null;
}
