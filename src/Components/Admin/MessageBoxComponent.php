<?php

namespace EtoA\Components\Admin;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('admin_message_box')]
class MessageBoxComponent
{
    public string $type = 'info';

    public ?string $title = null;

    public string $message;
}
