<?php

namespace EtoA\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('popup')]
class PopupComponent
{
    public string $title = '';
    public string $path;
}