<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

class ReportSearchRequest
{
    public ?string $type = null;
    public ?int $userId = null;
    public ?int $opponentId = null;
    public ?int $entityId = null;
    public ?bool $read = null;
    public ?bool $deleted = null;
    public ?bool $archived = null;
}
