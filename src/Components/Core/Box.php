<?php

namespace EtoA\Components\Core;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/box.html.twig')]
class Box
{
    public string $title = "";
    public string $class = "";
}