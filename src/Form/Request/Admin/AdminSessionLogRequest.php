<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\AdminUser;

class AdminSessionLogRequest
{
    public ?AdminUser $user = null;
}
