<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\Missile;
use EtoA\Entity\Planet;
use EtoA\Entity\User;

class MissilesSearchRequest
{
    public ?User $user = null;
    public ?Planet $entity = null;
    public ?Missile $missile = null;
}
