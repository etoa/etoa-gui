<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\AdminUser;
use EtoA\Entity\User;

class LogDebrisSearchRequest
{
    public ?int $date = null;
    public ?User $user = null;
    public ?AdminUser $admin = null;
}
