<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\Alliance;
use EtoA\Entity\Entity;
use EtoA\Entity\User;

class LogGameSearchRequest
{
    public ?User $user = null;
    public ?Alliance $alliance = null;
    public ?Entity $entity = null;
    public ?int $facility = null;
    public ?string $query = null;
    public ?int $severity = null;
    public ?int $object = null;
}
