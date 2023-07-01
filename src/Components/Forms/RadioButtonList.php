<?php

namespace EtoA\Components\Forms;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('forms:radio_button_list')]
class RadioButtonList
{
    public string $name;

    public mixed $value = null;

    public array $options = [];

    #[PreMount]
    public function preMount(array $data): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('name');
        $resolver->setDefined('value');
        $resolver->setDefined('options');
        return $resolver->resolve($data);
    }
}