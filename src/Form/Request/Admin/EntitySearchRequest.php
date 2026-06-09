<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\User;

class EntitySearchRequest
{
    public ?string $name = null;
    public ?int $entity = null;
    public ?int $cell = null;
    public ?User $user = null;
    public ?string $code = null;
    public ?bool $isMainPlanet = null;
    public ?bool $planetDebris = null;
}
