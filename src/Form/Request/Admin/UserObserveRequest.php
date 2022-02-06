<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

class UserObserveRequest
{
    public ?int $userId = null;
    public ?string $reason = null;
}
