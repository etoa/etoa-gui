<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\Entity;
use EtoA\Entity\Technology;
use EtoA\Entity\User;

class TechnologySearchRequest
{
    public ?User $user = null;
    public ?Technology $technology = null;
    public ?Entity $entity = null;
    public ?int $buildType = null;
}
