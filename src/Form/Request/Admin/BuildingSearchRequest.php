<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

class BuildingSearchRequest
{
    public ?int $userId = null;
    public ?int $entityId = null;
    public ?int $buildingId = null;
    public ?int $buildType = null;
}
