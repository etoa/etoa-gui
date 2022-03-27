<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

class TechnologySearchRequest
{
    public ?int $userId = null;
    public ?int $techId = null;
    public ?int $entityId = null;
    public ?int $buildType = null;
}
