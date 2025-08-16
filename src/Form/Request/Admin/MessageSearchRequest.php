<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\MessageCategory;
use EtoA\Entity\User;

class MessageSearchRequest
{
    public ?User $sender = null;
    public ?User $recipient = null;
    public ?string $subject = null;
    public ?string $text = null;
    public ?MessageCategory $category = null;
    public ?bool $read = null;
    public ?bool $deleted = null;
    public ?bool $archived = null;
    public ?bool $massmail = null;
}
