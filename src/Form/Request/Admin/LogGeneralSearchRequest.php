<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

class LogGeneralSearchRequest
{
    public ?int $facility = null;
    public ?string $query = null;
    public ?int $severity = null;
}
