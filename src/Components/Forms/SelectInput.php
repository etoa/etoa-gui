<?php

namespace EtoA\Components\Forms;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('forms:select_input')]
class SelectInput
{
    public string $name;

    public mixed $value = null;

    public array $options = [];

    public ?string $emptyValue = null;

    public string $emptyLabel = '---';

    public bool $keysAsLabel = false;

    public function getOptionsList(): array
    {
        if ($this->keysAsLabel) {
            return array_combine($this->options, $this->options);
        }
        return $this->options;
    }
}