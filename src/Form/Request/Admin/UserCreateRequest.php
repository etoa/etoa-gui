<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

class UserCreateRequest
{
    public string $name = '';
    public string $email = '';
    public string $nick = '';
    public string $password = '';
    public ?int $raceId = null;
    public bool $ghost = false;
}
