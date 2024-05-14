<?php

namespace EtoA\Components\Core;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('box')]
class BoxComponent
{
    public string $title = "";
    public string $class = "";
}