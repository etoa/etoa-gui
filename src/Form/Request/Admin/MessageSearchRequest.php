<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

class MessageSearchRequest
{
    public ?int $sender = null;
    public ?int $recipient = null;
    public ?string $subject = null;
    public ?string $text = null;
    public ?int $category = null;
    public ?bool $read = null;
    public ?bool $deleted = null;
    public ?bool $archived = null;
    public ?bool $massmail = null;
}
