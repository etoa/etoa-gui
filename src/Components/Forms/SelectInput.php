<?php

namespace EtoA\Components\Forms;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent('forms:select_input')]
class SelectInput
{
    public string $name;

    public mixed $value = null;

    public array $options = [];

    public ?string $emptyValue = null;

    public string $emptyLabel = '---';

    #[PreMount]
    public function preMount(array $data): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('name');
        $resolver->setDefined('value');
        $resolver->setDefined('options');
        $resolver->setDefined('emptyValue');
        $resolver->setDefined('emptyLabel');
        return $resolver->resolve($data);
    }
}