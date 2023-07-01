<?php

namespace EtoA\Components\Forms;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('forms:date_time_input')]
class DateTimeInput
{
    public string $name;

    public int $value = 0;
}