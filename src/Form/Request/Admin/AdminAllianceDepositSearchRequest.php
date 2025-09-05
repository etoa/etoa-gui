<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\User;

class AdminAllianceDepositSearchRequest
{
    public ?User $user = null;
    public ?bool $display = null;
}
