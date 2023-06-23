<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

class UserLoginFailureRequest
{
    public ?int $userId = null;
    public ?string $ip = null;
    public ?string $host = null;
    public ?string $client = null;
}
