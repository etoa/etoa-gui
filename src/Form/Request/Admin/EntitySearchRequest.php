<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

class EntitySearchRequest
{
    public ?string $name = null;
    public ?int $entity = null;
    public ?int $cell = null;
    public ?int $user = null;
    public ?string $code = null;
    public ?bool $isMainPlanet = null;
    public ?bool $planetDebris = null;
}
