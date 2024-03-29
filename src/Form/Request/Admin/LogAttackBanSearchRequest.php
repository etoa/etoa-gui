<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

class LogAttackBanSearchRequest
{
    public ?int $date = null;
    public ?string $action = null;
    public ?int $attacker = null;
    public ?int $defender = null;
}
