<?php declare(strict_types=1);

namespace EtoA\Form\Request\Admin;

use EtoA\Entity\User;

class ChatLogSearchRequest
{
    public ?User $user = null;
    public ?string $text = null;
}
