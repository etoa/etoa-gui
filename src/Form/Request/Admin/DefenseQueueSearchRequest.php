<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\Defense;
use EtoA\Entity\Planet;
use EtoA\Entity\User;

class DefenseQueueSearchRequest
{
    public ?User $user = null;
    public ?Planet $entity = null;
    public ?Defense $defense = null;
}
