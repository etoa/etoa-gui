<?php

namespace EtoA\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('alert')]
class AlertComponent
{
    public string $type = 'info';

    public ?string $title = null;
}
