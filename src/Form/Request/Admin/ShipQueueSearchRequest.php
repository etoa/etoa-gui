<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\Planet;
use EtoA\Entity\Ship;
use EtoA\Entity\User;

class ShipQueueSearchRequest
{
    public ?User $user = null;
    public ?Ship $ship = null;
    public ?Planet $entity = null;
}
