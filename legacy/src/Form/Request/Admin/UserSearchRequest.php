<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

class UserSearchRequest
{
    public ?string $nickname = null;
    public ?string $name = null;
    public ?string $email = null;
    public ?string $emailFix = null;
    public ?int $allianceId = null;
    public ?int $raceId = null;
    public ?bool $hmod = null;
    public ?bool $blocked = null;
    public ?bool $ghost = null;
    public ?bool $chatAdmin = null;
}
