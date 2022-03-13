<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

class LogGameSearchRequest
{
    public ?int $user = null;
    public ?int $alliance = null;
    public ?int $entity = null;
    public ?int $facility = null;
    public ?string $query = null;
    public ?int $severity = null;
    public ?int $object = null;
}
