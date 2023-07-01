<?php

namespace EtoA\Components\Forms;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('forms:radio_button_list')]
class RadioButtonList
{
    public string $name;

    public mixed $value = null;

    public array $options = [];
}