<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

class LogFleetsSearchRequest
{
    public ?int $facility = null;
    public ?int $severity = null;
    public ?string $action = null;
    public ?int $status = null;
    public ?int $fleetUser = null;
    public ?int $entityUser = null;
}
