<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\Building;
use EtoA\Entity\Planet;
use EtoA\Entity\User;

class BuildingSearchRequest
{
    public ?User $user = null;
    public ?Planet $entity = null;
    public ?Building $building = null;
    public ?int $buildType = null;
}
