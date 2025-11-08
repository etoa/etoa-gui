<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\Entity;
use EtoA\Entity\User;

class ReportSearchRequest
{
    public ?string $type = null;
    public ?User $user = null;
    public ?User $opponent = null;
    public ?Entity $entity = null;
    public ?bool $read = null;
    public ?bool $deleted = null;
    public ?bool $archived = null;
}
